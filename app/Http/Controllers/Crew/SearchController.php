<?php

namespace App\Http\Controllers\Crew;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const CATEGORY_IMAGES = [
        'festival'  => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800',
        'seminar'   => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
        'musik'     => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800',
        'olahraga'  => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
        'sosial'    => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=800',
        'kampus'    => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?w=800',
        'komunitas' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800',
        'umum'      => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800',
    ];

    private function getImageByCategory(?string $category): string
    {
        $key = strtolower(trim($category ?? 'umum'));
        return self::CATEGORY_IMAGES[$key] ?? self::CATEGORY_IMAGES['umum'];
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $category = $request->input('category', 'Semua');
        $location = trim((string) $request->input('location', ''));
        $position = (array) $request->input('position', []);
        $sort = strtolower((string) $request->input('sort', 'terdekat'));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 6;

        $dbQuery = Event::query()
            ->with(['category', 'organizer', 'positions'])
            ->where('status', EventStatus::Active)
            ->whereNotNull('published_at')
            ->whereDate('registration_deadline', '>=', now()->toDateString());

        $operator = $dbQuery->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        if ($query !== '') {
            $dbQuery->where(function ($q) use ($query, $operator) {
                $q->where('title', $operator, '%'.$query.'%')
                  ->orWhere('location', $operator, '%'.$query.'%')
                  ->orWhere('city', $operator, '%'.$query.'%')
                  ->orWhereHas('category', fn ($cq) => $cq->where('name', $operator, '%'.$query.'%'))
                  ->orWhereHas('organizer', fn ($oq) => $oq->where('organization_name', $operator, '%'.$query.'%'))
                  ->orWhereHas('positions', fn ($pq) => $pq->where('name', $operator, '%'.$query.'%'));
            });
        }

        if ($category !== '' && ! in_array($category, ['Semua', 'Semua Kategori'], true)) {
            $dbQuery->whereHas('category', fn ($cq) => $cq->where('name', $operator, '%'.$category.'%'));
        }

        if ($location !== '' && $location !== 'Semua Lokasi') {
            $dbQuery->where(function ($q) use ($location, $operator) {
                $q->where('city', $operator, '%'.$location.'%')
                  ->orWhere('location', $operator, '%'.$location.'%');
            });
        }

        if (! empty($position)) {
            $dbQuery->where(function ($q) use ($position, $operator) {
                foreach ($position as $pos) {
                    $q->orWhereHas('positions', fn ($pq) => $pq->where('name', $operator, '%'.$pos.'%'));
                }
            });
        }

        if (str_contains($sort, 'baru')) {
            $sort = 'terbaru';
            $dbQuery->orderByDesc('published_at');
        } elseif (str_contains($sort, 'populer')) {
            $sort = 'terpopuler';
            $dbQuery->withCount('positions')->orderByDesc('positions_count');
        } else {
            $sort = 'terdekat';
            $dbQuery->orderBy('registration_deadline');
        }

        $allDbEvents = $dbQuery->get();

        $allEvents = $allDbEvents->map(function (Event $event) {
            $positionNames = $event->positions->pluck('name');
            $positionsText = $positionNames->take(3)->implode(', ');
            if ($positionNames->count() > 3) {
                $positionsText .= '... ('.$positionNames->count().' posisi)';
            }

            $positionCount = $event->positions->count();
            $daysLeft = (int) now()->startOfDay()->diffInDays($event->registration_deadline, false);
            $daysLeft = max(0, $daysLeft);

            if ($daysLeft <= 7) {
                $slotType = 'urgent';
                $slotLabel = 'Sisa '.$daysLeft.' Hari';
            } elseif ($positionCount > 0) {
                $slotType = 'position';
                $slotLabel = $positionCount.' Posisi Dibuka';
            } else {
                $slotType = 'interview';
                $slotLabel = 'Seleksi Interview';
            }

            $startDate = $event->start_date?->translatedFormat('d M Y');
            $endDate = $event->end_date?->translatedFormat('d M Y');
            $dateFormatted = $endDate && $endDate !== $startDate ? $startDate.' – '.$endDate : ($startDate ?? '-');

            $imageUrl = $event->poster_path ? Storage::url($event->poster_path) : $this->getImageByCategory($event->category?->name);

            $isVerified = $event->organizer?->verification_status === VerificationStatus::Verified;

            return [
                'id' => $event->id, 'title' => $event->title, 'category' => $event->category?->name ?? 'Umum',
                'filter_category' => strtolower($event->category?->name ?? 'umum'), 'organizer' => $event->organizer?->organization_name ?? '-',
                'is_verified' => $isVerified, 'status_badge' => 'Open Volunteer', 'status_badge_type' => 'success',
                'date' => $dateFormatted, 'location' => trim(($event->location ?? '').($event->city ? ', '.$event->city : ''), ', '),
                'positions' => $positionsText ?: 'Belum ada posisi', 'days_left' => $daysLeft, 'position_count' => $positionCount,
                'slot_label' => $slotLabel, 'slot_type' => $slotType, 'image' => $imageUrl,
            ];
        });

        $totalEvents = $allEvents->count();
        $totalPages = (int) ceil($totalEvents / $perPage);
        $page = max(1, min($page, max(1, $totalPages)));
        $offset = ($page - 1) * $perPage;
        $paginatedEvents = $allEvents->slice($offset, $perPage)->values();

        $categories = ['Semua', 'Festival', 'Seminar', 'Musik', 'Olahraga', 'Sosial', 'Kampus', 'Komunitas'];
        $locations = ['Semua Lokasi', 'Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Bali'];

        return view('crew.search', compact('paginatedEvents', 'totalEvents', 'totalPages', 'page', 'perPage', 'query', 'category', 'location', 'position', 'sort', 'categories', 'locations'));
    }
}