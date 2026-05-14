<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhishReport;
use App\Models\ActivityLog;
use App\Services\AIAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminReportController extends Controller
{
    public function __construct(protected AIAnalyzerService $ai)
    {
    }

    public function index()
    {
        $reports = PhishReport::with('user', 'reviewer')
            ->latest()->paginate(15);

        $stats = [
            'pending' => PhishReport::where('status', 'pending')->count(),
            'confirmed_phish' => PhishReport::where('status', 'confirmed_phish')->count(),
            'false_positive' => PhishReport::where('status', 'false_positive')->count(),
            'blocked' => PhishReport::where('status', 'blocked')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    public function show(PhishReport $report)
    {
        $report->load('user', 'reviewer');
        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, PhishReport $report)
    {
        $request->validate([
            'status' => 'required|in:pending,analyzing,confirmed_phish,false_positive,blocked',
            'admin_feedback' => 'nullable|string|max:1000',
            'severity' => 'nullable|in:low,medium,high,critical',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_feedback' => $request->admin_feedback,
            'severity' => $request->severity ?? $report->severity,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Notifier l'utilisateur du changement de statut

        \App\Services\NotificationService::reportReviewed(
            $report->user_id,
            $report->id,
            $request->status,
            $request->admin_feedback ?? ''
        );




        // Notifier l'utilisateur via message automatique
        if ($request->admin_feedback) {
            \App\Models\Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $report->user_id,
                'subject' => "Retour sur votre signalement #{$report->id}",
                'body' => $request->admin_feedback,
                'phish_report_id' => $report->id,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'report_reviewed',
            'target_type' => 'PhishReport',
            'target_id' => $report->id,
            'description' => "Statut changé en : {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Signalement mis à jour.');
    }
}