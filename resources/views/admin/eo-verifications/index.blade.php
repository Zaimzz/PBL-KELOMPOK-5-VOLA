<x-layouts.admin title="Verifikasi EO">

    <!-- Header & Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <span class="font-medium text-brand-600 uppercase tracking-wider text-xs">MANAJEMEN KEMITRAAN</span>
            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
            <span class="uppercase tracking-wider text-xs">PBL KOLABORATIF</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Verifikasi EO</h1>
                <p class="text-gray-500 mt-1">Tinjau dan validasi pendaftaran akun Event Organizer baru yang mengajukan akses ke platform VOLA.</p>
            </div>
            
            <!-- Status Summary Badges -->
            <div class="flex items-center gap-2">
                <div class="bg-amber-100 text-amber-800 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>{{ $stats['pending'] }}</span>
                    <span class="font-normal opacity-80">Menunggu</span>
                </div>
                <div class="bg-green-100 text-green-800 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-green-200">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span>{{ $stats['verified'] }}</span>
                    <span class="font-normal opacity-80">Terverifikasi</span>
                </div>
                <div class="bg-red-100 text-red-800 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-red-200">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span>{{ $stats['rejected'] }}</span>
                    <span class="font-normal opacity-80">Ditolak</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <x-card class="mb-6 p-2">
        <form action="{{ route('admin.eo-verifications') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama organisasi atau penanggung jawab (PIC)"
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border-transparent rounded-lg focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
            </div>
            
            <div class="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
                <input type="hidden" name="status" id="status_input" value="{{ request('status') }}">
                
                <button type="button" onclick="setStatus('')"
                        class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors {{ !request('status') ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua Status
                </button>
                <button type="button" onclick="setStatus('pending')"
                        class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors {{ request('status') === 'pending' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Menunggu
                </button>
                <button type="button" onclick="setStatus('verified')"
                        class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors {{ request('status') === 'verified' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Terverifikasi
                </button>
                <button type="button" onclick="setStatus('rejected')"
                        class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors {{ request('status') === 'rejected' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Ditolak
                </button>
                
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.eo-verifications') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 underline flex items-center whitespace-nowrap">
                        Reset
                    </a>
                @endif
                
                <button type="submit" id="submit_filter" class="hidden">Filter</button>
            </div>
        </form>
    </x-card>

    <!-- Data Table -->
    <x-card padding="p-0" class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Organisasi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Penanggung Jawab (PIC)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokumen Terlampir</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($organizers as $organizer)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center shrink-0 text-brand-600 font-bold text-lg">
                                        {{ strtoupper(substr($organizer->organization_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $organizer->organization_name }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $organizer->organization_type ?? 'Organisasi' }}</span>
                                            <span class="text-xs text-gray-500">{{ $organizer->city }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $organizer->pic_name }}</div>
                                <div class="text-sm text-green-600 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    {{ $organizer->pic_phone }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900">{{ $organizer->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $organizer->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($organizer->document_path)
                                    <a href="{{ route('admin.eo-verifications.document', $organizer) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Dokumen Legalitas
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400 italic">Tidak ada dokumen</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-badge-status :status="$organizer->verification_status->value" />
                            </td>
                            <td class="px-6 py-4 text-center ">
                                @if($organizer->verification_status->value === 'pending')
                                    <div class="flex flex-col gap-2 w-24 mx-auto">
                                        <form action="{{ route('admin.eo-verifications.approve', $organizer) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>
                                        
                                        <button type="button" 
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $organizer->id }}')"
                                                class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-white border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Tolak
                                        </button>
                                    </div>
                                    
                                    <!-- Reject Modal -->
                                    <x-modal name="reject-modal-{{ $organizer->id }}" :show="false" maxWidth="md">
                                        <form method="POST" action="{{ route('admin.eo-verifications.reject', $organizer) }}" class="p-6">
                                            @csrf
                                            
                                            <h2 class="text-lg font-medium text-gray-900">
                                                Tolak Organisasi {{ $organizer->organization_name }}?
                                            </h2>
                                            
                                            <p class="mt-1 text-sm text-gray-600">
                                                Silakan berikan alasan penolakan. Alasan ini akan dikirimkan ke EO terkait agar mereka dapat memperbaikinya.
                                            </p>
                                            
                                            <div class="mt-4">
                                                <x-input-label for="rejection_reason_{{ $organizer->id }}" value="Alasan Penolakan" />
                                                <textarea 
                                                    id="rejection_reason_{{ $organizer->id }}" 
                                                    name="rejection_reason" 
                                                    rows="3" 
                                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                                    required
                                                >{{ old('rejection_reason') }}</textarea>
                                                <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                                            </div>
                                            
                                            <div class="mt-6 flex justify-end gap-3">
                                                <x-secondary-button x-on:click="$dispatch('close')">
                                                    Batal
                                                </x-secondary-button>
                                                <x-danger-button type="submit">
                                                    Kirim Penolakan
                                                </x-danger-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @else
                                    <button type="button" 
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'detail-modal-{{ $organizer->id }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                        Detail
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Detail Modal -->
                                    <x-modal name="detail-modal-{{ $organizer->id }}" :show="false" maxWidth="lg">
                                        <div class="p-6">
                                            <div class="flex items-start justify-between mb-4">
                                                <h2 class="text-xl font-bold text-gray-900">Detail Organisasi</h2>
                                                <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-500">Nama Organisasi</p>
                                                    <p class="mt-1 text-gray-900 font-semibold">{{ $organizer->organization_name }}</p>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Jenis Organisasi</p>
                                                        <p class="mt-1 text-gray-900">{{ $organizer->organization_type ?? '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Kota</p>
                                                        <p class="mt-1 text-gray-900">{{ $organizer->city }}</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                                                    <p class="mt-1 text-gray-900 text-sm">{{ $organizer->description ?: '-' }}</p>
                                                </div>
                                                <hr class="border-gray-100">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Penanggung Jawab</p>
                                                        <p class="mt-1 text-gray-900">{{ $organizer->pic_name }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Kontak (WhatsApp)</p>
                                                        <p class="mt-1 text-gray-900">{{ $organizer->pic_phone }}</p>
                                                    </div>
                                                </div>
                                                @if($organizer->social_link)
                                                <div>
                                                    <p class="text-sm font-medium text-gray-500">Tautan Sosial Media</p>
                                                    <a href="{{ $organizer->social_link }}" target="_blank" class="mt-1 text-brand-600 hover:underline flex items-center gap-1 text-sm">
                                                        {{ $organizer->social_link }}
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    </a>
                                                </div>
                                                @endif
                                                
                                                @if($organizer->verification_status->value === 'rejected' && $organizer->rejection_reason)
                                                <div class="bg-red-50 p-3 rounded-lg border border-red-100 mt-2">
                                                    <p class="text-sm font-medium text-red-800 mb-1">Alasan Penolakan:</p>
                                                    <p class="text-sm text-red-700">{{ $organizer->rejection_reason }}</p>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-6 flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')">
                                                    Tutup
                                                </x-secondary-button>
                                            </div>
                                        </div>
                                    </x-modal>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <x-empty-state 
                                    title="Tidak ada data ditemukan" 
                                    description="Belum ada pendaftaran organisasi yang sesuai dengan pencarian atau filter Anda." 
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
            <div class="p-4 border-t border-gray-100 text-sm text-gray-500 text-center">
                Menampilkan {{ $organizers->count() }} permohonan.
            </div>
        @endif
    </x-card>

    <!-- Information Cards at bottom -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-brand-600 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Kriteria Persetujuan</h3>
            <p class="text-sm text-gray-500">Pastikan NIB / SK Dekanat organisasi aktif, penanggung jawab terdaftar memiliki KTP valid, dan ruang lingkup kegiatan relevan dengan pengabdian mahasiswa.</p>
        </x-card>

        <x-card>
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Komunikasi PIC</h3>
            <p class="text-sm text-gray-500">Hubungi nomor WhatsApp penanggung jawab jika dokumen legalitas terpotong atau membutuhkan kelengkapan surat izin rektorat/fakultas.</p>
        </x-card>

        <x-card>
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Akses Pasca Verifikasi</h3>
            <p class="text-sm text-gray-500">EO yang telah disetujui akan otomatis memperoleh wewenang membuat publikasi event dan membuka pendaftaran relawan mahasiswa se-Indonesia.</p>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function setStatus(status) {
            document.getElementById('status_input').value = status;
            document.getElementById('submit_filter').click();
        }
    </script>
    @endpush
</x-layouts.admin>
