<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->withCount('phishReports')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'department' => 'nullable|string|max:100',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'role' => $request->role,
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_created',
            'target_type' => 'User',
            'target_id' => $user->id,
            'description' => "Utilisateur créé : {$user->email}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} créé.");
    }

    public function show(User $user)
    {
        $user->load('phishReports', 'activityLogs');
        $logs = ActivityLog::where('user_id', $user->id)->latest()->limit(20)->get();
        return view('admin.users.show', compact('user', 'logs'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department' => 'nullable|string|max:100',
            'role' => 'required|in:user,admin',
            'vigilance_score' => 'nullable|integer|min:0|max:100',
            'is_active' => 'nullable|in:0,1',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'role' => $request->role,
            'vigilance_score' => $request->vigilance_score ?? $user->vigilance_score,
            'is_active' => $request->filled('is_active') ? (bool) $request->is_active : $user->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Agent {$user->name} mis à jour avec succès.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Impossible de supprimer votre propre compte.');
        $name = $user->name;
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$name} supprimé.");
    }

    public function toggle(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activé' : 'désactivé';
        return redirect()->back()->with('success', "Compte {$status}.");
    }
}