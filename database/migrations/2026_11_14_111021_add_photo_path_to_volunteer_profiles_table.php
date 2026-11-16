<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Event;

class ApplicationAcceptedNotification extends Notification
{
    use Queueable;

    protected $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function via($notifiable): array
    {
        return ['database']; // Simpan ke database saja
    }

    public function toArray($notifiable): array
    {
        $event = $this->application->position->event;
        
        return [
            'type' => 'accepted',
            'title' => 'Lamaran Diterima!',
            'message' => "Selamat, kamu diterima sebagai <strong>{$this->application->position->name}</strong> untuk event <strong>{$event->title}</strong>.",
            'link' => route('my-events.index'), // Arahkan ke Event Saya
            'application_id' => $this->application->id,
            'event_id' => $event->id,
        ];
    }
}