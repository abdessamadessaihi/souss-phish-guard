<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — Souss Phish Guard</title>
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

    <div class="auth-wrap" style="padding:40px 20px;">
        <div class="auth-card fade-in" style="max-width:500px;">
            <div class="scan-line"></div>

            <div class="auth-logo">
                <div class="auth-logo-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="auth-title">ENREGISTREMENT</div>
                <div class="auth-sub">Rejoignez la force de défense SPG</div>
            </div>

            @if($errors->any())
                <div class="cyber-alert danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('user.register.submit') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="cyber-label">NOM COMPLET</label>
                        <input type="text" name="name" class="cyber-input" placeholder="Votre nom"
                            value="{{ old('name') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">ADRESSE EMAIL</label>
                        <input type="email" name="email" class="cyber-input" placeholder="agent@organisation.ma"
                            value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">DÉPARTEMENT</label>
                        <select name="department" class="cyber-select">
                            <option value="">Sélectionner...</option>
                            <option value="IT">IT / Informatique</option>
                            <option value="RH">Ressources Humaines</option>
                            <option value="Finance">Finance</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Direction">Direction</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">LANGUE</label>
                        <select name="locale" class="cyber-select">
                            <option value="fr">Français</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">MOT DE PASSE</label>
                        <input type="password" name="password" class="cyber-input" placeholder="Min. 8 caractères"
                            required>
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">CONFIRMER LE MOT DE PASSE</label>
                        <input type="password" name="password_confirmation" class="cyber-input"
                            placeholder="Répétez le mot de passe" required>
                    </div>
                </div>

                <div
                    style="margin:20px 0;padding:14px;background:rgba(0,245,255,0.04);border:1px solid var(--border-solid);border-radius:8px;font-size:12px;color:var(--text-muted);">
                    <i class="bi bi-info-circle text-cyan"></i>
                    Votre compte sera activé après validation par un administrateur.
                </div>

                <button type="submit" class="btn-cyber btn-cyber-primary w-100 justify-content-center"
                    style="padding:14px;">
                    <i class="bi bi-shield-plus"></i> CRÉER MON COMPTE
                </button>
            </form>

            <div class="auth-divider">Déjà membre ?</div>
            <a href="{{ route('login') }}" class="btn-cyber btn-cyber-success w-100 justify-content-center"
                style="padding:12px;">
                <i class="bi bi-box-arrow-in-right"></i> SE CONNECTER
            </a>
        </div>
    </div>
</body>

</html>