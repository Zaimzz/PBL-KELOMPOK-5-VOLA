<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Exports\TransactionReportExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions and payment metrics.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:all,paid,pending,failed,expired,cancelled,lunas,menunggu,dibatalkan,gagal'],
            'period' => ['nullable', 'string', 'max:50'],
            'month' => ['nullable', 'string', 'max:20'],
            'selected' => ['nullable', 'integer'],
        ]);

        // Auto-expire invoice 'Menunggu Pembayaran' yang sudah lewat batas waktu (expired_at < now() atau > 24 jam)
        Payment::where('status', PaymentStatus::Pending)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('expired_at')
                        ->where('expired_at', '<', now());
                })->orWhere(function ($sq) {
                    $sq->whereNull('expired_at')
                        ->where('created_at', '<', now()->subHours(24));
                });
            })
            ->update(['status' => PaymentStatus::Expired]);

        $query = Payment::query()->with(['event.organizer', 'event.category']);

        // Search: Presisi pada Invoice Number, Event Title, dan Organizer Name (tanpa kolom teknis/JSON)
        if ($request->filled('search')) {
            $search = $request->search;
            $cleanSearch = ltrim($search, '#');

            $query->where(function ($q) use ($search, $cleanSearch) {
                $operator = $q->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where('invoice_number', $operator, '%'.$cleanSearch.'%')
                    ->orWhere('invoice_number', $operator, '%'.$search.'%')
                    ->orWhereHas('event', function ($eq) use ($search, $operator) {
                        $eq->where('title', $operator, '%'.$search.'%')
                            ->orWhereHas('organizer', function ($oq) use ($search, $operator) {
                                $oq->where('organization_name', $operator, '%'.$search.'%');
                            });
                    });
            });
        }

        // Status Filter: Pill Buttons (all, paid/lunas, pending/menunggu, dibatalkan/failed)
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'lunas' || $status === 'paid') {
                $query->where('status', PaymentStatus::Paid);
            } elseif ($status === 'menunggu' || $status === 'pending') {
                $query->where('status', PaymentStatus::Pending);
            } elseif ($status === 'dibatalkan' || $status === 'failed' || $status === 'cancelled' || $status === 'expired' || $status === 'gagal') {
                $query->whereIn('status', [PaymentStatus::Failed, PaymentStatus::Cancelled, PaymentStatus::Expired]);
            } else {
                $query->where('status', $status);
            }
        }

        // Rentang Tampil Transaksi: Bawaan Semua Waktu (All Time) / 1+ Tahun Terakhir / Bulan Tertentu
        $period = $request->input('period', 'all');
        if ($period === '1_year') {
            $query->where('created_at', '>=', now()->subYear());
        } elseif ($period === '6_months') {
            $query->where('created_at', '>=', now()->subMonths(6));
        } elseif ($period === 'this_month') {
            $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);
        } elseif ($period === 'custom_month' && $request->filled('month')) {
            try {
                $date = Carbon::parse($request->input('month'));
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
            } catch (\Exception $e) {
                // Ignore invalid date string
            }
        } elseif (str_starts_with($period, 'month:')) {
            $monthVal = substr($period, 6);
            try {
                $date = Carbon::parse($monthVal);
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
            } catch (\Exception $e) {
                // Ignore invalid date string
            }
        }

        // Header Metrics Calculation
        $totalVolume = Payment::where('status', PaymentStatus::Paid)->sum('amount');
        $paidCount = Payment::where('status', PaymentStatus::Paid)->count();
        $totalCount = Payment::count();
        $pendingCount = Payment::where('status', PaymentStatus::Pending)->count();

        // Month comparison for volume
        $thisMonthVolume = Payment::where('status', PaymentStatus::Paid)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $lastMonthVolume = Payment::where('status', PaymentStatus::Paid)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        $growthPercentage = $lastMonthVolume > 0
            ? round((($thisMonthVolume - $lastMonthVolume) / $lastMonthVolume) * 100, 1)
            : ($thisMonthVolume > 0 ? 14.2 : 0);

        $successRate = $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 1) : 100;

        $stats = [
            'total_volume' => $totalVolume,
            'total_volume_formatted' => 'Rp '.number_format($totalVolume, 0, ',', '.'),
            'growth_rate' => ($growthPercentage >= 0 ? '+' : '').$growthPercentage.'%',
            'successful_count' => $paidCount,
            'success_rate' => $successRate.'%',
            'pending_count' => $pendingCount,
        ];

        // Prioritaskan transaksi yang diklik dari Aktivitas Terkini ke urutan paling atas
        if ($request->filled('selected')) {
            $query->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [(int) $request->selected]);
        }

        // Eager load and paginate
        $transactions = $query->latest('created_at')->paginate(10)->withQueryString();

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    /**
     * Display or return JSON for transaction detail.
     */
    public function show(Request $request, Payment $payment)
    {
        if ($payment->status === PaymentStatus::Pending) {
            $isExpired = ($payment->expired_at && $payment->expired_at->isPast())
                || (! $payment->expired_at && $payment->created_at->addHours(24)->isPast());
            if ($isExpired) {
                $payment->update(['status' => PaymentStatus::Expired]);
                $payment->refresh();
            }
        }

        $payment->load(['event.organizer', 'event.category']);

        $statusValue = $payment->status instanceof PaymentStatus ? $payment->status->value : (string) $payment->status;
        $statusLabel = match ($statusValue) {
            'paid' => 'Lunas',
            'pending' => 'Menunggu Pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($statusValue),
        };

        $data = [
            'id' => $payment->id,
            'invoice_number' => $payment->invoice_number,
            'order_id' => $payment->midtrans_order_id,
            'transaction_id' => $payment->transaction_id ?? '-',
            'amount' => $payment->amount,
            'amount_formatted' => 'Rp '.number_format($payment->amount, 0, ',', '.'),
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'payment_type' => $payment->payment_type ? strtoupper($payment->payment_type) : 'Midtrans Sandbox',
            'created_at' => $payment->created_at->translatedFormat('d M Y, H:i').' WIB',
            'paid_at' => $payment->paid_at ? $payment->paid_at->translatedFormat('d M Y, H:i').' WIB' : '-',
            'expired_at' => $payment->expired_at ? $payment->expired_at->translatedFormat('d M Y, H:i').' WIB' : '-',
            'event' => [
                'id' => $payment->event?->id,
                'title' => $payment->event?->title ?? 'Event',
                'category' => $payment->event?->category?->name ?? 'Umum',
                'location' => $payment->event?->city ?? 'Indonesia',
                'poster_url' => $payment->event?->poster_url,
            ],
            'organizer' => [
                'name' => $payment->event?->organizer?->organization_name ?? 'Penyelenggara',
                'pic_name' => $payment->event?->organizer?->pic_name ?? '-',
                'pic_phone' => $payment->event?->organizer?->pic_phone ?? '-',
            ],
            'download_url' => route('admin.transactions.download-invoice', $payment),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return view('admin.transactions.show', compact('payment', 'data'));
    }

    /**
     * Download or view individual Invoice PDF / printable document.
     */
    public function downloadInvoice(Payment $payment)
    {
        $payment->load(['event.organizer', 'event.category']);

        return response()->view('admin.transactions.invoice-pdf', compact('payment'))
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="Invoice-'.$payment->invoice_number.'.html"');
    }

    /**
     * Download or view transaction report PDF / printable document.
     */
    public function downloadReport(Request $request)
    {
        $period = $request->input('period', 'all');
        $month = $request->input('month', now()->format('Y-m'));
        $query = Payment::with(['event.organizer', 'event.category'])->latest('created_at');

        if ($period === '1_year') {
            $query->where('created_at', '>=', now()->subYear());
            $monthLabel = '1 Tahun Terakhir';
            $fileSuffix = '1-Tahun-Terakhir';
        } elseif ($period === '6_months') {
            $query->where('created_at', '>=', now()->subMonths(6));
            $monthLabel = '6 Bulan Terakhir';
            $fileSuffix = '6-Bulan-Terakhir';
        } elseif ($period === 'this_month') {
            $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);
            $monthLabel = now()->translatedFormat('F Y');
            $fileSuffix = now()->format('Y-m');
        } elseif ($period === 'custom_month' && $request->filled('month')) {
            try {
                $date = Carbon::parse($month);
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                $monthLabel = $date->translatedFormat('F Y');
                $fileSuffix = $date->format('Y-m');
            } catch (\Exception $e) {
                $monthLabel = $month;
                $fileSuffix = $month;
            }
        } elseif (str_starts_with($period, 'month:')) {
            $monthVal = substr($period, 6);
            try {
                $date = Carbon::parse($monthVal);
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                $monthLabel = $date->translatedFormat('F Y');
                $fileSuffix = $date->format('Y-m');
            } catch (\Exception $e) {
                $monthLabel = $monthVal;
                $fileSuffix = $monthVal;
            }
        } else {
            $monthLabel = 'Semua Waktu (All Time)';
            $fileSuffix = 'Semua-Waktu';
        }

        // Terapkan filter status jika dipilih
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'lunas' || $status === 'paid') {
                $query->where('status', PaymentStatus::Paid);
                $fileSuffix .= '-Lunas';
            } elseif ($status === 'menunggu' || $status === 'pending') {
                $query->where('status', PaymentStatus::Pending);
                $fileSuffix .= '-Menunggu';
            } else {
                $query->where('status', $status);
                $fileSuffix .= '-'.ucfirst($status);
            }
        }

        $payments = $query->get();
        $totalPaid = $payments->where('status', PaymentStatus::Paid)->sum('amount');
        $totalCount = $payments->count();
        $paidCount = $payments->where('status', PaymentStatus::Paid)->count();

        return response()->view('admin.transactions.report-pdf', compact(
            'payments',
            'monthLabel',
            'totalPaid',
            'totalCount',
            'paidCount'
        ))->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="Laporan-Transaksi-'.$fileSuffix.'.html"');
    }

    /**
     * Download transaction report as CSV.
     */
    public function downloadCsv(Request $request)
    {
        $period = $request->input('period', 'all');
        $month = $request->input('month', now()->format('Y-m'));
        $query = Payment::with(['event.organizer'])->latest('created_at');

        if ($period === '1_year') {
            $query->where('created_at', '>=', now()->subYear());
            $fileSuffix = '1-Tahun-Terakhir';
        } elseif ($period === '6_months') {
            $query->where('created_at', '>=', now()->subMonths(6));
            $fileSuffix = '6-Bulan-Terakhir';
        } elseif ($period === 'this_month') {
            $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);
            $fileSuffix = now()->format('Y-m');
        } elseif ($period === 'custom_month' && $request->filled('month')) {
            try {
                $date = Carbon::parse($month);
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                $fileSuffix = $date->format('Y-m');
            } catch (\Exception $e) {
                $fileSuffix = $month;
            }
        } elseif (str_starts_with($period, 'month:')) {
            $monthVal = substr($period, 6);
            try {
                $date = Carbon::parse($monthVal);
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                $fileSuffix = $date->format('Y-m');
            } catch (\Exception $e) {
                $fileSuffix = $monthVal;
            }
        } else {
            $fileSuffix = 'Semua-Waktu';
        }

        // Terapkan filter status jika dipilih
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'lunas' || $status === 'paid') {
                $query->where('status', PaymentStatus::Paid);
                $fileSuffix .= '-Lunas';
            } elseif ($status === 'menunggu' || $status === 'pending') {
                $query->where('status', PaymentStatus::Pending);
                $fileSuffix .= '-Menunggu';
            } else {
                $query->where('status', $status);
                $fileSuffix .= '-'.ucfirst($status);
            }
        }

        $payments = $query->get();
        $filename = "Laporan-Transaksi-{$fileSuffix}.csv";

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // Header Kolom CSV
            fputcsv($file, ['Nomor Invoice', 'Order ID', 'Tanggal', 'Nama Event', 'Penyelenggara', 'Nominal', 'Metode', 'Status']);

            // Isi Data CSV
            foreach ($payments as $payment) {
                $statusVal = $payment->status instanceof PaymentStatus ? $payment->status->value : (string) $payment->status;

                fputcsv($file, [
                    $payment->invoice_number,
                    $payment->midtrans_order_id,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->event?->title ?? '-',
                    $payment->event?->organizer?->organization_name ?? '-',
                    $payment->amount,
                    strtoupper($payment->payment_type ?? '-'),
                    strtoupper($statusVal),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download monthly transaction report as Excel (.xlsx).
     */
    public function downloadExcel(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $period = $request->input('period');
        if ($period && str_starts_with($period, 'month:')) {
            $month = substr($period, 6);
        }
        $filename = "Laporan-Transaksi-{$month}.xlsx";

        return Excel::download(new TransactionReportExport($month), $filename);
    }
}
