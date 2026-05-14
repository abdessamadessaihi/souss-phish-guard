@extends('layouts.app')
@section('title', 'Profil Agent')
@section('page-title', 'PROFIL AGENT')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Utilisateurs</span> / {{ $user->name }}</div>
                <div class="page-header-title">{{ $user->name }}</div>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-cyber btn-cyber-warning"><i
                        class="bi bi-pencil"></i> Modifier</a>
                <a href="{{ route('admin.users.index') }}" class="btn-cyber btn-cyber-primary"><i
                        class="bi bi-arrow-left"></i> Retour</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="cyber-card text-center mb-3">
                    <div class="user-avatar" style="width:70px;height:70px;font-size:24px;margin:0 auto 16px;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div style="font-size:18px;font-weight:600;">{{ $user->name }}</div>
                    <div style="font-size:13px;color:var(--text-muted);margin:4px 0;">{{ $user->email }}</div>
                    <span class="cyber-badge {{ $user->is_active ? 'badge-low' : 'badge-critical' }} mt-2">
                        {{ $user->is_active ? 'ACTIF' : 'INACTIF' }}
                    </span>
                    <div style="margin-top:20px;">
                        <div style="font-size:48px;font-family:'Share Tech Mono',monospace;color:var(--neon-cyan);">
                            {{ $user->vigilance_score }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">SCORE VIGILANCE</div>
                        <div class="cyber-progress mt-2">
                            <div class="cyber-progress-fill {{ $user->vigilance_score >= 70 ? 'progress-green' : 'progress-orange' }}"
                                style="width:{{ $user->vigilance_score }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="cyber-card">
                    <div class="cyber-card-title mb-3"><i class="bi bi-info-circle"></i> INFORMATIONS</div>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div class="d-flex justify-content-between"><span
                                class="text-muted-cyber">Département</span><span>{{ $user->department ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">Rôle</span><span
                                class="cyber-badge badge-info">{{ strtoupper($user->role) }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">Signalements</span><span
                                class="font-mono">{{ $user->reports_count }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">Tests réussis</span><span
                                class="font-mono text-green">{{ $user->simulations_passed }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">Tests échoués</span><span
                                class="font-mono text-red">{{ $user->simulations_failed }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">Dernière co.</span><span
                                style="font-size:11px;">{{ $user->last_login_at?->diffForHumans() ?? 'Jamais' }}</span>
                        </div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">IP dernière
                                co.</span><span class="font-mono"
                                style="font-size:11px;">{{ $user->last_login_ip ?? 'N/A' }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted-cyber">Inscrit le</span><span
                                style="font-size:11px;">{{ $user->created_at->format('d/m/Y') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Derniers signalements -->
                <div class="cyber-card mb-3">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-flag-fill"></i> DERNIERS SIGNALEMENTS</div>
                    </div>
                    @forelse($user->phishReports()->latest()->limit(5)->get() as $r)
                        <div
                            style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-solid);">
                            <span
                                class="cyber-badge {{ ['critical' => 'badge-critical', 'high' => 'badge-high', 'medium' => 'badge-medium', 'low' => 'badge-low'][$r->severity ?? 'low'] ?? 'badge-low' }}">{{ strtoupper($r->type) }}</span>
                            <div
                                style="flex:1;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted);">
                                {{ Str::limit($r->content, 60) }}</div>
                            <span style="font-size:11px;color:var(--text-muted);">{{ $r->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">Aucun signalement
                        </div>
                    @endforelse
                </div>

                <!-- Logs activité -->
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-clock-history"></i> LOGS D'ACTIVITÉ</div>
                    </div>
                    @forelse($logs as $log)
                        <div
                            style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border-solid);font-size:12px;">
                            <i class="bi bi-circle-fill" style="font-size:6px;color:var(--neon-cyan);flex-shrink:0;"></i>
                            <div style="flex:1;color:var(--text-muted);">{{ $log->description }}</div>
                            <div style="color:var(--text-muted);flex-shrink:0;">{{ $log->created_at->format('d/m H:i') }}</div>
                            <div class="font-mono" style="font-size:10px;color:var(--text-muted);flex-shrink:0;">
                                {{ $log->ip_address }}</div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">Aucun log</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection