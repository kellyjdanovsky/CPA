@extends('layouts.master')
@section('page_title', 'Liste des étudiants')

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
    .student-photo {
        height: 40px;
        width: 40px;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .student-photo:hover {
        transform: scale(1.5);
        z-index: 10;
    }
    .student-status {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }
    .status-normal {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    .status-adra {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    .status-team3 {
        background-color: #fff8e1;
        color: #f57f17;
    }
    .action-buttons .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        transition: background-color 0.2s;
    }
    .action-buttons .dropdown-item i {
        margin-right: 8px;
        font-size: 16px;
    }
    .action-buttons .dropdown-item:hover {
        background-color: #f5f5f5;
    }
    .student-name {
        font-weight: 500;
        color: #333;
    }
    .student-name:hover {
        color: var(--primary-color);
        text-decoration: none;
    }
</style>
@endsection

@section('content')

    <div class="card fade-in">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title">Liste des étudiants</h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">Tous les étudiants</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Classes</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_classes as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                        @endforeach
                    </div>
                </li>
                <li class="nav-item ml-auto">
                    <a href="{{ route('students.list_all') }}" class="nav-link bg-primary text-white">
                        <i class="icon-list3 mr-2"></i> Vue détaillée de tous les étudiants
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
                            <th>Classe</th>
                            <th>Section</th>
                            <th>Date de naissance</th>
                            <th>Âge</th>
                            <th>Père/Tuteur</th>
                            <th>Mère/Tutrice</th>
                            <th>Statut</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle student-photo" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td><a href="{{ route('students.show', Qs::hash($s->id)) }}" class="student-name">{{ $s->user->name }}</a></td>
                                <td><span class="badge badge-primary">{{ $s->adm_no }}</span></td>
                                <td>{{ $s->my_class->name }}</td>
                                <td>{{ $s->section->name }}</td>
                                <td class="dob-value">{{ $s->user->dob }}</td>
                                <td>{{ Qs::calculateAge($s->user->dob) }}</td>
                                <td>{{ $s->user->nom_p }} <small class="text-muted d-block">{{ $s->user->prof_p }}</small></td>
                                <td>{{ $s->user->nom_m }} <small class="text-muted d-block">{{ $s->user->prof_m }}</small></td>
                                <td>
                                    @php
                                        $statusClass = 'status-normal';
                                        if($s->user->status == 'ADRA') {
                                            $statusClass = 'status-adra';
                                        } elseif($s->user->status == 'Team3') {
                                            $statusClass = 'status-team3';
                                        }
                                    @endphp
                                    <span class="student-status {{ $statusClass }}">{{ $s->user->status }}</span>
                                </td>
                                <td>{{ $s->user->phone }}</td>
                                <td class="text-center">
                                    <div class="list-icons action-buttons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye text-primary"></i> Voir</a>
                                                @if(Qs::userIsTeamSA())
                                                    <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil text-info"></i> Modifier</a>
                                                    <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock text-warning"></i> Réinitialiser le mot de passe</a>
                                                @endif
                                                @if(Qs::userIsSuperAdmin())
                                                    <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash text-danger"></i> Supprimer</a>
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
                                <th>Père/Tuteur</th>
                                <th>Mère/Tutrice</th>
                                <th>Statut</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students->where('my_class_id', $c->id) as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle student-photo" src="{{ $s->user->photo }}" alt="photo"></td>
                                    <td><a href="{{ route('students.show', Qs::hash($s->id)) }}" class="student-name">{{ $s->user->name }}</a></td>
                                    <td><span class="badge badge-primary">{{ $s->adm_no }}</span></td>
                                    <td>{{ $s->section->name }}</td>
                                    <td class="dob-value">{{ $s->user->dob }}</td>
                                    <td>{{ Qs::calculateAge($s->user->dob) }}</td>
                                    <td>{{ $s->user->nom_p }} <small class="text-muted d-block">{{ $s->user->prof_p }}</small></td>
                                    <td>{{ $s->user->nom_m }} <small class="text-muted d-block">{{ $s->user->prof_m }}</small></td>
                                    <td>
                                        @php
                                            $statusClass = 'status-normal';
                                            if($s->user->status == 'ADRA') {
                                                $statusClass = 'status-adra';
                                            } elseif($s->user->status == 'Team3') {
                                                $statusClass = 'status-team3';
                                            }
                                        @endphp
                                        <span class="student-status {{ $statusClass }}">{{ $s->user->status }}</span>
                                    </td>
                                    <td>{{ $s->user->phone }}</td>
                                    <td class="text-center">
                                        <div class="list-icons action-buttons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye text-primary"></i> Voir</a>
                                                    @if(Qs::userIsTeamSA())
                                                        <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil text-info"></i> Modifier</a>
                                                        <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock text-warning"></i> Réinitialiser le mot de passe</a>
                                                    @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                        <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash text-danger"></i> Supprimer</a>
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
        function confirmDelete(id) {
            if (confirm("Voulez-vous vraiment supprimer cet étudiant ?")) {
                document.getElementById('item-delete-'+id).submit();
            }
        }
    </script>
@endsection
