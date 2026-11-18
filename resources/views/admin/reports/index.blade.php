<x-layouts.admin title="Laporan Pengguna">

    <!-- Header Section -->
    <div class="mb-6 relative z-10 opacity-100 w-full max-w-full min-w-0">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 w-full">
            <div class="max-w-lg">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border mb-2.5 bg-rose-50 text-rose-700 border-rose-200/70">
                    <span class="relative flex h-2 w-2 mr-0.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                    </span>
                    <span>PUSAT ADUAN • MODERASI KOMUNITAS</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight" aria-label="Laporan Pengguna">Laporan <span class="text-amber-500">Pengguna</span></h1>
                <p class="text-slate-600 text-sm mt-1 leading-relaxed">Kelola aduan pelanggaran dari komunitas, verifikasi bukti pendukung, dan lakukan tindakan moderasi akun.</p>
            </div>

            <!-- Header Metrics: 3 Luxury Cards (1 Baris Sejajar) -->
            <div id="admin-metrics-container" class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full xl:w-auto shrink-0">
                <!-- Menunggu Tindakan -->
                <x-metric-card label="Menunggu Tindakan" :value="number_format($stats['pending'], 0, ',', '.')" bg="bg-amber-100" text="text-amber-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </x-metric-card>

                <!-- Sedang Ditinjau -->
                <x-metric-card label="Sedang Ditinjau" :value="number_format($stats['in_review'], 0, ',', '.')" bg="bg-blue-100" text="text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </x-metric-card>

                <!-- Laporan Selesai -->
                <x-metric-card label="Laporan Selesai" :value="number_format($stats['resolved'], 0, ',', '.')" bg="bg-emerald-100" text="text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </x-metric-card>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div id="admin-content-container" x-data="reportManager()" class="w-full max-w-full min-w-0 space-y-6">

        <!-- Search & Filter Card -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-3 shadow-sm">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nomor tiket #RPT, nama pelapor, terlapor, atau alasan laporan"
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                <!-- Filter Status Pills: Terpisah & Bersih (Clean Gap Pills) -->
                <div class="flex items-center gap-2 w-full lg:w-auto overflow-x-auto pb-1 lg:pb-0">
                    <input type="hidden" name="status" id="reportStatusInput" value="{{ $statusFilter }}">

                    <button type="button"
                            onclick="setReportStatus('all')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $statusFilter === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Semua Status
                    </button>

                    <button type="button"
                            onclick="setReportStatus('pending')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $statusFilter === 'pending' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Menunggu
                    </button>

                    <button type="button"
                            onclick="setReportStatus('in_review')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $statusFilter === 'in_review' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Ditinjau
                    </button>

                    <button type="button"
                            onclick="setReportStatus('resolved')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $statusFilter === 'resolved' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Selesai
                    </button>

                    <button type="button"
                            onclick="setReportStatus('rejected')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $statusFilter === 'rejected' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Ditolak
                    </button>
                </div>

                @php
                    $hasActiveFilter = (request('search') && trim(request('search')) !== '')
                        || (request('status') && request('status') !== 'all');
                @endphp

                @if($hasActiveFilter)
                    <a href="{{ route('admin.reports.index') }}" 
                       data-reset="true"
                       x-data="{ resetting: false }"
                       @click="resetting = true"
                       class="group inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800 active:scale-95 transition-all shrink-0">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:-rotate-45 shrink-0"
                             :class="{ 'animate-spin text-brand-600': resetting }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
            </div>
        @endif

        <!-- Data Table -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden w-full max-w-full">
            <div class="overflow-x-auto w-full">
                <table class="table-fixed w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                            <th class="py-3.5 px-3 w-[15%]">ID Laporan</th>
                            <th class="py-3.5 px-3 w-[16%]">Pelapor</th>
                            <th class="py-3.5 px-3 w-[16%]">Terlapor</th>
                            <th class="py-3.5 px-2.5 w-[17%]">Alasan Laporan</th>
                            <th class="py-3.5 px-2 w-[11%] text-center">Bukti</th>
                            <th class="py-3.5 px-2 w-[12%] text-center">Status</th>
                            <th class="py-3.5 px-2.5 w-[13%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($reports as $report)
                            @php
                                $statusVal = $report->status->value;
                                $statusBadgeClass = match($statusVal) {
                                    'pending' => 'bg-amber-50 text-amber-800 border-amber-200/70',
                                    'in_review' => 'bg-sky-50 text-sky-700 border-sky-200/70',
                                    'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200/70',
                                };
                                $statusDotClass = match($statusVal) {
                                    'pending' => 'bg-amber-600',
                                    'in_review' => 'bg-sky-600',
                                    'resolved' => 'bg-emerald-600',
                                    'rejected' => 'bg-rose-600',
                                };
                                $statusLabel = match($statusVal) {
                                    'pending' => 'Menunggu',
                                    'in_review' => 'Ditinjau',
                                    'resolved' => 'Selesai',
                                    'rejected' => 'Ditolak',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- ID Laporan & Waktu -->
                                <td class="py-3.5 px-3 min-w-0">
                                    <div class="font-bold text-gray-900 text-xs tracking-tight truncate">#{{ $report->ticket_number }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5 whitespace-nowrap">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</div>
                                </td>

                                <!-- Pelapor -->
                                <td class="py-3.5 px-3 min-w-0">
                                    <div class="font-bold text-gray-900 text-xs truncate">{{ $report->reporter->name ?? 'User' }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 truncate" title="{{ $report->reporter->email ?? '-' }}">{{ $report->reporter->email ?? '-' }}</div>
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">
                                        {{ $report->reporter?->role === \App\Enums\UserRole::Eo ? 'EO' : 'Crew' }}
                                    </span>
                                </td>

                                <!-- Terlapor -->
                                <td class="py-3.5 px-3 min-w-0">
                                    <div class="font-bold text-gray-900 text-xs truncate">{{ $report->reported->name ?? 'User' }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 truncate" title="{{ $report->reported->email ?? '-' }}">{{ $report->reported->email ?? '-' }}</div>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $report->reported->role === \App\Enums\UserRole::Eo ? 'bg-indigo-50 text-brand-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $report->reported->role === \App\Enums\UserRole::Eo ? 'EO' : 'Crew' }}
                                        </span>
                                        @if($report->reported->status === \App\Enums\UserStatus::Suspended)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700">
                                                Ditangguhkan
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Alasan Laporan -->
                                <td class="py-3.5 px-2.5 min-w-0">
                                    <span class="inline-block font-bold text-gray-900 text-[11px] bg-slate-100 text-slate-800 px-2 py-0.5 rounded-md mb-1 max-w-full truncate" title="{{ $report->reason }}">
                                        {{ $report->reason }}
                                    </span>
                                    <p class="text-[11px] text-gray-600 line-clamp-2 leading-snug break-words" title="{{ $report->description }}">{{ $report->description }}</p>
                                    @if($report->action_taken)
                                        <div class="text-[10px] text-brand-700 font-semibold mt-1 flex items-center gap-1 truncate">
                                            <svg class="w-3 h-3 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="truncate">{{ $report->action_taken }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Bukti Pendukung -->
                                <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                    @if($report->evidence_url)
                                        @php
                                            $isImgEvidence = $report->evidence_is_image;
                                        @endphp
                                        <button type="button"
                                                onclick="window.openAdminLightbox('{{ $report->evidence_url }}', 'Bukti Laporan #{{ $report->ticket_number }}', 'Alasan: {{ addslashes($report->reason) }}', '{{ $isImgEvidence ? 'image' : 'pdf' }}')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span>Lihat Bukti</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusBadgeClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDotClass }} shrink-0"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <!-- Tindakan Admin -->
                                <td class="py-3.5 px-2.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <button type="button"
                                                @click="openActionModal({{ $report->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold rounded-xl transition-colors">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                            </svg>
                                            Tindak Lanjuti
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <p class="font-semibold text-gray-700">Tidak ada laporan aduan yang ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Semua antrean moderasi telah ditangani atau belum ada aduan baru dari pengguna.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-100">
                {{ $reports->withQueryString()->links('components.pagination') }}
            </div>
        </div>

        <!-- Modal Tindak Lanjuti / Resolusi Laporan (Alpine.js Landscape 2-Column - Compact Anti-Scroll) -->
        <template x-teleport="body">
            <div x-show="actionModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] overflow-y-auto overscroll-y-contain"
                 style="display: none;"
                 @keydown.escape.window="actionModalOpen = false">

                <!-- Fullscreen Backdrop Dimmer: WAJIB menutupi 100% seluruh layar termasuk Top Navbar dan Sidebar kiri -->
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity z-[9999]"
                     @click="actionModalOpen = false"></div>

                <!-- Centering Wrapper: Full viewport overlay dengan safe padding (p-6 sm:p-8 lg:pl-64) -->
                <div class="fixed inset-0 z-[9999] lg:pl-64 flex items-center justify-center p-6 sm:p-8"
                     @click.self="actionModalOpen = false">
                    
                    <!-- Modal Dialog Card (Landscape 2-Column Compact Proporsional) -->
                    <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl relative border border-gray-100 flex flex-col max-h-[85vh] max-h-[85dvh] overflow-hidden my-auto"
                         @click.stop
                         @click.outside="actionModalOpen = false">
                        
                        <form :action="'/admin/reports/' + (selectedReport ? selectedReport.id : '') + '/action'"
                              method="POST"
                              @submit="isSubmitting = true"
                              class="flex flex-col flex-1 min-h-0 overflow-hidden">
                            @csrf

                            <!-- Modal Header: px-5 py-3.5 -->
                            <div class="px-5 py-3.5 border-b border-gray-100 bg-white shrink-0 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <template x-if="loadingReport">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full">Tindak Lanjuti Laporan</span>
                                            <h3 class="text-sm sm:text-base font-extrabold text-gray-900 truncate">Memuat Berkas Aduan...</h3>
                                        </div>
                                    </template>

                                    <template x-if="!loadingReport && selectedReport">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full"
                                                      x-text="'#' + selectedReport.ticket_number"></span>
                                                <span class="text-[11px] text-gray-400" x-text="selectedReport.created_at"></span>
                                            </div>
                                            <h3 class="text-sm sm:text-base font-extrabold text-gray-900 mt-0.5 truncate" x-text="selectedReport.reason"></h3>
                                        </div>
                                    </template>

                                    <template x-if="!loadingReport && reportError">
                                        <div>
                                            <h3 class="text-sm sm:text-base font-extrabold text-rose-600">Gagal Memuat Laporan</h3>
                                            <p class="text-[11px] text-gray-400 mt-0.5">Silakan gunakan tombol coba lagi di bawah</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Modal Body: p-5 with gap-3 or space-y-3 (Compact fit in 768px viewport) -->
                            <div class="p-5 overflow-y-auto overscroll-y-contain flex-1 min-h-0 text-xs">
                                <!-- Circular Spinner Loading State (Reference B) -->
                                <template x-if="loadingReport">
                                    <div class="py-12 text-center text-gray-400 my-auto">
                                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2.5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <p class="text-xs font-medium text-gray-600">Memuat berkas aduan & detail para pihak...</p>
                                    </div>
                                </template>

                                <!-- Error State with Retry Button -->
                                <template x-if="!loadingReport && reportError">
                                    <div class="py-10 px-6 text-center space-y-3.5 my-auto">
                                        <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900">Gagal Mengambil Detail Laporan</h4>
                                            <p class="text-xs text-gray-500 mt-0.5 max-w-sm mx-auto" x-text="reportError"></p>
                                        </div>
                                        <div>
                                            <button type="button" @click="openActionModal(currentReportId)"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Coba Lagi
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Loaded Content -->
                                <template x-if="!loadingReport && !reportError && selectedReport">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                                    
                                    <!-- KOLOM KIRI: Informasi Pelapor, Terlapor, & Uraian Kejadian (lg:col-span-5) -->
                                    <div class="lg:col-span-5 space-y-2.5">
                                        <!-- Identitas Pelapor & Terlapor Card -->
                                        <div class="bg-gray-50/80 rounded-xl p-2.5 border border-gray-100 space-y-2">
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                Identitas Pihak Terkait
                                            </div>
                                            
                                            <div class="space-y-1.5">
                                                <!-- Pelapor -->
                                                <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-2xs">
                                                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pelapor (Aduan Dari)</span>
                                                    <p class="font-bold text-gray-900 text-xs mt-0.5 truncate" x-text="selectedReport && selectedReport.reporter ? selectedReport.reporter.name : '-'"></p>
                                                    <p class="text-[11px] text-gray-500 truncate" x-text="selectedReport && selectedReport.reporter ? selectedReport.reporter.email : ''"></p>
                                                    <div class="mt-0.5">
                                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 uppercase"
                                                              x-text="selectedReport && selectedReport.reporter ? (selectedReport.reporter.role === 'eo' ? 'Event Organizer' : 'Crew') : '-'"></span>
                                                    </div>
                                                </div>

                                                <!-- Terlapor -->
                                                <div class="p-2 bg-white rounded-lg border border-rose-100 shadow-2xs">
                                                    <span class="text-[10px] font-semibold text-rose-500 uppercase tracking-wider">Pihak Terlapor</span>
                                                    <p class="font-bold text-rose-700 text-xs mt-0.5 truncate" x-text="selectedReport && selectedReport.reported ? selectedReport.reported.name : '-'"></p>
                                                    <p class="text-[11px] text-gray-500 truncate" x-text="selectedReport && selectedReport.reported ? selectedReport.reported.email : ''"></p>
                                                    <div class="mt-0.5 flex items-center gap-1.5">
                                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 uppercase"
                                                              x-text="selectedReport && selectedReport.reported ? (selectedReport.reported.role === 'eo' ? 'Event Organizer' : 'Crew') : '-'"></span>
                                                        <template x-if="selectedReport && selectedReport.reported && selectedReport.reported.status === 'suspended'">
                                                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                                                Ditangguhkan
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Uraian / Kronologi Kejadian -->
                                        <div class="bg-gray-50/80 rounded-xl p-2.5 border border-gray-100 space-y-1.5">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Uraian / Kronologi Kejadian
                                            </span>
                                            <div class="p-2.5 bg-white rounded-lg border border-gray-100 text-gray-700 leading-relaxed text-xs max-h-24 overflow-y-auto overscroll-y-contain shadow-2xs whitespace-pre-line"
                                                 x-text="selectedReport ? selectedReport.description : '-'"></div>
                                        </div>

                                        <!-- Bukti Pendukung (jika ada) -->
                                        <template x-if="selectedReport && selectedReport.evidence_url">
                                            <div class="p-2 bg-indigo-50/60 rounded-xl border border-indigo-100 flex items-center justify-between">
                                                <div class="flex items-center gap-1.5 text-brand-900 text-xs">
                                                    <svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    <span class="font-semibold text-[11px]">Bukti Terlampir</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button"
                                                            x-on:click="window.openAdminLightbox(selectedReport.evidence_url, 'Bukti Laporan #' + selectedReport.ticket_number, 'Alasan: ' + selectedReport.reason, selectedReport.evidence_is_image ? 'image' : 'pdf')"
                                                            class="px-2.5 py-1 bg-white hover:bg-brand-50 text-brand-700 font-bold text-[11px] rounded-lg border border-brand-200 transition-colors shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                                        <svg class="w-3 h-3 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Lihat di Lightbox</span>
                                                    </button>
                                                    <a :href="selectedReport.evidence_url" target="_blank" rel="noopener noreferrer"
                                                       class="px-2 py-1 bg-white hover:bg-gray-50 text-gray-600 font-medium text-[11px] rounded-lg border border-gray-200 transition-colors shadow-2xs inline-flex items-center gap-1">
                                                        <span>Tab Baru</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- KOLOM KANAN: Pilihan Keputusan & Catatan Moderasi (lg:col-span-7) -->
                                    <div class="lg:col-span-7 space-y-2.5">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-900 mb-1.5">Pilih Keputusan / Tindakan Moderasi</label>
                                            
                                            <!-- Grid Keputusan Compact 1 Baris Teks -->
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                <label class="flex items-center gap-2 p-2 rounded-xl border cursor-pointer transition-all"
                                                       :class="selectedAction === 'in_review' ? 'bg-blue-50/80 border-blue-400 ring-1 ring-blue-300' : 'bg-gray-50 hover:bg-gray-100/80 border-gray-200'">
                                                    <input type="radio" name="action" value="in_review" x-model="selectedAction" required class="text-brand-600 focus:ring-brand-500">
                                                    <span class="text-xs font-bold text-gray-900 truncate">Investigasi / Ditinjau</span>
                                                </label>

                                                <label class="flex items-center gap-2 p-2 rounded-xl border cursor-pointer transition-all"
                                                       :class="selectedAction === 'warning' ? 'bg-amber-50/80 border-amber-400 ring-1 ring-amber-300' : 'bg-gray-50 hover:bg-gray-100/80 border-gray-200'">
                                                    <input type="radio" name="action" value="warning" x-model="selectedAction" required class="text-amber-600 focus:ring-amber-500">
                                                    <span class="text-xs font-bold text-amber-950 truncate">Beri Peringatan</span>
                                                </label>

                                                <label class="flex items-center gap-2 p-2 rounded-xl border cursor-pointer transition-all"
                                                       :class="selectedAction === 'suspend' ? 'bg-rose-50/80 border-rose-400 ring-1 ring-rose-300' : 'bg-gray-50 hover:bg-gray-100/80 border-gray-200'">
                                                    <input type="radio" name="action" value="suspend" x-model="selectedAction" required class="text-rose-600 focus:ring-rose-500">
                                                    <span class="text-xs font-bold text-rose-950 truncate">Suspend Akun Terlapor</span>
                                                </label>

                                                <label class="flex items-center gap-2 p-2 rounded-xl border cursor-pointer transition-all"
                                                       :class="selectedAction === 'resolve' ? 'bg-emerald-50/80 border-emerald-400 ring-1 ring-emerald-300' : 'bg-gray-50 hover:bg-gray-100/80 border-gray-200'">
                                                    <input type="radio" name="action" value="resolve" x-model="selectedAction" required class="text-emerald-600 focus:ring-emerald-500">
                                                    <span class="text-xs font-bold text-emerald-950 truncate">Selesaikan Laporan</span>
                                                </label>
                                            </div>

                                            <label class="flex items-center gap-2 p-2 mt-1.5 rounded-xl border cursor-pointer transition-all"
                                                   :class="selectedAction === 'reject' ? 'bg-slate-100 border-slate-400 ring-1 ring-slate-300' : 'bg-gray-50 hover:bg-gray-100/80 border-gray-200'">
                                                <input type="radio" name="action" value="reject" x-model="selectedAction" required class="text-gray-600 focus:ring-gray-500">
                                                <span class="text-xs font-bold text-gray-800 truncate">Tolak Laporan (Aduan Tidak Terbukti / Gugur)</span>
                                            </label>
                                        </div>

                                        <!-- Dampak jika memilih Suspend (Compact Box) -->
                                        <div x-show="selectedAction === 'suspend'" x-cloak class="p-2.5 bg-rose-50/90 border border-rose-200/80 rounded-xl text-left text-xs text-rose-900 space-y-1 leading-snug shadow-2xs">
                                            <p class="font-bold flex items-center gap-1.5 text-rose-950 text-[11px]">
                                                <svg class="w-3.5 h-3.5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Peringatan Dampak Penangguhan Akun Terlapor:
                                            </p>
                                            <div class="space-y-0.5 text-[11px] text-rose-800 pl-4 list-disc">
                                                <p>&bull; <strong>Auto-logout</strong> dan pemblokiran sesi seketika.</p>
                                                <p>&bull; <strong>Crew:</strong> Pencabutan akses ke riwayat keikutsertaan & sertifikat.</p>
                                                <p>&bull; <strong>EO:</strong> Pembekuan event operasional dan penguncian NIB.</p>
                                            </div>
                                        </div>

                                        <!-- Catatan Moderasi: rows="2" -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-900 mb-1">Catatan Moderasi / Uraian Keputusan</label>
                                            <textarea name="admin_notes" rows="2"
                                                      placeholder="Tuliskan catatan tindak lanjut, instruksi klarifikasi, atau alasan penolakan..."
                                                      class="w-full p-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors leading-relaxed"></textarea>
                                        </div>
                                    </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Modal Footer: px-5 py-3 -->
                            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/75 shrink-0 flex items-center justify-end gap-2.5 rounded-b-2xl">
                                <button type="button" @click="actionModalOpen = false" :disabled="isSubmitting"
                                        class="px-4 py-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors shadow-2xs">
                                    Batal
                                </button>

                                <button type="submit" :disabled="isSubmitting || loadingReport || reportError !== null"
                                        class="inline-flex items-center justify-center gap-1.5 px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Terapkan Tindakan'"></span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>

    @push('scripts')
    <script>
        function setReportStatus(status) {
            const input = document.getElementById('reportStatusInput');
            if (input && input.form) {
                input.value = status;
                window.adminSubmitForm(input.form);
            }
        }

        function reportManager() {
            return {
                actionModalOpen: false,
                isSubmitting: false,
                loadingReport: false,
                reportError: null,
                currentReportId: null,
                selectedReport: null,
                selectedAction: 'in_review',

                openActionModal(reportId) {
                    this.currentReportId = reportId;
                    this.loadingReport = true;
                    this.reportError = null;
                    this.selectedReport = null;
                    this.actionModalOpen = true;

                    fetch('/admin/reports/' + reportId, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Status HTTP ' + res.status);
                        return res.json();
                    })
                    .then(res => {
                        if (res.success && res.data) {
                            this.selectedReport = res.data;
                            this.selectedAction = 'in_review';
                        } else {
                            throw new Error(res.message || 'Data laporan tidak ditemukan.');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to load report:', err);
                        this.reportError = 'Gagal memuat detail laporan. Periksa koneksi Anda dan coba lagi.';
                    })
                    .finally(() => {
                        this.loadingReport = false;
                    });
                }
            };
        }
    </script>
    @endpush

</x-layouts.admin>