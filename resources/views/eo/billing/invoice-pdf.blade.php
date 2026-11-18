@php
    $sv = $payment->status instanceof \App\Enums\PaymentStatus ? $payment->status->value : (string) $payment->status;
    $statusLabel = match ($sv) {
        'paid' => 'LUNAS',
        'pending' => 'MENUNGGU PEMBAYARAN',
        'expired' => 'KEDALUWARSA',
        'cancelled' => 'DIBATALKAN',
        'failed' => 'GAGAL',
        default => strtoupper($sv),
    };
    $badgeClass = match ($sv) {
        'paid' => 'badge-paid',
        'pending' => 'badge-pending',
        default => 'badge-other',
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $payment->invoice_number }} — VOLA</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 32px 40px;
        }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .header-table td { vertical-align: top; }
        .logo { font-size: 26px; font-weight: bold; color: #4f46e5; }
        .logo span { color: #0f172a; }
        .logo-sub { color: #64748b; font-size: 10px; margin-top: 2px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 20px; margin: 0; color: #0f172a; }
        .invoice-title p { margin: 3px 0 0; color: #64748b; font-size: 11px; }
        .badge { display: inline-block; padding: 3px 10px; font-size: 9px; font-weight: bold; color: #ffffff; margin-top: 6px; }
        .badge-paid { background-color: #16a34a; }
        .badge-pending { background-color: #d97706; }
        .badge-other { background-color: #64748b; }
        .divider { border-top: 2px solid #e2e8f0; margin: 0 0 20px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { width: 50%; vertical-align: top; padding-right: 20px; }
        .meta-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin: 0 0 5px; }
        .meta-value { font-size: 12px; font-weight: bold; color: #1e293b; line-height: 1.6; margin: 0; }
        .meta-sub { font-size: 10px; color: #64748b; font-weight: normal; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items th { background-color: #f8fafc; text-align: left; padding: 9px 10px; font-size: 9px; color: #475569; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .items td { padding: 12px 10px; font-size: 11px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .items .amount { text-align: right; font-weight: bold; }
        .items .center { text-align: center; }
        .item-note { font-size: 10px; color: #64748b; margin-top: 4px; }
        .totals { width: 300px; border-collapse: collapse; margin-left: auto; margin-bottom: 24px; }
        .totals td { padding: 5px 0; font-size: 12px; }
        .totals .label { color: #64748b; }
        .totals .value { text-align: right; }
        .totals .grand td { border-top: 2px solid #e2e8f0; padding-top: 10px; font-size: 15px; font-weight: bold; color: #4f46e5; }
        .footer { border-top: 1px solid #f1f5f9; padding-top: 16px; text-align: center; font-size: 10px; color: #94a3b8; line-height: 1.6; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="logo">VOLA<span>.</span></div>
                <div class="logo-sub">Platform Kolaborasi Crew &amp; Event Indonesia</div>
            </td>
            <td class="invoice-title">
                <h1>INVOICE RESMI</h1>
                <p>#{{ $payment->invoice_number }}</p>
                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="meta-table">
        <tr>
            <td>
                <p class="meta-label">Ditagihkan Kepada</p>
                <p class="meta-value">{{ $payment->event?->organizer?->organization_name ?? 'Pihak Penyelenggara' }}</p>
                <p class="meta-value meta-sub">PIC: {{ $payment->event?->organizer?->pic_name ?? '—' }} ({{ $payment->event?->organizer?->pic_phone ?? '—' }})</p>
                <p class="meta-value meta-sub">{{ $payment->event?->organizer?->city ?? 'Indonesia' }}</p>
            </td>
            <td>
                <p class="meta-label">Rincian Transaksi</p>
                <p class="meta-value meta-sub">Tanggal Terbit: <strong>{{ $payment->created_at->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                <p class="meta-value meta-sub">Order ID: <strong>{{ $payment->midtrans_order_id }}</strong></p>
                @if ($payment->payment_type)
                    <p class="meta-value meta-sub">Metode Pembayaran: <strong>{{ strtoupper($payment->payment_type) }}</strong></p>
                @endif
                @if ($payment->paid_at)
                    <p class="meta-value meta-sub">Waktu Lunas: <strong>{{ $payment->paid_at->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>DESKRIPSI LAYANAN</th>
                <th>EVENT TERKAIT</th>
                <th class="center">DURASI</th>
                <th class="amount">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Paket Publikasi {{ $package['name'] ?? ucfirst(str_replace('_', ' ', $payment->package_name ?? 'Layanan Vola')) }}</strong>
                    <div class="item-note">Termasuk penayangan publik, sistem seleksi Crew, dan analitik penerimaan.</div>
                </td>
                <td>{{ $payment->event?->title ?? '—' }}</td>
                <td class="center">
                    {{ $payment->package_duration_days ? $payment->package_duration_days.' hari' : '—' }}
                </td>
                <td class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Pajak / PPN (0%):</td>
            <td class="value">Rp 0</td>
        </tr>
        <tr class="grand">
            <td>Total Bayar:</td>
            <td class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis secara digital oleh VOLA Platform Crew Indonesia dan sah tanpa tanda tangan basah.<br>
        &copy; {{ date('Y') }} VOLA Platform. All rights reserved.
    </div>

</body>
</html>
