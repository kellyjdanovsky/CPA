@extends('layouts.master')
@section('page_title', 'Gestion des Décaissements (OP)')

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">
            <i class="icon-file-text2 mr-2"></i> Gestion des Ordres de Paiement (OP) - Année {{ $selected_year }}
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('payments.decaissements.create') }}" class="btn btn-primary">
                    <i class="icon-plus2 mr-2"></i> Nouvel OP
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Filtres -->
        <form id="filter-form" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date début" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date fin" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-control">
                        <option value="">Tous statuts</option>
                        <option value="EN_ATTENTE" {{ request('statut') == 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
                        <option value="APPROUVE" {{ request('statut') == 'APPROUVE' ? 'selected' : '' }}>Approuvé</option>
                        <option value="PAYE" {{ request('statut') == 'PAYE' ? 'selected' : '' }}>Payé</option>
                        <option value="ANNULE" {{ request('statut') == 'ANNULE' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="beneficiaire" class="form-control" placeholder="Bénéficiaire" value="{{ request('beneficiaire') }}">
                </div>
                <div class="col-md-2">
                    <select name="projet_rubrique" class="form-control">
                        <option value="">Tous projets</option>
                        @foreach($projets_rubriques as $projet)
                            <option value="{{ $projet }}" {{ request('projet_rubrique') == $projet ? 'selected' : '' }}>
                                {{ $projet }}
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
                <div class="card bg-info">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['en_attente_count'] }}</h3>
                        <div class="text-white-75">En Attente</div>
                        <small class="text-white-50">{{ number_format($stats['en_attente_montant'], 0, ',', ' ') }} Ar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['approuve_count'] }}</h3>
                        <div class="text-white-75">Approuvés</div>
                        <small class="text-white-50">{{ number_format($stats['approuve_montant'], 0, ',', ' ') }} Ar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['paye_count'] }}</h3>
                        <div class="text-white-75">Payés</div>
                        <small class="text-white-50">{{ number_format($stats['paye_montant'], 0, ',', ' ') }} Ar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger">
                    <div class="card-body text-center">
                        <h3 class="text-white mb-0">{{ $stats['annule_count'] }}</h3>
                        <div class="text-white-75">Annulés</div>
                        <small class="text-white-50">{{ number_format($stats['annule_montant'], 0, ',', ' ') }} Ar</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="mb-3">
            <div class="btn-group">
                <a href="{{ route('payments.decaissements.export_excel') }}{{ '?' . http_build_query(request()->all()) }}" 
                   class="btn btn-success">
                    <i class="icon-file-excel mr-2"></i> Export Excel
                </a>
                <button type="button" class="btn btn-primary" onclick="printMultiple()">
                    <i class="icon-printer mr-2"></i> Imprimer Sélection
                </button>
            </div>
        </div>

        <!-- Table des décaissements -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Date</th>
                        <th>Référence OP</th>
                        <th>Bénéficiaire</th>
                        <th>Montant</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($decaissements as $decaissement)
                    <tr>
                        <td><input type="checkbox" class="row-select" value="{{ $decaissement->id }}"></td>
                        <td>{{ $decaissement->date_decaissement->format('d/m/Y') }}</td>
                        <td><span class="badge badge-primary">{{ $decaissement->reference_op }}</span></td>
                        <td>{{ $decaissement->beneficiaire }}</td>
                        <td><strong>{{ number_format($decaissement->montant, 0, ',', ' ') }} Ar</strong></td>
                        <td title="{{ $decaissement->motif }}">
                            {{ Str::limit($decaissement->motif, 50) }}
                        </td>
                        <td>
                            @php
                                $statusClass = [
                                    'EN_ATTENTE' => 'badge-info',
                                    'APPROUVE' => 'badge-warning', 
                                    'PAYE' => 'badge-success',
                                    'ANNULE' => 'badge-danger'
                                ][$decaissement->statut] ?? 'badge-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ $decaissement->statut }}
                            </span>
                        </td>
                        <td>
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('payments.decaissements.show', $decaissement->id) }}" class="dropdown-item">
                                            <i class="icon-eye"></i> Voir
                                        </a>
                                        
                                        @if($decaissement->statut == 'EN_ATTENTE')
                                            <a href="{{ route('payments.decaissements.edit', $decaissement->id) }}" class="dropdown-item">
                                                <i class="icon-pencil"></i> Modifier
                                            </a>
                                            <a href="#" onclick="approveOP({{ $decaissement->id }})" class="dropdown-item">
                                                <i class="icon-checkmark"></i> Approuver
                                            </a>
                                            <a href="#" onclick="cancelOP({{ $decaissement->id }})" class="dropdown-item text-danger">
                                                <i class="icon-cross"></i> Annuler
                                            </a>
                                        @endif
                                        
                                        @if($decaissement->statut == 'APPROUVE')
                                            <a href="#" onclick="markAsPaid({{ $decaissement->id }})" class="dropdown-item">
                                                <i class="icon-checkmark3"></i> Marquer Payé
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('payments.decaissements.print_op', $decaissement->id) }}" class="dropdown-item" target="_blank">
                                            <i class="icon-printer"></i> Imprimer OP
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucun décaissement trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $decaissements->links() }}
    </div>
</div>

<script>
// Sélection multiple
$('#select-all').change(function() {
    $('.row-select').prop('checked', this.checked);
});

function approveOP(id) {
    if (confirm('Approuver cet ordre de paiement ?')) {
        $.post('{{ url("payments/decaissements/approve") }}/' + id, {
            _token: '{{ csrf_token() }}'
        }).done(function() {
            location.reload();
        }).fail(function() {
            alert('Erreur lors de l\'approbation');
        });
    }
}

function markAsPaid(id) {
    if (confirm('Marquer cet OP comme payé ?')) {
        $.post('{{ url("payments/decaissements/mark-paid") }}/' + id, {
            _token: '{{ csrf_token() }}'
        }).done(function() {
            location.reload();
        }).fail(function() {
            alert('Erreur lors de la mise à jour');
        });
    }
}

function cancelOP(id) {
    if (confirm('Annuler cet ordre de paiement ?')) {
        $.post('{{ url("payments/decaissements/cancel") }}/' + id, {
            _token: '{{ csrf_token() }}'
        }).done(function() {
            location.reload();
        }).fail(function() {
            alert('Erreur lors de l\'annulation');
        });
    }
}

function printMultiple() {
    var selected = $('.row-select:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length == 0) {
        alert('Veuillez sélectionner au moins un OP');
        return;
    }
    
    var form = $('<form method="POST" action="{{ route("payments.decaissements.print_multiple") }}" target="_blank">');
    form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
    selected.forEach(function(id) {
        form.append('<input type="hidden" name="ids[]" value="' + id + '">');
    });
    $('body').append(form);
    form.submit();
    form.remove();
}
</script>

@endsection