@extends('layouts.app')
@section('title', 'Analyseur IA')
@section('page-title', 'ANALYSEUR IA')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <div class="page-breadcrumb">SPG / <span>Analyseur IA</span></div>
            <div class="page-header-title">Analyseur IA Temps Réel</div>
            <div class="page-header-sub">Détection de phishing propulsée par Claude AI</div>
        </div>
        <div id="apiStatus" style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:6px 12px;border-radius:4px;border:1px solid var(--border);">
            <span id="apiDot" style="display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--text-muted);margin-right:6px;"></span>
            <span id="apiLabel">Vérification...</span>
        </div>
    </div>

    <div class="row g-3">
        <!-- Input -->
        <div class="col-lg-5">
            <div class="cyber-card">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-radar"></i> SOUMETTRE</div>
                </div>

                <!-- Type selector -->
                <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
                    @foreach(['url'=>'🔗 URL','email'=>'📧 Email','sms'=>'💬 SMS','other'=>'⚠️ Autre'] as $val=>$lbl)
                    <button type="button" onclick="setType('{{ $val }}')" id="type-{{ $val }}"
                        class="type-btn {{ $val==='url'?'active':'' }}"
                        style="padding:7px 14px;border-radius:4px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:1px;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text-dim);transition:all .18s;">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>

                <div class="cyber-form-group">
                    <label class="cyber-label">CONTENU À ANALYSER</label>
                    <textarea id="analyzeContent" class="cyber-textarea" rows="7"
                        placeholder="Collez ici l'URL, l'email ou le SMS suspect..."></textarea>
                </div>

                <button onclick="runAnalysis()" id="analyzeBtn"
                    class="btn-cyber btn-cyber-primary w-100 justify-content-center" style="padding:12px;">
                    <i class="bi bi-play-circle-fill"></i> ANALYSER
                </button>

                <!-- Exemples rapides -->
                <div style="margin-top:16px;padding:12px;background:var(--bg-input);border-radius:6px;border:1px solid var(--border);">
                    <div style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-dim);letter-spacing:2px;margin-bottom:8px;">EXEMPLES RAPIDES</div>
                    <div style="display:flex;flex-direction:column;gap:5px;">
                        @foreach([
                            ['url','http://micros0ft-login.tk/verify-account'],
                            ['email','Votre compte a été suspendu. Cliquez immédiatement.'],
                            ['url','https://www.google.com'],
                        ] as [$t,$ex])
                        <button onclick="loadExample('{{ $t }}','{{ addslashes($ex) }}')"
                            style="background:transparent;border:1px solid var(--border);color:var(--text-dim);padding:6px 10px;border-radius:4px;font-size:10px;cursor:pointer;text-align:left;font-family:'JetBrains Mono',monospace;transition:all .15s;"
                            onmouseover="this.style.borderColor='var(--border-med)';this.style.color='var(--green)'"
                            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-dim)'">
                            {{ Str::limit($ex, 45) }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Résultats -->
        <div class="col-lg-7">
            <!-- État initial -->
            <div id="resultEmpty" class="cyber-card" style="text-align:center;padding:60px 20px;">
                <i class="bi bi-cpu" style="font-size:44px;color:var(--text-muted);display:block;margin-bottom:14px;"></i>
                <div style="font-family:'JetBrains Mono',monospace;color:var(--text-dim);font-size:11px;letter-spacing:2px;">EN ATTENTE D'ANALYSE</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:8px;">Soumettez un contenu pour démarrer</div>
            </div>

            <!-- Résultat -->
            <div id="resultCard" style="display:none;">
                <div class="cyber-card mb-3">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-activity"></i> RÉSULTAT</div>
                        <span id="verdictBadge" class="cyber-badge"></span>
                    </div>

                    <div style="display:flex;align-items:center;gap:24px;margin-bottom:20px;">
                        <div style="text-align:center;flex-shrink:0;">
                            <div id="scoreDisplay" class="risk-score-num" style="color:var(--green);">0</div>
                            <div style="font-size:9px;color:var(--text-dim);font-family:'JetBrains Mono',monospace;letter-spacing:1px;">/100</div>
                        </div>
                        <div style="flex:1;">
                            <div class="cyber-progress" style="height:8px;margin-bottom:12px;">
                                <div id="scoreBar" class="cyber-progress-fill progress-green" style="width:0%;transition:width 1s ease;"></div>
                            </div>
                            <div id="analysisText" style="font-size:13px;line-height:1.7;color:var(--text-dim);"></div>
                        </div>
                    </div>

                    <!-- Indicateurs -->
                    <div id="indicatorsSection">
                        <div style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-dim);letter-spacing:2px;margin-bottom:8px;">INDICATEURS</div>
                        <div id="indicatorsList"></div>
                    </div>

                    <!-- Recommandation -->
                    <div id="recommendation" style="margin-top:14px;padding:12px;border-radius:6px;font-size:13px;border-left:3px solid var(--green);background:rgba(57,255,126,.04);"></div>
                </div>

                <!-- VirusTotal -->
                <div class="cyber-card" id="vtCard" style="display:none;">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-shield-exclamation"></i> VIRUSTOTAL</div>
                        <span id="vtSource" class="cyber-badge badge-pending"></span>
                    </div>
                    <div class="row g-2 text-center" id="vtStats"></div>
                    <div id="vtLink" style="margin-top:12px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentType = 'url';

// Style boutons type
function setType(type) {
    currentType = type;
    document.querySelectorAll('.type-btn').forEach(b => {
        b.style.background = 'transparent';
        b.style.color = 'var(--text-dim)';
        b.style.borderColor = 'var(--border)';
    });
    const active = document.getElementById('type-' + type);
    active.style.background = 'var(--green-glow)';
    active.style.color = 'var(--green)';
    active.style.borderColor = 'var(--border-med)';
}
setType('url');

function loadExample(type, content) {
    setType(type);
    document.getElementById('analyzeContent').value = content;
}

// Vérifier status API
async function checkApiStatus() {
    try {
        const res  = await fetch('{{ route("user.analyzer.scan") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({content:'test',type:'other'})
        });
        const data = await res.json();
        const isReal = data.api_used === 'claude';
        document.getElementById('apiDot').style.background   = isReal ? 'var(--green)' : 'var(--amber)';
        document.getElementById('apiLabel').textContent = isReal ? 'Claude AI connecté' : 'Mode simulation';
        document.getElementById('apiLabel').style.color = isReal ? 'var(--green)' : 'var(--amber)';
    } catch(e) {
        document.getElementById('apiLabel').textContent = 'Service indisponible';
        document.getElementById('apiLabel').style.color = 'var(--red)';
    }
}
checkApiStatus();

async function runAnalysis() {
    const content = document.getElementById('analyzeContent').value.trim();
    if (!content) { alert('Veuillez saisir un contenu à analyser.'); return; }

    const btn = document.getElementById('analyzeBtn');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> ANALYSE EN COURS...';
    btn.disabled = true;

    try {
        const res  = await fetch('{{ route("user.analyzer.scan") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({content, type: currentType})
        });
        const data = await res.json();
        displayResult(data);
    } catch(e) {
        alert('Erreur de connexion. Réessayez.');
    }

    btn.innerHTML = '<i class="bi bi-play-circle-fill"></i> ANALYSER';
    btn.disabled = false;
}

function displayResult(data) {
    document.getElementById('resultEmpty').style.display = 'none';
    document.getElementById('resultCard').style.display  = 'block';

    const score   = data.score || 0;
    const verdict = data.verdict || 'safe';

    // Score color
    const colors = {phishing:'var(--red)',suspicious:'var(--amber)',safe:'var(--green)'};
    const barCls  = {phishing:'progress-red',suspicious:'progress-orange',safe:'progress-green'};
    const color   = colors[verdict] || 'var(--green)';

    document.getElementById('scoreDisplay').textContent = score;
    document.getElementById('scoreDisplay').style.color = color;

    const bar = document.getElementById('scoreBar');
    bar.style.width = score + '%';
    bar.className   = 'cyber-progress-fill ' + (barCls[verdict] || 'progress-green');

    // Badge
    const badge = document.getElementById('verdictBadge');
    const badgeMap = {
        phishing:  ['badge-critical','🚨 PHISHING'],
        suspicious:['badge-high','⚠️ SUSPECT'],
        safe:      ['badge-low','✅ SÛR']
    };
    const [cls, lbl] = badgeMap[verdict] || ['badge-info','INCONNU'];
    badge.className   = 'cyber-badge ' + cls;
    badge.textContent = lbl;

    document.getElementById('analysisText').textContent = data.analysis || '';

    // Indicateurs
    const indicators = data.indicators || [];
    document.getElementById('indicatorsList').innerHTML = indicators.map(i =>
        `<div style="display:flex;align-items:center;gap:8px;padding:5px 10px;background:var(--bg-input);border-radius:4px;font-size:11px;margin-bottom:4px;color:var(--text-dim);font-family:'JetBrains Mono',monospace;">
            <span style="color:var(--amber);">▸</span>${i}
        </div>`
    ).join('');

    // Recommandation
    const rec = document.getElementById('recommendation');
    rec.style.borderLeftColor = color;
    rec.style.background = verdict==='phishing' ? 'rgba(255,68,85,.04)' : verdict==='suspicious' ? 'rgba(255,184,0,.04)' : 'rgba(57,255,126,.04)';
    rec.innerHTML = `<strong style="color:${color};">Recommandation :</strong> ${data.recommendation || ''}`;

    // VirusTotal
    if (data.virustotal) {
        const vt = data.virustotal;
        document.getElementById('vtCard').style.display = 'block';
        document.getElementById('vtSource').textContent = vt.source === 'virustotal' ? 'VirusTotal Live' : 'Simulation';
        document.getElementById('vtStats').innerHTML = [
            {k:'malicious', c:'var(--red)',   l:'MALVEILLANT'},
            {k:'suspicious',c:'var(--amber)', l:'SUSPECT'},
            {k:'harmless',  c:'var(--green)', l:'SÛR'},
            {k:'undetected',c:'var(--text-dim)',l:'NON DÉT.'},
        ].map(({k,c,l}) => `
            <div class="col-3">
                <div style="padding:12px;background:var(--bg-input);border-radius:6px;border:1px solid var(--border);">
                    <div style="font-size:22px;font-family:'JetBrains Mono',monospace;color:${c};">${vt[k]||0}</div>
                    <div style="font-size:9px;color:var(--text-dim);letter-spacing:1px;">${l}</div>
                </div>
            </div>
        `).join('');

        if (vt.url && vt.url !== '#') {
            document.getElementById('vtLink').innerHTML = `<a href="${vt.url}" target="_blank" class="btn-cyber btn-cyber-primary" style="font-size:10px;padding:6px 14px;"><i class="bi bi-box-arrow-up-right"></i> Voir sur VirusTotal</a>`;
        }
    }
}
</script>
@endsection