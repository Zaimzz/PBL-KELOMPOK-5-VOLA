<x-layouts.crew title="Event Saya - VOLA">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Event Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Informasi dan jadwal penugasan aktifmu.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-xl">
            <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-bold text-[#4F46E5]">Crew Terdaftar Aktif</span>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-6 overflow-x-auto" x-data="{ activeTab: 'upcoming' }">
            <button @click="activeTab = 'upcoming'" 
                    :class="activeTab === 'upcoming' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Akan Datang <span class="text-gray-400 ml-1">{{ $counts['upcoming'] }}</span>
            </button>
            <button @click="activeTab = 'ongoing'" 
                    :class="activeTab === 'ongoing' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Sedang Berlangsung <span class="text-gray-400 ml-1">{{ $counts['ongoing'] }}</span>
            </button>
            <button @click="activeTab = 'completed'" 
                    :class="activeTab === 'completed' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Selesai <span class="text-gray-400 ml-1">{{ $counts['completed'] }}</span>
            </button>
        </nav>
    </div>

    {{-- Event Cards --}}
    <div class="space-y-6" x-data="{ activeTab: 'upcoming' }">
        @foreach($myEvents as $event)
        @if($event['status'] === 'accepted')
        <div x-show="activeTab === 'upcoming'" class="space-y-4">
            
            {{-- Main Event Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $event['status_label'] }}
                                </span>
                                <span class="text-sm text-gray-600">•</span>
                                <span class="text-sm text-gray-600">{{ $event['organizer'] }}</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $event['event_title'] }}</h2>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <span class="text-xs text-gray-500">ID Crew:</span>
                        <span class="text-sm font-bold text-gray-900 ml-1">{{ $event['crew_id'] }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Posisi Penugasan</p>
                            <p class="text-sm font-bold text-gray-900">{{ $event['position'] }}</p>
                            <p class="text-xs text-gray-600">{{ $event['division'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jadwal & Shift</p>
                            <p class="text-sm font-bold text-gray-900">{{ $event['schedule'] }}</p>
                            <p class="text-xs text-gray-600">{{ $event['shift_time'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Lokasi Bertugas</p>
                            <p class="text-sm font-bold text-gray-900">{{ $event['location'] }}</p>
                            <p class="text-xs text-gray-600">{{ $event['access_info'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WhatsApp & PIC Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                
                {{-- WhatsApp Group Card --}}
                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-gray-900 mb-1">Grup WhatsApp Koordinasi</h3>
                            <p class="text-sm text-gray-600">Grup resmi relawan telah dibuka untuk koordinasi teknis dan pembagian rundown harian acara.</p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <a href="{{ $event['whatsapp_group'] }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition">
                            Buka WhatsApp Grup Koordinasi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- PIC Contact Card --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Narahubung</p>
                    <h3 class="text-base font-bold text-gray-900 mb-4">Kontak PIC Lapangan</h3>
                    
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-[#4F46E5]">BS</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $event['pic_name'] }}</p>
                            <p class="text-xs text-gray-600">{{ $event['pic_role'] }}</p>
                            <p class="text-xs text-gray-600">{{ $event['pic_phone'] }}</p>
                        </div>
                    </div>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $event['pic_phone']) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-lg hover:bg-gray-800 transition">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Hubungi PIC via WhatsApp
                    </a>
                </div>
            </div>

            {{-- Tasks & Briefing Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                
                {{-- Tasks Card --}}
                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-base font-bold text-gray-900">Panduan & Tanggung Jawab Tugas</h3>
                    </div>
                    <ul class="space-y-3">
                        @foreach($event['tasks'] as $task)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $task }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Briefing Location Card --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Lokasi Briefing</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Titik Kumpul Lapangan</h3>
                    <p class="text-sm font-bold text-gray-900 mb-1">{{ $event['briefing_location'] }}</p>
                    <p class="text-xs text-gray-600 mb-4">{{ $event['briefing_address'] }}</p>
                    <p class="text-xs text-gray-500 mb-3">{{ $event['briefing_city'] }}</p>
                    <a href="{{ $event['google_maps_link'] }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-[#4F46E5] hover:text-indigo-700 transition">
                        Lihat di Google Maps
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
        @endif
        @endforeach
    </div>
</div>
</x-layouts.crew>