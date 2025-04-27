@extends('layouts.master')
@section('page_title', 'Gérer les paiements')
@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h5 class="card-title">
            <i class="icon-cash3 mr-2 text-primary"></i>
            Gestion des paiements pour <span class="font-weight-bold text-primary">{{ $sr->user->name }}</span>
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Résumé des paiements -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="font-weight-semibold">Total à payer</h6>
                        <h3 class="font-weight-bold mb-0">
                            @php
                                $totalAmount = 0;
                                foreach($uncleared as $uc) {
                                    $totalAmount += $uc->payment->amount;
                                }
                                foreach($cleared as $cl) {
                                    $totalAmount += $cl->payment->amount;
                                }
                                echo number_format($totalAmount, 0, ',', ' ') . ' Ar';
                            @endphp
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success-100">
                    <div class="card-body text-center">
                        <h6 class="font-weight-semibold">Montant payé</h6>
                        <h3 class="font-weight-bold mb-0 text-success">
                            @php
                                $totalPaid = 0;
                                foreach($uncleared as $uc) {
                                    $totalPaid += $uc->amt_paid ?: 0;
                                }
                                foreach($cleared as $cl) {
                                    $totalPaid += $cl->payment->amount;
                                }
                                echo number_format($totalPaid, 0, ',', ' ') . ' Ar';
                            @endphp
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger-100">
                    <div class="card-body text-center">
                        <h6 class="font-weight-semibold">Reste à payer</h6>
                        <h3 class="font-weight-bold mb-0 text-danger">
                            @php
                                $totalBalance = $totalAmount - $totalPaid;
                                echo number_format($totalBalance, 0, ',', ' ') . ' Ar';
                            @endphp
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglets -->
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <ul class="nav nav-tabs nav-tabs-bottom card-header-tabs mx-0">
                    <li class="nav-item">
                        <a href="#all-uc" class="nav-link active" data-toggle="tab">
                            <i class="icon-exclamation mr-1 text-danger"></i>
                            Paiements incomplets
                            <span class="badge badge-pill badge-danger ml-1">{{ count($uncleared) }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#all-cl" class="nav-link" data-toggle="tab">
                            <i class="icon-checkmark-circle mr-1 text-success"></i>
                            Paiements complets
                            <span class="badge badge-pill badge-success ml-1">{{ count($cleared) }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content">
                    <!-- Paiements incomplets -->
                    <div class="tab-pane fade show active" id="all-uc">
                        <div class="table-responsive">
                            <table id="table-uncleared" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Titre</th>
                                        <th width="10%">Montant</th>
                                        <th width="10%">Payé</th>
                                        <th width="10%">Reste</th>
                                        <th width="30%">Paiement</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($uncleared as $uc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="font-weight-semibold">{{ $uc->payment->title }}</div>
                                                <div class="text-muted small">
                                                    <span class="badge badge-info">Réf: {{ $uc->payment->ref_no }}</span>
                                                    <span class="badge badge-secondary">Reçu: {{ $uc->ref_no }}</span>
                                                </div>
                                            </td>

                                            <!-- Montant -->
                                            <td class="font-weight-bold" id="amt-{{ Qs::hash($uc->id) }}" data-amount="{{ $uc->payment->amount }}">
                                                {{ number_format($uc->payment->amount, 0, ',', ' ') }} Ar
                                            </td>

                                            <!-- Montant payé -->
                                            <td id="amt_paid-{{ Qs::hash($uc->id) }}" data-amount="{{ $uc->amt_paid ?: 0 }}" class="text-success font-weight-bold">
                                                {{ number_format($uc->amt_paid ?: 0, 0, ',', ' ') }} Ar
                                            </td>

                                            <!-- Solde -->
                                            <td id="bal-{{ Qs::hash($uc->id) }}" class="text-danger font-weight-bold">
                                                {{ number_format($uc->balance ?: $uc->payment->amount, 0, ',', ' ') }} Ar
                                            </td>

                                            <!-- Formulaire de paiement -->
                                            <td>
                                                <form id="{{ Qs::hash($uc->id) }}" method="post" class="ajax-pay" action="{{ route('payments.pay_now', Qs::hash($uc->id)) }}">
                                                    @csrf
                                                    <div class="form-row">
                                                        <div class="col-md-5 mb-2">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="icon-cash"></i></span>
                                                                </div>
                                                                <input min="1" max="{{ $uc->balance ?: $uc->payment->amount }}"
                                                                    id="val-{{ Qs::hash($uc->id) }}"
                                                                    class="form-control"
                                                                    required
                                                                    placeholder="Montant"
                                                                    title="Montant à payer"
                                                                    name="amt_paid"
                                                                    type="number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <select name="methode" class="form-control">
                                                                <option value="cash">Cash</option>
                                                                <option value="Adra">ADRA</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <button data-text="Payer" class="btn btn-primary btn-block" type="submit">
                                                                <i class="icon-paperplane mr-1"></i> Payer
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <a target="_blank" href="{{ route('payments.receipts', Qs::hash($uc->id)) }}" class="btn btn-outline-primary btn-sm" data-toggle="tooltip" title="Imprimer le reçu">
                                                        <i class="icon-printer"></i>
                                                    </a>
                                                    <button type="button" id="{{ Qs::hash($uc->id) }}" onclick="confirmReset(this.id)" class="btn btn-outline-danger btn-sm ml-1" data-toggle="tooltip" title="Réinitialiser le paiement">
                                                        <i class="icon-reset"></i>
                                                    </button>
                                                    <form method="post" id="item-reset-{{ Qs::hash($uc->id) }}" action="{{ route('payments.reset_record', Qs::hash($uc->id)) }}" class="hidden">@csrf @method('delete')</form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Paiements complets -->
                    <div class="tab-pane fade" id="all-cl">
                        <div class="table-responsive">
                            <table id="table-cleared" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Titre</th>
                                        <th width="15%">Montant</th>
                                        <th width="15%">Année</th>
                                        <th width="15%">Statut</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cleared as $cl)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="font-weight-semibold">{{ $cl->payment->title }}</div>
                                                <div class="text-muted small">
                                                    <span class="badge badge-info">Réf: {{ $cl->payment->ref_no }}</span>
                                                    <span class="badge badge-secondary">Reçu: {{ $cl->ref_no }}</span>
                                                </div>
                                            </td>

                                            <!-- Montant -->
                                            <td class="font-weight-bold">
                                                {{ number_format($cl->payment->amount, 0, ',', ' ') }} Ar
                                            </td>

                                            <td>{{ $cl->year }}</td>

                                            <td>
                                                <span class="badge badge-success badge-pill">Payé</span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <a target="_blank" href="{{ route('payments.receipts', Qs::hash($cl->id)) }}" class="btn btn-outline-primary btn-sm" data-toggle="tooltip" title="Imprimer le reçu">
                                                        <i class="icon-printer"></i>
                                                    </a>
                                                    <button type="button" id="{{ Qs::hash($cl->id) }}" onclick="confirmReset(this.id)" class="btn btn-outline-danger btn-sm ml-1" data-toggle="tooltip" title="Réinitialiser le paiement">
                                                        <i class="icon-reset"></i>
                                                    </button>
                                                    <form method="post" id="item-reset-{{ Qs::hash($cl->id) }}" action="{{ route('payments.reset_record', Qs::hash($cl->id)) }}" class="hidden">@csrf @method('delete')</form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Désactiver l'initialisation automatique des DataTables
    $.fn.dataTable.ext.classes.sTable = 'table table-striped';

    $(document).ready(function() {
        // Initialiser les tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Options communes pour les DataTables
        var dataTableOptions = {
            responsive: true,
            language: {
                search: '<span>Rechercher :</span> _INPUT_',
                searchPlaceholder: 'Tapez pour filtrer...',
                lengthMenu: '<span>Afficher :</span> _MENU_',
                paginate: { 'first': 'Premier', 'last': 'Dernier', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
            },
            buttons: {
                buttons: [
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-light'
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-light'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-light'
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="icon-three-bars"></i> Visibilité',
                        className: 'btn bg-blue btn-icon dropdown-toggle'
                    }
                ]
            }
        };

        // Initialiser le tableau des paiements incomplets
        var tableUncleared = $('#table-uncleared').DataTable(dataTableOptions);

        // Variable pour stocker l'instance du tableau des paiements complets
        var tableCleared = null;

        // Initialiser le tableau des paiements complets uniquement lorsque l'onglet est affiché
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target === '#all-cl' && tableCleared === null) {
                tableCleared = $('#table-cleared').DataTable(dataTableOptions);
            }
        });
    });

    // Fonction pour confirmer la réinitialisation d'un paiement
    function confirmReset(id) {
        if (confirm("Êtes-vous sûr de vouloir réinitialiser ce paiement ? Cette action ne peut pas être annulée.")) {
            $('#item-reset-'+id).submit();
        }
    }
</script>
@endsection
