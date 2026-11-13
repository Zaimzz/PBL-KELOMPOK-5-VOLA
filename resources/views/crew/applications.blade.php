<x-layouts.crew title="Lamaran Saya - VOLA">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ activeTab: 'all' }">
    
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Lamaran Saya</h1>
        <p class="mt-1 text-sm text-gray-500">Pantau status pendaftaran volunteer kamu.</p>
    </div>

        <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-6 overflow-x-auto pb-1">
          
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Semua 
                <span :class="activeTab === 'all' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ count($applications) }}</span>
            </button>
            
            <button @click="activeTab = 'pending'" 
                    :class="activeTab === 'pending' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Menunggu Seleksi 
                <span :class="activeTab === 'pending' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ count(array_filter($applications, fn($a) => $a['status'] === 'pending')) }}</span>
            </button>
            
            <button @click="activeTab = 'processing'" 
                    :class="activeTab === 'processing' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Sedang Diproses 
                <span :class="activeTab === 'processing' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ count(array_filter($applications, fn($a) => $a['status'] === 'processing')) }}</span>
            </button>
          
            <button @click="activeTab = 'accepted'" 
                    :class="activeTab === 'accepted' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Diterima 
                <span :class="activeTab === 'accepted' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ count(array_filter($applications, fn($a) => $a['status'] === 'accepted')) }}</span>
            </button>
            
            <button @click="activeTab = 'rejected'" 
                    :class="activeTab === 'rejected' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5">
                Ditolak 
                <span :class="activeTab === 'rejected' ? 'text-[#4F46E5]' : 'text-gray-400'">{{ count(array_filter($applications, fn($a) => $a['status'] === 'rejected')) }}</span>
            </button>
        </nav>
    </div>

    <div class="space-y-4" :key="'list-' + activeTab"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0">

        @forelse($applications as $index => $application)
        
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300"
             x-show="activeTab === 'all' || activeTab === '{{ $application['status'] }}'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             :style="`transition-delay: ${({{ $index }} % 5) * 50}ms`">
           
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2">
                    @if($application['status'] === 'accepted')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full border border-emerald-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $application['status_label'] }}
                        </span>
                    @elseif($application['status'] === 'pending')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full border border-amber-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $application['status_label'] }}
                        </span>
                    @elseif($application['status'] === 'processing')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full border border-blue-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $application['status_label'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full border border-red-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ $application['status_label'] }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-gray-400 font-medium">Dilamar: {{ $application['applied_at'] }}</span>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-1 leading-snug">{{ $application['event_title'] }}</h3>
                <p class="text-sm text-gray-600">{{ $application['event_organizer'] }} • <span class="text-gray-500">{{ $application['event_category'] }}</span></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4 mb-5">
                <div class="flex items-start gap-2.5 text-sm text-gray-600">
                    <div class="mt-0.5 w-4 h-4 text-gray-400 shrink-0">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">Posisi</p>
                        <p class="font-medium text-gray-800">{{ $application['position'] }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-2.5 text-sm text-gray-600">
                    <div class="mt-0.5 w-4 h-4 text-gray-400 shrink-0">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">Lokasi</p>
                        <p class="font-medium text-gray-800">{{ $application['location'] }}</p>
                    </div>
                </div>
            </div>

            @if(!empty($application['message']))
            <div class="mb-5 p-3 rounded-xl border 
                @if($application['message_type'] === 'success') bg-emerald-50 border-emerald-100 text-emerald-800
                @elseif($application['message_type'] === 'warning') bg-amber-50 border-amber-100 text-amber-800
                @elseif($application['message_type'] === 'info') bg-blue-50 border-blue-100 text-blue-800
                @else bg-red-50 border-red-100 text-red-800 @endif">
                
                <div class="flex items-start gap-3">
                    @if($application['message_type'] === 'success')
                        <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($application['message_type'] === 'warning')
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @elseif($application['message_type'] === 'info')
                        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                    <p class="text-sm leading-relaxed">{{ $application['message'] }}</p>
                </div>
            </div>
            @endif

            <div class="flex items-center justify-end gap-2">
                @if($application['status'] === 'accepted')
                    <a href="{{ route('applications.download-acceptance', $application['id']) }}" 
                       class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh Bukti Penerimaan
                    </a>
                    <a href="{{ route('my-events.index') }}" 
                       class="px-4 py-2 bg-[#4F46E5] text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Lihat di Event Saya
                    </a>
                @else
                    <a href="{{ route('events.detail', ['event' => $application['event_id']]) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat Detail Event
                    </a>
                @endif
            </div>
        </div>
        @empty
         
            <div x-show="activeTab === 'all'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-50 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Lamaran</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">Kamu belum mengajukan lamaran volunteer apapun. Yuk cari event yang cocok!</p>
                <a href="{{ route('search.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Event Sekarang
                </a>
            </div>
        @endforelse

        <div x-show="activeTab === 'pending' && {{ count(array_filter($applications, fn($a) => $a['status'] === 'pending')) }} === 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center" style="display: none;">
            <div class="w-16 h-16 mx-auto mb-4 bg-amber-50 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Lamaran Menunggu</h3>
        </div>

        <div x-show="activeTab === 'processing' && {{ count(array_filter($applications, fn($a) => $a['status'] === 'processing')) }} === 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center" style="display: none;">
            <div class="w-16 h-16 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Lamaran Diproses</h3>
        </div>

        <div x-show="activeTab === 'accepted' && {{ count(array_filter($applications, fn($a) => $a['status'] === 'accepted')) }} === 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center" style="display: none;">
            <div class="w-16 h-16 mx-auto mb-4 bg-emerald-50 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Lamaran Diterima</h3>
            <a href="{{ route('search.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                Cari Event Lainnya
            </a>
        </div>

        <div x-show="activeTab === 'rejected' && {{ count(array_filter($applications, fn($a) => $a['status'] === 'rejected')) }} === 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center" style="display: none;">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-50 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Lamaran Ditolak</h3>
        </div>

    </div>
</div>
</x-layouts.crew>