@extends('layouts.app')
@section('title', 'Centre de Formation')
@section('page-title', 'CENTRE DE FORMATION')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Formation</span></div>
                <div class="page-header-title">Centre de Formation</div>
                <div class="page-header-sub">Renforcez vos compétences en cybersécurité</div>
            </div>
            <div class="cyber-badge badge-info" style="padding:10px 16px;font-size:13px;align-self:center;">
                <i class="bi bi-shield-fill-check"></i>
                Score actuel : {{ auth()->user()->vigilance_score }}/100
            </div>
        </div>

        <!-- Stats progression -->
        @php
            $total = $trainings->count();
            $completed = auth()->user()->trainings()->wherePivot('status', 'completed')->count();
            $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp
        <div class="cyber-card mb-4">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <div style="text-align:center;">
                        <div
                            style="font-size:32px;font-weight:700;color:var(--emerald);font-family:'JetBrains Mono',monospace;">
                            {{ $completed }}/{{ $total }}</div>
                        <div style="font-size:11px;color:var(--text-secondary);letter-spacing:1px;">MODULES COMPLÉTÉS</div>
                    </div>
                    <div style="min-width:200px;">
                        <div
                            style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-secondary);margin-bottom:6px;">
                            <span>Progression globale</span><span>{{ $progress }}%</span>
                        </div>
                        <div class="cyber-progress" style="height:8px;">
                            <div class="cyber-progress-fill progress-green" style="width:{{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
                @if($progress === 100)
                    <div
                        style="padding:12px 20px;background:rgba(16,185,129,0.1);border:1px solid var(--border-muted);border-radius:var(--radius);text-align:center;">
                        <div style="font-size:24px;">🏆</div>
                        <div style="font-size:12px;color:var(--emerald);font-weight:600;margin-top:4px;">Formation complète !
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Grille formations -->
        <div class="row g-3">
            @forelse($trainings as $t)
                @php
                    $pivot = auth()->user()->trainings()->where('training_id', $t->id)->first();
                    $status = $pivot?->pivot?->status ?? 'not_started';
                    $score = $pivot?->pivot?->score ?? 0;
                    $attempts = $pivot?->pivot?->attempts ?? 0;
                    $typeIcons = ['video' => 'bi-play-circle-fill', 'quiz' => 'bi-patch-question-fill', 'article' => 'bi-file-text-fill', 'simulation' => 'bi-bug-fill'];
                    $typeColors = ['video' => 'green', 'quiz' => 'purple', 'article' => 'blue', 'simulation' => 'red'];
                    $diffColors = ['beginner' => 'badge-low', 'intermediate' => 'badge-medium', 'advanced' => 'badge-high'];
                @endphp
                <div class="col-md-4 col-sm-6">
                    <div class="cyber-card h-100" style="display:flex;flex-direction:column;">

                        <!-- Badge statut -->
                        <div style="position:absolute;top:16px;right:16px;z-index:1;">
                            @if($status === 'completed')
                                <span class="cyber-badge badge-low"><i class="bi bi-check-circle-fill"></i> COMPLÉTÉ</span>
                            @elseif($status === 'in_progress')
                                <span class="cyber-badge badge-pending"><i class="bi bi-hourglass-split"></i> EN COURS</span>
                            @else
                                <span class="cyber-badge"
                                    style="background:var(--bg-input);color:var(--text-muted);border:1px solid var(--border-subtle);">NOUVEAU</span>
                            @endif
                        </div>

                        <!-- Icône type -->
                        <div
                            style="width:48px;height:48px;border-radius:var(--radius);display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px;
                            background:{{ $typeColors[$t->type] ?? 'green' === 'green' ? 'var(--emerald-dim)' : ($typeColors[$t->type] ?? 'blue' === 'blue' ? 'var(--sky-dim)' : 'var(--violet-dim)') }};
                            color:var(--{{ $typeColors[$t->type] ?? 'emerald' === 'green' ? 'emerald' : ($typeColors[$t->type] ?? 'blue' === 'blue' ? 'sky' : 'violet') }});">
                            <i class="bi {{ $typeIcons[$t->type] ?? 'bi-book-fill' }}"></i>
                        </div>

                        <div
                            style="font-size:15px;font-weight:700;margin-bottom:8px;color:var(--text-primary);padding-right:100px;">
                            {{ $t->title }}
                        </div>
                        <div style="font-size:12px;color:var(--text-secondary);line-height:1.7;margin-bottom:16px;flex:1;">
                            {{ Str::limit($t->description, 120) }}
                        </div>

                        <!-- Badges infos -->
                        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;">
                            <span class="cyber-badge badge-info">
                                <i class="bi bi-{{ $typeIcons[$t->type] ?? 'book' }}"></i> {{ strtoupper($t->type) }}
                            </span>
                            <span class="cyber-badge {{ $diffColors[$t->difficulty] ?? 'badge-low' }}">
                                {{ strtoupper($t->difficulty) }}
                            </span>
                            <span class="cyber-badge"
                                style="background:var(--bg-input);color:var(--text-secondary);border:1px solid var(--border-subtle);">
                                <i class="bi bi-clock"></i> {{ $t->duration_minutes }} min
                            </span>
                            <span class="cyber-badge"
                                style="background:var(--amber-dim);color:var(--amber);border:1px solid rgba(245,158,11,.2);">
                                <i class="bi bi-star-fill"></i> +{{ $t->points_reward }} pts
                            </span>
                        </div>

                        <!-- Score si complété -->
                        @if($status === 'completed')
                            <div style="margin-bottom:14px;">
                                <div
                                    style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-secondary);margin-bottom:4px;">
                                    <span>Score obtenu</span>
                                    <span style="color:var(--emerald);font-weight:600;">{{ $score }}%</span>
                                </div>
                                <div class="cyber-progress">
                                    <div class="cyber-progress-fill progress-green" style="width:{{ $score }}%"></div>
                                </div>
                                @if($attempts > 0)
                                    <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">{{ $attempts }} tentative(s)
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div style="display:flex;gap:8px;margin-top:auto;">
                            <a href="{{ route('user.training.show', $t) }}"
                                class="btn-cyber {{ $status === 'completed' ? 'btn-cyber-warning' : 'btn-cyber-primary' }}"
                                style="flex:1;justify-content:center;">
                                <i
                                    class="bi bi-{{ $status === 'completed' ? 'arrow-repeat' : ($status === 'in_progress' ? 'play-fill' : 'play-circle-fill') }}"></i>
                                {{ $status === 'completed' ? 'Revoir' : ($status === 'in_progress' ? 'Continuer' : 'Commencer') }}
                            </a>
                            @if($status === 'completed')
                                <a href="{{ route('user.training.certificate', $t) }}" class="btn-cyber btn-cyber-success"
                                    style="padding:8px 12px;" title="Télécharger le certificat PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="cyber-card text-center" style="padding:60px;">
                        <i class="bi bi-book"
                            style="font-size:48px;color:var(--text-muted);display:block;margin-bottom:16px;"></i>
                        <div style="color:var(--text-secondary);">Aucune formation disponible pour le moment.</div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection