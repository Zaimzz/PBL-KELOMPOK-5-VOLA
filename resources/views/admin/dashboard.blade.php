<x-layouts.admin title="Beranda Admin">
    
    <!-- Greeting Section Dinamis -->
    <div class="mb-8 bg-gradient-to-r from-brand-600 via-indigo-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <!-- Background subtle glow -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-20 top-0 w-32 h-32 bg-indigo-400/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-white/15 text-white backdrop-blur-xs mb-3 border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    PUSAT KONTROL UTAMA
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Selamat {{ $greetingTime }}, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="text-indigo-100 text-sm mt-1.5 max-w-xl leading-relaxed">
                    Ada <strong class="text-white font-bold">{{ $pendingEoCount }} penyelenggara</strong> baru yang butuh verifikasi dan <strong class="text-white font-bold">{{ $pendingReviewEventCount }} event</strong> menanti kurasi kelayakan hari ini.
                </p>
            </div>

            <!-- Quick Jump Actions in Greeting -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.eo-verifications') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-brand-700 hover:bg-indigo-50 font-bold text-xs rounded-xl shadow-md transition-all active:scale-95">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Verifikasi EO
                    @if($pendingEoCount > 0)
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-extrabold">{{ $pendingEoCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.review-events.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-500/40 hover:bg-indigo-500/60 border border-white/30 text-white font-bold text-xs rounded-xl backdrop-blur-xs transition-all active:scale-95">
                    <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Review Event
                    @if($pendingReviewEventCount > 0)
                        <span class="bg-rose-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-extrabold">{{ $pendingReviewEventCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Overview Metrics (At a Glance) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- 1. Total Event Berjalan -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:border-brand-200 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500">Total Event Berjalan</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($activeEventCount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1 mt-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Publikasi aktif & terbuka
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-brand-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- 2. Transaksi Bulan Ini -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:border-brand-200 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500">Transaksi Bulan Ini</span>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-500 font-medium mt-2">
                        Dari {{ $transactionCount }} riwayat pembayaran
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- 3. Laporan Menunggu Tindakan -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:border-brand-200 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500">Laporan Menunggu</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($pendingReportsCount, 0, ',', '.') }}</h3>
                    <p class="text-xs font-semibold {{ $pendingReportsCount > 0 ? 'text-amber-600' : 'text-gray-400' }} mt-2">
                        {{ $pendingReportsCount > 0 ? 'Aduan butuh tindakan admin' : 'Tidak ada aduan aktif' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- 4. Total Relawan -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:border-brand-200 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500">Total Relawan</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalVolunteersCount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-blue-600 font-semibold mt-2">
                        Volunteer terdaftar di platform
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Cards Grid (Aksi Cepat) -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Aksi Cepat Moderasi</h2>
            <span class="text-xs text-gray-400">Pintasan menu operasional admin</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <!-- 1. Katalog Event Publik -->
            <a href="{{ route('admin.active-events.index') }}"
               class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-brand-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 text-sm truncate">Katalog Event Publik</div>
                    <div class="text-[11px] text-gray-400 truncate">{{ $activeEventCount }} event aktif</div>
                </div>
            </a>

            <!-- 2. Kontrol Basis Akun -->
            <a href="{{ route('admin.users.index') }}"
               class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 text-sm truncate">Kontrol Basis Akun</div>
                    <div class="text-[11px] text-gray-400 truncate">Volunteer & EO</div>
                </div>
            </a>

            <!-- 3. Rekap Transaksi -->
            <a href="{{ route('admin.transactions.index') }}"
               class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 text-sm truncate">Data Transaksi</div>
                    <div class="text-[11px] text-gray-400 truncate">Ekspor & Invoice</div>
                </div>
            </a>

            <!-- 4. Laporan Komunitas -->
            <a href="{{ route('admin.reports.index') }}"
               class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 text-sm truncate">Laporan Komunitas</div>
                    <div class="text-[11px] text-gray-400 truncate">{{ $pendingReportsCount }} perlu aksi</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Grid: Pending Tasks (Left 7 Cols) & Recent Activity Feed (Right 5 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Pending Tasks Widget (Antrean Kurasi & Verifikasi yang Belum Selesai) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Antrean Perlu Tindakan</h2>
                    <p class="text-xs text-gray-500">Pekerjaan prioritas yang membutuhkan persetujuan admin segera</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $actionRequired->count() }} antrean
                </span>
            </div>

            @if($actionRequired->isEmpty())
                <div class="bg-white border border-gray-100 rounded-2xl p-8 text-center shadow-sm">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-sm mb-1">Semua Antrean Bersih!</h3>
                    <p class="text-xs text-gray-500">Tidak ada pengajuan event atau verifikasi EO yang tertunda saat ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($actionRequired as $action)
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:border-gray-200 transition-all duration-300 hover:shadow-md flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $action['icon_color'] }}">
                                @if($action['type'] == 'event')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $action['type'] == 'event' ? 'text-indigo-700 bg-indigo-50 border border-indigo-200/50' : 'text-amber-700 bg-amber-50 border border-amber-200/50' }} px-2 py-0.5 rounded-full">
                                        {{ $action['type'] == 'event' ? 'Pengajuan Event' : 'Verifikasi EO' }}
                                    </span>
                                    <span class="text-xs text-gray-400">&bull; {{ $action['time'] }}</span>
                                </div>
                                <h3 class="font-bold text-gray-900 text-sm truncate">{{ $action['title'] }}</h3>
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $action['subtitle'] }}</p>
                            </div>
                            <div class="shrink-0 self-center">
                                <a href="{{ $action['action_link'] }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 text-xs font-bold rounded-xl {{ $action['type'] == 'event' ? 'bg-brand-600 text-white hover:bg-brand-700 shadow-brand-500/20' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }} transition-all duration-200 shadow-xs active:scale-95">
                                    <span>{{ $action['action_text'] }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Activity Feed (Aktivitas Terkini Platform) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Aktivitas Terkini</h2>
                    <p class="text-xs text-gray-500">Log riwayat interaksi terbaru di sistem VOLA</p>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm divide-y divide-gray-100">
                @forelse($recentActivities as $act)
                    <div class="py-3.5 first:pt-0 last:pb-0 flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-brand-600 mt-1.5 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $act['badge_color'] }}">
                                    {{ $act['badge'] }}
                                </span>
                                <span class="text-[11px] text-gray-400 shrink-0">{{ $act['time'] }}</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-xs mt-1 truncate">{{ $act['title'] }}</h4>
                            <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1 leading-relaxed">{{ $act['description'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Belum ada riwayat aktivitas terbaru yang tercatat di sistem.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</x-layouts.admin>
