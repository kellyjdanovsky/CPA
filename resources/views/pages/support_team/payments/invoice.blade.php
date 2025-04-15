@extends('layouts.master')
@section('page_title', 'Gérer les paiements')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-bold">Gestion des paiements pour {{ $sr->user->name}} </h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-uc" class="nav-link active" data-toggle="tab"> Paiements incomplets</a></li>
                <li class="nav-item"><a href="#all-cl" class="nav-link" data-toggle="tab">Paiements complets</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-uc">
                    <table class="table datatable-button-html5-columns table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Ref</th>
                            <th>Montant</th>
                            <th>Montant payé</th>
                            <th>Reste à payer</th>
                            <th>Payer maintenant</th>
                            <th>Methode payement</th>
                            <th>No Recu</th>
                            <th>Année</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($uncleared as $uc)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $uc->payment->title }}</td>
                                <td>{{ $uc->payment->ref_no }}</td>

                                {{--Montant--}}
                                <td class="font-weight-bold" id="amt-{{ Qs::hash($uc->id) }}" data-amount="{{ $uc->payment->amount }}">{{ $uc->payment->amount }}</td>

                                {{--Montant payé--}}
                                <td id="amt_paid-{{ Qs::hash($uc->id) }}" data-amount="{{ $uc->amt_paid ?: 0 }}" class="text-blue font-weight-bold">{{ $uc->amt_paid ?: '0.00' }}</td>

                                {{--Solde--}}
                                <td id="bal-{{ Qs::hash($uc->id) }}" class="text-danger font-weight-bold">{{ $uc->balance ?: $uc->payment->amount }}</td>

                                {{--Formulaire de paiement maintenant--}}
                                <td>
                                    <form id="{{ Qs::hash($uc->id) }}" method="post" class="ajax-pay" action="{{ route('payments.pay_now', Qs::hash($uc->id)) }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-7">
                                                <input min="1" max="{{ $uc->balance ?: $uc->payment->amount }}" id="val-{{ Qs::hash($uc->id) }}" class="form-control" required placeholder="Payer" title="Payer maintenant" name="amt_paid" type="number">
                                            </div>
                                            <div class="col-md-5">
                                                <button data-text="Payer" class="btn btn-danger" type="submit">Payer <i class="icon-paperplane ml-2"></i></button>
                                            </div>
                                        </div>
                                    
                                </td>
                                <td>
                                    <div class="col-md-5"></div>
                                    <select  name="methode" >
                                    <option value="cash">Cash</option>
                                    <option value="Adra">Adra</option>
                                </select>
                            </form>
                                </td>
                                {{--No Recu--}}
                                <td>{{ $uc->ref_no }}</td>
                               



                                <td>{{ $uc->year }}</td>

                                {{--Action--}}
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">

                                                {{--Réinitialiser le paiement--}}
                                                <a id="{{ Qs::hash($uc->id) }}" onclick="confirmReset(this.id)" href="#" class="dropdown-item"><i class="icon-reset"></i> Réinitialiser le paiement</a>
                                                <form method="post" id="item-reset-{{ Qs::hash($uc->id) }}" action="{{ route('payments.reset_record', Qs::hash($uc->id)) }}" class="hidden">@csrf @method('delete')</form>

                                                {{--Reçu--}}
                                                <a target="_blank" href="{{ route('payments.receipts', Qs::hash($uc->id)) }}" class="dropdown-item"><i class="icon-printer"></i> Imprimer le reçu</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="all-cl">
                    <table class="table datatable-button-html5-columns table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>_Ref</th>
                            <th>Montant</th>
                            <th>No_recu</th>
                            <th>Année</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cleared as $cl)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $cl->payment->title }}</td>
                                <td>{{ $cl->payment->ref_no }}</td>

                                {{--Montant--}}
                                <td class="font-weight-bold">{{ $cl->payment->amount }}</td>
                                {{--No Recu--}}
                                <td>{{ $cl->ref_no }}</td>

                                <td>{{ $cl->year }}</td>

                                {{--Action--}}
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">

                                                {{--Réinitialiser le paiement--}}
                                                <a id="{{ Qs::hash($cl->id) }}" onclick="confirmReset(this.id)" href="#" class="dropdown-item"><i class="icon-reset"></i> Réinitialiser le paiement</a>
                                                <form method="post" id="item-reset-{{ Qs::hash($cl->id) }}" action="{{ route('payments.reset_record', Qs::hash($cl->id)) }}" class="hidden">@csrf @method('delete')</form>

                                                {{--Reçu--}}
                                                <a target="_blank" href="{{ route('payments.receipts', Qs::hash($cl->id)) }}" class="dropdown-item"><i class="icon-printer"></i> Imprimer le reçu</a>
                                            </div>
                                        </div>
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

    {{--Liste des factures de paiement terminée--}}

@endsection
