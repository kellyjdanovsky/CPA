@extends('layouts.master')
@section('page_title', 'Réinscription des Élèves')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Réinscription des Élèves</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span>Cette page vous permet de réinscrire des élèves de l'année scolaire précédente ({{ $previous_year }}) vers l'année scolaire actuelle ({{ $current_year }}). Les élèves conserveront toutes leurs informations personnelles.</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="{{ route('students.reenrollment.search') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <input type="text" name="search_term" class="form-control" placeholder="Rechercher un élève par nom...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">Rechercher</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="{{ route('students.reenrollment.selector') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="prev_class_id">Classe précédente:</label>
                                    <select required class="form-control select" name="prev_class_id" id="prev_class_id">
                                        <option value="">Sélectionner une classe</option>
                                        @foreach($my_classes as $c)
                                            <option {{ (isset($prev_class_id) && $prev_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="prev_section_id">Section précédente:</label>
                                    <select required class="form-control select" name="prev_section_id" id="prev_section_id">
                                        <option value="">Sélectionner une section</option>
                                        @foreach($sections as $s)
                                            <option {{ (isset($prev_section_id) && $prev_section_id == $s->id) ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="new_class_id">Nouvelle classe:</label>
                                    <select required class="form-control select" name="new_class_id" id="new_class_id">
                                        <option value="">Sélectionner une classe</option>
                                        @foreach($my_classes as $c)
                                            <option {{ (isset($new_class_id) && $new_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="new_section_id">Nouvelle section:</label>
                                    <select required class="form-control select" name="new_section_id" id="new_section_id">
                                        <option value="">Sélectionner une section</option>
                                        @foreach($sections as $s)
                                            <option {{ (isset($new_section_id) && $new_section_id == $s->id) ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Afficher les élèves <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            @if($selected)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <form method="post" action="{{ route('students.reenrollment.reenroll', [$prev_class_id, $prev_section_id, $new_class_id, $new_section_id]) }}">
                            @csrf
                            <div class="card">
                                <div class="card-header bg-indigo-400">
                                    <h6 class="card-title font-weight-semibold">Élèves de {{ $previous_year }} - Classe: {{ $students->first()->my_class->name ?? '' }} Section: {{ $students->first()->section->name ?? '' }}</h6>
                                </div>

                                <div class="card-body">
                                    @if($students->count() > 0)
                                        <table class="table datatable-button-html5-columns">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="select-all-students">
                                                </th>
                                                <th>N°</th>
                                                <th>Photo</th>
                                                <th>Nom</th>
                                                <th>N° d'admission</th>
                                                <th>Classe</th>
                                                <th>Section</th>
                                                <th>Statut</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($students as $s)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="student-{{ $s->id }}" class="student-checkbox">
                                                    </td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                                    <td>{{ $s->user->name }}</td>
                                                    <td>{{ $s->adm_no }}</td>
                                                    <td>{{ $s->my_class->name }}</td>
                                                    <td>{{ $s->section->name }}</td>
                                                    <td>
                                                        @php
                                                            $current_year = Qs::getSetting('current_session');
                                                            $exists = App\Models\StudentRecord::where('user_id', $s->user_id)
                                                                ->where('session', $current_year)
                                                                ->exists();
                                                        @endphp
                                                        @if($exists)
                                                            <span class="badge badge-success">Déjà inscrit</span>
                                                        @else
                                                            <span class="badge badge-danger">Non inscrit</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn btn-success btn-lg">Réinscrire les élèves sélectionnés <i class="icon-checkmark-circle ml-2"></i></button>
                                        </div>
                                    @else
                                        <div class="alert alert-warning border-0 alert-dismissible">
                                            Aucun élève trouvé pour cette classe et section dans l'année {{ $previous_year }}.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#select-all-students').change(function() {
                $('.student-checkbox').prop('checked', $(this).prop('checked'));
            });
        });
    </script>

@endsection
