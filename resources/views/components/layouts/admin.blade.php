<!DOCTYPE html>
<html lang="id" class="overscroll-none">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vola' }} — Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @php
        $adminUnreadInitial = \Illuminate\Support\Facades\Cache::remember(
            'admin_nav_unread_'.(auth()->id() ?? 0),
            30,
            fn () => \Illuminate\Support\Facades\DB::table('notifications')
                ->where('notifiable_type', \App\Models\User::class)
                ->where('notifiable_id', auth()->id())
                ->whereNull('read_at')
                ->count()
        );
    @endphp

    <script>
        function adminNavbar(initialUnread = 0) {
            return {
                sidebarOpen: false,

                // Global Search State
                searchQuery: '',
                searchResults: [],
                totalResults: 0,
                searchOpen: false,
                isSearching: false,
                searchTimer: null,
                searchAbortController: null,

                // Notifications State
                notifOpen: false,
                notifTab: 'all',
                notifications: [],
                unreadCount: initialUnread,
                totalCount: 0,
                loadingNotifs: false,

                // Profile Dropdown State
                profileOpen: false,
                logoutModalOpen: false,

                // Search Methods
                handleSearchInput() {
                    clearTimeout(this.searchTimer);
                    if (this.searchAbortController) {
                        this.searchAbortController.abort();
                        this.searchAbortController = null;
                    }

                    const query = this.searchQuery.trim();
                    if (query.length < 2) {
                        this.searchResults = [];
                        this.totalResults = 0;
                        this.searchOpen = false;
                        this.isSearching = false;
                        return;
                    }

                    this.isSearching = true;
                    this.searchOpen = true;

                    this.searchTimer = setTimeout(() => {
                        const currentQuery = this.searchQuery.trim();
                        if (currentQuery.length < 2) {
                            this.isSearching = false;
                            return;
                        }

                        this.searchAbortController = new AbortController();

                        fetch(`{{ route('admin.api.global-search') }}?q=${encodeURIComponent(currentQuery)}`, {
                            signal: this.searchAbortController.signal,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            // Validasi pencocokan query agar response lama yang terlambat tidak menimpa ketikan terbaru
                            if (currentQuery !== this.searchQuery.trim()) {
                                return;
                            }
                            if (data && data.success) {
                                this.searchResults = data.categories || [];
                                this.totalResults = data.total_results || 0;
                            }
                        })
                        .catch(err => {
                            if (err.name !== 'AbortError') {
                                console.error('Global search error:', err);
                            }
                        })
                        .finally(() => {
                            if (currentQuery === this.searchQuery.trim()) {
                                this.isSearching = false;
                            }
                        });
                    }, 350);
                },

                clearSearch() {
                    if (this.searchAbortController) {
                        this.searchAbortController.abort();
                        this.searchAbortController = null;
                    }
                    clearTimeout(this.searchTimer);
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.totalResults = 0;
                    this.searchOpen = false;
                    this.isSearching = false;
                },

                // Notifications Methods
                fetchNotifications() {
                    this.loadingNotifs = true;
                    fetch('{{ route('admin.api.notifications') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success) {
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unread_count || 0;
                            this.totalCount = data.total_count || 0;
                        }
                    })
                    .catch(err => console.error('Fetch notifications error:', err))
                    .finally(() => {
                        this.loadingNotifs = false;
                    });
                },

                filteredNotifications() {
                    if (this.notifTab === 'unread') {
                        return this.notifications.filter(n => !n.is_read);
                    }
                    return this.notifications;
                },

                handleNotificationClick(item) {
                    if (!item.is_read) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        fetch(`/admin/api/notifications/${item.id}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            item.is_read = true;
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                            window.location.href = data.redirect_url || item.url;
                        })
                        .catch(() => {
                            window.location.href = item.url;
                        });
                    } else {
                        window.location.href = item.url;
                    }
                },

                markAllAsRead() {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('{{ route('admin.api.notifications.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success) {
                            this.notifications.forEach(n => n.is_read = true);
                            this.unreadCount = 0;
                        }
                    })
                    .catch(err => console.error('Mark all read error:', err));
                }
            };
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 text-gray-900 font-sans overscroll-none" x-data="adminNavbar({{ $adminUnreadInitial }})" :class="{ 'overflow-hidden': sidebarOpen || logoutModalOpen }">

    <!-- Subtle Top Loading Bar -->
    <div id="admin-top-progress" class="fixed top-0 left-0 h-0.5 bg-gradient-to-r from-brand-600 via-indigo-500 to-[#4F46E5] z-[99999] transition-all duration-300 pointer-events-none opacity-0 w-0"></div>

    {{-- Tailwind compiler safelist for dynamic notification & search badge classes --}}
    <div class="hidden bg-emerald-50 bg-emerald-100 text-emerald-600 text-emerald-700 border-emerald-200 bg-indigo-50 bg-indigo-100 text-indigo-600 text-indigo-700 border-indigo-200 bg-purple-50 bg-purple-100 text-purple-600 text-purple-700 border-purple-200 bg-sky-50 bg-sky-100 text-sky-600 text-sky-700 border-sky-200 bg-rose-50 bg-rose-100 text-rose-600 text-rose-700 border-rose-200 bg-amber-50 bg-amber-100 text-amber-600 text-amber-700 border-amber-200"></div>

    <div class="min-h-screen flex overscroll-none">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col overscroll-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-100 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
                    <span class="text-xs text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-0.5 rounded-full">Admin</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1 overscroll-y-contain">

                @php
                    $sidebarCounts = \Illuminate\Support\Facades\Cache::remember('admin_sidebar_counts', 30, function () {
                        return [
                            'pending_eo' => \App\Models\OrganizerProfile::where('verification_status', 'pending')->count(),
                            'pending_review' => \App\Models\Event::where('status', \App\Enums\EventStatus::PendingReview)->count(),
                            'active_event' => \App\Models\Event::where('status', \App\Enums\EventStatus::Active)->count(),
                            'pending_reports' => \App\Models\Report::whereIn('status', [
                                \App\Enums\ReportStatus::Pending,
                                \App\Enums\ReportStatus::InReview,
                            ])->count(),
                        ];
                    });
                @endphp

                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Beranda
                        </div>
                    </a>

                    <a href="{{ route('admin.eo-verifications') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.eo-verifications') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Verifikasi EO
                        </div>
                        @if (($sidebarCounts['pending_eo'] ?? 0) > 0)
                            <span
                                class="bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $sidebarCounts['pending_eo'] }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.review-events.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.review-event*') || request()->routeIs('admin.review-events*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Review Event
                        </div>
                        @if (($sidebarCounts['pending_review'] ?? 0) > 0)
                            <span
                                class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $sidebarCounts['pending_review'] }}</span>
                        @endif
                    </a>

                    <!-- Event Aktif -->
                    <a href="{{ route('admin.active-events.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.active-events*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Event Aktif
                        </div>
                        @if (($sidebarCounts['active_event'] ?? 0) > 0)
                            <span
                                class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $sidebarCounts['active_event'] }}</span>
                        @endif
                    </a>

                    <!-- Pengguna -->
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Pengguna
                        </div>
                    </a>

                    <!-- Laporan -->
                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Laporan
                        </div>
                        @if (($sidebarCounts['pending_reports'] ?? 0) > 0)
                            <span
                                class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $sidebarCounts['pending_reports'] }}</span>
                        @endif
                    </a>

                    <!-- Transaksi -->
                    <a href="{{ route('admin.transactions.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.transactions*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Transaksi
                        </div>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Overlay mobile -->
        <div x-show="sidebarOpen" x-on:click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 lg:hidden"
            x-cloak></div>

        <!-- Main Content -->
        <div class="min-w-0 flex-1 lg:ml-64 min-h-screen flex flex-col justify-between overscroll-none">

            <!-- Topbar -->
            <header
                class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30 shrink-0">

                <div class="flex items-center gap-2 flex-1 max-w-xl">
                    <!-- Mobile hamburger -->
                    <button type="button" x-on:click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 shrink-0"
                        aria-label="Buka menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Global Search Bar -->
                    <div class="relative w-full max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                                </svg>
                            </div>
                            <input type="text"
                                x-model="searchQuery"
                                x-on:input="handleSearchInput()"
                                x-on:focus="if (searchQuery.trim().length >= 2) searchOpen = true"
                                x-on:keydown.escape="searchOpen = false"
                                placeholder="Cari organisasi EO, judul event, pengguna, tiket laporan, atau invoice"
                                class="w-full pl-10 pr-9 py-2 text-xs sm:text-sm bg-gray-50 hover:bg-gray-100/70 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition-all text-gray-800 placeholder:text-gray-400">

                            <!-- Clear / Spinner button -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="isSearching" class="animate-spin w-4 h-4 text-brand-600" fill="none" viewBox="0 0 24 24" x-cloak>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <button x-show="!isSearching && searchQuery.length > 0" x-on:click="clearSearch()" type="button" class="text-gray-400 hover:text-gray-600" x-cloak>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Global Search Dropdown Results -->
                        <div x-show="searchOpen"
                            x-on:click.away="searchOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-full sm:w-[480px] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50 max-h-[460px] flex flex-col"
                            x-cloak>

                            <!-- Search Result Header -->
                            <div class="px-4 py-2.5 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
                                <span x-text="searchQuery ? 'Hasil untuk &quot;' + searchQuery + '&quot;' : 'Pencarian Global'"></span>
                                <span x-show="totalResults > 0" class="font-semibold text-brand-600" x-text="totalResults + ' item ditemukan'"></span>
                            </div>

                            <!-- Searching state -->
                            <div x-show="isSearching" class="py-8 text-center text-gray-400">
                                <svg class="animate-spin w-6 h-6 text-brand-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-xs font-medium text-gray-500">Mencari di seluruh modul Admin...</p>
                            </div>

                            <!-- Empty result state -->
                            <div x-show="!isSearching && totalResults === 0" class="py-8 px-4 text-center text-gray-400">
                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                                </svg>
                                <p class="text-xs font-medium text-gray-600">Tidak ada hasil ditemukan untuk "<span class="font-bold text-gray-800" x-text="searchQuery"></span>"</p>
                                <p class="text-[11px] text-gray-400 mt-1">Coba gunakan kata kunci nama organisasi, judul event, PIC, kota, nomor NIB, atau tiket aduan.</p>
                            </div>

                            <!-- Results list grouped by category -->
                            <div x-show="!isSearching && totalResults > 0" class="overflow-y-auto max-h-[380px] p-2 divide-y divide-gray-50 overscroll-y-contain">
                                <template x-for="category in searchResults" :key="category.name">
                                    <div class="py-2 first:pt-0 last:pb-0">
                                        <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 flex items-center justify-between">
                                            <span x-text="category.name"></span>
                                            <span class="text-[10px] text-gray-400" x-text="category.items.length + ' hasil'"></span>
                                        </div>
                                        <div class="mt-1 space-y-0.5">
                                            <template x-for="item in category.items" :key="item.id">
                                                <a :href="item.url" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-gray-50/80 transition group">
                                                    <div class="min-w-0 flex-1 pr-3">
                                                        <p class="text-xs font-bold text-gray-900 group-hover:text-brand-600 truncate" x-text="item.title"></p>
                                                        <p class="text-[11px] text-gray-500 truncate mt-0.5" x-text="item.subtitle"></p>
                                                    </div>
                                                    <span :class="item.badge_color" class="text-[10px] font-bold px-2 py-0.5 rounded-full border shrink-0" x-text="item.badge"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer with shortcut guide -->
                            <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                <span>Tekan <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-mono">ESC</kbd> untuk menutup</span>
                                <span x-text="totalResults > 0 ? (totalResults + ' item di ' + searchResults.length + ' modul') : '7 Modul Terintegrasi'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side actions: Notifications & User profile -->
                <div class="flex items-center gap-3 shrink-0">

                    <!-- Notification Bell Dropdown -->
                    <div class="relative">
                        <button type="button"
                            x-on:click="notifOpen = !notifOpen; if(notifOpen && notifications.length === 0) fetchNotifications()"
                            class="relative p-2 text-gray-500 hover:text-gray-800 transition-colors rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                            aria-label="Buka notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Red pill unread count badge -->
                            <span x-show="unreadCount > 0"
                                x-text="unreadCount"
                                class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1 border-2 border-white shadow-sm"
                                x-cloak></span>
                        </button>

                        <!-- Notifications Dropdown Card -->
                        <div x-show="notifOpen"
                            x-on:click.away="notifOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-3 w-80 sm:w-96 md:w-[420px] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50 flex flex-col"
                            x-cloak>

                            <!-- Dropdown Header -->
                            <div class="px-5 pt-4 pb-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-900 text-base tracking-tight">Notifikasi</h3>
                                    <span x-show="unreadCount > 0"
                                        class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-0.5 rounded-full"
                                        x-text="unreadCount + ' Baru'"
                                        x-cloak></span>
                                </div>
                                <button type="button"
                                    x-on:click="markAllAsRead()"
                                    class="text-xs font-medium text-brand-600 hover:text-brand-800 transition">
                                    Tandai semua dibaca
                                </button>
                            </div>

                            <!-- Tabs: Semua & Belum Dibaca -->
                            <div class="px-5 pb-3 flex items-center gap-2 border-b border-gray-100">
                                <button type="button"
                                    x-on:click="notifTab = 'all'"
                                    :class="notifTab === 'all' ? 'bg-[#1E1B4B] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                    class="px-3.5 py-1 text-xs font-semibold rounded-full transition-colors flex items-center gap-1.5">
                                    <span>Semua</span>
                                    <span :class="notifTab === 'all' ? 'text-indigo-200' : 'text-gray-400'" class="text-[11px]" x-text="'(' + totalCount + ')'"></span>
                                </button>
                                <button type="button"
                                    x-on:click="notifTab = 'unread'"
                                    :class="notifTab === 'unread' ? 'bg-[#1E1B4B] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                    class="px-3.5 py-1 text-xs font-semibold rounded-full transition-colors flex items-center gap-1.5">
                                    <span>Belum Dibaca</span>
                                    <span :class="notifTab === 'unread' ? 'text-indigo-200' : 'text-gray-400'" class="text-[11px]" x-text="'(' + unreadCount + ')'"></span>
                                </button>
                            </div>

                            <!-- Notifications List -->
                            <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-50 overscroll-y-contain">

                                <!-- Loading state -->
                                <div x-show="loadingNotifs" class="py-10 text-center text-gray-400">
                                    <svg class="animate-spin w-6 h-6 text-brand-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-500">Memuat notifikasi...</p>
                                </div>

                                <!-- Empty state -->
                                <div x-show="!loadingNotifs && filteredNotifications().length === 0" class="py-12 px-4 text-center text-gray-400">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-600">Tidak ada notifikasi</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Semua pembaruan aktivitas sistem telah ditinjau.</p>
                                </div>

                                <!-- Items -->
                                <template x-for="item in filteredNotifications()" :key="item.id">
                                    <div x-on:click="handleNotificationClick(item)"
                                        class="p-4 hover:bg-gray-50/80 transition cursor-pointer flex items-start gap-3 relative group"
                                        :class="{ 'bg-brand-50/15': !item.is_read }">

                                        <!-- Category Circular Icon -->
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                                            :class="item.icon_bg">
                                            <template x-if="item.icon === 'check' || item.category === 'payment'">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </template>
                                            <template x-if="item.icon === 'users' || item.icon === 'sparkles' || item.category === 'volunteer' || item.category === 'review'">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </template>
                                            <template x-if="item.icon === 'event' || item.category === 'event'">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </template>
                                            <template x-if="item.icon === 'calendar' || item.icon === 'shield' || item.category === 'eo'">
                                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </template>
                                            <template x-if="item.icon === 'alert' || item.category === 'report'">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </template>
                                            <template x-if="!['check', 'users', 'sparkles', 'event', 'calendar', 'shield', 'alert'].includes(item.icon) && !['payment', 'volunteer', 'review', 'event', 'eo', 'report'].includes(item.category)">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                            </template>
                                        </div>

                                        <!-- Content details -->
                                        <div class="flex-1 min-w-0 pr-2">
                                            <h4 class="text-xs font-bold text-gray-900 leading-snug line-clamp-1" x-text="item.title"></h4>
                                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2 leading-relaxed" x-text="item.description"></p>
                                            <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 mt-1.5">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span x-text="item.created_at_human"></span>
                                            </span>
                                        </div>

                                        <!-- Unread blue indicator dot -->
                                        <div x-show="!item.is_read" class="w-2 h-2 rounded-full bg-blue-600 shrink-0 mt-2" x-cloak></div>
                                    </div>
                                </template>
                            </div>

                            <!-- Dropdown Footer -->
                            <div class="border-t border-gray-100 p-3 bg-gray-50/50 rounded-b-2xl text-center">
                                <a href="{{ route('admin.dashboard') }}#riwayat-log"
                                    x-on:click="notifOpen = false"
                                    class="text-xs font-semibold text-brand-600 hover:text-brand-800 transition inline-flex items-center gap-1">
                                    <span>Lihat Semua Notifikasi</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown (Clean Logout Only) -->
                    <div class="relative ml-1">
                        <button type="button"
                            x-on:click="profileOpen = !profileOpen"
                            class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none p-1 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="relative w-8 h-8 rounded-full bg-brand-600 overflow-hidden shrink-0 flex items-center justify-center text-white text-xs font-bold tracking-wider shadow-sm">
                                @if(auth()->user()?->avatar_path)
                                    <img src="{{ auth()->user()->avatar_url }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                    <div class="hidden w-full h-full flex items-center justify-center bg-brand-600 text-white font-bold text-xs">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                                    </div>
                                @else
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                                @endif
                            </div>
                            <span class="hidden sm:block font-semibold text-xs text-gray-800">{{ auth()->user()->name ?? 'Admin VOLA' }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="profileOpen ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Minimalist Profile Dropdown - STRICTLY LOGOUT ONLY -->
                        <div x-show="profileOpen"
                            x-on:click.away="profileOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 rounded-xl shadow-xl bg-white border border-gray-100 p-1.5 z-50"
                            x-cloak>
                            <button type="button"
                                x-on:click="logoutModalOpen = true; profileOpen = false"
                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-left group">
                                <svg class="w-4 h-4 text-rose-500 group-hover:text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            @if (session('warning'))
                <div class="mx-4 sm:mx-6 mt-4">
                    <x-alert type="warning">{{ session('warning') }}</x-alert>
                </div>
            @endif

            <!-- Page Content -->
            <main id="admin-main-content" class="min-w-0 flex-1 p-4 sm:p-6 lg:p-8 relative overscroll-none w-full">
                <!-- Background Decoration Aura & Subtle Grid -->
                <div class="absolute top-0 inset-x-0 h-96 bg-gradient-to-b from-brand-50/40 via-indigo-50/15 to-transparent pointer-events-none z-0"></div>
                <div class="absolute inset-0 opacity-40 pointer-events-none z-0" style="background-image: radial-gradient(rgba(99, 102, 241, 0.08) 1px, transparent 1px); background-size: 24px 24px;"></div>
                <div id="admin-slot-container" class="min-w-0 relative z-10 w-full opacity-100">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="shrink-0 mt-auto pt-5 pb-6 px-4 sm:px-6 lg:px-8 border-t border-gray-200/70 text-xs text-gray-400">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 text-center sm:text-left">
                        <span class="font-bold text-gray-600">VOLA<span class="text-brand-600">.</span></span>
                        <span>&copy; {{ date('Y') }} Platform Manajemen & Rekrutmen Crew Indonesia.</span>
                    </div>
                    <div class="text-gray-400">
                        Panel Administrasi Internal
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL KONFIRMASI LOGOUT ADMINISTRATOR          -->
    <!-- ============================================== -->
    <div x-show="logoutModalOpen" 
         x-on:keydown.escape.window="if (!window.adminLightboxOpen && !document.querySelector('[data-admin-lightbox][data-open=\'true\']')) logoutModalOpen = false"
         class="fixed inset-0 z-40 overflow-y-auto overscroll-y-contain"
         style="z-index: 40; display: none;"
         x-cloak>
        
        <!-- Backdrop Overlay with subtle blur: Menutupi seluruh layar -->
        <div x-show="logoutModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40"
             style="z-index: 40;"
             x-on:click="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return; logoutModalOpen = false"
             x-on:mousedown.self="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return;"></div>

        <!-- Modal Dialog Content: Full viewport centering dengan safe padding -->
        <div class="fixed inset-0 z-40 flex items-center justify-center p-6 sm:p-8 lg:pl-64"
             style="z-index: 40;"
             x-on:click.self="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return; logoutModalOpen = false"
             x-on:mousedown.self="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return;">
            <div x-show="logoutModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 x-on:click.stop
                 @click.outside="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return; logoutModalOpen = false"
                 class="w-full max-w-md bg-white rounded-2xl p-5 shadow-2xl border border-gray-100 text-left relative z-10 overflow-hidden my-auto flex flex-col space-y-3.5">
                
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Konfirmasi Keluar Sesi</h3>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                            Apakah Anda yakin ingin mengakhiri sesi Administrator VOLA?
                        </p>
                    </div>
                </div>

                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-600 space-y-1">
                    <div class="flex items-center gap-1.5 font-semibold text-gray-800 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tujuan Pengalihan:</span>
                    </div>
                    <p class="text-gray-500 pl-5 text-[11px] leading-relaxed">
                        Sesi ditutup dan Anda dialihkan ke <span class="font-bold text-gray-800">Halaman Masuk Administrator</span>.
                    </p>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button" 
                            x-on:click="logoutModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all inline-flex items-center gap-1.5">
                            <span>Ya, Keluar</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Global Image & Document Lightbox -->
    <x-admin-lightbox />

    <!-- Global Admin Interaction & Seamless Partial Swap Scripts -->
    <x-admin-scripts />

    @stack('scripts')
</body>

</html>
