@extends('layouts.master')
@section('page_title', 'Informations sur l\'étudiant - '.$my_class->name)

@section('page_styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .datatable-button-html5-columns {
        width: 100%;
        white-space: nowrap;
    }
    .datatable-button-html5-columns th, .datatable-button-html5-columns td {
        padding: 8px 10px;
        vertical-align: middle;
    }
    .datatable-button-html5-columns th {
        font-weight: bold;
        background-color: #f5f5f5;
    }
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
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $my_class->name.' '.$s->section->name }}</td>
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
                @endforeach
            </div>
        </div>
    </div>

    {{--Student List Ends--}}
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialiser les champs éditables
        initializeEditableFields();

        // Fonction pour initialiser les champs éditables
        function initializeEditableFields() {
            // Gérer le clic sur un champ éditable
            $('.editable').on('click', function() {
                // Ne rien faire si on est déjà en mode édition
                if ($(this).hasClass('editing')) {
                    return;
                }

                const field = $(this).data('field');
                const studentId = $(this).data('student-id');
                const currentValue = $(this).text().trim();

                // Créer l'élément d'édition approprié selon le type de champ
                let editElement = '';

                if (field === 'status') {
                    // Créer un menu déroulant pour le statut
                    editElement = `
                        <select class="edit-input">
                            <option value="Normal" ${currentValue === 'Normal' ? 'selected' : ''}>Normal</option>
                            <option value="ADRA" ${currentValue === 'ADRA' ? 'selected' : ''}>ADRA</option>
                            <option value="TEAM3" ${currentValue === 'TEAM3' ? 'selected' : ''}>TEAM3</option>
                        </select>
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else if (field === 'student_type') {
                    // Créer un menu déroulant pour le type d'étudiant
                    editElement = `
                        <select class="edit-input">
                            <option value="Nouveau" ${currentValue === 'Nouveau' ? 'selected' : ''}>Nouveau</option>
                            <option value="Ancien" ${currentValue === 'Ancien' ? 'selected' : ''}>Ancien</option>
                        </select>
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else if (field === 'academic_status') {
                    // Créer un menu déroulant pour le statut académique
                    editElement = `
                        <select class="edit-input">
                            <option value="Passant" ${currentValue === 'Passant' ? 'selected' : ''}>Passant</option>
                            <option value="Redoublant" ${currentValue === 'Redoublant' ? 'selected' : ''}>Redoublant</option>
                        </select>
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else if (field === 'dob') {
                    // Créer un sélecteur de date pour la date de naissance
                    editElement = `
                        <input type="text" class="edit-input datepicker" value="${currentValue}">
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else {
                    // Créer un champ de texte pour les autres champs
                    editElement = `
                        <input type="text" class="edit-input" value="${currentValue}">
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                }

                // Remplacer le contenu par l'élément d'édition
                $(this).html(editElement).addClass('editing');

                // Initialiser le datepicker si nécessaire
                if (field === 'dob') {
                    $(this).find('.datepicker').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        endDate: new Date()
                    });
                }

                // Focus sur l'élément d'édition
                $(this).find('.edit-input').focus();

                // Gérer la perte de focus (pour sauvegarder)
                $(this).find('.edit-input').on('blur', function() {
                    const newValue = $(this).val().trim();
                    saveField(field, newValue, studentId, $(this).closest('.editable'));
                });

                // Gérer la touche Entrée
                $(this).find('.edit-input').on('keypress', function(e) {
                    if (e.which === 13) { // Touche Entrée
                        const newValue = $(this).val().trim();
                        saveField(field, newValue, studentId, $(this).closest('.editable'));
                    }
                });

                // Pour le datepicker, sauvegarder lors de la sélection d'une date
                if (field === 'dob') {
                    $(this).find('.datepicker').on('changeDate', function() {
                        const newValue = $(this).val().trim();
                        saveField(field, newValue, studentId, $(this).closest('.editable'));
                    });
                }

                // Pour les selects, sauvegarder lors du changement
                if (field === 'status' || field === 'student_type' || field === 'academic_status') {
                    $(this).find('select').on('change', function() {
                        const newValue = $(this).val();
                        saveField(field, newValue, studentId, $(this).closest('.editable'));
                    });
                }
            });
        }

        // Fonction pour sauvegarder un champ
        function saveField(field, value, studentId, element) {
            // Afficher l'indicateur de sauvegarde
            element.find('.save-indicator').show();

            // Envoyer la requête AJAX
            $.ajax({
                url: '{{ route("ajax.update_student_field") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_id: studentId,
                    field_name: field,
                    field_value: value
                },
                success: function(response) {
                    if (response.ok) {
                        // Mettre à jour l'affichage
                        element.removeClass('editing').html(value);

                        // Si c'est une date de naissance, mettre à jour l'âge
                        if (field === 'dob' && response.age) {
                            $('.age-display[data-student-id="' + studentId + '"]').text(response.age);
                        }

                        // Afficher un message de succès
                        new PNotify({
                            text: response.msg,
                            type: 'success'
                        });
                    } else {
                        // Afficher un message d'erreur
                        element.removeClass('editing').html(value);
                        new PNotify({
                            text: response.msg,
                            type: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    // Gérer les erreurs
                    let errorMsg = 'Une erreur est survenue';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    }

                    // Restaurer la valeur précédente
                    element.removeClass('editing').html(value);

                    // Afficher un message d'erreur
                    new PNotify({
                        text: errorMsg,
                        type: 'error'
                    });
                }
            });
        }
    });
</script>
@endsection@extends('layouts.master')
@section('page_title', 'Informations sur l\'étudiant - '.$my_class->name)

@section('page_styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .datatable-button-html5-columns {
        width: 100%;
        white-space: nowrap;
    }
    .datatable-button-html5-columns th, .datatable-button-html5-columns td {
        padding: 8px 10px;
        vertical-align: middle;
    }
    .datatable-button-html5-columns th {
        font-weight: bold;
        background-color: #f5f5f5;
    }
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
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $my_class->name.' '.$s->section->name }}</td>
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
                @endforeach
            </div>
        </div>
    </div>

    {{--Student List Ends--}}
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialiser les champs éditables
        initializeEditableFields();

        // Fonction pour initialiser les champs éditables
        function initializeEditableFields() {
            // Gérer le clic sur un champ éditable
            $('.editable').on('click', function() {
                // Ne rien faire si on est déjà en mode édition
                if ($(this).hasClass('editing')) {
                    return;
                }

                const field = $(this).data('field');
                const studentId = $(this).data('student-id');
                const currentValue = $(this).text().trim();

                // Créer l'élément d'édition approprié selon le type de champ
                let editElement = '';

                if (field === 'status') {
                    // Créer un menu déroulant pour le statut
                    editElement = `
                        <select class="edit-input">
                            <option value="Normal" ${currentValue === 'Normal' ? 'selected' : ''}>Normal</option>
                            <option value="ADRA" ${currentValue === 'ADRA' ? 'selected' : ''}>ADRA</option>
                            <option value="TEAM3" ${currentValue === 'TEAM3' ? 'selected' : ''}>TEAM3</option>
                        </select>
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else if (field === 'student_type') {
                    // Créer un menu déroulant pour le type d'étudiant
                    editElement = `
                        <select class="edit-input">
                            <option value="Nouveau" ${currentValue === 'Nouveau' ? 'selected' : ''}>Nouveau</option>
                            <option value="Ancien" ${currentValue === 'Ancien' ? 'selected' : ''}>Ancien</option>
                        </select>
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else if (field === 'academic_status') {
                    // Créer un menu déroulant pour le statut académique
                    editElement = `
                        <select class="edit-input">
                            <option value="Passant" ${currentValue === 'Passant' ? 'selected' : ''}>Passant</option>
                            <option value="Redoublant" ${currentValue === 'Redoublant' ? 'selected' : ''}>Redoublant</option>
                        </select>
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else if (field === 'dob') {
                    // Créer un sélecteur de date pour la date de naissance
                    editElement = `
                        <input type="text" class="edit-input datepicker" value="${currentValue}">
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                } else {
                    // Créer un champ de texte pour les autres champs
                    editElement = `
                        <input type="text" class="edit-input" value="${currentValue}">
                        <span class="save-indicator"><i class="icon-spinner2 spinner"></i></span>
                    `;
                }

                // Remplacer le contenu par l'élément d'édition
                $(this).html(editElement).addClass('editing');

                // Initialiser le datepicker si nécessaire
                if (field === 'dob') {
                    $(this).find('.datepicker').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        endDate: new Date()
                    });
                }

                // Focus sur l'élément d'édition
                $(this).find('.edit-input').focus();

                // Gérer la perte de focus (pour sauvegarder)
                $(this).find('.edit-input').on('blur', function() {
                    const newValue = $(this).val().trim();
                    saveField(field, newValue, studentId, $(this).closest('.editable'));
                });

                // Gérer la touche Entrée
                $(this).find('.edit-input').on('keypress', function(e) {
                    if (e.which === 13) { // Touche Entrée
                        const newValue = $(this).val().trim();
                        saveField(field, newValue, studentId, $(this).closest('.editable'));
                    }
                });

                // Pour le datepicker, sauvegarder lors de la sélection d'une date
                if (field === 'dob') {
                    $(this).find('.datepicker').on('changeDate', function() {
                        const newValue = $(this).val().trim();
                        saveField(field, newValue, studentId, $(this).closest('.editable'));
                    });
                }

                // Pour les selects, sauvegarder lors du changement
                if (field === 'status' || field === 'student_type' || field === 'academic_status') {
                    $(this).find('select').on('change', function() {
                        const newValue = $(this).val();
                        saveField(field, newValue, studentId, $(this).closest('.editable'));
                    });
                }
            });
        }

        // Fonction pour sauvegarder un champ
        function saveField(field, value, studentId, element) {
            // Afficher l'indicateur de sauvegarde
            element.find('.save-indicator').show();

            // Envoyer la requête AJAX
            $.ajax({
                url: '{{ route("ajax.update_student_field") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_id: studentId,
                    field_name: field,
                    field_value: value
                },
                success: function(response) {
                    if (response.ok) {
                        // Mettre à jour l'affichage
                        element.removeClass('editing').html(value);

                        // Si c'est une date de naissance, mettre à jour l'âge
                        if (field === 'dob' && response.age) {
                            $('.age-display[data-student-id="' + studentId + '"]').text(response.age);
                        }

                        // Afficher un message de succès
                        new PNotify({
                            text: response.msg,
                            type: 'success'
                        });
                    } else {
                        // Afficher un message d'erreur
                        element.removeClass('editing').html(value);
                        new PNotify({
                            text: response.msg,
                            type: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    // Gérer les erreurs
                    let errorMsg = 'Une erreur est survenue';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    }

                    // Restaurer la valeur précédente
                    element.removeClass('editing').html(value);

                    // Afficher un message d'erreur
                    new PNotify({
                        text: errorMsg,
                        type: 'error'
                    });
                }
            });
        }
    });
</script>
@endsection