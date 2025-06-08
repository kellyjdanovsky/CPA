@extends('layouts.master')
@section('page_title', 'Résultats de recherche d\'élèves')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline bg-primary text-white">
            <h5 class="card-title"><i class="icon-search4 mr-2"></i> Résultats de recherche</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible bg-info-100">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span class="font-weight-semibold"><i class="icon-info22 mr-2"></i> 
                            Résultats de recherche pour les élèves de l'année scolaire {{ $previous_year }}
                            @if(!empty($search_term)) avec le terme "{{ $search_term }}" @endif
                            @if(!empty($class_id)) dans la classe {{ $my_classes->firstWhere('id', $class_id)->name ?? '' }} @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <a href="{{ route('students.reenrollment') }}" class="btn btn-secondary">
                        <i class="icon-arrow-left7 mr-2"></i> Retour à la page de réinscription
                    </a>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group">
                        <a href="{{ route('students.reenrollment.import.form') }}" class="btn btn-primary">
                            <i class="icon-upload4 mr-2"></i> Importer des élèves
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recherche avancée d'élèves -->
            <div class="card mb-4 border-left-primary border-left-3">
                <div class="card-header bg-light">
                    <h6 class="card-title font-weight-semibold"><i class="icon-search4 mr-2"></i>Affiner la recherche</h6>
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
                                        <input type="text" name="search_term" class="form-control" placeholder="Rechercher un élève par nom..." value="{{ $search_term ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control select" name="class_id">
                                        <option value="">Toutes les classes</option>
                                        @foreach($my_classes as $c)
                                            <option value="{{ $c->id }}" {{ (isset($class_id) && $class_id == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
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

            @if($students->count() > 0)
                <div class="card border-left-indigo border-left-3">
                    <div class="card-header bg-indigo-400 text-white">
                        <h6 class="card-title font-weight-semibold">
                            <i class="icon-users4 mr-2"></i> Élèves trouvés ({{ $students->count() }})
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" id="select-all-students" class="form-check-input-styled-primary">
                                            Sélectionner tous les élèves non inscrits
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" id="batch-reenroll-btn" class="btn btn-success" disabled>
                                        <i class="icon-users4 mr-2"></i> Réinscrire les élèves sélectionnés
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form id="batch-reenroll-form" method="post" action="{{ route('students.reenrollment.batch_reenroll') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="selected_students" id="selected-students-input" value="">
                        </form>

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
                                    <th width="15%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $s)
                                    @php
                                        $exists = isset($existing_students) && in_array($s->user_id, $existing_students);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" data-student-id="{{ $s->user_id }}" class="form-check-input-styled-primary student-checkbox" {{ $exists ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <img class="rounded-circle img-thumbnail" style="height: 50px; width: 50px;" src="{{ $s->photo ?? $s->user->photo ?? asset('global_assets/images/placeholders/placeholder.jpg') }}" alt="photo">
                                        </td>
                                        <td>
                                            <div class="font-weight-semibold">{{ $s->student_name ?? $s->user->name ?? 'N/A' }}</div>
                                        </td>
                                        <td>{{ $s->adm_no }}</td>
                                        <td>{{ $s->class_name ?? $s->my_class->name ?? 'N/A' }}</td>
                                        <td>{{ $s->section_name ?? $s->section->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($exists)
                                                <span class="badge badge-success badge-pill"><i class="icon-checkmark2 mr-1"></i> Déjà inscrit</span>
                                            @else
                                                <span class="badge badge-danger badge-pill"><i class="icon-cross2 mr-1"></i> Non inscrit</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$exists)
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#reenroll_modal_{{ $s->id }}">
                                                    <i class="icon-user-plus mr-1"></i> Réinscrire
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="reenroll_modal_{{ $s->id }}" tabindex="-1" role="dialog" aria-labelledby="reenrollModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <form method="post" action="{{ route('students.reenrollment.reenroll_student', $s->user_id) }}">
                                                                @csrf
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title" id="reenrollModalLabel">
                                                                        <i class="icon-user-plus mr-2"></i> Réinscrire {{ $s->student_name ?? $s->user->name ?? 'cet élève' }}
                                                                    </h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-3 text-center">
                                                                            <img class="rounded-circle img-thumbnail" style="height: 100px; width: 100px;" src="{{ $s->photo ?? $s->user->photo ?? asset('global_assets/images/placeholders/placeholder.jpg') }}" alt="photo">
                                                                            <h5 class="mt-2">{{ $s->student_name ?? $s->user->name ?? 'N/A' }}</h5>
                                                                            <p class="text-muted">Classe précédente: {{ $s->class_name ?? $s->my_class->name ?? 'N/A' }} - {{ $s->section_name ?? $s->section->name ?? 'N/A' }}</p>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="my_class_id"><i class="icon-office mr-1"></i> Nouvelle classe:</label>
                                                                        <select required class="form-control select" name="my_class_id" id="my_class_id">
                                                                            <option value="">Sélectionner une classe</option>
                                                                            @foreach($my_classes as $c)
                                                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="section_id"><i class="icon-grid52 mr-1"></i> Nouvelle section:</label>
                                                                        <select required class="form-control select" name="section_id" id="section_id">
                                                                            <option value="">Sélectionner une section</option>
                                                                            @foreach($sections as $sec)
                                                                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light" data-dismiss="modal">
                                                                        <i class="icon-cross2 mr-1"></i> Annuler
                                                                    </button>
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="icon-checkmark3 mr-1"></i> Réinscrire
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-success">
                                                    <i class="icon-checkmark-circle mr-1"></i> Déjà inscrit
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal pour la réinscription en masse -->
                <div class="modal fade" id="batch_reenroll_modal" tabindex="-1" role="dialog" aria-labelledby="batchReenrollModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="batch-class-form" method="post" action="{{ route('students.reenrollment.batch_reenroll_submit') }}">
                                @csrf
                                <input type="hidden" name="student_ids" id="batch-student-ids">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="batchReenrollModalLabel">
                                        <i class="icon-users4 mr-2"></i> Réinscrire les élèves sélectionnés
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Vous êtes sur le point de réinscrire <span id="selected-count" class="font-weight-bold">0</span> élève(s) pour l'année scolaire {{ $current_year }}.</p>
                                    
                                    <div class="form-group">
                                        <label for="batch_class_id"><i class="icon-office mr-1"></i> Nouvelle classe:</label>
                                        <select required class="form-control select" name="class_id" id="batch_class_id">
                                            <option value="">Sélectionner une classe</option>
                                            @foreach($my_classes as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="batch_section_id"><i class="icon-grid52 mr-1"></i> Nouvelle section:</label>
                                        <select required class="form-control select" name="section_id" id="batch_section_id">
                                            <option value="">Sélectionner une section</option>
                                            @foreach($sections as $sec)
                                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">
                                        <i class="icon-cross2 mr-1"></i> Annuler
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="icon-checkmark3 mr-1"></i> Réinscrire
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning border-0 alert-dismissible">
                    <i class="icon-warning2 mr-2"></i> Aucun élève trouvé avec ces critères de recherche.
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
                updateBatchButton();
            });
            
            // Mettre à jour le bouton de réinscription en masse
            $('.student-checkbox').change(function() {
                updateBatchButton();
            });
            
            // Ouvrir la modal de réinscription en masse
            $('#batch-reenroll-btn').click(function() {
                var selectedIds = [];
                $('.student-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('student-id'));
                });
                
                $('#batch-student-ids').val(JSON.stringify(selectedIds));
                $('#selected-count').text(selectedIds.length);
                $('#batch_reenroll_modal').modal('show');
            });
            
            // Fonction pour mettre à jour le bouton de réinscription en masse
            function updateBatchButton() {
                var selectedCount = $('.student-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#batch-reenroll-btn').prop('disabled', false);
                } else {
                    $('#batch-reenroll-btn').prop('disabled', true);
                }
            }
        });
    </script>

@endsection
