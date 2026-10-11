<x-layouts.crew title="Profil Saya - VOLA">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        @php
            // Format nama user untuk filename (ganti spasi dengan underscore)
            $userFilename = str_replace(' ', '_', $user->name);
            $currentYear = date('Y');
            $cvFilename = "CV_{$userFilename}_{$currentYear}.pdf";
            $portfolioFilename = "Portfolio_Event_{$userFilename}.pdf";
        @endphp

        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-[#4F46E5]">Akun</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium">Profil Saya</span>
            </nav>
        </div>

        <!-- Header Halaman -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi diri, kontak, dan dokumen portofolio kamu.</p>
        </div>

        @if(session('status'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            </div>
        @endif

        <!-- Card Profil Utama -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <!-- Avatar dengan Badge -->
                <div class="relative shrink-0">
                    <div class="w-20 h-20 rounded-full bg-[#4F46E5] text-white flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <!-- Badge Verifikasi -->
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>

                <!-- Info Profil -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1z"/>
                            </svg>
                            {{ $profile?->institution ?? 'Belum diatur' }}
                        </p>
                        <p class="text-sm text-gray-600 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $profile?->city ?? 'Belum diatur' }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Profil
                        </a>
                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-bold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat Profil Publik
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tentang Saya -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Tentang Saya</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $profile?->bio ?? 'Belum ada deskripsi tentang diri Anda. Silakan edit profil untuk menambahkan bio.' }}
                </p>
            </div>

            <!-- Kontak Info -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500">Email Kampus</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ $user->email }}</p>
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

        <!-- Dokumen & Keahlian -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Dokumen Resmi & Portfolio -->
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-gray-900">Dokumen Resmi & Portfolio</h3>
                    <p class="text-xs text-gray-500 mt-1">Berkas yang dibagikan secara otomatis ke kurator acara saat melamar event.</p>
                </div>

                <div class="space-y-3">
                    <!-- CV File (Nama Dinamis) -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-[#4F46E5] transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $cvFilename }}</p>
                                <p class="text-xs text-gray-500">1,8 MB • Diperbarui 10 Feb {{ $currentYear }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded">Terunggah</span>
                            <button class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg transition">Lihat CV</button>
                            <button class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg transition">Ganti File</button>
                        </div>
                    </div>

                    <!-- Portfolio File (Nama Dinamis) -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-[#4F46E5] transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $portfolioFilename }}</p>
                                <p class="text-xs text-gray-500">4,2 MB • Dokumentasi Acara & UI</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded">PDF Desain</span>
                            <button class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg transition">Unduh</button>
                            <button class="px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition">Hapus</button>
                        </div>
                    </div>

                    <!-- Upload Area -->
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-[#4F46E5] transition cursor-pointer">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-bold text-gray-900 mb-1">Unggah Berkas Baru</p>
                        <p class="text-xs text-gray-500 mb-3">Tarik & lepas berkas PDF atau JPG (maksimal 5MB)</p>
                        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition">
                            Pilih Berkas Dari Perangkat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Keahlian & Minat -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Keahlian & Minat</h3>
                        <p class="text-xs text-gray-500 mt-1">Kategori keahlian dan peran lapangan yang kamu kuasai.</p>
                    </div>
                    <span class="text-xs font-bold text-[#4F46E5]">7 Keahlian</span>
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="px-3 py-1.5 bg-indigo-50 text-[#4F46E5] text-xs font-medium rounded-lg border border-indigo-100">Liaison Officer (LO)</span>
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">Public Speaking</span>
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">Figma & UI/UX</span>
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">Desain Grafis</span>
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">Registrasi & Ticketing</span>
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">Dokumentasi Foto</span>
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">Manajemen Waktu</span>
                </div>

                <button class="w-full py-2.5 border-2 border-dashed border-gray-200 text-[#4F46E5] text-xs font-bold rounded-xl hover:border-[#4F46E5] hover:bg-indigo-50 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Keahlian
                </button>
            </div>
        </div>

        <!-- Riwayat Event -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Riwayat Singkat Event Terakhir</h3>
                    <p class="text-xs text-gray-500 mt-1">Daftar kepanitiaan dan kegiatan volunteer terbaru yang telah selesai.</p>
                </div>
                <a href="#" class="text-xs font-bold text-[#4F46E5] hover:text-indigo-800 flex items-center gap-1">
                    Lihat Semua Riwayat
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($recentEvents as $event)
                <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-xl hover:border-[#4F46E5] hover:shadow-sm transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-[#4F46E5]">{{ strtoupper(substr($event['name'], 0, 3)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-gray-900 truncate">{{ $event['name'] }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Peran: {{ $event['role'] }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full">{{ $event['status'] }}</span>
                            <span class="text-[10px] text-gray-400">{{ $event['date'] }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-2 text-center py-8 text-sm text-gray-500">
                    Belum ada riwayat event.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.crew>