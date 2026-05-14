<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ Test de Sécurité — SPG</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Exo+2:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/cyber-style.css') }}" rel="stylesheet">
</head>

<body>
    <div class="cyber-bg">
        <div class="cyber-grid"></div>
    </div>

    <div class="auth-wrap">
        <div class="auth-card fade-in"
            style="max-width:520px;border-color:rgba(255,107,0,0.4);box-shadow:0 30px 80px rgba(0,0,0,0.6),0 0 40px rgba(255,107,0,0.08);">
            <div class="scan-line"
                style="background:linear-gradient(90deg,transparent,var(--neon-orange),transparent);"></div>

            <div style="text-align:center;margin-bottom:28px;">
                <div
                    style="width:80px;height:80px;background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 16px;box-shadow:0 0 30px rgba(255,107,0,0.2);">
                    ⚠️
                </div>
                <div
                    style="font-family:'Share Tech Mono',monospace;font-size:20px;color:var(--neon-orange);letter-spacing:2px;margin-bottom:6px;">
                    TEST DE VIGILANCE
                </div>
                <div style="font-size:13px;color:var(--text-muted);">Souss Phish Guard — Security Operations Center
                </div>
            </div>

            <div
                style="padding:20px;background:rgba(255,107,0,0.06);border:1px solid rgba(255,107,0,0.2);border-radius:12px;margin-bottom:20px;text-align:center;">
                <div style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:10px;">
                    Vous venez de cliquer sur un <span style="color:var(--neon-red);">lien de phishing simulé</span>.
                </div>
                <div style="font-size:13px;color:var(--text-muted);line-height:1.8;">
                    Ceci était un <strong style="color:var(--neon-orange);">test de sécurité organisé</strong> par votre
                    équipe SOC.<br>
                    <strong style="color:var(--neon-cyan);">Aucune donnée n'a été compromise. Vous êtes en
                        sécurité.</strong>
                </div>
            </div>

            <!-- Ce qu'il fallait faire -->
            <div style="padding:16px;background:rgba(0,10,25,0.6);border-radius:10px;margin-bottom:20px;">
                <div
                    style="font-family:'Share Tech Mono',monospace;font-size:11px;color:var(--neon-cyan);letter-spacing:2px;margin-bottom:12px;">
                    ✅ CE QU'IL FALLAIT FAIRE
                </div>
                @foreach([
                                        "Vérifier l'adresse email de l'expéditeur",
                                        "Analyser le lien via l'Analyseur IA SPG",
                                        "Signaler l'email via la plateforme SPG",
                                        "Ne jamais cliquer sur des liens urgents",
                                    ] as $tip)
                                    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;font-size:13px;color:var(--text-muted);border-bottom:1px solid var(--border-solid);">
                                    <i class="bi bi-check-circle-fill" style="color:var(--neon-green);flex-shrink:0;"></i>
                                        {{ $tip }}

                       <            /div>
                @endforeach
            </div>
 
           @if(isset($result) && $result)
                <div style="padding:12px;background:rgba(0,245,255,0.04);border-radius:8px;margin-bottom:16px;font-size:12px;color:var(--text-muted);font-family:'Share Tech Mono',monospace;">
                    Simulation #{{ $result->simulation_id }} · Agent : {{ $result->user->name ?? 'N/A' }}
               </div>
        @endif
   
               <div style="display:flex;gap:10px;">
            <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? '/admin/dashboard' : '/user/dashboard') : '/user/login' }}"
                   class="btn-cyber btn-cyber-primary w-100 justify-content-center" style="padding:14px;">
                    <i class="bi bi-house-fill"></i> Retour au Dashboard
       
                        </a>    
           </di v>
   
             <div style="margin-top:16px;text-align:center;">
                <a href="{{ auth()->check() ? '/user/training' : '/user/login' }}" style="font-size:12px;color:var(--neon-cyan);text-decoration:none;">
       
         <i class="bi bi-mortarboard-fill"></i> Accéder au Centre de Formation
            </a>
        </div>
    </div>
</div>
</body>
</html>