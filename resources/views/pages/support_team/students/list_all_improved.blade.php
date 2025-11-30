@extends('layouts.master')
@section('page_title', 'Liste complète des étudiants')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/inline_editing.css') }}">
<style>
    /* Modern Dashboard Styling */
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .dashboard-header h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .dashboard-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        border-left: 4px solid;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, transparent 50%, rgba(0, 0, 0, 0.02) 50%);
        border-radius: 0 15px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .stat-card.total { border-left-color: #667eea; }
    .stat-card.boys { border-left-color: #36a2eb; }
    .stat-card.girls { border-left-color: #ff6384; }
    .stat-card.normal { border-left-color: #4caf50; }
    .stat-card.adra { border-left-color: #ff9800; }
    .stat-card.team3 { border-left-color: #f44336; }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .stat-change {
        font-size: 0.85rem;
        margin-top: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
    }

    .stat-change.positive {
        background: #d4edda;
        color: #155724;
    }

    .stat-change.negative {
        background: #f8d7da;
        color: #721c24;
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        height: 100%;
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .chart-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    /* Data Table Enhancements */
    .table-wrapper {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .table-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Column Visibility Panel */
    .column-visibility-panel {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    }

    .column-visibility-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .btn-modern {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: all 0.3s;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Tab Navigation */
    .nav-tabs-highlight {
        border-bottom: 3px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .nav-tabs-highlight .nav-link {
        border: none;
        color: #718096;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: all 0.3s;
        border-radius: 8px 8px 0 0;
    }

    .nav-tabs-highlight .nav-link:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }

    .nav-tabs-highlight .nav-link.active {
        color: #667eea;
        background: white;
        border-bottom: 3px solid #667eea;
        margin-bottom: -3px;
    }

    /* Responsive Grid */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .dashboard-header h2 {
            font-size: 1.5rem;
        }
    }

    /* Quick Stats Table */
    .quick-stats-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    }

    .quick-stats-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        padding: 1rem;
        text-align: left;
    }

    .quick-stats-table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .quick-stats-table tr:last-child td {
        border-bottom: none;
    }

    .quick-stats-table tr:hover {
        background: rgba(102, 126, 234, 0.03);
    }

    /* Badge Styles */
    .badge-modern {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Loading States */
    .stat-loading {
        height: 2.5rem;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endsection

@section('content')
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="icon-users4 mr-3"></i>Gestion des Étudiants</h2>
                <p class="mb-0">Vue d'ensemble et statistiques détaillées de tous les étudiants</p>
            </div>
            <div>
                <button class="btn btn-light btn-modern" onclick="window.location.reload()">
                    <i class="icon-spinner11 mr-2"></i>Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="stats-grid">
        <!-- Total Students -->
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="icon-users4" style="color: #667eea;"></i>
            </div>
            <div class="stat-value" id="total-students">{{ $total_students ?? 0 }}</div>
            <div class="stat-label">Total Étudiants</div>
            <div class="stat-change positive">
                <i class="icon-arrow-up8 mr-1"></i>Actifs
            </div>
        </div>

        <!-- Boys -->
        <div class="stat-card boys">
            <div class="stat-icon">
                <i class="icon-user-tie" style="color: #36a2eb;"></i>
            </div>
            <div class="stat-value" id="boys-count">
                {{ $all_students->where('user.gender', 'Male')->count() }}
            </div>
            <div class="stat-label">Garçons</div>
            <div class="stat-change">
                <span id="boys-percent">{{ $total_students > 0 ? number_format(($all_students->where('user.gender', 'Male')->count() / $total_students) * 100, 1) : 0 }}%</span>
            </div>
        </div>

        <!-- Girls -->
        <div class="stat-card girls">
            <div class="stat-icon">
                <i class="icon-woman" style="color: #ff6384;"></i>
            </div>
            <div class="stat-value" id="girls-count">
                {{ $all_students->where('user.gender', 'Female')->count() }}
            </div>
            <div class="stat-label">Filles</div>
            <div class="stat-change">
                <span id="girls-percent">{{ $total_students > 0 ? number_format(($all_students->where('user.gender', 'Female')->count() / $total_students) * 100, 1) : 0 }}%</span>
            </div>
        </div>

        <!-- Normal Status -->
        <div class="stat-card normal">
            <div class="stat-icon">
                <i class="icon-check" style="color: #4caf50;"></i>
            </div>
            <div class="stat-value" id="normal-count">
                {{ $students_by_status['Normal'] ?? 0 }}
            </div>
            <div class="stat-label">Normal</div>
        </div>

        <!-- ADRA -->
        <div class="stat-card adra">
            <div class="stat-icon">
                <i class="icon-aid-kit" style="color: #ff9800;"></i>
            </div>
            <div class="stat-value" id="adra-count">
                {{ $students_by_status['ADRA'] ?? 0 }}
            </div>
            <div class="stat-label">ADRA</div>
        </div>

        <!-- TEAM3 -->
        <div class="stat-card team3">
            <div class="stat-icon">
                <i class="icon-collaboration" style="color: #f44336;"></i>
            </div>
            <div class="stat-value" id="team3-count">
                {{ $students_by_status['TEAM3'] ?? 0 }}
            </div>
            <div class="stat-label">TEAM3</div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="nav-item">
            <a href="#all-students" class="nav-link active" data-toggle="tab">
                <i class="icon-list mr-2"></i>Liste des étudiants
            </a>
        </li>
        <li class="nav-item">
            <a href="#statistics" class="nav-link" data-toggle="tab">
                <i class="icon-stats-bars mr-2"></i>Statistiques détaillées
            </a>
        </li>
        <li class="nav-item">
            <a href="#charts" class="nav-link" data-toggle="tab">
                <i class="icon-chart mr-2"></i>Graphiques
            </a>
        </li>
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <i class="icon-filter3 mr-2"></i>Filtrer par classe
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach($my_classes as $c)
                    <a href="#class-{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                @endforeach
            </div>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- All Students Tab -->
        <div class="tab-pane fade show active" id="all-students">
            <div class="table-wrapper">
                <!-- Column Visibility Panel -->
                <div class="column-visibility-panel">
                    <h6 class="mb-3" style="color: #2d3748; font-weight: 700;">
                        <i class="icon-eye mr-2"></i>Gestion de l'affichage des colonnes
                    </h6>
                    <div class="column-visibility-controls">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-light btn-modern" onclick="window.inlineEditor.showAllColumns()">
                                <i class="icon-eye mr-2"></i>Tout afficher
                            </button>
                            <button type="button" class="btn btn-light btn-modern" onclick="window.inlineEditor.hideAllColumns()">
                                <i class="icon-eye-blocked mr-2"></i>Tout masquer
                            </button>
                            <button type="button" class="btn btn-light btn-modern" onclick="window.inlineEditor.resetColumnVisibility()">
                                <i class="icon-reset mr-2"></i>Réinitialiser
                            </button>
                        </div>
                        <div class="hidden-columns-indicator d-none badge badge-warning">
                            <i class="icon-info2 mr-1"></i>
                            <span id="hidden-columns-count">0</span> colonne(s) masquée(s)
                        </div>
                    </div>
                </div>

                <!-- Table Header -->
                <div class="table-header">
                    <h3 class="table-title">
                        <i class="icon-table mr-2"></i>Liste complète des étudiants
                    </h3>
                    <div class="table-actions">
                        <span class="badge badge-primary badge-modern">
                            <i class="icon-users mr-1"></i>{{ $total_students }} étudiants
                        </span>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover datatable-button-html5-columns">
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
                                <td class="photo-column"><img class="rounded-circle" src="{{ $s->user->photo }}" alt="photo" style="width: 40px; height: 40px;"></td>
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
                                <td>
                                    <span class="badge badge-{{ ($s->user->status ?? 'Normal') == 'Normal' ? 'success' : (($s->user->status ?? 'Normal') == 'ADRA' ? 'warning' : 'danger') }}">
                                        {{ $s->user->status ?? 'Normal' }}
                                    </span>
                                </td>
                                <td class="editable editable-cell" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->student_type ?? 'Nouveau' }}">{{ $s->user->student_type ?? 'Nouveau' }}</td>
                                <td class="editable editable-cell" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->academic_status ?? 'Passant' }}">{{ $s->user->academic_status ?? 'Passant' }}</td>
                                <td>
                                    <i class="icon-{{ $s->user->gender == 'Male' ? 'user-tie' : 'woman' }} mr-1"></i>
                                    {{ $s->user->gender == 'Male' ? 'Masculin' : ($s->user->gender == 'Female' ? 'Féminin' : '-') }}
                                </td>
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
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> Voir le profil</a>
                                                @if(Qs::userIsTeamSA())
                                                    <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Modifier</a>
                                                    <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Réinitialiser mot de passe</a>
                                                @endif
                                                <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> Fiche de notes</a>
                                                @if(Qs::userIsSuperAdmin())
                                                    <div class="dropdown-divider"></div>
                                                    <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item text-danger"><i class="icon-trash"></i> Supprimer</a>
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
        </div>

        <!-- Statistics Tab -->
        <div class="tab-pane fade" id="statistics">
            <div class="row">
                <!-- Statistics by Class -->
                <div class="col-md-8">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h4 class="chart-card-title"><i class="icon-graduation2 mr-2"></i>Répartition par classe</h4>
                        </div>
                        <div class="quick-stats-table">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Classe</th>
                                        <th class="text-center">Garçons</th>
                                        <th class="text-center">Filles</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($my_classes as $class)
                                        @php
                                            $classStudents = $all_students->where('my_class_id', $class->id);
                                            $boys = $classStudents->where('user.gender', 'Male')->count();
                                            $girls = $classStudents->where('user.gender', 'Female')->count();
                                            $total = $classStudents->count();
                                            $percentage = $total_students > 0 ? ($total / $total_students) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $class->name }}</strong></td>
                                            <td class="text-center"><span class="badge badge-info">{{ $boys }}</span></td>
                                            <td class="text-center"><span class="badge badge-danger">{{ $girls }}</span></td>
                                            <td class="text-center"><strong>{{ $total }}</strong></td>
                                            <td class="text-center">{{ number_format($percentage, 1) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Summary -->
                <div class="col-md-4">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h4 class="chart-card-title"><i class="icon-stats-dots mr-2"></i>Résumé</h4>
                        </div>
                        <div class="quick-stats-table">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td><strong>Total Classes</strong></td>
                                        <td class="text-right"><span class="badge badge-primary badge-modern">{{ $my_classes->count() }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ratio G/F</strong></td>
                                        <td class="text-right">
                                            @php
                                                $boysCount = $all_students->where('user.gender', 'Male')->count();
                                                $girlsCount = $all_students->where('user.gender', 'Female')->count();
                                                $ratio = $girlsCount > 0 ? number_format($boysCount / $girlsCount, 2) : 'N/A';
                                            @endphp
                                            {{ $ratio }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Âge moyen</strong></td>
                                        <td class="text-right">
                                            @php
                                                $totalAge = 0;
                                                $studentWithAge = 0;
                                                foreach($all_students as $student) {
                                                    if($student->user->dob) {
                                                        $totalAge += \App\Helpers\Qs::calculateAge($student->user->dob);
                                                        $studentWithAge++;
                                                    }
                                                }
                                                $avgAge = $studentWithAge > 0 ? number_format($totalAge / $studentWithAge, 1) : 0;
                                            @endphp
                                            {{ $avgAge }} ans
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Tab -->
        <div class="tab-pane fade" id="charts">
            <div class="row">
                <!-- Gender Distribution Chart -->
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h4 class="chart-card-title"><i class="icon-pie-chart5 mr-2"></i>Répartition par genre</h4>
                        </div>
                        <div class="chart-container">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h4 class="chart-card-title"><i class="icon-pie-chart5 mr-2"></i>Répartition par statut</h4>
                        </div>
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Class Distribution Chart -->
                <div class="col-md-12 mt-3">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h4 class="chart-card-title"><i class="icon-bar-chart mr-2"></i>Répartition par classe</h4>
                        </div>
                        <div class="chart-container" style="height: 400px;">
                            <canvas id="classChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Specific Tabs -->
        @foreach($my_classes as $mc)
            <div class="tab-pane fade" id="class-{{ $mc->id }}">
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="icon-graduation2 mr-2"></i>{{ $mc->name }}
                        </h3>
                        <div class="table-actions">
                            @php
                                $classStudents = $all_students->where('my_class_id', $mc->id);
                            @endphp
                            <span class="badge badge-primary badge-modern">
                                <i class="icon-users mr-1"></i>{{ $classStudents->count() }} étudiants
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>N°</th>
                                <th>Photo</th>
                                <th>Nom</th>
                                <th>N° d'admission</th>
                                <th>Section</th>
                                <th>Âge</th>
                                <th>Sexe</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($classStudents as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" src="{{ $s->user->photo }}" alt="photo" style="width: 40px; height: 40px;"></td>
                                    <td>{{ $s->user->name }}</td>
                                    <td>{{ $s->adm_no }}</td>
                                    <td>{{ $s->section->name }}</td>
                                    <td>
                                        @if($s->user->dob)
                                            {{ \App\Helpers\Qs::calculateAge($s->user->dob) }} ans
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <i class="icon-{{ $s->user->gender == 'Male' ? 'user-tie' : 'woman' }} mr-1"></i>
                                        {{ $s->user->gender == 'Male' ? 'M' : ($s->user->gender == 'Female' ? 'F' : '-') }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ ($s->user->status ?? 'Normal') == 'Normal' ? 'success' : (($s->user->status ?? 'Normal') == 'ADRA' ? 'warning' : 'danger') }}">
                                            {{ $s->user->status ?? 'Normal' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="btn btn-sm btn-primary">
                                            <i class="icon-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/inline_editing.js') }}"></script>
<script>
    $(document).ready(function() {
        // Configuration de l'éditeur en ligne
        if (window.inlineEditor) {
            window.inlineEditor.options.csrfToken = '{{ csrf_token() }}';
            window.inlineEditor.options.saveUrl = '{{ route("ajax.update_student_field") }}';
        }

        // Données pour les graphiques
        const boysCount = {{ $all_students->where('user.gender', 'Male')->count() }};
        const girlsCount = {{ $all_students->where('user.gender', 'Female')->count() }};
        const normalCount = {{ $students_by_status['Normal'] ?? 0 }};
        const adraCount = {{ $students_by_status['ADRA'] ?? 0 }};
        const team3Count = {{ $students_by_status['TEAM3'] ?? 0 }};

        // Graphique de genre
        if (document.getElementById('genderChart')) {
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Garçons', 'Filles'],
                    datasets: [{
                        data: [boysCount, girlsCount],
                        backgroundColor: ['#36a2eb', '#ff6384'],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 14, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }

        // Graphique de statut
        if (document.getElementById('statusChart')) {
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Normal', 'ADRA', 'TEAM3'],
                    datasets: [{
                        data: [normalCount, adraCount, team3Count],
                        backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 14, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }

        // Graphique par classe
        if (document.getElementById('classChart')) {
            const classNames = [];
            const classCounts = [];
            
            @foreach($my_classes as $class)
                classNames.push('{{ $class->name }}');
                classCounts.push({{ $all_students->where('my_class_id', $class->id)->count() }});
            @endforeach

            const classCtx = document.getElementById('classChart').getContext('2d');
            new Chart(classCtx, {
                type: 'bar',
                data: {
                    labels: classNames,
                    datasets: [{
                        label: 'Nombre d\'étudiants',
                        data: classCounts,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 12, weight: 'bold' }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12, weight: 'bold' }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Animation des chiffres
        function animateValue(id, start, end, duration) {
            const obj = document.getElementById(id);
            if (!obj) return;
            
            const range = end - start;
            const increment = end > start ? 1 : -1;
            const stepTime = Math.abs(Math.floor(duration / range));
            let current = start;
            
            const timer = setInterval(function() {
                current += increment;
                obj.textContent = current;
                if (current == end) {
                    clearInterval(timer);
                }
            }, stepTime);
        }

        // Animer les statistiques au chargement
        setTimeout(() => {
            animateValue('total-students', 0, {{ $total_students }}, 1000);
            animateValue('boys-count', 0, boysCount, 1000);
            animateValue('girls-count', 0, girlsCount, 1000);
            animateValue('normal-count', 0, normalCount, 1000);
            animateValue('adra-count', 0, adraCount, 1000);
            animateValue('team3-count', 0, team3Count, 1000);
        }, 300);
    });
</script>
@endsection
