<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} | VOLA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="vola-detail-page">

<div class="vd-app">

    <!-- SIDEBAR -->
    <aside class="vd-sidebar">
        <a href="{{ route('home') }}" class="vd-logo">
            VOLA<span>.</span>
            <small>Crew</small>
        </a>

        <p class="vd-menu-label">MENU UTAMA</p>

        <nav class="vd-navigation">
            <a href="{{ route('home') }}">⌂ <span>Beranda</span></a>
            <a href="{{ route('home') }}#event" class="active">⌕ <span>Cari Event</span></a>
            <a href="{{ route('login') }}">▤ <span>Lamaran Saya</span></a>
            <a href="{{ route('login') }}">▣ <span>Event Saya</span></a>
            <a href="{{ route('login') }}">◷ <span>Riwayat</span></a>
        </nav>

        <p class="vd-menu-label">AKUN</p>

        <nav class="vd-navigation">
            <a href="{{ route('login') }}">♙ <span>Profil Saya</span></a>
        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="vd-main">

        <!-- HEADER -->
        <header class="vd-topbar">
            <div class="vd-search">
                <span>⌕</span>
                <span>Cari event, posisi volunteer, atau lokasi...</span>
            </div>

            <div class="vd-topbar-actions">
                <span class="vd-status">● Status: Siap Bertugas</span>
                <a href="{{ route('login') }}" class="vd-login-link">Masuk</a>
                <a href="{{ route('register') }}" class="vd-register-link">Daftar</a>
            </div>
        </header>

        <main class="vd-content">
 
            @if (session('success'))
                <div class="vd-alert vd-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="vd-alert vd-alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <!-- BREADCRUMB -->
            <div class="vd-breadcrumb">
                <a href="{{ route('home') }}#event">← Kembali ke Cari Event</a>
                <span>/</span>
                <span>{{ $event->category?->name ?? 'Event' }}</span>
                <span>/</span>
                <strong>{{ $event->title }}</strong>
            </div>

            <!-- BANNER EVENT -->
            <section
                class="vd-hero"
                @if ($event->poster_path)
                    style="background-image: linear-gradient(90deg, rgba(9,16,39,.96), rgba(9,16,39,.65)), url('{{ asset('storage/' . $event->poster_path) }}');"
                @endif
            >
                <div class="vd-hero-top">
                    <span class="vd-open-badge">● Open Volunteer &amp; Crew Rekrutmen</span>
                    <span class="vd-deadline-badge">
                        Batas: {{ $event->registration_deadline?->translatedFormat('d M Y') ?? 'Belum ditentukan' }}
                    </span>
                </div>

                <div class="vd-hero-bottom">
                    <div>
                        <p class="vd-hero-category">
                            {{ $event->category?->name ?? 'Event' }}
                            <span>•</span>
                            Penyelenggara:
                            {{ $event->organizer?->organization_name ?? 'Penyelenggara VOLA' }}
                        </p>

                        <h1>{{ $event->title }}</h1>
                        <p class="vd-hero-location">
                            {{ $event->city ?: ($event->location ?: 'Lokasi belum tersedia') }}
                        </p>
                    </div>

                    <div class="vd-hero-buttons">
                        <button type="button" class="vd-button vd-button-outline"
                            onclick="navigator.clipboard.writeText(window.location.href).then(() => alert('Link event berhasil disalin.')).catch(() => alert('Silakan salin URL dari browser.'))">
                            ♧ Bagikan
                        </button>

                        <a href="{{ route('login') }}" class="vd-button vd-button-primary">
                            Daftar Sekarang →
                        </a>
                    </div>
                </div>
            </section>

            <!-- INFORMASI JADWAL -->
            <section class="vd-info-strip">
                <div class="vd-info-item">
                    <span class="vd-info-icon">▦</span>
                    <div>
                        <small>TANGGAL PELAKSANAAN</small>
                        <strong>
                            {{ $event->start_date?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}
                            @if ($event->end_date && $event->end_date->format('Y-m-d') !== $event->start_date?->format('Y-m-d'))
                                – {{ $event->end_date->translatedFormat('d F Y') }}
                            @endif
                        </strong>
                        <p>Tanggal kegiatan</p>
                    </div>
                </div>

                <div class="vd-info-item">
                    <span class="vd-info-icon">◷</span>
                    <div>
                        <small>WAKTU KEGIATAN</small>
                        <strong>
                            @if ($event->start_time)
                                {{ \Illuminate\Support\Str::substr((string) $event->start_time, 0, 5) }}
                                @if ($event->end_time)
                                    – {{ \Illuminate\Support\Str::substr((string) $event->end_time, 0, 5) }}
                                @endif
                                WIB
                            @else
                                Belum ditentukan
                            @endif
                        </strong>
                        <p>Jam pelaksanaan</p>
                    </div>
                </div>

                <div class="vd-info-item">
                    <span class="vd-info-icon">⌖</span>
                    <div>
                        <small>LOKASI PENUGASAN</small>
                        <strong>{{ $event->location ?: 'Belum ditentukan' }}</strong>
                        <p>{{ $event->city ?: 'Lokasi belum tersedia' }}</p>
                    </div>
                </div>

                <div class="vd-info-item vd-info-deadline">
                    <span class="vd-info-icon">⚑</span>
                    <div>
                        <small>BATAS PENDAFTARAN</small>
                        <strong>
                            {{ $event->registration_deadline?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}
                        </strong>
                        <p>Tanggal penutupan pendaftaran</p>
                    </div>
                </div>
            </section>

            <!-- NAVIGASI BAGIAN -->
            <nav class="vd-tabs">
                <a href="#posisi">♧ Posisi Dibuka ({{ $event->positions->count() }})</a>
                <a href="#tentang">Tentang Event</a>
                <a href="#benefit">Benefit &amp; Fasilitas</a>
                <a href="#syarat">Syarat &amp; Ketentuan</a>
            </nav>

            <div class="vd-layout">

                <!-- KOLOM KIRI -->
                <div class="vd-left-column">

                    <section id="posisi" class="vd-section">
                        <div class="vd-section-heading">
                            <div>
                                <h2>Posisi Volunteer yang Dibuka</h2>
                                <p>Pilih posisi yang paling sesuai dengan keahlian dan minatmu.</p>
                            </div>

                            <span class="vd-total-quota">
                                Total Kuota: {{ $event->positions->sum('quota') }} Orang
                            </span>
                        </div>

                        @forelse ($event->positions as $position)
                            <article class="vd-position-card">
                                <div class="vd-position-content">
                                    <div class="vd-position-title">
                                        <span class="vd-position-label">Posisi Volunteer</span>
                                        <h3>{{ $position->name }}</h3>
                                    </div>

                                    <p class="vd-position-description">
                                        {{ $position->description ?: 'Informasi posisi belum tersedia.' }}
                                    </p>

                                    <div class="vd-position-meta">
                                        <span>♧ Kuota: {{ $position->quota }} Orang</span>
                                        @if ($position->requirements)
                                            <span>✓ Persyaratan tersedia</span>
                                        @endif
                                    </div>

                                    @if ($position->requirements)
                                        <p class="vd-requirements">
                                            <strong>Syarat:</strong>
                                            {{ $position->requirements }}
                                        </p>
                                    @endif
                                </div>
                                <form

                                    <button type="submit" class="vd-select-position">
                                        Pilih Posisi →
                                    </button>
                                </form>
                            </article>
                        @empty
                            <div class="vd-empty-state">
                                Belum ada posisi volunteer yang tersedia untuk event ini.
                            </div>
                        @endforelse
                    </section>

                    <!-- TENTANG EVENT -->
                    <section id="tentang" class="vd-content-card">
                        <h2>Tentang Event</h2>
                        <div class="vd-description">
                            {{ $event->description ?: 'Deskripsi event belum tersedia.' }}
                        </div>
                    </section>

                    <!-- BENEFIT -->
                    <section id="benefit" class="vd-content-card">
                        <h2>Benefit &amp; Fasilitas</h2>

                        @if (is_array($event->benefits) && count($event->benefits))
                            <ul class="vd-check-list">
                                @foreach ($event->benefits as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="vd-muted">Informasi benefit belum tersedia.</p>
                        @endif
                    </section>

                    <!-- SYARAT -->
                    <section id="syarat" class="vd-content-card">
                        <h2>Syarat &amp; Ketentuan</h2>

                        <ul class="vd-check-list vd-bullet-list">
                            @forelse ($event->positions->pluck('requirements')->filter()->unique() as $requirement)
                                <li>{{ $requirement }}</li>
                            @empty
                                <li>Persyaratan umum belum dicantumkan oleh penyelenggara.</li>
                            @endforelse
                        </ul>
                    </section>

                </div>

                <!-- KOLOM KANAN -->
                <aside class="vd-right-column">

                    <section class="vd-register-card">
                        <div class="vd-card-heading">
                            <h2>Pendaftaran Relawan</h2>
                            <span class="vd-online-badge">● Tersedia</span>
                        </div>

                        <div class="vd-selected-position">
                            <small>Total posisi tersedia</small>
                            <strong>{{ $event->positions->count() }} posisi</strong>
                            <p>Total kuota: {{ $event->positions->sum('quota') }} orang</p>
                        </div>

                        <a href="{{ route('login') }}" class="vd-apply-button">
                            ⊙ Login untuk Mendaftar
                        </a>

                        <p class="vd-register-note">
                            Login diperlukan untuk melanjutkan proses pendaftaran.
                        </p>
                    </section>

                    <section class="vd-content-card vd-organizer-card">
                        <h2>Penyelenggara</h2>
                        <h3>{{ $event->organizer?->organization_name ?? 'Penyelenggara VOLA' }}</h3>

                        @if ($event->organizer?->organization_type)
                            <p class="vd-muted">{{ $event->organizer->organization_type }}</p>
                        @endif

                        <p>
                            {{ $event->organizer?->description ?: 'Informasi organisasi belum tersedia.' }}
                        </p>

                        @if ($event->organizer?->city)
                            <p class="vd-organizer-city">⌖ {{ $event->organizer->city }}</p>
                        @endif
                    </section>

                    <section class="vd-content-card vd-location-card">
                        <h2>Lokasi Event</h2>
                        <div class="vd-map-placeholder">
                            <span>⌖</span>
                            <strong>{{ $event->city ?: 'Lokasi belum tersedia' }}</strong>
                        </div>
                        <p>
                            {{ $event->location ?: 'Alamat lengkap belum dicantumkan oleh penyelenggara.' }}
                        </p>
                    </section>

                </aside>
            </div>
        </main>

        <footer class="vd-footer">
            © {{ date('Y') }} VOLA Platform Relawan Indonesia. All rights reserved.
        </footer>
    </div>
</div>

</body>
</html>