@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'TABLEAU DE BORD')

@section('content')
    <div class="fade-in">
        {{-- PAGE HEADER --}}
        <div class="page-header d-flex align-items-start justify-content-between mb-4">
            <div>
                <div class="page-breadcrumb" style="font-size:11px;color:var(--text-muted);letter-spacing:2px;margin-bottom:6px;">
                    SPG / <span style="color:var(--neon-cyan);">Dashboard</span>
                </div>
                <div class="page-header-title" style="font-size:22px;font-weight:700;color:#fff;font-family:'Share Tech Mono',monospace;">
                    Bonjour, {{ auth()->user()->name }}
                </div>
                <div class="page-header-sub" style="font-size:13px;color:var(--text-muted);margin-top:4px;">
                    Voici votre état de sécurité du jour
                </div>
            </div>
            <a href="{{ route('user.reports.create') }}" class="btn-cyber btn-cyber-danger" style="white-space:nowrap;">
                <i class="bi bi-flag-fill"></i> Signaler une menace
            </a>
        </div>

        {{-- STAT CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="bi bi-shield-fill-check"></i></div>
                    <div>
                        <div class="stat-value text-cyan">{{ auth()->user()->vigilance_score }}</div>
                        <div class="stat-label">SCORE VIGILANCE</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="bi bi-flag-fill"></i></div>
                    <div>
                        <div class="stat-value text-orange">{{ auth()->user()->reports_count }}</div>
                        <div class="stat-label">SIGNALEMENTS</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-patch-check-fill"></i></div>
                    <div>
                        <div class="stat-value text-green">{{ auth()->user()->simulations_passed }}</div>
                        <div class="stat-label">TESTS RÉUSSIS</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-mortarboard-fill"></i></div>
                    <div>
                        <div class="stat-value text-purple">
                            {{ auth()->user()->trainings()->wherePivot('status', 'completed')->count() }}
                        </div>
                        <div class="stat-label">FORMATIONS</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN ROW: Vigilance Gauge + Recent Reports --}}
        <div class="row g-3 mb-3">

            {{-- VIGILANCE GAUGE --}}
            <div class="col-md-4">
                <div class="cyber-card h-100">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title">
                            <i class="bi bi-speedometer2"></i> NIVEAU VIGILANCE
                        </div>
                    </div>
                    <div class="text-center py-3">
                        @php $score = auth()->user()->vigilance_score; @endphp

                        {{-- Circular SVG gauge --}}
                        <div style="position:relative;width:130px;height:130px;margin:0 auto 16px;">
                            <svg viewBox="0 0 120 120" width="130" height="130" style="transform:rotate(-90deg)">
                                <circle cx="60" cy="60" r="52"
                                    fill="none"
                                    stroke="rgba(0,245,255,0.08)"
                                    stroke-width="10"/>
                                <circle cx="60" cy="60" r="52"
                                    fill="none"
                                    stroke="{{ $score >= 75 ? '#00f5ff' : ($score >= 50 ? '#f5a623' : '#ff4d4d') }}"
                                    stroke-width="10"
                                    stroke-linecap="round"
                                    stroke-dasharray="326"
                                    stroke-dashoffset="{{ 326 - (326 * $score / 100) }}"
                                    style="transition: stroke-dashoffset 1s ease;"/>
                            </svg>
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                                        font-family:'Share Tech Mono',monospace;font-size:26px;
                                        color:{{ $score >= 75 ? 'var(--neon-cyan)' : ($score >= 50 ? 'var(--neon-orange,#f5a623)' : '#ff4d4d') }};">
                                {{ $score }}%
                            </div>
                        </div>

                        <div class="font-mono text-cyan" style="font-size:15px;font-weight:600;letter-spacing:1px;margin-bottom:4px;">
                            {{ auth()->user()->vigilance_level }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);letter-spacing:1px;">
                            {{ auth()->user()->department ?? 'Aucun département' }}
                        </div>

                        {{-- Score bar breakdown (optional visual detail) --}}
                        <div style="margin-top:20px;padding:0 12px;text-align:left;">
                            @foreach([
                                ['label' => 'Signalements', 'val' => min(100, auth()->user()->reports_count * 10), 'color' => 'var(--neon-orange,#f5a623)'],
                                ['label' => 'Formations',   'val' => min(100, auth()->user()->trainings()->wherePivot('status','completed')->count() * 20), 'color' => 'var(--neon-purple,#a855f7)'],
                                ['label' => 'Tests réussis','val' => min(100, auth()->user()->simulations_passed * 25), 'color' => 'var(--neon-green,#22c55e)'],
                            ] as $bar)
                            <div style="margin-bottom:10px;">
                                <div style="display:flex;justify-content:space-between;font-size:10px;
                                            color:var(--text-muted);letter-spacing:1px;margin-bottom:3px;">
                                    <span>{{ $bar['label'] }}</span>
                                    <span style="color:{{ $bar['color'] }};">{{ $bar['val'] }}%</span>
                                </div>
                                <div style="height:4px;background:rgba(255,255,255,0.06);border-radius:2px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $bar['val'] }}%;background:{{ $bar['color'] }};
                                                border-radius:2px;transition:width 1s ease;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- RECENT REPORTS --}}
            <div class="col-md-8">
                <div class="cyber-card h-100">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title">
                            <i class="bi bi-clock-history"></i> MES DERNIERS SIGNALEMENTS
                        </div>
                        <a href="{{ route('user.reports.index') }}"
                           class="btn-cyber btn-cyber-primary"
                           style="padding:5px 12px;font-size:11px;">
                            Voir tout
                        </a>
                    </div>

                    @php
                        $reports = auth()->user()->phishReports()->latest()->limit(5)->get();
                        $badges  = ['critical' => 'badge-critical', 'high' => 'badge-high', 'medium' => 'badge-medium', 'low' => 'badge-low'];
                        $colors  = ['critical' => 'red', 'high' => 'orange', 'medium' => 'cyan', 'low' => 'green'];
                    @endphp

                    <div style="padding:0 4px;">
                        @forelse($reports as $r)
                            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;
                                        border-bottom:1px solid var(--border-solid);">
                                <div class="stat-icon {{ $colors[$r->severity ?? 'low'] ?? 'cyan' }}"
                                     style="width:36px;height:36px;font-size:14px;flex-shrink:0;">
                                    <i class="bi bi-flag-fill"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#e2e8f0;">
                                        {{ Str::limit($r->content, 60) }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                        {{ $r->created_at->diffForHumans() }}
                                        @if($r->severity)
                                            &nbsp;·&nbsp;
                                            <span style="text-transform:uppercase;letter-spacing:1px;">{{ $r->severity }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="cyber-badge {{ $badges[$r->severity ?? 'low'] ?? 'badge-low' }}">
                                    {{ $r->ai_risk_score ?? 0 }}%
                                </span>
                            </div>
                        @empty
                            <div style="text-align:center;padding:48px 20px;color:var(--text-muted);font-size:13px;">
                                <i class="bi bi-shield-check"
                                   style="font-size:40px;display:block;margin-bottom:12px;color:var(--border-glow);"></i>
                                Aucun signalement pour le moment.<br>
                                <a href="{{ route('user.reports.create') }}" style="color:var(--neon-cyan);">
                                    Soumettre un signalement
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="row g-3">
            <div class="col-12">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-lightning-fill"></i> ACTIONS RAPIDES</div>
                    </div>
                    <div class="row g-3">
                        @foreach([
                            ['route' => 'user.analyzer.index',  'icon' => 'bi-radar',          'color' => 'cyan',   'title' => 'Analyser URL / Email', 'sub' => 'Détection IA temps réel'],
                            ['route' => 'user.reports.create',  'icon' => 'bi-flag-fill',       'color' => 'red',    'title' => 'Signaler une menace',   'sub' => 'Soumettre un rapport'],
                            ['route' => 'user.training.index',  'icon' => 'bi-mortarboard-fill','color' => 'purple', 'title' => 'Se former',             'sub' => 'Modules interactifs'],
                            ['route' => 'user.messages.index',  'icon' => 'bi-chat-dots-fill',  'color' => 'green',  'title' => 'Messagerie SOC',        'sub' => 'Contacter l\'équipe'],
                        ] as $item)
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route($item['route']) }}" style="text-decoration:none;">
                                    <div class="stat-card" style="flex-direction:column;text-align:center;padding:28px 16px;
                                                                   cursor:pointer;transition:transform .2s,box-shadow .2s;"
                                         onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,245,255,0.12)'"
                                         onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <i class="bi {{ $item['icon'] }}"
                                           style="font-size:32px;color:var(--neon-{{ $item['color'] }});margin-bottom:12px;display:block;"></i>
                                        <div style="font-size:13px;font-weight:600;color:#e2e8f0;">{{ $item['title'] }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">{{ $item['sub'] }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection