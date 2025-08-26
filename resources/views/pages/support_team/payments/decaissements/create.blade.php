@extends('layouts.master')
@section('page_title', 'Nouvel Ordre de Paiement')

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">
            <i class="icon-plus2 mr-2"></i> Nouvel Ordre de Paiement (OP)
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('payments.decaissements.index') }}" class="btn btn-light">
                    <i class="icon-arrow-left8 mr-2"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('payments.decaissements.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_decaissement">Date de Décaissement <span class="text-danger">*</span></label>
                        <input type="date" name="date_decaissement" id="date_decaissement" class="form-control" 
                               value="{{ old('date_decaissement', date('Y-m-d')) }}" required>
                        @error('date_decaissement')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="beneficiaire">Bénéficiaire <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiaire" id="beneficiaire" class="form-control" 
                               value="{{ old('beneficiaire') }}" required>
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
                                   step="0.01" min="0" value="{{ old('montant') }}" required>
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
                            <option value="Espèces" {{ old('mode_paiement') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                            <option value="Chèque" {{ old('mode_paiement') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                            <option value="Virement" {{ old('mode_paiement') == 'Virement' ? 'selected' : '' }}>Virement</option>
                            <option value="Mobile Money" {{ old('mode_paiement') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('mode_paiement')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="motif">Motif <span class="text-danger">*</span></label>
                <textarea name="motif" id="motif" class="form-control" rows="3" required>{{ old('motif') }}</textarea>
                @error('motif')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="projet_rubrique">Projet/Rubrique</label>
                <select name="projet_rubrique" id="projet_rubrique" class="form-control">
                    <option value="">Choisir un projet/rubrique</option>
                    @foreach($projets_rubriques as $projet)
                        <option value="{{ $projet }}" {{ old('projet_rubrique') == $projet ? 'selected' : '' }}>
                            {{ $projet }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    Ou créer un nouveau projet : 
                    <input type="text" id="nouveau_projet" class="form-control mt-1" placeholder="Nouveau projet/rubrique">
                </small>
            </div>

            <div class="form-group">
                <label for="piece_justificative">Pièce Justificative</label>
                <input type="file" name="piece_justificative" id="piece_justificative" class="form-control-file">
                <small class="form-text text-muted">Formats acceptés: PDF, JPG, PNG, DOC, DOCX (max: 2MB)</small>
                @error('piece_justificative')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea name="observations" id="observations" class="form-control" rows="2">{{ old('observations') }}</textarea>
            </div>

            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">

            <!-- Aperçu du montant en lettres -->
            <div class="alert alert-info" id="montant-lettres" style="display: none;">
                <strong>Montant en lettres :</strong> <span id="montant-lettres-text"></span>
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-light" onclick="history.back()">
                    <i class="icon-cross2 mr-2"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-checkmark mr-2"></i> Créer l'OP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gestion du nouveau projet
    $('#nouveau_projet').on('blur', function() {
        var nouveauProjet = $(this).val().trim();
        if (nouveauProjet) {
            // Ajouter l'option et la sélectionner
            var option = new Option(nouveauProjet, nouveauProjet, true, true);
            $('#projet_rubrique').append(option);
            $(this).val('');
        }
    });

    // Convertir montant en lettres (version simplifiée)
    $('#montant').on('input', function() {
        var montant = parseFloat($(this).val());
        if (montant && !isNaN(montant)) {
            // Affichage simple - dans un vrai système, utiliser une API de conversion
            $('#montant-lettres-text').text(montant.toLocaleString('fr-FR') + ' Ariary');
            $('#montant-lettres').show();
        } else {
            $('#montant-lettres').hide();
        }
    });

    // Validation du fichier
    $('#piece_justificative').change(function() {
        var file = this.files[0];
        if (file) {
            var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            var maxSize = 2 * 1024 * 1024; // 2MB

            if (!allowedTypes.includes(file.type)) {
                alert('Type de fichier non autorisé. Utilisez PDF, JPG, PNG, DOC ou DOCX.');
                $(this).val('');
                return;
            }

            if (file.size > maxSize) {
                alert('Le fichier est trop volumineux. Taille maximum: 2MB.');
                $(this).val('');
                return;
            }
        }
    });
});
</script>

@endsection