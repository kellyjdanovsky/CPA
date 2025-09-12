<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
    // IMPORTANT: Définir toutes les variables nécessaires au début pour éviter les erreurs "undefined variable"
    // Calculer la position et les informations de classement
    $rang = \App\Models\ExamRecord::where('my_class_id', $my_class->id)
                 ->where('student_id', $sr->user->id)
                 ->where('exam_id', $ex->id)
                 ->first();
    
    // Calculer la moyenne de la classe pour cet examen avec la même méthode que tabulation
    // Récupérer tous les étudiants de la classe/section qui ont des notes pour cet examen
    // (consistent with weighted-grades approach)
    $current_section_id = $sr->section_id;
    
    // Get student IDs who have exam records for this exam
    $exam_student_ids = \App\Models\ExamRecord::where('my_class_id', $my_class->id)
                                              ->where('exam_id', $ex->id)
                                              ->where('year', $year)
                                              ->pluck('student_id')
                                              ->toArray();
    
    $all_students = \App\Models\StudentRecord::where('my_class_id', $my_class->id)
                                                ->where('section_id', $current_section_id)
                                                ->where('grad_date', null)
                                                ->whereIn('user_id', $exam_student_ids)
                                                ->get();
    
    // Récupérer toutes les matières
    $all_subjects = \App\Models\Subject::where('my_class_id', $my_class->id)->get();
    
    // Récupérer toutes les notes pour cette classe et cet examen
    $all_marks = \App\Models\Mark::where([
        'exam_id' => $ex->id,
        'my_class_id' => $my_class->id,
        'year' => $year
    ])->get();
    
    $class_averages_collection = collect();
    
    foreach($all_students as $student) {
        // Utiliser la même méthode de calcul que dans le tableau tabulation
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
    $total_students = $class_averages_collection->count();
    
    // Calculer le nombre total d'étudiants dans la section qui ont des notes pour cet examen
    // (consistent with weighted-grades: only students with exam records)
    $total_eleve = \App\Models\StudentRecord::where('my_class_id', $my_class->id)
                                            ->where('section_id', $current_section_id)
                                            ->where('grad_date', null)
                                            ->whereIn('user_id', $exam_student_ids)
                                            ->count();
    
    // Générer la position française formatée
    $positionEnFrancais = '-';
    if ($rang && $rang->pos) {
        $position = Mk::getSuffix($rang->pos);
        
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
@endphp

{{-- Optimized Print Styles for A4 Landscape with Enhanced Quality and Professional Layout --}}
<style>
@media screen {
    /* Screen preview styling with centering */
    body {
        background: #f8f9fa !important;
        padding: 20px !important;
        min-height: 100vh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .print-preview {
        background: #f8f9fa;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }
    
    .print-container {
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
        max-width: 1200px;
        margin: 0 auto;
        transform: scale(0.85);
        transform-origin: center center;
        position: relative;
    }
}

@media print {
    @page {
        size: A4 landscape;
        margin: 5mm; /* Set to 5mm as requested */
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
        box-sizing: border-box !important;
    }
    
    body {
        font-family: 'Arial', 'Helvetica', sans-serif !important;
        font-size: 0.75vw !important; /* Using relative units for better scaling */
        line-height: 1.0 !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: white !important;
        color: #000 !important;
        overflow: visible !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    script {
        display: none !important;
    }
    
    /* Optimized Student Info Section - compact for single page */
    .student-info {
        width: 100% !important;
        margin: 0.2vh 0 !important;
        border-collapse: collapse !important;
        page-break-inside: avoid !important;
    }
    
    .student-info td {
        padding: 0.3vh 0.4vw !important;
        border: 0.1vh solid #000 !important;
        font-size: 0.7vw !important;
        text-align: left !important;
        vertical-align: middle !important;
        background: white !important;
        color: #000 !important;
        font-weight: 600 !important;
        line-height: 1.0 !important;
    }
    
    /* Enhanced Main Table Design - optimized for single page */
    .marks-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0.1vh 0 !important;
        table-layout: fixed !important;
        background: white !important;
        page-break-inside: avoid !important;
    }
    
    .marks-table th {
        padding: 0.2vh 0.1vw !important;
        border: 0.1vh solid #000 !important;
        font-size: 0.75vw !important;
        font-weight: bold !important;
        text-align: center !important;
        vertical-align: middle !important;
        background-color: #f5f5f5 !important;
        color: #000 !important;
        line-height: 1.0 !important;
    }
    
    .marks-table td {
        padding: 0.2vh 0.1vw !important;
        border: 0.1vh solid #000 !important;
        font-size: 0.7vw !important;
        text-align: center !important;
        vertical-align: middle !important;
        background: white !important;
        color: #000 !important;
        line-height: 1.0 !important;
        font-weight: 600 !important;
    }
    
    /* Enhanced number display - optimized for single page */
    .marks-table td.moyen_sans_coef,
    .marks-table td.notetotalaveccoef,
    .marks-table td.coef {
        font-size: 0.75vw !important;
        font-weight: 900 !important;
    }
    
    /* Score cells - optimized */
    .marks-table tbody tr td:nth-child(3),
    .marks-table tbody tr td:nth-child(4),
    .marks-table tbody tr td:nth-child(5) {
        font-size: 0.75vw !important;
        font-weight: 900 !important;
        background-color: #f8f9fa !important;
    }
    
    /* Average and total columns - optimized */
    .marks-table tbody tr td:nth-child(6),
    .marks-table tbody tr td:nth-child(8) {
        font-size: 0.75vw !important;
        font-weight: 900 !important;
        background-color: #fff3cd !important;
    }
    
    /* Coefficient column - optimized */
    .marks-table tbody tr td:nth-child(7) {
        font-size: 0.7vw !important;
        font-weight: bold !important;
        background-color: #e8f4fd !important;
        color: #0066cc !important;
    }
    
    /* Optimized Column Widths for maximum space utilization using percentages */
    .col-numero { width: 3% !important; }
    .col-matiere { width: 22% !important; text-align: left !important; }
    .col-ds1 { width: 7% !important; }
    .col-ds2 { width: 7% !important; }
    .col-exam { width: 7% !important; }
    .col-moyenne { width: 9% !important; }
    .col-coef { width: 5% !important; }
    .col-total { width: 10% !important; }
    .col-remarques { width: 30% !important; text-align: left !important; }
    
    /* Enhanced Total Row - optimized for single page */
    .total-row {
        background-color: #e5e7eb !important;
        font-weight: bold !important;
        font-size: 0.75vw !important;
        border: 0.1vh solid #000 !important;
    }
    
    .total-row td {
        padding: 0.2vh 0.1vw !important;
        border: 0.1vh solid #000 !important;
        font-weight: bold !important;
        color: #000 !important;
        font-size: 0.75vw !important;
        line-height: 1.0 !important;
    }
    
    /* Special styling for total numbers - compact */
    .total-row .P_totalpoi,
    .total-row .P_moyenne-1 {
        font-size: 0.8vw !important;
        font-weight: 900 !important;
    }
    
    /* Formula explanation - compact */
    .formula-row {
        background-color: #f8f9fa !important;
        font-size: 0.5vw !important;
        text-align: center !important;
        padding: 0.1vh !important;
        border: 0.1vh solid #000 !important;
        font-weight: bold !important;
        color: #000 !important;
        line-height: 1.0 !important;
    }
    
    /* Enhanced Grade Scale - compact */
    .grade-scale {
        font-size: 0.5vw !important;
        background-color: #f3f4f6 !important;
        border: 0.1vh solid #000 !important;
        text-align: center !important;
        padding: 0.1vh !important;
        font-weight: bold !important;
        color: #000 !important;
        line-height: 1.0 !important;
    }
    
    /* Class Statistics Enhancement - compact for single page */
    .class-stats {
        background-color: #f9fafb !important;
        border: 0.1vh solid #000 !important;
        margin: 0.1vh 0 !important;
        padding: 0.1vh !important;
    }
    
    .class-stats td {
        font-size: 0.5vw !important;
        padding: 0.1vh !important;
        border: 0.1vh solid #000 !important;
        font-weight: bold !important;
        color: #000 !important;
        line-height: 1.0 !important;
    }
    
    /* Annual Averages Section - compact for single page */
    .annual-averages {
        margin-top: 0.1vh !important;
        font-size: 0.5vw !important;
        border: 0.1vh solid #000 !important;
        padding: 0.1vh !important;
        background: white !important;
        page-break-inside: avoid !important;
    }
    
    .annual-averages h5 {
        font-size: 0.6vw !important;
        margin: 0 0 0.1vh 0 !important;
        text-align: center !important;
        background: #000 !important;
        color: white !important;
        padding: 0.1vh !important;
        line-height: 1.0 !important;
    }
    
    /* Comments Section Optimization - minimal for single page */
    .comments-section {
        margin-top: 0.1vh !important;
        font-size: 0.5vw !important;
        page-break-inside: avoid !important;
        border: 0.1vh solid #333 !important;
        padding: 0.1vh !important;
    }
    
    .comments-section h4 {
        font-size: 0.5vw !important;
        margin: 0 0 0.1vh 0 !important;
        border-bottom: 0.1vh solid #ccc !important;
        padding-bottom: 0.1vh !important;
        padding-bottom: 0.1vh !important;
        line-height: 1.0 !important;
    }
    
    .comments-section div {
        font-size: 0.5vw !important;
        line-height: 1.0 !important;
        padding: 0.1vh !important;
    }
    
    /* Signature lines - minimal for single page */
    .signature-section {
        margin-top: 0.1vh !important;
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 0.5vw !important;
        color: #000 !important;
    }
    
    .signature-box {
        width: 33.33% !important;
        text-align: center !important;
        border-top: 0.1vh solid #000 !important;
        padding-top: 0.1vh !important;
        font-weight: bold !important;
        display: table-cell !important;
        vertical-align: top !important;
        line-height: 1.0 !important;
    }
    
    /* Hide screen-only elements */
    .no-print {
        display: none !important;
    }
    
    /* Ensure all print elements are visible */
    .print-visible {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Ensure page doesn't break in middle of important sections */
    .page-break-avoid {
        page-break-inside: avoid !important;
    }
    
    /* Watermark styling for print */
    .watermark {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        opacity: 0.03 !important;
        z-index: -1 !important;
        font-size: 10vw !important;
        font-weight: bold !important;
        color: #000 !important;
        pointer-events: none !important;
        text-align: center !important;
        line-height: 0.8 !important;
        white-space: nowrap !important;
    }
    
    /* Force single page layout - automatic scaling and centering */
    .main-content {
        width: 100% !important;
        max-width: calc(100vw - 10mm) !important;
        margin: 0 !important;
        padding: 0 !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
        transform-origin: center center !important;
        max-height: calc(100vh - 10mm) !important;
        overflow: visible !important;
        flex-shrink: 0 !important;
    }
    
    /* Simplified print container for single page fit with centering */
    .print-container,
    .preview-container {
        width: 100% !important;
        max-width: calc(100vw - 10mm) !important;
        margin: 0 !important;
        padding: 0 !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
        transform-origin: center center !important;
        max-height: calc(100vh - 10mm) !important;
        display: block !important;
        position: relative !important;
        /* Dynamic scaling to fit content on single page */
        transform: scale(0.92) !important;
    }
    
    /* Ensure table fits on single page */
    .marks-table {
        page-break-inside: avoid !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
        width: 100% !important;
        table-layout: fixed !important;
    }
    
    /* Optimize space usage for all elements */
    .student-info,
    .class-stats,
    .annual-averages,
    .comments-section,
    .signature-section {
        page-break-inside: avoid !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
    }
    
    /* Force all content into single page - enhanced */
    * {
        page-break-inside: avoid !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
    }
    
    /* Force single page layout with specific height constraint */
    .preview-container {
        max-height: calc(100vh - 10mm) !important;
        overflow: hidden !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
    }
    
    /* Ensure all tables fit within page */
    table {
        page-break-inside: avoid !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
    }
    
    /* Force content to fit in viewport */
    html {
        overflow: hidden !important;
    }
    
    /* Automatic content scaling for single page with centering */
    html, body {
        height: 100vh !important;
        width: 100vw !important;
        overflow: visible !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    /* Scale down entire content if needed and maintain centering */
    .preview-container {
        transform: scale(0.92) !important;
        transform-origin: center center !important;
        display: block !important;
        position: relative !important;
    }
}

/* Print button styling for screen */
@media screen {
    .print-actions {
        background: white;
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .print-btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
        margin: 0 10px;
    }
    .print-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        color: white;
    }
    .preview-container {
        max-width: 1200px;
        margin: 20px auto;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
}

/* General table styling for both print and screen */
.marks-table {
    border-collapse: collapse;
    margin: 10px 0;
}

.marks-table th,
.marks-table td {
    padding: 6px 4px;
    border: 1px solid #000;
    text-align: center;
    vertical-align: middle;
}

.marks-table th {
    background-color: #f0f0f0;
    font-weight: bold;
}

.total-row {
    background-color: #e8e8e8;
    font-weight: bold;
}

.class-stats {
    background-color: #f9f9f9;
    border: 2px solid #ddd;
    margin: 10px 0;
    padding: 8px;
    border-radius: 5px;
}

.comments-section {
    margin-top: 15px;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
}
</style>

{{-- Professional Print Header --}}
<div class="no-print print-actions">
    <button onclick="printBulletin()" class="print-btn">
        <i class="icon-printer"></i> Imprimer le Bulletin
    </button>
    <button onclick="window.close()" class="print-btn" style="background: linear-gradient(135deg, #6c757d, #495057);">
        <i class="icon-cross"></i> Fermer
    </button>
</div>

<script>
function printBulletin() {
    // Simple print function without complex JavaScript
    try {
        window.print();
    } catch(e) {
        alert('Erreur lors de l\'impression. Veuillez utiliser Ctrl+P.');
    }
}
</script>

{{-- Main bulletin content wrapper --}}
<div class="preview-container">
    {{-- Student Information Header --}}
    <table class="student-info print-visible">
        <tbody>
            <tr>
                <td><strong>NOM ET PRÉNOMS:</strong> {{ strtoupper($sr->user->name) }}</td>
                <td><strong>NUMÉRO D'ADMISSION:</strong> {{ $sr->adm_no }}</td>
                <td><strong>CLASSE:</strong> {{ strtoupper($my_class->name) }}</td>
                <td><strong>SECTION:</strong> {{ strtoupper($sr->section->name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td><strong>BULLETIN DE NOTES POUR</strong> {!! strtoupper(Mk::getSuffix($ex->term)) !!} <strong>TRIMESTRE</strong></td>
                <td><strong>ANNÉE ACADÉMIQUE:</strong> {{ $ex->year }}</td>
                <td><strong>ÂGE:</strong>
                    {{ $sr->age ?: ($sr->user->dob ? date_diff(date_create($sr->user->dob), date_create('now'))->y : '-') }} ans
                </td>
                <td><strong>NUMÉRO ÉLÈVE:</strong> {{ $sr->id }}</td>
            </tr>
            <tr class="class-stats">
                <td colspan="2">
                    <strong>STATISTIQUES DE LA CLASSE:</strong> 
                    Moyenne générale: <strong>{{ number_format($class_average, 2) }}/20</strong> | 
                    Étudiants: <strong>{{ $total_students }}</strong>
                </td>
                <td colspan="2">
                    <strong>CLASSEMENT SECTION:</strong>
                    Rang: <strong>{!! $positionEnFrancais !!} / {{ $total_eleve }}</strong> |
                    @php
                        $student_average = $rang ? $rang->ave : 0;
                        $performance_text = 'À évaluer';
                        $performance_color = '#6b7280';
                        
                        if($student_average >= 16) { 
                            $performance_text = 'Excellent'; 
                            $performance_color = '#059669'; 
                        } elseif($student_average >= 14) { 
                            $performance_text = 'Très Bien'; 
                            $performance_color = '#0ea5e9'; 
                        } elseif($student_average >= 12) { 
                            $performance_text = 'Bien'; 
                            $performance_color = '#3b82f6'; 
                        } elseif($student_average >= 10) { 
                            $performance_text = 'Passable'; 
                            $performance_color = '#f59e0b'; 
                        } elseif($student_average > 0) { 
                            $performance_text = 'Insuffisant'; 
                            $performance_color = '#ef4444'; 
                        }
                    @endphp
                    Niveau: <strong style="color: {{ $performance_color }}">{{ $performance_text }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Professional Marks Table --}}
    <table class="marks-table print-visible">
        <thead>
            <tr style="background-color: #2563eb !important; color: white !important;">
                <th class="col-numero">N°</th>
                <th class="col-matiere">MATIÈRES</th>
                <th class="col-ds1">DS1<br><small>(20)</small></th>
                <th class="col-ds2">DS2<br><small>(20)</small></th>
                <th class="col-exam">EXAMEN<br><small>(20)</small></th>
                <th class="col-moyenne">Moyenne<br><small>(/20)</small></th>
                <th class="col-coef">Coeff</th>
                <th class="col-total">Total avec<br><small>Coeff</small></th>
                <th class="col-remarques">REMARQUES</th>
            </tr>
            <tr class="formula-row">
                <th colspan="9" style="background-color: #e5e7eb !important; color: #000 !important; font-size: 7px !important; padding: 1.5mm; text-align: center;">
                    <strong>FORMULE:</strong> Moyenne = (DS1 + DS2 + EXAMEN) ÷ Nombre de notes saisies | Total avec Coeff = Moyenne × Coefficient
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach ($subjects as $sub)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="col-matiere">{{ $sub->name }}</td>

                @foreach ($marks->where('subject_id', $sub->id)->where('exam_id', $ex->id) as $mk)
                    <td>{{ $mk->t1 ?: '-' }}</td>
                    <td>{{ $mk->t2 ?: '-' }}</td>
                    <td>{{ $mk->exm ?: '-' }}</td>

                    {{-- Calculate subject average --}}
                    <td class="moyen_sans_coef">
                        @php
                            $t1 = $mk->t1 ?: 0;
                            $t2 = $mk->t2 ?: 0;
                            $exm = $mk->exm ?: 0;

                            $values = [$t1, $t2, $exm];
                            $sum = array_sum($values);
                            $count = count(array_filter($values, fn($value) => $value > 0));
                            $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                        @endphp
                        {{ number_format($moyen_sans_coef, 2) }}
                    </td>

                    <td class="coef">{{ $sub->coef }}</td>

                    {{-- Calculate weighted total --}}
                    <td class="notetotalaveccoef">
                        @php
                            $moyen_avec_coef = $moyen_sans_coef * $sub->coef;
                        @endphp
                        {{ number_format($moyen_avec_coef, 2) }}
                    </td>

                    {{-- Display remarks --}}
                    <td class="remarks-cell">
                        @php
                            $display_comment = '';
                            if (!empty($mk->comment)) {
                                $display_comment = $mk->comment;
                            } elseif ($count > 0) {
                                $display_comment = \App\Helpers\MarkComment::getComment($moyen_sans_coef);
                            } else {
                                $display_comment = 'Aucune note';
                            }
                        @endphp
                        <span style="font-weight: 600;">{{ $display_comment }}</span>
                    </td>
                @endforeach
            </tr>
            @endforeach

            {{-- Enhanced Total Row --}}
            <tr class="total-row" style="background-color: #fbbf24 !important;">
                <td colspan="3" style="text-align: center; font-weight: bold;"><strong>TOTAL DES POINTS</strong></td>
                <td class="P_totalpoi" style="color: #dc2626 !important; font-weight: 900;"><strong>{{ number_format($rang->total ?? 0, 2) }}</strong></td>
                <td style="text-align: center; font-weight: bold;"><strong>MOYENNE GÉNÉRALE</strong></td>
                <td class="P_moyenne-{{$ex->id}}" style="color: #059669 !important; font-weight: 900;"><strong>{{ number_format($rang->ave ?? 0, 2) }}/20</strong></td>
                <td style="text-align: center; font-weight: bold;"><strong>RANG</strong></td>
                <td colspan="2" style="text-align: center; font-weight: bold;">
                    <strong>{!! $positionEnFrancais !!} / {{ $total_eleve }}</strong>
                </td>
            </tr>
            
            {{-- Grade Scale Reference --}}
            <tr class="grade-scale-row">
                <td colspan="9" style="background-color: #f3f4f6 !important; font-size: 7px !important; text-align: center; padding: 1.5mm; font-weight: 600; border: 1px solid #000;">
                    <strong>ÉCHELLE D'ÉVALUATION:</strong>
                    18-20: Excellent | 16-17.9: Très Bien | 14-15.9: Bien | 12-13.9: Assez Bien | 10-11.9: Passable | &lt;10: Insuffisant
                </td>
            </tr>
        </tbody>
    </table>
    
    @if($ex->term == 3)
    <!-- Section des moyennes annuelles optimisée pour impression -->
    <div class="annual-averages print-visible" style="margin-top: 6px; border: 2px solid #1e3a8a; background-color: #eff6ff;">
        <h5 style="background-color: #1e3a8a; color: white; text-align: center; margin: 0; padding: 3px; font-size: 9px;">
            MOYENNES ANNUELLES - BILAN COMPLET
        </h5>
        @php
            // Récupérer tous les examens créés pour cette année
            $all_exams_print = \App\Models\Exam::where('year', $year)->orderBy('term')->get();
        @endphp
        
        <div style="display: flex; justify-content: space-between; margin: 4px 2px; background-color: white; padding: 2px; border: 1px solid #1e3a8a;">
            @foreach($all_exams_print as $exam_print)
                <div style="flex: 1; margin: 0 1px; text-align: center; border-right: 1px solid #e5e7eb;">
                    <label style="font-weight: bold; font-size: 8px; color: #1e3a8a;">{{ $exam_print->name }}:</label>
                    <div class="exam-average" data-exam-id="{{ $exam_print->id }}" id="moyenne_exam_{{ $exam_print->id }}" style="font-size: 8px; font-weight: bold;">
                        Calcul...
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="text-align: center; border-top: 2px solid #1e3a8a; padding: 3px; background-color: #fbbf24; margin-top: 2px;">
            <strong style="font-size: 9px; color: #1e3a8a;">MOYENNE GÉNÉRALE ANNUELLE:</strong>
            <span id="moyenne_annuelle_finale" style="font-weight: bold; font-size: 10px; color: #dc2626;">Calcul...</span>
            <span style="font-size: 7px; color: #374151;"> | Objectif: 10/20 minimum pour le passage</span>
        </div>
        
        @php
            // Déterminer la décision de passage basée sur la moyenne annuelle (sera calculée par JS)
            $decision_passage = "En cours d'évaluation...";
            $decision_couleur = "#6b7280";
        @endphp
        
        <div style="text-align: center; background-color: #f9fafb; padding: 2px; margin-top: 2px; border-top: 1px solid #e5e7eb;">
            <strong style="font-size: 8px; color: #374151;">DÉCISION PROVISOIRE:</strong>
            <span id="decision_passage" style="font-size: 8px; font-weight: bold; color: {{ $decision_couleur }};">{{ $decision_passage }}</span>
        </div>
    </div>
    @endif

{{-- jQuery Loading --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- TOTAL NOTE AVEC COEF SUR TOUTE MATIERE --}}
<script>
    $(document).ready(function() {
        // Fonction pour ajuster l'affichage selon le mode (print/screen)
        function adjustDisplayMode() {
            if (window.matchMedia && window.matchMedia('print').matches) {
                // Mode impression - optimisations spécifiques
                $('body').addClass('print-mode');
                $('.marks-table').addClass('print-optimized');
            }
        }
        
        // Ajuster lors du chargement
        adjustDisplayMode();
        
        // Ajuster lors de l'impression
        window.addEventListener('beforeprint', adjustDisplayMode);
        
        // Attendre que la moyenne soit calculée
        setTimeout(function() {
            var totalpoint = 0;
            var total_coef = 0;

            // Parcourir chaque ligne de matière
            $(".notetotalaveccoef").each(function(index) {
                var value = parseFloat($(this).text());

                // Vérifier si la valeur est différente de zéro (matière avec au moins une note)
                if (!isNaN(value) && value > 0) {
                    totalpoint += value;

                    // Récupérer le coefficient correspondant à cette matière
                    var coef = parseFloat($(".coef").eq(index).text());
                    if (!isNaN(coef)) {
                        total_coef += coef;
                    }
                }
            });

            var moyenne = total_coef > 0 ? totalpoint / total_coef : 0;
            var moyenne_sur_20 = moyenne;

            // Show only the numbers without labels
            var totalDisplay = totalpoint.toFixed(2);
            var averageDisplay = moyenne.toFixed(2) + "/20";

            // Display only the numbers in the total cells with enhanced styling
            $(".P_totalpoi").html('<strong style="font-size: 18px; font-weight: 900;">' + totalDisplay + '</strong>');
            $(".P_moyenne-{{$ex->id}}").html('<strong style="font-size: 18px; font-weight: 900;">' + averageDisplay + '</strong>');

            // Stocker la moyenne pour le commentaire général
            window.moyenne_generale = moyenne_sur_20;

            // Mettre à jour la moyenne de l'examen actuel
            $("#moyenne_exam_{{ $ex->id }}").text(moyenne_sur_20.toFixed(2) + "/20");

            // Si c'est le 3ème trimestre, calculer la moyenne annuelle
            @if($ex->term == 3)
                // Fonction unifiée pour valider une moyenne (identique au fichier show)
                function validateAverage(average, examName) {
                    if (average > 20) {
                        console.log("ATTENTION: Moyenne " + examName + " (" + average.toFixed(2) + ") dépasse 20, limitée à 20");
                        return 20;
                    }
                    return average;
                }

                // Fonction unifiée pour récupérer la moyenne d'un examen (identique au fichier show)
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

                    console.log("=== CALCUL DES MOYENNES ANNUELLES (PRINT) ===");
                    console.log("Moyenne actuelle calculée: " + moyenne_sur_20.toFixed(2));

                    // Récupérer toutes les moyennes des examens (identique au fichier show)
                    @foreach($all_exams_print as $exam)
                        var examAverage = getExamAverage({{ $exam->id }}, "{{ $exam->name }}", {{ $exam->id == $ex->id ? 'true' : 'false' }});

                        if (examAverage !== null && examAverage > 0) {
                            examAverages.push(examAverage);
                            totalAverage += examAverage;
                            validExams++;
                            console.log("Examen {{ $exam->name }}: " + examAverage.toFixed(2));
                            $("#moyenne_exam_{{ $exam->id }}").html('<strong style="font-size: 16px; font-weight: 900;">' + examAverage.toFixed(2) + '/20</strong>');
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
                                $("#moyenne_exam_{{ $exam->id }}").html('<strong style="font-size: 16px; font-weight: 900;">' + calculatedAverage.toFixed(2) + '/20</strong>');
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
                                    $("#moyenne_exam_{{ $exam->id }}").html('<strong style="font-size: 16px; font-weight: 900;">' + fallbackAverage.toFixed(2) + '/20</strong>');
                                @else
                                    console.log("Impossible de calculer la moyenne pour {{ $exam->name }}");
                                    $("#moyenne_exam_{{ $exam->id }}").html('<span style="font-size: 16px; font-weight: 600;">0.00/20</span>');
                                @endif
                            @endif
                        }
                    @endforeach

                    console.log("=== RÉSUMÉ DES MOYENNES ===");
                    console.log("Examens valides: " + validExams);
                    console.log("Total des moyennes: " + totalAverage.toFixed(2));
                    console.log("Moyennes individuelles: " + examAverages.map(avg => avg.toFixed(2)).join(", "));
                    console.log("Calcul: (" + examAverages.map(avg => avg.toFixed(2)).join(" + ") + ") / " + validExams + " = " + (totalAverage / validExams).toFixed(2));

                    // Calculer et afficher la moyenne annuelle
                    if (validExams > 0) {
                        var annualAverage = validateAverage(totalAverage / validExams, "Moyenne annuelle");

                        console.log("Moyenne annuelle finale (PRINT): " + annualAverage.toFixed(2));
                        $("#moyenne_annuelle_finale").html('<strong style="font-size: 18px; font-weight: 900;">' + annualAverage.toFixed(2) + '/20</strong>');

                        // Stocker la moyenne annuelle pour utilisation ultérieure et comparaison
                        window.moyenne_annuelle_finale_print = annualAverage;
                        window.moyenne_annuelle_finale = annualAverage;
                        
                        // Déterminer la décision de passage automatiquement
                        var decision_passage = "";
                        var decision_couleur = "";
                        
                        if (annualAverage >= 10) {
                            decision_passage = "ADMIS(E) - PASSAGE AUTORISÉ";
                            decision_couleur = "#059669"; // Vert
                        } else if (annualAverage >= 8) {
                            decision_passage = "ADMIS(E) AVEC RÉSERVE - SUIVI RENFORCÉ";
                            decision_couleur = "#d97706"; // Orange
                        } else {
                            decision_passage = "REDOUBLEMENT RECOMMANDÉ";
                            decision_couleur = "#dc2626"; // Rouge
                        }
                        
                        $("#decision_passage").text(decision_passage).css("color", decision_couleur);

                        // Vérifier la cohérence avec la vue show si disponible
                        if (typeof window.moyenne_annuelle_finale_show !== 'undefined') {
                            var difference = Math.abs(annualAverage - window.moyenne_annuelle_finale_show);
                            if (difference > 0.01) {
                                console.log("⚠️ INCOHÉRENCE DÉTECTÉE:");
                                console.log("Show: " + window.moyenne_annuelle_finale_show.toFixed(2));
                                console.log("Print: " + annualAverage.toFixed(2));
                                console.log("Différence: " + difference.toFixed(2));
                            } else {
                                console.log("✅ Cohérence parfaite entre Show et Print");
                            }
                        }
                    } else {
                        console.log("Aucun examen valide, affichage 0.00");
                        $("#moyenne_annuelle_finale").text("0.00/20");
                        window.moyenne_annuelle_finale = 0;
                    }
                }

                // Fonction pour s'assurer que tous les examens affichent leurs moyennes
                function ensureAllExamAveragesDisplayed() {
                    @foreach($all_exams_print as $exam)
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

                                    // Si pas de moyenne en DB, calculer manuellement avec la même logique que show
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

                                        $exam_average = $totalCoef > 0 ? $totalPoints / $totalCoef : 0;

                                        // Validation: s'assurer que la moyenne ne dépasse pas 20
                                        if($exam_average > 20) {
                                            $exam_average = 20;
                                        }
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
                        console.log("=== TEST FINAL DE COHÉRENCE ===");
                        @foreach($all_exams_print as $exam)
                            var examAvg = $("#moyenne_exam_{{ $exam->id }}").text();
                            console.log("{{ $exam->name }}: " + examAvg);
                        @endforeach
                        var finalAvg = $("#moyenne_annuelle_finale").text();
                        console.log("Moyenne annuelle finale: " + finalAvg);
                        console.log("=== FIN TEST ===");
                    }, 500);
                }, 1500);
            @endif
        }, 500); // Attendre 500ms pour s'assurer que la moyenne est calculée
        
        // Génération des commentaires après calcul de la moyenne
        setTimeout(function() {
            var moyenne = window.moyenne_generale || 0;
            
            // Commentaire de l'enseignant
            var commentaire_enseignant = "";
            if (moyenne >= 0 && moyenne < 5) {
                commentaire_enseignant = "Moyenne très faible. Il faut persévérer, chaque progrès compte.";
            } else if (moyenne >= 5 && moyenne < 8) {
                commentaire_enseignant = "Résultats insuffisants, mais des efforts peuvent relancer la dynamique.";
            } else if (moyenne >= 8 && moyenne < 10) {
                commentaire_enseignant = "Moyenne fragile. Du potentiel à développer avec plus de régularité.";
            } else if (moyenne >= 10 && moyenne < 12) {
                commentaire_enseignant = "Moyenne juste. Des bases sont posées, il faut les renforcer.";
            } else if (moyenne >= 12 && moyenne < 14) {
                commentaire_enseignant = "Moyenne correcte. Il faut continuer à s'investir pour consolider les acquis.";
            } else if (moyenne >= 14 && moyenne < 16) {
                commentaire_enseignant = "Bon travail. Un bel investissement à maintenir.";
            } else if (moyenne >= 16 && moyenne < 18) {
                commentaire_enseignant = "Très bon niveau. L'élève est sérieux et régulier.";
            } else if (moyenne >= 18 && moyenne <= 20) {
                commentaire_enseignant = "Excellent parcours. Un exemple à suivre, bravo !";
            }
            
            // Commentaire du directeur
            var commentaire_directeur = "";
            if (moyenne >= 0 && moyenne < 5) {
                commentaire_directeur = "Ne lâche rien, chaque effort t'aidera à avancer.";
            } else if (moyenne >= 5 && moyenne < 8) {
                commentaire_directeur = "Courage et persévérance mèneront à la réussite.";
            } else if (moyenne >= 8 && moyenne < 10) {
                commentaire_directeur = "Continue à progresser, tu en es capable.";
            } else if (moyenne >= 10 && moyenne < 12) {
                commentaire_directeur = "Des bases posées, à renforcer pas à pas.";
            } else if (moyenne >= 12 && moyenne < 14) {
                commentaire_directeur = "Des efforts visibles, poursuis dans cette voie.";
            } else if (moyenne >= 14 && moyenne < 16) {
                commentaire_directeur = "Bon travail, garde cette motivation.";
            } else if (moyenne >= 16 && moyenne < 18) {
                commentaire_directeur = "Excellente implication, continue ainsi !";
            } else if (moyenne >= 18 && moyenne <= 20) {
                commentaire_directeur = "Félicitations ! Un parcours remarquable.";
            }
            
            // Afficher les commentaires
            $("#commentaire_general").text(commentaire_enseignant);
            $("#commentaire_directeur").text(commentaire_directeur);
        }, 600);
    });
</script>

    <!-- Professional Comments and Decision Section -->
    @if(!empty($rang->t_comment) || !empty($rang->p_comment) || $ex->term == 3)
    <div class="comments-section print-visible" style="margin-top: 4mm; border: 1.5px solid #2563eb; background: #f8fafc; page-break-inside: avoid;">
    <h4 style="background: #2563eb; color: white; margin: 0; padding: 2mm; text-align: center; font-size: 9px; font-weight: bold;">
        OBSERVATIONS ET COMMENTAIRES PÉDAGOGIQUES
    </h4>
    
    <div style="padding: 3mm; background: white; margin: 1mm;">
        @if(!empty($rang->t_comment))
            <div style="margin-bottom: 2mm; border-left: 3px solid #059669; padding-left: 2mm;">
                <strong style="font-size: 8px; color: #2563eb;">Commentaire du professeur:</strong>
                <div style="font-size: 8px; line-height: 1.2; margin-top: 1mm; font-style: italic;">{{ $rang->t_comment }}</div>
            </div>
        @endif
        
        @if(!empty($rang->p_comment))
            <div style="margin-bottom: 2mm; border-left: 3px solid #dc2626; padding-left: 2mm;">
                <strong style="font-size: 8px; color: #2563eb;">Commentaire de la direction:</strong>
                <div style="font-size: 8px; line-height: 1.2; margin-top: 1mm; font-style: italic;">{{ $rang->p_comment }}</div>
            </div>
        @endif
        
        @if($ex->term == 3)
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2mm; margin-top: 2mm; text-align: center;">
                <strong style="font-size: 9px; color: #dc2626;">DÉCISION DE FIN D'ANNÉE:</strong>
                <div id="decision_finale" style="font-size: 9px; font-weight: bold; margin-top: 1mm; padding: 1mm; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 3px;">
                    En cours d'évaluation...
                </div>
            </div>
        @endif
    </div>
    </div>
    @endif

    @if($ex->term == 3)
    <!-- Administrative Decision Section for Final Term -->
    <div class="comments-section print-visible" style="background-color: #fef3c7; border: 2px solid #f59e0b; margin-top: 3mm; page-break-inside: avoid;">
    <h4 style="color: #92400e; background-color: #fbbf24; text-align: center; margin: 0 0 2mm 0; padding: 2mm; font-size: 9px;">DÉCISION ADMINISTRATIVE FINALE</h4>
    <div style="padding: 2mm;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2mm; font-size: 8px;">
            <tr>
                <td style="width: 50%; padding: 0;">
                    <span style="font-weight: bold;">Décision finale:</span>
                    <span style="margin-left: 5px;">☐ Passant</span>
                    <span style="margin-left: 10px;">☐ Redoublant</span>
                    <span style="margin-left: 10px;">☐ Orientation</span>
                </td>
                <td style="width: 50%; text-align: right; padding: 0;">
                    <span style="font-weight: bold;">Date du conseil:</span>
                    <span style="margin-left: 5px; border-bottom: 1px solid #000; display: inline-block; width: 60px; height: 10px;"></span>
                </td>
            </tr>
        </table>
        <div style="margin-bottom: 2mm; font-size: 8px;">
            <span style="font-weight: bold;">Classe d'affectation {{ intval($ex->year) + 1 }}:</span>
            <span style="margin-left: 5px; border-bottom: 1px solid #000; display: inline-block; width: 150px; height: 10px;"></span>
        </div>
        <div style="font-size: 6px; font-style: italic; color: #6b7280; text-align: center; margin-top: 1mm;">
            Cette décision est prise par le conseil de classe selon les critères académiques de l'établissement.
        </div>
    </div>
    </div>
    @endif

    <!-- Professional Signature Section -->
    <table class="signature-section print-visible" style="margin-top: 4mm; border-top: 2px solid #2563eb; padding-top: 3mm; page-break-inside: avoid; width: 100%; border-collapse: collapse;">
    <tr>
        <td class="signature-box" style="width: 33.33%; text-align: center; border-top: 2px solid #000; padding-top: 3mm; vertical-align: top;">
            <div style="border-bottom: 1.5px solid #000; height: 15mm; margin-bottom: 2mm;"></div>
            <p style="font-weight: bold; font-size: 8px; margin: 0;">Le Professeur Principal</p>
            <p style="font-size: 7px; margin: 0; color: #6b7280;">Signature et cachet</p>
        </td>
        <td class="signature-box" style="width: 33.33%; text-align: center; border-top: 2px solid #000; padding-top: 3mm; vertical-align: top;">
            <div style="border-bottom: 1.5px solid #000; height: 15mm; margin-bottom: 2mm;"></div>
            <p style="font-weight: bold; font-size: 8px; margin: 0;">Le Directeur des Études</p>
            <p style="font-size: 7px; margin: 0; color: #6b7280;">Signature et cachet</p>
        </td>
        <td class="signature-box" style="width: 33.33%; text-align: center; border-top: 2px solid #000; padding-top: 3mm; vertical-align: top;">
            <div style="border-bottom: 1.5px solid #000; height: 15mm; margin-bottom: 2mm;"></div>
            <p style="font-weight: bold; font-size: 8px; margin: 0;">Le Parent/Tuteur</p>
            <p style="font-size: 7px; margin: 0; color: #6b7280;">Lu et approuvé le: ___/___/____</p>
        </td>
    </tr>
    </table>
        
    <!-- Official Stamp Section -->
    <div style="text-align: center; margin-top: 3mm; border: 1px dashed #6b7280; padding: 3mm; background: #f9fafb;">
        <p style="font-size: 7px; color: #6b7280; margin: 0; font-weight: bold;">
            ESPACE RÉSERVÉ AU CACHET OFFICIEL DE L'ÉTABLISSEMENT
        </p>
        <div style="height: 15mm;"></div>
    </div>
        
    <!-- Legal Information Footer -->
    <div style="margin-top: 3mm; border-top: 1px solid #e5e7eb; padding-top: 2mm; text-align: center;">
        <p style="font-size: 6px; color: #6b7280; margin: 0; line-height: 1.2;">
            Document officiel du {{ Qs::getSetting('system_name') ?: 'Collège Privé Adventiste' }} - Toute reproduction ou falsification est interdite<br>
            Généré le {{ date('d/m/Y à H:i') }} par {{ Auth::user()->name ?? 'Système' }} | 
            Pour vérification: {{ Qs::getSetting('phone') ?: 'Contactez l\'établissement' }}
        </p>
    </div>

</div> {{-- End preview-container --}}