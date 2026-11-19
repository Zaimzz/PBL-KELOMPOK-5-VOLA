<x-layouts.admin title="Verifikasi EO">

    <!-- Header & Breadcrumb -->
    <div class="mb-6 relative z-10 opacity-100">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border mb-2.5 bg-indigo-50 text-indigo-700 border-indigo-200/70">
                    <span class="relative flex h-2 w-2 mr-0.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    <span>MANAJEMEN KEMITRAAN • KURASI LEGALITAS</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Verifikasi <span class="text-amber-600">EO</span></h1>
                <p class="text-slate-600 text-sm mt-1">Tinjau dan validasi pendaftaran akun Event Organizer baru yang mengajukan akses ke platform VOLA.</p>
            </div>
            
            <!-- Header Metrics: 3 Luxury Cards with Micro-interactions -->
            <div id="admin-metrics-container" class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full xl:w-auto shrink-0">
                <!-- Menunggu -->
                <x-metric-card label="Menunggu Verifikasi" :value="number_format($stats['pending'], 0, ',', '.')" bg="bg-amber-100" text="text-amber-700" class="w-full sm:min-w-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </x-metric-card>

                <!-- Terverifikasi -->
                <x-metric-card label="Terverifikasi" :value="number_format($stats['verified'], 0, ',', '.')" bg="bg-emerald-100" text="text-emerald-700" class="w-full sm:min-w-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </x-metric-card>

                <!-- Ditolak -->
                <x-metric-card label="Ditolak" :value="number_format($stats['rejected'], 0, ',', '.')" bg="bg-rose-100" text="text-rose-700" class="w-full sm:min-w-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </x-metric-card>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div id="admin-content-container" x-data="eoVerificationManager(@js($organizersPayload ?? []), {{ request('selected') ? (int) request('selected') : 'null' }})" class="space-y-6">

        <!-- Filter & Search Bar -->
        <x-card class="p-3">
            <form action="{{ route('admin.eo-verifications') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                <div class="relative flex-1 min-w-0">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama organisasi, nama PIC, kota, atau nomor NIB"
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>
                
                <div class="flex items-center gap-2 overflow-x-auto pb-1.5 lg:pb-0 text-xs shrink-0 max-w-full">
                    <input type="hidden" name="status" id="status_input" value="{{ request('status') }}">
                    
                    <button type="button" onclick="setStatus('')"
                            class="px-3.5 py-2 font-semibold rounded-xl whitespace-nowrap transition-colors {{ !request('status') ? 'bg-brand-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua Status
                    </button>
                    <button type="button" onclick="setStatus('pending')"
                            class="px-3.5 py-2 font-semibold rounded-xl whitespace-nowrap transition-colors {{ request('status') === 'pending' ? 'bg-brand-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Menunggu
                    </button>
                    <button type="button" onclick="setStatus('verified')"
                            class="px-3.5 py-2 font-semibold rounded-xl whitespace-nowrap transition-colors {{ request('status') === 'verified' ? 'bg-brand-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Terverifikasi
                    </button>
                    <button type="button" onclick="setStatus('rejected')"
                            class="px-3.5 py-2 font-semibold rounded-xl whitespace-nowrap transition-colors {{ request('status') === 'rejected' ? 'bg-brand-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Ditolak
                    </button>
                    
                    @php
                        $hasActiveFilter = (request('search') && trim(request('search')) !== '')
                            || (request('status') && request('status') !== 'all' && request('status') !== '');
                    @endphp

                    @if($hasActiveFilter)
                        <a href="{{ route('admin.eo-verifications') }}" 
                           data-reset="true"
                           x-data="{ resetting: false }"
                           @click="resetting = true"
                           class="group inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800 active:scale-95 transition-all whitespace-nowrap shrink-0">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:-rotate-45 shrink-0"
                                 :class="{ 'animate-spin text-brand-600': resetting }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reset</span>
                        </a>
                    @endif
                    
                    <button type="submit" id="submit_filter" class="hidden">Filter</button>
                </div>
            </form>
        </x-card>

        <!-- Data Table -->
        <x-card padding="p-0" class="overflow-hidden border border-slate-200/90 rounded-2xl w-full min-w-0">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse table-fixed">
                    <colgroup>
                        <col class="w-[22%]">
                        <col class="w-[17%]">
                        <col class="w-[15%]">
                        <col class="w-[17%]">
                        <col class="w-[11%]">
                        <col class="w-[18%]">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="w-[22%] px-3 sm:px-4 py-3.5 whitespace-nowrap">Nama Organisasi</th>
                            <th class="w-[17%] px-3 sm:px-4 py-3.5 whitespace-nowrap">Penanggung Jawab (PIC)</th>
                            <th class="w-[15%] px-3 sm:px-4 py-3.5 whitespace-nowrap">Tanggal Pengajuan</th>
                            <th class="w-[17%] px-3 sm:px-4 py-3.5 whitespace-nowrap">Dokumen Terlampir</th>
                            <th class="w-[11%] px-2 sm:px-3 py-3.5 text-center whitespace-nowrap">Status</th>
                            <th class="w-[18%] px-2 sm:px-3 py-3.5 text-center whitespace-nowrap">Aksi Moderasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                        @forelse($organizers as $organizer)
                            @php
                                $hasPhysicalDoc = !empty($organizer->document_path);
                                $vStatus = $organizer->verification_status instanceof \App\Enums\VerificationStatus ? $organizer->verification_status->value : (string) $organizer->verification_status;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors duration-150">
                                <!-- Organisasi -->
                                <td class="px-3 sm:px-4 py-3.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center shrink-0 text-brand-600 font-extrabold text-xs shadow-2xs overflow-hidden relative">
                                            @if($organizer->user?->avatar_path)
                                                <img src="{{ $organizer->user->avatar_url }}" 
                                                     alt="{{ $organizer->organization_name }}"
                                                     class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                <div class="hidden w-full h-full flex items-center justify-center font-extrabold text-xs text-brand-600">
                                                    {{ strtoupper(substr($organizer->organization_name, 0, 2)) }}
                                                </div>
                                            @else
                                                {{ strtoupper(substr($organizer->organization_name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-gray-900 text-xs sm:text-sm line-clamp-2 leading-tight" title="{{ $organizer->organization_name }}">{{ $organizer->organization_name }}</div>
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-500 flex-wrap">
                                                <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium truncate max-w-[100px]">{{ $organizer->organization_type ?? 'Organisasi' }}</span>
                                                <span class="text-gray-400 truncate max-w-[80px]">{{ $organizer->city }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- PIC -->
                                <td class="px-3 sm:px-4 py-3.5">
                                    <div class="font-semibold text-gray-900 text-xs sm:text-sm truncate" title="{{ $organizer->pic_name }}">{{ $organizer->pic_name }}</div>
                                    <div class="text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-0.5 truncate">
                                        <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        <span class="truncate">{{ $organizer->pic_phone }}</span>
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="px-3 sm:px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-medium text-gray-900 text-xs sm:text-sm">{{ $organizer->created_at ? $organizer->created_at->translatedFormat('d M Y') : '-' }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $organizer->created_at ? $organizer->created_at->format('H:i').' WIB' : '' }}</div>
                                </td>

                                <!-- Dokumen Terlampir & NIB -->
                                <td class="px-3 sm:px-4 py-3.5">
                                    <div class="space-y-1">
                                        @if($hasPhysicalDoc)
                                            @php
                                                $isImgDoc = preg_match('/\.(jpg|jpeg|png|webp|svg)$/i', $organizer->document_path);
                                                $docRoute = route('admin.eo-verifications.document', $organizer);
                                            @endphp
                                            <button type="button"
                                                    onclick="window.openAdminLightbox('{{ $docRoute }}', '{{ addslashes($organizer->organization_name) }}', 'Dokumen Verifikasi: {{ addslashes($organizer->organization_type ?? 'Legalitas') }}', '{{ $isImgDoc ? 'image' : 'pdf' }}')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 text-[11px] font-semibold rounded-lg hover:bg-blue-100 transition-colors shadow-2xs cursor-pointer whitespace-nowrap"
                                                    title="Lihat Dokumen Legalitas">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Lihat Dokumen</span>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-gray-400 bg-gray-100 rounded whitespace-nowrap">
                                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>Belum diunggah</span>
                                            </span>
                                        @endif

                                        @php
                                            $nibNumber = $organizer->nib ?? $organizer->legal_number ?? null;
                                        @endphp
                                        <div class="flex items-center gap-1 text-[11px] whitespace-nowrap" x-data="{ copied: false }">
                                            <span class="text-[10px] uppercase font-bold text-gray-400">NIB:</span>
                                            @if(!empty($nibNumber))
                                                <code class="px-1 py-0.2 bg-gray-100 text-gray-800 rounded font-mono font-semibold text-[10px] truncate max-w-[85px]" title="{{ $nibNumber }}">{{ $nibNumber }}</code>
                                                <button type="button"
                                                        @click="navigator.clipboard.writeText('{{ $nibNumber }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                                        class="text-gray-400 hover:text-brand-600 transition-colors p-0.5 shrink-0"
                                                        :title="copied ? 'Tersalin!' : 'Salin Nomor NIB/SK'">
                                                    <template x-if="!copied">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </template>
                                                    <template x-if="copied">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </template>
                                                </button>
                                            @else
                                                <span class="text-[10px] text-gray-400 italic">Belum ada</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-2 sm:px-3 py-3.5 text-center whitespace-nowrap">
                                    @if($vStatus === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                            Menunggu
                                        </span>
                                    @elseif($vStatus === 'verified')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="px-2 sm:px-3 py-3.5 text-center">
                                    @if($vStatus === 'pending')
                                        <div class="inline-flex flex-col items-center gap-1.5">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- Tombol Setujui -->
                                                <button type="button"
                                                        x-on:click.prevent="$dispatch('open-modal', 'approve-modal-{{ $organizer->id }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full transition-colors cursor-pointer whitespace-nowrap"
                                                        title="Setujui Akun Organisasi">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Setujui</span>
                                                </button>

                                                <!-- Tombol Tolak -->
                                                <button type="button"
                                                        x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $organizer->id }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-full transition-colors cursor-pointer whitespace-nowrap"
                                                        title="Tolak Pengajuan Organisasi">
                                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <span>Tolak</span>
                                                </button>
                                            </div>

                                            <!-- Tombol Detail -->
                                            <button type="button"
                                                    @click="openDetailModal({{ $organizer->id }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full transition-colors cursor-pointer whitespace-nowrap"
                                                    title="Lihat Detail Organisasi">
                                                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Detail</span>
                                            </button>
                                        </div>
                                    @else
                                        <!-- Tombol Detail untuk Terverifikasi / Ditolak -->
                                        <button type="button"
                                                @click="openDetailModal({{ $organizer->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full transition-colors cursor-pointer whitespace-nowrap"
                                                title="Lihat Detail Organisasi">
                                            <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>
                                    @endif

                                    @if($vStatus === 'pending')
                                        <!-- Modal Konfirmasi Persetujuan -->
                                        <x-modal name="approve-modal-{{ $organizer->id }}" :show="false" maxWidth="md">
                                            <form method="POST" action="{{ route('admin.eo-verifications.approve', $organizer) }}" class="p-5 text-left flex flex-col space-y-3">
                                                @csrf
                                                
                                                <div class="flex items-start gap-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h3 class="text-base font-extrabold text-gray-900 truncate">
                                                            Setujui Kemitraan EO?
                                                        </h3>
                                                        <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $organizer->organization_name }}</p>
                                                    </div>
                                                </div>
                                                
                                                <p class="text-xs text-gray-600 leading-relaxed">
                                                    Organisasi <strong class="text-gray-800 font-bold">{{ $organizer->organization_name }}</strong> akan diverifikasi secara resmi dan mendapatkan akses penuh untuk mempublikasikan event di platform VOLA.
                                                </p>
                                                
                                                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                                                    <x-secondary-button type="button" @click="show = false; $dispatch('close')">
                                                        BATAL
                                                    </x-secondary-button>
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                                                        Ya, Setujui
                                                    </button>
                                                </div>
                                            </form>
                                        </x-modal>

                                        <!-- Reject Modal per Organizer -->
                                        <x-modal name="reject-modal-{{ $organizer->id }}" :show="false" maxWidth="md">
                                            <form method="POST" action="{{ route('admin.eo-verifications.reject', $organizer) }}" class="p-5 text-left flex flex-col space-y-3"
                                                  x-data="{
                                                      reason: '',
                                                      hasError: false,
                                                      handleSubmit(e) {
                                                          if (this.reason.trim() === '') {
                                                              e.preventDefault();
                                                              this.hasError = true;
                                                              return false;
                                                          }
                                                          this.hasError = false;
                                                      },
                                                      resetForm() {
                                                          this.reason = '';
                                                          this.hasError = false;
                                                          this.$el.querySelectorAll('.text-red-600').forEach(el => el.style.display = 'none');
                                                          const btn = this.$el.querySelector('button[type=submit]');
                                                          if (btn && window.unlockAdminButton) {
                                                              window.unlockAdminButton(btn);
                                                          }
                                                      }
                                                  }"
                                                  @open-modal.window="if ($event.detail === 'reject-modal-{{ $organizer->id }}') resetForm()"
                                                  @modal-closed.window="if ($event.detail === 'reject-modal-{{ $organizer->id }}') resetForm()"
                                                  @submit="handleSubmit($event)">
                                                @csrf
                                                
                                                <div class="flex items-start gap-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h3 class="text-base font-extrabold text-gray-900 truncate">
                                                            Tolak Pengajuan Organisasi?
                                                        </h3>
                                                        <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $organizer->organization_name }}</p>
                                                    </div>
                                                </div>
                                                
                                                <p class="text-xs text-gray-600 leading-relaxed">
                                                    Berikan alasan penolakan spesifik kepada pihak <strong class="text-gray-800 font-bold">{{ $organizer->organization_name }}</strong> agar dapat memperbaiki berkas pendaftarannya.
                                                </p>
                                                
                                                <div>
                                                    <label for="alasan_{{ $organizer->id }}" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                                        Alasan Penolakan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <textarea 
                                                        id="alasan_{{ $organizer->id }}" 
                                                        name="alasan" 
                                                        x-model="reason"
                                                        @input="if (reason.trim() !== '') hasError = false"
                                                        rows="2" 
                                                        placeholder="Contoh: Dokumen NIB tidak terbaca atau masa berlaku surat izin telah kedaluwarsa..."
                                                        class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors"
                                                        x-bind:class="hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-200' : ''"
                                                    >{{ old('alasan') }}</textarea>
                                                    <p x-show="hasError" class="text-rose-500 text-xs font-semibold mt-0.5" x-cloak>Alasan penolakan wajib diisi!</p>
                                                    <x-input-error :messages="$errors->get('alasan')" class="mt-1" />
                                                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-1" />
                                                </div>
                                                
                                                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                                                    <x-secondary-button type="button" @click="resetForm(); show = false; $dispatch('close')">
                                                        BATAL
                                                    </x-secondary-button>
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                                                        Kirim Penolakan
                                                    </button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <x-empty-state 
                                        title="Tidak ada data ditemukan" 
                                        description="Belum ada pendaftaran organisasi yang sesuai dengan pencarian atau status filter Anda." 
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bar -->
            @if ($organizers->total() > 0)
                <div class="p-4 border-t border-gray-100">
                    {{ $organizers->withQueryString()->links('components.pagination') }}
                </div>
            @endif
        </x-card>

        <!-- ============================================== -->
        <!-- MODAL DETAIL EO (COMPACT ANTI-SCROLL)          -->
        <!-- ============================================== -->
        <x-modal name="eo-detail-modal" :show="false" maxWidth="3xl">
            <div class="p-5 flex flex-col flex-1 min-h-0 overflow-hidden">
                <!-- Overlay Loading State -->
                <template x-if="loading">
                    <div class="py-12 text-center text-gray-400 my-auto">
                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs font-medium text-gray-600">Memuat berkas profil organisasi...</p>
                    </div>
                </template>

                <!-- Error State with Retry Button -->
                <template x-if="!loading && hasError">
                    <div class="py-10 text-center space-y-3.5 my-auto">
                        <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Gagal Memuat Profil EO</h4>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="errorMessage"></p>
                        </div>
                        <div>
                            <button type="button" @click="openDetailModal(currentId)"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Loaded State -->
                <template x-if="!loading && !hasError && currentOrganizer">
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        <!-- Header Modal -->
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-sm shadow-xs overflow-hidden relative shrink-0">
                                    <template x-if="currentOrganizer.avatar_url">
                                        <img :src="currentOrganizer.avatar_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!currentOrganizer.avatar_url">
                                        <span x-text="currentOrganizer.avatar_initials"></span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-base sm:text-lg font-extrabold text-gray-900 truncate" x-text="currentOrganizer.organization_name"></h2>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-semibold bg-gray-100 text-gray-700 px-2 py-0.5 rounded" x-text="currentOrganizer.organization_type"></span>
                                        <span class="text-[11px] text-gray-400">&bull; <span x-text="currentOrganizer.city"></span></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" @click="closeDetailModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Tutup Modal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Grid Details -->
                        <div class="space-y-3 text-xs flex-1 overflow-y-auto overscroll-y-contain min-h-0 py-3 pr-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-gray-50/80 p-3 rounded-xl border border-gray-100">
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Penanggung Jawab (PIC)</span>
                                    <p class="font-bold text-gray-900 text-xs mt-0.5" x-text="currentOrganizer.pic_name"></p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Kontak WhatsApp</span>
                                    <p class="font-bold text-emerald-600 text-xs mt-0.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        <span x-text="currentOrganizer.pic_phone"></span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Tanggal Pengajuan</span>
                                    <p class="font-bold text-gray-800 text-xs mt-0.5" x-text="currentOrganizer.created_at_formatted"></p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Status Verifikasi</span>
                                    <p class="font-bold uppercase tracking-wider text-xs mt-0.5"
                                       :class="currentOrganizer.status === 'verified' ? 'text-emerald-600' : (currentOrganizer.status === 'rejected' ? 'text-rose-600' : 'text-amber-600')"
                                       x-text="currentOrganizer.status">
                                    </p>
                                </div>
                            </div>

                            <!-- Deskripsi Profil -->
                            <div>
                                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Singkat Organisasi</h4>
                                <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-700 leading-relaxed max-h-16 overflow-y-auto"
                                     x-text="currentOrganizer.description">
                                </div>
                            </div>

                            <!-- Media Sosial / Website -->
                            <template x-if="currentOrganizer.social_link">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Tautan Publik / Media Sosial</h4>
                                    <a :href="currentOrganizer.social_link" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-brand-600 hover:text-brand-700 hover:underline font-semibold">
                                        <span x-text="currentOrganizer.social_link"></span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </template>

                            <!-- Nomor Legalitas Resmi (NIB / SK) -->
                            <div class="p-2.5 bg-indigo-50/60 rounded-xl border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2" x-data="{ copiedNib: false }">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700">Nomor Legalitas Terdaftar (NIB / SK)</span>
                                    <p class="font-mono font-bold text-xs sm:text-sm text-gray-900 mt-0.5 tracking-tight" x-text="currentOrganizer.nib || 'Belum dicantumkan oleh EO'"></p>
                                </div>
                                <template x-if="currentOrganizer.nib">
                                    <button type="button"
                                            @click="navigator.clipboard.writeText(currentOrganizer.nib); copiedNib = true; setTimeout(() => copiedNib = false, 1500)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 rounded-lg text-xs font-bold shadow-2xs transition-all active:scale-95 shrink-0">
                                        <template x-if="!copiedNib">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </template>
                                        <template x-if="copiedNib">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                        <span x-text="copiedNib ? 'Nomor Tersalin!' : 'Salin NIB/SK'"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Dokumen Terlampir Section -->
                            <div>
                                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Dokumen Legalitas Terlampir</h4>
                                <template x-if="currentOrganizer.has_document && currentOrganizer.document_url">
                                    <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 text-blue-900 text-xs">
                                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="font-bold text-xs" x-text="currentOrganizer.document_type === 'image' ? 'Foto Dokumen / Identitas EO' : 'Dokumen Legalitas (PDF)'"></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <a :href="currentOrganizer.document_url + '?download=1'" 
                                                   target="_blank"
                                                   download
                                                   class="px-2 py-1 bg-white hover:bg-blue-50 text-blue-700 font-bold text-[10px] rounded-lg border border-blue-200 transition-colors shadow-2xs inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <span>Unduh</span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Preview if Image and not failed -->
                                        <template x-if="currentOrganizer.document_type === 'image' && !docImageFailed">
                                            <div class="relative rounded-lg overflow-hidden bg-slate-100 border border-blue-200/60 h-36 group cursor-pointer"
                                                 x-on:click="window.openAdminLightbox(currentOrganizer.document_url, currentOrganizer.organization_name, 'Dokumen Legalitas: ' + currentOrganizer.organization_type, 'image')">
                                                
                                                <!-- Skeleton Loader selama gambar sedang dimuat -->
                                                <div x-show="docImageLoading"
                                                     class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100 animate-pulse z-10">
                                                    <svg class="w-6 h-6 animate-spin text-blue-500 mb-1.5" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                    </svg>
                                                    <span class="text-[11px] font-medium text-slate-500">Memuat pratinjau dokumen...</span>
                                                </div>

                                                <img :src="currentOrganizer.document_url" 
                                                     :alt="currentOrganizer.organization_name"
                                                     x-on:load="docImageLoading = false"
                                                     x-on:error="docImageLoading = false; docImageFailed = true"
                                                     :class="docImageLoading ? 'opacity-0' : 'opacity-100'"
                                                     class="w-full h-full object-contain group-hover:scale-105 transition-all duration-200">
                                                
                                                <div x-show="!docImageLoading"
                                                     class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-white font-bold text-[11px] backdrop-blur-md border border-white/30 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0M10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                        </svg>
                                                        Perbesar Foto Dokumen
                                                    </span>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Fallback if Image failed to load -->
                                        <template x-if="currentOrganizer.document_type === 'image' && docImageFailed">
                                            <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-amber-900 truncate">Pratinjau visual tidak tersedia untuk berkas ini</p>
                                                        <p class="text-[11px] text-amber-700 truncate">Berkas gambar belum tersedia secara fisik atau format tidak didukung.</p>
                                                    </div>
                                                </div>
                                                <a :href="currentOrganizer.document_url"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white border border-amber-300 text-amber-800 text-xs font-semibold rounded-lg hover:bg-amber-100 transition-colors shrink-0 shadow-2xs">
                                                    <span>Buka Berkas</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </template>

                                        <!-- Preview Button for PDF / Document -->
                                        <template x-if="currentOrganizer.document_type !== 'image' || (!docImageFailed && !docImageLoading)">
                                            <button type="button"
                                                    x-on:click="window.openAdminLightbox(currentOrganizer.document_url, currentOrganizer.organization_name, 'Dokumen Legalitas: ' + currentOrganizer.organization_type, currentOrganizer.document_type || 'pdf')"
                                                    class="w-full py-2 px-3 bg-white hover:bg-blue-50 text-blue-700 rounded-lg border border-blue-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span x-text="currentOrganizer.document_type === 'image' ? 'Perbesar Foto Dokumen di Lightbox' : 'Buka Dokumen PDF di Lightbox'"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!currentOrganizer.has_document || !currentOrganizer.document_url">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-xl">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>Dokumen belum diunggah oleh penyelenggara</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Catatan Penolakan jika ada -->
                            <template x-if="currentOrganizer.status === 'rejected' && currentOrganizer.rejection_reason">
                                <div class="bg-rose-50 border border-rose-100 rounded-xl p-2.5">
                                    <h4 class="text-[11px] font-bold text-rose-800 mb-0.5">Catatan Penolakan Sebelumnya:</h4>
                                    <p class="text-xs text-rose-700 leading-relaxed" x-text="currentOrganizer.rejection_reason"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Modal: Only shown when status is pending -->
                        <template x-if="currentOrganizer.status === 'pending'">
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                                <button type="button"
                                        @click="$dispatch('close'); $dispatch('open-modal', 'approve-modal-' + currentOrganizer.id)"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui Akun
                                </button>

                                <button type="button"
                                        @click="$dispatch('close'); $dispatch('open-modal', 'reject-modal-' + currentOrganizer.id)"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tolak Akun
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </x-modal>

    </div>

    @push('scripts')
    <script>
        function setStatus(status) {
            document.getElementById('status_input').value = status;
            document.getElementById('submit_filter').click();
        }

        function eoVerificationManager(initialOrganizers, autoSelectedId) {
            return {
                organizers: initialOrganizers || [],
                currentOrganizer: null,
                loading: false,
                hasError: false,
                errorMessage: '',
                currentId: null,
                docImageLoading: false,
                docImageFailed: false,

                init() {
                    if (autoSelectedId) {
                        this.$nextTick(() => {
                            this.openDetailModal(autoSelectedId, false);
                        });
                    }

                    // Handle Back/Forward browser navigation
                    window.addEventListener('popstate', (e) => {
                        const curUrl = new URL(window.location.href);
                        const sel = curUrl.searchParams.get('selected');
                        if (sel) {
                            this.openDetailModal(parseInt(sel), false);
                        } else {
                            this.$dispatch('close');
                            this.currentOrganizer = null;
                            this.currentId = null;
                        }
                    });

                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.currentId) {
                            this.closeDetailModal();
                        }
                    });
                },

                openDetailModal(id, shouldPushState = true) {
                    this.currentId = id;
                    this.loading = true;
                    this.hasError = false;
                    this.errorMessage = '';
                    this.currentOrganizer = null;
                    this.docImageLoading = false;
                    this.docImageFailed = false;

                    if (shouldPushState) {
                        const url = new URL(window.location.href);
                        if (url.searchParams.get('selected') !== String(id)) {
                            url.searchParams.set('selected', id);
                            window.history.pushState({ selected: id }, '', url.toString());
                        }
                    }

                    this.$dispatch('open-modal', 'eo-detail-modal');

                    const found = this.organizers.find(o => o.id == id);
                    setTimeout(() => {
                        if (found) {
                            this.currentOrganizer = found;
                            this.docImageLoading = found.document_type === 'image' && Boolean(found.has_document && found.document_url);
                            this.loading = false;
                        } else {
                            this.hasError = true;
                            this.errorMessage = 'Data profil organisasi tidak ditemukan atau telah diperbarui.';
                            this.loading = false;
                        }
                    }, 150);
                },

                closeDetailModal() {
                    this.$dispatch('close');
                    this.currentOrganizer = null;
                    this.currentId = null;
                    const url = new URL(window.location.href);
                    if (url.searchParams.has('selected')) {
                        url.searchParams.delete('selected');
                        window.history.pushState({}, '', url.toString());
                    }
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>