@extends('layouts.app')
@section('title', 'Rapports & Alertes')
@section('page-title', 'RAPPORTS & ALERTES')

@section('content')
<div class="fade-in">
    <!-- Bloc Envoi Alertes Email -->
<div class="cyber-card mb-4">
    <div class="cyber-card-header">
        <div class="cyber-card-title">
            <i class="bi bi-envelope-exclamation-fill"></i> ENVOI D'ALERTES EMAIL
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.alerts.csv') }}" class="btn-cyber btn-cyber-success"
                style="padding:6px 14px;font-size:11px;">
                <i class="bi bi-download"></i> Télécharger CSV
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Formulaire alerte -->
        <div class="col-md-7">
            <form method="POST" action="{{ route('admin.alerts.send') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="cyber-label">NIVEAU D'ALERTE</label>
                        <select name="alert_level" class="cyber-select" required>
                            <option value="danger">🚨 Critique</option>
                            <option value="warning" selected>⚠️ Avertissement</option>
                            <option value="info">ℹ️ Information</option>
                            <option value="success">✅ Sensibilisation</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="cyber-label">DESTINATAIRE</label>
                        <select name="target" class="cyber-select" onchange="toggleUser(this.value)" required>
                            <option value="all">Tous les agents</option>
                            <option value="specific">Agent spécifique</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="userDiv" style="display:none;">
                        <label class="cyber-label">AGENT</label>
                        <select name="user_id" class="cyber-select">
                            @foreach(\App\Models\User::where('role', 'user')->where('is_active', true)->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="cyber-label">SIGNALEMENT ASSOCIÉ (optionnel)</label>
                        <select name="report_id" class="cyber-select">
                            <option value="">Aucun</option>
                            @foreach(\App\Models\PhishReport::latest()->limit(20)->get() as $r)
                                <option value="{{ $r->id }}">#{{ $r->id }} — {{ strtoupper($r->type) }} — Score:
                                    {{ $r->ai_risk_score }}%
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">SUJET DE L'EMAIL *</label>
                        <input type="text" name="subject" class="cyber-input" required
                            placeholder="Ex: Campagne de phishing détectée — Action requise">
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">MESSAGE *</label>
                        <textarea name="message" class="cyber-textarea" rows="4" required
                            placeholder="Rédigez votre message d'alerte ou de sensibilisation...&#10;&#10;Ex: Nous avons détecté une campagne de phishing ciblant notre organisation..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-cyber btn-cyber-danger w-100 justify-content-center"
                            style="padding:12px;">
                            <i class="bi bi-send-fill"></i> ENVOYER L'ALERTE PAR EMAIL
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Envoi CSV -->
        <div class="col-md-5">
            <div
                style="height:100%;padding:20px;background:var(--bg-elevated);border-radius:var(--radius-lg);border:1px solid var(--border-subtle);">
                <div
                    style="font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:2px;color:var(--text-secondary);margin-bottom:16px;">
                    📊 RAPPORT CSV
                </div>
                <p style="font-size:13px;color:var(--text-secondary);line-height:1.7;margin-bottom:16px;">
                    Exportez et envoyez le rapport complet des signalements au format CSV avec toutes les colonnes
                    détaillées.
                </p>
                <form method="POST" action="{{ route('admin.alerts.sendCsv') }}">
                    @csrf
                    <label class="cyber-label">EMAIL DESTINATAIRE</label>
                    <input type="email" name="email" class="cyber-input mb-2" placeholder="responsable@organisation.ma"
                        required value="{{ auth()->user()->email }}">
                    <button type="submit" class="btn-cyber btn-cyber-primary w-100 justify-content-center mt-2">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Envoyer le rapport CSV
                    </button>
                </form>

                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border-subtle);">
                    <div style="font-size:11px;color:var(--text-muted);line-height:1.8;">
                        Le CSV contient : ID, Date, Agent, Type, Score IA, Sévérité, Statut, IP suspecte et plus.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
    <script>
        function toggleUser(val) {
            document.getElementById('userDiv').style.display = val === 'specific' ? 'block' : 'none';
        }
    </script>
@endsection