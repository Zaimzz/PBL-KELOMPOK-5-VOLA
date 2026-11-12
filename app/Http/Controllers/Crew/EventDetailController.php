<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class EventDetailController extends Controller
{
    public function show($event): View
    {
        $eventsData = [
            1 => [
                'eventData' => [
                    'id' => 1,
                    'title' => 'Indonesia Comic & Pop Culture Con 2025',
                    'category' => 'Festival',
                    'filter_category' => 'Festival',
                    'organizer' => 'PT Pop Media Nusantara',
                    'organizer_abbr' => 'PMN',
                    'organizer_type' => 'Penyelenggara Pameran & Festival',
                    'organizer_desc' => 'Penyelenggara pameran pop culture, festival komik, dan konvensi kreatif berskala nasional di Indonesia.',
                    'organizer_verified' => true,
                    'status' => 'Open Volunteer',
                    'status_badge' => 'Open Volunteer',
                    'status_badge_type' => 'success',
                    'days_left' => 5,
                    'date' => '14 – 16 Juni 2025',
                    'date_duration' => '3 Hari Penuh Acara',
                    'shift_time' => '09.00 – 21.00 WIB',
                    'shift_note' => 'Termasuk Rotasi Istirahat',
                    'location' => 'JCC Senayan, Jakarta',
                    'location_city' => 'Jakarta Pusat',
                    'location_short' => 'JCC Senayan',
                    'entrance' => 'Hall A & B Entrance',
                    'deadline' => '10 Juni 2025',
                    'deadline_time' => 'Pukul 23:59 WIB',
                    'total_positions' => 5,
                    'total_quota' => 47,
                    'address' => 'Jl. Gatot Subroto No.1, Gelora, Kecamatan Tanah Abang, Kota Jakarta Pusat, DKI Jakarta 10270.',
                    'meeting_point' => 'Pintu loading dock Crew barat seberang Gelora Bung Karno Gate 7.',
                    'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                ],
                'positions' => [
                    ['id' => 1, 'title' => 'Liaison Officer (LO) Bintang Tamu', 'badge' => 'Unggulan', 'sisa_kursi' => 2, 'description' => 'Mendampingi cosplayer global, artist undangan internasional, dan pengisi suara sepanjang jadwal panel wawancara dan meet & greet.', 'quota' => 8, 'date' => '13 Juni 2025 (Briefing H-1)', 'requirements' => ['Lancar Bahasa Inggris / Jepang', 'Penampilan Rapi & Ramah', 'Komunikatif'], 'is_selected' => false, 'capacity' => 6, 'filled' => 6, 'percentage' => 75],
                    ['id' => 2, 'title' => 'Usher & Floor Guide', 'badge' => null, 'sisa_kursi' => 5, 'description' => 'Memandu pengunjung ke area pameran artist alley, menjaga flow antrean tanda tangan, dan membantu info booth.', 'quota' => 10, 'requirements' => ['Komunikatif & Sopan', 'Fisik Prima'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 50],
                    ['id' => 3, 'title' => 'Stage Crew & Logistik', 'badge' => null, 'sisa_kursi' => 7, 'description' => 'Membantu persiapan panggung utama, mengatur sound check, mic wireless, serta rundown lomba cosplay.', 'quota' => 15, 'requirements' => ['Fisik Prima & Cekatan', 'Bisa Kerja Tim'], 'is_selected' => false, 'capacity' => 8, 'filled' => 8, 'percentage' => 53],
                    ['id' => 4, 'title' => 'Dokumentasi & Media', 'badge' => null, 'sisa_kursi' => 3, 'description' => 'Mengabadikan momen seru festival komik, live update media sosial, dan membuat reels recap harian.', 'quota' => 8, 'requirements' => ['Kamera Pribadi / Smartphone Flagship', 'Menguasai Canva/CapCut'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 62],
                    ['id' => 5, 'title' => 'Registrasi & Check-in', 'badge' => null, 'sisa_kursi' => 4, 'description' => 'Mengelola proses registrasi peserta tiket VIP, penukaran wristband, dan pembagian goodie bag eksklusif.', 'quota' => 8, 'requirements' => ['Teliti & Ramah', 'Menguasai Scanner QR & Excel'], 'is_selected' => false, 'capacity' => 4, 'filled' => 4, 'percentage' => 50],
                ],
            ],
            6 => [
                'eventData' => [
                    'id' => 6,
                    'title' => 'Pesta Rakyat UMKM & Kuliner Nusantara',
                    'category' => 'Komunitas',
                    'filter_category' => 'Komunitas',
                    'organizer' => 'Asosiasi Kreatif Jabar',
                    'organizer_abbr' => 'AKJ',
                    'organizer_type' => 'Asosiasi Ekonomi Kreatif',
                    'organizer_desc' => 'Asosiasi pelaku usaha mikro, kuliner tradisional, dan kriya kreatif Jawa Barat.',
                    'organizer_verified' => true,
                    'status' => 'Open Volunteer',
                    'status_badge' => 'Open Volunteer',
                    'status_badge_type' => 'success',
                    'days_left' => 55,
                    'date' => '28 – 29 Mei 2025',
                    'date_duration' => '2 Hari Penuh Acara',
                    'shift_time' => '08.00 – 22.00 WIB',
                    'shift_note' => 'Termasuk Rotasi Istirahat',
                    'location' => 'Gasibu, Bandung',
                    'location_city' => 'Bandung',
                    'location_short' => 'Gasibu',
                    'entrance' => 'Pintu Gerbang Selatan',
                    'deadline' => '20 Mei 2025',
                    'deadline_time' => 'Pukul 23:59 WIB',
                    'total_positions' => 3,
                    'total_quota' => 25,
                    'address' => 'Lapangan Gasibu, Jl. Diponegoro, Bandung 40115.',
                    'meeting_point' => 'Pintu Gerbang Selatan Gasibu, Bandung.',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800',
                ],
                'positions' => [
                    ['id' => 1, 'title' => 'Booth Coordinator & Pendamping UMKM', 'badge' => 'Unggulan', 'sisa_kursi' => 5, 'description' => 'Mendampingi lebih dari 80 stan kuliner tradisional dalam kebutuhan fasilitas listrik dan kebersihan.', 'quota' => 10, 'date' => '27 Mei 2025 (Briefing H-1)', 'requirements' => ['Komunikatif & Sigap', 'Tahan di Area Outdoor'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 50],
                    ['id' => 2, 'title' => 'Logistik & Distribusi Listrik Stand', 'badge' => null, 'sisa_kursi' => 5, 'description' => 'Membantu loading/unloading perlengkapan pameran serta mengatur inventaris meja-kursi stan.', 'quota' => 10, 'requirements' => ['Fisik Prima', 'Cekatan & Kerja Tim'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 50],
                    ['id' => 3, 'title' => 'Medis & Penanganan Cuaca', 'badge' => null, 'sisa_kursi' => 2, 'description' => 'Menyediakan pertolongan pertama bagi pengunjung dan pedagang yang mengalami keletihan.', 'quota' => 5, 'requirements' => ['Sertifikat P3K / PMI', 'Tenang Menghadapi Pasien'], 'is_selected' => false, 'capacity' => 3, 'filled' => 3, 'percentage' => 60],
                ],
            ],
            12 => [
                'eventData' => [
                    'id' => 12,
                    'title' => 'Run For Earth Green Marathon 2025',
                    'category' => 'Olahraga',
                    'filter_category' => 'Olahraga',
                    'organizer' => 'Yayasan Bumi Lestari',
                    'organizer_abbr' => 'YBL',
                    'organizer_type' => 'Yayasan Konservasi Lingkungan',
                    'organizer_desc' => 'Yayasan penggiat gaya hidup sehat ramah lingkungan dan lomba lari minim emisi karbon.',
                    'organizer_verified' => true,
                    'status' => 'Open Volunteer',
                    'status_badge' => 'Open Volunteer',
                    'status_badge_type' => 'success',
                    'days_left' => 8,
                    'date' => '28 Juni 2025',
                    'date_duration' => '1 Hari Acara',
                    'shift_time' => '05.00 – 12.00 WIB',
                    'shift_note' => 'Start Pagi Dini Hari',
                    'location' => 'GBK Senayan, Jakarta',
                    'location_city' => 'Jakarta Pusat',
                    'location_short' => 'GBK Senayan',
                    'entrance' => 'Plaza Barat GBK',
                    'deadline' => '23 Juni 2025',
                    'deadline_time' => 'Pukul 23:59 WIB',
                    'total_positions' => 3,
                    'total_quota' => 40,
                    'address' => 'Jl. Pintu Satu Senayan, Gelora, Tanah Abang, Jakarta Pusat 10270.',
                    'meeting_point' => 'Plaza Barat GBK, dekat pintu masuk utama.',
                    'image' => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
                ],
                'positions' => [
                    ['id' => 1, 'title' => 'Water Marshal & Hydration Point', 'badge' => 'Urgent', 'sisa_kursi' => 2, 'description' => 'Menjaga stasiun air di sepanjang lintasan 10K dan 21K, memastikan pelari mendapatkan hidrasi tanpa sampah berserakan.', 'quota' => 12, 'date' => '27 Juni 2025 (Briefing H-1)', 'requirements' => ['Siap Bangun Subuh (04.00 WIB)', 'Fisik Prima & Bersemangat'], 'is_selected' => false, 'capacity' => 10, 'filled' => 10, 'percentage' => 83],
                    ['id' => 2, 'title' => 'Medis & Pertolongan Pertama (PMR)', 'badge' => null, 'sisa_kursi' => 3, 'description' => 'Standby di pos kesehatan rute lari, memberi semprotan pereda kram dan penanganan darurat bagi pelari.', 'quota' => 8, 'requirements' => ['Sertifikat P3K / KSR / Mahasiswa Kesehatan', 'Tenang & Tanggap'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 62],
                    ['id' => 3, 'title' => 'Rute Marshal & Pemandu Persimpangan', 'badge' => null, 'sisa_kursi' => 5, 'description' => 'Menjaga persimpangan jalan umum bersama dishub, memastikan pelari tetap di koridor yang aman.', 'quota' => 10, 'requirements' => ['Fisik Kuat Berdiri', 'Suara Lantang & Motivatif'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 50],
                ],
            ],
            16 => [
                'eventData' => [
                    'id' => 16,
                    'title' => 'Bandung Creative Tech Summit 2025',
                    'category' => 'Seminar',
                    'filter_category' => 'Seminar',
                    'organizer' => 'Bandung Digital Hub',
                    'organizer_abbr' => 'BDH',
                    'organizer_type' => 'Inkubator Teknologi & Digital Hub',
                    'organizer_desc' => 'Pusat pengembangan talenta rekayasa perangkat lunak dan kecerdasan buatan Kota Bandung.',
                    'organizer_verified' => true,
                    'status' => 'Open Volunteer',
                    'status_badge' => 'Open Volunteer',
                    'status_badge_type' => 'success',
                    'days_left' => 30,
                    'date' => '10 – 11 April 2025',
                    'date_duration' => '2 Hari Penuh Acara',
                    'shift_time' => '08.00 – 18.00 WIB',
                    'shift_note' => 'Termasuk Rotasi Istirahat',
                    'location' => 'Sabuga ITB, Bandung',
                    'location_city' => 'Bandung',
                    'location_short' => 'Sabuga ITB',
                    'entrance' => 'Lobby Utama Sabuga ITB',
                    'deadline' => '5 April 2025',
                    'deadline_time' => 'Pukul 23:59 WIB',
                    'total_positions' => 2,
                    'total_quota' => 15,
                    'address' => 'Jl. Ir. H. Djuanda No.1, Tamansari, Coblong, Bandung 40132.',
                    'meeting_point' => 'Lobby Utama Gedung Sabuga ITB / Bandung Digital Hub.',
                    'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800',
                ],
                'positions' => [
                    ['id' => 1, 'title' => 'Exhibition Crew & Tech Demo Assistant', 'badge' => 'Unggulan', 'sisa_kursi' => 3, 'description' => 'Membantu instalasi prototipe IoT dan demo kecerdasan buatan dari berbagai startup teknologi.', 'quota' => 8, 'date' => '9 April 2025 (Briefing H-1)', 'requirements' => ['Familiar Hardware / Software Dasar', 'Komunikatif'], 'is_selected' => false, 'capacity' => 5, 'filled' => 5, 'percentage' => 62],
                    ['id' => 2, 'title' => 'Guest Relation & Keynote Stage Assistant', 'badge' => null, 'sisa_kursi' => 5, 'description' => 'Menyambut pembicara pakar teknologi internasional dan mengelola mikrofon sesi tanya jawab.', 'quota' => 7, 'requirements' => ['Lancar Bahasa Inggris', 'Penampilan Rapi'], 'is_selected' => false, 'capacity' => 2, 'filled' => 2, 'percentage' => 28],
                ],
            ],
        ];

        $eventNum = (int) $event;

        if (isset($eventsData[$eventNum])) {
            $eventData = $eventsData[$eventNum]['eventData'];
            $positions = $eventsData[$eventNum]['positions'];
        } else {
            $dbEvent = Event::with(['category', 'organizer', 'positions'])->find($eventNum);
            if ($dbEvent) {
                $categoryName = $dbEvent->category?->name ?? 'Umum';
                $eventData = [
                    'id' => $dbEvent->id,
                    'title' => $dbEvent->title,
                    'category' => $categoryName,
                    'filter_category' => $categoryName,
                    'organizer' => $dbEvent->organizer?->organization_name ?? 'Penyelenggara Event',
                    'organizer_abbr' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $dbEvent->organizer?->organization_name ?? 'EVT'), 0, 3)),
                    'organizer_type' => 'Penyelenggara Event',
                    'organizer_desc' => 'Penyelenggara resmi kegiatan kerelawanan dan event komunitas.',
                    'organizer_verified' => true,
                    'status' => 'Open Volunteer',
                    'status_badge' => 'Open Volunteer',
                    'status_badge_type' => 'success',
                    'days_left' => $dbEvent->registration_deadline ? max(0, (int) now()->diffInDays($dbEvent->registration_deadline, false)) : 7,
                    'date' => $dbEvent->start_date ? $dbEvent->start_date->translatedFormat('d F Y') : 'Segera Diumumkan',
                    'date_duration' => 'Sesuai Jadwal Penyelenggara',
                    'shift_time' => '08.00 – 17.00 WIB',
                    'shift_note' => 'Termasuk Rotasi Istirahat',
                    'location' => $dbEvent->location ?: ($dbEvent->city ?? 'Indonesia'),
                    'location_city' => $dbEvent->city ?? 'Jakarta',
                    'location_short' => $dbEvent->location ? explode(',', $dbEvent->location)[0] : 'Lokasi Event',
                    'entrance' => 'Pintu Masuk Utama',
                    'deadline' => $dbEvent->registration_deadline ? $dbEvent->registration_deadline->translatedFormat('d F Y') : 'Sebelum Acara',
                    'deadline_time' => 'Pukul 23:59 WIB',
                    'total_positions' => $dbEvent->positions->count(),
                    'total_quota' => (int) $dbEvent->positions->sum('quota'),
                    'address' => $dbEvent->location ?: 'Lokasi penyelenggaraan event resmi.',
                    'meeting_point' => 'Pintu Masuk Utama / Meja Informasi Panitia.',
                    'image' => $dbEvent->poster_path ? asset('storage/' . $dbEvent->poster_path) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                ];
                $positions = $dbEvent->positions->map(function ($pos, $index) {
                    $quota = $pos->quota ?? 5;
                    $filled = 2;
                    $pct = $quota > 0 ? (int) round(($filled / $quota) * 100) : 0;
                    return [
                        'id' => $pos->id ?? ($index + 1),
                        'title' => $pos->name,
                        'badge' => $index === 0 ? 'Unggulan' : null,
                        'sisa_kursi' => max(1, (int) ($quota - $filled)),
                        'description' => $pos->description ?: 'Bertanggung jawab dalam kelancaran operasional divisi selama kegiatan event berlangsung.',
                        'quota' => $quota,
                        'date' => 'Briefing H-1 di Lokasi Acara',
                        'requirements' => ['Komunikatif & Bersemangat', 'Bisa Bekerja Sama dalam Tim'],
                        'is_selected' => false,
                        'capacity' => $filled,
                        'filled' => $filled,
                        'percentage' => $pct,
                    ];
                })->toArray();
            } else {
                $eventData = $eventsData[1]['eventData'];
                $positions = $eventsData[1]['positions'];
            }
        }

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
            'Berkomitmen hadir penuh selama acara berlangsung',
            'Bertanggung jawab, proaktif, dan dapat bekerja secara kolaboratif',
            'Wajib mengikuti technical meeting dan briefing H-1 di lokasi',
        ];

        return view('crew.event-detail', compact(
            'eventData', 'positions', 'recruitmentFlow', 'benefits', 'requirements'
        ));
    }
}