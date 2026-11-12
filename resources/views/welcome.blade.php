
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>VOLA — Temukan Event, Bangun Pengalamanmu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="vola-landing">

    <header class="vola-navbar">
        <div class="vola-container vola-nav-inner">           
            <a href="{{ route('home') }}#beranda"
            class="vola-brand"
            aria-label="VOLA - Beranda">
                VOLA<span>.</span>
            </a>

            <nav class="vola-nav-links" id="volaNavLinks">
                <a href="#beranda" class="is-active">Beranda</a>
                <a href="#event">Cari Event</a>
                <a href="#tentang">Tentang Kami</a>
            </nav>

            <div class="vola-nav-actions">
                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="vola-btn vola-btn-primary vola-btn-small">
                        Profil Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="vola-login-link">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="vola-btn vola-btn-primary vola-btn-small">
                        Daftar
                    </a>
                @endauth
            </div>

            <button type="button"
                    class="vola-menu-toggle"
                    id="volaMenuToggle"
                    aria-label="Buka menu"
                    aria-expanded="false"
                    aria-controls="volaNavLinks">
                ☰
            </button>
        </div>
    </header>

    <main>
        <section class="vola-hero" id="beranda">
            <div class="vola-container vola-hero-grid">

                <div class="vola-hero-content">
                    <span class="vola-eyebrow">
                        <span aria-hidden="true">✦</span>
                        Find. Join. Contribute.
                    </span>

                    <h1>
                        Temukan Event,<br>
                        Bangun <span>Pengalamanmu.</span>
                    </h1>

                    <p class="vola-hero-description">
                        Platform yang menghubungkan mahasiswa dengan peluang
                        volunteer event, serta membantu Event Organizer
                        menemukan kru lapangan terbaik.
                    </p>

                    <div class="vola-hero-actions">
                        <a href="#event"
                           class="vola-btn vola-btn-primary">
                            Cari Event Sekarang <span>→</span>
                        </a>

                        <a href="{{ route('register') }}"
                           class="vola-btn vola-btn-outline">
                            <span>⊕</span> Buat Event (Untuk EO)
                        </a>
                    </div>

                    <div class="vola-stats">
                        <div class="vola-stat">
                            <strong>50+ Event</strong>
                            <span>Kampus</span>
                        </div>

                        <div class="vola-stat">
                            <strong>350+ Relawan</strong>
                            <span>Aktif</span>
                        </div>

                        <div class="vola-stat">
                            <strong>100% Gratis</strong>
                            <span>Mahasiswa</span>
                        </div>
                    </div>
                </div>

                <div class="vola-hero-visual">
                    <img
                        src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=1100&q=85"
                        alt="Sekelompok anak muda dalam kegiatan bersama"
                        fetchpriority="high"
                    >

                    <div class="vola-photo-caption">
                        <span>
                            <span class="vola-status-dot">●</span>
                            Komunitas &amp; Relawan Lapangan
                        </span>
                        <span class="vola-verified">VOLA Community</span>
                    </div>
                </div>

            </div>
        </section>

        
<!-- HOW VOLA WORKS -->
<section class="vola-how" id="tentang">
    <div class="vola-container">

        <div class="vola-section-heading">
            <span class="vola-section-label">
                LANGKAH SEDERHANA
            </span>

            <h2>Bagaimana VOLA Bekerja</h2>

            <p>
                Proses sederhana dan transparan, baik untuk kamu
                yang ingin menambah pengalaman kepanitiaan maupun
                mengelola kru acara.
            </p>
        </div>

        <div class="vola-steps-grid">

            <!-- CARD VOLUNTEER -->
            <article class="vola-steps-card">
                <div class="vola-steps-header">
                    <div class="vola-role-icon">♡</div>

                    <div>
                        <span class="vola-role-kicker">
                            PERAN RELAWAN
                        </span>
                        <h3>Volunteer &amp; Kru</h3>
                    </div>

                    <span class="vola-role-badge">Gratis</span>
                </div>

                <div class="vola-step-list">
                    <div class="vola-step-item">
                        <span class="vola-step-number">1</span>
                        <div>
                            <h4>Cari Event Sesuai Minat</h4>
                            <p>
                                Temukan peluang volunteer berdasarkan
                                kategori, kota, jadwal, dan minatmu.
                            </p>
                        </div>
                    </div>

                    <div class="vola-step-item">
                        <span class="vola-step-number">2</span>
                        <div>
                            <h4>Lamar Posisi Lapangan</h4>
                            <p>
                                Pelajari deskripsi dan persyaratan posisi,
                                lalu ajukan lamaran sesuai kualifikasimu.
                            </p>
                        </div>
                    </div>

                    <div class="vola-step-item">
                        <span class="vola-step-number">3</span>
                        <div>
                            <h4>Raih Pengalaman &amp; Relasi</h4>
                            <p>
                                Ikuti proses seleksi dan dapatkan pengalaman
                                baru bersama penyelenggara acara.
                            </p>
                        </div>
                    </div>
                </div>

                <a href="#event" class="vola-steps-link">
                    Eksplorasi peluang volunteer →
                </a>
            </article>

            <!-- CARD EVENT ORGANIZER -->
            <article class="vola-steps-card">
                <div class="vola-steps-header">
                    <div class="vola-role-icon is-green">⚑</div>

                    <div>
                        <span class="vola-role-kicker is-green">
                            PENYELENGGARA
                        </span>
                        <h3>Event Organizer</h3>
                    </div>

                    <span class="vola-role-badge is-green">
                        EO &amp; Panitia
                    </span>
                </div>

                <div class="vola-step-list">
                    <div class="vola-step-item">
                        <span class="vola-step-number">1</span>
                        <div>
                            <h4>Lengkapi Profil Organisasi</h4>
                            <p>
                                Isi informasi organisasi dan dokumen
                                legalitas untuk proses verifikasi.
                            </p>
                        </div>
                    </div>

                    <div class="vola-step-item">
                        <span class="vola-step-number">2</span>
                        <div>
                            <h4>Publikasikan Kebutuhan Kru</h4>
                            <p>
                                Buat event, tentukan posisi, kuota,
                                persyaratan, dan informasi pendaftaran.
                            </p>
                        </div>
                    </div>

                    <div class="vola-step-item">
                        <span class="vola-step-number">3</span>
                        <div>
                            <h4>Kelola Rekrutmen Event</h4>
                            <p>
                                Pantau kebutuhan acara dan kelola
                                informasi rekrutmen melalui dashboard EO.
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('register') }}"
                   class="vola-steps-link is-green">
                    Mulai sebagai penyelenggara →
                </a>
            </article>

        </div>
    </div>
</section>


<!-- EVENT PILIHAN -->
<section class="vola-events" id="event">
    <div class="vola-container">

        <div class="vola-events-heading">
            <div>
                <span class="vola-section-label">
                    DIREKTORI EVENT
                </span>

                <h2>Event Pilihan &amp; Terbuka</h2>

                <p>
                    Temukan peluang volunteer dan pengalaman baru
                    bersama komunitas serta penyelenggara acara.
                </p>
            </div>

            <a href="{{ route('login') }}"
               class="vola-btn vola-btn-outline">
                Lihat Semua Event →
            </a>
        </div>

    <div class="vola-event-grid">
        @forelse ($events as $event)
            <article class="vola-event-card">
                <div class="vola-event-tags">
                    <span class="vola-event-category">
                        {{ $event->category?->name ?? 'Event' }}
                    </span>

                    <span class="vola-open-tag">
                        ● Open Volunteer
                    </span>
                </div>

                <h3>{{ $event->title }}</h3>

                <p class="vola-event-organizer">
                    {{ $event->organizer?->organization_name ?? 'Penyelenggara VOLA' }}
                </p>

                <div class="vola-event-info">
                    <p>⌖ {{ $event->city ?: $event->location }}</p>

                    <p>
                        ▦
                        {{ $event->start_date
                            ? $event->start_date->translatedFormat('d M Y')
                            : 'Jadwal belum tersedia' }}
                    </p>
                </div>

                <div class="vola-event-benefit">
                    ♧
                    {{ collect($event->benefits ?? [])->filter()->take(2)->join(' · ') ?: 'Pengalaman dan relasi baru' }}
                </div>

            <a href="{{ route('events.show', $event->slug) }}"
            class="vola-btn vola-event-button">
                Lihat Detail
            </a>           
        </article>
        @empty
            <div class="vola-events-empty">
                <h3>Belum ada event yang tersedia</h3>
                <p>
                    Cek kembali nanti untuk menemukan kesempatan
                    volunteer terbaru dari VOLA.
                </p>
            </div>
        @endforelse

        </div>
    </div>
</section>

<!-- CTA PENYELENGGARA -->
<section class="vola-cta-section">
    <div class="vola-container">
        <div class="vola-cta-box">

            <div class="vola-cta-content">
                <span class="vola-cta-label">
                    KOLABORASI PENYELENGGARA
                </span>

                <h2>
                    Punya Event Sendiri &amp; Butuh Kru Lapangan?
                </h2>

                <p>
                    Publikasikan kebutuhan relawan acaramu dan temukan
                    anak muda berbakat untuk menyukseskan acara.
                </p>
            </div>

            <a href="{{ route('register') }}"
               class="vola-btn vola-cta-button">
                Daftarkan Event Sekarang →
            </a>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="vola-footer">
    © {{ date('Y') }} VOLA Platform Relawan Indonesia.
    All rights reserved.
</footer>

    </main>

    <script>
        const menuToggle = document.getElementById('volaMenuToggle');
        const navLinks = document.getElementById('volaNavLinks');

        menuToggle.addEventListener('click', function () {
            const isOpen = navLinks.classList.toggle('is-open');

            menuToggle.setAttribute('aria-expanded', String(isOpen));
            menuToggle.setAttribute(
                'aria-label',
                isOpen ? 'Tutup menu' : 'Buka menu'
            );
            menuToggle.textContent = isOpen ? '✕' : '☰';
        });

        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navLinks.classList.remove('is-open');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Buka menu');
                menuToggle.textContent = '☰';
            });
        });
    </script>

</body>
</html>
