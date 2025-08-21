@extends('layouts.master')
@section('page_title', 'Tableau de Bord')
@section('content')

    <!-- Modern Dashboard Header -->
    <div class="modern-dashboard-header mb-5">
        <div class="header-background">
            <div class="header-overlay"></div>
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </div>
        <div class="header-content">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="welcome-section">
                            <div class="user-greeting">
                                <h1 class="greeting-text">Bonjour, {{ Auth::user()->name }} 👋</h1>
                                <p class="greeting-subtitle">Voici un aperçu de votre établissement aujourd'hui</p>
                            </div>
                            <div class="school-identity">
                                <div class="school-logo-modern">
                                    <i class="icon-graduation2"></i>
                                </div>
                                <div class="school-info">
                                    <h2 class="school-name">Collège Privé Adventiste Avaratetezana</h2>
                                    <div class="school-meta">
                                        <span class="meta-item">
                                            <i class="icon-calendar"></i>
                                            Année scolaire {{ $current_session ?? '2024-2025' }}
                                        </span>
                                        <span class="meta-item">
                                            <i class="icon-location3"></i>
                                            Avaratetezana, Madagascar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="header-stats">
                            <div class="quick-stat">
                                <div class="stat-icon">
                                    <i class="icon-pulse2"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number">{{ date('d') }}</span>
                                    <span class="stat-label">{{ date('M Y') }}</span>
                                </div>
                            </div>
                            <div class="weather-widget" id="weather-widget">
                                <div class="weather-icon">
                                    <i class="icon-sun" id="weather-icon"></i>
                                </div>
                                <div class="weather-info">
                                    <span class="temperature" id="temperature">24°C</span>
                                    <span class="weather-desc" id="weather-desc">Antananarivo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modern Statistics Cards -->
    <div class="modern-stats-section mb-5">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="modern-stat-card students-card">
                        <div class="card-background">
                            <div class="bg-pattern"></div>
                        </div>
                        <div class="card-content">
                            <div class="stat-header">
                                <div class="stat-icon-container">
                                    <i class="icon-users4"></i>
                                </div>
                                <div class="stat-trend">
                                    <i class="icon-trending-up"></i>
                                    <span>+5.2%</span>
                                </div>
                            </div>
                            <div class="stat-body">
                                <h3 class="stat-number counter-animated">{{ $total_active_students ?? '0' }}</h3>
                                <p class="stat-label">Élèves Actifs</p>
                                <div class="stat-progress">
                                    <div class="progress-bar" style="width: {{ min(($total_active_students ?? 0) / 400 * 100, 100) }}%"></div>
                                </div>
                                <span class="stat-subtitle">{{ round(min(($total_active_students ?? 0) / 400 * 100, 100), 1) }}% de capacité</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="modern-stat-card teachers-card">
                        <div class="card-background">
                            <div class="bg-pattern"></div>
                        </div>
                        <div class="card-content">
                            <div class="stat-header">
                                <div class="stat-icon-container">
                                    <i class="icon-user-tie"></i>
                                </div>
                                <div class="stat-trend positive">
                                    <i class="icon-trending-up"></i>
                                    <span>+2</span>
                                </div>
                            </div>
                            <div class="stat-body">
                                <h3 class="stat-number counter-animated">{{ $total_teachers ?? '0' }}</h3>
                                <p class="stat-label">Enseignants</p>
                                <div class="stat-progress">
                                    <div class="progress-bar" style="width: {{ min(($total_teachers ?? 0) / 30 * 100, 100) }}%"></div>
                                </div>
                                <span class="stat-subtitle">{{ ($total_teachers ?? 0) >= 25 ? 'Équipe complète' : 'En recrutement' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="modern-stat-card classes-card">
                        <div class="card-background">
                            <div class="bg-pattern"></div>
                        </div>
                        <div class="card-content">
                            <div class="stat-header">
                                <div class="stat-icon-container">
                                    <i class="icon-books"></i>
                                </div>
                                <div class="stat-trend stable">
                                    <i class="icon-minus"></i>
                                    <span>0%</span>
                                </div>
                            </div>
                            <div class="stat-body">
                                <h3 class="stat-number counter-animated">{{ $total_classes ?? '0' }}</h3>
                                <p class="stat-label">Classes</p>
                                <div class="stat-progress">
                                    <div class="progress-bar" style="width: {{ ($total_classes ?? 0) > 0 ? 100 : 0 }}%"></div>
                                </div>
                                <span class="stat-subtitle">{{ ($total_classes ?? 0) > 0 ? 'Toutes actives' : 'Aucune classe' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="modern-stat-card success-card">
                        <div class="card-background">
                            <div class="bg-pattern"></div>
                        </div>
                        <div class="card-content">
                            <div class="stat-header">
                                <div class="stat-icon-container">
                                    <i class="icon-medal"></i>
                                </div>
                                <div class="stat-trend positive">
                                    <i class="icon-trending-up"></i>
                                    <span>+3%</span>
                                </div>
                            </div>
                            <div class="stat-body">
                                <h3 class="stat-number counter-animated">{{ $success_rate ?? '0' }}<span class="stat-unit">%</span></h3>
                                <p class="stat-label">Taux de Réussite</p>
                                <div class="stat-progress">
                                    <div class="progress-bar" style="width: {{ $success_rate ?? 0 }}%"></div>
                                </div>
                                <span class="stat-subtitle">
                                    @if(($success_rate ?? 0) >= 90)
                                        Excellent niveau
                                    @elseif(($success_rate ?? 0) >= 75)
                                        Bon niveau
                                    @elseif(($success_rate ?? 0) >= 60)
                                        Niveau correct
                                    @else
                                        À améliorer
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="quick-actions-section mb-5">
        <div class="container-fluid">
            <div class="section-header mb-4">
                <h3 class="section-title">
                    <i class="icon-flash section-icon"></i>
                    Actions Rapides
                </h3>
                <p class="section-subtitle">Accès direct aux fonctionnalités principales</p>
            </div>

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('students.create') }}" class="quick-action-card">
                        <div class="action-icon students-action">
                            <i class="icon-user-plus"></i>
                        </div>
                        <div class="action-content">
                            <h4>Nouvel Élève</h4>
                            <p>Inscrire un nouvel élève</p>
                        </div>
                        <div class="action-arrow">
                            <i class="icon-arrow-right8"></i>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('payments.create') }}" class="quick-action-card">
                        <div class="action-icon payments-action">
                            <i class="icon-cash3"></i>
                        </div>
                        <div class="action-content">
                            <h4>Nouveau Paiement</h4>
                            <p>Créer un type de paiement</p>
                        </div>
                        <div class="action-arrow">
                            <i class="icon-arrow-right8"></i>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('marks.index') }}" class="quick-action-card">
                        <div class="action-icon marks-action">
                            <i class="icon-clipboard3"></i>
                        </div>
                        <div class="action-content">
                            <h4>Gérer Notes</h4>
                            <p>Saisir et modifier les notes</p>
                        </div>
                        <div class="action-arrow">
                            <i class="icon-arrow-right8"></i>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('users.index') }}" class="quick-action-card">
                        <div class="action-icon users-action">
                            <i class="icon-users4"></i>
                        </div>
                        <div class="action-content">
                            <h4>Utilisateurs</h4>
                            <p>Gérer les utilisateurs</p>
                        </div>
                        <div class="action-arrow">
                            <i class="icon-arrow-right8"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques de Promotion Section -->
    @if(isset($promotion_stats) && $promotion_stats['total'] > 0)
    <div class="promotion-stats-section mb-5">
        <div class="container-fluid">
            <div class="section-header mb-4">
                <h3 class="section-title">
                    <i class="icon-trending-up section-icon"></i>
                    Statistiques de Promotion
                </h3>
                <p class="section-subtitle">Répartition des décisions pour l'année {{ $current_session ?? '2024-2025' }}</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="promotion-stat-card passants-card">
                        <div class="stat-icon-container">
                            <i class="icon-checkmark-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-number">{{ $promotion_stats['passants'] }}</h4>
                            <p class="stat-label">Passants</p>
                            <div class="stat-percentage">{{ $promotion_stats['passants_percent'] }}%</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="promotion-stat-card redoublants-card">
                        <div class="stat-icon-container">
                            <i class="icon-reload"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-number">{{ $promotion_stats['redoublants'] }}</h4>
                            <p class="stat-label">Redoublants</p>
                            <div class="stat-percentage">{{ $promotion_stats['redoublants_percent'] }}%</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="promotion-stat-card quittés-card">
                        <div class="stat-icon-container">
                            <i class="icon-exit"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-number">{{ $promotion_stats['quittés'] }}</h4>
                            <p class="stat-label">Quittés</p>
                            <div class="stat-percentage">{{ $promotion_stats['quittés_percent'] }}%</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="promotion-stat-card total-card">
                        <div class="stat-icon-container">
                            <i class="icon-users"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-number">{{ $promotion_stats['total'] }}</h4>
                            <p class="stat-label">Total Évalués</p>
                            <div class="stat-percentage">100%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modern School Information Section -->
    <div class="modern-school-info-section mb-5">
        <div class="container-fluid">
            <div class="section-header mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="section-title-group">
                        <h2 class="section-title">
                            <i class="icon-office section-icon"></i>
                            Informations sur l'établissement
                        </h2>
                        <p class="section-subtitle">Vue d'ensemble complète de notre institution</p>
                    </div>
                    <div class="section-actions">
                        <span class="modern-badge academic-year">
                            <i class="icon-calendar"></i>
                            Année scolaire {{ $current_session ?? '2024-2025' }}
                        </span>
                    </div>
                </div>
            </div>
            <!-- Modern School Identity Banner -->
            <div class="modern-school-banner">
                <div class="banner-background">
                    <div class="banner-pattern"></div>
                </div>
                <div class="banner-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="school-identity-modern">
                                <div class="school-logo-badge">
                                    <i class="icon-graduation2"></i>
                                </div>
                                <div class="school-details">
                                    <h3 class="school-name-modern">Collège Privé Adventiste Avaratetezana</h3>
                                    <div class="school-badges">
                                        <span class="info-badge code-badge">
                                            <i class="icon-barcode"></i>
                                            Code: 102051020
                                        </span>
                                        <span class="info-badge type-badge">
                                            <i class="icon-office"></i>
                                            Collège privé
                                        </span>
                                        <span class="info-badge status-badge active">
                                            <i class="icon-checkmark-circle"></i>
                                            Actif
                                        </span>
                                    </div>
                                    <div class="contact-info-modern">
                                        <div class="contact-item">
                                            <i class="icon-phone"></i>
                                            <span>038 34 921 09</span>
                                        </div>
                                        <div class="contact-item">
                                            <i class="icon-envelop5"></i>
                                            <span>epadventistavaratetezana@gmail.com</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="school-metrics-modern">
                                <div class="metric-card">
                                    <div class="metric-icon">
                                        <i class="icon-calendar3"></i>
                                    </div>
                                    <div class="metric-info">
                                        <span class="metric-value">1985</span>
                                        <span class="metric-label">Année de création</span>
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-icon">
                                        <i class="icon-medal"></i>
                                    </div>
                                    <div class="metric-info">
                                        <span class="metric-value">A+</span>
                                        <span class="metric-label">Accréditation</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - General Information -->
                <div class="col-md-6">
                    <div class="info-card mb-3">
                        <div class="info-card-header">
                            <i class="icon-location3 mr-2 text-primary"></i>
                            <h6 class="font-weight-semibold mb-0">Localisation</h6>
                        </div>
                        <div class="info-card-body">
                            <div class="location-map mb-3">
                                <div class="location-pin">
                                    <i class="icon-map5"></i>
                                    <span>Avaratetezana, Madagascar</span>
                                </div>
                            </div>
                            <ul class="list list-unstyled mb-0">
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">DREN:</span>
                                    <span class="font-weight-semibold ml-auto">Antananarivo</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">CISCO:</span>
                                    <span class="font-weight-semibold ml-auto">Antananarivo Antsimondrano</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">Commune:</span>
                                    <span class="font-weight-semibold ml-auto">Ampitatafika</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">ZAP:</span>
                                    <span class="font-weight-semibold ml-auto">Ampitatafika</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">Fokontany:</span>
                                    <span class="font-weight-semibold ml-auto">Avaratetezana</span>
                                </li>
                                <li class="d-flex">
                                    <span class="text-muted mr-2">Quartier:</span>
                                    <span class="font-weight-semibold ml-auto">Avaratetezana</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="icon-user mr-2 text-primary"></i>
                            <h6 class="font-weight-semibold mb-0">Administration</h6>
                        </div>
                        <div class="info-card-body">
                            <div class="admin-profile mb-3">
                                <div class="admin-avatar">
                                    <i class="icon-user-tie"></i>
                                </div>
                                <div class="admin-info">
                                    <h6 class="mb-0">Andriantsoa Lalaina Casimir Irène</h6>
                                    <span class="text-muted">Directeur de l'établissement</span>
                                </div>
                            </div>
                            <ul class="list list-unstyled mb-0">
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">Propriétaire:</span>
                                    <span class="font-weight-semibold ml-auto">Église Adventiste Avaratetezana</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">Direction affiliée:</span>
                                    <span class="font-weight-semibold ml-auto">DN Adventiste</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">Code d'affiliation:</span>
                                    <span class="font-weight-semibold ml-auto">05</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <span class="text-muted mr-2">NIF:</span>
                                    <span class="font-weight-semibold ml-auto">Non renseigné</span>
                                </li>
                                <li class="d-flex">
                                    <span class="text-muted mr-2">Fondation:</span>
                                    <span class="font-weight-semibold ml-auto">1985</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Structure and Infrastructure -->
                <div class="col-md-6">
                    <div class="info-card mb-3">
                        <div class="info-card-header">
                            <i class="icon-graduation2 mr-2 text-primary"></i>
                            <h6 class="font-weight-semibold mb-0">Structure scolaire</h6>
                        </div>
                        <div class="info-card-body">
                            <div class="school-levels mb-3">
                                <div class="level-item active">
                                    <span class="level-icon"><i class="icon-baby"></i></span>
                                    <span class="level-name">Préscolaire</span>
                                </div>
                                <div class="level-item active">
                                    <span class="level-icon"><i class="icon-reading"></i></span>
                                    <span class="level-name">Primaire</span>
                                </div>
                                <div class="level-item active">
                                    <span class="level-icon"><i class="icon-pen"></i></span>
                                    <span class="level-name">Collège</span>
                                </div>
                                <div class="level-item">
                                    <span class="level-icon"><i class="icon-book"></i></span>
                                    <span class="level-name">Lycée</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Niveau</th>
                                            <th>Sections</th>
                                            <th>Effectif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Préscolaire</td>
                                            <td class="stat-editable" id="prescolaire-sections" data-type="text" data-label="Sections préscolaires">PS, MS, GS</td>
                                            <td><span class="badge badge-info stat-editable" id="prescolaire-count" data-type="number" data-label="Effectif préscolaire" data-format="number">75</span></td>
                                        </tr>
                                        <tr>
                                            <td>Primaire</td>
                                            <td class="stat-editable" id="primaire-sections" data-type="text" data-label="Sections primaires">T1, T2, T3, T4, T5</td>
                                            <td><span class="badge badge-info stat-editable" id="primaire-count" data-type="number" data-label="Effectif primaire" data-format="number">150</span></td>
                                        </tr>
                                        <tr>
                                            <td>Collège</td>
                                            <td class="stat-editable" id="college-sections" data-type="text" data-label="Sections collège">T6, T7, T8, T9</td>
                                            <td><span class="badge badge-info stat-editable" id="college-count" data-type="number" data-label="Effectif collège" data-format="number">125</span></td>
                                        </tr>
                                        <tr>
                                            <td>Classes ESH</td>
                                            <td class="status-editable" id="esh-status" data-label="Statut classes ESH" data-options='["Oui", "Non", "En projet"]'>Oui</td>
                                            <td><span class="badge badge-info stat-editable" id="esh-count" data-type="number" data-label="Effectif ESH" data-format="number">15</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card" id="infrastructure-section">
                        <div class="info-card-header">
                            <i class="icon-home mr-2 text-primary"></i>
                            <h6 class="font-weight-semibold mb-0">Infrastructures</h6>
                        </div>
                        <div class="info-card-body">
                            <div class="infrastructure-overview mb-3">
                                <div class="progress-container">
                                    <div class="progress-label">État général des infrastructures</div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 85%"></div>
                                    </div>
                                    <div class="progress-value stat-editable" id="infrastructure-state" data-type="number" data-label="État général des infrastructures" data-format="percentage">85%</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list list-unstyled mb-0">
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-classrooms-label" data-type="text" data-label="Libellé infrastructure">Salles de classe:</span>
                                            <span class="badge badge-success ml-auto stat-editable" id="infra-classrooms-count" data-type="number" data-label="Nombre de salles de classe" data-format="number">11</span>
                                        </li>
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-principal-label" data-type="text" data-label="Libellé infrastructure">Bureau du proviseur:</span>
                                            <span class="badge badge-success ml-auto stat-editable" id="infra-principal-count" data-type="number" data-label="Nombre de bureaux du proviseur" data-format="number">1</span>
                                        </li>
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-kitchen-label" data-type="text" data-label="Libellé infrastructure">Cuisine:</span>
                                            <span class="badge badge-success ml-auto stat-editable" id="infra-kitchen-count" data-type="number" data-label="Nombre de cuisines" data-format="number">1</span>
                                        </li>
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-girls-wc-label" data-type="text" data-label="Libellé infrastructure">WC/Latrines filles:</span>
                                            <span class="badge badge-success ml-auto stat-editable" id="infra-girls-wc-count" data-type="number" data-label="Nombre de WC/Latrines filles" data-format="number">5</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list list-unstyled mb-0">
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-boys-wc-label" data-type="text" data-label="Libellé infrastructure">WC/Latrines garçons:</span>
                                            <span class="badge badge-success ml-auto stat-editable" id="infra-boys-wc-count" data-type="number" data-label="Nombre de WC/Latrines garçons" data-format="number">5</span>
                                        </li>
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-water-label" data-type="text" data-label="Libellé infrastructure">Point d'eau:</span>
                                            <span class="badge badge-success ml-auto stat-editable" id="infra-water-count" data-type="number" data-label="Nombre de points d'eau" data-format="number">2</span>
                                        </li>
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-sport-label" data-type="text" data-label="Libellé infrastructure">Terrain de sport:</span>
                                            <span class="badge badge-warning ml-auto stat-editable" id="infra-sport-count" data-type="number" data-label="Nombre de terrains de sport" data-format="number">1</span>
                                        </li>
                                        <li class="d-flex mb-2">
                                            <span class="text-muted mr-2 stat-editable" id="infra-library-label" data-type="text" data-label="Libellé infrastructure">Bibliothèque:</span>
                                            <span class="badge badge-danger ml-auto stat-editable" id="infra-library-count" data-type="number" data-label="Nombre de bibliothèques" data-format="number">0</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="font-weight-semibold mb-2">Langues enseignées</h6>
                                <div class="language-tags">
                                    <span class="language-tag stat-editable" id="language-french" data-type="text" data-label="Langue enseignée">Français (FRS)</span>
                                    <span class="language-tag stat-editable" id="language-english" data-type="text" data-label="Langue enseignée">Anglais (ANG)</span>
                                    <span class="language-tag stat-editable" id="language-malagasy" data-type="text" data-label="Langue enseignée">Malagasy (MAL)</span>
                                    <span class="language-tag stat-editable" id="language-svt" data-type="text" data-label="Langue enseignée">SVT</span>
                                    <span class="language-tag stat-editable" id="language-eps" data-type="text" data-label="Langue enseignée">EPS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vision et Objectifs -->
    <div class="card fade-in mb-4 school-info-card" id="vision-objectives-section">
        <div class="card-header bg-white d-flex align-items-center">
            <div class="d-flex align-items-center">
                <i class="icon-eye icon-2x text-primary mr-2"></i>
                <h5 class="card-title mb-0">Vision et Objectifs</h5>
            </div>
            <div class="header-elements ml-auto">
                <span class="badge badge-pill badge-primary stat-editable" id="priorities-year" data-type="text" data-label="Année des priorités">Priorités {{ $current_session ?? '2024-2025' }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="vision-mission-card mb-3">
                        <div class="vision-mission-header">
                            <i class="icon-lamp3 text-primary mr-2"></i>
                            <h6 class="font-weight-bold mb-0">Notre Vision</h6>
                        </div>
                        <div class="vision-mission-body">
                            <p class="mb-0 stat-editable" id="school-vision" data-type="text" data-label="Vision de l'école">Devenir un établissement d'excellence reconnu pour la qualité de son enseignement, l'épanouissement de ses élèves et son engagement envers les valeurs chrétiennes adventistes.</p>
                        </div>
                    </div>
                    
                    <div class="vision-mission-card">
                        <div class="vision-mission-header">
                            <i class="icon-heart text-primary mr-2"></i>
                            <h6 class="font-weight-bold mb-0">Notre Mission</h6>
                        </div>
                        <div class="vision-mission-body">
                            <p class="mb-0 stat-editable" id="school-mission" data-type="text" data-label="Mission de l'école">Offrir une éducation holistique qui développe les compétences académiques, sociales et spirituelles des élèves, en les préparant à devenir des citoyens responsables et engagés dans leur communauté.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="objectives-card">
                        <div class="objectives-header">
                            <i class="icon-target text-primary mr-2"></i>
                            <h6 class="font-weight-bold mb-0">Objectifs Stratégiques</h6>
                        </div>
                        <div class="objectives-body">
                            <ul class="objectives-list">
                                <li class="objective-item">
                                    <div class="objective-icon">
                                        <i class="icon-checkmark-circle"></i>
                                    </div>
                                    <div class="objective-content">
                                        <h6 class="objective-title stat-editable" id="objective-1-title" data-type="text" data-label="Titre de l'objectif 1">Excellence Académique</h6>
                                        <p class="objective-description stat-editable" id="objective-1-description" data-type="text" data-label="Description de l'objectif 1">Atteindre un taux de réussite de 98% aux examens nationaux d'ici 2025.</p>
                                    </div>
                                </li>
                                <li class="objective-item">
                                    <div class="objective-icon">
                                        <i class="icon-checkmark-circle"></i>
                                    </div>
                                    <div class="objective-content">
                                        <h6 class="objective-title stat-editable" id="objective-2-title" data-type="text" data-label="Titre de l'objectif 2">Développement des Infrastructures</h6>
                                        <p class="objective-description stat-editable" id="objective-2-description" data-type="text" data-label="Description de l'objectif 2">Construire une bibliothèque moderne et un laboratoire scientifique d'ici 2024.</p>
                                    </div>
                                </li>
                                <li class="objective-item">
                                    <div class="objective-icon">
                                        <i class="icon-checkmark-circle"></i>
                                    </div>
                                    <div class="objective-content">
                                        <h6 class="objective-title stat-editable" id="objective-3-title" data-type="text" data-label="Titre de l'objectif 3">Inclusion et Diversité</h6>
                                        <p class="objective-description stat-editable" id="objective-3-description" data-type="text" data-label="Description de l'objectif 3">Renforcer les programmes d'inclusion pour les élèves à besoins spécifiques.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Qs::userIsTeamSA())
    <div class="row fade-in">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-card bg-primary has-bg-image">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 icon-box">
                            <i class="icon-users4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white">{{ $total_active_students }}</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Total élèves</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-card bg-danger has-bg-image">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 icon-box">
                            <i class="icon-users2"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white">{{ $users->where('user_type', 'teacher')->count() }}</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Total Enseignants</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-card bg-info has-bg-image">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 icon-box">
                            <i class="icon-calendar"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white">{{ $current_session ?? '2024-25' }}</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Année scolaire</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        @php
                            // Calculer le pourcentage d'avancement de l'année scolaire
                            $currentDate = now();
                            $startDate = now()->month >= 9 ? now()->setMonth(9)->setDay(1) : now()->subYear()->setMonth(9)->setDay(1);
                            $endDate = now()->month >= 9 ? now()->addYear()->setMonth(6)->setDay(30) : now()->setMonth(6)->setDay(30);

                            $totalDays = $startDate->diffInDays($endDate);
                            $elapsedDays = $startDate->diffInDays($currentDate);
                            $progressPercent = $totalDays > 0 ? min(round(($elapsedDays / $totalDays) * 100), 100) : 0;
                        @endphp
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <div class="mt-1 text-right">
                            <small class="text-white-50">{{ $progressPercent }}% écoulé</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

       {{-- Display Student Count Per Class --}}
       <div class="card fade-in mt-3">
           <div class="card-header bg-white">
               <h5 class="card-title">Nombre d'élèves par classe</h5>
               <div class="header-elements">
                   <div class="list-icons">
                       <a class="list-icons-item" data-action="collapse"></a>
                   </div>
               </div>
           </div>
           <div class="card-body">
               <div class="row">
                   @foreach($classes as $class)
                   <div class="col-sm-6 col-md-4 col-xl-3 mb-3">
                       <div class="card dashboard-card bg-teal has-bg-image">
                           <div class="card-body">
                               <div class="d-flex align-items-center">
                                   <div class="mr-3 icon-box">
                                       <i class="icon-users2"></i>
                                   </div>
                                   <div>
                                       <h3 class="mb-0 text-white">{{ $class_student_counts[$class->id] }}</h3>
                                       <span class="text-uppercase font-size-xs font-weight-bold text-white-50">{{ $class->name }}</span>
                                   </div>
                               </div>
                               <div class="mt-3">
                                   <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                                       <div class="progress-bar bg-white" style="width: {{ min(100, ($class_student_counts[$class->id] / max(1, $total_active_students)) * 100) }}%"></div>
                                   </div>
                                   <div class="mt-1 text-right">
                                       <small class="text-white-50">{{ number_format(($class_student_counts[$class->id] / max(1, $total_active_students)) * 100, 1) }}% du total</small>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
                   @endforeach
               </div>
           </div>
       </div>
       @endif

    <!-- Dashboard Footer -->
    <div class="card fade-in mt-4 school-info-card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="footer-section">
                        <h6 class="font-weight-bold mb-3"><i class="icon-info22 mr-2 text-primary"></i>À propos</h6>
                        <p class="text-muted mb-3 stat-editable" id="about-description" data-type="text" data-label="Description à propos">Ce tableau de bord présente les informations essentielles de l'établissement. Pour plus de détails, veuillez consulter les sections correspondantes.</p>
                        <div class="d-flex flex-wrap">
                            <span class="badge badge-pill badge-primary mr-2 mb-2 stat-editable" id="tag-1" data-type="text" data-label="Tag 1">Éducation</span>
                            <span class="badge badge-pill badge-info mr-2 mb-2 stat-editable" id="tag-2" data-type="text" data-label="Tag 2">Gestion</span>
                            <span class="badge badge-pill badge-success mr-2 mb-2 stat-editable" id="tag-3" data-type="text" data-label="Tag 3">Excellence</span>
                            <span class="badge badge-pill badge-warning mr-2 mb-2 stat-editable" id="tag-4" data-type="text" data-label="Tag 4">Innovation</span>
                            <span class="badge badge-pill badge-secondary mb-2 stat-editable" id="tag-5" data-type="text" data-label="Tag 5">Communauté</span>
                        </div>
                        <div class="mt-3 text-right">
                            <span class="text-muted">Dernière mise à jour: {{ date('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

