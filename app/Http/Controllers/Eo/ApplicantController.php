<?php

namespace App\Http\Controllers\Eo;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use App\Notifications\ApplicationDecided;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants for the authenticated EO's events.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        if (! $organizerProfile) {
            return view('eo.applicants.index', [
                'applications' => collect(),
                'events' => collect(),
                'selectedEvent' => null,
                'positions' => collect(),
                'totalCount' => 0,
                'pendingCount' => 0,
                'acceptedCount' => 0,
                'organizerProfile' => null,
            ]);
        }

        // All events owned by this EO (any status)
        $events = Event::where('organizer_id', $organizerProfile->id)
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->get();

        // Resolve selected event
        $selectedEventId = $request->query('event_id');
        $selectedEvent = $events->firstWhere('id', $selectedEventId) ?? $events->first();

        // If EO has no events at all
        if (! $selectedEvent) {
            return view('eo.applicants.index', [
                'applications' => collect(),
                'events' => collect(),
                'selectedEvent' => null,
                'positions' => collect(),
                'totalCount' => 0,
                'pendingCount' => 0,
                'acceptedCount' => 0,
                'organizerProfile' => $organizerProfile,
            ]);
        }

        // Positions for the selected event (for filter dropdown)
        $positions = $selectedEvent->positions()->orderBy('name')->get();

        // Build base query for applications on the selected event
        $query = Application::query()
            ->whereHas('position', fn ($q) => $q->where('event_id', $selectedEvent->id))
            ->with([
                'volunteerProfile.user',
                'position',
            ]);

        // Filter: position
        if ($positionId = $request->query('position_id')) {
            $query->where('position_id', $positionId);
        }

        // Filter: status
        if ($statusFilter = $request->query('status')) {
            $status = ApplicationStatus::tryFrom($statusFilter);
            if ($status) {
                $query->where('status', $status);
            }
        }

        // Search: volunteer name (LIKE is case-insensitive on SQLite and adequate on PostgreSQL)
        if ($search = $request->query('search')) {
            $query->whereHas('volunteerProfile.user', function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']);
            });
        }

        // Statistics for selected event (always based on full event, ignoring search/filters)
        $statRows = DB::table('applications')
            ->join('event_positions', 'applications.position_id', '=', 'event_positions.id')
            ->where('event_positions.event_id', $selectedEvent->id)
            ->selectRaw('applications.status, COUNT(*) as aggregate')
            ->groupBy('applications.status')
            ->pluck('aggregate', 'applications.status');

        $totalCount = (int) $statRows->sum();
        $pendingCount = (int) ($statRows[ApplicationStatus::Pending->value] ?? 0);
        $acceptedCount = (int) ($statRows[ApplicationStatus::Accepted->value] ?? 0);

        $applications = $query->orderByDesc('applied_at')->paginate(10)->withQueryString();

        return view('eo.applicants.index', compact(
            'applications',
            'events',
            'selectedEvent',
            'positions',
            'totalCount',
            'pendingCount',
            'acceptedCount',
            'organizerProfile',
        ));
    }

    /**
     * Accept a volunteer application.
     * Only the EO who owns the event may accept.
     * The position row is locked so concurrent accepts cannot exceed its quota.
     */
    public function accept(Request $request, Application $application): RedirectResponse
    {
        $this->authorizeApplication($request, $application);

        $result = DB::transaction(function () use ($application): string {
            $position = EventPosition::query()
                ->whereKey($application->position_id)
                ->lockForUpdate()
                ->firstOrFail();

            $application->refresh();

            if ($application->status !== ApplicationStatus::Pending) {
                return 'invalid_status';
            }

            $acceptedCount = Application::query()
                ->where('position_id', $position->id)
                ->where('status', ApplicationStatus::Accepted)
                ->count();

            if ($acceptedCount >= $position->quota) {
                return 'quota_full';
            }

            $application->update([
                'status' => ApplicationStatus::Accepted,
                'decided_at' => now(),
                'decision_note' => null,
            ]);

            return 'accepted';
        });

        if ($result === 'invalid_status') {
            return back()->with('error', 'Hanya pendaftar berstatus menunggu yang dapat diterima.');
        }

        if ($result === 'quota_full') {
            return back()->with('error', 'Kuota posisi '.$application->position?->name.' sudah penuh. Pendaftar tidak dapat diterima.');
        }

        // Notify volunteer via Laravel notification system
        $volunteer = $application->fresh()->volunteerProfile?->user;
        if ($volunteer) {
            $volunteer->notify(new ApplicationDecided($application, 'accepted'));
            Cache::forget('crew_application_counts_'.$application->volunteer_profile_id);
            Cache::forget('crew_unread_notifs_'.$volunteer->id);
            Cache::forget('crew_history_counts_'.$application->volunteer_profile_id);
        }

        return back()->with('success', 'Pendaftar berhasil diterima.');
    }

    /**
     * Reject a volunteer application.
     * Only the EO who owns the event may reject.
     */
    public function reject(Request $request, Application $application): RedirectResponse
    {
        $this->authorizeApplication($request, $application);

        if (! in_array($application->status, [ApplicationStatus::Pending, ApplicationStatus::Accepted], true)) {
            return back()->with('error', 'Hanya pendaftar berstatus menunggu atau diterima yang dapat ditolak.');
        }

        $validated = $request->validate([
            'decision_note' => ['nullable', 'string', 'max:500'],
        ]);

        $application->update([
            'status' => ApplicationStatus::Rejected,
            'decided_at' => now(),
            'decision_note' => $validated['decision_note'] ?? null,
        ]);

        // Notify volunteer via Laravel notification system
        $volunteer = $application->volunteerProfile?->user;
        if ($volunteer) {
            $volunteer->notify(new ApplicationDecided($application, 'rejected'));
            Cache::forget('crew_application_counts_'.$application->volunteer_profile_id);
            Cache::forget('crew_unread_notifs_'.$volunteer->id);
            Cache::forget('crew_history_counts_'.$application->volunteer_profile_id);
        }

        return back()->with('success', 'Pendaftar berhasil ditolak.');
    }

    /**
     * Ensure the authenticated EO owns the event associated with this application.
     * Aborts with 403 if not.
     */
    private function authorizeApplication(Request $request, Application $application): void
    {
        $organizerProfile = $request->user()->organizerProfile;

        if (! $organizerProfile) {
            abort(403, 'Profil organisasi tidak ditemukan.');
        }

        $eventOwnerId = $application->position?->event?->organizer_id;

        if ($eventOwnerId !== $organizerProfile->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pendaftaran ini.');
        }
    }
}
