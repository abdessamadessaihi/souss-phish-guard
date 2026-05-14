<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Souss Phish Guard — Plateforme Cybersécurité Entreprise</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
  --emerald:#10b981; --emerald-dim:rgba(16,185,129,0.1);
  --amber:#f59e0b; --rose:#f43f5e;
  --sky:#0ea5e9; --violet:#8b5cf6;
  --bg:#0d1117; --surface:#161b22; --elevated:#1c2333;
  --border:rgba(48,54,61,0.8); --border-em:rgba(16,185,129,0.2);
  --text:#e6edf3; --muted:#8b949e; --dim:#484f58;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Space Grotesk',sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden;}

/* BG */
.hero-bg{position:fixed;inset:0;z-index:0;pointer-events:none;
  background:radial-gradient(ellipse 80% 50% at 50% -10%,rgba(16,185,129,0.08) 0%,transparent 70%),
             radial-gradient(ellipse 40% 40% at 100% 100%,rgba(139,92,246,0.05) 0%,transparent 60%);}
.grid-bg{position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:linear-gradient(rgba(48,54,61,0.35) 1px,transparent 1px),linear-gradient(90deg,rgba(48,54,61,0.35) 1px,transparent 1px);
  background-size:40px 40px;mask-image:radial-gradient(ellipse 100% 100% at 50% 0%,black 40%,transparent 100%);}

/* NAVBAR */
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 5%;transition:all .3s;border-bottom:1px solid transparent;}
nav.scrolled{background:rgba(13,17,23,0.95);backdrop-filter:blur(12px);border-color:var(--border);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:64px;}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
.logo-icon{width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,var(--emerald),#059669);display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 0 20px rgba(16,185,129,0.3);}
.logo-text{font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:700;color:var(--emerald);letter-spacing:1px;}
.logo-sub{font-size:9px;color:var(--muted);letter-spacing:2px;display:block;}
.nav-links{display:flex;align-items:center;gap:28px;}
.nav-links a{color:var(--muted);text-decoration:none;font-size:13px;font-weight:500;transition:color .2s;}
.nav-links a:hover{color:var(--text);}
.nav-cta{display:flex;align-items:center;gap:10px;}
.btn-nav{padding:7px 16px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;border:1px solid;}
.btn-nav-ghost{background:none;border-color:var(--border);color:var(--muted);}
.btn-nav-ghost:hover{border-color:var(--border-em);color:var(--emerald);}
.btn-nav-solid{background:var(--emerald);border-color:var(--emerald);color:#fff;}
.btn-nav-solid:hover{background:#059669;box-shadow:0 0 20px rgba(16,185,129,0.3);}
.btn-nav-admin{background:rgba(139,92,246,0.1);border-color:rgba(139,92,246,0.3);color:var(--violet);}
.btn-nav-admin:hover{background:rgba(139,92,246,0.2);}

/* HERO */
.hero{min-height:100vh;display:flex;align-items:center;padding:80px 5% 60px;position:relative;z-index:1;}
.hero-content{max-width:680px;}
.hero-badge{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:20px;background:var(--emerald-dim);border:1px solid var(--border-em);font-size:11px;font-family:'JetBrains Mono',monospace;color:var(--emerald);letter-spacing:1px;margin-bottom:28px;}
.hero-badge span{width:6px;height:6px;border-radius:50%;background:var(--emerald);animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.hero h1{font-size:clamp(32px,5vw,54px);font-weight:800;line-height:1.15;letter-spacing:-1px;margin-bottom:20px;}
.hero h1 .accent{color:var(--emerald);}
.hero p{font-size:17px;color:var(--muted);line-height:1.8;margin-bottom:36px;max-width:540px;}
.hero-cta{display:flex;gap:14px;flex-wrap:wrap;}
.btn-hero-primary{padding:14px 28px;border-radius:8px;background:var(--emerald);color:#fff;text-decoration:none;font-weight:700;font-size:14px;transition:all .2s;border:1px solid var(--emerald);}
.btn-hero-primary:hover{background:#059669;box-shadow:0 0 30px rgba(16,185,129,0.3);color:#fff;}
.btn-hero-ghost{padding:14px 28px;border-radius:8px;background:none;color:var(--text);text-decoration:none;font-weight:600;font-size:14px;transition:all .2s;border:1px solid var(--border);}
.btn-hero-ghost:hover{border-color:var(--border-em);color:var(--emerald);}

/* Dashboard preview */
.hero-visual{position:absolute;right:5%;top:50%;transform:translateY(-50%);width:44%;max-width:580px;}
.dashboard-preview{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.5),0 0 40px rgba(16,185,129,0.05);}
.dash-bar{height:36px;background:rgba(13,17,23,0.8);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;padding:0 14px;}
.dash-dot{width:10px;height:10px;border-radius:50%;}
.dash-title{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--dim);margin-left:8px;letter-spacing:1px;}
.dash-body{padding:16px;display:flex;flex-direction:column;gap:10px;}
.dash-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
.dash-stat{background:var(--elevated);border-radius:6px;padding:12px;border:1px solid var(--border);}
.dash-stat-val{font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:700;}
.dash-stat-lbl{font-size:9px;color:var(--dim);margin-top:2px;letter-spacing:1px;}
.dash-chart{background:var(--elevated);border-radius:6px;padding:12px;height:80px;border:1px solid var(--border);display:flex;align-items:flex-end;gap:4px;}
.dash-bar-item{flex:1;border-radius:3px 3px 0 0;transition:height .5s;}
.dash-table{background:var(--elevated);border-radius:6px;border:1px solid var(--border);overflow:hidden;}
.dash-row{display:flex;justify-content:space-between;padding:8px 12px;border-bottom:1px solid var(--border);font-size:10px;}
.dash-row:last-child{border-bottom:none;}
.badge-sm{padding:2px 6px;border-radius:3px;font-size:8px;font-family:'JetBrains Mono',monospace;}

/* SECTIONS */
section{position:relative;z-index:1;padding:80px 5%;}
.section-badge{display:inline-block;padding:5px 14px;border-radius:20px;background:var(--emerald-dim);border:1px solid var(--border-em);font-size:10px;font-family:'JetBrains Mono',monospace;color:var(--emerald);letter-spacing:2px;margin-bottom:16px;}
.section-title{font-size:clamp(24px,4vw,40px);font-weight:800;letter-spacing:-0.5px;margin-bottom:12px;}
.section-sub{font-size:16px;color:var(--muted);line-height:1.7;max-width:560px;}

/* CARDS */
.card-cyber{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;transition:all .2s;position:relative;overflow:hidden;}
.card-cyber:hover{border-color:var(--border-em);transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,0.3);}
.card-cyber::before{content:'';position:absolute;top:0;left:20%;right:20%;height:1px;background:linear-gradient(90deg,transparent,rgba(16,185,129,0.2),transparent);}
.card-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:14px;}

/* KPI */
.kpi-num{font-family:'JetBrains Mono',monospace;font-size:48px;font-weight:800;color:var(--emerald);line-height:1;}
.kpi-label{font-size:13px;color:var(--muted);margin-top:8px;}

/* FEATURES GRID */
.feature-item{display:flex;gap:14px;align-items:flex-start;}
.feat-icon{width:36px;height:36px;border-radius:8px;background:var(--emerald-dim);border:1px solid var(--border-em);display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--emerald);flex-shrink:0;}

/* CTA FINAL */
.cta-section{background:linear-gradient(135deg,rgba(16,185,129,0.06),rgba(139,92,246,0.04));border:1px solid var(--border-em);border-radius:16px;padding:60px;text-align:center;}

/* FOOTER */
footer{border-top:1px solid var(--border);padding:40px 5%;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;}
.footer-brand{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--emerald);}
.footer-links{display:flex;gap:20px;}
.footer-links a{color:var(--dim);font-size:12px;text-decoration:none;}
.footer-links a:hover{color:var(--muted);}

/* REVEAL */
.reveal{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.reveal.visible{opacity:1;transform:translateY(0);}

@media(max-width:968px){
  .hero-visual{display:none;}
  .hero-content{max-width:100%;}
  nav .nav-links{display:none;}
}
</style>
</head>
<body>

<div class="hero-bg"></div>
<div class="grid-bg"></div>

<!-- NAVBAR -->
<nav id="navbar">
  <div class="nav-inner">
    <a href="/" class="nav-logo">
      <div class="logo-icon">🛡️</div>
      <div>
        <span class="logo-text">SOUSS PHISH GUARD</span>
        <span class="logo-sub">SECURITY OPERATIONS CENTER</span>
      </div>
    </a>
    <div class="nav-links">
      <a href="#features">Fonctionnalités</a>
      <a href="#usecases">Cas d'usage</a>
      <a href="#kpi">Résultats</a>
      <a href="#why">Pourquoi SPG</a>
    </div>
    <div class="nav-cta">
      <a href="{{ route('user.login') }}" class="btn-nav btn-nav-ghost">Connexion</a>
      <a href="{{ route('admin.login') }}" class="btn-nav btn-nav-admin"><i class="bi bi-shield-fill"></i> Admin</a>
      <a href="{{ route('user.register') }}" class="btn-nav btn-nav-solid">Commencer</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">
      <span></span>
      PLATEFORME CYBERSÉCURITÉ ENTREPRISE
    </div>
    <h1>Renforcez la <span class="accent">sécurité humaine</span> de votre organisation</h1>
    <p>Simulez des attaques de phishing, formez vos équipes, mesurez les risques et renforcez la culture cybersécurité de votre entreprise grâce à l'intelligence artificielle.</p>
    <div class="hero-cta">
      <a href="{{ route('user.login') }}" class="btn-hero-primary">
        <i class="bi bi-box-arrow-in-right"></i> Accéder à la plateforme
      </a>
      <a href="#features" class="btn-hero-ghost">
        <i class="bi bi-play-circle"></i> Voir les fonctionnalités
      </a>
    </div>

    <!-- Métriques rapides -->
    <div style="display:flex;gap:32px;margin-top:48px;padding-top:32px;border-top:1px solid var(--border);">
      @foreach([['val'=>'91%','lbl'=>'des attaques via email'],['val'=>'3x','lbl'=>'réduction des clics'],['val'=>'100%','lbl'=>'formations traçables']] as $m)
      <div>
        <div style="font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;color:var(--emerald);">{{ $m['val'] }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $m['lbl'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Dashboard preview -->
  <div class="hero-visual">
    <div class="dashboard-preview">
      <div class="dash-bar">
        <div class="dash-dot" style="background:#ff5f57;"></div>
        <div class="dash-dot" style="background:#febc2e;"></div>
        <div class="dash-dot" style="background:#28c840;"></div>
        <span class="dash-title">SPG — COMMAND CENTER</span>
      </div>
      <div class="dash-body">
        <div class="dash-stat-row">
          <div class="dash-stat">
            <div class="dash-stat-val" style="color:#f43f5e;">12</div>
            <div class="dash-stat-lbl">ALERTES</div>
          </div>
          <div class="dash-stat">
            <div class="dash-stat-val" style="color:#f59e0b;">34%</div>
            <div class="dash-stat-lbl">TAUX CLIC</div>
          </div>
          <div class="dash-stat">
            <div class="dash-stat-val" style="color:#10b981;">87%</div>
            <div class="dash-stat-lbl">VIGILANCE</div>
          </div>
        </div>
        <div class="dash-chart" id="previewChart"></div>
        <div class="dash-table">
          @foreach([
            ['Alice M.','IT','🟡 Suspect','45%'],
            ['Karim B.','Finance','🔴 Phishing','88%'],
            ['Sara T.','RH','🟢 Sûr','12%'],
          ] as $row)
          <div class="dash-row">
            <span>{{ $row[0] }}</span>
            <span style="color:var(--dim);">{{ $row[1] }}</span>
            <span>{{ $row[2] }}</span>
            <span class="badge-sm" style="background:rgba(16,185,129,0.1);color:#10b981;">{{ $row[3] }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CAS D'USAGE -->
<section id="usecases" style="background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
  <div style="text-align:center;margin-bottom:48px;" class="reveal">
    <div class="section-badge">CAS D'USAGE ENTREPRISE</div>
    <div class="section-title">Adapté à chaque département</div>
    <div class="section-sub mx-auto">Souss Phish Guard s'intègre dans tous les processus métier de votre organisation.</div>
  </div>
  <div class="row g-3 reveal">
    @foreach([
      ['icon'=>'👥','color'=>'#10b981','title'=>'Ressources Humaines','desc'=>'Formation obligatoire des nouveaux employés. Traçabilité des formations pour la conformité RH.'],
      ['icon'=>'💰','color'=>'#f59e0b','title'=>'Finance','desc'=>'Prévention des fraudes par virement. Simulation d\'emails de faux prestataires.'],
      ['icon'=>'📊','color'=>'#0ea5e9','title'=>'Direction Générale','desc'=>'Tableaux de bord exécutifs. Métriques de risque humain pour le COMEX.'],
      ['icon'=>'💻','color'=>'#8b5cf6','title'=>'IT / DSI','desc'=>'Campagnes automatisées. Intégration Active Directory. Rapports techniques avancés.'],
      ['icon'=>'⚖️','color'=>'#f43f5e','title'=>'Conformité / Audit','desc'=>'Preuves de sensibilisation ISO 27001. Rapports d\'audit téléchargeables en CSV.'],
    ] as $case)
    <div class="col-md col-sm-6 col-12">
      <div class="card-cyber h-100">
        <div class="card-icon" style="background:{{ $case['color'] }}18;color:{{ $case['color'] }};">{{ $case['icon'] }}</div>
        <div style="font-size:14px;font-weight:700;margin-bottom:8px;">{{ $case['title'] }}</div>
        <div style="font-size:12px;color:var(--muted);line-height:1.7;">{{ $case['desc'] }}</div>
      </div>
    </div>
    @endforeach
  </div>
</section>

<!-- FONCTIONNALITÉS -->
<section id="features">
  <div class="row align-items-center g-5">
    <div class="col-lg-5 reveal">
      <div class="section-badge">FONCTIONNALITÉS</div>
      <div class="section-title">Tout pour gérer le risque humain</div>
      <div class="section-sub">Une plateforme complète pour simuler, former, mesurer et protéger.</div>
    </div>
    <div class="col-lg-7 reveal">
      <div class="row g-3">
        @foreach([
          ['bi-envelope-fill','Simulations phishing internes','Modèles réalistes Microsoft, bancaires, RH, livraison'],
          ['bi-cpu-fill','Analyse IA automatique','Score de risque 0-100 avec Claude AI pour chaque menace'],
          ['bi-graph-up','Tracking comportemental','Suivi clics, ouvertures, soumissions en temps réel'],
          ['bi-file-earmark-bar-graph-fill','Rapports CSV détaillés','Export complet des campagnes pour audit et conformité'],
          ['bi-mortarboard-fill','Formation après incident','Module de formation déclenché automatiquement après un clic'],
          ['bi-building','Multi-département','Gestion par département avec scores de vigilance individuels'],
          ['bi-shield-fill-check','Scoring employés','Score de vigilance personnalisé et certifié pour chaque agent'],
          ['bi-robot','Chatbot sécurité IA','Assistant 24/7 pour répondre aux questions de sécurité'],
        ] as $feat)
        <div class="col-md-6">
          <div class="feature-item">
            <div class="feat-icon"><i class="bi {{ $feat[0] }}"></i></div>
            <div>
              <div style="font-size:13px;font-weight:700;margin-bottom:3px;">{{ $feat[1] }}</div>
              <div style="font-size:12px;color:var(--muted);line-height:1.6;">{{ $feat[2] }}</div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<!-- KPI -->
<section id="kpi" style="background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);text-align:center;">
  <div class="reveal" style="margin-bottom:48px;">
    <div class="section-badge">RÉSULTATS MESURABLES</div>
    <div class="section-title">Des chiffres qui parlent</div>
  </div>
  <div class="row g-4 reveal">
    @foreach([
      ['+1000','Simulations envoyées'],
      ['90%','Amélioration vigilance'],
      ['3x','Moins d\'incidents'],
      ['ISO 27001','Conformité assurée'],
    ] as $kpi)
    <div class="col-md-3 col-6">
      <div class="card-cyber">
        <div class="kpi-num" data-target="{{ preg_replace('/[^0-9]/','',$kpi[0]) }}">{{ $kpi[0] }}</div>
        <div class="kpi-label">{{ $kpi[1] }}</div>
      </div>
    </div>
    @endforeach
  </div>
</section>

<!-- POURQUOI SPG -->
<section id="why">
  <div style="text-align:center;margin-bottom:48px;" class="reveal">
    <div class="section-badge">POURQUOI SPG</div>
    <div class="section-title">La plateforme pensée pour le Maroc</div>
    <div class="section-sub mx-auto">Conçue à Agadir, adaptée aux réalités des entreprises marocaines et internationales.</div>
  </div>
  <div class="row g-3 reveal">
    @foreach([
      ['bi-shield-fill-check','#10b981','Sécurité maximale','Données hébergées localement. Aucune fuite vers des serveurs tiers.'],
      ['bi-lightning-fill','#f59e0b','Déploiement rapide','Opérationnel en moins de 24h. Aucune installation complexe.'],
      ['bi-people-fill','#0ea5e9','PME & Grandes entreprises','Adapté à toutes les tailles d\'organisation, de 10 à 10 000 employés.'],
      ['bi-translate','#8b5cf6','Bilingue FR / EN','Interface complète en français et en anglais.'],
      ['bi-bar-chart-fill','#f43f5e','Tableaux de bord décisionnels','Métriques en temps réel pour les RSSI et dirigeants.'],
      ['bi-cpu-fill','#10b981','IA intégrée','Claude AI pour l\'analyse des menaces et la génération de scénarios.'],
    ] as $why)
    <div class="col-md-4 col-sm-6">
      <div class="card-cyber">
        <div class="card-icon" style="background:{{ $why[1] }}15;color:{{ $why[1] }};">
          <i class="bi {{ $why[0] }}"></i>
        </div>
        <div style="font-size:14px;font-weight:700;margin-bottom:8px;">{{ $why[2] }}</div>
        <div style="font-size:12px;color:var(--muted);line-height:1.7;">{{ $why[3] }}</div>
      </div>
    </div>
    @endforeach
  </div>
</section>

<!-- CTA FINAL -->
<section>
  <div class="cta-section reveal">
    <div class="section-badge" style="margin-bottom:20px;">COMMENCER MAINTENANT</div>
    <div class="section-title">Protégez votre organisation dès aujourd'hui</div>
    <div class="section-sub mx-auto" style="margin:12px auto 32px;">
      Rejoignez les organisations qui font confiance à Souss Phish Guard pour leur cybersécurité interne.
    </div>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
      <a href="{{ route('user.login') }}" class="btn-hero-primary">
        <i class="bi bi-box-arrow-in-right"></i> Accéder à la plateforme
      </a>
      <a href="{{ route('user.register') }}" class="btn-hero-ghost">
        <i class="bi bi-person-plus-fill"></i> Créer un compte entreprise
      </a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div>
    <div class="footer-brand">🛡 SOUSS PHISH GUARD</div>
    <div style="font-size:11px;color:var(--dim);margin-top:4px;">Security Operations Center · Agadir, Maroc</div>
  </div>
  <div class="footer-links">
    <a href="{{ route('user.login') }}">Connexion</a>
    <a href="{{ route('admin.login') }}">Admin</a>
    <a href="{{ route('user.register') }}">Inscription</a>
    <a href="#features">Fonctionnalités</a>
  </div>
  <div style="font-size:11px;color:var(--dim);">© {{ date('Y') }} Souss Phish Guard — ENSA Agadir</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Navbar scroll
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 30);
});

// Reveal on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Dashboard preview bars
const bars = document.getElementById('previewChart');
if(bars) {
    const heights = [30,55,40,70,50,85,60,45,75,55,90,65];
    const colors  = ['#10b981','#10b981','#f59e0b','#10b981','#f43f5e','#10b981','#10b981','#f59e0b','#10b981','#10b981','#10b981','#f43f5e'];
    heights.forEach((h,i) => {
        const bar = document.createElement('div');
        bar.className = 'dash-bar-item';
        bar.style.cssText = `height:${h}%;background:${colors[i]};opacity:0.7;`;
        bars.appendChild(bar);
    });
}

// Counter animation
function animateCounters() {
    document.querySelectorAll('.kpi-num[data-target]').forEach(el => {
        const target = parseInt(el.dataset.target);
        if(!target) return;
        let count = 0;
        const step = Math.ceil(target / 50);
        const timer = setInterval(() => {
            count = Math.min(count + step, target);
            const prefix = el.textContent.includes('+') ? '+' : '';
            const suffix = el.textContent.includes('%') ? '%' : '';
            el.textContent = prefix + count + suffix;
            if(count >= target) clearInterval(timer);
        }, 30);
    });
}

const kpiObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting) { animateCounters(); kpiObserver.disconnect(); } });
});
const kpiSection = document.querySelector('#kpi');
if(kpiSection) kpiObserver.observe(kpiSection);
</script>
</body>
</html>