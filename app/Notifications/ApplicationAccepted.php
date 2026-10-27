<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationAccepted extends Notification
{
    use Queueable;
    protected $eventTitle;

    public function __construct($eventTitle = 'Indonesia Comic & Pop Culture Con 2025')
    {
        $this->eventTitle = $eventTitle;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'accepted',
            'title' => 'Lamaran Diterima!',
            'message' => 'Selamat, kamu diterima sebagai <strong>Liaison Officer</strong> untuk event <strong>' . $this->eventTitle . '</strong>.',
            'link' => '#', // Dikosongkan sesuai permintaan
            'time' => '15 mnt lalu'
        ];
    }
}