<x-layouts.admin title="Laporan Pelanggaran">

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Laporan Pengguna</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola aduan pelanggaran dari komunitas, verifikasi bukti pendukung, dan lakukan tindakan moderasi akun.</p>
            </div>

            <!-- Header Metrics: 3 Luxury Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:w-auto">
                <!-- Menunggu Tindakan -->
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['pending'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 font-medium">Menunggu Tindakan</div>
                    </div>
                </div>

                <!-- Sedang Ditinjau -->
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['in_review'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 font-medium">Sedang Ditinjau</div>
                    </div>
                </div>

                <!-- Laporan Selesai -->
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['resolved'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 font-medium">Laporan Selesai</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div x-data="reportManager()" class="space-y-6">

        <!-- Search & Filter Card -->
        <x-card class="p-3">
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
                           placeholder="Cari ID tiket (#RPT-), nama pelapor, terlapor, atau alasan aduan..."
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

                @if(request('search') || ($statusFilter && $statusFilter !== 'all'))
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                @endif
            </form>
        </x-card>

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
        <x-card padding="p-0" class="overflow-hidden shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="py-3.5 px-6">ID Laporan</th>
                            <th class="py-3.5 px-6">Pelapor</th>
                            <th class="py-3.5 px-6">Terlapor</th>
                            <th class="py-3.5 px-6">Alasan Laporan</th>
                            <th class="py-3.5 px-4 text-center">Bukti</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($reports as $report)
                            @php
                                $statusVal = $report->status->value;
                                $statusBadgeClass = match($statusVal) {
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'in_review' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                                $statusDotClass = match($statusVal) {
                                    'pending' => 'bg-amber-500',
                                    'in_review' => 'bg-blue-500',
                                    'resolved' => 'bg-emerald-500',
                                    'rejected' => 'bg-gray-400',
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
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 text-xs tracking-tight">#{{ $report->ticket_number }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</div>
                                </td>

                                <!-- Pelapor -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900 text-xs">{{ $report->reporter->name ?? 'User' }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">{{ $report->reporter->email ?? '-' }}</div>
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">
                                        {{ ucfirst($report->reporter->role->value ?? 'volunteer') }}
                                    </span>
                                </td>

                                <!-- Terlapor -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900 text-xs">{{ $report->reported->name ?? 'User' }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">{{ $report->reported->email ?? '-' }}</div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $report->reported->role === \App\Enums\UserRole::Eo ? 'bg-indigo-50 text-brand-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $report->reported->role === \App\Enums\UserRole::Eo ? 'EO' : 'Volunteer' }}
                                        </span>
                                        @if($report->reported->status === \App\Enums\UserStatus::Suspended)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700">
                                                Ditangguhkan
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Alasan Laporan -->
                                <td class="py-4 px-6">
                                    <span class="inline-block font-bold text-gray-900 text-xs bg-slate-100 text-slate-800 px-2 py-0.5 rounded-md mb-1">
                                        {{ $report->reason }}
                                    </span>
                                    <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $report->description }}</p>
                                    @if($report->action_taken)
                                        <div class="text-[11px] text-brand-700 font-semibold mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $report->action_taken }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Bukti Pendukung -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($report->evidence_path)
                                        <a href="{{ Storage::url($report->evidence_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $statusBadgeClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDotClass }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <!-- Tindakan Admin -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="openActionModal({{ $report->id }})"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold rounded-xl transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                        </svg>
                                        Tindak Lanjuti
                                    </button>
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

            @if($reports->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $reports->links() }}
                </div>
            @endif
        </x-card>

        <!-- Modal Tindak Lanjuti / Resolusi Laporan (Alpine.js) -->
        <div x-show="actionModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="actionModalOpen = false">

            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl relative border border-gray-100"
                 @click.outside="actionModalOpen = false">
                
                <!-- Close Button -->
                <button type="button" @click="actionModalOpen = false" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Modal Header -->
                <div class="pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full"
                              x-text="'#' + (selectedReport ? selectedReport.ticket_number : '')"></span>
                        <span class="text-xs text-gray-400" x-text="selectedReport ? selectedReport.created_at : ''"></span>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mt-1" x-text="selectedReport ? selectedReport.reason : 'Tindak Lanjuti Laporan'"></h3>
                </div>

                <!-- Modal Body: Detail Aduan -->
                <div class="py-4 space-y-4 text-xs">
                    <!-- Reporter & Reported Info Box -->
                    <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                        <div>
                            <span class="text-gray-400 font-medium">Pelapor (Aduan Dari)</span>
                            <p class="font-bold text-gray-800 text-xs mt-0.5" x-text="selectedReport && selectedReport.reporter ? selectedReport.reporter.name : '-'"></p>
                            <span class="text-[10px] text-gray-500" x-text="selectedReport && selectedReport.reporter ? selectedReport.reporter.email : ''"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium">Pihak Terlapor</span>
                            <p class="font-bold text-rose-700 text-xs mt-0.5" x-text="selectedReport && selectedReport.reported ? selectedReport.reported.name : '-'"></p>
                            <span class="text-[10px] text-gray-500" x-text="selectedReport && selectedReport.reported ? selectedReport.reported.email : ''"></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <span class="text-gray-400 font-medium">Uraian / Kronologi Kejadian</span>
                        <div class="mt-1 p-3 bg-gray-50 rounded-xl border border-gray-100 text-gray-700 leading-relaxed max-h-32 overflow-y-auto"
                             x-text="selectedReport ? selectedReport.description : '-'"></div>
                    </div>

                    <!-- Action Form -->
                    <form :action="'/admin/reports/' + (selectedReport ? selectedReport.id : '') + '/action'" method="POST" @submit="isSubmitting = true">
                        @csrf

                        <div class="space-y-3 pt-2">
                            <label class="block text-xs font-bold text-gray-900">Pilih Keputusan / Tindakan Moderasi</label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2.5 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 cursor-pointer transition-colors">
                                    <input type="radio" name="action" value="in_review" x-model="selectedAction" required class="text-brand-600 focus:ring-brand-500">
                                    <span class="text-xs font-semibold text-gray-800">Tandai Ditinjau</span>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 bg-amber-50/60 hover:bg-amber-100/60 rounded-xl border border-amber-200 cursor-pointer transition-colors">
                                    <input type="radio" name="action" value="warning" x-model="selectedAction" required class="text-amber-600 focus:ring-amber-500">
                                    <span class="text-xs font-semibold text-amber-900">Beri Peringatan</span>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 bg-rose-50/60 hover:bg-rose-100/60 rounded-xl border border-rose-200 cursor-pointer transition-colors">
                                    <input type="radio" name="action" value="suspend" x-model="selectedAction" required class="text-rose-600 focus:ring-rose-500">
                                    <span class="text-xs font-semibold text-rose-900">Suspend Terlapor</span>
                                </label>

                                <label class="flex items-center gap-2 p-2.5 bg-emerald-50/60 hover:bg-emerald-100/60 rounded-xl border border-emerald-200 cursor-pointer transition-colors">
                                    <input type="radio" name="action" value="resolve" x-model="selectedAction" required class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-emerald-900">Selesaikan Laporan</span>
                                </label>
                            </div>

                            <label class="flex items-center gap-2 p-2.5 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 cursor-pointer transition-colors">
                                <input type="radio" name="action" value="reject" x-model="selectedAction" required class="text-gray-600 focus:ring-gray-500">
                                <span class="text-xs font-semibold text-gray-700">Tolak Laporan (Aduan Tidak Terbukti / Gugur)</span>
                            </label>

                            <!-- Catatan Moderasi -->
                            <div class="pt-2">
                                <label class="block text-xs font-bold text-gray-900 mb-1">Catatan Moderasi / Uraian Keputusan</label>
                                <textarea name="admin_notes" rows="3"
                                          placeholder="Tuliskan catatan tindak lanjut atau alasan penolakan..."
                                          class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors"></textarea>
                            </div>
                        </div>

                        <!-- Footer Buttons -->
                        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="actionModalOpen = false" :disabled="isSubmitting"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                                Batal
                            </button>

                            <button type="submit" :disabled="isSubmitting"
                                    class="inline-flex items-center justify-center gap-1.5 px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
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

    </div>

    @push('scripts')
    <script>
        function setReportStatus(status) {
            const input = document.getElementById('reportStatusInput');
            if (input) {
                input.value = status;
                input.form.submit();
            }
        }

        function reportManager() {
            return {
                actionModalOpen: false,
                isSubmitting: false,
                selectedReport: null,
                selectedAction: 'in_review',

                openActionModal(reportId) {
                    this.selectedReport = null;
                    this.actionModalOpen = true;

                    fetch('/admin/reports/' + reportId)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                this.selectedReport = res.data;
                            }
                        })
                        .catch(err => {
                            console.error('Failed to load report:', err);
                        });
                }
            };
        }
    </script>
    @endpush

</x-layouts.admin>
