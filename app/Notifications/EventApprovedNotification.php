<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Event $event) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => 'Pengajuan Event Disetujui',
            'message' => "Pengajuan event '{$this->event->title}' telah disetujui dan kini aktif dipublikasikan.",
            'status' => 'approved',
        ];
    }
}
