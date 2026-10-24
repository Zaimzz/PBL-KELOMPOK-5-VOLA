<x-layouts.admin title="Review Event">

    <!-- Header & Breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs font-semibold text-brand-600 uppercase tracking-wider mb-1.5">
            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>PANEL KURASI &bull; PENGAJUAN TERBUKA</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Review Event</h1>
                <p class="text-gray-500 text-sm mt-1">Kurasi dan tinjau kelayakan pengajuan event baru sebelum dipublikasikan ke feed pencarian relawan.</p>
            </div>

            <!-- Header Metrics -->
            <div class="flex items-center gap-3">
                <!-- Pengajuan Menunggu -->
                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-2.5 shadow-sm flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-bold text-gray-900">{{ $stats['pending'] }}</span>
                        <span class="text-xs text-gray-500 leading-tight">Pengajuan<br class="hidden sm:inline"> Menunggu</span>
                    </div>
                </div>

                <!-- Disetujui Bulan Ini -->
                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-2.5 shadow-sm flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-bold text-gray-900">{{ $stats['approved_this_month'] }}</span>
                        <span class="text-xs text-gray-500 leading-tight">Disetujui<br class="hidden sm:inline"> Bulan Ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div x-data="reviewEventApp(@js($eventsPayload), {{ $selectedEvent ? $selectedEvent['id'] : 'null' }})" class="space-y-6">

        <!-- Grid Layout: Left Panel (Queue) & Right Panel (Detail) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- ========================================== -->
            <!-- PANEL KIRI: DAFTAR ANTREAN KURASI (7 Cols) -->
            <!-- ========================================== -->
            <div class="lg:col-span-7 space-y-4">

                <!-- Header Antrean & Search/Filter Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-base font-bold text-gray-900">Daftar Antrean Kurasi</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                            {{ $events->total() }} Item
                        </span>
                    </div>
                </div>

                <!-- Search Input & Status Filter -->
                <x-card class="p-3">
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
                                   placeholder="Cari EO, pengajuan event..."
                                   class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                        </div>

                        <!-- Status Filter Buttons -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                            <input type="hidden" name="status" id="statusFilterInput" value="{{ request('status') }}">
                            
                            <button type="submit" onclick="document.getElementById('statusFilterInput').value='pending_review'"
                                    class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-colors {{ request('status', 'pending_review') === 'pending_review' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Menunggu Review
                            </button>
                            <button type="submit" onclick="document.getElementById('statusFilterInput').value='active'"
                                    class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-colors {{ request('status') === 'active' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Disetujui
                            </button>
                            <button type="submit" onclick="document.getElementById('statusFilterInput').value='rejected'"
                                    class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-colors {{ request('status') === 'rejected' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Ditolak
                            </button>
                            <button type="submit" onclick="document.getElementById('statusFilterInput').value='all'"
                                    class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-colors {{ request('status') === 'all' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Semua Status
                            </button>
                        </div>
                    </form>
                </x-card>

                <!-- Cards Queue List -->
                <div class="space-y-3">
                    @forelse($events as $event)
                        @php
                            $status = $event->status instanceof \App\Enums\EventStatus ? $event->status->value : (string) $event->status;
                            $totalQuota = $event->positions ? $event->positions->sum('quota') : 0;
                        @endphp
                        
                        <div 
                            x-on:click="selectEvent({{ $event->id }})"
                            :class="selectedId === {{ $event->id }} 
                                ? 'border-2 border-brand-500 shadow-md ring-4 ring-brand-50 bg-white' 
                                : 'border border-gray-100 bg-white shadow-sm hover:border-gray-200 hover:shadow-md'"
                            class="rounded-2xl p-5 transition-all cursor-pointer relative"
                        >
                            <!-- Top Row: Status Badge, Category, Date, and Action Button -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Status Badge -->
                                    @if($status === 'pending_review')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/60">
                                            Menunggu Review
                                        </span>
                                    @elseif($status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                            Disetujui
                                        </span>
                                    @elseif($status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200/60">
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
                                            Sedang Ditinjau
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
                                            @if($status === 'active')
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

                            <!-- Event Title -->
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-brand-600 transition-colors mb-2">
                                {{ $event->title }}
                            </h3>

                            <!-- Bottom Row: Organizer Info & Volunteer Quota -->
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <div class="flex items-center gap-1 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $event->organizer->organization_name ?? 'Penyelenggara' }}</span>
                                </div>
                                <span class="text-gray-300">&bull;</span>
                                <span class="font-normal">{{ $totalQuota }} Kuota Relawan</span>
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

                <!-- Pagination -->
                @if($events->hasPages())
                    <div class="pt-2">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

            <!-- ============================================== -->
            <!-- PANEL KANAN: DETAIL PENGAJUAN EVENT (5 Cols)   -->
            <!-- ============================================== -->
            <div class="lg:col-span-5">
                <div class="sticky top-20 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

                    <template x-if="selectedEvent">
                        <div>
                            <!-- Header Banner / Thumbnail Image with Dark Overlay -->
                            <div class="relative h-56 sm:h-64 bg-slate-900 overflow-hidden">
                                <template x-if="selectedEvent.poster_url">
                                    <img :src="selectedEvent.poster_url" :alt="selectedEvent.title" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!selectedEvent.poster_url">
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 flex items-center justify-center p-6 text-center">
                                        <div class="absolute inset-0 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:16px_16px] opacity-20"></div>
                                        <div class="relative z-10">
                                            <span class="inline-block px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 text-xs font-semibold mb-2" x-text="selectedEvent.category"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Bottom Gradient & Title Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-6">
                                    <p class="text-[11px] font-bold tracking-widest uppercase text-indigo-300 mb-1">DETAIL PENGAJUAN</p>
                                    <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight" x-text="selectedEvent.title"></h3>
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

                                <!-- Section: Kebutuhan Kuota Relawan -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">KEBUTUHAN KUOTA RELAWAN</p>
                                        <p class="text-xs font-bold text-brand-600">Total: <span x-text="selectedEvent.total_quota"></span> Relawan</p>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="pos in selectedEvent.positions" :key="pos.id || pos.name">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200/80 text-xs font-semibold text-gray-800">
                                                <span x-text="pos.name"></span>:&nbsp;<span class="text-brand-600" x-text="pos.quota"></span>
                                            </span>
                                        </template>
                                        <template x-if="!selectedEvent.positions || selectedEvent.positions.length === 0">
                                            <span class="text-xs text-gray-400 italic">Belum ada rincian divisi posisi relawan.</span>
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

                                    <template x-if="selectedEvent.status === 'active'">
                                        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-xl border border-emerald-200 text-xs text-center font-semibold">
                                            ✓ Event ini telah disetujui dan saat ini aktif dipublikasikan ke publik.
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
        <!-- MODAL TOLAK PENGAJUAN EVENT (TUGAS 1 STANDARD) -->
        <!-- ============================================== -->
        <x-modal name="reject-event-modal" :show="false" maxWidth="md">
            <form method="POST" 
                  :action="selectedEvent ? selectedEvent.reject_url : ''" 
                  class="p-6"
                  x-data="{ reason: '', hasError: false }"
                  @submit.prevent="reason.trim() === '' ? hasError = true : $el.submit()">
                @csrf

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            Tolak Pengajuan Event?
                        </h2>
                        <p class="text-xs text-gray-500 truncate max-w-xs" x-text="selectedEvent ? selectedEvent.title : ''"></p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    Silakan berikan alasan penolakan. Alasan ini akan dikirimkan ke pihak EO terkait agar mereka dapat memperbaikinya.
                </p>

                <div class="mt-4">
                    <x-input-label for="alasan_reject_event" value="Alasan Penolakan" />
                    <textarea 
                        id="alasan_reject_event" 
                        name="alasan" 
                        x-model="reason"
                        @input="if (reason.trim() !== '') hasError = false"
                        rows="3" 
                        placeholder="Contoh: Dokumen perizinan tidak lengkap, deskripsi kegiatan kurang jelas..."
                        class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm text-sm"
                        x-bind:class="hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    >{{ old('alasan') }}</textarea>
                    <p x-show="hasError" class="text-red-500 text-sm mt-1" x-cloak>Alasan penolakan wajib diisi!</p>
                    <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <x-danger-button type="submit">
                        Kirim Penolakan
                    </x-danger-button>
                </div>
            </form>
        </x-modal>

        <!-- ============================================== -->
        <!-- MODAL KONFIRMASI SETUJUI EVENT                 -->
        <!-- ============================================== -->
        <x-modal name="approve-event-modal" :show="false" maxWidth="md">
            <form method="POST" :action="selectedEvent ? selectedEvent.approve_url : ''" class="p-6">
                @csrf

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            Setujui Pengajuan Event?
                        </h2>
                        <p class="text-xs text-gray-500 truncate max-w-xs" x-text="selectedEvent ? selectedEvent.title : ''"></p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-6">
                    Event yang disetujui akan langsung aktif dan siap tayang secara publik di feed pencarian relawan. Sistem akan mengirimkan notifikasi persetujuan ke akun EO.
                </p>

                <div class="flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
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

                init() {
                    if (this.selectedId && this.events.length > 0) {
                        this.selectedEvent = this.events.find(e => e.id === this.selectedId) || this.events[0];
                    }
                },

                async selectEvent(id) {
                    this.selectedId = id;
                    const cached = this.events.find(e => e.id === id);
                    if (cached) {
                        this.selectedEvent = cached;
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
                            this.selectedEvent = data;
                        }
                    } catch (error) {
                        console.error('Gagal mengambil rincian event:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>

</x-layouts.admin>
