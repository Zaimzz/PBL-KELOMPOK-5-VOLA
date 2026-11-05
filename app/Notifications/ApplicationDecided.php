<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationDecided extends Notification
{
    use Queueable;

    /**
     * @param  Application  $application  The application that was decided.
     * @param  string  $decision  Either 'accepted' or 'rejected'.
     */
    public function __construct(
        public readonly Application $application,
        public readonly string $decision,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $eventTitle = $this->application->position?->event?->title ?? 'Event';
        $positionName = $this->application->position?->name ?? 'posisi';

        if ($this->decision === 'accepted') {
            return [
                'type' => 'application_accepted',
                'title' => 'Pendaftaran Diterima! 🎉',
                'message' => "Selamat! Pendaftaran Anda sebagai {$positionName} di event \"{$eventTitle}\" telah diterima.",
                'link' => '/lamaran-saya',
                'time' => now()->toISOString(),
            ];
        }

        return [
            'type' => 'application_rejected',
            'title' => 'Pendaftaran Tidak Diterima',
            'message' => "Mohon maaf, pendaftaran Anda sebagai {$positionName} di event \"{$eventTitle}\" tidak dapat diterima saat ini.",
            'link' => '/lamaran-saya',
            'time' => now()->toISOString(),
        ];
    }
}

