@extends('layouts.master')
@section('page_title', 'Informations sur l\'étudiant - '.$my_class->name)

@section('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/datatable_responsive.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/inline_editing.css') }}">
<style>
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
            <h6 class="card-title">Informations sur l'étudiant - {{ $my_class->name }}</h6>
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
                </div>
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">Tous les étudiants de la classe {{ $my_class->name }}</a></li>
                <li class="nav-item"><a href="#all-students-all-classes" class="nav-link" data-toggle="tab">Tous les étudiants de toutes les classes</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Sections</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($sections as $s)
                            <a href="#s{{ $s->id }}" class="dropdown-item" data-toggle="tab">{{ $my_class->name.' '.$s->name }}</a>
                        @endforeach
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Filtrer par classe</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_classes as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                        @endforeach
                    </div>
                </li>
                <li class="nav-item ml-auto">
                    <a href="{{ route('students.list_all') }}" class="nav-link bg-info text-white">
                        <i class="icon-list3 mr-2"></i> Voir tous les étudiants
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-students">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                                <tr>
                                    <th class="all">N°</th>
                                    <th class="none">Photo</th>
                                    <th class="all">Nom</th>
                                    <th class="min-tablet">N° d'admission</th>
                                    <th class="min-tablet">Classe/Section</th>
                                    <th class="none">Date de naissance</th>
                                    <th class="none">Âge</th>
                                    <th class="none">Adresse</th>
                                    <th class="none">Religion</th>
                                    <th class="min-tablet">Statut</th>
                                    <th class="none">Type</th>
                                    <th class="none">Statut académique</th>
                                    <th class="none">Sexe</th>
                                    <th class="none">Père/Tuteur</th>
                                    <th class="none">Profession père</th>
                                    <th class="none">Mère/Tutrice</th>
                                    <th class="none">Profession mère</th>
                                    <th class="none">Téléphone</th>
                                    <th class="all no-export">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                        <td>{{ $s->user->name }}</td>
                                        <td>{{ $s->adm_no }}</td>
                                        <td>{{ $my_class->name.' '.$s->section->name }}</td>
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
                                @include('partials.student_action_buttons', ['s' => $s])
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="all-students-all-classes">
                    <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th class="all">N°</th>
                            <th class="none">Photo</th>
                            <th class="all">Nom</th>
                            <th class="min-tablet">N° d'admission</th>
                            <th class="min-tablet">Classe/Section</th>
                            <th class="none">Date de naissance</th>
                            <th class="none">Âge</th>
                            <th class="none">Adresse</th>
                            <th class="min-tablet">Statut</th>
                            <th class="none">Type</th>
                            <th class="none">Statut académique</th>
                            <th class="none">Sexe</th>
                            <th class="none">Père/Tuteur</th>
                            <th class="none">Profession père</th>
                            <th class="none">Mère/Tutrice</th>
                            <th class="none">Profession mère</th>
                            <th class="none">Téléphone</th>
                            <th class="all no-export">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($all_students as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $s->my_class->name.' '.$s->section->name }}</td>
                                <td class="editable" data-field="dob" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->dob }}</td>
                                <td class="age-display" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->age }}</td>
                                <td class="editable" data-field="address" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->address }}</td>
                                <td class="editable" data-field="status" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->status ?? 'Normal' }}</td>
                                <td class="editable" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->student_type ?? 'Nouveau' }}</td>
                                <td class="editable" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->academic_status ?? 'Passant' }}</td>
                                <td class="editable" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->gender == 'Male' ? 'Masculin' : ($s->user->gender == 'Female' ? 'Féminin' : '-') }}</td>
                                <td class="editable" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_p }}</td>
                                <td class="editable" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_p }}</td>
                                <td class="editable" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_m }}</td>
                                <td class="editable" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_m }}</td>
                                <td class="editable" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->phone }}</td>
                                @include('partials.student_action_buttons', ['s' => $s])
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                @foreach($sections as $se)
                    <div class="tab-pane fade" id="s{{$se->id}}">
                        <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th class="all">N°</th>
                                <th class="none">Photo</th>
                                <th class="all">Nom</th>
                                <th class="min-tablet">N° d'admission</th>
                                <th class="min-tablet">Classe/Section</th>
                                <th class="none">Date de naissance</th>
                                <th class="none">Âge</th>
                                <th class="none">Adresse</th>
                                <th class="min-tablet">Statut</th>
                                <th class="none">Type</th>
                                <th class="none">Statut académique</th>
                                <th class="none">Sexe</th>
                                <th class="none">Père/Tuteur</th>
                                <th class="none">Profession père</th>
                                <th class="none">Mère/Tutrice</th>
                                <th class="none">Profession mère</th>
                                <th class="none">Téléphone</th>
                                <th class="all no-export">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students->where('section_id', $se->id) as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                    <td>{{ $s->user->name }}</td>
                                    <td>{{ $s->adm_no }}</td>
                                    <td>{{ $my_class->name.' '.$s->section->name }}</td>
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
                                    <td class="editable" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->student_type ?? 'Nouveau' }}</td>
                                    <td class="editable" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->academic_status ?? 'Passant' }}</td>
                                    <td class="editable" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->gender == 'Male' ? 'Masculin' : ($s->user->gender == 'Female' ? 'Féminin' : '-') }}</td>
                                    <td class="editable" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_p }}</td>
                                    <td class="editable" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_p }}</td>
                                    <td class="editable" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_m }}</td>
                                    <td class="editable" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_m }}</td>
                                    <td class="editable" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->phone }}</td>
                                    @include('partials.student_action_buttons', ['s' => $s])
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                @endforeach

                @foreach($my_classes as $mc)
                    <div class="tab-pane fade" id="c{{$mc->id}}">
                        <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th class="all">N°</th>
                                <th class="none">Photo</th>
                                <th class="all">Nom</th>
                                <th class="min-tablet">N° d'admission</th>
                                <th class="min-tablet">Classe/Section</th>
                                <th class="none">Date de naissance</th>
                                <th class="none">Âge</th>
                                <th class="none">Adresse</th>
                                <th class="min-tablet">Statut</th>
                                <th class="none">Type</th>
                                <th class="none">Statut académique</th>
                                <th class="none">Sexe</th>
                                <th class="none">Père/Tuteur</th>
                                <th class="none">Profession père</th>
                                <th class="none">Mère/Tutrice</th>
                                <th class="none">Profession mère</th>
                                <th class="none">Téléphone</th>
                                <th class="all no-export">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($all_students->where('my_class_id', $mc->id) as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                    <td>{{ $s->user->name }}</td>
                                    <td>{{ $s->adm_no }}</td>
                                    <td>{{ $mc->name.' '.$s->section->name }}</td>
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
                                    <td class="editable" data-field="student_type" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->student_type ?? 'Nouveau' }}</td>
                                    <td class="editable" data-field="academic_status" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->academic_status ?? 'Passant' }}</td>
                                    <td class="editable" data-field="gender" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->gender == 'Male' ? 'Masculin' : ($s->user->gender == 'Female' ? 'Féminin' : '-') }}</td>
                                    <td class="editable" data-field="nom_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_p }}</td>
                                    <td class="editable" data-field="prof_p" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_p }}</td>
                                    <td class="editable" data-field="nom_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->nom_m }}</td>
                                    <td class="editable" data-field="prof_m" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->prof_m }}</td>
                                    <td class="editable" data-field="phone" data-student-id="{{ Qs::hash($s->id) }}">{{ $s->user->phone }}</td>
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

    {{--Student List Ends--}}
@endsection

@section('scripts')
<script src="{{ asset('assets/js/inline_editing.js') }}"></script>
<script>
    $(document).ready(function() {
        // Le système d'édition en ligne est maintenant géré par inline_editing.js
        // Configuration du token CSRF
        if (window.inlineEditor) {
            window.inlineEditor.options.csrfToken = '{{ csrf_token() }}';
            window.inlineEditor.options.saveUrl = '{{ route("ajax.update_student_field") }}';
        }

        // Gestion des actions groupées
        $('#check-all').on('change', function() {
            $('.student-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkButton();
        });

        $(document).on('change', '.student-checkbox', function() {
            updateBulkButton();
            var allChecked = $('.student-checkbox:checked').length === $('.student-checkbox').length;
            $('#check-all').prop('checked', allChecked);
        });

        function updateBulkButton() {
            var count = $('.student-checkbox:checked').length;
            $('#selected-count').text(count);
            $('#bulk-actions-btn').prop('disabled', count === 0);
        }

        window.bulkAction = function(action) {
            var selected = [];
            $('.student-checkbox:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) return;

            if (action === 'delete' && !confirm('Êtes-vous sûr de vouloir supprimer ces ' + selected.length + ' étudiants ?')) {
                return;
            }
            
            if (action === 'change_status') {
                var status = prompt("Entrez le nouveau statut (Normal, ADRA, TEAM3) :");
                if (!status) return;
                // On passe le statut en paramètre supplémentaire
                performBulkAction(action, selected, { status: status });
                return;
            }

            performBulkAction(action, selected);
        }

        function performBulkAction(action, ids, extraData = {}) {
            $.ajax({
                url: '{{ route("students.bulk_action") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    ids: ids,
                    ...extraData
                },
                success: function(response) {
                    alert('Action effectuée avec succès');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Erreur : ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText));
                }
            });
        }
    });
</script>
@endsection
