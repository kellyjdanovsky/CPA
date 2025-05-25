@extends('layouts.master')
@section('page_title', 'Gestion des Projets')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Gestion des Projets</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Rechercher un projet...">
                    <div class="input-group-append">
                        <button class="btn btn-light" type="button" id="search-button">
                            <i class="icon-search4"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('projets.create') }}" class="btn btn-primary">
                    <i class="icon-plus2 mr-2"></i> Nouveau Projet
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="projets-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Budget</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projets as $p)
                    <tr>
                        <td>{{ $p->nom }}</td>
                        <td>{{ Str::limit($p->description, 50) }}</td>
                        <td>{{ $p->date_debut ? $p->date_debut->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $p->date_fin ? $p->date_fin->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $p->budget ? number_format($p->budget, 0, ',', ' ') . ' Ar' : 'N/A' }}</td>
                        <td>
                            @if($p->statut == 'actif')
                                <span class="badge badge-success">Actif</span>
                            @elseif($p->statut == 'inactif')
                                <span class="badge badge-warning">Inactif</span>
                            @elseif($p->statut == 'termine')
                                <span class="badge badge-secondary">Terminé</span>
                            @else
                                <span class="badge badge-info">{{ $p->statut }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('projets.show', $p->id) }}" class="dropdown-item">
                                            <i class="icon-eye"></i> Voir Détails
                                        </a>
                                        <a href="{{ route('projets.edit', $p->id) }}" class="dropdown-item">
                                            <i class="icon-pencil"></i> Modifier
                                        </a>
                                        <a href="#" class="dropdown-item" onclick="confirmDelete({{ $p->id }})">
                                            <i class="icon-trash"></i> Supprimer
                                        </a>
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

<!-- Formulaire de suppression caché -->
<form id="delete-form" method="post" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Recherche instantanée dans les résultats affichés
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#projets-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Bouton de recherche (pour la recherche instantanée)
        $('#search-button').on('click', function() {
            $('#search-input').trigger('keyup'); // Déclenche la recherche instantanée
        });
    });

    // Confirmation de suppression
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
            var form = $('#delete-form');
            form.attr('action', '{{ url("projets") }}/' + id);
            form.submit();
        }
    }
</script>
@endsection
