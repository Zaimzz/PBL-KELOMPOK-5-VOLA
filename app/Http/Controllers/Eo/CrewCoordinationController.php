<?php

namespace App\Http\Controllers\Eo;

use App\Enums\ApplicationStatus;
use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrewCoordinationController extends Controller
{
    /**
     * Display accepted crew grouped by position for the selected event.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        if (! $organizerProfile) {
            return view('eo.crew.index', [
                'events' => collect(),
                'selectedEvent' => null,
                'positions' => collect(),
                'crewByPosition' => collect(),
                'totalAccepted' => 0,
                'openQuotaCount' => 0,
                'organizerProfile' => null,
                'filterPositionId' => null,
            ]);
        }

        // All active events owned by this EO
        $events = Event::where('organizer_id', $organizerProfile->id)
            ->where('status', EventStatus::Active)
            ->orderByDesc('created_at')
            ->get();

        // Resolve selected event
        $selectedEventId = $request->query('event_id');
        $selectedEvent = $events->firstWhere('id', $selectedEventId) ?? $events->first();

        if (! $selectedEvent) {
            return view('eo.crew.index', [
                'events' => $events,
                'selectedEvent' => null,
                'positions' => collect(),
                'crewByPosition' => collect(),
                'totalAccepted' => 0,
                'openQuotaCount' => 0,
                'organizerProfile' => $organizerProfile,
                'filterPositionId' => null,
            ]);
        }

        // All positions for the selected event
        $positions = EventPosition::where('event_id', $selectedEvent->id)
            ->withCount(['applications as accepted_count' => function ($q) {
                $q->where('status', ApplicationStatus::Accepted);
            }])
            ->orderBy('name')
            ->get();

        // Position filter
        $filterPositionId = $request->query('position_id');

        // Build accepted applications query
        $query = Application::query()
            ->whereHas('position', fn ($q) => $q->where('event_id', $selectedEvent->id))
            ->where('status', ApplicationStatus::Accepted)
            ->with(['volunteerProfile.user', 'position']);

        if ($filterPositionId) {
            $query->where('position_id', $filterPositionId);
        }

        $acceptedApplications = $query->orderBy('decided_at')->get();

        // Group by position
        $crewByPosition = $acceptedApplications->groupBy('position_id');

        // Stats
        $totalAccepted = $positions->sum('accepted_count');

        // Positions with remaining quota (quota > accepted_count)
        $openQuotaCount = $positions->filter(function (EventPosition $pos) {
            if ($pos->quota === null) {
                return false; // unlimited treated as not "open quota" for the counter
            }

            return ($pos->quota - $pos->accepted_count) > 0;
        })->count();

        return view('eo.crew.index', compact(
            'events',
            'selectedEvent',
            'positions',
            'crewByPosition',
            'totalAccepted',
            'openQuotaCount',
            'organizerProfile',
            'filterPositionId',
        ));
    }

    /**
     * Export accepted crew as CSV for the selected event.
     * Only the EO who owns the event may export.
     */
    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        if (! $organizerProfile) {
            return redirect()->route('eo.crew.index')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $eventId = $request->query('event_id');
        $event = Event::where('organizer_id', $organizerProfile->id)
            ->where('id', $eventId)
            ->first();

        if (! $event) {
            // Fallback to first owned event
            $event = Event::where('organizer_id', $organizerProfile->id)->first();
        }

        if (! $event) {
            return redirect()->route('eo.crew.index')->with('error', 'Event tidak ditemukan.');
        }

        $filterPositionId = $request->query('position_id');

        $query = Application::query()
            ->whereHas('position', fn ($q) => $q->where('event_id', $event->id))
            ->where('status', ApplicationStatus::Accepted)
            ->with(['volunteerProfile.user', 'position']);

        if ($filterPositionId) {
            $query->where('position_id', $filterPositionId);
        }

        $crew = $query->orderBy('decided_at')->get();

        $filename = 'daftar-crew-vola-'.Str::slug($event->title).'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($crew) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'No',
                'Nama Volunteer',
                'Institusi',
                'Posisi / Divisi',
                'Nomor WhatsApp',
                'Tanggal Diterima',
            ]);

            foreach ($crew as $index => $application) {
                $volunteer = $application->volunteerProfile;
                $user = $volunteer?->user;
                $phone = self::normalizePhone($user?->phone ?? '');

                fputcsv($handle, [
                    $index + 1,
                    $user?->name ?? '—',
                    $volunteer?->institution ?? '—',
                    $application->position?->name ?? '—',
                    $phone ?: '—',
                    $application->decided_at?->translatedFormat('d M Y') ?? '—',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Normalize a phone number to international format (e.g., 6281234567890).
     * Strips all non-digit characters and replaces leading 0 with 62.
     */
    public static function normalizePhone(string $phone): string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '' || $digits === null) {
            return '';
        }

        // Replace leading 0 with 62 (Indonesia country code)
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        // If no country code prefix, assume Indonesia
        if (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }

    /**
     * Build a wa.me Click-to-Chat URL with a pre-filled message.
     * Returns empty string if phone is empty.
     */
    public static function buildWhatsAppUrl(string $phone, string $volunteerName, string $eventTitle): string
    {
        $normalized = self::normalizePhone($phone);

        if ($normalized === '') {
            return '';
        }

        $message = "Halo {$volunteerName}, saya dari tim EO \"{$eventTitle}\". Kami ingin menghubungi Anda terkait koordinasi crew untuk event ini. Terima kasih!";

        return 'https://wa.me/'.$normalized.'?text='.urlencode($message);
    }
}
