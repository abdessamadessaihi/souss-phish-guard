<?php
namespace App\Http\Controllers;

use App\Models\Simulation;
use App\Models\SimulationResult;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SimulationTrackController extends Controller
{
    // Pixel de tracking — email ouvert
    public function trackOpen(string $token)
    {
        $result = SimulationResult::where('unique_token', $token)->first();

        if ($result && !$result->email_opened) {
            $result->update([
                'email_opened' => true,
                'opened_at' => now(),
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Incrémenter compteur simulation
            $result->simulation->increment('opened_count');
        }

        // Retourner pixel 1x1 transparent
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200)->header('Content-Type', 'image/gif');
    }

    // Clic sur le lien
    public function trackClick(string $token)
    {
        $result = SimulationResult::where('unique_token', $token)->first();

        if ($result) {
            if (!$result->link_clicked) {
                $result->update([
                    'link_clicked' => true,
                    'clicked_at' => now(),
                    'outcome' => 'clicked',
                    'user_ip' => request()->ip(),
                ]);
                $result->simulation->increment('clicked_count');

                // Notifier l'admin
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'simulation',
                        'title' => '⚠️ Simulation — Lien cliqué',
                        'body' => "{$result->user->name} a cliqué sur le lien de simulation #{$result->simulation_id}",
                        'link' => '/admin/simulations/' . $result->simulation_id,
                        'icon' => 'bi-mouse2-fill',
                        'color' => 'orange',
                    ]);
                }
            }

            return redirect()->route('track.landing', $token);
        }

        return redirect('/');
    }

    // Page de révélation
    public function landing(string $token)
    {
        $result = SimulationResult::with('simulation')->where('unique_token', $token)->first();
        return view('simulation.landing', compact('result'));
    }

    // Soumission de données sur la page de phishing
    public function trackSubmit(Request $request, string $token)
    {
        $result = SimulationResult::where('unique_token', $token)->first();

        if ($result && !$result->data_submitted) {
            $result->update([
                'data_submitted' => true,
                'submitted_at' => now(),
                'outcome' => 'submitted',
            ]);
            $result->simulation->increment('submitted_count');

            // Notifier l'admin
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'simulation',
                    'title' => '🚨 Simulation — Données soumises',
                    'body' => "{$result->user->name} a soumis ses données sur la fausse page #{$result->simulation_id}",
                    'link' => '/admin/simulations/' . $result->simulation_id,
                    'icon' => 'bi-exclamation-triangle-fill',
                    'color' => 'red',
                ]);
            }
        }

        return redirect()->route('track.landing', $token);
    }
}