@extends('layouts.master')
@section('page_title', 'Feuille de tabulation - Version améliorée')
@section('content')

{{-- Safe variable initialization --}}
@php
    $students = $students ?? collect();
    $subjects = $subjects ?? collect();
    $marks = $marks ?? collect();
    $exr = $exr ?? collect();
    $my_class = $my_class ?? null;
    $section = $section ?? null;
    $ex = $ex ?? null;
    $year = $year ?? date('Y');
    $selected = $selected ?? false;
    $my_classes = $my_classes ?? collect();
    $exams = $exams ?? collect();
    $sections = $sections ?? collect();
    $my_class_id = $my_class_id ?? null;
    $section_id = $section_id ?? null;
    $exam_id = $exam_id ?? null;
    
    $studentCount = $students ? (method_exists($students, 'count') ? $students->count() : count($students)) : 0;
@endphp

<style>
.tabulation-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.tabulation-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.tabulation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
}

.ranking-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.ranking-table th {
    background: #343a40;
    color: white;
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    border: 1px solid #dee2e6;
}

.ranking-table td {
    padding: 10px 8px;
    text-align: center;
    border: 1px solid #dee2e6;
    vertical-align: middle;
}

.ranking-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.ranking-table tbody tr:hover {
    background-color: #e3f2fd;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: left;
}

.student-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 11px;
}

.position-badge {
    padding: 4px 8px;
    border-radius: 15px;
    font-weight: bold;
    font-size: 11px;
    color: white;
    min-width: 30px;
}

.position-1 { background: linear-gradient(135deg, #ffd700, #ffb300); }
.position-2 { background: linear-gradient(135deg, #c0c0c0, #a0a0a0); }
.position-3 { background: linear-gradient(135deg, #cd7f32, #a0522d); }
.position-top10 { background: linear-gradient(135deg, #28a745, #1e7e34); }
.position-other { background: linear-gradient(135deg, #6c757d, #495057); }

.mark-excellent { color: #28a745; font-weight: bold; }
.mark-good { color: #007bff; font-weight: bold; }
.mark-average { color: #ffc107; }
.mark-poor { color: #dc3545; }

.mention-badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
    color: white;
}

.mention-excellent { background: #28a745; }
.mention-good { background: #007bff; }
.mention-fair { background: #ffc107; color: #333; }
.mention-pass { background: #fd7e14; }
.mention-fail { background: #dc3545; }

@media print {
    .no-print { display: none !important; }
    .tabulation-container { background: white !important; padding: 0 !important; }
    .ranking-table { font-size: 9px !important; }
    @page { size: A4 landscape; margin: 8mm 6mm; }
}
</style>

<div class="tabulation-container">
    <div class="container-fluid">
        
        {{-- Selection Form --}}
        <div class="tabulation-card">
            <div class="tabulation-header">
                <h4 class="mb-0">
                    <i class="icon-clipboard mr-2"></i>
                    Sélection des paramètres de tabulation
                </h4>
            </div>
            
            <div class="card-body">
                <form method="post" action="{{ route('marks.tabulation_select') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Examen :</label>
                                <select required name="exam_id" class="form-control">
                                    <option value="">Sélectionnez un examen</option>
                                    @if($exams && $exams->count() > 0)
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}" {{ ($selected && $exam_id == $exam->id) ? 'selected' : '' }}>
                                                {{ $exam->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Classe :</label>
                                <select required name="my_class_id" class="form-control" onchange="getClassSections(this.value)">
                                    <option value="">Sélectionnez une classe</option>
                                    @if($my_classes && $my_classes->count() > 0)
                                        @foreach($my_classes as $class)
                                            <option value="{{ $class->id }}" {{ ($selected && $my_class_id == $class->id) ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Section :</label>
                                <select required name="section_id" class="form-control">
                                    <option value="">Sélectionnez une section</option>
                                    @if($selected && $sections && $sections->count() > 0)
                                        @foreach($sections->where('my_class_id', $my_class_id ?? 0) as $sect)
                                            <option value="{{ $sect->id }}" {{ ($section_id == $sect->id) ? 'selected' : '' }}>
                                                {{ $sect->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="icon-search mr-2"></i>Afficher
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selected && $studentCount > 0)
            {{-- Statistics Dashboard --}}
            @php
                $averagesCollection = collect();
                
                if ($studentCount > 0 && $subjects && $subjects->count() > 0) {
                    foreach($students as $student) {
                        $totalPoints = 0;
                        $usedCoef = 0;
                        
                        foreach($subjects as $subject) {
                            $markRecord = $marks ? $marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first() : null;
                            if ($markRecord) {
                                $t1 = $markRecord->t1 ?: 0;
                                $t2 = $markRecord->t2 ?: 0;
                                $exm = $markRecord->exm ?: 0;
                                
                                $values = [$t1, $t2, $exm];
                                $sum = array_sum($values);
                                $count = count(array_filter($values, fn($value) => $value > 0));
                                
                                if ($count > 0) {
                                    $subjectAverage = $sum / $count;
                                    $totalPoints += ($subjectAverage * $subject->coef);
                                    $usedCoef += $subject->coef;
                                }
                            }
                        }
                        
                        if ($usedCoef > 0) {
                            $average = $totalPoints / $usedCoef;
                            $averagesCollection->push($average);
                        }
                    }
                }
                
                $classAverage = $averagesCollection->count() > 0 ? $averagesCollection->avg() : 0;
                $topScore = $averagesCollection->count() > 0 ? $averagesCollection->max() : 0;
                $lowestScore = $averagesCollection->count() > 0 ? $averagesCollection->min() : 0;
                $passCount = $averagesCollection->filter(function($avg) { return $avg >= 10; })->count();
                $passRate = $studentCount > 0 ? ($passCount / $studentCount) * 100 : 0;
            @endphp
            
            <div class="tabulation-card">
                <div class="tabulation-header">
                    <h4 class="mb-0">
                        <i class="icon-bar-chart mr-2"></i>
                        Statistiques - {{ $my_class->name ?? '' }} {{ $section->name ?? '' }} - {{ $ex->name ?? '' }} ({{ $year }})
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <h5 class="text-info">{{ $studentCount }}</h5>
                            <small class="text-muted">Étudiants</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="text-primary" title="Formule: Somme des moyennes / Nombre d'étudiants">{{ number_format($classAverage, 2) }}/20</h5>
                            <small class="text-muted">Moyenne Classe</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="text-success">{{ number_format($topScore, 2) }}/20</h5>
                            <small class="text-muted">Meilleur Score</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="text-warning">{{ number_format($lowestScore, 2) }}/20</h5>
                            <small class="text-muted">Score Min</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="text-success">{{ $passCount }}</h5>
                            <small class="text-muted">Admis (≥10)</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="text-info">{{ number_format($passRate, 1) }}%</h5>
                            <small class="text-muted">Taux Réussite</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ranking Table --}}
            <div class="tabulation-card">
                <div class="tabulation-header">
                    <h4 class="mb-0">
                        <i class="icon-list-numbered mr-2"></i>
                        Classement détaillé avec notes par matière
                    </h4>
                </div>
                
                <div class="table-responsive">
                    <table class="ranking-table" id="rankingTable">
                        <thead>
                            <tr>
                                <th width="6%">Rang</th>
                                <th width="20%">Étudiant</th>
                                @if($subjects && $subjects->count() > 0)
                                    @foreach($subjects as $subject)
                                        <th width="8%" title="{{ $subject->name }}">
                                            {{ strtoupper(substr($subject->name, 0, 4)) }}<br>
                                            <small>({{ $subject->coef }})</small>
                                        </th>
                                    @endforeach
                                @endif
                                <th width="10%">Total</th>
                                <th width="10%">Moyenne</th>
                                <th width="12%">Mention</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($students && $students->count() > 0 && $exr)
                                @php
                                    // Sort students by position
                                    $sortedStudents = $students->sortBy(function($student) use ($exr) {
                                        $examRecord = $exr->where('student_id', $student->user_id)->first();
                                        return $examRecord ? $examRecord->pos : 999;
                                    });
                                @endphp
                                
                                @foreach($sortedStudents as $student)
                                    @php
                                        // Get student exam record
                                        $examRecord = $exr->where('student_id', $student->user_id)->first();
                                        $position = $examRecord ? $examRecord->pos : 999;
                                        
                                        // Calculate student average
                                        $totalPoints = 0;
                                        $usedCoef = 0;
                                        
                                        if ($subjects && $subjects->count() > 0) {
                                            foreach($subjects as $subject) {
                                                $markRecord = $marks ? $marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first() : null;
                                                if ($markRecord) {
                                                    $t1 = $markRecord->t1 ?: 0;
                                                    $t2 = $markRecord->t2 ?: 0;
                                                    $exm = $markRecord->exm ?: 0;
                                                    
                                                    $values = [$t1, $t2, $exm];
                                                    $sum = array_sum($values);
                                                    $count = count(array_filter($values, fn($value) => $value > 0));
                                                    
                                                    if ($count > 0) {
                                                        $subjectAverage = $sum / $count;
                                                        $totalPoints += ($subjectAverage * $subject->coef);
                                                        $usedCoef += $subject->coef;
                                                    }
                                                }
                                            }
                                        }
                                        
                                        $average = $usedCoef > 0 ? $totalPoints / $usedCoef : 0;
                                        
                                        // Determine mention
                                        $mention = '';
                                        $mentionClass = 'mention-fail';
                                        if ($average >= 16) {
                                            $mention = 'Très Bien';
                                            $mentionClass = 'mention-excellent';
                                        } elseif ($average >= 14) {
                                            $mention = 'Bien';
                                            $mentionClass = 'mention-good';
                                        } elseif ($average >= 12) {
                                            $mention = 'Assez Bien';
                                            $mentionClass = 'mention-fair';
                                        } elseif ($average >= 10) {
                                            $mention = 'Passable';
                                            $mentionClass = 'mention-pass';
                                        } else {
                                            $mention = 'Insuffisant';
                                            $mentionClass = 'mention-fail';
                                        }
                                        
                                        // Position badge class
                                        $positionClass = 'position-other';
                                        if ($position == 1) $positionClass = 'position-1';
                                        elseif ($position == 2) $positionClass = 'position-2';
                                        elseif ($position == 3) $positionClass = 'position-3';
                                        elseif ($position <= 10) $positionClass = 'position-top10';
                                    @endphp
                                    
                                    <tr data-student-name="{{ strtolower($student->user->name ?? '') }}" data-average="{{ $average }}" data-mention="{{ $mention }}">
                                        <td>
                                            <span class="position-badge {{ $positionClass }}">{{ $position }}</span>
                                        </td>
                                        <td>
                                            <div class="student-info">
                                                <div class="student-avatar">
                                                    {{ strtoupper(substr($student->user->name ?? 'XX', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $student->user->name ?? 'N/A' }}</strong>
                                                    @if($position <= 3)
                                                        <br><small class="text-muted">
                                                            @if($position == 1) 🥇 Champion
                                                            @elseif($position == 2) 🥈 Vice-Champion
                                                            @elseif($position == 3) 🥉 Troisième
                                                            @endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        {{-- Subject marks --}}
                                        @if($subjects && $subjects->count() > 0)
                                            @foreach($subjects as $subject)
                                                @php
                                                    $markRecord = $marks ? $marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first() : null;
                                                    $mark = '-';
                                                    $markClass = '';
                                                    $formula = '';
                                                    
                                                    if ($markRecord) {
                                                        $t1 = $markRecord->t1 ?: 0;
                                                        $t2 = $markRecord->t2 ?: 0;
                                                        $exm = $markRecord->exm ?: 0;
                                                        
                                                        $values = [$t1, $t2, $exm];
                                                        $sum = array_sum($values);
                                                        $count = count(array_filter($values, fn($value) => $value > 0));
                                                        
                                                        if ($count > 0) {
                                                            $mark = $sum / $count;
                                                            
                                                            // Create formula explanation
                                                            $valuesText = [];
                                                            if ($t1 > 0) $valuesText[] = 'DS1: ' . $t1;
                                                            if ($t2 > 0) $valuesText[] = 'DS2: ' . $t2;
                                                            if ($exm > 0) $valuesText[] = 'Exam: ' . $exm;
                                                            
                                                            $formula = '(' . implode(' + ', $valuesText) . ') / ' . $count . ' = ' . number_format($mark, 2);
                                                            $formula .= ' × ' . $subject->coef . ' = ' . number_format($mark * $subject->coef, 2);
                                                            
                                                            // Mark color coding
                                                            if ($mark >= 16) $markClass = 'mark-excellent';
                                                            elseif ($mark >= 14) $markClass = 'mark-good';
                                                            elseif ($mark >= 10) $markClass = 'mark-average';
                                                            else $markClass = 'mark-poor';
                                                        }
                                                    }
                                                @endphp
                                                
                                                <td class="{{ $markClass }}" title="{{ $formula }}" data-toggle="tooltip">
                                                    @if($mark !== '-')
                                                        {{ number_format($mark, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @endif
                                        
                                        <td class="font-weight-bold">{{ number_format($totalPoints, 2) }}</td>
                                        <td class="font-weight-bold {{ $average >= 10 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($average, 2) }}/20
                                        </td>
                                        <td>
                                            <span class="mention-badge {{ $mentionClass }}">{{ $mention }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center text-muted py-4">
                                        <i class="icon-info mr-2"></i>Aucun étudiant trouvé
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
        @elseif($selected && $studentCount == 0)
            {{-- No data found message --}}
            <div class="tabulation-card">
                <div class="card-body text-center py-5">
                    <h5 class="text-muted"><i class="icon-info mr-2"></i>Aucune donnée trouvée</h5>
                    <p>Aucun résultat trouvé pour la sélection actuelle.</p>
                    <div class="mt-3">
                        <strong>Paramètres actuels :</strong><br>
                        Examen : {{ $ex->name ?? 'Non spécifié' }}<br>
                        Classe : {{ $my_class->name ?? 'Non spécifiée' }}<br>
                        Section : {{ $section->name ?? 'Non spécifiée' }}<br>
                        Année : {{ $year }}
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            Vérifiez que des notes ont été saisies pour cette combinaison d'examen, classe et section.
                        </small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips for formula display
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
            container: 'body'
        });
    }
    
    // Filter functionality
    const studentFilter = document.getElementById('studentFilter');
    const minGrade = document.getElementById('minGrade');
    const maxGrade = document.getElementById('maxGrade');
    const mentionFilter = document.getElementById('mentionFilter');
    
    function applyFilters() {
        const rows = document.querySelectorAll('#rankingTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (row.cells.length <= 1) return; // Skip empty rows
            
            let show = true;
            const studentName = row.getAttribute('data-student-name') || '';
            const average = parseFloat(row.getAttribute('data-average')) || 0;
            const mention = row.getAttribute('data-mention') || '';
            
            // Student name filter
            if (studentFilter && studentFilter.value && !studentName.includes(studentFilter.value.toLowerCase())) {
                show = false;
            }
            
            // Grade filters
            if (minGrade && minGrade.value && average < parseFloat(minGrade.value)) {
                show = false;
            }
            if (maxGrade && maxGrade.value && average > parseFloat(maxGrade.value)) {
                show = false;
            }
            
            // Mention filter
            if (mentionFilter && mentionFilter.value && mention !== mentionFilter.value) {
                show = false;
            }
            
            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        
        console.log(`Filter applied: ${visibleCount} students visible`);
    }
    
    // Add event listeners
    if (studentFilter) studentFilter.addEventListener('input', applyFilters);
    if (minGrade) minGrade.addEventListener('change', applyFilters);
    if (maxGrade) maxGrade.addEventListener('change', applyFilters);
    if (mentionFilter) mentionFilter.addEventListener('change', applyFilters);
    
    // Reset filters function
    window.resetFilters = function() {
        if (studentFilter) studentFilter.value = '';
        if (minGrade) minGrade.value = '';
        if (maxGrade) maxGrade.value = '';
        if (mentionFilter) mentionFilter.value = '';
        applyFilters();
    };
    
    // Get class sections function for dynamic section loading
    window.getClassSections = function(classId) {
        console.log('Selected class ID:', classId);
        // This would typically make an AJAX request to get sections
    };
});
</script>

@endsection

            {{-- Filters --}}
            <div class="card no-print mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" id="studentFilter" class="form-control" placeholder="Rechercher un étudiant...">
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="minGrade" class="form-control" placeholder="Note min" step="0.1" min="0" max="20">
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="maxGrade" class="form-control" placeholder="Note max" step="0.1" min="0" max="20">
                        </div>
                        <div class="col-md-2">
                            <select id="mentionFilter" class="form-control">
                                <option value="">Toutes mentions</option>
                                <option value="Très Bien">Très Bien</option>
                                <option value="Bien">Bien</option>
                                <option value="Assez Bien">Assez Bien</option>
                                <option value="Passable">Passable</option>
                                <option value="Insuffisant">Insuffisant</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button onclick="resetFilters()" class="btn btn-secondary mr-2">
                                <i class="icon-refresh mr-1"></i>Réinitialiser
                            </button>
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="icon-printer mr-1"></i>Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>