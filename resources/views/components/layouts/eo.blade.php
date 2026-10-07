<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vola' }} — Event Organizer</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 font-sans" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-100 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-xl font-bold text-brand-600">VOLA<span class="text-gray-900">.</span></span>
                    <span class="text-xs text-gray-400 font-medium bg-gray-100 px-2 py-0.5 rounded-full">Event Organizer</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">

                <!-- MANAJEMEN EVENT -->
                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Manajemen Event</p>

                    <a href="{{ route('eo.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('eo.dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Beranda
                    </a>

                    <a href="{{ route('eo.events.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('eo.events.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Event Saya
                    </a>
                </div>

                <!-- REKRUTMEN -->
                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2 mt-4">Rekrutmen</p>

                    <!-- Placeholder - Fase 6 -->
                    <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed select-none"
                          title="Akan tersedia di pembaruan berikutnya">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pendaftar
                    </span>

                    <!-- Placeholder - Fase 6 -->
                    <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed select-none"
                          title="Akan tersedia di pembaruan berikutnya">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Koordinasi Kru
                    </span>
                </div>

                <!-- KEUANGAN -->
                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2 mt-4">Keuangan</p>

                    <!-- Placeholder - Fase 5 -->
                    <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed select-none"
                          title="Akan tersedia di pembaruan berikutnya">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Pembayaran & Tagihan
                    </span>
                </div>

                <!-- AKUN -->
                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2 mt-4">Akun</p>

                    <a href="{{ route('eo.organization') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('eo.organization') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Profil Organisasi
                    </a>
                </div>
            </nav>

            <!-- User info at bottom -->
            <div class="border-t border-gray-100 p-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center shrink-0">
                        <span class="text-brand-700 text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name ?? '' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div
            x-show="sidebarOpen"
            x-on:click="sidebarOpen = false"
            class="fixed inset-0 bg-black/30 z-30 lg:hidden"
            x-cloak
        ></div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64 flex flex-col">

            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">

                <!-- Mobile hamburger -->
                <button
                    type="button"
                    x-on:click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden text-gray-500 hover:text-gray-700 p-1 rounded-lg"
                    aria-label="Buka menu"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Search bar (placeholder) -->
                <div class="hidden sm:flex flex-1 max-w-sm mx-4">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                        <input type="text"
                               placeholder="Cari event, divisi..."
                               class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                               disabled>
                    </div>
                </div>

                <div class="flex-1 sm:flex-none"></div>

                <!-- Right side actions -->
                <div class="flex items-center gap-3">

                    <!-- Create Event Button -->
                    <a href="{{ route('eo.events.create') }}"
                       class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Event
                    </a>

                    <!-- User dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            x-on:click="open = !open"
                            class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none"
                        >
                            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                                <span class="text-brand-700 text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <span class="hidden sm:block font-medium">{{ auth()->user()->name ?? '' }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-on:click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white border border-gray-100 py-1 z-50"
                            x-cloak
                        >
                            <a href="{{ route('eo.organization') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Profil Organisasi
                            </a>
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Keluar
                                </button>
                            </form>
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
            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-100 py-4 px-4 sm:px-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} VOLA Platform Relawan Indonesia. All rights reserved.
            </footer>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
