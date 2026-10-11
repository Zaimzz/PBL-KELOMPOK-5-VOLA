<x-layouts.crew title="{{ $eventData['title'] }} - VOLA">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <div class="mb-4">
            <nav class="flex items-center text-sm text-gray-500 flex-wrap gap-1">
                <a href="{{ route('dashboard') }}" class="hover:text-[#4F46E5] flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Cari Event
                </a>
                <span class="mx-1">/</span>
                <a href="#" class="hover:text-[#4F46E5]">Cari Event</a>
                <span class="mx-1">/</span>
                <a href="#" class="hover:text-[#4F46E5]">Festival & Pop Culture</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">{{ $eventData['title'] }}</span>
            </nav>
        </div>

        <div class="relative rounded-2xl overflow-hidden mb-6 h-64 bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800">
          
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
          
            <div class="absolute top-4 left-4 right-4 flex items-start justify-between">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">
                    <span class="w-2 h-2 bg-white rounded-full"></span>
                    {{ $eventData['status'] }}
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sisa {{ $eventData['days_left'] }} Hari Lagi!
                </span>
            </div>

            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                <p class="text-xs text-indigo-200 font-medium mb-2">
                    {{ $eventData['category'] }} • Penyelenggara: {{ $eventData['organizer'] }}
                    @if(!empty($eventData['organizer_verified']))
                        <svg class="w-3 h-3 inline text-blue-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </p>

                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                 
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight max-w-3xl">
                        {{ $eventData['title'] }}
                    </h1>

                    <div class="flex items-center gap-2 shrink-0">
                        <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur text-white text-sm font-bold rounded-xl hover:bg-white/20 transition border border-white/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Bagikan
                        </button>
                        <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg">
                            Daftar Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Tanggal Pelaksanaan</span>
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $eventData['date'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $eventData['date_duration'] }}</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Waktu Shift</span>
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $eventData['shift_time'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $eventData['shift_note'] }}</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Lokasi Penugasan</span>
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $eventData['location'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $eventData['location_city'] }}</p>
            </div>

            <div class="bg-white border border-red-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-red-500 uppercase tracking-wide">Batas Pendaftaran</span>
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $eventData['deadline'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $eventData['deadline_time'] }}</p>
            </div>
        </div>

        <div class="border-b border-gray-200 mb-6" x-data="{ activeTab: 'positions' }">
            <nav class="flex gap-6 overflow-x-auto">
                <button @click="activeTab = 'positions'" 
                        :class="activeTab === 'positions' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Posisi Dibuka ({{ $eventData['total_positions'] }})
                </button>
                <button @click="activeTab = 'about'" 
                        :class="activeTab === 'about' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                    Tentang Event
                </button>
                <button @click="activeTab = 'benefits'" 
                        :class="activeTab === 'benefits' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                    Benefit & Fasilitas
                </button>
                <button @click="activeTab = 'requirements'" 
                        :class="activeTab === 'requirements' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                    Syarat & Ketentuan
                </button>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Posisi Volunteer yang Dibuka</h2>
                    <span class="text-xs font-bold text-[#4F46E5] bg-indigo-50 px-3 py-1 rounded-full">Total Kuota: {{ $eventData['total_quota'] }} Kru</span>
                </div>
                <p class="text-sm text-gray-500 mb-4">Pilih divisi yang paling cocok dengan keahlian dan minat lapangan Anda.</p>

                <div class="space-y-3">
                    @foreach($positions as $position)
                    <div class="bg-white border-2 {{ !empty($position['is_selected']) ? 'border-[#4F46E5]' : 'border-gray-100' }} rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-2 flex-wrap gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if(!empty($position['badge']))
                                    <span class="px-2 py-0.5 bg-[#4F46E5] text-white text-[10px] font-bold rounded">{{ $position['badge'] }}</span>
                                @endif
                                <span class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[10px] font-bold rounded border border-orange-100">Sisa {{ $position['sisa_kursi'] }} Kursi</span>
                                <h3 class="text-base font-bold text-gray-900">{{ $position['title'] }}</h3>
                            </div>
                            <button class="inline-flex items-center gap-1 px-4 py-2 bg-indigo-50 text-[#4F46E5] text-xs font-bold rounded-xl hover:bg-indigo-100 transition whitespace-nowrap">
                                Pilih Posisi
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 mb-3">{{ $position['description'] }}</p>

                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mb-2">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Kuota: {{ $position['quota'] }} Orang
                            </span>
                            @if(!empty($position['date']))
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $position['date'] }}
                            </span>
                            @endif
                        </div>

                        @if(!empty($position['requirements']))
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($position['requirements'] as $req)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 text-gray-600 text-[11px] rounded border border-gray-200">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    {{ $req }}
                                </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mt-6">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Alur Rekrutmen Volunteer</h3>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($recruitmentFlow as $flow)
                        <div class="text-center">
                            <div class="w-10 h-10 mx-auto rounded-full {{ $flow['step'] === 5 ? 'bg-emerald-500' : 'bg-[#4F46E5]' }} text-white flex items-center justify-center font-bold text-sm mb-2">
                                {{ $flow['step'] }}
                            </div>
                            <p class="text-xs font-bold text-gray-900 mb-0.5">{{ $flow['title'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $flow['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <!-- Benefit -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            Benefit Relawan
                        </h3>
                        <ul class="space-y-2">
                            @foreach($benefits as $benefit)
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $benefit }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Syarat Umum
                        </h3>
                        <ul class="space-y-2">
                            @foreach($requirements as $req)
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#4F46E5] shrink-0 mt-2"></span>
                                {{ $req }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
               
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-900">Pendaftaran Relawan</h3>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            Online
                        </span>
                    </div>

                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Posisi yang dipilih saat ini:</p>
                        @php
                            $selectedPosition = collect($positions)->firstWhere('is_selected', true);
                        @endphp
                        @if($selectedPosition)
                            <p class="text-sm font-bold text-[#4F46E5]">{{ $selectedPosition['title'] }}</p>
                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-500">Kapasitas Divisi</span>
                                    <span class="text-xs font-bold text-gray-900">{{ $selectedPosition['capacity'] }} / {{ $selectedPosition['filled'] }} Terisi ({{ $selectedPosition['percentage'] }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-[#4F46E5] h-2 rounded-full" style="width: {{ $selectedPosition['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <button class="w-full py-3 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ajukan Lamaran Volunteer
                    </button>

                    <p class="text-[10px] text-gray-400 text-center mt-3">Pendaftaran gratis tanpa dipungut biaya apapun.</p>

                    <div class="border-t border-gray-100 pt-4 mt-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-[#4F46E5] text-white flex items-center justify-center font-bold text-sm">
                                PMN
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 flex items-center gap-1">
                                    {{ $eventData['organizer'] }}
                                    @if(!empty($eventData['organizer_verified']))
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </p>
                                <p class="text-[10px] text-gray-500">Penyelenggara Pameran & Festival</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600">Penyelenggara pameran pop culture, festival komik, dan konvensi kreatif berskala nasional di Indonesia.</p>
                    </div>

                    <div class="border-t border-gray-100 pt-4 mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-gray-900">Lokasi Event & Titik Kumpul</h4>
                            <span class="text-[10px] font-bold text-[#4F46E5]">JCC Senayan</span>
                        </div>
                       
                        <div class="relative h-32 bg-gray-100 rounded-xl overflow-hidden mb-3">
                            <div class="absolute inset-0" style="background: linear-gradient(135deg, #e0e7ff 0%, #f3f4f6 100%);">
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <div class="w-3 h-3 bg-[#4F46E5] rounded-full border-2 border-white shadow-lg"></div>
                                </div>
                                <div class="absolute top-1/3 left-1/4 w-16 h-0.5 bg-gray-300 rotate-12"></div>
                                <div class="absolute top-2/3 right-1/4 w-20 h-0.5 bg-gray-300 -rotate-6"></div>
                            </div>
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-8 bg-[#4F46E5] text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg whitespace-nowrap">
                                 Hall A & B Entrance
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase mb-0.5">Alamat:</p>
                                <p class="text-xs text-gray-600">{{ $eventData['address'] }}</p>
                            </div>
                            <div class="p-2 bg-amber-50 border border-amber-100 rounded-lg">
                                <p class="text-[10px] font-bold text-amber-700 mb-0.5">Titik Kumpul:</p>
                                <p class="text-xs text-amber-900">{{ $eventData['meeting_point'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.crew>