@extends('layouts.master')
@section('page_title', 'Gestion des Dépenses')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Gestion des Dépenses</h6>
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
                <a href="{{ route('decaissements.index') }}" class="nav-link active">
                    <i class="icon-list3 mr-2"></i> Liste des Dépenses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('decaissements.create') }}" class="nav-link">
                    <i class="icon-plus2 mr-2"></i> Ajouter une Dépense
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-decaissements">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-input" placeholder="Rechercher...">
                            <div class="input-group-append">
                                <button class="btn btn-light" type="button" id="search-button">
                                    <i class="icon-search4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('decaissements.create') }}" class="btn btn-primary">
                            <i class="icon-plus2 mr-2"></i> Nouvelle Dépense
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="decaissements-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Motif</th>
                                <th>Bénéficiaire</th>
                                <th>Montant</th>
                                <th>Méthode</th>
                                <th>Référence</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($decaissements as $d)
                            <tr>
                                <td>{{ $d->formatted_date }}</td>
                                <td>{{ $d->motif }}</td>
                                <td>{{ $d->beneficiaire }}</td>
                                <td>{{ $d->formatted_montant }}</td>
                                <td>{{ $d->methode_paiement }}</td>
                                <td>{{ $d->reference ?? 'N/A' }}</td>
                                <td>{!! $d->status_badge !!}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="{{ route('decaissements.show', $d->id) }}" class="dropdown-item">
                                                    <i class="icon-eye"></i> Voir Détails
                                                </a>
                                                <a href="{{ route('decaissements.edit', $d->id) }}" class="dropdown-item">
                                                    <i class="icon-pencil"></i> Modifier
                                                </a>
                                                @if($d->piece)
                                                <a href="{{ route('decaissements.download_piece', $d->id) }}" class="dropdown-item">
                                                    <i class="icon-file-download"></i> Télécharger Pièce
                                                </a>
                                                @endif
                                                <a href="#" class="dropdown-item" onclick="confirmDelete({{ $d->id }})">
                                                    <i class="icon-trash"></i> Supprimer
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#status-modal-{{ $d->id }}">
                                                    <i class="icon-checkmark3"></i> Changer Statut
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal pour changer le statut -->
                                    <div id="status-modal-{{ $d->id }}" class="modal fade" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Changer le statut</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <form action="{{ route('decaissements.update_status', $d->id) }}" method="post">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="status">Statut:</label>
                                                            <select name="status" id="status" class="form-control">
                                                                <option value="en_attente" {{ $d->status == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                                                <option value="approuve" {{ $d->status == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                                                                <option value="rejete" {{ $d->status == 'rejete' ? 'selected' : '' }}>Rejeté</option>
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
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="delete-form" method="post" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Recherche dans le tableau
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#decaissements-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        // Bouton de recherche
        $('#search-button').on('click', function() {
            var value = $('#search-input').val().toLowerCase();
            $('#decaissements-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    
    // Confirmation de suppression
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
            var form = $('#delete-form');
            form.attr('action', '/decaissements/' + id);
            form.submit();
        }
    }
</script>
@endsection
