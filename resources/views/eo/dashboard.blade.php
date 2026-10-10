<x-layouts.eo title="Beranda EO — Vola">

    {{-- Verification Status Warning --}}
    @if ($organizerProfile && $organizerProfile->verification_status->value !== 'verified')
        <div class="mb-6">
            @if ($organizerProfile->verification_status->value === 'pending')
                <x-alert type="warning">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.19 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <strong>Profil Organisasi Menunggu Verifikasi</strong><br>
                            Akun Anda sedang dalam proses verifikasi admin. Anda dapat membuat draft event, tetapi belum dapat mengirimkan untuk review sampai akun terverifikasi.
                        </div>
                    </div>
                </x-alert>
            @elseif ($organizerProfile->verification_status->value === 'rejected')
                <x-alert type="error">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong>Profil Organisasi Ditolak</strong><br>
                            @if ($organizerProfile->rejection_reason)
                                Alasan: {{ $organizerProfile->rejection_reason }}<br>
                            @endif
                            Silakan perbarui profil organisasi Anda dan ajukan kembali.
                        </div>
                    </div>
                </x-alert>
            @endif
        </div>
    @endif

    {{-- Greeting --}}
    <div class="mb-8">
        @if ($organizerProfile && $organizerProfile->verification_status->value === 'verified')
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-200">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    Partner Terverifikasi Resmi
                </span>
            </div>
        @endif

        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            Selamat Datang, {{ auth()->user()->name }} 👋
        </h1>
        <p class="mt-1 text-gray-500">Kelola rekrutmen relawan, seleksi kru, dan kelancaran publikasi event Anda.</p>
    </div>

    {{-- No Organizer Profile Warning --}}
    @if (! $organizerProfile)
        <div class="mb-8">
            <x-alert type="warning">
                Profil organisasi Anda belum dibuat. Silakan lengkapi
                <a href="{{ route('eo.organization') }}" class="font-semibold underline">Profil Organisasi</a>
                untuk dapat membuat dan mengirim event.
            </x-alert>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-card padding="p-5">
            <p class="text-xs text-gray-500 mb-1">Total Event</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalEvents }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Semua event
            </div>
        </x-card>

        <x-card padding="p-5">
            <p class="text-xs text-gray-500 mb-1">Draft</p>
            <p class="text-3xl font-bold text-gray-600">{{ $draftCount }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Belum dikirim
            </div>
        </x-card>

        <x-card padding="p-5">
            <p class="text-xs text-gray-500 mb-1">Menunggu Review</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $pendingReviewCount }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dalam antrian review
            </div>
        </x-card>

        <x-card padding="p-5">
            <p class="text-xs text-gray-500 mb-1">Ditolak</p>
            <p class="text-3xl font-bold text-red-600">{{ $rejectedCount }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Perlu perbaikan
            </div>
        </x-card>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <a href="{{ route('eo.events.create') }}"
           class="flex items-center gap-4 p-5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition-colors group">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:bg-white/30 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-lg">Buat Event Baru</p>
                <p class="text-brand-100 text-sm">Buat dan kelola event rekrutmen</p>
            </div>
        </a>

        <a href="{{ route('eo.events.index') }}"
           class="flex items-center gap-4 p-5 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl transition-colors group">
            <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-lg text-gray-900">Event Saya</p>
                <p class="text-gray-500 text-sm">Lihat dan kelola semua event</p>
            </div>
        </a>
    </div>

    {{-- Recent Events --}}
    <div class="mb-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Event Saya</h2>
                <p class="text-sm text-gray-500">Daftar event yang Anda kelola</p>
            </div>
            <a href="{{ route('eo.events.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                Lihat Semua →
            </a>
        </div>

        @if ($events->isEmpty())
            <x-card>
                <x-empty-state
                    title="Belum ada event"
                    description="Mulai dengan membuat event pertama Anda."
                >
                    <x-slot name="action">
                        <a href="{{ route('eo.events.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Event Pertama
                        </a>
                    </x-slot>
                </x-empty-state>
            </x-card>
        @else
            <div class="space-y-4">
                @foreach ($events as $event)
                    <x-card padding="p-0">
                        <div class="flex items-stretch gap-0">
                            {{-- Poster --}}
                            <div class="w-28 sm:w-36 shrink-0 bg-gray-100 rounded-l-xl overflow-hidden">
                                @if ($event->poster_path)
                                    <img src="{{ Storage::url($event->poster_path) }}"
                                         alt="Poster {{ $event->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center min-h-[80px]">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 p-4 sm:p-5 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <x-badge-status :status="$event->status->value" />
                                            @if ($event->category)
                                                <span class="text-xs text-gray-500">{{ $event->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $event->title }}</h3>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-gray-500">
                                        @if ($event->start_date)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $event->start_date->translatedFormat('d M Y') }}
                                            </span>
                                        @endif
                                        @if ($event->location)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                </svg>
                                                {{ $event->location }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $event->positions->count() }} posisi
                                    </p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 mt-3">
                                    @if (in_array($event->status->value, ['draft', 'rejected']))
                                        <a href="{{ route('eo.events.edit', $event) }}"
                                           class="text-xs px-3 py-1.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-colors">
                                            Kelola Event
                                        </a>
                                    @else
                                        <a href="{{ route('eo.events.edit', $event) }}"
                                           class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                            Lihat Detail
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>

</x-layouts.eo>
