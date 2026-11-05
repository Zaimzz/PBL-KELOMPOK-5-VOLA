<x-layouts.eo title="Pendaftar — Vola">

    {{-- ================================================================
         STATE 1: EO belum memiliki profil organisasi
    ================================================================ --}}
    @if (! $organizerProfile)
        <div class="mb-6">
            <x-alert type="warning">
                Profil organisasi Anda belum dibuat. Silakan lengkapi
                <a href="{{ route('eo.organization') }}" class="font-semibold underline">Profil Organisasi</a>
                terlebih dahulu.
            </x-alert>
        </div>

        <div class="text-center py-24">
            <div class="mx-auto w-20 h-20 bg-brand-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Profil Organisasi Diperlukan</h2>
            <p class="text-gray-500 max-w-sm mx-auto mb-6">Lengkapi profil organisasi Anda untuk mulai mengelola pendaftar event.</p>
            <a href="{{ route('eo.organization') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                Lengkapi Profil
            </a>
        </div>

    {{-- ================================================================
         STATE 2: EO memiliki profil tapi belum punya event
    ================================================================ --}}
    @elseif ($events->isEmpty())
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Seleksi Pendaftar Relawan</h1>
            <p class="text-gray-500 mt-1">Lihat data dan berkas pendaftar untuk menentukan calon volunteer.</p>
        </div>

        <x-card>
            <div class="text-center py-16">
                <div class="mx-auto w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Event</h2>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">
                    Anda perlu membuat event terlebih dahulu agar volunteer dapat mendaftar dan muncul di halaman ini.
                </p>
                <a href="{{ route('eo.events.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Event Pertama
                </a>
            </div>
        </x-card>

    {{-- ================================================================
         STATE 3: EO memiliki event (tampilan utama)
    ================================================================ --}}
    @else

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Seleksi Pendaftar Relawan</h1>
                    <p class="text-gray-500 mt-1">
                        Lihat data dan berkas pendaftar untuk menentukan calon volunteer.
                        @if ($selectedEvent)
                            Event: <span class="font-semibold text-gray-700">{{ $selectedEvent->title }}</span>
                        @endif
                    </p>
                </div>

                {{-- Event Selector --}}
                @if ($events->count() > 1)
                    <div class="shrink-0">
                        <form method="GET" action="{{ route('eo.applicants.index') }}" id="event-selector-form">
                            <label for="event_id" class="sr-only">Pilih Event</label>
                            <select
                                id="event_id"
                                name="event_id"
                                onchange="document.getElementById('event-selector-form').submit()"
                                class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                            >
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}" {{ $selectedEvent?->id === $event->id ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::limit($event->title, 45) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

            {{-- Total Pendaftar --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pendaftar</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalCount) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Relawan</p>
                </div>
                <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Menunggu Seleksi --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Menunggu Seleksi</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ number_format($pendingCount) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Berkas</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Diterima --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Diterima</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($acceptedCount) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Relawan Diterima</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm mb-4">
            <form method="GET" action="{{ route('eo.applicants.index') }}" class="p-4 flex flex-col sm:flex-row gap-3">
                {{-- Keep selected event --}}
                @if ($selectedEvent)
                    <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                @endif

                {{-- Search --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama pendaftar..."
                        class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent bg-gray-50"
                    >
                </div>

                {{-- Filter: Divisi --}}
                <select
                    name="position_id"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                >
                    <option value="">Semua Divisi</option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter: Status --}}
                <select
                    name="status"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                >
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Dicabut</option>
                </select>

                {{-- Submit & Reset --}}
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        Cari
                    </button>
                    @if (request()->hasAny(['search', 'position_id', 'status']))
                        <a href="{{ route('eo.applicants.index', array_filter(['event_id' => $selectedEvent?->id])) }}"
                           class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">

            @if ($applications->isEmpty())
                {{-- ====================================================
                     Empty State — No applicants matching filter/search
                ==================================================== --}}
                <div class="text-center py-20 px-6">
                    @if (request()->hasAny(['search', 'position_id', 'status']))
                        {{-- Empty: search/filter no result --}}
                        <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Hasil</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-6">
                            Tidak ditemukan pendaftar yang sesuai dengan filter yang dipilih. Coba ubah atau hapus filter.
                        </p>
                        <a href="{{ route('eo.applicants.index', array_filter(['event_id' => $selectedEvent?->id])) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Hapus Filter
                        </a>
                    @else
                        {{-- Empty: no applicants at all for this event --}}
                        <div class="mx-auto w-24 h-24 mb-6">
                            {{-- Ilustrasi sederhana rekrutmen --}}
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <circle cx="48" cy="48" r="48" fill="#F5F3FF"/>
                                <path d="M32 64c0-8.837 7.163-16 16-16s16 7.163 16 16" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round"/>
                                <circle cx="48" cy="36" r="8" stroke="#7C3AED" stroke-width="2.5"/>
                                <path d="M62 44l4 4-4 4M62 48h8" stroke="#C4B5FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M24 56h4M24 60h8M24 64h6" stroke="#DDD6FE" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pendaftar</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-6">
                            Belum ada volunteer yang mendaftar ke event ini. Pendaftar yang masuk akan muncul secara otomatis di halaman ini.
                        </p>
                        <a href="{{ route('eo.events.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Lihat Event Saya
                        </a>
                    @endif
                </div>

            @else
                {{-- ====================================================
                     Applicants Table
                ==================================================== --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Relawan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Divisi Pilihan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Melamar</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Berkas Relawan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Keputusan / Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($applications as $application)
                                @php
                                    $volunteer = $application->volunteerProfile;
                                    $user = $volunteer?->user;
                                    $initials = strtoupper(substr($user?->name ?? 'V', 0, 2));
                                    $colors = ['bg-brand-100 text-brand-700', 'bg-amber-100 text-amber-700', 'bg-green-100 text-green-700', 'bg-rose-100 text-rose-700', 'bg-indigo-100 text-indigo-700'];
                                    $color = $colors[$application->id % count($colors)];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- Relawan --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full {{ $color }} flex items-center justify-center shrink-0 text-sm font-semibold">
                                                {{ $initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 truncate max-w-[160px]">{{ $user?->name ?? '—' }}</p>
                                                @if ($volunteer?->institution)
                                                    <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $volunteer->institution }}</p>
                                                @endif
                                                @if ($volunteer?->city)
                                                    <p class="text-xs text-gray-400">{{ $volunteer->city }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Divisi Pilihan --}}
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-brand-50 text-brand-700 text-xs font-medium">
                                            {{ $application->position?->name ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Tanggal Melamar --}}
                                    <td class="px-5 py-4 text-xs text-gray-600 whitespace-nowrap">
                                        {{ $application->applied_at?->translatedFormat('d M Y') ?? $application->created_at->translatedFormat('d M Y') }}
                                    </td>

                                    {{-- Berkas Relawan --}}
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-1">
                                            @if ($volunteer?->cv_path)
                                                <a href="{{ Storage::url($volunteer->cv_path) }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1.5 text-xs text-brand-600 hover:text-brand-700 font-medium transition-colors">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    CV.pdf
                                                </a>
                                            @endif
                                            @if ($volunteer?->portfolio_path)
                                                <a href="{{ Storage::url($volunteer->portfolio_path) }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Portofolio
                                                </a>
                                            @endif
                                            @if (! $volunteer?->cv_path && ! $volunteer?->portfolio_path)
                                                <span class="text-xs text-gray-400 italic">Tidak ada berkas</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status Badge --}}
                                    <td class="px-5 py-4">
                                        @php $statusValue = $application->status->value; @endphp
                                        @if ($statusValue === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu Review
                                            </span>
                                        @elseif ($statusValue === 'accepted')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                Diterima
                                            </span>
                                        @elseif ($statusValue === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                                Ditolak
                                            </span>
                                        @elseif ($statusValue === 'withdrawn')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                                Dicabut
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2"
                                             x-data="{ showRejectModal: false }">

                                            @if ($statusValue === 'pending')
                                                {{-- Accept --}}
                                                <form method="POST"
                                                      action="{{ route('eo.applicants.accept', $application) }}"
                                                      x-data
                                                      x-on:submit.prevent="if(confirm('Terima pendaftaran {{ addslashes($user?->name ?? 'relawan ini') }}?')) $el.submit()">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                                        Terima
                                                    </button>
                                                </form>

                                                {{-- Reject --}}
                                                <button
                                                    type="button"
                                                    x-on:click="showRejectModal = true"
                                                    class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-medium rounded-lg border border-red-200 hover:bg-red-100 transition-colors">
                                                    Tolak
                                                </button>

                                            @elseif ($statusValue === 'accepted')
                                                {{-- Revert: cancel acceptance --}}
                                                <form method="POST"
                                                      action="{{ route('eo.applicants.reject', $application) }}"
                                                      x-data
                                                      x-on:submit.prevent="if(confirm('Batalkan penerimaan {{ addslashes($user?->name ?? 'relawan ini') }}? Status akan berubah menjadi Ditolak.')) $el.submit()">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision_note" value="Penerimaan dibatalkan oleh EO.">
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors border border-gray-200">
                                                        Batal Terima
                                                    </button>
                                                </form>

                                            @elseif ($statusValue === 'rejected')
                                                {{-- Reconsider: accept again --}}
                                                <form method="POST"
                                                      action="{{ route('eo.applicants.accept', $application) }}"
                                                      x-data
                                                      x-on:submit.prevent="if(confirm('Terima ulang pendaftaran {{ addslashes($user?->name ?? 'relawan ini') }}?')) $el.submit()">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-brand-50 text-brand-600 text-xs font-medium rounded-lg border border-brand-200 hover:bg-brand-100 transition-colors">
                                                        Tinjau Ulang
                                                    </button>
                                                </form>

                                            @elseif ($statusValue === 'withdrawn')
                                                <span class="text-xs text-gray-400 italic">Dicabut oleh relawan</span>
                                            @endif

                                            {{-- Reject Modal --}}
                                            @if ($statusValue === 'pending')
                                                <div
                                                    x-show="showRejectModal"
                                                    x-cloak
                                                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                    x-on:keydown.escape.window="showRejectModal = false"
                                                >
                                                    <div class="absolute inset-0 bg-black/40" x-on:click="showRejectModal = false"></div>
                                                    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
                                                         x-transition:enter="transition ease-out duration-200"
                                                         x-transition:enter-start="opacity-0 scale-95"
                                                         x-transition:enter-end="opacity-100 scale-100">
                                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Tolak Pendaftar</h3>
                                                        <p class="text-sm text-gray-500 mb-4">
                                                            Anda akan menolak pendaftaran <strong>{{ $user?->name }}</strong>. Tindakan ini dapat dibatalkan kemudian.
                                                        </p>
                                                        <form method="POST" action="{{ route('eo.applicants.reject', $application) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="mb-4">
                                                                <label for="decision_note_{{ $application->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                    Alasan Penolakan <span class="text-gray-400 font-normal">(opsional)</span>
                                                                </label>
                                                                <textarea
                                                                    id="decision_note_{{ $application->id }}"
                                                                    name="decision_note"
                                                                    rows="3"
                                                                    placeholder="Contoh: Kuota posisi sudah terpenuhi."
                                                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"
                                                                ></textarea>
                                                            </div>
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button"
                                                                        x-on:click="showRejectModal = false"
                                                                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                                    Batal
                                                                </button>
                                                                <button type="submit"
                                                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                                                    Ya, Tolak
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($applications->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-sm text-gray-500">
                            Menampilkan
                            <span class="font-medium">{{ $applications->firstItem() }}–{{ $applications->lastItem() }}</span>
                            dari <span class="font-medium">{{ $applications->total() }}</span> pendaftar
                        </p>
                        <div>
                            {{ $applications->links() }}
                        </div>
                    </div>
                @else
                    <div class="px-5 py-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500">
                            Menampilkan <span class="font-medium">{{ $applications->total() }}</span> pendaftar
                        </p>
                    </div>
                @endif
            @endif

        </div>{{-- /Main Table Card --}}

    @endif{{-- /STATE 3 --}}

</x-layouts.eo>

