<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventDetailController extends Controller
{
    public function show($event): View
    {
        // Data dummy event (nanti bisa diganti database)
        $eventData = [
            'title' => 'Indonesia Comic & Pop Culture Con 2025',
            'category' => 'MICE & Pop Culture',
            'organizer' => 'PT Pop Media Nusantara',
            'organizer_verified' => true,
            'status' => 'Open Volunteer & Crew Rekrutmen',
            'days_left' => 5,
            'date' => '14 – 16 Juni 2025',
            'date_duration' => '3 Hari Penuh Acara',
            'shift_time' => '09.00 – 21.00 WIB',
            'shift_note' => 'Termasuk Rotasi Istirahat',
            'location' => 'JCC Senayan, Hall A & B',
            'location_city' => 'Jakarta Pusat',
            'deadline' => '30 April 2025',
            'deadline_time' => 'Pukul 23:59 WIB',
            'total_positions' => 5,
            'total_quota' => 47,
            'address' => 'Jl. Gatot Subroto No.1, Gelora, Kecamatan Tanah Abang, Kota Jakarta Pusat, DKI Jakarta 10270.',
            'meeting_point' => 'Pintu loading dock kru barat seberang Gelora Bung Karno Gate 7.',
        ];

        $positions = [
            [
                'id' => 1,
                'title' => 'Liaison Officer (LO) Bintang Tamu',
                'badge' => 'Unggulan',
                'sisa_kursi' => 2,
                'description' => 'Mendampingi komikus global, cosplayer undangan internasional, dan pengisi suara sepanjang jadwal panel wawancara, workshop, dan fansign meet & greet.',
                'quota' => 8,
                'date' => '13 – 16 Juni (Briefing H-1)',
                'requirements' => ['Lancar Bahasa Inggris / Jepang'],
                'is_selected' => true,
                'capacity' => 6,
                'filled' => 8,
                'percentage' => 75,
            ],
            [
                'id' => 2,
                'title' => 'Usher & Ticketing Registrasi',
                'badge' => null,
                'sisa_kursi' => 7,
                'description' => 'Mengelola gerbang masuk tiket elektronik, verifikasi gelang tanda pengenal VIP/Reguler, serta mendistribusikan official goodie bag pameran.',
                'quota' => 15,
                'requirements' => ['Teliti & Cepat Menggunakan Scanner', 'Ramah & Berorientasi Layanan'],
                'is_selected' => false,
            ],
            [
                'id' => 3,
                'title' => 'Dokumentasi & Live Content Social Media',
                'badge' => null,
                'sisa_kursi' => 3,
                'description' => 'Mengabadikan momen seru kompetisi cosplay, live coverage ke Instagram Reels/TikTok VOLA dan Pop Con, serta kurasi foto jurnalisme harian.',
                'quota' => 6,
                'requirements' => ['Kamera Pribadi / Flagship Phone', 'Wajib Melampirkan Portofolio'],
                'is_selected' => false,
            ],
            [
                'id' => 4,
                'title' => 'Divisi Acara & Stage Management',
                'badge' => null,
                'sisa_kursi' => 4,
                'description' => 'Membantu Stage Manager mengatur antrean penampil, standby kebutuhan audio-visual, serta menjaga ketepatan rundown waktu panggung utama.',
                'quota' => 10,
                'requirements' => ['Fisik Prima & Cekatan', 'Manajemen Waktu Ketat'],
                'is_selected' => false,
            ],
            [
                'id' => 5,
                'title' => 'Konsumsi & Logistik Crew',
                'badge' => null,
                'sisa_kursi' => 3,
                'description' => 'Menjamin ketersediaan konsumsi, air mineral, hospitality rider pengisi acara, dan distribusi perlengkapan operasional seluruh kru.',
                'quota' => 8,
                'requirements' => ['Rapi dalam Pencatatan Inventaris', 'Fokus & Suportif'],
                'is_selected' => false,
            ],
        ];

        $recruitmentFlow = [
            ['step' => 1, 'title' => 'Pilih Posisi', 'desc' => 'Sesuai minat divisi'],
            ['step' => 2, 'title' => 'Ajukan Berkas', 'desc' => 'Data diri & CV'],
            ['step' => 3, 'title' => 'Seleksi Review', 'desc' => 'Verifikasi tim EO'],
            ['step' => 4, 'title' => 'Pengumuman', 'desc' => 'Notifikasi email & app'],
            ['step' => 5, 'title' => 'Briefing & Event', 'desc' => 'Onboarding H-1'],
        ];

        $benefits = [
            'Uang saku harian / transport pengganti',
            'Official Crew T-Shirt, ID badge & lanyard eksklusif',
            'E-Sertifikat resmi bertaraf nasional dari penyelenggara',
            'Konsumsi makan berat & snack box selama jam shift',
            'Akses Full Pass area pameran pada waktu rotasi istirahat',
        ];

        $requirements = [
            'Berusia minimal 18 tahun (mahasiswa atau fresh graduate dipersilakan)',
            'Berkomitmen hadir penuh selama 3 hari berturut-turut (14 – 16 Juni 2025)',
            'Bertanggung jawab, proaktif, dan dapat bekerja secara kolaboratif',
            'Wajib mengikuti technical meeting dan briefing H-1 di lokasi',
        ];

        return view('crew.event-detail', compact(
            'eventData', 'positions', 'recruitmentFlow', 'benefits', 'requirements'
        ));
    }
}