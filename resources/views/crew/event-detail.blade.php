<x-layouts.crew title="{{ $eventData['title'] }} - VOLA">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  
    @php $fromDashboard = request('from') === 'dashboard'; @endphp
    <div class="mb-4">
        <nav class="flex items-center text-sm text-gray-500">
            @if($fromDashboard)
                <a href="{{ route('dashboard') }}" class="hover:text-[#4F46E5] flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            @else
                <a href="{{ route('search.index') }}" class="hover:text-[#4F46E5] flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Cari Event
                </a>
            @endif
        </nav>
    </div>

    <div class="relative rounded-2xl overflow-hidden mb-6 h-72">
        <img src="{{ $eventData['image'] }}" alt="{{ $eventData['title'] }}"
            class="w-full h-full object-cover"
            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?w=800';">
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-black/30"></div>
        <div class="absolute top-4 left-4 right-4 flex items-start justify-between">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">
                <span class="w-2 h-2 bg-white rounded-full"></span>
                {{ $eventData['status'] }}
            </span>
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-6">
            <p class="text-xs text-indigo-200 font-medium mb-2">Penyelenggara : {{ $eventData['organizer'] }}</p>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight max-w-3xl">{{ $eventData['title'] }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Tanggal Pelaksanaan</span>
            </div>
            <p class="text-sm font-bold text-gray-900">{{ $eventData['date'] }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Waktu Shift</span>
            </div>
            <p class="text-sm font-bold text-gray-900">{{ $eventData['shift_time'] }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Lokasi</span>
            </div>
            <p class="text-sm font-bold text-gray-900">{{ $eventData['location'] }}</p>
        </div>
        <div class="bg-white border border-red-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-[10px] font-bold text-red-500 uppercase tracking-wide">Batas Pendaftaran</span>
            </div>
            <p class="text-sm font-bold text-gray-900">{{ $eventData['deadline'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
         x-data="{ 
            selectedPositionId: null, 
            activeTab: 'positions', 
            showApplicationForm: false,
            isSubmitting: false,
            showSuccessModal: false,
            showErrorModal: false,
            modalMessage: '',
            positions: {{ Js::from($positions) }},
             applicationData: {
                 motivation: '',
                 experience: '',
                 availability: '',
                 agree: false
             },
             get selectedPosition() {
                 if (this.selectedPositionId === null) return null;
                 return this.positions.find(p => p.id === this.selectedPositionId) || null;
             },
             selectPosition(positionId) {
                 this.selectedPositionId = positionId;
                 this.showApplicationForm = false;
             },
             openApplicationForm() {
                 if (this.selectedPositionId === null) {
                     alert('Silakan pilih posisi terlebih dahulu!');
                     return;
                 }
                 this.showApplicationForm = true;
                 this.$nextTick(() => {
                     document.getElementById('application-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
                 });
             },
             submitApplication() {
                if (!this.applicationData.agree) {
                    this.modalMessage = 'Anda harus menyetujui syarat dan ketentuan!';
                    this.showErrorModal = true;
                    return;
                }
                
                this.isSubmitting = true;
                
                const formData = new FormData();
                formData.append('position_id', this.selectedPositionId);
                formData.append('motivation', this.applicationData.motivation);
                formData.append('experience', this.applicationData.experience);
                formData.append('availability', this.applicationData.availability);
                formData.append('agree', this.applicationData.agree ? '1' : '0');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("applications.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.modalMessage = data.message;
                        this.showSuccessModal = true;
                        
                        this.showApplicationForm = false;
                        this.selectedPositionId = null;
                        this.applicationData = { motivation: '', experience: '', availability: '', agree: false };
                    } else {
                        this.modalMessage = data.message || 'Terjadi kesalahan saat mengajukan lamaran.';
                        this.showErrorModal = true;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.modalMessage = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                    this.showErrorModal = true;
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
            }
         }">

        <div class="lg:col-span-2">
           
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex gap-6 overflow-x-auto">
                    <button @click="activeTab = 'positions'; showApplicationForm = false" 
                            :class="activeTab === 'positions' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Posisi Dibuka ({{ $eventData['total_positions'] }})
                    </button>
                    <button @click="activeTab = 'about'; showApplicationForm = false" 
                            :class="activeTab === 'about' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                        Tentang Event
                    </button>
                    <button @click="activeTab = 'benefits'; showApplicationForm = false" 
                            :class="activeTab === 'benefits' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                        Benefit & Fasilitas
                    </button>
                    <button @click="activeTab = 'requirements'; showApplicationForm = false" 
                            :class="activeTab === 'requirements' ? 'border-[#4F46E5] text-[#4F46E5]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-3 px-1 border-b-2 font-bold text-sm whitespace-nowrap transition">
                        Syarat & Ketentuan
                    </button>
                </nav>
            </div>

            <div x-show="activeTab === 'positions' && !showApplicationForm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Posisi Volunteer yang Tersedia</h2>
                    <p class="text-sm text-gray-600">Pilih salah satu posisi yang sesuai dengan keahlian dan minat Anda.</p>
                </div>

                <div class="space-y-4">
                    @foreach($positions as $position)
                    <div class="bg-white rounded-2xl p-5 transition cursor-pointer relative"
                         :class="selectedPositionId === {{ $position['id'] }} ? 'border-2 border-[#4F46E5] shadow-lg' : 'border-2 border-gray-100 hover:border-gray-200'"
                         @click="selectPosition({{ $position['id'] }})">
                        
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2 flex-wrap flex-1">
                                @if(!empty($position['badge']))
                                    <span class="px-3 py-1 bg-[#4F46E5] text-white text-xs font-bold rounded-full">{{ $position['badge'] }}</span>
                                @endif
                                <span class="px-3 py-1 bg-orange-50 text-orange-600 text-xs font-bold rounded-full border border-orange-100">
                                    Sisa {{ $position['sisa_kursi'] }} Kursi
                                </span>
                                <h3 class="text-base font-bold text-gray-900">{{ $position['title'] }}</h3>
                            </div>
                            
                            <button @click.stop="selectPosition({{ $position['id'] }})" 
                                    class="ml-4 px-4 py-2 text-sm font-bold rounded-xl transition whitespace-nowrap flex items-center gap-1"
                                    :class="selectedPositionId === {{ $position['id'] }} ? 'bg-[#4F46E5] text-white hover:bg-indigo-700' : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100'">
                                Pilih Posisi
                                <svg x-show="selectedPositionId === {{ $position['id'] }}" 
                                     class="w-4 h-4" 
                                     fill="none" 
                                     stroke="currentColor" 
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 mb-3">{{ $position['description'] }}</p>
                        
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Kuota : {{ $position['quota'] }} Orang
                            </span>
                            @if(!empty($position['date']))
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $position['date'] }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mt-6">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Alur Rekrutmen Volunteer</h3>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($recruitmentFlow as $flow)
                        <div class="text-center">
                            <div class="w-10 h-10 mx-auto rounded-full {{ $flow['step'] === 5 ? 'bg-emerald-500' : 'bg-[#4F46E5]' }} text-white flex items-center justify-center font-bold text-sm mb-2">
                                {{ $flow['step'] }}
                            </div>
                            <p class="text-xs font-bold text-gray-900 mb-0.5">{{ $flow['title'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $flow['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'positions' && showApplicationForm" id="application-form" style="display: none;">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white mb-6">
                    <h3 class="text-xl font-bold mb-2">Ajukan Lamaran Volunteer</h3>
                    <p class="text-indigo-100 text-sm">Lengkapi formulir di bawah ini untuk mengajukan lamaran sebagai volunteer.</p>
                </div>

                <template x-if="selectedPosition !== null">
                    <div class="bg-white border-2 border-[#4F46E5] rounded-2xl p-5 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Posisi yang dipilih :</p>
                                <h4 class="text-lg font-bold text-[#4F46E5]" x-text="selectedPosition.title"></h4>
                                <p class="text-sm text-gray-600 mt-1" x-text="selectedPosition.description"></p>
                            </div>
                            <button @click="showApplicationForm = false; selectedPositionId = null" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>

                <form @submit.prevent="submitApplication" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-5">
                   
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Motivasi Bergabung <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="applicationData.motivation" 
                                  required
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent resize-none"
                                  placeholder="Ceritakan mengapa Anda ingin menjadi volunteer di event ini..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimal 100 karakter</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Pengalaman Relevan <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <textarea x-model="applicationData.experience" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent resize-none"
                                  placeholder="Pengalaman volunteer atau kerja tim sebelumnya (jika ada)..."></textarea>
                    </div>

                    <div class="flex items-start gap-3">
                        <input type="checkbox" x-model="applicationData.agree" required class="mt-1 w-4 h-4 text-[#4F46E5] border-gray-300 rounded focus:ring-[#4F46E5]">
                        <label class="text-sm text-gray-600">
                            Saya menyatakan bahwa data yang diisi adalah benar dan bersedia mengikuti seluruh proses seleksi 
                            serta komitmen untuk berpartisipasi jika diterima. <span class="text-red-500">*</span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" 
                                @click="showApplicationForm = false; selectedPositionId = null"
                                class="flex-1 px-6 py-3 border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="flex-1 px-6 py-3 bg-[#4F46E5] text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Lamaran'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="activeTab === 'about'" style="display: none;">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tentang Event</h3>
                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed space-y-4">
                        <p>
                            <strong>{{ $eventData['title'] }}</strong> adalah acara bergengsi yang diselenggarakan oleh 
                            <strong>{{ $eventData['organizer'] }}</strong>. Event ini merupakan wadah bagi para profesional, 
                            komunitas, dan enthusiast untuk berkumpul, berbagi pengetahuan, dan menciptakan jaringan yang bermanfaat.
                        </p>
                        
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 my-4">
                            <h4 class="font-bold text-[#4F46E5] mb-2">Mengapa Event Ini Penting?</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Menambah wawasan dan pengetahuan terkini di industri</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Memperluas jaringan profesional dan komunitas</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Berkesempatan belajar langsung dari para ahli dan praktisi</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Mendapatkan pengalaman praktis yang berharga</span>
                                </li>
                            </ul>
                        </div>

                        <p>
                            Sebagai volunteer di event ini, Anda akan menjadi bagian penting dari kesuksesan acara. 
                            Anda akan bekerja sama dengan tim profesional, mendapatkan pengalaman berharga, dan 
                            berkontribusi langsung dalam menciptakan pengalaman terbaik bagi para peserta.
                        </p>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'benefits'" style="display: none;">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Benefit & Fasilitas untuk Volunteer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($benefits as $benefit)
                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                            <svg class="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $benefit }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'requirements'" style="display: none;">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Syarat & Ketentuan</h3>
                    <ul class="space-y-3">
                        @foreach($requirements as $req)
                        <li class="flex items-start gap-3 text-sm text-gray-700">
                            <span class="w-2 h-2 rounded-full bg-[#4F46E5] shrink-0 mt-2"></span>
                            {{ $req }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>

        <div class="space-y-4">
           
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Pendaftaran Relawan</h3>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        Online
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Posisi yang dipilih saat ini :</p>
                    <template x-if="selectedPosition === null">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-center">
                            <p class="text-sm text-gray-400 italic">Belum ada posisi yang dipilih</p>
                        </div>
                    </template>
                    <template x-if="selectedPosition !== null">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                            <p class="text-sm font-bold text-[#4F46E5] mb-2" x-text="selectedPosition.title"></p>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs text-gray-500">Kapasitas Divisi</span>
                                <span class="text-xs font-bold text-gray-900" x-text="selectedPosition.filled + ' / ' + selectedPosition.quota + ' Terisi (' + selectedPosition.percentage + '%)'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-[#4F46E5] h-2.5 rounded-full transition-all duration-500" :style="'width: ' + selectedPosition.percentage + '%'"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <button @click="openApplicationForm()" 
                        class="w-full py-3 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ajukan Lamaran Volunteer
                </button>
                <p class="text-[10px] text-gray-400 text-center mt-3">Pendaftaran gratis tanpa dipungut biaya apapun.</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-[#4F46E5] text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr($eventData['organizer'], 0, 3)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 flex items-center gap-1">
                            {{ $eventData['organizer'] }}
                            @if(!empty($eventData['organizer_verified']))
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">{{ $eventData['organizer_type'] ?? 'Penyelenggara Event' }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $eventData['organizer_desc'] ?? 'Penyelenggara event profesional di Indonesia.' }}</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-bold text-gray-900">Lokasi Event</h4>
                    <span class="text-xs font-bold text-[#4F46E5]">{{ $eventData['location_city'] }}</span>
                </div>

                <div class="relative h-36 bg-gray-50 rounded-xl overflow-hidden mb-4 border border-gray-100">
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, #e0e7ff 0%, #f3f4f6 100%);">
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div class="w-3 h-3 bg-[#4F46E5] rounded-full border-2 border-white shadow-lg"></div>
                        </div>
                        <div class="absolute top-1/3 left-1/4 w-16 h-0.5 bg-gray-300 rotate-12"></div>
                        <div class="absolute top-2/3 right-1/4 w-20 h-0.5 bg-gray-300 -rotate-6"></div>
                    </div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-8 bg-[#4F46E5] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg whitespace-nowrap">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $eventData['location'] ?? 'Lokasi Event' }}
                    </div>
                </div>

                <div class="space-y-2">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase mb-0.5">Alamat :</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $eventData['address'] }}</p>
                    </div>
                </div>

                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($eventData['address'] . ' ' . $eventData['location_city']) }}" 
                   target="_blank" 
                   class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Lihat di Google Maps
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>

        </div>

        <div x-show="showSuccessModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.outside="showSuccessModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
                
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Lamaran Berhasil Diajukan! 🎉</h3>
                <p class="text-sm text-gray-600 text-center mb-6" x-text="modalMessage"></p>
                
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-6">
                    <p class="text-xs text-indigo-700 text-center">
                        📧 Tim kami akan menghubungi Anda melalui email/WhatsApp.
                    </p>
                </div>
                
                <button @click="showSuccessModal = false"
                        class="w-full py-3 bg-[#4F46E5] text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    Oke, Mengerti!
                </button>
            </div>
        </div>

        <div x-show="showErrorModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.outside="showErrorModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
                
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Terjadi Kesalahan</h3>
                <p class="text-sm text-gray-600 text-center mb-6" x-text="modalMessage"></p>
                
                <button @click="showErrorModal = false"
                        class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition">
                    Tutup
                </button>
            </div>
        </div>

    </div> 
</div> 
</x-layouts.crew>