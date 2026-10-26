<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->input('q', '');
        $category = $request->input('category', 'Semua');
        $location = $request->input('location', '');
        $position = $request->input('position', []);
        $time = $request->input('time', '');
        $status = $request->input('status', []);
        $page = $request->input('page', 1);
        $perPage = 6;

        $allEvents = [
            [
                'id' => 1,
                'title' => 'Indonesia Comic & Pop Culture Con 2025',
                'category' => 'MICE & POP CULTURE',
                'filter_category' => 'Festival',
                'organizer' => 'PT Pop Media Nusantara',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 5,
                'date' => '14 – 16 Juni 2025',
                'location' => 'JCC Senayan, Jakarta',
                'positions' => 'LO, Usher, Stage Crew, Media, Registrasi',
                'position_count' => 5,
                'image' => asset('images/dashboard-crew/tech-summit.jpg'),
            ],
            [
                'id' => 2,
                'title' => 'Jogja International Street Festival 2025',
                'category' => 'SENI & BUDAYA',
                'filter_category' => 'Festival',
                'organizer' => 'Dinas Pariwisata DIY',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 12,
                'date' => '2 – 3 Mei 2025',
                'location' => 'Malioboro, Yogyakarta',
                'positions' => 'Crowd Control, Info Booth, Media',
                'position_count' => 12,
                'image' => asset('images/dashboard-crew/java-jazz.jpg'),
            ],
            [
                'id' => 3,
                'title' => 'Surabaya Young Leaders Climate Conference 2025',
                'category' => 'SEMINAR & LINGKUNGAN',
                'filter_category' => 'Seminar',
                'organizer' => 'EcoYouth Initiative',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 20,
                'date' => '20 Mei 2025',
                'location' => 'Grand City, Surabaya',
                'positions' => 'LO, Notulensi, Registrasi, VIP',
                'position_count' => 8,
                'image' => asset('images/dashboard-crew/green-marathon.jpg'),
            ],
            [
                'id' => 4,
                'title' => 'Festival Film Dokumenter 2025',
                'category' => 'FILM & SENI',
                'filter_category' => 'Festival',
                'organizer' => 'Sinema Kolektif ID',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 30,
                'date' => '1 – 5 Juli 2025',
                'location' => 'TIM, Jakarta Pusat',
                'positions' => 'Ticketing, Screening, Usher, Dokumentasi',
                'position_count' => 5,
                'image' => asset('images/dashboard-crew/coffee-expo.jpg'),
            ],
            [
                'id' => 5,
                'title' => 'Web3 & AI Developer Summit 2025',
                'category' => 'TEKNOLOGI',
                'filter_category' => 'Seminar',
                'organizer' => 'Nusantara Dev Hub',
                'is_verified' => true,
                'status_badge' => 'Event Informasi Saja',
                'status_badge_type' => 'warning',
                'days_left' => 15,
                'date' => '12 – 14 Juni 2025',
                'location' => 'Menara Multimedia, Jakarta',
                'positions' => 'Khusus Peserta / Undangan',
                'position_count' => 0,
                'image' => asset('images/dashboard-crew/tech-summit.jpg'),
            ],
            [
                'id' => 6,
                'title' => 'Pesta Rakyat UMKM & Kuliner Nusantara 2025',
                'category' => 'KULINER & BAZAAR',
                'filter_category' => 'Kampus',
                'organizer' => 'Asosiasi Kreatif Jabar',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 25,
                'date' => '28 – 29 Mei 2025',
                'location' => 'Gasibu, Bandung',
                'positions' => 'Booth Coord, Logistik, MC, Security',
                'position_count' => 25,
                'image' => asset('images/dashboard-crew/coffee-expo.jpg'),
            ],
            [
                'id' => 7,
                'title' => 'Bandung Creative Tech Summit 2025',
                'category' => 'SEMINAR & TEKNOLOGI',
                'filter_category' => 'Seminar',
                'organizer' => 'Bandung Digital Hub',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 15,
                'date' => '12 – 13 Juni 2025',
                'location' => 'Sabuga ITB, Bandung',
                'positions' => 'Registrasi, Usher, Media',
                'position_count' => 3,
                'image' => asset('images/dashboard-crew/tech-summit.jpg'),
            ],
            [
                'id' => 8,
                'title' => 'Jakarta International Java Jazz Festival 2025',
                'category' => 'MUSIK & KONSER',
                'filter_category' => 'Festival',
                'organizer' => 'PT Java Festival Production',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 10,
                'date' => '23 – 25 Mei 2025',
                'location' => 'JIExpo Kemayoran, Jakarta',
                'positions' => 'Liaison Officer (LO), Stage Crew, Ticketing',
                'position_count' => 3,
                'image' => asset('images/dashboard-crew/java-jazz.jpg'),
            ],
            [
                'id' => 9,
                'title' => 'Run For Earth Green Marathon 2025',
                'category' => 'OLAHRAGA & SOSIAL',
                'filter_category' => 'Olahraga',
                'organizer' => 'Yayasan Bumi Lestari',
                'is_verified' => true,
                'status_badge' => 'Sisa 5 Hari',
                'status_badge_type' => 'warning',
                'days_left' => 5,
                'date' => '28 Juni 2025',
                'location' => 'GBK Senayan, Jakarta',
                'positions' => 'Water Marshal, Medis, Rute',
                'position_count' => 3,
                'image' => asset('images/dashboard-crew/green-marathon.jpg'),
            ],
            [
                'id' => 10,
                'title' => 'Nusantara Coffee & Food Expo 2025',
                'category' => 'PAMERAN & KULINER',
                'filter_category' => 'Kampus',
                'organizer' => 'Kopi Kita Media Event',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 20,
                'date' => '4 – 6 Juli 2025',
                'location' => 'Jogja Expo Center, Yogyakarta',
                'positions' => 'Exhibition Crew & Guest Relation',
                'position_count' => 2,
                'image' => asset('images/dashboard-crew/coffee-expo.jpg'),
            ],
            [
                'id' => 11,
                'title' => 'Bali International Arts Festival 2025',
                'category' => 'SENI & BUDAYA',
                'filter_category' => 'Festival',
                'organizer' => 'Dinas Kebudayaan Bali',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 40,
                'date' => '10 – 20 Juli 2025',
                'location' => 'Taman Budaya, Denpasar',
                'positions' => 'Usher, Stage Crew, Media',
                'position_count' => 15,
                'image' => asset('images/dashboard-crew/java-jazz.jpg'),
            ],
            [
                'id' => 12,
                'title' => 'Jakarta Marathon 2025',
                'category' => 'OLAHRAGA',
                'filter_category' => 'Olahraga',
                'organizer' => 'Borneo Vision',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 35,
                'date' => '15 Juli 2025',
                'location' => 'Bundaran HI, Jakarta',
                'positions' => 'Water Station, Medis, Rute',
                'position_count' => 50,
                'image' => asset('images/dashboard-crew/green-marathon.jpg'),
            ],
            [
                'id' => 13,
                'title' => 'Indonesia Education Expo 2025',
                'category' => 'SEMINAR & PENDIDIKAN',
                'filter_category' => 'Seminar',
                'organizer' => 'EduNation ID',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 22,
                'date' => '5 – 6 Juni 2025',
                'location' => 'ICE BSD, Tangerang',
                'positions' => 'Booth Assistant, Registrasi, MC',
                'position_count' => 20,
                'image' => asset('images/dashboard-crew/tech-summit.jpg'),
            ],
            [
                'id' => 14,
                'title' => 'Komunitas Peduli Sampah 2025',
                'category' => 'SOSIAL & LINGKUNGAN',
                'filter_category' => 'Sosial',
                'organizer' => 'Green Indonesia',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 18,
                'date' => '1 Juni 2025',
                'location' => 'Pantai Ancol, Jakarta',
                'positions' => 'Koordinator Lapangan, Logistik',
                'position_count' => 30,
                'image' => asset('images/dashboard-crew/green-marathon.jpg'),
            ],
            [
                'id' => 15,
                'title' => 'Kampus Merdeka Festival 2025',
                'category' => 'KAMPUS & PENDIDIKAN',
                'filter_category' => 'Kampus',
                'organizer' => 'Kemendikbudristek',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 28,
                'date' => '10 – 12 Juli 2025',
                'location' => 'Universitas Indonesia, Depok',
                'positions' => 'Panitia Acara, Dokumentasi',
                'position_count' => 40,
                'image' => asset('images/dashboard-crew/coffee-expo.jpg'),
            ],
            [
                'id' => 16,
                'title' => 'Musik Indie Fest 2025',
                'category' => 'MUSIK',
                'filter_category' => 'Festival',
                'organizer' => 'Indie Collective',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 33,
                'date' => '20 Juli 2025',
                'location' => 'Lapangan Dago, Bandung',
                'positions' => 'Stage Crew, Security, Ticketing',
                'position_count' => 25,
                'image' => asset('images/dashboard-crew/java-jazz.jpg'),
            ],
            [
                'id' => 17,
                'title' => 'Komunitas Developer Meetup 2025',
                'category' => 'TEKNOLOGI',
                'filter_category' => 'Komunitas',
                'organizer' => 'Dev Community ID',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 14,
                'date' => '25 Mei 2025',
                'location' => 'GoWork Sudirman, Jakarta',
                'positions' => 'Registrasi, Dokumentasi',
                'position_count' => 5,
                'image' => asset('images/dashboard-crew/tech-summit.jpg'),
            ],
            [
                'id' => 18,
                'title' => 'Pentas Seni Budaya Nusantara 2025',
                'category' => 'SENI & BUDAYA',
                'filter_category' => 'Komunitas',
                'organizer' => 'Sanggar Budaya',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'days_left' => 45,
                'date' => '1 – 2 Agustus 2025',
                'location' => 'Taman Ismail Marzuki, Jakarta',
                'positions' => 'Stage Crew, Usher, Dokumentasi',
                'position_count' => 15,
                'image' => asset('images/dashboard-crew/java-jazz.jpg'),
            ],
        ];

        $filteredEvents = collect($allEvents)->filter(function ($event) use ($query, $category, $location, $position, $time, $status) {
           
            if (!empty($query)) {
                $searchable = strtolower($query);
                if (!str_contains(strtolower($event['title']), $searchable)
                    && !str_contains(strtolower($event['category']), $searchable)
                    && !str_contains(strtolower($event['organizer']), $searchable)
                    && !str_contains(strtolower($event['location']), $searchable)) {
                    return false;
                }
            }

            if ($category !== 'Semua' && $event['filter_category'] !== $category) {
                return false;
            }

            if (!empty($location) && !str_contains(strtolower($event['location']), strtolower($location))) {
                return false;
            }

            if (!empty($position)) {
                $hasPosition = false;
                foreach ($position as $pos) {
                    if (str_contains(strtolower($event['positions']), strtolower($pos))) {
                        $hasPosition = true;
                        break;
                    }
                }
                if (!$hasPosition) return false;
            }

            if (!empty($status)) {
                $hasStatus = false;
                foreach ($status as $stat) {
                    if (str_contains(strtolower($event['status_badge']), strtolower($stat))) {
                        $hasStatus = true;
                        break;
                    }
                }
                if (!$hasStatus) return false;
            }

            return true;
        })->values();

        $totalEvents = $filteredEvents->count();
        $totalPages = ceil($totalEvents / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedEvents = $filteredEvents->slice($offset, $perPage)->values();

        $categories = ['Semua', 'Festival', 'Seminar', 'Sosial', 'Kampus', 'Komunitas', 'Olahraga', 'Musik'];
        $locations = ['Semua Lokasi', 'Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Bali'];

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
            'categories',
            'locations'
        ));
    }
}