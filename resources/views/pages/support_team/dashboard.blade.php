@extends('layouts.master')
@section('page_title', 'Tableau de Bord')
@section('content')

@php
    $userName = Auth::user()->name;
    $currentSession = Qs::getCurrentSession();
@endphp

<!-- Welcome Banner -->
<div class="card mb-4 bg-primary text-white shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="card-body py-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1 font-weight-bold">Bienvenue, {{ $userName }} 👋</h2>
                <p class="mb-0 text-white-50">Session en cours : {{ $currentSession }} | Date : {{ \App\Helpers\DateHelper::formatFrenchDate(now()->format('Y-m-d')) }}</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="{{ route('payments.manage') }}" class="btn btn-light btn-sm text-primary font-weight-bold shadow-sm">
                    <i class="icon-cash3 mr-2"></i> Faire un encaissement
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Row 1: Quick KPI Cards -->
<div class="row mb-2">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white shadow-sm h-100" style="background: linear-gradient(45deg, #0288d1, #26c6da);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="mb-0 font-weight-bold">{{ $total_active_students ?? 0 }}</h4>
                        <span>Total Élèves</span>
                    </div>
                    <i class="icon-users icon-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white shadow-sm h-100" style="background: linear-gradient(45deg, #388e3c, #81c784);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="mb-0 font-weight-bold">{{ $total_teachers ?? 0 }}</h4>
                        <span>Total Enseignants</span>
                    </div>
                    <i class="icon-user-tie icon-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white shadow-sm h-100" style="background: linear-gradient(45deg, #00796b, #4db6ac);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="mb-0 font-weight-bold">{{ number_format($total_receipts_month ?? 0, 0, ',', ' ') }} Ar</h4>
                        <span>Recettes du Mois</span>
                    </div>
                    <i class="icon-cash icon-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white shadow-sm h-100" style="background: linear-gradient(45deg, #f57c00, #ffb74d);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h4 class="mb-0 font-weight-bold">{{ $recovery_rate ?? 0 }}%</h4>
                        <span>Taux de Recouvrement</span>
                    </div>
                    <i class="icon-chart-growth icon-2x opacity-50"></i>
                </div>
                <div class="progress" style="height: 6px; background-color: rgba(255,255,255,0.3);">
                    <div class="progress-bar bg-white" style="width: {{ $recovery_rate ?? 0 }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Financial Charts -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-bold"><i class="icon-stats-bars2 mr-2"></i> Recettes vs Dépenses (12 derniers mois)</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-bold"><i class="icon-pie-chart mr-2"></i> Répartition Paiements</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="paymentStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Top 10 Impayés & Effectifs par Classe -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white header-elements-inline border-bottom-danger">
                <h6 class="card-title font-weight-bold text-danger"><i class="icon-warning2 mr-2"></i> Top 10 Impayés</h6>
                <div class="header-elements">
                    <a href="{{ route('payments.manage') }}" class="btn btn-sm btn-outline-danger">Gérer</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm text-nowrap">
                    <thead>
                        <tr>
                            <th>Élève</th>
                            <th>Classe</th>
                            <th class="text-right">Montant Dû</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($top_unpaid ?? [] as $unpaid)
                            <tr>
                                <td>{{ $unpaid->student->user->name ?? 'N/A' }}</td>
                                <td>{{ $unpaid->student->my_class->name ?? 'N/A' }}</td>
                                <td class="text-right font-weight-bold text-danger">{{ number_format($unpaid->total_balance, 0, ',', ' ') }} Ar</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucun impayé significatif</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-bold"><i class="icon-users4 mr-2"></i> Effectifs par Classe</h6>
            </div>
            <div class="card-body">
                <canvas id="classCountChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Alerts & Quick Actions -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="card-title font-weight-bold"><i class="icon-bell3 mr-2"></i> Alertes Système</h6>
            </div>
            <div class="card-body p-0">
                <ul class="media-list media-list-linked">
                    @if(isset($unpaid_count_critical) && $unpaid_count_critical > 10)
                    <li>
                        <a href="{{ route('payments.manage') }}" class="media p-3 bg-light border-left-danger border-left-3">
                            <div class="mr-3">
                                <i class="icon-cash3 text-danger"></i>
                            </div>
                            <div class="media-body">
                                <span class="font-weight-semibold text-danger">Impayés Critiques</span>
                                <span class="d-block text-muted">{{ $unpaid_count_critical }} élèves avec des paiements en retard.</span>
                            </div>
                        </a>
                    </li>
                    @endif
                    
                    @if(isset($pending_backups) && $pending_backups)
                    <li>
                        <a href="{{ route('super_admin.backups') }}" class="media p-3 bg-light border-left-warning border-left-3">
                            <div class="mr-3">
                                <i class="icon-database-time2 text-warning"></i>
                            </div>
                            <div class="media-body">
                                <span class="font-weight-semibold text-warning">Sauvegarde Nécessaire</span>
                                <span class="d-block text-muted">La dernière sauvegarde date de plus de 7 jours.</span>
                            </div>
                        </a>
                    </li>
                    @endif

                    @if(isset($term_ending) && $term_ending)
                    <li>
                        <a href="{{ route('marks.index') }}" class="media p-3 bg-light border-left-info border-left-3">
                            <div class="mr-3">
                                <i class="icon-calendar-warning text-info"></i>
                            </div>
                            <div class="media-body">
                                <span class="font-weight-semibold text-info">Fin de Trimestre</span>
                                <span class="d-block text-muted">Pensez à clôturer la saisie des notes.</span>
                            </div>
                        </a>
                    </li>
                    @endif
                    
                    @if(!isset($unpaid_count_critical) || ($unpaid_count_critical <= 10 && !$pending_backups && !$term_ending))
                    <li class="media p-3">
                        <div class="media-body text-center text-muted">
                            <i class="icon-checkmark3 text-success mr-2"></i> Aucune alerte active.
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="card-footer text-center bg-white border-0 pt-0">
                <form action="{{ route('notifications.check-alerts') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm"><i class="icon-sync mr-2"></i> Vérifier les alertes</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="card-title font-weight-bold"><i class="icon-flash mr-2"></i> Actions Rapides</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 col-md-4 mb-3">
                        <a href="{{ route('students.create') }}" class="btn btn-light btn-block p-3 d-flex flex-column align-items-center">
                            <i class="icon-user-plus icon-2x text-primary mb-2"></i>
                            <span>Inscrire un élève</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-4 mb-3">
                        <a href="{{ route('marks.index') }}" class="btn btn-light btn-block p-3 d-flex flex-column align-items-center">
                            <i class="icon-clipboard3 icon-2x text-success mb-2"></i>
                            <span>Saisir des notes</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-4 mb-3">
                        <a href="{{ route('payments.manage') }}" class="btn btn-light btn-block p-3 d-flex flex-column align-items-center">
                            <i class="icon-cash3 icon-2x text-warning mb-2"></i>
                            <span>Encaisser un paiement</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-4 mb-3">
                        <a href="{{ route('marks.bulk') }}" class="btn btn-light btn-block p-3 d-flex flex-column align-items-center">
                            <i class="icon-printer icon-2x text-info mb-2"></i>
                            <span>Imprimer un bulletin</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-4 mb-3">
                        <a href="{{ route('students.list_all') }}" class="btn btn-light btn-block p-3 d-flex flex-column align-items-center">
                            <i class="icon-list-numbered icon-2x text-secondary mb-2"></i>
                            <span>Faire l'appel</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-4 mb-3">
                        <a href="{{ route('students.list_all') }}" class="btn btn-light btn-block p-3 d-flex flex-column align-items-center">
                            <i class="icon-certificate icon-2x text-danger mb-2"></i>
                            <span>Générer un certificat</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Recent Activity -->
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-bold"><i class="icon-history mr-2"></i> Derniers Paiements Encaissés</h6>
                <div class="header-elements">
                    <a href="{{ route('payments.encaissements.index') }}" class="text-default"><i class="icon-menu7"></i></a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm text-nowrap">
                    <thead>
                        <tr>
                            <th>Élève</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_receipts ?? [] as $receipt)
                            <tr>
                                <td>{{ $receipt->pr->student->user->name ?? 'N/A' }}</td>
                                <td class="font-weight-semibold text-success">+{{ number_format($receipt->amt_paid, 0, ',', ' ') }} Ar</td>
                                <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucun paiement récent</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-bold"><i class="icon-envelop2 mr-2"></i> Dernières Notifications</h6>
                <div class="header-elements">
                    <a href="{{ route('notifications.index') }}" class="text-default"><i class="icon-menu7"></i></a>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="media-list media-list-linked">
                    @forelse($recent_notifications ?? [] as $notif)
                        <li>
                            <a href="{{ $notif->link ?? '#' }}" class="media p-3 {{ !$notif->is_read ? 'bg-light' : '' }}">
                                <div class="mr-3">
                                    <div class="bg-{{ $notif->color ?? 'info' }}-400 rounded-circle p-2">
                                        <i class="{{ $notif->icon ?? 'icon-info22' }} text-white"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <span class="font-weight-semibold {{ !$notif->is_read ? 'text-dark' : 'text-muted' }}">{{ $notif->title }}</span>
                                    <span class="d-block text-muted font-size-sm">{{ \Illuminate\Support\Str::limit($notif->message, 40) }}</span>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <span class="text-muted font-size-xs">{{ $notif->created_at->shortAbsoluteDiffForHumans() }}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="media p-3">
                            <div class="media-body text-center text-muted">Aucune notification.</div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Revenue Chart
    @if(isset($monthly_revenue) && count($monthly_revenue) > 0)
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const revData = @json(array_reverse($monthly_revenue));
    
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: revData.map(d => d.month),
            datasets: [
                {
                    label: 'Recettes (Ar)',
                    data: revData.map(d => d.recettes),
                    backgroundColor: '#4caf50',
                    borderRadius: 4
                },
                {
                    label: 'Dépenses (Ar)',
                    data: revData.map(d => d.depenses),
                    backgroundColor: '#f44336',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return (value / 1000).toFixed(1) + 'k';
                            return value;
                        }
                    }
                }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
    @endif

    // Payment Status Chart
    @if(isset($payment_status))
    const payCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: ['Payé', 'Partiel', 'Impayé'],
            datasets: [{
                data: [{{ $payment_status['paye'] }}, {{ $payment_status['partiel'] }}, {{ $payment_status['impaye'] }}],
                backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    @endif

    // Class Count Chart
    @if(isset($class_student_counts) && isset($classes))
    const classCtx = document.getElementById('classCountChart').getContext('2d');
    
    // Prepare data mapping
    const classLabels = [];
    const classData = [];
    
    @foreach($classes as $class)
        @if(isset($class_student_counts[$class->id]))
            classLabels.push('{{ $class->name }}');
            classData.push({{ $class_student_counts[$class->id] }});
        @endif
    @endforeach

    new Chart(classCtx, {
        type: 'bar',
        data: {
            labels: classLabels,
            datasets: [{
                label: 'Effectif',
                data: classData,
                backgroundColor: '#2196f3',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
    @endif
});
</script>
@endsection
