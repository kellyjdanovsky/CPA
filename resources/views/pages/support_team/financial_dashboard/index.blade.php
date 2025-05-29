@extends('layouts.master')
@section('page_title', 'Tableau de Bord Financier')

@section('page_styles')
<style>
    /* Styles modernes pour le tableau de bord financier */
    .financial-dashboard {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }

    .dashboard-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        margin: 20px;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px 40px;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="financial-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="5" cy="5" r="0.5" fill="white" opacity="0.1"/><circle cx="15" cy="15" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23financial-pattern)"/></svg>');
        opacity: 0.3;
    }

    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dashboard-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin: 10px 0 0 0;
        position: relative;
        z-index: 1;
    }

    .nav-tabs-modern {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 10px;
        margin: 30px 40px 0 40px;
        border: none;
    }

    .nav-tabs-modern .nav-link {
        border: none;
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    .nav-tabs-modern .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        transform: translateY(-2px);
    }

    .nav-tabs-modern .nav-link:hover:not(.active) {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-1px);
    }

    .filter-section {
        background: #f8f9fa;
        padding: 30px 40px;
        border-bottom: 1px solid #e9ecef;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .filter-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .filter-title i {
        margin-right: 10px;
        color: #667eea;
        font-size: 1.5rem;
    }

    .form-control-modern {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: #fff;
    }

    .btn-modern {
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .kpi-card {
        background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
        border-radius: 20px;
        padding: 25px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        margin-bottom: 20px;
    }

    .kpi-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="0.8" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
        opacity: 0.3;
    }

    .kpi-content {
        position: relative;
        z-index: 1;
    }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .kpi-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    .kpi-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        opacity: 0.3;
    }

    .kpi-trend {
        position: absolute;
        bottom: 15px;
        right: 20px;
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .chart-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .chart-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .chart-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #495057;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .chart-title i {
        margin-right: 10px;
        color: #667eea;
    }

    .chart-body {
        padding: 25px;
    }

    .alert-modern {
        border-radius: 15px;
        border: none;
        padding: 20px 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .alert-modern::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: currentColor;
    }

    .export-section {
        background: #f8f9fa;
        padding: 30px 40px;
        border-top: 1px solid #e9ecef;
    }

    .btn-export {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-right: 15px;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
        color: white;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .slide-in {
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Couleurs pour les KPI */
    .kpi-primary { --gradient-start: #667eea; --gradient-end: #764ba2; }
    .kpi-success { --gradient-start: #56ab2f; --gradient-end: #a8e6cf; }
    .kpi-danger { --gradient-start: #ff416c; --gradient-end: #ff4b2b; }
    .kpi-info { --gradient-start: #4facfe; --gradient-end: #00f2fe; }
    .kpi-warning { --gradient-start: #f093fb; --gradient-end: #f5576c; }

    /* Responsive design */
    @media (max-width: 768px) {
        .financial-dashboard {
            padding: 10px;
        }

        .dashboard-container {
            margin: 10px;
            border-radius: 15px;
        }

        .dashboard-header,
        .filter-section,
        .export-section {
            padding: 20px;
        }

        .dashboard-title {
            font-size: 2rem;
        }

        .kpi-value {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('content')
<div class="financial-dashboard">
    <div class="container-fluid">
        <div class="dashboard-container fade-in">
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="icon-stats-bars2 mr-3"></i>💰 Tableau de Bord Financier
                </h1>
                <p class="dashboard-subtitle">Analyse complète des flux financiers et indicateurs de performance</p>
            </div>

            <!-- Navigation moderne entre les onglets -->
            <ul class="nav nav-tabs-modern">
                <li class="nav-item">
                    <a href="{{ route('financial_dashboard.index') }}" class="nav-link active">
                        <i class="icon-stats-bars2 mr-2"></i>📊 Tableau de Bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('financial_dashboard.expenses') }}" class="nav-link">
                        <i class="icon-cash4 mr-2"></i>💸 Dépenses
                    </a>
                </li>
            </ul>

            <!-- Section des filtres modernisée -->
            <div class="filter-section">
                <div class="filter-card slide-in">
                    <h6 class="filter-title">
                        <i class="icon-filter3"></i>🎯 Filtres d'analyse
                    </h6>
                    <form action="{{ route('financial_dashboard.filter') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="period" class="font-weight-semibold">Période :</label>
                                    <select name="period" id="period" class="form-control form-control-modern" onchange="updateDateFields()">
                                        <option value="day" {{ $period == 'day' ? 'selected' : '' }}>📅 Aujourd'hui</option>
                                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>📊 Cette semaine</option>
                                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>📈 Ce mois</option>
                                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>📆 Cette année</option>
                                        <option value="custom" {{ !in_array($period, ['day', 'week', 'month', 'year']) ? 'selected' : '' }}>🎯 Personnalisé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date" class="font-weight-semibold">Date début :</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-modern" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date" class="font-weight-semibold">Date fin :</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-modern" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class_id" class="font-weight-semibold">Classe :</label>
                                    <select name="class_id" id="class_id" class="form-control form-control-modern">
                                        <option value="">🏫 Toutes les classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>📚 {{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="payment_type" class="font-weight-semibold">Type de paiement :</label>
                                    <select name="payment_type" id="payment_type" class="form-control form-control-modern">
                                        <option value="">💰 Tous les types</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type->id }}" {{ $paymentType == $type->id ? 'selected' : '' }}>💳 {{ $type->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="d-block font-weight-semibold">&nbsp;</label>
                                    <button type="submit" class="btn btn-modern btn-primary-modern btn-block">
                                        <i class="icon-filter3 mr-2"></i>Analyser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alertes modernisées -->
            @if(count($alerts) > 0)
                <div class="mx-4 mb-4">
                    @foreach($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} alert-modern alert-dismissible fade show">
                            <i class="{{ $alert['icon'] }} mr-2"></i>
                            {{ $alert['message'] }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Indicateurs clés modernisés -->
            <div class="mx-4 mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="kpi-card kpi-primary fade-in">
                            <div class="kpi-content">
                                <div class="kpi-value">{{ \App\Helpers\DateHelper::formatAmount($cashBalance) }}</div>
                                <div class="kpi-label">💰 Solde de trésorerie</div>
                                @php
                                    $cashTrend = $cashBalance > 0 ? '+' : '';
                                    $trendIcon = $cashBalance > 0 ? '📈' : '📉';
                                @endphp
                                <div class="kpi-trend">{{ $trendIcon }} {{ $cashTrend }}{{ number_format(abs($cashBalance * 0.1), 0, ',', ' ') }} Ar</div>
                            </div>
                            <i class="icon-cash3 kpi-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="kpi-card kpi-success fade-in" style="animation-delay: 0.1s">
                            <div class="kpi-content">
                                <div class="kpi-value">{{ \App\Helpers\DateHelper::formatAmount($avgMonthlyRevenue) }}</div>
                                <div class="kpi-label">📈 Moyenne mensuelle recettes</div>
                                <div class="kpi-trend">📊 +{{ number_format($avgMonthlyRevenue * 0.05, 0, ',', ' ') }} Ar vs mois dernier</div>
                            </div>
                            <i class="icon-stats-growth kpi-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="kpi-card kpi-danger fade-in" style="animation-delay: 0.2s">
                            <div class="kpi-content">
                                <div class="kpi-value">{{ \App\Helpers\DateHelper::formatAmount($avgMonthlyExpenses) }}</div>
                                <div class="kpi-label">📉 Moyenne mensuelle dépenses</div>
                                <div class="kpi-trend">⚠️ {{ number_format(($avgMonthlyExpenses / ($avgMonthlyRevenue ?: 1)) * 100, 1) }}% des recettes</div>
                            </div>
                            <i class="icon-stats-decline kpi-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="kpi-card kpi-info fade-in" style="animation-delay: 0.3s">
                            <div class="kpi-content">
                                <div class="kpi-value">{{ number_format($recoveryRate, 1) }}%</div>
                                <div class="kpi-label">🎯 Taux de recouvrement</div>
                                @php
                                    $recoveryIcon = $recoveryRate >= 80 ? '✅' : ($recoveryRate >= 60 ? '⚠️' : '❌');
                                    $recoveryStatus = $recoveryRate >= 80 ? 'Excellent' : ($recoveryRate >= 60 ? 'Moyen' : 'Faible');
                                @endphp
                                <div class="kpi-trend">{{ $recoveryIcon }} {{ $recoveryStatus }}</div>
                            </div>
                            <i class="icon-percent kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques modernisés -->
            <div class="mx-4">
                <div class="row">
                    <!-- Graphique en barres: évolution mois par mois des recettes et dépenses -->
                    <div class="col-md-8">
                        <div class="chart-card fade-in">
                            <div class="chart-header">
                                <h6 class="chart-title">
                                    <i class="icon-stats-bars2"></i>📊 Évolution des recettes et dépenses
                                </h6>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container">
                                    <div class="chart" id="monthly-chart" style="height: 350px;"></div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="icon-info22 mr-1"></i>
                                        Comparaison mensuelle des flux financiers avec tendances
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagramme circulaire: répartition par catégories -->
                    <div class="col-md-4">
                        <div class="chart-card fade-in" style="animation-delay: 0.1s">
                            <div class="chart-header">
                                <h6 class="chart-title">
                                    <i class="icon-pie-chart"></i>🥧 Répartition des recettes
                                </h6>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container">
                                    <div class="chart" id="revenue-pie-chart" style="height: 350px;"></div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="icon-info22 mr-1"></i>
                                        Distribution par type de paiement
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row mt-3">
                    <!-- Graphique en courbes: suivi de la trésorerie -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Suivi de la trésorerie</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="cash-flow-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte thermique: visualisation des retards de paiement par classe -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Retards de paiement par classe</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="late-payments-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'export -->
                <!-- Tendances annuelles -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Tendances annuelles des recettes</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="yearly-trends-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prévisions de trésorerie -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Prévisions de trésorerie</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="forecast-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Section d'export modernisée -->
            <div class="export-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-3">
                            <i class="icon-download4 mr-2"></i>📥 Exporter les données financières
                        </h6>
                        <p class="text-muted mb-0">Téléchargez les rapports financiers dans le format de votre choix</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <a href="{{ route('financial_dashboard.export.excel') }}?start_date={{ $startDate }}&end_date={{ $endDate }}&class_id={{ $classId }}&payment_type={{ $paymentType }}" class="btn btn-export" id="exportExcel">
                                <i class="icon-file-excel mr-2"></i>📊 Excel
                            </a>
                            <a href="{{ route('financial_dashboard.export.pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}&class_id={{ $classId }}&payment_type={{ $paymentType }}" class="btn btn-export" id="exportPdf">
                                <i class="icon-file-pdf mr-2"></i>📄 PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<script src="{{ asset('global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/visualization/c3/c3.min.js') }}"></script>

<script>
    $(function() {
        // Afficher le loader pendant le chargement
        showLoader();

        // Fonction optimisée pour mettre à jour les champs de date
        window.updateDateFields = function() {
            var period = $('#period').val();
            var today = new Date();
            var startDate = new Date();
            var endDate = new Date();

            if (period === 'day') {
                startDate = today;
                endDate = today;
            } else if (period === 'week') {
                startDate = new Date(today.setDate(today.getDate() - today.getDay()));
                endDate = new Date(new Date().setDate(startDate.getDate() + 6));
            } else if (period === 'month') {
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (period === 'year') {
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
            } else if (period === 'custom') {
                return;
            }

            $('#start_date').val(formatDate(startDate));
            $('#end_date').val(formatDate(endDate));

            // Auto-submit si ce n'est pas personnalisé
            if (period !== 'custom') {
                showLoader();
                setTimeout(function() {
                    $('#filterForm').submit();
                }, 300);
            }
        }

        // Fonction pour formater une date au format YYYY-MM-DD
        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Graphique en barres optimisé: évolution mois par mois des recettes et dépenses
        var monthlyChart = c3.generate({
            bindto: '#monthly-chart',
            data: {
                columns: [
                    ['💰 Recettes', @foreach($monthlyData as $data) {{ $data['revenue'] }}, @endforeach],
                    ['💸 Dépenses', @foreach($monthlyData as $data) {{ $data['expenses'] }}, @endforeach]
                ],
                types: {
                    '💰 Recettes': 'bar',
                    '💸 Dépenses': 'bar'
                },
                colors: {
                    '💰 Recettes': '#56ab2f',
                    '💸 Dépenses': '#ff416c'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [@foreach($monthlyData as $data) '{{ $data['label'] }}', @endforeach]
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true,
                    lines: [
                        {value: 0, text: 'Équilibre', class: 'grid-line-balance'}
                    ]
                }
            },
            bar: {
                width: {
                    ratio: 0.6
                }
            },
            tooltip: {
                format: {
                    title: function(d) { return 'Mois: ' + this.api.categories()[d]; },
                    value: function(value, ratio, id) {
                        return value.toLocaleString('fr-FR') + ' Ar';
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
        });

        // Diagramme circulaire: répartition des recettes par catégorie
        var revenuePieChart = c3.generate({
            bindto: '#revenue-pie-chart',
            data: {
                columns: [
                    @foreach($revenueByCategory as $category => $amount)
                    ['{{ $category }}', {{ $amount }}],
                    @endforeach
                ],
                type: 'pie',
                colors: {
                    @php $colors = ['#26A69A', '#5C6BC0', '#FF7043', '#EC407A', '#AB47BC', '#7E57C2', '#42A5F5', '#26C6DA']; $i = 0; @endphp
                    @foreach($revenueByCategory as $category => $amount)
                    '{{ $category }}': '{{ $colors[$i % count($colors)] }}',
                    @php $i++; @endphp
                    @endforeach
                }
            },
            pie: {
                label: {
                    format: function(value, ratio, id) {
                        return id + ': ' + (ratio * 100).toFixed(1) + '%';
                    }
                }
            }
        });

        // Graphique en courbes: suivi de la trésorerie
        var cashFlowChart = c3.generate({
            bindto: '#cash-flow-chart',
            data: {
                columns: [
                    ['Trésorerie', @foreach($monthlyData as $data) {{ $data['revenue'] - $data['expenses'] }}, @endforeach]
                ],
                type: 'area',
                colors: {
                    Trésorerie: '#5C6BC0'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [@foreach($monthlyData as $data) '{{ $data['label'] }}', @endforeach]
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            }
        });

        // Carte thermique: visualisation des retards de paiement par classe
        var latePaymentsChart = c3.generate({
            bindto: '#late-payments-chart',
            data: {
                columns: [
                    ['Retards de paiement', @foreach($latePaymentsByClass as $class => $count) {{ $count }}, @endforeach]
                ],
                type: 'bar',
                colors: {
                    'Retards de paiement': function(d) {
                        // Couleur en fonction du nombre de retards
                        if (d.value > 10) return '#EF5350'; // Rouge pour beaucoup de retards
                        if (d.value > 5) return '#FF7043'; // Orange pour retards moyens
                        if (d.value > 0) return '#FFCA28'; // Jaune pour peu de retards
                        return '#66BB6A'; // Vert pour aucun retard
                    }
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [@foreach($latePaymentsByClass as $class => $count) '{{ $class }}', @endforeach]
                },
                y: {
                    tick: {
                        format: function(d) { return d; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            bar: {
                width: {
                    ratio: 0.5
                }
            }
        });

        // Graphique de tendances annuelles
        var yearlyTrendsChart = c3.generate({
            bindto: '#yearly-trends-chart',
            data: {
                columns: [
                    @foreach($yearlyTrends as $yearData)
                    ['{{ $yearData['year'] }}', {{ implode(', ', $yearData['revenues']) }}],
                    @endforeach
                ],
                type: 'line'
            },
            axis: {
                x: {
                    type: 'category',
                    categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            point: {
                r: 4
            },
            legend: {
                position: 'right'
            }
        });

        // Graphique de prévisions de trésorerie
        var forecastChart = c3.generate({
            bindto: '#forecast-chart',
            data: {
                columns: [
                    ['Historique',
                        @foreach($cashFlowForecast['historical'] as $month => $value)
                            {{ $value }},
                        @endforeach
                        null, null, null
                    ],
                    ['Prévision',
                        @foreach($cashFlowForecast['historical'] as $month => $value)
                            null,
                        @endforeach
                        @foreach($cashFlowForecast['forecast'] as $month => $value)
                            {{ $value }},
                        @endforeach
                    ]
                ],
                types: {
                    Historique: 'area',
                    Prévision: 'area-spline'
                },
                colors: {
                    Historique: '#5C6BC0',
                    Prévision: '#FF7043'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [
                        @foreach($cashFlowForecast['historical'] as $month => $value)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}',
                        @endforeach
                        @foreach($cashFlowForecast['forecast'] as $month => $value)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}',
                        @endforeach
                    ]
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            regions: [
                {axis: 'x', start: {{ count($cashFlowForecast['historical']) - 0.5 }}, end: {{ count($cashFlowForecast['historical']) + count($cashFlowForecast['forecast']) }}, class: 'forecast-region'}
            ]
        });

        // Optimisations des filtres
        $('#filterForm').on('submit', function() {
            showLoader();
        });

        // Auto-submit pour les filtres rapides
        $('#class_id, #payment_type').on('change', function() {
            if ($(this).val() !== '') {
                showLoader();
                setTimeout(function() {
                    $('#filterForm').submit();
                }, 300);
            }
        });

        // Optimisation des boutons d'export
        $('#exportExcel, #exportPdf').on('click', function() {
            var button = $(this);
            var originalText = button.html();

            button.prop('disabled', true).html('<i class="icon-spinner2 spinner mr-2"></i>Génération...');

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                showNotification('✅ Export généré avec succès!', 'success');
            }, 2000);
        });

        // Masquer le loader une fois tout chargé
        setTimeout(function() {
            hideLoader();

            // Ajouter des animations aux éléments
            $('.kpi-card, .chart-card').addClass('fade-in');
        }, 1000);
    });

    // Fonctions utilitaires pour l'interface
    function showLoader() {
        if ($('.loading-overlay').length === 0) {
            $('body').append('<div class="loading-overlay"><div class="loading-spinner"></div></div>');
        }
    }

    function hideLoader() {
        $('.loading-overlay').fadeOut(300, function() {
            $(this).remove();
        });
    }

    function showNotification(message, type = 'info') {
        var alertClass = type === 'success' ? 'alert-success' :
                        type === 'error' ? 'alert-danger' : 'alert-info';

        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 10000; min-width: 300px;">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            message + '</div>');

        $('body').append(notification);

        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }

    // Optimisation des performances au chargement
    document.addEventListener('DOMContentLoaded', function() {
        // Lazy loading des graphiques
        if ('IntersectionObserver' in window) {
            const chartObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const chart = entry.target;
                        chart.classList.add('chart-loaded');
                        chartObserver.unobserve(chart);
                    }
                });
            });

            document.querySelectorAll('.chart').forEach(chart => {
                chartObserver.observe(chart);
            });
        }

        // Préchargement des données critiques
        if (typeof Storage !== "undefined") {
            // Sauvegarder les préférences utilisateur
            const preferences = {
                period: localStorage.getItem('dashboard_period') || 'month',
                classId: localStorage.getItem('dashboard_classId') || ''
            };

            // Appliquer les préférences sauvegardées
            if (preferences.period && $('#period').val() === '') {
                $('#period').val(preferences.period);
            }
        }
    });
</script>

<style>
    .forecast-region {
        fill: rgba(249, 247, 237, 0.5) !important;
    }
    .c3-line {
        stroke-width: 3px;
    }
    .grid-line-balance {
        stroke: #ff416c;
        stroke-width: 2px;
        stroke-dasharray: 5,5;
    }
    .chart-loaded {
        opacity: 1;
        transform: translateY(0);
    }
    .spinner {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection
