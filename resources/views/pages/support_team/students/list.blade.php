@extends('layouts.master')
@section('page_title', 'Informations sur l\'étudiant - '.$my_class->name)

@section('page_styles')
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
</style>
@endsection



@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Liste des étudiants</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
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
                        @foreach($students as $s)
                            <tr>
                                <td class="number-column">{{ $loop->iteration }}</td>
                                <td class="photo-column"><img class="rounded-circle" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td class="editable editable-cell" data-field="name" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->user->name }}">{{ $s->user->name }}</td>
                                <td class="editable editable-cell" data-field="adm_no" data-student-id="{{ Qs::hash($s->id) }}" data-original-value="{{ $s->adm_no }}">{{ $s->adm_no }}</td>
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

                <div class="tab-pane fade" id="all-students-all-classes">
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

                @foreach($sections as $se)
                    <div class="tab-pane fade" id="s{{$se->id}}">
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
                @endforeach

                @foreach($my_classes as $mc)
                    <div class="tab-pane fade" id="c{{$mc->id}}">
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
        window.inlineEditor.options.csrfToken = '{{ csrf_token() }}';
        window.inlineEditor.options.saveUrl = '{{ route("ajax.update_student_field") }}';
    });

    });
</script>
@endsection