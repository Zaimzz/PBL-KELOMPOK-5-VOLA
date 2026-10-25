<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Notifications\EventApprovedNotification;
use App\Notifications\EventRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewEventController extends Controller
{
    /**
     * Display the review event curation queue and metrics.
     */
    public function index(Request $request)
    {
        $query = Event::with(['organizer.user', 'category', 'positions']);

        // Search by event title or organizer/pic name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $operator = $q->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where('title', $operator, '%'.$search.'%')
                    ->orWhereHas('organizer', function ($oq) use ($search, $operator) {
                        $oq->where('organization_name', $operator, '%'.$search.'%')
                            ->orWhere('pic_name', $operator, '%'.$search.'%');
                    });
            });
        }

        // PERBAIKAN: Batasi ruang lingkup hanya untuk event di tahap kurasi
        $curationStatuses = [
            EventStatus::PendingReview,
            EventStatus::Active,
            EventStatus::Rejected,
            // EventStatus::AwaitingPayment // Buka komentar ini jika status ini dianggap masuk panel kurasi
        ];

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'all') {
                // Jangan biarkan kosong. Kunci query HANYA pada status kurasi.
                $query->whereIn('status', $curationStatuses);
            } else {
                $query->where('status', $request->status);
            }
        } else {
            // Default queue shows pending review events
            $query->where('status', EventStatus::PendingReview);
        }

        $events = $query->latest()->paginate(10)->withQueryString();

        // Metrics for header
        $stats = [
            'pending' => Event::where('status', EventStatus::PendingReview)->count(),
            'approved_this_month' => Event::where('status', EventStatus::Active)
                ->where(function ($q) {
                    $q->whereMonth('published_at', now()->month)
                        ->whereYear('published_at', now()->year)
                        ->orWhere(function ($sq) {
                            $sq->whereNull('published_at')
                                ->whereMonth('updated_at', now()->month)
                                ->whereYear('updated_at', now()->year);
                        });
                })->count(),
        ];

        // Format events payload for Alpine.js dynamic detail preview
        $eventsPayload = $events->getCollection()->map(function ($event) {
            return $this->transformEvent($event);
        })->values();

        // Initially selected event (specific selected item or first item in list)
        $selectedEvent = null;
        if ($request->filled('selected')) {
            $selectedEvent = $eventsPayload->firstWhere('id', (int) $request->selected);
            if (! $selectedEvent) {
                $targetEvent = Event::with(['organizer.user', 'category', 'positions'])->find($request->selected);
                if ($targetEvent) {
                    $selectedEvent = $this->transformEvent($targetEvent);
                    $eventsPayload->prepend($selectedEvent);
                }
            }
        }

        if (! $selectedEvent) {
            $selectedEvent = $eventsPayload->first() ?? null;
        }

        return view('admin.review-event.index', compact('events', 'stats', 'eventsPayload', 'selectedEvent'));
    }

    /**
     * Return detailed event data (JSON for Alpine.js dynamic panel or view).
     */
    public function show(Request $request, Event $event)
    {
        $event->load(['category', 'organizer.user', 'positions']);
        $data = $this->transformEvent($event);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return view('admin.review-event.index', [
            'events' => Event::with(['organizer.user', 'category', 'positions'])->where('status', EventStatus::PendingReview)->latest()->paginate(10),
            'stats' => [
                'pending' => Event::where('status', EventStatus::PendingReview)->count(),
                'approved_this_month' => Event::where('status', EventStatus::Active)->count(),
            ],
            'eventsPayload' => collect([$data]),
            'selectedEvent' => $data,
        ]);
    }

    /**
     * Approve the event: set status to Active (published) and notify EO.
     */
    public function approve(Request $request, Event $event)
    {
        $event->update([
            'status' => EventStatus::Active,
            'published_at' => now(),
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        if ($event->organizer && $event->organizer->user) {
            $event->organizer->user->notify(new EventApprovedNotification($event));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event berhasil disetujui dan siap tayang ke publik.',
                'event' => $event,
            ]);
        }

        return redirect()->route('admin.review-events.index')
            ->with('success', 'Event "'.$event->title.'" berhasil disetujui dan kini siap tayang secara publik.');
    }

    /**
     * Reject the event: validate rejection reason, set status to Rejected, and notify EO.
     */
    public function reject(Request $request, Event $event)
    {
        if ($request->has('rejection_reason') && ! $request->has('alasan')) {
            $request->validate([
                'rejection_reason' => 'required|string',
            ], [
                'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            ]);
            $reason = $request->rejection_reason;
        } else {
            $request->validate([
                'alasan' => 'required|string',
            ], [
                'alasan.required' => 'Alasan penolakan wajib diisi.',
            ]);
            $reason = $request->alasan;
        }

        $event->update([
            'status' => EventStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
            'published_at' => null,
        ]);

        if ($event->organizer && $event->organizer->user) {
            $event->organizer->user->notify(new EventRejectedNotification($event, $reason));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan event berhasil ditolak.',
                'event' => $event,
            ]);
        }

        return redirect()->route('admin.review-events.index')
            ->with('success', 'Pengajuan event "'.$event->title.'" berhasil ditolak.');
    }

    /**
     * Transform an event model into structured display array.
     */
    private function transformEvent(Event $event): array
    {
        $statusVal = $event->status instanceof EventStatus ? $event->status->value : (string) $event->status;

        $statusLabel = match ($statusVal) {
            'pending_review' => 'Menunggu Review',
            'active' => 'Disetujui',
            'rejected' => 'Ditolak',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'draft' => 'Draft',
            'closed' => 'Ditutup',
            'finished' => 'Selesai',
            default => ucfirst($statusVal),
        };

        $totalQuota = $event->positions ? $event->positions->sum('quota') : 0;

        $benefitsArray = [];
        if (is_array($event->benefits)) {
            $benefitsArray = $event->benefits;
        } elseif (is_string($event->benefits) && ! empty($event->benefits)) {
            $decoded = json_decode($event->benefits, true);
            $benefitsArray = is_array($decoded) ? $decoded : [$event->benefits];
        }

        return [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'status' => $statusVal,
            'status_label' => $statusLabel,
            'category' => $event->category?->name ?? 'Umum',
            'organizer' => [
                'id' => $event->organizer?->id,
                'name' => $event->organizer?->organization_name ?? 'Penyelenggara',
                'pic_name' => $event->organizer?->pic_name ?? $event->pic_name ?? '-',
                'pic_phone' => $event->organizer?->pic_phone ?? $event->pic_phone ?? '-',
                'is_verified' => $event->organizer?->verification_status === VerificationStatus::Verified,
            ],
            'date_formatted' => $this->formatDateRange($event),
            'location' => $event->location ?? '-',
            'city' => $event->city ?? '',
            'full_location' => trim(($event->location ?? '').($event->city ? ', '.$event->city : ''), ', '),
            'description' => $event->description ?: 'Tidak ada deskripsi singkat untuk pengajuan event ini.',
            'benefits' => $benefitsArray,
            'benefits_text' => ! empty($benefitsArray) ? implode(', ', $benefitsArray) : 'Tidak ada benefit tercantum.',
            'positions' => $event->positions->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'quota' => $p->quota,
                ];
            })->values()->all(),
            'total_quota' => $totalQuota,
            'poster_url' => $event->poster_path ? Storage::url($event->poster_path) : null,
            'submitted_date_formatted' => 'Diajukan '.($event->created_at ? $event->created_at->translatedFormat('d M Y') : '-'),
            'rejection_reason' => $event->rejection_reason,
            'approve_url' => route('admin.review-events.approve', $event),
            'reject_url' => route('admin.review-events.reject', $event),
        ];
    }

    /**
     * Format start and end date into human-readable Indonesian range.
     */
    private function formatDateRange(Event $event): string
    {
        if (! $event->start_date) {
            return 'Tanggal belum ditentukan';
        }

        $start = $event->start_date;
        $end = $event->end_date;

        if (! $end || $start->equalTo($end)) {
            return $start->translatedFormat('d F Y');
        }

        $days = $start->diffInDays($end) + 1;
        $durationText = " ({$days} Hari Penuh)";

        if ($start->format('Y-m') === $end->format('Y-m')) {
            return $start->format('d').' – '.$end->translatedFormat('d F Y').$durationText;
        }

        return $start->translatedFormat('d M').' – '.$end->translatedFormat('d M Y').$durationText;
    }
}
