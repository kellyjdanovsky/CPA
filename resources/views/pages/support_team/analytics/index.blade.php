@extends('layouts.master')
@section('page_title', 'Tableau de Bord Analytique')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title"><i class="icon-stats-dots mr-2"></i> Vue d'ensemble des Performances</h5>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
                <a class="list-icons-item" data-action="reload"></a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            {{-- Graphique 1: Répartition par Sexe --}}
            <div class="col-md-4">
                <div class="card card-body border-top-primary">
                    <h6 class="font-weight-semibold">Répartition Garçons / Filles</h6>
                    <div class="chart-container" style="position: relative; height:250px; width:100%">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Graphique 2: Répartition par Statut --}}
            <div class="col-md-4">
                <div class="card card-body border-top-success">
                    <h6 class="font-weight-semibold">Statuts (Normal / ADRA / TEAM3)</h6>
                    <div class="chart-container" style="position: relative; height:250px; width:100%">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Graphique 3: Élèves par Classe --}}
            <div class="col-md-4">
                <div class="card card-body border-top-info">
                    <h6 class="font-weight-semibold">Effectifs par Classe</h6>
                    <div class="chart-container" style="position: relative; height:250px; width:100%">
                        <canvas id="classChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            {{-- Section Paiements --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h6 class="card-title">Dernières Transactions</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th>Paiement</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments_stats['recent_transactions'] as $trans)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <a href="#">
                                                    <img src="{{ $trans->student->photo ?? asset('global_assets/images/user.png') }}" class="rounded-circle" width="32" height="32" alt="">
                                                </a>
                                            </div>
                                            <div>
                                                <a href="{{ route('students.show', Qs::hash($trans->student_id)) }}" class="text-default font-weight-semibold">{{ $trans->student->name }}</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted">{{ $trans->payment->title ?? 'N/A' }}</span></td>
                                    <td><span class="text-success font-weight-bold">{{ number_format($trans->amt_paid, 0, ',', ' ') }} Ar</span></td>
                                    <td>{{ $trans->updated_at->diffForHumans() }}</td>
                                    <td>
                                        @if($trans->paid)
                                            <span class="badge badge-success">Payé</span>
                                        @else
                                            <span class="badge badge-warning">Partiel</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Résumé Chiffré --}}
            <div class="col-md-4">
                <div class="card bg-blue-400">
                    <div class="card-body">
                        <div class="d-flex">
                            <h3 class="font-weight-semibold mb-0">{{ $students_stats['total'] }}</h3>
                            <span class="badge badge-mark border-white-400 ml-auto align-self-center"></span>
                        </div>
                        <div>Total Élèves Actifs</div>
                    </div>
                </div>

                <div class="card bg-success-400">
                    <div class="card-body">
                        <div class="d-flex">
                            <h3 class="font-weight-semibold mb-0">{{ number_format($payments_stats['total_collected'], 0, ',', ' ') }} Ar</h3>
                            <span class="badge badge-mark border-white-400 ml-auto align-self-center"></span>
                        </div>
                        <div>Total Recettes (Année en cours)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts pour Chart.js --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données injectées depuis le contrôleur
        const genderData = @json($students_stats['by_gender']);
        const statusData = @json($status_stats);
        const classData = @json($students_stats['by_class']);

        // 1. Gender Chart (Doughnut)
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Garçons', 'Filles'],
                datasets: [{
                    data: [genderData['Male'] ?? 0, genderData['Female'] ?? 0],
                    backgroundColor: ['#36A2EB', '#FF6384'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 2. Status Chart (Pie)
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Normal', 'ADRA', 'TEAM3'],
                datasets: [{
                    data: [statusData['Normal'], statusData['ADRA'], statusData['TEAM3']],
                    backgroundColor: ['#4BC0C0', '#FF9F40', '#9966FF'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Class Chart (Bar)
        new Chart(document.getElementById('classChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(classData),
                datasets: [{
                    label: 'Nombre d\'élèves',
                    data: Object.values(classData),
                    backgroundColor: '#36A2EB',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>

@endsection
