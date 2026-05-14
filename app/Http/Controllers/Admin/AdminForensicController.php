<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIAnalyzerService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminForensicController extends Controller
{
    public function __construct(protected AIAnalyzerService $ai)
    {
    }

    public function index()
    {
        return view('admin.forensic');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            // Utilise email_headers et PAS headers (réservé Laravel)
            'email_headers' => 'required|string|min:10',
        ]);

        // Utilise input() et PAS $request->headers
        $headers = $request->input('email_headers');
        $result = $this->ai->analyzeHeaders($headers);

        $riskLevel = $result['risk_level'] ?? 'low';
        if (in_array($riskLevel, ['high', 'critical'])) {
            NotificationService::forensicAnalysis(auth()->user(), $riskLevel);
        }

        return response()->json($result);
    }
}