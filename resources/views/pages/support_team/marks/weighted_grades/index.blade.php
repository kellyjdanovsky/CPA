@extends('layouts.master')
@section('page_title', 'Notes Pondérées - Classement par Coefficients')
@section('content')

{{-- Initialize all variables at the top to prevent undefined errors --}}
@php
    use App\Helpers\NumberFormat;
    
    // Safe variable initialization following project specifications
    $students = $students ?? collect();
    $subjects = $subjects ?? collect();
    $marks = $marks ?? collect();
    $exr = $exr ?? collect();
    $my_class = $my_class ?? null;
    $section = $section ?? null;
    $ex = $ex ?? null;
    $year = $year ?? date('Y');
    $selected = $selected ?? false;
    $weighted_results = $weighted_results ?? [];
    
    // Initialize calculation variables
    $studentCount = 0;
    $classAverage = 0;
    $topScore = 0;
    $lowestScore = 0;
    $passCount = 0;
    $passRate = 0;
    
    // Safe collection counts
    if ($students && method_exists($students, 'count')) {
        $studentCount = $students->count();
    } elseif (is_array($students)) {
        $studentCount = count($students);
    }
@endphp

<style>
/* Styles pour le classement et les notes pondérées */
.podium-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 20px;
    margin: 10px 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.podium-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.gold-podium {
    background: linear-gradient(135deg, #fff9c4 0%, #f7d794 100%);
    border: 3px solid #f39c12;
}

.silver-podium {
    background: linear-gradient(135deg, #f8f9fa 0%, #dee2e6 100%);
    border: 3px solid #95a5a6;
}

.bronze-podium {
    background: linear-gradient(135deg, #fdf2e9 0%, #e67e22 30%);
    border: 3px solid #d35400;
}

.podium-medal {
    position: relative;
    display: inline-block;
}

.position-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 14px;
}

.position-badge.gold {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.position-badge.silver {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

.position-badge.bronze {
    background: linear-gradient(135deg, #e67e22, #d35400);
}

.position-badge-large {
    font-size: 16px;
    padding: 8px 12px;
    min-width: 40px;
}

.student-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.student-name-cell {
    min-width: 200px;
}

.student-name {
    font-size: 16px;
    line-height: 1.2;
}

.ranking-table {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.ranking-table thead th {
    border: none;
    font-size: 13px;
    padding: 15px 8px;
    vertical-align: middle;
    background-color: #f8f9fa;
}

.ranking-table tbody td {
    padding: 12px 8px;
    vertical-align: middle;
    border-left: none;
    border-right: none;
}

.ranking-table tbody tr {
    border-bottom: 2px solid #f8f9fa;
}

.ranking-table tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.badge-lg {
    font-size: 12px;
    padding: 6px 10px;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 5px 0;
}

.stats-label {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

.fade-in {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
}

.subject-grade-cell {
    font-weight: 600;
    text-align: center;
    min-width: 100px;
}

.subject-grade-cell.pass {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    border-radius: 4px;
}

.subject-grade-cell.fail {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
    border-radius: 4px;
}

.total-points-cell {
    font-weight: 700;
    background-color: #e9ecef;
    text-align: center;
}

.average-cell {
    font-weight: 700;
    font-size: 16px;
    text-align: center;
}

.ranking-cell {
    font-weight: 700;
    font-size: 18px;
    text-align: center;
    min-width: 80px;
}

.ranking-cell.gold {
    color: #f39c12;
    background-color: rgba(243, 156, 18, 0.1);
}

.ranking-cell.silver {
    color: #95a5a6;
    background-color: rgba(149, 165, 166, 0.1);
}

.ranking-cell.bronze {
    color: #e67e22;
    background-color: rgba(230, 126, 34, 0.1);
}

.mention-cell {
    font-weight: 600;
    text-align: center;
    min-width: 120px;
}

.mention-cell.excellent {
    color: #007bff;
    background-color: rgba(0, 123, 255, 0.1);
    border-radius: 4px;
}

.mention-cell.good {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    border-radius: 4px;
}

.mention-cell.average {
    color: #ffc107;
    background-color: rgba(255, 193, 7, 0.1);
    border-radius: 4px;
}

.mention-cell.passable {
    color: #17a2b8;
    background-color: rgba(23, 162, 184, 0.1);
    border-radius: 4px;
}

.mention-cell.fail {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
    border-radius: 4px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .student-name-cell {
        min-width: 120px;
    }
    
    .subject-grade-cell {
        min-width: 70px;
        font-size: 11px;
    }
    
    .ranking-cell {
        font-size: 14px;
        min-width: 60px;
    }
    
    .average-cell {
        font-size: 14px;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .table-responsive {
        overflow-x: visible;
    }
    
    body {
        font-size: 10px;
    }
    
    .stats-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .podium-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title font-weight-bold">Notes Pondérées avec Coefficients</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="row">
            <!-- Selector -->
            @if(!$selected)
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span><i class="icon-info22 mr-2"></i> Sélectionnez un examen, une classe et une section pour afficher les notes pondérées.</span>
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <form method="post" action="{{ route('marks.weighted_grades_select') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exam_id" class="font-weight-bold">Examen: <span class="text-danger">*</span></label>
                                <select required id="exam_id" name="exam_id" class="form-control select">
                                    <option value="">Sélectionnez l'Examen</option>
                                    @foreach($exams as $ex)
                                        <option {{ old('exam_id') == $ex->id ? 'selected' : '' }} value="{{ $ex->id }}">{{ $ex->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="my_class_id" class="font-weight-bold">Classe: <span class="text-danger">*</span></label>
                                <select required onchange="getClassSections(this.value)" id="my_class_id" name="my_class_id" class="form-control select">
                                    <option value="">Sélectionnez la Classe</option>
                                    @foreach($my_classes as $c)
                                        <option {{ old('my_class_id') == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="section_id" class="font-weight-bold">Section: <span class="text-danger">*</span></label>
                                <select required id="section_id" name="section_id" class="form-control select">
                                    <option value="">Sélectionnez la Section</option>
                                    @if(old('my_class_id'))
                                        @foreach($sections->where('my_class_id', old('my_class_id')) as $s)
                                            <option {{ old('section_id') == $s->id ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">Afficher les Notes Pondérées <i class="icon-arrow-right14 ml-2"></i></button>
                    </div>
                </form>
            </div>
        </div>

        @if($selected)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="font-weight-bold mb-0">
                                <i class="icon-books mr-2"></i>{{ $my_class->name }} {{ $section->name }}
                            </h4>
                            <p class="text-muted mb-0">
                                <i class="icon-calendar mr-1"></i>{{ $ex->name }} - Session {{ $year }}
                            </p>
                        </div>
                        <div class="no-print">
                            <div class="btn-group">
                                <a href="{{ route('marks.weighted_grades') }}" class="btn btn-secondary">
                                    <i class="icon-reset mr-2"></i>Nouvelle Sélection
                                </a>
                                <a href="{{ route('marks.print_weighted_grades', [$exam_id, $my_class_id, $section_id]) }}" target="_blank" class="btn btn-info">
                                    <i class="icon-printer mr-2"></i>Imprimer
                                </a>
                                <a href="{{ route('marks.export_weighted_grades', [$exam_id, $my_class_id, $section_id]) }}" class="btn btn-success">
                                    <i class="icon-file-excel mr-2"></i>Exporter CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <div class="stats-value">{{ $studentCount }}</div>
                            <div class="stats-label">Étudiants</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <div class="stats-value">{{ $subjects->count() }}</div>
                            <div class="stats-label">Matières</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <div class="stats-value">{{ NumberFormat::formatWithoutRounding(collect($weighted_results)->avg('average'), 2) }}</div>
                            <div class="stats-label">Moyenne Classe</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <div class="stats-value">{{ collect($weighted_results)->where('average', '>=', 10)->count() }}</div>
                            <div class="stats-label">Réussite (≥10)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Podium for Top 3 Students -->
            @if(count($weighted_results) >= 3)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="font-weight-bold mb-3"><i class="icon-trophy3 mr-2"></i>Podium - Meilleurs Étudiants</h5>
                        <div class="row">
                            <!-- 2nd Place -->
                            <div class="col-md-4">
                                <div class="podium-card silver-podium text-center">
                                    <div class="position-badge silver">2</div>
                                    <div class="avatar-circle mx-auto mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                                        {{ substr($weighted_results[1]['student_name'], 0, 1) }}
                                    </div>
                                    <h5 class="font-weight-bold">{{ $weighted_results[1]['student_name'] }}</h5>
                                    <div class="font-weight-bold" style="font-size: 24px; color: #95a5a6;">
                                        {{ NumberFormat::formatWithoutRounding($weighted_results[1]['average'], 2) }} / 20
                                    </div>
                                    <div class="badge badge-secondary mt-2">{{ $weighted_results[1]['mention'] }}</div>
                                </div>
                            </div>

                            <!-- 1st Place -->
                            <div class="col-md-4">
                                <div class="podium-card gold-podium text-center">
                                    <div class="position-badge gold">1</div>
                                    <div class="avatar-circle mx-auto mb-3" style="width: 70px; height: 70px; font-size: 28px;">
                                        {{ substr($weighted_results[0]['student_name'], 0, 1) }}
                                    </div>
                                    <h5 class="font-weight-bold">{{ $weighted_results[0]['student_name'] }}</h5>
                                    <div class="font-weight-bold" style="font-size: 28px; color: #f39c12;">
                                        {{ NumberFormat::formatWithoutRounding($weighted_results[0]['average'], 2) }} / 20
                                    </div>
                                    <div class="badge badge-warning mt-2">{{ $weighted_results[0]['mention'] }}</div>
                                </div>
                            </div>

                            <!-- 3rd Place -->
                            <div class="col-md-4">
                                <div class="podium-card bronze-podium text-center">
                                    <div class="position-badge bronze">3</div>
                                    <div class="avatar-circle mx-auto mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                                        {{ substr($weighted_results[2]['student_name'], 0, 1) }}
                                    </div>
                                    <h5 class="font-weight-bold">{{ $weighted_results[2]['student_name'] }}</h5>
                                    <div class="font-weight-bold" style="font-size: 24px; color: #e67e22;">
                                        {{ NumberFormat::formatWithoutRounding($weighted_results[2]['average'], 2) }} / 20
                                    </div>
                                    <div class="badge badge-danger mt-2">{{ $weighted_results[2]['mention'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Weighted Grades Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover ranking-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">Rang</th>
                                    <th>Étudiant</th>
                                    @foreach($subjects as $subject)
                                        <th class="text-center" title="Coefficient: {{ $subject->coef }}">
                                            {{ $subject->name }}<br><small>(Coef {{ $subject->coef }})</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center" style="width: 100px;">Total Points</th>
                                    <th class="text-center" style="width: 100px;">Moyenne</th>
                                    <th class="text-center" style="width: 120px;">Mention</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weighted_results as $result)
                                    <tr class="fade-in">
                                        <td class="ranking-cell {{ $result['rank'] == 1 ? 'gold' : ($result['rank'] == 2 ? 'silver' : ($result['rank'] == 3 ? 'bronze' : '')) }}">
                                            {{ $result['rank'] }}
                                            @if($result['rank'] <= 3)
                                                <i class="icon-trophy3 ml-1"></i>
                                            @endif
                                        </td>
                                        <td class="student-name-cell">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle mr-3">
                                                    {{ substr($result['student_name'], 0, 1) }}
                                                </div>
                                                <div class="student-name">{{ $result['student_name'] }}</div>
                                            </div>
                                        </td>
                                        @foreach($result['subject_marks'] as $subject_mark)
                                            <td class="subject-grade-cell {{ $subject_mark['raw_mark'] !== null && $subject_mark['raw_mark'] >= 10 ? 'pass' : ($subject_mark['raw_mark'] !== null ? 'fail' : '') }}">
                                                @if($subject_mark['weighted_mark'] !== null)
                                                    {{ NumberFormat::formatWithoutRounding($subject_mark['weighted_mark'], 2) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="total-points-cell">
                                            {{ NumberFormat::formatWithoutRounding($result['total_points'], 2) }}
                                        </td>
                                        <td class="average-cell">
                                            {{ NumberFormat::formatWithoutRounding($result['average'], 2) }}
                                        </td>
                                        <td class="mention-cell 
                                            {{ $result['mention'] == 'Très Bien' ? 'excellent' : 
                                               ($result['mention'] == 'Bien' ? 'good' : 
                                               ($result['mention'] == 'Assez Bien' ? 'average' : 
                                               ($result['mention'] == 'Passable' ? 'passable' : 'fail'))) }}">
                                            {{ $result['mention'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="row mt-4 no-print">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Légende</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold">Mentions</h6>
                                    <ul class="list-unstyled">
                                        <li><span class="badge badge-primary">Très Bien</span> : Moyenne ≥ 16</li>
                                        <li><span class="badge badge-success">Bien</span> : Moyenne ≥ 14</li>
                                        <li><span class="badge badge-info">Assez Bien</span> : Moyenne ≥ 12</li>
                                        <li><span class="badge badge-warning">Passable</span> : Moyenne ≥ 10</li>
                                        <li><span class="badge badge-danger">Insuffisant</span> : Moyenne &lt; 10</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold">Notes</h6>
                                    <ul class="list-unstyled">
                                        <li><span class="badge badge-success">Pass</span> : Note ≥ 10</li>
                                        <li><span class="badge badge-danger">Fail</span> : Note &lt; 10</li>
                                        <li><span class="badge badge-secondary">N/A</span> : Note non disponible</li>
                                    </ul>
                                    <p class="mt-2">
                                        <small><strong>Note :</strong> Toutes les notes sont affichées avec 2 chiffres après la virgule.</small>
                                    </p>
                                    <p class="mt-1">
                                        <small><strong>Total Points :</strong> Somme de toutes les notes de matières de l'étudiant.</small>
                                    </p>
                                    <p class="mt-1">
                                        <small><strong>Moyenne :</strong> Total Points ÷ Somme des Coefficients.</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Function to get class sections
    function getClassSections(class_id) {
        if (!class_id) return;
        
        $.ajax({
            url: '{{ route('get_class_sections', ':id') }}'.replace(':id', class_id),
            type: 'GET',
            success: function(data) {
                $('#section_id').empty().append('<option value="">Sélectionnez la Section</option>');
                $.each(data, function(i, section) {
                    $('#section_id').append('<option value="' + section.id + '">' + section.name + '</option>');
                });
            },
            error: function() {
                alert('Erreur lors du chargement des sections');
            }
        });
    }

    // Initialize select2 for better dropdowns
    $(document).ready(function() {
        $('.select').select2();
    });
</script>

@endsection