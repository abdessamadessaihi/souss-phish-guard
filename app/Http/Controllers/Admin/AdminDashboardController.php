<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhishReport;
use App\Models\Simulation;
use App\Models\SimulationResult;
use App\Models\User;
use App\Models\ActivityLog;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats globales pertinentes pour l'admin
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'pending_reports' => PhishReport::where('status', 'pending')->count(),
            'confirmed_phish' => PhishReport::where('status', 'confirmed_phish')->count(),
            'total_simulations' => Simulation::count(),
            'running_simulations' => Simulation::where('status', 'running')->count(),
            'total_clicks' => SimulationResult::where('link_clicked', true)->count(),
            'total_reported' => SimulationResult::where('reported_phish', true)->count(),
            'click_rate' => $this->globalClickRate(),
            'report_rate' => $this->globalReportRate(),
            'high_risk_users' => $this->highRiskUsers(),
            'avg_vigilance' => round(User::where('role', 'user')->avg('vigilance_score') ?? 0),
        ];

        $recentReports = PhishReport::with('user')->latest()->limit(6)->get();
        $recentUsers = User::where('role', 'user')->latest()->limit(5)->get();

        // Données graphique par département
        $deptStats = User::where('role', 'user')
            ->selectRaw('department, COUNT(*) as total, AVG(vigilance_score) as avg_score')
            ->groupBy('department')
            ->get();

        // Activité 7 jours
        $weeklyReports = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyReports[] = [
                'date' => $date->format('D'),
                'reports' => PhishReport::whereDate('created_at', $date)->count(),
                'phish' => PhishReport::whereDate('created_at', $date)->where('status', 'confirmed_phish')->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'recentReports', 'recentUsers', 'deptStats', 'weeklyReports'));
    }

    private function globalClickRate(): float
    {
        $targets = SimulationResult::count();
        if ($targets === 0)
            return 0;
        return round((SimulationResult::where('link_clicked', true)->count() / $targets) * 100, 1);
    }

    private function globalReportRate(): float
    {
        $targets = SimulationResult::count();
        if ($targets === 0)
            return 0;
        return round((SimulationResult::where('reported_phish', true)->count() / $targets) * 100, 1);
    }

    private function highRiskUsers(): int
    {
        return User::where('role', 'user')->where('vigilance_score', '<', 40)->count();
    }
}