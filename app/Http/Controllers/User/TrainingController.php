<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::where('is_active', true)->get();
        $userTrainings = auth()->user()->trainings()->pluck('training_id')->toArray();
        return view('user.training.index', compact('trainings', 'userTrainings'));
    }

    public function show(Training $training)
    {
        $pivot = auth()->user()->trainings()
            ->where('training_id', $training->id)->first();

        if (!$pivot) {
            auth()->user()->trainings()->attach($training->id, [
                'status' => 'in_progress',
                'attempts' => 1,
            ]);
        }

        // FIX CRITIQUE : decoder quiz_data correctement
        $quizData = $training->quiz_data;

        // Si c'est une string JSON → décoder
        if (is_string($quizData)) {
            $quizData = json_decode($quizData, true) ?? [];
        }

        // Si c'est null ou pas un array → tableau vide
        if (!is_array($quizData)) {
            $quizData = [];
        }

        return view('user.training.show', compact('training', 'pivot', 'quizData'));
    }

    public function complete(Request $request, Training $training)
    {
        $request->validate(['score' => 'required|integer|min:0|max:100']);

        $score = (int) $request->score;
        $passed = $score >= 70;
        $status = $passed ? 'completed' : 'in_progress';

        $pivot = auth()->user()->trainings()->where('training_id', $training->id)->first();
        $attempts = $pivot ? (($pivot->pivot->attempts ?? 0) + 1) : 1;

        auth()->user()->trainings()->syncWithoutDetaching([
            $training->id => [
                'status' => $status,
                'score' => $score,
                'attempts' => $attempts,
                'completed_at' => $passed ? now() : null,
            ]
        ]);

        if ($passed) {
            $newScore = min(100, auth()->user()->vigilance_score + $training->points_reward);
            auth()->user()->update(['vigilance_score' => $newScore]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'training_completed',
            'target_type' => 'Training',
            'target_id' => $training->id,
            'description' => "Formation '{$training->title}' — Score: {$score}%",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'passed' => $passed,
            'score' => $score,
            'message' => $passed
                ? "Félicitations ! Score : {$score}%. +" . $training->points_reward . " points vigilance."
                : "Score insuffisant ({$score}%). Il faut 70% minimum. Réessayez !",
        ]);
    }

    public function certificate(Training $training)
    {
        $pivot = auth()->user()->trainings()
            ->where('training_id', $training->id)->first();

        if (!$pivot || $pivot->pivot->status !== 'completed') {
            return redirect()->route('user.training.index')
                ->with('error', 'Vous devez compléter la formation pour obtenir le certificat.');
        }

        $pdf = Pdf::loadView('user.training.certificate-pdf', [
            'user' => auth()->user(),
            'training' => $training,
            'score' => $pivot->pivot->score,
            'date' => $pivot->pivot->completed_at,
        ]);

        return $pdf->download("certificat-{$training->id}.pdf");
    }
}