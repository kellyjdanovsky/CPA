@extends('layouts.master')
@section('page_title', 'Vérification des impayés - Classe ' . $nom_classe->name)
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">
                <i class="icon-cash2 mr-2"></i>
                @if($student_type == 'unpaid')
                    Élèves impayés - Classe {{ $nom_classe->name }}
                @else
                    Tous les élèves - Classe {{ $nom_classe->name }}
                @endif
            </h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('payments.export_unpaid', ['payment_ids' => implode(',', $payment_ids), 'id_class' => $id_class, 'statuses' => implode(',', $statuses), 'student_type' => $student_type]) }}" class="btn btn-success btn-sm">
                        <i class="icon-file-excel mr-2"></i>Exporter en Excel
                    </a>
                    <a href="{{ route('payments.verified') }}" class="btn btn-info btn-sm ml-2">
                        <i class="icon-arrow-left52 mr-2"></i>Retour
                    </a>
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <h6 class="font-weight-bold">Motifs de paiement sélectionnés :</h6>
                <div class="d-flex flex-wrap">
                    @foreach($payments as $payment)
                        <span class="badge badge-primary mr-2 mb-2 p-2">{{ $payment->title }}</span>
                    @endforeach
                </div>
            </div>

            @if(count($unpaid_students) > 0)
                <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nom de l'élève</th>
                                <th>Classe</th>
                                <th>Statut</th>
                                <th>Montant total à payer</th>
                                <th>Montant déjà payé</th>
                                <th>Date du paiement</th>
                                <th>Montant restant à payer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaid_students as $index => $us)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $us['student']->user->name }}</td>
                                    <td>{{ $nom_classe->name }} {{ $us['student']->section->name }}</td>
                                    <td>
                                        @php
                                            $statusClass = 'badge-secondary';
                                            if($us['status'] == 'ADRA') {
                                                $statusClass = 'badge-info';
                                            } elseif($us['status'] == 'Normal') {
                                                $statusClass = 'badge-primary';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $us['status'] }}</span>
                                    </td>
                                    <td>{{ number_format($us['total_amount'], 0, ',', ' ') }} Ar</td>
                                    <td>{{ number_format($us['amount_paid'], 0, ',', ' ') }} Ar</td>
                                    <td>
                                        @if($us['last_payment_date'])
                                            {{ date('d/m/Y', strtotime($us['last_payment_date'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($us['amount_due'], 0, ',', ' ') }} Ar</td>
                                    <td>
                                        <div class="list-icons">
                                            <a href="{{ route('payments.invoice', [Qs::hash($us['student']->user_id), Qs::getCurrentSession()]) }}" class="btn btn-danger btn-sm">
                                                <i class="icon-credit-card mr-1"></i>Payer
                                            </a>
                                            <button type="button" class="btn btn-info btn-sm ml-1" data-toggle="modal" data-target="#payment_details_{{ $us['student']->id }}">
                                                <i class="icon-eye mr-1"></i>Détails
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Modales pour les détails de paiement --}}
                @foreach($unpaid_students as $us)
                    <div id="payment_details_{{ $us['student']->id }}" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-info">
                                    <h5 class="modal-title">
                                        <i class="icon-history mr-2"></i>
                                        Historique des paiements - {{ $us['student']->user->name }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Nom de l'élève:</strong> {{ $us['student']->user->name }}</p>
                                            <p><strong>Classe:</strong> {{ $nom_classe->name }} {{ $us['student']->section->name }}</p>
                                            <p><strong>Statut:</strong> <span class="badge {{ $us['status'] == 'ADRA' ? 'badge-info' : 'badge-primary' }}">{{ $us['status'] }}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Motifs de paiement:</strong> {{ $us['payment_titles'] }}</p>
                                            <p><strong>Montant total à payer:</strong> {{ number_format($us['total_amount'], 0, ',', ' ') }} Ar</p>
                                            <p><strong>Montant restant à payer:</strong> {{ number_format($us['amount_due'], 0, ',', ' ') }} Ar</p>
                                        </div>
                                    </div>

                                    <h6 class="font-weight-bold mb-3">Détail des paiements par motif</h6>

                                    @foreach($us['payment_records'] as $pr)
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">{{ $pr['payment']->title }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-2">
                                                    <div class="col-md-4">
                                                        <p><strong>Montant total:</strong> 
                                                            @if($us['status'] == 'ADRA')
                                                                {{ number_format($pr['payment']->amount * 0.25, 0, ',', ' ') }} Ar (25%)
                                                            @else
                                                                {{ number_format($pr['payment']->amount, 0, ',', ' ') }} Ar
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>Montant payé:</strong> {{ number_format($pr['amount_paid'], 0, ',', ' ') }} Ar</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>Montant restant:</strong> {{ number_format($pr['amount_due'], 0, ',', ' ') }} Ar</p>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>N°</th>
                                                                <th>Date de paiement</th>
                                                                <th>Montant payé</th>
                                                                <th>Solde restant</th>
                                                                <th>Méthode</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $receipts = $pr['record']->receipt;
                                                                $totalPaid = 0;
                                                            @endphp

                                                            @if($receipts && $receipts->count() > 0)
                                                                @foreach($receipts->sortBy('created_at') as $index => $receipt)
                                                                    @php
                                                                        $totalPaid += $receipt->amt_paid;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td>{{ date('d/m/Y à H:i', strtotime($receipt->created_at)) }}</td>
                                                                        <td>{{ number_format($receipt->amt_paid, 0, ',', ' ') }} Ar</td>
                                                                        <td>{{ number_format($receipt->balance, 0, ',', ' ') }} Ar</td>
                                                                        <td>{{ $receipt->methode ?? 'Non spécifié' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="bg-light">
                                                                    <td colspan="2" class="font-weight-bold text-right">Total payé:</td>
                                                                    <td class="font-weight-bold">{{ number_format($totalPaid, 0, ',', ' ') }} Ar</td>
                                                                    <td class="font-weight-bold">{{ number_format($pr['amount_due'], 0, ',', ' ') }} Ar</td>
                                                                    <td></td>
                                                                </tr>
                                                            @else
                                                                <tr>
                                                                    <td colspan="5" class="text-center">Aucun paiement effectué pour le moment.</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="modal-footer">
                                    <a href="{{ route('payments.invoice', [Qs::hash($us['student']->user_id), Qs::getCurrentSession()]) }}" class="btn btn-danger">
                                        <i class="icon-credit-card mr-1"></i>Effectuer un paiement
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    Aucun élève impayé trouvé pour cette classe et ces motifs de paiement.
                </div>
            @endif
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            $('.datatable-button-html5-columns').DataTable({
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
                                columns: [0, 1, 2, 3, 4, 5, 6, 7]
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
                "order": [[0, "asc"]],
                "language": {
                    "search": '<span>Rechercher :</span> _INPUT_',
                    "searchPlaceholder": 'Tapez pour filtrer...',
                    "lengthMenu": '<span>Afficher :</span> _MENU_',
                    "paginate": { 'first': 'Premier', 'last': 'Dernier', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
                }
            });
        });
    </script>
@endsection
