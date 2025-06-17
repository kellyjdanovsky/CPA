@extends('layouts.master')
@section('page_title', 'Reçu ADRA & TEAM 3')

@section('content')

<style>
    /* Styles pour le reçu */
    .receipt-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        padding: 0;
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .receipt-header {
        background: linear-gradient(to right, #3a7bd5, #00d2ff);
        color: white;
        padding: 20px;
        position: relative;
    }
    
    .receipt-logo {
        position: absolute;
        top: 20px;
        right: 20px;
        height: 80px;
        width: auto;
    }
    
    .receipt-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .receipt-subtitle {
        font-size: 16px;
        opacity: 0.8;
    }
    
    .receipt-body {
        padding: 30px;
    }
    
    .receipt-info {
        margin-bottom: 30px;
    }
    
    .receipt-info-title {
        font-weight: 600;
        color: #3a7bd5;
        margin-bottom: 5px;
    }
    
    .receipt-info-value {
        font-size: 16px;
    }
    
    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    
    .receipt-table th {
        background-color: #f8f9fa;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
    }
    
    .receipt-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .receipt-table tr:last-child td {
        border-bottom: none;
    }
    
    .receipt-total {
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-radius: 5px;
        margin-bottom: 30px;
    }
    
    .receipt-total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .receipt-total-row:last-child {
        margin-bottom: 0;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
    }
    
    .receipt-total-label {
        font-weight: 600;
        color: #495057;
    }
    
    .receipt-total-value {
        font-weight: 700;
        color: #3a7bd5;
    }
    
    .amount-in-words {
        margin-top: 15px;
        font-style: italic;
        border-top: 1px dashed #e9ecef;
        padding-top: 15px;
    }
    
    .amount-in-words .receipt-total-label {
        color: #6c757d;
    }
    
    .amount-in-words .receipt-total-value {
        color: #6c757d;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .receipt-footer {
        text-align: center;
        padding: 20px;
        border-top: 1px solid #e9ecef;
        color: #6c757d;
    }
    
    .receipt-actions {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }
    
    .receipt-action-btn {
        margin: 0 10px;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .receipt-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .print-btn {
        background: linear-gradient(to right, #3a7bd5, #00d2ff);
        border: none;
        color: white;
    }
    
    .back-btn {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #495057;
    }
    
    .adra-badge {
        background: linear-gradient(to right, #3a7bd5, #00d2ff);
        color: white;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 4px;
    }
    
    .team3-badge {
        background: linear-gradient(to right, #28a745, #5cb85c);
        color: white;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 4px;
    }
    
    @media print {
        .receipt-actions, .navbar, .sidebar, .footer, .header {
            display: none !important;
        }
        
        .content-wrapper {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .receipt-container {
            box-shadow: none !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }
        
        body {
            background-color: white !important;
        }
    }
</style>

<div class="receipt-actions">
    <a href="{{ route('payments.adra_team3') }}" class="btn receipt-action-btn back-btn">
        <i class="icon-arrow-left8 mr-2"></i> Retour
    </a>
    <button onclick="window.print()" class="btn receipt-action-btn print-btn">
        <i class="icon-printer4 mr-2"></i> Imprimer
    </button>
</div>

<div class="receipt-container">
    <div class="receipt-header">
        <img src="{{ asset('global_assets/images/logo_light.png') }}" alt="Logo" class="receipt-logo">
        <h1 class="receipt-title">Reçu de Paiement</h1>
        <p class="receipt-subtitle">{{ $s['system_name'] ?? 'Système de Gestion Scolaire' }}</p>
    </div>
    
    <div class="receipt-body">
        <div class="row">
            <div class="col-md-6">
                <div class="receipt-info">
                    <div class="receipt-info-title">Élève</div>
                    <div class="receipt-info-value">{{ $student->user->name }}</div>
                </div>
                
                <div class="receipt-info">
                    <div class="receipt-info-title">Classe</div>
                    <div class="receipt-info-value">{{ $student->my_class->name }}</div>
                </div>
                
                <div class="receipt-info">
                    <div class="receipt-info-title">Année scolaire</div>
                    <div class="receipt-info-value">{{ $s['current_session'] ?? date('Y') }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="receipt-info">
                    <div class="receipt-info-title">Statut</div>
                    <div class="receipt-info-value">
                        @if($status == 'ADRA')
                            <span class="adra-badge">ADRA</span>
                        @elseif($status == 'TEAM3')
                            <span class="team3-badge">TEAM 3</span>
                        @else
                            <span class="badge badge-secondary">{{ $status }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="receipt-info">
                    <div class="receipt-info-title">Code de référence</div>
                    <div class="receipt-info-value">
                        @if(isset($reference_codes) && count($reference_codes) > 1)
                            <button type="button" class="btn btn-sm btn-light" data-toggle="popover" 
                                title="Codes de référence" 
                                data-content="@foreach($reference_codes as $index => $code){{ $index > 0 ? '<br>' : '' }}{{ $code }}@endforeach"
                                data-html="true">
                                Multiples <i class="icon-info22 ml-1"></i>
                            </button>
                        @else
                            {{ $reference_code }}
                        @endif
                    </div>
                </div>
                
                <div class="receipt-info">
                    <div class="receipt-info-title">Date</div>
                    <div class="receipt-info-value">{{ $receipt_date ?? date('d/m/Y') }}</div>
                </div>
            </div>
        </div>
        
        <h4 class="mb-3 mt-4">Détails des paiements</h4>
        
        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Paiement</th>
                    <th>Montant original</th>
                    <th>Pourcentage</th>
                    <th>Montant payé</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment_details as $payment)
                    <tr>
                        <td>{{ $payment['title'] }}</td>
                        <td>{{ number_format($payment['original_amount'], 0, ',', ' ') }} Ar</td>
                        <td>{{ $payment['percentage'] }}%</td>
                        <td>{{ number_format($payment['paid_amount'], 0, ',', ' ') }} Ar</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="receipt-total">
            <div class="receipt-total-row">
                <span class="receipt-total-label">Montant total</span>
                <span class="receipt-total-value">{{ number_format($total_amount, 0, ',', ' ') }} Ar</span>
            </div>
            
            @if($status == 'ADRA')
                <div class="receipt-total-row">
                    <span class="receipt-total-label">Prise en charge ADRA (75%)</span>
                    <span class="receipt-total-value">{{ number_format($total_amount, 0, ',', ' ') }} Ar</span>
                </div>
            @endif
            
            <div class="receipt-total-row amount-in-words">
                <span class="receipt-total-label">Arrêté à la somme de</span>
                <span class="receipt-total-value">{{ NumberToWords::convert($total_amount) }}</span>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-6">
                <p class="font-weight-semibold">Signature de l'élève</p>
                <div style="height: 70px; border-bottom: 1px solid #e9ecef;"></div>
            </div>
            <div class="col-md-6 text-right">
                <p class="font-weight-semibold">Signature et cachet</p>
                <div style="height: 70px; border-bottom: 1px solid #e9ecef;"></div>
            </div>
        </div>
    </div>
    
    <div class="receipt-footer">
        <p>{{ $s['system_name'] ?? 'Système de Gestion Scolaire' }} - {{ $s['address'] ?? '' }}</p>
        <p>{{ $s['phone'] ?? '' }} | {{ $s['email'] ?? '' }}</p>
    </div>
</div>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Initialiser les popovers
        $('[data-toggle="popover"]').popover({
            trigger: 'hover',
            placement: 'top'
        });
    });
</script>
@endsection