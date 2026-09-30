<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vola - Platform Volunteer & Event' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold text-brand-600">
                    VOLA<span class="text-gray-900">.</span>
                </a>

                {{-- <div class="hidden sm:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-brand-600">Beranda</a>
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-brand-600">Cari Event</a>
                </div> --}}

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-brand-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-brand-600">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-100 mt-16 py-8 text-center text-sm text-gray-400">
        &copy; {{ date('Y') }} Vola Platform Relawan Indonesia. All rights reserved.
    </footer>

</body>
</html>