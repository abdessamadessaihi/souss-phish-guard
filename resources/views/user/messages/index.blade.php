@extends('layouts.app')
@section('title', 'Messagerie')
@section('page-title', 'MESSAGERIE SOC')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Messagerie</span></div>
                <div class="page-header-title">Messagerie Sécurisée</div>
                <div class="page-header-sub">Communication directe avec l'équipe SOC</div>
            </div>
            @if($unread > 0)
                <span class="cyber-badge badge-critical" style="padding:10px 16px;font-size:13px;">
                    <i class="bi bi-bell-fill"></i> {{ $unread }} non lu(s)
                </span>
            @endif
        </div>

        <div class="row g-3">
            <!-- Nouveau message -->
            <div class="col-md-4">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-pencil-fill"></i> NOUVEAU MESSAGE</div>
                    </div>
                    <form method="POST" action="{{ route('user.messages.store') }}">
                        @csrf
                        <div class="cyber-form-group">
                            <label class="cyber-label">DESTINATAIRE</label>
                            <select name="receiver_id" class="cyber-select" required>
                                <option value="">Sélectionner un Guardian...</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">🛡️ {{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cyber-form-group">
                            <label class="cyber-label">SUJET</label>
                            <input type="text" name="subject" class="cyber-input" placeholder="Objet du message">
                        </div>
                        <div class="cyber-form-group">
                            <label class="cyber-label">MESSAGE *</label>
                            <textarea name="body" class="cyber-textarea" rows="5" required
                                placeholder="Décrivez votre problème de sécurité..."></textarea>
                        </div>
                        <button type="submit" class="btn-cyber btn-cyber-primary w-100 justify-content-center">
                            <i class="bi bi-send-fill"></i> ENVOYER
                        </button>
                    </form>
                </div>
            </div>

            <!-- Liste messages -->
            <div class="col-md-8">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-inbox-fill"></i> BOÎTE DE RÉCEPTION</div>
                    </div>
                    @forelse($messages as $msg)
                        @php
                            $isSent = $msg->sender_id === auth()->id();
                            $isUnread = !$msg->is_read && !$isSent;
                        @endphp
                        <div
                            style="display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid var(--border-solid);{{ $isUnread ? 'background:rgba(0,245,255,0.02);' : '' }}">
                            <div class="user-avatar"
                                style="width:40px;height:40px;font-size:13px;flex-shrink:0;{{ $isSent ? 'background:rgba(168,85,247,0.15);color:var(--neon-purple);' : '' }}">
                                {{ strtoupper(substr($isSent ? $msg->receiver->name : $msg->sender->name, 0, 2)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                    <span
                                        style="font-size:13px;font-weight:{{ $isUnread ? '700' : '400' }};color:var(--text-primary);">
                                        {{ $isSent ? '→ ' . $msg->receiver->name : $msg->sender->name }}
                                    </span>
                                    @if($isUnread)
                                        <span class="cyber-badge badge-critical"
                                            style="font-size:9px;padding:2px 6px;">NOUVEAU</span>
                                    @endif
                                    @if($isSent)
                                        <span class="cyber-badge badge-pending" style="font-size:9px;padding:2px 6px;">ENVOYÉ</span>
                                    @endif
                                </div>
                                <div
                                    style="font-size:12px;font-weight:{{ $isUnread ? '600' : '400' }};color:{{ $isUnread ? 'var(--text-primary)' : 'var(--text-muted)' }};">
                                    {{ $msg->subject ?? 'Sans sujet' }}
                                </div>
                                <div
                                    style="font-size:11px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ Str::limit($msg->body, 60) }}
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:11px;color:var(--text-muted);">{{ $msg->created_at->diffForHumans() }}
                                </div>
                                <a href="{{ route('user.messages.show', $msg) }}" class="btn-cyber btn-cyber-primary mt-1"
                                    style="padding:4px 10px;font-size:10px;">
                                    <i class="bi bi-eye"></i> Lire
                                </a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:50px;color:var(--text-muted);">
                            <i class="bi bi-inbox"
                                style="font-size:40px;display:block;margin-bottom:12px;color:var(--border-glow);"></i>
                            Aucun message pour le moment.
                        </div>
                    @endforelse
                    <div class="mt-3">{{ $messages->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection