@extends('layouts.app')
@section('title', 'IA Forensic')
@section('page-title', 'IA FORENSIC')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>IA Forensic</span></div>
                <div class="page-header-title">Analyse Forensic IA</div>
                <div class="page-header-sub">Analyse approfondie des en-têtes email via Claude AI</div>
            </div>
            <div id="apiStatusBadge" class="cyber-badge badge-pending">
                <i class="bi bi-circle-fill" style="font-size:6px;animation:blink 2s infinite;"></i> Vérification IA...
            </div>
        </div>

        <div class="row g-3">
            <!-- Input -->
            <div class="col-md-5">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-code-square"></i> EN-TÊTES EMAIL</div>
                        <span class="cyber-badge badge-info">Claude AI</span>
                    </div>

                    <div class="cyber-form-group">
                        <label class="cyber-label">COLLEZ LES HEADERS COMPLETS</label>
                        <textarea id="headersInput" class="cyber-textarea" rows="16"
                            placeholder="Received: from mail.suspect.tk (185.220.101.45)&#10;  by mx.company.com...&#10;From: security@micros0ft.com&#10;DKIM-Signature: v=1;...&#10;X-Mailer: Mass Mailer Pro"></textarea>
                    </div>

                    <button onclick="loadExample()" class="btn-cyber btn-cyber-warning w-100 justify-content-center mb-2" style="padding:9px;">
                        <i class="bi bi-lightning-fill"></i> Charger un exemple
                    </button>

                    <button onclick="analyzeHeaders()" id="analyzeBtn"
                            class="btn-cyber btn-cyber-primary w-100 justify-content-center" style="padding:12px;">
                        <i class="bi bi-cpu-fill"></i> ANALYSER VIA IA FORENSIC
                    </button>

                    <!-- Guide -->
                    <div style="margin-top:16px;padding:14px;background:var(--bg-input);border-radius:var(--radius);font-size:12px;color:var(--text-secondary);border:1px solid var(--border-subtle);">
                        <div style="font-weight:600;color:var(--text-primary);margin-bottom:8px;">📋 Comment obtenir les headers ?</div>
                        <div style="line-height:1.9;">
                            <strong>Gmail :</strong> ⋮ → Afficher l'original<br>
                            <strong>Outlook :</strong> Fichier → Propriétés<br>
                            <strong>Thunderbird :</strong> Ctrl+U<br>
                            <strong>Apple Mail :</strong> Présentation → Message → Headers longs
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résultats -->
            <div class="col-md-7">
                <!-- État initial -->
                <div id="resultEmpty" class="cyber-card" style="text-align:center;padding:80px 20px;">
                    <i class="bi bi-cpu" style="font-size:48px;color:var(--text-muted);display:block;margin-bottom:16px;"></i>
                    <div style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-secondary);letter-spacing:2px;">EN ATTENTE D'ANALYSE</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:8px;">Collez des en-têtes et lancez l'analyse</div>
                </div>

                <!-- Loading -->
                <div id="resultLoading" style="display:none;" class="cyber-card" style="text-align:center;padding:80px 20px;">
                    <div style="text-align:center;padding:60px 20px;">
                        <i class="bi bi-cpu-fill" style="font-size:48px;color:var(--emerald);display:block;margin-bottom:16px;animation:pulse 1s infinite;"></i>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--emerald);letter-spacing:2px;">ANALYSE IA EN COURS...</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:8px;">Claude analyse les headers...</div>
                    </div>
                </div>

                <!-- Résultats -->
                <div id="resultCard" style="display:none;">
                    <!-- Métriques principales -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-icon red"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <div class="stat-value text-red font-mono" id="resOriginIp" style="font-size:14px;">-</div>
                                    <div class="stat-label">IP ORIGINE</div>
                                    <div style="font-size:11px;color:var(--text-secondary);" id="resCountry">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-icon amber"><i class="bi bi-shield-exclamation"></i></div>
                                <div>
                                    <div class="stat-value" id="resRisk" style="font-size:14px;">-</div>
                                    <div class="stat-label">NIVEAU RISQUE</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auth SPF/DKIM/DMARC -->
                    <div class="cyber-card mb-3">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-shield-fill-check"></i> AUTHENTIFICATION EMAIL</div>
                        </div>
                        <div class="row g-2" id="authChecks"></div>
                    </div>

                    <!-- IOC -->
                    <div class="cyber-card mb-3">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-exclamation-triangle-fill"></i> INDICATEURS (IOC)</div>
                        </div>
                        <div id="iocList"></div>
                    </div>

                    <!-- Relays + recommandation -->
                    <div class="cyber-card">
                        <div class="cyber-card-header">
                            <div class="cyber-card-title"><i class="bi bi-diagram-3-fill"></i> SERVEURS RELAIS</div>
                        </div>
                        <div id="relayList" style="margin-bottom:14px;"></div>
                        <div id="recommendation" style="padding:14px;border-radius:var(--radius);font-size:13px;line-height:1.7;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
    // Vérifie le status API
    async function checkApi() {
        try {
            const res  = await fetch('{{ route("user.analyzer.scan") }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({content:'test',type:'other'})
            });
            const data = await res.json();
            const badge = document.getElementById('apiStatusBadge');
            const isReal = data.api_used === 'claude';
            badge.className = 'cyber-badge ' + (isReal ? 'badge-low' : 'badge-high');
            badge.innerHTML = `<i class="bi bi-circle-fill" style="font-size:6px;"></i> ${isReal ? 'Claude AI connecté' : 'Mode simulation'}`;
        } catch(e) {}
    }
    checkApi();

    function loadExample() {
        document.getElementById('headersInput').value =
    `Received: from mail.phish-domain.tk (185.220.101.45)
      by mx.company.ma with SMTP; Mon, 28 Apr 2026 10:23:11 +0100
    Received: from [192.168.1.1] by mail.phish-domain.tk
    From: "Microsoft Security" <security@micros0ft-alerts.tk>
    To: employee@company.ma
    Subject: URGENT: Your account will be suspended
    Date: Mon, 28 Apr 2026 09:23:11 +0000
    Message-ID: <fake123@phish-domain.tk>
    X-Mailer: Atomic Mass Mailer 5.0
    DKIM-Signature: v=1; a=rsa-sha256; d=phish-domain.tk;
    Return-Path: <bounce@phish-domain.tk>
    X-Spam-Status: Yes, score=8.2`;
    }

    async function analyzeHeaders() {
        const headers = document.getElementById('headersInput').value.trim();
        if (!headers) { alert('Collez des en-têtes email avant d\'analyser.'); return; }

        const btn = document.getElementById('analyzeBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> ANALYSE EN COURS...';

        document.getElementById('resultEmpty').style.display   = 'none';
        document.getElementById('resultLoading').style.display = 'block';
        document.getElementById('resultCard').style.display    = 'none';

        try {
            const res = await fetch('{{ route("admin.forensic.analyze") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                // Utilise email_headers (pas headers — réservé Laravel)
                body: JSON.stringify({ email_headers: headers })
            });

            if (!res.ok) {
                const err = await res.json();
                alert('Erreur API : ' + (err.message || 'Inconnue'));
                return;
            }

            const data = await res.json();
            displayResult(data);
        } catch(e) {
            alert('Erreur de connexion : ' + e.message);
            document.getElementById('resultEmpty').style.display   = 'block';
            document.getElementById('resultLoading').style.display = 'none';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cpu-fill"></i> ANALYSER VIA IA FORENSIC';
            document.getElementById('resultLoading').style.display = 'none';
        }
    }

    function displayResult(data) {
        document.getElementById('resultCard').style.display = 'block';

        // IP + Pays
        document.getElementById('resOriginIp').textContent = data.origin_ip || 'Inconnu';
        document.getElementById('resCountry').textContent  = '🌍 ' + (data.origin_country || 'N/A');

        // Niveau risque
        const riskEl    = document.getElementById('resRisk');
        const riskMap   = { critical:'var(--rose)', high:'var(--amber)', medium:'var(--sky)', low:'var(--emerald)' };
        const riskColor = riskMap[data.risk_level] || 'var(--emerald)';
        riskEl.textContent = (data.risk_level || 'unknown').toUpperCase();
        riskEl.style.color = riskColor;

        // Auth checks SPF/DKIM/DMARC
        const checks = [
            { label:'SPF',   val:data.spf,   pass:data.spf==='PASS' },
            { label:'DKIM',  val:data.dkim,  pass:data.dkim==='PASS' },
            { label:'DMARC', val:data.dmarc, pass:data.dmarc==='PASS' },
        ];
        document.getElementById('authChecks').innerHTML = checks.map(c => `
            <div class="col-4">
                <div style="padding:14px;text-align:center;background:${c.pass?'rgba(16,185,129,0.08)':'rgba(244,63,94,0.08)'};border-radius:var(--radius);border:1px solid ${c.pass?'var(--border-muted)':'rgba(244,63,94,.2)'};">
                    <i class="bi bi-${c.pass?'check-circle-fill':'x-circle-fill'}" style="color:${c.pass?'var(--emerald)':'var(--rose)'};font-size:22px;display:block;margin-bottom:6px;"></i>
                    <div style="font-family:'JetBrains Mono',monospace;font-size:12px;color:${c.pass?'var(--emerald)':'var(--rose)'};">${c.label}: ${c.val||'N/A'}</div>
                </div>
            </div>
        `).join('');

        // IOC
        const iocs = data.ioc || [];
        document.getElementById('iocList').innerHTML = iocs.length
            ? iocs.map(i => `
                <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;background:var(--bg-input);border-radius:var(--radius-sm);margin-bottom:6px;font-size:12px;color:var(--text-primary);">
                    <i class="bi bi-dot" style="color:var(--rose);font-size:24px;flex-shrink:0;margin-top:-4px;"></i>
                    ${i}
                </div>
            `).join('')
            : '<div style="color:var(--text-muted);font-size:13px;padding:10px;">Aucun IOC détecté.</div>';

        // Relays
        const relays = data.relay_servers || [];
        document.getElementById('relayList').innerHTML = relays.length
            ? relays.map((r, i) => `
                <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--bg-input);border-radius:var(--radius-sm);margin-bottom:4px;font-size:12px;font-family:'JetBrains Mono',monospace;">
                    <span style="color:var(--text-muted);">${i+1}</span>
                    <i class="bi bi-arrow-right" style="color:var(--amber);"></i>
                    <span>${r}</span>
                </div>
            `).join('')
            : '<div style="color:var(--text-muted);font-size:13px;padding:10px;">Aucun relay identifié.</div>';

        // Recommandation
        const rec = document.getElementById('recommendation');
        rec.style.background   = 'rgba(245,158,11,0.06)';
        rec.style.border       = '1px solid rgba(245,158,11,0.2)';
        rec.style.borderLeft   = '3px solid var(--amber)';
        rec.innerHTML = `<strong style="color:var(--amber);">💡 Recommandation :</strong> ${data.recommendation || 'Aucune recommandation.'}`;
    }
    </script>
@endsection