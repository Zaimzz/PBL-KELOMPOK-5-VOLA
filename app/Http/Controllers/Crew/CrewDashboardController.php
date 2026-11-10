<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CrewDashboardController extends Controller
{
    public function index(): View
    {
        $authUser = auth()->user();
        $firstName = explode(' ', $authUser->name)[0] ?? 'Crew';

        $allEvents = [
            [
                'id' => 1,
                'title' => 'Indonesia Comic & Pop Culture Con 2025',
                'category' => 'Festival',
                'filter_category' => 'festival',
                'organizer' => 'PT Pop Media Nusantara',
                'organizer_verified' => true,
                'location' => 'JCC Senayan, Jakarta',
                'date' => '14 – 16 Juni 2025',
                'deadline' => '10 Juni 2025',
                'status' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                'total_positions' => 5,
                'positions_list' => 'Liaison Officer, Usher, Stage Crew, Dokumentasi, Registrasi',
            ],
            [
                'id' => 2,
                'title' => 'Jogja International Street Festival 2025',
                'category' => 'Festival',
                'filter_category' => 'festival',
                'organizer' => 'Dinas Pariwisata DIY',
                'organizer_verified' => true,
                'location' => 'Malioboro, Yogyakarta',
                'date' => '2 – 3 Mei 2025',
                'deadline' => '25 April 2025',
                'status' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'image' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800',
                'total_positions' => 3,
                'positions_list' => 'Crowd Control, Info Desk, Medis',
            ],
            [
                'id' => 3,
                'title' => 'Surabaya Young Leaders Climate Conference',
                'category' => 'Seminar',
                'filter_category' => 'seminar',
                'organizer' => 'EcoYouth Initiative',
                'organizer_verified' => true,
                'location' => 'Grand City, Surabaya',
                'date' => '20 Mei 2025',
                'deadline' => '15 Mei 2025',
                'status' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800',
                'total_positions' => 4,
                'positions_list' => 'LO Pembicara, Notulensi, Registrasi, VIP Handler',
            ],
            [
                'id' => 4,
                'title' => 'Festival Film Dokumenter Indonesia 2025',
                'category' => 'Festival',
                'filter_category' => 'festival',
                'organizer' => 'Sinema Kolektif ID',
                'organizer_verified' => true,
                'location' => 'TIM, Jakarta Pusat',
                'date' => '1 – 5 Juli 2025',
                'deadline' => '25 Juni 2025',
                'status' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'image' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=800',
                'total_positions' => 3,
                'positions_list' => 'Screening Officer, Ticketing, Dokumentasi',
            ],
        ];

        $recommendedEvents = $allEvents;
        $featuredEvents = $allEvents;
        
        $activeApplicationsCount = 2;
        $acceptedCount = 1;
        $pendingReviewCount = 1;

        $stats = [
            'total_events' => count($allEvents),
            'applied' => 2,
            'accepted' => 1,
            'completed' => 3,
        ];

        $categories = [
            ['slug' => 'all', 'name' => 'Semua', 'count' => count($allEvents)],
            ['slug' => 'festival', 'name' => 'Festival', 'count' => count(array_filter($allEvents, fn($e) => $e['filter_category'] === 'festival'))],
            ['slug' => 'seminar', 'name' => 'Seminar', 'count' => count(array_filter($allEvents, fn($e) => $e['filter_category'] === 'seminar'))],
            ['slug' => 'musik', 'name' => 'Musik', 'count' => count(array_filter($allEvents, fn($e) => $e['filter_category'] === 'musik'))],
            ['slug' => 'olahraga', 'name' => 'Olahraga', 'count' => count(array_filter($allEvents, fn($e) => $e['filter_category'] === 'olahraga'))],
            ['slug' => 'sosial', 'name' => 'Sosial', 'count' => count(array_filter($allEvents, fn($e) => $e['filter_category'] === 'sosial'))],
        ];

        return view('crew.dashboard', compact(
            'firstName',
            'recommendedEvents',
            'featuredEvents',
            'activeApplicationsCount',
            'acceptedCount',
            'pendingReviewCount',
            'stats',
            'categories'
        ));
    }
}