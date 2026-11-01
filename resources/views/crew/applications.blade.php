<x-layouts.crew title="Lamaran Saya - VOLA">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Lamaran Saya</h1>
        <p class="mt-1 text-sm text-gray-500">Pantau status pendaftaran volunteer kamu.</p>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-6 overflow-x-auto" x-data="{ activeTab: 'all' }">
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Semua <span class="text-gray-400 ml-1">{{ count($applications) }}</span>
            </button>
            <button @click="activeTab = 'pending'" 
                    :class="activeTab === 'pending' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Menunggu Seleksi <span class="text-gray-400 ml-1">{{ count(array_filter($applications, fn($a) => $a['status'] === 'pending')) }}</span>
            </button>
            <button @click="activeTab = 'processing'" 
                    :class="activeTab === 'processing' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Sedang Diproses <span class="text-gray-400 ml-1">{{ count(array_filter($applications, fn($a) => $a['status'] === 'processing')) }}</span>
            </button>
            <button @click="activeTab = 'accepted'" 
                    :class="activeTab === 'accepted' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Diterima <span class="text-gray-400 ml-1">{{ count(array_filter($applications, fn($a) => $a['status'] === 'accepted')) }}</span>
            </button>
            <button @click="activeTab = 'rejected'" 
                    :class="activeTab === 'rejected' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                Ditolak <span class="text-gray-400 ml-1">{{ count(array_filter($applications, fn($a) => $a['status'] === 'rejected')) }}</span>
            </button>
        </nav>
    </div>

    {{-- Application Cards --}}
    <div class="space-y-4" x-data="{ activeTab: 'all' }">
        @foreach($applications as $application)
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition"
             x-show="activeTab === 'all' || activeTab === '{{ $application['status'] }}'">
            
            {{-- Header dengan Status --}}
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    @if($application['status'] === 'accepted')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $application['status_label'] }}
                        </span>
                    @elseif($application['status'] === 'pending')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $application['status_label'] }}
                        </span>
                    @elseif($application['status'] === 'processing')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ $application['status_label'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ $application['status_label'] }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">Dilamar: {{ $application['applied_at'] }}</span>
            </div>

            {{-- Event Info --}}
            <div class="mb-4">
                <h3 class="text-base font-bold text-gray-900 mb-1">{{ $application['event_title'] }}</h3>
                <p class="text-sm text-gray-600">{{ $application['event_organizer'] }} • {{ $application['event_category'] }}</p>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Posisi: {{ $application['position'] }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Lokasi: {{ $application['location'] }}</span>
                </div>
            </div>

            {{-- Status Message --}}
            @if($application['message_type'] === 'success')
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-emerald-800">{{ $application['message'] }}</p>
                    </div>
                </div>
            @elseif($application['message_type'] === 'warning')
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm text-amber-800">{{ $application['message'] }}</p>
                    </div>
                </div>
            @elseif($application['message_type'] === 'info')
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-blue-800">{{ $application['message'] }}</p>
                    </div>
                </div>
            @else
                <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-red-800">{{ $application['message'] }}</p>
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-2">
                @if($application['status'] === 'accepted')
                    <button class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Unduh Bukti Penerimaan
                    </button>
                    <button class="px-4 py-2 bg-[#4F46E5] text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">
                        Lihat di Event Saya
                    </button>
                @elseif($application['status'] === 'pending' && isset($application['can_cancel']) && $application['can_cancel'])
                    <button class="px-4 py-2 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                        Batalkan Lamaran
                    </button>
                @else
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                        Lihat Detail Event
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
</x-layouts.crew>