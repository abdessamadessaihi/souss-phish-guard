@extends('layouts.app')
@section('title', 'Simulations')
@section('page-title', 'CAMPAGNES SIMULATION')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Simulations</span></div>
                <div class="page-header-title">Campagnes de Simulation</div>
                <div class="page-header-sub">Testez la vigilance de vos équipes</div>
            </div>
            <a href="{{ route('admin.simulations.create') }}" class="btn-cyber btn-cyber-danger">
                <i class="bi bi-plus-circle-fill"></i> Nouvelle campagne
            </a>
        </div>

        <div class="row g-3 mb-4">
            @foreach([
                    ['label' => 'TOTAL', 'val' => $stats['total'], 'color' => 'cyan', 'icon' => 'bi-envelope-fill'],
                    ['label' => 'EN COURS', 'val' => $stats['running'], 'color' => 'orange', 'icon' => 'bi-play-fill'],
                    ['label' => 'TERMINÉES', 'val' => $stats['completed'], 'color' => 'green', 'icon' => 'bi-check-circle-fill'],
                    ['label' => 'BROUILLONS', 'val' => $stats['draft'], 'color' => 'purple', 'icon' => 'bi-pencil-fill'],
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
            @if($simulations->isEmpty())
                <div style="text-align:center;padding:60px;">
                    <i class="bi bi-envelope-fill" style="font-size:50px;color:var(--border-glow);display:block;margin-bottom:16px;"></i>
                    <div class="font-mono" style="color:var(--text-muted);">Aucune campagne créée</div>
                    <a href="{{ route('admin.simulations.create') }}" class="btn-cyber btn-cyber-primary mt-3">
                        <i class="bi bi-plus-circle-fill"></i> Créer la première
                    </a>
                </div>


            @else





                                                        <table class="cyber-table">
                                        <thead>
                                            <tr>
                                                    <th>#</th><th>NOM</th><th>TEMPLATE</th>
                                                    <th>CIBLES</th><th>CLICS</th><th>TAUX</th><th>STATUT</th><th>DATE</th><th>ACTIONS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($simulations as $s)
                                                    <tr>
                                                        <td class="font-mono text-muted-cyber">#{{ $s->id }}</td>
                                                        <td style="font-weight:500;">{{ $s->name }}</td>
                                                        <td><span class="cyber-badge badge-info">{{ strtoupper($s->template) }}</span></td>
                                                        <td class
                                                               ="font-mono">{{ $s->targets_count }}</td>
                                                        <td class="font-mono text-orange">{{ $s->clicked_count }}</td>
                                                        <td>
                                                            @if($s->targets_count > 0)
                                                                @php $rate = round(($s->clicked_count / $s->targets_count) * 100); @endphp
                                                                <span style="color:{{ $rate >= 50 ? 'var(--neon-red)' : ($rate >= 25 ? 'var(--neon-orange)' : 'var(--neon-green)') }};font-family:'Share Tech Mono',monospace;">
                                                                    {{ $rate }}%
                                                                </span>
                                                            @else
                                                                <span style="color:var(--text-muted);">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php $st = ['draft' => 'badge-pending', 'running' => 'badge-high', 'completed' => 'badge-low', 'scheduled' => 'badge-info']; @endphp
                                                        <spa    n class="cyber-badge {{ $st[$s->status] ?? 'badge-info' }}">{{ strtoupper($s->status) }}      </span>
                                                    </td>
                                                        <td styl    e="font-size:11px;color:var(--text-muted);">{{ $s->created_at->format('d/m/Y') }}</td>

                                                                                    <td>
                                                           <div     style="display:flex;gap:6px;">

                                                                   <a href="{{ route('admin.simulations.show', $s) }}" class="btn-cyber btn-cyber-primary" style="padding:5px 10px;font-size:10px;"><i class="bi bi-eye"></i></a>
                                                               @if($s->status !== 'completed')

                                                                   <a href="{{ route('admin.simulations.edit', $s) }}" class="btn-cyber btn-cyber-warning" style="padding:5px 10px;font-size:10px;"><i class="bi bi-pencil"></i></a>
                                                            @endif
                                                                <form method="POST" action="{{ route('admin.simulations.destroy', $s) }}" onsubmit="return confirm('Supprimer cette simulation ?')">
                                                                    @csrf @method('DELETE')
                                                                    <button class="btn-cyber btn-cyber-danger" style="padding:5px 10px;font-size:10px;"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-3">{{ $simulations->links() }}</div>
                                @endif
        </div>
    </div>
@endsection