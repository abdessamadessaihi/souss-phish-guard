<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simulation;
use App\Models\SimulationResult;
use App\Models\User;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AdminSimulationController extends Controller
{
    public function index()
    {
        $simulations = Simulation::with('creator')
            ->latest()->paginate(10);

        $stats = [
            'total' => Simulation::count(),
            'running' => Simulation::where('status', 'running')->count(),
            'completed' => Simulation::where('status', 'completed')->count(),
            'draft' => Simulation::where('status', 'draft')->count(),
        ];

        return view('admin.simulations.index', compact('simulations', 'stats'));
    }

    public function create()
    {
        $users = User::where('role', 'user')
            ->where('is_active', true)->get();
        return view('admin.simulations.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template' => 'required|in:microsoft,bank,hr,delivery,custom',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'from_name' => 'required|string|max:100',
            'from_email' => 'required|email',
            'targets' => 'required|array|min:1',
            'targets.*' => 'exists:users,id',
        ]);

        $simulation = Simulation::create([
            'created_by' => auth()->id(),
            'name' => $request->name,
            'template' => $request->template,
            'subject' => $request->subject,
            'body' => $request->body,
            'from_name' => $request->from_name,
            'from_email' => $request->from_email,
            'tracking_token' => Str::random(32),
            'status' => 'draft',
            'targets_count' => count($request->targets),
        ]);

        // Créer les résultats pour chaque cible
        foreach ($request->targets as $userId) {
            SimulationResult::create([
                'simulation_id' => $simulation->id,
                'user_id' => $userId,
                'unique_token' => Str::random(40),
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'simulation_created',
            'target_type' => 'Simulation',
            'target_id' => $simulation->id,
            'description' => "Simulation créée : {$simulation->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.simulations.show', $simulation)
            ->with('success', 'Simulation créée. Vous pouvez maintenant la lancer.');
    }

    public function show(Simulation $simulation)
    {
        $simulation->load('creator', 'results.user');
        $results = $simulation->results()->with('user')->get();

        $stats = [
            'opened' => $results->where('email_opened', true)->count(),
            'clicked' => $results->where('link_clicked', true)->count(),
            'submitted' => $results->where('data_submitted', true)->count(),
            'reported' => $results->where('reported_phish', true)->count(),
            'safe' => $results->where('outcome', 'safe')->count(),
        ];

        return view('admin.simulations.show', compact('simulation', 'results', 'stats'));
    }

    public function edit(Simulation $simulation)
    {
        abort_if($simulation->status === 'completed', 403, 'Impossible de modifier une simulation terminée.');
        $users = User::where('role', 'user')->where('is_active', true)->get();
        return view('admin.simulations.edit', compact('simulation', 'users'));
    }

    public function update(Request $request, Simulation $simulation)
    {
        abort_if($simulation->status === 'completed', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $simulation->update($request->only('name', 'subject', 'body', 'from_name', 'from_email'));

        return redirect()->route('admin.simulations.show', $simulation)
            ->with('success', 'Simulation mise à jour.');
    }

    public function destroy(Simulation $simulation)
    {
        $simulation->results()->delete();
        $simulation->delete();
        return redirect()->route('admin.simulations.index')
            ->with('success', 'Simulation supprimée.');
    }

    public function launch(Request $request, Simulation $simulation)
    {
        abort_if($simulation->status === 'completed', 403);

        $results = $simulation->results()->with('user')->get();
        if ($results->isEmpty()) {
            return back()->with('error', 'Aucune cible définie.');
        }

        $sent = 0;
        foreach ($results as $result) {
            try {
                $trackingPixel = url("/track/open/{$result->unique_token}");
                $clickUrl = url("/track/click/{$result->unique_token}");
                $body = $this->buildBody($simulation->body, $clickUrl, $trackingPixel, $result->user->name);

                // ⚠️ Envoi ANONYME — from_email et from_name de la simulation
                // PAS de notification in-app — c'est un test secret
                Mail::html($body, function ($mail) use ($simulation, $result, $trackingPixel) {
                    $mail->to($result->user->email, $result->user->name)
                        ->subject($simulation->subject)
                        ->from($simulation->from_email, $simulation->from_name)
                        ->replyTo($simulation->from_email, $simulation->from_name);
                    // PAS d'en-tête X-SPG ou autre qui trahit l'origine
                });

                $sent++;

                // Log interne uniquement (pas de notification user)
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'simulation_sent',
                    'target_type' => 'SimulationResult',
                    'target_id' => $result->id,
                    'description' => "Email simulation envoyé à {$result->user->email}",
                ]);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Simulation send error', [
                    'user' => $result->user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $simulation->update([
            'status' => 'running',
            'targets_count' => $results->count(),
        ]);

        // Seule notification : à l'admin qui lance
        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'type' => 'system',
            'title' => "Simulation lancée : {$simulation->name}",
            'body' => "{$sent}/{$results->count()} emails envoyés anonymement",
            'link' => "/admin/simulations/{$simulation->id}",
            'icon' => 'bi-send-fill',
            'color' => 'green',
        ]);

        return redirect()->route('admin.simulations.show', $simulation)
            ->with('success', "{$sent} emails de simulation envoyés anonymement.");
    }

    private function buildBody(string $body, string $clickUrl, string $trackingPixel, string $name): string
    {
        $body = str_replace(['{{name}}', '{name}', '{{NAME}}'], $name, $body);
        // Remplacer tous les liens par le lien de tracking
        $body = preg_replace('/href=["\']([^"\']*)["\']/', "href=\"{$clickUrl}\"", $body);
        // Pixel de tracking invisible
        return nl2br($body) . "<img src='{$trackingPixel}' width='1' height='1' style='display:none;border:0;'/>";
    }

    private function injectTracking(string $body, string $clickUrl, string $trackingPixel, string $name): string
    {
        // Remplacer les placeholders
        $body = str_replace(['{{name}}', '{{NAME}}', '{name}'], $name, $body);
        // Wrapper tous les liens href avec le tracking
        $body = preg_replace(
            '/href=["\']([^"\']*)["\']/',
            "href=\"{$clickUrl}\"",
            $body
        );
        return $body;
    }
}