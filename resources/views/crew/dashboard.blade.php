<x-layouts.crew title="VOLA - Beranda Crew">
  
    <div x-data="dashboardData()" 
         x-init="initEvents()"
         data-events='@json($recommendedEvents)'>

        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 tracking-tight flex items-center gap-2">
                Halo, {{ $firstName }}! 👋
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Temukan event dan pengalaman baru yang sesuai dengan minatmu.
            </p>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm mb-6">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <span class="text-sm font-semibold text-gray-400 shrink-0 mr-2">Kategori:</span>
                @foreach ($categories as $category)
                    <button type="button" @click="activeCategory = '{{ $category }}'"
                        :class="activeCategory === '{{ $category }}'
                            ? 'bg-[#4F46E5] text-white shadow-md font-bold' :
                              'bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium'"
                        class="px-4 py-1.5 rounded-full text-sm transition-all shrink-0 cursor-pointer whitespace-nowrap">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between h-32">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Lamaran Aktif</span>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-[#4F46E5] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-semibold text-gray-900">{{ $activeApplicationsCount }}</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between h-32">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Menunggu Seleksi</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-semibold text-gray-900">{{ $pendingReviewCount }}</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between h-32">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Diterima</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-semibold text-gray-900">{{ $acceptedCount }}</p>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-end justify-between mb-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Event yang Mungkin Cocok Untukmu</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilihan kurasi relawan berdasarkan minat dan ketersediaanmu</p>
                </div>
               
                <a href="{{ route('search.index') }}" 
                   x-show="hasVisibleEvents"
                   class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-[#4F46E5] hover:text-indigo-800 transition">
                    Lihat Semua Event
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($recommendedEvents as $event)
                    <div x-show="activeCategory === 'Semua' || activeCategory === '{{ $event['filter_category'] }}'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" 
                        x-transition:enter-end="opacity-100 scale-100"
                        class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all flex flex-col group">
                       
                        <div class="relative h-40 w-full overflow-hidden bg-gray-100 shrink-0">
                            <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                onerror="this.src='{{ asset('images/placeholder-event.jpg') }}'">

                            <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-1.5 pointer-events-none">
                                <span class="bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                    {{ $event['category'] }}
                                </span>

                                @if ($event['status_badge_type'] === 'warning')
                                    <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                        {{ $event['status_badge'] }}
                                    </span>
                                @elseif($event['status_badge_type'] === 'danger')
                                    <span class="bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                        {{ $event['status_badge'] }}
                                    </span>
                                @else
                                    <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                        {{ $event['status_badge'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 flex flex-col flex-1 justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 min-h-[40px]">
                                    {{ $event['title'] }}
                                </h3>

                                <div class="flex items-center gap-1.5 mt-2">
                                    <span class="text-xs text-gray-500 font-medium truncate">{{ $event['organizer'] }}</span>
                                    @if ($event['is_verified'])
                                        <svg class="w-3.5 h-3.5 text-[#4F46E5] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="mt-3 space-y-1.5 text-xs text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $event['date'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $event['location'] }}</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mt-4">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide block mb-1">Posisi Dibuka:</span>
                                    <p class="text-xs font-semibold text-gray-700 leading-relaxed line-clamp-2">
                                        {{ $event['positions'] }}
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('events.detail', ['event' => $event['id'] ?? 1]) }}" 
                               class="block w-full mt-4 py-2.5 bg-white text-[#4F46E5] text-sm font-bold rounded-xl border-2 border-[#4F46E5] hover:bg-[#4F46E5] hover:text-white transition text-center">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-gray-200">
                        <p class="text-gray-500 text-sm">Belum ada event aktif saat ini.</p>
                    </div>
                @endforelse

                <div x-show="!hasVisibleEvents" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="col-span-full">
                    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Event Tersedia</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Belum ada event dalam kategori <span class="font-semibold text-[#4F46E5]" x-text="activeCategory"></span> saat ini.
                        </p>
                </div>
            </div>

            <div x-show="hasVisibleEvents" class="mt-6 sm:hidden text-center">
                <a href="{{ route('search.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-[#4F46E5]">
                    Lihat Semua Event
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <script>
        function dashboardData() {
            return {
                activeCategory: 'Semua',
                allEvents: [],
                get hasVisibleEvents() {
                    if (this.activeCategory === 'Semua') return true;
                    return this.allEvents.some(e => e.filter_category === this.activeCategory);
                },
                initEvents() {
                    const el = document.querySelector('[data-events]');
                    if (el) {
                        try {
                            this.allEvents = JSON.parse(el.getAttribute('data-events'));
                        } catch (e) {
                            this.allEvents = [];
                        }
                    }
                }
            }
        }
    </script>
</x-layouts.crew>