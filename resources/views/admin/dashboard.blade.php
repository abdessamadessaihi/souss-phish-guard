@extends('layouts.app')
@section('title', 'Command Center')
@section('page-title', 'COMMAND CENTER')
@php
    use App\Models\PhishReport;
@endphp
@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Admin</span></div>
                <div class="page-header-title">Command Center</div>
                <div class="page-header-sub">Vue globale — {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <a href="{{ route('admin.simulations.create') }}" class="btn-cyber btn-cyber-danger">
                <i class="bi bi-envelope-fill"></i> Nouvelle simulation
            </a>
        </div>

        <!-- KPIs principaux -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="stat-value text-red">{{ $stats['pending_reports'] }}</div>
                        <div class="stat-label">ALERTES EN ATTENTE</div>
                        <div class="stat-trend" style="color:var(--text-secondary);">{{ $stats['confirmed_phish'] }} confirmés</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-mouse2-fill"></i></div>
                    <div>
                        <div class="stat-value text-amber">{{ $stats['click_rate'] }}%</div>
                        <div class="stat-label">TAUX DE CLIC</div>
                        <div class="stat-trend down">{{ $stats['total_clicks'] }} clics total</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-flag-fill"></i></div>
                    <div>
                        <div class="stat-value text-green">{{ $stats['report_rate'] }}%</div>
                        <div class="stat-label">TAUX SIGNALEMENT</div>
                        <div class="stat-trend up">{{ $stats['total_reported'] }} signalés</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-person-exclamation-fill"></i></div>
                    <div>
                        <div class="stat-value text-red">{{ $stats['high_risk_users'] }}</div>
                        <div class="stat-label">AGENTS À RISQUE</div>
                        <div class="stat-trend" style="color:var(--text-secondary);">Score &lt; 40%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs secondaires -->
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-4">
                <div class="cyber-card text-center" style="padding:16px;">
                    <div style="font-size:22px;font-weight:700;color:var(--sky);font-family:'JetBrains Mono',monospace;">{{ $stats['total_users'] }}</div>
                    <div style="font-size:10px;color:var(--text-muted);letter-spacing:1px;margin-top:3px;">AGENTS</div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="cyber-card text-center" style="padding:16px;">
                    <div style="font-size:22px;font-weight:700;color:var(--emerald);font-family:'JetBrains Mono',monospace;">{{ $stats['active_users'] }}</div>
                    <div style="font-size:10px;color:var(--text-muted);letter-spacing:1px;margin-top:3px;">ACTIFS</div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="cyber-card text-center" style="padding:16px;">
                    <div style="font-size:22px;font-weight:700;color:var(--amber);font-family:'JetBrains Mono',monospace;">{{ $stats['total_simulations'] }}</div>
                    <div style="font-size:10px;color:var(--text-muted);letter-spacing:1px;margin-top:3px;">SIMULATIONS</div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="cyber-card text-center" style="padding:16px;">
                    <div style="font-size:22px;font-weight:700;color:var(--rose);font-family:'JetBrains Mono',monospace;">{{ $stats['running_simulations'] }}</div>
                    <div style="font-size:10px;color:var(--text-muted);letter-spacing:1px;margin-top:3px;">EN COURS</div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="cyber-card text-center" style="padding:16px;">
                    <div style="font-size:22px;font-weight:700;color:var(--emerald);font-family:'JetBrains Mono',monospace;">{{ $stats['avg_vigilance'] }}%</div>
                    <div style="font-size:10px;color:var(--text-muted);letter-spacing:1px;margin-top:3px;">VIGILANCE MOY.</div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="cyber-card text-center" style="padding:16px;">
                    <div style="font-size:22px;font-weight:700;color:var(--violet);font-family:'JetBrains Mono',monospace;">{{ PhishReport::count() }}</div>
                    <div style="font-size:10px;color:var(--text-muted);letter-spacing:1px;margin-top:3px;">SIGNALEMENTS</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Graphique activité -->
            <div class="col-md-8">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-graph-up"></i> ACTIVITÉ 7 JOURS</div>
                    </div>
                    <canvas id="activityChart" height="120"></canvas>
                </div>
            </div>

            <!-- Stats par département -->
            <div class="col-md-4">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-building"></i> PAR DÉPARTEMENT</div>
                    </div>
                    @forelse($deptStats as $dept)
                        <div style="margin-bottom:12px;">
                            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                                <span style="color:var(--text-primary);">{{ $dept->department ?? 'N/A' }}</span>
                                <span style="color:var(--emerald);font-family:'JetBrains Mono',monospace;">{{ round($dept->avg_score) }}%</span>
                            </div>
                            <div class="cyber-progress">
                                <div class="cyber-progress-fill {{ $dept->avg_score >= 70 ? 'progress-green' : ($dept->avg_score >= 40 ? 'progress-orange' : 'progress-red') }}"
                                     style="width:{{ round($dept->avg_score) }}%"></div>
                            </div>
                            <div style="font-size:10px;color:var(--text-muted);margin-top:2px;">{{ $dept->total }} agent(s)</div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">Aucune donnée</div>
                    @endforelse
                </div>
            </div>

            <!-- Dernières alertes -->
            <div class="col-md-6">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-exclamation-triangle-fill"></i> DERNIÈRES ALERTES</div>
                        <a href="{{ route('admin.reports.index') }}" class="btn-cyber btn-cyber-primary" style="padding:5px 12px;font-size:10px;">Voir tout</a>
                    </div>
                    @forelse($recentReports as $r)
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border-subtle);">
                            <div class="stat-icon {{ $r->severity === 'critical' ? 'red' : ($r->severity === 'high' ? 'amber' : 'green') }}" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">
                                <i class="bi bi-flag-fill"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit($r->content, 45) }}</div>
                                <div style="font-size:10px;color:var(--text-muted);">{{ $r->user->name ?? 'N/A' }} — {{ $r->created_at->diffForHumans() }}</div>
                            </div>
                            @php $bm = ['critical' => 'badge-critical', 'high' => 'badge-high', 'medium' => 'badge-medium', 'low' => 'badge-low']; @endphp
                            <span class="cyber-badge {{ $bm[$r->severity ?? 'low'] ?? 'badge-low' }}">{{ $r->ai_risk_score ?? 0 }}%</span>
                        </div>
                    @empty
                        <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px;">Aucune alerte</div>
                    @endforelse
                </div>
            </div>

            <!-- Agents récents + actions rapides -->
            <div class="col-md-6">
                <div class="cyber-card mb-3">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-people-fill"></i> AGENTS À SURVEILLER</div>
                        <a href="{{ route('admin.users.index') }}" class="btn-cyber btn-cyber-primary" style="padding:5px 12px;font-size:10px;">Gérer</a>
                    </div>
                    @foreach(App\Models\User::where('role', 'user')->orderBy('vigilance_score', 'asc')->limit(4)->get() as $u)
                        <div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border-subtle);">
                            <div class="user-avatar" style="width:30px;height:30px;font-size:10px;flex-shrink:0;">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                            <div style="flex:1;">
                                <div style="font-size:12px;">{{ $u->name }}</div>
                                <div style="font-size:10px;color:var(--text-muted);">{{ $u->department ?? 'N/A' }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px;font-family:'JetBrains Mono',monospace;color:{{ $u->vigilance_score < 40 ? 'var(--rose)' : ($u->vigilance_score < 70 ? 'var(--amber)' : 'var(--emerald)') }};">{{ $u->vigilance_score }}%</div>
                                <div class="cyber-progress" style="width:60px;">
                                    <div class="cyber-progress-fill {{ $u->vigilance_score < 40 ? 'progress-red' : ($u->vigilance_score < 70 ? 'progress-orange' : 'progress-green') }}" style="width:{{ $u->vigilance_score }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Accès rapides -->
                <div class="cyber-card">
                    <div class="cyber-card-title mb-3"><i class="bi bi-lightning-fill"></i> ACCÈS RAPIDES</div>
                    <div class="row g-2">
                        @foreach([
                                ['route' => 'admin.reports.index', 'icon' => 'bi-exclamation-triangle-fill', 'color' => 'red', 'label' => 'Alertes'],
                                ['route' => 'admin.simulations.create', 'icon' => 'bi-envelope-fill', 'color' => 'amber', 'label' => 'Simulation'],
                                ['route' => 'admin.users.index', 'icon' => 'bi-people-fill', 'color' => 'blue', 'label' => 'Agents'],
                                ['route' => 'admin.forensic.index', 'icon' => 'bi-cpu-fill', 'color' => 'purple', 'label' => 'Forensic'],
                            ] as $item)
                            <div class="col-6">
                                <a href="{{ route($item['route']) }}" style="text-decoration:none;">
                                    <div style="padding:14px;background:var(--bg-elevated);border-radius:var(--radius);border:1px solid var(--border-subtle);text-align:center;transition:all .15s;cursor:pointer;"
                                         onmouseover="this.style.borderColor='var(--border-muted)'"
                                         onmouseout="this.style.borderColor='var(--border-subtle)'">
                                        <i class="bi {{ $item['icon'] }}" style="font-size:20px;color:var(--{{ $item['color'] === 'blue' ? 'sky' : ($item['color'] === 'red' ? 'rose' : ($item['color'] === 'amber' ? 'amber' : 'violet')) }});display:block;margin-bottom:6px;"></i>
                                        <div style="font-size:11px;color:var(--text-secondary);font-weight:600;">{{ $item['label'] }}</div>
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

@section('scripts')
    <script>
    const labels = @json(array_column($weeklyReports, 'date'));
    const reports = @json(array_column($weeklyReports, 'reports'));
    const phish   = @json(array_column($weeklyReports, 'phish'));

    new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label:'Signalements', data:reports, borderColor:'var(--emerald)', backgroundColor:'rgba(16,185,129,0.06)', tension:.4, fill:true, pointBackgroundColor:'var(--emerald)' },
                { label:'Phishing confirmé', data:phish, borderColor:'var(--rose)', backgroundColor:'rgba(244,63,94,0.05)', tension:.4, fill:true, pointBackgroundColor:'var(--rose)' },
            ]
        },
        options: {
            plugins: { legend: { labels: { color:'var(--text-secondary)', font:{ size:11 } } } },
            scales: {
                x: { ticks:{color:'var(--text-muted)'}, grid:{color:'rgba(255,255,255,.04)'} },
                y: { ticks:{color:'var(--text-muted)'}, grid:{color:'rgba(255,255,255,.04)'}, beginAtZero:true },
            }
        }
    });
    </script>
@endsection