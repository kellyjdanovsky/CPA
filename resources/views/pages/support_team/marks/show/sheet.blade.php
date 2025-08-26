
@php
    use App\Models\StudentRecord;
    use App\Models\ExamRecord;
    use App\Models\Mark;
    use App\Models\Subject;
    use App\Helpers\NumberFormat;
    
    // IMPORTANT: Définir toutes les variables nécessaires au début pour éviter les erreurs "undefined variable"
    // Calculate position variables at the top to use in summary card with safety checks
    // Ensure dynamic calculation of student count by section and active status
    $current_section_id = $sr->section_id;
    
    // Calculate student count using the same approach as weighted-grades:
    // Only count students who actually have exam records for this exam
    $exam_student_ids = \App\Models\ExamRecord::where('my_class_id', $my_class->id)
                                              ->where('exam_id', $ex->id)
                                              ->where('year', $year)
                                              ->pluck('student_id')
                                              ->toArray();
    
    // Count only active students in this section who have exam records
    $total_eleve = StudentRecord::where('my_class_id', $my_class->id)
                                ->where('section_id', $current_section_id)
                                ->where('grad_date', null)
                                ->whereIn('user_id', $exam_student_ids)
                                ->count();
    
    // Safely get the exam record for the current student and exam
    $current_exam_record = $exr->where('student_id', $sr->user->id)->first();
    $position = $current_exam_record ? (Mk::getSuffix($current_exam_record->pos) ?: '-') : '-';
    
    // Get the detailed exam record for additional information
    $rang = ExamRecord::where('my_class_id', $my_class->id)
                     ->where('student_id', $sr->user->id)
                     ->where('exam_id', $ex->id)
                     ->first();
    
    $positionEnFrancais = '-';
    
    // Only process position if we have a valid position
    if ($position !== '-' && !empty($position)) {
        // Extract the text part without HTML tags for lookup
        $positionText = strip_tags($position); // Remove <sup> tags
        
        // Tableau de correspondance pour les numéros ordinaux français
        $positionsEnFrancais = [
            '1st' => '1<sup>er</sup>',
            '2nd' => '2<sup>e</sup>',
            '3rd' => '3<sup>e</sup>',
            '4th' => '4<sup>e</sup>',
            '5th' => '5<sup>e</sup>',
            '6th' => '6<sup>e</sup>',
            '7th' => '7<sup>e</sup>',
            '8th' => '8<sup>e</sup>',
            '9th' => '9<sup>e</sup>',
            '10th' => '10<sup>e</sup>',
            '11th' => '11<sup>e</sup>',
            '12th' => '12<sup>e</sup>',
            '13th' => '13<sup>e</sup>',
            '14th' => '14<sup>e</sup>',
            '15th' => '15<sup>e</sup>',
            '16th' => '16<sup>e</sup>',
            '17th' => '17<sup>e</sup>',
            '18th' => '18<sup>e</sup>',
            '19th' => '19<sup>e</sup>',
            '20th' => '20<sup>e</sup>',
        ];
        
        // Vérifier si la position est dans le tableau de correspondance
        if (array_key_exists($positionText, $positionsEnFrancais)) {
            $positionEnFrancais = $positionsEnFrancais[$positionText];
        } else {
            // Fallback transformation for other positions beyond 20
            // First extract number and suffix separately
            if (preg_match('/^(\d+)<sup>(st|nd|rd|th)<\/sup>$/', $position, $matches)) {
                $number = $matches[1];
                // For French: 1er for first, all others get 'e'
                if ($number == '1') {
                    $positionEnFrancais = $number . '<sup>er</sup>';
                } else {
                    $positionEnFrancais = $number . '<sup>e</sup>';
                }
            } else {
                // Fallback: simple text replacement
                $positionEnFrancais = str_replace(['<sup>st</sup>', '<sup>nd</sup>', '<sup>rd</sup>', '<sup>th</sup>'], ['<sup>er</sup>', '<sup>e</sup>', '<sup>e</sup>', '<sup>e</sup>'], $position);
            }
        }
    }
    
    // Calculer la moyenne de la classe correctement : somme des moyennes / nombre d'élèves
    // Récupérer tous les étudiants de la classe/section qui ont des notes pour cet examen
    // (consistent with weighted-grades and total count calculation)
    $all_students = StudentRecord::where('my_class_id', $my_class->id)
                                 ->where('section_id', $current_section_id)
                                 ->where('grad_date', null)
                                 ->whereIn('user_id', $exam_student_ids)
                                 ->get();
    
    // Récupérer toutes les matières
    $all_subjects = Subject::where('my_class_id', $my_class->id)->get();
    
    // Récupérer toutes les notes pour cette classe et cet examen
    $all_marks = Mark::where([
        'exam_id' => $ex->id,
        'my_class_id' => $my_class->id,
        'year' => $year
    ])->get();
    
    $class_averages_collection = collect();
    
    foreach($all_students as $student) {
        // Utiliser la même méthode de calcul que dans le tableau show
        $totalPoints = 0;
        $usedCoef = 0;
        
        foreach($all_subjects as $subject) {
            $markRecord = $all_marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first();
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
            $class_averages_collection->push($average);
        }
    }
    
    $class_average = $class_averages_collection->count() > 0 ? ($class_averages_collection->sum() / $class_averages_collection->count()) : 0;
    $total_students_with_averages = $class_averages_collection->count();
@endphp

<style>
    /* Enhanced modern bulletin design with better visual hierarchy */
    .border-left-primary {
        border-left: 6px solid #4f46e5 !important;
    }
    
    /* Modern gradient stats card */
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        border-radius: 20px;
        border: none;
        overflow: hidden;
        position: relative;
        margin-bottom: 30px;
    }
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
        animation: shimmer 3s infinite linear;
    }
    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: 200px 0; }
    }
    .stats-card .card-header {
        background: rgba(255,255,255,0.15) !important;
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(255,255,255,0.3);
        padding: 25px;
    }
    .stats-card .card-header h5 {
        color: white !important;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
    }
    .stats-card .card-body {
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(15px);
        padding: 30px;
    }
    
    /* Enhanced statistics display */
    .stats-value {
        font-size: 2.2rem;
        font-weight: 900;
        margin: 0.8rem 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .stats-label {
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 12px;
        color: #495057;
    }
    
    /* Modern performance indicators */
    .performance-indicator {
        padding: 0.6rem 1.2rem;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 800;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        transition: all 0.3s ease;
        display: inline-block;
    }
    .performance-indicator:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }
    
    .mention-excellent { 
        background: linear-gradient(135deg, #28a745, #20c997); 
        color: white;
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }
    .mention-good { 
        background: linear-gradient(135deg, #007bff, #6f42c1); 
        color: white;
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }
    .mention-average { 
        background: linear-gradient(135deg, #fd7e14, #ffc107); 
        color: white;
        box-shadow: 0 6px 20px rgba(253, 126, 20, 0.4);
    }
    .mention-poor { 
        background: linear-gradient(135deg, #dc3545, #e83e8c); 
        color: white;
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }
    
    /* Enhanced table with modern glassmorphism */
    .table-enhanced {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border: 1px solid rgba(255,255,255,0.3);
        margin-top: 20px;
    }
    .table-enhanced thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border: none;
        padding: 18px 12px;
        font-size: 0.95rem;
        text-align: center;
        position: relative;
    }
    .table-enhanced thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
    }
    
    .table-enhanced tbody tr {
        transition: all 0.4s ease;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }
    .table-enhanced tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .table-enhanced tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border: none;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .table-enhanced .total-row {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-weight: 800;
        border-top: 3px solid #667eea;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    .table-enhanced .total-row td {
        padding: 20px 12px;
        font-size: 1.1rem;
        color: #495057;
    }
    
    /* Modern card styling */
    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        overflow: hidden;
        margin-bottom: 30px;
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(10px);
    }
    .card-modern .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 25px;
    }
    .card-modern .card-body {
        padding: 30px;
        background: rgba(255,255,255,0.98);
    }
    
    /* Enhanced performance badges */
    .performance-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    .performance-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    }
    
    .performance-excellent {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    .performance-good {
        background: linear-gradient(135deg, #007bff, #6f42c1);
        color: white;
    }
    .performance-average {
        background: linear-gradient(135deg, #fd7e14, #ffc107);
        color: white;
    }
    .performance-poor {
        background: linear-gradient(135deg, #dc3545, #e83e8c);
        color: white;
    }
    
    /* Smooth animations for values */
    @keyframes countUp {
        from { 
            opacity: 0; 
            transform: translateY(20px) scale(0.9); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
    }
    .animate-value {
        animation: countUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    /* Enhanced column dividers */
    .border-right {
        position: relative;
    }
    .border-right::after {
        content: '';
        position: absolute;
        right: 0;
        top: 10%;
        bottom: 10%;
        width: 2px;
        background: linear-gradient(to bottom, transparent, #dee2e6, transparent);
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .stats-card .card-body .row .col-lg-3 {
            margin-bottom: 25px;
        }
        .stats-value {
            font-size: 1.8rem;
        }
        .table-enhanced {
            font-size: 0.9rem;
        }
    }
    
    /* Loading state for dynamic content */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<!-- Carte résumé des statistiques -->
<div class="card mb-4 stats-card border-left-primary">
    <div class="card-header bg-transparent">
        <h5 class="mb-0 text-primary font-weight-bold">Résumé des performances - {{ $ex->name }}</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="border-right h-100 d-flex flex-column justify-content-center">
                    <h5 class="stats-label text-primary mb-1">Total Points</h5>
                    <h3 class="stats-value text-primary mb-1 total-points-display-{{$ex->id}}">Calcul...</h3>
                    <small class="text-muted subjects-count-{{$ex->id}}">Matières actives</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="border-right h-100 d-flex flex-column justify-content-center">
                    <h5 class="stats-label text-success mb-1">Moyenne Finale</h5>
                    <h3 class="stats-value text-success mb-1 average-display-{{$ex->id}}">Calcul...</h3>
                    <span class="performance-indicator mention-poor mention-display-{{$ex->id}}">Calcul...</span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="border-right h-100 d-flex flex-column justify-content-center">
                    <h5 class="stats-label text-info mb-1">Rang</h5>
                    <h3 class="stats-value text-info mb-1">{!! $positionEnFrancais !!} / {!! $total_eleve !!}</h3>
                    <small class="text-muted">{{ $my_class->name }}</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="border-right h-100 d-flex flex-column justify-content-center">
                    <h5 class="stats-label text-secondary mb-1">Moyenne Classe</h5>
                    <h3 class="stats-value text-secondary mb-1 animate-value">{{ number_format($class_average, 2) }}/20</h3>
                    <small class="text-muted">{{ $total_students_with_averages }} élèves</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="h-100 d-flex flex-column justify-content-center">
                    <h5 class="stats-label text-warning mb-1">Performance</h5>
                    @php
                        $pos_num = 0;
                        $performance_percent = 100;
                        $performance_color = 'info';
                        
                        if ($position !== '-' && !empty($position)) {
                            $pos_num = intval(str_replace(['er', 'e', 'st', 'nd', 'rd', 'th'], '', $position));
                            if ($pos_num > 0 && $total_eleve > 1) {
                                $performance_percent = (($total_eleve - ($pos_num - 1)) / $total_eleve) * 100;
                            }
                        }
                        
                        $performance_color = $performance_percent >= 80 ? 'success' : ($performance_percent >= 60 ? 'info' : ($performance_percent >= 40 ? 'warning' : 'danger'));
                    @endphp
                    @if($position !== '-' && !empty($position))
                        <h3 class="stats-value text-{{ $performance_color }} mb-1">{{ number_format($performance_percent, 0) }}%</h3>
                        <small class="text-muted">Top {{ number_format($performance_percent, 0) }}% de la classe</small>
                    @else
                        <h3 class="stats-value text-muted mb-1">N/A</h3>
                        <small class="text-muted">Position non disponible</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-enhanced text-center">
    <thead>
        <tr>
            <th rowspan="2">N°</th>
            <th rowspan="2">MATIÈRES</th>
            <th rowspan="2">DS1<br>(20)</th>
            <th rowspan="2">DS2<br>(20)</th>
            <th rowspan="2">EXAMENS<br>(20)</th>
            <th rowspan="2">Moyenne (/20)<br></th>

            {{-- @if ($ex->term == 3) --}}{{-- 3e trimestre --}}{{--
        <th rowspan="2">TOTAL <br>(100%) 3<sup>e</sup> TRIMESTRE</th>
        <th rowspan="2">1<sup>er</sup> <br> TRIMESTRE</th>
        <th rowspan="2">2<sup>e</sup> <br> TRIMESTRE</th>
        <th rowspan="2">CUM (300%) <br> 1<sup>er</sup> + 2<sup>e</sup> + 3<sup>e</sup></th>
        <th rowspan="2">CUM MOY</th>
        @endif --}}

            <th rowspan="2">coefficient</th>
            <th rowspan="2">Total avec Coef<br><small class="text-muted">(Moyenne × Coef)</small></th>
            <th rowspan="2">REMARQUES</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($subjects as $sub)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $sub->name }}</td>
                @foreach ($marks->where('subject_id', $sub->id)->where('exam_id', $ex->id) as $mk)
                    <td>{{ $mk->t1 ?: '-' }}</td>
                    <td>{{ $mk->t2 ?: '-' }}</td>
                    <td>{{ $mk->exm ?: '-' }}</td>
                    <td>
                        @php
                            // Récupérer les valeurs de t1, t2, et exm
                            $t1 = $mk->t1 ?: 0; // Si t1 est null, on le considère comme 0
                            $t2 = $mk->t2 ?: 0; // Si t2 est null, on le considère comme 0
                            $exm = $mk->exm ?: 0; // Si exm est null, on le considère comme 0

                            // Calcul de la moyenne sans coefficient (en ne divisant que si on a des valeurs)
                            $values = [$t1, $t2, $exm];
                            $sum = array_sum($values); // Additionner toutes les notes
                            $count = count(array_filter($values, function($value) { return $value > 0; })); // Compter combien de valeurs sont supérieures à 0

                            // Si count est supérieur à 0 (au moins une note), on calcule la moyenne
                            $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                        @endphp

                        {{ number_format($moyen_sans_coef, 2) }} <!-- Afficher la moyenne, formatée avec 2 décimales -->
                    </td>

                    {{-- 3e trimestre --}}
                    {{-- @if ($ex->term == 3)
                     <td>{{ $mk->tex3 ?: '-' }}</td>
                     <td>{{ Mk::getSubTotalTerm($student_id, $sub->id, 1, $mk->my_class_id, $year) }}</td>
                     <td>{{ Mk::getSubTotalTerm($student_id, $sub->id, 2, $mk->my_class_id, $year) }}</td>
                     <td>{{ $mk->cum ?: '-' }}</td>
                     <td>{{ $mk->cum_ave ?: '-' }}</td>
                 @endif --}}

                    {{-- Grade, Position Matière & Remarques --}}

                    <td class="coef-{{$ex->id}}">{{ $sub->coef }}</td>
                    <td class="notetotalaveccoef-{{$ex->id}}" data-subject-id="{{ $sub->id }}" data-coef="{{ $sub->coef }}">
                        @php
                            // Récupérer les notes pour cette matière
                            $t1 = $mk->t1 ?: 0;
                            $t2 = $mk->t2 ?: 0;
                            $exm = $mk->exm ?: 0;

                            // Calcul de la moyenne sur 20 (même logique que colonne Moyenne)
                            $values = [$t1, $t2, $exm];
                            $sum = array_sum($values);
                            $count = count(array_filter($values, function($value) { return $value > 0; }));
                            $moyenne_sur_20 = $count > 0 ? $sum / $count : 0;

                            // Formule corrigée: moyenne × coefficient
                            $totalAvecCoef = $moyenne_sur_20 * $sub->coef;

                            // Debug pour les administrateurs
                            $debug_info = '';
                            if(Qs::userIsTeamSA()) {
                                $debug_info = " title=\"Debug: t1={$t1}, t2={$t2}, exm={$exm}, moyenne={$moyenne_sur_20}, coef={$sub->coef}, total={$totalAvecCoef}\"";
                            }
                        @endphp
                        <span {!! $debug_info !!}>{{ NumberFormat::formatWithoutRounding($totalAvecCoef, 2) }}</span>
                    </td>
                    <td class="remark-cell" data-mark-id="{{ $mk->id }}">
                        @php
                            // Récupérer les valeurs de t1, t2, et exm
                            $t1 = $mk->t1 ?: 0;
                            $t2 = $mk->t2 ?: 0;
                            $exm = $mk->exm ?: 0;

                            // Calcul de la moyenne sans coefficient
                            $values = [$t1, $t2, $exm];
                            $sum = array_sum($values);
                            $count = count(array_filter($values, function($value) { return $value > 0; }));
                            $moyen_sans_coef = $count > 0 ? $sum / $count : 0;

                            // Générer le commentaire basé sur la moyenne seulement si l'étudiant a des notes
                            $auto_comment = '';
                            $commentColor = 'text-muted';

                            // Afficher les remarques seulement si l'étudiant a au moins une note dans DS1, DS2, ou examen
                            if ($count > 0) {
                                $auto_comment = \App\Helpers\MarkComment::getComment($moyen_sans_coef);
                                $commentColor = \App\Helpers\MarkComment::getCommentColor($moyen_sans_coef);
                            }

                            // Utiliser le commentaire personnalisé s'il existe, sinon le commentaire automatique
                            $display_comment = $mk->comment ?: $auto_comment ?: '-';
                        @endphp

                        @if(Qs::userIsTeamSAT())
                            <div class="remark-display" style="cursor: pointer; min-height: 20px;">
                                <span class="{{ $commentColor }} remark-text">{{ $display_comment }}</span>
                                <i class="icon-pencil ml-2 text-muted" style="font-size: 12px;" title="Cliquer pour modifier"></i>
                            </div>
                            <div class="remark-edit" style="display: none;">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control remark-input" value="{{ $mk->comment }}" placeholder="Remarque personnalisée...">
                                    <div class="input-group-append">
                                        <button class="btn btn-success btn-sm save-remark" type="button" title="Sauvegarder">
                                            <i class="icon-checkmark"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-sm cancel-remark" type="button" title="Annuler">
                                            <i class="icon-cross"></i>
                                        </button>
                                        @if($mk->comment)
                                            <button class="btn btn-danger btn-sm delete-remark" type="button" title="Supprimer">
                                                <i class="icon-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="{{ $commentColor }}">{{ $display_comment }}</span>
                        @endif
                    </td>
                @endforeach
                <script>
                    $(document).ready(function() {
                        // Check if jQuery is available
                        if (typeof $ === 'undefined') {
                            console.error('jQuery is not loaded!');
                            return;
                        }
                                        
                        console.log('Initializing real-time editing functionality...');
                                        
                        var totalpoint = 0;
                        var total_coef = 0;
                        var subjects_count = 0;

                        // Parcourir chaque ligne de matière
                        $(".notetotalaveccoef-{{$ex->id}}").each(function(index) {
                            var value = parseFloat($(this).text());
                            var subjectId = $(this).data('subject-id');
                            var coef = parseFloat($(this).data('coef'));
                            
                            // Debug pour les administrateurs
                            @if(Qs::userIsTeamSA())
                                console.log('Matière ' + subjectId + ': Total avec coef = ' + value + ', Coefficient = ' + coef);
                            @endif
                            
                            // Vérifier si la valeur est différente de zéro (matière avec au moins une note)
                            if (!isNaN(value) && value > 0) {
                                // Utiliser la formule: moyenne x coefficient
                                // La valeur affichée est déjà calculée avec cette formule côté serveur
                                totalpoint += value;
                                subjects_count++;
                                
                                // Récupérer le coefficient correspondant à cette matière
                                if (!isNaN(coef)) {
                                    total_coef += coef;
                                }
                            }
                        });

                        // Calculer la moyenne en utilisant la somme des coefficients
                        var moyenne = (total_coef > 0) ? (totalpoint / total_coef) : 0;

                        // Affichage formaté du total des points avec plus de détails
                        var totalDisplay = totalpoint.toFixed(2);
                        if (subjects_count > 0) {
                            totalDisplay += " (" + subjects_count + " matière" + (subjects_count > 1 ? "s" : "") + ")";
                        }

                        // Affichage des résultats dans le tableau
                        $(".P_totalpoi-{{$ex->id}}").html('<strong>' + totalDisplay + '</strong>');
                        
                        // Mise à jour de la carte résumé
                        $(".total-points-display-{{$ex->id}}").text(totalpoint.toFixed(2));
                        $(".subjects-count-{{$ex->id}}").text(subjects_count + " matière" + (subjects_count > 1 ? "s" : ""));
                        
                        // Utiliser toujours la moyenne calculée (pas de comparaison avec la base de données)
                        var moyenne_finale = moyenne;
                        
                        // Mettre à jour la moyenne affichée
                        $(".P_moyenne-{{$ex->id}}").html('<strong>' + moyenne.toFixed(2) + '/20</strong>');
                        $(".average-display-{{$ex->id}}").text(moyenne.toFixed(2) + '/20');
                        
                        // Mettre à jour la mention avec le style approprié
                        var mention = '';
                        var mentionClass = 'mention-poor';
                        if(moyenne_finale >= 16) {
                            mention = 'Très Bien';
                            mentionClass = 'mention-excellent';
                        } else if(moyenne_finale >= 14) {
                            mention = 'Bien';
                            mentionClass = 'mention-good';
                        } else if(moyenne_finale >= 12) {
                            mention = 'Assez Bien';
                            mentionClass = 'mention-good';
                        } else if(moyenne_finale >= 10) {
                            mention = 'Passable';
                            mentionClass = 'mention-average';
                        } else {
                            mention = 'Insuffisant';
                            mentionClass = 'mention-poor';
                        }
                        
                        // Mettre à jour la mention avec la nouvelle classe
                        var $mentionElement = $(".mention-display-{{$ex->id}}");
                        $mentionElement.text(mention);
                        $mentionElement.removeClass('mention-excellent mention-good mention-average mention-poor');
                        $mentionElement.addClass(mentionClass);
                        
                        // La moyenne de classe est maintenant calculée côté serveur et affichée directement
                        // Plus besoin de calcul dynamique côté client
                        
                        // Ajouter des informations de debug si nécessaire (pour les admins)
                        @if(Qs::userIsTeamSA())
                            console.log('Examen {{$ex->id}} - Total points: ' + totalpoint.toFixed(2) + ', Coef total: ' + total_coef + ', Moyenne calculée: ' + moyenne.toFixed(2) + ', Moyenne classe: {{ number_format($class_average, 2) }} ({{ $total_students_with_averages }} élèves)');
                        @endif
                    });
                    
                    // Real-time editing functionality for remarks - Fixed
                    $(document).on('click', '.remark-display', function() {
                        var $this = $(this);
                        var $parent = $this.closest('.remark-cell');
                        var $editDiv = $parent.find('.remark-edit');
                        
                        $this.hide();
                        $editDiv.show();
                        $editDiv.find('.remark-input').focus();
                    });
                    
                    $(document).on('click', '.cancel-remark', function() {
                        var $parent = $(this).closest('.remark-cell');
                        var $displayDiv = $parent.find('.remark-display');
                        var $editDiv = $parent.find('.remark-edit');
                        
                        $editDiv.hide();
                        $displayDiv.show();
                    });
                    
                    $(document).on('click', '.save-remark', function() {
                        var $button = $(this);
                        var $parent = $button.closest('.remark-cell');
                        var $displayDiv = $parent.find('.remark-display');
                        var $editDiv = $parent.find('.remark-edit');
                        var $input = $editDiv.find('.remark-input');
                        var markId = $parent.data('mark-id');
                        var newComment = $input.val();
                        
                        // Show loading state
                        $button.prop('disabled', true);
                        $button.html('<i class="icon-spinner spin"></i>');
                        
                        // AJAX request to save comment
                        console.log('Sending AJAX request to save remark for mark ID:', markId);
                        $.ajax({
                            url: '{{ route("marks.comment.update") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                mark_id: markId,
                                comment: newComment
                            },
                            success: function(response) {
                                console.log('AJAX Response:', response);
                                if(response.success) {
                                    // Update display
                                    var displayText = newComment || response.auto_comment || '-';
                                    $displayDiv.find('.remark-text').text(displayText);
                                    
                                    // Update color based on response
                                    if(response.comment_color) {
                                        $displayDiv.find('.remark-text').removeClass().addClass(response.comment_color + ' remark-text');
                                    }
                                    
                                    // Show success message using existing notification system
                                    if (typeof flash !== 'undefined') {
                                        flash({msg: 'Remarque mise à jour avec succès!', type: 'success'});
                                    } else {
                                        alert('Remarque mise à jour avec succès!');
                                    }
                                    
                                    // Hide edit form
                                    $editDiv.hide();
                                    $displayDiv.show();
                                } else {
                                    console.error('AJAX request failed:', response);
                                    if (typeof flash !== 'undefined') {
                                        flash({msg: 'Erreur lors de la mise à jour de la remarque.', type: 'danger'});
                                    } else {
                                        alert('Erreur lors de la mise à jour de la remarque.');
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('AJAX Error:', xhr.responseText);
                                if (typeof flash !== 'undefined') {
                                    flash({msg: 'Erreur de connexion lors de la mise à jour.', type: 'danger'});
                                } else {
                                    alert('Erreur de connexion lors de la mise à jour.');
                                }
                            },
                            complete: function() {
                                // Reset button state
                                $button.prop('disabled', false);
                                $button.html('<i class="icon-checkmark"></i>');
                            }
                        });
                    });
                    
                    $(document).on('click', '.delete-remark', function() {
                        var $button = $(this);
                        var $parent = $button.closest('.remark-cell');
                        var $displayDiv = $parent.find('.remark-display');
                        var $editDiv = $parent.find('.remark-edit');
                        var $input = $editDiv.find('.remark-input');
                        var markId = $parent.data('mark-id');
                        
                        if(confirm('Êtes-vous sûr de vouloir supprimer cette remarque personnalisée ?')) {
                            // Show loading state
                            $button.prop('disabled', true);
                            $button.html('<i class="icon-spinner spin"></i>');
                            
                            // AJAX request to delete comment
                            console.log('Sending AJAX request to delete remark for mark ID:', markId);
                            $.ajax({
                                url: '{{ route("marks.comment.delete") }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    mark_id: markId
                                },
                                success: function(response) {
                                    if(response.success) {
                                        // Reset input and display auto comment
                                        $input.val('');
                                        var displayText = response.auto_comment || '-';
                                        $displayDiv.find('.remark-text').text(displayText);
                                        
                                        // Update color
                                        if(response.comment_color) {
                                            $displayDiv.find('.remark-text').removeClass().addClass(response.comment_color + ' remark-text');
                                        }
                                        
                                        if (typeof flash !== 'undefined') {
                                            flash({msg: 'Remarque supprimée avec succès!', type: 'success'});
                                        } else {
                                            alert('Remarque supprimée avec succès!');
                                        }
                                        
                                        // Hide edit form
                                        $editDiv.hide();
                                        $displayDiv.show();
                                    } else {
                                        if (typeof flash !== 'undefined') {
                                            flash({msg: 'Erreur lors de la suppression de la remarque.', type: 'danger'});
                                        } else {
                                            alert('Erreur lors de la suppression de la remarque.');
                                        }
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log('AJAX Error:', xhr.responseText);
                                    if (typeof flash !== 'undefined') {
                                        flash({msg: 'Erreur de connexion lors de la suppression.', type: 'danger'});
                                    } else {
                                        alert('Erreur de connexion lors de la suppression.');
                                    }
                                },
                                complete: function() {
                                    // Reset button state
                                    $button.prop('disabled', false);
                                    $button.html('<i class="icon-trash"></i>');
                                }
                            });
                        }
                    });
                    
                    // Real-time editing for general comments
                    $('.edit-general-comment').on('click', function() {
                        var examId = $(this).data('exam-id');
                        var commentType = $(this).data('comment-type');
                        
                        $('#commentaire_general_display_' + examId).hide();
                        $('#commentaire_general_edit_' + examId).show();
                        $('#commentaire_general_edit_' + examId + ' textarea').focus();
                    });
                    
                    $('.cancel-general-comment').on('click', function() {
                        var examId = $(this).data('exam-id');
                        
                        $('#commentaire_general_edit_' + examId).hide();
                        $('#commentaire_general_display_' + examId).show();
                    });
                    
                    $('.save-general-comment').on('click', function() {
                        var $button = $(this);
                        var examId = $button.data('exam-id');
                        var commentType = $button.data('comment-type');
                        var newComment = $('#commentaire_general_edit_' + examId + ' textarea').val();
                        
                        // Show loading state
                        $button.prop('disabled', true);
                        $button.html('<i class="icon-spinner spin"></i> Sauvegarde...');
                        
                        // AJAX request to save general comment
                        $.ajax({
                            url: '{{ route("marks.general.comment.update") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                exam_id: examId,
                                student_id: '{{ $sr->user->id }}',
                                comment_type: commentType,
                                comment: newComment
                            },
                            success: function(response) {
                                if(response.success) {
                                    // Update display
                                    var displayText = newComment || response.auto_comment || 'Aucun commentaire personnalisé';
                                    $('#commentaire_general_display_' + examId).html('<span class="text-primary font-weight-bold">' + displayText + '</span>');
                                    
                                    if (typeof flash !== 'undefined') {
                                        flash({msg: 'Commentaire mis à jour avec succès!', type: 'success'});
                                    } else {
                                        alert('Commentaire mis à jour avec succès!');
                                    }
                                    
                                    // Hide edit form
                                    $('#commentaire_general_edit_' + examId).hide();
                                    $('#commentaire_general_display_' + examId).show();
                                } else {
                                    if (typeof flash !== 'undefined') {
                                        flash({msg: 'Erreur lors de la mise à jour du commentaire.', type: 'danger'});
                                    } else {
                                        alert('Erreur lors de la mise à jour du commentaire.');
                                    }
                                }
                            },
                            error: function() {
                                if (typeof flash !== 'undefined') {
                                    flash({msg: 'Erreur de connexion lors de la mise à jour.', type: 'danger'});
                                } else {
                                    alert('Erreur de connexion lors de la mise à jour.');
                                }
                            },
                            complete: function() {
                                // Reset button state
                                $button.prop('disabled', false);
                                $button.html('<i class="icon-checkmark"></i> Sauvegarder');
                            }
                        });
                    });
                    
                    $('.delete-general-comment').on('click', function() {
                        var $button = $(this);
                        var examId = $button.data('exam-id');
                        var commentType = $button.data('comment-type');
                        
                        if(confirm('Êtes-vous sûr de vouloir supprimer ce commentaire personnalisé ?')) {
                            // Show loading state
                            $button.prop('disabled', true);
                            $button.html('<i class="icon-spinner spin"></i> Suppression...');
                            
                            // AJAX request to delete general comment
                            $.ajax({
                                url: '{{ route("marks.general.comment.delete") }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    exam_id: examId,
                                    student_id: '{{ $sr->user->id }}',
                                    comment_type: commentType
                                },
                                success: function(response) {
                                    if(response.success) {
                                        // Reset textarea and display auto comment
                                        $('#commentaire_general_edit_' + examId + ' textarea').val('');
                                        var displayText = response.auto_comment || 'Commentaire automatique basé sur la moyenne';
                                        $('#commentaire_general_display_' + examId).html('<span class="text-muted">' + displayText + '</span>');
                                        
                                        if (typeof flash !== 'undefined') {
                                            flash({msg: 'Commentaire supprimé avec succès!', type: 'success'});
                                        } else {
                                            alert('Commentaire supprimé avec succès!');
                                        }
                                        
                                        // Hide edit form
                                        $('#commentaire_general_edit_' + examId).hide();
                                        $('#commentaire_general_display_' + examId).show();
                                    } else {
                                        if (typeof flash !== 'undefined') {
                                            flash({msg: 'Erreur lors de la suppression du commentaire.', type: 'danger'});
                                        } else {
                                            alert('Erreur lors de la suppression du commentaire.');
                                        }
                                    }
                                },
                                error: function() {
                                    if (typeof flash !== 'undefined') {
                                        flash({msg: 'Erreur de connexion lors de la suppression.', type: 'danger'});
                                    } else {
                                        alert('Erreur de connexion lors de la suppression.');
                                    }
                                },
                                complete: function() {
                                    // Reset button state
                                    $button.prop('disabled', false);
                                    $button.html('<i class="icon-trash"></i> Supprimer');
                                }
                            });
                        }
                    });
                </script>
            </tr>
            @endforeach
            <tr>

            <td colspan="4" class="text-right"><strong>TOTAL DES POINTS OBTENUS :</strong></td>
            <td class="P_totalpoi-{{$ex->id}} text-center font-weight-bold text-primary" style="font-size: 1.1em;">Calcul...</td>
            <td class="text-right"><strong>MOYENNE FINALE :</strong></td>
            <td class="P_moyenne-{{$ex->id}} text-center font-weight-bold text-success" style="font-size: 1.1em;">{{ number_format($exr->ave ?? 0, 2) }}/20</td>
            <td class="text-center">
                <strong>RANG:</strong> 
                <span class="font-weight-bold text-info" style="font-size: 1.1em;">
                    {!! $positionEnFrancais !!} / {!! $total_eleve !!}
                </span>

            </td>
        </tr>
    </tbody>
</table>

{{-- TOTAL NOTE AVEC COEF SUR TOUTE MATIERE --}}

<!-- Commentaire général de l'enseignant -->
<div class="card mt-4 card-modern">
    <div class="card-header">
        <h5 class="card-title mb-0 text-white">
            <i class="icon-comment mr-2"></i>Commentaire de l'enseignant
        </h5>
        @if(Qs::userIsTeamSAT())
            <button class="btn btn-sm btn-outline-light edit-general-comment" data-exam-id="{{ $ex->id }}" data-comment-type="teacher">
                <i class="icon-pencil"></i> Modifier
            </button>
        @endif
    </div>
    <div class="card-body">
        <div id="commentaire_general_display_{{ $ex->id }}" class="p-4 bg-light rounded-lg border-left-primary" style="border-left: 4px solid #4f46e5;">
            <!-- Le commentaire sera inséré ici par JavaScript -->
        </div>
        @if(Qs::userIsTeamSAT())
            <div id="commentaire_general_edit_{{ $ex->id }}" class="mt-3" style="display: none;">
                <div class="form-group">
                    <textarea class="form-control" rows="3" placeholder="Commentaire personnalisé de l'enseignant..."></textarea>
                </div>
                <div class="text-right">
                    <button class="btn btn-success btn-sm save-general-comment" data-exam-id="{{ $ex->id }}" data-comment-type="teacher">
                        <i class="icon-checkmark"></i> Sauvegarder
                    </button>
                    <button class="btn btn-secondary btn-sm cancel-general-comment" data-exam-id="{{ $ex->id }}">
                        <i class="icon-cross"></i> Annuler
                    </button>
                    <button class="btn btn-danger btn-sm delete-general-comment" data-exam-id="{{ $ex->id }}" data-comment-type="teacher">
                        <i class="icon-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@if($ex->term == 3)
<!-- Affichage de la moyenne générale annuelle -->
<div class="card mt-4 card-modern">
    <div class="card-header">
        <h5 class="card-title mb-0 text-white">
            <i class="icon-trophy mr-2"></i>Moyennes annuelles
        </h5>
    </div>
    <div class="card-body">
        @php
            // Récupérer tous les examens créés pour cette année
            $all_exams = \App\Models\Exam::where('year', $year)->orderBy('term')->get();
        @endphp

        <div class="row">
            @foreach($all_exams as $exam)
                <div class="col-md-4 mb-3">
                    <div class="bg-light p-3 rounded border-left-primary" style="border-left: 4px solid #4f46e5;">
                        <label class="font-weight-bold text-primary mb-2">Moyenne {{ $exam->name }} :</label>
                        <div class="exam-average h4 text-success" data-exam-id="{{ $exam->id }}" id="moyenne_exam_{{ $exam->id }}">
                            Calcul en cours...
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center p-4 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                    <h3 class="font-weight-bold text-white mb-2">
                        <i class="icon-star mr-2"></i>Moyenne générale annuelle
                    </h3>
                    <h2 class="text-white" id="moyenne_annuelle_finale">Calcul en cours...</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    $(document).ready(function() {
        // Attendre que la moyenne soit calculée
        setTimeout(function() {
            var totalpoint = 0;
            var total_coef = 0;
            
            // Parcourir chaque ligne de matière
            $(".notetotalaveccoef-{{ $ex->id }}").each(function(index) {
                var value = parseFloat($(this).text());
                
                // Vérifier si la valeur est différente de zéro (matière avec au moins une note)
                if (!isNaN(value) && value > 0) {
                    totalpoint += value;
                    
                    // Récupérer le coefficient correspondant à cette matière
                    var coef = parseFloat($(".coef-{{ $ex->id }}").eq(index).text());
                    if (!isNaN(coef)) {
                        total_coef += coef;
                    }
                }
            });
            
            var moyenne = total_coef > 0 ? totalpoint / total_coef : 0;
            var moyenne_sur_20 = moyenne;
            
            // Stocker la moyenne pour d'autres calculs
            window.moyenne_generale = moyenne_sur_20;
            
            var commentaire = "";
            var commentaireClass = "";
            
            // Déterminer le commentaire en fonction de la moyenne
            if (moyenne_sur_20 >= 0 && moyenne_sur_20 < 5) {
                commentaire = "Moyenne très faible. Il faut persévérer, chaque progrès compte.";
                commentaireClass = "text-danger font-weight-bold";
            } else if (moyenne_sur_20 >= 5 && moyenne_sur_20 < 8) {
                commentaire = "Résultats insuffisants, mais des efforts peuvent relancer la dynamique.";
                commentaireClass = "text-warning font-weight-bold";
            } else if (moyenne_sur_20 >= 8 && moyenne_sur_20 < 10) {
                commentaire = "Moyenne fragile. Du potentiel à développer avec plus de régularité.";
                commentaireClass = "text-warning font-weight-bold";
            } else if (moyenne_sur_20 >= 10 && moyenne_sur_20 < 12) {
                commentaire = "Moyenne juste. Des bases sont posées, il faut les renforcer.";
                commentaireClass = "text-primary font-weight-bold";
            } else if (moyenne_sur_20 >= 12 && moyenne_sur_20 < 14) {
                commentaire = "Moyenne correcte. Il faut continuer à s'investir pour consolider les acquis.";
                commentaireClass = "text-primary font-weight-bold";
            } else if (moyenne_sur_20 >= 14 && moyenne_sur_20 < 16) {
                commentaire = "Bon travail. Un bel investissement à maintenir.";
                commentaireClass = "text-success font-weight-bold";
            } else if (moyenne_sur_20 >= 16 && moyenne_sur_20 < 18) {
                commentaire = "Très bon niveau. L'élève est sérieux et régulier.";
                commentaireClass = "text-success font-weight-bold";
            } else if (moyenne_sur_20 >= 18 && moyenne_sur_20 <= 20) {
                commentaire = "Excellent parcours. Un exemple à suivre, bravo !";
                commentaireClass = "text-purple font-weight-bold";
            }
            
            // Afficher le commentaire avec la classe de couleur appropriée
            $("#commentaire_general_{{ $ex->id }}").html('<span class="' + commentaireClass + '">' + commentaire + '</span>');
            
            // Mettre à jour la moyenne de l'examen actuel
            $("#moyenne_exam_{{ $ex->id }}").text(moyenne_sur_20.toFixed(2) + "/20");

            // Si c'est le 3ème trimestre, calculer la moyenne annuelle
            @if($ex->term == 3)
                // Fonction unifiée pour valider une moyenne
                function validateAverage(average, examName) {
                    if (average > 20) {
                        console.log("ATTENTION: Moyenne " + examName + " (" + average.toFixed(2) + ") dépasse 20, limitée à 20");
                        return 20;
                    }
                    return average;
                }

                // Fonction unifiée pour récupérer la moyenne d'un examen
                function getExamAverage(examId, examName, isCurrentExam) {
                    if (isCurrentExam) {
                        return validateAverage(moyenne_sur_20, examName + " (actuel)");
                    }

                    // Essayer de récupérer depuis P_moyenne
                    var examElement = $(".P_moyenne-" + examId);
                    if (examElement.length > 0) {
                        var examText = examElement.text();
                        console.log("Texte P_moyenne-" + examId + ": '" + examText + "'");

                        // Format "Moyenne 12.23"
                        var match = examText.match(/Moyenne\s+(\d+\.?\d*)/);
                        if (match && match[1]) {
                            return validateAverage(parseFloat(match[1]), examName + " (format Moyenne)");
                        }

                        // Format "MOYENNE FINALE : 12.23"
                        var match2 = examText.match(/MOYENNE FINALE\s*:\s*(\d+\.?\d*)/);
                        if (match2 && match2[1]) {
                            return validateAverage(parseFloat(match2[1]), examName + " (format MOYENNE FINALE)");
                        }
                    }

                    return null; // Aucune moyenne trouvée
                }

                // Fonction pour calculer la moyenne annuelle à partir des éléments P_moyenne
                function calculateAnnualAverage() {
                    var examAverages = [];
                    var totalAverage = 0;
                    var validExams = 0;

                    console.log("=== CALCUL DES MOYENNES ANNUELLES (SHOW) ===");
                    console.log("Moyenne actuelle calculée: " + moyenne_sur_20.toFixed(2));

                    // Récupérer toutes les moyennes des examens
                    @foreach($all_exams as $exam)
                        var examAverage = getExamAverage({{ $exam->id }}, "{{ $exam->name }}", {{ $exam->id == $ex->id ? 'true' : 'false' }});

                        if (examAverage !== null && examAverage > 0) {
                            examAverages.push(examAverage);
                            totalAverage += examAverage;
                            validExams++;
                            console.log("Examen {{ $exam->name }}: " + examAverage.toFixed(2));
                            $("#moyenne_exam_{{ $exam->id }}").text(examAverage.toFixed(2) + "/20");
                        } else {
                            // Priorité 1: Calculer manuellement d'abord (plus fiable que la DB)
                            @php
                                // Calculer la moyenne manuellement à partir des marks avec la même logique que show
                                $marks = \App\Models\Mark::where([
                                    'student_id' => $sr->user->id,
                                    'exam_id' => $exam->id,
                                    'my_class_id' => $my_class->id,
                                    'year' => $year
                                ])->get();

                                $totalPoints = 0;
                                $totalCoef = 0;

                                foreach($marks as $mark) {
                                    $subject = \App\Models\Subject::find($mark->subject_id);
                                    if($subject) {
                                        // Calculer la moyenne de la matière (t1 + t2 + exm) / nombre de notes
                                        $t1 = $mark->t1 ?: 0;
                                        $t2 = $mark->t2 ?: 0;
                                        $exm = $mark->exm ?: 0;

                                        $values = [$t1, $t2, $exm];
                                        $sum = array_sum($values);
                                        $count = count(array_filter($values, function($value) { return $value > 0; }));
                                        $moyen_sans_coef = $count > 0 ? $sum / $count : 0;

                                        // Utiliser cette moyenne avec le coefficient
                                        if($moyen_sans_coef > 0) {
                                            $totalPoints += ($moyen_sans_coef * $subject->coef);
                                            $totalCoef += $subject->coef;
                                        }
                                    }
                                }

                                $calculated_average = $totalCoef > 0 ? $totalPoints / $totalCoef : 0;

                                // Validation: s'assurer que la moyenne ne dépasse pas 20
                                if($calculated_average > 20) {
                                    $calculated_average = 20;
                                }
                            @endphp
                            console.log("Calcul manuel pour {{ $exam->name }}: {{ number_format($calculated_average, 2) }}");
                            @if($calculated_average > 0)
                                var calculatedAverage = validateAverage({{ number_format($calculated_average, 2) }}, "{{ $exam->name }} (calculé manuellement)");
                                examAverages.push(calculatedAverage);
                                totalAverage += calculatedAverage;
                                validExams++;
                                console.log("Examen {{ $exam->name }} (calculé manuellement): " + calculatedAverage.toFixed(2));
                                $("#moyenne_exam_{{ $exam->id }}").text(calculatedAverage.toFixed(2) + "/20");
                            @else
                                // Priorité 2: Fallback vers la base de données si calcul manuel échoue
                                @php
                                    $exam_record = \App\Models\ExamRecord::where([
                                        'student_id' => $sr->user->id,
                                        'exam_id' => $exam->id,
                                        'year' => $year
                                    ])->first();
                                    $exam_average = $exam_record ? ($exam_record->ave ?? 0) : 0;
                                @endphp
                                console.log("Fallback DB pour {{ $exam->name }}: {{ $exam_average }}");
                                @if($exam_average > 0)
                                    var fallbackAverage = validateAverage({{ number_format($exam_average, 2) }}, "{{ $exam->name }} (fallback DB)");
                                    examAverages.push(fallbackAverage);
                                    totalAverage += fallbackAverage;
                                    validExams++;
                                    console.log("Examen {{ $exam->name }} (fallback DB): " + fallbackAverage.toFixed(2));
                                    $("#moyenne_exam_{{ $exam->id }}").text(fallbackAverage.toFixed(2) + "/20");
                                @else
                                    console.log("Impossible de calculer la moyenne pour {{ $exam->name }}");
                                    $("#moyenne_exam_{{ $exam->id }}").text("0.00/20");
                                @endif
                            @endif
                        }
                    @endforeach

                    console.log("=== RÉSUMÉ DES MOYENNES (SHOW) ===");
                    console.log("Examens valides: " + validExams);
                    console.log("Total des moyennes: " + totalAverage.toFixed(2));
                    console.log("Moyennes individuelles: " + examAverages.map(avg => avg.toFixed(2)).join(", "));
                    console.log("Calcul: (" + examAverages.map(avg => avg.toFixed(2)).join(" + ") + ") / " + validExams + " = " + (totalAverage / validExams).toFixed(2));

                    // Calculer et afficher la moyenne annuelle
                    if (validExams > 0) {
                        var annualAverage = validateAverage(totalAverage / validExams, "Moyenne annuelle");

                        console.log("Moyenne annuelle finale (SHOW): " + annualAverage.toFixed(2));
                        $("#moyenne_annuelle_finale").text(annualAverage.toFixed(2) + "/20");

                        // Stocker la moyenne annuelle pour utilisation ultérieure et comparaison
                        window.moyenne_annuelle_finale_show = annualAverage;
                        window.moyenne_annuelle_finale = annualAverage;
                    } else {
                        console.log("Aucun examen valide, affichage 0.00");
                        $("#moyenne_annuelle_finale").text("0.00/20");
                        window.moyenne_annuelle_finale = 0;
                    }
                }

                // Fonction pour s'assurer que tous les examens affichent leurs moyennes
                function ensureAllExamAveragesDisplayed() {
                    @foreach($all_exams as $exam)
                        @if($exam->id == $ex->id)
                            // L'examen actuel est déjà mis à jour
                            if ($("#moyenne_exam_{{ $exam->id }}").text() === "Calcul en cours...") {
                                $("#moyenne_exam_{{ $exam->id }}").text(moyenne_sur_20.toFixed(2) + "/20");
                            }
                        @else
                            // Vérifier si l'examen a une moyenne affichée
                            if ($("#moyenne_exam_{{ $exam->id }}").text() === "Calcul en cours...") {
                                @php
                                    $exam_record = \App\Models\ExamRecord::where([
                                        'student_id' => $sr->user->id,
                                        'exam_id' => $exam->id,
                                        'year' => $year
                                    ])->first();
                                    $exam_average = $exam_record ? ($exam_record->ave ?? 0) : 0;

                                    // Si pas de moyenne en DB, calculer manuellement
                                    if($exam_average <= 0) {
                                        $marks = \App\Models\Mark::where([
                                            'student_id' => $sr->user->id,
                                            'exam_id' => $exam->id,
                                            'my_class_id' => $my_class->id,
                                            'year' => $year
                                        ])->get();

                                        $totalPoints = 0;
                                        $totalCoef = 0;

                                        foreach($marks as $mark) {
                                            $subject = \App\Models\Subject::find($mark->subject_id);
                                            if($subject) {
                                                $termValue = 0;
                                                if($exam->term == 1) $termValue = $mark->tex1;
                                                elseif($exam->term == 2) $termValue = $mark->tex2;
                                                elseif($exam->term == 3) $termValue = $mark->tex3;

                                                if($termValue > 0) {
                                                    $totalPoints += $termValue;
                                                    $totalCoef += $subject->coef;
                                                }
                                            }
                                        }

                                        $exam_average = $totalCoef > 0 ? $totalPoints / $totalCoef : 0;
                                    }
                                @endphp
                                @if($exam_average > 0)
                                    $("#moyenne_exam_{{ $exam->id }}").text("{{ number_format($exam_average, 2) }}/20");
                                @else
                                    $("#moyenne_exam_{{ $exam->id }}").text("0.00/20");
                                @endif
                            }
                        @endif
                    @endforeach
                }

                // S'assurer que tous les examens affichent leurs moyennes
                ensureAllExamAveragesDisplayed();

                // Calculer immédiatement après avoir mis à jour la moyenne actuelle
                setTimeout(function() {
                    calculateAnnualAverage();
                }, 100);

                // Recalculer après un délai plus long pour s'assurer que tous les éléments sont mis à jour
                setTimeout(function() {
                    ensureAllExamAveragesDisplayed();
                    calculateAnnualAverage();

                    // Test final de cohérence
                    setTimeout(function() {
                        console.log("=== TEST FINAL DE COHÉRENCE (SHOW) ===");
                        @foreach($all_exams as $exam)
                            var examAvg = $("#moyenne_exam_{{ $exam->id }}").text();
                            console.log("{{ $exam->name }}: " + examAvg);
                        @endforeach
                        var finalAvg = $("#moyenne_annuelle_finale").text();
                        console.log("Moyenne annuelle finale: " + finalAvg);
                        console.log("=== FIN TEST (SHOW) ===");
                    }, 500);
                }, 1500);
            @endif
        }, 500); // Attendre 500ms pour s'assurer que la moyenne est calculée
    });
</script>

{{-- Moyene NOTE AVEC COEF SUR TOUTE MATIERE --}}
