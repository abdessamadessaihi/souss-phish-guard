<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion utilisateur
     */
    public function showUserLogin()
    {
        return view('auth.login');
    }

    /**
     * Gérer la connexion utilisateur
     */
    public function loginUser(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Votre compte est désactivé.']);
        }

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Connexion utilisateur réussie',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('user.dashboard');
    }

    /**
     * Afficher le formulaire de connexion admin
     */
    public function showAdminLogin()
    {
        return view('auth.admin-login'); // Assurez-vous que cette vue existe ou utilisez auth.login
    }

    /**
     * Gérer la connexion admin
     */
    public function loginAdmin(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->isAdmin()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Accès réservé aux administrateurs.']);
        }

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Connexion administration réussie',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.dashboard');
    }
}
