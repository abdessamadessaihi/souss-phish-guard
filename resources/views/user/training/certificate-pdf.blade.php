<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #020812;
            color: #e2f0ff;
            margin: 0;
            padding: 40px;
        }

        .cert {
            border: 3px solid #00f5ff;
            border-radius: 16px;
            padding: 60px;
            text-align: center;
            background: #060d1a;
        }

        .logo {
            font-size: 48px;
            color: #00f5ff;
            margin-bottom: 16px;
        }

        .title {
            font-size: 28px;
            color: #00f5ff;
            letter-spacing: 4px;
            margin-bottom: 8px;
        }

        .sub {
            font-size: 14px;
            color: #4a7a9b;
            letter-spacing: 2px;
            margin-bottom: 40px;
        }

        .certify {
            font-size: 16px;
            color: #4a7a9b;
            margin-bottom: 20px;
        }

        .name {
            font-size: 36px;
            color: #e2f0ff;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .training-name {
            font-size: 20px;
            color: #00f5ff;
            margin: 20px 0;
            padding: 16px;
            border: 1px solid rgba(0, 245, 255, 0.3);
            border-radius: 8px;
        }

        .score {
            font-size: 48px;
            color: #00ff88;
            margin: 20px 0;
        }

        .date {
            font-size: 13px;
            color: #4a7a9b;
            margin-top: 30px;
        }

        .divider {
            border: none;
            border-top: 1px solid rgba(0, 245, 255, 0.2);
            margin: 30px 0;
        }
    </style>
</head>

<body>
    <div class="cert">
        <div class="logo">🛡️</div>
        <div class="title">SOUSS PHISH GUARD</div>
        <div class="sub">CERTIFICAT DE VIGILANCE</div>
        <hr class="divider">
        <div class="certify">Ce certificat est décerné à</div>
        <div class="name">{{ $user->name }}</div>
        <div class="certify">pour avoir complété avec succès</div>
        <div class="training-name">{{ $training->title }}</div>
        <div class="score">{{ $score }}%</div>
        <div class="certify">Score obtenu</div>
        <hr class="divider">
        <div class="date">
            Délivré le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}<br>
            Département : {{ $user->department ?? 'N/A' }}<br>
            Souss Phish Guard — Security Operations Center
        </div>
    </div>
</body>

</html>