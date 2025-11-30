@extends('layouts.master')
@section('page_title', 'Liste complète des étudiants')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/inline_editing.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/student_statistics.css') }}">
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<style>
    .editable {
        cursor: pointer;
        position: relative;
    }
    .editable:hover {
        background-color: #f9f9f9;
    }
    .editable:hover::after {
        content: "\f044"; /* FontAwesome edit icon */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        color: #777;
        font-size: 12px;
    }
    .editing {
        padding: 0 !important;
    }
    .editing input, .editing select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #4caf50;
        border-radius: 4px;
    }
    .editing select {
        height: 38px;
    }
    .editing .datepicker {
        width: 100%;
    }
    .save-indicator {
        margin-left: 5px;
        display: none;
    }
    .save-success {
        color: green;
    }
    .save-error {
        color: red;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .stats-card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #333;
    }
    .stats-label {
        font-size: 1rem;
        color: #777;
    }
    /* Column visibility controls */
    .column-visibility-panel {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .column-visibility-panel h6 {
        margin-bottom: 1rem;
        color: #495057;
        font-weight: 600;
    }
    .column-visibility-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .column-visibility-controls .btn-group {
        margin-right: 1rem;
    }
    .column-visibility-controls .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    .column-visibility-controls .btn i {
        margin-right: 0.25rem;
    }
    .hidden-columns-indicator {
        background-color: #fff3cd;
        color: #856404;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .datatable-header.has-hidden-columns {
        border-left: 3px solid #ffc107;
    }
</style>
@endsection

@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Liste complète des étudiants</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <!-- Column Visibility Controls -->
            <div class="column-visibility-panel">
                <h6 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>Colonnes à afficher</span>
                    <small class="text-muted">Cliquez sur le bouton "Colonnes" ci-dessus ou utilisez les boutons ci-dessous</small>
                </h6>
                <div class="column-visibility-controls mt-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light" onclick="window.inlineEditor.showAllColumns()" title="Afficher toutes les colonnes">
                            <i class="icon-eye"></i> Tout afficher
                        </button>
                        <button type="button" class="btn btn-light" onclick="window.inlineEditor.hideAllColumns()" title="Masquer toutes les colonnes">
                            <i class="icon-eye-blocked"></i> Tout masquer
                        </button>
                        <button type="button" class="btn btn-light" onclick="window.inlineEditor.resetColumnVisibility()" title="Réinitialiser la visibilité des colonnes">
                            <i class="icon-reset"></i> Réinitialiser
                        </button>
                    </div>
                    <div class="hidden-columns-indicator d-none">
                        <i class="icon-info2 mr-1"></i>
                        <span id="hidden-columns-count">0</span> colonne(s) masquée(s)
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">Tous les étudiants</a></li>
                <li class="nav-item"><a href="#student-stats" class="nav-link" data-toggle="tab">Statistiques des étudiants</a></li>
                <li class="nav-item"><a href="#detailed-stats" class="nav-link" data-toggle="tab">Statistiques détaillées</a></li>
                <li class="nav-item"><a href="#student-types" class="nav-link" data-toggle="tab">Types d'étudiants</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Filtrer par classe</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_classes as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-students">
                    <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>N° d'admission</th>
                            <th>Classe/Section</th>
                            <th>Date de naissance</th>
                            <th>Âge</th>
                            <th>Adresse</th>
                            <th>Religion</th>
                            <th>Statut</th>
                            <th>Type</th>
                            <th>Statut académique</th>
                            <th>Sexe</th>
                            <th>Père/Tuteur</th>
                            <th>Profession père</th>
                            <th>Mère/Tutrice</th>
                            <th>Profession mère</th>
                            <th>Téléphone</th>
                            <th class="no-export">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($all_students as $s)
                            <tr>
                                <td class="number-column">{{ $loop->iteration }}</td>
                                <td class="photo-column"><img class="rounded-circle" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td class="editable editable-cell" data-field="name" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->name }}">{{ $s->user->name }}</td>
                                <td class="editable editable-cell" data-field="adm_no" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->adm_no }}">{{ $s->adm_no }}</td>
                                <td>{{ $s->my_class->name.' '.$s->section->name }}</td>
                                <td class="editable editable-cell" data-field="dob" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->dob }}">{{ $s->user->dob }}</td>
                                <td class="age-display age-column" data-student-id="{{ Qs::hash($s->id) }}" data-dob="{{ $s->user->dob }}">
                                    @if($s->user->dob)
                                        {{ \App\Helpers\Qs::calculateAge($s->user->dob) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="editable editable-cell" data-field="address" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->address }}">{{ $s->user->address }}</td>
                                <td class="editable editable-cell" data-field="religion" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->religion }}">{{ $s->user->religion }}</td>
                                <td class="editable editable-cell" data-field="status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->status ?? 'Normal' }}">{{ $s->user->status ?? 'Normal' }}</td>
                                <td class="editable editable-cell" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->student_type ?? 'Nouveau' }}">{{ $s->user->student_type ?? 'Nouveau' }}</td>
                                <td class="editable editable-cell" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->academic_status ?? 'Passant' }}">{{ $s->user->academic_status ?? 'Passant' }}</td>
                                <td class="editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->gender ?? '' }}">{{ $s->user->gender == 'Male' ? 'Masculin' : ($s->user->gender == 'Female' ? 'Féminin' : '-') }}</td>
                                <td class="editable editable-cell" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_p }}">{{ $s->user->nom_p }}</td>
                                <td class="editable editable-cell" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_p }}">{{ $s->user->prof_p }}</td>
                                <td class="editable editable-cell" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_m }}">{{ $s->user->nom_m }}</td>
                                <td class="editable editable-cell" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_m }}">{{ $s->user->prof_m }}</td>
                                <td class="editable editable-cell" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->phone }}">{{ $s->user->phone }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> Voir le profil</a>
                                                @if(Qs::userIsTeamSA())
                                                    <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Modifier</a>
                                                    <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Réinitialiser le mot de passe</a>
                                                @endif
                                                <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> Fiche de notes</a>

                                                {{--Suppression--}}
                                                @if(Qs::userIsSuperAdmin())
                                                    <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Supprimer</a>
                                                    <form method="post" id="item-delete-{{ Qs::hash($s->user->id) }}" action="{{ route('students.destroy', Qs::hash($s->user->id)) }}" class="hidden">@csrf @method('delete')</form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="student-types">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white header-elements-inline">
                                    <h6 class="card-title">Types d'étudiants</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="student-type-chart"></canvas>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Nombre d'étudiants</th>
                                                        <th>Pourcentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Nouveau</td>
                                                        <td id="nouveaux-count">-</td>
                                                        <td id="nouveaux-percent">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ancien</td>
                                                        <td id="anciens-count">-</td>
                                                        <td id="anciens-percent">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white header-elements-inline">
                                    <h6 class="card-title">Statuts académiques</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="academic-status-chart"></canvas>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Statut</th>
                                                        <th>Nombre d'étudiants</th>
                                                        <th>Pourcentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Passant</td>
                                                        <td id="passants-count">-</td>
                                                        <td id="passants-percent">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Redoublant</td>
                                                        <td id="redoublants-count">-</td>
                                                        <td id="redoublants-percent">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="student-stats">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card stats-card bg-primary text-white">
                                <div class="card-body text-center">
                                    <div class="stats-value">{{ $total_students }}</div>
                                    <div class="stats-label">Nombre total d'étudiants</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card bg-success text-white">
                                <div class="card-body text-center">
                                    <div class="stats-value">{{ count($my_classes) }}</div>
                                    <div class="stats-label">Nombre de classes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card bg-warning text-white">
                                <div class="card-body text-center">
                                    <div class="stats-value">{{ ($students_by_status['ADRA'] ?? 0) + ($students_by_status['TEAM3'] ?? 0) }}</div>
                                    <div class="stats-label">Étudiants avec statut spécial</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title">Répartition par statut</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Statut</th>
                                                <th>Nombre d'étudiants</th>
                                                <th>Pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students_by_status as $status => $count)
                                            <tr>
                                                <td>{{ $status }}</td>
                                                <td>{{ $count }}</td>
                                                <td>{{ $total_students > 0 ? number_format(($count / $total_students) * 100, 2) : '0.00' }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="chart-container">
                                        <canvas id="status-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="card-title">Répartition par classe</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Classe</th>
                                                <th>Nombre d'étudiants</th>
                                                <th>Pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students_by_class as $class)
                                            <tr>
                                                <td>{{ $class['name'] }}</td>
                                                <td>{{ $class['count'] }}</td>
                                                <td>{{ $total_students > 0 ? number_format(($class['count'] / $total_students) * 100, 2) : '0.00' }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="chart-container">
                                        <canvas id="class-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detailed-stats">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title">Statistiques détaillées des étudiants avec filtres</h6>
                        </div>
                        <div class="card-body">
                            <!-- Filtres (Ligne 1) -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label><i class="icon-graduation2"></i> Classe</label>
                                    <select id="class-filter" class="form-control">
                                        <option value="">📚 Toutes les classes</option>
                                        @foreach($my_classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label><i class="icon-calendar"></i> Âge minimum</label>
                                    <select id="age-min-filter" class="form-control">
                                        <option value="">Aucun</option>
                                        @for($age = 3; $age <= 25; $age++)
                                            <option value="{{ $age }}">{{ $age }} ans</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label><i class="icon-calendar"></i> Âge maximum</label>
                                    <select id="age-max-filter" class="form-control">
                                        <option value="">Aucun</option>
                                        @for($age = 3; $age <= 25; $age++)
                                            <option value="{{ $age }}">{{ $age }} ans</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label><i class="icon-users"></i> Sexe</label>
                                    <select id="gender-filter" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="Male">Masculin</option>
                                        <option value="Female">Féminin</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label><i class="icon-certificate"></i> Statut</label>
                                    <select id="status-filter" class="form-control">
                                        <option value="">Tous les statuts</option>
                                        <option value="Normal">Normal</option>
                                        <option value="ADRA">ADRA</option>
                                        <option value="TEAM3">TEAM3</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Filtres (Ligne 2) -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label><i class="icon-book"></i> Type d'étudiant</label>
                                    <select id="student-type-filter" class="form-control">
                                        <option value="">Tous les types</option>
                                        <option value="Nouveau">Nouveau</option>
                                        <option value="Ancien">Ancien</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label><i class="icon-trophy"></i> Statut académique</label>
                                    <select id="academic-status-filter" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="Passant">Passant</option>
                                        <option value="Redoublant">Redoublant</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>&nbsp;</label>
                                    <div class="btn-group d-block">
                                        <button class="btn btn-primary btn-lg" onclick="applyFilters()">
                                            <i class="icon-filter3"></i> Appliquer les filtres
                                        </button>
                                        <button class="btn btn-secondary btn-lg" onclick="resetFilters()">
                                            <i class="icon-reset"></i> Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques résumées -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center p-3">
                                            <h3 id="stat-total" class="mb-0">0</h3>
                                            <small>Total étudiants filtrés</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center p-3">
                                            <h3 id="stat-age-moyen" class="mb-0">-</h3>
                                            <small>Âge moyen</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center p-3">
                                            <h3 id="stat-garcons" class="mb-0">0</h3>
                                            <small>Garçons</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center p-3">
                                            <h3 id="stat-filles" class="mb-0">0</h3>
                                            <small>Filles</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Répartition par âge -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="card-title mb-0"><i class="icon-stats-bars"></i> Répartition par âge (étudiants filtrés)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container" style="height: 300px;">
                                                <canvas id="age-distribution-chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau de ventilation détaillée par âge et sexe -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="card-title mb-0"><i class="icon-users4"></i> Répartition détaillée Filles/Garçons par âge</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
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
                                                            <td colspan="6" class="text-center text-muted">
                                                                <i class="icon-info3"></i> Appliquez les filtres pour voir les statistiques détaillées
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau détaillé des étudiants filtrés -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0"><i class="icon-table2"></i> Liste détaillée des étudiants</h6>
                                            <button class="btn btn-success btn-sm" onclick="exportFilteredStudents()">
                                                <i class="icon-file-excel"></i> Exporter Excel
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="filtered-students-table" class="table table-bordered table-striped table-hover">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>N°</th>
                                                            <th>Nom</th>
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
                                                            <td colspan="9" class="text-center text-muted">
                                                                <i class="icon-info3"></i> Appliquez les filtres pour voir les résultats
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($my_classes as $c)
                <div class="tab-pane fade" id="c{{ $c->id }}">
                    <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>N° d'admission</th>
                            <th>Section</th>
                            <th>Date de naissance</th>
                            <th>Âge</th>
                            <th>Adresse</th>
                            <th>Statut</th>
                            <th>Sexe</th>
                            <th>Père/Tuteur</th>
                            <th>Profession père</th>
                            <th>Mère/Tutrice</th>
                            <th>Profession mère</th>
                            <th>Téléphone</th>
                            <th class="no-export">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($all_students->where('my_class_id', $c->id) as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $s->section->name }}</td>
                                <td>{{ $s->user->dob }}</td>
                                <td>
                                    @if($s->user->dob)
                                        {{ \App\Helpers\Qs::calculateAge($s->user->dob) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $s->user->address }}</td>
                                <td>{{ $s->user->status ?? 'Normal' }}</td>
                                <td>{{ $s->user->gender == 'Male' ? 'Masculin' : ($s->user->gender == 'Female' ? 'Féminin' : '-') }}</td>
                                <td>{{ $s->user->nom_p }}</td>
                                <td>{{ $s->user->prof_p }}</td>
                                <td>{{ $s->user->nom_m }}</td>
                                <td>{{ $s->user->prof_m }}</td>
                                <td>{{ $s->user->phone }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> Voir les informations</a>
                                                @if(Qs::userIsTeamSA())
                                                    <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Modifier</a>
                                                    <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Réinitialiser le mot de passe</a>
                                                @endif
                                                <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> Fiche de notes</a>

                                                {{--Suppression--}}
                                                @if(Qs::userIsSuperAdmin())
                                                    <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Supprimer</a>
                                                    <form method="post" id="item-delete-{{ Qs::hash($s->user->id) }}" action="{{ route('students.destroy', Qs::hash($s->user->id)) }}" class="hidden">@csrf @method('delete')</form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
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

    {{--Fin de la liste des étudiants--}}

@endsection

@section('scripts')
<script src="{{ asset('assets/js/inline_editing.js') }}"></script>
<script>
    $(document).ready(function() {
        // Configuration de l'édition en ligne
        if (window.inlineEditor) {
            window.inlineEditor.options.csrfToken = '{{ csrf_token() }}';
            window.inlineEditor.options.saveUrl = '{{ route("ajax.update_student_field") }}';
        }

        // Initialiser les graphiques si Chart.js est disponible
        if (typeof Chart !== 'undefined') {
            // Graphique de répartition par statut
            const statusCtx = document.getElementById('status-chart');
            if (statusCtx) {
                new Chart(statusCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode(array_keys($students_by_status)) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($students_by_status)) !!},
                            backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            title: {
                                display: true,
                                text: 'Répartition des étudiants par statut'
                            }
                        }
                    }
                });
            }

            // Graphique de répartition par classe
            const classCtx = document.getElementById('class-chart');
            if (classCtx) {
                const classLabels = [];
                const classData = [];
                const classColors = [];

                function getRandomColor() {
                    const letters = '0123456789ABCDEF';
                    let color = '#';
                    for (let i = 0; i < 6; i++) {
                        color += letters[Math.floor(Math.random() * 16)];
                    }
                    return color;
                }

                @foreach($students_by_class as $class)
                    classLabels.push('{{ $class['name'] }}');
                    classData.push({{ $class['count'] }});
                    classColors.push(getRandomColor());
                @endforeach

                new Chart(classCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: classLabels,
                        datasets: [{
                            label: 'Nombre d\'étudiants',
                            data: classData,
                            backgroundColor: classColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Nombre d\'étudiants par classe'
                            }
                        }
                    }
                });
            }
        }

        // Calculer les statistiques des types d'étudiants
        calculateStudentTypeStats();

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
            $('#nouveaux-percent').text(totalStudents > 0 ? (nouveauxCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            $('#anciens-percent').text(totalStudents > 0 ? (anciensCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            
            $('#passants-count').text(passantsCount);
            $('#redoublants-count').text(redoublantsCount);
            $('#passants-percent').text(totalStudents > 0 ? (passantsCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            $('#redoublants-percent').text(totalStudents > 0 ? (redoublantsCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            
            if (typeof Chart !== 'undefined') {
                const studentTypeCtx = document.getElementById('student-type-chart');
                if (studentTypeCtx && !window.studentTypeChart) {
                    window.studentTypeChart = new Chart(studentTypeCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: ['Nouveau', 'Ancien'],
                            datasets: [{
                                data: [nouveauxCount, anciensCount],
                                backgroundColor: ['#4CAF50', '#2196F3'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                } else if (window.studentTypeChart) {
                    window.studentTypeChart.data.datasets[0].data = [nouveauxCount, anciensCount];
                    window.studentTypeChart.update();
                }
                
                const academicStatusCtx = document.getElementById('academic-status-chart');
                if (academicStatusCtx && !window.academicStatusChart) {
                    window.academicStatusChart = new Chart(academicStatusCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: ['Passant', 'Redoublant'],
                            datasets: [{
                                data: [passantsCount, redoublantsCount],
                                backgroundColor: ['#4CAF50', '#FFC107'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                } else if (window.academicStatusChart) {
                    window.academicStatusChart.data.datasets[0].data = [passantsCount, redoublantsCount];
                    window.academicStatusChart.update();
                }
            }
        }

        // ==========================================
        // FILTRES DÉTAILLÉS PAR ÂGE
        // ==========================================
        
        // Données des étudiants pour le filtrage (Global)
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
            const status = $('#status-filter').val();
            const studentType = $('#student-type-filter').val();
            const academicStatus = $('#academic-status-filter').val();

            console.log("Applying filters...", {
                classId, ageMin, ageMax, gender, status, studentType, academicStatus
            });

            // Filtrer les étudiants
            let filteredStudents = window.allStudentsData.filter(student => {
                if (student.age === 0) return false; // Ignorer ceux sans date de naissance
                
                if (classId && student.class_id != classId) return false;
                if (ageMin && student.age < parseInt(ageMin)) return false;
                if (ageMax && student.age > parseInt(ageMax)) return false;
                if (gender && student.gender !== gender) return false;
                if (status && student.status !== status) return false;
                if (studentType && student.student_type !== studentType) return false;
                if (academicStatus && student.academic_status !== academicStatus) return false;
                
                return true;
            });

            console.log("Filtered students:", filteredStudents.length);

            // Mettre à jour les statistiques
            updateStats(filteredStudents);

            // Mettre à jour le tableau
            updateTable(filteredStudents);

            // Mettre à jour le graphique de répartition par âge
            updateAgeDistributionChart(filteredStudents);
            
            // Mettre à jour le tableau de ventilation détaillée
            updateGenderAgeBreakdown(filteredStudents);
        }

        function updateStats(students) {
            const total = students.length;
            const boys = students.filter(s => s.gender === 'Male').length;
            const girls = students.filter(s => s.gender === 'Female').length;
            
            let ageSum = 0;
            students.forEach(s => {
                ageSum += s.age;
            });
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
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="icon-info3"></i> Aucun étudiant ne correspond aux critères sélectionnés
                        </td>
                    </tr>
                `);
                return;
            }

            students.forEach((student, index) => {
                const genderIcon = student.gender === 'Male' ? 
                    '<i class="icon-user-tie text-primary"></i> M' : 
                    '<i class="icon-woman text-danger"></i> F';
                
                const statusBadge = student.status === 'Normal' ? 
                    '<span class="badge badge-success">Normal</span>' :
                    student.status === 'ADRA' ?
                    '<span class="badge badge-warning">ADRA</span>' :
                    '<span class="badge badge-danger">TEAM3</span>';

                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${student.name}</strong></td>
                        <td>${student.class_name}</td>
                        <td>${student.section_name}</td>
                        <td><span class="badge badge-info">${student.age} ans</span></td>
                        <td>${genderIcon}</td>
                        <td>${statusBadge}</td>
                        <td>${student.student_type}</td>
                        <td>${student.phone}</td>
                    </tr>
                `);
            });
        }

        function updateAgeDistributionChart(students) {
            // Compter les étudiants par âge et par sexe
            const ageGenderCount = {};
            students.forEach(s => {
                if (s.age > 0) {
                    if (!ageGenderCount[s.age]) {
                        ageGenderCount[s.age] = { boys: 0, girls: 0 };
                    }
                    if (s.gender === 'Male') {
                        ageGenderCount[s.age].boys++;
                    } else if (s.gender === 'Female') {
                        ageGenderCount[s.age].girls++;
                    }
                }
            });

            // Trier les âges
            const ages = Object.keys(ageGenderCount).map(Number).sort((a, b) => a - b);
            const boysData = ages.map(age => ageGenderCount[age].boys);
            const girlsData = ages.map(age => ageGenderCount[age].girls);

            // Créer/mettre à jour le graphique
            const ctx = document.getElementById('age-distribution-chart');
            if (ctx) {
                if (window.ageDistributionChart) {
                    window.ageDistributionChart.destroy();
                }

                window.ageDistributionChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ages.map(age => age + ' ans'),
                        datasets: [
                            {
                                label: 'Garçons',
                                data: boysData,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2
                            },
                            {
                                label: 'Filles',
                                data: girlsData,
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Distribution des étudiants par âge et sexe'
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        }

        function updateGenderAgeBreakdown(students) {
            // Compter les étudiants par âge et par sexe
            const ageGenderCount = {};
            let totalBoys = 0, totalGirls = 0;

            students.forEach(s => {
                if (s.age > 0) {
                    if (!ageGenderCount[s.age]) {
                        ageGenderCount[s.age] = { boys: 0, girls: 0 };
                    }
                    if (s.gender === 'Male') {
                        ageGenderCount[s.age].boys++;
                        totalBoys++;
                    } else if (s.gender === 'Female') {
                        ageGenderCount[s.age].girls++;
                        totalGirls++;
                    }
                }
            });

            // Trier les âges
            const ages = Object.keys(ageGenderCount).map(Number).sort((a, b) => a - b);

            // Remplir le tableau
            const tbody = $('#gender-age-breakdown-body');
            tbody.empty();

            if (ages.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="icon-info3"></i> Aucune donnée disponible avec ces filtres
                        </td>
                    </tr>
                `);
            } else {
                ages.forEach(age => {
                    const boys = ageGenderCount[age].boys;
                    const girls = ageGenderCount[age].girls;
                    const total = boys + girls;
                    const boysPercent = total > 0 ? ((boys / total) * 100).toFixed(1) : 0;
                    const girlsPercent = total > 0 ? ((girls / total) * 100).toFixed(1) : 0;

                    tbody.append(`
                        <tr>
                            <td class="text-center"><strong>${age} ans</strong></td>
                            <td class="text-center text-primary"><strong>${boys}</strong></td>
                            <td class="text-center text-danger"><strong>${girls}</strong></td>
                            <td class="text-center bg-light"><strong>${total}</strong></td>
                            <td class="text-center">${boysPercent}%</td>
                            <td class="text-center">${girlsPercent}%</td>
                        </tr>
                    `);
                });
            }

            // Mettre à jour le pied de tableau
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
            $('#class-filter').val('');
            $('#age-min-filter').val('');
            $('#age-max-filter').val('');
            $('#gender-filter').val('');
            $('#status-filter').val('');
            $('#student-type-filter').val('');
            $('#academic-status-filter').val('');
            
            $('#stat-total').text('0');
            $('#stat-garcons').text('0');
            $('#stat-filles').text('0');
            $('#stat-age-moyen').text('-');
            
            $('#filtered-students-body').html(`
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        <i class="icon-info3"></i> Appliquez les filtres pour voir les résultats
                    </td>
                </tr>
            `);
            
            $('#gender-age-breakdown-body').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="icon-info3"></i> Appliquez les filtres pour voir les statistiques détaillées
                    </td>
                </tr>
            `);
            
            $('#total-boys').text('0');
            $('#total-girls').text('0');
            $('#total-all').text('0');
            $('#total-boys-percent').text('-');
            $('#total-girls-percent').text('-');

            if (window.ageDistributionChart) {
                window.ageDistributionChart.destroy();
                window.ageDistributionChart = null;
            }
        }

        window.exportFilteredStudents = function() {
            const classId = $('#class-filter').val();
            const ageMin = $('#age-min-filter').val();
            const ageMax = $('#age-max-filter').val();
            const gender = $('#gender-filter').val();
            const status = $('#status-filter').val();
            const studentType = $('#student-type-filter').val();
            const academicStatus = $('#academic-status-filter').val();

            // Filtrer les étudiants
            let filteredStudents = window.allStudentsData.filter(student => {
                if (student.age === 0) return false;
                
                if (classId && student.class_id != classId) return false;
                if (ageMin && student.age < parseInt(ageMin)) return false;
                if (ageMax && student.age > parseInt(ageMax)) return false;
                if (gender && student.gender !== gender) return false;
                if (status && student.status !== status) return false;
                if (studentType && student.student_type !== studentType) return false;
                if (academicStatus && student.academic_status !== academicStatus) return false;
                
                return true;
            });

            if (filteredStudents.length === 0) {
                alert('Aucun étudiant à exporter avec ces filtres');
                return;
            }

            // Créer les données pour l'export
            const exportData = filteredStudents.map((student, index) => {
                return {
                    'N°': index + 1,
                    'Nom': student.name,
                    'Classe': student.class_name,
                    'Section': student.section_name,
                    'Âge': student.age + ' ans',
                    'Sexe': student.gender === 'Male' ? 'Masculin' : 'Féminin',
                    'Statut': student.status,
                    'Type': student.student_type,
                    'Téléphone': student.phone
                };
            });

            // Créer un fichier Excel
            const worksheet = XLSX.utils.json_to_sheet(exportData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Étudiants filtrés');

            // Télécharger le fichier
            const fileName = `Etudiants_Filtres_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(workbook, fileName);
        }
    });

</script>
@endsection
