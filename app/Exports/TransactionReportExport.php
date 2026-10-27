<?php

namespace App\Exports;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected ?string $month = null) {}

    public function collection(): Collection
    {
        $query = Payment::with(['event.organizer'])->latest('created_at');

        if ($this->month && $this->month !== 'all') {
            try {
                $date = Carbon::parse($this->month);
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
            } catch (\Exception $e) {
                // Abaikan jika format bulan tidak valid
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Order ID',
            'Tanggal Transaksi',
            'Nama Event',
            'Penyelenggara (EO)',
            'Nominal (Rp)',
            'Metode Pembayaran',
            'Status',
        ];
    }

    /**
     * @param  Payment  $payment
     */
    public function map($payment): array
    {
        $statusVal = $payment->status instanceof PaymentStatus
            ? $payment->status->value
            : (string) $payment->status;

        $statusLabel = match ($statusVal) {
            'paid' => 'LUNAS',
            'pending' => 'MENUNGGU PEMBAYARAN',
            'failed' => 'GAGAL',
            'expired' => 'KEDALUWARSA',
            'cancelled' => 'DIBATALKAN',
            default => strtoupper($statusVal),
        };

        return [
            '#'.$payment->invoice_number,
            $payment->midtrans_order_id ?? '-',
            $payment->created_at->format('d/m/Y H:i'),
            $payment->event?->title ?? '-',
            $payment->event?->organizer?->organization_name ?? '-',
            $payment->amount,
            strtoupper($payment->payment_type ?? 'MIDTRANS'),
            $statusLabel,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Brand Indigo
                ],
            ],
        ];
    }
}
