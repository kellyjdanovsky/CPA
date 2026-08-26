@extends('layouts.master')
@section('page_title', 'Gestion des Décaissements (OP)')

@push('page_css')
<style>
    /* === PREMIUM DASHBOARD STYLES === */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --hover-transform: translateY(-5px);
    }

    .decaissement-header {
        background: var(--primary-gradient);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.25);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .decaissement-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    .decaissement-header h4 {
        color: #fff;
        font-weight: 700;
        font-size: 1.75rem;
        letter-spacing: -0.5px;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .decaissement-header .subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-nouvel-op {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        color: #fff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-nouvel-op:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        color: #fff;
    }

    /* Stats Cards with Glassmorphism feel */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: currentColor;
        opacity: 0.8;
    }

    .stat-card:hover {
        transform: var(--hover-transform);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .stat-card.pending { color: #f6ad55; }
    .stat-card.approved { color: #4299e1; }
    .stat-card.paid { color: #48bb78; }
    .stat-card.cancelled { color: #f56565; }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        background: currentColor;
        color: white;
        opacity: 0.9;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 800;
        color: #2d3748;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-amount {
        font-size: 1.1rem;
        color: inherit;
        font-weight: 700;
        margin-top: 0.5rem;
        opacity: 0.9;
    }

    /* Modern Filter Card */
    .filter-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(0,0,0,0.03);
    }

    .filter-card .form-control, .filter-card .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background-color: #f8fafc;
    }

    .filter-card .form-control:focus, .filter-card .form-select:focus {
        border-color: #667eea;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-filter {
        background: #2d3748;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-filter:hover {
        background: #1a202c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Enhanced Action Bar */
    .action-bar {
        background: #fff;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .btn-action {
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border: none;
    }

    .btn-action.export {
        background: rgba(72, 187, 120, 0.1);
        color: #38a169;
    }
    .btn-action.export:hover {
        background: #38a169;
        color: white;
    }

    .btn-action.print {
        background: rgba(66, 153, 225, 0.1);
        color: #3182ce;
    }
    .btn-action.print:hover {
        background: #3182ce;
        color: white;
    }

    /* Premium Data Table */
    .data-table-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .table-modern {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        background: #f8fafc;
        padding: 1.25rem 1rem;
        font-weight: 700;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #edf2f7;
    }

    .table-modern tbody tr {
        transition: all 0.2s;
    }

    .table-modern tbody tr:hover {
        background-color: #f7fafc;
        transform: scale(1.005);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        z-index: 10;
        position: relative;
    }

    .table-modern tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
        color: #2d3748;
        font-size: 0.95rem;
    }

    /* Badges & Elements */
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    
    .status-badge.pending { background: #fffaf0; color: #ed8936; border: 1px solid #feebc8; }
    .status-badge.approved { background: #ebf8ff; color: #4299e1; border: 1px solid #bee3f8; }
    .status-badge.paid { background: #f0fff4; color: #48bb78; border: 1px solid #c6f6d5; }
    .status-badge.cancelled { background: #fff5f5; color: #f56565; border: 1px solid #fed7d7; }

    .amount-display {
        font-family: 'Monaco', 'Consolas', monospace;
        font-weight: 600;
        color: #2d3748;
        background: #f7fafc;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
    }

    .ref-badge {
        background: #e2e8f0;
        color: #4a5568;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.85rem;
        letter-spacing: -0.5px;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .decaissement-header, .stats-grid, .filter-card, .action-bar, .data-table-card {
        animation: fadeIn 0.4s ease-out forwards;
    }

    .stats-grid { animation-delay: 0.1s; }
    .filter-card { animation-delay: 0.2s; }
    .data-table-card { animation-delay: 0.3s; }

    /* Custom Checkbox */
    .custom-checkbox-modern {
        appearance: none;
        background-color: #fff;
        margin: 0;
        font: inherit;
        color: #667eea;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #cbd5e0;
        border-radius: 4px;
        display: grid;
        place-content: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .custom-checkbox-modern::before {
        content: "";
        width: 0.65rem;
        height: 0.65rem;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em white;
        transform-origin: center;
        clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    }

    .custom-checkbox-modern:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .custom-checkbox-modern:checked::before {
        transform: scale(1);
    }

</style>
@endpush

@section('content')

@include('pages.support_team.payments.partials.header_tabs')

<!-- Header Section -->
<div class="decaissement-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="icon-file-text2 mr-2"></i> Gestion des Ordres de Paiement (OP)</h4>
        <span class="subtitle">Année scolaire {{ $selected_year }}</span>
    </div>
    <a href="{{ route('payments.decaissements.create') }}" class="btn btn-nouvel-op">
        <i class="icon-plus2 mr-2"></i> Nouvel OP
    </a>
</div>

<!-- Statistics Cards -->
@if(isset($statistics))
<div class="stats-grid">
    <div class="stat-card pending">
        <div class="stat-icon"><i class="icon-hourglass"></i></div>
        <div class="stat-value">{{ $statistics['en_attente_count'] ?? 0 }}</div>
        <div class="stat-label">En Attente</div>
        <div class="stat-amount">{{ number_format($statistics['en_attente_montant'] ?? 0, 0, ',', ' ') }} Ar</div>
    </div>
    <div class="stat-card approved">
        <div class="stat-icon"><i class="icon-checkmark-circle"></i></div>
        <div class="stat-value">{{ $statistics['approuve_count'] ?? 0 }}</div>
        <div class="stat-label">Approuvés</div>
        <div class="stat-amount">{{ number_format($statistics['approuve_montant'] ?? 0, 0, ',', ' ') }} Ar</div>
    </div>
    <div class="stat-card paid">
        <div class="stat-icon"><i class="icon-checkmark3"></i></div>
        <div class="stat-value">{{ $statistics['paye_count'] ?? 0 }}</div>
        <div class="stat-label">Payés</div>
        <div class="stat-amount">{{ number_format($statistics['paye_montant'] ?? 0, 0, ',', ' ') }} Ar</div>
    </div>
    <div class="stat-card cancelled">
        <div class="stat-icon"><i class="icon-cancel-circle2"></i></div>
        <div class="stat-value">{{ $statistics['annule_count'] ?? 0 }}</div>
        <div class="stat-label">Annulés</div>
        <div class="stat-amount">{{ number_format($statistics['annule_montant'] ?? 0, 0, ',', ' ') }} Ar</div>
    </div>
</div>
@endif

<!-- Filter Section -->
<div class="filter-card">
    <form id="filter-form" class="row align-items-end g-3">
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Date début</label>
            <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Date fin</label>
            <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Statut</label>
            <select name="statut" class="form-control">
                <option value="">Tous les statuts</option>
                <option value="EN_ATTENTE" {{ request('statut') == 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
                <option value="APPROUVE" {{ request('statut') == 'APPROUVE' ? 'selected' : '' }}>Approuvé</option>
                <option value="PAYE" {{ request('statut') == 'PAYE' ? 'selected' : '' }}>Payé</option>
                <option value="ANNULE" {{ request('statut') == 'ANNULE' ? 'selected' : '' }}>Annulé</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Bénéficiaire</label>
            <input type="text" name="beneficiaire" class="form-control" placeholder="Rechercher..." value="{{ request('beneficiaire') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Projet/Rubrique</label>
            <select name="projet_rubrique" class="form-control">
                <option value="">Tous les projets</option>
                @foreach($projets_rubriques as $projet)
                    <option value="{{ $projet }}" {{ request('projet_rubrique') == $projet ? 'selected' : '' }}>
                        {{ $projet }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-filter flex-grow-1">
                <i class="icon-search4"></i> Filtrer
            </button>
            <a href="{{ route('payments.decaissements.index') }}" class="btn btn-reset" data-tooltip="Réinitialiser">
                <i class="icon-reset"></i>
            </a>
        </div>
    </form>
</div>

<!-- Action Bar -->
<div class="action-bar">
    <a href="{{ route('payments.decaissements.export_excel') }}{{ '?' . http_build_query(request()->all()) }}" 
       class="btn btn-action export">
        <i class="icon-file-excel"></i> Export Excel
    </a>
    <button type="button" class="btn btn-action print" onclick="printMultiple()">
        <i class="icon-printer"></i> Imprimer Sélection
    </button>
    <span class="text-muted align-self-center ml-auto" id="selection-count" style="display: none;">
        <strong id="count-value">0</strong> OP sélectionné(s)
    </span>
</div>

<!-- Data Table -->
<div class="data-table-card">
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="select-all" class="custom-checkbox-modern">
                    </th>
                    <th>Date</th>
                    <th>Référence OP</th>
                    <th>Bénéficiaire</th>
                    <th>Montant</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($decaissements as $decaissement)
                <tr>
                    <td>
                        <input type="checkbox" class="row-select custom-checkbox-modern" value="{{ $decaissement->id }}">
                    </td>
                    <td>
                        <span class="text-nowrap">{{ $decaissement->date_decaissement->format('d/m/Y') }}</span>
                    </td>
                    <td>
                        <span class="ref-badge">{{ $decaissement->reference_op }}</span>
                    </td>
                    <td>{{ $decaissement->beneficiaire }}</td>
                    <td>
                        <span class="amount-display">{{ number_format($decaissement->montant, 0, ',', ' ') }} Ar</span>
                    </td>
                    <td title="{{ $decaissement->motif }}">
                        {{ \Illuminate\Support\Str::limit($decaissement->motif, 40) }}
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'EN_ATTENTE' => 'pending',
                                'APPROUVE' => 'approved', 
                                'PAYE' => 'paid',
                                'ANNULE' => 'cancelled'
                            ];
                            $statusLabels = [
                                'EN_ATTENTE' => 'En attente',
                                'APPROUVE' => 'Approuvé', 
                                'PAYE' => 'Payé',
                                'ANNULE' => 'Annulé'
                            ];
                        @endphp
                        <span class="status-badge {{ $statusClasses[$decaissement->statut] ?? '' }}">
                            {{ $statusLabels[$decaissement->statut] ?? $decaissement->statut }}
                        </span>
                    </td>
                    <td>
                        <div class="quick-actions">
                            <a href="{{ route('payments.decaissements.show', $decaissement->id) }}" 
                               class="quick-action-btn view" data-tooltip="Voir détails">
                                <i class="icon-eye"></i>
                            </a>
                            
                            @if($decaissement->statut == 'EN_ATTENTE')
                                <a href="{{ route('payments.decaissements.edit', $decaissement->id) }}" 
                                   class="quick-action-btn edit" data-tooltip="Modifier">
                                    <i class="icon-pencil"></i>
                                </a>
                                <button onclick="approveOP({{ $decaissement->id }})" 
                                        class="quick-action-btn approve" data-tooltip="Valider">
                                    <i class="icon-checkmark"></i>
                                </button>
                                <button onclick="cancelOP({{ $decaissement->id }})" 
                                        class="quick-action-btn cancel" data-tooltip="Annuler">
                                    <i class="icon-cross"></i>
                                </button>
                                <button onclick="deleteOP({{ $decaissement->id }})" 
                                        class="quick-action-btn delete" data-tooltip="Supprimer">
                                    <i class="icon-trash"></i>
                                </button>
                            @endif
                            
                            @if($decaissement->statut == 'APPROUVE')
                                <button onclick="markAsPaid({{ $decaissement->id }})" 
                                        class="quick-action-btn paid" data-tooltip="Marquer payé">
                                    <i class="icon-checkmark3"></i>
                                </button>
                            @endif
                            
                            <a href="{{ route('payments.decaissements.print_thermal', $decaissement->id) }}" 
                               class="quick-action-btn print" target="_blank" data-tooltip="Ticket 58mm" style="background: #eef2ff; color: #4f46e5; border-color: #c7d2fe;">
                                <i class="icon-newspaper2"></i>
                            </a>
                            <a href="{{ route('payments.decaissements.print_op', $decaissement->id) }}" 
                               class="quick-action-btn print" target="_blank" data-tooltip="Imprimer A4">
                                <i class="icon-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="icon-file-empty"></i>
                            <h5>Aucun décaissement trouvé</h5>
                            <p>Modifiez vos filtres ou créez un nouvel ordre de paiement</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($decaissements->hasPages())
    <div class="pagination-modern">
        {{ $decaissements->links() }}
    </div>
    @endif
</div>

@endsection

@push('page_js')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#select-all').change(function() {
        $('.row-select').prop('checked', this.checked);
        updateSelectionCount();
    });
    
    // Individual checkbox change
    $(document).on('change', '.row-select', function() {
        updateSelectionCount();
        
        // Update "select all" checkbox state
        var total = $('.row-select').length;
        var checked = $('.row-select:checked').length;
        $('#select-all').prop('checked', total === checked);
        $('#select-all').prop('indeterminate', checked > 0 && checked < total);
    });
    
    function updateSelectionCount() {
        var count = $('.row-select:checked').length;
        var $counter = $('#selection-count');
        
        if (count > 0) {
            $counter.show();
            $('#count-value').text(count);
        } else {
            $counter.hide();
        }
    }
    });


function approveOP(id) {
    Swal.fire({
        title: 'Approuver cet OP ?',
        text: 'Cette action validera cet ordre de paiement.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="icon-checkmark mr-1"></i> Approuver',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ url("payments/decaissements/approve") }}/' + id, {
                _token: '{{ csrf_token() }}'
            }).done(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Approuvé !',
                    text: 'L\'ordre de paiement a été approuvé.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }).fail(function() {
                Swal.fire('Erreur', 'Une erreur s\'est produite.', 'error');
            });
        }
    });
}

function markAsPaid(id) {
    Swal.fire({
        title: 'Marquer comme payé ?',
        text: 'Confirmer le paiement de cet ordre.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#00b894',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="icon-checkmark3 mr-1"></i> Confirmer le paiement',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ url("payments/decaissements/mark-paid") }}/' + id, {
                _token: '{{ csrf_token() }}'
            }).done(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Payé !',
                    text: 'L\'ordre de paiement est maintenant marqué comme payé.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }).fail(function() {
                Swal.fire('Erreur', 'Une erreur s\'est produite.', 'error');
            });
        }
    });
}

function cancelOP(id) {
    Swal.fire({
        title: 'Annuler cet OP ?',
        text: 'Cette action est irréversible !',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="icon-cross mr-1"></i> Annuler l\'OP',
        cancelButtonText: 'Non, garder'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ url("payments/decaissements/cancel") }}/' + id, {
                _token: '{{ csrf_token() }}'
            }).done(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Annulé',
                    text: 'L\'ordre de paiement a été annulé.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }).fail(function() {
                Swal.fire('Erreur', 'Une erreur s\'est produite.', 'error');
            });
        }
    });
}

function deleteOP(id) {
    Swal.fire({
        title: 'Supprimer cet OP ?',
        text: 'Cette action est irréversible et supprimera définitivement l\'enregistrement.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="icon-trash mr-1"></i> Supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("payments/decaissements") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Supprimé !',
                        text: 'L\'ordre de paiement a été supprimé.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    var msg = 'Une erreur s\'est produite.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Erreur', msg, 'error');
                }
            });
        }
    });
}

function printMultiple() {
    var selected = $('.row-select:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length == 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucune sélection',
            text: 'Veuillez sélectionner au moins un OP à imprimer.'
        });
        return;
    }
    
    var form = $('<form method="POST" action="{{ route("payments.decaissements.print_multiple") }}" target="_blank">');
    form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
    selected.forEach(function(id) {
        form.append('<input type="hidden" name="decaissement_ids[]" value="' + id + '">');
    });
    $('body').append(form);
    form.submit();
    form.remove();
}
</script>
@endpush