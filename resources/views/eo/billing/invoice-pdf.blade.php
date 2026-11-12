<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $payment->invoice_number }} — VOLA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; padding: 40px; background: #f8fafc; color: #1e293b; }
        .invoice-card { max-width: 820px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 48px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 24px; margin-bottom: 32px; }
        .logo { font-size: 28px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px; }
        .logo span { color: #0f172a; }
        .logo-sub { margin: 4px 0 0; color: #64748b; font-size: 12px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0; font-size: 22px; color: #0f172a; }
        .invoice-title p { margin: 4px 0 0; color: #64748b; font-size: 13px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700; margin-top: 8px; }
        .badge.paid { background: #dcfce7; color: #15803d; }
        .badge.pending { background: #fef3c7; color: #b45309; }
        .badge.other { background: #f1f5f9; color: #64748b; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px; }
        .meta-col h3 { font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; margin: 0 0 8px; }
        .meta-col p { margin: 0; font-size: 14px; font-weight: 600; color: #1e293b; line-height: 1.6; }
        .meta-col span { font-size: 13px; color: #64748b; font-weight: 400; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        th { background: #f8fafc; text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 700; color: #475569; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px; font-size: 14px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .total-box { display: flex; justify-content: flex-end; margin-bottom: 40px; }
        .total-row { width: 300px; display: flex; justify-content: space-between; padding: 7px 0; font-size: 14px; }
        .total-row.grand { border-top: 2px solid #e2e8f0; padding-top: 12px; font-size: 18px; font-weight: 800; color: #4f46e5; }
        .footer { border-top: 1px solid #f1f5f9; padding-top: 24px; text-align: center; font-size: 12px; color: #94a3b8; }
        .print-btn { background: #4f46e5; color: white; border: none; padding: 10px 24px; font-size: 13px; font-weight: 600; border-radius: 8px; cursor: pointer; float: right; margin-bottom: 20px; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-card { border: none; box-shadow: none; padding: 0; }
            .print-btn { display: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div style="max-width: 820px; margin: 0 auto 16px;" class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <div style="clear: both;"></div>
    </div>

    <div class="invoice-card">
        {{-- Header --}}
        <div class="header">
            <div>
                <div class="logo">VOLA<span>.</span></div>
                <p class="logo-sub">Platform Kolaborasi Relawan & Event Indonesia</p>
            </div>
            <div class="invoice-title">
                <h1>INVOICE RESMI</h1>
                <p>#{{ $payment->invoice_number }}</p>
                @php
                    $sv = $payment->status instanceof \App\Enums\PaymentStatus ? $payment->status->value : (string) $payment->status;
                @endphp
                <span class="badge {{ $sv === 'paid' ? 'paid' : ($sv === 'pending' ? 'pending' : 'other') }}">
                    {{ match ($sv) { 'paid' => 'LUNAS', 'pending' => 'MENUNGGU PEMBAYARAN', 'expired' => 'KEDALUWARSA', 'cancelled' => 'DIBATALKAN', 'failed' => 'GAGAL', default => strtoupper($sv) } }}
                </span>
            </div>
        </div>

        {{-- Meta grid --}}
        <div class="meta-grid">
            <div class="meta-col">
                <h3>Ditagihkan Kepada:</h3>
                <p>{{ $payment->event?->organizer?->organization_name ?? 'Pihak Penyelenggara' }}</p>
                <span>PIC: {{ $payment->event?->organizer?->pic_name ?? '—' }} ({{ $payment->event?->organizer?->pic_phone ?? '—' }})</span><br>
                <span>{{ $payment->event?->organizer?->city ?? 'Indonesia' }}</span>
            </div>
            <div class="meta-col">
                <h3>Rincian Transaksi:</h3>
                <span>Tanggal Terbit: </span><strong>{{ $payment->created_at->translatedFormat('d F Y, H:i') }} WIB</strong><br>
                <span>Order ID: </span><strong>{{ $payment->midtrans_order_id }}</strong><br>
                @if ($payment->payment_type)
                    <span>Metode Pembayaran: </span><strong>{{ strtoupper($payment->payment_type) }}</strong><br>
                @endif
                @if ($payment->paid_at)
                    <span>Waktu Lunas: </span><strong>{{ $payment->paid_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                @endif
            </div>
        </div>

        {{-- Item table --}}
        <table>
            <thead>
                <tr>
                    <th>DESKRIPSI LAYANAN</th>
                    <th>EVENT TERKAIT</th>
                    <th>DURASI</th>
                    <th style="text-align: right;">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Paket Publikasi {{ $package['name'] ?? ucfirst(str_replace('_', ' ', $payment->package_name ?? 'Layanan Vola')) }}</strong>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                            Termasuk penayangan publik, sistem seleksi relawan, dan analitik penerimaan.
                        </div>
                    </td>
                    <td>{{ $payment->event?->title ?? '—' }}</td>
                    <td>
                        @if ($payment->package_duration_days)
                            {{ $payment->package_duration_days }} hari
                        @else
                            —
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: 700;">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Total box --}}
        <div class="total-box">
            <div>
                <div class="total-row">
                    <span style="color: #64748b;">Subtotal:</span>
                    <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span style="color: #64748b;">Pajak / PPN (0%):</span>
                    <span>Rp 0</span>
                </div>
                <div class="total-row grand">
                    <span>Total Bayar:</span>
                    <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            Dokumen ini dibuat otomatis secara digital oleh VOLA Platform Relawan Indonesia dan sah tanpa tanda tangan basah.<br>
            &copy; {{ date('Y') }} VOLA Platform. All rights reserved.
        </div>
    </div>

</body>
</html>

