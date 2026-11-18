<x-layouts.crew title="Riwayat Pengalaman - VOLA">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
        showReportModal: false,
        showReportSuccessModal: false,
        isSubmittingReport: false,
        reportTicketNumber: '',
        modalMessage: '',
        reportTarget: {
            eventId: null,
            reportedId: null,
            eventTitle: '',
            organizerName: ''
        },
        reportData: {
            reason: '',
            description: ''
        },
        openReportModal(eventId, reportedId, eventTitle, organizerName) {
            this.reportTarget = { eventId, reportedId, eventTitle, organizerName };
            this.reportData = { reason: '', description: '' };
            if (this.$refs.evidenceInput) {
                this.$refs.evidenceInput.value = '';
            }
            this.showReportModal = true;
        },
        submitReport() {
            if (!this.reportData.reason) {
                alert('Silakan pilih alasan pelaporan terlebih dahulu.');
                return;
            }
            if (!this.reportData.description || this.reportData.description.trim().length < 20) {
                alert('Kronologi kejadian minimal 20 karakter agar laporan jelas.');
                return;
            }

            this.isSubmittingReport = true;
            const formData = new FormData();
            if (this.reportTarget.eventId) formData.append('event_id', this.reportTarget.eventId);
            if (this.reportTarget.reportedId) formData.append('reported_id', this.reportTarget.reportedId);
            formData.append('reason', this.reportData.reason);
            formData.append('description', this.reportData.description);
            if (this.$refs.evidenceInput && this.$refs.evidenceInput.files[0]) {
                formData.append('evidence', this.$refs.evidenceInput.files[0]);
            }
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('crew.reports.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (body.success) {
                    this.showReportModal = false;
                    this.reportTicketNumber = body.ticket_number || '';
                    this.modalMessage = body.message;
                    this.showReportSuccessModal = true;
                    this.reportData = { reason: '', description: '' };
                    if (this.$refs.evidenceInput) {
                        this.$refs.evidenceInput.value = '';
                    }
                } else {
                    alert(body.message || 'Gagal mengajukan laporan.');
                }
            })
            .catch(error => {
                console.error('Report error:', error);
                alert('Terjadi kesalahan jaringan saat mengirim laporan.');
            })
            .finally(() => {
                this.isSubmittingReport = false;
            });
        }
     }">
   
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Riwayat Pengalaman</h1>
        <p class="mt-1 text-sm text-gray-500">Rekam jejak kontribusi volunteer kamu.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Total Event Diikuti</p>
                <p class="text-2xl font-bold text-gray-900">{{ $counts['all'] }} Event</p>
                <p class="text-xs text-gray-500">Seluruh kegiatan terverifikasi</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Sertifikat Terkumpul</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalCertificates }} Sertifikat</p>
                <p class="text-xs text-gray-500">Siap diunduh kapan saja</p>
            </div>
        </div>
    </div>

    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-6 overflow-x-auto pb-1">
            @php
                $tabs = [
                    'all' => 'Semua',
                    'festival' => 'Festival',
                    'seminar' => 'Seminar',
                    'sosial' => 'Sosial',
                    'kampus' => 'Kampus',
                    'komunitas' => 'Komunitas',
                    'olahraga' => 'Olahraga',
                    'musik' => 'Musik',
                ];
            @endphp

            @foreach($tabs as $key => $label)
                @php
                    $isActive = $activeCategory === $key;
                    $count = $counts[$key] ?? 0;
                    $textClass = $isActive ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700';
                    $numberDisplay = $count; 
                @endphp
                
                <a href="{{ route('history.index', ['category' => $key]) }}" 
                   class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-1.5 {{ $textClass }}">
                    {{ $label }} 
                    <span class="{{ $isActive ? 'text-[#4F46E5]' : 'text-gray-400' }}">{{ $numberDisplay }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="space-y-4">
        @forelse($paginatedHistories as $history)
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <h3 class="text-base font-bold text-gray-900">{{ $history['event_title'] }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                            {{ $history['status_label'] }}
                        </span>
                    </div>
                    <p class="text-sm text-[#4F46E5] font-medium mb-3">{{ $history['organizer'] }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Posisi :</p>
                                <p class="text-xs text-gray-700">{{ $history['position'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Waktu :</p>
                                <p class="text-xs text-gray-700">{{ $history['date'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Lokasi :</p>
                                <p class="text-xs text-gray-700">{{ $history['location'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <button type="button" 
                            @click="openReportModal({{ $history['event_id'] ?? 'null' }}, {{ $history['organizer_user_id'] ?? 'null' }}, '{{ addslashes($history['event_title']) }}', '{{ addslashes($history['organizer']) }}')" 
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Laporkan EO
                    </button>

                    @if($history['status'] === 'accepted')
                    <a href="{{ route('history.downloadCertificate', ['id' => $history['id']]) }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border-2 border-[#4F46E5] text-[#4F46E5] text-sm font-bold rounded-xl hover:bg-[#4F46E5] hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Sertifikat (PDF)
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Event</h3>
            <p class="text-sm text-gray-500">Belum ada riwayat event untuk kategori ini.</p>
        </div>
        @endforelse
    </div>

    @if($totalPages > 1)
    <div class="mt-6 flex items-center justify-end">
        <div class="flex items-center gap-2">
            
            @if($currentPage > 1)
                <a href="{{ route('history.index', array_merge(['category' => $activeCategory], ['page' => $currentPage - 1])) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-300 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Sebelumnya
                </a>
            @endif

            @for($i = 1; $i <= $totalPages; $i++)
                @if($i === $currentPage)                  
                    <span class="inline-flex items-center justify-center min-w-[2.5rem] h-10 px-3 text-sm font-bold text-white bg-[#4F46E5] rounded-lg shadow-md ring-2 ring-[#4F46E5] ring-offset-1">
                        {{ $i }}
                    </span>
                @else                  
                    <a href="{{ route('history.index', array_merge(['category' => $activeCategory], ['page' => $i])) }}" 
                       class="inline-flex items-center justify-center min-w-[2.5rem] h-10 px-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-indigo-50 hover:text-[#4F46E5] hover:border-indigo-300 transition shadow-sm">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            @if($currentPage < $totalPages)
                <a href="{{ route('history.index', array_merge(['category' => $activeCategory], ['page' => $currentPage + 1])) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-300 transition shadow-sm">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Modal Laporkan EO / Event -->
    <div x-show="showReportModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.outside="if(!isSubmittingReport) showReportModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 relative max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900 leading-tight">Laporkan EO / Event</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Laporan akan diteruskan ke tim Admin VOLA secara rahasia.</p>
                    </div>
                </div>
                <button type="button" 
                        @click="if(!isSubmittingReport) showReportModal = false"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-3.5 mb-5 space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 font-medium">Event :</span>
                    <span class="font-bold text-gray-900 text-right truncate max-w-[240px]" x-text="reportTarget.eventTitle"></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 font-medium">Penyelenggara :</span>
                    <span class="font-bold text-[#4F46E5] text-right" x-text="reportTarget.organizerName"></span>
                </div>
            </div>

            <form @submit.prevent="submitReport" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Alasan Pelaporan <span class="text-rose-500">*</span>
                    </label>
                    <select x-model="reportData.reason" 
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                        <option value="" disabled>-- Pilih Alasan Pelaporan --</option>
                        <option value="Penipuan / Ketidaksesuaian Informasi Event">Penipuan / Ketidaksesuaian Informasi Event</option>
                        <option value="Perlakuan Tidak Layak / Diskriminasi">Perlakuan Tidak Layak / Diskriminasi</option>
                        <option value="Pelanggaran Keselamatan & Kesehatan Relawan">Pelanggaran Keselamatan & Kesehatan Relawan</option>
                        <option value="Pembatalan Sepihak Tanpa Kejelasan">Pembatalan Sepihak Tanpa Kejelasan</option>
                        <option value="Eksploitasi Jam Kerja / Beban Kerja Tak Wajar">Eksploitasi Jam Kerja / Beban Kerja Tak Wajar</option>
                        <option value="Penyalahgunaan Data / Privasi">Penyalahgunaan Data / Privasi</option>
                        <option value="Lainnya">Lainnya (Pelanggaran Lain)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Kronologi & Penjelasan Detail <span class="text-rose-500">*</span>
                    </label>
                    <textarea x-model="reportData.description"
                              rows="4"
                              required
                              class="w-full px-3.5 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none transition"
                              placeholder="Jelaskan secara rinci apa yang terjadi, waktu kejadian, dan fakta pendukung lainnya..."></textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Minimal 20 karakter agar mudah diverifikasi admin.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Bukti Pendukung <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="file" 
                           x-ref="evidenceInput"
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-200 rounded-xl p-1 bg-white">
                    <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, atau PDF (maks. 5MB).</p>
                </div>

                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-start gap-2 text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pastikan informasi yang Anda berikan jujur dan dapat dipertanggungjawabkan demi keamanan bersama.</span>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" 
                            @click="showReportModal = false"
                            :disabled="isSubmittingReport"
                            class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="isSubmittingReport"
                            class="flex-1 px-4 py-2.5 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 transition text-sm shadow-md shadow-rose-200 flex items-center justify-center gap-2 disabled:opacity-50">
                        <svg x-show="isSubmittingReport" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmittingReport ? 'Mengirim Laporan...' : 'Kirim Laporan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showReportSuccessModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.outside="showReportSuccessModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 relative text-center">
            
            <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 ring-8 ring-rose-50/50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-black text-gray-900 mb-1">Laporan Berhasil Diajukan</h3>
            <p class="text-xs font-semibold text-rose-600 mb-3" x-show="reportTicketNumber" x-text="'Nomor Tiket: #' + reportTicketNumber"></p>
            <p class="text-sm text-gray-600 leading-relaxed mb-6" x-text="modalMessage"></p>
            
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs text-gray-500 mb-6 text-left space-y-1.5">
                <p class="font-bold text-gray-800">📌 Apa langkah selanjutnya?</p>
                <p>• Laporan masuk antrean Admin dengan status <strong>Pending</strong>.</p>
                <p>• Tim Admin akan memvalidasi bukti dan memeriksa pihak terlapor.</p>
                <p>• Sanksi berupa peringatan hingga suspend akun akan dijatuhkan jika terbukti melanggar.</p>
            </div>

            <button type="button" 
                    @click="showReportSuccessModal = false"
                    class="w-full py-3 bg-[#4F46E5] text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 text-sm">
                Selesai & Tutup
            </button>
        </div>
    </div>
</x-layouts.crew>