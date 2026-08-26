@extends('layouts.master')
@section('page_title', 'Liste complète des étudiants')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/datatable_responsive.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/inline_editing.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/student_statistics.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/student_list_modern.css') }}">
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')

    {{-- Header du Dashboard Moderne --}}
    <div class="students-dashboard-header d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h2><i class="icon-users mr-2"></i>Liste Complète des Étudiants</h2>
            <p>Gestion centrale des effectifs, filtres avancés, personnalisation des colonnes et export Excel direct</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap" style="gap: 10px;">
            <a href="{{ route('students.statistics.print_report') }}" target="_blank" class="btn btn-light font-weight-semibold shadow-sm text-primary">
                <i class="icon-printer mr-1"></i> Rapport A4 Paysage
            </a>
            <a href="{{ route('students.statistics.export') }}" class="btn btn-light font-weight-semibold shadow-sm text-success">
                <i class="icon-file-excel mr-1"></i> Export Statistiques
            </a>
            @if(Qs::userIsTeamSA())
                <a href="{{ route('students.create') }}" class="btn btn-outline-light font-weight-semibold">
                    <i class="icon-plus2 mr-1"></i> Nouvel Élève
                </a>
            @endif
            <button type="button" class="btn btn-outline-light font-weight-semibold" onclick="location.reload();">
                <i class="icon-sync mr-1"></i> Actualiser
            </button>
        </div>
    </div>

    {{-- Cartes Statistiques KPI --}}
    @php
        $totalBoys = $all_students->where('user.gender', 'Male')->count();
        $totalGirls = $all_students->where('user.gender', 'Female')->count();
        $totalNormal = ($students_by_status['Normal'] ?? 0);
        $totalAdra = ($students_by_status['ADRA'] ?? 0);
        $totalTeam3 = ($students_by_status['TEAM3'] ?? 0);
    @endphp
    <div class="students-stats-grid">
        <div class="students-kpi-card kpi-total">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $total_students }}</div>
                <div class="kpi-title">Total Élèves</div>
                <div class="kpi-subtext">Effectif global inscrit</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-users4"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-boys">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalBoys }}</div>
                <div class="kpi-title">Garçons (Masculin)</div>
                <div class="kpi-subtext">{{ $total_students > 0 ? round(($totalBoys / $total_students) * 100, 1) : 0 }}% de l'effectif</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-user-tie"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-girls">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalGirls }}</div>
                <div class="kpi-title">Filles (Féminin)</div>
                <div class="kpi-subtext">{{ $total_students > 0 ? round(($totalGirls / $total_students) * 100, 1) : 0 }}% de l'effectif</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-woman"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-normal">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalNormal }}</div>
                <div class="kpi-title">Statut Normal</div>
                <div class="kpi-subtext">Régime standard</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-checkmark-circle"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-adra">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalAdra }}</div>
                <div class="kpi-title">Statut ADRA</div>
                <div class="kpi-subtext">Bénéficiaires ADRA</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-heart6"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-team3">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalTeam3 }}</div>
                <div class="kpi-title">Statut TEAM 3</div>
                <div class="kpi-subtext">Bénéficiaires TEAM 3</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-star-full2"></i>
            </div>
        </div>
    </div>

    {{-- Panneau de Visibilité des Colonnes & Export Excel --}}
    @include('partials.students.column_manager')

    {{-- Carte Principale avec Onglets --}}
    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-body p-3 p-md-4">
            
            {{-- Navigation par Onglets --}}
            <ul class="nav nav-tabs students-nav-tabs">
                <li class="nav-item">
                    <a href="#all-students" class="nav-link active" data-toggle="tab">
                        <i class="icon-list-unordered mr-1 text-primary"></i> Tous les élèves 
                        <span class="badge badge-primary badge-pill ml-1">{{ $total_students }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#student-stats" class="nav-link" data-toggle="tab">
                        <i class="icon-pie-chart mr-1 text-success"></i> Statistiques globales
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#detailed-stats" class="nav-link" data-toggle="tab">
                        <i class="icon-filter3 mr-1 text-info"></i> Filtres par âge & sexe
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#student-types" class="nav-link" data-toggle="tab">
                        <i class="icon-graduation2 mr-1 text-warning"></i> Types & Statuts
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#religion-stats" class="nav-link" data-toggle="tab">
                        <i class="icon-sphere mr-1 text-danger"></i> Religions & Cultes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#class-matrix-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-table2 mr-1 text-primary"></i> Synthèse par Classe
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-windows2 mr-1 text-secondary"></i> Classes ({{ count($my_classes) }})
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm">
                        @foreach($my_classes as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item d-flex justify-content-between align-items-center" data-toggle="tab">
                                <span>{{ $c->name }}</span>
                                <span class="badge badge-light-secondary badge-pill">{{ $all_students->where('my_class_id', $c->id)->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Onglet 1 : Tous les étudiants --}}
                <div class="tab-pane fade show active" id="all-students">
                    <div class="students-table-wrapper">
                        <table class="table students-table datatable-button-html5-columns">
                            <thead>
                                <tr>
                                    <th class="all" style="width: 40px;">N°</th>
                                    <th class="none" style="width: 50px;">Photo</th>
                                    <th class="all">Nom & Prénom</th>
                                    <th class="min-tablet">N° d'admission</th>
                                    <th class="min-tablet">Classe / Section</th>
                                    <th class="none">Date de naissance</th>
                                    <th class="none">Âge</th>
                                    <th class="none">Adresse</th>
                                    <th class="none">Religion</th>
                                    <th class="min-tablet">Statut</th>
                                    <th class="none">Type</th>
                                    <th class="none">Statut académique</th>
                                    <th class="none">Sexe</th>
                                    <th class="none">Père / Tuteur</th>
                                    <th class="none">Profession père</th>
                                    <th class="none">Mère / Tutrice</th>
                                    <th class="none">Profession mère</th>
                                    <th class="none">Téléphone</th>
                                    <th class="all no-export text-center" style="width: 110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($all_students as $s)
                                    <tr>
                                        <td class="font-weight-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">
                                            <img class="student-avatar" src="{{ $s->user->photo }}" alt="{{ $s->user->name }}" loading="lazy">
                                        </td>
                                        <td>
                                            <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="student-link-name editable editable-cell" data-field="name" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->name }}">
                                                {{ $s->user->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary font-weight-bold editable editable-cell" data-field="adm_no" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->adm_no }}">
                                                {{ $s->adm_no }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-weight-semibold text-dark">{{ $s->my_class->name }}</span>
                                            <small class="text-muted d-block">{{ $s->section->name }}</small>
                                        </td>
                                        <td class="editable editable-cell" data-field="dob" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->dob }}">
                                            {{ $s->user->dob ?? '-' }}
                                        </td>
                                        <td class="age-display age-column font-weight-semibold text-center" data-student-id="{{ Qs::hash($s->id) }}" data-dob="{{ $s->user->dob }}">
                                            @if($s->user->dob)
                                                <span class="badge badge-light-info font-weight-semibold">{{ \App\Helpers\Qs::calculateAge($s->user->dob) }} ans</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="editable editable-cell" data-field="address" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->address }}">
                                            {{ $s->user->address ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell" data-field="religion" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->religion }}">
                                            {{ $s->user->religion ?? '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusVal = $s->user->status ?? 'Normal';
                                                $badgeCls = ($statusVal == 'ADRA') ? 'badge-light-warning' : (($statusVal == 'TEAM3' || $statusVal == 'Team3') ? 'badge-light-danger' : 'badge-light-success');
                                            @endphp
                                            <span class="badge {{ $badgeCls }} font-weight-bold editable editable-cell" data-field="status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $statusVal }}">
                                                {{ $statusVal }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-secondary editable editable-cell" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->student_type ?? 'Nouveau' }}">
                                                {{ $s->user->student_type ?? 'Nouveau' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info editable editable-cell" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->academic_status ?? 'Passant' }}">
                                                {{ $s->user->academic_status ?? 'Passant' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($s->user->gender == 'Male')
                                                <span class="gender-badge male editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="Male">
                                                    <i class="icon-user-tie mr-1"></i> Masculin
                                                </span>
                                            @elseif($s->user->gender == 'Female')
                                                <span class="gender-badge female editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="Female">
                                                    <i class="icon-woman mr-1"></i> Féminin
                                                </span>
                                            @else
                                                <span class="editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="">-</span>
                                            @endif
                                        </td>
                                        <td class="editable editable-cell" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_p }}">
                                            {{ $s->user->nom_p ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell text-muted font-size-sm" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_p }}">
                                            {{ $s->user->prof_p ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_m }}">
                                            {{ $s->user->nom_m ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell text-muted font-size-sm" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_m }}">
                                            {{ $s->user->prof_m ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell font-weight-semibold" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->phone }}">
                                            {{ $s->user->phone ?? '-' }}
                                        </td>
                                        @include('partials.student_action_buttons', ['s' => $s])
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Onglet 2 : Statistiques des étudiants --}}
                <div class="tab-pane fade" id="student-stats">
                    <div class="row mt-2">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="icon-pie-chart mr-1 text-success"></i> Répartition par Statut
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Statut</th>
                                                    <th class="text-center">Nombre d'étudiants</th>
                                                    <th class="text-center">Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students_by_status as $status => $count)
                                                <tr>
                                                    <td>
                                                        <span class="badge {{ $status == 'Normal' ? 'badge-light-success' : ($status == 'ADRA' ? 'badge-light-warning' : 'badge-light-danger') }} font-weight-bold">
                                                            {{ $status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center font-weight-bold">{{ $count }}</td>
                                                    <td class="text-center">{{ $total_students > 0 ? number_format(($count / $total_students) * 100, 1) : '0.0' }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="status-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="icon-stats-bars mr-1 text-primary"></i> Répartition par Classe
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive mb-3" style="max-height: 150px; overflow-y: auto;">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Classe</th>
                                                    <th class="text-center">Effectif</th>
                                                    <th class="text-center">Part</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students_by_class as $class)
                                                <tr>
                                                    <td><strong>{{ $class['name'] }}</strong></td>
                                                    <td class="text-center font-weight-bold">{{ $class['count'] }}</td>
                                                    <td class="text-center">{{ $total_students > 0 ? number_format(($class['count'] / $total_students) * 100, 1) : '0.0' }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="class-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Onglet 3 : Statistiques Détaillées par Âge & Filtres --}}
                <div class="tab-pane fade" id="detailed-stats">
                    <div class="card border shadow-none" style="border-radius: 12px;">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="card-title font-weight-bold mb-0 text-dark">
                                <i class="icon-filter3 mr-1 text-primary"></i> Filtres combinés d'analyse
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Filtres Ligne 1 -->
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-graduation2"></i> Classe</label>
                                    <select id="class-filter" class="form-control form-control-sm">
                                        <option value="">Toutes les classes</option>
                                        @foreach($my_classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-calendar"></i> Âge min</label>
                                    <select id="age-min-filter" class="form-control form-control-sm">
                                        <option value="">Aucun</option>
                                        @for($age = 3; $age <= 25; $age++)
                                            <option value="{{ $age }}">{{ $age }} ans</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-calendar"></i> Âge max</label>
                                    <select id="age-max-filter" class="form-control form-control-sm">
                                        <option value="">Aucun</option>
                                        @for($age = 3; $age <= 25; $age++)
                                            <option value="{{ $age }}">{{ $age }} ans</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-users"></i> Sexe</label>
                                    <select id="gender-filter" class="form-control form-control-sm">
                                        <option value="">Tous</option>
                                        <option value="Male">Masculin</option>
                                        <option value="Female">Féminin</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-certificate"></i> Statut</label>
                                    <select id="status-filter" class="form-control form-control-sm">
                                        <option value="">Tous les statuts</option>
                                        <option value="Normal">Normal</option>
                                        <option value="ADRA">ADRA</option>
                                        <option value="TEAM3">TEAM3</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Filtres Ligne 2 -->
                            <div class="row g-2 mb-4">
                                <div class="col-md-3">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-sphere"></i> Religion</label>
                                    <select id="religion-filter" class="form-control form-control-sm">
                                        <option value="">Toutes les religions</option>
                                        <option value="Adventiste">Adventiste</option>
                                        <option value="Catholique">Catholique</option>
                                        <option value="FJKM">FJKM</option>
                                        <option value="FLM">FLM</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Judaïsme">Judaïsme</option>
                                        <option value="Apokalipsy">Apokalipsy</option>
                                        <option value="Autres">Autres</option>
                                        <option value="Non renseigné">Non renseigné</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-book"></i> Type</label>
                                    <select id="student-type-filter" class="form-control form-control-sm">
                                        <option value="">Tous les types</option>
                                        <option value="Nouveau">Nouveau</option>
                                        <option value="Ancien">Ancien</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-semibold font-size-xs text-uppercase text-muted"><i class="icon-trophy"></i> Statut académique</label>
                                    <select id="academic-status-filter" class="form-control form-control-sm">
                                        <option value="">Tous</option>
                                        <option value="Passant">Passant</option>
                                        <option value="Redoublant">Redoublant</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-primary" onclick="applyFilters()">
                                            <i class="icon-filter3 mr-1"></i> Filtrer
                                        </button>
                                        <button class="btn btn-light border" onclick="resetFilters()">
                                            <i class="icon-reset mr-1"></i> Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- KPIs Filtrés -->
                            <div class="row g-2 mb-4">
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded text-center border">
                                        <h4 id="stat-total" class="mb-0 font-weight-bold text-primary">0</h4>
                                        <small class="text-muted text-uppercase font-weight-semibold">Total filtré</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded text-center border">
                                        <h4 id="stat-age-moyen" class="mb-0 font-weight-bold text-info">-</h4>
                                        <small class="text-muted text-uppercase font-weight-semibold">Âge moyen</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded text-center border">
                                        <h4 id="stat-garcons" class="mb-0 font-weight-bold text-success">0</h4>
                                        <small class="text-muted text-uppercase font-weight-semibold">Garçons</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded text-center border">
                                        <h4 id="stat-filles" class="mb-0 font-weight-bold text-danger">0</h4>
                                        <small class="text-muted text-uppercase font-weight-semibold">Filles</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Graphique par Âge -->
                            <div class="card mb-4 border shadow-none" style="border-radius: 10px;">
                                <div class="card-header bg-light">
                                    <h6 class="card-title font-weight-bold mb-0">
                                        <i class="icon-stats-bars mr-1"></i> Répartition par Âge et Sexe
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 280px;">
                                        <canvas id="age-distribution-chart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau de répartition détaillée -->
                            <div class="table-responsive mb-4">
                                <table id="gender-age-breakdown-table" class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">Âge</th>
                                            <th class="text-center text-primary"><i class="icon-user-tie"></i> Garçons</th>
                                            <th class="text-center text-danger"><i class="icon-woman"></i> Filles</th>
                                            <th class="text-center bg-info text-white"><i class="icon-users"></i> Total</th>
                                            <th class="text-center">% Garçons</th>
                                            <th class="text-center">% Filles</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gender-age-breakdown-body">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">
                                                <i class="icon-info3 mr-1"></i> Cliquez sur "Appliquer les filtres" pour générer les statistiques détaillées
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td class="text-center">TOTAL</td>
                                            <td class="text-center text-primary" id="total-boys">0</td>
                                            <td class="text-center text-danger" id="total-girls">0</td>
                                            <td class="text-center bg-info text-white" id="total-all">0</td>
                                            <td class="text-center" id="total-boys-percent">-</td>
                                            <td class="text-center" id="total-girls-percent">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Tableau des Étudiants Filtrés -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold mb-0"><i class="icon-table2 mr-1"></i> Élèves filtrés</h6>
                                <button class="btn btn-success btn-sm" onclick="exportFilteredStudents()">
                                    <i class="icon-file-excel mr-1"></i> Exporter la sélection filtrée
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="filtered-students-table" class="table table-bordered table-striped table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom & Prénom</th>
                                            <th>Classe</th>
                                            <th>Section</th>
                                            <th>Âge</th>
                                            <th>Sexe</th>
                                            <th>Statut</th>
                                            <th>Type</th>
                                            <th>Téléphone</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filtered-students-body">
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-3">
                                                <i class="icon-info3 mr-1"></i> Appliquez les filtres pour afficher les élèves correspondants
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Onglet 4 : Types d'étudiants & Statuts académiques --}}
                <div class="tab-pane fade" id="student-types">
                    <div class="row mt-2">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="icon-book mr-1 text-primary"></i> Types d'élèves (Nouveaux / Anciens)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container mb-3" style="height: 250px;">
                                        <canvas id="student-type-chart"></canvas>
                                    </div>
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Type</th>
                                                <th class="text-center">Effectif</th>
                                                <th class="text-center">Pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-light-success font-weight-bold">Nouveau</span></td>
                                                <td class="text-center font-weight-bold" id="nouveaux-count">-</td>
                                                <td class="text-center" id="nouveaux-percent">-</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-light-primary font-weight-bold">Ancien</span></td>
                                                <td class="text-center font-weight-bold" id="anciens-count">-</td>
                                                <td class="text-center" id="anciens-percent">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="icon-trophy mr-1 text-warning"></i> Statuts Académiques (Passants / Redoublants)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container mb-3" style="height: 250px;">
                                        <canvas id="academic-status-chart"></canvas>
                                    </div>
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Statut académique</th>
                                                <th class="text-center">Effectif</th>
                                                <th class="text-center">Pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-light-info font-weight-bold">Passant</span></td>
                                                <td class="text-center font-weight-bold" id="passants-count">-</td>
                                                <td class="text-center" id="passants-percent">-</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-light-warning font-weight-bold">Redoublant</span></td>
                                                <td class="text-center font-weight-bold" id="redoublants-count">-</td>
                                                <td class="text-center" id="redoublants-percent">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Onglet 5 : Statistiques par Religion / Culte --}}
                <div class="tab-pane fade" id="religion-stats">
                    <div class="row mt-2">
                        <div class="col-md-5 mb-4">
                            <div class="card h-100 border shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="icon-pie-chart mr-1 text-danger"></i> Répartition par Religion
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 280px;">
                                        <canvas id="religion-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7 mb-4">
                            <div class="card h-100 border shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="icon-table2 mr-1 text-primary"></i> Détail des Effectifs par Dénomination
                                    </h6>
                                    <span class="badge badge-light-primary font-weight-bold">Total : {{ $total_students }}</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Religion / Dénomination</th>
                                                    <th class="text-center">Effectif</th>
                                                    <th class="text-center">♂ Garçons</th>
                                                    <th class="text-center">♀ Filles</th>
                                                    <th class="text-center">Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students_by_religion as $rel => $rCount)
                                                @php
                                                    $rBoys = $all_students->filter(fn($s) => ($rel === 'Non renseigné' ? (!$s->user || is_null($s->user->religion)) : ($s->user && $s->user->religion === $rel)) && $s->user->gender === 'Male')->count();
                                                    $rGirls = $all_students->filter(fn($s) => ($rel === 'Non renseigné' ? (!$s->user || is_null($s->user->religion)) : ($s->user && $s->user->religion === $rel)) && $s->user->gender === 'Female')->count();
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $rel }}</strong></td>
                                                    <td class="text-center font-weight-bold">{{ $rCount }}</td>
                                                    <td class="text-center text-primary">{{ $rBoys }}</td>
                                                    <td class="text-center text-danger">{{ $rGirls }}</td>
                                                    <td class="text-center">{{ $total_students > 0 ? number_format(($rCount / $total_students) * 100, 1) : '0.0' }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light font-weight-bold">
                                                <tr>
                                                    <td>TOTAL</td>
                                                    <td class="text-center">{{ $total_students }}</td>
                                                    <td class="text-center text-primary">{{ $totalBoys }}</td>
                                                    <td class="text-center text-danger">{{ $totalGirls }}</td>
                                                    <td class="text-center">100.0%</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Onglet 6 : Tableau Croisé Synthétique par Classe --}}
                <div class="tab-pane fade" id="class-matrix-tab">
                    <div class="card border shadow-none" style="border-radius: 12px;">
                        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="card-title font-weight-bold mb-0 text-dark">
                                <i class="icon-grid5 mr-1 text-primary"></i> Synthèse Croisée Complète par Classe
                            </h6>
                            <div>
                                <a href="{{ route('students.statistics.print_report') }}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="icon-printer mr-1"></i> Imprimer Rapport A4
                                </a>
                                <a href="{{ route('students.statistics.export') }}" class="btn btn-success btn-sm ml-1">
                                    <i class="icon-file-excel mr-1"></i> Export Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-nowrap mb-0 font-size-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th rowspan="2" class="text-left font-weight-bold" style="vertical-align: middle;">Classe</th>
                                            <th rowspan="2" class="text-center font-weight-bold" style="vertical-align: middle;">Effectif</th>
                                            <th colspan="3" class="text-center" style="background: #e0e7ff;">Genre</th>
                                            <th rowspan="2" class="text-center font-weight-bold" style="vertical-align: middle;">Âge Moy.</th>
                                            <th colspan="3" class="text-center" style="background: #ecfdf5;">Régime / Statut</th>
                                            <th colspan="2" class="text-center" style="background: #fef3c7;">Type</th>
                                            <th colspan="2" class="text-center" style="background: #f1f5f9;">Statut Académique</th>
                                            <th colspan="4" class="text-center" style="background: #fae8ff;">Religions Principales</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background: #e0e7ff;">♂ Garçons</th>
                                            <th class="text-center" style="background: #e0e7ff;">♀ Filles</th>
                                            <th class="text-center" style="background: #e0e7ff;">% F</th>
                                            <th class="text-center" style="background: #ecfdf5;">Normal</th>
                                            <th class="text-center" style="background: #ecfdf5;">ADRA</th>
                                            <th class="text-center" style="background: #ecfdf5;">TEAM3</th>
                                            <th class="text-center" style="background: #fef3c7;">Nouveau</th>
                                            <th class="text-center" style="background: #fef3c7;">Ancien</th>
                                            <th class="text-center" style="background: #f1f5f9;">Passant</th>
                                            <th class="text-center" style="background: #f1f5f9;">Redoublant</th>
                                            <th class="text-center" style="background: #fae8ff;">Adventiste</th>
                                            <th class="text-center" style="background: #fae8ff;">Catholique</th>
                                            <th class="text-center" style="background: #fae8ff;">FJKM</th>
                                            <th class="text-center" style="background: #fae8ff;">Autres</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($class_matrix as $row)
                                        <tr>
                                            <td class="font-weight-bold"><a href="#c{{ $row['id'] }}" data-toggle="tab">{{ $row['name'] }}</a></td>
                                            <td class="text-center font-weight-bold bg-light">{{ $row['total'] }}</td>
                                            <td class="text-center text-primary">{{ $row['boys'] }}</td>
                                            <td class="text-center text-danger">{{ $row['girls'] }}</td>
                                            <td class="text-center">{{ $row['total'] > 0 ? round(($row['girls'] / $row['total']) * 100, 0) : 0 }}%</td>
                                            <td class="text-center font-weight-semibold">{{ $row['avg_age'] }} ans</td>
                                            <td class="text-center">{{ $row['normal'] }}</td>
                                            <td class="text-center text-warning font-weight-bold">{{ $row['adra'] }}</td>
                                            <td class="text-center text-danger font-weight-bold">{{ $row['team3'] }}</td>
                                            <td class="text-center">{{ $row['nouveau'] }}</td>
                                            <td class="text-center">{{ $row['ancien'] }}</td>
                                            <td class="text-center">{{ $row['passant'] }}</td>
                                            <td class="text-center">{{ $row['redoublant'] }}</td>
                                            <td class="text-center">{{ $row['adventiste'] }}</td>
                                            <td class="text-center">{{ $row['catholique'] }}</td>
                                            <td class="text-center">{{ $row['fjkm'] }}</td>
                                            <td class="text-center">{{ $row['autres_rel'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr style="background-color: #d1fae5;">
                                            <td>TOTAL ÉCOLE</td>
                                            <td class="text-center">{{ $total_students }}</td>
                                            <td class="text-center text-primary">{{ $totalBoys }}</td>
                                            <td class="text-center text-danger">{{ $totalGirls }}</td>
                                            <td class="text-center">{{ $total_students > 0 ? round(($totalGirls / $total_students) * 100, 1) : 0 }}%</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">{{ $totalNormal }}</td>
                                            <td class="text-center text-warning">{{ $totalAdra }}</td>
                                            <td class="text-center text-danger">{{ $totalTeam3 }}</td>
                                            <td class="text-center">{{ $all_students->where('user.student_type', 'Nouveau')->count() }}</td>
                                            <td class="text-center">{{ $all_students->where('user.student_type', 'Ancien')->count() }}</td>
                                            <td class="text-center">{{ $all_students->where('user.academic_status', 'Passant')->count() }}</td>
                                            <td class="text-center">{{ $all_students->where('user.academic_status', 'Redoublant')->count() }}</td>
                                            <td class="text-center">{{ $students_by_religion['Adventiste'] ?? 0 }}</td>
                                            <td class="text-center">{{ $students_by_religion['Catholique'] ?? 0 }}</td>
                                            <td class="text-center">{{ $students_by_religion['FJKM'] ?? 0 }}</td>
                                            <td class="text-center">{{ $total_students - (($students_by_religion['Adventiste'] ?? 0) + ($students_by_religion['Catholique'] ?? 0) + ($students_by_religion['FJKM'] ?? 0)) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Onglets par Classe individuelle --}}
                @foreach($my_classes as $c)
                <div class="tab-pane fade" id="c{{ $c->id }}">
                    <div class="students-table-wrapper">
                        <table class="table students-table datatable-button-html5-columns">
                            <thead>
                                <tr>
                                    <th class="all" style="width: 40px;">N°</th>
                                    <th class="none" style="width: 50px;">Photo</th>
                                    <th class="all">Nom & Prénom</th>
                                    <th class="min-tablet">N° d'admission</th>
                                    <th class="min-tablet">Section</th>
                                    <th class="none">Date de naissance</th>
                                    <th class="none">Âge</th>
                                    <th class="none">Adresse</th>
                                    <th class="none">Religion</th>
                                    <th class="min-tablet">Statut</th>
                                    <th class="none">Type</th>
                                    <th class="none">Statut académique</th>
                                    <th class="none">Sexe</th>
                                    <th class="none">Père / Tuteur</th>
                                    <th class="none">Profession père</th>
                                    <th class="none">Mère / Tutrice</th>
                                    <th class="none">Profession mère</th>
                                    <th class="none">Téléphone</th>
                                    <th class="all no-export text-center" style="width: 110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($all_students->where('my_class_id', $c->id) as $s)
                                    <tr>
                                        <td class="font-weight-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">
                                            <img class="student-avatar" src="{{ $s->user->photo }}" alt="{{ $s->user->name }}" loading="lazy">
                                        </td>
                                        <td>
                                            <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="student-link-name editable editable-cell" data-field="name" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->name }}">
                                                {{ $s->user->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary font-weight-bold editable editable-cell" data-field="adm_no" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->adm_no }}">
                                                {{ $s->adm_no }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-weight-semibold">{{ $s->section->name }}</span>
                                        </td>
                                        <td class="editable editable-cell" data-field="dob" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->dob }}">
                                            {{ $s->user->dob ?? '-' }}
                                        </td>
                                        <td class="age-display age-column font-weight-semibold text-center" data-student-id="{{ Qs::hash($s->id) }}" data-dob="{{ $s->user->dob }}">
                                            @if($s->user->dob)
                                                <span class="badge badge-light-info font-weight-semibold">{{ \App\Helpers\Qs::calculateAge($s->user->dob) }} ans</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="editable editable-cell" data-field="address" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->address }}">
                                            {{ $s->user->address ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell" data-field="religion" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->religion }}">
                                            {{ $s->user->religion ?? '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusVal = $s->user->status ?? 'Normal';
                                                $badgeCls = ($statusVal == 'ADRA') ? 'badge-light-warning' : (($statusVal == 'TEAM3' || $statusVal == 'Team3') ? 'badge-light-danger' : 'badge-light-success');
                                            @endphp
                                            <span class="badge {{ $badgeCls }} font-weight-bold editable editable-cell" data-field="status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $statusVal }}">
                                                {{ $statusVal }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-secondary editable editable-cell" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->student_type ?? 'Nouveau' }}">
                                                {{ $s->user->student_type ?? 'Nouveau' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info editable editable-cell" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->academic_status ?? 'Passant' }}">
                                                {{ $s->user->academic_status ?? 'Passant' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($s->user->gender == 'Male')
                                                <span class="gender-badge male editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="Male">
                                                    <i class="icon-user-tie mr-1"></i> Masculin
                                                </span>
                                            @elseif($s->user->gender == 'Female')
                                                <span class="gender-badge female editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="Female">
                                                    <i class="icon-woman mr-1"></i> Féminin
                                                </span>
                                            @else
                                                <span class="editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="">-</span>
                                            @endif
                                        </td>
                                        <td class="editable editable-cell" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_p }}">
                                            {{ $s->user->nom_p ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell text-muted font-size-sm" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_p }}">
                                            {{ $s->user->prof_p ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_m }}">
                                            {{ $s->user->nom_m ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell text-muted font-size-sm" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_m }}">
                                            {{ $s->user->prof_m ?? '-' }}
                                        </td>
                                        <td class="editable editable-cell font-weight-semibold" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->phone }}">
                                            {{ $s->user->phone ?? '-' }}
                                        </td>
                                        @include('partials.student_action_buttons', ['s' => $s])
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/inline_editing.js') }}"></script>
<script src="{{ asset('assets/js/student_table_manager.js') }}"></script>
<script>
    $(document).ready(function() {
        if (window.inlineEditor) {
            window.inlineEditor.options.csrfToken = '{{ csrf_token() }}';
            window.inlineEditor.options.saveUrl = '{{ route("ajax.update_student_field") }}';
        }

        // Initialiser les graphiques
        if (typeof Chart !== 'undefined') {
            const statusCtx = document.getElementById('status-chart');
            if (statusCtx) {
                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode(array_keys($students_by_status)) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($students_by_status)) !!},
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 2
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
            }

            const classCtx = document.getElementById('class-chart');
            if (classCtx) {
                const classLabels = [];
                const classData = [];
                const classColors = ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#3b82f6', '#14b8a6'];

                @foreach($students_by_class as $idx => $class)
                    classLabels.push("{{ $class['name'] }}");
                    classData.push({{ $class['count'] }});
                @endforeach

                new Chart(classCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: classLabels,
                        datasets: [{
                            label: 'Élèves par classe',
                            data: classData,
                            backgroundColor: classColors.slice(0, classLabels.length),
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }

            const religionCtx = document.getElementById('religion-chart');
            if (religionCtx) {
                new Chart(religionCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode(array_keys($students_by_religion)) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($students_by_religion)) !!},
                            backgroundColor: ['#4f46e5', '#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#64748b', '#cbd5e0'],
                            borderWidth: 2
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
            }
        }

        // Calculer les types d'élèves
        calculateStudentTypeStats();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') === '#student-types') {
                calculateStudentTypeStats();
            }
        });

        function calculateStudentTypeStats() {
            let nouveauxCount = 0;
            let anciensCount = 0;
            let passantsCount = 0;
            let redoublantsCount = 0;
            const totalStudents = {{ $total_students }};
            
            $('.editable[data-field="student_type"]').each(function() {
                const value = $(this).text().trim();
                if (value === 'Nouveau') {
                    nouveauxCount++;
                } else if (value === 'Ancien') {
                    anciensCount++;
                }
            });
            
            $('.editable[data-field="academic_status"]').each(function() {
                const value = $(this).text().trim();
                if (value === 'Passant') {
                    passantsCount++;
                } else if (value === 'Redoublant') {
                    redoublantsCount++;
                }
            });
            
            $('#nouveaux-count').text(nouveauxCount);
            $('#anciens-count').text(anciensCount);
            $('#nouveaux-percent').text(totalStudents > 0 ? (nouveauxCount / totalStudents * 100).toFixed(1) + '%' : '0%');
            $('#anciens-percent').text(totalStudents > 0 ? (anciensCount / totalStudents * 100).toFixed(1) + '%' : '0%');
            
            $('#passants-count').text(passantsCount);
            $('#redoublants-count').text(redoublantsCount);
            $('#passants-percent').text(totalStudents > 0 ? (passantsCount / totalStudents * 100).toFixed(1) + '%' : '0%');
            $('#redoublants-percent').text(totalStudents > 0 ? (redoublantsCount / totalStudents * 100).toFixed(1) + '%' : '0%');
            
            if (typeof Chart !== 'undefined') {
                const studentTypeCtx = document.getElementById('student-type-chart');
                if (studentTypeCtx && !window.studentTypeChart) {
                    window.studentTypeChart = new Chart(studentTypeCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Nouveaux', 'Anciens'],
                            datasets: [{
                                data: [nouveauxCount, anciensCount],
                                backgroundColor: ['#10b981', '#3b82f6'],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                } else if (window.studentTypeChart) {
                    window.studentTypeChart.data.datasets[0].data = [nouveauxCount, anciensCount];
                    window.studentTypeChart.update();
                }
                
                const academicStatusCtx = document.getElementById('academic-status-chart');
                if (academicStatusCtx && !window.academicStatusChart) {
                    window.academicStatusChart = new Chart(academicStatusCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Passants', 'Redoublants'],
                            datasets: [{
                                data: [passantsCount, redoublantsCount],
                                backgroundColor: ['#06b6d4', '#f59e0b'],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                } else if (window.academicStatusChart) {
                    window.academicStatusChart.data.datasets[0].data = [passantsCount, redoublantsCount];
                    window.academicStatusChart.update();
                }
            }
        }

        // Filtres par âge
        window.allStudentsData = [
            @foreach($all_students as $s)
                {
                    id: {{ $s->id }},
                    name: "{!! addslashes($s->user->name) !!}",
                    class_id: {{ $s->my_class_id }},
                    class_name: "{{ $s->my_class->name }}",
                    section_name: "{{ $s->section->name }}",
                    dob: "{{ $s->user->dob }}",
                    age: {{ $s->user->dob ? \App\Helpers\Qs::calculateAge($s->user->dob) : 0 }},
                    gender: "{{ $s->user->gender }}",
                    religion: "{!! addslashes($s->user->religion ?? 'Non renseigné') !!}",
                    status: "{{ $s->user->status ?? 'Normal' }}",
                    student_type: "{{ $s->user->student_type ?? 'Nouveau' }}",
                    academic_status: "{{ $s->user->academic_status ?? 'Passant' }}",
                    phone: "{{ $s->user->phone }}"
                },
            @endforeach
        ];

        window.ageDistributionChart = null;

        window.applyFilters = function() {
            const classId = $('#class-filter').val();
            const ageMin = $('#age-min-filter').val();
            const ageMax = $('#age-max-filter').val();
            const gender = $('#gender-filter').val();
            const religion = $('#religion-filter').val();
            const status = $('#status-filter').val();
            const studentType = $('#student-type-filter').val();
            const academicStatus = $('#academic-status-filter').val();

            let filteredStudents = window.allStudentsData.filter(student => {
                if (student.age === 0) return false;
                if (classId && student.class_id != classId) return false;
                if (ageMin && student.age < parseInt(ageMin)) return false;
                if (ageMax && student.age > parseInt(ageMax)) return false;
                if (gender && student.gender !== gender) return false;
                if (religion && student.religion !== religion) return false;
                if (status && student.status !== status) return false;
                if (studentType && student.student_type !== studentType) return false;
                if (academicStatus && student.academic_status !== academicStatus) return false;
                return true;
            });

            updateStats(filteredStudents);
            updateTable(filteredStudents);
            updateAgeDistributionChart(filteredStudents);
            updateGenderAgeBreakdown(filteredStudents);
        };

        function updateStats(students) {
            const total = students.length;
            const boys = students.filter(s => s.gender === 'Male').length;
            const girls = students.filter(s => s.gender === 'Female').length;
            
            let ageSum = 0;
            students.forEach(s => { ageSum += s.age; });
            const avgAge = total > 0 ? (ageSum / total).toFixed(1) : 0;

            $('#stat-total').text(total);
            $('#stat-garcons').text(boys);
            $('#stat-filles').text(girls);
            $('#stat-age-moyen').text(total > 0 ? avgAge + ' ans' : '-');
        }

        function updateTable(students) {
            const tbody = $('#filtered-students-body');
            tbody.empty();

            if (students.length === 0) {
                tbody.append('<tr><td colspan="9" class="text-center text-muted py-3"><i class="icon-info3 mr-1"></i> Aucun élève ne correspond aux critères sélectionnés</td></tr>');
                return;
            }

            students.forEach((student, index) => {
                const genderBadge = student.gender === 'Male' ? 
                    '<span class="gender-badge male"><i class="icon-user-tie mr-1"></i> M</span>' : 
                    '<span class="gender-badge female"><i class="icon-woman mr-1"></i> F</span>';
                
                const statusBadge = student.status === 'Normal' ? 
                    '<span class="badge badge-light-success font-weight-bold">Normal</span>' :
                    (student.status === 'ADRA' ? '<span class="badge badge-light-warning font-weight-bold">ADRA</span>' : '<span class="badge badge-light-danger font-weight-bold">TEAM3</span>');

                tbody.append(`
                    <tr>
                        <td class="text-center font-weight-semibold">${index + 1}</td>
                        <td><strong>${student.name}</strong></td>
                        <td><span class="badge badge-light-secondary">${student.class_name}</span></td>
                        <td>${student.section_name}</td>
                        <td class="text-center"><span class="badge badge-light-info font-weight-bold">${student.age} ans</span></td>
                        <td class="text-center">${genderBadge}</td>
                        <td>${statusBadge}</td>
                        <td>${student.student_type}</td>
                        <td>${student.phone || '-'}</td>
                    </tr>
                `);
            });
        }

        function updateAgeDistributionChart(students) {
            const ageGenderCount = {};
            students.forEach(s => {
                if (s.age > 0) {
                    if (!ageGenderCount[s.age]) ageGenderCount[s.age] = { boys: 0, girls: 0 };
                    if (s.gender === 'Male') ageGenderCount[s.age].boys++;
                    else if (s.gender === 'Female') ageGenderCount[s.age].girls++;
                }
            });

            const ages = Object.keys(ageGenderCount).map(Number).sort((a, b) => a - b);
            const boysData = ages.map(age => ageGenderCount[age].boys);
            const girlsData = ages.map(age => ageGenderCount[age].girls);

            const ctx = document.getElementById('age-distribution-chart');
            if (ctx && typeof Chart !== 'undefined') {
                if (window.ageDistributionChart) window.ageDistributionChart.destroy();

                window.ageDistributionChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ages.map(age => age + ' ans'),
                        datasets: [
                            { label: 'Garçons', data: boysData, backgroundColor: '#3b82f6', borderRadius: 4 },
                            { label: 'Filles', data: girlsData, backgroundColor: '#ec4899', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }
        }

        function updateGenderAgeBreakdown(students) {
            const ageGenderCount = {};
            let totalBoys = 0, totalGirls = 0;

            students.forEach(s => {
                if (s.age > 0) {
                    if (!ageGenderCount[s.age]) ageGenderCount[s.age] = { boys: 0, girls: 0 };
                    if (s.gender === 'Male') { ageGenderCount[s.age].boys++; totalBoys++; }
                    else if (s.gender === 'Female') { ageGenderCount[s.age].girls++; totalGirls++; }
                }
            });

            const ages = Object.keys(ageGenderCount).map(Number).sort((a, b) => a - b);
            const tbody = $('#gender-age-breakdown-body');
            tbody.empty();

            if (ages.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center text-muted py-3"><i class="icon-info3 mr-1"></i> Aucune donnée disponible</td></tr>');
            } else {
                ages.forEach(age => {
                    const boys = ageGenderCount[age].boys;
                    const girls = ageGenderCount[age].girls;
                    const total = boys + girls;
                    const boysPercent = total > 0 ? ((boys / total) * 100).toFixed(1) : 0;
                    const girlsPercent = total > 0 ? ((girls / total) * 100).toFixed(1) : 0;

                    tbody.append(`
                        <tr>
                            <td class="text-center font-weight-bold">${age} ans</td>
                            <td class="text-center text-primary font-weight-bold">${boys}</td>
                            <td class="text-center text-danger font-weight-bold">${girls}</td>
                            <td class="text-center bg-light font-weight-bold">${total}</td>
                            <td class="text-center">${boysPercent}%</td>
                            <td class="text-center">${girlsPercent}%</td>
                        </tr>
                    `);
                });
            }

            const totalAll = totalBoys + totalGirls;
            const totalBoysPercent = totalAll > 0 ? ((totalBoys / totalAll) * 100).toFixed(1) : 0;
            const totalGirlsPercent = totalAll > 0 ? ((totalGirls / totalAll) * 100).toFixed(1) : 0;

            $('#total-boys').text(totalBoys);
            $('#total-girls').text(totalGirls);
            $('#total-all').text(totalAll);
            $('#total-boys-percent').text(totalBoysPercent + '%');
            $('#total-girls-percent').text(totalGirlsPercent + '%');
        }

        window.resetFilters = function() {
            $('#class-filter, #age-min-filter, #age-max-filter, #gender-filter, #religion-filter, #status-filter, #student-type-filter, #academic-status-filter').val('');
            $('#stat-total, #stat-garcons, #stat-filles, #total-boys, #total-girls, #total-all').text('0');
            $('#stat-age-moyen, #total-boys-percent, #total-girls-percent').text('-');
            $('#filtered-students-body').html('<tr><td colspan="9" class="text-center text-muted py-3"><i class="icon-info3 mr-1"></i> Appliquez les filtres pour voir les résultats</td></tr>');
            $('#gender-age-breakdown-body').html('<tr><td colspan="6" class="text-center text-muted py-3"><i class="icon-info3 mr-1"></i> Appliquez les filtres pour voir les statistiques</td></tr>');
            if (window.ageDistributionChart) {
                window.ageDistributionChart.destroy();
                window.ageDistributionChart = null;
            }
        };

        window.exportFilteredStudents = function() {
            if (typeof XLSX === 'undefined') {
                alert('Bibliothèque XLSX indisponible.');
                return;
            }

            const classId = $('#class-filter').val();
            const ageMin = $('#age-min-filter').val();
            const ageMax = $('#age-max-filter').val();
            const gender = $('#gender-filter').val();
            const religion = $('#religion-filter').val();
            const status = $('#status-filter').val();
            const studentType = $('#student-type-filter').val();
            const academicStatus = $('#academic-status-filter').val();

            let filteredStudents = window.allStudentsData.filter(student => {
                if (student.age === 0) return false;
                if (classId && student.class_id != classId) return false;
                if (ageMin && student.age < parseInt(ageMin)) return false;
                if (ageMax && student.age > parseInt(ageMax)) return false;
                if (gender && student.gender !== gender) return false;
                if (religion && student.religion !== religion) return false;
                if (status && student.status !== status) return false;
                if (studentType && student.student_type !== studentType) return false;
                if (academicStatus && student.academic_status !== academicStatus) return false;
                return true;
            });

            if (filteredStudents.length === 0) {
                alert('Aucun élève à exporter avec ces critères.');
                return;
            }

            const exportData = filteredStudents.map((student, index) => {
                return {
                    'N°': index + 1,
                    'Nom & Prénom': student.name,
                    'Classe': student.class_name,
                    'Section': student.section_name,
                    'Âge': student.age + ' ans',
                    'Sexe': student.gender === 'Male' ? 'Masculin' : 'Féminin',
                    'Religion': student.religion || '-',
                    'Statut': student.status,
                    'Type': student.student_type,
                    'Téléphone': student.phone || '-'
                };
            });

            const ws = XLSX.utils.json_to_sheet(exportData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Élèves filtrés');
            XLSX.writeFile(wb, `Eleves_Filtres_${new Date().toISOString().split('T')[0]}.xlsx`);
        };
    });
</script>
@endsection
