<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PhishReport;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AIAnalyzerService;
use App\Services\VirusTotalService;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected AIAnalyzerService $ai,
        protected VirusTotalService $vt
    ) {
    }

    public function index()
    {
        $reports = PhishReport::where('user_id', auth()->id())
            ->latest()->paginate(10);
        return view('user.reports.index', compact('reports'));
    }

    public function create()
    {
        return view('user.reports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:url,email,sms,other',
            'content' => 'required|string|max:5000',
            'subject' => 'nullable|string|max:255',
            'sender_email' => 'nullable|email|max:255',
            'sender_ip' => 'nullable|ip',
            'email_headers' => 'nullable|string|max:10000',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'analyzing';

        // Analyse IA
        $ai = $this->ai->analyze($data['content'], $data['type']);
        $data['ai_analysis'] = $ai['analysis'];
        $data['ai_risk_score'] = $ai['score'];
        $data['severity'] = $ai['severity'];
        $data['status'] = 'pending';

        // VirusTotal si URL
        if ($data['type'] === 'url') {
            $data['virustotal_result'] = $this->vt->scanUrl($data['content']);
        }

        $report = PhishReport::create($data);

        auth()->user()->increment('reports_count');
        auth()->user()->update([
            'vigilance_score' => min(100, auth()->user()->vigilance_score + 10)
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'report_submitted',
            'target_type' => 'PhishReport',
            'target_id' => $report->id,
            'description' => "Signalement #{$report->id} soumis",
            'ip_address' => $request->ip(),
        ]);

        // Notifier admins
        NotificationService::notifyAdmins(
            'new_report',
            'Nouveau signalement #' . $report->id,
            auth()->user()->name . ' — ' . strtoupper($report->type) . ' — Score: ' . ($report->ai_risk_score ?? 0) . '%',
            '/admin/reports/' . $report->id,
            'bi-flag-fill',
            ($report->ai_risk_score ?? 0) >= 70 ? 'red' : 'amber'
        );

        return redirect()->route('user.reports.show', $report)
            ->with('success', 'Signalement soumis et analysé par l\'IA.');
    }

    public function show(PhishReport $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);
        return view('user.reports.show', compact('report'));
    }

    public function edit(PhishReport $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);
        abort_if($report->status !== 'pending', 403, 'Ce signalement ne peut plus être modifié.');
        return view('user.reports.edit', compact('report'));
    }

    public function update(Request $request, PhishReport $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);
        abort_if($report->status !== 'pending', 403);

        $data = $request->validate([
            'type' => 'required|in:url,email,sms,other',
            'content' => 'required|string|max:5000',
            'subject' => 'nullable|string|max:255',
            'sender_email' => 'nullable|email|max:255',
        ]);

        $ai = $this->ai->analyze($data['content'], $data['type']);
        $data['ai_analysis'] = $ai['analysis'];
        $data['ai_risk_score'] = $ai['score'];
        $data['severity'] = $ai['severity'];

        $report->update($data);

        return redirect()->route('user.reports.show', $report)
            ->with('success', 'Signalement mis à jour et re-analysé.');
    }

    public function destroy(PhishReport $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);
        abort_if($report->status !== 'pending', 403);
        $report->delete();
        return redirect()->route('user.reports.index')
            ->with('success', 'Signalement supprimé.');
    }
}