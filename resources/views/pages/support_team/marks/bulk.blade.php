@extends('layouts.master')
@section('page_title', 'Sélectionnez le bulletin de notes de l\'étudiant')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-books mr-2"></i> Sélectionnez le bulletin de notes de l'étudiant</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
                <form method="post" action="{{ route('marks.bulk_select') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <fieldset>

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="my_class_id" class="col-form-label font-weight-bold">Classe :</label>
                                            <select required onchange="getClassSections(this.value)" id="my_class_id" name="my_class_id" class="form-control select">
                                                <option value="">Sélectionnez la classe</option>
                                                @foreach($my_classes as $c)
                                                    <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="section_id" class="col-form-label font-weight-bold">Section :</label>
                                            <select required id="section_id" name="section_id" data-placeholder="Sélectionnez d'abord la classe" class="form-control select">
                                        @if($selected)
                                            @foreach($sections as $s)
                                                    <option {{ ($section_id == $s->id ? 'selected' : '') }} value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                            @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="year" class="col-form-label font-weight-bold">Année scolaire :</label>
                                            <select required id="year" name="year" class="form-control select">
                                                @php
                                                    $current_year = Qs::getSetting('current_session');

                                                    // Si nous sommes dans la vue de sélection initiale (pas encore de classe/section sélectionnée)
                                                    if (!isset($selected) || !$selected) {
                                                        // Récupérer toutes les années scolaires disponibles dans la base de données
                                                        $available_years = App\Models\Mark::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

                                                        // Si aucune année n'est disponible, utiliser l'année actuelle
                                                        if (empty($available_years)) {
                                                            $available_years = [$current_year];
                                                        }
                                                    } else {
                                                        // Si aucune année n'est disponible pour les élèves sélectionnés, utiliser l'année actuelle
                                                        if (empty($available_years)) {
                                                            $available_years = [$current_year];
                                                        }
                                                    }
                                                @endphp
                                                @foreach($available_years as $school_year)
                                                    <option {{ (isset($year) && $school_year == $year) || (!isset($year) && $school_year == $current_year) ? 'selected' : '' }} value="{{ $school_year }}">{{ $school_year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </fieldset>
                        </div>

                        <div class="col-md-2 mt-4">
                            <div class="text-right mt-1">
                                <button type="submit" class="btn btn-primary">Voir les bulletins de notes <i class="icon-paperplane ml-2"></i></button>
                            </div>
                        </div>

                    </div>

                </form>
        </div>
    </div>
    @if($selected)
    <div class="card">
        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>N°</th>
                    <th>Photo</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Genre</th>
                    <th>Classe</th>
                    <th>ADM_No</th>
                    <th>Voir le bulletin</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($students as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                        @php
                            // Séparer le nom complet en prénom et nom
                            $full_name = $s->user->name;
                            $name_parts = explode(' ', $full_name, 2);
                            $first_name = $name_parts[0] ?? '';
                            $last_name = $name_parts[1] ?? '';
                        @endphp
                        <td>{{ $first_name }}</td>
                        <td>{{ $last_name }}</td>
                        <td>
                            @if(isset($s->user->status))
                                <span class="badge badge-{{ $s->user->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($s->user->status) }}
                                </span>
                            @else
                                <span class="badge badge-success">Actif</span>
                            @endif
                        </td>
                        <td>
                            @if($s->user->gender)
                                <span class="badge badge-{{ $s->user->gender == 'Male' ? 'primary' : 'info' }}">
                                    {{ $s->user->gender == 'Male' ? 'Masculin' : 'Féminin' }}
                                </span>
                            @else
                                <span class="text-muted">Non défini</span>
                            @endif
                        </td>
                        <td>
                            @if($s->my_class)
                                <span class="badge badge-dark">{{ $s->my_class->name }}</span>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </td>
                        <td>{{ $s->adm_no }}</td>
                        <td>
                            @php
                                // Vérifier si l'élève a des bulletins pour l'année sélectionnée
                                $student_id = $s->user_id;
                                $available_years = App\Repositories\ExamRepo::getStudentExamYears($student_id);
                                $has_marks = $available_years->contains('year', $year);
                            @endphp

                            @if($has_marks)
                                <a class="btn btn-success btn-sm" href="{{ route('marks.show', [Qs::hash($s->user_id), $year]) }}">
                                    <i class="icon-eye mr-1"></i> Voir le bulletin
                                </a>
                            @else
                                <span class="text-muted">Aucun bulletin</span>
                            @endif
                        </td>
                        <td>
                            @if($has_marks)
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown">
                                        <i class="icon-menu7 mr-1"></i> Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <h6 class="dropdown-header">Options d'impression</h6>
                                        <button type="button" class="dropdown-item print-report-btn"
                                                data-student-id="{{ Qs::hash($s->user_id) }}"
                                                data-year="{{ $year }}"
                                                data-student-name="{{ $s->user->name }}">
                                            <i class="icon-printer mr-1"></i> Imprimer bulletin personnalisé
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Impression rapide par trimestre</h6>
                                        @php
                                            // Récupérer les examens disponibles pour cet étudiant
                                            $available_exams = \App\Models\Exam::where('year', $year)
                                                ->whereExists(function ($query) use ($s, $year) {
                                                    $query->select(\DB::raw(1))
                                                        ->from('exam_records')
                                                        ->whereRaw('exam_records.exam_id = exams.id')
                                                        ->where('exam_records.student_id', $s->user_id)
                                                        ->where('exam_records.year', $year);
                                                })
                                                ->get();
                                        @endphp

                                        @foreach($available_exams as $exam)
                                            <a class="dropdown-item" href="{{ route('marks.print', [Qs::hash($s->user_id), $exam->id, $year]) }}" target="_blank">
                                                <i class="icon-printer2 mr-1"></i> {{ $exam->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Aucune action</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

<!-- Modal pour sélectionner les examens -->
<div id="print_report_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Imprimer le bulletin - <span id="student_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="print_report_form" method="get" action="#">
                <div class="modal-body">
                    <input type="hidden" id="modal_student_id" name="student_id">
                    <input type="hidden" id="modal_year" name="year">
                    
                    <div class="alert alert-info">
                        <i class="icon-info22 mr-2"></i> Sélectionnez les examens que vous souhaitez inclure dans le bulletin. Vous pouvez sélectionner plusieurs examens pour créer un bulletin combiné.
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Sélectionnez les examens à inclure :</label>
                        <div id="exams_container" class="mt-2 border p-3 rounded bg-light">
                            <!-- Les examens seront chargés ici dynamiquement -->
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Chargement...</span>
                                </div>
                                <p class="mt-2">Chargement des examens disponibles...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Imprimer <i class="icon-printer ml-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Fonction pour mettre à jour les années scolaires disponibles
        function updateAvailableYears() {
            var classId = $('#my_class_id').val();
            var sectionId = $('#section_id').val();

            if (classId && sectionId) {
                // Faire une requête AJAX pour obtenir les années scolaires disponibles
                $.ajax({
                    url: '{{ route("ajax.get_available_years") }}',
                    type: 'GET',
                    data: {
                        class_id: classId,
                        section_id: sectionId
                    },
                    success: function(response) {
                        // Mettre à jour le sélecteur d'années scolaires
                        var yearSelect = $('#year');
                        yearSelect.empty();

                        if (response.years.length > 0) {
                            $.each(response.years, function(index, year) {
                                var selected = (year === '{{ Qs::getSetting("current_session") }}') ? 'selected' : '';
                                yearSelect.append('<option value="' + year + '" ' + selected + '>' + year + '</option>');
                            });
                        } else {
                            // Si aucune année n'est disponible, utiliser l'année actuelle
                            yearSelect.append('<option value="{{ Qs::getSetting("current_session") }}" selected>{{ Qs::getSetting("current_session") }}</option>');
                        }

                        // Réinitialiser le sélecteur Select2
                        yearSelect.select2();
                    },
                    error: function() {
                        console.error('Erreur lors de la récupération des années scolaires disponibles');
                    }
                });
            }
        }

        // Mettre à jour les années scolaires lorsque la section change
        $('#section_id').on('change', function() {
            updateAvailableYears();
        });
        
        // Gestion du bouton d'impression du bulletin
        $('.print-report-btn').on('click', function() {
            var studentId = $(this).data('student-id');
            var year = $(this).data('year');
            var studentName = $(this).data('student-name');
            
            // Mettre à jour les données du modal
            $('#student_name').text(studentName);
            $('#modal_student_id').val(studentId);
            $('#modal_year').val(year);
            
            // Vider le conteneur d'examens et afficher le chargement
            $('#exams_container').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div><p class="mt-2">Chargement des examens disponibles...</p></div>');
            
            // Afficher le modal pendant le chargement
            $('#print_report_modal').modal('show');
            
            // Charger les examens disponibles pour cet étudiant et cette année
            $.ajax({
                url: '{{ route("ajax.get_available_years") }}',
                type: 'GET',
                data: {
                    student_id: studentId,
                    year: year
                },
                success: function(response) {
                    // Vider le conteneur d'examens
                    $('#exams_container').empty();
                    
                    if (response.exams && response.exams.length > 0) {
                        // Ajouter un bouton pour tout sélectionner/désélectionner
                        var selectAllBtn = '<div class="mb-3">' +
                            '<div class="btn-group btn-group-sm">' +
                            '<button type="button" class="btn btn-outline-primary" id="select_all_exams"><i class="icon-checkbox-checked mr-1"></i> Tout sélectionner</button>' +
                            '<button type="button" class="btn btn-outline-secondary" id="deselect_all_exams"><i class="icon-checkbox-unchecked mr-1"></i> Tout désélectionner</button>' +
                            '</div>' +
                            '</div>';
                        $('#exams_container').append(selectAllBtn);
                        
                        // Créer une liste de cases à cocher pour chaque examen, groupées par trimestre
                        var examsByTerm = {};
                        
                        // Regrouper les examens par trimestre
                        $.each(response.exams, function(index, exam) {
                            if (!examsByTerm[exam.term]) {
                                examsByTerm[exam.term] = [];
                            }
                            examsByTerm[exam.term].push(exam);
                        });
                        
                        // Créer un accordéon pour chaque trimestre
                        var accordionHtml = '<div class="accordion" id="exams_accordion">';
                        
                        // Fonction pour obtenir le nom du trimestre
                        function getTermName(term) {
                            switch(term) {
                                case 1: return "Premier Trimestre";
                                case 2: return "Deuxième Trimestre";
                                case 3: return "Troisième Trimestre";
                                default: return "Trimestre " + term;
                            }
                        }
                        
                        // Parcourir les trimestres dans l'ordre
                        [1, 2, 3].forEach(function(term) {
                            if (examsByTerm[term] && examsByTerm[term].length > 0) {
                                var termName = getTermName(term);
                                var termId = 'term_' + term;
                                
                                accordionHtml += '<div class="card">';
                                accordionHtml += '<div class="card-header bg-light" id="heading_' + termId + '">';
                                accordionHtml += '<h5 class="mb-0">';
                                accordionHtml += '<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_' + termId + '" aria-expanded="true" aria-controls="collapse_' + termId + '">';
                                accordionHtml += termName + ' <span class="badge badge-primary ml-1">' + examsByTerm[term].length + '</span>';
                                accordionHtml += '</button>';
                                accordionHtml += '</h5>';
                                accordionHtml += '</div>';
                                
                                accordionHtml += '<div id="collapse_' + termId + '" class="collapse show" aria-labelledby="heading_' + termId + '" data-parent="#exams_accordion">';
                                accordionHtml += '<div class="card-body">';
                                
                                // Ajouter les examens de ce trimestre
                                $.each(examsByTerm[term], function(index, exam) {
                                    accordionHtml += '<div class="custom-control custom-checkbox mb-2">';
                                    accordionHtml += '<input type="checkbox" class="custom-control-input exam-checkbox" id="exam_' + exam.id + '" name="exam_ids[]" value="' + exam.id + '">';
                                    accordionHtml += '<label class="custom-control-label" for="exam_' + exam.id + '">' + exam.name + '</label>';
                                    accordionHtml += '</div>';
                                });
                                
                                accordionHtml += '</div>'; // card-body
                                accordionHtml += '</div>'; // collapse
                                accordionHtml += '</div>'; // card
                            }
                        });
                        
                        accordionHtml += '</div>'; // accordion
                        
                        $('#exams_container').append(accordionHtml);
                        
                        // Gérer les boutons de sélection/désélection
                        $('#select_all_exams').on('click', function() {
                            $('.exam-checkbox').prop('checked', true);
                        });
                        
                        $('#deselect_all_exams').on('click', function() {
                            $('.exam-checkbox').prop('checked', false);
                        });
                    } else {
                        $('#exams_container').html('<div class="alert alert-warning">Aucun examen disponible pour cette année scolaire.</div>');
                    }
                },
                error: function() {
                    console.error('Erreur lors de la récupération des examens disponibles');
                    $('#exams_container').html('<div class="alert alert-danger">Erreur lors du chargement des examens. Veuillez réessayer.</div>');
                }
            });
        });
        
        // Gestion du formulaire d'impression
        $('#print_report_form').on('submit', function(e) {
            e.preventDefault();
            
            var studentId = $('#modal_student_id').val();
            var year = $('#modal_year').val();
            
            // Récupérer tous les examens sélectionnés
            var selectedExams = [];
            $('.exam-checkbox:checked').each(function() {
                selectedExams.push($(this).val());
            });
            
            if (selectedExams.length === 0) {
                alert('Veuillez sélectionner au moins un examen');
                return;
            }
            
            // Créer l'URL avec les examens sélectionnés
            var printUrl = '{{ url("marks/print_multiple") }}/' + studentId + '/' + year + '?exams=' + selectedExams.join(',');
            
            // Rediriger vers la page d'impression
            window.open(printUrl, '_blank');
            
            // Fermer le modal
            $('#print_report_modal').modal('hide');
        });
    });
</script>
@endsection