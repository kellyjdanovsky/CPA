@extends('layouts.master')
@section('page_title', 'Gestion du Barème de Notation')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">
                <i class="icon-medal mr-2"></i>
                Gestion du Barème de Notation
            </h6>
            <div class="header-elements">
                <button class="btn btn-primary" onclick="BaremeManager.showEditor()">
                    <i class="icon-cog3"></i>
                    Configurer Barème Auto
                </button>
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-grades" class="nav-link active" data-toggle="tab"><i class="icon-list"></i> Liste des Remarques</a></li>
                <li class="nav-item"><a href="#bareme-display" class="nav-link" data-toggle="tab"><i class="icon-star-full2"></i> Barème Complet</a></li>
                <li class="nav-item"><a href="#new-grade" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Ajouter Remarque</a></li>
            </ul>

            <div class="tab-content">
                {{-- Liste des grades existants --}}
                <div class="tab-pane fade show active" id="all-grades">
                    <div class="alert alert-info border-0 mb-3">
                        <i class="icon-info-circle mr-2"></i>
                        <strong>Info:</strong> Ces remarques sont utilisées dans les bulletins selon les notes obtenues.
                    </div>
                    
<div class="table-responsive">
                    <table class="table datatable-button-html5-columns table-hover">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Mention</th>
                                <th>Type de Classe</th>
                                <th>Plage de Notes</th>
                                <th>Remarque</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($grades as $gr)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge badge-primary font-weight-semibold">
                                        {{ $gr->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($gr->class_type_id)
                                        <span class="badge badge-info">
                                            {{ $class_types->where('id', $gr->class_type_id)->first()->name }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Toutes les classes</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        {{ $gr->mark_from }} - {{ $gr->mark_to }}
                                    </span>
                                </td>
                                <td>
                                    <em class="text-muted">{{ $gr->remark }}</em>
                                </td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(Qs::userIsTeamSA())
                                                    <a href="{{ route('grades.edit', $gr->id) }}" class="dropdown-item">
                                                        <i class="icon-pencil"></i> Modifier
                                                    </a>
                                                @endif
                                                @if(Qs::userIsSuperAdmin())
                                                    <a id="{{ $gr->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item">
                                                        <i class="icon-trash"></i> Supprimer
                                                    </a>
                                                    <form method="post" id="item-delete-{{ $gr->id }}" action="{{ route('grades.destroy', $gr->id) }}" class="hidden">
                                                        @csrf @method('delete')
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Affichage du barème complet --}}
                <div class="tab-pane fade" id="bareme-display">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-primary border-0 mb-3">
                                <i class="icon-info mr-2"></i>
                                <strong>Barème Automatique:</strong> Ce barème est généré automatiquement et peut être personnalisé avec le bouton "Configurer Barème Auto".
                            </div>
                            
                            {{-- Le barème sera injecté ici par JavaScript --}}
                            <div id="bareme-auto-display"></div>
                            
                            <div class="text-center mt-4">
                                <button class="btn btn-primary" onclick="BaremeManager.showEditor()">
                                    <i class="icon-cog3"></i>
                                    Modifier le Barème Automatique
                                </button>
                                <button class="btn btn-outline-secondary" onclick="BaremeManager.reset()">
                                    <i class="icon-reload"></i>
                                    Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulaire d'ajout --}}
                <div class="tab-pane fade" id="new-grade">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <span><i class="icon-info mr-2"></i> Si la mention s'applique à toutes les classes, sélectionnez <strong>NON APPLICABLE.</strong> Sinon, choisissez le type de classe concerné.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-plus-circle2 mr-2"></i>
                                        Nouvelle Remarque/Mention
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('grades.store') }}">
                                        @csrf
                                        
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label font-weight-semibold">
                                                Mention <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-9">
                                                <input name="name" value="{{ old('name') }}" required type="text" 
                                                       class="form-control text-uppercase" placeholder="Ex: A1, Excellent, Très Bien">
                                                <small class="form-text text-muted">Le nom de la mention qui apparaîtra sur le bulletin</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="class_type_id" class="col-lg-3 col-form-label font-weight-semibold">
                                                Type de Classe
                                            </label>
                                            <div class="col-lg-9">
                                                <select class="form-control select" name="class_type_id" id="class_type_id">
                                                    <option value="">Non Applicable (Toutes les classes)</option>
                                                    @foreach($class_types as $ct)
                                                        <option {{ old('class_type_id') == $ct->id ? 'selected' : '' }} value="{{ $ct->id }}">
                                                            {{ $ct->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label font-weight-semibold">
                                                Note Minimale <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-4">
                                                <input min="0" max="100" step="0.01" name="mark_from" value="{{ old('mark_from') }}" 
                                                       required type="number" class="form-control" placeholder="0">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label font-weight-semibold">
                                                Note Maximale <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-4">
                                                <input min="0" max="100" step="0.01" name="mark_to" value="{{ old('mark_to') }}" 
                                                       required type="number" class="form-control" placeholder="100">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="remark" class="col-lg-3 col-form-label font-weight-semibold">
                                                Remarque <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-9">
                                                <select class="form-control select" name="remark" id="remark">
                                                    <option value="">Sélectionner une remarque...</option>
                                                    @foreach(Mk::getRemarks() as $rem)
                                                        <option {{ old('remark') == $rem ? 'selected' : '' }} value="{{ $rem }}">{{ $rem }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    <i class="icon-info mr-1"></i>
                                                    Remarque personnalisée ? Utilisez le barème automatique ci-dessus
                                                </small>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="icon-cross2"></i> Annuler
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="icon-checkmark"></i> Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Afficher le barème automatique dans l'onglet
    if (typeof BaremeManager !== 'undefined') {
        BaremeManager.displayIn('#bareme-auto-display');
        
        // Rafraîchir l'affichage quand le barème est mis à jour
        window.addEventListener('baremeUpdated', function() {
            BaremeManager.displayIn('#bareme-auto-display');
            CPAModern.showToast('Barème mis à jour avec succès !', 'success', 2000);
        });
        
        console.log('%c📊 Barème Manager intégré', 'color: #6366f1; font-weight: bold;');
    } else {
        console.warn('⚠ BaremeManager non disponible');
        $('#bareme-auto-display').html('<div class="alert alert-warning"><i class="icon-warning mr-2"></i>Le gestionnaire de barème n\'est pas chargé. Veuillez vérifier que bareme-manager.js est inclus.</div>');
    }
});
</script>
@endsection
