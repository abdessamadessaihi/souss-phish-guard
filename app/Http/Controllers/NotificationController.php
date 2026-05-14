<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Liste notifications de l'utilisateur connecté
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->limit(50)->get();

        return response()->json([
            'notifications' => $notifications,
            'unread' => $notifications->where('is_read', false)->count(),
        ]);
    }

    // Marquer une comme lue
    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    // Marquer toutes comme lues
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}