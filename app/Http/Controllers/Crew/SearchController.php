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

        $allEvents = [
            [
                'id' => 1,
                'title' => 'Indonesia Comic & Pop Culture Con 2025',
                'category' => 'Festival',
                'filter_category' => 'Festival',
                'organizer' => 'PT Pop Media Nusantara',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '14 – 16 Juni 2025',
                'location' => 'JCC Senayan, Jakarta',
                'positions' => 'LO, Usher, Stage Crew... (5 posisi)',
                'days_left' => 5,
                'position_count' => 5,
                'slot_label' => 'Sisa 5 Hari',
                'slot_type' => 'urgent',
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
            ],
            [
                'id' => 2,
                'title' => 'Jogja International Street Festival 2025',
                'category' => 'Festival',
                'filter_category' => 'Festival',
                'organizer' => 'Dinas Pariwisata DIY',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '2 – 3 Mei 2025',
                'location' => 'Malioboro, Yogyakarta',
                'positions' => 'Crowd Control, Info... Desk, Medis (3 posisi)',
                'days_left' => 25,
                'position_count' => 12,
                'slot_label' => 'Sisa 12 Slot Kru',
                'slot_type' => 'slot',
                'image' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800',
            ],
            [
                'id' => 3,
                'title' => 'Surabaya Young Leaders Climate Conference',
                'category' => 'Seminar',
                'filter_category' => 'Seminar',
                'organizer' => 'EcoYouth Initiative',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '20 Mei 2025',
                'location' => 'Grand City, Surabaya',
                'positions' => 'LO, Notulensi, Registrasi, VIP',
                'days_left' => 30,
                'position_count' => 0,
                'slot_label' => 'Seleksi Interview',
                'slot_type' => 'interview',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800',
            ],
            [
                'id' => 4,
                'title' => 'Festival Film Dokumenter Indonesia 2025',
                'category' => 'Festival',
                'filter_category' => 'Festival',
                'organizer' => 'Sinema Kolektif ID',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '1 – 5 Juli 2025',
                'location' => 'TIM, Jakarta Pusat',
                'positions' => 'Ticketing, Screening Officer, Dokumentasi',
                'days_left' => 45,
                'position_count' => 5,
                'slot_label' => '5 Posisi Dibuka',
                'slot_type' => 'position',
                'image' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=800',
            ],
            [
                'id' => 5,
                'title' => 'Web3 & AI Developer Summit Nusantara 2025',
                'category' => 'Seminar',
                'filter_category' => 'Seminar',
                'organizer' => 'Nusantara Dev Hub',
                'is_verified' => true,
                'status_badge' => 'Event Informasi Saja',
                'status_badge_type' => 'info',
                'date' => '12 – 14 Juni 2025',
                'location' => 'Menara Multimedia, Jakarta',
                'positions' => 'Khusus Peserta / Audiens Hackathon',
                'days_left' => 35,
                'position_count' => 0,
                'slot_label' => 'Tiket Gratis (RSVP)',
                'slot_type' => 'rsvp',
                'image' => 'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=800',
            ],
            [
                'id' => 6,
                'title' => 'Pesta Rakyat UMKM & Kuliner Nusantara',
                'category' => 'Komunitas',
                'filter_category' => 'Komunitas',
                'organizer' => 'Asosiasi Kreatif Jabar',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '28 – 29 Mei 2025',
                'location' => 'Gasibu, Bandung',
                'positions' => 'Booth Coord, Logistik, Medis',
                'days_left' => 55,
                'position_count' => 25,
                'slot_label' => 'Butuh 25 Kru',
                'slot_type' => 'crew',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800',
            ],
            [
                'id' => 7,
                'title' => 'Jakarta International Book Fair 2025',
                'category' => 'Komunitas',
                'filter_category' => 'Komunitas',
                'organizer' => 'IKAPI DKI Jakarta',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '20 – 28 September 2025',
                'location' => 'JCC Senayan, Jakarta Pusat',
                'positions' => 'Booth Assistant, Informasi, Dokumentasi',
                'days_left' => 60,
                'position_count' => 8,
                'slot_label' => '8 Posisi Dibuka',
                'slot_type' => 'position',
                'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800',
            ],
            [
                'id' => 8,
                'title' => 'Bali Arts & Culture Week 2025',
                'category' => 'Festival',
                'filter_category' => 'Festival',
                'organizer' => 'Dinas Kebudayaan Bali',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '10 – 17 Juni 2025',
                'location' => 'Taman Budaya Bali, Denpasar',
                'positions' => 'Guide Wisata, Dokumentasi, Protokoler',
                'days_left' => 40,
                'position_count' => 6,
                'slot_label' => 'Sisa 6 Slot Kru',
                'slot_type' => 'slot',
                'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800',
            ],
            [
                'id' => 9,
                'title' => 'Surabaya Startup Expo 2025',
                'category' => 'Seminar',
                'filter_category' => 'Seminar',
                'organizer' => 'East Java Startup Hub',
                'is_verified' => false,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '3 – 4 Agustus 2025',
                'location' => 'Graha Pena, Surabaya',
                'positions' => 'Reception, Floor Manager, Media',
                'days_left' => 70,
                'position_count' => 4,
                'slot_label' => '4 Posisi Dibuka',
                'slot_type' => 'position',
                'image' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800',
            ],
            [
                'id' => 10,
                'title' => 'Nusantara Coffee & Food Expo 2025',
                'category' => 'Kampus',
                'filter_category' => 'Kampus',
                'organizer' => 'Kopi Kita Media Event',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '4 – 6 Juli 2025',
                'location' => 'Jogja Expo Center, Yogyakarta',
                'positions' => 'Exhibition Crew, Guest Relation',
                'days_left' => 50,
                'position_count' => 7,
                'slot_label' => '7 Posisi Dibuka',
                'slot_type' => 'position',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800',
            ],
            [
                'id' => 11,
                'title' => 'Bandung Design Week 2025',
                'category' => 'Festival',
                'filter_category' => 'Festival',
                'organizer' => 'Bandung Creative Network',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '23 – 27 Juli 2025',
                'location' => 'Gedung Sate, Bandung',
                'positions' => 'Kurator, Dokumentasi, Registrasi',
                'days_left' => 65,
                'position_count' => 9,
                'slot_label' => 'Butuh 9 Kru',
                'slot_type' => 'crew',
                'image' => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=800',
            ],
            [
                'id' => 12,
                'title' => 'Run For Earth Green Marathon 2025',
                'category' => 'Olahraga',
                'filter_category' => 'Olahraga',
                'organizer' => 'Yayasan Bumi Lestari',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '28 Juni 2025',
                'location' => 'GBK Senayan, Jakarta',
                'positions' => 'Water Marshal, Medis & First Aid, Rute Marshal',
                'days_left' => 8,
                'position_count' => 10,
                'slot_label' => 'Sisa 8 Hari',
                'slot_type' => 'urgent',
                'image' => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
            ],
            [
                'id' => 13,
                'title' => 'Makassar International Writers Festival 2025',
                'category' => 'Komunitas',
                'filter_category' => 'Komunitas',
                'organizer' => 'Yayasan La Galigo',
                'is_verified' => false,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '5 – 8 Juni 2025',
                'location' => 'Fort Rotterdam, Makassar',
                'positions' => 'Pemandu Tamu, Media, Registrasi',
                'days_left' => 75,
                'position_count' => 0,
                'slot_label' => 'Seleksi Interview',
                'slot_type' => 'interview',
                'image' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=800',
            ],
            [
                'id' => 14,
                'title' => 'Jakarta Fashion & Beauty Expo 2025',
                'category' => 'Kampus',
                'filter_category' => 'Kampus',
                'organizer' => 'Fashionlink Indonesia',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '18 – 20 Juli 2025',
                'location' => 'ICE BSD, Tangerang Selatan',
                'positions' => 'Runway Assistant, Registrasi, Dokumentasi',
                'days_left' => 58,
                'position_count' => 15,
                'slot_label' => 'Butuh 15 Kru',
                'slot_type' => 'crew',
                'image' => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800',
            ],
            [
                'id' => 15,
                'title' => 'Yogyakarta International Heritage Walk 2025',
                'category' => 'Olahraga',
                'filter_category' => 'Olahraga',
                'organizer' => 'Visit Jogja Foundation',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '15 Juni 2025',
                'location' => 'Kraton Yogyakarta, Yogyakarta',
                'positions' => 'Guide, Marshal, Medis',
                'days_left' => 7,
                'position_count' => 3,
                'slot_label' => 'Sisa 7 Hari',
                'slot_type' => 'urgent',
                'image' => 'https://images.unsplash.com/photo-1555899434-94d1368aa7af?w=800',
            ],
            [
                'id' => 16,
                'title' => 'Bandung Creative Tech Summit 2025',
                'category' => 'Seminar',
                'filter_category' => 'Seminar',
                'organizer' => 'Bandung Digital Hub',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '10 – 11 April 2025',
                'location' => 'Bandung Digital Hub, Bandung',
                'positions' => 'Exhibition Crew, Guest Relation',
                'days_left' => 30,
                'position_count' => 2,
                'slot_label' => '2 Slot Kru',
                'slot_type' => 'slot',
                'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800',
            ],
            [
                'id' => 17,
                'title' => 'Bali Beach Cleanup & Social Action 2025',
                'category' => 'Sosial',
                'filter_category' => 'Sosial',
                'organizer' => 'Ocean Guardians Bali',
                'is_verified' => true,
                'status_badge' => 'Event Informasi Saja',
                'status_badge_type' => 'info',
                'date' => '15 Juni 2025',
                'location' => 'Kuta Beach, Bali',
                'positions' => 'Koordinator Lapangan, Dokumentasi, Logistik',
                'days_left' => 33,
                'position_count' => 0,
                'slot_label' => 'Tiket Gratis (RSVP)',
                'slot_type' => 'rsvp',
                'image' => 'https://images.unsplash.com/photo-1618477247222-acbdb0e159b3?w=800',
            ],
            [
                'id' => 18,
                'title' => 'Jakarta International Java Jazz Festival 2025',
                'category' => 'Musik',
                'filter_category' => 'Musik',
                'organizer' => 'PT Java Festival Production',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '5 – 7 Maret 2025',
                'location' => 'JIExpo Kemayoran, Jakarta Pusat',
                'positions' => 'Water Marshal, Media, Route',
                'days_left' => 9,
                'position_count' => 3,
                'slot_label' => 'Sisa 9 Hari',
                'slot_type' => 'urgent',
                'image' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800',
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

                    if (! str_contains($searchable, strtolower($query))) {
                        return false;
                    }
                }

                if (
                    $category !== '' &&
                    ! in_array($category, ['Semua', 'Semua Kategori']) &&
                    ! str_contains(
                        strtolower($event['filter_category']),
                        strtolower($category)
                    ) &&
                    ! str_contains(
                        strtolower($event['category']),
                        strtolower($category)
                    )
                ) {
                    return false;
                }

                if (
                    $location !== '' &&
                    $location !== 'Semua Lokasi' &&
                    ! str_contains(
                        strtolower($event['location']),
                        strtolower($location)
                    )
                ) {
                    return false;
                }

                if (! empty($position)) {
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

                    if (! $hasPosition) {
                        return false;
                    }
                }

                if (! empty($status)) {
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

                    if (! $hasStatus) {
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
            'Seminar',
            'Sosial',
            'Kampus',
            'Komunitas',
            'Olahraga',
            'Musik',
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
