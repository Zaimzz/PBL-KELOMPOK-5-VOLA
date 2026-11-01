<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $applications = [
            [
                'id' => 1,
                'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'event_organizer' => 'PT Pop Media Nusantara',
                'event_category' => 'Expo & Festival',
                'position' => 'Liaison Officer (LO) Bintang Tamu',
                'location' => 'ICE BSD City, Tangerang',
                'status' => 'accepted',
                'status_label' => 'Diterima',
                'applied_at' => '24 April 2025',
                'message' => 'Selamat! Kamu lolos seleksi berkas & wawancara. Silakan cek detail di menu Event Saya.',
                'message_type' => 'success',
                'can_cancel' => false,
            ],
            [
                'id' => 2,
                'event_title' => 'Bandung Creative Tech Summit 2025',
                'event_organizer' => 'Bandung Digital Hub',
                'event_category' => 'Konferensi Teknologi',
                'position' => 'Usher & Registrasi Tamu VIP',
                'location' => 'Sasana Budaya Ganesha (Sabuga), Bandung',
                'status' => 'pending',
                'status_label' => 'Menunggu Seleksi',
                'applied_at' => '26 April 2025',
                'message' => 'Berkas telah terkirim dan sedang menunggu kurasi awal panitia.',
                'message_type' => 'warning',
                'can_cancel' => true,
            ],
            [
                'id' => 3,
                'event_title' => 'Run For Earth Green Marathon 2025',
                'event_organizer' => 'Yayasan Bumi Lestari',
                'event_category' => 'Kegiatan Sosial & Olahraga',
                'position' => 'Water Station Marshal',
                'location' => 'GBK Senayan, Jakarta',
                'status' => 'processing',
                'status_label' => 'Sedang Diproses',
                'applied_at' => '20 April 2025',
                'message' => 'Tahap 2: Wawancara online. Jadwal screening akan dikonfirmasi panitia.',
                'message_type' => 'info',
                'can_cancel' => false,
            ],
            [
                'id' => 4,
                'event_title' => 'Jakarta Culinary & Coffee Festival 2025',
                'event_organizer' => 'Jakarta Food & Hospitality Expo',
                'event_category' => 'Festival Kuliner',
                'position' => 'Stage Manager Assistant',
                'location' => 'Senayan Park, Jakarta',
                'status' => 'rejected',
                'status_label' => 'Tidak Lolos',
                'applied_at' => '18 April 2025',
                'message' => 'Kuota telah terpenuhi. Terima kasih atas partisipasi dan antusiasme.',
                'message_type' => 'error',
                'can_cancel' => false,
            ],
        ];

        return view('crew.applications', compact('applications'));
    }
}