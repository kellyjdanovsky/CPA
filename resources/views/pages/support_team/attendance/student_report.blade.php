@extends('layouts.master')
@section('page_title', 'Rapport de Présence - ' . $student->name)

@section('content')
<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-success-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $stats['present'] }}</h3>
                    <span class="text-uppercase font-size-xs">Jours Présent</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-checkmark3 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-danger-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $stats['absent'] }}</h3>
                    <span class="text-uppercase font-size-xs">Jours Absent</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-cross2 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-warning-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $stats['retard'] }}</h3>
                    <span class="text-uppercase font-size-xs">Retards</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-alarm icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-info-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $stats['taux'] }}%</h3>
                    <span class="text-uppercase font-size-xs">Taux de Présence</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-percent icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header header-elements-inline bg-dark">
        <h6 class="card-title font-weight-bold">Historique des présences : {{ $student->name }}</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable-basic table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Période</th>
                        <th>Statut</th>
                        <th>Observations</th>
                        <th>Marqué par</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $att)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($att->date)) }}</td>
                        <td>{{ ucfirst($att->period) }}</td>
                        <td>
                            @if($att->status == 'present')
                                <span class="badge badge-success">Présent</span>
                            @elseif($att->status == 'absent')
                                <span class="badge badge-danger">Absent</span>
                            @elseif($att->status == 'retard')
                                <span class="badge badge-warning">Retard</span>
                            @elseif($att->status == 'excuse')
                                <span class="badge badge-info">Excusé</span>
                            @endif
                        </td>
                        <td>{{ $att->observations ?? '-' }}</td>
                        <td>{{ $att->markedBy->name ?? 'Système' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
