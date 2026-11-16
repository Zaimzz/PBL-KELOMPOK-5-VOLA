<?php

namespace Database\Seeders;

use App\Models\PositionType;
use Illuminate\Database\Seeder;

class PositionTypeSeeder extends Seeder
{
    /**
     * Seed katalog posisi volunteer bawaan.
     * Memakai firstOrCreate agar perubahan katalog oleh pengelola tidak tertimpa.
     */
    public function run(): void
    {
        $order = 0;

        foreach ($this->catalog() as $category => $positions) {
            foreach ($positions as $name => $template) {
                PositionType::firstOrCreate(
                    ['name' => $name],
                    [
                        'category' => $category,
                        'default_description' => $template['description'],
                        'default_requirements' => implode("\n", $template['requirements']),
                        'is_active' => true,
                        'sort_order' => ++$order,
                    ]
                );
            }
        }
    }

    /**
     * @return array<string, array<string, array{description: string, requirements: array<int, string>}>>
     */
    private function catalog(): array
    {
        return [
            'Acara & Operasional' => [
                'Event Crew' => [
                    'description' => 'Membantu pelaksanaan operasional acara, memastikan kebutuhan kegiatan terpenuhi, serta mendukung panitia agar acara berjalan dengan lancar.',
                    'requirements' => [
                        'Mampu bekerja sama dalam tim',
                        'Komunikatif dan bertanggung jawab',
                        'Disiplin serta mampu mengikuti arahan panitia',
                        'Bersedia bekerja sesuai jadwal kegiatan',
                    ],
                ],
                'Stage Crew' => [
                    'description' => 'Membantu persiapan, pengaturan, dan pengelolaan area panggung, termasuk membantu perpindahan perlengkapan serta mendukung kelancaran kegiatan di area panggung.',
                    'requirements' => [
                        'Memiliki kemampuan kerja sama tim',
                        'Sigap dan teliti',
                        'Mampu mengikuti instruksi teknis',
                        'Bersedia bekerja dengan aktivitas fisik sesuai kebutuhan',
                    ],
                ],
                'Runner' => [
                    'description' => 'Membantu memenuhi kebutuhan operasional acara dengan mengantarkan barang, dokumen, perlengkapan, atau informasi antarbagian selama kegiatan berlangsung.',
                    'requirements' => [
                        'Cekatan dan responsif',
                        'Memiliki komunikasi yang baik',
                        'Mampu bekerja secara mandiri maupun dalam tim',
                        'Disiplin dan mampu bekerja sesuai arahan',
                    ],
                ],
                'Usher' => [
                    'description' => 'Menyambut dan mengarahkan peserta atau tamu menuju area kegiatan, membantu pengaturan tempat duduk, serta memberikan informasi terkait lokasi acara.',
                    'requirements' => [
                        'Ramah dan komunikatif',
                        'Memiliki sikap sopan dan profesional',
                        'Mampu memberikan arahan dengan jelas',
                        'Berpenampilan rapi dan disiplin',
                    ],
                ],
            ],
            'Registrasi' => [
                'Registration Staff' => [
                    'description' => 'Melayani proses registrasi peserta, melakukan pemeriksaan data pendaftaran, membantu verifikasi kehadiran, dan memastikan proses check-in berjalan tertib.',
                    'requirements' => [
                        'Teliti dalam memeriksa data',
                        'Mampu berkomunikasi dengan baik',
                        'Ramah dan bertanggung jawab',
                        'Mampu mengoperasikan perangkat digital dasar',
                    ],
                ],
                'Ticketing Staff' => [
                    'description' => 'Membantu proses pemeriksaan tiket, validasi tiket peserta, serta menangani kebutuhan administrasi tiket selama acara berlangsung.',
                    'requirements' => [
                        'Teliti dan jujur',
                        'Mampu mengoperasikan perangkat digital atau pemindai tiket jika diperlukan',
                        'Komunikatif dan ramah',
                        'Mampu bekerja secara terorganisir',
                    ],
                ],
                'Information Desk' => [
                    'description' => 'Memberikan informasi kepada peserta dan tamu mengenai jadwal, lokasi, fasilitas, serta berbagai kebutuhan informasi terkait acara.',
                    'requirements' => [
                        'Memiliki komunikasi yang baik',
                        'Menguasai informasi dasar mengenai acara',
                        'Ramah, sabar, dan responsif',
                        'Mampu memberikan informasi secara jelas',
                    ],
                ],
                'Guest Relation Officer' => [
                    'description' => 'Menyambut dan membantu kebutuhan tamu undangan, memastikan kenyamanan tamu, serta memberikan pelayanan selama acara berlangsung.',
                    'requirements' => [
                        'Memiliki kemampuan komunikasi dan interpersonal yang baik',
                        'Ramah dan berorientasi pada pelayanan',
                        'Berpenampilan rapi dan sopan',
                        'Mampu menangani kebutuhan tamu dengan baik',
                    ],
                ],
            ],
            'Publikasi & Dokumentasi' => [
                'Photographer' => [
                    'description' => 'Mengambil foto selama kegiatan berlangsung, mendokumentasikan momen penting, serta memastikan hasil dokumentasi sesuai kebutuhan publikasi dan arsip acara.',
                    'requirements' => [
                        'Memiliki kemampuan dasar fotografi',
                        'Memahami penggunaan kamera atau perangkat fotografi',
                        'Memiliki ketelitian dan kreativitas',
                        'Memiliki atau mampu mengoperasikan peralatan fotografi sesuai kebutuhan',
                    ],
                ],
                'Videographer' => [
                    'description' => 'Merekam rangkaian kegiatan acara, mengambil footage yang diperlukan, serta mendukung kebutuhan dokumentasi video untuk publikasi dan arsip acara.',
                    'requirements' => [
                        'Memiliki kemampuan dasar videografi',
                        'Memahami teknik pengambilan video',
                        'Mampu mengoperasikan kamera atau perangkat perekam',
                        'Kreatif dan teliti',
                    ],
                ],
                'Social Media Staff' => [
                    'description' => 'Mengelola kebutuhan publikasi acara melalui media sosial, membuat dan mengunggah konten, serta membantu menyampaikan informasi kegiatan kepada audiens.',
                    'requirements' => [
                        'Memahami penggunaan media sosial',
                        'Memiliki kemampuan komunikasi dan penulisan konten',
                        'Kreatif dan mengikuti tren media sosial',
                        'Mampu bekerja sesuai jadwal publikasi',
                    ],
                ],
                'Content Creator' => [
                    'description' => 'Membuat konsep dan menghasilkan konten kreatif untuk mendukung promosi serta dokumentasi acara dalam bentuk foto, video, maupun konten digital lainnya.',
                    'requirements' => [
                        'Kreatif dan memiliki ide konten',
                        'Memahami dasar pembuatan konten digital',
                        'Mampu menggunakan aplikasi editing dasar',
                        'Memahami karakteristik audiens dan platform media sosial',
                    ],
                ],
                'Design Staff' => [
                    'description' => 'Membuat materi desain visual untuk kebutuhan publikasi dan operasional acara, seperti poster, banner, konten media sosial, dan materi visual lainnya.',
                    'requirements' => [
                        'Memiliki kemampuan dasar desain grafis',
                        'Mampu menggunakan aplikasi desain seperti Canva, Figma, atau Adobe',
                        'Kreatif dan memperhatikan detail',
                        'Mampu menyesuaikan desain dengan identitas visual acara',
                    ],
                ],
            ],
            'Hubungan & Komunikasi' => [
                'Liaison Officer (LO)' => [
                    'description' => 'Menjadi penghubung antara panitia dengan tamu, pembicara, pengisi acara, atau pihak terkait lainnya serta membantu memenuhi kebutuhan mereka selama kegiatan berlangsung.',
                    'requirements' => [
                        'Memiliki kemampuan komunikasi yang baik',
                        'Ramah, sopan, dan bertanggung jawab',
                        'Mampu berkoordinasi dan bekerja secara terorganisir',
                        'Mampu menangani kebutuhan tamu dengan responsif',
                    ],
                ],
                'Public Relations' => [
                    'description' => 'Membangun hubungan baik dengan media, mitra, dan masyarakat, serta menyampaikan informasi resmi acara kepada pihak eksternal.',
                    'requirements' => [
                        'Memiliki kemampuan komunikasi yang baik',
                        'Ramah, percaya diri, dan mampu menjaga etika komunikasi',
                        'Mampu menjaga citra acara dan penyelenggara',
                        'Mampu bekerja sama dengan tim publikasi dan panitia',
                    ],
                ],
                'Sponsorship Staff' => [
                    'description' => 'Membantu pengelolaan hubungan dengan sponsor dan mitra, menyiapkan kebutuhan komunikasi, serta memastikan kewajiban publikasi kepada sponsor terpenuhi selama acara.',
                    'requirements' => [
                        'Memiliki kemampuan komunikasi dan negosiasi dasar',
                        'Rapi dalam administrasi dan dokumentasi kerja sama',
                        'Profesional dan bertanggung jawab',
                        'Mampu bekerja sesuai target dan jadwal',
                    ],
                ],
                'Talent Liaison' => [
                    'description' => 'Menjadi penghubung antara panitia dengan talent atau pengisi acara, membantu koordinasi jadwal, kebutuhan teknis, serta memastikan kesiapan talent sebelum dan selama penampilan.',
                    'requirements' => [
                        'Memiliki kemampuan komunikasi yang baik',
                        'Mampu berkoordinasi dan mengatur jadwal',
                        'Responsif dan mampu bekerja di bawah tekanan',
                        'Disiplin serta bertanggung jawab',
                    ],
                ],
            ],
            'Logistik' => [
                'Logistics Staff' => [
                    'description' => 'Membantu pengelolaan kebutuhan logistik acara, termasuk pendataan, pengaturan, distribusi, dan pengawasan perlengkapan yang digunakan selama kegiatan.',
                    'requirements' => [
                        'Teliti dan terorganisir',
                        'Mampu bekerja secara fisik sesuai kebutuhan',
                        'Bertanggung jawab terhadap perlengkapan',
                        'Mampu bekerja sama dengan tim',
                    ],
                ],
                'Consumption Staff' => [
                    'description' => 'Membantu persiapan, pendataan, pembagian, dan distribusi konsumsi kepada panitia, volunteer, tamu, maupun peserta sesuai kebutuhan acara.',
                    'requirements' => [
                        'Teliti dan bertanggung jawab',
                        'Mampu bekerja secara terorganisir',
                        'Menjaga kebersihan dan kerapian',
                        'Mampu bekerja sama dengan tim',
                    ],
                ],
                'Equipment Staff' => [
                    'description' => 'Membantu persiapan, pemeriksaan, pengaturan, distribusi, dan pengembalian peralatan yang digunakan untuk mendukung pelaksanaan acara.',
                    'requirements' => [
                        'Teliti dan bertanggung jawab',
                        'Mampu melakukan pendataan perlengkapan',
                        'Memahami instruksi penggunaan peralatan',
                        'Mampu bekerja sama dan menjaga keamanan perlengkapan',
                    ],
                ],
            ],
        ];
    }
}
