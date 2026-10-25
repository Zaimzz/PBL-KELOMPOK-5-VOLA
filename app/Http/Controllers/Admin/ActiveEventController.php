<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActiveEventController extends Controller
{
    /**
     * Display a listing of active and published events.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:all,buka_rekrutmen,sedang_berjalan,selesai,active,closed,finished'],
        ]);

        $allowedStatuses = [
            EventStatus::Active,
            EventStatus::Closed,
            EventStatus::Finished,
        ];

        $query = Event::query()->whereIn('status', $allowedStatuses);

        // Search Filter: event title, location, city, organizer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $operator = $q->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where('title', $operator, '%'.$search.'%')
                    ->orWhere('location', $operator, '%'.$search.'%')
                    ->orWhere('city', $operator, '%'.$search.'%')
                    ->orWhereHas('organizer', function ($oq) use ($search, $operator) {
                        $oq->where('organization_name', $operator, '%'.$search.'%')
                            ->orWhere('pic_name', $operator, '%'.$search.'%');
                    });
            });
        }

        // Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $cat = $request->category;
            $query->whereHas('category', function ($cq) use ($cat) {
                $cq->where('id', $cat)->orWhere('slug', $cat);
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            $today = now()->toDateString();

            if ($status === 'buka_rekrutmen') {
                $query->where('status', EventStatus::Active)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('registration_deadline')
                            ->orWhereDate('registration_deadline', '>=', $today);
                    });
            } elseif ($status === 'sedang_berjalan') {
                $query->where('status', EventStatus::Active)
                    ->where(function ($q) use ($today) {
                        $q->whereDate('registration_deadline', '<', $today)
                            ->orWhere(function ($sq) use ($today) {
                                $sq->whereDate('start_date', '<=', $today)
                                    ->whereDate('end_date', '>=', $today);
                            });
                    });
            } elseif ($status === 'selesai' || $status === 'finished') {
                $query->where(function ($q) use ($today) {
                    $q->where('status', EventStatus::Finished)
                        ->orWhereDate('end_date', '<', $today);
                });
            } elseif ($status === 'active') {
                $query->where('status', EventStatus::Active);
            } elseif ($status === 'closed') {
                $query->where('status', EventStatus::Closed);
            }
        }

        // Header Metrics
        $stats = [
            'total_active' => Event::where('status', EventStatus::Active)->count(),
            'registered_volunteers' => Application::where('status', ApplicationStatus::Accepted)
                ->whereHas('position.event', fn ($q) => $q->whereIn('status', $allowedStatuses))
                ->count(),
            'open_registration' => Event::where('status', EventStatus::Active)
                ->where(function ($q) {
                    $q->whereNull('registration_deadline')
                        ->orWhereDate('registration_deadline', '>=', now()->toDateString());
                })->count(),
        ];

        // Eager load relations to prevent N+1 queries
        $events = $query->with([
            'organizer',
            'category',
            'positions.applications' => function ($q) {
                $q->where('status', ApplicationStatus::Accepted);
            },
        ])->latest('published_at')->latest('id')->paginate(5)->withQueryString();

        $categories = EventCategory::orderBy('name')->get();

        return view('admin.active-events.index', compact('events', 'stats', 'categories'));
    }

    /**
     * Display or return JSON for event detail modal.
     */
    public function show(Request $request, Event $event)
    {
        $allowedStatuses = [
            EventStatus::Active,
            EventStatus::Closed,
            EventStatus::Finished,
        ];

        if (! in_array($event->status, $allowedStatuses)) {
            abort(404, 'Event tidak ditemukan atau belum dipublikasikan.');
        }

        $event->load([
            'organizer.user',
            'category',
            'positions.applications' => function ($q) {
                $q->where('status', ApplicationStatus::Accepted);
            },
        ]);

        $totalQuota = $event->positions->sum('quota');
        $acceptedCount = $event->positions->sum(fn ($p) => $p->applications->count());
        $progressPercent = $totalQuota > 0 ? min(100, round(($acceptedCount / $totalQuota) * 100)) : 0;

        $today = now()->toDateString();
        $isRegistrationOpen = $event->status === EventStatus::Active &&
            (! $event->registration_deadline || $event->registration_deadline->toDateString() >= $today);

        $statusBadge = 'Sedang Berjalan';
        if ($event->status === EventStatus::Finished || ($event->end_date && $event->end_date->toDateString() < $today)) {
            $statusBadge = 'Selesai';
        } elseif ($isRegistrationOpen) {
            $statusBadge = 'Buka Rekrutmen';
        }

        $data = [
            'id' => $event->id,
            'event_code' => 'ID: EVT-'.($event->start_date ? $event->start_date->format('Y') : now()->format('Y')).'-'.str_pad($event->id, 4, '0', STR_PAD_LEFT),
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description ?: 'Tidak ada deskripsi singkat untuk event ini.',
            'category' => $event->category?->name ?? 'Umum',
            'organizer_name' => $event->organizer?->organization_name ?? 'Penyelenggara',
            'organizer_type' => $event->organizer?->organization_type ?? 'EO Terverifikasi',
            'pic_name' => $event->organizer?->pic_name ?? $event->pic_name ?? '-',
            'pic_phone' => $event->organizer?->pic_phone ?? $event->pic_phone ?? '-',
            'contact_info' => $event->contact_info ?? '-',
            'location' => $event->location ?? '-',
            'city' => $event->city ?? '',
            'full_location' => trim(($event->location ?? '').($event->city ? ', '.$event->city : ''), ', '),
            'start_date' => $event->start_date ? $event->start_date->translatedFormat('d M Y') : '-',
            'end_date' => $event->end_date ? $event->end_date->translatedFormat('d M Y') : '-',
            'registration_deadline' => $event->registration_deadline ? $event->registration_deadline->translatedFormat('d M Y') : 'Terbuka',
            'status' => $event->status->value,
            'status_badge' => $statusBadge,
            'total_quota' => $totalQuota,
            'accepted_count' => $acceptedCount,
            'progress_percent' => $progressPercent,
            'poster_url' => $event->poster_path ? Storage::url($event->poster_path) : null,
            'benefits' => is_array($event->benefits) ? $event->benefits : ($event->benefits ? [$event->benefits] : []),
            'positions' => $event->positions->map(fn ($p) => [
                'name' => $p->name,
                'quota' => $p->quota,
                'filled' => $p->applications->count(),
                'description' => $p->description,
            ])->values(),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return view('admin.active-events.show', compact('event', 'data'));
    }
}
