@extends('layouts.app')
@section('title', 'Modifier Signalement')
@section('page-title', 'MODIFIER SIGNALEMENT')

@section('content')
    <div class="fade-in" style="max-width:800px;">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Signalements</span> / Modifier #{{ $report->id }}</div>
                <div class="page-header-title">Modifier le signalement</div>
            </div>
            <a href="{{ route('user.reports.show', $report) }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="cyber-card">
            <form method="POST" action="{{ route('user.reports.update', $report) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="cyber-form-group">
                            <label class="cyber-label">TYPE *</label>
                            <select name="type" class="cyber-select" required>
                                <option value="url" {{ $report->type == 'url' ? 'selected' : '' }}>🔗 URL</option>
                                <option value="email" {{ $report->type == 'email' ? 'selected' : '' }}>📧 Email</option>
                                <option value="sms" {{ $report->type == 'sms' ? 'selected' : '' }}>💬 SMS</option>
                                <option value="other" {{ $report->type == 'other' ? 'selected' : '' }}>⚠️ Autre</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cyber-form-group">
                            <label class="cyber-label">OBJET</label>
                            <input type="text" name="subject" class="cyber-input"
                                value="{{ old('subject', $report->subject) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="cyber-form-group">
                            <label class="cyber-label">CONTENU *</label>
                            <textarea name="content" class="cyber-textarea" rows="5"
                                required>{{ old('content', $report->content) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cyber-form-group">
                            <label class="cyber-label">EMAIL EXPÉDITEUR</label>
                            <input type="email" name="sender_email" class="cyber-input"
                                value="{{ old('sender_email', $report->sender_email) }}">
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn-cyber btn-cyber-primary">
                        <i class="bi bi-arrow-clockwise"></i> METTRE À JOUR & RE-ANALYSER
                    </button>
                    <a href="{{ route('user.reports.show', $report) }}" class="btn-cyber btn-cyber-warning">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection