@extends('layouts.app')
@section('title', 'Simulation')
@section('page-title', 'DÉTAIL SIMULATION')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <div class="page-breadcrumb">SPG / Admin / <span>Simulations</span> / #{{ $simulation->id }}</div>
            <div class="page-header-title">{{ $simulation->name }}</div>
        </div>
        <div style="display:flex;gap:10px;">
            @if($simulation->status !== 'completed')
            <form method="POST" action="{{ route('admin.simulations.launch', $simulation) }}" onsubmit="return confirm('Lancer la campagne ? Les emails seront envoyés immédiatement.')">
                @csrf
                <button class="btn-cyber btn-cyber-danger" style="padding:10px 20px;">
                    <i class="bi bi-send-fill"></i> LANCER LA CAMPAGNE
                </button>
            </form>
            @endif
            <a href="{{ route('admin.simulations.index') }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        @foreach([
            ['label'=>'CIBLES','val'=>$simulation->targets_count,'color'=>'cyan','icon'=>'bi-people-fill'],
            ['label'=>'EMAILS OUVERTS','val'=>$stats['opened'],'color'=>'orange','icon'=>'bi-envelope-open-fill'],
            ['label'=>'LIENS CLIQUÉS','val'=>$stats['clicked'],'color'=>'red','icon'=>'bi-mouse2-fill'],
            ['label'=>'DONNÉES SOUMISES','val'=>$stats['submitted'],'color'=>'red','icon'=>'bi-exclamation-octagon-fill'],
            ['label'=>'ONT SIGNALÉ','val'=>$stats['reported'],'color'=>'green','icon'=>'bi-flag-fill'],
            ['label'=>'RÉSISTANTS','val'=>$stats['safe'],'color'=>'green','icon'=>'bi-shield-fill-check'],
        ] as $s)
        <div class="col-md-2 col-4">
            <div class="stat-card" style="flex-direction:column;text-align:center;padding:16px;">
                <i class="bi {{ $s['icon'] }}" style="font-size:24px;color:var(--neon-{{ $s['color'] }});margin-bottom:8px;"></i>
                <div style="font-size:22px;font-weight:700;color:var(--neon-{{ $s['color'] }});">{{ $s['val'] }}</div>
                <div class="stat-label" style="font-size:9px;">{{ $s['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-3">
        <!-- Infos simulation -->
        <div class="col-md-4">
            <div class="cyber-card">
                <div class="cyber-card-title mb-3"><i class="bi bi-info-circle"></i> INFORMATIONS</div>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Template</span><span class="cyber-badge badge-info">{{ strtoupper($simulation->template) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Statut</span>
                        @php $st=['draft'=>'badge-pending','running'=>'badge-high','completed'=>'badge-low']; @endphp
                        <span class="cyber-badge {{ $st[$simulation->status]??'badge-info' }}">{{ strtoupper($simulation->status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Expéditeur</span><span style="font-size:11px;">{{ $simulation->from_name }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Email</span><span style="font-size:11px;">{{ $simulation->from_email }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Créé par</span><span>{{ $simulation->creator->name }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted-cyber">Date</span><span style="font-size:11px;">{{ $simulation->created_at->format('d/m/Y H:i') }}</span></div>
                </div>

                @if($simulation->targets_count > 0)
                <div style="margin-top:20px;">
                    <div style="font-size:10px;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;font-family:'Share Tech Mono',monospace;">TAUX DE CLIC</div>
                    @php $clickRate = $simulation->targets_count > 0 ? round(($simulation->clicked_count / $simulation->targets_count) * 100) : 0; @endphp
                    <div class="cyber-progress" style="height:10px;">
                        <div class="cyber-progress-fill {{ $clickRate >= 50 ? 'progress-red' : ($clickRate >= 25 ? 'progress-orange' : 'progress-green') }}"
                             style="width:{{ $clickRate }}%"></div>
                    </div>
                    <div style="text-align:right;font-size:12px;margin-top:4px;color:{{ $clickRate >= 50 ? 'var(--neon-red)' : ($clickRate >= 25 ? 'var(--neon-orange)' : 'var(--neon-green)') }};">
                        {{ $clickRate }}% ont cliqué
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Résultats par cible -->
        <div class="col-md-8">
            <div class="cyber-card">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-table"></i> RÉSULTATS PAR AGENT</div>
                </div>
                <table class="cyber-table">
                    <thead>
                        <tr><th>AGENT</th><th>OUVERT</th><th>CLIQUÉ</th><th>SOUMIS</th><th>SIGNALÉ</th><th>RÉSULTAT</th></tr>
                    </thead>
                    <tbody>
                    @forelse($results as $r)
                    <tr>
                        <td>
                            <div style="font-size:13px;">{{ $r->user->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $r->user->department ?? 'N/A' }}</div>
                        </td>
                        <td>
                            @if($r->email_opened)
                                <i class="bi bi-check-circle-fill text-orange"></i>
                                <div style="font-size:10px;color:var(--text-muted);">{{ $r->opened_at?->format('H:i') }}</div>
                            @else <i class="bi bi-dash-circle text-muted-cyber"></i> @endif
                        </td>
                        <td>
                            @if($r->link_clicked)
                                <i class="bi bi-x-circle-fill text-red"></i>
                                <div style="font-size:10px;color:var(--text-muted);">{{ $r->clicked_at?->format('H:i') }}</div>
                            @else <i class="bi bi-shield-check text-green"></i> @endif
                        </td>
                        <td>
                            @if($r->data_submitted)
                                <i class="bi bi-exclamation-triangle-fill text-red"></i>
                            @else <i class="bi bi-dash text-muted-cyber"></i> @endif
                        </td>
                        <td>
                            @if($r->reported_phish)
                                <i class="bi bi-flag-fill text-green"></i>
                            @else <i class="bi bi-dash text-muted-cyber"></i> @endif
                        </td>
                        <td>
                            @php
                                $outcomes = [
                                    'safe'      => ['badge-low',  '✅ Résistant'],
                                    'clicked'   => ['badge-high', '⚠️ A cliqué'],
                                    'submitted' => ['badge-critical','🚨 A soumis'],
                                    'reported'  => ['badge-low',  '🚩 A signalé'],
                                ];
                                [$cls,$lbl] = $outcomes[$r->outcome] ?? ['badge-pending','En attente'];
                            @endphp
                            <span class="cyber-badge {{ $cls }}">{{ $lbl }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:30px;">Aucun résultat</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection