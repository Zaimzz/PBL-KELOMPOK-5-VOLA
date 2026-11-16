<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $profile = $user?->volunteerProfile;

        if (!$profile) {
            return view('crew.history', [
                'paginatedHistories' => collect([]),
                'counts' => ['all' => 0, 'festival' => 0, 'seminar' => 0, 'sosial' => 0, 'kampus' => 0, 'komunitas' => 0, 'olahraga' => 0, 'musik' => 0],
                'totalCertificates' => 0,
                'activeCategory' => $request->input('category', 'all'),
                'totalPages' => 0,
                'currentPage' => 1,
            ]);
        }

        $activeCategory = $request->input('category', 'all');

        $query = Application::where('volunteer_profile_id', $profile->id)
            ->whereIn('status', ['accepted', 'rejected'])
            ->with(['position.event.category', 'position.event.organizer']);

        if ($activeCategory !== 'all') {
            $query->whereHas('position.event.category', function ($q) use ($activeCategory) {
              
                $q->where('name', $activeCategory); 
            });
        }

        $totalCertificates = Application::where('volunteer_profile_id', $profile->id)
            ->where('status', 'accepted')
            ->count();

        $baseQueryAll = Application::where('volunteer_profile_id', $profile->id)
            ->whereIn('status', ['accepted', 'rejected']);

        $counts = [
            'all' => (clone $baseQueryAll)->count(),
            'festival' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Festival'))->count(),
            'seminar' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Seminar'))->count(),
            'sosial' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Sosial'))->count(),
            'kampus' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Kampus'))->count(),
            'komunitas' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Komunitas'))->count(),
            'olahraga' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Olahraga'))->count(),
            'musik' => (clone $baseQueryAll)->whereHas('position.event.category', fn($q) => $q->where('name', 'Musik'))->count(),
        ];

        $perPage = 4;
        $paginatedApps = $query->orderByDesc('applied_at')->paginate($perPage);

        $histories = $paginatedApps->getCollection()->map(function ($app) {
            $event = $app->position?->event;
            
            $startDate = $event?->start_date?->translatedFormat('d M Y');
            $endDate = $event?->end_date?->translatedFormat('d M Y');
            $dateFormatted = $startDate;
            if ($endDate && $endDate !== $startDate) {
                $dateFormatted .= ' – ' . $endDate;
            }

            $location = trim(($event?->location ?? '') . ', ' . ($event?->city ?? ''));

            return [
                'id' => $app->id,
                'event_title' => $event?->title ?? 'Event Tidak Ditemukan',
                'organizer' => $event?->organizer?->organization_name ?? '-',
                'position' => $app->position?->name ?? '-',
                'date' => $dateFormatted ?: '-',
                'location' => $location ?: '-',
                'category' => $event?->category?->name ?? 'Umum',
                'hours' => 0, 
                'status_label' => $app->status === 'accepted' ? 'Selesai' : 'Ditolak',
                'status' => $app->status,
            ];
        });

        $result = new \Illuminate\Pagination\LengthAwarePaginator(
            $histories,
            $paginatedApps->total(),
            $perPage,
            $paginatedApps->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('crew.history', [
            'paginatedHistories' => $result,
            'counts' => $counts,
            'totalCertificates' => $totalCertificates,
            'activeCategory' => $activeCategory,
            'totalPages' => $result->lastPage(),
            'currentPage' => $result->currentPage(),
            'perPage' => $perPage,
        ]);
    }

    public function downloadCertificate(int $id)
    {
        $user = Auth::user();
        $profile = $user?->volunteerProfile;

        if (!$profile) abort(403);

        $app = Application::where('volunteer_profile_id', $profile->id)
            ->where('id', $id)
            ->with(['position.event.category', 'position.event.organizer'])
            ->firstOrFail();

        $event = $app->position?->event;
        
        if (!$event || $app->status !== 'accepted') {
            abort(404, 'Sertifikat tidak tersedia untuk lamaran ini.');
        }

        $displayName = $user->name ?? 'Volunteer';

        $startDate = $event->start_date?->translatedFormat('d M Y');
        $endDate = $event->end_date?->translatedFormat('d M Y');
        $dateFormatted = $startDate;
        if ($endDate && $endDate !== $startDate) {
            $dateFormatted .= ' – ' . $endDate;
        }

        $eventData = [
            'id' => $app->id,
            'event_title' => $event->title,
            'organizer' => $event->organizer?->organization_name ?? '-',
            'position' => $app->position?->name ?? '-',
            'location' => trim(($event->location ?? '') . ', ' . ($event->city ?? '')),
            'date' => $dateFormatted,
            'category' => $event->category?->name ?? 'Umum',
        ];

        $pdf = Pdf::loadView('crew.certificate-pdf', compact('eventData', 'displayName'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Sertifikat-' . str_replace(' ', '-', $event->title) . '-' . str_replace(' ', '-', $displayName) . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadPdf()
    {
        $user = Auth::user();
        $profile = $user?->volunteerProfile;

        if (!$profile) abort(403);

        $apps = Application::where('volunteer_profile_id', $profile->id)
            ->whereIn('status', ['accepted', 'rejected'])
            ->with(['position.event.category', 'position.event.organizer'])
            ->orderByDesc('applied_at')
            ->get();

        $displayName = $user->name ?? 'Volunteer';
        $displayEmail = $user->email ?? '-';
        $displayCity = $user->city ?? '-';

        $totalEvents = $apps->count();
        $totalHours = 0; 
        $totalCategories = $apps->pluck('position.event.category.name')->unique()->filter()->count();

        $histories = $apps->map(function ($app) {
            $event = $app->position?->event;
            $startDate = $event?->start_date?->translatedFormat('d M Y');
            $endDate = $event?->end_date?->translatedFormat('d M Y');
            $dateFormatted = $startDate;
            if ($endDate && $endDate !== $startDate) $dateFormatted .= ' – ' . $endDate;

            return [
                'event_title' => $event?->title ?? '-',
                'organizer' => $event?->organizer?->organization_name ?? '-',
                'position' => $app->position?->name ?? '-',
                'date' => $dateFormatted ?: '-',
                'location' => trim(($event?->location ?? '') . ', ' . ($event?->city ?? '')),
                'category' => $event?->category?->name ?? 'Umum',
                'hours' => 0,
                'status_label' => $app->status === 'accepted' ? 'Selesai' : 'Ditolak',
            ];
        });

        $pdf = Pdf::loadView('crew.history-pdf', compact(
            'displayName',
            'displayEmail',
            'displayCity',
            'totalEvents',
            'totalHours',
            'totalCategories',
            'histories'
        ));

        $pdf->setPaper('A4', 'portrait');
        $filename = 'Riwayat-Pengalaman-' . str_replace(' ', '-', $displayName) . '.pdf';

        return $pdf->download($filename);
    }
}