@extends('layouts.master')
@section('page_title', 'Liste des étudiants')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/datatable_responsive.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/inline_editing.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/student_list_modern.css') }}">
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endsection

@section('content')

    {{-- Header du Dashboard Moderne --}}
    <div class="students-dashboard-header d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h2><i class="icon-users mr-2"></i>Gestion des Étudiants</h2>
            <p>Répertoire général, filtres de visibilité des colonnes et export Excel direct</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap" style="gap: 10px;">
            <a href="{{ route('students.list_all') }}" class="btn btn-light font-weight-semibold shadow-sm">
                <i class="icon-stats-growth mr-1 text-primary"></i> Vue détaillée & Statistiques
            </a>
            @if(Qs::userIsTeamSA())
                <a href="{{ route('students.create') }}" class="btn btn-outline-light font-weight-semibold">
                    <i class="icon-plus2 mr-1"></i> Inscription
                </a>
            @endif
        </div>
    </div>

    {{-- Cartes Statistiques KPI --}}
    @php
        $allSt = $students;
        $totalSt = $allSt->count();
        $totalBoys = $allSt->where('user.gender', 'Male')->count();
        $totalGirls = $allSt->where('user.gender', 'Female')->count();
        $totalNormal = $allSt->where('user.status', 'Normal')->count() + $allSt->where('user.status', null)->count();
        $totalAdra = $allSt->where('user.status', 'ADRA')->count();
        $totalTeam3 = $allSt->where('user.status', 'TEAM3')->count() + $allSt->where('user.status', 'Team3')->count();
    @endphp
    <div class="students-stats-grid">
        <div class="students-kpi-card kpi-total">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalSt }}</div>
                <div class="kpi-title">Total Élèves</div>
                <div class="kpi-subtext">Effectif global</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-users4"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-boys">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalBoys }}</div>
                <div class="kpi-title">Garçons</div>
                <div class="kpi-subtext">{{ $totalSt > 0 ? round(($totalBoys / $totalSt) * 100, 1) : 0 }}% de l'effectif</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-user-tie"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-girls">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalGirls }}</div>
                <div class="kpi-title">Filles</div>
                <div class="kpi-subtext">{{ $totalSt > 0 ? round(($totalGirls / $totalSt) * 100, 1) : 0 }}% de l'effectif</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-woman"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-normal">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalNormal }}</div>
                <div class="kpi-title">Normal</div>
                <div class="kpi-subtext">Standard</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-checkmark-circle"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-adra">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalAdra }}</div>
                <div class="kpi-title">ADRA</div>
                <div class="kpi-subtext">Bénéficiaires</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="icon-heart6"></i>
            </div>
        </div>

        <div class="students-kpi-card kpi-team3">
            <div class="kpi-info-wrapper">
                <div class="kpi-number">{{ $totalTeam3 }}</div>
                <div class="kpi-title">TEAM 3</div>
                <div class="kpi-subtext">Bénéficiaires</div>
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
            <ul class="nav nav-tabs students-nav-tabs">
                <li class="nav-item">
                    <a href="#all-students" class="nav-link active" data-toggle="tab">
                        <i class="icon-list-unordered mr-1 text-primary"></i> Tous les élèves
                        <span class="badge badge-primary badge-pill ml-1">{{ $totalSt }}</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-windows2 mr-1 text-secondary"></i> Filtrer par classe
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm">
                        @foreach($my_classes as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item d-flex justify-content-between align-items-center" data-toggle="tab">
                                <span>{{ $c->name }}</span>
                                <span class="badge badge-light-secondary badge-pill">{{ $students->where('my_class_id', $c->id)->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Onglet : Tous les étudiants --}}
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
                                @foreach($students as $s)
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

                {{-- Onglets par Classe --}}
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
                                    @foreach($students->where('my_class_id', $c->id) as $s)
                                        <tr>
                                            <td class="font-weight-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center"><img class="student-avatar" src="{{ $s->user->photo }}" alt="{{ $s->user->name }}" loading="lazy"></td>
                                            <td>
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="student-link-name editable editable-cell" data-field="name" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->name }}">
                                                    {{ $s->user->name }}
                                                </a>
                                            </td>
                                            <td><span class="badge badge-light-primary font-weight-bold editable editable-cell" data-field="adm_no" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->adm_no }}">{{ $s->adm_no }}</span></td>
                                            <td><span class="font-weight-semibold">{{ $s->section->name }}</span></td>
                                            <td class="editable editable-cell" data-field="dob" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->dob }}">{{ $s->user->dob ?? '-' }}</td>
                                            <td class="age-display age-column font-weight-semibold text-center" data-student-id="{{ Qs::hash($s->id) }}" data-dob="{{ $s->user->dob }}">
                                                @if($s->user->dob)
                                                    <span class="badge badge-light-info font-weight-semibold">{{ \App\Helpers\Qs::calculateAge($s->user->dob) }} ans</span>
                                                @else - @endif
                                            </td>
                                            <td class="editable editable-cell" data-field="address" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->address }}">{{ $s->user->address ?? '-' }}</td>
                                            <td class="editable editable-cell" data-field="religion" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->religion }}">{{ $s->user->religion ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $statusVal = $s->user->status ?? 'Normal';
                                                    $badgeCls = ($statusVal == 'ADRA') ? 'badge-light-warning' : (($statusVal == 'TEAM3' || $statusVal == 'Team3') ? 'badge-light-danger' : 'badge-light-success');
                                                @endphp
                                                <span class="badge {{ $badgeCls }} font-weight-bold editable editable-cell" data-field="status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $statusVal }}">{{ $statusVal }}</span>
                                            </td>
                                            <td><span class="badge badge-light-secondary editable editable-cell" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->student_type ?? 'Nouveau' }}">{{ $s->user->student_type ?? 'Nouveau' }}</span></td>
                                            <td><span class="badge badge-light-info editable editable-cell" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->academic_status ?? 'Passant' }}">{{ $s->user->academic_status ?? 'Passant' }}</span></td>
                                            <td class="text-center">
                                                @if($s->user->gender == 'Male')
                                                    <span class="gender-badge male editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="Male"><i class="icon-user-tie mr-1"></i> Masculin</span>
                                                @elseif($s->user->gender == 'Female')
                                                    <span class="gender-badge female editable editable-cell" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="Female"><i class="icon-woman mr-1"></i> Féminin</span>
                                                @else - @endif
                                            </td>
                                            <td class="editable editable-cell" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_p }}">{{ $s->user->nom_p ?? '-' }}</td>
                                            <td class="editable editable-cell text-muted font-size-sm" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_p }}">{{ $s->user->prof_p ?? '-' }}</td>
                                            <td class="editable editable-cell" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->nom_m }}">{{ $s->user->nom_m ?? '-' }}</td>
                                            <td class="editable editable-cell text-muted font-size-sm" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->prof_m }}">{{ $s->user->prof_m ?? '-' }}</td>
                                            <td class="editable editable-cell font-weight-semibold" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->phone }}">{{ $s->user->phone ?? '-' }}</td>
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
    });

    function confirmDelete(id) {
        if (confirm("Voulez-vous vraiment supprimer cet étudiant ?")) {
            document.getElementById('item-delete-'+id).submit();
        }
    }
</script>
@endsection
