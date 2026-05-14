<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    // ── USER LOGIN ──────────────────────────────
    public function createUser()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect('/admin/dashboard')
                : redirect('/user/dashboard');
        }
        return view('auth.login');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->withInput();
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Compte admin — utilisez /admin/login'])->withInput();
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Compte désactivé. Contactez un administrateur.']);
        }

        $request->session()->regenerate();
        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Connexion utilisateur',
            'ip_address' => $request->ip(),
        ]);

        return redirect('/user/dashboard');
    }

    // ── ADMIN LOGIN ─────────────────────────────
    public function createAdmin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin())
                return redirect('/admin/dashboard');
            Auth::logout();
        }
        return view('auth.admin-login');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        $user = Auth::user();

        if (!$user->isAdmin()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Ce compte n\'a pas les droits Guardian.'])->withInput();
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Compte Guardian désactivé.']);
        }

        $request->session()->regenerate();
        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'admin_login',
            'description' => 'Connexion Guardian',
            'ip_address' => $request->ip(),
        ]);

        return redirect('/admin/dashboard');
    }

    // ── LOGOUT ──────────────────────────────────
    public function destroy(Request $request)
    {
        $isAdmin = Auth::check() && Auth::user()->isAdmin();

        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'Déconnexion',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($isAdmin ? '/admin/login' : '/user/login');
    }
}