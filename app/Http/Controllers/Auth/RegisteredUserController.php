<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'department' => ['nullable', 'string', 'max:100'],
            'locale' => ['nullable', 'in:fr,en'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'locale' => $request->input('locale', 'fr'),
            'role' => 'user',
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'description' => 'Nouveau compte créé',
            'ip_address' => $request->ip(),
        ]);

        return redirect('/user/dashboard');
    }
}