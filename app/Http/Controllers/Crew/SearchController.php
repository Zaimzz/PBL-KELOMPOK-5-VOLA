<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $category = $request->input('category', 'Semua');
        $location = trim((string) $request->input('location', ''));
        $position = (array) $request->input('position', []);
        $time = $request->input('time', '');
        $status = (array) $request->input('status', []);
        $sort = strtolower((string) $request->input('sort', 'terdekat'));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 6;

        // Data dummy event.
        // Ganti dengan query database jika data event sudah tersedia.
        $allEvents = [
            [
                'id' => 1,
                'title' => 'Indonesia Comic & Pop Culture Con 2025',
                'category' => 'Festival & Pop Culture',
                'filter_category' => 'Festival & Seni',
                'organizer' => 'PT Pop Media Nusantara',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '14 – 16 Juni 2025',
                'location' => 'JCC Senayan, Jakarta Pusat',
                'positions' => 'Liaison Officer (LO), Stage Crew, Registrasi, Usher, Media',
                'days_left' => 0,
                'position_count' => 5,
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
            ],
            [
                'id' => 2,
                'title' => 'Jakarta International Java Jazz Festival 2025',
                'category' => 'Musik & Konser',
                'filter_category' => 'Musik & Konser',
                'organizer' => 'PT Java Festival Production',
                'is_verified' => true,
                'status_badge' => 'Sisa 12 Hari',
                'status_badge_type' => 'warning',
                'date' => '5 – 7 Maret 2025',
                'location' => 'JIExpo Kemayoran, Jakarta Pusat',
                'positions' => 'Water Marshal, Media, Route',
                'days_left' => 12,
                'position_count' => 3,
                'image' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800',
            ],
            [
                'id' => 3,
                'title' => 'Bandung Creative Tech Summit 2025',
                'category' => 'Seminar & Teknologi',
                'filter_category' => 'Seminar & Teknologi',
                'organizer' => 'Bandung Digital Hub',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '10 – 11 April 2025',
                'location' => 'Bandung Digital Hub, Bandung',
                'positions' => 'Exhibition Crew, Guest Relation',
                'days_left' => 30,
                'position_count' => 2,
                'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800',
            ],
            [
                'id' => 4,
                'title' => 'Yogyakarta Heritage Marathon 2025',
                'category' => 'Olahraga & Marathon',
                'filter_category' => 'Olahraga & Marathon',
                'organizer' => 'Yogya Runners Community',
                'is_verified' => false,
                'status_badge' => 'Sisa 20 Hari',
                'status_badge_type' => 'warning',
                'date' => '25 Mei 2025',
                'location' => 'Malioboro, Yogyakarta',
                'positions' => 'Route Marshal, Water Station, Medical',
                'days_left' => 20,
                'position_count' => 3,
                'image' => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
            ],
            [
                'id' => 5,
                'title' => 'Bali Beach Cleanup & Social Action',
                'category' => 'Kegiatan Sosial',
                'filter_category' => 'Kegiatan Sosial',
                'organizer' => 'Ocean Guardians Bali',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '15 Juni 2025',
                'location' => 'Kuta Beach, Bali',
                'positions' => 'Koordinator Lapangan, Dokumentasi, Logistik',
                'days_left' => 25,
                'position_count' => 3,
                'image' => 'https://images.unsplash.com/photo-1618477247222-acbdb0e159b3?w=800',
            ],
        ];

        // Pencarian dan filter event.
        $filteredEvents = collect($allEvents)->filter(
            function ($event) use (
                $query,
                $category,
                $location,
                $position,
                $status,
                $time
            ) {
                if ($query !== '') {
                    $searchable = strtolower(implode(' ', [
                        $event['title'],
                        $event['category'],
                        $event['organizer'],
                        $event['location'],
                        $event['positions'],
                    ]));

                    if (!str_contains($searchable, strtolower($query))) {
                        return false;
                    }
                }

                if (
                    $category !== '' &&
                    !in_array($category, ['Semua', 'Semua Kategori']) &&
                    !str_contains(
                        strtolower($event['filter_category']),
                        strtolower($category)
                    ) &&
                    !str_contains(
                        strtolower($event['category']),
                        strtolower($category)
                    )
                ) {
                    return false;
                }

                if (
                    $location !== '' &&
                    $location !== 'Semua Lokasi' &&
                    !str_contains(
                        strtolower($event['location']),
                        strtolower($location)
                    )
                ) {
                    return false;
                }

                if (!empty($position)) {
                    $hasPosition = false;

                    foreach ($position as $pos) {
                        if (str_contains(
                            strtolower($event['positions']),
                            strtolower((string) $pos)
                        )) {
                            $hasPosition = true;
                            break;
                        }
                    }

                    if (!$hasPosition) {
                        return false;
                    }
                }

                if (!empty($status)) {
                    $hasStatus = false;

                    foreach ($status as $stat) {
                        if (str_contains(
                            strtolower($event['status_badge']),
                            strtolower((string) $stat)
                        )) {
                            $hasStatus = true;
                            break;
                        }
                    }

                    if (!$hasStatus) {
                        return false;
                    }
                }

                if (
                    $time === 'bulan-ini' &&
                    $event['days_left'] > 30
                ) {
                    return false;
                }

                if (
                    $time === '1-3-bulan' &&
                    (
                        $event['days_left'] <= 30 ||
                        $event['days_left'] > 90
                    )
                ) {
                    return false;
                }

                return true;
            }
        )->values();

        // Pengurutan event.
        if (str_contains($sort, 'baru')) {
            $sort = 'terbaru';
            $filteredEvents = $filteredEvents
                ->sortByDesc('id')
                ->values();
        } elseif (str_contains($sort, 'populer')) {
            $sort = 'terpopuler';
            $filteredEvents = $filteredEvents
                ->sortByDesc('position_count')
                ->values();
        } else {
            $sort = 'terdekat';
            $filteredEvents = $filteredEvents
                ->sortBy('days_left')
                ->values();
        }

        // Pagination.
        $totalEvents = $filteredEvents->count();
        $totalPages = (int) ceil($totalEvents / $perPage);
        $page = max(1, min($page, max(1, $totalPages)));
        $offset = ($page - 1) * $perPage;

        $paginatedEvents = $filteredEvents
            ->slice($offset, $perPage)
            ->values();

        // Pilihan filter.
        $categories = [
            'Semua',
            'Festival',
            'Festival & Seni',
            'Seminar',
            'Seminar & Teknologi',
            'Sosial',
            'Kegiatan Sosial',
            'Kampus',
            'Komunitas',
            'Olahraga',
            'Olahraga & Marathon',
            'Musik',
            'Musik & Konser',
        ];

        $locations = [
            'Semua Lokasi',
            'Jakarta',
            'Bandung',
            'Yogyakarta',
            'Surabaya',
            'Bali',
        ];

        return view('crew.search', compact(
            'paginatedEvents',
            'totalEvents',
            'totalPages',
            'page',
            'perPage',
            'query',
            'category',
            'location',
            'position',
            'time',
            'status',
            'sort',
            'categories',
            'locations'
        ));
    }
}
