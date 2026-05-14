@extends('layouts.app')
@section('title', 'Signalement #' . $report->id)
@section('page-title', 'DÉTAIL SIGNALEMENT')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <div class="page-breadcrumb">SPG / Admin / <span>Alertes</span> / #{{ $report->id }}</div>
            <div class="page-header-title">Signalement #{{ $report->id }}</div>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn-cyber btn-cyber-warning">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row g-3">
        <!-- Infos + Action -->
        <div class="col-md-4">
            <div class="cyber-card mb-3">
                <div class="cyber-card-title mb-3"><i class="bi bi-info-circle"></i> INFORMATIONS</div>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Agent</span><strong>{{ $report->user->name }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Email</span><span style="font-size:11px;">{{ $report->user->email }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Type</span><span class="cyber-badge badge-info">{{ strtoupper($report->type) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Score IA</span>
                        <span class="font-mono" style="color:{{ ($report->ai_risk_score??0) >= 70 ? 'var(--neon-red)' : 'var(--neon-green)' }}">
                            {{ $report->ai_risk_score ?? 0 }}/100
                        </span>
                    </div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Date</span><span style="font-size:11px;">{{ $report->created_at->format('d/m/Y H:i') }}</span></div>
                    @if($report->sender_email)
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Expéditeur</span><span style="font-size:11px;">{{ $report->sender_email }}</span></div>
                    @endif
                    @if($report->sender_ip)
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">IP source</span><span class="font-mono" style="font-size:11px;">{{ $report->sender_ip }}</span></div>
                    @endif
                </div>
            </div>

            <!-- Action admin -->
            <div class="cyber-card">
                <div class="cyber-card-title mb-3"><i class="bi bi-shield-fill-check"></i> ACTION GUARDIAN</div>
                <form method="POST" action="{{ route('admin.reports.status', $report) }}">
                    @csrf @method('PATCH')
                    <div class="cyber-form-group">
                        <label class="cyber-label">STATUT</label>
                        <select name="status" class="cyber-select" required>
                            @foreach(['pending'=>'En attente','analyzing'=>'En analyse','confirmed_phish'=>'Phishing confirmé','false_positive'=>'Faux positif','blocked'=>'Bloqué'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $report->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cyber-form-group">
                        <label class="cyber-label">SÉVÉRITÉ</label>
                        <select name="severity" class="cyber-select">
                            @foreach(['low'=>'Faible','medium'=>'Moyenne','high'=>'Élevée','critical'=>'Critique'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $report->severity === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cyber-form-group">
                        <label class="cyber-label">FEEDBACK POUR L'AGENT</label>
                        <textarea name="admin_feedback" class="cyber-textarea" rows="4"
                                  placeholder="Votre retour sera envoyé à l'agent par message...">{{ $report->admin_feedback }}</textarea>
                    </div>
                    <button type="submit" class="btn-cyber btn-cyber-primary w-100 justify-content-center">
                        <i class="bi bi-save-fill"></i> VALIDER
                    </button>
                </form>
            </div>
        </div>

        <!-- Contenu + Analyse -->
        <div class="col-md-8">
            <div class="cyber-card mb-3">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-file-text"></i> CONTENU SIGNALÉ</div>
                </div>
                <div style="background:rgba(0,10,25,0.8);border:1px solid var(--border-solid);border-radius:8px;padding:16px;font-family:'Share Tech Mono',monospace;font-size:12px;color:var(--text-muted);word-break:break-all;max-height:150px;overflow-y:auto;">
                    {{ $report->content }}
                </div>
            </div>

            <div class="cyber-card mb-3">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-robot"></i> ANALYSE IA</div>
                    @php $s=$report->ai_risk_score??0; @endphp
                    <span class="font-mono" style="font-size:24px;color:{{ $s>=70?'var(--neon-red)':($s>=40?'var(--neon-orange)':'var(--neon-green)') }}">{{ $s }}/100</span>
                </div>
                <div class="cyber-progress mb-3" style="height:8px;">
                    <div class="cyber-progress-fill {{ $s>=70?'progress-red':($s>=40?'progress-orange':'progress-green') }}" style="width:{{ $s }}%"></div>
                </div>
                <div style="font-size:13px;line-height:1.8;">{{ $report->ai_analysis ?? 'Analyse non disponible.' }}</div>
            </div>

            @if($report->virustotal_result)
            <div class="cyber-card mb-3">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-shield-exclamation"></i> VIRUSTOTAL</div>
                </div>
                @php $vt=$report->virustotal_result; @endphp
                <div class="row g-2 text-center">
                    @foreach(['malicious'=>['red','MALVEILLANT'],'suspicious'=>['orange','SUSPECT'],'harmless'=>['green','SÛR'],'undetected'=>['muted-cyber','NON DÉT.']] as $key=>[$color,$label])
                    <div class="col-3">
                        <div style="padding:12px;background:rgba(0,10,25,0.5);border-radius:8px;">
                            <div style="font-size:22px;font-family:'Share Tech Mono',monospace;color:var(--{{ $color==='muted-cyber'?'text-muted':'neon-'.$color }});">{{ $vt[$key]??0 }}</div>
                            <div style="font-size:10px;color:var(--text-muted);">{{ $label }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($report->email_headers)
            <div class="cyber-card">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-code-square"></i> EN-TÊTES EMAIL</div>
                    <a href="{{ route('admin.forensic.index') }}" class="btn-cyber btn-cyber-primary" style="padding:5px 12px;font-size:11px;">
                        <i class="bi bi-cpu-fill"></i> Analyser via IA Forensic
                    </a>
                </div>
                <pre style="background:rgba(0,10,25,0.8);border:1px solid var(--border-solid);border-radius:8px;padding:12px;font-size:11px;color:var(--text-muted);max-height:200px;overflow:auto;white-space:pre-wrap;">{{ $report->email_headers }}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection