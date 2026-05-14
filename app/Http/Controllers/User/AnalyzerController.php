<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AIAnalyzerService;
use App\Services\VirusTotalService;
use Illuminate\Http\Request;

class AnalyzerController extends Controller
{
    public function __construct(
        protected AIAnalyzerService $ai,
        protected VirusTotalService $vt
    ) {
    }

    public function index()
    {
        return view('user.analyzer');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'type' => 'nullable|string|max:20',
            'history' => 'nullable|array|max:30',
            'history.*.role' => 'nullable|string|in:user,assistant',
            'history.*.content' => 'nullable|string|max:3000',
        ]);

        $type = $request->input('type', 'url');
        $content = trim($request->input('content'));

        // ── Mode CHATBOT ──
        if ($type === 'chat') {
            $history = $request->input('history', []);
            $reply = $this->ai->chatResponse($content, $history);
            return response()->json(['result' => $reply]);
        }

        // ── Mode ANALYSE ──
        $result = $this->ai->analyze($content, $type);
        $vtResult = null;

        if (in_array($type, ['url', 'other']) && filter_var($content, FILTER_VALIDATE_URL)) {
            $vtResult = $this->vt->scanUrl($content);
        } elseif ($type === 'url') {
            $vtResult = $this->vt->scanUrl($content);
        }

        return response()->json(array_merge($result, [
            'virustotal' => $vtResult,
            'api_used' => $this->ai->hasNoKey() === false ? 'claude' : 'simulation',
        ]));
    }
}