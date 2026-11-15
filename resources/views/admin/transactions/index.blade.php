<x-layouts.admin title="Data Transaksi">

    <!-- Header Section -->
    <div class="mb-6">
        <div class="mb-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-brand-50 text-brand-700 border border-brand-100">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                PORTAL KEUANGAN &bull; Rekapitulasi Platform
            </span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Data Transaksi</h1>
                <p class="text-gray-500 text-sm mt-1">Rekapitulasi pembayaran layanan publikasi dan rekrutmen event oleh pihak penyelenggara.</p>
            </div>

            <!-- Top Right: Download Actions Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" type="button" 
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-brand-700 bg-brand-50 border border-brand-200 rounded-xl hover:bg-brand-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Laporan
                    <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{'rotate-180': open}">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                    x-transition:enter="transition ease-out duration-100" 
                    x-transition:enter-start="transform opacity-0 scale-95" 
                    x-transition:enter-end="transform opacity-100 scale-100" 
                    x-transition:leave="transition ease-in duration-75" 
                    x-transition:leave-start="transform opacity-100 scale-100" 
                    x-transition:leave-end="transform opacity-0 scale-95" 
                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden" 
                    style="display: none;">
                    <div class="py-1">
                        <a href="{{ route('admin.transactions.download-report', ['period' => request('period'), 'month' => request('month'), 'status' => request('status')]) }}" target="_blank" 
                        class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                            <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Cetak PDF
                        </a>
                        <a href="{{ route('admin.transactions.download-excel', ['period' => request('period'), 'month' => request('month'), 'status' => request('status')]) }}" 
                        class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Ekspor Excel (.xlsx)
                        </a>
                        <a href="{{ route('admin.transactions.download-csv', ['period' => request('period'), 'month' => request('month'), 'status' => request('status')]) }}" 
                        class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Ekspor CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <!-- Total Volume Pembayaran -->
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500">Total Volume Pembayaran</span>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_volume_formatted'] }}</h3>
                        <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1 mt-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            {{ $stats['growth_rate'] }} vs bulan sebelumnya
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Transaksi Berhasil -->
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500">Transaksi Berhasil</span>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['successful_count'] }} <span class="text-sm font-normal text-gray-500">Invoice</span></h3>
                        <p class="text-xs text-gray-500 font-medium mt-2">
                            Tingkat keberhasilan pembayaran <span class="font-bold text-gray-700">{{ $stats['success_rate'] }}</span>
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Menunggu Pembayaran -->
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500">Menunggu Pembayaran</span>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['pending_count'] }} <span class="text-sm font-normal text-gray-500">Invoice</span></h3>
                        <p class="text-xs text-amber-600 font-semibold flex items-center gap-1 mt-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Batas waktu < 24 jam
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content & Alpine.js Manager -->
    <div x-data="transactionManager()" class="space-y-6">

        <!-- Search & Filter Bar -->
        <x-card class="p-3">
            <form action="{{ route('admin.transactions.index') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-3">
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
                           placeholder="Cari nomor invoice #INV, nama event, atau penyelenggara"
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                <!-- Status Filter PILL BUTTONS (Warna Aktif: Biru, Terpisah) -->
                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 overflow-x-auto pb-1 sm:pb-0">
                    <input type="hidden" name="status" id="txStatusInput" value="{{ request('status', 'all') }}">

                    <button type="button"
                            onclick="setTxStatus('all')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ request('status', 'all') === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Semua Status
                    </button>

                    <button type="button"
                            onclick="setTxStatus('lunas')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ request('status') === 'lunas' || request('status') === 'paid' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Lunas
                    </button>

                    <button type="button"
                            onclick="setTxStatus('menunggu')"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ request('status') === 'menunggu' || request('status') === 'pending' ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Menunggu Pembayaran
                    </button>
                </div>

                <!-- Period / Date Range Filter Dropdown + Conditional Month Picker -->
                @php
                    $rawPeriod = request('period', 'all');
                    $selectedPeriod = str_starts_with($rawPeriod, 'month:') ? 'custom_month' : ($rawPeriod === 'all' && request('month') ? 'custom_month' : $rawPeriod);
                    $selectedMonth = str_starts_with($rawPeriod, 'month:') ? substr($rawPeriod, 6) : request('month', now()->format('Y-m'));
                @endphp
                <div x-data="{ 
                        open: false,
                        period: '{{ $selectedPeriod }}',
                        options: {
                            'all': 'Semua Waktu',
                            '1_year': '1 Tahun Terakhir',
                            '6_months': '6 Bulan Terakhir',
                            'this_month': 'Bulan Ini',
                            'custom_month': 'Pilih Bulan Tertentu'
                        },
                        select(val) {
                            this.period = val;
                            this.open = false;
                            if (val !== 'custom_month') {
                                $nextTick(() => { this.$refs.hiddenPeriod.form.submit(); });
                            }
                        }
                    }" 
                    class="flex flex-col sm:flex-row items-center gap-2 w-full lg:w-auto shrink-0 relative"
                    @click.away="open = false">
                    
                    <input type="hidden" name="period" :value="period" x-ref="hiddenPeriod">

                    <!-- Custom Dropdown Button -->
                    <div class="relative w-full sm:w-56 shrink-0">
                        <button type="button" 
                                @click="open = !open" 
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-left">
                            <span class="truncate" x-text="options[period] || 'Semua Waktu'"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" 
                                 :class="{ 'rotate-180 text-brand-600': open }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                             class="absolute left-0 right-0 mt-1.5 py-1.5 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden" 
                             style="display: none;" 
                             x-cloak>
                            <template x-for="(label, key) in options" :key="key">
                                <button type="button" 
                                        @click="select(key)"
                                        class="w-full flex items-center justify-between px-3.5 py-2 text-xs sm:text-sm text-left transition-colors"
                                        :class="period === key ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-700 hover:bg-gray-50'">
                                    <span x-text="label"></span>
                                    <svg x-show="period === key" class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <input type="month"
                           name="month"
                           value="{{ $selectedMonth }}"
                           x-show="period === 'custom_month'"
                           x-cloak
                           @change="$el.form.submit()"
                           class="w-full sm:w-auto py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                @if(request('search') || (request('status') && request('status') !== 'all') || (request('period') && request('period') !== 'all') || request('month'))
                    <a href="{{ route('admin.transactions.index') }}" 
                       x-data="{ resetting: false }"
                       @click="resetting = true"
                       class="group inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-500 hover:text-gray-800 active:scale-95 transition-all shrink-0">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:-rotate-45 shrink-0"
                             :class="{ 'animate-spin text-brand-600': resetting }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </x-card>

        <!-- Transactions Table Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3.5 px-6">Nomor Invoice</th>
                            <th class="py-3.5 px-6">Penyelenggara & Event</th>
                            <th class="py-3.5 px-4">Nominal</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($transactions as $tx)
                            @php
                                $statusStr = $tx->status instanceof \App\Enums\PaymentStatus ? $tx->status->value : (string) $tx->status;
                                $payType = strtolower($tx->payment_type ?? '');
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- Nomor Invoice -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <button type="button"
                                            x-on:click="openDetail({{ $tx->id }})"
                                            class="font-bold text-brand-600 hover:text-brand-800 hover:underline transition-colors">
                                        #{{ $tx->invoice_number }}
                                    </button>
                                </td>

                                <!-- Penyelenggara & Event -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900 text-sm line-clamp-1">
                                        {{ $tx->event->title ?? '-' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate max-w-[180px]">{{ $tx->event->organizer->organization_name ?? 'Penyelenggara' }}</span>
                                    </div>
                                </td>

                                <!-- Nominal -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-extrabold text-gray-900">
                                        Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-700">
                                        {{ $tx->created_at->translatedFormat('d M Y') }}
                                    </div>
                                </td>

                                <!-- Metode Pembayaran -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if(str_contains($payType, 'qris'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            QRIS
                                        </span>
                                    @elseif(str_contains($payType, 'bca'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                            </svg>
                                            BCA VA
                                        </span>
                                    @elseif(str_contains($payType, 'mandiri'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-100">
                                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                            </svg>
                                            Mandiri Virtual Account
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200">
                                            {{ strtoupper($tx->payment_type ?: 'Midtrans') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($statusStr === 'paid')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Lunas
                                        </span>
                                    @elseif($statusStr === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            {{ ucfirst($statusStr) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- View Detail (Eye Icon) -->
                                        <button type="button"
                                                x-on:click="openDetail({{ $tx->id }})"
                                                title="Lihat Detail Transaksi"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-600 border border-brand-200/80 hover:bg-brand-600 hover:text-white hover:shadow-xs transition-all duration-150 group">
                                            <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Download Invoice (Download Icon) -->
                                        <a href="{{ route('admin.transactions.download-invoice', $tx) }}"
                                           target="_blank"
                                           title="Unduh Invoice PDF"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/80 hover:bg-emerald-600 hover:text-white hover:shadow-xs transition-all duration-150 group">
                                            <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center">
                                    <x-empty-state 
                                        title="Tidak ada transaksi ditemukan" 
                                        description="Tidak ada catatan transaksi atau invoice yang sesuai dengan parameter pencarian Anda." 
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL DETAIL TRANSAKSI / INVOICE (COMPACT)     -->
        <!-- ============================================== -->
        <x-modal name="transaction-detail-modal" :show="false" maxWidth="2xl">
            <div class="p-5 flex flex-col flex-1 min-h-0 overflow-hidden">
                <template x-if="loading">
                    <div class="py-12 text-center text-gray-400 my-auto">
                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs font-medium text-gray-600">Memuat rincian transaksi & invoice...</p>
                    </div>
                </template>

                <template x-if="!loading && hasError">
                    <div class="py-10 text-center space-y-3.5 my-auto">
                        <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Gagal Memuat Detail Transaksi</h4>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="errorMessage"></p>
                        </div>
                        <div>
                            <button type="button" @click="openDetail(currentTxId)"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && !hasError && currentTx">
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        <!-- Modal Header -->
                        <div class="pb-3 border-b border-gray-100 shrink-0">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Detail Transaksi</span>
                            <h3 class="text-base sm:text-lg font-extrabold text-gray-900 mt-0.5" x-text="'#' + currentTx.invoice_number"></h3>
                        </div>

                        <!-- Info Grid -->
                        <div class="space-y-3 text-xs flex-1 overflow-y-auto overscroll-y-contain min-h-0 py-3 pr-1">
                            <div class="bg-gray-50 p-3 rounded-xl space-y-2 border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] text-gray-500">Status Pembayaran</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                          :class="currentTx.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                          x-text="currentTx.status_label"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] text-gray-500">Total Nominal</span>
                                    <span class="text-sm font-extrabold text-gray-900" x-text="currentTx.amount_formatted"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] text-gray-500">Metode</span>
                                    <span class="text-xs font-semibold text-gray-800" x-text="currentTx.payment_type"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] text-gray-500">Waktu Tagihan</span>
                                    <span class="text-xs font-semibold text-gray-800" x-text="currentTx.created_at"></span>
                                </div>
                                <div class="flex justify-between items-center" x-show="currentTx.paid_at && currentTx.paid_at !== '-'">
                                    <span class="text-[11px] text-gray-500">Waktu Lunas</span>
                                    <span class="text-xs font-semibold text-emerald-700" x-text="currentTx.paid_at"></span>
                                </div>
                            </div>

                            <!-- Event Details -->
                            <div class="border border-gray-100 rounded-xl p-3 space-y-1.5 bg-white">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Terkait Event</span>
                                <h4 class="font-bold text-gray-900 text-xs" x-text="currentTx.event.title"></h4>
                                <p class="text-[11px] text-gray-500" x-text="'Penyelenggara: ' + currentTx.organizer.name"></p>
                                <p class="text-[11px] text-gray-500" x-text="'PIC: ' + currentTx.organizer.pic_name + ' (' + currentTx.organizer.pic_phone + ')'"></p>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end shrink-0">
                            <a :href="currentTx.download_url"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Unduh Invoice
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </x-modal>

    </div>

    <!-- Script Filter Status Pills -->
    <script>
        function setTxStatus(status) {
            document.getElementById('txStatusInput').value = status;
            document.getElementById('txStatusInput').form.submit();
        }

        function transactionManager() {
            return {
                currentTx: null,
                loading: false,
                hasError: false,
                errorMessage: '',
                currentTxId: null,

                async openDetail(id) {
                    this.currentTxId = id;
                    this.loading = true;
                    this.hasError = false;
                    this.errorMessage = '';
                    this.currentTx = null;
                    this.$dispatch('open-modal', 'transaction-detail-modal');

                    try {
                        const response = await fetch(`/admin/transactions/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Gagal mengambil rincian transaksi (HTTP ' + response.status + ')');
                        }

                        this.currentTx = await response.json();
                    } catch (error) {
                        console.error('Gagal mengambil rincian transaksi:', error);
                        this.hasError = true;
                        this.errorMessage = error.message || 'Gagal mengambil rincian transaksi.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>

</x-layouts.admin>