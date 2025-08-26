@extends('layouts.master')
@section('page_title', 'Feuille de tabulation - Version améliorée')
@section('content')

{{-- Initialize all variables at the top to prevent undefined errors --}}
@php
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
/* Styles pour le podium et le classement */
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .podium-card {
        margin-bottom: 20px;
    }
    
    .student-name-cell {
        min-width: 150px;
    }
    
    .stats-value {
        font-size: 1.5rem;
    }
    
    .ranking-table {
        font-size: 12px;
    }
}
</style>
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
                
                {{-- Advanced Filters --}}
                @if($selected)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card border-left-info">
                            <div class="card-header bg-light collapsed" data-toggle="collapse" data-target="#advancedFilters">
                                <h6 class="mb-0">
                                    <i class="icon-filter mr-2"></i>Filtres avancés
                                    <i class="icon-chevron-down float-right"></i>
                                </h6>
                            </div>
                            <div id="advancedFilters" class="collapse">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Rechercher un étudiant :</label>
                                                <input type="text" id="studentSearch" class="form-control" placeholder="Nom de l'étudiant...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Moyenne min :</label>
                                                <input type="number" id="minAverage" class="form-control" step="0.1" min="0" max="20" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Moyenne max :</label>
                                                <input type="number" id="maxAverage" class="form-control" step="0.1" min="0" max="20" placeholder="20">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Mention :</label>
                                                <select id="mentionFilter" class="form-control">
                                                    <option value="">Toutes</option>
                                                    <option value="Très Bien">Très Bien</option>
                                                    <option value="Bien">Bien</option>
                                                    <option value="Assez Bien">Assez Bien</option>
                                                    <option value="Passable">Passable</option>
                                                    <option value="Insuffisant">Insuffisant</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="btn-group d-block">
                                                    <button type="button" class="btn btn-info" onclick="applyFilters()">
                                                        <i class="icon-filter"></i> Appliquer
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                                        <i class="icon-reset"></i> Réinitialiser
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </form>
        </div>
</div>

    {{-- Diagnostic message when parameters are set but no data is found --}}
    @if(request()->segment(3) && request()->segment(4) && request()->segment(5) && !$selected)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="icon-info22 mr-2"></i>
                            Aucun résultat trouvé pour cette sélection
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <h6><i class="icon-warning mr-2"></i>Paramètres actuels :</h6>
                            <ul class="mb-0">
                                <li><strong>Examen ID :</strong> {{ request()->segment(3) }} - {{ $ex->name ?? 'Non trouvé' }}</li>
                                <li><strong>Classe ID :</strong> {{ request()->segment(4) }} - {{ $my_class->name ?? 'Non trouvée' }}</li>
                                <li><strong>Section ID :</strong> {{ request()->segment(5) }} - {{ $section->name ?? 'Non trouvée' }}</li>
                                <li><strong>Année scolaire :</strong> {{ $year ?? 'Non spécifiée' }}</li>
                            </ul>
                        </div>
                        
                        <h6><i class="icon-lightbulb mr-2"></i>Causes possibles :</h6>
                        <ul>
                            <li>Aucune note n'a été enregistrée pour cette combinaison examen/classe/section</li>
                            <li>Les notes existent mais pour une année scolaire différente</li>
                            <li>L'examen, la classe ou la section sélectionnée n'existe pas</li>
                            <li>Les étudiants n'ont pas encore été inscrits pour cet examen</li>
                        </ul>
                        
                        <h6><i class="icon-gear mr-2"></i>Solutions suggérées :</h6>
                        <ol>
                            <li>Vérifiez que des notes ont été saisies pour cet examen</li>
                            <li>Assurez-vous que l'année scolaire est correcte</li>
                            <li>Vérifiez que les étudiants sont bien inscrits dans cette classe/section</li>
                            <li>Contactez l'administrateur si le problème persiste</li>
                        </ol>
                        
                        <div class="mt-3">
                            <a href="{{ route('marks.tabulation') }}" class="btn btn-primary">
                                <i class="icon-arrow-left8 mr-2"></i>Refaire une sélection
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Si une sélection a été faite --}}
    @if($selected && isset($students) && $students->count() > 0)
        {{-- Statistiques du classement --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card fade-in">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="icon-trophy mr-2"></i>
                            Statistiques du classement - {{ $my_class->name??'' }}{{ isset($section) && $section ? ' '.$section->name : '' }} - {{ $ex->name??'' }} ({{ $year??'' }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $studentCount = (isset($students) && $students) ? (is_array($students) ? count($students) : $students->count()) : 0;
                            $averagesCollection = collect();
                            
                            // Only process if we have students and subjects
                            if($studentCount > 0 && isset($subjects) && $subjects && $subjects->count() > 0) {
                                // Pré-calculer les totaux des coefficients pour éviter les répétitions
                                $totalCoef = $subjects->sum('coef');
                                
                                foreach($students as $student) {
                                    $totalPoints = 0;
                                    $usedCoef = 0;
                                    
                                    foreach($subjects as $subject) {
                                        $markRecord = isset($marks) && $marks ? $marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first() : null;
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
                            
                            $classAverage = $averagesCollection->count() > 0 ? ($averagesCollection->sum() / $averagesCollection->count()) : 0;
                            $topScore = $averagesCollection->count() > 0 ? $averagesCollection->max() : 0;
                            $lowestScore = $averagesCollection->count() > 0 ? $averagesCollection->min() : 0;
                            $passCount = $averagesCollection->filter(function($avg) { return $avg >= 10; })->count();
                            $passRate = $studentCount > 0 ? ($passCount / $studentCount) * 100 : 0;
                        @endphp
                        
                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="border-right h-100 d-flex flex-column justify-content-center">
                                    <h5 class="stats-label text-info mb-1">Étudiants</h5>
                                    <h3 class="stats-value text-info mb-1">{{ $studentCount }}</h3>
                                    <small class="text-muted">Total inscrits</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border-right h-100 d-flex flex-column justify-content-center">
                                    <h5 class="stats-label text-primary mb-1">Moyenne classe</h5>
                                    <h3 class="stats-value text-primary mb-1" title="Formule: Somme des moyennes / Nombre d'étudiants" data-toggle="tooltip">{{ number_format($classAverage, 2) }}/20</h3>
                                    <small class="text-muted">Générale</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border-right h-100 d-flex flex-column justify-content-center">
                                    <h5 class="stats-label text-success mb-1">Meilleur score</h5>
                                    <h3 class="stats-value text-success mb-1">{{ number_format($topScore, 2) }}/20</h3>
                                    <small class="text-muted">Maximum</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border-right h-100 d-flex flex-column justify-content-center">
                                    <h5 class="stats-label text-warning mb-1">Score minimum</h5>
                                    <h3 class="stats-value text-warning mb-1">{{ number_format($lowestScore, 2) }}/20</h3>
                                    <small class="text-muted">Minimum</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border-right h-100 d-flex flex-column justify-content-center">
                                    <h5 class="stats-label text-success mb-1">Admis</h5>
                                    <h3 class="stats-value text-success mb-1">{{ $passCount }}</h3>
                                    <small class="text-muted">≥ 10/20</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="h-100 d-flex flex-column justify-content-center">
                                    <h5 class="stats-label text-info mb-1">Taux de réussite</h5>
                                    <h3 class="stats-value text-info mb-1">{{ number_format($passRate, 1) }}%</h3>
                                    <small class="text-muted">Classe</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Podium des 3 premiers --}}
        @php
            $sortedStudentsForPodium = collect();
            if(isset($students) && $students && $students->count() > 0 && isset($exr) && $exr) {
                $sortedStudentsForPodium = is_array($students) 
                    ? collect($students)->sortBy(function($student) use ($exr) {
                        $examRecord = $exr->where('student_id', $student->user_id)->first();
                        return $examRecord ? $examRecord->pos : 999;
                    })->take(3)
                    : $students->sortBy(function($student) use ($exr) {
                        $examRecord = $exr->where('student_id', $student->user_id)->first();
                        return $examRecord ? $examRecord->pos : 999;
                    })->take(3);
            }
        @endphp
        
        @if($sortedStudentsForPodium->count() >= 3)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card fade-in">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="icon-trophy mr-2"></i>
                            Podium du classement
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            {{-- 2ème place --}}
                            @if($sortedStudentsForPodium->count() >= 2)
                            @php $second = $sortedStudentsForPodium->skip(1)->first(); @endphp
                            <div class="col-md-3">
                                <div class="text-center podium-card silver-podium">
                                    <div class="podium-medal">
                                        <i class="icon-medal" style="font-size: 3rem; color: #C0C0C0;"></i>
                                        <div class="position-badge silver">2</div>
                                    </div>
                                    <h5 class="mt-3">{{ $second->user->name }}</h5>
                                    @php
                                        $secondExamRecord = $exr->where('student_id', $second->user_id)->first();
                                        $totalCoef = $subjects->sum('coef');
                                        $secondAvg = $secondExamRecord && $totalCoef > 0 ? $secondExamRecord->total / $totalCoef : 0;
                                    @endphp
                                    <p class="mb-1"><strong>{{ number_format($secondAvg, 2) }}/20</strong></p>
                                    <small class="text-muted">2ème place</small>
                                </div>
                            </div>
                            @endif
                            
                            {{-- 1ère place --}}
                            @if($sortedStudentsForPodium->count() >= 1)
                            @php $first = $sortedStudentsForPodium->first(); @endphp
                            <div class="col-md-3">
                                <div class="text-center podium-card gold-podium">
                                    <div class="podium-medal">
                                        <i class="icon-trophy" style="font-size: 4rem; color: #FFD700;"></i>
                                        <div class="position-badge gold">1</div>
                                    </div>
                                    <h4 class="mt-3 font-weight-bold">{{ $first->user->name }}</h4>
                                    @php
                                        $firstExamRecord = $exr->where('student_id', $first->user_id)->first();
                                        $firstAvg = $firstExamRecord && $totalCoef > 0 ? $firstExamRecord->total / $totalCoef : 0;
                                    @endphp
                                    <p class="mb-1"><strong class="text-success">{{ number_format($firstAvg, 2) }}/20</strong></p>
                                    <small class="text-success font-weight-bold">🏆 CHAMPION 🏆</small>
                                </div>
                            </div>
                            @endif
                            
                            {{-- 3ème place --}}
                            @if($sortedStudentsForPodium->count() >= 3)
                            @php $third = $sortedStudentsForPodium->skip(2)->first(); @endphp
                            <div class="col-md-3">
                                <div class="text-center podium-card bronze-podium">
                                    <div class="podium-medal">
                                        <i class="icon-medal" style="font-size: 3rem; color: #CD7F32;"></i>
                                        <div class="position-badge bronze">3</div>
                                    </div>
                                    <h5 class="mt-3">{{ $third->user->name }}</h5>
                                    @php
                                        $thirdExamRecord = $exr->where('student_id', $third->user_id)->first();
                                        $thirdAvg = $thirdExamRecord && $totalCoef > 0 ? $thirdExamRecord->total / $totalCoef : 0;
                                    @endphp
                                    <p class="mb-1"><strong>{{ number_format($thirdAvg, 2) }}/20</strong></p>
                                    <small class="text-muted">3ème place</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tableau de classement complet --}}
        <div class="card fade-in mt-3">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">
                            <i class="icon-list-numbered mr-2 text-primary"></i>
                            Classement complet - {{ $my_class->name.' '.$section->name.' - '.$ex->name.' ('.$year.')' }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <span class="badge badge-info">{{ (isset($students) && $students) ? (is_array($students) ? count($students) : $students->count()) : 0 }} étudiants classés</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- En-tête pour impression seulement --}}
                <div class="school-header d-none">
                    <div class="school-logo-container">
                        <img src="{{ asset('global_assets/images/logo_light.png') }}" alt="Logo école" class="school-logo">
                    </div>
                    <div class="school-info">
                        <h4 class="m-0 font-weight-bold">{{ config('app.name') }}</h4>
                        <p class="m-0">Centre de Formation Professionnelle</p>
                        <p class="m-0">Tel: 038 34 921 09</p>
                        <p class="m-0">Année Scolaire: {{ $year }}</p>
                    </div>
                    <div class="school-logo-placeholder"></div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover ranking-table">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" width="80">
                                    <i class="icon-trophy mr-1"></i> Rang
                                </th>
                                <th class="font-weight-bold">
                                    <i class="icon-user mr-1"></i> Étudiant
                                </th>
                                @foreach($subjects as $sub)
                                    <th title="{{ $sub->name }}" class="text-center font-weight-bold" width="80">
                                        <div>{{ strtoupper($sub->slug ?: substr($sub->name, 0, 4)) }}</div>
                                        <small class="d-block text-muted">({{ $sub->coef }})</small>
                                    </th>
                                @endforeach
                                <th class="text-center bg-danger text-white font-weight-bold" width="100">
                                    <i class="icon-calculator mr-1"></i> Total
                                </th>
                                <th class="text-center bg-primary text-white font-weight-bold" width="100">
                                    <i class="icon-statistics mr-1"></i> Moyenne
                                </th>
                                <th class="text-center bg-success text-white font-weight-bold" width="120">
                                    <i class="icon-trophy mr-1"></i> Mention
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Sort the $students collection based on position
                                $sortedStudents = collect();
                                if(isset($students) && $students && $students->count() > 0 && isset($exr) && $exr) {
                                    $sortedStudents = is_array($students) 
                                        ? collect($students)->sortBy(function($student) use ($exr) {
                                            $examRecord = $exr->where('student_id', $student->user_id)->first();
                                            return $examRecord ? $examRecord->pos : 999;
                                        })
                                        : $students->sortBy(function($student) use ($exr) {
                                            $examRecord = $exr->where('student_id', $student->user_id)->first();
                                            return $examRecord ? $examRecord->pos : 999;
                                        });
                                }
                                
                                // Pré-calculer les totaux des coefficients pour éviter les répétitions
                                $totalCoef = (isset($subjects) && $subjects) ? $subjects->sum('coef') : 0;
                                
                                // Pré-calculer les statistiques des étudiants
                                $studentStats = [];
                                if($sortedStudents->count() > 0 && isset($subjects) && $subjects && $subjects->count() > 0) {
                                    foreach($sortedStudents as $student) {
                                        $examRecord = isset($exr) && $exr ? $exr->where('student_id', $student->user_id)->first() : null;
                                        $position = $examRecord ? $examRecord->pos : 999;
                                        
                                        $totalPoints = 0;
                                        $usedCoef = 0;
                                        
                                        foreach($subjects as $subject) {
                                            $markRecord = isset($marks) && $marks ? $marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first() : null;
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
                                    
                                    $average = $usedCoef > 0 ? $totalPoints / $usedCoef : 0;
                                    
                                    // Déterminer la mention
                                    $mention = '';
                                    $mentionBadge = 'badge-secondary';
                                    if ($average >= 16) {
                                        $mention = 'Très Bien';
                                        $mentionBadge = 'badge-success';
                                    } elseif ($average >= 14) {
                                        $mention = 'Bien';
                                        $mentionBadge = 'badge-primary';
                                    } elseif ($average >= 12) {
                                        $mention = 'Assez Bien';
                                        $mentionBadge = 'badge-info';
                                    } elseif ($average >= 10) {
                                        $mention = 'Passable';
                                        $mentionBadge = 'badge-warning';
                                    } else {
                                        $mention = 'Insuffisant';
                                        $mentionBadge = 'badge-danger';
                                    }
                                    
                                    // Déterminer la classe de ligne et l'icône
                                    $rowClass = '';
                                    $positionIcon = '';
                                    $positionBadgeClass = 'badge-secondary';
                                    
                                    if ($position == 1) {
                                        $rowClass = 'table-success border-success';
                                        $positionIcon = '<i class="icon-trophy text-warning mr-2"></i>';
                                        $positionBadgeClass = 'badge-success';
                                    } elseif ($position == 2) {
                                        $rowClass = 'table-info border-info';
                                        $positionIcon = '<i class="icon-medal text-info mr-2"></i>';
                                        $positionBadgeClass = 'badge-info';
                                    } elseif ($position == 3) {
                                        $rowClass = 'table-warning border-warning';
                                        $positionIcon = '<i class="icon-medal text-warning mr-2"></i>';
                                        $positionBadgeClass = 'badge-warning';
                                    } elseif ($position <= 5) {
                                        $rowClass = 'table-light border-primary';
                                        $positionBadgeClass = 'badge-primary';
                                    } elseif ($position <= 10) {
                                        $positionBadgeClass = 'badge-info';
                                    }
                                    
                                    $studentStats[$student->user_id] = [
                                        'position' => $position,
                                        'totalPoints' => $totalPoints,
                                        'average' => $average,
                                        'mention' => $mention,
                                        'mentionBadge' => $mentionBadge,
                                        'rowClass' => $rowClass,
                                        'positionIcon' => $positionIcon,
                                        'positionBadgeClass' => $positionBadgeClass
                                    ];
                                }
                            @endphp

                            @if($sortedStudents->count() > 0)
                                @foreach($sortedStudents as $s)
                                    @php
                                        $stats = isset($studentStats[$s->user_id]) ? $studentStats[$s->user_id] : [
                                            'position' => 999,
                                            'totalPoints' => 0,
                                            'average' => 0,
                                            'mention' => '-',
                                            'mentionBadge' => 'badge-secondary',
                                            'rowClass' => '',
                                            'positionIcon' => '',
                                            'positionBadgeClass' => 'badge-secondary'
                                        ];
                                        $position = $stats['position'];
                                        $totalPoints = $stats['totalPoints'];
                                        $average = $stats['average'];
                                        $mention = $stats['mention'];
                                        $mentionBadge = $stats['mentionBadge'];
                                        $rowClass = $stats['rowClass'];
                                        $positionIcon = $stats['positionIcon'];
                                        $positionBadgeClass = $stats['positionBadgeClass'];
                                    @endphp
                                
                                <tr class="{{ $rowClass }}">
                                    {{-- Colonne Rang --}}
                                    <td class="text-center font-weight-bold" style="vertical-align: middle;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            {!! $positionIcon !!}
                                            <span class="badge {{ $positionBadgeClass }} position-badge-large">
                                                {{ $position }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    {{-- Nom de l'étudiant --}}
                                    <td class="font-weight-semibold student-name-cell">
                                        <div class="d-flex align-items-center">
                                            <div class="student-avatar mr-3">
                                                <div class="avatar-circle">
                                                    {{ strtoupper(substr($s->user->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="student-name">{{ $s->user->name }}</div>
                                                @if($position <= 3)
                                                    <small class="text-muted">
                                                        @if($position == 1) 🥇 Champion
                                                        @elseif($position == 2) 🥈 Vice-champion  
                                                        @elseif($position == 3) 🥉 Troisième
                                                        @endif
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- Notes par matière --}}
                                    @if(isset($subjects) && $subjects && $subjects->count() > 0)
                                        @foreach($subjects as $sub)
                                            @php
                                                // Récupérer la note depuis les marks en utilisant les composants t1, t2, exm
                                                $markRecord = (isset($marks) && $marks) ? $marks->where('student_id', $s->user_id)->where('subject_id', $sub->id)->first() : null;
                                                $mark = '-';
                                                
                                                if ($markRecord) {
                                                    // Calculer la moyenne à partir de t1, t2, exm (même logique que show/print)
                                                    $t1 = $markRecord->t1 ?: 0;
                                                    $t2 = $markRecord->t2 ?: 0;
                                                    $exm = $markRecord->exm ?: 0;
                                                    
                                                    // Formule: (Somme des notes) / (Nombre de notes présentes)
                                                    $values = [$t1, $t2, $exm];
                                                    $sum = array_sum($values);
                                                    $count = count(array_filter($values, fn($value) => $value > 0));
                                                    
                                                    $formula = '';
                                                    $valuesText = [];
                                                    
                                                    if ($t1 > 0) $valuesText[] = 'DS1: ' . $t1;
                                                    if ($t2 > 0) $valuesText[] = 'DS2: ' . $t2;
                                                    if ($exm > 0) $valuesText[] = 'Exam: ' . $exm;
                                                    
                                                    if (count($valuesText) > 0) {
                                                        $formula = '(' . implode(' + ', $valuesText) . ') / ' . $count . ' = ';
                                                    }
                                                    
                                                    if ($count > 0) {
                                                        $mark = $sum / $count; // Moyenne sans coefficient pour l'affichage
                                                        // Ajouter la formule complète
                                                        $formula .= number_format($mark, 2) . ' × ' . $sub->coef . ' = ' . number_format($mark * $sub->coef, 2);
                                                    }
                                                }
                                                
                                                $markClass = 'text-muted';
                                                if ($mark != '-' && is_numeric($mark)) {
                                                    if ($mark >= 16) {
                                                        $markClass = 'text-success font-weight-bold';
                                                    } elseif ($mark >= 14) {
                                                        $markClass = 'text-primary font-weight-bold';
                                                    } elseif ($mark >= 12) {
                                                        $markClass = 'text-info font-weight-semibold';
                                                    } elseif ($mark >= 10) {
                                                        $markClass = 'text-warning';
                                                    } else {
                                                        $markClass = 'text-danger';
                                                    }
                                                }
                                            @endphp
                                            <td class="text-center {{ $markClass }}" title="{{ $formula ?? 'Aucune note' }}" data-toggle="tooltip" data-html="true">
                                                @if($mark != '-' && is_numeric($mark))
                                                    {{ number_format($mark, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif

                                    {{-- Total des points --}}
                                    <td class="text-center font-weight-bold">
                                        <span class="badge badge-danger badge-lg">{{ number_format($totalPoints, 2) }}</span>
                                    </td>
                                    
                                    {{-- Moyenne --}}
                                    <td class="text-center font-weight-bold">
                                        @php
                                            $avgBadgeClass = 'badge-secondary';
                                            if ($average >= 16) {
                                                $avgBadgeClass = 'badge-success';
                                            } elseif ($average >= 14) {
                                                $avgBadgeClass = 'badge-primary';
                                            } elseif ($average >= 10) {
                                                $avgBadgeClass = 'badge-info';
                                            } elseif ($average < 10 && $average > 0) {
                                                $avgBadgeClass = 'badge-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $avgBadgeClass }} badge-lg">{{ $average > 0 ? number_format($average, 2) : '-' }}</span>
                                    </td>
                                    
                                    {{-- Mention --}}
                                    <td class="text-center font-weight-bold">
                                        <span class="badge {{ $mentionBadge }} badge-lg">{{ $mention ?: '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center text-muted py-4">
                                        <i class="icon-info22 mr-2"></i>
                                        Aucun étudiant trouvé pour ce classement
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Actions et statistiques --}}
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="icon-statistics mr-2"></i>Analyse du classement</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    // Réutiliser les moyennes pré-calculées pour éviter les calculs redondants
                                    $excellentCount = $averagesCollection->filter(function($avg) { return $avg >= 16; })->count();
                                    $goodCount = $averagesCollection->filter(function($avg) { return $avg >= 14 && $avg < 16; })->count();
                                    $averageCount = $averagesCollection->filter(function($avg) { return $avg >= 10 && $avg < 14; })->count();
                                    $poorCount = $averagesCollection->filter(function($avg) { return $avg < 10; })->count();
                                @endphp
                                
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-success badge-lg mb-2">{{ $excellentCount }}</span>
                                            <small class="text-muted">Excellent<br>(≥16)</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-primary badge-lg mb-2">{{ $goodCount }}</span>
                                            <small class="text-muted">Bien<br>(14-15.9)</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-info badge-lg mb-2">{{ $averageCount }}</span>
                                            <small class="text-muted">Passable<br>(10-13.9)</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-danger badge-lg mb-2">{{ $poorCount }}</span>
                                            <small class="text-muted">Insuffisant<br>(<10)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="icon-printer mr-2"></i>Actions</h6>
                                        </div>
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <a target="_blank" href="{{ route('marks.print_tabulation', [$exam_id, $my_class_id, $section_id]) }}" class="btn btn-danger btn-lg mb-3">
                                                <i class="icon-printer mr-2"></i> Imprimer le classement
                                            </a>
                                            
                                            <button onclick="exportToExcel()" class="btn btn-success btn-lg mb-3">
                                                <i class="icon-file-excel mr-2"></i> Exporter en Excel
                                            </button>
                                            
                                            <a href="{{ route('marks.tabulation', [$exam_id, $my_class_id, $section_id]) }}?export=pdf" class="btn btn-info btn-lg mb-3">
                                                <i class="icon-file-pdf mr-2"></i> Exporter en PDF
                                            </a>
                                            
                                            <button onclick="shareResults()" class="btn btn-primary btn-lg">
                                                <i class="icon-share2 mr-2"></i> Partager les résultats
                                            </button>

                                            <div class="mt-3 text-center">
                                                <small class="text-muted">Format d'export: 
                                                    <span class="badge badge-light" title="Formule: Moyenne (/20) × Coefficient = Total" data-toggle="tooltip">Moyenne × Coefficient</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                </div>
            </div>
        </div>
    
    <script>
        function exportToExcel() {
            // Fonction pour exporter en Excel
            let data = [];
            let headers = ['Rang', 'Étudiant'];
            
            // Récupérer les en-têtes des matières avec leurs coefficients
            $('thead th').slice(2, -3).each(function() {
                let subjectName = $(this).attr('title') || $(this).text().trim();
                let coef = $(this).find('.text-muted').text().trim();
                headers.push(subjectName + ' ' + coef);
            });
            headers.push('Total', 'Moyenne', 'Mention');
            data.push(headers);
            
            // Récupérer les données des étudiants
            $('tbody tr:visible').each(function() {
                let row = [];
                $(this).find('td').each(function(index) {
                    if(index === 1) {
                        // Nom de l'étudiant (nettoyer le HTML)
                        row.push($(this).find('.student-name').text().trim());
                    } else if(index >= 2 && index < $(this).parent().find('td').length - 3) {
                        // Notes des matières (avec formule en info-bulle)
                        let value = $(this).text().trim();
                        let formula = $(this).attr('title') || '';
                        row.push(value !== '-' ? value : '');
                    } else {
                        row.push($(this).text().trim().replace(/\s+/g, ' '));
                    }
                });
                if(row.length > 0) data.push(row);
            });
            
            // Ajouter des métadonnées et statistiques
            let metaData = [
                ['Feuille de tabulation - {{ $my_class->name ?? "" }} {{ $section->name ?? "" }}'],
                ['Examen: {{ $ex->name ?? "" }} - Année: {{ $year ?? "" }}'],
                ['Généré le: ' + new Date().toLocaleString()],
                ['Moyenne de classe: {{ number_format($classAverage ?? 0, 2) }}/20'],
                ['Total élèves: {{ (isset($students) && $students) ? (is_array($students) ? count($students) : $students->count()) : 0 }}'],
                [''] // ligne vide avant les données
            ];
            
            data = [...metaData, ...data];
            
            // Créer et télécharger le fichier
            let csvContent = data.map(row => row.join('\t')).join('\n'); // Using tabs for better Excel compatibility
            let blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8' }); // BOM for Excel UTF-8
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'classement_{{ $my_class->name ?? "classe" }}_{{ $ex->name ?? "examen" }}.xls'); // .xls extension for Excel
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            // Notification de succès
            swal('Succès!', 'Le fichier Excel a été téléchargé avec succès.', 'success');
        }
        
        function shareResults() {
            if (navigator.share) {
                navigator.share({
                    title: 'Résultats de classement - {{ $my_class->name ?? "" }} {{ $section->name ?? "" }}',
                    text: 'Consultez les résultats de l\'examen {{ $ex->name ?? "" }} pour la classe {{ $my_class->name ?? "" }}.',
                    url: window.location.href
                });
            } else {
                // Fallback - copier l'URL
                navigator.clipboard.writeText(window.location.href).then(function() {
                    swal('Lien copié!', 'Le lien de partage a été copié dans le presse-papiers.', 'success');
                });
            }
        }
        
        function applyFilters() {
            let studentSearch = $('#studentSearch').val().toLowerCase();
            let minAverage = parseFloat($('#minAverage').val()) || 0;
            let maxAverage = parseFloat($('#maxAverage').val()) || 20;
            let mentionFilter = $('#mentionFilter').val();
            
            let visibleCount = 0;
            
            $('.ranking-table tbody tr').each(function() {
                let show = true;
                let $row = $(this);
                
                // Filtre par nom d'étudiant
                if (studentSearch) {
                    let studentName = $row.find('.student-name').text().toLowerCase();
                    if (!studentName.includes(studentSearch)) {
                        show = false;
                    }
                }
                
                // Filtre par moyenne
                let averageText = $row.find('td').eq(-2).find('.badge').text();
                let average = parseFloat(averageText) || 0;
                if (average < minAverage || average > maxAverage) {
                    show = false;
                }
                
                // Filtre par mention
                if (mentionFilter) {
                    let mention = $row.find('td').last().find('.badge').text();
                    if (mention !== mentionFilter) {
                        show = false;
                    }
                }
                
                if (show) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });
            
            // Mettre à jour le compteur
            $('.card-header .badge-info').text(visibleCount + ' étudiants affichés');
            
            // Animation pour indiquer que le filtre a été appliqué
            $('.ranking-table').addClass('filter-applied');
            setTimeout(() => $('.ranking-table').removeClass('filter-applied'), 1000);
        }
        
        function resetFilters() {
            $('#studentSearch').val('');
            $('#minAverage').val('');
            $('#maxAverage').val('');
            $('#mentionFilter').val('');
            
            $('.ranking-table tbody tr').show();
            $('.card-header .badge-info').text('{{ (isset($students) && $students) ? (is_array($students) ? count($students) : $students->count()) : 0 }} étudiants classés');
        }
        
        $(document).ready(function() {
            // Activation des tooltips Bootstrap pour la vérification des calculs
            $('[data-toggle="tooltip"]').tooltip({
                html: true,
                container: 'body',
                boundary: 'window'
            });

            // Recherche en temps réel
            $('#studentSearch').on('input', function() {
                if ($(this).val().length > 2 || $(this).val().length === 0) {
                    applyFilters();
                }
            });
            
            // Filtrage en temps réel pour les moyennes
            $('#minAverage, #maxAverage').on('change', function() {
                applyFilters();
            });

            // Filtrage immédiat pour le filtre de mention
            $('#mentionFilter').on('change', function() {
                applyFilters();
            });
            
            // Animation d'entrée pour les lignes
            $('.ranking-table tbody tr').each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
            });

            // Débug des calculs - Afficher les formules au survol
            $('.ranking-table tbody td').each(function() {
                let formula = $(this).attr('title');
                if (formula && formula !== '') {
                    $(this).css('cursor', 'help');
                }
            });
        });
    </script>
    
    <style>
        .filter-applied {
            animation: pulse 0.5s ease-in-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .ranking-table tbody tr {
            animation: fadeInUp 0.6s ease-out both;
        }
        
        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .stats-value {
                font-size: 1.2rem;
            }
            
            .podium-card {
                padding: 15px;
            }
            
            .ranking-table {
                font-size: 11px;
            }

            .ranking-table th {
                white-space: normal;
                word-break: break-word;
            }
            
            .btn-lg {
                padding: 8px 16px;
                font-size: 14px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .student-name-cell {
                min-width: 120px;
            }

            /* Stacking cards on mobile */
            .podium-card {
                margin-bottom: 15px;
            }

            /* Improve filter layout on mobile */
            #advancedFilters .form-group {
                margin-bottom: 10px;
            }
        }
        
        /* Print styles for ranking */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            
            .ranking-table {
                font-size: 10px;
                table-layout: fixed !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }

            .ranking-table th {
                padding: 4px 2px !important;
                font-size: 9px !important;
            }
            
            .ranking-table td {
                padding: 3px 2px !important;
                font-size: 9px !important;
                line-height: 1.15 !important;
            }
            
            .ranking-table .student-name-cell {
                min-width: 120px !important;
            }
            
            .badge {
                border: 1px solid #000 !important;
                color: #000 !important;
                font-size: 8px !important;
                padding: 2px 4px !important;
            }

            .ranking-table thead th:nth-child(1) { width: 3% !important; }  /* N° */
            .ranking-table thead th:nth-child(2) { width: 18% !important; } /* Étudiant */
            /* Matières = 6% chacune */
            .ranking-table thead th:nth-last-child(3) { width: 9% !important; }  /* Total */
            .ranking-table thead th:nth-last-child(2) { width: 8% !important; }  /* Moyenne */
            .ranking-table thead th:nth-last-child(1) { width: 10% !important; } /* Mention */

            @page {
                size: A4 landscape;
                margin: 8mm 6mm;
            }

            body {
                width: 100%;
                max-width: 27.8cm;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* En-tête de l'école */
            .school-header {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 5mm !important;
                border-bottom: 1px solid #000 !important;
                padding-bottom: 2mm !important;
            }

            .school-info {
                text-align: center !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
                margin: 0 auto !important;
            }

            .school-logo {
                height: 15mm !important;
                width: auto !important;
            }

            /* 10 élèves par page */
            .ranking-table tbody tr:nth-child(10n) {
                page-break-after: always !important;
            }

            /* Style des statistiques en haut de page */
            .stats-row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin-bottom: 3mm !important;
                font-size: 9px !important;
                justify-content: space-between !important;
                padding: 1mm 0 !important;
                border-bottom: 1px dashed #ccc !important;
            }

            .stats-item {
                flex: 1 !important;
                max-width: 18% !important;
                text-align: center !important;
                padding: 1mm !important;
            }

            .stats-value {
                font-size: 11px !important;
                font-weight: bold !important;
            }
        }
    </style>

    <script>
        // Animation d'apparition des éléments
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });

            // Ajout des éléments spécifiques pour l'impression
            window.addEventListener('beforeprint', function() {
                // Afficher l'en-tête d'impression
                document.querySelector('.school-header').classList.remove('d-none');
                
                // Personnaliser l'affichage pour l'impression
                let stylesheet = document.createElement('style');
                stylesheet.id = 'print-style';
                stylesheet.innerHTML = `
                    @page {
                        size: A4 landscape;
                        margin: 8mm 6mm;
                    }
                    .card-header, .card-body {
                        padding: 0 !important;
                    }
                    .table-responsive {
                        overflow: visible !important;
                    }
                    .school-header {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 10px;
                        padding-bottom: 5px;
                        border-bottom: 1px solid #000;
                    }
                `;
                document.head.appendChild(stylesheet);

                // Statistiques d'en-tête pour chaque page d'impression
                document.querySelectorAll('.ranking-table tbody tr:nth-child(10n)').forEach(function(tr) {
                    let statsRow = document.createElement('tr');
                    statsRow.className = 'stats-row-print';
                    statsRow.innerHTML = `
                        <td colspan="100%">
                            <div class="stats-row">
                                <div class="stats-item">
                                    <div>Total élèves: <span class="stats-value">{{ (isset($students) && $students) ? (is_array($students) ? count($students) : $students->count()) : 0 }}</span></div>
                                </div>
                                <div class="stats-item">
                                    <div>Moyenne: <span class="stats-value">{{ number_format($classAverage, 2) }}/20</span></div>
                                </div>
                                <div class="stats-item">
                                    <div>Admis: <span class="stats-value">{{ $passCount ?? 0 }}</span></div>
                                </div>
                                <div class="stats-item">
                                    <div>Taux: <span class="stats-value">{{ number_format($passRate ?? 0, 1) }}%</span></div>
                                </div>
                                <div class="stats-item">
                                    <div>Page: <span class="stats-value">${Math.ceil((tr.rowIndex+1)/10)}</span></div>
                                </div>
                            </div>
                        </td>
                    `;
                    tr.parentNode.insertBefore(statsRow, tr.nextSibling);
                });
                
                console.log('Print preparation complete');
            });

            // Nettoyer après l'impression
            window.addEventListener('afterprint', function() {
                document.querySelector('.school-header').classList.add('d-none');
                document.getElementById('print-style')?.remove();
                document.querySelectorAll('.stats-row-print').forEach(el => el.remove());
                console.log('Print cleanup complete');
            });
        });
    </script>
@endsection
