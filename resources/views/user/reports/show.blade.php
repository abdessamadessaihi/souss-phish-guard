@extends('layouts.app')
@section('title', 'Signalement #' . $report->id)
@section('page-title', 'DÉTAIL SIGNALEMENT')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Signalements</span> / #{{ $report->id }}</div>
                <div class="page-header-title">Signalement #{{ $report->id }}</div>
            </div>
            <a href="{{ route('user.reports.index') }}" class="btn-cyber btn-cyber-primary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="row g-3">

            <!-- Score IA -->
            <div class="col-md-4">
                <div class="cyber-card text-center">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-cpu-fill"></i> SCORE IA</div>
                    </div>
                    @php $score = $report->ai_risk_score ?? 0; @endphp
                    <div style="margin:20px 0;">
                        <div style="font-size:64px;font-family:'Share Tech Mono',monospace;
                            color:{{ $score >= 70 ? 'var(--neon-red)' : ($score >= 40 ? 'var(--neon-orange)' : 'var(--neon-green)') }};
                            text-shadow:0 0 20px currentColor;line-height:1;">
                            {{ $score }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">/ 100</div>
                    </div>
                    <div class="cyber-progress mb-3">
                        <div class="cyber-progress-fill {{ $score >= 70 ? 'progress-red' : ($score >= 40 ? 'progress-orange' : 'progress-green') }}"
                            style="width:{{ $score }}%"></div>
                    </div>
                    @php
                        $badgeMap = ['critical' => 'badge-critical', 'high' => 'badge-high', 'medium' => 'badge-medium', 'low' => 'badge-low'];
                    @endphp
                    <span class="cyber-badge {{ $badgeMap[$report->severity ?? 'low'] ?? 'badge-low' }}"
                        style="font-size:13px;padding:8px 16px;">
                        {{ strtoupper($report->severity ?? 'LOW') }}
                    </span>
                </div>

                <!-- Statut -->
                <div class="cyber-card mt-3">
                    <div class="cyber-card-title mb-3"><i class="bi bi-info-circle"></i> INFORMATIONS</div>
                    <div style="display:flex;flex-direction:column;gap:12px;font-size:13px;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Type</span>
                            <span class="cyber-badge badge-info">{{ strtoupper($report->type) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Statut</span>
                            @php $st = ['pending' => 'badge-pending', 'confirmed_phish' => 'badge-critical', 'false_positive' => 'badge-low', 'blocked' => 'badge-high'];@endphp
                            <span class="cyber-badge {{ $st[$report->status] ?? 'badge-info' }}">{{ $report->status }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Date</span>
                            <span>{{ $report->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($report->sender_email)
                            <div class="d-flex justify-content-between">
                                <span class="text-muted-cyber">Expéditeur</span>
                                <span
                                    style="font-size:12px;font-family:'Share Tech Mono',monospace;">{{ $report->sender_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Analyse détaillée -->
            <div class="col-md-8">
                <div class="cyber-card mb-3">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-file-text-fill"></i> CONTENU SIGNALÉ</div>
                    </div>
                    <div
                        style="background:rgba(0,10,25,0.8);border:1px solid var(--border-solid);border-radius:8px;padding:16px;font-family:'Share Tech Mono',monospace;font-size:12px;color:var(--text-muted);word-break:break-all;max-height:150px;overflow-y:auto;">
                        {{ $report->content }}
                    </div>
                </div>

                <div class="cyber-card mb-3">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-robot"></i> ANALYSE IA</div>
                    </div>
                    <div style="font-size:13px;line-height:1.8;color:var(--text-primary);">
                        {{ $report->ai_analysis ?? 'Analyse en cours...' }}
                    </div>
                </div>

                @if($report->virustotal_result)
                    <div class="cyber-card mb-3">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-shield-exclamation"></i> VIRUSTOTAL</div>
                            <span
                                style="font-size:11px;color:var(--text-muted);">{{ $report->virustotal_result['source'] ?? '' }}</span>
                        </div>
                        @php $vt = $report->virustotal_result; @endphp
                        <div class="row g-2 text-center">
                            <div class="col-3">
                                <div style="padding:12px;background:rgba(255,0,60,0.1);border-radius:8px;">
                                    <div style="font-size:24px;color:var(--neon-red);font-family:'Share Tech Mono',monospace;">
                                        {{ $vt['malicious'] ?? 0 }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);">MALVEILLANT</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="padding:12px;background:rgba(255,107,0,0.1);border-radius:8px;">
                                    <div
                                        style="font-size:24px;color:var(--neon-orange);font-family:'Share Tech Mono',monospace;">
                                        {{ $vt['suspicious'] ?? 0 }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);">SUSPECT</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="padding:12px;background:rgba(0,255,136,0.1);border-radius:8px;">
                                    <div
                                        style="font-size:24px;color:var(--neon-green);font-family:'Share Tech Mono',monospace;">
                                        {{ $vt['harmless'] ?? 0 }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);">SÛR</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="padding:12px;background:rgba(0,245,255,0.05);border-radius:8px;">
                                    <div
                                        style="font-size:24px;color:var(--text-muted);font-family:'Share Tech Mono',monospace;">
                                        {{ $vt['undetected'] ?? 0 }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);">NON DÉTECTÉ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($report->admin_feedback)
                    <div class="cyber-card">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-chat-dots-fill"></i> RETOUR DE L'ÉQUIPE SÉCURITÉ</div>
                        </div>
                        <div
                            style="font-size:13px;padding:12px;background:rgba(0,245,255,0.04);border-radius:8px;border-left:3px solid var(--neon-cyan);">
                            {{ $report->admin_feedback }}
                        </div>
                    </div>
                @endif

                @if($report->status === 'pending')
                    <div style="display:flex;gap:10px;margin-top:16px;">
                        <a href="{{ route('user.reports.edit', $report) }}" class="btn-cyber btn-cyber-warning">
                            <i class="bi bi-pencil-fill"></i> Modifier
                        </a>
                        <form method="POST" action="{{ route('user.reports.destroy', $report) }}"
                            onsubmit="return confirm('Supprimer ce signalement ?')">
                            @csrf @method('DELETE')
                            <button class="btn-cyber btn-cyber-danger"><i class="bi bi-trash-fill"></i> Supprimer</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection