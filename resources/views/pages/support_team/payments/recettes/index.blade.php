@extends('layouts.master')
@section('page_title', 'Gestion des Recettes')

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">
            <i class="icon-coins mr-2"></i> Gestion des Recettes - Année {{ $selected_year }}
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('payments.recettes.create') }}" class="btn btn-primary">
                    <i class="icon-plus2 mr-2"></i> Nouvelle Recette
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Filtres -->
        <form id="filter-form" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date début" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date fin" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2">
                    <select name="type_recette" class="form-control">
                        <option value="">Tous types</option>
                        <option value="NORMAL" {{ request('type_recette') == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                        <option value="ADRA" {{ request('type_recette') == 'ADRA' ? 'selected' : '' }}>ADRA</option>
                        <option value="TEAM3" {{ request('type_recette') == 'TEAM3' ? 'selected' : '' }}>TEAM3</option>
                        <option value="DIVERS" {{ request('type_recette') == 'DIVERS' ? 'selected' : '' }}>Divers</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="class_id" class="form-control">
                        <option value="">Toutes classes</option>
                        @foreach($my_classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="icon-search4 mr-2"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>

        <!-- Statistiques -->
        @if(isset($stats))
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['total_recettes'] }}</h3>
                        <div class="text-white-75">Total Recettes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ number_format($stats['total_montant'], 0, ',', ' ') }} Ar</h3>
                        <div class="text-white-75">Montant Total</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['adra_count'] }}</h3>
                        <div class="text-white-75">ADRA</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['team3_count'] }}</h3>
                        <div class="text-white-75">TEAM3</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="mb-3">
            <div class="btn-group">
                <a href="{{ route('payments.recettes.export_excel') }}{{ '?' . http_build_query(request()->all()) }}" 
                   class="btn btn-success">
                    <i class="icon-file-excel mr-2"></i> Export Excel
                </a>
                <a href="{{ route('payments.recettes.export_pdf') }}{{ '?' . http_build_query(request()->all()) }}" 
                   class="btn btn-danger">
                    <i class="icon-file-pdf mr-2"></i> Export PDF
                </a>
                <button type="button" class="btn btn-info" onclick="syncReceipts()">
                    <i class="icon-sync mr-2"></i> Synchroniser
                </button>
            </div>
        </div>

        <!-- Table des recettes -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Étudiant</th>
                        <th>Classe</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Mode Paiement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recettes as $recette)
                    <tr>
                        <td>{{ $recette->date_recette->format('d/m/Y') }}</td>
                        <td><span class="badge badge-info">{{ $recette->reference_recette }}</span></td>
                        <td>
                            @if($recette->student)
                                {{ $recette->student->name }}
                            @else
                                {{ $recette->beneficiaire_nom }}
                            @endif
                        </td>
                        <td>
                            @if($recette->myClass)
                                {{ $recette->myClass->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $recette->type_recette == 'ADRA' ? 'primary' : ($recette->type_recette == 'TEAM3' ? 'success' : 'secondary') }}">
                                {{ $recette->type_recette }}
                            </span>
                        </td>
                        <td><strong>{{ number_format($recette->montant_encaisse, 0, ',', ' ') }} Ar</strong></td>
                        <td>{{ $recette->mode_paiement }}</td>
                        <td>
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('payments.recettes.show', $recette->id) }}" class="dropdown-item">
                                            <i class="icon-eye"></i> Voir
                                        </a>
                                        <a href="{{ route('payments.recettes.edit', $recette->id) }}" class="dropdown-item">
                                            <i class="icon-pencil"></i> Modifier
                                        </a>
                                        <a href="#" onclick="deleteRecette({{ $recette->id }})" class="dropdown-item text-danger">
                                            <i class="icon-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucune recette trouvée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $recettes->links() }}
    </div>
</div>

<script>
function syncReceipts() {
    if (confirm('Synchroniser les recettes avec les reçus existants ?')) {
        $.post('{{ route("payments.recettes.sync_receipts") }}', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            alert('Synchronisation terminée: ' + response.created_count + ' recettes créées');
            location.reload();
        }).fail(function() {
            alert('Erreur lors de la synchronisation');
        });
    }
}

function deleteRecette(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recette ?')) {
        $.ajax({
            url: '/payments/recettes/destroy/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                location.reload();
            },
            error: function() {
                alert('Erreur lors de la suppression');
            }
        });
    }
}
</script>

@endsection