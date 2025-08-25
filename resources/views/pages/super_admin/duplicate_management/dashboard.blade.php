@extends('layouts.master')
@section('page_title', 'Gestion des doublons - Tableau de bord')
@section('content')

<div class="content">
    
    <!-- Dashboard Header -->
    <div class="card bg-primary text-white">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">
                <i class="icon-shield-check mr-2"></i>
                Système de Protection contre les Doublons
            </h5>
            <div class="header-elements">
                <span class="badge badge-light">
                    Protection Active
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="d-flex align-items-center">
                        <i class="icon-stats-bars3 icon-2x text-white-75 mr-3"></i>
                        <div>
                            <div class="font-size-h3 font-weight-semibold">{{ $stats['overall']->total_attempts ?? 0 }}</div>
                            <div class="text-white-75">Tentatives totales (30j)</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="d-flex align-items-center">
                        <i class="icon-shield-notice icon-2x text-white-75 mr-3"></i>
                        <div>
                            <div class="font-size-h3 font-weight-semibold">{{ $stats['overall']->blocked_attempts ?? 0 }}</div>
                            <div class="text-white-75">Doublons bloqués</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="d-flex align-items-center">
                        <i class="icon-checkmark3 icon-2x text-white-75 mr-3"></i>
                        <div>
                            <div class="font-size-h3 font-weight-semibold">{{ $stats['overall']->allowed_attempts ?? 0 }}</div>
                            <div class="text-white-75">Opérations autorisées</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="d-flex align-items-center">
                        <i class="icon-users icon-2x text-white-75 mr-3"></i>
                        <div>
                            <div class="font-size-h3 font-weight-semibold">{{ $stats['overall']->unique_users ?? 0 }}</div>
                            <div class="text-white-75">Utilisateurs actifs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="btn-group btn-group-justified">
                <a href="{{ route('duplicate.logs') }}" class="btn btn-light">
                    <i class="icon-list mr-2"></i>Journaux des doublons
                </a>
                <a href="{{ route('duplicate.locks') }}" class="btn btn-light">
                    <i class="icon-lock mr-2"></i>Verrous actifs
                </a>
                <a href="{{ route('duplicate.report') }}" class="btn btn-light">
                    <i class="icon-file-text mr-2"></i>Rapport complet
                </a>
                <button type="button" class="btn btn-light" onclick="searchDuplicates()">
                    <i class="icon-search4 mr-2"></i>Rechercher doublons
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistics by Table -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Statistiques par table</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height" id="table-stats-chart"></div>
                    </div>
                    
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Table</th>
                                    <th>Bloqués</th>
                                    <th>Autorisés</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['by_table'] as $table => $tableStats)
                                <tr>
                                    <td class="font-weight-semibold">{{ ucfirst(str_replace('_', ' ', $table)) }}</td>
                                    <td>
                                        <span class="badge badge-danger">
                                            {{ $tableStats->where('status', 'blocked')->sum('count') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ $tableStats->where('status', 'allowed')->sum('count') }}
                                        </span>
                                    </td>
                                    <td>{{ $tableStats->sum('count') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Critical Events -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Événements critiques récents</h6>
                    <div class="header-elements">
                        <span class="badge badge-pill badge-warning">{{ count($recentCritical) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($recentCritical) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentCritical->take(5) as $event)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ ucfirst($event->table_name) }}</h6>
                                    <small>{{ \Carbon\Carbon::parse($event->attempted_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge badge-danger">{{ $event->status }}</span>
                                    @if($event->user_id)
                                        Utilisateur: {{ $event->user_id }}
                                    @endif
                                </p>
                                @if($event->reason)
                                <small>{{ $event->reason }}</small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        
                        @if(count($recentCritical) > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('duplicate.logs') }}?status=blocked" class="btn btn-sm btn-outline-primary">
                                Voir tous les événements critiques
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="icon-checkmark-circle icon-3x mb-3"></i>
                            <h6>Aucun événement critique</h6>
                            <p>Système fonctionnant normalement</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Patterns Detection -->
    @if(count($patterns['frequent_duplicators']) > 0 || count($patterns['rapid_attempts']) > 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white header-elements-inline">
                    <h6 class="card-title">
                        <i class="icon-warning mr-2"></i>
                        Modèles de doublons détectés
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(count($patterns['frequent_duplicators']) > 0)
                        <div class="col-lg-6">
                            <h6>Utilisateurs avec doublons fréquents</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Utilisateur</th>
                                            <th>Table</th>
                                            <th>Tentatives</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($patterns['frequent_duplicators']->take(5) as $duplicator)
                                        <tr>
                                            <td>{{ $duplicator->user_id }}</td>
                                            <td>{{ $duplicator->table_name }}</td>
                                            <td>
                                                <span class="badge badge-warning">{{ $duplicator->attempt_count }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        
                        @if(count($patterns['rapid_attempts']) > 0)
                        <div class="col-lg-6">
                            <h6>Tentatives rapides détectées</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Utilisateur</th>
                                            <th>Table</th>
                                            <th>Tentatives rapides</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($patterns['rapid_attempts']->take(5) as $rapid)
                                        <tr>
                                            <td>{{ $rapid->user_id }}</td>
                                            <td>{{ $rapid->table_name }}</td>
                                            <td>
                                                <span class="badge badge-danger">{{ $rapid->rapid_attempts }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction Locks Status -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">État des verrous de transaction</h6>
                    <div class="header-elements">
                        <button type="button" class="btn btn-sm btn-primary" onclick="refreshLockStatus()">
                            <i class="icon-reload-alt mr-1"></i>Actualiser
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="card bg-success">
                                <div class="card-body text-center">
                                    <h3 class="mb-0 text-white">{{ $lockStats['summary']->total_active_locks ?? 0 }}</h3>
                                    <div class="text-white-75">Verrous actifs</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="card bg-info">
                                <div class="card-body text-center">
                                    <h3 class="mb-0 text-white">{{ $lockStats['summary']->unique_users ?? 0 }}</h3>
                                    <div class="text-white-75">Utilisateurs</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            @if(isset($lockStats['by_operation']) && count($lockStats['by_operation']) > 0)
                            <h6>Types d'opérations verrouillées</h6>
                            <div class="progress-group">
                                @foreach($lockStats['by_operation'] as $operation => $count)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ ucfirst($operation) }}</span>
                                    <span class="badge badge-primary">{{ $count }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Duplicates Modal -->
<div class="modal fade" id="duplicateSearchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechercher des doublons</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Table à analyser</label>
                    <select class="form-control" id="duplicateTable">
                        <option value="">Toutes les tables</option>
                        <option value="student_records">Enregistrements d'étudiants</option>
                        <option value="payment_records">Enregistrements de paiement</option>
                        <option value="receipts">Reçus</option>
                        <option value="marks">Notes</option>
                    </select>
                </div>
                <div id="duplicateResults" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="performDuplicateSearch()">
                    <i class="icon-search4 mr-2"></i>Rechercher
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function searchDuplicates() {
    $('#duplicateSearchModal').modal('show');
}

function performDuplicateSearch() {
    const table = $('#duplicateTable').val();
    const resultsDiv = $('#duplicateResults');
    
    resultsDiv.html('<div class="text-center"><i class="icon-spinner spinner mr-2"></i>Recherche en cours...</div>');
    
    $.get('{{ route("duplicate.search") }}', { table: table })
        .done(function(response) {
            if (response.success) {
                displayDuplicateResults(response.duplicates, response.table);
            } else {
                resultsDiv.html('<div class="alert alert-danger">Erreur lors de la recherche</div>');
            }
        })
        .fail(function() {
            resultsDiv.html('<div class="alert alert-danger">Erreur de connexion</div>');
        });
}

function displayDuplicateResults(duplicates, table) {
    const resultsDiv = $('#duplicateResults');
    
    if (table && duplicates.length > 0) {
        let html = '<div class="alert alert-info">Doublons trouvés dans ' + table + ':</div>';
        html += '<div class="table-responsive"><table class="table table-sm table-striped">';
        html += '<thead><tr><th>IDs dupliqués</th><th>Nombre</th><th>Action</th></tr></thead><tbody>';
        
        duplicates.forEach(function(duplicate) {
            html += '<tr>';
            html += '<td>' + duplicate.duplicate_ids + '</td>';
            html += '<td><span class="badge badge-warning">' + duplicate.duplicate_count + '</span></td>';
            html += '<td><button class="btn btn-sm btn-danger" onclick="confirmRemoveDuplicates(\'' + table + '\', \'' + duplicate.duplicate_ids + '\')">Supprimer</button></td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        resultsDiv.html(html);
    } else if (table) {
        resultsDiv.html('<div class="alert alert-success">Aucun doublon trouvé dans ' + table + '</div>');
    } else {
        // All tables
        let hasAnyDuplicates = false;
        let html = '';
        
        Object.keys(duplicates).forEach(function(tableName) {
            if (duplicates[tableName].length > 0) {
                hasAnyDuplicates = true;
                html += '<h6>' + tableName.replace('_', ' ') + '</h6>';
                html += '<div class="table-responsive"><table class="table table-sm table-striped mb-3">';
                html += '<thead><tr><th>IDs dupliqués</th><th>Nombre</th><th>Action</th></tr></thead><tbody>';
                
                duplicates[tableName].forEach(function(duplicate) {
                    html += '<tr>';
                    html += '<td>' + duplicate.duplicate_ids + '</td>';
                    html += '<td><span class="badge badge-warning">' + duplicate.duplicate_count + '</span></td>';
                    html += '<td><button class="btn btn-sm btn-danger" onclick="confirmRemoveDuplicates(\'' + tableName + '\', \'' + duplicate.duplicate_ids + '\')">Supprimer</button></td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
            }
        });
        
        if (hasAnyDuplicates) {
            resultsDiv.html(html);
        } else {
            resultsDiv.html('<div class="alert alert-success">Aucun doublon trouvé dans aucune table</div>');
        }
    }
}

function confirmRemoveDuplicates(table, duplicateIds) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ces doublons ? Cette action est irréversible.')) {
        const ids = duplicateIds.split(',').map(id => parseInt(id));
        const keepId = ids[0]; // Keep the first (oldest) record
        
        $.post('{{ route("duplicate.remove") }}', {
            _token: '{{ csrf_token() }}',
            table: table,
            duplicate_ids: ids,
            keep_id: keepId
        })
        .done(function(response) {
            if (response.success) {
                alert('Doublons supprimés avec succès');
                performDuplicateSearch(); // Refresh results
            } else {
                alert('Erreur: ' + response.message);
            }
        })
        .fail(function() {
            alert('Erreur de connexion');
        });
    }
}

function refreshLockStatus() {
    location.reload();
}

// Auto-refresh every 5 minutes
setInterval(function() {
    refreshLockStatus();
}, 300000);
</script>
@endsection