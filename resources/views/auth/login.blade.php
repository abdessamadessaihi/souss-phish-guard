<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Agent — Souss Phish Guard</title>
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
        <div class="auth-card fade-in">
            <div class="scan-line"></div>

            <div class="auth-logo">
                <div class="auth-logo-icon"><i class="bi bi-person-fill-lock"></i></div>
                <div class="auth-title">ESPACE AGENT</div>
                <div class="auth-sub">Souss Phish Guard — Portail Utilisateur</div>
            </div>

            @if(session('error'))
                <div class="cyber-alert danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="cyber-alert danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('user.login.submit') }}">
                @csrf
                <div class="cyber-form-group">
                    <label class="cyber-label">ADRESSE EMAIL</label>
                    <div class="position-relative">
                        <i class="bi bi-envelope-fill position-absolute"
                            style="left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;z-index:1;"></i>
                        <input type="email" name="email" class="cyber-input" style="padding-left:42px"
                            placeholder="agent@organisation.ma" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="cyber-form-group">
                    <label class="cyber-label">MOT DE PASSE</label>
                    <div class="position-relative">
                        <i class="bi bi-lock-fill position-absolute"
                            style="left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;z-index:1;"></i>
                        <input type="password" name="password" id="pwField" class="cyber-input"
                            style="padding-left:42px;padding-right:42px" placeholder="••••••••" required>
                        <button type="button" onclick="togglePw()"
                            style="background:none;border:none;position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);cursor:pointer;z-index:1;">
                            <i class="bi bi-eye-fill" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <label
                        style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text-muted);">
                        <input type="checkbox" name="remember" style="accent-color:var(--neon-cyan);"> Se souvenir
                    </label>
                </div>

                <button type="submit" class="btn-cyber btn-cyber-primary w-100 justify-content-center"
                    style="padding:14px;">
                    <i class="bi bi-box-arrow-in-right"></i> CONNEXION AGENT
                </button>
            </form>

            <div class="auth-divider">Nouveau sur la plateforme ?</div>
            <a href="{{ route('register') }}" class="btn-cyber btn-cyber-success w-100 justify-content-center"
                style="padding:12px;">
                <i class="bi bi-person-plus-fill"></i> CRÉER UN COMPTE
            </a>

            <!-- Lien vers admin -->
            <div style="margin-top:20px;text-align:center;">
                <a href="{{ route('admin.login') }}"
                    style="font-size:12px;color:var(--text-muted);text-decoration:none;font-family:'Share Tech Mono',monospace;letter-spacing:1px;">
                    <i class="bi bi-shield-fill"></i> Accès Guardian (Admin)
                </a>
            </div>

            <!-- Comptes test -->
            <div
                style="margin-top:16px;padding:12px;background:rgba(0,245,255,0.04);border:1px solid var(--border-solid);border-radius:8px;font-size:11px;color:var(--text-muted);font-family:'Share Tech Mono',monospace;">
                <div style="color:var(--neon-cyan);margin-bottom:4px;">⚡ COMPTE TEST</div>
                user1@spg.ma / User@SPG2024!
            </div>
        </div>
    </div>

    <script>
        function togglePw() {
            const f = document.getElementById('pwField');
            const i = document.getElementById('pwIcon');
            f.type = f.type === 'password' ? 'text' : 'password';
            i.className = f.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
        }
    </script>
</body>

</html>