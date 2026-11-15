<x-layouts.crew title="Event Saya - VOLA">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
     x-data="{ 
        activeTab: 'all',
        events: @json($events ?? []),
        
        get filteredEvents() {
            if (this.activeTab === 'all') return this.events;
            return this.events.filter(e => e.status === this.activeTab);
        },
        
        getCounts() {
            return {
                all: this.events.length,
                upcoming: this.events.filter(e => e.status === 'upcoming').length,
                ongoing: this.events.filter(e => e.status === 'ongoing').length,
                completed: this.events.filter(e => e.status === 'completed').length
            };
        },
        
        getStatusBadgeClass(status) {
            if (status === 'upcoming') return 'bg-blue-50 text-blue-600 border-blue-100';
            if (status === 'ongoing') return 'bg-emerald-50 text-emerald-600 border-emerald-100';
            return 'bg-gray-50 text-gray-600 border-gray-100';
        },
        
        getStatusLabel(status) {
            if (status === 'upcoming') return 'Akan Datang';
            if (status === 'ongoing') return 'Sedang Berlangsung';
            return 'Selesai';
        }
     }">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Event Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar event yang lamarannya telah Anda terima.</p>
    </div>

    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-6 overflow-x-auto pb-1">
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Semua 
                <span :class="activeTab === 'all' ? 'text-[#4F46E5]' : 'text-gray-400'" x-text="getCounts().all"></span>
            </button>
            <button @click="activeTab = 'upcoming'" 
                    :class="activeTab === 'upcoming' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Akan Datang 
                <span :class="activeTab === 'upcoming' ? 'text-[#4F46E5]' : 'text-gray-400'" x-text="getCounts().upcoming"></span>
            </button>
            <button @click="activeTab = 'ongoing'" 
                    :class="activeTab === 'ongoing' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Sedang Berlangsung 
                <span :class="activeTab === 'ongoing' ? 'text-[#4F46E5]' : 'text-gray-400'" x-text="getCounts().ongoing"></span>
            </button>
            <button @click="activeTab = 'completed'" 
                    :class="activeTab === 'completed' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Selesai 
                <span :class="activeTab === 'completed' ? 'text-[#4F46E5]' : 'text-gray-400'" x-text="getCounts().completed"></span>
            </button>
        </nav>
    </div>

    <div x-show="true" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="min-h-[400px]">
         
        <template x-if="filteredEvents.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="event in filteredEvents" :key="event.id">
                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition group">
                        <div class="relative h-48 overflow-hidden">
                            <img :src="event.image" :alt="event.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border" 
                                      :class="getStatusBadgeClass(event.status)" 
                                      x-text="getStatusLabel(event.status)"></span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 pt-12">
                                <p class="text-xs text-indigo-200 font-medium mb-1" x-text="event.organizer"></p>
                                <h3 class="text-lg font-bold text-white leading-tight line-clamp-2" x-text="event.title"></h3>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <p class="text-sm text-gray-600 truncate" x-text="'Posisi: ' + event.position"></p>
                            </div>
                            
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-sm text-gray-600" x-text="event.date"></p>
                            </div>

                            <div class="flex items-center gap-2 mb-5">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm text-gray-600 truncate" x-text="event.location"></p>
                            </div>

                            <a :href="'{{ route("events.detail", ":id") }}'.replace(':id', event.id)" 
                               class="block w-full py-2.5 text-center border-2 border-[#4F46E5] text-[#4F46E5] text-sm font-bold rounded-xl hover:bg-[#4F46E5] hover:text-white transition">
                                Lihat Detail Event
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="filteredEvents.length === 0">
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200 shadow-sm">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-indigo-50 flex items-center justify-center">
                    <svg class="w-12 h-12 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    <span x-show="activeTab === 'all'">Belum Ada Event</span>
                    <span x-show="activeTab === 'upcoming'">Tidak Ada Event Mendatang</span>
                    <span x-show="activeTab === 'ongoing'">Tidak Ada Event Berlangsung</span>
                    <span x-show="activeTab === 'completed'">Belum Ada Riwayat Event</span>
                </h3>
            </div>
        </template>
    </div>

</div>
</x-layouts.crew>