@extends('layouts.master')
@section('page_title', 'Feuille de tabulation')
@section('content')
    <div class="card fade-in">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title"><i class="icon-books mr-2 text-primary"></i> Feuille de tabulation</h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('marks.tabulation_select') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exam_id" class="col-form-label font-weight-bold">Examen :</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-clipboard3"></i></span>
                                </div>
                                <select required id="exam_id" name="exam_id" class="form-control select" data-placeholder="Sélectionnez un examen">
                                    @foreach($exams as $exm)
                                        <option {{ ($selected && $exam_id == $exm->id) ? 'selected' : '' }} value="{{ $exm->id }}">{{ $exm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="my_class_id" class="col-form-label font-weight-bold">Classe :</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-graduation2"></i></span>
                                </div>
                                <select onchange="getClassSections(this.value)" required id="my_class_id" name="my_class_id" class="form-control select" data-placeholder="Sélectionnez une classe">
                                    <option value=""></option>
                                    @foreach($my_classes as $c)
                                        <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="section_id" class="col-form-label font-weight-bold">Section :</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-users"></i></span>
                                </div>
                                <select required id="section_id" name="section_id" data-placeholder="Sélectionnez d'abord une classe" class="form-control select">
                                    @if($selected)
                                        @foreach($sections->where('my_class_id', $my_class_id) as $s)
                                            <option {{ $section_id == $s->id ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 mt-4">
                        <div class="text-right mt-1">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="icon-search4 mr-2"></i> Afficher
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Si une sélection a été faite --}}
    @if($selected)
        <div class="card fade-in mt-3">
            <div class="card-header bg-white">
                <h5 class="card-title">
                    <i class="icon-clipboard3 mr-2 text-success"></i>
                    Feuille de tabulation pour {{ $my_class->name.' '.$section->name.' - '.$ex->name.' ('.$year.')' }}
                </h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped marks-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="font-weight-bold">NOMS DES ÉTUDIANTS</th>
                                @foreach($subjects as $sub)
                                    <th title="{{ $sub->name }}" class="text-center font-weight-bold" rowspan="2">
                                        <div>{{ strtoupper($sub->slug ?: $sub->name) }}</div>
                                        <small class="d-block text-muted">(Coef: {{ $sub->coef }})</small>
                                    </th>
                                @endforeach
                                <th class="text-center bg-pink-100 text-danger font-weight-bold">Total</th>
                                <th class="text-center bg-blue-100 text-primary font-weight-bold">Moyenne</th>
                                <th class="text-center bg-teal-100 text-success font-weight-bold">Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Sort the $students collection based on the suffix returned by Mk::getSuffix()
                                $sortedStudents = $students->sortBy(function($student) use ($exr) {
                                    $suffix = Mk::getSuffix($exr->where('student_id', $student->user_id)->first()->pos);
                                    // Extract the numeric value from the suffix
                                    preg_match('/(\d+)/', $suffix, $matches);
                                    return isset($matches[0]) ? $matches[0] : PHP_INT_MAX;
                                });
                            @endphp

                            @foreach($sortedStudents as $s)
                                @php
                                    $position = $exr->where('student_id', $s->user_id)->first()->pos;
                                    $rowClass = '';
                                    if ($position == 1) {
                                        $rowClass = 'bg-success-50';
                                    } elseif ($position == 2) {
                                        $rowClass = 'bg-info-50';
                                    } elseif ($position == 3) {
                                        $rowClass = 'bg-warning-50';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="font-weight-semibold student-name">{{ $s->user->name }}</td>
                                    @foreach($subjects as $sub)
                                        @php
                                            $mark = $marks->where('student_id', $s->user_id)->where('subject_id', $sub->id)->first()->$tex ?? '-';
                                            $markClass = '';
                                            if ($mark != '-') {
                                                if ($mark >= 16) {
                                                    $markClass = 'text-success font-weight-bold';
                                                } elseif ($mark >= 14) {
                                                    $markClass = 'text-primary font-weight-bold';
                                                } elseif ($mark >= 10) {
                                                    $markClass = 'text-info';
                                                } elseif ($mark < 10) {
                                                    $markClass = 'text-danger';
                                                }
                                            }
                                        @endphp
                                        <td class="text-center {{ $markClass }}">{{ $mark ?: '-' }}</td>
                                    @endforeach

                                    <td class="text-center font-weight-bold text-danger">
                                        {{ $exr->where('student_id', $s->user_id)->first()->total ?: '-' }}
                                    </td>
                                    <td class="text-center font-weight-bold text-primary">
                                        @php
                                            // Calculer la somme des coefficients
                                            $totalCoef = 0;
                                            foreach($subjects as $sub) {
                                                $totalCoef += $sub->coef;
                                            }

                                            // Récupérer le total des points
                                            $totalPoints = $exr->where('student_id', $s->user_id)->first()->total ?: 0;

                                            // Calculer la moyenne (total des points / total des coefficients)
                                            $average = $totalCoef > 0 ? $totalPoints / $totalCoef : 0;

                                            // Arrondir à deux chiffres après la virgule
                                            $average = number_format($average, 2);

                                            // Déterminer la classe CSS en fonction de la moyenne
                                            $avgClass = '';
                                            if ($average >= 16) {
                                                $avgClass = 'badge-success';
                                            } elseif ($average >= 14) {
                                                $avgClass = 'badge-primary';
                                            } elseif ($average >= 10) {
                                                $avgClass = 'badge-info';
                                            } elseif ($average < 10) {
                                                $avgClass = 'badge-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $avgClass }}">{{ $average }}</span>
                                    </td>
                                    <td class="text-center font-weight-bold text-success">
                                        @php
                                            $position = $exr->where('student_id', $s->user_id)->first()->pos;
                                            $suffix = Mk::getSuffix($position);
                                            $badgeClass = 'badge-secondary';

                                            if ($position == 1) {
                                                $badgeClass = 'badge-success';
                                            } elseif ($position == 2) {
                                                $badgeClass = 'badge-primary';
                                            } elseif ($position == 3) {
                                                $badgeClass = 'badge-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{!! $suffix ?: '-' !!}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Bouton d'impression --}}
                <div class="text-center mt-4">
                    <a target="_blank" href="{{  route('marks.print_tabulation', [$exam_id, $my_class_id, $section_id]) }}" class="btn btn-danger btn-lg">
                        <i class="icon-printer mr-2"></i> Imprimer la feuille de tabulation
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
