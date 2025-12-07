@extends('layouts.master')
@section('page_title', 'Détails OP - ' . $decaissement->reference_op)

@push('page_css')
<style>
    /* === Professional Details Page Styling === */
    .page-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(30, 60, 114, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header h4 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .page-header .ref-badge {
        background: rgba(255,255,255,0.2);
        color: #fff;
        padding: 6px 16px;
        border-radius: 8px;
        font-family: 'Consolas', monospace;
        font-size: 1.1rem;
    }
    
    .header-actions {
        display: flex;
        gap: 12px;
    }
    
    .btn-header {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-header.back {
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
    
    .btn-header.back:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }
    
    .btn-header.print {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
        color: #fff;
        box-shadow: 0 4px 15px rgba(0, 114, 255, 0.4);
    }
    
    .btn-header.print:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 114, 255, 0.5);
    }
    
    /* Main Content Layout */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    
    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Detail Cards */
    .detail-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .detail-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 16px 24px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .detail-card-header i {
        font-size: 1.2rem;
        color: #667eea;
    }
    
    .detail-card-header h6 {
        margin: 0;
        font-weight: 600;
        color: #2d3436;
    }
    
    .detail-card-body {
        padding: 24px;
    }
    
    /* Info Table */
    .info-table {
        width: 100%;
    }
    
    .info-table tr {
        border-bottom: 1px solid #f1f3f5;
    }
    
    .info-table tr:last-child {
        border-bottom: none;
    }
    
    .info-table td {
        padding: 14px 0;
        vertical-align: top;
    }
    
    .info-table .label {
        width: 160px;
        font-weight: 600;
        color: #636e72;
        font-size: 0.9rem;
    }
    
    .info-table .value {
        color: #2d3436;
    }
    
    /* Amount Highlight */
    .amount-highlight {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        border: 2px solid #38a169;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 16px;
    }
    
    .amount-highlight .amount-value {
        font-size: 2rem;
        font-weight: 700;
        color: #22543d;
    }
    
    .amount-highlight .amount-words {
        color: #2f855a;
        font-style: italic;
        margin-top: 8px;
        font-size: 0.95rem;
    }
    
    /* File Attachment */
    .file-attachment {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
    }
    
    .file-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
    }
    
    .file-info h6 {
        margin: 0 0 4px;
        color: #2d3436;
    }
    
    .file-info a {
        color: #667eea;
        font-size: 0.9rem;
    }
    
    .file-status {
        margin-left: auto;
    }
    
    .badge-valid {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.1) 100%);
        color: #1e7e34;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-pending {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%);
        color: #b97500;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    /* Status Sidebar */
    .status-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .status-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 16px 24px;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
    }
    
    .status-card-header h6 {
        margin: 0;
        font-weight: 600;
        color: #2d3436;
    }
    
    .status-display {
        text-align: center;
        padding: 30px 24px;
    }
    
    .status-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 2rem;
    }
    
    .status-icon.pending {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.1) 100%);
        color: #17a2b8;
    }
    
    .status-icon.approved {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%);
        color: #ffc107;
    }
    
    .status-icon.paid {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.1) 100%);
        color: #28a745;
    }
    
    .status-icon.cancelled {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.1) 100%);
        color: #dc3545;
    }
    
    .status-label {
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .status-label.pending { color: #17a2b8; }
    .status-label.approved { color: #ffc107; }
    .status-label.paid { color: #28a745; }
    .status-label.cancelled { color: #dc3545; }
    
    /* Workflow Timeline */
    .workflow-timeline {
        padding: 0 24px 24px;
    }
    
    .timeline-item {
        display: flex;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #f1f3f5;
        position: relative;
    }
    
    .timeline-item:last-child {
        border-bottom: none;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 19px;
        top: 50px;
        bottom: -16px;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item:last-child::before {
        display: none;
    }
    
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #636e72;
        flex-shrink: 0;
        z-index: 1;
    }
    
    .timeline-icon.completed {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: #fff;
    }
    
    .timeline-content {
        flex: 1;
    }
    
    .timeline-title {
        font-weight: 600;
        color: #2d3436;
        margin-bottom: 4px;
    }
    
    .timeline-desc {
        color: #636e72;
        font-size: 0.9rem;
    }
    
    .timeline-date {
        color: #b2bec3;
        font-size: 0.8rem;
        margin-top: 4px;
    }
    
    /* Action Buttons */
    .action-buttons {
        padding: 20px 24px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .btn-workflow {
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-workflow.approve {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #fff;
    }
    
    .btn-workflow.approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
    }
    
    .btn-workflow.pay {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: #fff;
    }
    
    .btn-workflow.pay:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    }
    
    .btn-workflow.edit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }
    
    .btn-workflow.edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-workflow.cancel {
        background: #fff;
        border: 2px solid #dc3545;
        color: #dc3545;
    }
    
    .btn-workflow.cancel:hover {
        background: #dc3545;
        color: #fff;
    }
    
    /* Payment Mode Badge */
    .payment-mode-badge {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(102, 126, 234, 0.1) 100%);
        color: #667eea;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h4><i class="icon-file-text2 mr-2"></i> Ordre de Paiement</h4>
        <span class="ref-badge">{{ $decaissement->reference_op }}</span>
    </div>
    <div class="header-actions">
        <a href="{{ route('payments.decaissements.index') }}" class="btn btn-header back">
            <i class="icon-arrow-left8"></i> Retour
        </a>
        <a href="{{ route('payments.decaissements.print_op', $decaissement->id) }}" target="_blank" class="btn btn-header print">
            <i class="icon-printer"></i> Imprimer
        </a>
    </div>
</div>

<div class="content-grid">
    <!-- Main Content -->
    <div class="main-content">
        <!-- General Information -->
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="icon-info22"></i>
                <h6>Informations Générales</h6>
            </div>
            <div class="detail-card-body">
                <table class="info-table">
                    <tr>
                        <td class="label">Bénéficiaire</td>
                        <td class="value"><strong>{{ $decaissement->beneficiaire }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td class="value">{{ $decaissement->date_decaissement->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Motif</td>
                        <td class="value">{{ $decaissement->motif }}</td>
                    </tr>
                    <tr>
                        <td class="label">Mode de Paiement</td>
                        <td class="value">
                            <span class="payment-mode-badge">{{ $decaissement->mode_paiement }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Projet / Rubrique</td>
                        <td class="value">{{ $decaissement->projet_rubrique ?? 'Non spécifié' }}</td>
                    </tr>
                    @if($decaissement->observations)
                    <tr>
                        <td class="label">Observations</td>
                        <td class="value">{{ $decaissement->observations }}</td>
                    </tr>
                    @endif
                </table>
                
                <!-- Amount Display -->
                <div class="amount-highlight">
                    <div class="amount-value">{{ number_format($decaissement->montant, 0, ',', ' ') }} Ar</div>
                    <div class="amount-words">{{ ucfirst($decaissement->montant_lettres) }}</div>
                </div>
            </div>
        </div>
        
        <!-- File Attachment -->
        @if($decaissement->hasPieceJustificative())
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="icon-attachment"></i>
                <h6>Pièce Justificative</h6>
            </div>
            <div class="detail-card-body">
                <div class="file-attachment">
                    <div class="file-icon">
                        <i class="icon-file-pdf"></i>
                    </div>
                    <div class="file-info">
                        <h6>{{ $decaissement->piece_justificative_nom }}</h6>
                        <a href="{{ route('payments.decaissements.download_piece', $decaissement->id) }}">
                            <i class="icon-download4 mr-1"></i> Télécharger le document
                        </a>
                    </div>
                    <div class="file-status">
                        @if($decaissement->piece_justificative_valide)
                            <span class="badge-valid"><i class="icon-checkmark3 mr-1"></i> Validée</span>
                        @else
                            <span class="badge-pending"><i class="icon-spinner2 mr-1"></i> En attente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Sidebar: Status & Workflow -->
    <div class="sidebar">
        <div class="status-card">
            <div class="status-card-header">
                <h6>Statut & Workflow</h6>
            </div>
            
            <!-- Status Display -->
            <div class="status-display">
                @php
                    $statusConfig = [
                        'EN_ATTENTE' => ['class' => 'pending', 'icon' => 'icon-hourglass', 'label' => 'En Attente'],
                        'APPROUVE' => ['class' => 'approved', 'icon' => 'icon-checkmark-circle', 'label' => 'Approuvé'],
                        'PAYE' => ['class' => 'paid', 'icon' => 'icon-wallet', 'label' => 'Payé'],
                        'ANNULE' => ['class' => 'cancelled', 'icon' => 'icon-cancel-circle2', 'label' => 'Annulé'],
                    ];
                    $config = $statusConfig[$decaissement->statut] ?? ['class' => '', 'icon' => 'icon-help', 'label' => $decaissement->statut];
                @endphp
                
                <div class="status-icon {{ $config['class'] }}">
                    <i class="{{ $config['icon'] }}"></i>
                </div>
                <div class="status-label {{ $config['class'] }}">{{ $config['label'] }}</div>
            </div>
            
            <!-- Workflow Timeline -->
            <div class="workflow-timeline">
                <div class="timeline-item">
                    <div class="timeline-icon completed"><i class="icon-user"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Créé par</div>
                        <div class="timeline-desc">{{ $decaissement->creator->name ?? 'Système' }}</div>
                        <div class="timeline-date">{{ $decaissement->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
                
                @if($decaissement->approved_by)
                <div class="timeline-item">
                    <div class="timeline-icon completed"><i class="icon-checkmark"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Approuvé par</div>
                        <div class="timeline-desc">{{ $decaissement->approver->name }}</div>
                        <div class="timeline-date">{{ $decaissement->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
                @endif
                
                @if($decaissement->paid_by)
                <div class="timeline-item">
                    <div class="timeline-icon completed"><i class="icon-wallet"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Payé par</div>
                        <div class="timeline-desc">{{ $decaissement->payer->name }}</div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Action Buttons -->
            @if(in_array($decaissement->statut, ['EN_ATTENTE', 'APPROUVE']))
            <div class="action-buttons">
                @if($decaissement->statut == 'EN_ATTENTE')
                    <button onclick="approveOP({{ $decaissement->id }})" class="btn-workflow approve">
                        <i class="icon-checkmark3"></i> Approuver
                    </button>
                    <a href="{{ route('payments.decaissements.edit', $decaissement->id) }}" class="btn-workflow edit">
                        <i class="icon-pencil"></i> Modifier
                    </a>
                    <button onclick="cancelOP({{ $decaissement->id }})" class="btn-workflow cancel">
                        <i class="icon-cross2"></i> Annuler
                    </button>
                @elseif($decaissement->statut == 'APPROUVE')
                    <button onclick="markAsPaid({{ $decaissement->id }})" class="btn-workflow pay">
                        <i class="icon-wallet"></i> Marquer comme Payé
                    </button>
                    <button onclick="cancelOP({{ $decaissement->id }})" class="btn-workflow cancel">
                        <i class="icon-cross2"></i> Annuler
                    </button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('page_js')
<script>
function approveOP(id) {
    Swal.fire({
        title: 'Approuver cet OP ?',
        text: 'Cette action validera cet ordre de paiement.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
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
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="icon-wallet mr-1"></i> Confirmer le paiement',
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
</script>
@endpush
