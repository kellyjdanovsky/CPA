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
                {{-- Filtres --}}
                <form method="GET" action="{{ route('decaissements.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_debut">Date début</label>
                                <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_fin">Date fin</label>
                                <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status_filter">Statut</label>
                                <select name="status_filter" id="status_filter" class="form-control select">
                                    <option value="">Tous</option>
                                    <option value="en_attente" {{ request('status_filter') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="approuve" {{ request('status_filter') == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                                    <option value="rejete" {{ request('status_filter') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="projet_filter">Projet</label>
                                <select name="projet_filter" id="projet_filter" class="form-control select">
                                    <option value="">Tous</option>
                                    @foreach($projets ?? [] as $projet)
                                        <option value="{{ $projet->id }}" {{ request('projet_filter') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="user_filter">Créé par</label>
                                <select name="user_filter" id="user_filter" class="form-control select">
                                    <option value="">Tous</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ request('user_filter') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class="btn btn-info btn-block">Filtrer</button>
                        </div>
                    </div>
                </form>

                <div class="row mb-3">
                    <div class="col-md-6">
                        {{-- La barre de recherche peut être conservée ou intégrée aux filtres --}}
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-input" placeholder="Rechercher dans les résultats...">
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
                                <th>Projet</th> {{-- Nouvelle colonne --}}
                                <th>Créé par</th> {{-- Nouvelle colonne --}}
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
                                <td>{{ $d->projet->nom ?? 'N/A' }}</td> {{-- Affichage du projet --}}
                                <td>{{ $d->user->name ?? 'Inconnu' }}</td> {{-- Affichage de l'utilisateur --}}
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
        // Initialiser Select2 si utilisé
        if ($.fn.select2) {
            $('.select').select2();
        }

        // Recherche instantanée dans les résultats affichés
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#decaissements-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Bouton de recherche (pour la recherche instantanée)
        $('#search-button').on('click', function() {
            $('#search-input').trigger('keyup'); // Déclenche la recherche instantanée
        });

        // Les filtres sont gérés par la soumission du formulaire GET
        // Pas besoin de JS supplémentaire pour le filtrage principal
    });

    // Confirmation de suppression
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
            var form = $('#delete-form');
            // Assurez-vous que la route est correcte
            form.attr('action', '{{ url("decaissements") }}/' + id);
            form.submit();
        }
    }
</script>
@endsection
