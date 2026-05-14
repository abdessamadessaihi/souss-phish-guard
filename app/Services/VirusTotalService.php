<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VirusTotalService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.virustotal.key', '');
    }

    public function scanUrl(string $url): array
    {
        if ($this->hasNoKey()) {
            return $this->simulateScan($url);
        }

        try {
            // Étape 1 — Soumettre l'URL
            $submit = Http::withHeaders([
                'x-apikey' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->timeout(15)->asForm()->post(
                    'https://www.virustotal.com/api/v3/urls',
                    ['url' => $url]
                );

            if (!$submit->successful()) {
                Log::warning('VT submit failed', ['status' => $submit->status()]);
                return $this->simulateScan($url);
            }

            $analysisId = $submit->json('data.id');
            if (!$analysisId)
                return $this->simulateScan($url);

            // Étape 2 — Attendre puis récupérer
            sleep(3);

            $result = Http::withHeaders([
                'x-apikey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(15)->get(
                    "https://www.virustotal.com/api/v3/analyses/{$analysisId}"
                );

            if ($result->successful()) {
                $stats = $result->json('data.attributes.stats', []);
                $urlId = base64_encode(rtrim($url, '='));

                return [
                    'malicious' => (int) ($stats['malicious'] ?? 0),
                    'suspicious' => (int) ($stats['suspicious'] ?? 0),
                    'harmless' => (int) ($stats['harmless'] ?? 0),
                    'undetected' => (int) ($stats['undetected'] ?? 0),
                    'source' => 'virustotal',
                    'url' => "https://www.virustotal.com/gui/url/{$urlId}/detection",
                ];
            }

            Log::warning('VT result failed', ['status' => $result->status()]);

        } catch (\Exception $e) {
            Log::error('VirusTotal error', ['error' => $e->getMessage()]);
        }

        return $this->simulateScan($url);
    }

    public function hasNoKey(): bool
    {
        return empty($this->apiKey) || strlen($this->apiKey) < 10;
    }

    private function simulateScan(string $url): array
    {
        $lower = strtolower($url);

        $suspectScore = 0;
        if (str_contains($lower, '.tk'))
            $suspectScore += 3;
        if (str_contains($lower, '.ml'))
            $suspectScore += 3;
        if (str_contains($lower, 'login'))
            $suspectScore += 2;
        if (str_contains($lower, 'verify'))
            $suspectScore += 2;
        if (str_contains($lower, 'secure-'))
            $suspectScore += 2;
        if (str_contains($lower, 'account'))
            $suspectScore += 1;
        if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url))
            $suspectScore += 4;

        $isSuspect = $suspectScore >= 3;

        return [
            'malicious' => $isSuspect ? rand(8, 30) : 0,
            'suspicious' => $isSuspect ? rand(3, 10) : rand(0, 1),
            'harmless' => $isSuspect ? rand(20, 45) : rand(55, 75),
            'undetected' => rand(8, 18),
            'source' => 'simulation',
            'url' => '#',
        ];
    }
}