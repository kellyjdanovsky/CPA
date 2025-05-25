@extends('layouts.master')
@section('page_title', 'Ajouter un Projet')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Ajouter un Projet</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <form method="post" action="{{ route('projets.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom">Nom du Projet <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                        @error('nom')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="statut">Statut <span class="text-danger">*</span></label>
                        <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror" required>
                            <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                            <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                        @error('statut')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_debut">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut') is-invalid @enderror" value="{{ old('date_debut') }}">
                        @error('date_debut')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_fin">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin') is-invalid @enderror" value="{{ old('date_fin') }}">
                        @error('date_fin')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="budget">Budget (Ar)</label>
                        <input type="number" name="budget" id="budget" class="form-control @error('budget') is-invalid @enderror" value="{{ old('budget') }}" min="0" step="1000">
                        @error('budget')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Espace réservé pour d'autres champs si nécessaire -->
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-right">
                <a href="{{ route('projets.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection
