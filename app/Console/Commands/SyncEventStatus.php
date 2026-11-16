<?php

namespace App\Console\Commands;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('events:sync-status')]
#[Description('Menutup event yang deadline pendaftarannya lewat dan menyelesaikan event yang tanggalnya lewat')]
class SyncEventStatus extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->toDateString();

        $closed = Event::query()
            ->where('status', EventStatus::Active)
            ->whereNotNull('registration_deadline')
            ->whereDate('registration_deadline', '<', $today)
            ->where(function ($query) use ($today): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->update(['status' => EventStatus::Closed]);

        $finished = Event::query()
            ->whereIn('status', [EventStatus::Active, EventStatus::Closed])
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', $today)
            ->update(['status' => EventStatus::Finished]);

        $this->info("{$closed} event ditutup, {$finished} event diselesaikan.");

        return self::SUCCESS;
    }
}
