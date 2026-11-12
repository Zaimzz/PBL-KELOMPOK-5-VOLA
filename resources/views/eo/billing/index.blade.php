<x-layouts.eo title="Pembayaran & Tagihan — Vola">

    {{-- ================================================================
         STATE 1: Profil organisasi belum dibuat
    ================================================================ --}}
    @if (! $organizerProfile)
        <div class="text-center py-24">
            <div class="mx-auto w-20 h-20 bg-brand-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Profil Organisasi Diperlukan</h2>
            <p class="text-gray-500 max-w-sm mx-auto mb-6">Lengkapi profil organisasi Anda sebelum mengakses fitur pembayaran.</p>
            <a href="{{ route('eo.organization.edit') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                Lengkapi Profil
            </a>
        </div>

    @else

        {{-- ============================================================
             HEADER
        ============================================================ --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pembayaran & Tagihan</h1>
            <p class="text-gray-500 mt-1">Kelola paket publikasi, invoice, dan riwayat pembayaran event.</p>
        </div>

        {{-- ============================================================
             RINGKASAN FINANSIAL
        ============================================================ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

            {{-- Total Transaksi Berhasil --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Transaksi Berhasil</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($paidCount) }}</p>
                    <p class="text-xs text-gray-400 truncate">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Tagihan Aktif --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Tagihan Aktif</p>
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($pendingCount) }}</p>
                    <p class="text-xs text-gray-400 truncate">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Total Event --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Event</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($events->count()) }}</p>
                    <p class="text-xs text-gray-400">Event terdaftar</p>
                </div>
            </div>

            {{-- Info Paket --}}
            <div class="bg-brand-600 rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-white/70 font-medium uppercase tracking-wide">Paket Tersedia</p>
                    <p class="text-2xl font-bold text-white">{{ count($packages) }}</p>
                    <p class="text-xs text-white/70">Pilih sesuai kebutuhan</p>
                </div>
            </div>
        </div>

        {{-- ============================================================
             PILIH PAKET PUBLIKASI
        ============================================================ --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-8"
             x-data="billingApp()"
             x-init="init()">

            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">Pilih Paket Publikasi Event</h2>
                <p class="text-sm text-gray-500 mt-1">Pilih paket yang sesuai dengan kebutuhan publikasi event Anda. Harga berlaku sama untuk semua jenis EO.</p>
            </div>

            {{-- Event selector --}}
            @if ($events->isNotEmpty())
                <div class="mb-5">
                    <label for="pkg-event-select" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Event yang akan dipublikasikan
                    </label>
                    <select id="pkg-event-select"
                            x-model="selectedEventId"
                            class="w-full sm:max-w-md text-sm border border-gray-200 rounded-lg px-3 py-2.5 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">— Pilih Event —</option>
                        @foreach ($events as $evt)
                            <option value="{{ $evt->id }}">{{ \Illuminate\Support\Str::limit($evt->title, 60) }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-5 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-amber-800">Belum ada event</p>
                        <p class="text-sm text-amber-700 mt-0.5">Buat event terlebih dahulu sebelum memilih paket publikasi.</p>
                        <a href="{{ route('eo.events.create') }}" class="text-sm font-medium text-amber-800 underline mt-1 inline-block">Buat Event</a>
                    </div>
                </div>
            @endif

            {{-- Package cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
                @foreach ($packages as $slug => $pkg)
                    <div
                        x-on:click="selectPackage('{{ $slug }}')"
                        :class="selectedPackage === '{{ $slug }}'
                            ? 'border-brand-500 ring-2 ring-brand-500 bg-brand-50 cursor-pointer'
                            : 'border-gray-200 hover:border-brand-300 cursor-pointer'"
                        class="relative border-2 rounded-xl p-5 transition-all"
                    >
                        {{-- Recommended badge --}}
                        @if ($pkg['badge'])
                            <span class="absolute -top-2.5 left-4 text-xs font-semibold bg-brand-600 text-white px-2.5 py-0.5 rounded-full">
                                {{ $pkg['badge'] }}
                            </span>
                        @endif

                        {{-- Selected indicator --}}
                        <div x-show="selectedPackage === '{{ $slug }}'"
                             class="absolute top-3 right-3 w-5 h-5 bg-brand-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>

                        <p class="text-sm font-semibold text-gray-900 mb-1">{{ $pkg['name'] }}</p>
                        <p class="text-2xl font-bold text-brand-600 mb-1">Rp {{ number_format($pkg['price'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $pkg['duration_days'] }} hari publikasi</p>
                        <p class="text-xs text-gray-400 leading-relaxed">{{ $pkg['description'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Order summary + pay button --}}
            <div x-show="selectedPackage && selectedEventId" x-cloak>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Pesanan</p>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Paket</span>
                            <span class="font-medium text-gray-800" x-text="packageName()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Durasi</span>
                            <span class="font-medium text-gray-800" x-text="packageDuration() + ' hari'"></span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                            <span class="font-semibold text-gray-700">Total</span>
                            <span class="font-bold text-brand-600 text-base" x-text="packagePrice()"></span>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    x-on:click="pay()"
                    :disabled="loading"
                    :class="loading ? 'opacity-60 cursor-wait' : 'hover:bg-brand-700'"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-brand-600 text-white text-sm font-semibold rounded-xl transition-colors"
                >
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <svg x-show="loading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Bayar Sekarang'"></span>
                </button>

                {{-- Error message --}}
                <p x-show="errorMsg" x-cloak x-text="errorMsg"
                   class="mt-3 text-sm text-red-600"></p>
            </div>

            <p x-show="!selectedPackage || !selectedEventId" class="text-sm text-gray-400 mt-2">
                Pilih event dan paket untuk melanjutkan pembayaran.
            </p>
        </div>

        {{-- ============================================================
             RIWAYAT INVOICE & PEMBAYARAN
        ============================================================ --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">

            {{-- Table header + filters --}}
            <div class="p-5 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Riwayat Invoice & Pembayaran</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Semua transaksi dan status publikasi event Anda.</p>
                    </div>
                </div>

                {{-- Search + Status filter --}}
                <form method="GET" action="{{ route('eo.billing.index') }}" class="mt-4 flex flex-col sm:flex-row gap-3">
                    {{-- Preserve event selection from URL --}}
                    @if (request('event_id'))
                        <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                    @endif

                    <div class="relative flex-1 max-w-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari nomor invoice atau nama event…"
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 bg-gray-50">
                    </div>

                    <select name="status"
                            onchange="this.form.submit()"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="expired" {{ $statusFilter === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                        <option value="failed" {{ $statusFilter === 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>

                    <button type="submit"
                            class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        Cari
                    </button>
                </form>
            </div>

            {{-- Table --}}
            @if ($payments->isEmpty())
                <div class="text-center py-20 px-6">
                    <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        @if ($search || $statusFilter !== 'all')
                            Tidak Ada Hasil
                        @else
                            Belum Ada Invoice
                        @endif
                    </h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-5">
                        @if ($search || $statusFilter !== 'all')
                            Tidak ada invoice yang sesuai dengan pencarian atau filter yang dipilih.
                        @else
                            Pilih paket publikasi di atas untuk membuat invoice pertama Anda.
                        @endif
                    </p>
                    @if ($search || $statusFilter !== 'all')
                        <a href="{{ route('eo.billing.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Hapus Filter
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide whitespace-nowrap">No. Invoice</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Nama Event</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide whitespace-nowrap">Paket</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide whitespace-nowrap">Tanggal</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide whitespace-nowrap">Metode</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide whitespace-nowrap">Jumlah</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Status</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($payments as $payment)
                                @php
                                    $statusValue = $payment->status instanceof \App\Enums\PaymentStatus
                                        ? $payment->status->value
                                        : (string) $payment->status;
                                    $statusConfig = match ($statusValue) {
                                        'paid'      => ['label' => 'Lunas',              'class' => 'bg-green-50 text-green-700 border border-green-200'],
                                        'pending'   => ['label' => 'Menunggu',           'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
                                        'failed'    => ['label' => 'Gagal',              'class' => 'bg-red-50 text-red-700 border border-red-200'],
                                        'expired'   => ['label' => 'Kedaluwarsa',        'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                                        'cancelled' => ['label' => 'Dibatalkan',         'class' => 'bg-gray-100 text-gray-500 border border-gray-200'],
                                        default     => ['label' => ucfirst($statusValue), 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                                    };
                                    $pkgCfg = \App\Support\PublicationPackage::find($payment->package_name ?? '');
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-brand-600 whitespace-nowrap">
                                        #{{ $payment->invoice_number }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-800 max-w-[200px]">
                                        <span class="line-clamp-2">{{ $payment->event?->title ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                        {{ $pkgCfg['name'] ?? ($payment->package_name ? ucfirst(str_replace('_', ' ', $payment->package_name)) : 'Layanan Vola') }}
                                        @if ($payment->package_duration_days)
                                            <span class="block text-xs text-gray-400">{{ $payment->package_duration_days }} hari</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                        {{ $payment->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                        {{ $payment->payment_type ? strtoupper($payment->payment_type) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('eo.billing.show', $payment) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                Detail
                                            </a>
                                            <a href="{{ route('eo.billing.download', $payment) }}"
                                               target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                PDF
                                            </a>
                                            @if ($statusValue === 'pending' && $payment->snap_token)
                                                <button
                                                    type="button"
                                                    onclick="reopenSnap('{{ $payment->snap_token }}', '{{ $payment->id }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                                                    Bayar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination (only for paginator, not plain Collection) --}}
                @if ($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $payments->links() }}
                    </div>
                @endif
            @endif
        </div>

    @endif

    {{-- ================================================================
         ALPINE.JS COMPONENT + MIDTRANS SNAP
    ================================================================ --}}
    @push('scripts')
        {{-- Midtrans Snap.js — only the CLIENT key is exposed to the browser --}}
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ config('services.midtrans.client_key') }}"></script>

        <script>
            // Package catalogue mirrored from PHP (prices are validated backend-side)
            const PACKAGES = @json(array_values($packages));

            function billingApp() {
                return {
                    selectedPackage: '',
                    selectedEventId: '{{ $selectedEvent?->id ?? '' }}',
                    loading: false,
                    errorMsg: '',
                    paymentId: null,

                    init() {
                        // Restore from URL param if present
                        const params = new URLSearchParams(window.location.search);
                        if (params.get('event_id')) {
                            this.selectedEventId = params.get('event_id');
                        }
                    },

                    selectPackage(slug) {
                        this.selectedPackage = slug;
                        this.errorMsg = '';
                    },

                    getPackage() {
                        return PACKAGES.find(p => p.slug === this.selectedPackage) || null;
                    },

                    packageName() {
                        return this.getPackage()?.name ?? '';
                    },

                    packageDuration() {
                        return this.getPackage()?.duration_days ?? '';
                    },

                    packagePrice() {
                        const price = this.getPackage()?.price ?? 0;
                        return 'Rp ' + price.toLocaleString('id-ID');
                    },

                    async pay() {
                        if (!this.selectedEventId || !this.selectedPackage) {
                            this.errorMsg = 'Pilih event dan paket terlebih dahulu.';
                            return;
                        }

                        this.loading = true;
                        this.errorMsg = '';

                        try {
                            const resp = await fetch('{{ route('eo.billing.invoice.create') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    event_id: this.selectedEventId,
                                    package_slug: this.selectedPackage,
                                }),
                            });

                            const data = await resp.json();

                            if (!resp.ok) {
                                this.errorMsg = data.error || 'Terjadi kesalahan. Silakan coba lagi.';
                                this.loading = false;
                                return;
                            }

                            // Store payment ID for verify call after payment
                            this.paymentId = data.payment_id ?? null;

                            // Open Midtrans Snap popup with the token from backend
                            snap.pay(data.snap_token, {
                                onSuccess: () => {
                                    this.loading = false;
                                    // Navigate to server-side finish route which verifies & redirects.
                                    // Using window.location.href is more reliable than fetch+reload
                                    // because Midtrans may also redirect the browser simultaneously,
                                    // cancelling any in-flight async fetch calls.
                                    if (this.paymentId) {
                                        window.location.href = `/eo/billing/${this.paymentId}/finish`;
                                    } else {
                                        window.location.reload();
                                    }
                                },
                                onPending: () => {
                                    this.loading = false;
                                    window.location.reload();
                                },
                                onError: () => {
                                    this.loading = false;
                                    this.errorMsg = 'Pembayaran gagal. Silakan coba lagi.';
                                },
                                onClose: () => {
                                    this.loading = false;
                                    // Reload agar status terbaru dari webhook langsung terlihat
                                    window.location.reload();
                                },
                            });

                        } catch (e) {
                            this.loading = false;
                            this.errorMsg = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
                        }
                    },
                };
            }

            // Re-open Snap popup for an existing pending invoice
            function reopenSnap(token, paymentId) {
                if (typeof snap === 'undefined') {
                    alert('Midtrans Snap belum dimuat. Coba muat ulang halaman.');
                    return;
                }
                snap.pay(token, {
                    onSuccess: () => {
                        // Navigate to the server-side finish route which verifies status
                        // then redirects to billing index — more reliable than a fetch+reload
                        // because the Midtrans finish URL redirect may cancel async JS calls.
                        if (paymentId) {
                            window.location.href = `/eo/billing/${paymentId}/finish`;
                        } else {
                            window.location.reload();
                        }
                    },
                    onPending: () => window.location.reload(),
                    onError: () => {},
                    onClose: () => window.location.reload(),
                });
            }
        </script>
    @endpush

</x-layouts.eo>

