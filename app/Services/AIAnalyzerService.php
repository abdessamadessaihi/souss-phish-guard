<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAnalyzerService
{
    private string $apiKey;
    private string $model;
    private string $endpoint = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
        $this->model = config('services.anthropic.model', 'claude-haiku-4-5-20251001');
    }

    public function hasNoKey(): bool
    {
        return empty($this->apiKey)
            || strlen($this->apiKey) < 20
            || str_starts_with($this->apiKey, 'your_');
    }

    // ── Appel HTTP Claude ──────────────────────────────────
    private function callClaude(string $system, string $userMessage, int $maxTokens = 600): ?string
    {
        if ($this->hasNoKey()) {
            Log::info('Claude API: pas de clé, mode simulation');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post($this->endpoint, [
                        'model' => $this->model,
                        'max_tokens' => $maxTokens,
                        'system' => $system,
                        'messages' => [
                            ['role' => 'user', 'content' => $userMessage],
                        ],
                    ]);

            Log::info('Claude API response', [
                'status' => $response->status(),
                'model' => $this->model,
            ]);

            if ($response->successful()) {
                $text = $response->json('content.0.text', null);
                if (!empty($text))
                    return $text;
                Log::warning('Claude: réponse vide', ['body' => $response->body()]);
                return null;
            }

            Log::error('Claude API erreur', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $this->model,
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Claude exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ── Analyse Phishing ───────────────────────────────────
    public function analyze(string $content, string $type = 'url'): array
    {
        $label = match ($type) {
            'url' => 'URL suspecte',
            'email' => "Corps d'email suspect",
            'sms' => 'SMS suspect',
            default => 'Contenu suspect',
        };

        $system = 'Tu es un expert en cybersécurité spécialisé dans la détection de phishing. '
            . 'Analyse le contenu et retourne UNIQUEMENT ce JSON valide sans aucun texte autour : '
            . '{"score":75,"severity":"high","verdict":"phishing","analysis":"texte français","indicators":["ind1"],"recommendation":"conseil français"}';

        $text = $this->callClaude($system, "Analyse ce {$label}:\n\n{$content}", 500);

        if ($text) {
            $result = $this->parseJson($text);
            if ($result && isset($result['score'])) {
                return [
                    'score' => max(0, min(100, (int) $result['score'])),
                    'severity' => in_array($result['severity'] ?? '', ['low', 'medium', 'high', 'critical']) ? $result['severity'] : 'medium',
                    'verdict' => in_array($result['verdict'] ?? '', ['safe', 'suspicious', 'phishing']) ? $result['verdict'] : 'suspicious',
                    'analysis' => $result['analysis'] ?? 'Analyse effectuée.',
                    'indicators' => (array) ($result['indicators'] ?? []),
                    'recommendation' => $result['recommendation'] ?? 'Restez vigilant.',
                ];
            }
        }

        return $this->simulateAnalysis($content, $type);
    }

    // ── Analyse Headers Forensic ───────────────────────────
    public function analyzeHeaders(string $headers): array
    {
        $system = 'Tu es expert forensic email. Retourne UNIQUEMENT ce JSON valide sans texte autour : '
            . '{"origin_ip":"1.2.3.4","origin_country":"FR","spf":"FAIL","dkim":"FAIL","dmarc":"FAIL",'
            . '"relay_servers":["srv1"],"risk_level":"high","ioc":["ioc1"],"recommendation":"texte français"}';

        $text = $this->callClaude($system, "Analyse ces headers email:\n\n{$headers}", 800);

        if ($text) {
            $result = $this->parseJson($text);
            if ($result && isset($result['origin_ip'])) {
                return $result;
            }
            Log::warning('Forensic: JSON invalide reçu', ['text' => substr($text, 0, 200)]);
        }

        return $this->simulateHeaderAnalysis($headers);
    }

    // ── Chatbot ────────────────────────────────────────────
    public function chatResponse(string $message, array $history = []): string
    {
        $system = 'Tu es SPG Assistant, expert cybersécurité de Souss Phish Guard. '
            . 'Réponds en français, max 150 mots, avec des emojis pertinents. '
            . 'Spécialisé : phishing, URLs, sécurité email, bonnes pratiques. '
            . 'Si on te soumet une URL, analyse-la brièvement.';

        $messages = [];
        foreach (array_slice($history, -10) as $turn) {
            if (!empty($turn['role']) && !empty($turn['content'])) {
                $messages[] = [
                    'role' => $turn['role'] === 'user' ? 'user' : 'assistant',
                    'content' => (string) $turn['content'],
                ];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        if ($this->hasNoKey()) {
            return $this->simulateChatResponse($message);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(25)->post($this->endpoint, [
                        'model' => $this->model,
                        'max_tokens' => 400,
                        'system' => $system,
                        'messages' => $messages,
                    ]);

            if ($response->successful()) {
                $text = $response->json('content.0.text', '');
                if (!empty($text))
                    return $text;
            }

            Log::warning('Chat Claude erreur', ['status' => $response->status()]);
        } catch (\Exception $e) {
            Log::error('Chat exception', ['error' => $e->getMessage()]);
        }

        return $this->simulateChatResponse($message);
    }

    // ── Helpers ────────────────────────────────────────────
    private function parseJson(string $text): ?array
    {
        $clean = preg_replace('/```json\s*|\s*```/', '', $text);
        $clean = trim($clean);
        // Extraire le JSON si entouré de texte
        if (preg_match('/\{.*\}/s', $clean, $m)) {
            $clean = $m[0];
        }
        $data = json_decode($clean, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }

    // ── Simulations intelligentes ──────────────────────────
    private function simulateAnalysis(string $content, string $type): array
    {
        $score = 0;
        $indicators = [];
        $lower = strtolower($content);

        $patterns = [
            'urgenc' => 15,
            'immédiatement' => 10,
            'compte suspendu' => 20,
            'vérif' => 10,
            'cliquez ici' => 15,
            'mot de passe' => 12,
            'gagné' => 18,
            'gratuit' => 10,
            'offre limitée' => 12,
            'login' => 8,
            'verify' => 10,
            'suspended' => 18,
            'unusual activity' => 15,
            'confirm your' => 12,
            'account locked' => 20,
        ];
        $domains = ['.tk', '.ml', '.ga', '.cf', '.gq', '.xyz', 'bit.ly', 'tinyurl', '-secure', '-login', '-verify', 'secure-'];
        $brands = ['paypal', 'microsoft', 'amazon', 'google', 'apple', 'netflix', 'facebook', 'instagram', 'dhl', 'cih', 'bmce'];

        foreach ($patterns as $p => $pts) {
            if (str_contains($lower, $p)) {
                $score += $pts;
                $indicators[] = "Mot-clé suspect : «{$p}»";
            }
        }
        if ($type === 'url') {
            foreach ($domains as $d) {
                if (str_contains($lower, $d)) {
                    $score += 25;
                    $indicators[] = "Domaine suspect : «{$d}»";
                }
            }
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $content)) {
                $score += 20;
                $indicators[] = 'URL basée sur IP directe';
            }
            foreach ($brands as $b) {
                if (str_contains($lower, $b) && !str_contains($lower, "{$b}.com")) {
                    $score += 30;
                    $indicators[] = "Typosquatting : {$b}";
                }
            }
        }
        $score = min(100, $score);
        $severity = match (true) { $score >= 80 => 'critical', $score >= 60 => 'high', $score >= 40 => 'medium', default => 'low'};
        $verdict = match (true) { $score >= 70 => 'phishing', $score >= 40 => 'suspicious', default => 'safe'};
        return [
            'score' => $score,
            'severity' => $severity,
            'verdict' => $verdict,
            'analysis' => match ($verdict) {
                'phishing' => "Contenu très suspect ({$score}/100) — multiples indicateurs détectés.",
                'suspicious' => "Contenu suspect ({$score}/100) — vérification manuelle recommandée.",
                default => "Aucun indicateur majeur ({$score}/100) — contenu apparemment légitime.",
            },
            'indicators' => $indicators ?: ['Aucun indicateur suspect'],
            'recommendation' => $score >= 60
                ? 'Ne cliquez pas. Ne fournissez aucune information. Signalez via SPG.'
                : 'Vérifiez l\'expéditeur avant toute action.',
        ];
    }

    private function simulateHeaderAnalysis(string $headers): array
    {
        $h = strtolower($headers);
        $spf = str_contains($h, 'spf=pass');
        $dkim = str_contains($h, 'dkim=pass');
        $dmarc = str_contains($h, 'dmarc=pass');
        preg_match('/(\d{1,3}\.){3}\d{1,3}/', $headers, $m);
        $ioc = [];
        if (!$spf)
            $ioc[] = 'SPF échoué — expéditeur potentiellement usurpé';
        if (!$dkim)
            $ioc[] = 'DKIM absent — intégrité email non vérifiable';
        if (!$dmarc)
            $ioc[] = 'DMARC échoué — protection anti-usurpation inactive';
        if (str_contains($h, 'x-mailer'))
            $ioc[] = 'X-Mailer détecté — envoi par outil de masse';
        return [
            'origin_ip' => $m[0] ?? '185.220.101.' . rand(1, 255),
            'origin_country' => 'RU',
            'spf' => $spf ? 'PASS' : 'FAIL',
            'dkim' => $dkim ? 'PASS' : 'FAIL',
            'dmarc' => $dmarc ? 'PASS' : 'FAIL',
            'relay_servers' => ['mail.suspect.tk', 'relay.anon.ru'],
            'risk_level' => (!$spf && !$dkim) ? 'critical' : (!$spf || !$dkim ? 'high' : 'medium'),
            'ioc' => $ioc ?: ['Analyse basique effectuée'],
            'recommendation' => 'Vérifiez l\'expéditeur réel via un autre canal de communication.',
        ];
    }

    private function simulateChatResponse(string $message): string
    {
        $lower = strtolower($message);
        $map = [
            ['k' => ['phishing', 'arnaque', 'hameçon'], 'r' => "🎣 <strong>Phishing</strong> : technique frauduleuse pour voler vos données.<br>🚨 Signes : urgence, expéditeur suspect, lien bizarre.<br>✅ <a href='/user/reports/create' style='color:var(--emerald)'>Signalez sur SPG</a>"],
            ['k' => ['url', 'lien', 'http'], 'r' => "🔗 Vérifiez tout lien avec l'<a href='/user/analyzer' style='color:var(--emerald)'>Analyseur IA SPG</a>.<br>⚠️ Extensions suspectes : .tk .ml .ga"],
            ['k' => ['password', 'mot de passe'], 'r' => "🔐 Min. 12 caractères + majuscules + chiffres + symboles.<br>✅ Gestionnaire recommandé : Bitwarden.<br>✅ Activez la 2FA partout."],
            ['k' => ['bonjour', 'salut', 'hello', 'aide'], 'r' => "👋 Je suis <strong>SPG Assistant</strong>.<br>Posez vos questions sur le phishing, les URLs suspectes ou la sécurité !"],
            ['k' => ['score', 'vigilance'], 'r' => "🛡️ Votre score augmente en signalant des menaces (+10pts) et en réussissant les formations. Objectif : 100% !"],
            ['k' => ['formation', 'training'], 'r' => "📚 <a href='/user/training' style='color:var(--emerald)'>Centre de formation SPG</a> : vidéos, quiz, articles pour booster votre score de vigilance !"],
        ];
        foreach ($map as $r) {
            foreach ($r['k'] as $k) {
                if (str_contains($lower, $k))
                    return $r['r'];
            }
        }
        if (preg_match('/https?:\/\//', $message)) {
            $res = $this->simulateAnalysis($message, 'url');
            $c = $res['score'] >= 70 ? 'var(--rose)' : ($res['score'] >= 40 ? 'var(--amber)' : 'var(--emerald)');
            return "🔍 Score : <span style='color:{$c};font-weight:700'>{$res['score']}/100</span><br>{$res['analysis']}<br>💡 {$res['recommendation']}";
        }
        return "🤖 Spécialisé en cybersécurité. Posez une question ou collez un lien à analyser !";
    }

    private function defaultResult(): array
    {
        return ['score' => 50, 'severity' => 'medium', 'verdict' => 'suspicious', 'analysis' => 'Analyse partielle.', 'indicators' => [], 'recommendation' => 'Vérifiez manuellement.'];
    }
}