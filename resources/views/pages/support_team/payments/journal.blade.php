@extends('layouts.master')
@section('page_title', 'Journal des Paiements')

@section('page_styles')
<style>
    /* Styles optimisés pour le journal des paiements */
    .journal-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }

    .journal-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }

    .journal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 20px 20px 0 0;
        position: relative;
        overflow: hidden;
    }

    .journal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .journal-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .journal-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 5px 0 0 0;
        position: relative;
        z-index: 1;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 25px 30px;
        border-bottom: 1px solid #e9ecef;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 20px;
    }

    .filter-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .filter-title i {
        margin-right: 10px;
        color: #667eea;
    }

    .form-control-modern {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: #fff;
    }

    .btn-modern {
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .btn-light-modern {
        background: #f8f9fa;
        color: #495057;
        border: 2px solid #e9ecef;
    }

    .btn-light-modern:hover {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-1px);
    }

    .stats-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        margin: 0 30px 25px 30px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .stats-summary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
    }

    .stats-content {
        position: relative;
        z-index: 1;
    }

    .stats-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .stats-amount {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
    }

    .table-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin: 0 30px;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 12px;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f1f3f4;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .table-modern tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border: none;
    }

    .badge-modern {
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .export-section {
        padding: 25px 30px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .btn-export {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
        color: white;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .journal-container {
            padding: 10px;
        }

        .journal-header,
        .filter-section,
        .stats-summary,
        .export-section {
            padding: 20px;
        }

        .table-modern {
            margin: 0 20px;
        }

        .journal-title {
            font-size: 1.5rem;
        }

        .stats-amount {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('content')
<div class="journal-container">
    <div class="container-fluid">
        <div class="journal-card fade-in">
            <div class="journal-header">
                <h1 class="journal-title">
                    <i class="icon-cash3 mr-3"></i>Journal des Paiements
                </h1>
                <p class="journal-subtitle">Suivi en temps réel des transactions financières</p>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <h6 class="filter-title">
                        <i class="icon-calendar22"></i>Filtres de période
                    </h6>
                    <form action="{{ route('payments.journal.filter') }}" method="get" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="period" class="font-weight-semibold">Période :</label>
                                    <select name="period" id="period" class="form-control form-control-modern" onchange="toggleDateFields()">
                                        <option value="day" {{ $period == 'day' ? 'selected' : '' }}>📅 Journalier</option>
                                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>📊 Hebdomadaire</option>
                                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>📈 Mensuel</option>
                                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>🎯 Personnalisé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 date-field" id="start_date_container" style="{{ $period == 'custom' ? '' : 'display: none;' }}">
                                <div class="form-group">
                                    <label for="start_date" class="font-weight-semibold">Date de début :</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-modern" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3 date-field" id="end_date_container" style="{{ $period == 'custom' ? '' : 'display: none;' }}">
                                <div class="form-group">
                                    <label for="end_date" class="font-weight-semibold">Date de fin :</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-modern" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-modern btn-primary-modern mr-2">
                                        <i class="icon-filter4 mr-2"></i>Filtrer
                                    </button>
                                    <a href="{{ route('payments.journal') }}" class="btn btn-modern btn-light-modern">
                                        <i class="icon-reset mr-2"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="filter-title">
                                    <i class="icon-filter3"></i>Filtres avancés
                                </h6>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="class_id" class="font-weight-semibold">Classe :</label>
                                    <select name="class_id" id="class_id" class="form-control form-control-modern">
                                        <option value="">🏫 Toutes les classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ isset($selectedClass) && $selectedClass == $class->id ? 'selected' : '' }}>
                                                📚 {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_type" class="font-weight-semibold">Objet du paiement :</label>
                                    <select name="payment_type" id="payment_type" class="form-control form-control-modern">
                                        <option value="">💰 Tous les types</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type->title }}" {{ isset($selectedPaymentType) && $selectedPaymentType == $type->title ? 'selected' : '' }}>
                                                📋 {{ $type->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_method" class="font-weight-semibold">Mode de paiement :</label>
                                    <select name="payment_method" id="payment_method" class="form-control form-control-modern">
                                        <option value="">💳 Tous les modes</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->methode }}" {{ isset($selectedPaymentMethod) && $selectedPaymentMethod == $method->methode ? 'selected' : '' }}>
                                                💵 {{ $method->methode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="student_name" class="font-weight-semibold">Rechercher un élève :</label>
                                    <input type="text" name="student_name" id="student_name" class="form-control form-control-modern" placeholder="🔍 Nom de l'élève..." value="{{ $studentName ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="stats-summary fade-in">
                <div class="stats-content">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="stats-title">
                                📊 Résumé de la période : {{ $period == 'day' ? 'Journalier' : ($period == 'week' ? 'Hebdomadaire' : ($period == 'month' ? 'Mensuel' : 'Personnalisé')) }}
                            </h6>
                            <p class="mb-2">
                                <strong>📅 Période :</strong>
                                @if($startDate == $endDate)
                                    {{ \App\Helpers\DateHelper::formatForReceipt($startDate) }}
                                @else
                                    Du {{ \App\Helpers\DateHelper::formatForReceipt($startDate) }} au {{ \App\Helpers\DateHelper::formatForReceipt($endDate) }}
                                @endif
                            </p>
                            <p class="mb-0">
                                <strong>📈 Nombre de transactions :</strong> {{ count($receipts) }}
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="stats-title">💰 Total des paiements</div>
                            <div class="stats-amount">{{ \App\Helpers\DateHelper::formatAmount($totalAmount) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-modern datatable-button-html5-columns">
                <thead>
                    <tr>
                        <th>Date/Heure</th>
                        <th>Élève</th>
                        <th>Statut</th>
                        <th>Classe</th>
                        <th>Objet du Paiement</th>
                        <th>Montant (Ar)</th>
                        <th>Mode de Paiement</th>
                        <th>Référence / Reçu</th>
                        <th>Observations</th>
                        <th>Validé par</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $receipt)
                        <tr>
                            <td>{{ \App\Helpers\DateHelper::formatFrenchWithTime($receipt->created_at) }}</td>
                            <td>
                                @if($receipt->pr && $receipt->pr->student)
                                    {{ $receipt->pr->student->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($receipt->pr && $receipt->pr->student)
                                    @php
                                        $status = $receipt->pr->student->status ?? 'Normal';
                                    @endphp
                                    @if($status == 'ADRA')
                                        <span class="badge badge-modern badge-info">🏛️ ADRA</span>
                                    @elseif($status == 'TEAM3')
                                        <span class="badge badge-modern badge-warning">⭐ TEAM3</span>
                                    @else
                                        <span class="badge badge-modern badge-secondary">👤 Normal</span>
                                    @endif
                                @else
                                    <span class="badge badge-modern badge-light">❓ N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $class = 'N/A';
                                    if($receipt->pr && $receipt->pr->student && $receipt->pr->student->student_record) {
                                        $studentRecord = $receipt->pr->student->student_record;
                                        if($studentRecord->my_class) {
                                            $class = $studentRecord->my_class->name;
                                            if($studentRecord->section) {
                                                $class .= ' ' . $studentRecord->section->name;
                                            }
                                        }
                                    }
                                @endphp
                                {{ $class }}
                            </td>
                            <td>
                                @if($receipt->pr && $receipt->pr->payment)
                                    {{ $receipt->pr->payment->title }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td><strong>{{ \App\Helpers\DateHelper::formatAmount($receipt->amt_paid) }}</strong></td>
                            <td>{{ $receipt->methode ?? 'Espèces' }}</td>
                            <td>{{ $receipt->reference_number ?? $receipt->pr->ref_no ?? 'N/A' }}</td>
                            <td>{{ $receipt->observations ?? '' }}</td>
                            <td>{{ $receipt->created_by ?? 'Système' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-primary text-white">
                        <th colspan="5" class="text-right">TOTAL</th>
                        <th><strong>{{ \App\Helpers\DateHelper::formatAmount($totalAmount) }}</strong></th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

            <!-- Statistiques détaillées modernisées -->
            <div class="mt-4 mx-4">
                <h5 class="mb-4">
                    <i class="icon-stats-bars mr-2"></i>📊 Statistiques détaillées
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="filter-card">
                            <div class="filter-title">
                                <i class="icon-graduation2"></i>📚 Total par classe
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Classe</th>
                                            <th class="text-right">Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($classTotals as $className => $amount)
                                            <tr>
                                                <td><strong>{{ $className }}</strong></td>
                                                <td class="text-right">
                                                    <span class="badge badge-modern badge-success">
                                                        {{ \App\Helpers\DateHelper::formatAmount($amount) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-primary text-white">
                                            <th>💰 Total</th>
                                            <th class="text-right">
                                                <strong>{{ \App\Helpers\DateHelper::formatAmount($classTotalSum) }}</strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="filter-card">
                            <div class="filter-title">
                                <i class="icon-cash3"></i>💳 Total par objet de paiement
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Objet</th>
                                            <th class="text-right">Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paymentTypeTotals as $paymentTitle => $amount)
                                            <tr>
                                                <td><strong>{{ $paymentTitle }}</strong></td>
                                                <td class="text-right">
                                                    <span class="badge badge-modern badge-info">
                                                        {{ \App\Helpers\DateHelper::formatAmount($amount) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-primary text-white">
                                            <th>💰 Total</th>
                                            <th class="text-right">
                                                <strong>{{ \App\Helpers\DateHelper::formatAmount($paymentTypeTotalSum) }}</strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section d'export modernisée -->
            <div class="export-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-3">
                            <i class="icon-download4 mr-2"></i>📥 Exporter les données
                        </h6>
                        <p class="text-muted mb-0">Téléchargez les données filtrées dans le format de votre choix</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <a href="{{ route('payments.journal.export.excel') }}?period={{ $period }}&start_date={{ $startDate }}&end_date={{ $endDate }}{{ isset($selectedClass) ? '&class_id='.$selectedClass : '' }}{{ isset($selectedPaymentType) ? '&payment_type='.$selectedPaymentType : '' }}{{ isset($selectedPaymentMethod) ? '&payment_method='.$selectedPaymentMethod : '' }}{{ isset($studentName) ? '&student_name='.$studentName : '' }}" class="btn btn-export" id="exportExcelServer">
                                <i class="icon-file-excel mr-2"></i>📊 Excel (Serveur)
                            </a>
                            <button id="export-excel" class="btn btn-export">
                                <i class="icon-file-excel mr-2"></i>⚡ Excel (Rapide)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        // Afficher le loader pendant le chargement
        showLoader();

        // Configuration optimisée de DataTable
        var table = $('.datatable-button-html5-columns').DataTable({
            "processing": true,
            "deferRender": true,
            "stateSave": true,
            "stateDuration": 60 * 60 * 24, // 24 heures
            buttons: {
                buttons: [
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-modern btn-light-modern',
                        text: '<i class="icon-copy mr-2"></i>Copier',
                        exportOptions: {
                            columns: [ 0, ':visible' ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-modern btn-export',
                        text: '<i class="icon-file-excel mr-2"></i>Excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-modern btn-export',
                        text: '<i class="icon-file-pdf mr-2"></i>PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="icon-three-bars mr-2"></i>Colonnes',
                        className: 'btn btn-modern btn-primary-modern'
                    }
                ]
            },
            "pageLength": 50,
            "lengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Tout"]],
            "order": [[0, "desc"]],
            "language": {
                "search": '<span>🔍 Rechercher :</span> _INPUT_',
                "searchPlaceholder": 'Tapez pour filtrer en temps réel...',
                "lengthMenu": '<span>📄 Afficher :</span> _MENU_ <span>entrées</span>',
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                "infoEmpty": "Aucune entrée disponible",
                "infoFiltered": "(filtré à partir de _MAX_ entrées au total)",
                "loadingRecords": "Chargement en cours...",
                "processing": "Traitement en cours...",
                "zeroRecords": "Aucun enregistrement correspondant trouvé",
                "paginate": {
                    'first': '⏮️ Premier',
                    'last': '⏭️ Dernier',
                    'next': 'Suivant ➡️',
                    'previous': '⬅️ Précédent'
                }
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();
                var total = api
                    .column(4, { page: 'current' })
                    .data()
                    .reduce(function(acc, val) {
                        // Extraire le montant numérique (supprimer 'Ar' et les espaces)
                        var amount = val.replace(/[^0-9]/g, '');
                        return acc + parseInt(amount);
                    }, 0);

                // Mettre à jour le total dans le pied de tableau
                $(api.column(4).footer()).html(total.toLocaleString('fr-FR') + ' Ar');
            },
            "columnDefs": [
                { "width": "10%", "targets": 0, "className": "text-center" }, // Date/Heure
                { "width": "12%", "targets": 1, "className": "font-weight-bold" }, // Élève
                { "width": "8%", "targets": 2, "className": "text-center" },  // Statut
                { "width": "10%", "targets": 3, "className": "text-center" }, // Classe
                { "width": "12%", "targets": 4 }, // Objet du Paiement
                { "width": "10%", "targets": 5, "className": "text-right font-weight-bold" }, // Montant
                { "width": "8%", "targets": 6, "className": "text-center" },  // Mode de Paiement
                { "width": "10%", "targets": 7, "className": "text-center" }, // Référence
                { "width": "10%", "targets": 8 }, // Observations
                { "width": "8%", "targets": 9, "className": "text-center" }   // Validé par
            ],
            "initComplete": function(settings, json) {
                // Masquer le loader une fois le tableau initialisé
                hideLoader();

                // Ajouter des animations aux lignes
                $('.table-modern tbody tr').addClass('fade-in');

                // Optimiser la recherche en temps réel
                var searchInput = $('.dataTables_filter input');
                searchInput.addClass('form-control-modern');
                searchInput.attr('placeholder', '🔍 Recherche instantanée...');

                // Ajouter des tooltips aux boutons
                $('.dt-buttons .btn').each(function() {
                    $(this).attr('title', $(this).text().trim());
                });
            }
        });

        // Fonction optimisée pour exporter en Excel
        $('#export-excel').on('click', function() {
            showLoader();

            // Récupérer les données du tableau avec optimisation
            var data = [];
            var button = $(this);
            button.prop('disabled', true).html('<i class="icon-spinner2 spinner mr-2"></i>Génération...');

            // En-tête
            var headers = [];
            $('.datatable-button-html5-columns thead th').each(function() {
                headers.push($(this).text());
            });
            data.push(headers);

            // Données
            $('.datatable-button-html5-columns tbody tr').each(function() {
                var rowData = [];
                $(this).find('td').each(function() {
                    rowData.push($(this).text().trim());
                });
                data.push(rowData);
            });

            // Ajouter la ligne de total
            var totalRow = ['', '', '', '', 'TOTAL', '{{ number_format($totalAmount, 0, ',', ' ') }} Ar', '', '', '', ''];
            data.push(totalRow);

            // Créer un classeur Excel
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Ajouter la feuille au classeur
            XLSX.utils.book_append_sheet(wb, ws, 'Journal des Paiements');

            // Ajouter une feuille pour les statistiques
            var statsData = [];

            // Statistiques par classe
            statsData.push(['Statistiques par classe']);
            statsData.push(['Classe', 'Montant (Ar)']);

            @foreach($classTotals as $className => $amount)
                statsData.push(['{{ $className }}', '{{ number_format($amount, 0, ',', ' ') }} Ar']);
            @endforeach

            statsData.push(['Total', '{{ number_format($classTotalSum, 0, ',', ' ') }} Ar']);

            // Espace entre les tableaux
            statsData.push(['']);
            statsData.push(['']);

            // Statistiques par type de paiement
            statsData.push(['Statistiques par objet de paiement']);
            statsData.push(['Objet', 'Montant (Ar)']);

            @foreach($paymentTypeTotals as $paymentTitle => $amount)
                statsData.push(['{{ $paymentTitle }}', '{{ number_format($amount, 0, ',', ' ') }} Ar']);
            @endforeach

            statsData.push(['Total', '{{ number_format($paymentTypeTotalSum, 0, ',', ' ') }} Ar']);

            var statsWs = XLSX.utils.aoa_to_sheet(statsData);
            XLSX.utils.book_append_sheet(wb, statsWs, 'Statistiques');

            // Générer le nom du fichier avec la date
            var fileName = 'Journal_Paiements_' +
                           @if($startDate == $endDate)
                               '{{ date('d-m-Y', strtotime($startDate)) }}'
                           @else
                               '{{ date('d-m-Y', strtotime($startDate)) }}_au_{{ date('d-m-Y', strtotime($endDate)) }}'
                           @endif;

            // Télécharger le fichier avec gestion d'erreur
            try {
                XLSX.writeFile(wb, fileName + '.xlsx');

                // Notification de succès
                showNotification('✅ Export Excel généré avec succès!', 'success');
            } catch (error) {
                console.error('Erreur lors de l\'export:', error);
                showNotification('❌ Erreur lors de la génération du fichier Excel', 'error');
            } finally {
                // Restaurer le bouton
                button.prop('disabled', false).html('<i class="icon-file-excel mr-2"></i>⚡ Excel (Rapide)');
                hideLoader();
            }
        });

        // Optimisation du formulaire de filtres
        $('#filterForm').on('submit', function() {
            showLoader();
        });

        // Auto-submit pour les filtres rapides
        $('#period, #class_id, #payment_type, #payment_method').on('change', function() {
            if ($(this).val() !== '') {
                showLoader();
                setTimeout(function() {
                    $('#filterForm').submit();
                }, 300);
            }
        });

        // Recherche en temps réel pour les étudiants
        let searchTimeout;
        $('#student_name').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($('#student_name').val().length >= 3 || $('#student_name').val().length === 0) {
                    showLoader();
                    $('#filterForm').submit();
                }
            }, 500);
        });
    });

    // Fonctions utilitaires pour l'interface
    function showLoader() {
        if ($('.loading-overlay').length === 0) {
            $('body').append('<div class="loading-overlay"><div class="loading-spinner"></div></div>');
        }
    }

    function hideLoader() {
        $('.loading-overlay').fadeOut(300, function() {
            $(this).remove();
        });
    }

    function showNotification(message, type = 'info') {
        var alertClass = type === 'success' ? 'alert-success' :
                        type === 'error' ? 'alert-danger' : 'alert-info';

        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 10000; min-width: 300px;">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            message + '</div>');

        $('body').append(notification);

        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }

    function toggleDateFields() {
        var period = document.getElementById('period').value;
        var dateFields = document.querySelectorAll('.date-field');

        if (period === 'custom') {
            dateFields.forEach(function(field) {
                field.style.display = 'block';
                field.classList.add('fade-in');
            });
        } else {
            dateFields.forEach(function(field) {
                field.style.display = 'none';
            });
        }
    }

    // Optimisation des performances au chargement
    document.addEventListener('DOMContentLoaded', function() {
        // Lazy loading des images si présentes
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Préchargement des données critiques
        if (typeof Storage !== "undefined") {
            // Sauvegarder les préférences utilisateur
            const preferences = {
                pageLength: localStorage.getItem('journal_pageLength') || 50,
                searchTerm: localStorage.getItem('journal_searchTerm') || ''
            };

            // Appliquer les préférences sauvegardées
            if (preferences.searchTerm) {
                $('#student_name').val(preferences.searchTerm);
            }
        }
    });
</script>
@endsection
