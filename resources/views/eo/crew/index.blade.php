<x-layouts.eo title="Koordinasi Crew — Vola">

    @php
        use App\Http\Controllers\Eo\CrewCoordinationController;
    @endphp

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Profil Organisasi Diperlukan</h2>
            <p class="text-gray-500 max-w-sm mx-auto mb-6">Lengkapi profil organisasi Anda untuk mulai mengelola crew event.</p>
            <a href="{{ route('eo.organization') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                Lengkapi Profil
            </a>
        </div>

    {{-- ================================================================
         STATE 2: EO belum memiliki event
    ================================================================ --}}
    @elseif ($events->isEmpty())
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Koordinasi Crew &amp; Relawan Diterima</h1>
            <p class="text-gray-500 mt-1">Daftar resmi relawan yang telah lolos seleksi. Hubungi dan koordinasikan jadwal.</p>
        </div>
        <x-card>
            <div class="text-center py-16">
                <div class="mx-auto w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Event</h2>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">Buat event terlebih dahulu agar volunteer dapat mendaftar dan crew dapat dikelola di sini.</p>
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
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Koordinasi Crew &amp; Relawan Diterima</h1>
                    <p class="text-gray-500 mt-1">
                        Daftar resmi relawan yang telah lolos seleksi. Hubungi dan koordinasikan jadwal.
                        @if ($selectedEvent)
                            Event: <span class="font-semibold text-gray-700">{{ $selectedEvent->title }}</span>
                        @endif
                    </p>
                </div>

                {{-- Event Selector --}}
                @if ($events->count() > 1)
                    <div class="shrink-0">
                        <form method="GET" action="{{ route('eo.crew.index') }}" id="event-form">
                            <label for="event_id" class="sr-only">Pilih Event</label>
                            <select
                                id="event_id"
                                name="event_id"
                                onchange="document.getElementById('event-form').submit()"
                                class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500"
                            >
                                @foreach ($events as $evt)
                                    <option value="{{ $evt->id }}" {{ $selectedEvent?->id === $evt->id ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::limit($evt->title, 45) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Stats + Download bar --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">

                {{-- Total diterima --}}
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Relawan Diterima</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalAccepted) }}</p>
                        <p class="text-xs text-gray-400">Relawan</p>
                    </div>
                </div>

                <div class="hidden sm:block w-px h-14 bg-gray-100"></div>

                {{-- Sisa Kuota --}}
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sisa Kuota Terbuka</p>
                        @if ($positions->isEmpty())
                            <p class="text-3xl font-bold text-gray-400">—</p>
                        @else
                            <p class="text-3xl font-bold text-amber-600">{{ $openQuotaCount }} Posisi</p>
                            <p class="text-xs text-gray-400">
                                @php
                                    $positionsDetail = $positions->filter(fn($p) => $p->quota !== null && ($p->quota - $p->accepted_count) > 0);
                                @endphp
                                @if ($positionsDetail->isNotEmpty())
                                    {{ $positionsDetail->map(fn($p) => $p->name.' ('.($p->quota - $p->accepted_count).')')->implode(', ') }}
                                @else
                                    Semua kuota terpenuhi
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="hidden sm:block w-px h-14 bg-gray-100"></div>

                {{-- Download --}}
                <div class="shrink-0">
                    @if ($selectedEvent && $totalAccepted > 0)
                        <a href="{{ route('eo.crew.export', array_filter(['event_id' => $selectedEvent->id, 'position_id' => $filterPositionId])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Unduh Data Relawan (CSV)
                        </a>
                    @else
                        <button disabled
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-200 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Unduh Data Relawan (CSV)
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Position Filter Chips --}}
        @if ($positions->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-6">
                {{-- All filter --}}
                <a href="{{ route('eo.crew.index', array_filter(['event_id' => $selectedEvent?->id])) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium border transition-colors
                       {{ ! $filterPositionId ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300 hover:text-brand-600' }}">
                    Semua Divisi
                    <span class="text-xs {{ ! $filterPositionId ? 'opacity-80' : 'text-gray-400' }}">({{ $totalAccepted }})</span>
                </a>

                @foreach ($positions as $pos)
                    <a href="{{ route('eo.crew.index', array_filter(['event_id' => $selectedEvent?->id, 'position_id' => $pos->id])) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium border transition-colors
                           {{ (string) $filterPositionId === (string) $pos->id ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300 hover:text-brand-600' }}">
                        {{ $pos->name }}
                        <span class="text-xs {{ (string) $filterPositionId === (string) $pos->id ? 'opacity-80' : 'text-gray-400' }}">({{ $pos->accepted_count }})</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ================================================================
             STATE 3a: Belum ada crew yang diterima (no accepted yet)
        ================================================================ --}}
        @if ($crewByPosition->isEmpty())
            <x-card>
                <div class="text-center py-20 px-6">
                    @if ($filterPositionId)
                        {{-- Empty: filter has no results --}}
                        <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Crew pada Divisi Ini</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-6">
                            Belum ada relawan yang diterima untuk divisi yang dipilih.
                        </p>
                        <a href="{{ route('eo.crew.index', array_filter(['event_id' => $selectedEvent?->id])) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Lihat Semua Divisi
                        </a>
                    @else
                        {{-- Empty: no crew accepted at all --}}
                        <div class="mx-auto w-24 h-24 mb-6">
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <circle cx="48" cy="48" r="48" fill="#F5F3FF"/>
                                <circle cx="30" cy="38" r="8" stroke="#A78BFA" stroke-width="2"/>
                                <circle cx="66" cy="38" r="8" stroke="#A78BFA" stroke-width="2"/>
                                <circle cx="48" cy="34" r="10" stroke="#7C3AED" stroke-width="2.5"/>
                                <path d="M18 68c0-6.627 5.373-12 12-12M66 56c6.627 0 12 5.373 12 12" stroke="#C4B5FD" stroke-width="2" stroke-linecap="round"/>
                                <path d="M30 68c0-9.941 8.059-18 18-18s18 8.059 18 18" stroke="#7C3AED" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M56 52l4 4-4 4" stroke="#DDD6FE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Crew yang Diterima</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-6">
                            Volunteer yang berhasil lolos seleksi akan muncul di halaman ini. Mulai lakukan seleksi pada menu Pendaftar.
                        </p>
                        <a href="{{ route('eo.applicants.index', array_filter(['event_id' => $selectedEvent?->id])) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Kelola Pendaftar
                        </a>
                    @endif
                </div>
            </x-card>

        @else
        {{-- ================================================================
             STATE 3b: Ada crew yang diterima — tampilkan per posisi
        ================================================================ --}}

            {{-- Loop setiap posisi yang memiliki crew --}}
            @foreach ($crewByPosition as $positionId => $applications)
                @php
                    $position = $applications->first()->position;
                    $quota = $position?->quota;
                    $acceptedInPos = $applications->count();
                    $remaining = $quota !== null ? $quota - $acceptedInPos : null;
                    $isFull = $quota !== null && $remaining <= 0;

                    $avatarColors = [
                        'bg-brand-100 text-brand-700',
                        'bg-amber-100 text-amber-700',
                        'bg-green-100 text-green-700',
                        'bg-rose-100 text-rose-700',
                        'bg-indigo-100 text-indigo-700',
                        'bg-teal-100 text-teal-700',
                        'bg-orange-100 text-orange-700',
                    ];
                @endphp

                <div class="mb-8">
                    {{-- Section header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-brand-500 inline-block"></span>
                            <h2 class="text-base font-semibold text-gray-900">
                                {{ $position?->name ?? 'Posisi Tidak Diketahui' }}
                                <span class="text-gray-400 font-normal text-sm">— {{ $acceptedInPos }} Relawan Diterima</span>
                            </h2>
                        </div>
                        @if ($quota !== null)
                            @if ($isFull)
                                <span class="text-xs font-medium px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full">
                                    Kuota Terpenuhi
                                </span>
                            @else
                                <span class="text-xs font-medium px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                                    Sisa {{ $remaining }} Kuota
                                </span>
                            @endif
                        @else
                            <span class="text-xs font-medium px-3 py-1 bg-gray-100 text-gray-500 border border-gray-200 rounded-full">
                                Tidak Dibatasi
                            </span>
                        @endif
                    </div>

                    {{-- Crew Cards Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($applications as $application)
                            @php
                                $volunteer = $application->volunteerProfile;
                                $user = $volunteer?->user;
                                $name = $user?->name ?? 'Tanpa Nama';
                                $initials = strtoupper(mb_substr($name, 0, 1)).strtoupper(mb_substr(strstr($name, ' ') ?: ' ', 1, 1));
                                $colorClass = $avatarColors[$application->id % count($avatarColors)];

                                $rawPhone = $user?->phone ?? '';
                                $normalizedPhone = \App\Http\Controllers\Eo\CrewCoordinationController::normalizePhone($rawPhone);
                                $hasPhone = $normalizedPhone !== '';
                                $whatsappUrl = $hasPhone
                                    ? \App\Http\Controllers\Eo\CrewCoordinationController::buildWhatsAppUrl($rawPhone, $name, $selectedEvent?->title ?? '')
                                    : '';
                            @endphp

                            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">

                                {{-- Volunteer info --}}
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full {{ $colorClass }} flex items-center justify-center shrink-0 text-sm font-semibold">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $name }}</p>
                                        @if ($volunteer?->institution)
                                            <p class="text-xs text-gray-400 truncate">{{ $volunteer->institution }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Details --}}
                                <div class="space-y-1.5 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-xs">Posisi:</span>
                                        <span class="font-medium text-gray-700 text-xs text-right">{{ $position?->name ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-xs">WhatsApp:</span>
                                        @if ($hasPhone)
                                            <span class="font-medium text-gray-700 text-xs">+{{ $normalizedPhone }}</span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum tersedia</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- WhatsApp Button --}}
                                @if ($hasPhone)
                                    <a href="{{ $whatsappUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        {{-- WhatsApp icon --}}
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        Hubungi WhatsApp
                                    </a>
                                @else
                                    <button disabled
                                            title="Nomor WhatsApp belum tersedia"
                                            class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gray-200 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        Nomor WhatsApp Belum Tersedia
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        @endif {{-- end crew empty check --}}

    @endif {{-- end STATE 3 --}}

</x-layouts.eo>

