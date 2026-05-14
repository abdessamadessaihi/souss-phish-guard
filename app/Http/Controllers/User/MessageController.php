<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->orWhere('sender_id', auth()->id())
            ->with(['sender', 'receiver'])
            ->latest()->paginate(15);

        $unread = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)->count();

        $admins = User::where('role', 'admin')->where('is_active', true)->get();

        return view('user.messages.index', compact('messages', 'unread', 'admins'));
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
            'subject' => $request->subject ?? 'Message SOC',
            'body' => $request->body,
        ]);

        // Notification — body toujours une string
        $receiver = User::find($request->receiver_id);
        if ($receiver) {
            NotificationService::message(
                $receiver,
                auth()->user(),
                Str::limit($request->body, 80)
            );
        }

        return redirect()->route('user.messages.index')
            ->with('success', 'Message envoyé avec succès.');
    }

    public function show(Message $message)
    {
        abort_if(
            $message->sender_id !== auth()->id() &&
            $message->receiver_id !== auth()->id(),
            403
        );

        if ($message->receiver_id === auth()->id() && !$message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }

        return view('user.messages.show', compact('message'));
    }
}