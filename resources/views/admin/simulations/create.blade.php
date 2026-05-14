@extends('layouts.app')
@section('title', 'Nouvelle Simulation')
@section('page-title', 'NOUVELLE CAMPAGNE')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Simulations</span> / Nouvelle</div>
                <div class="page-header-title">Créer une campagne</div>
            </div>
            <a href="{{ route('admin.simulations.index') }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <form method="POST" action="{{ route('admin.simulations.store') }}">
            @csrf
            <div class="row g-3">

                <!-- Config campagne -->
                <div class="col-md-6">
                    <div class="cyber-card">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-gear-fill"></i> CONFIGURATION</div>
                        </div>
                        <div class="cyber-form-group">
                            <label class="cyber-label">NOM DE LA CAMPAGNE *</label>
                            <input type="text" name="name" class="cyber-input" required
                                placeholder="Ex: Test Microsoft Q2 2026" value="{{ old('name') }}">
                        </div>
                        <div class="cyber-form-group">
                            <label class="cyber-label">TEMPLATE *</label>
                            <select name="template" class="cyber-select" required onchange="loadTemplate(this.value)">
                                <option value="">Sélectionner...</option>
                                <option value="microsoft">📧 Fausse alerte Microsoft</option>
                                <option value="bank">🏦 Fausse alerte bancaire</option>
                                <option value="hr">👥 Faux email RH</option>
                                <option value="delivery">📦 Fausse livraison</option>
                                <option value="custom">✏️ Personnalisé</option>
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="cyber-form-group">
                                    <label class="cyber-label">NOM EXPÉDITEUR *</label>
                                    <input type="text" name="from_name" id="fromName" class="cyber-input" required
                                        placeholder="Microsoft Security" value="{{ old('from_name') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="cyber-form-group">
                                    <label class="cyber-label">EMAIL EXPÉDITEUR *</label>
                                    <input type="email" name="from_email" id="fromEmail" class="cyber-input" required
                                        placeholder="security@microsoft.com" value="{{ old('from_email') }}">
                                </div>
                            </div>
                        </div>
                        <div class="cyber-form-group">
                            <label class="cyber-label">OBJET DE L'EMAIL *</label>
                            <input type="text" name="subject" id="subject" class="cyber-input" required
                                placeholder="Alerte sécurité : action requise" value="{{ old('subject') }}">
                        </div>
                    </div>
                </div>

                <!-- Cibles -->
                <div class="col-md-6">
                    <div class="cyber-card h-100">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-people-fill"></i> CIBLES</div>
                            <button type="button" onclick="selectAll()" class="btn-cyber btn-cyber-primary"
                                style="padding:5px 12px;font-size:11px;">Tout sélectionner</button>
                        </div>
                        <div style="max-height:280px;overflow-y:auto;scrollbar-width:thin;">
                            @foreach($users as $u)
                                <label
                                    style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;cursor:pointer;margin-bottom:4px;border:1px solid var(--border-solid);transition:all .2s;"
                                    class="user-target-item">
                                    <input type="checkbox" name="targets[]" value="{{ $u->id }}"
                                        style="accent-color:var(--neon-cyan);width:16px;height:16px;" onchange="updateCount()">
                                    <div class="user-avatar" style="width:32px;height:32px;font-size:11px;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:13px;">{{ $u->name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $u->department ?? 'N/A' }} ·
                                            Score: {{ $u->vigilance_score }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div
                            style="margin-top:12px;padding:10px;background:rgba(0,245,255,0.04);border-radius:8px;font-family:'Share Tech Mono',monospace;font-size:12px;color:var(--neon-cyan);">
                            <i class="bi bi-people-fill"></i> <span id="targetCount">0</span> cible(s) sélectionnée(s)
                        </div>
                        @error('targets')<div class="cyber-invalid">Sélectionnez au moins une cible.</div>@enderror
                    </div>
                </div>

                <!-- Corps email -->
                <div class="col-12">
                    <div class="cyber-card">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-file-text-fill"></i> CORPS DE L'EMAIL</div>
                            <span style="font-size:11px;color:var(--text-muted);">Utilise <code style="background:rgba(0,245,255,0.1);padding:1px 5px;border-radius:3px;color:var(--neon-cyan);">@{{name}}</code> pour personnaliser</span>
                        </div>
                        <textarea name="body" id="body" class="cyber-textarea" rows="12" required
                            placeholder="Rédigez le corps de l'email de simulation...">{{ old('body') }}</textarea>

                        <!-- Aperçu -->
                        <div style="margin-top:12px;">
                            <button type="button" onclick="togglePreview()" class="btn-cyber btn-cyber-warning"
                                style="padding:8px 16px;font-size:11px;">
                                <i class="bi bi-eye"></i> Aperçu email
                            </button>
                        </div>
                        <div id="preview"
                            style="display:none;margin-top:12px;padding:20px;background:white;border-radius:8px;color:#333;font-family:Arial,sans-serif;font-size:14px;">
                        </div>
                    </div>
                </div>
                

                <div class="col-12">
                    <!-- Exemples de simulations prêts à l'emploi -->
<div class="col-12">
    <div style="padding:16px;background:var(--bg-input);border-radius:var(--radius);border:1px solid var(--border-subtle);">
        <div style="font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:2px;color:var(--text-secondary);margin-bottom:12px;">
            ⚡ EXEMPLES 
        </div>
        <div class="row g-2">
            @foreach([
                ['label'=>'🔵 Microsoft','icon'=>'bi-microsoft','val'=>'microsoft'],
                ['label'=>'🏦 Banque CIH','icon'=>'bi-bank2','val'=>'bank'],
                ['label'=>'👥 RH Paie','icon'=>'bi-people-fill','val'=>'hr'],
                ['label'=>'📦 DHL Livraison','icon'=>'bi-box-seam-fill','val'=>'delivery'],
            ] as $tpl)
            <div class="col-md-3 col-6">
                <button type="button" onclick="loadTemplate('{{ $tpl['val'] }}')"
                    style="width:100%;padding:12px;background:var(--bg-elevated);border:1px solid var(--border-subtle);border-radius:var(--radius);cursor:pointer;text-align:center;transition:all .15s;color:var(--text-secondary);font-size:12px;font-weight:600;"
                    onmouseover="this.style.borderColor='var(--border-muted)';this.style.color='var(--emerald)';"
                    onmouseout="this.style.borderColor='var(--border-subtle)';this.style.color='var(--text-secondary)';">
                    <i class="bi {{ $tpl['icon'] }}" style="display:block;font-size:20px;margin-bottom:6px;"></i>
                    {{ $tpl['label'] }}
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>
                    <button type="submit" class="btn-cyber btn-cyber-danger" style="padding:14px 32px;">
                        <i class="bi bi-save-fill"></i> CRÉER LA CAMPAGNE
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @verbatim
    <script>
        const templates = {
            microsoft: {
                fromName: 'Microsoft Security',
                fromEmail: 'security-alert@microsoft-support.com',
                subject: '⚠️ Activité suspecte détectée sur votre compte Microsoft',
                body: `Cher(e) {{name}},

                        Nous avons détecté une connexion inhabituelle sur votre compte Microsoft depuis une localisation inconnue.

                        📍 Localisation : Moscou, Russie
                        🕐 Heure : ${new Date().toLocaleString('fr-FR')}
                        💻 Appareil : Windows 11 Chrome

                        Si vous n'êtes pas à l'origine de cette connexion, votre compte est peut-être compromis.

                        ➡️ Cliquez ici pour sécuriser votre compte immédiatement

                        Si vous ne sécurisez pas votre compte dans les 24 heures, il sera temporairement suspendu.

                        Cordialement,
                        L'équipe Sécurité Microsoft`
            },
            bank: {
                fromName: 'CIH Bank Sécurité',
                fromEmail: 'alerte@cih-banque-secure.com',
                subject: '🚨 URGENT : Votre compte bancaire a été suspendu',
                body: `Cher(e) Client(e) {{name}},

                        Suite à des transactions suspectes détectées sur votre compte, nous avons été contraints de le suspendre temporairement.

                        Pour réactiver votre compte et éviter la perte de vos fonds, veuillez vérifier votre identité en cliquant sur le lien ci-dessous.

                        ➡️ Réactiver mon compte maintenant

                        Cette action doit être effectuée dans les 48h ouvrées.

                        Cordialement,
                        Service Sécurité CIH Bank`
            },
            hr: {
                fromName: 'Direction RH',
                fromEmail: 'rh-noreply@company-internal.com',
                subject: '📋 Action requise : Mise à jour de vos informations salariales',
                body: `Bonjour {{name}},

                        Suite à la mise à jour de notre système de paie, nous vous demandons de confirmer vos coordonnées bancaires pour le virement de votre salaire du mois prochain.

                        ➡️ Mettre à jour mes informations

                        Sans action de votre part avant vendredi, votre virement pourrait être retardé.

                        Cordialement,
                        Direction des Ressources Humaines`
            },
            delivery: {
                fromName: 'DHL Express',
                fromEmail: 'tracking@dhl-express-delivery.tk',
                subject: '📦 Votre colis est en attente — Frais de douane requis',
                body: `Bonjour {{name}},

                        Votre colis (N° tracking : DHL-2026-{{name}}) est en attente à notre centre de tri.

                        Des frais de douane de 4,99€ sont requis pour finaliser la livraison.

                        ➡️ Payer les frais et recevoir mon colis

                        Sans paiement sous 72h, le colis sera retourné à l'expéditeur.

                        DHL Express`
            },
            custom: {
                fromName: '',
                fromEmail: '',
                subject: '',
                body: ''
            }
        };

        function loadTemplate(type) {
            if (!templates[type]) return;
            const t = templates[type];
            document.getElementById('fromName').value = t.fromName;
            document.getElementById('fromEmail').value = t.fromEmail;
            document.getElementById('subject').value = t.subject;
            document.getElementById('body').value = t.body;
        }

        function togglePreview() {
            const preview = document.getElementById('preview');
            const body = document.getElementById('body').value;
            if (preview.style.display === 'none') {
                preview.style.display = 'block';
                preview.innerHTML = body.replace(/\n/g, '<br>').replace(/{{name}}/g, '<strong>[Nom Cible]</strong>');
                            } else {
                preview.style.display = 'none';
            }
        }

        function selectAll() {
            document.querySelectorAll('input[name="targets[]"]').forEach(cb => { cb.checked = true; });
            updateCount();
        }C

        function updateCount() {
            const count = document.querySelectorAll('input[name="targets[]"]:checked').length;
            document.getElementById('targetCount').textContent = count;
        }

        // Style checkbox hover
        document.querySelectorAll('.user-target-item').forEach(item => {
            item.addEventListener('mouseover', () => { item.style.background = 'rgba(0,245,255,0.04)'; item.style.borderColor = 'var(--border-glow)'; });
            item.addEventListener('mouseout', () => { item.style.background = ''; item.style.borderColor = 'var(--border-solid)'; });
        });
    </script>
    @endverbatim
@endsection