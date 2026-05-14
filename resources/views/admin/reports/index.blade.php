@extends('layouts.app')
@section('title', 'Gestion des Alertes')
@section('page-title', 'GESTION DES ALERTES')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Alertes</span></div>
                <div class="page-header-title">Signalements & Alertes</div>
                <div class="page-header-sub">{{ $reports->total() }} signalement(s) au total</div>
            </div>
        </div>

        <!-- Stats rapides -->
        <div class="row g-3 mb-4">
            @foreach([
                    ['label' => 'EN ATTENTE', 'val' => $stats['pending'], 'color' => 'orange', 'icon' => 'bi-hourglass-split'],
                    ['label' => 'PHISHING CONFIRMÉ', 'val' => $stats['confirmed_phish'], 'color' => 'red', 'icon' => 'bi-exclamation-octagon-fill'],
                    ['label' => 'FAUX POSITIF', 'val' => $stats['false_positive'], 'color' => 'green', 'icon' => 'bi-check-circle-fill'],
                    ['label' => 'BLOQUÉ', 'val' => $stats['blocked'], 'color' => 'purple', 'icon' => 'bi-shield-fill-x'],
                ] as $s)
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon {{ $s['color'] }}"><i class="bi {{ $s['icon'] }}"></i></div>
                        <div>
                            <div class="stat-value text-{{ $s['color'] }}">{{ $s['val'] }}</div>
                            <div class="stat-label">{{ $s['label'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>




                            <div class="cyber-card">




                                <table class="cyber-table">
                <thead>
                    <tr>
                            <th>#</th><th>AGENT</th><th>TYPE</th><th>CONTENU</th>
                            <th>RISQUE IA</th><th>SÉVÉRITÉ</th><th>STATUT</th><th>DATE</th><th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($reports as $r)
                            <tr>
                                <td class="font-mono text-muted-cyber">#{{ $r->id }}</td>
                                <td style="font-size:12px;">{{ $r->user->name ?? 'N/A' }}</td>
                                <td><span class="cyber-badge badge-info">{{ strtoupper($r->type) }}</span></td>
                              <td   style="max-width:180px;
                                    o   verflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;color:var(--text-muted);">
                                    {{ Str::limit($r->content, 45) }}
                            </td    >
                               <td> 
                                    @if($r->ai_risk_score !== null)
                                        <span class="font-mono" style="color:{{ $r->ai_risk_score >= 70 ? 'var(--neon-red)' : ($r->ai_risk_score >= 40 ? 'var(--neon-orange)' : 'var(--neon-green)') }}">
                                            {{ $r->ai_risk_score }}%
                                        </span>
                                       @else <span style="color:var(--text-muted)">-</span> @endif
                               </td >
                                <td>
                                    @php $badges = ['critical' => 'badge-critical', 'high' => 'badge-high', 'medium' => 'badge-medium', 'low' => 'badge-low']; @endphp
                                    <span class="cyber-badge {{ $badges[$r->severity ?? 'low'] ?? 'badge-low' }}">
                                        {{ strtoupper($r->severity ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    @php $st = ['pending' => 'badge-pending', 'confirmed_phish' => 'badge-critical', 'false_positive' => 'badge-low', 'blocked' => 'badge-high', 'analyzing' => 'badge-info']; @endphp
                                <spa    n class="cyber-badge {{ $st[$r->status] ?? 'badge-info' }}">{{ $r->status }}</span>
                            </td    >
                                <td style="font-size:11px;color:var(--text-muted);">{{ $r->created_at->format('d/m H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.reports.show', $r) }}" class="btn-cyber btn-cyber-primary" style="padding:5px 10px;font-size:10px;">

                                        <i class="bi bi-eye"></i> Voir


                                                    </a>
                            </td>
                        </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">Aucun signalement</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $reports->links() }}</div>
        </div>
    </div>
    <!-- Bloc envoi alertes -->
<div class="cyber-card mb-4">
    <div class="cyber-card-header">
        <div class="cyber-card-title"><i class="bi bi-envelope-exclamation-fill"></i> ENVOI D'ALERTES EMAIL</div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.alerts.csv') }}" class="btn-cyber btn-cyber-success" style="padding:6px 14px;font-size:10px;">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>
    <div class="row g-3">
        <!-- Envoyer alerte -->
        <div class="col-md-6">
            <form method="POST" action="{{ route('admin.alerts.send') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-6">
                        <label class="cyber-label">DESTINATAIRE</label>
                        <select name="target" class="cyber-select" onchange="toggleUserSelect(this.value)">
                            <option value="all">Tous les agents</option>
                            <option value="specific">Agent spécifique</option>
                        </select>
                    </div>
                    <div class="col-6" id="userSelectDiv" style="display:none;">
                        <label class="cyber-label">AGENT</label>
                        <select name="user_id" class="cyber-select">
                            @foreach(\App\Models\User::where('role','user')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">SUJET</label>
                        <input type="text" name="subject" class="cyber-input" required placeholder="Alerte phishing détecté">
                    </div>
                    <div class="col-12">
                        <label class="cyber-label">MESSAGE</label>
                        <textarea name="message" class="cyber-textarea" rows="3" required placeholder="Détails de l'alerte..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-cyber btn-cyber-danger w-100 justify-content-center">
                            <i class="bi bi-send-fill"></i> ENVOYER L'ALERTE
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Envoyer CSV par email -->
        <div class="col-md-6">
            <form method="POST" action="{{ route('admin.alerts.sendCsv') }}">
                @csrf
                <label class="cyber-label">ENVOYER LE RAPPORT CSV PAR EMAIL</label>
                <div style="display:flex;gap:8px;margin-top:8px;">
                    <input type="email" name="email" class="cyber-input" placeholder="destinataire@exemple.com" required style="flex:1;">
                    <button type="submit" class="btn-cyber btn-cyber-primary" style="white-space:nowrap;">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Envoyer CSV
                    </button>
                </div>
                <div style="margin-top:12px;font-size:11px;color:var(--text-dim);font-family:'JetBrains Mono',monospace;">
                    Le CSV contiendra tous les signalements filtrés actuels.
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
function toggleUserSelect(val) {
    document.getElementById('userSelectDiv').style.display = val === 'specific' ? 'block' : 'none';
}
</script>
@endsection