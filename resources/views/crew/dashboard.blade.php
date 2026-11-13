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
            <span class="text-sm font-semibold text-gray-400 shrink-0 mr-2">Kategori :</span>
            @foreach ($categories as $category)
                <button type="button" 
                        @click="activeCategory = '{{ $category['slug'] }}'"
                        :class="activeCategory === '{{ $category['slug'] }}'
                            ? 'bg-[#4F46E5] text-white shadow-md' :
                            'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                        class="px-4 py-1.5 rounded-full text-sm font-medium transition-all shrink-0 cursor-pointer whitespace-nowrap">
                    {{ $category['name'] }}
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
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Event yang Mungkin Cocok Untukmu</h2>
                <p class="text-sm text-gray-500 mt-1">Pilihan kurasi relawan berdasarkan minat dan ketersediaanmu</p>
            </div>
            <a href="{{ route('search.index') }}" class="text-sm font-bold text-[#4F46E5] hover:text-indigo-700 transition flex items-center gap-1">
                Lihat Semua Event
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div x-show="filteredEvents.length === 0" class="bg-white border border-gray-100 rounded-2xl p-12 shadow-sm text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Event</h3>
            <p class="text-sm text-gray-500 mb-4">
                Belum ada event tersedia untuk kategori <strong class="text-[#4F46E5]" x-text="activeCategoryName"></strong>.
            </p>
        </div>

        <div x-show="filteredEvents.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                     <template x-for="event in filteredEvents" :key="event.id">
             <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col">
                 <div class="relative h-44 overflow-hidden bg-gray-100">
                     {{-- ✅ TAMBAHKAN ONERROR SEBAGAI PENGAMAN --}}
                     <img :src="event.image" 
                          :alt="event.title" 
                          onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?w=800';"
                          class="w-full h-full object-cover">
                          
                     <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                     <div class="absolute top-3 left-3">
                         <span class="px-3 py-1.5 bg-gray-900/80 backdrop-blur text-white text-xs font-bold rounded-full border border-white/20" 
                               x-text="event.category"></span>
                     </div>
                     <div class="absolute top-3 right-3">
                         <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full shadow-lg"
                               :class="event.status_badge_type === 'warning' 
                                   ? 'bg-orange-500 text-white' 
                                   : 'bg-emerald-500 text-white'">
                             <span x-text="event.status"></span>
                         </span>
                     </div>
                 </div>
                 <div class="p-4 flex flex-col flex-1">
                     <h3 class="text-base font-bold text-gray-900 mb-1.5 line-clamp-2" x-text="event.title"></h3>
                     <div class="flex items-center gap-1.5 mb-3">
                         <span class="text-sm text-gray-600" x-text="event.organizer"></span>
                         <svg x-show="event.organizer_verified" class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                         </svg>
                     </div>
                     <div class="space-y-1.5 mb-3">
                         <div class="flex items-center gap-2 text-xs text-gray-500">
                             <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                             </svg>
                             <span x-text="event.date"></span>
                         </div>
                         <div class="flex items-center gap-2 text-xs text-gray-500">
                             <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                             </svg>
                             <span class="truncate" x-text="event.location"></span>
                         </div>
                     </div>
                     <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mb-4 flex-1">
                         <p class="text-xs font-bold text-gray-700 mb-1">Posisi Dibuka :</p>
                         <p class="text-xs text-gray-600 line-clamp-2" x-text="event.positions_list"></p>
                     </div>
                     <a :href="'/events/' + event.id + '?from=dashboard'" 
                        class="w-full py-2.5 border-2 border-[#4F46E5] text-[#4F46E5] text-sm font-bold rounded-xl hover:bg-[#4F46E5] hover:text-white transition text-center">
                         Lihat Detail
                     </a>
                 </div>
             </div>
         </template>
        </div>
    </div>
</div>

<script>
    function dashboardData() {
        return {
            activeCategory: 'all',
            allEvents: [],
            categoryNames: {
                'all': 'Semua',
                'festival': 'Festival',
                'seminar': 'Seminar',
                'musik': 'Musik',
                'olahraga': 'Olahraga',
                'sosial': 'Sosial',
                'kampus': 'Kampus',
                'komunitas': 'Komunitas'
            },
            get activeCategoryName() {
                return this.categoryNames[this.activeCategory] || this.activeCategory;
            },
            get filteredEvents() {
                if (this.activeCategory === 'all') {
                    return this.allEvents;
                }
                return this.allEvents.filter(event => event.filter_category === this.activeCategory);
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