<x-layouts.eo title="Detail Invoice {{ $payment->invoice_number }} — Vola">

    {{-- Breadcrumb --}}
    <div class="mb-5">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('eo.billing.index') }}" class="hover:text-brand-600 transition-colors">Pembayaran & Tagihan</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">#{{ $payment->invoice_number }}</span>
        </nav>
    </div>

    @php
        $statusValue = $payment->status instanceof \App\Enums\PaymentStatus
            ? $payment->status->value
            : (string) $payment->status;
        $statusConfig = match ($statusValue) {
            'paid'      => ['label' => 'Lunas',       'class' => 'bg-green-50 text-green-700 border border-green-200'],
            'pending'   => ['label' => 'Menunggu',    'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
            'failed'    => ['label' => 'Gagal',       'class' => 'bg-red-50 text-red-700 border border-red-200'],
            'expired'   => ['label' => 'Kedaluwarsa', 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
            'cancelled' => ['label' => 'Dibatalkan',  'class' => 'bg-gray-100 text-gray-500 border border-gray-200'],
            default     => ['label' => ucfirst($statusValue), 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
        };
    @endphp

    <div class="max-w-3xl">
        {{-- Header card --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-xl font-bold text-gray-900">Invoice Resmi</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusConfig['class'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">#{{ $payment->invoice_number }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('eo.billing.download', $payment) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Invoice (PDF)
                    </a>
                    @if ($statusValue === 'pending' && $payment->snap_token)
                        <button
                            type="button"
                            onclick="reopenSnap('{{ $payment->snap_token }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Bayar Sekarang
                        </button>
                    @endif
                </div>
            </div>

            {{-- Info grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Ditagihkan Kepada</p>
                    <p class="font-semibold text-gray-900">{{ $payment->event?->organizer?->organization_name ?? 'Penyelenggara' }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">PIC: {{ $payment->event?->organizer?->pic_name ?? '—' }}</p>
                    <p class="text-sm text-gray-500">{{ $payment->event?->organizer?->city ?? 'Indonesia' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Rincian Transaksi</p>
                    <div class="space-y-1 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal Invoice</span>
                            <span class="font-medium">{{ $payment->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Order ID</span>
                            <span class="font-mono text-xs">{{ $payment->midtrans_order_id }}</span>
                        </div>
                        @if ($payment->paid_at)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Waktu Lunas</span>
                                <span class="font-medium text-green-700">{{ $payment->paid_at->translatedFormat('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif
                        @if ($payment->payment_type)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Metode</span>
                                <span class="font-medium uppercase">{{ $payment->payment_type }}</span>
                            </div>
                        @endif
                        @if ($payment->expired_at && $statusValue === 'pending')
                            <div class="flex justify-between">
                                <span class="text-gray-400">Jatuh Tempo</span>
                                <span class="font-medium text-red-600">{{ $payment->expired_at->translatedFormat('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Item detail --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Rincian Layanan</p>

            <div class="border border-gray-100 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase">Deskripsi Layanan</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase">Event</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-900">
                                    Paket Publikasi {{ $package['name'] ?? ucfirst(str_replace('_', ' ', $payment->package_name ?? 'Layanan Vola')) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if ($payment->package_duration_days)
                                        Durasi publikasi {{ $payment->package_duration_days }} hari — termasuk penayangan publik, sistem seleksi relawan, dan analitik penerimaan.
                                    @else
                                        Biaya layanan publikasi & kurasi event VOLA.
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                {{ $payment->event?->title ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="flex justify-end mt-4">
                <div class="w-64 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Pajak/PPN (0%)</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-bold text-brand-600">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back link --}}
        <a href="{{ route('eo.billing.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Pembayaran & Tagihan
        </a>
    </div>

    @if ($statusValue === 'pending' && $payment->snap_token)
        @push('scripts')
            <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
            <script>
                function reopenSnap(token) {
                    snap.pay(token, {
                        onSuccess: () => window.location.reload(),
                        onPending: () => window.location.reload(),
                        onError: () => {},
                        onClose: () => {},
                    });
                }
            </script>
        @endpush
    @endif

</x-layouts.eo>

