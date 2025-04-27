@extends('layouts.master')
@section('page_title', 'Paiement des Étudiants')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2"></i> Paiement des Étudiants</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#manage-payments" class="nav-link active" data-toggle="tab">Gestion des paiements</a></li>
                <li class="nav-item"><a href="#special-receipts" class="nav-link" data-toggle="tab">Reçus ADRA/TEAM3</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="manage-payments">
                    <form method="post" action="{{ route('payments.select_class') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="my_class_id" class="col-form-label font-weight-bold">Classe :</label>
                                            <select required id="my_class_id" name="my_class_id" class="form-control select">
                                                <option value="">Choisir une classe</option>
                                                @foreach($my_classes as $c)
                                                    <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2 mt-4">
                                        <div class="text-right mt-1">
                                            <button type="submit" class="btn btn-primary">Valider <i class="icon-paperplane ml-2"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="special-receipts">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="card">
                                <div class="card-header bg-indigo-400 text-white header-elements-inline">
                                    <h6 class="card-title">Générer des reçus pour les élèves ADRA et TEAM3</h6>
                                </div>

                                <div class="card-body">
                                    <form method="post" action="{{ route('payments.generate_special_receipts') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="class_id" class="font-weight-bold">Classe :</label>
                                                    <select required id="class_id" name="class_id" class="form-control select">
                                                        <option value="">Choisir une classe</option>
                                                        @foreach($my_classes as $c)
                                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="payment_id" class="font-weight-bold">Motif de paiement :</label>
                                                    <select required id="payment_id" name="payment_id" class="form-control select">
                                                        <option value="">Choisir un motif</option>
                                                        @foreach($payments as $p)
                                                            <option value="{{ $p->id }}">{{ $p->title }} - {{ $p->amount }} Ar</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <strong>Note :</strong> Cette action va générer des reçus pour tous les élèves ADRA et TEAM3 de la classe sélectionnée :
                                            <ul>
                                                <li>Pour les élèves ADRA : 75% du montant total sera facturé</li>
                                                <li>Pour les élèves TEAM3 : 100% du montant total sera facturé</li>
                                            </ul>
                                            Le mode de paiement sera automatiquement défini sur "ADRA".
                                            <p class="mt-2 mb-0"><strong>Après génération, vous serez automatiquement redirigé vers la page d'impression des reçus (format 58mm pour imprimante thermique).</strong></p>
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Générer et imprimer les reçus <i class="icon-printer ml-2"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($selected)
        <div class="card">
            <div class="card-body">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Ref_No</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->adm_no }}</td>
                            <td>
                                @php
                                    $status = $s->user->status ?? 'Normal';
                                    $statusClass = '';
                                    $statusText = $status;

                                    // Définir la classe CSS en fonction du statut
                                    if ($status == 'Normal') {
                                        $statusClass = 'badge badge-success';
                                    } elseif ($status == 'ADRA') {
                                        $statusClass = 'badge badge-info';
                                    } elseif ($status == 'Team3') {
                                        $statusClass = 'badge badge-warning';
                                    }
                                @endphp
                                <span class="{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a href="#" class=" btn btn-danger" data-toggle="dropdown"> Gestion par session <i class="icon-arrow-down5"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ route('payments.invoice', [Qs::hash($s->user_id)]) }}" class="dropdown-item">Tous les paiements</a>
                                        @foreach(Pay::getYears($s->user_id) as $py)
                                            @if($py)
                                                <a href="{{ route('payments.invoice', [Qs::hash($s->user_id), $py]) }}" class="dropdown-item">{{ $py }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
