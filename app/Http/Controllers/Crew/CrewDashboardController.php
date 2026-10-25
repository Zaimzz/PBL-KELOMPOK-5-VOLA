<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CrewDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $userName = $user?->name ?? 'Crew';
        $firstName = explode(' ', trim($userName))[0];

        // Data dummy metrik untuk badge sidebar
        $activeApplicationsCount = 3;
        $pendingReviewCount = 2;
        $acceptedCount = 1;

        $categories = ['Semua', 'Festival', 'Seminar', 'Sosial', 'Kampus', 'Komunitas', 'Olahraga'];

        // 4 Event untuk Beranda dengan gambar lokal
        $recommendedEvents = [
            [
                'id' => 1,
                'title' => 'Bandung Creative Tech Summit 2025',
                'category' => 'Seminar & Tech',
                'filter_category' => 'Seminar',
                'organizer' => 'Bandung Digital Hub',
                'is_verified' => true,
                'status_badge' => 'Pendaftaran Buka',
                'status_badge_type' => 'success',
                'date' => '12 – 13 Jun 2025',
                'location' => 'Sabuga ITB, Bandung',
                'positions' => 'Registrasi, Usher, Media',
                'image' => asset('images/dashboard-crew/tech-summit.jpg'),
            ],
            [
                'id' => 2,
                'title' => 'Jakarta International Java Jazz Festival 2025',
                'category' => 'Festival Musik',
                'filter_category' => 'Festival',
                'organizer' => 'PT Java Festival Production',
                'is_verified' => true,
                'status_badge' => 'Pendaftaran Buka',
                'status_badge_type' => 'success',
                'date' => '23 – 25 Mei 2025',
                'location' => 'JIExpo Kemayoran, Jakarta',
                'positions' => 'Liaison Officer (LO), Stage Crew, Ticketing',
                'image' => asset('images/dashboard-crew/java-jazz.jpg'),
            ],
            [
                'id' => 3,
                'title' => 'Run For Earth Green Marathon 2025',
                'category' => 'Olahraga & Sosial',
                'filter_category' => 'Olahraga',
                'organizer' => 'Yayasan Bumi Lestari',
                'is_verified' => true,
                'status_badge' => 'Sisa 5 Hari',
                'status_badge_type' => 'warning',
                'date' => '28 Jun 2025',
                'location' => 'GBK Senayan, Jakarta',
                'positions' => 'Water Marshal, Medis, Rute',
                'image' => asset('images/dashboard-crew/green-marathon.jpg'),
            ],
            [
                'id' => 4,
                'title' => 'Nusantara Coffee & Food Expo 2025',
                'category' => 'Pameran & Kuliner',
                'filter_category' => 'Kampus',
                'organizer' => 'Kopi Kita Media Event',
                'is_verified' => true,
                'status_badge' => 'Pendaftaran Buka',
                'status_badge_type' => 'success',
                'date' => '4 – 6 Jul 2025',
                'location' => 'Jogja Expo Center, Yogyakarta',
                'positions' => 'Exhibition Crew & Guest Relation',
                'image' => asset('images/dashboard-crew/coffee-expo.jpg'),
            ],
        ];

        return view('crew.dashboard', compact(
            'user',
            'profile',
            'firstName',
            'activeApplicationsCount',
            'pendingReviewCount',
            'acceptedCount',
            'categories',
            'recommendedEvents'
        ));
    }
}
