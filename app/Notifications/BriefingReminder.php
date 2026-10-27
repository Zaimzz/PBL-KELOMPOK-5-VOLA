<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BriefingReminder extends Notification
{
    use Queueable;

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'briefing',
            'title' => 'Pengingat Briefing Online',
            'message' => 'Briefing teknis via Zoom akan diselenggarakan besok pukul 19.30 WIB. Tautan ruang pertemuan telah tersedia.',
            'link' => '#',
            'time' => '2 jam lalu'
        ];
    }
}