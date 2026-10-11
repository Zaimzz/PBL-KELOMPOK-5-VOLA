<x-layouts.crew title="Pengaturan - VOLA">
    <!-- Container utama digeser ke kiri dengan max-width spesifik -->
    <div x-data="{ 
            activeTab: '{{ session('preferences_saved') ? 'preferences' : 'account' }}',
            showNotification: {{ session('preferences_saved') ? 'true' : 'false' }}
         }" 
         class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header Halaman -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kredensial akun crew dan preferensi pencarian event kamu.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar Tab Navigasi (Kiri) -->
            <div class="w-full lg:w-60 shrink-0 space-y-1">
                <button 
                    @click="activeTab = 'account'"
                    :class="activeTab === 'account' ? 'bg-indigo-50 text-[#4F46E5] font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Akun & Keamanan
                </button>
                
                <button 
                    @click="activeTab = 'preferences'"
                    :class="activeTab === 'preferences' ? 'bg-indigo-50 text-[#4F46E5] font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Preferensi Event
                </button>
            </div>

            <!-- Konten Form (Kanan) -->
            <div class="flex-1 min-w-0 space-y-6">
                
                <!-- TAB 1: AKUN & KEAMANAN -->
                <div x-show="activeTab === 'account'" x-transition.opacity class="space-y-6">
                    
                    <!-- Card Informasi Akun -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-gray-900 mb-1">Informasi Akun & Keamanan</h2>
                        <p class="text-xs text-gray-500 mb-6">Kredensial login utama dan kontak verifikasi.</p>

                        <!-- Email Section -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-b border-gray-50 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Alamat Email</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900">{{ $user->email }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Terverifikasi
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Email aktif digunakan untuk verifikasi akun crew.
                                </p>
                            </div>
                            <button type="button" class="px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition whitespace-nowrap">Ubah Email</button>
                        </div>

                        <!-- Phone Section -->
                        <form action="{{ route('settings.update.profile') }}" method="POST" class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-b border-gray-50 gap-4">
                            @csrf @method('PUT')
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Nomor WhatsApp Aktif</p>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+62 812-xxxx-xxxx" class="text-sm font-bold text-gray-900 bg-transparent border-none p-0 focus:ring-0 w-full placeholder-gray-300">
                                <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Digunakan untuk undangan grup koordinasi panitia event.
                                </p>
                            </div>
                            <button type="submit" class="px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition whitespace-nowrap">Ubah Nomor</button>
                        </form>

                        <!-- Password Section -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Kata Sandi</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 tracking-widest">••••••••••••</span>
                                    <span class="text-[10px] text-gray-400">(Terakhir diubah 2 bulan lalu)</span>
                                </div>
                            </div>
                            <button type="button" class="px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition whitespace-nowrap">Ubah Kata Sandi</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: PREFERENSI EVENT -->
                <div x-show="activeTab === 'preferences'" x-transition.opacity class="space-y-6">
                    
                    <!-- Card Preferensi -->
                    <form action="{{ route('settings.update.preferences') }}" method="POST" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        @csrf @method('PUT')
                        
                        <!-- Notifikasi Sukses (Auto-hide setelah 3 detik) -->
                        <div x-show="showNotification" 
                             x-init="setTimeout(() => showNotification = false, 3000)"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-500"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm font-medium text-emerald-800">Preferensi event berhasil disimpan!</p>
                        </div>
                        
                        <h2 class="text-lg font-bold text-gray-900 mb-1">Preferensi Pencarian Event</h2>
                        <p class="text-xs text-gray-500 mb-6">Bantu kami menampilkan rekomendasi event yang paling sesuai dengan minatmu.</p>

                        <!-- Checkbox Kategori Menggunakan Alpine.js + Flash Session -->
                        <div class="mb-6">
                            <p class="text-sm font-bold text-gray-900 mb-3">
                                Kategori Minat Event 
                                <span class="text-gray-400 font-normal text-xs">(Pilih minimal 1)</span>
                            </p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($availableCategories as $cat)
                                    @php
                                        // Cek apakah baru saja save (flash session)
                                        $justSaved = session('preferences_saved');
                                        
                                        // Jika baru save, set semua ke false. Jika tidak, ambil dari database
                                        if ($justSaved) {
                                            $isChecked = false;
                                        } else {
                                            $currentSkills = $selectedSkills ?? [];
                                            $isChecked = in_array($cat['id'], $currentSkills);
                                        }
                                    @endphp
                                    
                                    {{-- Setiap checkbox punya state Alpine sendiri --}}
                                    <div x-data="{ checked: {{ $isChecked ? 'true' : 'false' }} }">
                                        <label class="cursor-pointer relative group">
                                            {{-- Input asli: tetap ada untuk form submission, disinkronkan dengan Alpine --}}
                                            <input type="checkbox" 
                                                   name="skills[]" 
                                                   value="{{ $cat['id'] }}" 
                                                   class="sr-only" 
                                                   :checked="checked"
                                                   @click="checked = !checked">
                                            
                                            {{-- Card Container --}}
                                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all duration-200"
                                                 :class="checked ? 'border-[#4F46E5] bg-indigo-50' : 'border-gray-200 bg-white group-hover:border-indigo-300'">
                                                
                                                {{-- Kotak Centang --}}
                                                <div class="w-5 h-5 shrink-0 rounded border-2 flex items-center justify-center transition-colors duration-200"
                                                     :class="checked ? 'bg-[#4F46E5] border-[#4F46E5]' : 'bg-white border-gray-300'">
                                                    
                                                    {{-- Ikon Centang: muncul/hilang via Alpine --}}
                                                    <svg x-show="checked" 
                                                         x-transition:enter="transition ease-out duration-150"
                                                         x-transition:enter-start="opacity-0 scale-50"
                                                         x-transition:enter-end="opacity-100 scale-100"
                                                         x-transition:leave="transition ease-in duration-100"
                                                         x-transition:leave-start="opacity-100 scale-100"
                                                         x-transition:leave-end="opacity-0 scale-50"
                                                         class="w-3.5 h-3.5 text-white" 
                                                         fill="none" 
                                                         stroke="currentColor" 
                                                         viewBox="0 0 24 24"
                                                         style="stroke-width: 3;">
                                                        <path stroke-linecap="round" 
                                                              stroke-linejoin="round" 
                                                              d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                
                                                {{-- Label Text --}}
                                                <span class="text-sm font-medium select-none transition-colors duration-200"
                                                      :class="checked ? 'text-[#4F46E5] font-semibold' : 'text-gray-700'">
                                                    {{ $cat['name'] }}
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kota / Wilayah Utama -->
                        <div class="mb-8">
                            <p class="text-sm font-bold text-gray-900 mb-3">Kota / Wilayah Utama</p>
                            <select name="city" class="w-full sm:w-2/3 px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent outline-none appearance-none cursor-pointer">
                                <option value="" disabled {{ !$preferredCity ? 'selected' : '' }}>Pilih wilayah utama...</option>
                                <option value="Malang" {{ $preferredCity == 'Malang' ? 'selected' : '' }}>Malang</option>
                                <option value="Jakarta" {{ $preferredCity == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                <option value="Bandung" {{ $preferredCity == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                <option value="Yogyakarta" {{ $preferredCity == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                                <option value="Surabaya" {{ $preferredCity == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                            </select>
                            <button type="button" class="mt-3 text-xs font-bold text-[#4F46E5] hover:text-indigo-800 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Wilayah Lain
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Perubahan preferensi akan langsung disesuaikan di rekomendasi beranda.
                            </p>
                            <div class="flex gap-3">
                                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">Batal</a>
                                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#4F46E5] rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-layouts.crew>