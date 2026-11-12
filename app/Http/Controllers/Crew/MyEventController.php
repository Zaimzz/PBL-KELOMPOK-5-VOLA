<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MyEventController extends Controller
{
    public function index(): View
    {
        $myEvents = [
            [
                'id' => 1,
                'event_id' => 1,
                'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'event_organizer' => 'PT Pop Media Nusantara',
                'event_category' => 'Festival',
                'crew_id' => '#VOLA-ICP-042',
                'position' => 'Liaison Officer (LO) Bintang Tamu',
                'division' => 'Divisi Talent & Guest Hospitality',
                'schedule' => '16 – 18 Mei 2025',
                'shift_time' => '09.00 – 21.00 WIB (Rotasi Istirahat)',
                'location' => 'Hall 5-8 ICE BSD City',
                'access_info' => 'Akses Pintu Gerbang Barat Relawan',
                'status' => 'accepted',
                'status_label' => 'Diterima • Terkonfirmasi',
                'whatsapp_group' => 'https://chat.whatsapp.com/example123',
                'pic_name' => 'Budi Santoso',
                'pic_role' => 'Koordinator LO & Hospitality',
                'pic_phone' => '0812-9988-XXXX',
                'pic_initials' => 'BS',
                'briefing_location' => 'Lobby Hall 5 ICE BSD',
                'briefing_address' => 'Pintu Akses Kargo / Pintu Relawan 5C',
                'briefing_city' => 'Kab. Tangerang, Banten',
                'google_maps_link' => 'https://maps.google.com/?q=ICE+BSD',
                'tasks' => [
                    'Hadir di lokasi (Holding Room) 30 menit sebelum jadwal briefing pagi dimulai.',
                    'Menjaga kerahasiaan identitas dan privasi seluruh bintang tamu dan talent pengisi acara.',
                    'Selalu aktifkan radio komunikasi dan standby berkoordinasi langsung dengan Koordinator LO.',
                    'Dress code : Celana panjang bahan/chino hitam, sepatu sneakers tertutup. Rompi panitia & ID Card resmi dibagikan saat registrasi ulang di lokasi.',
                ],
            ],
        ];

        $completedEvents = [
            [
                'id' => 1,
                'event_title' => 'Java Jazz Festival 2024',
                'event_organizer' => 'PT Java Festival Production',
                'position' => 'Stage Crew & Sound Assistant',
                'date' => '24 – 26 Mei 2024',
                'location' => 'JIExpo Kemayoran, Jakarta',
                'category' => 'Musik',
                'hours' => 30,
                'crew_id' => '#VOLA-JJF-001',
                'certificate_available' => true,
            ],
            [
                'id' => 2,
                'event_title' => 'Bandung Creative Tech Summit 2024',
                'event_organizer' => 'Bandung Digital Hub',
                'position' => 'Usher & FoH Coordinator',
                'date' => '12 – 13 Oktober 2024',
                'location' => 'Sasana Budaya Ganesha (Sabuga), Bandung',
                'category' => 'Seminar',
                'hours' => 18,
                'crew_id' => '#VOLA-BCT-002',
                'certificate_available' => true,
            ],
            [
                'id' => 3,
                'event_title' => 'Jakarta Marathon 2024',
                'event_organizer' => 'Yayasan Bumi Lestari',
                'position' => 'Water Station & Medical Marshal',
                'date' => '18 Agustus 2024',
                'location' => 'GBK Senayan, Jakarta',
                'category' => 'Olahraga',
                'hours' => 8,
                'crew_id' => '#VOLA-JKM-003',
                'certificate_available' => true,
            ],
            [
                'id' => 4,
                'event_title' => 'Indonesia Comic Con 2023',
                'event_organizer' => 'Panorama Media',
                'position' => 'Ticketing & Registration Crew',
                'date' => '4 – 5 November 2023',
                'location' => 'Jakarta Convention Center (JCC)',
                'category' => 'Festival',
                'hours' => 20,
                'crew_id' => '#VOLA-ICC-004',
                'certificate_available' => true,
            ],
            [
                'id' => 5,
                'event_title' => 'Surabaya Music Fest 2023',
                'event_organizer' => 'East Java Entertainment',
                'position' => 'Stage Crew & Sound Assistant',
                'date' => '15 – 17 September 2023',
                'location' => 'Grand City Convention Hall, Surabaya',
                'category' => 'Musik',
                'hours' => 24,
                'crew_id' => '#VOLA-SMF-005',
                'certificate_available' => true,
            ],
            [
                'id' => 6,
                'event_title' => 'AI & Future Tech Conference 2023',
                'event_organizer' => 'Nusantara Tech Community',
                'position' => 'Registration & Info Desk',
                'date' => '20 – 21 Juli 2023',
                'location' => 'Menara Mandiri, Jakarta',
                'category' => 'Seminar',
                'hours' => 14,
                'crew_id' => '#VOLA-AIF-006',
                'certificate_available' => true,
            ],
            [
                'id' => 7,
                'event_title' => 'Green Earth Run 2023',
                'event_organizer' => 'Yayasan Hijau Indonesia',
                'position' => 'Route Marshal & First Aid',
                'date' => '5 Juni 2023',
                'location' => 'Taman Monas, Jakarta',
                'category' => 'Olahraga',
                'hours' => 6,
                'crew_id' => '#VOLA-GER-007',
                'certificate_available' => true,
            ],
            [
                'id' => 8,
                'event_title' => 'Startup Summit Indonesia 2023',
                'event_organizer' => 'Indonesia Startup Forum',
                'position' => 'VIP Handler & Documentation',
                'date' => '10 – 12 Maret 2023',
                'location' => 'ICE BSD, Tangerang',
                'category' => 'Seminar',
                'hours' => 22,
                'crew_id' => '#VOLA-SSI-008',
                'certificate_available' => true,
            ],
        ];

        $counts = [
            'upcoming' => count($myEvents),
            'ongoing' => 0,
            'completed' => count($completedEvents),
        ];

        return view('crew.my-events', compact('myEvents', 'completedEvents', 'counts'));
    }
}