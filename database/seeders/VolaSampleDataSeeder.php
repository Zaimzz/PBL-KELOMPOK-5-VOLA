<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\Payment;
use App\Models\Report;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VolaSampleDataSeeder extends Seeder
{
    /**
     * Menjalankan generator data contoh terpadu untuk seluruh modul VOLA.
     * Mengelola data Crew, EO, Event Kurasi, Event Aktif, Posisi, Lamaran, Laporan, dan Transaksi.
     */
    public function run(): void
    {
        $this->ensureSampleAssetsExist();

        // 1. Ambil data master kategori dan admin (sudah disiapkan oleh seeder pendahulu)
        $categories = EventCategory::all();
        $admin = User::where('role', UserRole::Admin)->first()
            ?? User::where('email', 'admin@vola.test')->first();

        // 2. Buat Data Relawan (Crew / Volunteer) Realistis
        $cities = ['Malang', 'Surabaya', 'Bandung', 'Jakarta Selatan', 'Yogyakarta', 'Semarang', 'Denpasar', 'Surakarta'];
        $universities = [
            'Politeknik Negeri Malang', 'Universitas Brawijaya', 'Universitas Negeri Malang',
            'Universitas Indonesia', 'Institut Teknologi Bandung', 'Universitas Gadjah Mada',
            'Universitas Airlangga', 'Universitas Diponegoro',
        ];
        $skillPool = [
            'Liaison Officer', 'Public Speaking', 'Dokumentasi & Fotografi', 'Videografi & Editing',
            'Manajemen Logistik', 'Registrasi & Ticketing', 'Pertolongan Pertama (Medis)', 'Master of Ceremony (MC)',
        ];

        $volunteerList = [
            ['name' => 'Aditya Pratama', 'email' => 'aditya.pratama@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Nabila Putri Cahyani', 'email' => 'nabila.putri@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Rizky Firmansyah', 'email' => 'rizky.firman@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Siti Rahmawati', 'email' => 'siti.rahma@yahoo.com', 'status' => UserStatus::Active],
            ['name' => 'Fajar Nugraha', 'email' => 'fajar.nugraha@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Dewi Anggraini', 'email' => 'dewi.anggraini@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Bima Sakti Yudha', 'email' => 'bima.sakti@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Anisa Maharani', 'email' => 'anisa.maharani@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Dimas Arya Putra', 'email' => 'dimas.arya@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Intan Permatasari', 'email' => 'intan.permata@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Farhan Maulana', 'email' => 'farhan.maulana@gmail.com', 'status' => UserStatus::Active],
            ['name' => 'Clarissa Aurelia', 'email' => 'clarissa.aurelia@gmail.com', 'status' => UserStatus::Active],
            // Akun Crew Ditangguhkan (Suspended) untuk pengujian filter moderasi Admin
            ['name' => 'Bayu Kusuma', 'email' => 'bayu.kusuma@gmail.com', 'status' => UserStatus::Suspended],
            ['name' => 'Tiara Melinda', 'email' => 'tiara.melinda@gmail.com', 'status' => UserStatus::Suspended],
            ['name' => 'Galih Ramadhan', 'email' => 'galih.ramadhan@gmail.com', 'status' => UserStatus::Suspended],
        ];

        $volunteers = collect();
        $volunteerProfiles = collect();

        foreach ($volunteerList as $index => $vData) {
            $createdDaysAgo = ($index % 6) * 28 + fake()->numberBetween(1, 20);
            $createdAt = now()->subDays($createdDaysAgo);

            $user = User::firstOrCreate(
                ['email' => $vData['email']],
                [
                    'name' => $vData['name'],
                    'email_verified_at' => $createdAt,
                    'password' => Hash::make('password'),
                    'phone' => '08'.fake()->numerify('##########'),
                    'avatar_path' => ($index % 2 === 0) ? 'profiles/701k5GeSiAtNwc9GY4ZsCOQBmnGyb7G1SG35VT1M.jpg' : null,
                    'role' => UserRole::Volunteer,
                    'status' => $vData['status'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            $volProfile = VolunteerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => 'Mahasiswa aktif yang berdedikasi dan memiliki antusiasme tinggi dalam kepanitiaan dan pengabdian masyarakat.',
                    'institution' => $universities[$index % count($universities)],
                    'city' => $cities[$index % count($cities)],
                    'photo_path' => ($index % 2 === 0) ? 'profiles/701k5GeSiAtNwc9GY4ZsCOQBmnGyb7G1SG35VT1M.jpg' : null,
                    'skills' => array_slice($skillPool, $index % 4, 3),
                    'experience' => 'Berpengalaman menjadi relawan di 3 event tingkat regional dengan fokus pada divisi operasional lapangan.',
                    'cv_path' => 'documents/cangkang1.jpg',
                    'portfolio_path' => 'documents/cangkang1.jpg',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            $volunteers->push($user);
            $volunteerProfiles->push($volProfile);
        }

        // 3. Buat Data Event Organizer (EO) Realistis
        $eoDataset = [
            // Status: Pending (Menunggu Verifikasi) - 10 EO
            [
                'org_name' => 'Yayasan Lentera Edukasi Nusantara', 'type' => 'Yayasan', 'city' => 'Malang',
                'pic' => 'Dr. H. Hendra Wijaya', 'phone' => '081234112233', 'email' => 'info@lenteraedukasi.org',
                'status' => VerificationStatus::Pending, 'nib' => '9120003418291', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Komunitas Kreatif Arema Muda', 'type' => 'Komunitas', 'city' => 'Malang',
                'pic' => 'Ahmad Fauzi', 'phone' => '081333445566', 'email' => 'kontak@aremamuda.id',
                'status' => VerificationStatus::Pending, 'nib' => '8120309481723', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'PT Harmoni Citra Kreasi', 'type' => 'Perusahaan', 'city' => 'Surabaya',
                'pic' => 'Eko Prasetyo, S.E.', 'phone' => '081298765432', 'email' => 'admin@harmonicitra.co.id',
                'status' => VerificationStatus::Pending, 'nib' => '0220108392019', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'BEM Fakultas Ilmu Komputer Polinema', 'type' => 'Kampus', 'city' => 'Malang',
                'pic' => 'Rian Sanjaya', 'phone' => '085712345678', 'email' => 'bemfik@polinema.ac.id',
                'status' => VerificationStatus::Pending, 'nib' => '1920048392102', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Lingkar Hijau Bumi Indonesia', 'type' => 'Komunitas', 'city' => 'Bandung',
                'pic' => 'Gita Saraswati', 'phone' => '087812908765', 'email' => 'relawan@lingkarhijau.org',
                'status' => VerificationStatus::Pending, 'nib' => '9120938491024', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'PT Panggung Musik Indonesia', 'type' => 'Perusahaan', 'city' => 'Jakarta Selatan',
                'pic' => 'Dicky Chandra', 'phone' => '081190283741', 'email' => 'production@panggungmusik.com',
                'status' => VerificationStatus::Pending, 'nib' => '0120938410293', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Yayasan Sahabat Anak Bangsa', 'type' => 'Yayasan', 'city' => 'Yogyakarta',
                'pic' => 'Nurul Hidayati, S.Sos.', 'phone' => '081387654321', 'email' => 'halo@sahabatanak.or.id',
                'status' => VerificationStatus::Pending, 'nib' => '9120384910294', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Himpunan Mahasiswa Informatika Brawijaya', 'type' => 'Kampus', 'city' => 'Malang',
                'pic' => 'Kevin Alamsyah', 'phone' => '085698765432', 'email' => 'himaif@ub.ac.id',
                'status' => VerificationStatus::Pending, 'nib' => '1920038492011', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Semarang Creative Hub', 'type' => 'Komunitas', 'city' => 'Semarang',
                'pic' => 'Bagus Wicaksono', 'phone' => '081290817263', 'email' => 'info@semarangcreative.id',
                'status' => VerificationStatus::Pending, 'nib' => '8120938491028', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Bali Event Production Network', 'type' => 'Perusahaan', 'city' => 'Denpasar',
                'pic' => 'Wayan Sudarta', 'phone' => '081987654321', 'email' => 'contact@balieventpro.com',
                'status' => VerificationStatus::Pending, 'nib' => '0220938491022', 'user_status' => UserStatus::Active,
            ],

            // Status: Verified (Terverifikasi) - 10 EO
            [
                'org_name' => 'PT Sinergi Mahakarya Nusantara', 'type' => 'Perusahaan', 'city' => 'Jakarta Selatan',
                'pic' => 'Indra Gunawan', 'phone' => '081211112222', 'email' => 'corporate@sinergikarya.co.id',
                'status' => VerificationStatus::Verified, 'nib' => '0120004928192', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Komunitas Relawan Muda Surabaya', 'type' => 'Komunitas', 'city' => 'Surabaya',
                'pic' => 'Mega Anggun', 'phone' => '081333334444', 'email' => 'halo@relawansurabaya.id',
                'status' => VerificationStatus::Verified, 'nib' => '8120005928193', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Yayasan Peduli Sehat Indonesia', 'type' => 'Yayasan', 'city' => 'Malang',
                'pic' => 'dr. Wahyu Pratama', 'phone' => '081244445555', 'email' => 'sekretariat@pedulisehat.or.id',
                'status' => VerificationStatus::Verified, 'nib' => '9120006928194', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'BEM Institut Teknologi Sepuluh Nopember', 'type' => 'Kampus', 'city' => 'Surabaya',
                'pic' => 'Faris Zulkarnain', 'phone' => '085755556666', 'email' => 'bem@its.ac.id',
                'status' => VerificationStatus::Verified, 'nib' => '1920007928195', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Jogja Festival Management', 'type' => 'Perusahaan', 'city' => 'Yogyakarta',
                'pic' => 'Agung Laksono', 'phone' => '081866667777', 'email' => 'info@jogjafestival.com',
                'status' => VerificationStatus::Verified, 'nib' => '0220008928196', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Komunitas Literasi Buku Keliling', 'type' => 'Komunitas', 'city' => 'Bandung',
                'pic' => 'Sri Wahyuni', 'phone' => '081377778888', 'email' => 'redaksi@bukukeliling.org',
                'status' => VerificationStatus::Verified, 'nib' => '8120009928197', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'PT Suara Musik Khatulistiwa', 'type' => 'Perusahaan', 'city' => 'Surabaya',
                'pic' => 'Rangga Danendra', 'phone' => '081288889999', 'email' => 'event@suaramusik.co.id',
                'status' => VerificationStatus::Verified, 'nib' => '0120010928198', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Solo Creative Community Forum', 'type' => 'Komunitas', 'city' => 'Surakarta',
                'pic' => 'Haryo Dananjaya', 'phone' => '081999990000', 'email' => 'forum@solocreative.id',
                'status' => VerificationStatus::Verified, 'nib' => '8120011928199', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'Yayasan Kasih Difabel Mandiri', 'type' => 'Yayasan', 'city' => 'Malang',
                'pic' => 'Ratna Dewi, S.Pd.', 'phone' => '081200001111', 'email' => 'kontak@kasihdifabel.or.id',
                'status' => VerificationStatus::Verified, 'nib' => '9120012928200', 'user_status' => UserStatus::Active,
            ],
            [
                'org_name' => 'PT Spektrum Inspirasi Bangsa', 'type' => 'Perusahaan', 'city' => 'Jakarta Selatan',
                'pic' => 'Bambang Supriyanto', 'phone' => '081211223344', 'email' => 'business@spektruminspirasi.com',
                'status' => VerificationStatus::Verified, 'nib' => '0120013928201', 'user_status' => UserStatus::Active,
            ],

            // Status: Rejected (Ditolak) - 5 EO
            [
                'org_name' => 'Komunitas Event Malang Ceria', 'type' => 'Komunitas', 'city' => 'Malang',
                'pic' => 'Rudi Hermawan', 'phone' => '085711223344', 'email' => 'ceria@malangevent.my.id',
                'status' => VerificationStatus::Rejected, 'nib' => '8120014928202', 'user_status' => UserStatus::Active,
                'rejection_reason' => 'Nomor Induk Berusaha (NIB) tidak terdaftar di sistem OSS dan dokumen legalitas identitas PIC buram.',
            ],
            [
                'org_name' => 'CV Karya Utama Production', 'type' => 'Perusahaan', 'city' => 'Surabaya',
                'pic' => 'Hendra Setiawan', 'phone' => '081222334455', 'email' => 'karyautama@surabaya-pro.com',
                'status' => VerificationStatus::Rejected, 'nib' => '0220015928203', 'user_status' => UserStatus::Active,
                'rejection_reason' => 'Alamat kantor operasional fiktif dan penanggung jawab tidak dapat dihubungi melalui telepon.',
            ],
            [
                'org_name' => 'Forum Pemuda Mandiri Bersatu', 'type' => 'Komunitas', 'city' => 'Bandung',
                'pic' => 'Taufik Hidayat', 'phone' => '087833445566', 'email' => 'sekretariat@pemudamandiri.org',
                'status' => VerificationStatus::Rejected, 'nib' => '8120016928204', 'user_status' => UserStatus::Active,
                'rejection_reason' => 'Struktur organisasi dan AD/ART lembaga tidak disertakan dalam lampiran pendaftaran.',
            ],
            // Akun EO Ditangguhkan (Suspended) untuk pengujian filter moderasi Admin
            [
                'org_name' => 'PT Gelora Kreasi Nusantara', 'type' => 'Perusahaan', 'city' => 'Jakarta Selatan',
                'pic' => 'Doni Siregar', 'phone' => '081144556677', 'email' => 'legal@gelorakreasi.co.id',
                'status' => VerificationStatus::Verified, 'nib' => '0120017928205', 'user_status' => UserStatus::Suspended,
            ],
            [
                'org_name' => 'Komunitas Indie Musika Malang', 'type' => 'Komunitas', 'city' => 'Malang',
                'pic' => 'Gilang Pratista', 'phone' => '081355667788', 'email' => 'management@indiemusik.id',
                'status' => VerificationStatus::Rejected, 'nib' => '8120018928206', 'user_status' => UserStatus::Suspended,
                'rejection_reason' => 'Terdapat laporan pelanggaran hak cipta dan pelanggaran komitmen pembagian honor crew.',
            ],
        ];

        $eoProfiles = collect();
        foreach ($eoDataset as $i => $eoData) {
            $createdDaysAgo = ($i % 6) * 25 + fake()->numberBetween(1, 15);
            $createdAt = now()->subDays($createdDaysAgo);

            $user = User::firstOrCreate(
                ['email' => $eoData['email']],
                [
                    'name' => $eoData['pic'],
                    'email_verified_at' => $createdAt,
                    'password' => Hash::make('password'),
                    'phone' => $eoData['phone'],
                    'avatar_path' => ($i % 2 === 0) ? 'profiles/701k5GeSiAtNwc9GY4ZsCOQBmnGyb7G1SG35VT1M.jpg' : null,
                    'role' => UserRole::Eo,
                    'status' => $eoData['user_status'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            $profile = OrganizerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_name' => $eoData['org_name'],
                    'organization_type' => $eoData['type'],
                    'description' => 'Organisasi profesional yang bergerak di bidang penyelenggaraan kegiatan sosial, edukasi, dan festival kepemudaan tingkat nasional.',
                    'city' => $eoData['city'],
                    'pic_name' => $eoData['pic'],
                    'pic_phone' => $eoData['phone'],
                    'social_link' => 'https://instagram.com/'.Str::slug($eoData['org_name']),
                    'document_path' => ($i % 2 === 0) ? 'documents/cangkang1.jpg' : 'documents/legalitas-sample.pdf',
                    'verification_status' => $eoData['status'],
                    'verified_by' => $eoData['status'] === VerificationStatus::Verified ? $admin?->id : null,
                    'verified_at' => $eoData['status'] === VerificationStatus::Verified ? $createdAt->copy()->addDays(1) : null,
                    'rejection_reason' => $eoData['rejection_reason'] ?? null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            $eoProfiles->push($profile);
        }

        $verifiedEos = $eoProfiles->where('verification_status', VerificationStatus::Verified);

        // 4. Buat Event Kurasi (Review Event) - 30 Event
        $curatedEventTitles = [
            // Pending Review (12 Event)
            ['title' => 'Malang Youth Innovation Summit 2026', 'cat' => 'Seminar', 'status' => EventStatus::PendingReview, 'city' => 'Malang'],
            ['title' => 'Festival Musik Senja Nusantara', 'cat' => 'Musik', 'status' => EventStatus::PendingReview, 'city' => 'Surabaya'],
            ['title' => 'Gerakan Bersih Pantai Kenjeran Bersama', 'cat' => 'Sosial', 'status' => EventStatus::PendingReview, 'city' => 'Surabaya'],
            ['title' => 'Turnamen Bola Basket Pelajar Se-Jawa Timur', 'cat' => 'Olahraga', 'status' => EventStatus::PendingReview, 'city' => 'Malang'],
            ['title' => 'Pameran Seni Rupa Modern Ruang & Waktu', 'cat' => 'Komunitas', 'status' => EventStatus::PendingReview, 'city' => 'Yogyakarta'],
            ['title' => 'Simposium Nasional Kecerdasan Buatan & Karir', 'cat' => 'Kampus', 'status' => EventStatus::PendingReview, 'city' => 'Bandung'],
            ['title' => 'Festival Kuliner Warisan Rasa Nusantara', 'cat' => 'Festival', 'status' => EventStatus::PendingReview, 'city' => 'Jakarta Selatan'],
            ['title' => 'Workshop UI/UX Design & Portofolio Review', 'cat' => 'Komunitas', 'status' => EventStatus::PendingReview, 'city' => 'Malang'],
            ['title' => 'Aksi Tanam 10.000 Mangrove Pesisir Utara', 'cat' => 'Sosial', 'status' => EventStatus::PendingReview, 'city' => 'Semarang'],
            ['title' => 'Pentas Teater Mahasiswa Lintas Kampus', 'cat' => 'Kampus', 'status' => EventStatus::PendingReview, 'city' => 'Surakarta'],
            ['title' => 'Maraton Relawan Kemanusiaan 10K Surabaya', 'cat' => 'Olahraga', 'status' => EventStatus::PendingReview, 'city' => 'Surabaya'],
            ['title' => 'Konferensi Pemimpin Komunitas Muda Indonesia', 'cat' => 'Seminar', 'status' => EventStatus::PendingReview, 'city' => 'Jakarta Selatan'],

            // Rejected (8 Event)
            ['title' => 'Pesta Musik Underground Malam Malang', 'cat' => 'Musik', 'status' => EventStatus::Rejected, 'city' => 'Malang', 'reason' => 'Izin keramaian kepolisian belum terlampir dan jadwal selesai melebihi batas jam malam kota.'],
            ['title' => 'Seminar Skema Cepat Sukses Finansial', 'cat' => 'Seminar', 'status' => EventStatus::Rejected, 'city' => 'Surabaya', 'reason' => 'Materi acara terindikasi skema promosi investasi tidak berizin resmi dari OJK.'],
            ['title' => 'Turnamen E-Sport Amatir Tanpa Protokol', 'cat' => 'Olahraga', 'status' => EventStatus::Rejected, 'city' => 'Bandung', 'reason' => 'Rincian alokasi hadiah dan pembagian uang saku crew tidak transparan.'],
            ['title' => 'Bakti Sosial Pengobatan Alternatif Keliling', 'cat' => 'Sosial', 'status' => EventStatus::Rejected, 'city' => 'Yogyakarta', 'reason' => 'Tenaga medis yang dilibatkan tidak memiliki Surat Tanda Registrasi (STR) resmi.'],
            ['title' => 'Bazar Malam Kampus Lintas Generasi', 'cat' => 'Kampus', 'status' => EventStatus::Rejected, 'city' => 'Semarang', 'reason' => 'Surat rekomendasi persetujuan dari dekanat fakultas belum ditandatangani.'],
            ['title' => 'Festival Komedi Tunggal Mahasiswa 2026', 'cat' => 'Festival', 'status' => EventStatus::Rejected, 'city' => 'Malang', 'reason' => 'Paket deskripsi jobdesk crew tidak memenuhi standar keselamatan kerja panggung.'],
            ['title' => 'Kopdar Otomotif & Pameran Modifikasi', 'cat' => 'Komunitas', 'status' => EventStatus::Rejected, 'city' => 'Surabaya', 'reason' => 'Lokasi acara berada di jalan umum tanpa izin penutupan jalan dari dinas perhubungan.'],
            ['title' => 'Workshop Trading Harian Bergaransi', 'cat' => 'Seminar', 'status' => EventStatus::Rejected, 'city' => 'Jakarta Selatan', 'reason' => 'Konten workshop tidak sesuai dengan pedoman kurasi edukasi platform VOLA.'],

            // Finished / Disetujui (10 Event)
            ['title' => 'Pekan Olahraga Mahasiswa Jawa Timur 2026', 'cat' => 'Olahraga', 'status' => EventStatus::Finished, 'city' => 'Surabaya'],
            ['title' => 'Festival Teater Nusantara Jogja Istimewa', 'cat' => 'Festival', 'status' => EventStatus::Finished, 'city' => 'Yogyakarta'],
            ['title' => 'Seminar Transformasi Digital UMKM Malang Raya', 'cat' => 'Seminar', 'status' => EventStatus::Finished, 'city' => 'Malang'],
            ['title' => 'Bakti Sosial Senyum Anak Yatim Piatu', 'cat' => 'Sosial', 'status' => EventStatus::Finished, 'city' => 'Bandung'],
            ['title' => 'Konferensi Pengembang Web Terbuka Indonesia', 'cat' => 'Kampus', 'status' => EventStatus::Finished, 'city' => 'Malang'],
            ['title' => 'Simfoni Musik Klasik Gedung Kesenian', 'cat' => 'Musik', 'status' => EventStatus::Finished, 'city' => 'Jakarta Selatan'],
            ['title' => 'Workshop Penulisan Karya Jurnalistik Muda', 'cat' => 'Komunitas', 'status' => EventStatus::Finished, 'city' => 'Surakarta'],
            ['title' => 'Pameran Fotografi Pusaka Budaya Nusantara', 'cat' => 'Komunitas', 'status' => EventStatus::Finished, 'city' => 'Semarang'],
            ['title' => 'Festival Sains dan Teknologi Siswa SMA', 'cat' => 'Festival', 'status' => EventStatus::Finished, 'city' => 'Denpasar'],
            ['title' => 'Donor Darah Akbar Peduli Kemanusiaan', 'cat' => 'Sosial', 'status' => EventStatus::Finished, 'city' => 'Malang'],
        ];

        $allCuratedEvents = collect();
        foreach ($curatedEventTitles as $cIdx => $cItem) {
            $cat = $categories->firstWhere('name', $cItem['cat']) ?? $categories->first();
            $eo = $verifiedEos->values()->get($cIdx % $verifiedEos->count());
            $startDate = now()->addDays(15 + $cIdx * 3);

            $ev = Event::firstOrCreate(
                ['slug' => Str::slug($cItem['title'])],
                [
                    'organizer_id' => $eo->id,
                    'category_id' => $cat->id,
                    'title' => $cItem['title'],
                    'description' => 'Event kolaborasi berskala besar yang membuka kesempatan bagi generasi muda untuk berkontribusi secara langsung dalam tim operasional.',
                    'poster_path' => ($cIdx % 2 === 0) ? 'posters/wlPPMC4NhaPWiMahkWBrEu7fjsb0kRvJoZUeo93S.jpg' : null,
                    'location' => 'Gedung Kesenian dan Balai Pertemuan Kota '.$cItem['city'],
                    'city' => $cItem['city'],
                    'start_date' => $startDate,
                    'end_date' => $startDate->copy()->addDays(2),
                    'start_time' => '08:00',
                    'end_time' => '17:00',
                    'registration_deadline' => $startDate->copy()->subDays(5),
                    'contact_info' => $eo->pic_phone,
                    'pic_name' => $eo->pic_name,
                    'pic_phone' => $eo->pic_phone,
                    'benefits' => ['Sertifikat Resmi Penyelenggara', 'Konsumsi & Uang Transport', 'Rompi & Merchandise Panitia', 'Jejaring Komunitas'],
                    'status' => $cItem['status'],
                    'rejection_reason' => $cItem['reason'] ?? null,
                    'reviewed_by' => in_array($cItem['status'], [EventStatus::Finished, EventStatus::Rejected]) ? $admin?->id : null,
                    'reviewed_at' => in_array($cItem['status'], [EventStatus::Finished, EventStatus::Rejected]) ? now()->subDays(10) : null,
                    'published_at' => $cItem['status'] === EventStatus::Finished ? now()->subDays(10) : null,
                ]
            );
            $allCuratedEvents->push($ev);
        }

        // 5. Buat Event Aktif (20 Event) dan Posisi Kepanitiaan (60 Posisi)
        $activeEventsList = [
            ['title' => 'Malang Jazz & Arts Festival 2026', 'cat' => 'Musik', 'city' => 'Malang', 'days_ahead' => 20],
            ['title' => 'Hackathon Inovasi Digital Mahasiswa Jawa Timur', 'cat' => 'Kampus', 'city' => 'Surabaya', 'days_ahead' => 15],
            ['title' => 'Aksi Tanam Seribu Pohon Lereng Gunung Bromo', 'cat' => 'Sosial', 'city' => 'Malang', 'days_ahead' => 10],
            ['title' => 'Surabaya Heritage Run & Walk Marathon 2026', 'cat' => 'Olahraga', 'city' => 'Surabaya', 'days_ahead' => 25],
            ['title' => 'Pameran Startup Teknologi & Kreatif Nusantara', 'cat' => 'Seminar', 'city' => 'Jakarta Selatan', 'days_ahead' => 18],
            ['title' => 'Festival Tari Tradisional Kreasi Anak Bangsa', 'cat' => 'Festival', 'city' => 'Yogyakarta', 'days_ahead' => 12],
            ['title' => 'Bandung Book Fair & Literasi Komunitas', 'cat' => 'Komunitas', 'city' => 'Bandung', 'days_ahead' => 22],
            ['title' => 'Bakti Sosial Cek Kesehatan & Gigi Gratis Lansia', 'cat' => 'Sosial', 'city' => 'Malang', 'days_ahead' => 8],
            ['title' => 'Pesta Kopi Nusantara & Barista Championship', 'cat' => 'Festival', 'city' => 'Semarang', 'days_ahead' => 30],
            ['title' => 'Simposium AI for Climate Change Indonesia', 'cat' => 'Seminar', 'city' => 'Jakarta Selatan', 'days_ahead' => 14],
            ['title' => 'Turnamen Bulutangkis Piala Rektor Cup 2026', 'cat' => 'Olahraga', 'city' => 'Surabaya', 'days_ahead' => 16],
            ['title' => 'Konser Amal Kolaborasi Suara Harapan', 'cat' => 'Musik', 'city' => 'Bandung', 'days_ahead' => 28],
            ['title' => 'Workshop Pembuatan Film Pendek Indie', 'cat' => 'Komunitas', 'city' => 'Yogyakarta', 'days_ahead' => 9],
            ['title' => 'Olimpiade Sains Terapan Nasional Polinema', 'cat' => 'Kampus', 'city' => 'Malang', 'days_ahead' => 21],
            ['title' => 'Denpasar Eco-Art Festival & Beach Clean-Up', 'cat' => 'Sosial', 'city' => 'Denpasar', 'days_ahead' => 17],
            ['title' => 'Pameran Robotika dan Otomasi Industri 2026', 'cat' => 'Kampus', 'city' => 'Surabaya', 'days_ahead' => 24],
            ['title' => 'Festival Teater Tradisional Wayang Wong Remaja', 'cat' => 'Festival', 'city' => 'Surakarta', 'days_ahead' => 19],
            ['title' => 'Piala Futsal Mahasiswa Nasional Cup IV', 'cat' => 'Olahraga', 'city' => 'Malang', 'days_ahead' => 26],
            ['title' => 'Seminar Nasional Kesehatan Mental Gen-Z', 'cat' => 'Seminar', 'city' => 'Malang', 'days_ahead' => 11],
            ['title' => 'Parade Musik Akustik Kampus Merdeka', 'cat' => 'Musik', 'city' => 'Bandung', 'days_ahead' => 23],
        ];

        $activeEvents = collect();
        $allPositions = collect();

        foreach ($activeEventsList as $aIdx => $aItem) {
            $cat = $categories->firstWhere('name', $aItem['cat']) ?? $categories->first();
            $eo = $verifiedEos->values()->get($aIdx % $verifiedEos->count());
            $startDate = now()->addDays($aItem['days_ahead']);

            $ev = Event::firstOrCreate(
                ['slug' => Str::slug($aItem['title'])],
                [
                    'organizer_id' => $eo->id,
                    'category_id' => $cat->id,
                    'title' => $aItem['title'],
                    'description' => 'Kegiatan tahunan unggulan yang menghadirkan ribuan peserta serta membutuhkan relawan muda penuh semangat untuk mengisi posisi strategis operasional acara.',
                    'poster_path' => ($aIdx % 2 === 0) ? 'posters/wlPPMC4NhaPWiMahkWBrEu7fjsb0kRvJoZUeo93S.jpg' : null,
                    'location' => 'Pusat Konvensi dan Gedung Serbaguna '.$aItem['city'],
                    'city' => $aItem['city'],
                    'start_date' => $startDate,
                    'end_date' => $startDate->copy()->addDays(2),
                    'start_time' => '07:30',
                    'end_time' => '18:00',
                    'registration_deadline' => $startDate->copy()->subDays(3),
                    'contact_info' => $eo->pic_phone,
                    'pic_name' => $eo->pic_name,
                    'pic_phone' => $eo->pic_phone,
                    'benefits' => ['Sertifikat Resmi Bertaraf Nasional', 'Konsumsi Lengkap 3 Hari', 'Uang Saku Harian', 'Official Merchandise Panitia'],
                    'status' => EventStatus::Active,
                    'rejection_reason' => null,
                    'reviewed_by' => $admin?->id,
                    'reviewed_at' => now()->subDays(14),
                    'published_at' => now()->subDays(14),
                ]
            );

            // Buat 3 Posisi per Event Aktif
            $positionsList = [
                ['name' => 'Liaison Officer (LO) VVIP', 'quota' => 5],
                ['name' => 'Koordinator Registrasi & Ticketing', 'quota' => 8],
                ['name' => 'Tim Dokumentasi & Multimedia', 'quota' => 4],
            ];
            foreach ($positionsList as $posData) {
                $pos = EventPosition::firstOrCreate(
                    [
                        'event_id' => $ev->id,
                        'name' => $posData['name'],
                    ],
                    [
                        'description' => 'Bertanggung jawab dalam pengelolaan alur tamu dan kelancaran divisi terkait selama acara berlangsung.',
                        'quota' => $posData['quota'],
                        'requirements' => 'Komunikasi aktif, mampu bekerja sama dalam tim, disiplin, dan bersedia mengikuti pembekalan panitia.',
                    ]
                );
                $allPositions->push($pos);
            }

            $activeEvents->push($ev);
        }

        // 6. Buat Data Lamaran Relawan (Applications)
        // Menghubungkan profil Crew ke posisi-posisi event aktif secara konsisten
        // Mematuhi constraint: UNIQUE (volunteer_profile_id, position_id)
        $applicationMotivations = [
            'Saya memiliki pengalaman 2 tahun di bidang kepanitiaan festival kampus dan siap berkontribusi secara maksimal di divisi ini.',
            'Tertarik untuk mengasah keterampilan public speaking dan berinteraksi langsung dengan pengisi acara dan tamu VVIP.',
            'Memiliki perlengkapan kamera profesional dan portofolio dokumentasi event yang siap mendukung kelancaran publikasi acara.',
            'Ingin memperluas jejaring profesional di bidang industri kreatif dan manajemen kegiatan berskala nasional.',
            'Memiliki sertifikasi pertolongan pertama (P3K) dan siap bertugas menjaga standar kesehatan serta keselamatan kerja acara.',
        ];

        // Buat 35 data lamaran dengan status variatif: accepted, pending, rejected
        $createdApps = 0;
        $targetApps = 35;
        $activeVolProfiles = $volunteerProfiles->take(12); // Hanya ambil volunteer berstatus aktif
        $candidatePositions = $allPositions->shuffle();

        foreach ($candidatePositions as $pos) {
            if ($createdApps >= $targetApps) {
                break;
            }

            // Pilih 1 s/d 2 volunteer per posisi
            $volunteersForPos = $activeVolProfiles->random(min(2, $activeVolProfiles->count()));

            foreach ($volunteersForPos as $volProf) {
                if ($createdApps >= $targetApps) {
                    break;
                }

                // Tentukan status: mayoritas diterima (accepted) untuk menghidupkan metrik & progress bar Admin
                // 1-22: accepted, 23-30: pending, 31-35: rejected
                if ($createdApps < 22) {
                    $status = ApplicationStatus::Accepted;
                    $appliedDaysAgo = fake()->numberBetween(10, 25);
                    $appliedAt = now()->subDays($appliedDaysAgo);
                    $decidedAt = $appliedAt->copy()->addDays(2);
                    $decisionNote = 'Selamat! Profil, pengalaman organisasi, dan keterampilan Anda sesuai dengan kualifikasi tim pelaksana.';
                } elseif ($createdApps < 30) {
                    $status = ApplicationStatus::Pending;
                    $appliedDaysAgo = fake()->numberBetween(1, 5);
                    $appliedAt = now()->subDays($appliedDaysAgo);
                    $decidedAt = null;
                    $decisionNote = null;
                } else {
                    $status = ApplicationStatus::Rejected;
                    $appliedDaysAgo = fake()->numberBetween(10, 20);
                    $appliedAt = now()->subDays($appliedDaysAgo);
                    $decidedAt = $appliedAt->copy()->addDays(3);
                    $decisionNote = 'Terima kasih atas antusiasme Anda. Saat ini kuota divisi telah terpenuhi oleh kandidat lain.';
                }

                Application::firstOrCreate(
                    [
                        'volunteer_profile_id' => $volProf->id,
                        'position_id' => $pos->id,
                    ],
                    [
                        'cover_letter' => $applicationMotivations[$createdApps % count($applicationMotivations)],
                        'status' => $status,
                        'applied_at' => $appliedAt,
                        'decided_at' => $decidedAt,
                        'decision_note' => $decisionNote,
                        'created_at' => $appliedAt,
                        'updated_at' => $decidedAt ?? $appliedAt,
                    ]
                );

                $createdApps++;
            }
        }

        // 7. Buat Laporan Pelanggaran (Reports) - 25 Tiket
        $reportReasons = [
            [
                'reason' => 'Ketidaksesuaian Pembagian Tugas di Lapangan',
                'desc' => 'Jobdesk yang diberikan di lapangan sangat berbeda dengan apa yang tercantum di deskripsi event. Crew diminta melakukan pekerjaan konstruksi panggung berat tanpa alat pelindung.',
                'status' => ReportStatus::Pending,
            ],
            [
                'reason' => 'Penyelenggara Tidak Dapat Dihubungi Menjelang Acara',
                'desc' => 'PIC event tidak merespons panggilan telepon dan pesan grup WhatsApp koordinasi sejak H-2 acara, menyebabkan relawan terlantar di lokasi pertemuan.',
                'status' => ReportStatus::Pending,
            ],
            [
                'reason' => 'Keterlambatan Pembayaran Uang Saku & Transportasi',
                'desc' => 'Uang saku yang dijanjikan cair maksimal H+1 setelah acara selesai hingga saat ini belum ditransfer dan penyelenggara tidak memberikan kepastian tanggal.',
                'status' => ReportStatus::InReview,
                'notes' => 'Admin telah mengirimkan surat teguran klarifikasi ke pihak penyelenggara terkait bukti transfer operasional.',
            ],
            [
                'reason' => 'Pelecehan Verbal atau Perilaku Tidak Pantas',
                'desc' => 'Salah satu koordinator lapangan melontarkan kata-kata merendahkan dan bentakan kasar yang tidak profesional kepada relawan divisi ticketing.',
                'status' => ReportStatus::InReview,
                'notes' => 'Sedang dilakukan wawancara terpisah kepada saksi mata dan pihak terlapor.',
            ],
            [
                'reason' => 'Pembatalan Partisipasi Mendadak Tanpa Alasan Jelas',
                'desc' => 'Relawan membatalkan kehadiran secara sepihak 2 jam sebelum acara dimulai tanpa alasan darurat yang valid, mengganggu alur registrasi.',
                'status' => ReportStatus::Resolved,
                'action' => 'Peringatan Tertulis & Pembekuan Akun Sementara',
                'notes' => 'Relawan telah diberikan sanksi peringatan keras sesuai aturan kode etik VOLA.',
            ],
            [
                'reason' => 'Ketidaksesuaian Fasilitas dan Konsumsi yang Dijanjikan',
                'desc' => 'Konsumsi makan siang yang dijanjikan pada deskripsi event tidak disediakan oleh EO selama 2 hari berturut-turut.',
                'status' => ReportStatus::Resolved,
                'action' => 'Mediasi Berhasil & Penggantian Biaya Konsumsi',
                'notes' => 'Penyelenggara telah mentransfer biaya pengganti makan siang kepada seluruh crew yang bertugas.',
            ],
            [
                'reason' => 'Pelanggaran Standar Keamanan & Keselamatan Kerja',
                'desc' => 'Kabel bertegangan tinggi di area panggung dibiarkan terbuka di tengah genangan air tanpa pengaman.',
                'status' => ReportStatus::Rejected,
                'action' => 'Laporan Ditolak',
                'notes' => 'Setelah dilakukan kroscek foto dan video pengawasan venue, instalasi listrik telah memenuhi SOP PLN.',
            ],
            [
                'reason' => 'Penyalahgunaan Data Pribadi Crew Event',
                'desc' => 'Nomor WhatsApp dan identitas relawan digunakan oleh pihak ketiga untuk penawaran produk asuransi komersial tanpa izin.',
                'status' => ReportStatus::Pending,
            ],
            [
                'reason' => 'Jam Kerja Melebihi Batas Kesepakatan',
                'desc' => 'Relawan diwajibkan lembur hingga pukul 02.00 dini hari padahal di perjanjian awal waktu kerja berakhir pukul 20.00 malam.',
                'status' => ReportStatus::InReview,
                'notes' => 'Menunggu tanggapan resmi dari perwakilan PIC event.',
            ],
            [
                'reason' => 'Sertifikat Relawan Tidak Diterbitkan Sesuai Janji',
                'desc' => 'Acara telah selesai 1 bulan yang lalu tetapi e-sertifikat yang dijanjikan tidak kunjung dikirimkan.',
                'status' => ReportStatus::Pending,
            ],
        ];

        for ($r = 1; $r <= 25; $r++) {
            $ticketNumber = sprintf('RPT-%s-%04d', now()->format('Ym'), $r);
            $template = $reportReasons[($r - 1) % count($reportReasons)];

            if ($r % 3 === 0) {
                $reporter = $eoProfiles->random()->user;
                $reported = $volunteers->random();
            } else {
                $reporter = $volunteers->random();
                $reported = $eoProfiles->random()->user;
            }

            $isResolvedOrRejected = in_array($template['status'], [ReportStatus::Resolved, ReportStatus::Rejected]);

            Report::firstOrCreate(
                ['ticket_number' => $ticketNumber],
                [
                    'reporter_id' => $reporter->id,
                    'reported_id' => $reported->id,
                    'reason' => $template['reason'],
                    'description' => $template['desc'].' [Nomor referensi tiket: '.$ticketNumber.']',
                    'evidence_path' => 'documents/cangkang1.jpg',
                    'status' => $template['status'],
                    'action_taken' => $template['action'] ?? null,
                    'admin_notes' => $template['notes'] ?? null,
                    'resolved_by' => $isResolvedOrRejected ? $admin?->id : null,
                    'resolved_at' => $isResolvedOrRejected ? now()->subDays(fake()->numberBetween(1, 14)) : null,
                    'created_at' => now()->subDays(fake()->numberBetween(1, 45)),
                    'updated_at' => now()->subDays(fake()->numberBetween(0, 10)),
                ]
            );
        }

        // 8. Buat Transaksi Pembayaran (Payments) - 26 Transaksi
        $allActiveAndCurated = $activeEvents->concat($allCuratedEvents);

        $transactionScenarios = [
            // Transaksi Bulan Ini
            ['days_ago' => 2, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],
            ['days_ago' => 5, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'bank_transfer'],
            ['days_ago' => 7, 'status' => PaymentStatus::Pending, 'pkg' => 'standard', 'amount' => 35000, 'type' => null],
            ['days_ago' => 9, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'bca_va'],
            ['days_ago' => 12, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],
            ['days_ago' => 14, 'status' => PaymentStatus::Pending, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => null],
            ['days_ago' => 18, 'status' => PaymentStatus::Paid, 'pkg' => 'standard', 'amount' => 35000, 'type' => 'mandiri_bill'],
            ['days_ago' => 20, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],

            // Transaksi 2 - 3 Bulan Lalu
            ['days_ago' => 45, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'bank_transfer'],
            ['days_ago' => 60, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],
            ['days_ago' => 75, 'status' => PaymentStatus::Pending, 'pkg' => 'standard', 'amount' => 35000, 'type' => null],
            ['days_ago' => 80, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'bca_va'],
            ['days_ago' => 88, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'qris'],

            // Transaksi 4 - 6 Bulan Lalu
            ['days_ago' => 110, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],
            ['days_ago' => 130, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'mandiri_bill'],
            ['days_ago' => 150, 'status' => PaymentStatus::Paid, 'pkg' => 'standard', 'amount' => 35000, 'type' => 'bca_va'],
            ['days_ago' => 165, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],
            ['days_ago' => 175, 'status' => PaymentStatus::Pending, 'pkg' => 'standard', 'amount' => 35000, 'type' => null],

            // Transaksi 7 - 11 Bulan Lalu
            ['days_ago' => 210, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'bank_transfer'],
            ['days_ago' => 250, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris'],
            ['days_ago' => 300, 'status' => PaymentStatus::Paid, 'pkg' => 'standard', 'amount' => 35000, 'type' => 'bca_va'],

            // Transaksi 1+ TAHUN LALU (Tahun 2025) untuk menguji filter rentang waktu Admin
            ['days_ago' => 380, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'bank_transfer', 'year' => 2025],
            ['days_ago' => 400, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris', 'year' => 2025],
            ['days_ago' => 420, 'status' => PaymentStatus::Paid, 'pkg' => 'standard', 'amount' => 35000, 'type' => 'bca_va', 'year' => 2025],
            ['days_ago' => 450, 'status' => PaymentStatus::Paid, 'pkg' => 'professional', 'amount' => 85000, 'type' => 'qris', 'year' => 2025],
            ['days_ago' => 480, 'status' => PaymentStatus::Paid, 'pkg' => 'enterprise', 'amount' => 150000, 'type' => 'mandiri_bill', 'year' => 2025],
        ];

        foreach ($transactionScenarios as $tIndex => $tItem) {
            $event = $allActiveAndCurated->values()->get($tIndex % $allActiveAndCurated->count());
            $createdDate = now()->subDays($tItem['days_ago']);
            $year = $tItem['year'] ?? $createdDate->year;
            $invNumber = sprintf('INV-%d-%04d', $year, $tIndex + 101);

            Payment::firstOrCreate(
                ['invoice_number' => $invNumber],
                [
                    'event_id' => $event->id,
                    'package_name' => $tItem['pkg'],
                    'package_duration_days' => $tItem['pkg'] === 'enterprise' ? 14 : ($tItem['pkg'] === 'professional' ? 7 : 3),
                    'midtrans_order_id' => 'ORDER-'.$year.'-'.Str::upper(Str::random(10)),
                    'snap_token' => 'snap-token-'.$invNumber,
                    'transaction_id' => $tItem['status'] === PaymentStatus::Paid ? Str::uuid()->toString() : null,
                    'payment_type' => $tItem['type'],
                    'amount' => $tItem['amount'],
                    'status' => $tItem['status'],
                    'paid_at' => $tItem['status'] === PaymentStatus::Paid ? $createdDate->copy()->addMinutes(15) : null,
                    'expired_at' => $createdDate->copy()->addHours(24),
                    'payload' => [
                        'payment_type' => $tItem['type'] ?? 'qris',
                        'transaction_time' => $createdDate->format('Y-m-d H:i:s'),
                    ],
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ]
            );
        }
    }

    /**
     * Memastikan berkas dummy pada disk public tersedia jika berjalan di lingkungan lokal.
     */
    private function ensureSampleAssetsExist(): void
    {
        try {
            $publicDisk = Storage::disk('public');
            if (! $publicDisk->exists('documents/legalitas-sample.pdf')) {
                $publicDisk->makeDirectory('documents');
                $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n4 0 obj\n<< /Length 110 >>\nstream\nBT\n/F1 18 Tf\n50 720 Td\n(DOKUMEN LEGALITAS PENYELENGGARA - VOLA) Tj\nET\nendstream\nendobj\n5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000234 00000 n \n0000000395 00000 n \ntrailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n465\n%%EOF";
                $publicDisk->put('documents/legalitas-sample.pdf', $pdfContent);
            }

            if (! $publicDisk->exists('documents/cangkang1.jpg') && extension_loaded('gd')) {
                $publicDisk->makeDirectory('documents');
                $im = imagecreatetruecolor(800, 1100);
                $bg = imagecolorallocate($im, 248, 250, 252);
                imagefilledrectangle($im, 0, 0, 800, 1100, $bg);
                $indigo = imagecolorallocate($im, 79, 70, 229);
                $dark = imagecolorallocate($im, 15, 23, 42);
                imagestring($im, 5, 40, 50, 'SURAT KEPUTUSAN LEGALITAS PENYELENGGARA EVENT', $indigo);
                imagestring($im, 4, 40, 90, 'Nomor Izin Berusaha (NIB): 0120004928192', $dark);
                imagestring($im, 3, 40, 130, 'Dokumen contoh verifikasi identitas dan legalitas kemitraan VOLA.', $dark);
                ob_start();
                imagejpeg($im, null, 90);
                $imgData = ob_get_clean();
                $publicDisk->put('documents/cangkang1.jpg', $imgData);
            }

            // Pastikan dokumen legalitas FEST. dan momenara juga tersedia di storage
            if (! $publicDisk->exists('organizer-documents/3oQs2aUy1mQujx2xdEg2FBlVbjQraj4bwBAg3j7q.jpg')) {
                if ($publicDisk->exists('documents/cangkang1.jpg')) {
                    $publicDisk->put('organizer-documents/3oQs2aUy1mQujx2xdEg2FBlVbjQraj4bwBAg3j7q.jpg', $publicDisk->get('documents/cangkang1.jpg'));
                }
            }

            if (! $publicDisk->exists('organizer-documents/QcyFkC7UrALxRz78N3089LXrOR5rCiubyDQAPUfS.pdf')) {
                if ($publicDisk->exists('documents/legalitas-sample.pdf')) {
                    $publicDisk->put('organizer-documents/QcyFkC7UrALxRz78N3089LXrOR5rCiubyDQAPUfS.pdf', $publicDisk->get('documents/legalitas-sample.pdf'));
                }
            }

            if (! $publicDisk->exists('reports/bukti-pelanggaran-1.jpg')) {
                $publicDisk->makeDirectory('reports');
                if (extension_loaded('gd')) {
                    for ($i = 1; $i <= 25; $i++) {
                        $im = imagecreatetruecolor(600, 400);
                        $bg = imagecolorallocate($im, 15, 23, 42);
                        imagefilledrectangle($im, 0, 0, 600, 400, $bg);
                        $rose = imagecolorallocate($im, 244, 63, 94);
                        $white = imagecolorallocate($im, 255, 255, 255);
                        imagestring($im, 5, 20, 20, 'BUKTI PELAPORAN VOLA #'.$i, $rose);
                        imagestring($im, 3, 20, 60, 'Lampiran bukti pelaporan digital terverifikasi.', $white);
                        ob_start();
                        imagejpeg($im, null, 85);
                        $data = ob_get_clean();
                        $publicDisk->put('reports/bukti-pelanggaran-'.$i.'.jpg', $data);
                    }
                }
            }
        } catch (\Throwable) {
            // Abaikan jika penyimpanan cloud / s3 tidak mengizinkan write tanpa credentials
        }
    }
}
