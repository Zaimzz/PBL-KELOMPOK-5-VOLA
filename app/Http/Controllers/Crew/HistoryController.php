<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $histories = [
            [
                'id' => 1,
                'event_title' => 'Java Jazz Festival 2024',
                'organizer' => 'PT Java Festival Production',
                'position' => 'Liaison Officer (LO) Artis Internasional',
                'date' => '24 – 26 Mei 2024',
                'location' => 'JIExpo Kemayoran, Jakarta',
                'category' => 'Musik & Festival',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 2,
                'event_title' => 'Bandung Creative Tech Summit 2024',
                'organizer' => 'Bandung Digital Hub & Diskominfo Jabar',
                'position' => 'Usher & FoH Coordinator',
                'date' => '12 – 13 Oktober 2024',
                'location' => 'Sasana Budaya Ganesha (Sabuga), Bandung',
                'category' => 'Konferensi & Teknologi',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 3,
                'event_title' => 'Jakarta Marathon 2024',
                'organizer' => 'Yayasan Bumi Lestari & PASI',
                'position' => 'Water Station & Medical Marshal',
                'date' => '18 Agustus 2024',
                'location' => 'GBK Senayan, Jakarta',
                'category' => 'Sosial & Olahraga',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 4,
                'event_title' => 'Indonesia Comic Con 2023',
                'organizer' => 'Panorama Media',
                'position' => 'Ticketing & Registration Crew',
                'date' => '4 – 5 November 2023',
                'location' => 'Jakarta Convention Center (JCC)',
                'category' => 'Musik & Festival',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 5,
                'event_title' => 'Surabaya Music Fest 2023',
                'organizer' => 'East Java Entertainment',
                'position' => 'Stage Crew & Sound Assistant',
                'date' => '15 – 17 September 2023',
                'location' => 'Grand City Convention Hall, Surabaya',
                'category' => 'Musik & Festival',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 6,
                'event_title' => 'AI & Future Tech Conference 2023',
                'organizer' => 'Nusantara Tech Community',
                'position' => 'Registration & Info Desk',
                'date' => '20 – 21 Juli 2023',
                'location' => 'Menara Mandiri, Jakarta',
                'category' => 'Konferensi & Teknologi',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 7,
                'event_title' => 'Green Earth Run 2023',
                'organizer' => 'Yayasan Hijau Indonesia',
                'position' => 'Route Marshal & First Aid',
                'date' => '5 Juni 2023',
                'location' => 'Taman Monas, Jakarta',
                'category' => 'Sosial & Olahraga',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
            [
                'id' => 8,
                'event_title' => 'Startup Summit Indonesia 2023',
                'organizer' => 'Indonesia Startup Forum',
                'position' => 'VIP Handler & Documentation',
                'date' => '10 – 12 Maret 2023',
                'location' => 'ICE BSD, Tangerang',
                'category' => 'Konferensi & Teknologi',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'certificate_url' => '#',
            ],
        ];

        $counts = [
            'all' => count($histories),
            'musik' => count(array_filter($histories, fn($h) => $h['category'] === 'Musik & Festival')),
            'tech' => count(array_filter($histories, fn($h) => $h['category'] === 'Konferensi & Teknologi')),
            'olahraga' => count(array_filter($histories, fn($h) => $h['category'] === 'Sosial & Olahraga')),
        ];

        $totalCertificates = count($histories);

        return view('crew.history', compact('histories', 'counts', 'totalCertificates'));
    }
}