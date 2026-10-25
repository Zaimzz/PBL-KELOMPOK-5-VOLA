<x-layouts.admin title="Event Aktif">

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Event Aktif</h1>
                <p class="text-gray-500 text-sm mt-1">Direktori seluruh event yang sedang berlangsung atau telah dipublikasikan di platform VOLA.</p>
            </div>

            <!-- Top Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Total Event Aktif -->
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-brand-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['total_active'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 font-medium">Total Event Aktif</div>
                    </div>
                </div>

                <!-- Relawan Terdaftar -->
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['registered_volunteers'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 font-medium">Relawan Terdaftar</div>
                    </div>
                </div>

                <!-- Buka Pendaftaran -->
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['open_registration'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 font-medium">Buka Pendaftaran</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Events Container (Alpine.js State for Detail Modal) -->
    <div x-data="activeEventsManager()" class="space-y-6">

        <!-- Search & Filter Card -->
        <x-card class="p-3">
            <form action="{{ route('admin.active-events.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama event, penyelenggara, atau lokasi..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                <!-- Dropdown Kategori -->
                <div class="w-full md:w-56">
                    <select name="category" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Status -->
                <div class="w-full md:w-52">
                    <select name="status" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="buka_rekrutmen" {{ request('status') == 'buka_rekrutmen' ? 'selected' : '' }}>Buka Rekrutmen</option>
                        <option value="sedang_berjalan" {{ request('status') == 'sedang_berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <a href="{{ route('admin.active-events.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold transition-colors shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </form>
        </x-card>

        <!-- Events Table Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3.5 px-6">Nama Event</th>
                            <th class="py-3.5 px-6">Penyelenggara</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Tanggal & Lokasi</th>
                            <th class="py-3.5 px-6 min-w-[200px]">Total Relawan</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($events as $event)
                            @php
                                $totalQuota = $event->positions ? $event->positions->sum('quota') : 0;
                                $acceptedVolunteers = $event->positions ? $event->positions->sum(fn($p) => $p->applications->count()) : 0;
                                $progressPercent = $totalQuota > 0 ? min(100, round(($acceptedVolunteers / $totalQuota) * 100)) : 0;
                                
                                $today = now()->toDateString();
                                $isRegOpen = $event->status === \App\Enums\EventStatus::Active && 
                                    (!$event->registration_deadline || $event->registration_deadline->toDateString() >= $today);
                                
                                if ($event->status === \App\Enums\EventStatus::Finished || ($event->end_date && $event->end_date->toDateString() < $today)) {
                                    $statusType = 'selesai';
                                    $statusText = 'Selesai';
                                } elseif ($isRegOpen) {
                                    $statusType = 'buka_rekrutmen';
                                    $statusText = 'Buka Rekrutmen';
                                } else {
                                    $statusType = 'sedang_berjalan';
                                    $statusText = 'Sedang Berjalan';
                                }

                                $eventYear = $event->start_date ? $event->start_date->format('Y') : now()->format('Y');
                                $eventCode = 'ID: EVT-' . $eventYear . '-' . str_pad($event->id, 4, '0', STR_PAD_LEFT);
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- Nama Event & ID -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-xl bg-slate-900 overflow-hidden shrink-0 shadow-sm relative">
                                            @if($event->poster_path)
                                                <img src="{{ Storage::url($event->poster_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-indigo-900 to-slate-900 flex items-center justify-center text-brand-300 font-bold text-xs">
                                                    {{ strtoupper(substr($event->title, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-1">{{ $event->title }}</h3>
                                            <p class="text-xs font-semibold text-brand-600 mt-0.5">{{ $eventCode }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Penyelenggara -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900 text-sm line-clamp-1">
                                        {{ $event->organizer->organization_name ?? 'Penyelenggara' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $event->organizer->organization_type ?? 'EO Terverifikasi' }}
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700">
                                        {{ $event->category?->name ?? 'Umum' }}
                                    </span>
                                </td>

                                <!-- Tanggal & Lokasi -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-800">
                                        @if($event->start_date && $event->end_date && !$event->start_date->equalTo($event->end_date))
                                            {{ $event->start_date->translatedFormat('d') }} – {{ $event->end_date->translatedFormat('d M Y') }}
                                        @elseif($event->start_date)
                                            {{ $event->start_date->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate max-w-[140px]">{{ $event->city ?: $event->location }}</span>
                                    </div>
                                </td>

                                <!-- Total Relawan & Progress Bar -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <span class="{{ $progressPercent >= 100 ? 'text-emerald-700' : 'text-gray-800' }}">
                                            {{ $acceptedVolunteers }}/{{ $totalQuota }} Relawan
                                            @if($progressPercent >= 100)
                                                <span class="text-[10px] text-emerald-600 font-semibold">(Penuh)</span>
                                            @endif
                                        </span>
                                        <span class="{{ $progressPercent >= 100 ? 'text-emerald-600' : 'text-gray-600' }}">{{ $progressPercent }}%</span>
                                    </div>
                                    <!-- Dynamic Progress Bar -->
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $statusType === 'selesai' ? 'bg-gray-400' : ($progressPercent >= 100 ? 'bg-emerald-500' : 'bg-brand-600') }}"
                                             style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($statusType === 'buka_rekrutmen')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700 border border-indigo-200/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                                            Buka Rekrutmen
                                        </span>
                                    @elseif($statusType === 'sedang_berjalan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Sedang Berjalan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                            Selesai
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button"
                                            x-on:click="openDetailModal({{ $event->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-xl transition-colors">
                                        Lihat Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center">
                                    <x-empty-state 
                                        title="Tidak ada event aktif ditemukan" 
                                        description="Tidak ada data event aktif yang sesuai dengan kata kunci pencarian atau filter yang dipilih." 
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="py-4 px-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-500">
                    Menampilkan <span class="font-semibold">{{ $events->firstItem() ?? 0 }}</span> - <span class="font-semibold">{{ $events->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $events->total() }}</span> event aktif
                </div>
                <div>
                    {{ $events->links() }}
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL DETAIL EVENT AKTIF                       -->
        <!-- ============================================== -->
        <x-modal name="active-event-detail-modal" :show="false" maxWidth="2xl">
            <div class="p-6">
                <template x-if="loading">
                    <div class="py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs">Memuat detail event...</p>
                    </div>
                </template>

                <template x-if="!loading && currentEvent">
                    <div>
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between pb-4 border-b border-gray-100 mb-5">
                            <div>
                                <span class="text-xs font-bold text-brand-600" x-text="currentEvent.event_code"></span>
                                <h2 class="text-xl font-extrabold text-gray-900 mt-0.5" x-text="currentEvent.title"></h2>
                                <p class="text-xs text-gray-500" x-text="'Penyelenggara: ' + currentEvent.organizer_name"></p>
                            </div>
                            <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Grid Details -->
                        <div class="space-y-4 text-sm max-h-[70vh] overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl">
                                <div>
                                    <span class="text-xs text-gray-500 font-medium">Kategori</span>
                                    <p class="font-bold text-gray-900 mt-0.5" x-text="currentEvent.category"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-medium">Status Event</span>
                                    <p class="font-bold text-gray-900 mt-0.5" x-text="currentEvent.status_badge"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-medium">Pelaksanaan</span>
                                    <p class="font-bold text-gray-900 mt-0.5" x-text="currentEvent.start_date + ' – ' + currentEvent.end_date"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-medium">Batas Registrasi</span>
                                    <p class="font-bold text-gray-900 mt-0.5" x-text="currentEvent.registration_deadline"></p>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-xs text-gray-500 font-medium">Lokasi</span>
                                    <p class="font-bold text-gray-900 mt-0.5" x-text="currentEvent.full_location"></p>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Event</h4>
                                <p class="text-xs text-gray-600 leading-relaxed" x-text="currentEvent.description"></p>
                            </div>

                            <!-- Kuota & Posisi Relawan -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kebutuhan Relawan</h4>
                                    <span class="text-xs font-bold text-brand-600" x-text="currentEvent.accepted_count + ' / ' + currentEvent.total_quota + ' Terisi (' + currentEvent.progress_percent + '%)'"></span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <template x-for="pos in currentEvent.positions" :key="pos.name">
                                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-800" x-text="pos.name"></span>
                                            <span class="text-xs font-bold text-brand-600" x-text="pos.filled + ' / ' + pos.quota + ' Kuota'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Benefit -->
                            <template x-if="currentEvent.benefits && currentEvent.benefits.length > 0">
                                <div class="bg-emerald-50/80 border border-emerald-100 rounded-xl p-3.5">
                                    <h4 class="text-xs font-bold text-emerald-800 mb-1">Benefit Relawan</h4>
                                    <p class="text-xs text-emerald-700" x-text="currentEvent.benefits.join(', ')"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Modal Footer -->
                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Tutup
                            </x-secondary-button>
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

                async openDetailModal(eventId) {
                    this.loading = true;
                    this.$dispatch('open-modal', 'active-event-detail-modal');

                    try {
                        const response = await fetch(`/admin/active-events/${eventId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            this.currentEvent = await response.json();
                        }
                    } catch (error) {
                        console.error('Gagal memuat detail event aktif:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>

</x-layouts.admin>
