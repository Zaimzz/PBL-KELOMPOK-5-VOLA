
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
            <a href="#tentang">Tentang Kami</a>
            <a href="#event">Cari Event</a>
            <a href="#cerita">Kisah</a>
        </nav>

        <div class="vola-nav-actions">
            @auth
                <a href="{{ match(auth()->user()->role) {
                    \App\Enums\UserRole::Admin => route('admin.dashboard'),
                    \App\Enums\UserRole::Eo => route('eo.dashboard'),
                    default => route('profile.edit')
                } }}"
                   class="vola-btn vola-btn-primary vola-btn-small">
                    {{ auth()->user()->role === \App\Enums\UserRole::Volunteer
                        ? 'Profil Saya'
                        : 'Dashboard' }}
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

            <!-- HERO CONTENT -->
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
                    <a href="#event" class="vola-btn vola-btn-primary">
                        Cari Event Sekarang <span>→</span>
                    </a>

                    <a href="{{ route('register') }}" class="vola-btn vola-btn-outline">
                        <span>⊕</span> Buat Event (Untuk EO)
                    </a>
                </div>

                <!-- STATISTICS -->
                <div class="vola-stats">
                    <div class="vola-stat">
                        <strong data-counter="{{ $totalEvents }}">
                            {{ number_format($totalEvents) }}+
                        </strong>
                        <span>Event Terbuka</span>
                    </div>

                    <div class="vola-stat">
                        <strong data-counter="{{ $totalVolunteers }}">
                            {{ number_format($totalVolunteers) }}+
                        </strong>
                        <span>Relawan Aktif</span>
                    </div>

                    <div class="vola-stat">
                        <strong data-counter="{{ $totalOrganizers }}">
                            {{ number_format($totalOrganizers) }}+
                        </strong>
                        <span>EO Terverifikasi</span>
                    </div>
                </div>
            </div>
            <!-- HERO CONTENT SELESAI -->

            <!-- HERO IMAGE -->
            <div class="vola-hero-visual">

                <div class="vola-hero-verified">
                    <span>✦</span>
                    100% Terverifikasi
                </div>

                <span class="vola-hero-decoration vola-decoration-yellow"></span>
                <span class="vola-hero-decoration vola-decoration-blue">✦</span>

                <img
                    src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=1100&q=85"
                    alt="Sekelompok anak muda dalam kegiatan bersama"
                    fetchpriority="high"
                >

                <div class="vola-hero-floating-card">
                    <div class="vola-floating-icon">♧</div>

                    <div class="vola-floating-info">
                        <strong>{{ number_format($totalVolunteers) }}+ Relawan Aktif</strong>
                        <span>Siap ikut berkontribusi</span>
                    </div>

                    <span class="vola-live-badge">
                        ● LIVE
                    </span>
                </div>
            </div>
            <!-- HERO IMAGE SELESAI -->

               </div>
    </section>


<!-- PARTNER & COLLABORATION -->
<section class="vola-partners">
    <div class="vola-container">

        <p class="vola-partners-heading">
            DIRANCANG SEBAGAI ALAT BANTU PENUH UNTUK:
        </p>

        <div class="vola-partners-list">

            <div class="vola-partner-item partner-blue">
                <span>♧</span>
                Promotor Musik
            </div>

            <div class="vola-partner-item partner-yellow">
                <span>✦</span>
                Festival Organizer
            </div>

            <div class="vola-partner-item partner-sky">
                <span>▣</span>
                Tech Conference
            </div>

            <div class="vola-partner-item partner-green">
                <span>♧</span>
                Komunitas Sosial
            </div>

            <div class="vola-partner-item partner-pink">
                <span>⚑</span>
                Event Kampus
            </div>

            <div class="vola-partner-item partner-purple">
                <span>♡</span>
                Komunitas Relawan
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


<!-- PUSAT WAWASAN KRU EVENT -->
<section class="vola-insights" id="cerita">
    <div class="vola-container">

        <div class="vola-insights-heading">
            <span class="vola-insights-label">
                PUSAT EDUKASI
            </span>

            <h2>Pusat Wawasan Kru Event</h2>

            <p>
                Tips, panduan, dan kisah sukses dari industri event
                organizer terkemuka.
            </p>
        </div>

        <div class="vola-insights-grid">

            <!-- ARTIKEL 1 -->
            <article class="vola-insight-card">
                <div class="vola-insight-image">
                    <img
                        src="https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=900&q=85"
                        alt="Tim produksi mengelola peralatan panggung event"
                        loading="lazy"
                    >
                </div>

                <div class="vola-insight-content">
                    <span class="vola-insight-category">
                        Tips Kru
                    </span>

                    <h3>
                        Cara Bikin CV Kru Lapangan yang Langsung Dilirik Promotor
                    </h3>

                    <p>
                        Ketahui tips penting untuk menyusun CV dan
                        menampilkan pengalaman event agar lebih menarik
                        bagi penyelenggara.
                    </p>

                    <div class="vola-insight-meta">
                        <span>Tips Karier</span>
                        <a href="#cerita">Baca selengkapnya →</a>
                    </div>
                </div>
            </article>

            <!-- ARTIKEL 2 -->
            <article class="vola-insight-card">
                <div class="vola-insight-image">
                    <img
                        src="https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=900&q=85"
                        alt="Suasana festival dan kru event di lapangan"
                        loading="lazy"
                    >
                </div>

                <div class="vola-insight-content">
                    <span class="vola-insight-category">
                        Kisah Sukses
                    </span>

                    <h3>
                        Dari Relawan ke Koordinator Lapangan: Kisah Sukses Kru
                    </h3>

                    <p>
                        Perjalanan mengembangkan pengalaman dari posisi
                        relawan hingga memiliki tanggung jawab lebih besar
                        dalam penyelenggaraan event.
                    </p>

                    <div class="vola-insight-meta">
                        <span>Inspirasi</span>
                        <a href="#cerita">Baca selengkapnya →</a>
                    </div>
                </div>
            </article>

            <!-- ARTIKEL 3 -->
            <article class="vola-insight-card">
                <div class="vola-insight-image">
                    <img
                        src="https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&w=900&q=85"
                        alt="Panggung konser dengan pencahayaan dan penonton"
                        loading="lazy"
                    >
                </div>

                <div class="vola-insight-content">
                    <span class="vola-insight-category">
                        Info EO
                    </span>

                    <h3>
                        Persiapan Hari-H Festival Berlangsung Aman & Tertib
                    </h3>

                    <p>
                        Panduan singkat mengenai persiapan teknis,
                        koordinasi kru, dan komunikasi agar acara berjalan
                        lancar.
                    </p>

                    <div class="vola-insight-meta">
                        <span>Manajemen Event</span>
                        <a href="#cerita">Baca selengkapnya →</a>
                    </div>
                </div>
            </article>

        </div>

    </div>
</section>

<!-- FEEDBACK KOMUNITAS -->
<section class="vola-testimonials">
    <div class="vola-container">

        <div class="vola-testimonials-heading">
            <span class="vola-testimonials-label">
                FEEDBACK KOMUNITAS
            </span>

            <h2>Kesan Penguji Beta Kami</h2>

            <p>
                Masukan dari pengguna membantu VOLA terus berkembang
                menjadi platform relawan yang lebih baik.
            </p>
        </div>

        <div class="vola-testimonials-grid">

            <!-- TESTIMONI CONTOH 1 -->
            <article class="vola-testimonial-card">
                <div class="vola-testimonial-stars"
                     aria-label="Contoh tampilan penilaian">
                    ★★★★★
                </div>

                <blockquote>
                    “Platform ini diharapkan dapat mempermudah pencarian
                    informasi event dan membantu relawan menemukan
                    kesempatan sesuai minat.”
                </blockquote>

                <div class="vola-testimonial-user">
                    <div class="vola-testimonial-avatar is-purple">
                        V
                    </div>

                    <div>
                        <strong>Contoh Pengguna</strong>
                        <span>Volunteer</span>
                    </div>
                </div>
            </article>

            <!-- TESTIMONI CONTOH 2 -->
            <article class="vola-testimonial-card">
                <div class="vola-testimonial-stars"
                     aria-label="Contoh tampilan penilaian">
                    ★★★★★
                </div>

                <blockquote>
                    “Informasi kebutuhan kru dalam satu platform dapat
                    membantu penyelenggara mengelola proses rekrutmen
                    dengan lebih terarah.”
                </blockquote>

                <div class="vola-testimonial-user">
                    <div class="vola-testimonial-avatar is-yellow">
                        E
                    </div>

                    <div>
                        <strong>Contoh Pengguna</strong>
                        <span>Event Organizer</span>
                    </div>
                </div>
            </article>

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
    <div class="vola-container">

        <div class="vola-footer-main">

            <!-- BRAND -->
            <div class="vola-footer-brand">
                <a href="{{ route('home') }}#beranda"
                   class="vola-footer-logo">
                    VOLA<span>.</span>
                </a>

                <p>
                    Sentralisasi informasi dan rekrutmen event
                    untuk memudahkan relawan menemukan peluang
                    serta membantu penyelenggara acara.
                </p>
            </div>

            <!-- FOOTER LINKS -->
            <div class="vola-footer-column">
                <h4>UNTUK CREW</h4>
                <a href="#event">Cari Event</a>
                <a href="{{ route('login') }}">Panduan Pelamar</a>
                <a href="#cerita">Wawasan</a>
            </div>

            <div class="vola-footer-column">
                <h4>UNTUK PENYELENGGARA</h4>
                <a href="{{ route('register') }}">Daftarkan Event</a>
                <a href="{{ route('login') }}">Fitur Manajemen</a>
                <a href="{{ route('register') }}">Harga Layanan</a>
            </div>

            <div class="vola-footer-column">
                <h4>PERUSAHAAN</h4>
                <a href="#tentang">Tentang VOLA</a>
                <a href="#cerita">Hubungi Kami</a>
                <a href="#tentang">Syarat &amp; Ketentuan</a>
            </div>

        </div>

        <!-- FOOTER BOTTOM -->
         <div class="vola-footer-bottom">
            <span>© {{ date('Y') }} VOLA. All rights reserved.</span>
        </div>
    </div>
</footer>

    </main>
<script>
    // MOBILE MENU
    const menuToggle = document.getElementById('volaMenuToggle');
    const navLinks = document.getElementById('volaNavLinks');

    if (menuToggle && navLinks) {
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
    }

    // ACTIVE NAVIGATION SAAT SCROLL
    const navMenuItems = document.querySelectorAll('.vola-nav-links a');

    const sectionIds = [
        'beranda',
        'tentang',
        'event',
        'cerita'
    ];

    function updateActiveMenu() {
        let activeSection = 'beranda';
        const scrollPoint = window.scrollY + 180;

        sectionIds.forEach(function (id) {
            const section = document.getElementById(id);

            if (section && section.offsetTop <= scrollPoint) {
                activeSection = id;
            }
        });

        navMenuItems.forEach(function (link) {
            link.classList.toggle(
                'is-active',
                link.getAttribute('href') === '#' + activeSection
            );
        });
    }

    window.addEventListener('scroll', updateActiveMenu, {
        passive: true
    });

    window.addEventListener('load', updateActiveMenu);
    updateActiveMenu();

    // ANIMATED STATISTICS COUNTER
    const counters = document.querySelectorAll('[data-counter]');

    function startCounter(counter) {
        const target = Number(counter.dataset.counter);

        if (!Number.isFinite(target)) return;

        const duration = 1500;
        const startTime = performance.now();

        function animate(currentTime) {
            const progress = Math.min(
                (currentTime - startTime) / duration,
                1
            );

            const easedProgress = 1 - Math.pow(1 - progress, 3);
            const currentValue = Math.floor(target * easedProgress);

            counter.textContent =
                currentValue.toLocaleString('id-ID') + '+';

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                counter.textContent =
                    target.toLocaleString('id-ID') + '+';
            }
        }

        counter.textContent = '0+';
        requestAnimationFrame(animate);
    }

    if ('IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver(
            function (entries, observer) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    startCounter(entry.target);
                    observer.unobserve(entry.target);
                });
            },
            {
                threshold: 0.2
            }
        );

        counters.forEach(function (counter) {
            counterObserver.observe(counter);
        });
    } else {
        counters.forEach(startCounter);
    }
</script>

</body>
</html>