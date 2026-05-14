@extends('layouts.app')
@section('title', 'Message')
@section('page-title', 'MESSAGE')

@section('content')
    <div class="fade-in" style="max-width:700px;">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Messagerie</span> / Message</div>
                <div class="page-header-title">{{ $message->subject ?? 'Message' }}</div>
            </div>
            <a href="{{ route('user.messages.index') }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="cyber-card">
            <div
                style="display:flex;align-items:center;gap:16px;padding-bottom:20px;border-bottom:1px solid var(--border-solid);margin-bottom:20px;">
                <div class="user-avatar" style="width:48px;height:48px;font-size:16px;flex-shrink:0;">
                    {{ strtoupper(substr($message->sender->name, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:15px;font-weight:600;">{{ $message->sender->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $message->sender->email }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $message->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
                @if($message->is_read)
                    <span class="cyber-badge badge-low ms-auto"><i class="bi bi-check2-all"></i> Lu</span>
                @endif
            </div>

            <div style="font-size:14px;line-height:1.9;color:var(--text-primary);white-space:pre-wrap;">{{ $message->body }}
            </div>

            @if($message->phishReport)
                <div
                    style="margin-top:20px;padding:14px;background:rgba(255,107,0,0.06);border:1px solid rgba(255,107,0,0.2);border-radius:8px;">
                    <div
                        style="font-size:12px;color:var(--neon-orange);margin-bottom:6px;font-family:'Share Tech Mono',monospace;">
                        <i class="bi bi-flag-fill"></i> SIGNALEMENT ASSOCIÉ
                    </div>
                    <a href="{{ route('user.reports.show', $message->phishReport) }}" class="btn-cyber btn-cyber-warning"
                        style="padding:6px 14px;font-size:11px;">
                        Voir le signalement #{{ $message->phishReport->id }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection