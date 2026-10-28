<x-layouts.crew title="Cari Event - VOLA">
    <div x-data="{ activeCategory: 'Semua' }">
        
        <!-- Header Pencarian -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                @if(!empty($query))
                    Hasil Pencarian: "{{ $query }}"
                @else
                    Cari Event
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                @if(!empty($query))
                    Ditemukan {{ count($events) }} event yang sesuai
                @else
                    Temukan event dan pengalaman baru yang sesuai dengan minatmu.
                @endif
            </p>
        </div>

        <!-- Search Bar -->
        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm mb-6">
            <form action="{{ route('search.index') }}" method="GET" class="flex gap-2">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $query }}"
                        placeholder="Cari event, posisi volunteer, atau lokasi..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent outline-none text-sm"
                    >
                </div>
                <button type="submit" class="px-6 py-2.5 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Filter Kategori Bar -->
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

        <!-- Hasil Pencarian -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($events as $event)
                <div x-show="activeCategory === 'Semua' || activeCategory === '{{ $event['filter_category'] }}'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" 
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    
                    <!-- Image Area -->
                    <div class="relative h-40 w-full overflow-hidden bg-gray-100 shrink-0">
                        <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            onerror="this.src='{{ asset('images/placeholder-event.jpg') }}'">

                        <!-- Badges -->
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

                    <!-- Content Area -->
                    <div class="p-4 flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 min-h-[40px]">
                                {{ $event['title'] }}
                            </h3>

                            <div class="flex items-center gap-1.5 mt-2">
                                <span class="text-xs text-gray-500 font-medium truncate">{{ $event['organizer'] }}</span>
                                @if ($event['is_verified'])
                                    <svg class="w-3.5 h-3.5 text-[#4F46E5] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
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

                        <!-- Tombol Lihat Detail -->
                        <a href="{{ route('events.detail', ['event' => $event['id']]) }}" 
                           class="block w-full mt-4 py-2.5 bg-white text-[#4F46E5] text-sm font-bold rounded-xl border-2 border-[#4F46E5] hover:bg-[#4F46E5] hover:text-white transition text-center">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">
                        @if(!empty($query))
                            Tidak ada event yang cocok dengan "{{ $query }}"
                        @else
                            Belum ada event aktif saat ini.
                        @endif
                    </p>
                    @if(!empty($query))
                        <p class="text-gray-400 text-xs mt-1">Coba gunakan kata kunci lain</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.crew>