<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f4f8;
        }

        .wrapper {
            max-width: 600px;
            margin: 30px auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            border-radius: 12px 12px 0 0;
            padding: 36px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #06d6a0, #10b981);
        }

        .shield-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            line-height: 64px;
            text-align: center;
        }

        .header h1 {
            color: #10b981;
            font-size: 13px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .header h2 {
            color: #f8fafc;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
        }

        /* Alert type banner */
        .alert-banner {
            padding: 14px 40px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .alert-banner.danger {
            background: #7f1d1d;
            color: #fca5a5;
        }

        .alert-banner.warning {
            background: #78350f;
            color: #fde68a;
        }

        .alert-banner.info {
            background: #1e3a5f;
            color: #93c5fd;
        }

        .alert-banner.success {
            background: #064e3b;
            color: #6ee7b7;
        }

        /* Body */
        .body {
            background: #ffffff;
            padding: 36px 40px;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .greeting {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .greeting strong {
            color: #0f172a;
        }

        .message-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #10b981;
            border-radius: 0 8px 8px 0;
            padding: 20px 24px;
            font-size: 15px;
            line-height: 1.8;
            color: #334155;
            margin-bottom: 24px;
        }

        /* Report card */
        .report-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .report-card h3 {
            color: #065f46;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #bbf7d0;
        }

        .report-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dcfce7;
            font-size: 13px;
        }

        .report-row:last-child {
            border-bottom: none;
        }

        .report-label {
            color: #64748b;
            font-weight: 500;
        }

        .report-value {
            color: #0f172a;
            font-weight: 600;
        }

        .badge-critical {
            background: #fee2e2;
            color: #dc2626;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-high {
            background: #fef3c7;
            color: #d97706;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-medium {
            background: #dbeafe;
            color: #2563eb;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-low {
            background: #dcfce7;
            color: #16a34a;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Tips */
        .tips-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .tips-box h3 {
            color: #92400e;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .tip-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 6px 0;
            font-size: 13px;
            color: #78350f;
        }

        .tip-icon {
            font-size: 15px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* CTA Button */
        .cta-wrap {
            text-align: center;
            margin: 28px 0;
        }

        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff !important;
            padding: 14px 36px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 24px 0;
        }

        /* Footer */
        .footer {
            background: #0f172a;
            border-radius: 0 0 12px 12px;
            padding: 28px 40px;
            text-align: center;
        }

        .footer-brand {
            color: #10b981;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .footer-sub {
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }

        .footer-links {
            margin-top: 14px;
        }

        .footer-links a {
            color: #475569;
            font-size: 11px;
            text-decoration: none;
            margin: 0 8px;
        }

        /* Score meter */
        .score-meter {
            margin: 16px 0;
        }

        .score-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .score-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            border-radius: 4px;
        }

        .score-critical {
            background: linear-gradient(90deg, #dc2626, #ef4444);
        }

        .score-high {
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }

        .score-medium {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
        }

        .score-safe {
            background: linear-gradient(90deg, #16a34a, #22c55e);
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- HEADER -->
        <div class="header">
            <div class="shield-icon">🛡️</div>
            <h1>Souss Phish Guard — SOC</h1>
            <h2>{{ $subject }}</h2>
        </div>

        <!-- ALERT BANNER -->
        @php
            $alertType = $alertLevel ?? 'warning';
            $bannerMap = [
                'danger' => ['class' => 'danger', 'icon' => '🚨', 'text' => 'ALERTE CRITIQUE DE SÉCURITÉ'],
                'warning' => ['class' => 'warning', 'icon' => '⚠️', 'text' => 'ALERTE DE SÉCURITÉ'],
                'info' => ['class' => 'info', 'icon' => 'ℹ️', 'text' => 'INFORMATION SÉCURITÉ'],
                'success' => ['class' => 'success', 'icon' => '✅', 'text' => 'SENSIBILISATION SÉCURITÉ'],
            ];
            $banner = $bannerMap[$alertType] ?? $bannerMap['warning'];
          @endphp
        <div class="alert-banner {{ $banner['class'] }}">
            {{ $banner['icon'] }} &nbsp; {{ $banner['text'] }}
        </div>

        <!-- BODY -->
        <div class="body">

            <div class="greeting">
                Bonjour <strong>{{ $user->name }}</strong>,<br>
                <span style="color:#64748b;font-size:14px;">{{ $user->department ?? 'Équipe' }} · Souss Phish
                    Guard</span>
            </div>

            <!-- Message principal -->
            <div class="message-box">
                {!! nl2br(e($message)) !!}
            </div>

            <!-- Signalement associé -->
            @if($report)
                <div class="report-card">
                    <h3>📋 Signalement associé #{{ $report->id }}</h3>

                    @php
                        $score = $report->ai_risk_score ?? 0;
                        $scoreClass = $score >= 80 ? 'critical' : ($score >= 60 ? 'high' : ($score >= 40 ? 'medium' : 'safe'));
                      @endphp

                    <div class="report-row">
                        <span class="report-label">Type de menace</span>
                        <span class="report-value">{{ strtoupper($report->type) }}</span>
                    </div>
                    <div class="report-row">
                        <span class="report-label">Score IA</span>
                        <span class="report-value">{{ $score }}/100</span>
                    </div>
                    <div class="score-meter">
                        <div class="score-bar">
                            <div class="score-fill score-{{ $scoreClass }}" style="width:{{ $score }}%"></div>
                        </div>
                    </div>
                    <div class="report-row">
                        <span class="report-label">Sévérité</span>
                        <span
                            class="badge-{{ $report->severity ?? 'low' }}">{{ strtoupper($report->severity ?? 'N/A') }}</span>
                    </div>
                    <div class="report-row">
                        <span class="report-label">Statut</span>
                        <span class="report-value">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                    </div>
                    @if($report->ai_analysis)
                        <div
                            style="margin-top:12px;padding:12px;background:#f0fdf4;border-radius:6px;font-size:13px;color:#065f46;border:1px solid #bbf7d0;">
                            <strong>Analyse IA :</strong> {{ Str::limit($report->ai_analysis, 200) }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Conseils sécurité -->
            <div class="tips-box">
                <h3>💡 Bonnes pratiques à adopter</h3>
                <div class="tip-item">
                    <span class="tip-icon">🔍</span>
                    <span>Vérifiez toujours l'adresse email de l'expéditeur avant de cliquer sur un lien.</span>
                </div>
                <div class="tip-item">
                    <span class="tip-icon">🔗</span>
                    <span>Ne cliquez jamais sur un lien urgent sans l'analyser via la plateforme SPG.</span>
                </div>
                <div class="tip-item">
                    <span class="tip-icon">🔐</span>
                    <span>Activez la double authentification (2FA) sur tous vos comptes professionnels.</span>
                </div>
                <div class="tip-item">
                    <span class="tip-icon">🚩</span>
                    <span>Signalez immédiatement tout email suspect via la plateforme SPG.</span>
                </div>
            </div>

            <!-- CTA -->
            <div class="cta-wrap">
                <a href="{{ url('/user/dashboard') }}" class="cta-btn">
                    🛡️ Accéder à la plateforme SPG
                </a>
            </div>

            <hr class="divider">

            <p style="font-size:12px;color:#94a3b8;text-align:center;line-height:1.7;">
                Cet email a été envoyé automatiquement par le Security Operations Center de Souss Phish Guard.<br>
                Si vous avez des questions, contactez votre équipe sécurité directement via la plateforme.
            </p>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-brand">🛡 SOUSS PHISH GUARD</div>
            <div class="footer-sub">
                Security Operations Center · {{ now()->format('d/m/Y') }}<br>
                Plateforme de détection et prévention du phishing
            </div>
            <div class="footer-links">
                <a href="{{ url('/user/dashboard') }}">Tableau de bord</a>
                <a href="{{ url('/user/reports/create') }}">Signaler une menace</a>
                <a href="{{ url('/user/training') }}">Formations</a>
            </div>
            <div
                style="margin-top:14px;padding-top:14px;border-top:1px solid #1e293b;font-size:10px;color:#334155;letter-spacing:1px;">
                NE PAS RÉPONDRE À CET EMAIL · GÉNÉRÉ AUTOMATIQUEMENT
            </div>
        </div>

    </div>
</body>

</html>