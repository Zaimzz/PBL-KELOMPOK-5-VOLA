<x-layouts.crew title="Riwayat Pengalaman - VOLA">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Riwayat Pengalaman</h1>
            <p class="mt-1 text-sm text-gray-500">Rekam jejak kontribusi volunteer kamu.</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Unduh CV / Ringkasan PDF
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Total Event Diikuti</p>
                <p class="text-2xl font-bold text-gray-900">{{ $counts['all'] }} Event</p>
                <p class="text-xs text-gray-500">Seluruh kegiatan terverifikasi</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Sertifikat Terkumpul</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalCertificates }} Sertifikat</p>
                <p class="text-xs text-gray-500">Siap diunduh kapan saja</p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <nav class="flex gap-2 overflow-x-auto" x-data="{ activeTab: 'all' }">
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'bg-[#4F46E5] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition">
                Semua ({{ $counts['all'] }})
            </button>
            <button @click="activeTab = 'musik'" 
                    :class="activeTab === 'musik' ? 'bg-[#4F46E5] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition">
                Musik & Festival ({{ $counts['musik'] }})
            </button>
            <button @click="activeTab = 'tech'" 
                    :class="activeTab === 'tech' ? 'bg-[#4F46E5] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition">
                Konferensi & Teknologi ({{ $counts['tech'] }})
            </button>
            <button @click="activeTab = 'olahraga'" 
                    :class="activeTab === 'olahraga' ? 'bg-[#4F46E5] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition">
                Sosial & Olahraga ({{ $counts['olahraga'] }})
            </button>
        </nav>
    </div>

    <div class="space-y-4" x-data="{ activeTab: 'all' }">
        @foreach($histories as $history)
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition"
             x-show="activeTab === 'all' || 
                     (activeTab === 'musik' && '{{ $history['category'] }}' === 'Musik & Festival') || 
                     (activeTab === 'tech' && '{{ $history['category'] }}' === 'Konferensi & Teknologi') || 
                     (activeTab === 'olahraga' && '{{ $history['category'] }}' === 'Sosial & Olahraga')">
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <h3 class="text-base font-bold text-gray-900">{{ $history['event_title'] }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                            {{ $history['status_label'] }}
                        </span>
                    </div>
                    <p class="text-sm text-[#4F46E5] font-medium mb-3">{{ $history['organizer'] }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Posisi:</p>
                                <p class="text-xs text-gray-700">{{ $history['position'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Waktu:</p>
                                <p class="text-xs text-gray-700">{{ $history['date'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Lokasi:</p>
                                <p class="text-xs text-gray-700">{{ $history['location'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="{{ $history['certificate_url'] }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-[#4F46E5] text-[#4F46E5] text-sm font-bold rounded-xl hover:bg-[#4F46E5] hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Sertifikat (PDF)
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 flex items-center justify-between">
        <p class="text-sm text-gray-500">Menampilkan 4 dari 8 riwayat event</p>
        <div class="flex items-center gap-1">
            <button class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">Sebelumnya</button>
            <button class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-[#4F46E5] text-white font-bold">1</button>
            <button class="w-8 h-8 flex items-center justify-center text-sm rounded-lg text-gray-600 hover:bg-gray-100 transition">2</button>
            <button class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">Selanjutnya</button>
        </div>
    </div>
</div>
</x-layouts.crew>