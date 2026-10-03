<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body class="font-sans text-gray-900 antialiased bg-[#FAF9FF] relative min-h-screen flex flex-col overflow-x-hidden">
        
        <!-- Mesh Gradient Background Blobs -->
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-purple-200 rounded-full mix-blend-multiply filter blur-[100px] opacity-50 z-0"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-emerald-100 rounded-full mix-blend-multiply filter blur-[120px] opacity-60 z-0"></div>
        
        <!-- Header -->
        <header class="relative z-10 w-full px-8 py-6 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-2xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">Explore</span>
            </div>
            <a href="/" class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-grow flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-[480px]">
                {{ $slot }}
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="relative z-10 py-6 text-center text-xs font-medium text-gray-400">
            &copy; {{ date('Y') }} VOLA Platform Relawan Indonesia. All rights reserved.
        </footer>
    </body>
</html>
