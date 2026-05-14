<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian Access — Souss Phish Guard</title>
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
            style="border-color:rgba(168,85,247,0.4);box-shadow:0 30px 80px rgba(0,0,0,0.6), 0 0 40px rgba(168,85,247,0.08);">
            <div class="scan-line"
                style="background:linear-gradient(90deg,transparent,var(--neon-purple),transparent);"></div>

            <div class="auth-logo">
                <div class="auth-logo-icon"
                    style="background:linear-gradient(135deg,rgba(168,85,247,0.2),rgba(255,0,60,0.2));border-color:rgba(168,85,247,0.4);color:var(--neon-purple);box-shadow:0 0 30px rgba(168,85,247,0.2);">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div class="auth-title" style="color:var(--neon-purple);">GUARDIAN ACCESS</div>
                <div class="auth-sub">Souss Phish Guard — Command Center</div>
            </div>

            <!-- Alerte sécurité -->
            <div
                style="margin-bottom:20px;padding:10px 14px;background:rgba(168,85,247,0.06);border:1px solid rgba(168,85,247,0.2);border-radius:8px;font-size:11px;color:var(--text-muted);font-family:'Share Tech Mono',monospace;text-align:center;letter-spacing:1px;">
                <i class="bi bi-lock-fill" style="color:var(--neon-purple);"></i>
                ACCÈS RÉSERVÉ AUX ADMINISTRATEURS · TOUTES LES TENTATIVES SONT LOGGÉES
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

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="cyber-form-group">
                    <label class="cyber-label" style="color:rgba(168,85,247,0.8);">IDENTIFIANT GUARDIAN</label>
                    <div class="position-relative">
                        <i class="bi bi-shield-fill position-absolute"
                            style="left:14px;top:50%;transform:translateY(-50%);color:var(--neon-purple);font-size:14px;z-index:1;"></i>
                        <input type="email" name="email" class="cyber-input"
                            style="padding-left:42px;border-color:rgba(168,85,247,0.3);" placeholder="admin@spg.ma"
                            value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="cyber-form-group">
                    <label class="cyber-label" style="color:rgba(168,85,247,0.8);">CLÉ D'ACCÈS</label>
                    <div class="position-relative">
                        <i class="bi bi-key-fill position-absolute"
                            style="left:14px;top:50%;transform:translateY(-50%);color:var(--neon-purple);font-size:14px;z-index:1;"></i>
                        <input type="password" name="password" id="pwField2" class="cyber-input"
                            style="padding-left:42px;padding-right:42px;border-color:rgba(168,85,247,0.3);"
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePw2()"
                            style="background:none;border:none;position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);cursor:pointer;z-index:1;">
                            <i class="bi bi-eye-fill" id="pwIcon2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-cyber w-100 justify-content-center mt-2"
                    style="padding:14px;background:rgba(168,85,247,0.1);border-color:var(--neon-purple);color:var(--neon-purple);font-family:'Share Tech Mono',monospace;letter-spacing:1.5px;">
                    <i class="bi bi-shield-lock-fill"></i> ACCÉDER AU COMMAND CENTER
                </button>
            </form>

            <div style="margin-top:20px;text-align:center;">
                <a href="{{ route('user.login') }}"
                    style="font-size:12px;color:var(--text-muted);text-decoration:none;font-family:'Share Tech Mono',monospace;letter-spacing:1px;">
                    <i class="bi bi-arrow-left"></i> Retour portail agent
                </a>
            </div>

            <!-- Compte test -->
            <div
                style="margin-top:16px;padding:12px;background:rgba(168,85,247,0.04);border:1px solid rgba(168,85,247,0.15);border-radius:8px;font-size:11px;color:var(--text-muted);font-family:'Share Tech Mono',monospace;">
                <div style="color:var(--neon-purple);margin-bottom:4px;">⚡ COMPTE TEST GUARDIAN</div>
                admin@spg.ma / Admin@SPG2024!
            </div>
        </div>
    </div>

    <script>
        function togglePw2() {
            const f = document.getElementById('pwField2');
            const i = document.getElementById('pwIcon2');
            f.type = f.type === 'password' ? 'text' : 'password';
            i.className = f.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
        }
    </script>
</body>

</html>