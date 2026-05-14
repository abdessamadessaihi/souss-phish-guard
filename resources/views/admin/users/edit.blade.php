@extends('layouts.app')
@section('title', 'Modifier Agent')
@section('page-title', 'MODIFIER AGENT')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Utilisateurs</span> / Modifier</div>
                <div class="page-header-title">{{ $user->name }}</div>
                <div class="page-header-sub">{{ $user->email }} · {{ $user->department ?? 'Aucun département' }}</div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-person-fill-gear"></i> MODIFIER LE COMPTE</div>
                        <span class="cyber-badge {{ $user->is_active ? 'badge-low' : 'badge-critical' }}">
                            {{ $user->is_active ? 'ACTIF' : 'INACTIF' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="cyber-alert danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                {{ $errors->first() }}
                                <button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="cyber-label">NOM COMPLET *</label>
                                <input type="text" name="name" class="cyber-input" required
                                    value="{{ old('name', $user->name) }}">
                                @error('name')<div class="cyber-invalid">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">ADRESSE EMAIL *</label>
                                <input type="email" name="email" class="cyber-input" required
                                    value="{{ old('email', $user->email) }}">
                                @error('email')<div class="cyber-invalid">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">DÉPARTEMENT</label>
                                <select name="department" class="cyber-select">
                                    <option value="">Sélectionner...</option>
                                    @foreach(['IT', 'RH', 'Finance', 'Commercial', 'Direction', 'Juridique', 'Marketing', 'Autre'] as $d)
                                        <option value="{{ $d }}" {{ ($user->department === $d) ? 'selected' : '' }}>{{ $d }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">RÔLE</label>
                                <select name="role" class="cyber-select">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>👤 Agent (Utilisateur)
                                    </option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>🛡️ Guardian (Admin)
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">SCORE DE VIGILANCE (0-100)</label>
                                <input type="number" name="vigilance_score" class="cyber-input" min="0" max="100"
                                    value="{{ old('vigilance_score', $user->vigilance_score) }}">
                                <div style="margin-top:6px;">
                                    <div class="cyber-progress">
                                        <div class="cyber-progress-fill {{ $user->vigilance_score >= 70 ? 'progress-green' : ($user->vigilance_score >= 40 ? 'progress-orange' : 'progress-red') }}"
                                            style="width:{{ $user->vigilance_score }}%"></div>
                                    </div>
                                    <div
                                        style="font-size:10px;color:var(--text-muted);margin-top:3px;text-align:right;font-family:'JetBrains Mono',monospace;">
                                        {{ $user->vigilance_score }}/100
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">STATUT DU COMPTE</label>
                                <select name="is_active" class="cyber-select">
                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>✅ Actif</option>
                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>❌ Désactivé</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <div
                                    style="padding:14px;background:var(--bg-input);border-radius:var(--radius);border:1px solid var(--border-subtle);">
                                    <div
                                        style="font-size:11px;color:var(--text-secondary);margin-bottom:10px;font-weight:600;">
                                        🔐 CHANGER LE MOT DE PASSE (optionnel)
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="cyber-label">NOUVEAU MOT DE PASSE</label>
                                            <input type="password" name="password" class="cyber-input" minlength="8"
                                                placeholder="Laisser vide pour ne pas changer">
                                            @error('password')<div class="cyber-invalid">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="cyber-label">CONFIRMER</label>
                                            <input type="password" name="password_confirmation" class="cyber-input"
                                                placeholder="Répétez le mot de passe">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:20px;display:flex;gap:10px;align-items:center;">
                            <button type="submit" class="btn-cyber btn-cyber-primary" style="padding:12px 24px;">
                                <i class="bi bi-save-fill"></i> SAUVEGARDER
                            </button>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-cyber btn-cyber-warning"
                                style="padding:12px 24px;">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Infos utilisateur -->
            <div class="col-md-4">
                <div class="cyber-card mb-3">
                    <div style="text-align:center;padding:20px 0;">
                        <div class="user-avatar" style="width:60px;height:60px;font-size:20px;margin:0 auto 12px;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div style="font-size:16px;font-weight:600;">{{ $user->name }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-top:3px;">{{ $user->email }}</div>
                    </div>

                    <div
                        style="border-top:1px solid var(--border-subtle);padding-top:16px;display:flex;flex-direction:column;gap:8px;font-size:12px;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Signalements</span>
                            <span class="font-mono text-green">{{ $user->reports_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Tests réussis</span>
                            <span class="font-mono text-green">{{ $user->simulations_passed }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Dernière co.</span>
                            <span>{{ $user->last_login_at?->diffForHumans() ?? 'Jamais' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">IP dernière co.</span>
                            <span class="font-mono" style="font-size:10px;">{{ $user->last_login_ip ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Inscrit le</span>
                            <span>{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="cyber-card">
                    <div class="cyber-card-title mb-3"><i class="bi bi-lightning-fill"></i> ACTIONS</div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="btn-cyber {{ $user->is_active ? 'btn-cyber-danger' : 'btn-cyber-success' }} w-100 justify-content-center">
                                <i class="bi bi-{{ $user->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                {{ $user->is_active ? 'Désactiver le compte' : 'Activer le compte' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.users.show', $user) }}"
                            class="btn-cyber btn-cyber-primary w-100 justify-content-center">
                            <i class="bi bi-eye-fill"></i> Voir le profil complet
                        </a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                onsubmit="return confirm('Supprimer définitivement {{ $user->name }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-cyber btn-cyber-danger w-100 justify-content-center">
                                    <i class="bi bi-trash-fill"></i> Supprimer l'agent
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection