<x-layouts.crew title="Profil Saya - VOLA">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
         x-data="{ 
            cvUrl: '{{ $profile?->cv_url ?? "" }}',
            cvName: '{{ $profile?->cv_path ? basename($profile->cv_path) : "" }}',
            portfolioUrl: '{{ $profile?->portfolio_url ?? "" }}',
            portfolioName: '{{ $profile?->portfolio_path ? basename($profile->portfolio_path) : "" }}',
            
            uploading: false,
            uploadType: null,
            activeUploadTarget: null,
           
            showModal: false,
            modalMessage: '',
            modalType: 'success',
            showConfirmModal: false,
            confirmTarget: null,
            
            showSkillsModal: false,

            selectUploadTarget(type) {
                this.activeUploadTarget = type;
            },

            triggerUpload() {
                if (!this.activeUploadTarget) return;
                const inputId = this.activeUploadTarget === 'cv_path' ? 'upload-cv' : 'upload-portfolio';
                document.getElementById(inputId).click();
            },

            async handleUpload(type, inputId) {
                const input = document.getElementById(inputId);
                if (!input.files[0]) return;

                this.uploading = true;
                this.uploadType = type;

                const formData = new FormData();
                formData.append('file', input.files[0]);
                formData.append('type', type);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const res = await fetch('{{ route("profile.documents.upload") }}', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    if (data.success) {
                        if (type === 'cv_path') {
                            this.cvUrl = data.url;
                            this.cvName = data.filename;
                        }
                        if (type === 'portfolio_path') {
                            this.portfolioUrl = data.url;
                            this.portfolioName = data.filename;
                        }
                        
                        this.modalMessage = data.message;
                        this.modalType = 'success';
                        this.showModal = true;
                        
                        this.activeUploadTarget = null;
                    } else {
                        this.modalMessage = 'Gagal mengunggah file.';
                        this.modalType = 'error';
                        this.showModal = true;
                    }
                } catch (err) {
                    console.error(err);
                    this.modalMessage = 'Terjadi kesalahan jaringan.';
                    this.modalType = 'error';
                    this.showModal = true;
                } finally {
                    this.uploading = false;
                    input.value = '';
                }
            },

            confirmDelete(type) {
                this.confirmTarget = type;
                this.showConfirmModal = true;
            },

            executeDelete() {
                if (this.confirmTarget) {
                    document.getElementById('delete-form-' + this.confirmTarget).submit();
                }
                this.showConfirmModal = false;
            }
         }">
        
        @php
            $skillsList = is_array($profile?->skills) ? $profile->skills : [];
            $availableSkills = [
                'Liaison Officer (LO)', 'Dokumentasi & Media', 'Registrasi & Ticketing',
                'Stage Crew', 'Keamanan / Security', 'Konsumsi & Logistik',
                'MC / Host', 'Desain Grafis', 'Videografer', 'First Aid / Medis'
            ];
        @endphp

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi diri dan dokumen portofolio kamu.</p>
        </div>

        @if(session('status'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
              
                <div class="relative shrink-0">
                    @if($profile?->photo_url)
                        <img src="{{ $profile->photo_url }}" 
                             alt="{{ $user->name }}" 
                             class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md">
                    @else
                        <div class="w-20 h-20 rounded-full bg-[#4F46E5] text-white flex items-center justify-center text-2xl font-bold border-4 border-white shadow-md">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif                   
                   
                </div>

                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-2">
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 w-fit">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Volunteer Terverifikasi
                        </span>
                    </div>
                    
                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-600 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $profile?->city ?? 'Belum diatur' }}
                        </p>
                        @if($profile?->institution)
                        <p class="text-sm text-gray-600 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $profile->institution }}
                        </p>
                        @endif
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Tentang Saya</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $profile?->bio ?? 'Belum ada deskripsi tentang diri Anda. Silakan edit profil untuk menambahkan bio.' }}
                </p>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500">Email</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->email }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500">WhatsApp / Kontak</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ $user->phone ?? 'Belum diatur' }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500">Domisili</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ $profile?->city ?? 'Belum diatur' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">      
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-gray-900">Dokumen Resmi & Portfolio</h3>
                </div>

                <div class="space-y-3">
                    <div @click="selectUploadTarget('cv_path')" 
                         :class="activeUploadTarget === 'cv_path' ? 'border-[#4F46E5] bg-indigo-50/30' : 'border-gray-200 hover:border-[#4F46E5]'"
                         class="flex items-center justify-between p-4 border rounded-xl transition group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900" x-text="cvName ? cvName : 'Belum Ada CV'"></p>
                                <p class="text-xs text-gray-500" x-text="cvUrl ? 'Siap dilihat' : 'Silakan unggah CV kamu'"></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2" @click.stop>
                            <template x-if="cvUrl">
                                <a :href="cvUrl" target="_blank" class="px-3 py-1.5 text-xs font-bold text-[#4F46E5] hover:bg-indigo-50 rounded-lg transition">Lihat File</a>
                            </template>
                            <template x-if="!cvUrl">
                                <span class="px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Lihat File</span>
                            </template>
                            
                            <input type="file" id="upload-cv" class="hidden" accept=".pdf,.jpg,.jpeg,.png" @change="handleUpload('cv_path', 'upload-cv')">
                          
                            <template x-if="cvUrl">
                                <label for="upload-cv" class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg transition cursor-pointer">
                                    <span x-show="!uploading || uploadType !== 'cv_path'">Ganti File</span>
                                    <span x-show="uploading && uploadType === 'cv_path'" class="text-[#4F46E5]">Mengunggah...</span>
                                </label>
                            </template>
                            <template x-if="!cvUrl">
                                <span class="px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Ganti File</span>
                            </template>

                            <template x-if="cvUrl">
                                <button @click="confirmDelete('cv_path')" class="px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition">Hapus File</button>
                            </template>
                            <template x-if="!cvUrl">
                                <span class="px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Hapus File</span>
                            </template>
                         
                            <form id="delete-form-cv_path" action="{{ route('profile.documents.delete', 'cv_path') }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>

                    <div @click="selectUploadTarget('portfolio_path')"
                         :class="activeUploadTarget === 'portfolio_path' ? 'border-[#4F46E5] bg-indigo-50/30' : 'border-gray-200 hover:border-[#4F46E5]'"
                         class="flex items-center justify-between p-4 border rounded-xl transition group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900" x-text="portfolioName ? portfolioName : 'Belum Ada Portfolio'"></p>
                                <p class="text-xs text-gray-500" x-text="portfolioUrl ? 'Siap dilihat' : 'Silakan unggah Portfolio kamu'"></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2" @click.stop>
                            <template x-if="portfolioUrl">
                                <a :href="portfolioUrl" target="_blank" class="px-3 py-1.5 text-xs font-bold text-[#4F46E5] hover:bg-indigo-50 rounded-lg transition">Lihat File</a>
                            </template>
                            <template x-if="!portfolioUrl">
                                <span class="px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Lihat File</span>
                            </template>
                            
                            <input type="file" id="upload-portfolio" class="hidden" accept=".pdf,.jpg,.jpeg,.png" @change="handleUpload('portfolio_path', 'upload-portfolio')">
                           
                            <template x-if="portfolioUrl">
                                <label for="upload-portfolio" class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg transition cursor-pointer">
                                    <span x-show="!uploading || uploadType !== 'portfolio_path'">Ganti File</span>
                                    <span x-show="uploading && uploadType === 'portfolio_path'" class="text-[#4F46E5]">Mengunggah...</span>
                                </label>
                            </template>
                            <template x-if="!portfolioUrl">
                                <span class="px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Ganti File</span>
                            </template>

                            <template x-if="portfolioUrl">
                                <button @click="confirmDelete('portfolio_path')" class="px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition">Hapus File</button>
                            </template>
                            <template x-if="!portfolioUrl">
                                <span class="px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Hapus File</span>
                            </template>

                            <form id="delete-form-portfolio_path" action="{{ route('profile.documents.delete', 'portfolio_path') }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>

                    <div x-show="(!cvUrl || !portfolioUrl) && activeUploadTarget" 
                         x-transition
                         class="border-2 border-dashed border-[#4F46E5] bg-indigo-50/50 rounded-xl p-8 text-center transition cursor-pointer"
                         @click="triggerUpload()">
                        <svg class="w-8 h-8 text-[#4F46E5] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-bold text-gray-900 mb-1">
                            Unggah Berkas : <span x-text="activeUploadTarget === 'cv_path' ? 'CV' : 'Portfolio'"></span>
                        </p>
                        <p class="text-xs text-gray-500 mb-3">Berkas PDF atau JPG (maksimal 5MB)</p>
                        <button class="px-4 py-2 bg-[#4F46E5] text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition pointer-events-none">
                            Pilih Berkas Dari Perangkat
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Keahlian & Minat</h3>
                        <p class="text-xs text-gray-500 mt-1">Kategori keahlian dan peran lapangan yang kamu kuasai.</p>
                    </div>
                    <span class="text-xs font-bold text-[#4F46E5]">{{ count($skillsList) }} Keahlian</span>
                </div>

                <div class="flex flex-wrap gap-2 mb-4 content-start">
                    @forelse($skillsList as $skill)
                        <span class="px-2.5 py-1 bg-indigo-50 text-[#4F46E5] text-xs font-medium rounded-md border border-indigo-100 h-fit leading-none whitespace-nowrap">
                            {{ $skill }}
                        </span>
                    @empty
                        <p class="text-xs text-gray-400 italic w-full text-center py-4">Belum ada keahlian yang ditambahkan.</p>
                    @endforelse
                </div>

                <button @click="showSkillsModal = true" type="button" class="w-full py-2.5 border-2 border-dashed border-gray-200 text-[#4F46E5] text-xs font-bold rounded-xl hover:border-[#4F46E5] hover:bg-indigo-50 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Keahlian
                </button>
            </div>
        </div>

        <div x-show="showSkillsModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.outside="showSkillsModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Keahlian & Minat</h3>
                    <button @click="showSkillsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <form action="{{ route('profile.update.skills') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <p class="text-xs text-gray-500 mb-3">Pilih keahlian yang kamu kuasai.</p>
                    
                    <div class="grid grid-cols-2 gap-2 mb-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($availableSkills as $skill)
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50 {{ in_array($skill, $skillsList) ? 'border-[#4F46E5] bg-indigo-50' : 'border-gray-200' }}">
                                <input type="checkbox" name="skills[]" value="{{ $skill }}" 
                                       {{ in_array($skill, $skillsList) ? 'checked' : '' }}
                                       class="rounded text-[#4F46E5] focus:ring-[#4F46E5]">
                                <span class="text-sm text-gray-700">{{ $skill }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mb-6">
                        <input type="text" name="custom_skill" placeholder="Tambahkan keahlian lain..." 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] text-sm">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showSkillsModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#4F46E5] rounded-lg hover:bg-indigo-700">Simpan Keahlian</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.outside="showModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative text-center">
                
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center"
                         :class="modalType === 'success' ? 'bg-emerald-100' : 'bg-red-100'">
                        <svg x-show="modalType === 'success'" class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="modalType === 'error'" class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-2" x-text="modalType === 'success' ? 'Berhasil!' : 'Oops!'"></h3>
                <p class="text-sm text-gray-600 mb-6" x-text="modalMessage"></p>
                
                <button @click="showModal = false"
                        class="w-full py-3 bg-[#4F46E5] text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    Oke, Mengerti!
                </button>
            </div>
        </div>

        <div x-show="showConfirmModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.outside="showConfirmModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative text-center">
                
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Dokumen?</h3>
                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus berkas ini? Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="flex gap-3">
                    <button @click="showConfirmModal = false"
                            class="flex-1 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button @click="executeDelete()"
                            class="flex-1 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.crew>