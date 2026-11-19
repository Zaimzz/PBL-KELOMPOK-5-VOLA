<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VOLA menghubungkan relawan dengan peluang event dan membantu penyelenggara menemukan kru lapangan yang tepat.">
    <meta name="theme-color" content="#ffffff">
    <title>VOLA — Temukan Event Impian, Bangun Pengalamanmu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="lp-page">
@php
    $dashboardUrl = auth()->check()
        ? match (auth()->user()->role) {
            \App\Enums\UserRole::Admin => route('admin.dashboard'),
            \App\Enums\UserRole::Eo => route('eo.dashboard'),
            default => route('dashboard'),
        }
        : route('login');
@endphp
<header class="lp-header">
    <div class="lp-container lp-header-inner">
        <a href="{{ route('home') }}" class="lp-logo" aria-label="VOLA Beranda">VOLA<span>.</span></a>
        <button class="lp-menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="lp-navigation">
            <span></span><span></span><span></span>
        </button>
        <nav class="lp-navigation" id="lp-navigation" aria-label="Navigasi utama">
            <a class="is-active" href="#beranda" aria-current="location">Beranda</a>
            <a href="#tentang">Tentang</a>
            <a href="#event">Cari Event</a>
            <a href="#wawasan">Wawasan</a>
        </nav>
        <div class="lp-header-actions">
            @auth
                <a class="lp-button lp-button-primary lp-button-small" href="{{ $dashboardUrl }}">Dashboard</a>
            @else
                <a class="lp-button lp-button-outline lp-button-small lp-login"
                href="{{ route('login') }}">
                    Masuk
                </a>
                <a class="lp-button lp-button-primary lp-button-small" href="{{ route('register') }}">Daftar</a>
            @endauth
        </div>
    </div>
</header>
<main>
    <section class="lp-hero" id="beranda">
        <div class="lp-hero-glow lp-hero-glow-one"></div>
        <div class="lp-hero-glow lp-hero-glow-two"></div>
        <div class="lp-container lp-hero-grid">
            <div class="lp-hero-copy lp-reveal">
                <span class="lp-eyebrow"><span aria-hidden="true">✦</span> Satu Platform untuk Semua Event</span>
                <h1>Temukan Event Impian,<span>Bangun Pengalamanmu.</span></h1>
                <p class="lp-hero-description">Platform yang menghubungkan antusiasme relawan dengan kebutuhan kru lapangan dari Event Organizer di seluruh Indonesia.</p>
                <div class="lp-hero-actions">
                    <a href="#event" class="lp-button lp-button-primary lp-button-large">Cari Event Sekarang <span aria-hidden="true">→</span></a>
                    <a href="{{ route('register') }}" class="lp-button lp-button-outline lp-button-large"><span aria-hidden="true">＋</span> Buat Event</a>
                </div>
                <div class="lp-trust-row">
                    <div class="lp-trust-item"><span class="lp-trust-icon">✓</span><span><strong>{{ number_format($landingStats['events']) }}</strong> Event Terbuka</span></div>
                    <div class="lp-trust-item"><span class="lp-trust-icon lp-trust-icon-green">✓</span><span><strong>{{ number_format($landingStats['volunteers']) }}</strong> Relawan Terdaftar</span></div>
                </div>
            </div>
            <div class="lp-hero-art lp-reveal lp-reveal-delay">
                <span class="lp-art-spark lp-art-spark-top">✦</span>
                <span class="lp-art-spark lp-art-spark-bottom">✧</span>
                <div class="lp-art-verification"><span>✦</span> EO Terverifikasi</div>
                <div class="lp-hero-photo-frame">
                    <img src="{{ asset('images/dashboard-crew/vola-hero-event-crew.webp') }}" alt="Suasana kegiatan pameran dan kru event di area expo" fetchpriority="high" width="1100" height="900">
                    <div class="lp-photo-overlay">
                        <div class="lp-photo-icon">✺</div>
                        <div><strong>Temukan komunitasmu</strong><span>Mulai pengalaman baru bersama VOLA</span></div>
                        <span class="lp-live-pill"><i></i> VOLA</span>
                    </div>
                </div>
                <div class="lp-floating-note"><span class="lp-floating-note-icon">♧</span><div><strong>{{ number_format($landingStats['organizers']) }} EO Terverifikasi</strong><span>Siap membuka peluang kolaborasi</span></div></div>
            </div>
        </div>
    </section>
    <section class="lp-category-strip" aria-label="Kategori event">
        <div class="lp-marquee">
            <div class="lp-marquee-track">
                {{-- Kumpulan pertama --}}
                <div class="lp-marquee-group">
                    @foreach ($eventCategories as $category)
                        <div class="lp-marquee-item">
                            {{ $category->name }}
                        </div>
                    @endforeach
                </div>
                {{-- Kumpulan duplikat --}}
                <div class="lp-marquee-group" aria-hidden="true">
                    @foreach ($eventCategories as $category)
                        <div class="lp-marquee-item">
                            {{ $category->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <section class="lp-pathways" id="tentang">
        <div class="lp-container">
            <div class="lp-section-heading lp-reveal">
                <span class="lp-section-kicker">LANGKAH MUDAH</span>
                <h2>Solusi Rekrutmen dalam Satu Pintu</h2>
                <p>Proses sederhana, transparan, dan terpusat tanpa perlu mencari peluang dari berbagai media sosial.</p>
            </div>
            <div class="lp-pathway-grid">
                <article class="lp-pathway-card lp-pathway-volunteer lp-reveal">
                    <div class="lp-pathway-heading"><span class="lp-pathway-icon">♙</span><div><span class="lp-pathway-label">JALUR KRU &amp; RELAWAN</span><h3>Untuk Kru &amp; Relawan Muda</h3></div></div>
                    <ol class="lp-steps">
                        <li><span>1</span><div><h4>Temukan Event Sesuai Minat</h4><p>Pilih konser, expo, festival olahraga, hingga konferensi yang sesuai dengan minat dan waktumu.</p></div></li>
                        <li><span>2</span><div><h4>Lamar Posisi dalam Sekali Klik</h4><p>Ajukan diri menggunakan profil dan CV digital tanpa mengisi formulir berulang.</p></div></li>
                        <li><span>3</span><div><h4>Bangun Portofolio Lapangan</h4><p>Kumpulkan pengalaman, perluas koneksi, dan kembangkan kemampuan di industri event.</p></div></li>
                    </ol>
                    <a class="lp-pathway-link" href="#event">Eksplorasi lowongan crew <span>→</span></a>
                </article>
                <article class="lp-pathway-card lp-pathway-organizer lp-reveal lp-reveal-delay">
                    <div class="lp-pathway-heading"><span class="lp-pathway-icon">⚑</span><div><span class="lp-pathway-label">JALUR PENYELENGGARA</span><h3>Untuk Event Organizer (EO)</h3></div></div>
                    <ol class="lp-steps">
                        <li><span>1</span><div><h4>Publikasikan Lowongan</h4><p>Tentukan divisi, kebutuhan kru, jadwal, lokasi, serta fasilitas yang ditawarkan.</p></div></li>
                        <li><span>2</span><div><h4>Jangkau Kandidat Tepat</h4><p>Perkenalkan peluang event kepada relawan yang tertarik dan sesuai kebutuhan acara.</p></div></li>
                        <li><span>3</span><div><h4>Kelola Seleksi Lebih Mudah</h4><p>Pantau pendaftar dan kelola proses rekrutmen melalui satu platform.</p></div></li>
                    </ol>
                    <a class="lp-pathway-link" href="{{ route('register') }}">Mulai pasang lowongan <span>→</span></a>
                </article>
            </div>
        </div>
    </section>
    <section class="lp-events" id="event">
        <div class="lp-container">
            <div class="lp-section-topline lp-reveal"><div><span class="lp-section-kicker">KATALOG TERBUKA</span><h2>Event Pilihan &amp; Terbuka</h2><p>Peluang rekrutmen kru dan relawan yang tersedia di VOLA.</p></div>
                <span class="lp-section-aside">{{ $events->count() }} event ditampilkan</span>
            </div>
            <div class="lp-event-grid">
                @forelse ($events as $event)
                    <article class="lp-event-card lp-reveal">
                        <div class="lp-event-poster">
                            <img src="{{ $event->poster_url }}" alt="Poster {{ $event->title }}" loading="lazy" width="800" height="480">
                        </div>
                        <div class="lp-event-card-top"><span class="lp-open-badge"><i></i> Open Recruitment</span><span class="lp-event-category">{{ $event->category?->name ?? 'Event' }}</span></div>
                        <div class="lp-event-card-content">
                            <div class="lp-event-organizer">{{ $event->organizer?->organization_name ?? 'Penyelenggara VOLA' }}</div>
                            <h3>{{ $event->title }}</h3>
                            <div class="lp-event-meta"><span><i>⌖</i>{{ $event->city ?: $event->location }}</span><span><i>▦</i>{{ $event->start_date?->translatedFormat('d M Y') ?? 'Jadwal menyusul' }}</span><span><i>✧</i>{{ collect($event->benefits ?? [])->filter()->take(2)->join(' · ') ?: 'Pengalaman dan relasi baru' }}</span></div>
                        </div>
                        <a href="{{ route('events.show', $event->slug) }}" class="lp-event-button">Lihat Detail <span>→</span></a>
                    </article>
                @empty
                    <div class="lp-empty-state"><span>✦</span><h3>Event baru sedang dipersiapkan</h3><p>Belum ada event terbuka saat ini. Silakan kembali lagi untuk menemukan peluang terbaru.</p></div>
                @endforelse
            </div>
        </div>
    </section>
    <section class="lp-insights" id="wawasan">
        <div class="lp-container">
            <div class="lp-section-heading lp-reveal"><span class="lp-section-kicker lp-kicker-yellow">PUSAT EDUKASI</span><h2>Pusat Wawasan Kru Event</h2><p>Tips dan panduan untuk memulai perjalananmu di industri event.</p></div>
            <div class="lp-insight-grid">
                <article class="lp-insight-card lp-reveal"><div class="lp-insight-image"><img src="{{ asset('images/dashboard-crew/green-marathon.jpg') }}" alt="Kegiatan event olahraga bersama relawan" loading="lazy" width="800" height="520"></div><div class="lp-insight-content"><span class="lp-insight-tag">TIPS KARIER</span><h3>Cara Menyiapkan Diri Menjadi Kru Event</h3><p>Kenali keterampilan dasar, siapkan CV, dan bangun pengalaman pertamamu di dunia event.</p><span class="lp-insight-read">Panduan untuk relawan <span>↗</span></span></div></article>
                <article class="lp-insight-card lp-reveal lp-reveal-delay"><div class="lp-insight-image"><img src="{{ asset('images/dashboard-crew/tech-summit.jpg') }}" alt="Suasana konferensi teknologi dan kegiatan kru event" loading="lazy" width="800" height="520"></div><div class="lp-insight-content"><span class="lp-insight-tag">PENGALAMAN</span><h3>Belajar Banyak Hal dari Pengalaman Volunteer</h3><p>Pengalaman di lapangan dapat melatih komunikasi, kerja sama tim, dan kemampuan beradaptasi.</p><span class="lp-insight-read">Cerita dan inspirasi <span>↗</span></span></div></article>
                <article class="lp-insight-card lp-reveal lp-reveal-delay-two"><div class="lp-insight-image"><img src="{{ asset('images/dashboard-crew/java-jazz.jpg') }}" alt="Suasana festival musik dan penonton" loading="lazy" width="800" height="520"></div><div class="lp-insight-content"><span class="lp-insight-tag">INFO EO</span><h3>Hal Penting dalam Mengelola Kru Event</h3><p>Perencanaan kebutuhan, pembagian tugas, dan komunikasi membantu acara berjalan lebih terarah.</p><span class="lp-insight-read">Wawasan penyelenggara <span>↗</span></span></div></article>
            </div>
        </div>
    </section>
    <section class="lp-community">
        <div class="lp-container">
            <div class="lp-section-heading lp-reveal"><span class="lp-section-kicker">KOMUNITAS VOLA</span><h2>Ruang Bertumbuh untuk Semua</h2><p>VOLA dirancang untuk membantu relawan dan penyelenggara berkolaborasi dengan lebih mudah.</p></div>
            <div class="lp-community-grid">
                <article class="lp-community-card lp-reveal"><span class="lp-community-symbol lp-symbol-purple">♡</span><div><h3>Untuk Relawan</h3><p>Temukan peluang, kembangkan keterampilan, dan bangun pengalaman dari berbagai acara.</p></div></article>
                <article class="lp-community-card lp-reveal lp-reveal-delay"><span class="lp-community-symbol lp-symbol-yellow">✳</span><div><h3>Untuk Penyelenggara</h3><p>Publikasikan kebutuhan kru dan kelola pendaftaran kandidat melalui satu platform.</p></div></article>
            </div>
        </div>
    </section>
    <section class="lp-cta">
        <div class="lp-container"><div class="lp-cta-panel lp-reveal"><div class="lp-cta-decoration"></div><div class="lp-cta-copy"><span>UNTUK PENYELENGGARA EVENT</span><h2>Siap Menggelar Event Besar Anda?</h2><p>Publikasikan kebutuhan kru dan temukan talenta lapangan untuk membantu menyukseskan acara Anda.</p></div><a href="{{ route('register') }}" class="lp-cta-button">Daftarkan Event Sekarang <span>→</span></a></div></div>
    </section>
</main>
<footer class="lp-footer">
    <div class="lp-container">
        <div class="lp-footer-grid">
            <div class="lp-footer-brand"><a href="{{ route('home') }}" class="lp-logo">VOLA<span>.</span></a><p>Sentralisasi informasi dan rekrutmen volunteer event untuk seluruh Indonesia.</p><span class="lp-footer-note">Temukan peluang. Bangun pengalaman.</span></div>
            <div class="lp-footer-column"><h3>UNTUK RELAWAN</h3><a href="#event">Cari Event</a><a href="{{ route('register') }}">Daftar Volunteer</a><a href="#wawasan">Pusat Wawasan</a></div>
            <div class="lp-footer-column"><h3>UNTUK PENYELENGGARA</h3><a href="{{ route('register') }}">Daftar sebagai EO</a><a href="{{ route('login') }}">Kelola Rekrutmen</a><a href="#tentang">Cara Kerja VOLA</a></div>
            <div class="lp-footer-column"><h3>PERUSAHAAN</h3><a href="#tentang">Tentang VOLA</a><a href="#tentang">Informasi VOLA</a><a href="#beranda">Kembali ke Atas ↑</a></div>
        </div>
        <div class="lp-footer-bottom"><span>© {{ date('Y') }} VOLA. All rights reserved.</span><span>Dibuat dengan <b>♥</b> di Indonesia.</span></div>
    </div>
</footer>
<script>
(() => {
    const header = document.querySelector('.lp-header');
    const menuButton = document.querySelector('.lp-menu-toggle');
    const navigation = document.querySelector('.lp-navigation');
    const navLinks = navigation ? Array.from(navigation.querySelectorAll('a[href^="#"]')) : [];
    const sectionIds = ['beranda', 'tentang', 'event', 'wawasan'];
    const sections = sectionIds.map((id) => document.getElementById(id)).filter(Boolean);
    const mobileQuery = window.matchMedia('(max-width: 1024px)');
    if (!header || !menuButton || !navigation) return;
    const setMenuOpen = (open) => {
        navigation.classList.toggle('is-open', open && mobileQuery.matches);
        menuButton.classList.toggle('is-open', open && mobileQuery.matches);
        menuButton.setAttribute('aria-expanded', String(open && mobileQuery.matches));
        menuButton.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    };
    menuButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setMenuOpen(!navigation.classList.contains('is-open'));
    });
    navLinks.forEach((link) => {
        link.addEventListener('click', () => {
            const targetId = link.getAttribute('href').slice(1);
            setActiveLink(targetId);
            setMenuOpen(false);
        });
    });
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) setMenuOpen(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMenuOpen(false);
    });
    const setActiveLink = (id) => {
        navLinks.forEach((link) => {
            const active = link.getAttribute('href') === `#${id}`;
            link.classList.toggle('is-active', active);
            if (active) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    };
    let scrollTicking = false;
    const updateActiveSection = () => {
        const marker = window.scrollY + header.offsetHeight + 130;
        let currentId = sections[0]?.id || 'beranda';
        sections.forEach((section) => {
            if (section.offsetTop <= marker) currentId = section.id;
        });
        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 8) {
            currentId = sections[sections.length - 1]?.id || currentId;
        }
        setActiveLink(currentId);
        scrollTicking = false;
    };
    window.addEventListener('scroll', () => {
        if (!scrollTicking) {
            window.requestAnimationFrame(updateActiveSection);
            scrollTicking = true;
        }
    }, { passive: true });
    window.addEventListener('resize', () => {
        if (!mobileQuery.matches) setMenuOpen(false);
        updateActiveSection();
    });
    window.addEventListener('hashchange', () => {
        const id = window.location.hash.slice(1);
        if (sectionIds.includes(id)) setActiveLink(id);
    });
    setMenuOpen(false);
    updateActiveSection();
    // Reveal animation
    const revealItems = document.querySelectorAll('.lp-reveal');
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -35px 0px' });
        revealItems.forEach((item) => revealObserver.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }
    // Isi marquee dengan pasangan grup identik agar tidak muncul ruang kosong
    // ketika lebar kategori lebih pendek daripada layar.
    const marqueeTrack = document.querySelector('.lp-marquee-track');
    const marqueeBaseGroup = marqueeTrack?.querySelector('.lp-marquee-group');
    if (marqueeTrack && marqueeBaseGroup && marqueeBaseGroup.children.length > 0) {
        const fillMarquee = () => {
            marqueeTrack.querySelectorAll('[data-marquee-clone="true"]').forEach((item) => item.remove());
            while (marqueeTrack.scrollWidth < window.innerWidth * 2) {
                for (let index = 0; index < 2; index += 1) {
                    const clone = marqueeBaseGroup.cloneNode(true);
                    clone.setAttribute('aria-hidden', 'true');
                    clone.dataset.marqueeClone = 'true';
                    marqueeTrack.appendChild(clone);
                }
            }
            marqueeTrack.style.animationDuration = `${Math.max(22, marqueeTrack.scrollWidth / 75)}s`;
        };
        fillMarquee();
        let marqueeResizeFrame = false;
        window.addEventListener('resize', () => {
            if (marqueeResizeFrame) return;
            marqueeResizeFrame = true;
            window.requestAnimationFrame(() => {
                fillMarquee();
                marqueeResizeFrame = false;
            });
        });
    }
})();
</script>
</body>
</html>
