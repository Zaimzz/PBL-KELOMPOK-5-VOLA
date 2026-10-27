<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationUnderReview extends Notification
{
    use Queueable;
    protected $eventTitle;

    public function __construct($eventTitle = 'Bandung Creative Tech Summit 2025')
    {
        $this->eventTitle = $eventTitle;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'review',
            'title' => 'Berkas Sedang Ditinjau',
            'message' => 'Berkas lamaranmu untuk <strong>' . $this->eventTitle . '</strong> sedang dalam proses kurasi panitia.',
            'link' => '#',
            'time' => 'Kemarin, 14:15'
        ];
    }
}