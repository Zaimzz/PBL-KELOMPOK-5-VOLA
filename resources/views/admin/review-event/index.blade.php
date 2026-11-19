<x-layouts.admin title="Review Event">

    <!-- Header & Breadcrumbs -->
    <div class="mb-6 relative z-10 opacity-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border mb-2.5 bg-amber-50 text-amber-700 border-amber-200/70">
                    <span class="relative flex h-2 w-2 mr-0.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span>PANEL KURASI • PENGAJUAN TERBUKA</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Review <span class="text-amber-500">Event</span></h1>
                <p class="text-slate-600 text-sm mt-1">Kurasi dan tinjau kelayakan pengajuan event baru sebelum dipublikasikan ke feed pencarian crew.</p>
            </div>

            <!-- Header Metrics: Luxury Cards -->
            <div id="admin-metrics-container" class="w-full lg:w-auto grid grid-cols-2 gap-3 sm:gap-4">
                <!-- Pengajuan Menunggu -->
                <div class="w-full bg-white border border-slate-200/90 rounded-2xl p-3.5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/10 hover:border-indigo-200/80 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-slate-900">{{ number_format($stats['pending'], 0, ',', '.') }}</div>
                        <div class="text-[11px] text-slate-600 font-medium">Pengajuan Menunggu</div>
                    </div>
                </div>

                <!-- Disetujui Bulan Ini -->
                <div class="w-full bg-white border border-slate-200/90 rounded-2xl p-3.5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/10 hover:border-indigo-200/80 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-slate-900">{{ number_format($stats['approved_this_month'], 0, ',', '.') }}</div>
                        <div class="text-[11px] text-slate-600 font-medium">Disetujui Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div id="admin-content-container" x-data="reviewEventApp(@js($eventsPayload), {{ $selectedEvent ? $selectedEvent['id'] : 'null' }})" class="space-y-6">

        <!-- Grid Layout: Left Panel (Queue) & Right Panel (Detail) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- ========================================== -->
            <!-- PANEL KIRI: DAFTAR ANTREAN KURASI (7 Cols) -->
            <!-- ========================================== -->
            <div class="lg:col-span-7 space-y-4">

                <!-- Header Antrean + Search & Filter dalam Satu Kartu -->
                <x-card class="p-4 space-y-3.5">
                    @php
                        $currentStatus = request('status', 'all');
                        if (empty($currentStatus)) {
                            $currentStatus = 'all';
                        }
                        $hasActiveFilter = (request('search') && trim(request('search')) !== '')
                            || (request('status') && request('status') !== 'all');
                    @endphp

                    <!-- Baris Atas: Judul & Badge Jumlah Item -->
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-base font-bold text-gray-900">Daftar Antrean Kurasi</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700">
                                {{ $events->total() }} Item
                            </span>
                        </div>
                    </div>

                    <!-- Form Search & Filter Status -->
                    <form action="{{ route('admin.review-events.index') }}" method="GET" class="space-y-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari judul pengajuan event, kategori, atau nama penyelenggara"
                                   class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                        </div>

                        <!-- Filter Status Pills: 2x2 di mobile dengan Reset di kanan, 1 baris di desktop (Persis Pola Laporan) -->
                        <div class="flex flex-row items-center justify-between sm:justify-start gap-2.5 w-full shrink-0 transition-all duration-200">
                            <input type="hidden" name="status" id="statusFilterInput" value="{{ $currentStatus }}">

                            <div class="grid grid-cols-2 gap-2 flex-1 sm:flex-initial sm:flex sm:flex-nowrap sm:items-center text-xs">
                                <button type="submit" onclick="document.getElementById('statusFilterInput').value='all'"
                                        class="px-3.5 py-1.5 font-bold rounded-xl transition-all whitespace-nowrap justify-center text-center w-full sm:w-auto {{ in_array($currentStatus, ['all', '']) ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Semua Status
                                </button>
                                <button type="submit" onclick="document.getElementById('statusFilterInput').value='pending_review'"
                                        class="px-3.5 py-1.5 font-bold rounded-xl transition-all whitespace-nowrap justify-center text-center w-full sm:w-auto {{ in_array($currentStatus, ['pending_review', 'pending']) ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Menunggu
                                </button>
                                <button type="submit" onclick="document.getElementById('statusFilterInput').value='active'"
                                        class="px-3.5 py-1.5 font-bold rounded-xl transition-all whitespace-nowrap justify-center text-center w-full sm:w-auto {{ in_array($currentStatus, ['active', 'approved']) ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Disetujui
                                </button>
                                <button type="submit" onclick="document.getElementById('statusFilterInput').value='rejected'"
                                        class="px-3.5 py-1.5 font-bold rounded-xl transition-all whitespace-nowrap justify-center text-center w-full sm:w-auto {{ $currentStatus === 'rejected' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Ditolak
                                </button>
                            </div>

                            @if($hasActiveFilter)
                                <a href="{{ route('admin.review-events.index') }}" 
                                   data-reset="true"
                                   x-data="{ resetting: false }"
                                   @click="resetting = true"
                                   class="group inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-gray-500 hover:text-gray-800 active:scale-95 transition-all whitespace-nowrap shrink-0 self-center">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:-rotate-45 shrink-0"
                                         :class="{ 'animate-spin text-brand-600': resetting }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </x-card>

                <!-- Cards Queue List -->
                <div class="space-y-3">
                    @forelse($events as $event)
                        @php
                            $status = $event->status instanceof \App\Enums\EventStatus ? $event->status->value : (string) $event->status;
                            $totalQuota = $event->positions ? $event->positions->sum('quota') : 0;
                            $isAlreadyReviewed = in_array($status, ['active', 'approved', 'rejected']);
                        @endphp
                        
                        <div 
                            x-on:click="selectEvent({{ $event->id }})"
                            :class="selectedId === {{ $event->id }} 
                                ? 'border-2 border-brand-500 shadow-md ring-4 ring-brand-50 bg-white' 
                                : 'border border-gray-100 bg-white shadow-sm hover:border-indigo-200/80 hover:shadow-lg hover:shadow-indigo-500/10 hover:-translate-y-0.5'"
                            class="rounded-2xl p-5 transition-all duration-200 cursor-pointer relative"
                        >
                            <!-- Top Row: Status Badge, Category, Date, and Action Button -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Status Badge -->
                                    @if($status === 'pending_review')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                                            Menunggu Review
                                        </span>
                                    @elseif($status === 'active' || $status === 'awaiting_payment')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                            Disetujui
                                        </span>
                                    @elseif($status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/70">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @endif

                                    <!-- Category Badge -->
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700">
                                        {{ $event->category?->name ?? 'Umum' }}
                                    </span>

                                    <!-- Date Note -->
                                    <span class="text-xs text-gray-400 font-medium">
                                        @if($status === 'active' && $event->published_at)
                                            Disetujui {{ $event->published_at->translatedFormat('d M Y') }}
                                        @else
                                            Diajukan {{ $event->created_at ? $event->created_at->translatedFormat('d M Y') : '-' }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Button on Card -->
                                <div>
                                    <template x-if="selectedId === {{ $event->id }}">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 text-white text-xs font-semibold rounded-xl shadow-sm">
                                            {{ $isAlreadyReviewed ? 'Detail Dibuka' : 'Sedang Ditinjau' }}
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </span>
                                    </template>
                                    <template x-if="selectedId !== {{ $event->id }}">
                                        <button type="button" 
                                                x-on:click.stop="selectEvent({{ $event->id }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-xl transition-colors">
                                            @if($isAlreadyReviewed)
                                                Detail
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            @else
                                                Tinjau
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Event Header: Thumbnail + Title & Organizer -->
                            <div class="flex items-start gap-3.5 mb-2.5">
                                <div class="w-14 h-14 rounded-xl bg-slate-900 overflow-hidden shrink-0 shadow-xs relative border border-gray-100 group/thumb">
                                    @if($event->poster_url)
                                        <div class="w-full h-full cursor-pointer relative"
                                             x-on:click.stop="window.openAdminLightbox('{{ $event->poster_url }}', '{{ addslashes($event->title) }}', 'Poster Event: {{ addslashes($event->category?->name ?? 'Umum') }}', 'image')"
                                             title="Klik untuk melihat poster penuh">
                                            <img src="{{ $event->poster_url }}" 
                                                 alt="{{ $event->title }}" 
                                                 class="w-full h-full object-cover transition-transform duration-200 group-hover/thumb:scale-110"
                                                 onerror="this.style.display='none'; this.parentElement.nextElementSibling.classList.remove('hidden');">
                                            <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover/thumb:opacity-100 transition-opacity flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0M10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="hidden w-full h-full bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 flex items-center justify-center text-brand-300 font-extrabold text-xs">
                                            {{ strtoupper(substr($event->title, 0, 2)) }}
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 flex items-center justify-center text-brand-300 font-extrabold text-xs">
                                            {{ strtoupper(substr($event->title, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-gray-900 group-hover:text-brand-600 transition-colors line-clamp-2">
                                        {{ $event->title }}
                                    </h3>
                                    <div class="flex items-center gap-1 font-medium text-xs text-gray-600 mt-1">
                                        <svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate">{{ $event->organizer->organization_name ?? 'Penyelenggara' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Row: Category & Crew Quota -->
                            <div class="flex items-center gap-2 text-xs text-gray-500 pt-1 border-t border-gray-50">
                                <span class="font-medium text-brand-600">{{ $event->category?->name ?? 'Umum' }}</span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="font-normal">{{ $totalQuota }} Kuota Crew</span>
                            </div>
                        </div>
                    @empty
                        <x-card class="p-8 text-center">
                            <x-empty-state 
                                title="Tidak ada antrean pengajuan" 
                                description="Tidak ada pengajuan event yang sesuai dengan pencarian atau filter status yang dipilih." 
                            />
                        </x-card>
                    @endforelse
                </div>

                <!-- Pagination Bar -->
                @if ($events->total() > 0)
                    <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-xs">
                        {{ $events->withQueryString()->links('components.pagination') }}
                    </div>
                @endif
            </div>

            <!-- ============================================== -->
            <!-- PANEL KANAN: DETAIL PENGAJUAN EVENT (5 Cols)   -->
            <!-- ============================================== -->
            <div id="event-detail-preview-panel" class="lg:col-span-5">
                <div class="sticky top-20 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

                    <template x-if="selectedEvent">
                        <div>
                            <!-- Header Banner / Thumbnail Image with Clean Gradient -->
                            <div class="relative h-56 sm:h-64 bg-slate-900 overflow-hidden group">
                                <template x-if="selectedEvent && selectedEvent.poster_url && !posterLoadFailed">
                                    <div class="w-full h-full relative cursor-pointer"
                                         :key="'poster-' + selectedEvent.id + '-' + selectedEvent.poster_url"
                                         x-on:click="window.openAdminLightbox(selectedEvent.poster_url, selectedEvent.title, 'Poster Event Kurasi (' + selectedEvent.category + ')', 'image')"
                                         title="Klik untuk memperbesar poster">
                                        <img :src="selectedEvent.poster_url" 
                                             :alt="selectedEvent.title" 
                                             :key="'img-' + selectedEvent.id + '-' + selectedEvent.poster_url"
                                             x-on:load="posterLoadFailed = false"
                                             x-on:error="posterLoadFailed = true"
                                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                        <!-- Hover Action Overlay -->
                                        <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-20">
                                            <span class="px-3.5 py-1.5 rounded-xl bg-slate-900/60 text-white font-bold text-xs backdrop-blur-md border border-white/20 flex items-center gap-1.5 shadow-lg">
                                                <svg class="w-4 h-4 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0M10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                </svg>
                                                Perbesar Poster
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!selectedEvent || !selectedEvent.poster_url || posterLoadFailed">
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 flex items-center justify-center p-6 text-center"
                                         :key="'empty-poster-' + (selectedEvent ? selectedEvent.id : 'none')">
                                        <div class="absolute inset-0 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:16px_16px] opacity-20"></div>
                                        <div class="relative z-10">
                                            <span class="inline-block px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 text-xs font-semibold mb-2" x-text="selectedEvent ? selectedEvent.category : 'Umum'"></span>
                                            <p class="text-xs text-indigo-200/80">Poster Belum Diunggah</p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Bottom Gradient & Title Overlay -->
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950 via-slate-950/75 to-transparent pt-20 pb-5 px-5 pointer-events-none z-10">
                                    <div class="border-l-[3px] border-indigo-400 pl-3">
                                        <p class="text-[10px] font-bold tracking-[0.16em] uppercase text-indigo-300 drop-shadow-[0_1px_4px_rgba(0,0,0,0.9)] mb-0.5">
                                            DETAIL PENGAJUAN
                                        </p>
                                        <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.9)]" x-text="selectedEvent.title"></h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Content Body -->
                            <div class="p-6 space-y-5">

                                <!-- Organizer Card -->
                                <div class="bg-indigo-50/70 border border-indigo-100/70 rounded-2xl p-4 flex items-center gap-3.5">
                                    <div class="w-11 h-11 rounded-xl bg-brand-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500">Penyelenggara</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <h4 class="font-bold text-gray-900 text-sm truncate" x-text="selectedEvent.organizer.name"></h4>
                                            <template x-if="selectedEvent.organizer.is_verified">
                                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info: Tanggal & Waktu -->
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-brand-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Tanggal & Waktu</p>
                                        <p class="text-sm font-bold text-gray-800 mt-0.5" x-text="selectedEvent.date_formatted"></p>
                                    </div>
                                </div>

                                <!-- Info: Lokasi Pelaksanaan -->
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-brand-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Lokasi Pelaksanaan</p>
                                        <p class="text-sm font-bold text-gray-800 mt-0.5" x-text="selectedEvent.full_location"></p>
                                    </div>
                                </div>

                                <!-- Section: Deskripsi Singkat -->
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">DESKRIPSI SINGKAT</p>
                                    <p class="text-sm text-gray-600 leading-relaxed" x-text="selectedEvent.description"></p>
                                </div>

                                <!-- Section: Kebutuhan Kuota Crew -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">KEBUTUHAN KUOTA CREW</p>
                                        <p class="text-xs font-bold text-brand-600">Total: <span x-text="selectedEvent.total_quota"></span> Crew</p>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="pos in selectedEvent.positions" :key="pos.id || pos.name">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200/80 text-xs font-semibold text-gray-800">
                                                <span x-text="pos.name"></span>:&nbsp;<span class="text-brand-600" x-text="pos.quota"></span>
                                            </span>
                                        </template>
                                        <template x-if="!selectedEvent.positions || selectedEvent.positions.length === 0">
                                            <span class="text-xs text-gray-400 italic">Belum ada rincian divisi posisi crew.</span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Section: Benefit yang Diberikan (Green Box) -->
                                <div class="bg-emerald-50/80 border border-emerald-100 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                        </svg>
                                        <span>Benefit yang Diberikan</span>
                                    </div>
                                    <p class="text-xs text-emerald-700 font-medium mt-1.5 leading-relaxed" x-text="selectedEvent.benefits_text"></p>
                                </div>

                                <!-- Rejection Reason if Rejected -->
                                <template x-if="selectedEvent.status === 'rejected' && selectedEvent.rejection_reason">
                                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4">
                                        <p class="text-xs font-bold text-rose-800 mb-1">Alasan Penolakan:</p>
                                        <p class="text-xs text-rose-700" x-text="selectedEvent.rejection_reason"></p>
                                    </div>
                                </template>

                                <!-- Action Buttons at Bottom -->
                                <div class="pt-2">
                                    <template x-if="selectedEvent.status === 'pending_review'">
                                        <div class="flex flex-col sm:flex-row items-center gap-3">
                                            <!-- Button Solid Biru: Setujui Event -->
                                            <button 
                                                type="button" 
                                                x-on:click="$dispatch('open-modal', 'approve-event-modal')"
                                                class="w-full sm:flex-1 py-3 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-sm flex items-center justify-center gap-2 shadow-sm transition-all hover:shadow"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui Event
                                            </button>

                                            <!-- Button Solid Merah Muda: Tolak Pengajuan -->
                                            <button 
                                                type="button" 
                                                x-on:click="$dispatch('open-modal', 'reject-event-modal')"
                                                class="w-full sm:flex-1 py-3 px-4 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 font-bold rounded-xl text-sm flex items-center justify-center gap-2 transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Tolak Pengajuan
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="selectedEvent.status === 'active' || selectedEvent.status === 'awaiting_payment'">
                                        <div class="bg-emerald-50 text-emerald-800 p-3.5 rounded-xl border border-emerald-200 text-xs text-center font-semibold space-y-1">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>✓ Event ini telah disetujui kurasi oleh Admin.</span>
                                            </div>
                                            <template x-if="selectedEvent.is_awaiting_payment || selectedEvent.status === 'awaiting_payment'">
                                                <p class="text-[11px] text-emerald-700/80 font-normal">Penyelenggara (EO) sedang dalam proses penyelesaian pembayaran paket publikasi.</p>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="selectedEvent.status === 'rejected'">
                                        <div class="bg-rose-50 text-rose-800 p-3 rounded-xl border border-rose-200 text-xs text-center font-semibold">
                                            ✕ Pengajuan event ini telah ditolak.
                                        </div>
                                    </template>
                                </div>

                            </div>
                        </div>
                    </template>

                    <!-- Empty Placeholder if No Event Selected -->
                    <template x-if="!selectedEvent">
                        <div class="p-12 text-center text-gray-400">
                            <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800 text-base mb-1">Pilih Event untuk Ditinjau</h4>
                            <p class="text-xs text-gray-500 max-w-xs mx-auto">Klik tombol "Tinjau" pada salah satu kartu pengajuan di panel kiri untuk melihat rincian lengkap event.</p>
                        </div>
                    </template>

                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- MODAL TOLAK PENGAJUAN EVENT (COMPACT)          -->
        <!-- ============================================== -->
        <x-modal name="reject-event-modal" :show="false" maxWidth="md" zIndex="50">
            <form method="POST" 
                  :action="selectedEvent ? selectedEvent.reject_url : ''" 
                  class="p-5 flex flex-col space-y-3"
                  x-data="{
                      reason: '',
                      hasError: false,
                      handleSubmit(e) {
                          if (this.reason.trim() === '') {
                              e.preventDefault();
                              this.hasError = true;
                              return false;
                          }
                          this.hasError = false;
                      },
                      resetForm() {
                          this.reason = '';
                          this.hasError = false;
                          this.$el.querySelectorAll('.text-red-600').forEach(el => el.style.display = 'none');
                          const btn = this.$el.querySelector('button[type=submit]');
                          if (btn && window.unlockAdminButton) {
                              window.unlockAdminButton(btn);
                          }
                      }
                  }"
                  @open-modal.window="if ($event.detail === 'reject-event-modal') resetForm()"
                  @modal-closed.window="if ($event.detail === 'reject-event-modal') resetForm()"
                  @submit="handleSubmit($event)">
                @csrf

                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-extrabold text-gray-900 break-words">
                            Tolak Pengajuan Event?
                        </h2>
                        <p class="text-xs text-gray-500 font-medium break-words mt-0.5" x-text="selectedEvent ? selectedEvent.title : ''"></p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Berikan alasan penolakan spesifik agar pihak penyelenggara dapat memperbaiki berkas pengajuannya.
                </p>

                <div>
                    <label for="alasan_reject_event" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        id="alasan_reject_event" 
                        name="alasan" 
                        x-model="reason"
                        @input="if (reason.trim() !== '') hasError = false"
                        rows="2" 
                        placeholder="Contoh: Dokumen perizinan tidak lengkap, deskripsi kegiatan kurang jelas..."
                        class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors"
                        x-bind:class="hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-200' : ''"
                    >{{ old('alasan') }}</textarea>
                    <p x-show="hasError" class="text-rose-500 text-xs font-semibold mt-0.5" x-cloak>Alasan penolakan wajib diisi!</p>
                    <x-input-error :messages="$errors->get('alasan')" class="mt-1" />
                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-1" />
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                    <x-secondary-button type="button" @click="resetForm(); show = false; $dispatch('close')">
                        BATAL
                    </x-secondary-button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- ============================================== -->
        <!-- MODAL KONFIRMASI SETUJUI EVENT (COMPACT)       -->
        <!-- ============================================== -->
        <x-modal name="approve-event-modal" :show="false" maxWidth="md" zIndex="50">
            <form method="POST" :action="selectedEvent ? selectedEvent.approve_url : ''" class="p-5 flex flex-col space-y-3">
                @csrf

                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-extrabold text-gray-900 break-words">
                            Setujui Pengajuan Event?
                        </h2>
                        <p class="text-xs text-gray-500 font-medium break-words mt-0.5" x-text="selectedEvent ? selectedEvent.title : ''"></p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Event yang disetujui akan dialihkan ke status siap terbit dan dapat dipublikasikan ke pencarian crew. Sistem akan mengirimkan notifikasi ke akun EO.
                </p>

                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                    <x-secondary-button type="button" @click="show = false; $dispatch('close')">
                        BATAL
                    </x-secondary-button>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                        Setujui & Publikasikan
                    </button>
                </div>
            </form>
        </x-modal>

    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function reviewEventApp(initialEvents, initialSelectedId) {
            return {
                events: initialEvents || [],
                selectedId: initialSelectedId,
                selectedEvent: null,
                loading: false,
                posterLoadFailed: false,

                init() {
                    this.posterLoadFailed = false;
                    if (this.selectedId) {
                        const found = this.events.find(e => Number(e.id) === Number(this.selectedId));
                        if (found) {
                            this.selectedEvent = Object.assign({}, found);
                        } else {
                            this.selectEvent(this.selectedId, false);
                        }
                    } else if (this.events.length > 0) {
                        this.selectedEvent = Object.assign({}, this.events[0]);
                        this.selectedId = this.events[0].id;
                    }

                    // Handle Back/Forward browser navigation
                    window.addEventListener('popstate', (e) => {
                        const curUrl = new URL(window.location.href);
                        const sel = curUrl.searchParams.get('selected');
                        if (sel && Number(sel) !== Number(this.selectedId)) {
                            this.selectEvent(Number(sel), false);
                        }
                    });
                },

                async selectEvent(id, shouldPushState = true) {
                    if (!id) return;
                    this.selectedId = Number(id);
                    this.posterLoadFailed = false;

                    // Update preview instan dari cache lokal
                    const cached = this.events.find(e => Number(e.id) === Number(id));
                    if (cached) {
                        this.selectedEvent = Object.assign({}, cached);
                    }

                    // Sync URL parameter ?selected=id via history.pushState
                    if (shouldPushState) {
                        const url = new URL(window.location.href);
                        if (url.searchParams.get('selected') !== String(id)) {
                            url.searchParams.set('selected', id);
                            window.history.pushState({ selected: id }, '', url.toString());
                        }
                    }

                    // Auto smooth scroll to detail panel on mobile / tablet screens
                    if (window.innerWidth < 1024) {
                        this.$nextTick(() => {
                            document.getElementById('event-detail-preview-panel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    }

                    // Fetch detail from show endpoint (JSON)
                    this.loading = true;
                    try {
                        const response = await fetch(`/admin/review-events/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (Number(this.selectedId) === Number(id)) {
                                this.selectedEvent = data;
                                this.posterLoadFailed = false;
                            }
                        }
                    } catch (error) {
                        console.error('Gagal mengambil rincian event:', error);
                    } finally {
                        if (Number(this.selectedId) === Number(id)) {
                            this.loading = false;
                        }
                    }
                }
            };
        }
    </script>

</x-layouts.admin>