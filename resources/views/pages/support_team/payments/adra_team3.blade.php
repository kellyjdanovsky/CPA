@extends('layouts.master')
@section('page_title', 'Reçu ADRA & TEAM 3')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/adra_team3.css') }}">
@endsection

@section('content')

<div class="card adra-team3-card">
    <div class="adra-team3-header">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="icon-printer4 icon-2x"></i>
            </div>
            <div>
                <h4 class="mb-0 font-weight-semibold">Reçu ADRA & TEAM 3</h4>
                <span class="text-white-50">Génération de reçus personnalisés pour les élèves ADRA et TEAM 3</span>
            </div>
            <div class="ml-auto">
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="get" action="{{ route('payments.adra_team3.filter') }}">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card bg-light">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="icon-filter3 mr-2"></i>Filtres de sélection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="class_id" class="font-weight-bold">
                                            <i class="icon-users4 mr-2"></i>Classe
                                        </label>
                                        <select required id="class_id" name="class_id" class="form-control select">
                                            <option value="">Choisir une classe</option>
                                            @foreach($my_classes as $c)
                                                <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status_filter" class="font-weight-bold">
                                            <i class="icon-user-check mr-2"></i>Statut des élèves
                                        </label>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="status[]" id="status_adra" value="ADRA" checked>
                                                <label class="form-check-label" for="status_adra">ADRA</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="status[]" id="status_team3" value="TEAM3" checked>
                                                <label class="form-check-label" for="status_team3">TEAM 3</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="payments_container" class="form-group mt-3" style="{{ !$selected ? 'display: none;' : '' }}">
                                <label for="payments" class="font-weight-bold">
                                    <i class="icon-cash3 mr-2"></i>Paiements à inclure
                                </label>
                                <div class="border p-3 rounded bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Sélectionnez les paiements à inclure dans les reçus</span>
                                        <div>
                                            <button type="button" id="select_all_payments" class="btn btn-sm btn-outline-primary">
                                                <i class="icon-checkbox-checked mr-1"></i>Tout sélectionner
                                            </button>
                                            <button type="button" id="deselect_all_payments" class="btn btn-sm btn-outline-danger ml-1">
                                                <i class="icon-checkbox-unchecked mr-1"></i>Tout désélectionner
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row" id="payments_list">
                                        @if($selected && isset($payments) && $payments->count() > 0)
                                            @foreach($payments as $payment)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input payment-checkbox" type="checkbox" 
                                                            name="payments[]" id="payment_{{ $payment->id }}" 
                                                            value="{{ $payment->id }}" 
                                                            data-amount="{{ $payment->amount }}">
                                                        <label class="form-check-label" for="payment_{{ $payment->id }}">
                                                            {{ $payment->title }} - {{ number_format($payment->amount, 0, ',', ' ') }} Ar
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-filter3 mr-2"></i>Filtrer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selected && isset($students) && $students->count() > 0)
    <div class="card adra-team3-card">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title">
                <i class="icon-users2 mr-2 text-primary"></i> Élèves ADRA & TEAM 3
            </h5>
            <div class="header-elements">
                <div class="btn-group">
                    <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#bulk-print-modal" {{ (!$selected || !$my_class_id || $students->isEmpty()) ? 'disabled' : '' }}>
                        <i class="icon-printer4 mr-2"></i> Imprimer tous les reçus
                    </button>
                    <a href="{{ $selected && $my_class_id ? route('payments.adra_team3.export_excel', ['class_id' => $my_class_id, 'payments' => isset($selected_payments) ? implode(',', $selected_payments->pluck('id')->toArray()) : '', 'status' => isset($status_filter) ? implode(',', (array)$status_filter) : 'ADRA,TEAM3']) : '#' }}" 
                       class="btn btn-primary" 
                       {{ (!$selected || !$my_class_id || $students->isEmpty()) ? 'disabled' : '' }}>
                        <i class="icon-file-excel mr-2"></i> Exporter vers Excel
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="icon-users4 icon-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ count($students) }} élève(s)</h5>
                                        <div class="text-muted">
                                            <span class="badge badge-info">ADRA: {{ $students->where('user.status', 'ADRA')->count() }}</span>
                                            <span class="badge badge-warning ml-1">TEAM3: {{ $students->where('user.status', 'TEAM3')->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="icon-cash3 icon-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ isset($selected_payments) ? count($selected_payments) : count($payments) }} paiement(s)</h5>
                                        <span class="text-muted">Année: {{ Qs::getCurrentSession() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="icon-graduation2 icon-2x text-indigo"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $my_class_id ? $students->first()->my_class->name : 'Toutes les classes' }}</h5>
                                        <span class="text-muted">Classe sélectionnée</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card card-table shadow-sm mb-3">
                <div class="card-header bg-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-semibold mb-0">
                            <i class="icon-list3 mr-2 text-primary"></i>Liste des élèves et reçus à imprimer
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="icon-filter3"></i>
                                        </span>
                                    </div>
                                    <select id="status-filter" class="form-control">
                                        <option value="">Tous les statuts</option>
                                        <option value="ADRA">ADRA (75%)</option>
                                        <option value="TEAM3">TEAM 3 (100%)</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button type="button" id="refresh-table" class="btn btn-light">
                                    <i class="icon-sync mr-1"></i> Actualiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                

                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered datatable-adra-team3" id="adra-team3-table">
                        <thead class="thead-primary">
                            <tr>
                                <th width="25%">Élève</th>
                                <th width="10%">Classe</th>
                                <th width="10%">Statut</th>
                                <th width="15%">Montant</th>
                                <th width="15%">Code référence</th>
                                <th width="25%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($students->count() > 0)
                            @foreach($students as $student)
                                @php
                                    $status = $student->user->status;
                                    $percentage = ($status == 'ADRA') ? 0.75 : 1.0;
                                    $totalAmount = 0;
                                    $selectedPayments = [];
                                    
                                    // Si des paiements ont été sélectionnés dans le filtre
                                    if(isset($selected_payments) && count($selected_payments) > 0) {
                                        foreach($selected_payments as $payment) {
                                            $totalAmount += $payment->amount * $percentage;
                                            $selectedPayments[] = $payment->title;
                                        }
                                    } else {
                                        // Sinon, utiliser tous les paiements disponibles
                                        foreach($payments as $payment) {
                                            $totalAmount += $payment->amount * $percentage;
                                            $selectedPayments[] = $payment->title;
                                        }
                                    }
                                    
                                    // Vérifier si l'élève a déjà des paiements enregistrés
                                    $hasPayments = false;
                                    foreach($payment_records as $pr) {
                                        if ($pr->student_id == $student->user_id && $pr->paid) {
                                            $hasPayments = true;
                                            break;
                                        }
                                    }
                                    
                                    // Déterminer la classe CSS selon le statut
                                    $statusClass = $status == 'ADRA' ? 'adra-row' : ($status == 'TEAM3' ? 'team3-row' : '');
                                @endphp
                            <tr class="{{ $statusClass }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <img src="{{ $student->user->photo ? asset('storage/uploads/'.$student->user->photo) : asset('global_assets/images/user.png') }}" 
                                                class="rounded-circle" width="40" height="40" alt="">
                                        </div>
                                        <div>
                                            <div class="font-weight-semibold">{{ $student->user->name }}</div>
                                            <div class="text-muted font-size-sm">
                                                <i class="icon-envelop5 font-size-sm mr-1"></i>{{ $student->user->email ?? 'Non défini' }}
                                            </div>
                                            <div class="payment-tags mt-1">
                                                @if(count($selectedPayments) > 0)
                                                    @foreach($selectedPayments as $index => $title)
                                                        @if($index < 2)
                                                            <span class="badge badge-info badge-pill">{{ $title }}</span>
                                                        @elseif($index == 2)
                                                            <span class="badge badge-secondary badge-pill">+{{ count($selectedPayments) - 2 }}</span>
                                                            @break
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $student->my_class->name }}</td>
                                <td>
                                    @if($status == 'ADRA')
                                        <span class="adra-badge">ADRA (75%)</span>
                                    @elseif($status == 'TEAM3')
                                        <span class="team3-badge">TEAM 3 (100%)</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $status ?? 'Normal' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <span class="badge badge-pill {{ $status == 'ADRA' ? 'badge-info' : 'badge-warning' }}">{{ $status == 'ADRA' ? '75%' : '100%' }}</span>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold">{{ number_format($totalAmount, 0, ',', ' ') }} Ar</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control reference-code" 
                                            value="{{ $student->adm_no ?? '' }}" 
                                            placeholder="Code référence"
                                            data-student-id="{{ $student->id }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-outline-primary save-reference" type="button" title="Enregistrer">
                                                <i class="icon-floppy-disk"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-justified">
                                        <button type="button" class="btn {{ $status == 'ADRA' ? 'btn-info' : 'btn-warning' }} btn-sm mr-1" data-toggle="modal" data-target="#payment-modal-{{ $student->id }}">
                                            <i class="icon-clipboard3 mr-1"></i> Sélectionner
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" onclick="printReceipt({{ $student->id }}, '{{ $student->user_id }}')">
                                            <i class="icon-printer4 mr-1"></i> Imprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de sélection des paiements -->
                            <div id="payment-modal-{{ $student->id }}" class="modal fade" tabindex="-1" data-status="{{ $student->user->status }}">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('payments.adra_team3.generate_receipt') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->user_id }}">
                                            
                                            <div class="modal-header {{ $student->user->status == 'ADRA' ? 'bg-info' : 'bg-warning' }}">
                                                <h5 class="modal-title text-white">
                                                    <i class="icon-clipboard3 mr-2"></i>
                                                    Sélection des paiements pour {{ $student->user->name }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                                    <h5 class="alert-heading">Erreurs de validation</h5>
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif
                                                
                                                <div class="card mb-3">
                                                    <div class="card-body bg-light">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center mb-3">
                                                                    <div class="mr-3">
                                                                        <img src="{{ $student->user->photo ? asset('storage/uploads/'.$student->user->photo) : asset('global_assets/images/user.png') }}" 
                                                                            class="rounded-circle" width="50" height="50" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0 font-weight-semibold">{{ $student->user->name }}</h6>
                                                                        <span class="text-muted">Classe: {{ $student->my_class->name }}</span>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label class="font-weight-semibold">Statut:</label>
                                                                    @if($student->user->status == 'ADRA')
                                                                        <span class="adra-badge ml-2">ADRA (75% du montant)</span>
                                                                    @elseif($student->user->status == 'TEAM3')
                                                                        <span class="team3-badge ml-2">TEAM 3 (100% du montant)</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="reference_code_{{ $student->id }}" class="font-weight-semibold">Code de référence:</label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="icon-barcode2"></i></span>
                                                                        </div>
                                                                        <input type="text" name="reference_code" id="reference_code_{{ $student->id }}" 
                                                                            class="form-control reference-code-input" 
                                                                            value="{{ $student->adm_no ?? '' }}" 
                                                                            placeholder="Code de référence">
                                                                    </div>
                                                                    <small class="form-text text-muted">Ce code apparaîtra sur le reçu imprimé</small>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label class="font-weight-semibold d-block">Actions rapides:</label>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary select-all-btn" data-student-id="{{ $student->id }}">
                                                                        <i class="icon-checkbox-checked mr-1"></i> Tout sélectionner
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger deselect-all-btn" data-student-id="{{ $student->id }}">
                                                                        <i class="icon-checkbox-unchecked mr-1"></i> Tout désélectionner
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr class="{{ $student->user->status == 'ADRA' ? 'bg-info' : 'bg-warning' }}">
                                                                <th style="width: 50px;" class="text-center">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input select-all" id="select-all-{{ $student->id }}">
                                                                        <label class="custom-control-label text-white" for="select-all-{{ $student->id }}"></label>
                                                                    </div>
                                                                </th>
                                                                <th class="text-white">Paiement</th>
                                                                <th class="text-white">Montant original</th>
                                                                <th class="text-white">Montant à payer ({{ ($student->user->status == 'ADRA') ? '75%' : '100%' }})</th>
                                                                <th class="text-white">Statut</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $percentage = ($student->user->status == 'ADRA') ? 0.75 : 1.0;
                                                                $totalAmount = 0;
                                                                $paymentsList = isset($selected_payments) && $selected_payments->count() > 0 ? $selected_payments : $payments;
                                                            @endphp
                                                            
                                                            @foreach($paymentsList as $payment)
                                                                @php
                                                                    $amountToPay = $payment->amount * $percentage;
                                                                    // Vérifier si le paiement est déjà effectué
                                                                    $isPaid = false;
                                                                    foreach($payment_records as $pr) {
                                                                        if ($pr->student_id == $student->user_id && $pr->payment_id == $payment->id && $pr->paid) {
                                                                            $isPaid = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                <tr class="payment-row">
                                                                    <td class="text-center">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox" class="custom-control-input payment-checkbox" 
                                                                                id="payment-{{ $student->id }}-{{ $payment->id }}" 
                                                                                name="payments[]" value="{{ $payment->id }}"
                                                                                data-amount="{{ $amountToPay }}"
                                                                                data-title="{{ $payment->title }}"
                                                                                {{ $isPaid ? 'disabled' : '' }}>
                                                                            <label class="custom-control-label" for="payment-{{ $student->id }}-{{ $payment->id }}"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="font-weight-semibold">{{ $payment->title }}</span>
                                                                        <div class="text-muted font-size-sm">{{ $payment->description ?? 'Aucune description' }}</div>
                                                                    </td>
                                                                    <td>{{ number_format($payment->amount, 0, ',', ' ') }} Ar</td>
                                                                    <td>{{ number_format($amountToPay, 0, ',', ' ') }} Ar</td>
                                                                    <td>
                                                                        @if($isPaid)
                                                                            <span class="badge badge-success">Déjà payé</span>
                                                                        @else
                                                                            <span class="badge badge-secondary">Non payé</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <div class="card mt-3 {{ $student->user->status == 'ADRA' ? 'border-info' : 'border-warning' }}">
                                                    <div class="card-header {{ $student->user->status == 'ADRA' ? 'bg-info' : 'bg-warning' }} text-white">
                                                        <h6 class="mb-0">Résumé du paiement</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <span class="font-weight-semibold">Montant total à payer:</span>
                                                                <div class="text-muted font-size-sm">
                                                                    Basé sur les paiements sélectionnés ({{ $student->user->status == 'ADRA' ? '75%' : '100%' }} du montant original)
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <span class="total-amount-value font-weight-bold {{ $student->user->status == 'ADRA' ? 'text-info' : 'text-warning' }}" style="font-size: 24px;">0 Ar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link" data-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn {{ $student->user->status == 'ADRA' ? 'btn-info' : 'btn-warning' }}" id="generate-receipt-btn-{{ $student->id }}">
                                                    <i class="icon-printer4 mr-2"></i> Générer le reçu
                                                </button>
                                            </div>
                                            
                                            <script>
                                                // Vérifier qu'au moins un paiement est sélectionné avant la soumission
                                                $(document).ready(function() {
                                                    $('#payment-modal-{{ $student->id }} form').on('submit', function(e) {
                                                        var checkedPayments = $(this).find('.payment-checkbox:checked').length;
                                                        
                                                        if (checkedPayments === 0) {
                                                            e.preventDefault();
                                                            
                                                            // Afficher une notification d'erreur
                                                            new Noty({
                                                                text: 'Veuillez sélectionner au moins un paiement',
                                                                type: 'error',
                                                                theme: 'limitless',
                                                                layout: 'topRight',
                                                                timeout: 3000
                                                            }).show();
                                                            
                                                            return false;
                                                        }
                                                        
                                                        // Tout est bon, continuer la soumission
                                                        return true;
                                                    });
                                                });
                                            </script>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Aucun élève ADRA ou TEAM 3 trouvé dans cette classe.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@elseif($selected)
    <div class="alert alert-info">
        Aucun élève avec le statut ADRA ou TEAM 3 n'a été trouvé dans cette classe.
    </div>
@endif

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Variables globales pour les paiements sélectionnés
        var selectedPayments = [];
        var selectedPaymentIds = [];
        
        // Initialisation de la DataTable avec des options améliorées
        try {
            console.log("Initialisation de la DataTable...");
            
            // Vérifier si la table existe et contient des données
            if ($('.datatable-button-html5-columns').length) {
                console.log("Table trouvée, initialisation...");
                
                // Détruire l'instance existante si elle existe
                if ($.fn.DataTable.isDataTable('.datatable-button-html5-columns')) {
                    $('.datatable-button-html5-columns').DataTable().destroy();
                }
                
                var dataTable = $('.datatable-button-html5-columns').DataTable({
                    language: {
                        search: '<span>Rechercher :</span> _INPUT_',
                        searchPlaceholder: 'Tapez pour filtrer...',
                        lengthMenu: '<span>Afficher :</span> _MENU_',
                        paginate: { 'first': 'Premier', 'last': 'Dernier', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' },
                        info: "Affichage de _START_ à _END_ sur _TOTAL_ élèves",
                        infoEmpty: "Aucun élève à afficher",
                        infoFiltered: "(filtré depuis _MAX_ élèves au total)",
                        zeroRecords: "Aucun élève correspondant trouvé",
                        emptyTable: "Aucun élève disponible"
                    },
                    dom: '<"datatable-header"Bfl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            text: '<i class="icon-copy3 mr-2"></i> Copier',
                            className: 'btn btn-light',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5]
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="icon-file-excel mr-2"></i> Excel',
                            className: 'btn btn-light',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5]
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="icon-file-pdf mr-2"></i> PDF',
                            className: 'btn btn-light',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5]
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="icon-printer mr-2"></i> Imprimer',
                            className: 'btn btn-light',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5]
                            }
                        }
                    ],
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [
                        { 
                            orderable: false,
                            targets: [3, 5, 6]
                        }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                    stateSave: true,
                    drawCallback: function() {
                        console.log("DataTable dessinée avec succès");
                        
                        // Réinitialiser les gestionnaires d'événements pour les éléments dynamiques
                        initializeEventHandlers();
                    }
                });
                
                console.log("DataTable initialisée avec succès");
            } else {
                console.error("Table non trouvée dans le DOM");
            }
        } catch (error) {
            console.error("Erreur lors de l'initialisation de la DataTable:", error);
        }
        
        // Fonction pour initialiser les gestionnaires d'événements
        function initializeEventHandlers() {
            // Gestion de la sauvegarde des codes de référence
            $('.save-reference').off('click').on('click', function() {
                var btn = $(this);
                var input = btn.closest('.input-group').find('.reference-code');
                var studentId = input.data('student-id');
                var referenceCode = input.val();
                
                saveReferenceCode(btn, studentId, referenceCode);
            });
            
            // Mise à jour du montant total lors de la sélection des paiements
            $('.payment-checkbox').off('change').on('change', function() {
                var modal = $(this).closest('.modal');
                updateTotalAmount(modal);
            });
            
            // Initialiser les tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Fonction pour sauvegarder le code de référence
        function saveReferenceCode(btn, studentId, referenceCode) {
            // Désactiver le bouton pendant la sauvegarde
            btn.prop('disabled', true).html('<i class="icon-spinner2 spinner"></i>');
            
            // Envoyer la requête AJAX pour sauvegarder le code de référence
            $.ajax({
                url: "{{ route('payments.adra_team3.save_reference') }}",
                type: 'POST',
                data: {
                    student_id: studentId,
                    reference_code: referenceCode,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Mettre à jour tous les champs de code de référence pour cet élève
                        $('.reference-code-input[id="reference_code_' + studentId + '"]').val(referenceCode);
                        $('.reference-code[data-student-id="' + studentId + '"]').val(referenceCode);
                        
                        // Réactiver le bouton avec une animation de succès
                        btn.removeClass('btn-outline-primary').addClass('btn-success')
                           .html('<i class="icon-checkmark4"></i>');
                        
                        // Afficher une notification de succès
                        new Noty({
                            text: response.message,
                            type: 'success',
                            theme: 'limitless',
                            layout: 'topRight',
                            timeout: 2500
                        }).show();
                    } else {
                        // Afficher une notification d'erreur
                        new Noty({
                            text: response.message || 'Une erreur est survenue',
                            type: 'error',
                            theme: 'limitless',
                            layout: 'topRight',
                            timeout: 3000
                        }).show();
                        
                        // Réactiver le bouton
                        btn.removeClass('btn-outline-primary').addClass('btn-danger')
                           .html('<i class="icon-cross2"></i>');
                    }
                    
                    // Rétablir le bouton après un délai
                    setTimeout(function() {
                        btn.removeClass('btn-success btn-danger').addClass('btn-outline-primary')
                           .html('<i class="icon-floppy-disk"></i>')
                           .prop('disabled', false);
                    }, 1500);
                },
                error: function(xhr) {
                    // Afficher une notification d'erreur
                    var errorMessage = 'Une erreur est survenue lors de la sauvegarde';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    new Noty({
                        text: errorMessage,
                        type: 'error',
                        theme: 'limitless',
                        layout: 'topRight',
                        timeout: 3000
                    }).show();
                    
                    // Réactiver le bouton
                    btn.removeClass('btn-outline-primary').addClass('btn-danger')
                       .html('<i class="icon-cross2"></i>');
                    
                    // Rétablir le bouton après un délai
                    setTimeout(function() {
                        btn.removeClass('btn-danger').addClass('btn-outline-primary')
                           .html('<i class="icon-floppy-disk"></i>')
                           .prop('disabled', false);
                    }, 1500);
                }
            });
        }
        
        // Filtrage par statut
        $('#status-filter').on('change', function() {
            var status = $(this).val();
            
            // Filtrer la colonne de statut (index 2)
            if (status) {
                dataTable.column(2).search(status).draw();
            } else {
                dataTable.column(2).search('').draw();
            }
        });
        
        // Bouton d'actualisation
        $('#refresh-table').on('click', function() {
            var btn = $(this);
            
            // Ajouter une classe pour l'animation de rotation
            btn.addClass('rotating');
            
            // Réinitialiser tous les filtres
            dataTable.search('').columns().search('').draw();
            $('#status-filter').val('');
            
            // Afficher une notification
            new Noty({
                text: 'Tableau actualisé avec succès',
                type: 'success',
                theme: 'limitless',
                layout: 'topRight',
                timeout: 2000
            }).show();
            
            // Retirer la classe d'animation après un délai
            setTimeout(function() {
                btn.removeClass('rotating');
            }, 1000);
        });
        
        // Gestion de la sélection des paiements dans le filtre
        $('#select_all_payments').on('click', function() {
            $('.payment-checkbox').prop('checked', true);
        });
        
        $('#deselect_all_payments').on('click', function() {
            $('.payment-checkbox').prop('checked', false);
        });
        
        // Gestion de la sélection de tous les paiements via checkbox dans les modals
        $('.select-all').change(function() {
            var isChecked = $(this).prop('checked');
            var modal = $(this).closest('.modal');
            
            modal.find('.payment-checkbox:not(:disabled)').prop('checked', isChecked);
            updateTotalAmount(modal);
        });
        
        // Gestion des boutons "Tout sélectionner" et "Tout désélectionner" dans les modals
        $('.select-all-btn').click(function() {
            var studentId = $(this).data('student-id');
            var modal = $('#payment-modal-' + studentId);
            
            modal.find('.payment-checkbox:not(:disabled)').prop('checked', true);
            modal.find('.select-all').prop('checked', true);
            updateTotalAmount(modal);
        });
        
        $('.deselect-all-btn').click(function() {
            var studentId = $(this).data('student-id');
            var modal = $('#payment-modal-' + studentId);
            
            modal.find('.payment-checkbox').prop('checked', false);
            modal.find('.select-all').prop('checked', false);
            updateTotalAmount(modal);
        });
        
        // Fonction pour mettre à jour le montant total dans les modals
        function updateTotalAmount(modal) {
            var total = 0;
            var status = modal.data('status') || 'NORMAL';
            var percentage = (status === 'ADRA') ? 0.75 : 1.0;
            var checkedCount = 0;
            
            modal.find('.payment-checkbox:checked').each(function() {
                var amount = parseFloat($(this).data('amount'));
                total += amount;
                checkedCount++;
            });
            
            // Formater le montant total
            var formattedTotal = formatAmount(total);
            modal.find('.total-amount-value').text(formattedTotal);
            
            // Mettre à jour le bouton de génération de reçu
            var submitBtn = modal.find('button[type="submit"]');
            if (checkedCount > 0) {
                submitBtn.prop('disabled', false);
                
                // Appliquer la classe de couleur appropriée selon le statut
                if (status === 'ADRA') {
                    submitBtn.removeClass('btn-secondary btn-warning').addClass('btn-info');
                } else if (status === 'TEAM3') {
                    submitBtn.removeClass('btn-secondary btn-info').addClass('btn-warning');
                } else {
                    submitBtn.removeClass('btn-secondary').addClass('btn-primary');
                }
            } else {
                submitBtn.prop('disabled', true).removeClass('btn-primary btn-info btn-warning').addClass('btn-secondary');
            }
            
            // Log pour débogage
            console.log('Modal: ' + modal.attr('id') + ', Paiements sélectionnés: ' + checkedCount + ', Total: ' + total);
        }
        
        // Initialiser les modales avec les montants totaux
        $('.modal').each(function() {
            updateTotalAmount($(this));
        });
        
        // Ajouter une classe pour l'animation de rotation
        $('<style>.rotating { animation: rotate 1s linear infinite; } @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>').appendTo('head');
        
        // Gestion de l'impression en masse
        $('#select-all-bulk-payments').on('click', function() {
            $('.bulk-payment-checkbox').prop('checked', true);
            updateBulkPrintTotals();
        });
        
        $('#deselect-all-bulk-payments').on('click', function() {
            $('.bulk-payment-checkbox').prop('checked', false);
            updateBulkPrintTotals();
        });
        
        // Mettre à jour les totaux lors de la sélection des paiements
        $('.bulk-payment-checkbox').on('change', function() {
            updateBulkPrintTotals();
        });
        
        // Fonction pour mettre à jour les totaux dans le modal d'impression en masse
        function updateBulkPrintTotals() {
            var totalAdra = 0;
            var totalTeam3 = 0;
            var selectedCount = 0;
            var selectedTitles = [];
            
            // Récupérer les paiements sélectionnés
            $('.bulk-payment-checkbox:checked').each(function() {
                var amount = parseFloat($(this).data('amount'));
                var title = $(this).data('title');
                
                totalAdra += amount * 0.75; // 75% pour ADRA
                totalTeam3 += amount; // 100% pour TEAM3
                
                selectedCount++;
                selectedTitles.push(title);
                selectedPaymentIds.push($(this).val());
            });
            
            // Mettre à jour les totaux affichés
            $('#bulk-print-adra-total').text(formatAmount(totalAdra));
            $('#bulk-print-team3-total').text(formatAmount(totalTeam3));
            $('#bulk-payment-count').text(selectedCount + ' paiement(s) sélectionné(s)');
            
            // Activer/désactiver le bouton d'impression selon la sélection
            if (selectedCount > 0) {
                $('#bulk-print-submit').prop('disabled', false);
            } else {
                $('#bulk-print-submit').prop('disabled', true);
            }
            
            // Stocker les paiements sélectionnés
            selectedPayments = selectedTitles;
        }
        
        // Formater un montant avec séparateur de milliers
        function formatAmount(amount) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' Ar';
        }
        
        // Initialiser les totaux d'impression en masse
        updateBulkPrintTotals();
        
        // Initialiser les gestionnaires d'événements
        initializeEventHandlers();
        
        // Mettre à jour le total des paiements sélectionnés dans le filtre
        $('.payment-checkbox').on('change', function() {
            updateFilterTotalAmount();
        });
        
        $('#select_all_payments').on('click', function() {
            $('.payment-checkbox').prop('checked', true);
            updateFilterTotalAmount();
        });
        
        $('#deselect_all_payments').on('click', function() {
            $('.payment-checkbox').prop('checked', false);
            updateFilterTotalAmount();
        });
        
        function updateFilterTotalAmount() {
            var total = 0;
            $('.payment-checkbox:checked').each(function() {
                total += parseFloat($(this).data('amount'));
            });
            $('#filter-total-amount').text(formatAmount(total));
        }
        
        // Initialiser le total des filtres
        updateFilterTotalAmount();
        
        // Initialiser le DataTable avec des fonctionnalités avancées
        var adraTeam3Table = $('.datatable-adra-team3').DataTable({
            autoWidth: false,
            dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                search: '<span>Rechercher :</span> _INPUT_',
                searchPlaceholder: 'Tapez pour filtrer...',
                lengthMenu: '<span>Afficher :</span> _MENU_',
                paginate: { 'first': 'Premier', 'last': 'Dernier', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' },
                info: 'Affichage de _START_ à _END_ sur _TOTAL_ élèves',
                emptyTable: 'Aucun élève trouvé',
                zeroRecords: 'Aucun résultat trouvé',
                infoEmpty: 'Aucun élève disponible',
                infoFiltered: '(filtré sur _MAX_ élèves au total)'
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="icon-file-excel mr-2"></i> Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    title: 'Liste des élèves ADRA & TEAM 3 - ' + new Date().toLocaleDateString('fr-FR')
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="icon-file-pdf mr-2"></i> PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    title: 'Liste des élèves ADRA & TEAM 3 - ' + new Date().toLocaleDateString('fr-FR')
                },
                {
                    extend: 'print',
                    text: '<i class="icon-printer mr-2"></i> Imprimer',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }
            ],
            columnDefs: [
                { 
                    orderable: false,
                    targets: [5]
                }
            ],
            order: [[0, 'asc']],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            pageLength: 25
        });
        
        // Filtrer le tableau par statut
        $('#status-filter').on('change', function() {
            var status = $(this).val();
            adraTeam3Table.column(2).search(status).draw();
        });
        
        // Bouton d'actualisation
        $('#refresh-table').on('click', function() {
            $('#status-filter').val('');
            adraTeam3Table.search('').columns().search('').draw();
        });
    });
    
    // Fonction pour imprimer le reçu d'un élève
    function printReceipt(studentId, userId) {
        // Vérifier si des paiements sont sélectionnés
        var selectedPayments = [];
        $('.payment-checkbox:checked').each(function() {
            selectedPayments.push($(this).val());
        });
        
        if (selectedPayments.length === 0) {
            // Afficher une notification d'erreur
            new Noty({
                text: 'Veuillez sélectionner au moins un paiement dans les filtres',
                type: 'error',
                theme: 'limitless',
                layout: 'topRight',
                timeout: 3000
            }).show();
            return;
        }
        
        // Récupérer le code de référence
        var referenceCode = $('.reference-code[data-student-id="' + studentId + '"]').val();
        
        // Créer un formulaire temporaire pour soumettre les données
        var form = $('<form>', {
            'method': 'post',
            'action': '{{ route("payments.adra_team3.generate_receipt") }}'
        });
        
        // Ajouter le token CSRF
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        // Ajouter l'ID de l'élève
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'student_id',
            'value': userId
        }));
        
        // Ajouter le code de référence
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'reference_code',
            'value': referenceCode
        }));
        
        // Ajouter les paiements sélectionnés
        $.each(selectedPayments, function(index, value) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'payments[]',
                'value': value
            }));
        });
        
        // Ajouter le formulaire au document et le soumettre
        $('body').append(form);
        form.submit();
    }
</script>

<!-- Modal d'impression en masse -->
@if($selected && $my_class_id && isset($students) && $students->count() > 0)
<div id="bulk-print-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('payments.adra_team3.bulk_generate_receipts') }}" method="post">
                @csrf
                <input type="hidden" name="class_id" value="{{ $my_class_id }}">
                
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">
                        <i class="icon-printer4 mr-2"></i>
                        Impression en masse des reçus pour la classe {{ $students->first()->my_class->name }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="icon-info22 mr-2"></i>
                        Cette fonctionnalité vous permet d'imprimer des reçus pour tous les élèves ADRA et TEAM 3 de la classe sélectionnée.
                        Les reçus seront générés avec les calculs appropriés selon le statut de chaque élève (75% pour ADRA, 100% pour TEAM 3).
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="icon-users4 icon-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $students->count() }} élève(s)</h5>
                                            <span class="text-muted">
                                                <span class="badge badge-info">ADRA: {{ $students->where('user.status', 'ADRA')->count() }}</span>
                                                <span class="badge badge-warning ml-1">TEAM 3: {{ $students->where('user.status', 'TEAM3')->count() }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="icon-cash3 icon-2x text-success"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0" id="bulk-payment-count">0 paiement(s) sélectionné(s)</h5>
                                            <span class="text-muted">Montant total variable selon le statut</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-semibold">Sélectionnez les paiements à inclure :</label>
                            <div>
                                <button type="button" id="select-all-bulk-payments" class="btn btn-sm btn-outline-primary">
                                    <i class="icon-checkbox-checked mr-1"></i>Tout sélectionner
                                </button>
                                <button type="button" id="deselect-all-bulk-payments" class="btn btn-sm btn-outline-danger ml-1">
                                    <i class="icon-checkbox-unchecked mr-1"></i>Tout désélectionner
                                </button>
                            </div>
                        </div>
                        
                        <div class="border p-3 rounded bg-white">
                            <div class="row">
                                @if(isset($selected_payments) && count($selected_payments) > 0)
                                    @foreach($selected_payments as $payment)
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input bulk-payment-checkbox" 
                                                    id="bulk-payment-{{ $payment->id }}" 
                                                    name="payments[]" 
                                                    value="{{ $payment->id }}"
                                                    data-amount="{{ $payment->amount }}"
                                                    data-title="{{ $payment->title }}">
                                                <label class="custom-control-label" for="bulk-payment-{{ $payment->id }}">
                                                    {{ $payment->title }} - {{ number_format($payment->amount, 0, ',', ' ') }} Ar
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($payments as $payment)
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input bulk-payment-checkbox" 
                                                    id="bulk-payment-{{ $payment->id }}" 
                                                    name="payments[]" 
                                                    value="{{ $payment->id }}"
                                                    data-amount="{{ $payment->amount }}"
                                                    data-title="{{ $payment->title }}">
                                                <label class="custom-control-label" for="bulk-payment-{{ $payment->id }}">
                                                    {{ $payment->title }} - {{ number_format($payment->amount, 0, ',', ' ') }} Ar
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card bg-light">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Aperçu des montants par statut</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge badge-info mr-1">ADRA</span>
                                            <span class="text-muted">(75% du montant)</span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 font-weight-bold text-primary" id="bulk-print-adra-total">0 Ar</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge badge-warning mr-1">TEAM 3</span>
                                            <span class="text-muted">(100% du montant)</span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 font-weight-bold text-primary" id="bulk-print-team3-total">0 Ar</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="icon-warning22 mr-2"></i>
                        <strong>Note:</strong> Cette action générera des reçus pour tous les élèves ADRA et TEAM 3 de la classe sélectionnée.
                        Assurez-vous que les codes de référence sont correctement définis pour chaque élève.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="bulk-print-submit" disabled>
                        <i class="icon-printer4 mr-2"></i> Générer et imprimer les reçus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection