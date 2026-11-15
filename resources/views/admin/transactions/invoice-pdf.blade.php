<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $payment->invoice_number }} — VOLA</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            @page { margin: 1cm; size: portrait; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 print:bg-white text-slate-800 p-4 sm:p-10 print:p-0 antialiased selection:bg-brand-200 selection:text-brand-900">

    <!-- Top Action Bar (Hidden in Print Mode) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center print:hidden no-print">
        <button type="button" onclick="window.close()" 
                class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all shadow-xs flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </button>
        <button type="button" onclick="window.print()" 
                class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-all shadow-xs flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak PDF
        </button>
    </div>

    <!-- Main Invoice Card (Matches report-pdf structure 100%) -->
    <div class="max-w-4xl mx-auto bg-white rounded-xl print:rounded-none border border-slate-200 print:border-none p-8 print:p-0 shadow-sm print:shadow-none print:max-w-full relative overflow-hidden">
        
        <!-- Header Kop Dokumen VOLA -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-5 mb-6">
            <div class="mb-4 sm:mb-0">
                <div class="text-2xl font-bold text-slate-900 tracking-tight leading-none">VOLA<span class="text-brand-600">.</span></div>
                <p class="mt-1 text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Platform Kolaborasi Crew & Event Indonesia</p>
            </div>
            <div class="sm:text-right">
                <h1 class="text-lg font-bold text-slate-900 uppercase tracking-tight">INVOICE RESMI</h1>
                <p class="mt-0.5 text-xs text-slate-500">Nomor Invoice: <strong class="text-slate-900 font-mono">#{{ $payment->invoice_number }}</strong></p>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $payment->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                        {{ $payment->status->value === 'paid' ? 'LUNAS' : 'MENUNGGU PEMBAYARAN' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Metadata Section (Ditagihkan Kepada & Rincian Transaksi) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <!-- Ditagihkan Kepada -->
            <div class="bg-white border border-slate-200 rounded-lg print:rounded-none p-4 print:p-3">
                <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Ditagihkan Kepada:</span>
                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $payment->event->organizer->organization_name ?? 'Penyelenggara Event' }}</h3>
                <div class="text-xs text-slate-600 mt-2 space-y-1">
                    <p><span class="text-slate-400">Nama PIC:</span> <strong class="text-slate-800">{{ $payment->event->organizer->pic_name ?? '-' }}</strong></p>
                    <p><span class="text-slate-400">Kontak:</span> <span class="text-slate-800">{{ $payment->event->organizer->pic_phone ?? '-' }}</span></p>
                    <p><span class="text-slate-400">Domisili / Kota:</span> <span class="text-slate-800">{{ $payment->event->organizer->city ?? 'Indonesia' }}</span></p>
                </div>
            </div>

            <!-- Rincian Transaksi -->
            <div class="bg-white border border-slate-200 rounded-lg print:rounded-none p-4 print:p-3">
                <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Rincian Transaksi:</span>
                <div class="text-xs text-slate-600 mt-2 space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Tanggal Terbit:</span>
                        <strong class="text-slate-900 whitespace-nowrap">{{ $payment->created_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Metode Bayar:</span>
                        <strong class="text-slate-900">{{ strtoupper($payment->payment_type ?: 'Midtrans Sandbox') }}</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Order ID:</span>
                        <strong class="text-slate-900 font-mono">{{ $payment->midtrans_order_id }}</strong>
                    </div>
                    @if($payment->paid_at)
                        <div class="flex justify-between items-center pt-1 border-t border-slate-100">
                            <span class="text-slate-400">Waktu Lunas:</span>
                            <strong class="text-emerald-700 whitespace-nowrap">{{ $payment->paid_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Layanan (Presisi Lebar Kolom 50% / 30% / 20% & whitespace-nowrap) -->
        <div class="border border-slate-200 rounded-lg print:rounded-none overflow-hidden mb-6">
            <table class="w-full text-left border-collapse text-xs print:text-[11px]">
                <thead>
                    <tr class="bg-slate-50 print:bg-transparent text-slate-600 border-b border-slate-200">
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider w-[50%]">Deskripsi Layanan</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider w-[30%]">Event Terkait</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider text-right w-[20%] whitespace-nowrap">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                    <tr>
                        <td class="py-4 px-4 align-top">
                            <div class="font-bold text-slate-900 text-sm">Biaya Layanan Publikasi & Kurasi Event VOLA</div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Termasuk kurasi kelayakan dokumen, penayangan publik di feed crew, modul seleksi kandidat, dan analitik kuota.
                            </p>
                        </td>
                        <td class="py-4 px-4 align-top">
                            <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $payment->event->title ?? '-' }}</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">{{ $payment->event->category->name ?? 'Kategori Umum' }}</div>
                        </td>
                        <td class="py-4 px-4 text-right align-top font-bold text-slate-900 whitespace-nowrap text-sm sm:text-base">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Perhitungan Pembayaran (Right-Aligned & Presisi) -->
        <div class="flex justify-end mb-8">
            <div class="w-full sm:w-80 border border-slate-200 rounded-lg p-4 bg-slate-50/50 print:bg-transparent">
                <div class="flex justify-between items-center text-xs text-slate-600 pb-2">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-900 whitespace-nowrap">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-xs text-slate-600 pb-2">
                    <span>Pajak / PPN (0%):</span>
                    <span class="font-semibold text-slate-900 whitespace-nowrap">Rp 0</span>
                </div>
                <div class="border-t border-slate-200 pt-2.5 mt-1 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Total Bayar:</span>
                    <span class="text-base sm:text-lg font-extrabold text-brand-600 whitespace-nowrap">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Dokumen -->
        <div class="border-t border-slate-200 pt-4 text-center">
            <p class="text-[10px] text-slate-400">
                Dokumen ini diterbitkan secara otomatis secara digital oleh VOLA Platform Manajemen & Rekrutmen Crew Indonesia dan sah tanpa tanda tangan basah.
            </p>
        </div>
    </div>

</body>
</html>
