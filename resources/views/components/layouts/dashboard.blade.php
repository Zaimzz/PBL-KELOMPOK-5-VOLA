<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vola' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 transform transition-transform lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center px-6 border-b border-gray-100">
                <a href="{{ route('home') }}" class="text-xl font-bold text-brand-600">
                    VOLA<span class="text-gray-900">.</span>
                </a>
            </div>

            <nav class="p-4 space-y-1">
                {{ $sidebar ?? '' }}
            </nav>
        </aside>

        <!-- Overlay untuk mobile -->
        <div
            x-show="sidebarOpen"
            x-on:click="sidebarOpen = false"
            class="fixed inset-0 bg-black/30 z-30 lg:hidden"
            x-cloak
        ></div>

        <!-- Main content -->
        <div class="flex-1 lg:ml-64">

            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">
                <button
                    type="button"
                    x-on:click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden text-gray-500"
                >
                    &#9776;
                </button>

                <div class="flex-1"></div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name ?? '' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                {{ $slot }}
            </main>

        </div>
    </div>

</body>
</html>