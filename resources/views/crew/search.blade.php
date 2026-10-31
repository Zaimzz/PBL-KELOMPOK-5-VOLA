<x-layouts.crew title="Cari Event - VOLA">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Temukan Event</h1>
        <p class="mt-1 text-sm text-gray-500">Cari event dan posisi volunteer yang sesuai dengan minat dan keahlianmu.</p>
    </div>

    <!-- Search Bar -->
    <div class="bg-white border border-gray-200 rounded-2xl p-2 shadow-sm mb-6">
        <form action="{{ route('search.index') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Cari nama event, posisi, atau lokasi..."
                    class="w-full pl-12 pr-4 py-3 rounded-xl border-0 focus:ring-0 outline-none text-sm"
                >
            </div>
            @if($category && $category !== 'Semua')
                <input type="hidden" name="category" value="{{ $category }}">
            @endif
            @if($location)
                <input type="hidden" name="location" value="{{ $location }}">
            @endif
            @foreach((array)$position as $pos)
                <input type="hidden" name="position[]" value="{{ $pos }}">
            @endforeach
            @if($time)
                <input type="hidden" name="time" value="{{ $time }}">
            @endif
            @foreach((array)$status as $st)
                <input type="hidden" name="status[]" value="{{ $st }}">
            @endforeach
            @if(!empty($sort) && $sort !== 'terdekat')
                <input type="hidden" name="sort" value="{{ $sort }}">
            @endif
            <button type="submit" class="px-6 py-3 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                Cari
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm sticky top-24 overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                    </h3>
                    <a href="{{ route('search.index') }}" class="text-sm font-semibold text-[#4F46E5] hover:text-indigo-800 transition">Reset Filter</a>
                </div>

                <!-- Form Filter -->
                <form action="{{ route('search.index') }}" method="GET" class="p-5" x-data="{ selectedCategory: '{{ $category ?: "Semua" }}', selectedTime: '{{ $time }}' }">
                    <input type="hidden" name="q" value="{{ $query }}">
                    <input type="hidden" name="category" :value="selectedCategory">
                    @if(!empty($sort) && $sort !== 'terdekat')
                        <input type="hidden" name="sort" value="{{ $sort }}">
                    @endif

                    <!-- KATEGORI -->
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Kategori</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($categories as $cat)
                                <button
                                    type="button"
                                    @click="selectedCategory = '{{ $cat }}'"
                                    :class="selectedCategory === '{{ $cat }}'
                                        ? 'bg-[#4F46E5] text-white border-[#4F46E5]'
                                        : 'bg-gray-100 text-gray-700 border-gray-100 hover:bg-gray-200'"
                                    class="px-3.5 py-1.5 rounded-full text-sm font-medium transition cursor-pointer border">
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- LOKASI -->
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Lokasi</p>
                        <div class="relative">
                            <select name="location" class="w-full pl-3 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#4F46E5] focus:border-[#4F46E5] outline-none cursor-pointer appearance-none">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc === 'Semua Lokasi' ? '' : $loc }}" {{ $location === ($loc === 'Semua Lokasi' ? '' : $loc) ? 'selected' : '' }}>
                                        {{ $loc }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- POSISI VOLUNTEER -->
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Posisi Volunteer</p>
                        <div class="space-y-2.5">
                            @php
                                $positions = ['Liaison Officer', 'Usher & Registrasi', 'Dokumentasi & Media', 'Acara & Stage', 'Konsumsi & Logistik'];
                            @endphp
                            @foreach($positions as $pos)
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" name="position[]" value="{{ $pos }}"
                                        {{ in_array($pos, (array)$position) ? 'checked' : '' }}
                                        class="w-4 h-4 text-[#4F46E5] border-gray-300 rounded focus:ring-[#4F46E5] focus:ring-offset-0 cursor-pointer">
                                    <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $pos }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- WAKTU -->
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Waktu</p>
                        <div class="space-y-2.5">
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="time" value="bulan-ini"
                                    x-model="selectedTime"
                                    {{ $time === 'bulan-ini' ? 'checked' : '' }}
                                    class="w-4 h-4 text-[#4F46E5] border-gray-300 focus:ring-[#4F46E5] focus:ring-offset-0 cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Bulan Ini</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="time" value="1-3-bulan"
                                    x-model="selectedTime"
                                    {{ $time === '1-3-bulan' ? 'checked' : '' }}
                                    class="w-4 h-4 text-[#4F46E5] border-gray-300 focus:ring-[#4F46E5] focus:ring-offset-0 cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">1â€“3 Bulan Ke Depan</span>
                            </label>
                        </div>
                    </div>

                    <!-- STATUS REKRUTMEN -->
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Status Rekrutmen</p>
                        <div class="space-y-2.5">
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="status[]" value="Open Volunteer"
                                    {{ in_array('Open Volunteer', (array)$status) ? 'checked' : '' }}
                                    class="w-4 h-4 text-[#4F46E5] border-gray-300 rounded focus:ring-[#4F46E5] focus:ring-offset-0 cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Open Volunteer</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="status[]" value="Event Informasi Saja"
                                    {{ in_array('Event Informasi Saja', (array)$status) ? 'checked' : '' }}
                                    class="w-4 h-4 text-[#4F46E5] border-gray-300 rounded focus:ring-[#4F46E5] focus:ring-offset-0 cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Event Informasi Saja</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Terapkan Filter -->
                    <button type="submit" class="w-full py-3 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-gray-900">{{ $totalEvents }} Event Ditemukan</h2>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Aktif</span>
                </div>
                <form action="{{ route('search.index') }}" method="GET" class="flex items-center gap-2 text-sm">
                    @if(!empty($query))
                        <input type="hidden" name="q" value="{{ $query }}">
                    @endif
                    @if(!empty($category) && $category !== 'Semua')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    @if(!empty($location))
                        <input type="hidden" name="location" value="{{ $location }}">
                    @endif
                    @foreach((array)$position as $pos)
                        <input type="hidden" name="position[]" value="{{ $pos }}">
                    @endforeach
                    @if(!empty($time))
                        <input type="hidden" name="time" value="{{ $time }}">
                    @endif
                    @foreach((array)$status as $st)
                        <input type="hidden" name="status[]" value="{{ $st }}">
                    @endforeach
                    <span class="text-gray-500">Urutkan:</span>
                    <select name="sort" onchange="this.form.submit()" class="border border-gray-200 rounded-lg pl-3 pr-10 py-1.5 text-sm bg-white focus:ring-2 focus:ring-[#4F46E5] outline-none cursor-pointer">
                        <option value="terdekat" {{ $sort === 'terdekat' ? 'selected' : '' }}>Batas Waktu Terdekat</option>
                        <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terpopuler" {{ $sort === 'terpopuler' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </form>
            </div>

            @if($paginatedEvents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($paginatedEvents as $event)
                        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all flex flex-col group">
                            <div class="relative h-40 w-full overflow-hidden bg-gray-100 shrink-0">
                                <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                    {{ $event['status_badge'] }}
                                </span>
                                <button class="absolute top-3 right-3 w-7 h-7 bg-white/90 backdrop-blur rounded-full flex items-center justify-center hover:bg-white transition">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1">{{ $event['category'] }}</p>
                                <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 min-h-[40px] mb-2">
                                    {{ $event['title'] }}
                                </h3>
                                <div class="flex items-center gap-1 mb-2">
                                    <span class="text-xs text-gray-600 font-medium truncate">{{ $event['organizer'] }}</span>
                                    @if($event['is_verified'])
                                        <svg class="w-3.5 h-3.5 text-[#4F46E5] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="space-y-1 text-xs text-gray-500 mb-3">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $event['location'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $event['date'] }}</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5 mb-3">
                                    <p class="text-[10px] text-gray-500 mb-0.5">Posisi: {{ $event['positions'] }}</p>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-auto">
                                    @php
                                        $slotLabel = $event['slot_label'] ?? null;
                                        $slotType  = $event['slot_type']  ?? 'default';
                                    @endphp
                                    @if($slotLabel)
                                        @if($slotType === 'urgent')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $slotLabel }}
                                            </span>
                                        @elseif($slotType === 'slot' || $slotType === 'crew')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $slotLabel }}
                                            </span>
                                        @elseif($slotType === 'position')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $slotLabel }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                {{ $slotLabel }}
                                            </span>
                                        @endif
                                    @else
                                        @if($event['days_left'] <= 10)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Sisa {{ $event['days_left'] }} Hari
                                            </span>
                                        @elseif($event['position_count'] > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $event['position_count'] }} {{ $event['position_count'] === 1 ? 'Posisi' : 'Slot' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                Seleksi Interview
                                            </span>
                                        @endif
                                    @endif
                                    <a href="{{ route('events.detail', ['event' => $event['id']]) }}"
                                       class="px-3 py-1.5 bg-[#4F46E5] text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($totalPages > 1)
                    <div class="mt-6 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Menampilkan {{ (($page - 1) * $perPage) + 1 }}â€“{{ min($page * $perPage, $totalEvents) }} dari {{ $totalEvents }} event
                        </p>
                        <div class="flex items-center gap-1">
                            @if($page > 1)
                                <a href="{{ route('search.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                                   class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">Sebelumnya</a>
                            @endif
                            @for($i = 1; $i <= $totalPages; $i++)
                                <a href="{{ route('search.index', array_merge(request()->query(), ['page' => $i])) }}"
                                   class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition
                                   {{ $i === $page ? 'bg-[#4F46E5] text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            @if($page < $totalPages)
                                <a href="{{ route('search.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                                   class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">Selanjutnya</a>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Event Ditemukan</h3>
                    <p class="text-sm text-gray-500 mb-4">Coba ubah filter atau kata kunci pencarianmu.</p>
                    <a href="{{ route('search.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4F46E5] text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition">
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
</x-layouts.crew>
