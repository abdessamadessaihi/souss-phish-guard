@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'GESTION AGENTS')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Utilisateurs</span></div>
                <div class="page-header-title">Gestion des Agents</div>
                <div class="page-header-sub">{{ $users->total() }} agent(s) enregistré(s)</div>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-cyber btn-cyber-primary">
                <i class="bi bi-person-plus-fill"></i> Nouvel agent
            </a>
        </div>

        <div class="cyber-card">
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>AGENT</th>
                        <th>EMAIL</th>
                        <th>DÉPARTEMENT</th>
                        <th>SCORE</th>
                        <th>SIGNALEMENTS</th>
                        <th>STATUT</th>
                        <th>DERNIÈRE CO.</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="user-avatar" style="width:34px;height:34px;font-size:11px;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:500;">{{ $u->name }}</div>
                                        <div style="font-size:10px;color:var(--text-muted);">#{{ $u->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:12px;color:var(--text-muted);">{{ $u->email }}</td>
                            <td><span class="cyber-badge badge-info">{{ $u->department ?? 'N/A' }}</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="cyber-progress" style="width:60px;">
                                        <div class="cyber-progress-fill {{ $u->vigilance_score >= 70 ? 'progress-green' : ($u->vigilance_score >= 40 ? 'progress-orange' : 'progress-red') }}"
                                            style="width:{{ $u->vigilance_score }}%"></div>
                                    </div>
                                    <span class="font-mono" style="font-size:12px;">{{ $u->vigilance_score }}</span>
                                </div>
                            </td>
                            <td class="font-mono text-center">{{ $u->phish_reports_count ?? 0 }}</td>
                            <td>
                                <span class="cyber-badge {{ $u->is_active ? 'badge-low' : 'badge-critical' }}">
                                    {{ $u->is_active ? 'ACTIF' : 'INACTIF' }}
                                </span>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted);">
                                {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Jamais' }}
                            </td>
                            <td>
                                <div style="display:flex;gap:5px;">
                                    <a href="{{ route('admin.users.show', $u) }}" class="btn-cyber btn-cyber-primary"
                                        style="padding:4px 8px;font-size:10px;"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn-cyber btn-cyber-warning"
                                        style="padding:4px 8px;font-size:10px;"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn-cyber {{ $u->is_active ? 'btn-cyber-danger' : 'btn-cyber-success' }}"
                                            style="padding:4px 8px;font-size:10px;"
                                            title="{{ $u->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="bi bi-{{ $u->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                        </button>
                                    </form>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                            onsubmit="return confirm('Supprimer cet agent ?')">
                                            @csrf @method('DELETE')
                                            <button class="btn-cyber btn-cyber-danger" style="padding:4px 8px;font-size:10px;"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Aucun agent</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $users->links() }}</div>
        </div>
    </div>
@endsection