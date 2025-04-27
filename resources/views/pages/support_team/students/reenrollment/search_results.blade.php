@extends('layouts.master')
@section('page_title', 'Résultats de recherche d\'élèves')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Résultats de recherche pour "{{ $search_term }}"</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span>Résultats de recherche pour les élèves de l'année scolaire {{ $previous_year }}. Sélectionnez une classe et une section pour réinscrire un élève.</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('students.reenrollment') }}" class="btn btn-secondary mb-3">
                        <i class="icon-arrow-left7 mr-2"></i> Retour
                    </a>
                </div>
            </div>

            @if($students->count() > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-indigo-400">
                                <h6 class="card-title font-weight-semibold">Élèves trouvés ({{ $students->count() }})</h6>
                            </div>

                            <div class="card-body">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Photo</th>
                                        <th>Nom</th>
                                        <th>N° d'admission</th>
                                        <th>Classe</th>
                                        <th>Section</th>
                                        <th>Statut</th>
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
                                            <td>
                                                @if(!$exists)
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#reenroll_modal_{{ $s->id }}">
                                                        Réinscrire
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="reenroll_modal_{{ $s->id }}" tabindex="-1" role="dialog" aria-labelledby="reenrollModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form method="post" action="{{ route('students.reenrollment.reenroll_student', $s->user_id) }}">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="reenrollModalLabel">Réinscrire {{ $s->user->name }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="my_class_id">Nouvelle classe:</label>
                                                                            <select required class="form-control select" name="my_class_id" id="my_class_id">
                                                                                <option value="">Sélectionner une classe</option>
                                                                                @foreach($my_classes as $c)
                                                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="section_id">Nouvelle section:</label>
                                                                            <select required class="form-control select" name="section_id" id="section_id">
                                                                                <option value="">Sélectionner une section</option>
                                                                                @foreach($sections as $sec)
                                                                                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                                        <button type="submit" class="btn btn-primary">Réinscrire</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Déjà inscrit</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning border-0 alert-dismissible">
                    Aucun élève trouvé pour le terme de recherche "{{ $search_term }}".
                </div>
            @endif
        </div>
    </div>

@endsection
