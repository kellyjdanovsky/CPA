@extends('layouts.master')
@section('page_title', 'Modifier Ordre de Paiement')

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">
            <i class="icon-pencil mr-2"></i> Modifier OP : <span class="text-primary">{{ $decaissement->reference_op }}</span>
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('payments.decaissements.show', $decaissement->id) }}" class="btn btn-light">
                    <i class="icon-arrow-left8 mr-2"></i> Annuler
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('payments.decaissements.update', $decaissement->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_decaissement">Date de Décaissement <span class="text-danger">*</span></label>
                        <input type="date" name="date_decaissement" id="date_decaissement" class="form-control" 
                               value="{{ old('date_decaissement', $decaissement->date_decaissement->format('Y-m-d')) }}" required>
                        @error('date_decaissement')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="beneficiaire">Bénéficiaire <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiaire" id="beneficiaire" class="form-control" 
                               value="{{ old('beneficiaire', $decaissement->beneficiaire) }}" required>
                        @error('beneficiaire')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="montant">Montant <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="montant" id="montant" class="form-control" 
                                   step="0.01" min="0" value="{{ old('montant', $decaissement->montant) }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text">Ar</span>
                            </div>
                        </div>
                        @error('montant')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mode_paiement">Mode de Paiement <span class="text-danger">*</span></label>
                        <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                            <option value="">Choisir le mode</option>
                            @foreach($payment_methods as $method)
                                <option value="{{ $method }}" {{ old('mode_paiement', $decaissement->mode_paiement) == $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                            {{-- Add defaults if list is empty --}}
                            @if($payment_methods->isEmpty())
                                <option value="Espèces" {{ old('mode_paiement', $decaissement->mode_paiement) == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                <option value="Chèque" {{ old('mode_paiement', $decaissement->mode_paiement) == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                <option value="Virement" {{ old('mode_paiement', $decaissement->mode_paiement) == 'Virement' ? 'selected' : '' }}>Virement</option>
                            @endif
                        </select>
                        @error('mode_paiement')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="motif">Motif <span class="text-danger">*</span></label>
                <textarea name="motif" id="motif" class="form-control" rows="3" required>{{ old('motif', $decaissement->motif) }}</textarea>
                @error('motif')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="projet_rubrique">Projet/Rubrique</label>
                <select name="projet_rubrique" id="projet_rubrique" class="form-control">
                    <option value="">Choisir un projet/rubrique</option>
                    @foreach($projets_rubriques as $projet)
                        <option value="{{ $projet }}" {{ old('projet_rubrique', $decaissement->projet_rubrique) == $projet ? 'selected' : '' }}>
                            {{ $projet }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Ou saisir nouveau si nécessaire</small>
            </div>

            <div class="form-group">
                <label for="piece_justificative">Pièce Justificative (Laisser vide pour conserver l'existant)</label>
                @if($decaissement->hasPieceJustificative())
                    <div class="mb-2">
                        <span class="badge badge-info"><i class="icon-file-text2 mr-1"></i> Fichier existant: {{ $decaissement->piece_justificative_nom }}</span>
                    </div>
                @endif
                <input type="file" name="piece_justificative" id="piece_justificative" class="form-control-file">
                <small class="form-text text-muted">Formats acceptés: PDF, JPG, PNG, DOC, DOCX (max: 2MB)</small>
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea name="observations" id="observations" class="form-control" rows="2">{{ old('observations', $decaissement->observations) }}</textarea>
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-light" onclick="history.back()">
                    <i class="icon-cross2 mr-2"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-checkmark mr-2"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#montant').on('input', function() {
        // Logic for amount preview if needed
    });
    
    // Allow new project entry if needed via select2 or simple input replacement
});
</script>
@endsection
