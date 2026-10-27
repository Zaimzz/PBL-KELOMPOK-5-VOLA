<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileReminder extends Notification
{
    use Queueable;

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'reminder',
            'title' => 'Pengingat Profil',
            'message' => 'Lengkapi profil dan unggah CV kamu untuk meningkatkan peluang diterima di event favoritmu.',
            'link' => '#',
            'time' => '2 hari lalu'
        ];
    }
}