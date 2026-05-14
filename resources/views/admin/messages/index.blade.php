@extends('layouts.app')
@section('title', 'Messagerie Admin')
@section('page-title', 'MESSAGERIE GUARDIAN')

@section('content')
    <div class="fade-in">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Messagerie</span></div>
                <div class="page-header-title">Messagerie Guardian</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-send-fill"></i> ENVOYER UN MESSAGE</div>
                    </div>
                    <form method="POST" action="{{ route('admin.messages.store') }}">
                        @csrf
                        <div class="cyber-form-group">
                            <label class="cyber-label">DESTINATAIRE</label>
                            <select name="receiver_id" class="cyber-select" required>
                                <option value="">Sélectionner un agent...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->department ?? 'N/A' }})</option>
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
                                placeholder="Votre message..."></textarea>
                        </div>
                        <button type="submit" class="btn-cyber btn-cyber-primary w-100 justify-content-center">
                            <i class="bi bi-send-fill"></i> ENVOYER
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div class="cyber-card-title"><i class="bi bi-inbox-fill"></i> MESSAGES ({{ $messages->total() }})
                        </div>
                    </div>
                    @forelse($messages as $msg)
                        @php $isSent = $msg->sender_id === auth()->id(); @endphp
                        <div
                            style="display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid var(--border-solid);">
                            <div class="user-avatar" style="width:40px;height:40px;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($isSent ? $msg->receiver->name : $msg->sender->name, 0, 2)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:500;">
                                    {{ $isSent ? '→ ' . $msg->receiver->name : $msg->sender->name }}
                                    @if($isSent)<span class="cyber-badge badge-pending"
                                    style="font-size:9px;">ENVOYÉ</span>@endif
                                    @if(!$msg->is_read && !$isSent)<span class="cyber-badge badge-critical"
                                    style="font-size:9px;">NOUVEAU</span>@endif
                                </div>
                                <div style="font-size:12px;color:var(--text-muted);">{{ $msg->subject ?? 'Sans sujet' }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ Str::limit($msg->body, 70) }}</div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;font-size:11px;color:var(--text-muted);">
                                {{ $msg->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:50px;color:var(--text-muted);">
                            <i class="bi bi-inbox"
                                style="font-size:40px;display:block;margin-bottom:12px;color:var(--border-glow);"></i>
                            Aucun message.
                        </div>
                    @endforelse
                    <div class="mt-3">{{ $messages->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection