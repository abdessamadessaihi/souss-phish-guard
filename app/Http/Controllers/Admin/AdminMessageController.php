<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['sender', 'receiver'])
            ->where('receiver_id', auth()->id())
            ->orWhere('sender_id', auth()->id())
            ->latest()->paginate(20);

        $unread = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)->count();

        $users = User::where('role', 'user')->where('is_active', true)->get();

        // Marquer tout comme lu
        Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.messages.index', compact('messages', 'unread', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject ?? 'Message de l\'équipe sécurité',
            'body' => $request->body,
        ]);

        // Notify receiver
        $receiver = User::find($request->receiver_id);
        if ($receiver) {
            NotificationService::message($receiver, auth()->user(), $message->subject ?? 'Sans sujet');
        }

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message envoyé.');
    }
}