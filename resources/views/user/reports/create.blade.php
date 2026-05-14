@extends('layouts.app')
@section('title', 'Nouveau Signalement')
@section('page-title', 'NOUVEAU SIGNALEMENT')

@section('content')
    <div class="fade-in" style="max-width:800px;">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Signalements</span> / Nouveau</div>
                <div class="page-header-title">Signaler une menace</div>
            </div>
            <a href="{{ route('user.reports.index') }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="cyber-card">
            <div class="cyber-card-header">
                <div class="cyber-card-title"><i class="bi bi-flag-fill"></i> FORMULAIRE DE SIGNALEMENT</div>
                <span class="cyber-badge badge-info"><i class="bi bi-robot"></i> Analyse IA automatique</span>
            </div>

            @if($errors->any())
                <div class="cyber-alert danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                    <button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                </div>
            @endif

            {{-- ACTION CORRECTE : user.reports.store --}}
            <form method="POST" action="{{ route('user.reports.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cyber-label">TYPE DE MENACE *</label>
                        <select name="type" class="cyber-select" required onchange="updateForm(this.value)">
                            <option value="">Sélectionner...</option>
                            <option value="url" {{ old('type') == 'url' ? 'selected' : '' }}>🔗 URL / Lien suspect</option>
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>📧 Email de phishing</option>
                            <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>💬 SMS suspect</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>⚠️ Autre</option>
                        </select>
                        @error('type')<div class="cyber-invalid">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="cyber-label">OBJET / SUJET</label>
                        <input type="text" name="subject" class="cyber-input" placeholder="Ex: Alerte sécurité Microsoft"
                            value="{{ old('subject') }}">
                    </div>

                    <div class="col-12">
                        <label class="cyber-label">CONTENU SUSPECT *</label>
                        <textarea name="content" id="contentField" class="cyber-textarea" rows="6" required
                            placeholder="Collez ici l'URL, le corps de l'email, ou le SMS suspect...">{{ old('content') }}</textarea>
                        @error('content')<div class="cyber-invalid">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="cyber-label">EMAIL DE L'EXPÉDITEUR</label>
                        <input type="email" name="sender_email" class="cyber-input" placeholder="expediteur@suspect.com"
                            value="{{ old('sender_email') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="cyber-label">IP DE L'EXPÉDITEUR (optionnel)</label>
                        <input type="text" name="sender_ip" class="cyber-input" placeholder="Ex: 185.220.101.45"
                            value="{{ old('sender_ip') }}">
                    </div>

                    <div class="col-12" id="headersSection" style="display:none;">
                        <label class="cyber-label">EN-TÊTES EMAIL (pour analyse forensic)</label>
                        <textarea name="email_headers" class="cyber-textarea" rows="4"
                            placeholder="Copiez ici les headers complets de l'email...">{{ old('email_headers') }}</textarea>
                        <div style="font-size:11px;color:var(--text-secondary);margin-top:5px;">
                            <i class="bi bi-info-circle"></i>
                            Gmail : ⋮ → Afficher l'original | Outlook : Fichier → Propriétés
                        </div>
                    </div>
                </div>

                <div
                    style="margin:20px 0;padding:14px;background:var(--bg-input);border-radius:var(--radius);font-size:12px;color:var(--text-secondary);border:1px solid var(--border-subtle);">
                    <i class="bi bi-robot" style="color:var(--emerald);"></i>
                    <strong style="color:var(--emerald);">Analyse IA automatique :</strong>
                    Dès la soumission, notre IA analysera le contenu et calculera un score de risque (0-100).
                    Pour les URLs, VirusTotal sera également consulté (70+ moteurs antivirus).
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn-cyber btn-cyber-danger" style="padding:12px 24px;">
                        <i class="bi bi-send-fill"></i> SOUMETTRE ET ANALYSER
                    </button>
                    <a href="{{ route('user.reports.index') }}" class="btn-cyber btn-cyber-warning"
                        style="padding:12px 24px;">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function updateForm(type) {
            const field = document.getElementById('contentField');
            const headers = document.getElementById('headersSection');
            const ph = {
                'url': 'Collez l\'URL suspecte ici\nEx: http://micros0ft-login.tk/account-verify',
                'email': 'Collez le corps complet de l\'email suspect ici...',
                'sms': 'Collez le contenu du SMS suspect ici...',
                'other': 'Décrivez la menace en détail...',
            };
            if (ph[type]) field.placeholder = ph[type];
            headers.style.display = (type === 'email') ? 'block' : 'none';
        }
    </script>
@endsection