<x-layouts.admin title="Event Aktif">

    <!-- Header Section -->
    <div class="mb-6 relative z-10 opacity-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border mb-2.5 bg-emerald-50 text-emerald-700 border-emerald-200/70">
                    <span class="relative flex h-2 w-2 mr-0.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                    </span>
                    <span>DIREKTORI PLATFORM • EVENT PUBLIK</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Event <span class="text-amber-600">Aktif</span></h1>
                <p class="text-slate-600 text-sm mt-1">Direktori seluruh event yang sedang berlangsung atau telah dipublikasikan di platform VOLA.</p>
            </div>

            <!-- Top Metrics Cards -->
            <div id="admin-metrics-container" class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto shrink-0">
                <!-- Total Event Aktif -->
                <x-metric-card label="Total Event Aktif" :value="number_format($stats['total_active'], 0, ',', '.')" bg="bg-indigo-50" text="text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </x-metric-card>

                <!-- Crew Terdaftar -->
                <x-metric-card label="Crew Terdaftar" :value="number_format($stats['registered_volunteers'], 0, ',', '.')" bg="bg-blue-50" text="text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </x-metric-card>

                <!-- Buka Pendaftaran -->
                <x-metric-card label="Buka Pendaftaran" :value="number_format($stats['open_registration'], 0, ',', '.')" bg="bg-amber-50" text="text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </x-metric-card>
            </div>
        </div>
    </div>

    <!-- Active Events Container (Alpine.js State for Detail Modal) -->
    <div id="admin-content-container" x-data="activeEventsManager()" class="w-full max-w-full min-w-0 space-y-6">

        <!-- Search & Filter Card -->
        <x-card class="p-3">
            <form action="{{ route('admin.active-events.index') }}" method="GET"
                class="flex flex-col md:flex-row items-center gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama event aktif, lokasi kota, atau penyelenggara"
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                <!-- Dropdown Kategori -->
                @php
                    $catOptions = [['value' => 'all', 'label' => 'Semua Kategori']];
                    foreach ($categories as $category) {
                        $catOptions[] = [
                            'value' => (string) $category->id,
                            'label' => $category->name,
                        ];
                    }
                @endphp
                <div x-data="{
                    open: false,
                    selected: '{{ (string) request('category', 'all') }}',
                    options: {{ \Illuminate\Support\Js::from($catOptions) }},
                    get selectedLabel() {
                        const found = this.options.find(item => item.value === this.selected);
                        return found ? found.label : 'Semua Kategori';
                    },
                    select(val) {
                        this.selected = val;
                        this.open = false;
                        $nextTick(() => { window.adminSubmitForm(this.$refs.hiddenCategory.form); });
                    }
                }" class="relative w-full md:w-56 shrink-0" @click.away="open = false">

                    <input type="hidden" name="category" :value="selected" x-ref="hiddenCategory">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl text-sm text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-left">
                        <span class="truncate" x-text="selectedLabel"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                            :class="{ 'rotate-180 text-brand-600': open }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute left-0 right-0 mt-1.5 py-1.5 bg-white border border-gray-100 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto"
                        style="display: none;" x-cloak>
                        <template x-for="item in options" :key="item.value">
                            <button type="button" @click="select(item.value)"
                                class="w-full flex items-center justify-between px-3.5 py-2 text-xs sm:text-sm text-left transition-colors"
                                :class="selected === item.value ? 'bg-brand-50 text-brand-700 font-bold' :
                                    'text-gray-700 hover:bg-gray-50'">
                                <span x-text="item.label" class="truncate"></span>
                                <svg x-show="selected === item.value" class="w-4 h-4 text-brand-600 shrink-0 ml-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Dropdown Status -->
                <div x-data="{
                    open: false,
                    selected: '{{ request('status', 'all') }}',
                    options: {
                        'all': 'Semua Status',
                        'buka_rekrutmen': 'Buka Rekrutmen',
                        'sedang_berjalan': 'Sedang Berjalan',
                        'selesai': 'Selesai'
                    },
                    select(val) {
                        this.selected = val;
                        this.open = false;
                        $nextTick(() => { window.adminSubmitForm(this.$refs.hiddenStatus.form); });
                    }
                }" class="relative w-full md:w-52 shrink-0" @click.away="open = false">

                    <input type="hidden" name="status" :value="selected" x-ref="hiddenStatus">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl text-sm text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-left">
                        <span class="truncate" x-text="options[selected] || 'Semua Status'"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                            :class="{ 'rotate-180 text-brand-600': open }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute left-0 right-0 mt-1.5 py-1.5 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden"
                        style="display: none;" x-cloak>
                        <template x-for="(label, key) in options" :key="key">
                            <button type="button" @click="select(key)"
                                class="w-full flex items-center justify-between px-3.5 py-2 text-xs sm:text-sm text-left transition-colors"
                                :class="selected === key ? 'bg-brand-50 text-brand-700 font-bold' :
                                    'text-gray-700 hover:bg-gray-50'">
                                <span x-text="label"></span>
                                <svg x-show="selected === key" class="w-4 h-4 text-brand-600 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Reset Button -->
                @php
                    $hasActiveFilter = (request('search') && trim(request('search')) !== '')
                        || (request('category') && request('category') !== 'all')
                        || (request('status') && request('status') !== 'all');
                @endphp

                @if($hasActiveFilter)
                    <a href="{{ route('admin.active-events.index') }}" 
                        data-reset="true"
                        x-data="{ resetting: false }" 
                        @click="resetting = true"
                        class="group inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800 active:scale-95 transition-all shrink-0">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:-rotate-45 shrink-0"
                            :class="{ 'animate-spin text-brand-600': resetting }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </x-card>

        <!-- Events Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden w-full max-w-full min-w-0">
            <div class="overflow-x-auto w-full">
                <table class="table-fixed w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3.5 px-4 w-[24%]">Nama Event</th>
                            <th class="py-3.5 px-3 w-[13%]">Penyelenggara</th>
                            <th class="py-3.5 px-2 w-[10%] text-center">Kategori</th>
                            <th class="py-3.5 px-3 w-[14%]">Tanggal & Lokasi</th>
                            <th class="py-3.5 px-3 w-[13%]">Total Crew</th>
                            <th class="py-3.5 px-2 w-[15%] text-center">Status</th>
                            <th class="py-3.5 px-3 w-[11%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($events as $event)
                            @php
                                $totalQuota = $event->positions ? $event->positions->sum('quota') : 0;
                                $acceptedVolunteers = $event->positions
                                    ? $event->positions->sum(fn($p) => $p->applications->count())
                                    : 0;
                                $progressPercent =
                                    $totalQuota > 0 ? min(100, round(($acceptedVolunteers / $totalQuota) * 100)) : 0;

                                $today = now()->toDateString();
                                $isFinished =
                                    $event->status === \App\Enums\EventStatus::Finished ||
                                    ($event->end_date && $event->end_date->toDateString() < $today);

                                $isRegOpen =
                                    $event->status === \App\Enums\EventStatus::Active &&
                                    (!$event->end_date || $event->end_date->toDateString() >= $today) &&
                                    (!$event->registration_deadline ||
                                        $event->registration_deadline->toDateString() >= $today);

                                if ($isFinished) {
                                    $statusType = 'selesai';
                                    $statusText = 'Selesai';
                                } elseif ($isRegOpen) {
                                    $statusType = 'buka_rekrutmen';
                                    $statusText = 'Buka Rekrutmen';
                                } elseif ($event->status === \App\Enums\EventStatus::Closed) {
                                    $statusType = 'closed';
                                    $statusText = 'Ditutup';
                                } else {
                                    $statusType = 'sedang_berjalan';
                                    $statusText = 'Sedang Berjalan';
                                }

                                $eventYear = $event->start_date ? $event->start_date->format('Y') : now()->format('Y');
                                $eventCode = 'ID: EVT-' . $eventYear . '-' . str_pad($event->id, 4, '0', STR_PAD_LEFT);
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- Nama Event & ID -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-11 h-11 rounded-xl bg-slate-900 overflow-hidden shrink-0 shadow-sm relative">
                                            @if ($event->poster_path)
                                                <img src="{{ $event->poster_url }}"
                                                    alt="{{ $event->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="w-full h-full bg-gradient-to-br from-indigo-900 to-slate-900 flex items-center justify-center text-brand-300 font-bold text-xs">
                                                    {{ strtoupper(substr($event->title, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="font-bold text-gray-900 text-sm leading-snug truncate" title="{{ $event->title }}">
                                                {{ $event->title }}</h3>
                                            <p class="text-xs font-semibold text-brand-600 mt-0.5 truncate">{{ $eventCode }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Penyelenggara -->
                                <td class="py-4 px-3">
                                    <div class="font-bold text-gray-900 text-sm truncate" title="{{ $event->organizer->organization_name ?? 'Penyelenggara' }}">
                                        {{ $event->organizer->organization_name ?? 'Penyelenggara' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5 truncate">
                                        {{ $event->organizer->organization_type ?? 'EO Terverifikasi' }}
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-4 px-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700 max-w-full truncate">
                                        {{ $event->category?->name ?? 'Umum' }}
                                    </span>
                                </td>

                                <!-- Tanggal & Lokasi -->
                                <td class="py-4 px-3">
                                    <div class="text-xs font-bold text-gray-800 whitespace-nowrap">
                                        @if ($event->start_date && $event->end_date && !$event->start_date->equalTo($event->end_date))
                                            {{ $event->start_date->translatedFormat('d') }} –
                                            {{ $event->end_date->translatedFormat('d M Y') }}
                                        @elseif($event->start_date)
                                            {{ $event->start_date->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5 min-w-0">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $event->city ?: $event->location }}</span>
                                    </div>
                                </td>

                                <!-- Total Crew & Progress Bar -->
                                <td class="py-4 px-3">
                                    <div class="flex items-center justify-between gap-1 text-xs font-bold mb-1.5">
                                        <span
                                            class="truncate {{ $progressPercent >= 100 ? 'text-emerald-700' : 'text-gray-800' }}">
                                            {{ $acceptedVolunteers }}/{{ $totalQuota }} Crew
                                            @if ($progressPercent >= 100)
                                                <span class="text-[10px] text-emerald-600 font-semibold">(Penuh)</span>
                                            @endif
                                        </span>
                                        <span
                                            class="shrink-0 {{ $progressPercent >= 100 ? 'text-emerald-600' : 'text-gray-600' }}">{{ $progressPercent }}%</span>
                                    </div>
                                    <!-- Dynamic Progress Bar -->
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $statusType === 'selesai' ? 'bg-gray-400' : ($progressPercent >= 100 ? 'bg-emerald-500' : 'bg-brand-600') }}"
                                            style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-2 text-center whitespace-nowrap">
                                    @if ($statusType === 'buka_rekrutmen')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-600 shrink-0"></span>
                                            Buka Rekrutmen
                                        </span>
                                    @elseif($statusType === 'sedang_berjalan')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                            Sedang Berjalan
                                        </span>
                                    @elseif($statusType === 'closed')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                            Ditutup
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                            Selesai
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <button type="button" x-on:click="openDetailModal({{ $event->id }})"
                                            class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-xl transition-colors">
                                            Lihat Detail
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center">
                                    <x-empty-state title="Tidak ada event aktif ditemukan"
                                        description="Tidak ada data event aktif yang sesuai dengan kata kunci pencarian atau filter yang dipilih." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $events->withQueryString()->links('components.pagination') }}
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL DETAIL EVENT AKTIF (COMPACT ANTI-SCROLL) -->
        <!-- ============================================== -->
        <x-modal name="active-event-detail-modal" :show="false" maxWidth="2xl">
            <div class="p-5 flex flex-col flex-1 min-h-0 overflow-hidden">
                <template x-if="loading">
                    <div class="py-12 text-center text-gray-400 my-auto">
                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2.5" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs font-medium text-gray-600">Memuat detail event aktif...</p>
                    </div>
                </template>

                <template x-if="!loading && hasError">
                    <div class="py-10 text-center space-y-3.5 my-auto">
                        <div
                            class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Gagal Memuat Detail Event</h4>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="errorMessage"></p>
                        </div>
                        <div>
                            <button type="button" @click="openDetailModal(currentEventId)"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && !hasError && currentEvent">
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        <!-- Modal Header: px-5 py-3.5 style -->
                        <div class="pb-3 border-b border-gray-100 shrink-0">
                            <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full"
                                x-text="currentEvent.event_code"></span>
                            <h2 class="text-base sm:text-lg font-extrabold text-gray-900 mt-1 truncate"
                                x-text="currentEvent.title"></h2>
                            <p class="text-xs text-gray-500 truncate"
                                x-text="'Penyelenggara: ' + currentEvent.organizer_name"></p>
                        </div>

                        <!-- Grid Details (Compact Internal Body) -->
                        <div class="space-y-3 text-xs flex-1 overflow-y-auto overscroll-y-contain min-h-0 py-3 pr-1">
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Kategori</span>
                                    <p class="font-bold text-gray-900 text-xs mt-0.5" x-text="currentEvent.category">
                                    </p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Status Event</span>
                                    <p class="font-bold text-gray-900 text-xs mt-0.5"
                                        x-text="currentEvent.status_badge"></p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Pelaksanaan</span>
                                    <p class="font-bold text-gray-900 text-xs mt-0.5"
                                        x-text="currentEvent.start_date + ' – ' + currentEvent.end_date"></p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-gray-400 font-medium">Batas Registrasi</span>
                                    <p class="font-bold text-gray-900 text-xs mt-0.5"
                                        x-text="currentEvent.registration_deadline"></p>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-[11px] text-gray-400 font-medium">Lokasi</span>
                                    <p class="font-bold text-gray-900 text-xs mt-0.5"
                                        x-text="currentEvent.full_location"></p>
                                </div>
                            </div>

                            <!-- Deskripsi Singkat -->
                            <div>
                                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi
                                    Event</h4>
                                <p class="text-xs text-gray-600 leading-relaxed max-h-16 overflow-y-auto pr-1"
                                    x-text="currentEvent.description"></p>
                            </div>

                            <!-- Kuota & Posisi Crew -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kebutuhan
                                        Crew</h4>
                                    <span class="text-xs font-bold text-brand-600"
                                        x-text="currentEvent.accepted_count + ' / ' + currentEvent.total_quota + ' Terisi (' + currentEvent.progress_percent + '%)'"></span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    <template x-for="pos in currentEvent.positions" :key="pos.name">
                                        <div
                                            class="p-2 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-800"
                                                x-text="pos.name"></span>
                                            <span class="text-xs font-bold text-brand-600"
                                                x-text="pos.filled + ' / ' + pos.quota + ' Kuota'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Benefit -->
                            <template x-if="currentEvent.benefits && currentEvent.benefits.length > 0">
                                <div class="bg-emerald-50/80 border border-emerald-100 rounded-xl p-2.5">
                                    <h4 class="text-[11px] font-bold text-emerald-800 mb-0.5">Benefit Crew</h4>
                                    <p class="text-xs text-emerald-700" x-text="currentEvent.benefits.join(', ')"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </x-modal>

    </div>

    <!-- Alpine.js Manager Script -->
    <script>
        function activeEventsManager() {
            return {
                currentEvent: null,
                loading: false,
                hasError: false,
                errorMessage: '',
                currentEventId: null,

                async openDetailModal(eventId) {
                    this.currentEventId = eventId;
                    this.loading = true;
                    this.hasError = false;
                    this.errorMessage = '';
                    this.currentEvent = null;
                    this.$dispatch('open-modal', 'active-event-detail-modal');

                    try {
                        const response = await fetch(`/admin/active-events/${eventId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat rincian event (HTTP ' + response.status + ')');
                        }

                        this.currentEvent = await response.json();
                    } catch (error) {
                        console.error('Gagal memuat detail event aktif:', error);
                        this.hasError = true;
                        this.errorMessage = error.message || 'Gagal memuat detail event aktif.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>

</x-layouts.admin>