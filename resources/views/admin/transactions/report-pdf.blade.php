<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Rekapitulasi Transaksi {{ $monthLabel }} — VOLA</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            @page { margin: 1cm; size: landscape; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="bg-slate-100 print:bg-white text-slate-800 p-4 sm:p-10 print:p-0 antialiased selection:bg-brand-200 selection:text-brand-900">

    <div class="max-w-5xl mx-auto mb-6 flex justify-between items-center print:hidden">
        <button type="button" onclick="window.close()" 
                class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </button>
        <button type="button" onclick="window.print()" 
                class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak PDF
        </button>
    </div>

    <!-- Menghapus rounded saat di-print (print:rounded-none) dan mengecilkan pading (print:p-0) -->
    <div class="max-w-5xl mx-auto bg-white rounded-xl print:rounded-none border border-slate-200 print:border-none p-8 print:p-0 shadow-sm print:shadow-none print:max-w-full relative overflow-hidden">
        
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-5 mb-5">
            <div class="mb-4 sm:mb-0">
                <div class="text-2xl font-bold text-slate-900 tracking-tight leading-none">VOLA<span class="text-brand-600">.</span></div>
                <p class="mt-1 text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Portal Keuangan Platform</p>
            </div>
            <div class="sm:text-right">
                <h1 class="text-lg font-bold text-slate-900 uppercase tracking-tight">REKAPITULASI TRANSAKSI</h1>
                <p class="mt-0.5 text-xs text-slate-500">Periode Laporan: <strong class="text-slate-900">{{ $monthLabel }}</strong></p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:gap-4 mb-6">
            <div class="bg-white border border-slate-200 rounded-lg print:rounded-none p-4 print:p-3">
                <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Total Volume</span>
                <h3 class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h3>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-lg print:rounded-none p-4 print:p-3">
                <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Transaksi Sukses</span>
                <h3 class="text-xl font-bold text-slate-900 mt-1">{{ $paidCount }} <span class="text-xs font-normal text-slate-500">/ {{ $totalCount }} Invoice</span></h3>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-lg print:rounded-none p-4 print:p-3">
                <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Waktu Ekstraksi</span>
                <h3 class="text-base font-bold text-slate-900 mt-1.5">{{ now()->translatedFormat('d M Y, H:i') }}</h3>
            </div>
        </div>

        <div class="border border-slate-200 rounded-lg print:rounded-none overflow-hidden mb-6">
            <table class="w-full text-left border-collapse text-xs print:text-[11px]">
                <thead>
                    <tr class="bg-slate-50 print:bg-transparent text-slate-600 border-b border-slate-200">
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider">No. Invoice</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider">Penyelenggara & Event</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider">Metode</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider text-center">Status</th>
                        <th class="py-2.5 px-4 font-semibold uppercase tracking-wider text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                    @forelse($payments as $tx)
                        <tr>
                            <td class="py-3 px-4 font-semibold text-slate-900">#{{ $tx->invoice_number }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $tx->event->title ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $tx->event->organizer->organization_name ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $tx->created_at->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ strtoupper($tx->payment_type ?: 'Midtrans') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $tx->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $tx->status->value === 'paid' ? 'LUNAS' : 'MENUNGGU' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">
                                Tidak ada transaksi yang tercatat pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 pt-4 text-center">
            <p class="text-[10px] text-slate-400">Dokumen Sistem Internal VOLA Platform</p>
        </div>
    </div>

</body>
</html>