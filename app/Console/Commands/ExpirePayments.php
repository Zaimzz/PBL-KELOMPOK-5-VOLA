<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('payments:expire')]
#[Description('Menandai pembayaran pending yang melewati batas waktu sebagai kedaluwarsa')]
class ExpirePayments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expired = Payment::query()
            ->where('status', PaymentStatus::Pending)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->update(['status' => PaymentStatus::Expired]);

        $this->info("{$expired} pembayaran ditandai kedaluwarsa.");

        return self::SUCCESS;
    }
}
