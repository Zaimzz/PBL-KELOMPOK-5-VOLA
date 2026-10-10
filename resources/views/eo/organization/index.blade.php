<x-layouts.eo title="Profil Organisasi — Vola">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('eo.dashboard') }}" class="hover:text-gray-700">Beranda EO</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">Profil Organisasi</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Organisasi</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola identitas resmi penyelenggara, kontak verifikasi, dan informasi organisasi.</p>
        </div>
        <a href="{{ route('eo.organization.edit') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Profil Organisasi
        </a>
    </div>

    @if (! $profile)
        {{-- Belum ada profil — ajak mengisi --}}
        <x-card>
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-1">Profil Organisasi Belum Diisi</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
                    Lengkapi data organisasi Anda dan upload dokumen legal untuk mengajukan verifikasi kepada Admin.
                </p>
                <a href="{{ route('eo.organization.edit') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                    Lengkapi Profil Sekarang
                </a>
            </div>
        </x-card>
    @else
        <div class="space-y-6">

            {{-- Status Verifikasi Banner --}}
            @php $vs = $profile->verification_status; @endphp

            @if ($vs === \App\Enums\VerificationStatus::Pending)
                <div class="flex items-start gap-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-800">Menunggu Verifikasi</p>
                        <p class="text-xs text-yellow-700 mt-0.5">Profil organisasi sedang diperiksa oleh Admin. Anda akan mendapat pemberitahuan setelah proses selesai.</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">● Pending</span>
                </div>

            @elseif ($vs === \App\Enums\VerificationStatus::Verified)
                <div class="flex items-start gap-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-800">✓ Terverifikasi Resmi</p>
                        <p class="text-xs text-green-700 mt-0.5">
                            Organisasi Anda telah diverifikasi
                            @if($profile->verified_at) pada {{ $profile->verified_at->translatedFormat('d F Y') }} @endif.
                            Anda dapat membuat dan mempublikasikan event.
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✓ Verified</span>
                </div>

            @elseif ($vs === \App\Enums\VerificationStatus::Rejected)
                <div class="flex items-start gap-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-800">Verifikasi Ditolak</p>
                        @if ($profile->rejection_reason)
                            <p class="text-xs text-red-700 mt-1"><span class="font-medium">Alasan:</span> {{ $profile->rejection_reason }}</p>
                        @endif
                        <p class="text-xs text-red-600 mt-2">Perbarui data & dokumen Anda, lalu ajukan kembali untuk proses verifikasi ulang.</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">✕ Ditolak</span>
                </div>
            @endif

            {{-- Profile Card --}}
            <x-card>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                    {{-- Avatar inisial --}}
                    <div class="w-16 h-16 rounded-2xl bg-brand-100 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-brand-700">
                            {{ strtoupper(substr($profile->organization_name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="text-lg font-bold text-gray-900">{{ $profile->organization_name }}</h2>
                            <x-badge-status :status="$vs->value" />
                        </div>
                        <p class="text-sm text-gray-500">{{ $profile->organization_type }}</p>
                    </div>
                </div>

                @if ($profile->description)
                    <p class="text-sm text-gray-600 mb-6 leading-relaxed">{{ $profile->description }}</p>
                @endif

                {{-- Info chips --}}
                <div class="flex flex-wrap gap-3">
                    @if ($profile->organization_type)
                        <div class="flex items-center gap-2 text-xs text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Bentuk Entitas: <strong>{{ $profile->organization_type }}</strong></span>
                        </div>
                    @endif
                    @if ($profile->city)
                        <div class="flex items-center gap-2 text-xs text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Domisili / Kantor: <strong>{{ $profile->city }}</strong></span>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- Informasi PIC & Kontak --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-card>
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi PIC
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Nama PIC</p>
                            <p class="text-sm font-medium text-gray-900">{{ $profile->pic_name ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Nomor Telepon</p>
                            <p class="text-sm font-medium text-gray-900">{{ $profile->pic_phone ?: '—' }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Social Link
                    </h3>
                    @if ($profile->social_link)
                        <a href="{{ $profile->social_link }}" target="_blank" rel="noopener noreferrer"
                           class="text-sm text-brand-600 hover:text-brand-700 break-all">
                            {{ $profile->social_link }}
                        </a>
                    @else
                        <p class="text-sm text-gray-400 italic">Belum diisi</p>
                    @endif
                </x-card>
            </div>

            {{-- Dokumen Legal --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Dokumen Legal
                    </h3>
                    <a href="{{ route('eo.organization.edit') }}"
                       class="text-xs text-brand-600 hover:text-brand-700 font-medium">Ganti Dokumen</a>
                </div>

                @if ($profile->document_path)
                    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">Dokumen sudah diunggah</p>
                            <p class="text-xs text-green-600">Format PDF / JPG / PNG — maks. 5 MB</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.19 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Dokumen legal belum diunggah</p>
                            <p class="text-xs text-yellow-600">Upload dokumen legal (surat izin, akta, dll.) untuk proses verifikasi.</p>
                        </div>
                    </div>
                @endif
            </x-card>

            {{-- Aksi Verifikasi --}}
            @if ($vs !== \App\Enums\VerificationStatus::Pending && $vs !== \App\Enums\VerificationStatus::Verified)
                <x-card>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                @if ($vs === \App\Enums\VerificationStatus::Rejected)
                                    Ajukan Verifikasi Kembali
                                @else
                                    Ajukan Verifikasi
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if ($vs === \App\Enums\VerificationStatus::Rejected)
                                    Setelah memperbarui profil dan dokumen, ajukan kembali untuk direview Admin.
                                @else
                                    Pastikan semua data dan dokumen legal sudah lengkap sebelum mengajukan.
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('eo.organization.verify') }}" class="shrink-0">
                            @csrf
                            <button type="submit"
                                    @if (! $profile->document_path) disabled title="Upload dokumen legal terlebih dahulu" @endif
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg transition-colors
                                           {{ $profile->document_path ? 'bg-brand-600 text-white hover:bg-brand-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @if ($vs === \App\Enums\VerificationStatus::Rejected)
                                    Ajukan Kembali
                                @else
                                    Ajukan Verifikasi
                                @endif
                            </button>
                        </form>
                    </div>
                </x-card>
            @endif

            {{-- Riwayat Event (dari data Event yang sudah ada) --}}
            @if ($events->isNotEmpty())
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900">Riwayat Event & Portofolio</h3>
                        <a href="{{ route('eo.events.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($events as $event)
                            <x-card>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    @if ($event->category)
                                        <span class="text-xs font-medium text-brand-600 uppercase tracking-wide">{{ $event->category->name }}</span>
                                    @endif
                                    <x-badge-status :status="$event->status->value" />
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2 leading-tight">{{ $event->title }}</h4>
                                <div class="space-y-1 text-xs text-gray-500">
                                    @if ($event->start_date)
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}
                                        </div>
                                    @endif
                                    @if ($event->location)
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                            {{ $event->location }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-xs text-gray-400">
                                        {{ $event->positions->count() ?? 0 }} Posisi
                                    </span>
                                    <a href="{{ route('eo.events.edit', $event) }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                                        Detail Event →
                                    </a>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    @endif

</x-layouts.eo>
