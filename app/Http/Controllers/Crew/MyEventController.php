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
                'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'organizer' => 'PT Pop Media Nusantara',
                'position' => 'Liaison Officer (LO) Bintang Tamu',
                'division' => 'Divisi Talent & Guest Hospitality',
                'schedule' => '16 – 18 Mei 2025',
                'shift_time' => '09.00 – 21.00 WIB (Rotasi Istirahat)',
                'location' => 'Hall 5-8 ICE BSD City',
                'access_info' => 'Akses Pintu Gerbang Barat Relawan',
                'crew_id' => '#VOLA-ICP-042',
                'status' => 'accepted', 
                'status_label' => 'Diterima • Terkonfirmasi',
                'whatsapp_group' => 'https://chat.whatsapp.com/example123',
                'pic_name' => 'Budi Santoso',
                'pic_role' => 'Koordinator LO & Hospitality',
                'pic_phone' => '0812-9988-XXXX',
                'briefing_location' => 'Lobby Hall 5 ICE BSD',
                'briefing_address' => 'Pintu Akses Kargo / Pintu Relawan 5C',
                'briefing_city' => 'Kab. Tangerang, Banten',
                'google_maps_link' => 'https://maps.google.com/?q=ICE+BSD',
                'tasks' => [
                    'Hadir di lokasi (Holding Room) 30 menit sebelum jadwal briefing pagi dimulai.',
                    'Menjaga kerahasiaan identitas dan privasi seluruh bintang tamu dan talent pengisi acara.',
                    'Selalu aktifkan radio komunikasi dan standby berkoordinasi langsung dengan Koordinator LO.',
                    'Dress code: Celana panjang bahan/chino hitam, sepatu sneakers tertutup. Rompi panitia & ID Card resmi dibagikan saat registrasi ulang di lokasi.',
                ],
            ],
        ];

        $counts = [
            'upcoming' => 1,
            'ongoing' => 0,
            'completed' => 8,
        ];

        return view('crew.my-events', compact('myEvents', 'counts'));
    }
}