@extends('layouts.app')
@section('title', 'Modifier Simulation')
@section('page-title', 'MODIFIER SIMULATION')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Simulations</span> / Modifier</div>
                <div class="page-header-title">{{ $simulation->name }}</div>
                <div class="page-header-sub">Campagne de simulation — Statut : {{ strtoupper($simulation->status) }}</div>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('admin.simulations.show', $simulation) }}" class="btn-cyber btn-cyber-warning">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-pencil-fill"></i> MODIFIER LA CAMPAGNE</div>
                        <span class="cyber-badge badge-pending">{{ strtoupper($simulation->template) }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.simulations.update', $simulation) }}">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="cyber-alert danger mb-3">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="cyber-label">NOM DE LA CAMPAGNE *</label>
                                <input type="text" name="name" class="cyber-input" required
                                    value="{{ old('name', $simulation->name) }}" placeholder="Ex: Test Microsoft Q2 2026">
                                @error('name')<div class="cyber-invalid">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">NOM EXPÉDITEUR *</label>
                                <input type="text" name="from_name" class="cyber-input" required
                                    value="{{ old('from_name', $simulation->from_name) }}" placeholder="Microsoft Security">
                            </div>

                            <div class="col-md-6">
                                <label class="cyber-label">EMAIL EXPÉDITEUR *</label>
                                <input type="email" name="from_email" class="cyber-input" required
                                    value="{{ old('from_email', $simulation->from_email) }}"
                                    placeholder="security@microsoft.com">
                            </div>

                            <div class="col-12">
                                <label class="cyber-label">SUJET DE L'EMAIL *</label>
                                <input type="text" name="subject" class="cyber-input" required
                                    value="{{ old('subject', $simulation->subject) }}"
                                    placeholder="Alerte sécurité : action requise">
                            </div>

                            <div class="col-12">
                                <label class="cyber-label">CORPS DE L'EMAIL *</label>
                                <div style="display:flex;gap:8px;margin-bottom:8px;">
                                    <button type="button" onclick="togglePreview()" class="btn-cyber btn-cyber-warning"
                                        style="padding:6px 12px;font-size:11px;">
                                        <i class="bi bi-eye"></i> Aperçu
                                    </button>
                                    <span style="font-size:11px;color:var(--text-secondary);margin-top:8px;">
                                        Utilisez <code
                                            style="background:var(--bg-input);padding:2px 6px;border-radius:3px;color:var(--emerald);">@{{name}}</code>
                                        pour personnaliser avec le prénom
                                    </span>
                                </div>
                                <textarea name="body" id="bodyField" class="cyber-textarea" rows="12" required
                                    placeholder="Corps de l'email...">{{ old('body', $simulation->body) }}</textarea>
                                <div id="preview"
                                    style="display:none;margin-top:8px;padding:20px;background:white;border-radius:8px;color:#333;font-family:Arial,sans-serif;font-size:14px;line-height:1.7;border:1px solid var(--border-default);">
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:20px;display:flex;gap:10px;">
                            <button type="submit" class="btn-cyber btn-cyber-primary" style="padding:12px 24px;">
                                <i class="bi bi-save-fill"></i> SAUVEGARDER LES MODIFICATIONS
                            </button>
                            <a href="{{ route('admin.simulations.show', $simulation) }}" class="btn-cyber btn-cyber-warning"
                                style="padding:12px 24px;">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Infos sidebar -->
            <div class="col-md-4">
                <div class="cyber-card mb-3">
                    <div class="cyber-card-title mb-3"><i class="bi bi-info-circle"></i> INFORMATIONS</div>
                    <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Statut</span>
                            @php $st = ['draft' => 'badge-pending', 'running' => 'badge-high', 'completed' => 'badge-low']; @endphp
                            <span
                                class="cyber-badge {{ $st[$simulation->status] ?? 'badge-info' }}">{{ strtoupper($simulation->status) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Template</span>
                            <span class="cyber-badge badge-info">{{ strtoupper($simulation->template) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Cibles</span>
                            <span class="font-mono text-green">{{ $simulation->targets_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Envoyés</span>
                            <span class="font-mono">{{ $simulation->opened_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Cliqués</span>
                            <span class="font-mono text-red">{{ $simulation->clicked_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Créé le</span>
                            <span style="font-size:11px;">{{ $simulation->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted-cyber">Par</span>
                            <span>{{ $simulation->creator->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                @if($simulation->status !== 'completed')
                    <div class="cyber-card">
                        <div class="cyber-card-title mb-3" style="color:var(--rose);"><i class="bi bi-send-fill"></i> LANCER
                        </div>
                        <p style="font-size:12px;color:var(--text-secondary);margin-bottom:14px;">
                            Après modification, vous pouvez lancer la campagne pour envoyer les emails aux cibles définies.
                        </p>
                        <form method="POST" action="{{ route('admin.simulations.launch', $simulation) }}"
                            onsubmit="return confirm('Lancer la campagne ? Les emails seront envoyés immédiatement.')">
                            @csrf
                            <button type="submit" class="btn-cyber btn-cyber-danger w-100 justify-content-center"
                                style="padding:11px;">
                                <i class="bi bi-play-fill"></i> LANCER LA CAMPAGNE
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function togglePreview() {
            const preview = document.getElementById('preview');
            const body = document.getElementById('bodyField').value;
            if (preview.style.display === 'none') {
                preview.style.display = 'block';
                preview.innerHTML = body
                    .replace(/\n/g, '<br>')
                    .replace(/\{\{name\}\}/g, '<strong style="color:#059669;">[Nom Cible]</strong>');
                preview.style.display = 'none';
            }
        }
    </script>
@endsection