<?php

return [

    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'ses' => ['key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY'), 'region' => env('AWS_DEFAULT_REGION', 'us-east-1')],
    'resend' => ['key' => env('RESEND_KEY')],

    // ── Anthropic Claude ──
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY', ''),
        'model' => 'claude-haiku-4-5-20251001',  // Modèle exact — NE PAS CHANGER
    ],

    // ── VirusTotal ──
    'virustotal' => [
        'key' => env('VIRUSTOTAL_API_KEY', ''),
    ],

    'hibp' => [
        'key' => env('HIBP_API_KEY', ''),
    ],

];