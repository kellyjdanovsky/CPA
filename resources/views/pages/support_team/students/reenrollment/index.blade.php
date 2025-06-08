@extends('layouts.master')
@section('page_title', 'Réinscription des Élèves')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline bg-primary text-white">
            <h5 class="card-title"><i class="icon-users4 mr-2"></i> Réinscription des Élèves</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible bg-info-100">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span class="font-weight-semibold"><i class="icon-info22 mr-2"></i> Cette page vous permet de réinscrire des élèves de l'année scolaire précédente ({{ $previous_year }}) vers l'année scolaire actuelle ({{ $current_year }}). Les élèves conserveront toutes leurs informations personnelles.</span>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="btn-group">
                        <a href="{{ route('students.reenrollment.import.form') }}" class="btn btn-primary">
                            <i class="icon-upload4 mr-2"></i> Importer des élèves
                        </a>
                        @if(isset($prev_class_id) && isset($prev_section_id))
                        <a href="{{ route('students.reenrollment.export', [$prev_class_id, $prev_section_id]) }}" class="btn btn-success">
                            <i class="icon-download4 mr-2"></i> Exporter cette liste
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recherche avancée d'élèves -->
            <div class="card mb-4 border-left-primary border-left-3">
                <div class="card-header bg-light">
                    <h6 class="card-title font-weight-semibold"><i class="icon-search4 mr-2"></i>Recherche avancée d'élèves</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('students.reenrollment.search') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-user"></i></span>
                                        </span>
                                        <input type="text" name="search_term" class="form-control" placeholder="Rechercher un élève par nom...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control select" name="class_id">
                                        <option value="">Toutes les classes</option>
                                        @foreach($my_classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="icon-search4 mr-2"></i> Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sélection des classes -->
            <div class="card mb-4 border-left-success border-left-3">
                <div class="card-header bg-light">
                    <h6 class="card-title font-weight-semibold"><i class="icon-graduation2 mr-2"></i>Sélection des classes</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('students.reenrollment.selector') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="card-title mb-0"><i class="icon-history mr-2"></i>Année précédente ({{ $previous_year }})</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prev_class_id"><i class="icon-office mr-1"></i>Classe précédente:</label>
                                                    <select required class="form-control select" name="prev_class_id" id="prev_class_id">
                                                        <option value="">Sélectionner une classe</option>
                                                        @foreach($my_classes as $c)
                                                            <option {{ (isset($prev_class_id) && $prev_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prev_section_id"><i class="icon-grid52 mr-1"></i>Section précédente:</label>
                                                    <select required class="form-control select" name="prev_section_id" id="prev_section_id">
                                                        <option value="">Sélectionner une section</option>
                                                        @foreach($sections as $s)
                                                            <option {{ (isset($prev_section_id) && $prev_section_id == $s->id) ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="card-title mb-0"><i class="icon-calendar mr-2"></i>Année actuelle ({{ $current_year }})</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="new_class_id"><i class="icon-office mr-1"></i>Nouvelle classe:</label>
                                                    <select required class="form-control select" name="new_class_id" id="new_class_id">
                                                        <option value="">Sélectionner une classe</option>
                                                        @foreach($my_classes as $c)
                                                            <option {{ (isset($new_class_id) && $new_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="new_section_id"><i class="icon-grid52 mr-1"></i>Nouvelle section:</label>
                                                    <select required class="form-control select" name="new_section_id" id="new_section_id">
                                                        <option value="">Sélectionner une section</option>
                                                        @foreach($sections as $s)
                                                            <option {{ (isset($new_section_id) && $new_section_id == $s->id) ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="icon-users4 mr-2"></i> Afficher les élèves <i class="icon-arrow-right8 ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($selected)
                <div class="card border-left-indigo border-left-3">
                    <div class="card-header bg-indigo-400 text-white">
                        <h6 class="card-title font-weight-semibold">
                            <i class="icon-users4 mr-2"></i> Élèves de {{ $previous_year }} - Classe: {{ $students->first()->my_class->name ?? '' }} Section: {{ $students->first()->section->name ?? '' }}
                        </h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a href="{{ route('students.reenrollment.export', [$prev_class_id, $prev_section_id]) }}" class="btn btn-outline-light btn-sm">
                                    <i class="icon-download4 mr-2"></i> Exporter
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" id="select-all-students" class="form-check-input-styled-primary">
                                                Sélectionner tous les élèves
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="btn-group">
                                            <a href="{{ route('students.reenrollment.reenroll_all', [$prev_class_id, $prev_section_id, $new_class_id, $new_section_id]) }}" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir réinscrire TOUS les élèves de cette classe?')">
                                                <i class="icon-users4 mr-2"></i> Réinscrire tous les élèves
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="post" action="{{ route('students.reenrollment.reenroll', [$prev_class_id, $prev_section_id, $new_class_id, $new_section_id]) }}">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered datatable-button-html5-columns">
                                        <thead class="thead-light">
                                        <tr>
                                            <th width="5%">
                                                <i class="icon-checkbox-checked2"></i>
                                            </th>
                                            <th width="5%">N°</th>
                                            <th width="10%">Photo</th>
                                            <th>Nom</th>
                                            <th>N° d'admission</th>
                                            <th>Classe</th>
                                            <th>Section</th>
                                            <th width="10%">Statut</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($students as $s)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        @php
                                                            $exists = isset($existing_students) && in_array($s->user_id, $existing_students);
                                                        @endphp
                                                        <input type="checkbox" name="student-{{ $s->id }}" class="form-check-input-styled-primary student-checkbox" {{ $exists ? 'disabled' : '' }}>
                                                    </div>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <img class="rounded-circle img-thumbnail" style="height: 50px; width: 50px;" src="{{ $s->user->photo }}" alt="photo">
                                                </td>
                                                <td>
                                                    <div class="font-weight-semibold">{{ $s->user->name }}</div>
                                                </td>
                                                <td>{{ $s->adm_no }}</td>
                                                <td>{{ $s->my_class->name }}</td>
                                                <td>{{ $s->section->name }}</td>
                                                <td>
                                                    @if($exists)
                                                        <span class="badge badge-success badge-pill"><i class="icon-checkmark2 mr-1"></i> Déjà inscrit</span>
                                                    @else
                                                        <span class="badge badge-danger badge-pill"><i class="icon-cross2 mr-1"></i> Non inscrit</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="icon-user-plus mr-2"></i> Réinscrire les élèves sélectionnés <i class="icon-checkmark-circle ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning border-0 alert-dismissible">
                                <i class="icon-warning2 mr-2"></i> Aucun élève trouvé pour cette classe et section dans l'année {{ $previous_year }}.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialiser les checkbox stylisés
            $('.form-check-input-styled-primary').uniform({
                wrapperClass: 'border-primary-600 text-primary-800'
            });
            
            // Sélectionner/désélectionner tous les élèves
            $('#select-all-students').change(function() {
                $('.student-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
                $.uniform.update('.student-checkbox');
            });
        });
    </script>

@endsection
