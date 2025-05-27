@extends('layouts.master')
@section('page_title', 'Journal des Paiements')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title"><i class="icon-cash3 mr-2"></i>Journal des Paiements</h5>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
                <a class="list-icons-item" data-action="reload"></a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('payments.journal.filter') }}" method="get" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="period">Période :</label>
                        <select name="period" id="period" class="form-control" onchange="toggleDateFields()">
                            <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Journalier</option>
                            <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Mensuel</option>
                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 date-field" id="start_date_container" style="{{ $period == 'custom' ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label for="start_date">Date de début :</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                </div>
                <div class="col-md-3 date-field" id="end_date_container" style="{{ $period == 'custom' ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label for="end_date">Date de fin :</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="{{ route('payments.journal') }}" class="btn btn-light ml-2">Réinitialiser</a>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="class_id">Classe :</label>
                        <select name="class_id" id="class_id" class="form-control">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ isset($selectedClass) && $selectedClass == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_type">Objet du paiement :</label>
                        <select name="payment_type" id="payment_type" class="form-control">
                            <option value="">Tous les types</option>
                            @foreach($paymentTypes as $type)
                                <option value="{{ $type->title }}" {{ isset($selectedPaymentType) && $selectedPaymentType == $type->title ? 'selected' : '' }}>
                                    {{ $type->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_method">Mode de paiement :</label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">Tous les modes</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->methode }}" {{ isset($selectedPaymentMethod) && $selectedPaymentMethod == $method->methode ? 'selected' : '' }}>
                                    {{ $method->methode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="student_name">Rechercher un élève :</label>
                        <input type="text" name="student_name" id="student_name" class="form-control" placeholder="Nom de l'élève" value="{{ $studentName ?? '' }}">
                    </div>
                </div>
            </div>
        </form>

        <div class="alert alert-info">
            <h6 class="alert-heading">Résumé de la période : {{ $period == 'day' ? 'Journalier' : ($period == 'week' ? 'Hebdomadaire' : ($period == 'month' ? 'Mensuel' : 'Personnalisé')) }}</h6>
            <p>
                <strong>Période :</strong>
                @if($startDate == $endDate)
                    {{ \App\Helpers\DateHelper::formatForReceipt($startDate) }}
                @else
                    Du {{ \App\Helpers\DateHelper::formatForReceipt($startDate) }} au {{ \App\Helpers\DateHelper::formatForReceipt($endDate) }}
                @endif
            </p>
            <p><strong>Total des paiements :</strong> <span class="badge badge-success" style="font-size: 14px;">{{ \App\Helpers\DateHelper::formatAmount($totalAmount) }}</span></p>
        </div>

        <div class="table-responsive">
            <table class="table table-striped datatable-button-html5-columns">
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
                                        <span class="badge badge-info">ADRA</span>
                                    @elseif($status == 'TEAM3')
                                        <span class="badge badge-warning">TEAM3</span>
                                    @else
                                        <span class="badge badge-secondary">Normal</span>
                                    @endif
                                @else
                                    N/A
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

        <div class="mt-4">
            <h5>Statistiques détaillées</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="card-title">Total par classe</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Classe</th>
                                        <th class="text-right">Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classTotals as $className => $amount)
                                        <tr>
                                            <td>{{ $className }}</td>
                                            <td class="text-right"><strong>{{ \App\Helpers\DateHelper::formatAmount($amount) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th>Total</th>
                                        <th class="text-right"><strong>{{ \App\Helpers\DateHelper::formatAmount($classTotalSum) }}</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="card-title">Total par objet de paiement</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Objet</th>
                                        <th class="text-right">Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentTypeTotals as $paymentTitle => $amount)
                                        <tr>
                                            <td>{{ $paymentTitle }}</td>
                                            <td class="text-right"><strong>{{ \App\Helpers\DateHelper::formatAmount($amount) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th>Total</th>
                                        <th class="text-right"><strong>{{ \App\Helpers\DateHelper::formatAmount($paymentTypeTotalSum) }}</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('payments.journal.export.excel') }}?period={{ $period }}&start_date={{ $startDate }}&end_date={{ $endDate }}{{ isset($selectedClass) ? '&class_id='.$selectedClass : '' }}{{ isset($selectedPaymentType) ? '&payment_type='.$selectedPaymentType : '' }}{{ isset($selectedPaymentMethod) ? '&payment_method='.$selectedPaymentMethod : '' }}{{ isset($studentName) ? '&student_name='.$studentName : '' }}" class="btn btn-success">
                <i class="icon-file-excel mr-2"></i>Exporter en Excel
            </a>
            <button id="export-excel" class="btn btn-info ml-2">
                <i class="icon-file-excel mr-2"></i>Exporter (JavaScript)
            </button>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('.datatable-button-html5-columns').DataTable({
            buttons: {
                buttons: [
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: [ 0, ':visible' ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="icon-three-bars"></i>',
                        className: 'btn btn-primary btn-icon dropdown-toggle'
                    }
                ]
            },
            "pageLength": 25,
            "order": [[0, "desc"]],
            "language": {
                "search": '<span>Rechercher :</span> _INPUT_',
                "searchPlaceholder": 'Tapez pour filtrer...',
                "lengthMenu": '<span>Afficher :</span> _MENU_',
                "paginate": { 'first': 'Premier', 'last': 'Dernier', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
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
                { "width": "10%", "targets": 0 }, // Date/Heure
                { "width": "12%", "targets": 1 }, // Élève
                { "width": "8%", "targets": 2 },  // Statut
                { "width": "10%", "targets": 3 }, // Classe
                { "width": "12%", "targets": 4 }, // Objet du Paiement
                { "width": "10%", "targets": 5 }, // Montant
                { "width": "8%", "targets": 6 },  // Mode de Paiement
                { "width": "10%", "targets": 7 }, // Référence
                { "width": "10%", "targets": 8 }, // Observations
                { "width": "8%", "targets": 9 }   // Validé par
            ]
        });

        // Fonction pour exporter en Excel
        $('#export-excel').on('click', function() {
            // Récupérer les données du tableau
            var data = [];

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

            // Télécharger le fichier
            XLSX.writeFile(wb, fileName + '.xlsx');
        });
    });

    function toggleDateFields() {
        var period = document.getElementById('period').value;
        var dateFields = document.querySelectorAll('.date-field');

        if (period === 'custom') {
            dateFields.forEach(function(field) {
                field.style.display = 'block';
            });
        } else {
            dateFields.forEach(function(field) {
                field.style.display = 'none';
            });
        }
    }
</script>
@endsection
