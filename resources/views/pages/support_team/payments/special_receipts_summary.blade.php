@extends('layouts.master')
@section('page_title', 'Résumé des Reçus ADRA/TEAM3')
@section('content')

<div class="card fade-in">
    <div class="card-header bg-white header-elements-inline">
        <h5 class="card-title">
            <i class="icon-file-text2 mr-2 text-primary"></i> Résumé des Reçus ADRA/TEAM3
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="mr-3">
                        <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="icon-graduation2 text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="font-weight-semibold mb-1">Classe</h6>
                        <h4 class="mb-0">{{ $class_name }}</h4>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <div class="mr-3">
                        <div class="bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="icon-coin-dollar text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="font-weight-semibold mb-1">Motif de paiement</h6>
                        <h4 class="mb-0">{{ $payment_title }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <div class="d-inline-flex flex-column align-items-end">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3 text-right">
                            <h6 class="font-weight-semibold mb-0">Date</h6>
                            <h4 class="mb-0">{{ date('d/m/Y') }}</h4>
                        </div>
                        <div class="bg-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="icon-calendar text-white"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="mr-3 text-right">
                            <h6 class="font-weight-semibold mb-0">Nombre total d'élèves</h6>
                            <h4 class="mb-0 font-weight-bold">{{ count($payment_records) }}</h4>
                        </div>
                        <div class="bg-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="icon-users4 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé des statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalBilledAmount ?? 0, 0, ',', ' ') }} Ar</h3>
                                <span class="text-uppercase font-size-xs">Montant total facturé</span>
                            </div>
                            <div class="ml-auto">
                                <i class="icon-cash3 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div>
                                <h3 class="mb-0 font-weight-bold">{{ $totalAdraStudents ?? 0 }}</h3>
                                <span class="text-uppercase font-size-xs">Élèves ADRA</span>
                            </div>
                            <div class="ml-auto">
                                <i class="icon-user-check icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div>
                                <h3 class="mb-0 font-weight-bold">{{ $totalTeam3Students ?? 0 }}</h3>
                                <span class="text-uppercase font-size-xs">Élèves TEAM3</span>
                            </div>
                            <div class="ml-auto">
                                <i class="icon-user-plus icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div>
                                <h3 class="mb-0">{{ count($adraUnpaidStudents ?? []) }}</h3>
                                <span class="text-uppercase font-size-xs">Élèves avec impayés</span>
                            </div>
                            <div class="ml-auto">
                                <i class="icon-alert icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">
                    <i class="icon-list3 mr-2 text-primary"></i> Liste détaillée des élèves
                </h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover datatable-responsive">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">N°</th>
                                <th>Nom de l'élève</th>
                                <th class="text-center">Statut</th>
                                <th class="text-right">Montant total</th>
                                <th class="text-right">Montant facturé</th>
                                @if($has_adra)
                                <th class="text-right">Montant cash (25%)</th>
                                <th class="text-right">Cash payé</th>
                                <th class="text-right">Cash restant</th>
                                <th class="text-center">État cash</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAmount = 0;
                                $totalBilledAmount = 0;
                                $totalCashAmount = 0;
                                $totalCashPaid = 0;
                                $totalCashRemaining = 0;
                                $totalAdraStudents = 0;
                                $totalTeam3Students = 0;
                                $totalPaidAmount = 0;
                                $totalRemainingAmount = 0;
                                $adraUnpaidStudents = [];
                            @endphp

                    @foreach($payment_records as $index => $pr)
                        @php
                            $sr = $pr['student'];
                            $payment = $pr['payment'];
                            $status = $sr->user->status ?? 'Normal';
                            $amount = $payment->amount;
                            $billedAmount = $amount;
                            $cashAmount = 0;
                            $cashPaid = 0;
                            $cashRemaining = 0;
                            $cashStatus = 'N/A';
                            $cashStatusClass = '';
                            $alreadyPaid = $pr['already_paid'] ?? false;
                            $paidAmount = $pr['paid_amount'] ?? 0;
                            $paymentStatus = $alreadyPaid ? 'Acquitté' : 'En cours';
                            $statusClass = $alreadyPaid ? 'badge-success' : 'badge-warning';

                            if ($status == 'ADRA') {
                                $billedAmount = $amount * 0.75;
                                $cashAmount = $amount * 0.25;
                                $totalAdraStudents++;

                                // Récupérer l'enregistrement de paiement pour vérifier les paiements en cash
                                $paymentRecord = \App\Models\PaymentRecord::with('receipt')
                                    ->where('student_id', $sr->user_id)
                                    ->where('payment_id', $payment->id)
                                    ->first();

                                if ($paymentRecord && $paymentRecord->receipt) {
                                    // Compter uniquement les paiements non-ADRA comme cash
                                    foreach ($paymentRecord->receipt as $receipt) {
                                        if ($receipt->payment_method != 'ADRA') {
                                            $cashPaid += $receipt->amt_paid;
                                        }
                                    }
                                }

                                $cashRemaining = $cashAmount - $cashPaid;
                                if ($cashRemaining < 0) $cashRemaining = 0;

                                if ($cashPaid >= $cashAmount) {
                                    $cashStatus = 'Acquitté';
                                    $cashStatusClass = 'badge-success';
                                } else {
                                    $cashStatus = 'En cours';
                                    $cashStatusClass = 'badge-warning';

                                    // Ajouter cet élève à la liste des élèves ADRA qui n'ont pas payé leur 25%
                                    $adraUnpaidStudents[] = [
                                        'student' => $sr,
                                        'payment' => $payment,
                                        'cashAmount' => $cashAmount,
                                        'cashPaid' => $cashPaid,
                                        'cashRemaining' => $cashRemaining
                                    ];
                                }
                            } elseif ($status == 'TEAM3') {
                                $totalTeam3Students++;
                            }

                            $totalAmount += $amount;
                            $totalBilledAmount += $billedAmount;
                            $totalCashAmount += $cashAmount;
                            $totalPaidAmount += $paidAmount;
                            $totalCashPaid += $cashPaid;
                            $totalCashRemaining += $cashRemaining;
                            $remainingAmount = $billedAmount - $paidAmount;
                            if ($remainingAmount < 0) $remainingAmount = 0;
                            $totalRemainingAmount += $remainingAmount;
                        @endphp
                        <tr class="{{ $status == 'ADRA' && $cashRemaining > 0 ? 'bg-warning-50' : '' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @if($status == 'ADRA')
                                            <div class="bg-info rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="icon-user-check text-white"></i>
                                            </div>
                                        @elseif($status == 'TEAM3')
                                            <div class="bg-warning rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="icon-user-plus text-white"></i>
                                            </div>
                                        @else
                                            <div class="bg-success rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="icon-user text-white"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-semibold">{{ $sr->user->name }}</div>
                                        <div class="text-muted font-size-sm">{{ $sr->my_class->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div>
                                    @if($status == 'ADRA')
                                        <span class="badge badge-flat badge-pill badge-info">ADRA</span>
                                    @elseif($status == 'TEAM3')
                                        <span class="badge badge-flat badge-pill badge-warning">TEAM3</span>
                                    @else
                                        <span class="badge badge-flat badge-pill badge-success">Normal</span>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <span class="badge badge-flat badge-pill {{ $statusClass }}">{{ $paymentStatus }}</span>
                                </div>
                            </td>
                            <td class="text-right font-weight-semibold">{{ number_format($amount, 0, ',', ' ') }} Ar</td>
                            <td class="text-right font-weight-semibold">{{ number_format($billedAmount, 0, ',', ' ') }} Ar</td>
                            @if($has_adra)
                            <td class="text-right">
                                @if($status == 'ADRA')
                                    <span class="font-weight-semibold">{{ number_format($cashAmount, 0, ',', ' ') }} Ar</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($status == 'ADRA')
                                    <span class="font-weight-semibold text-success">{{ number_format($cashPaid, 0, ',', ' ') }} Ar</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($status == 'ADRA')
                                    @if($cashRemaining > 0)
                                        <span class="font-weight-semibold text-danger">{{ number_format($cashRemaining, 0, ',', ' ') }} Ar</span>
                                    @else
                                        <span class="font-weight-semibold text-success">{{ number_format($payment->amount, 0, ',', ' ') }} Ar</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($status == 'ADRA')
                                    @if($cashStatus == 'Acquitté')
                                        <div class="badge badge-flat badge-pill badge-success">
                                            <i class="icon-checkmark2 mr-1"></i> {{ $cashStatus }}
                                        </div>
                                    @else
                                        <div class="badge badge-flat badge-pill badge-warning">
                                            <i class="icon-spinner11 mr-1"></i> {{ $cashStatus }}
                                        </div>
                                        @if($cashRemaining > 0)
                                            <div class="progress mt-1" style="height: 4px;">
                                                <div class="progress-bar bg-success" style="width: {{ ($cashPaid / $cashAmount) * 100 }}%"></div>
                                            </div>
                                            <div class="text-muted font-size-xs mt-1">
                                                {{ number_format(($cashPaid / $cashAmount) * 100, 0) }}% payé
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <th colspan="3" class="text-right font-weight-bold">TOTAL:</th>
                        <th class="text-right font-weight-bold">{{ number_format($totalAmount, 0, ',', ' ') }} Ar</th>
                        <th class="text-right font-weight-bold">{{ number_format($totalBilledAmount, 0, ',', ' ') }} Ar</th>
                        @if($has_adra)
                        <th class="text-right font-weight-bold">{{ number_format($totalCashAmount, 0, ',', ' ') }} Ar</th>
                        <th class="text-right font-weight-bold">{{ number_format($totalCashPaid, 0, ',', ' ') }} Ar</th>
                        <th class="text-right font-weight-bold">{{ number_format($totalCashRemaining, 0, ',', ' ') }} Ar</th>
                        <th class="text-center">
                            @if($totalCashRemaining > 0)
                                <span class="badge badge-flat badge-pill badge-warning">
                                    <i class="icon-spinner11 mr-1"></i> En cours
                                </span>
                            @else
                                <span class="badge badge-flat badge-pill badge-success">
                                    <i class="icon-checkmark2 mr-1"></i> Acquitté
                                </span>
                            @endif
                        </th>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Résumé des paiements -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="icon-cash3 mr-2"></i> Résumé des paiements
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="font-weight-semibold">Total facturé:</div>
                    <div class="font-weight-bold h5 mb-0">{{ number_format($totalBilledAmount, 0, ',', ' ') }} Ar</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="font-weight-semibold">Total déjà payé:</div>
                    <div class="font-weight-bold h5 mb-0 text-success">{{ number_format($totalPaidAmount, 0, ',', ' ') }} Ar</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-weight-semibold">Total restant à payer:</div>
                    <div class="font-weight-bold h5 mb-0 text-danger">{{ number_format($totalRemainingAmount, 0, ',', ' ') }} Ar</div>
                </div>

                <div class="progress mt-3" style="height: 8px;">
                    @php
                        $paymentPercentage = $totalBilledAmount > 0 ? ($totalPaidAmount / $totalBilledAmount) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $paymentPercentage }}%"></div>
                </div>
                <div class="text-center mt-2">
                    <span class="badge badge-pill badge-success">{{ number_format($paymentPercentage, 1) }}% payé</span>
                </div>
            </div>
        </div>
    </div>

    @if($has_adra)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="icon-coins mr-2"></i> Résumé des paiements cash ADRA (25%)
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="font-weight-semibold">Montant cash total (25%):</div>
                    <div class="font-weight-bold h5 mb-0">{{ number_format($totalCashAmount, 0, ',', ' ') }} Ar</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="font-weight-semibold">Montant cash déjà payé:</div>
                    <div class="font-weight-bold h5 mb-0 text-success">{{ number_format($totalCashPaid, 0, ',', ' ') }} Ar</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-weight-semibold">Montant cash restant:</div>
                    <div class="font-weight-bold h5 mb-0 text-danger">{{ number_format($totalCashRemaining, 0, ',', ' ') }} Ar</div>
                </div>

                <div class="progress mt-3" style="height: 8px;">
                    @php
                        $cashPercentage = $totalCashAmount > 0 ? ($totalCashPaid / $totalCashAmount) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $cashPercentage }}%"></div>
                </div>
                <div class="text-center mt-2">
                    <span class="badge badge-pill badge-success">{{ number_format($cashPercentage, 1) }}% payé</span>
                    @if(count($adraUnpaidStudents) > 0)
                        <span class="badge badge-pill badge-danger ml-2">{{ count($adraUnpaidStudents) }} élèves avec impayés</span>
                    @else
                        <span class="badge badge-pill badge-success ml-2">Tous les élèves ont payé</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

        @if(count($adraUnpaidStudents) > 0)
        <div class="card mt-4 fade-in">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="icon-alert mr-2"></i> Élèves ADRA n'ayant pas payé leur 25% cash ({{ count($adraUnpaidStudents) }} élèves)
                </h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item text-white" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="alert alert-warning border-0 rounded-0 m-0 px-3 py-2">
                    <div class="d-flex align-items-center">
                        <i class="icon-info22 mr-3 icon-2x"></i>
                        <span>Ces élèves doivent compléter leur paiement cash de 25% pour finaliser leur inscription. Le montant total restant à collecter est de <strong>{{ number_format(array_sum(array_column($adraUnpaidStudents, 'cashRemaining')), 0, ',', ' ') }} Ar</strong>.</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">N°</th>
                                <th>Élève</th>
                                <th class="text-right">Montant requis</th>
                                <th class="text-right">Déjà payé</th>
                                <th class="text-right">Restant</th>
                                <th>Progression</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adraUnpaidStudents as $index => $student)
                            @php
                                $percentPaid = ($student['cashPaid'] / $student['cashAmount']) * 100;
                                $progressClass = 'bg-danger';
                                if ($percentPaid >= 75) {
                                    $progressClass = 'bg-success';
                                } elseif ($percentPaid >= 50) {
                                    $progressClass = 'bg-info';
                                } elseif ($percentPaid >= 25) {
                                    $progressClass = 'bg-warning';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="bg-info rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="icon-user-check text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-semibold">{{ $student['student']->user->name }}</div>
                                            <div class="text-muted font-size-sm">{{ $student['student']->my_class->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right font-weight-semibold">{{ number_format($student['cashAmount'], 0, ',', ' ') }} Ar</td>
                                <td class="text-right font-weight-semibold text-success">{{ number_format($student['cashPaid'], 0, ',', ' ') }} Ar</td>
                                <td class="text-right font-weight-semibold text-danger">{{ number_format($student['cashRemaining'], 0, ',', ' ') }} Ar</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="width: 60%;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $percentPaid }}%"></div>
                                            </div>
                                        </div>
                                        <span class="badge badge-pill badge-{{ $progressClass == 'bg-danger' ? 'danger' : ($progressClass == 'bg-warning' ? 'warning' : ($progressClass == 'bg-info' ? 'info' : 'success')) }}">
                                            {{ number_format($percentPaid, 0) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="2" class="text-right font-weight-bold">TOTAL:</th>
                                <th class="text-right font-weight-bold">{{ number_format(array_sum(array_column($adraUnpaidStudents, 'cashAmount')), 0, ',', ' ') }} Ar</th>
                                <th class="text-right font-weight-bold text-success">{{ number_format(array_sum(array_column($adraUnpaidStudents, 'cashPaid')), 0, ',', ' ') }} Ar</th>
                                <th class="text-right font-weight-bold text-danger">{{ number_format(array_sum(array_column($adraUnpaidStudents, 'cashRemaining')), 0, ',', ' ') }} Ar</th>
                                <th>
                                    @php
                                        $totalRequired = array_sum(array_column($adraUnpaidStudents, 'cashAmount'));
                                        $totalPaid = array_sum(array_column($adraUnpaidStudents, 'cashPaid'));
                                        $percentPaid = ($totalPaid / $totalRequired) * 100;
                                        $overallProgressClass = 'bg-danger';
                                        if ($percentPaid >= 75) {
                                            $overallProgressClass = 'bg-success';
                                        } elseif ($percentPaid >= 50) {
                                            $overallProgressClass = 'bg-info';
                                        } elseif ($percentPaid >= 25) {
                                            $overallProgressClass = 'bg-warning';
                                        }
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="width: 60%;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $overallProgressClass }}" style="width: {{ $percentPaid }}%"></div>
                                            </div>
                                        </div>
                                        <span class="badge badge-pill badge-{{ $overallProgressClass == 'bg-danger' ? 'danger' : ($overallProgressClass == 'bg-warning' ? 'warning' : ($overallProgressClass == 'bg-info' ? 'info' : 'success')) }}">
                                            {{ number_format($percentPaid, 0) }}%
                                        </span>
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card fade-in">
                    <div class="card-header bg-teal text-white">
                        <h6 class="card-title mb-0">
                            <i class="icon-stats-bars mr-2"></i> Résumé par statut
                        </h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item text-white" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $totalAdraBilled = $totalAdraStudents * $payment->amount * 0.75;
                            $totalTeam3Billed = $totalTeam3Students * $payment->amount;

                            // Calculer les montants payés et restants par statut
                            $adraPaid = 0;
                            $team3Paid = 0;

                            foreach($payment_records as $pr) {
                                $status = $pr['student']->user->status ?? 'Normal';
                                $paidAmount = $pr['paid_amount'] ?? 0;

                                if ($status == 'ADRA') {
                                    $adraPaid += $paidAmount;
                                } elseif ($status == 'TEAM3') {
                                    $team3Paid += $paidAmount;
                                }
                            }

                            $adraRemaining = $totalAdraBilled - $adraPaid;
                            if ($adraRemaining < 0) $adraRemaining = 0;

                            $team3Remaining = $totalTeam3Billed - $team3Paid;
                            if ($team3Remaining < 0) $team3Remaining = 0;
                        @endphp

                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Statut</th>
                                        <th class="text-center">Élèves</th>
                                        <th class="text-right">Montant total</th>
                                        <th class="text-right">Payé</th>
                                        <th class="text-right">Restant</th>
                                        <th class="text-center">État</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($totalAdraStudents > 0)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="bg-info rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                        <i class="icon-user-check text-white" style="font-size: 12px;"></i>
                                                    </div>
                                                </div>
                                                <span class="font-weight-semibold">ADRA (75%)</span>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-semibold">{{ $totalAdraStudents }}</td>
                                        <td class="text-right font-weight-semibold">{{ number_format($totalAdraBilled, 0, ',', ' ') }} Ar</td>
                                        <td class="text-right font-weight-semibold text-success">{{ number_format($adraPaid, 0, ',', ' ') }} Ar</td>
                                        <td class="text-right font-weight-semibold text-danger">{{ number_format($adraRemaining, 0, ',', ' ') }} Ar</td>
                                        <td class="text-center">
                                            @if($adraRemaining > 0)
                                                <span class="badge badge-flat badge-pill badge-warning">
                                                    <i class="icon-spinner11 mr-1"></i> En cours
                                                </span>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-success" style="width: {{ ($adraPaid / $totalAdraBilled) * 100 }}%"></div>
                                                </div>
                                                <div class="text-muted font-size-xs mt-1">
                                                    {{ number_format(($adraPaid / $totalAdraBilled) * 100, 0) }}% payé
                                                </div>
                                            @else
                                                <span class="badge badge-flat badge-pill badge-success">
                                                    <i class="icon-checkmark2 mr-1"></i> Acquitté
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($totalTeam3Students > 0)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="bg-warning rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                        <i class="icon-user-plus text-white" style="font-size: 12px;"></i>
                                                    </div>
                                                </div>
                                                <span class="font-weight-semibold">TEAM3 (100%)</span>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-semibold">{{ $totalTeam3Students }}</td>
                                        <td class="text-right font-weight-semibold">{{ number_format($totalTeam3Billed, 0, ',', ' ') }} Ar</td>
                                        <td class="text-right font-weight-semibold text-success">{{ number_format($team3Paid, 0, ',', ' ') }} Ar</td>
                                        <td class="text-right font-weight-semibold text-danger">{{ number_format($team3Remaining, 0, ',', ' ') }} Ar</td>
                                        <td class="text-center">
                                            @if($team3Remaining > 0)
                                                <span class="badge badge-flat badge-pill badge-warning">
                                                    <i class="icon-spinner11 mr-1"></i> En cours
                                                </span>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-success" style="width: {{ ($team3Paid / $totalTeam3Billed) * 100 }}%"></div>
                                                </div>
                                                <div class="text-muted font-size-xs mt-1">
                                                    {{ number_format(($team3Paid / $totalTeam3Billed) * 100, 0) }}% payé
                                                </div>
                                            @else
                                                <span class="badge badge-flat badge-pill badge-success">
                                                    <i class="icon-checkmark2 mr-1"></i> Acquitté
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-center">{{ $totalAdraStudents + $totalTeam3Students }}</th>
                                        <th class="text-right">{{ number_format($totalBilledAmount, 0, ',', ' ') }} Ar</th>
                                        <th class="text-right text-success">{{ number_format($adraPaid + $team3Paid, 0, ',', ' ') }} Ar</th>
                                        <th class="text-right text-danger">{{ number_format($adraRemaining + $team3Remaining, 0, ',', ' ') }} Ar</th>
                                        <th class="text-center">
                                            @if($totalRemainingAmount > 0)
                                                <span class="badge badge-flat badge-pill badge-warning">
                                                    <i class="icon-spinner11 mr-1"></i> En cours
                                                </span>
                                            @else
                                                <span class="badge badge-flat badge-pill badge-success">
                                                    <i class="icon-checkmark2 mr-1"></i> Acquitté
                                                </span>
                                            @endif
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card fade-in">
                    <div class="card-header bg-violet text-white">
                        <h6 class="card-title mb-0">
                            <i class="icon-coin-dollar mr-2"></i> Détails des paiements cash ADRA (25%)
                        </h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item text-white" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-3">
                                        <h6 class="mb-1 text-muted">Élèves ADRA</h6>
                                        <h3 class="mb-0 font-weight-bold">{{ $totalAdraStudents }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-3">
                                        <h6 class="mb-1 text-muted">Élèves avec impayés</h6>
                                        <h3 class="mb-0 font-weight-bold text-danger">{{ count($adraUnpaidStudents) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1 text-muted font-size-sm">Montant total</h6>
                                        <h5 class="mb-0 font-weight-bold">{{ number_format($totalCashAmount, 0, ',', ' ') }} Ar</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1 text-muted font-size-sm">Montant payé</h6>
                                        <h5 class="mb-0 font-weight-bold text-success">{{ number_format($totalCashPaid, 0, ',', ' ') }} Ar</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1 text-muted font-size-sm">Montant restant</h6>
                                        <h5 class="mb-0 font-weight-bold text-danger">{{ number_format($totalCashRemaining, 0, ',', ' ') }} Ar</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="font-weight-semibold">Progression des paiements cash</h6>
                            <div class="progress" style="height: 10px;">
                                @php
                                    $cashPercentage = $totalCashAmount > 0 ? ($totalCashPaid / $totalCashAmount) * 100 : 0;
                                    $progressClass = 'bg-danger';
                                    if ($cashPercentage >= 75) {
                                        $progressClass = 'bg-success';
                                    } elseif ($cashPercentage >= 50) {
                                        $progressClass = 'bg-info';
                                    } elseif ($cashPercentage >= 25) {
                                        $progressClass = 'bg-warning';
                                    }
                                @endphp
                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $cashPercentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="font-weight-semibold">{{ number_format($cashPercentage, 1) }}% payé</span>
                                <span class="font-weight-semibold">
                                    @if($totalCashRemaining > 0)
                                        <span class="badge badge-flat badge-pill badge-warning">
                                            <i class="icon-spinner11 mr-1"></i> En cours
                                        </span>
                                    @else
                                        <span class="badge badge-flat badge-pill badge-success">
                                            <i class="icon-checkmark2 mr-1"></i> Acquitté
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card fade-in mt-4">
            <div class="card-body text-center">
                <h5 class="mb-3">Actions disponibles</h5>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('payments.print_special_receipts', ['receipt_ids' => $receipt_ids]) }}" class="btn btn-primary btn-lg mx-2">
                        <i class="icon-printer mr-2"></i> Imprimer les reçus
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-success btn-lg mx-2">
                        <i class="icon-file-pdf mr-2"></i> Imprimer ce résumé
                    </button>
                    <a href="{{ route('payments.manage') }}" class="btn btn-secondary btn-lg mx-2">
                        <i class="icon-arrow-left8 mr-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .navbar, .sidebar, .header-elements, .card-header .list-icons, .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-header {
            border-bottom: 1px solid #ddd !important;
        }
        body {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .content-wrapper {
            padding: 0 !important;
        }
        .page-header {
            display: none !important;
        }
    }
</style>
@endsection
