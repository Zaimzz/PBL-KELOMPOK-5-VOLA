<?php

/*
|--------------------------------------------------------------------------
| Rekomendasi Posisi Manual
|--------------------------------------------------------------------------
|
| Dipakai untuk menyarankan deskripsi dan kualifikasi ketika EO mengetik
| nama posisi manual yang tidak ada di katalog. Pencocokan memakai kata
| kunci utuh (tidak peka huruf besar/kecil). Jika tidak ada yang cocok,
| tidak ada rekomendasi yang ditampilkan.
|
*/

return [
    [
        'keywords' => ['video editor', 'editor video', 'video editing'],
        'description' => 'Mengolah dan menyunting materi video dokumentasi atau promosi acara menjadi konten video yang siap dipublikasikan.',
        'requirements' => [
            'Memahami teknik dasar video editing',
            'Mampu menggunakan aplikasi penyuntingan video',
            'Kreatif dan teliti',
            'Mampu bekerja sesuai tenggat waktu',
        ],
    ],
    [
        'keywords' => ['stage manager'],
        'description' => 'Mengoordinasikan jalannya rundown di area panggung, memastikan pengisi acara dan kru panggung siap sesuai jadwal.',
        'requirements' => [
            'Memiliki kemampuan koordinasi dan komunikasi yang baik',
            'Tenang dan sigap saat menghadapi perubahan jadwal',
            'Memahami alur rundown acara',
            'Disiplin dan bertanggung jawab',
        ],
    ],
    [
        'keywords' => ['interpreter', 'translator', 'penerjemah'],
        'description' => 'Membantu komunikasi antara panitia, pembicara, atau tamu yang menggunakan bahasa berbeda selama acara berlangsung.',
        'requirements' => [
            'Menguasai bahasa asing yang dibutuhkan secara lisan maupun tulisan',
            'Memiliki komunikasi yang jelas dan percaya diri',
            'Mampu menjaga kerahasiaan informasi acara',
            'Responsif dan bertanggung jawab',
        ],
    ],
    [
        'keywords' => ['mc', 'emcee', 'master of ceremony', 'pembawa acara'],
        'description' => 'Memandu jalannya acara di atas panggung sesuai rundown, menyapa audiens, serta menjaga suasana acara tetap hidup.',
        'requirements' => [
            'Percaya diri berbicara di depan umum',
            'Memiliki artikulasi dan intonasi yang jelas',
            'Mampu beradaptasi dengan situasi di lapangan',
            'Berpenampilan rapi dan sopan',
        ],
    ],
    [
        'keywords' => ['moderator'],
        'description' => 'Memandu sesi diskusi atau seminar, mengatur alur tanya jawab, dan menjaga waktu sesi sesuai rundown.',
        'requirements' => [
            'Memiliki kemampuan komunikasi dan memandu diskusi yang baik',
            'Memahami topik acara',
            'Mampu mengelola waktu dengan baik',
            'Netral dan profesional',
        ],
    ],
    [
        'keywords' => ['sound', 'audio', 'lighting', 'multimedia'],
        'description' => 'Membantu pengoperasian dan pengawasan perangkat teknis acara seperti suara, pencahayaan, atau tampilan layar selama kegiatan berlangsung.',
        'requirements' => [
            'Memahami dasar pengoperasian perangkat teknis terkait',
            'Teliti dan sigap menangani kendala teknis',
            'Mampu mengikuti instruksi teknis',
            'Bersedia hadir lebih awal untuk persiapan',
        ],
    ],
    [
        'keywords' => ['medis', 'medic', 'first aid', 'p3k'],
        'description' => 'Memberikan pertolongan pertama dan memantau kondisi kesehatan peserta serta panitia selama acara berlangsung.',
        'requirements' => [
            'Memiliki pengetahuan dasar pertolongan pertama',
            'Tenang dan cepat tanggap dalam situasi darurat',
            'Bersedia siaga selama acara berlangsung',
            'Bertanggung jawab dan teliti',
        ],
    ],
    [
        'keywords' => ['security', 'keamanan'],
        'description' => 'Membantu menjaga ketertiban dan keamanan area acara serta mengarahkan alur peserta agar tetap tertib.',
        'requirements' => [
            'Disiplin dan waspada',
            'Mampu berkomunikasi dengan tegas namun sopan',
            'Bersedia berjaga sesuai jadwal',
            'Mampu bekerja sama dengan tim',
        ],
    ],
    [
        'keywords' => ['driver', 'transport', 'transportasi'],
        'description' => 'Membantu mobilitas tamu, pengisi acara, atau perlengkapan acara sesuai jadwal yang ditentukan panitia.',
        'requirements' => [
            'Memiliki SIM yang masih berlaku sesuai kendaraan',
            'Mengenal area lokasi acara',
            'Disiplin waktu dan bertanggung jawab',
            'Mengutamakan keselamatan berkendara',
        ],
    ],
    [
        'keywords' => ['dekorasi', 'decoration', 'dekor'],
        'description' => 'Membantu menyiapkan, memasang, dan merapikan dekorasi area acara sesuai konsep yang telah ditentukan.',
        'requirements' => [
            'Kreatif dan memperhatikan detail',
            'Mampu bekerja sama dengan tim',
            'Bersedia melakukan aktivitas fisik ringan hingga sedang',
            'Mampu mengikuti arahan konsep dekorasi',
        ],
    ],
    [
        'keywords' => ['notulen', 'notulis', 'sekretaris', 'secretary'],
        'description' => 'Mencatat jalannya rapat atau kegiatan acara serta membantu administrasi dan dokumentasi tertulis panitia.',
        'requirements' => [
            'Teliti dan rapi dalam mencatat',
            'Memiliki kemampuan menulis yang baik',
            'Mampu mengelola dokumen dengan terorganisir',
            'Bertanggung jawab dan menjaga kerahasiaan informasi',
        ],
    ],
];
