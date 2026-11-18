<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Mengambil daftar notifikasi
    public function index()
    {
        $user = Auth::user();

        // Ambil semua notifikasi dari tabel 'notifications' milik user ini
        // Urutkan dari yang terbaru
        $notifications = $user->notifications()->latest()->get()->map(function ($notif) {
            return [
                'id' => $notif->id,
                'type' => $notif->data['type'] ?? 'info',
                'title' => $notif->data['title'] ?? 'Notifikasi',
                'message' => $notif->data['message'] ?? '',
                'time' => $notif->created_at->diffForHumans(), // "2 jam yang lalu"
                'read' => ! is_null($notif->read_at), // true jika sudah dibaca
                'link' => $notif->data['link'] ?? '#',
            ];
        });

        return response()->json($notifications);
    }

    // Menandai satu notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // Menandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
