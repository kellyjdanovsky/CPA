@extends('layouts.master')
@section('page_title', 'Détails de la Dépense')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Détails de la Dépense</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item">
                <a href="{{ route('financial_dashboard.expenses') }}" class="nav-link">
                    <i class="icon-stats-bars2 mr-2"></i> Tableau de Bord des Dépenses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('decaissements.index') }}" class="nav-link">
                    <i class="icon-list3 mr-2"></i> Liste des Dépenses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('decaissements.create') }}" class="nav-link">
                    <i class="icon-plus2 mr-2"></i> Ajouter une Dépense
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="icon-eye mr-2"></i> Détails de la Dépense
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="decaissement-details">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-transparent header-elements-inline">
                                <h6 class="card-title">Informations de la dépense</h6>
                                <div class="header-elements">
                                    <span class="badge badge-pill badge-primary">ID: {{ $decaissement->id }}</span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl>
                                            <dt>Date de paiement</dt>
                                            <dd>{{ $decaissement->formatted_date }}</dd>
                                            
                                            <dt>Montant</dt>
                                            <dd>{{ $decaissement->formatted_montant }}</dd>
                                            
                                            <dt>Catégorie</dt>
                                            <dd>{{ $decaissement->motif }}</dd>
                                            
                                            <dt>Bénéficiaire</dt>
                                            <dd>{{ $decaissement->beneficiaire }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl>
                                            <dt>Méthode de paiement</dt>
                                            <dd>{{ $decaissement->methode_paiement }}</dd>
                                            
                                            <dt>Référence</dt>
                                            <dd>{{ $decaissement->reference ?? 'N/A' }}</dd>
                                            
                                            <dt>Statut</dt>
                                            <dd>{!! $decaissement->status_badge !!}</dd>
                                            
                                            <dt>Année scolaire</dt>
                                            <dd>{{ $decaissement->year }}</dd>
                                        </dl>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h6>Description</h6>
                                        <p>{{ $decaissement->description ?? 'Aucune description fournie.' }}</p>
                                    </div>
                                </div>
                                
                                @if($decaissement->details_bancaires)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h6>Détails bancaires</h6>
                                        <p>{{ $decaissement->details_bancaires }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-transparent header-elements-inline">
                                <h6 class="card-title">Informations complémentaires</h6>
                            </div>
                            
                            <div class="card-body">
                                <dl>
                                    <dt>Créé par</dt>
                                    <dd>{{ $decaissement->user->name ?? 'Inconnu' }}</dd>
                                    
                                    <dt>Date de création</dt>
                                    <dd>{{ $decaissement->created_at->format('d/m/Y H:i') }}</dd>
                                    
                                    <dt>Dernière modification</dt>
                                    <dd>{{ $decaissement->updated_at->format('d/m/Y H:i') }}</dd>
                                </dl>
                                
                                @if($decaissement->piece)
                                <div class="mt-3">
                                    <h6>Pièce justificative</h6>
                                    <a href="{{ route('decaissements.download_piece', $decaissement->id) }}" class="btn btn-light btn-block">
                                        <i class="icon-file-download mr-2"></i> Télécharger la pièce
                                    </a>
                                </div>
                                @endif
                                
                                <div class="mt-3">
                                    <h6>Actions</h6>
                                    <div class="btn-group btn-group-justified">
                                        <a href="{{ route('decaissements.edit', $decaissement->id) }}" class="btn btn-primary">
                                            <i class="icon-pencil mr-2"></i> Modifier
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                            <i class="icon-trash mr-2"></i> Supprimer
                                        </button>
                                    </div>
                                    
                                    <button type="button" class="btn btn-light btn-block mt-2" data-toggle="modal" data-target="#status-modal">
                                        <i class="icon-checkmark3 mr-2"></i> Changer le statut
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer le statut -->
<div id="status-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le statut</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('decaissements.update_status', $decaissement->id) }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Statut:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="en_attente" {{ $decaissement->status == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="approuve" {{ $decaissement->status == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejete" {{ $decaissement->status == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="delete-form" action="{{ route('decaissements.destroy', $decaissement->id) }}" method="post" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('page_scripts')
<script>
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
