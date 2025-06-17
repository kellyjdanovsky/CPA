@extends('layouts.master')
@section('page_title', 'Mon tableau de bord')
@section('content')

    <!-- Dashboard Header -->
    <div id="dashboard-header-section" class="text-center mb-4 fade-in dashboard-header">
        <div class="school-logo-container mb-3">
            <i class="icon-graduation2 icon-4x text-primary"></i>
            <div class="pulse-ring"></div>
        </div>
        <h2 class="font-weight-bold text-gradient">Tableau de Bord - Gestion Scolaire</h2>
        <p class="text-muted lead">Bienvenue dans le système de gestion du Collège Privé Adventiste Avaratetezana</p>
        <div class="dashboard-quick-stats mt-4">
            <div class="row justify-content-center">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="quick-stat-card bg-primary-50">
                        <i class="icon-users4 stat-icon"></i>
                        <div class="stat-content">
                            <h3 class="stat-value counter-animated stat-editable" id="stat-students" data-type="number" data-label="Nombre d'élèves" data-format="number">{{ $total_active_students ?? '350+' }}</h3>
                            <p class="stat-label">Élèves</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="quick-stat-card bg-success-50">
                        <i class="icon-user-tie stat-icon"></i>
                        <div class="stat-content">
                            <h3 class="stat-value counter-animated stat-editable" id="stat-teachers" data-type="number" data-label="Nombre d'enseignants" data-format="number">{{ $users->where('user_type', 'teacher')->count() ?? '25+' }}</h3>
                            <p class="stat-label">Enseignants</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="quick-stat-card bg-info-50">
                        <i class="icon-books stat-icon"></i>
                        <div class="stat-content">
                            <h3 class="stat-value counter-animated stat-editable" id="stat-classes" data-type="number" data-label="Nombre de classes" data-format="number">12</h3>
                            <p class="stat-label">Classes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="quick-stat-card bg-warning-50">
                        <i class="icon-medal stat-icon"></i>
                        <div class="stat-content">
                            <h3 class="stat-value counter-animated stat-editable" id="stat-success-rate" data-type="number" data-label="Taux de réussite" data-format="percentage">95%</h3>
                            <p class="stat-label">Taux de réussite</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- School Information Card -->
    <div id="school-info-section" class="card fade-in mb-4 school-info-card">
        <div class="card-header bg-white d-flex align-items-center">
            <div class="d-flex align-items-center">
                <i class="icon-office icon-2x text-primary mr-2"></i>
                <h5 class="card-title mb-0">Informations sur l'établissement</h5>
            </div>
            <div class="header-elements ml-auto">
                <span class="badge badge-pill badge-primary mr-2 stat-editable" id="school-year" data-type="text" data-label="Année scolaire">Année scolaire 2023-2024</span>
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- School Identity Banner -->
            <div class="school-banner mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="school-name mb-1">Collège Privé Adventiste Avaratetezana</h3>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-pill badge-light mr-2">Code: <span class="stat-editable" id="school-code" data-type="text" data-label="Code établissement">102051020</span></span>
                            <span class="badge badge-pill badge-light mr-2">Type: <span class="stat-editable" id="school-type" data-type="text" data-label="Type d'établissement">Collège privé</span></span>
                            <span class="badge badge-pill badge-success">Statut: <span class="status-editable" id="school-status" data-label="Statut de l'établissement" data-options='["Actif", "Inactif", "En attente", "Suspendu"]'>Actif</span></span>
                        </div>
                        <div class="school-contact">
                            <span class="mr-3"><i class="icon-phone mr-1"></i> <span class="stat-editable" id="school-phone" data-type="text" data-label="Téléphone">038 34 921 09</span></span>
                            <span><i class="icon-envelop5 mr-1"></i> <span class="stat-editable" id="school-email" data-type="text" data-label="Email">epadventistavaratetezana@gmail.com</span></span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <div class="school-metrics">
                            <div class="metric-item">
                                <span class="metric-value counter-animated stat-editable" id="school-creation-year" data-type="number" data-label="Année de création" data-format="number">1985</span>
                                <span class="metric-label">Année de création</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-value counter-animated stat-editable" id="school-accreditation" data-type="text" data-label="Accréditation">A+</span>
                                <span class="metric-label">Accréditation</span>
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
                <span class="badge badge-pill badge-primary stat-editable" id="priorities-year" data-type="text" data-label="Année des priorités">Priorités 2023-2024</span>
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
                            <h3 class="mb-0 text-white">2023-24</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Année scolaire</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 60%"></div>
                        </div>
                        <div class="mt-1 text-right">
                            <small class="text-white-50">60% écoulé</small>
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

