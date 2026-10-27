<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Ambil semua notifikasi (untuk polling)
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->take(10)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->data['type'],
                'title' => $notification->data['title'],
                'message' => $notification->data['message'],
                'link' => $notification->data['link'],
                'time' => $notification->data['time'],
                'read' => !is_null($notification->read_at),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count()
        ]);
    }

    // Tandai 1 notifikasi sebagai dibaca
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    // Tandai SEMUA notifikasi sebagai dibaca
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}