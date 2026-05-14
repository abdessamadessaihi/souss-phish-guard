<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\AnalyzerController;
use App\Http\Controllers\User\TrainingController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSimulationController;
use App\Http\Controllers\Admin\AdminForensicController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AlertMailController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SimulationTrackController;

// ── Racine ── Landing Page
// ── LANDING PAGE ── (remplace la racine)
Route::get('/', function () {
    // Si connecté → dashboard direct
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect('/admin/dashboard')
            : redirect('/user/dashboard');
    }
    return view('welcome');
})->name('welcome');
// ── Changement de Langue ──
Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['fr', 'en']))
        $locale = 'fr';
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return redirect()->back();
})->name('lang.switch');

// ── Alias Laravel obligatoires ──
// ── Alias Laravel obligatoires ──
Route::get('/login', fn() => redirect('/user/login'))->name('login');
Route::get('/register', fn() => redirect('/user/register'))->name('register');
Route::post('/register', fn() => redirect('/user/register')); // ← AJOUTER CETTE LIGNE
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ── Tracking simulation (public) ──
Route::get('/track/open/{token}', [SimulationTrackController::class, 'trackOpen'])->name('track.open');
Route::get('/track/click/{token}', [SimulationTrackController::class, 'trackClick'])->name('track.click');
Route::get('/track/landing/{token}', [SimulationTrackController::class, 'landing'])->name('track.landing');
Route::post('/track/submit/{token}', [SimulationTrackController::class, 'trackSubmit'])->name('track.submit');

// ── Notifications (auth commun) ──
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::patch('/notifications/{n}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// ══════════════════════════════════════════
//  PORTAIL USER  /user/
// ══════════════════════════════════════════
Route::get('/user/login', [AuthenticatedSessionController::class, 'createUser'])->name('user.login');
Route::post('/user/login', [AuthenticatedSessionController::class, 'storeUser'])->name('user.login.submit');
Route::get('/user/register', [RegisteredUserController::class, 'create'])->name('user.register');
Route::post('/user/register', [RegisteredUserController::class, 'store'])->name('user.register.submit');

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Signalements
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Analyseur IA
    Route::get('/analyzer', [AnalyzerController::class, 'index'])->name('analyzer.index');
    Route::post('/analyzer/scan', [AnalyzerController::class, 'scan'])->name('analyzer.scan');

    // Formation
    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/{training}', [TrainingController::class, 'show'])->name('training.show');
    Route::post('/training/{training}/complete', [TrainingController::class, 'complete'])->name('training.complete');
    Route::get('/training/{training}/certificate', [TrainingController::class, 'certificate'])->name('training.certificate');

    // Messagerie
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
});

// ══════════════════════════════════════════
//  PORTAIL ADMIN  /admin/
// ══════════════════════════════════════════
Route::get('/admin/login', [AuthenticatedSessionController::class, 'createAdmin'])->name('admin.login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'storeAdmin'])->name('admin.login.submit');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Utilisateurs
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');

    // Signalements
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.status');

    // Alertes Email — DANS le groupe admin
    Route::get('/alerts/export-csv', [AlertMailController::class, 'exportCsv'])->name('alerts.csv');
    Route::post('/alerts/send', [AlertMailController::class, 'sendAlert'])->name('alerts.send');
    Route::post('/alerts/send-csv', [AlertMailController::class, 'sendCsvByEmail'])->name('alerts.sendCsv');

    // Simulations
    Route::get('/simulations', [AdminSimulationController::class, 'index'])->name('simulations.index');
    Route::get('/simulations/create', [AdminSimulationController::class, 'create'])->name('simulations.create');
    Route::post('/simulations', [AdminSimulationController::class, 'store'])->name('simulations.store');
    Route::get('/simulations/{simulation}', [AdminSimulationController::class, 'show'])->name('simulations.show');
    Route::get('/simulations/{simulation}/edit', [AdminSimulationController::class, 'edit'])->name('simulations.edit');
    Route::put('/simulations/{simulation}', [AdminSimulationController::class, 'update'])->name('simulations.update');
    Route::delete('/simulations/{simulation}', [AdminSimulationController::class, 'destroy'])->name('simulations.destroy');
    Route::post('/simulations/{simulation}/launch', [AdminSimulationController::class, 'launch'])->name('simulations.launch');

    // IA Forensic
    Route::get('/forensic', [AdminForensicController::class, 'index'])->name('forensic.index');
    Route::post('/forensic/analyze', [AdminForensicController::class, 'analyze'])->name('forensic.analyze');

    // Messagerie admin
    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [AdminMessageController::class, 'store'])->name('messages.store');
});