<x-layouts.crew title="Event Saya - VOLA">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
     x-data="{ activeTab: 'upcoming' }">
    
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Event Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Informasi dan jadwal penugasan aktifmu.</p>
        </div>
    </div>

    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-6 overflow-x-auto">
            <button @click="activeTab = 'upcoming'" 
                    :class="activeTab === 'upcoming' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Akan Datang 
                <span :class="activeTab === 'upcoming' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ $counts['upcoming'] }}</span>
            </button>
           
            <button @click="activeTab = 'ongoing'" 
                    :class="activeTab === 'ongoing' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Sedang Berlangsung 
                <span :class="activeTab === 'ongoing' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ $counts['ongoing'] }}</span>
            </button>
           
            <button @click="activeTab = 'completed'" 
                    :class="activeTab === 'completed' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Selesai 
                <span :class="activeTab === 'completed' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ $counts['completed'] }}</span>
            </button>
        </nav>
    </div>

    <div class="space-y-6">
        
        <template x-if="activeTab === 'upcoming'">
            <div class="space-y-6">
                @foreach($myEvents as $event)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                  
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                                        {{ $event['status_label'] }}
                                    </span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-600 font-medium">{{ $event['event_organizer'] }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $event['event_title'] }}</h3>
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">ID Crew :</p>
                            <p class="text-sm font-bold text-gray-900">{{ $event['crew_id'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-xs font-bold text-gray-500 uppercase">Posisi Penugasan</span>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ $event['position'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $event['division'] }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-gray-500 uppercase">Jadwal & Shift</span>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ $event['schedule'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $event['shift_time'] }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-xs font-bold text-gray-500 uppercase">Lokasi Bertugas</span>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ $event['location'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $event['access_info'] }}</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h4 class="text-sm font-bold text-gray-900">Panduan & Tanggung Jawab Tugas</h4>
                        </div>
                        <ul class="space-y-3">
                            @foreach($event['tasks'] as $task)
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-indigo-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 leading-relaxed">{{ $task }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Narahubung</p>
                            <h4 class="text-base font-bold text-gray-900 mb-3">Kontak PIC Lapangan</h4>
                            
                            <div class="bg-gray-50 rounded-lg p-3 mb-4 flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-sm font-bold text-indigo-600 shrink-0">
                                    {{ $event['pic_initials'] }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900">{{ $event['pic_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $event['pic_role'] }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $event['pic_phone'] }}</p>
                                </div>
                            </div>

                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $event['pic_phone']) }}" target="_blank" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-lg hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Hubungi PIC via WhatsApp
                            </a>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Lokasi Briefing</p>
                            <h4 class="text-base font-bold text-gray-900 mb-1">Titik Kumpul Lapangan</h4>
                            <p class="text-sm font-bold text-gray-900 mb-1">{{ $event['briefing_location'] }}</p>
                            <p class="text-xs text-gray-500 mb-4">{{ $event['briefing_address'] }}</p>
                            
                            <div class="relative h-40 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 mb-4">
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100">
                                    <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-300 -translate-y-1/2"></div>
                                    <div class="absolute top-0 bottom-0 left-1/2 w-1 bg-gray-300 -translate-x-1/2"></div>
                                    <div class="absolute top-1/4 left-1/4 w-1/2 h-0.5 bg-gray-300 rotate-12"></div>
                                    <div class="absolute bottom-1/4 right-1/4 w-1/3 h-0.5 bg-gray-300 -rotate-6"></div>
                                </div>
                               
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#4F46E5] text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg flex items-center gap-1.5 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                    {{ $event['briefing_location'] }}
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <p class="text-xs text-gray-500">{{ $event['briefing_city'] }}</p>
                                <a href="{{ $event['google_maps_link'] }}" target="_blank" 
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#4F46E5] hover:text-indigo-700 transition">
                                    Lihat di Google Maps
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </template>

        <template x-if="activeTab === 'ongoing'">
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-50 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Event Berlangsung</h3>
            </div>
        </template>

        <template x-if="activeTab === 'completed'">
            <div class="space-y-4">
                @foreach($completedEvents as $comp)
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition opacity-90 hover:opacity-100">
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-600 font-medium">{{ $comp['event_organizer'] }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $comp['event_title'] }}</h3>
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">ID Crew :</p>
                            <p class="text-sm font-bold text-gray-900">{{ $comp['crew_id'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Posisi</p>
                            <p class="text-sm font-bold text-gray-900">{{ $comp['position'] }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Tanggal</p>
                            <p class="text-sm font-bold text-gray-900">{{ $comp['date'] }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Lokasi</p>
                            <p class="text-sm font-bold text-gray-900">{{ $comp['location'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">{{ $comp['hours'] }} Jam Kontribusi</span>
                        </div>
                        
                        <a href="{{ route('history.downloadCertificate', ['id' => $comp['id']]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-[#4F46E5] text-[#4F46E5] text-sm font-bold rounded-xl hover:bg-[#4F46E5] hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Unduh Sertifikat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </template>

    </div>
</div>
</x-layouts.crew>