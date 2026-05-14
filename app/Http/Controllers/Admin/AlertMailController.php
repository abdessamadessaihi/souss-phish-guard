<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhishReport;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlertMailController extends Controller
{
    public function sendAlert(Request $request)
    {
        $request->validate([
            'target' => 'required|in:all,specific',
            'user_id' => 'nullable|exists:users,id',
            'alert_level' => 'required|in:danger,warning,info,success',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'report_id' => 'nullable|exists:phish_reports,id',
        ]);

        $targets = $request->target === 'all'
            ? User::where('role', 'user')->where('is_active', true)->get()
            : User::where('id', $request->user_id)->get();

        if ($targets->isEmpty()) {
            return back()->with('error', 'Aucun destinataire trouvé.');
        }

        $report = $request->filled('report_id')
            ? PhishReport::find($request->report_id)
            : null;

        $sent = 0;
        $errors = 0;

        foreach ($targets as $user) {
            try {
                $html = view('emails.alert', [
                    'user' => $user,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'report' => $report,
                    'alertLevel' => $request->alert_level,
                ])->render();

                Mail::html($html, function ($mail) use ($user, $request) {
                    $mail->to($user->email, $user->name)
                        ->subject('[SPG Sécurité] ' . $request->subject);
                });

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'system',
                    'title' => $request->subject,
                    'body' => Str::limit($request->message, 100),
                    'link' => '/user/dashboard',
                    'icon' => 'bi-envelope-exclamation-fill',
                    'color' => $request->alert_level === 'danger' ? 'red' : 'amber',
                ]);

                $sent++;
            } catch (\Exception $e) {
                Log::error('Alert mail error', ['user' => $user->email, 'error' => $e->getMessage()]);
                $errors++;
            }
        }

        $msg = "{$sent} alerte(s) envoyée(s) par email";
        if ($errors)
            $msg .= " ({$errors} échec(s))";
        return back()->with($sent > 0 ? 'success' : 'error', $msg);
    }
    public function exportCsv(Request $request)
    {
        $simulations = \App\Models\Simulation::with('results.user')->get();

        $csv = "\xEF\xBB\xBF"; // BOM UTF-8
        $csv .= "Campagne,Modèle,Date lancement,Statut campagne,";
        $csv .= "Agent,Email agent,Département,";
        $csv .= "Email ouvert,Date ouverture,";
        $csv .= "Lien cliqué,Date clic,";
        $csv .= "Données soumises,Date soumission,";
        $csv .= "A signalé,Date signalement,";
        $csv .= "Résultat,IP utilisateur\n";

        foreach ($simulations as $sim) {
            foreach ($sim->results as $r) {
                $outcome = match ($r->outcome) {
                    'submitted' => 'ÉCHEC CRITIQUE - A soumis ses données',
                    'clicked' => 'ÉCHEC - A cliqué sur le lien',
                    'reported' => 'SUCCÈS - A signalé le phishing',
                    default => 'NEUTRE - Aucune action',
                };

                $csv .= implode(',', [
                    '"' . str_replace('"', '""', $sim->name) . '"',
                    strtoupper($sim->template),
                    $sim->created_at->format('d/m/Y H:i'),
                    strtoupper($sim->status),
                    '"' . str_replace('"', '""', $r->user->name ?? 'N/A') . '"',
                    $r->user->email ?? 'N/A',
                    $r->user->department ?? 'N/A',
                    $r->email_opened ? 'OUI' : 'NON',
                    $r->opened_at ? $r->opened_at->format('d/m/Y H:i') : 'N/A',
                    $r->link_clicked ? 'OUI' : 'NON',
                    $r->clicked_at ? $r->clicked_at->format('d/m/Y H:i') : 'N/A',
                    $r->data_submitted ? 'OUI' : 'NON',
                    $r->submitted_at ? $r->submitted_at->format('d/m/Y H:i') : 'N/A',
                    $r->reported_phish ? 'OUI' : 'NON',
                    $r->reported_at ? $r->reported_at->format('d/m/Y H:i') : 'N/A',
                    '"' . $outcome . '"',
                    $r->user_ip ?? 'N/A',
                ]) . "\n";
            }
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="SPG-Rapport-Phishing-' . date('Ymd-His') . '.csv"');
    }

    public function sendCsvByEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $reports = PhishReport::with('user', 'reviewer')->latest()->get();
        $csv = "\xEF\xBB\xBF";
        $csv .= "ID,Date,Agent,Email,Type,Score IA,Sévérité,Statut\n";

        foreach ($reports as $r) {
            $csv .= implode(',', [
                $r->id,
                $r->created_at->format('d/m/Y'),
                '"' . str_replace('"', '""', $r->user->name ?? 'N/A') . '"',
                $r->user->email ?? 'N/A',
                strtoupper($r->type),
                ($r->ai_risk_score ?? 0) . '/100',
                strtoupper($r->severity ?? 'N/A'),
                $r->status,
            ]) . "\n";
        }

        try {
            $total = $reports->count();
            $critical = $reports->where('severity', 'critical')->count();
            $bodyHtml = "
            <div style='font-family:Segoe UI,Arial,sans-serif;max-width:500px;margin:20px auto;padding:30px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;'>
                <h2 style='color:#0f172a;margin-bottom:8px;'>📊 Rapport SPG — " . now()->format('d/m/Y') . "</h2>
                <p style='color:#64748b;margin-bottom:20px;'>Rapport des alertes de sécurité Souss Phish Guard.</p>
                <div style='background:#fff;padding:16px;border-radius:6px;border:1px solid #e2e8f0;margin-bottom:20px;'>
                    <div style='display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;'>
                        <span style='color:#64748b;'>Total signalements</span><strong>{$total}</strong>
                    </div>
                    <div style='display:flex;justify-content:space-between;padding:8px 0;'>
                        <span style='color:#64748b;'>Critiques</span><strong style='color:#dc2626;'>{$critical}</strong>
                    </div>
                </div>
                <p style='color:#64748b;font-size:12px;'>Le fichier CSV détaillé est joint à cet email.</p>
            </div>";

            Mail::html($bodyHtml, function ($mail) use ($request, $csv) {
                $mail->to($request->email)
                    ->subject('[SPG] Rapport CSV Alertes — ' . now()->format('d/m/Y'))
                    ->attachData($csv, 'SPG-Alertes-' . date('Ymd') . '.csv', ['mime' => 'text/csv']);
            });

            return back()->with('success', "Rapport CSV envoyé à {$request->email}");
        } catch (\Exception $e) {
            Log::error('CSV mail error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur SMTP : ' . $e->getMessage() . ' — Vérifiez votre App Password Gmail dans .env');
        }
    }
}