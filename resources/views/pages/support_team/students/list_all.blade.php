@extends('layouts.master')
@section('page_title', 'Liste complète des étudiants')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/inline_editing.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/student_statistics.css') }}">
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
</style>
@endsection

@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Liste complète des étudiants</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
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
                            <th>Père/Tuteur</th>
                            <th>Profession père</th>
                            <th>Mère/Tutrice</th>
                            <th>Profession mère</th>
                            <th>Téléphone</th>
                            <th>Action</th>
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
                    <!-- Cartes de statistiques -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card stats-card bg-light">
                                <div class="card-body text-center">
                                    <i class="icon-users2 icon-3x text-primary"></i>
                                    <h2 class="stats-value mt-2">{{ $total_students }}</h2>
                                    <p class="stats-label">Nombre total d'étudiants</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card bg-light">
                                <div class="card-body text-center">
                                    <i class="icon-graduation2 icon-3x text-success"></i>
                                    <h2 class="stats-value mt-2">{{ count($my_classes) }}</h2>
                                    <p class="stats-label">Nombre de classes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card bg-light">
                                <div class="card-body text-center">
                                    <i class="icon-user-check icon-3x text-info"></i>
                                    <h2 class="stats-value mt-2">{{ $students_by_status['ADRA'] + $students_by_status['TEAM3'] }}</h2>
                                    <p class="stats-label">Étudiants avec statut spécial</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques et tableaux -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white header-elements-inline">
                                    <h6 class="card-title">Répartition par statut</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="status-chart"></canvas>
                                    </div>
                                    <div class="row">
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
                                                    @foreach($students_by_status as $status => $count)
                                                    <tr>
                                                        <td>{{ $status }}</td>
                                                        <td>{{ $count }}</td>
                                                        <td>{{ number_format(($count / $total_students) * 100, 2) }}%</td>
                                                    </tr>
                                                    @endforeach
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
                                    <h6 class="card-title">Répartition par classe</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="class-chart"></canvas>
                                    </div>
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
                                                <td>{{ number_format(($class['count'] / $total_students) * 100, 2) }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detailed-stats">
                    <div id="statistics-detailed-container" class="statistics-container">
                        <!-- En-tête des statistiques -->
                        <div class="statistics-header">
                            <h3 class="statistics-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Statistiques détaillées des étudiants
                                <div class="title-underline"></div>
                            </h3>
                            <div class="statistics-controls">
                                <div class="control-group">
                                    <label for="class-selector" class="control-label">
                                        <i class="fas fa-school mr-1"></i>
                                        Classe
                                    </label>
                                    <select id="class-selector" class="form-control class-selector">
                                        <option value="all">🏫 Toutes les classes</option>
                                        @foreach($my_classes as $class)
                                            <option value="{{ $class->id }}">📚 {{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="action-buttons">
                                    <button id="refresh-stats" class="btn refresh-btn" title="Actualiser les statistiques">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button id="export-stats" class="btn export-btn">
                                        <i class="fas fa-file-excel mr-2"></i>
                                        Exporter Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Informations générales -->
                        <div class="statistics-info">
                            <div class="info-grid">
                                <div class="info-item" data-aos="fade-up" data-aos-delay="100">
                                    <div class="info-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="info-label">Total étudiants</div>
                                    <div class="info-value" id="total-students-count">
                                        <span class="counter" data-target="0">0</span>
                                    </div>
                                </div>
                                <div class="info-item" data-aos="fade-up" data-aos-delay="200">
                                    <div class="info-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="info-label">Classe sélectionnée</div>
                                    <div class="info-value" id="selected-class-name">-</div>
                                </div>
                                <div class="info-item" data-aos="fade-up" data-aos-delay="300">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="info-label">Dernière mise à jour</div>
                                    <div class="info-value" id="stats-generation-time">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Grille des cartes de statistiques -->
                        <div class="statistics-grid" id="statistics-cards-container">
                            <!-- Les cartes seront générées dynamiquement par JavaScript -->
                            <div class="loading-overlay">
                                <div class="loading-spinner"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($my_classes as $c)
                    <div class="tab-pane fade" id="c{{$c->id}}">
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
                                <th>Père/Tuteur</th>
                                <th>Profession père</th>
                                <th>Mère/Tutrice</th>
                                <th>Profession mère</th>
                                <th>Téléphone</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($all_students->where('my_class_id', $c->id) as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                    <td>{{ $s->user->name }}</td>
                                    <td>{{ $s->adm_no }}</td>
                                    <td>{{ $s->section->name }}</td>
                                    <td class="editable" data-field="dob" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->dob }}</td>
                                    <td class="age-display" data-student-id="{{ Qs::hash($s->id) }}" data-dob="{{ $s->user->dob }}">
                                        @if($s->user->dob)
                                            {{ \App\Helpers\Qs::calculateAge($s->user->dob) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="editable" data-field="address" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->address }}</td>
                                    <td class="editable" data-field="status" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->status ?? 'Normal' }}</td>
                                    <td class="editable" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_p }}</td>
                                    <td class="editable" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_p }}</td>
                                    <td class="editable" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_m }}</td>
                                    <td class="editable" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_m }}</td>
                                    <td class="editable" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->phone }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/inline_editing.js') }}"></script>
<script src="{{ asset('assets/js/student_statistics.js') }}"></script>
<script>
    $(document).ready(function() {
        // Configuration du système d'édition en ligne
        if (window.inlineEditor) {
            window.inlineEditor.options.csrfToken = '{{ csrf_token() }}';
            window.inlineEditor.options.saveUrl = '{{ route("ajax.update_student_field") }}';
        }

        // Initialiser les graphiques
        initializeCharts();
        
        // Calculer et afficher les statistiques pour les types d'étudiants et statuts académiques
        calculateStudentTypeStats();
        
        // Calculer l'âge pour tous les étudiants
        calculateAllAges();

        // Fonction pour initialiser les graphiques
        function initializeCharts() {
            // Graphique de répartition par statut
            const statusCtx = document.getElementById('status-chart').getContext('2d');
            const statusLabels = [];
            const statusData = [];
            const statusColors = ['#4CAF50', '#2196F3', '#FFC107'];

            @foreach($students_by_status as $status => $count)
                statusLabels.push('{{ $status }}');
                statusData.push({{ $count }});
            @endforeach

            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusColors,
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

            // Graphique de répartition par classe
            const classCtx = document.getElementById('class-chart').getContext('2d');
            const classLabels = [];
            const classData = [];
            const classColors = [];

            // Générer des couleurs aléatoires pour chaque classe
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

            new Chart(classCtx, {
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

        // Les fonctions d'édition en ligne sont maintenant gérées par inline_editing.js

        // Fonction pour calculer l'âge à partir de la date de naissance
        function calculateAge(dob) {
            if (!dob) return '';

            const birthDate = new Date(dob);
            if (isNaN(birthDate.getTime())) return '';

            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();

            if (today.getMonth() < birthDate.getMonth() ||
                (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age;
        }

        // Fonction pour calculer l'âge pour tous les étudiants
        function calculateAllAges() {
            $('.editable[data-field="dob"]').each(function() {
                const dob = $(this).text().trim();
                const studentId = $(this).data('student-id');
                const age = calculateAge(dob);

                if (age !== '') {
                    $('.age-display[data-student-id="' + studentId + '"]').text(age);
                }
            });
        }

        // L'édition en ligne est maintenant gérée par inline_editing.js
        
        // Fonction pour calculer et afficher les statistiques des types d'étudiants et statuts académiques
        function calculateStudentTypeStats() {
            let nouveauxCount = 0;
            let anciensCount = 0;
            let passantsCount = 0;
            let redoublantsCount = 0;
            const totalStudents = {{ $total_students }};
            
            // Parcourir tous les étudiants pour compter les types et statuts
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
            
            // Mettre à jour les tableaux
            $('#nouveaux-count').text(nouveauxCount);
            $('#anciens-count').text(anciensCount);
            $('#nouveaux-percent').text(totalStudents > 0 ? (nouveauxCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            $('#anciens-percent').text(totalStudents > 0 ? (anciensCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            
            $('#passants-count').text(passantsCount);
            $('#redoublants-count').text(redoublantsCount);
            $('#passants-percent').text(totalStudents > 0 ? (passantsCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            $('#redoublants-percent').text(totalStudents > 0 ? (redoublantsCount / totalStudents * 100).toFixed(2) + '%' : '0%');
            
            // Initialiser les graphiques si ce n'est pas déjà fait
            if (!window.studentTypeChart) {
                const studentTypeCtx = document.getElementById('student-type-chart').getContext('2d');
                window.studentTypeChart = new Chart(studentTypeCtx, {
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
            } else {
                // Mettre à jour les données du graphique existant
                window.studentTypeChart.data.datasets[0].data = [nouveauxCount, anciensCount];
                window.studentTypeChart.update();
            }
            
            if (!window.academicStatusChart) {
                const academicStatusCtx = document.getElementById('academic-status-chart').getContext('2d');
                window.academicStatusChart = new Chart(academicStatusCtx, {
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
            } else {
                // Mettre à jour les données du graphique existant
                window.academicStatusChart.data.datasets[0].data = [passantsCount, redoublantsCount];
                window.academicStatusChart.update();
            }
        }

    });
</script>
@endsection
