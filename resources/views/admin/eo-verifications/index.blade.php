<x-layouts.admin title="Verifikasi EO">

    <!-- Header & Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <span class="font-bold text-brand-600 uppercase tracking-wider text-xs">MANAJEMEN KEMITRAAN &bull; KURASI LEGALITAS</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Verifikasi EO</h1>
                <p class="text-gray-500 text-sm mt-1">Tinjau dan validasi pendaftaran akun Event Organizer baru yang mengajukan akses ke platform VOLA.</p>
            </div>
            
            <!-- Header Metrics: 3 Luxury Cards with Micro-interactions -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full sm:w-auto">
                <!-- Menunggu -->
                <div class="bg-white border border-gray-100 rounded-2xl p-3.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-gray-900">{{ number_format($stats['pending'], 0, ',', '.') }}</div>
                        <div class="text-[11px] text-gray-500 font-medium">Menunggu Verifikasi</div>
                    </div>
                </div>

                <!-- Terverifikasi -->
                <div class="bg-white border border-gray-100 rounded-2xl p-3.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-gray-900">{{ number_format($stats['verified'], 0, ',', '.') }}</div>
                        <div class="text-[11px] text-gray-500 font-medium">Terverifikasi</div>
                    </div>
                </div>

                <!-- Ditolak -->
                <div class="bg-white border border-gray-100 rounded-2xl p-3.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-gray-900">{{ number_format($stats['rejected'], 0, ',', '.') }}</div>
                        <div class="text-[11px] text-gray-500 font-medium">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div x-data="eoVerificationManager(@js($organizersPayload ?? []), {{ request('selected') ? (int) request('selected') : 'null' }})" class="space-y-6">

        <!-- Filter & Search Bar -->
        <x-card class="p-3">
            <form action="{{ route('admin.eo-verifications') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama organisasi atau penanggung jawab (PIC)..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>
                
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 text-xs">
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
                    
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.eo-verifications') }}" class="px-3 py-2 text-xs font-semibold text-gray-500 hover:text-gray-700 underline flex items-center whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                    
                    <button type="submit" id="submit_filter" class="hidden">Filter</button>
                </div>
            </form>
        </x-card>

        <!-- Data Table -->
        <x-card padding="p-0" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Nama Organisasi</th>
                            <th class="px-6 py-4">Penanggung Jawab (PIC)</th>
                            <th class="px-6 py-4">Tanggal Pengajuan</th>
                            <th class="px-6 py-4">Dokumen Terlampir</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi Moderasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                        @forelse($organizers as $organizer)
                            @php
                                $hasPhysicalDoc = false;
                                if (!empty($organizer->document_path)) {
                                    $hasPhysicalDoc = \Illuminate\Support\Facades\Storage::disk('local')->exists($organizer->document_path)
                                        || \Illuminate\Support\Facades\Storage::disk('public')->exists($organizer->document_path)
                                        || file_exists(public_path('storage/' . $organizer->document_path));
                                }
                                $vStatus = $organizer->verification_status instanceof \App\Enums\VerificationStatus ? $organizer->verification_status->value : (string) $organizer->verification_status;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors duration-150">
                                <!-- Organisasi -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center shrink-0 text-brand-600 font-extrabold text-sm shadow-xs">
                                            {{ strtoupper(substr($organizer->organization_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $organizer->organization_name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-medium">{{ $organizer->organization_type ?? 'Organisasi' }}</span>
                                                <span class="text-[11px] text-gray-400">{{ $organizer->city }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- PIC -->
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $organizer->pic_name }}</div>
                                    <div class="text-xs text-emerald-600 font-medium flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        {{ $organizer->pic_phone }}
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $organizer->created_at ? $organizer->created_at->translatedFormat('d M Y') : '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $organizer->created_at ? $organizer->created_at->format('H:i').' WIB' : '' }}</div>
                                </td>

                                <!-- Dokumen Terlampir -->
                                <td class="px-6 py-4">
                                    @if($hasPhysicalDoc)
                                        <a href="{{ route('admin.eo-verifications.document', $organizer) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-xl hover:bg-blue-100 transition-colors shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Dokumen Legalitas
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Dokumen belum tersedia
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($vStatus === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu
                                        </span>
                                    @elseif($vStatus === 'verified')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi (Revamped: Modern Solid/Pastel, Proportional Icons, No Tiny Button Spinners) -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($vStatus === 'pending')
                                            <!-- Tombol Setujui -->
                                            <form action="{{ route('admin.eo-verifications.approve', $organizer) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition-all duration-200 shadow-2xs active:scale-95"
                                                        title="Setujui Akun Organisasi">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <button type="button"
                                                    x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $organizer->id }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition-all duration-200 shadow-2xs active:scale-95"
                                                    title="Tolak Pengajuan Organisasi">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span>Tolak</span>
                                            </button>
                                        @endif

                                        <!-- Tombol Lihat Detail (Modal Overlay Loading State) -->
                                        <button type="button"
                                                @click="openDetailModal({{ $organizer->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-bold rounded-xl transition-all duration-200 shadow-2xs active:scale-95"
                                                title="Lihat Detail Organisasi">
                                            <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>
                                    </div>

                                    @if($vStatus === 'pending')
                                        <!-- Reject Modal per Organizer -->
                                        <x-modal name="reject-modal-{{ $organizer->id }}" :show="false" maxWidth="md">
                                            <form method="POST" action="{{ route('admin.eo-verifications.reject', $organizer) }}" class="p-6 text-left"
                                                  x-data="{ reason: '', hasError: false }"
                                                  @submit.prevent="reason.trim() === '' ? hasError = true : $el.submit()">
                                                @csrf
                                                
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-base font-extrabold text-gray-900">
                                                            Tolak Organisasi {{ $organizer->organization_name }}?
                                                        </h3>
                                                        <p class="text-xs text-gray-500">Berikan catatan penolakan spesifik kepada pihak penyelenggara.</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <label for="alasan_{{ $organizer->id }}" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                                        Alasan Penolakan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <textarea 
                                                        id="alasan_{{ $organizer->id }}" 
                                                        name="alasan" 
                                                        x-model="reason"
                                                        @input="if (reason.trim() !== '') hasError = false"
                                                        rows="3" 
                                                        placeholder="Contoh: Dokumen NIB tidak terbaca atau masa berlaku surat izin telah kedaluwarsa..."
                                                        class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors"
                                                        x-bind:class="hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-200' : ''"
                                                    >{{ old('alasan') }}</textarea>
                                                    <p x-show="hasError" class="text-rose-500 text-xs font-semibold mt-1" x-cloak>Alasan penolakan wajib diisi!</p>
                                                    <x-input-error :messages="$errors->get('alasan')" class="mt-1" />
                                                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-1" />
                                                </div>
                                                
                                                <div class="mt-6 flex justify-end gap-2.5">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        Batal
                                                    </x-secondary-button>
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
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
            
            <!-- Pagination -->
            @if($organizers->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $organizers->links() }}
                </div>
            @else
                <div class="p-4 border-t border-gray-100 text-xs text-gray-500 text-center">
                    Menampilkan {{ $organizers->count() }} permohonan kemitraan EO.
                </div>
            @endif
        </x-card>

        <!-- ============================================== -->
        <!-- MODAL DETAIL EO (STANDAR OVERLAY LOADING STATE) -->
        <!-- ============================================== -->
        <x-modal name="eo-detail-modal" :show="false" maxWidth="2xl">
            <div class="p-6">
                <!-- Overlay Loading State (Sesuai Standar Event Aktif) -->
                <template x-if="loading">
                    <div class="py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs">Memuat berkas profil organisasi...</p>
                    </div>
                </template>

                <!-- Loaded State -->
                <template x-if="!loading && currentOrganizer">
                    <div>
                        <!-- Header Modal -->
                        <div class="flex items-start justify-between pb-4 border-b border-gray-100 mb-5">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-base shadow-xs"
                                     x-text="currentOrganizer.avatar_initials">
                                </div>
                                <div>
                                    <h2 class="text-xl font-extrabold text-gray-900" x-text="currentOrganizer.organization_name"></h2>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-md" x-text="currentOrganizer.organization_type"></span>
                                        <span class="text-xs text-gray-400">&bull; <span x-text="currentOrganizer.city"></span></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Grid Details -->
                        <div class="space-y-4 text-xs max-h-[70vh] overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 bg-gray-50/80 p-4 rounded-2xl border border-gray-100">
                                <div>
                                    <span class="text-gray-400 font-medium">Penanggung Jawab (PIC)</span>
                                    <p class="font-bold text-gray-900 text-sm mt-0.5" x-text="currentOrganizer.pic_name"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium">Kontak WhatsApp</span>
                                    <p class="font-bold text-emerald-600 text-sm mt-0.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        <span x-text="currentOrganizer.pic_phone"></span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium">Tanggal Pengajuan</span>
                                    <p class="font-bold text-gray-800 text-xs mt-0.5" x-text="currentOrganizer.created_at_formatted"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium">Status Verifikasi</span>
                                    <p class="font-bold uppercase tracking-wider text-xs mt-0.5"
                                       :class="currentOrganizer.status === 'verified' ? 'text-emerald-600' : (currentOrganizer.status === 'rejected' ? 'text-rose-600' : 'text-amber-600')"
                                       x-text="currentOrganizer.status">
                                    </p>
                                </div>
                            </div>

                            <!-- Deskripsi Profil -->
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Deskripsi Singkat Organisasi</h4>
                                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-700 leading-relaxed"
                                     x-text="currentOrganizer.description">
                                </div>
                            </div>

                            <!-- Media Sosial / Website -->
                            <template x-if="currentOrganizer.social_link">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tautan Publik / Media Sosial</h4>
                                    <a :href="currentOrganizer.social_link" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-brand-600 hover:text-brand-700 hover:underline font-semibold">
                                        <span x-text="currentOrganizer.social_link"></span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </template>

                            <!-- Dokumen Terlampir Section (With Elegant Fallback UI) -->
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Dokumen Legalitas Terlampir</h4>
                                <template x-if="currentOrganizer.has_document && currentOrganizer.document_url">
                                    <a :href="currentOrganizer.document_url" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl hover:bg-blue-100 transition-colors shadow-2xs">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Buka Dokumen Legalitas (PDF/Image)</span>
                                    </a>
                                </template>
                                <template x-if="!currentOrganizer.has_document || !currentOrganizer.document_url">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-xl">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>Dokumen belum tersedia di server</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Catatan Penolakan jika ada -->
                            <template x-if="currentOrganizer.status === 'rejected' && currentOrganizer.rejection_reason">
                                <div class="bg-rose-50 border border-rose-100 rounded-xl p-3.5">
                                    <h4 class="text-xs font-bold text-rose-800 mb-0.5">Catatan Penolakan Sebelumnya:</h4>
                                    <p class="text-xs text-rose-700 leading-relaxed" x-text="currentOrganizer.rejection_reason"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Modal -->
                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <template x-if="currentOrganizer.status === 'pending'">
                                    <div class="flex items-center gap-2">
                                        <form :action="currentOrganizer.approve_url" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui Akun
                                            </button>
                                        </form>

                                        <button type="button"
                                                @click="$dispatch('close'); $dispatch('open-modal', 'reject-modal-' + currentOrganizer.id)"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Tolak Akun
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <x-secondary-button x-on:click="$dispatch('close')">
                                Tutup
                            </x-secondary-button>
                        </div>
                    </div>
                </template>
            </div>
        </x-modal>

    </div>

    <!-- Information Cards at bottom -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <x-card class="hover:shadow-md transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-brand-600 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1.5 text-sm">Kriteria Persetujuan</h3>
            <p class="text-xs text-gray-500 leading-relaxed">Pastikan NIB / SK Dekanat organisasi aktif, penanggung jawab terdaftar memiliki kontak valid, dan ruang lingkup kegiatan relevan dengan kerelawanan.</p>
        </x-card>

        <x-card class="hover:shadow-md transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1.5 text-sm">Komunikasi PIC</h3>
            <p class="text-xs text-gray-500 leading-relaxed">Hubungi nomor WhatsApp penanggung jawab jika dokumen legalitas terpotong atau membutuhkan kelengkapan surat keterangan organisasi.</p>
        </x-card>

        <x-card class="hover:shadow-md transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1.5 text-sm">Akses Pasca Verifikasi</h3>
            <p class="text-xs text-gray-500 leading-relaxed">EO yang telah disetujui akan otomatis memperoleh wewenang membuat publikasi event dan membuka pendaftaran relawan mahasiswa se-Indonesia.</p>
        </x-card>
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

                init() {
                    if (autoSelectedId) {
                        this.$nextTick(() => {
                            this.openDetailModal(autoSelectedId);
                        });
                    }
                },

                openDetailModal(id) {
                    this.loading = true;
                    this.currentOrganizer = null;
                    this.$dispatch('open-modal', 'eo-detail-modal');

                    const found = this.organizers.find(o => o.id == id);
                    setTimeout(() => {
                        this.currentOrganizer = found || null;
                        this.loading = false;
                    }, 220);
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>
