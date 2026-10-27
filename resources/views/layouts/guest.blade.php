<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen overscroll-none">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VOLA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-50/50 relative isolate min-h-screen flex flex-col overscroll-none overflow-x-hidden selection:bg-indigo-500 selection:text-white">
        
        <!-- =========================================================
             LIQUID AURORA BLOBS & EXTREME RANDOM FLOATING ICONS
             ========================================================= -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none select-none z-0" aria-hidden="true">
            
            <!-- Blob Warna (Dipisah posisinya agar tidak gampang nyatu di tengah) -->
            <div class="absolute top-[-10%] left-[-10%] bg-indigo-400 opacity-60 blur-[130px] mix-blend-multiply animate-aurora-1 rounded-[42%_58%_70%_30%_/_45%_45%_55%_55%] w-[60vw] h-[45vw] min-w-[500px] min-h-[400px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] bg-blue-400 opacity-60 blur-[130px] mix-blend-multiply animate-aurora-2 rounded-[55%_45%_35%_65%_/_60%_35%_65%_40%] w-[55vw] h-[40vw] min-w-[520px] min-h-[380px]"></div>
            <div class="absolute bottom-[-20%] left-[10%] bg-emerald-300 opacity-55 blur-[130px] mix-blend-multiply animate-aurora-3 rounded-[65%_35%_50%_50%_/_35%_65%_35%_65%] w-[50vw] h-[42vw] min-w-[460px] min-h-[380px]"></div>

            <!-- Ikon Partikel (Diperbanyak jadi 10 & disebar acak ekstrem) -->
            <!-- Kiri Atas (Terpotong) -->
            <svg class="absolute -left-6 -top-4 w-10 h-10 text-indigo-900/20 rotate-12 animate-float-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            <!-- Kanan Atas (Tengah ke Kanan) -->
            <svg class="absolute right-[15%] top-[8%] w-8 h-8 text-indigo-900/20 -rotate-12 animate-float-reverse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            <!-- Kiri Tengah -->
            <svg class="absolute left-[8%] top-[35%] w-7 h-7 text-indigo-900/15 rotate-45 animate-float-drift" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <!-- Kanan Tengah (Terpotong) -->
            <svg class="absolute -right-5 top-[45%] w-12 h-12 text-indigo-900/20 -rotate-45 animate-float-slow" style="animation-delay: -2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <!-- Bawah Tengah Kiri -->
            <svg class="absolute left-[25%] bottom-[15%] w-8 h-8 text-indigo-900/15 -rotate-12 animate-float-reverse" style="animation-delay: -3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <!-- Kanan Bawah Ekstrem -->
            <svg class="absolute right-[5%] bottom-[-5%] w-10 h-10 text-indigo-900/20 rotate-[20deg] animate-float-drift" style="animation-delay: -1s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <!-- Tengah Layar Atas -->
            <svg class="absolute left-[45%] top-[-3%] w-9 h-9 text-indigo-900/15 rotate-[60deg] animate-float-slow" style="animation-delay: -4s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <!-- Tambahan Acak 1 -->
            <svg class="absolute right-[30%] bottom-[30%] w-7 h-7 text-indigo-900/15 -rotate-[30deg] animate-float-reverse" style="animation-delay: -2.5s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <!-- Tambahan Acak 2 -->
            <svg class="absolute left-[15%] bottom-[-2%] w-8 h-8 text-indigo-900/20 rotate-[75deg] animate-float-drift" style="animation-delay: -1.5s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            <!-- Tambahan Acak 3 -->
            <svg class="absolute left-[35%] top-[20%] w-6 h-6 text-indigo-900/15 -rotate-12 animate-float-slow" style="animation-delay: -5s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
        </div>

        <!-- =========================================================
             HEADER (Z-INDEX 20)
             ========================================================= -->
        <header class="relative z-20 w-full px-6 sm:px-8 py-5 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center space-x-2">
                <span class="text-2xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
            </div>
            <a href="/" class="group flex items-center text-sm font-semibold text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1.5 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </header>

        <!-- =========================================================
             MAIN CONTENT WRAPPER (Z-INDEX 10)
             ========================================================= -->
        <main class="relative z-10 flex-1 flex items-center justify-center py-6 sm:py-10 px-4 sm:px-6">
            <div class="w-full max-w-[480px]">
                {{ $slot }}
            </div>
        </main>
        
        <!-- =========================================================
             FOOTER (Z-INDEX 20)
             ========================================================= -->
        <footer class="relative z-20 py-5 text-center text-xs font-medium text-gray-400 flex-shrink-0">
            &copy; {{ date('Y') }} VOLA Platform Relawan Indonesia. All rights reserved.
        </footer>
    </body>
</html>