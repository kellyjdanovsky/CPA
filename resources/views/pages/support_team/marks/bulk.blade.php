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
                    <th>Nom</th>
                    <th>ADM_No</th>
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
                        <td>
                            @php
                                // Vérifier si l'élève a des bulletins pour l'année sélectionnée
                                $student_id = $s->user_id;
                                $available_years = App\Repositories\ExamRepo::getStudentExamYears($student_id);
                                $has_marks = $available_years->contains('year', $year);
                            @endphp

                            @if($has_marks)
                                <a class="btn btn-success" href="{{ route('marks.show', [Qs::hash($s->user_id), $year]) }}">
                                    <i class="icon-eye mr-1"></i> Voir le bulletin
                                </a>
                            @else
                                <span class="text-muted">Aucun bulletin disponible</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
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
    });
</script>
@endsection
