<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'VOLA - Beranda Crew' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

@php
    $authUser = auth()->user();
    $profile = $authUser?->volunteerProfile;
    $displayName = $authUser?->name ?? 'Crew';
    $displayCity = $authUser->city ?? 'Indonesia';
    $unreadNotifs = $authUser?->unreadNotifications?->count() ?? 0;

    // Avatar Logic
    $displayAvatar = null;
    if ($authUser?->avatar_path) {
        $displayAvatar = filter_var($authUser->avatar_path, FILTER_VALIDATE_URL)
            ? $authUser->avatar_path
            : \Illuminate\Support\Facades\Storage::url($authUser->avatar_path);
    }
    $words = array_values(array_filter(explode(' ', trim($displayName))));
    $initials = count($words) >= 2 
        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
        : strtoupper(substr($displayName, 0, 2));
@endphp

<div class="flex min-h-screen">
    
    <aside class="hidden md:flex w-64 flex-col bg-white border-r border-gray-200 fixed h-full z-10">
        <div class="p-6 flex items-center gap-1">
            <span class="text-2xl font-black tracking-tighter text-gray-900">VOLA<span class="text-[#4F46E5]">.</span></span>
            <span class="ml-2 px-2 py-0.5 text-xs font-bold bg-indigo-50 text-[#4F46E5] rounded-md border border-indigo-100">Crew</span>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
            <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-4">Menu Utama</p>
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-[#4F46E5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda
            </a>
            
            <!-- PERBAIKAN: Menu ini akan aktif juga saat di halaman detail event -->
            <a href="{{ route('search.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('search.*') || request()->routeIs('events.detail') ? 'bg-indigo-50 text-[#4F46E5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Event
            </a>

            <a href="#" class="flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lamaran Saya
                </div>
                @if(isset($activeApplicationsCount) && $activeApplicationsCount > 0)
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $activeApplicationsCount }}</span>
                @endif
            </a>

            <a href="#" class="flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Event Saya
                </div>
                @if(isset($acceptedCount) && $acceptedCount > 0)
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $acceptedCount }}</span>
                @endif
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat
            </a>

            <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-8">Akun</p>
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-[#4F46E5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profil Saya
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col md:pl-64 w-full">
        
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-200 px-6 py-3 flex items-center justify-between gap-4">
          
            @if(!request()->routeIs('search.*'))
                <form action="{{ route('search.index') }}" method="GET" class="hidden sm:flex flex-1 max-w-lg relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-[#4F46E5] focus:border-[#4F46E5] sm:text-sm transition" 
                        placeholder="Cari event, posisi volunteer, atau lokasi..."
                    >
                </form>
            @endif

            <div class="flex items-center gap-4 ml-auto">
                <!-- Status Badge -->
                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-full">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold text-emerald-700">Status: Siap Bertugas</span>
                </div>

                <!-- Notification Dropdown (Versi Hardcode yang Stabil) -->
                <div x-data="{ 
                        open: false, 
                        activeTab: 'all',
                        notifications: [
                            {
                                id: 1,
                                type: 'accepted',
                                title: 'Lamaran Diterima!',
                                message: 'Selamat, kamu diterima sebagai <strong>Liaison Officer</strong> untuk event <strong>Indonesia Comic & Pop Culture Con 2025</strong>.',
                                time: '15 mnt lalu',
                                read: false,
                                link: '#'
                            },
                            {
                                id: 2,
                                type: 'briefing',
                                title: 'Pengingat Briefing Online',
                                message: 'Briefing teknis via Zoom akan diselenggarakan besok pukul 19.30 WIB. Tautan ruang pertemuan telah tersedia.',
                                time: '2 jam lalu',
                                read: false,
                                link: '#'
                            },
                            {
                                id: 3,
                                type: 'review',
                                title: 'Berkas Sedang Ditinjau',
                                message: 'Berkas lamaranmu untuk <strong>Bandung Creative Tech Summit 2025</strong> sedang dalam proses kurasi panitia.',
                                time: 'Kemarin, 14:15',
                                read: true,
                                link: '#'
                            },
                            {
                                id: 4,
                                type: 'reminder',
                                title: 'Pengingat Profil',
                                message: 'Lengkapi profil dan unggah CV kamu untuk meningkatkan peluang diterima di event favoritmu.',
                                time: '2 hari lalu',
                                read: true,
                                link: '#'
                            }
                        ],
                        get unreadCount() {
                            return this.notifications.filter(n => !n.read).length;
                        },
                        get hasVisibleNotifications() {
                            if (this.activeTab === 'all') return this.notifications.length > 0;
                            return this.notifications.some(n => !n.read);
                        },
                        get filteredNotifications() {
                            if (this.activeTab === 'all') return this.notifications;
                            return this.notifications.filter(n => !n.read);
                        },
                        markAsRead(id) {
                            const notif = this.notifications.find(n => n.id === id);
                            if (notif) notif.read = true;
                        },
                        markAllAsRead() {
                            this.notifications.forEach(n => n.read = true);
                        }
                     }" 
                     class="relative" @click.outside="open = false">
                    
                    <!-- Tombol Notifikasi -->
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-[#4F46E5] hover:bg-indigo-50 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadCount > 0" 
                              class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white" 
                              x-text="unreadCount"></span>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-[420px] bg-white rounded-2xl shadow-xl ring-1 ring-gray-200 overflow-hidden origin-top-right z-50">
                        
                        <!-- Header -->
                        <div class="px-5 pt-4 pb-3 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-900 text-base">Notifikasi</h3>
                                    <span x-show="unreadCount > 0" class="px-2 py-0.5 bg-indigo-50 text-[#4F46E5] text-xs font-bold rounded-full" x-text="unreadCount + ' Baru'"></span>
                                </div>
                                <button @click="markAllAsRead()" class="text-xs font-bold text-[#4F46E5] hover:text-indigo-800 transition">Tandai semua dibaca</button>
                            </div>
                            
                            <!-- Tab Filter -->
                            <div class="flex gap-5 text-sm border-b border-gray-100">
                                <button @click="activeTab = 'all'" 
                                        :class="activeTab === 'all' ? 'text-[#4F46E5] border-b-2 border-[#4F46E5] font-bold' : 'text-gray-500 hover:text-gray-700'"
                                        class="pb-2.5 transition">
                                    Semua (<span x-text="notifications.length"></span>)
                                </button>
                                <button @click="activeTab = 'unread'" 
                                        :class="activeTab === 'unread' ? 'text-[#4F46E5] border-b-2 border-[#4F46E5] font-bold' : 'text-gray-500 hover:text-gray-700'"
                                        class="pb-2.5 transition">
                                    Belum Dibaca (<span x-text="unreadCount"></span>)
                                </button>
                            </div>
                        </div>
                        
                        <!-- List Notifikasi -->
                        <div class="max-h-[420px] overflow-y-auto">
                            <template x-for="notif in filteredNotifications" :key="notif.id">
                                <div @click="markAsRead(notif.id)"
                                     class="px-5 py-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-b-0 cursor-pointer"
                                     :class="!notif.read ? 'bg-white' : 'bg-gray-50/50'">
                                    <div class="flex gap-3">
                                        <!-- Icon Bulat -->
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                             :class="{
                                                'bg-emerald-100 text-emerald-600': notif.type === 'accepted',
                                                'bg-indigo-100 text-indigo-600': notif.type === 'briefing',
                                                'bg-blue-100 text-blue-600': notif.type === 'review',
                                                'bg-amber-100 text-amber-600': notif.type === 'reminder'
                                             }">
                                            <template x-if="notif.type === 'accepted'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </template>
                                            <template x-if="notif.type === 'briefing'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </template>
                                            <template x-if="notif.type === 'review'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </template>
                                            <template x-if="notif.type === 'reminder'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </template>
                                        </div>
                                        
                                        <!-- Konten -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2 mb-1">
                                                <div class="flex items-center gap-1.5">
                                                    <p class="text-sm font-bold text-gray-900" x-html="notif.title"></p>
                                                    <span x-show="!notif.read" class="w-1.5 h-1.5 rounded-full bg-[#4F46E5]"></span>
                                                </div>
                                                <span class="text-[11px] text-gray-400 whitespace-nowrap shrink-0" x-text="notif.time"></span>
                                            </div>
                                            <p class="text-xs text-gray-600 leading-relaxed mb-1.5" x-html="notif.message"></p>
                                            
                                            <!-- Link Detail -->
                                            <template x-if="notif.link && notif.link !== '#'">
                                                <a :href="notif.link" @click.stop class="inline-flex items-center gap-1 text-xs font-bold text-[#4F46E5] hover:text-indigo-800 transition mt-1">
                                                    Lihat Detail Penugasan
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Empty State -->
                            <div x-show="!hasVisibleNotifications" class="px-5 py-12 text-center">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500" x-text="activeTab === 'unread' ? 'Semua notifikasi sudah dibaca' : 'Belum ada notifikasi'"></p>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-center">
                            <button @click="markAllAsRead()" class="text-xs font-bold text-[#4F46E5] hover:text-indigo-800 transition">Tandai Semua Notifikasi Terbaca</button>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 hover:bg-gray-50 p-1.5 pr-3 rounded-full transition border border-transparent hover:border-gray-200">
                        @if($displayAvatar)
                            <img src="{{ $displayAvatar }}" alt="{{ $displayName }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-9 h-9 rounded-full bg-[#4F46E5] text-white flex items-center justify-center text-sm font-bold border border-indigo-200">
                                {{ $initials }}
                            </div>
                        @endif
                        <div class="hidden lg:block text-left">
                            <p class="text-sm font-bold text-gray-900 leading-none">{{ $displayName }}</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $displayCity }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden z-50 py-2">
                        
                        <div class="px-5 py-3 border-b border-gray-100 mb-1">
                            <p class="text-xs font-bold text-gray-900 tracking-wide">Masuk sebagai</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ $authUser->email }}</p>
                        </div>

                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#4F46E5] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Lihat Profil</span>
                        </a>

                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-[#4F46E5] transition group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#4F46E5] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Pengaturan</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-5 py-2.5 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 lg:p-8">
            {{ $slot }}
        </main>

        <footer class="py-6 text-center text-xs text-gray-400 border-t border-gray-200 bg-white">
            © 2026 VOLA Platform Relawan Indonesia. All rights reserved.
        </footer>
    </div>
</div>
</body>
</html>