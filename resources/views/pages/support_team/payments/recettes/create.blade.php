@extends('layouts.master')
@section('page_title', 'Nouvelle Recette')

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">
            <i class="icon-plus2 mr-2"></i> Nouvelle Recette
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('payments.recettes.index') }}" class="btn btn-light">
                    <i class="icon-arrow-left8 mr-2"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('payments.recettes.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_recette">Date de Recette <span class="text-danger">*</span></label>
                        <input type="date" name="date_recette" id="date_recette" class="form-control" 
                               value="{{ old('date_recette', date('Y-m-d')) }}" required>
                        @error('date_recette')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type_recette">Type de Recette <span class="text-danger">*</span></label>
                        <select name="type_recette" id="type_recette" class="form-control" required>
                            <option value="">Choisir le type</option>
                            <option value="NORMAL" {{ old('type_recette') == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                            <option value="ADRA" {{ old('type_recette') == 'ADRA' ? 'selected' : '' }}>ADRA</option>
                            <option value="TEAM3" {{ old('type_recette') == 'TEAM3' ? 'selected' : '' }}>TEAM3</option>
                            <option value="DIVERS" {{ old('type_recette') == 'DIVERS' ? 'selected' : '' }}>Divers</option>
                        </select>
                        @error('type_recette')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="montant_encaisse">Montant Encaissé <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="montant_encaisse" id="montant_encaisse" class="form-control" 
                                   step="0.01" min="0" value="{{ old('montant_encaisse') }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text">Ar</span>
                            </div>
                        </div>
                        @error('montant_encaisse')
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
                            <option value="Autre" {{ old('mode_paiement') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('mode_paiement')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informations étudiant (conditionnelles) -->
            <div id="student-info" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="student_id">Étudiant</label>
                            <select name="student_id" id="student_id" class="form-control select2">
                                <option value="">Choisir un étudiant</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="class_id">Classe</label>
                            <select name="class_id" id="class_id" class="form-control">
                                <option value="">Choisir une classe</option>
                                @foreach($my_classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations bénéficiaire (pour recettes diverses) -->
            <div id="beneficiaire-info" style="display: none;">
                <div class="form-group">
                    <label for="beneficiaire_nom">Nom du Bénéficiaire</label>
                    <input type="text" name="beneficiaire_nom" id="beneficiaire_nom" class="form-control" 
                           value="{{ old('beneficiaire_nom') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea name="observations" id="observations" class="form-control" rows="2">{{ old('observations') }}</textarea>
            </div>

            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">

            <div class="text-right">
                <button type="button" class="btn btn-light" onclick="history.back()">
                    <i class="icon-cross2 mr-2"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-checkmark mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialiser Select2
    $('.select2').select2({
        placeholder: 'Rechercher un étudiant...',
        allowClear: true
    });

    // Gérer l'affichage conditionnel des champs
    $('#type_recette').change(function() {
        var type = $(this).val();
        
        if (type === 'DIVERS') {
            $('#student-info').hide();
            $('#beneficiaire-info').show();
            $('#student_id, #class_id').prop('required', false);
        } else if (type === 'NORMAL' || type === 'ADRA' || type === 'TEAM3') {
            $('#student-info').show();
            $('#beneficiaire-info').hide();
            $('#student_id').prop('required', true);
        } else {
            $('#student-info').hide();
            $('#beneficiaire-info').hide();
            $('#student_id, #class_id').prop('required', false);
        }
    });

    // Déclencher le changement initial
    $('#type_recette').trigger('change');
});
</script>

@endsection