@extends('layouts.app')
@section('title', 'Nouvel Agent')
@section('page-title', 'NOUVEL AGENT')

@section('content')
    <div class="fade-in" style="max-width:600px;">
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / Admin / <span>Utilisateurs</span> / Nouveau</div>
                <div class="page-header-title">Créer un agent</div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-cyber btn-cyber-warning"><i class="bi bi-arrow-left"></i>
                Retour</a>
        </div>

        <div class="cyber-card">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cyber-label">NOM COMPLET *</label>
                        <input type="text" name="name" class="cyber-input" required value="{{ old('name') }}">
                        @error('name')<div class="cyber-invalid">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">EMAIL *</label>
                        <input type="email" name="email" class="cyber-input" required value="{{ old('email') }}">
                        @error('email')<div class="cyber-invalid">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">DÉPARTEMENT</label>
                        <select name="department" class="cyber-select">
                            <option value="">Sélectionner...</option>
                            @foreach(['IT', 'RH', 'Finance', 'Commercial', 'Direction', 'Autre'] as $d)
                                <option value="{{ $d }}" {{ old('department') === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">RÔLE *</label>
                        <select name="role" class="cyber-select" required>
                            <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>👤 Agent</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>🛡️ Guardian (Admin)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">MOT DE PASSE *</label>
                        <input type="password" name="password" class="cyber-input" required minlength="8">
                        @error('password')<div class="cyber-invalid">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="cyber-label">CONFIRMER *</label>
                        <input type="password" name="password_confirmation" class="cyber-input" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn-cyber btn-cyber-primary"><i class="bi bi-person-plus-fill"></i> CRÉER
                        L'AGENT</button>
                </div>
            </form>
        </div>
    </div>
@endsection