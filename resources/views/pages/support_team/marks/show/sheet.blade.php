@php
    // Calculations for student's marks and class statistics
    $rang = \App\Models\ExamRecord::where('my_class_id', $my_class->id)
                 ->where('student_id', $sr->user->id)
                 ->where('exam_id', $ex->id)
                 ->first();

    $exam_student_ids = \App\Models\ExamRecord::where('my_class_id', $my_class->id)
                                              ->where('exam_id', $ex->id)
                                              ->where('year', $year)
                                              ->pluck('student_id')
                                              ->toArray();

    $all_students = \App\Models\StudentRecord::where('my_class_id', $my_class->id)
                                                ->where('section_id', $sr->section_id)
                                                ->where('grad_date', null)
                                                ->whereIn('user_id', $exam_student_ids)
                                                ->get();

    $all_subjects = \App\Models\Subject::where('my_class_id', $my_class->id)->get();
    $all_marks = \App\Models\Mark::where(['exam_id' => $ex->id, 'my_class_id' => $my_class->id, 'year' => $year])->get();

    // Calculate weighted averages for ranking and student statistics - consistent with MarkRepo->getPos
    $student_averages = [];
    $class_averages_collection = collect();
    $student_total_points = 0;
    $student_total_coefficients = 0;
    
    // Calculate student's total points and coefficients (same logic as in MarkRepo->getPos)
    foreach($all_subjects as $subject) {
        $markRecord = $all_marks->where('student_id', $sr->user->id)->where('subject_id', $subject->id)->first();
        if ($markRecord) {
            // Get individual grades (same as in marks/show)
            $t1 = $markRecord->t1 ?: 0;
            $t2 = $markRecord->t2 ?: 0;
            $exm = $markRecord->exm ?: 0;
            
            // Calculate average from DS1, DS2, Exam (same logic as marks/show)
            $values = [$t1, $t2, $exm];
            $sum = array_sum($values);
            $count = count(array_filter($values, function($value) { return $value > 0; }));
            $moyenne_sur_20 = $count > 0 ? $sum / $count : 0;
            
            if($moyenne_sur_20 > 0) {
                // Apply formula: moyenne × coefficient
                $totalAvecCoef = $moyenne_sur_20 * $subject->coef;
                
                // Calculate weighted points for average calculation
                $student_total_points += $totalAvecCoef;
                $student_total_coefficients += $subject->coef;
            }
        }
    }
    
    // Calculate student's weighted average
    $student_weighted_average = $student_total_coefficients > 0 ? $student_total_points / $student_total_coefficients : 0;

    // Calculate class averages for ranking (same logic as in MarkRepo->getPos)
    foreach($all_students as $student) {
        $totalPoints = 0;
        $usedCoef = 0;
        foreach($all_subjects as $subject) {
            $markRecord = $all_marks->where('student_id', $student->user_id)->where('subject_id', $subject->id)->first();
            if ($markRecord) {
                // Get individual grades (same as in marks/show)
                $t1 = $markRecord->t1 ?: 0;
                $t2 = $markRecord->t2 ?: 0;
                $exm = $markRecord->exm ?: 0;
                
                // Calculate average from DS1, DS2, Exam (same logic as marks/show)
                $values = [$t1, $t2, $exm];
                $sum = array_sum($values);
                $count = count(array_filter($values, function($value) { return $value > 0; }));
                $moyenne_sur_20 = $count > 0 ? $sum / $count : 0;
                
                if($moyenne_sur_20 > 0) {
                    // Apply formula: moyenne × coefficient
                    $totalAvecCoef = $moyenne_sur_20 * $subject->coef;
                    
                    // Calculate weighted points for average calculation
                    $totalPoints += $totalAvecCoef;
                    $usedCoef += $subject->coef;
                }
            }
        }
        if ($usedCoef > 0) {
            $average = $totalPoints / $usedCoef;
            $student_averages[$student->user_id] = $average;
            $class_averages_collection->push($average);
        }
    }

    // Sort students by average (descending) to determine ranking
    arsort($student_averages);
    $student_rank = array_search($sr->user->id, array_keys($student_averages)) + 1;

    $class_average = $class_averages_collection->count() > 0 ? ($class_averages_collection->sum() / $class_averages_collection->count()) : 0;
    $total_students = $class_averages_collection->count();
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    /* A4 Landscape Print Optimization */
    @media print {
        @page {
            size: A4 landscape;
            margin: 0.5cm;
        }
        
        * {
            color: #000 !important;
            background: transparent !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        body {
            background-color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            line-height: 1.2;
        }
        
        .bulletin-container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border-radius: 0;
            border: none;
        }
        
        .bulletin-header {
            background: #000 !important;
            color: #fff !important;
            padding: 8px 0;
            text-align: center;
            border-bottom: 2px solid #000 !important;
        }
        
        .school-title {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 3px 0;
            letter-spacing: 0.6px;
        }
        
        .exam-title {
            font-size: 13px;
            font-weight: 700;
            margin: 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .school-info p {
            margin: 1px 0;
            color: #fff !important;
            font-size: 9px;
        }
        
        .student-details {
            background: #f5f5f5 !important;
            padding: 6px;
            border-bottom: 1px solid #000 !important;
        }
        
        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            margin: 0;
        }
        
        .detail-item {
            background: white;
            padding: 4px 6px;
            border-radius: 2px;
            border-left: 2px solid #000 !important;
            font-size: 8px;
            box-shadow: none;
        }
        
        .detail-item strong {
            color: #000 !important;
            font-weight: 700;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 8px;
            background: white;
        }
        
        .marks-table th {
            background: #000 !important;
            color: #fff !important;
            padding: 4px 2px;
            text-align: center;
            font-weight: 700;
            font-size: 9px;
            border: 1px solid #000 !important;
        }
        
        .marks-table td {
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #000 !important;
            vertical-align: middle;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background: #f8f8f8 !important;
        }
        
        .subject-name {
            text-align: left !important;
            font-weight: 600;
            color: #000 !important;
            font-size: 9px;
        }
        
        .grade {
            font-weight: 700;
            color: #000 !important;
            font-size: 9px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            padding: 6px;
            background: #f5f5f5 !important;
        }
        
        .summary-item {
            background: white;
            padding: 8px;
            border-radius: 3px;
            text-align: center;
            box-shadow: none;
            border-top: 2px solid #000 !important;
        }
        
        .summary-item h4 {
            margin: 0 0 4px 0;
            color: #000 !important;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .summary-item p {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            color: #000 !important;
        }
        
        .footer-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 10px;
            padding: 6px;
            background: #ffffff;
            border-top: 1px solid #000 !important;
        }
        
        .comments, .signatures {
            padding: 0;
        }
        
        .comments h4, .signatures h4 {
            color: #000 !important;
            margin: 0 0 4px 0;
            font-size: 9px;
            font-weight: 700;
            padding-bottom: 3px;
            border-bottom: 1px solid #000 !important;
        }
        
        .comments p {
            margin: 0 0 3px 0;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .signature-line {
            height: 25px;
            border-bottom: 1px solid #000 !important;
            margin: 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000 !important;
            font-size: 7px;
            font-weight: 600;
        }
        
        .no-print {
            display: none;
        }
    }
    
    /* Screen-specific styling - Modern & Premium Design */
    @media screen {
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .bulletin-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border-radius: 16px;
            overflow: hidden;
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .bulletin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .bulletin-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .school-title {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 10px 0;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .exam-title {
            font-size: 24px;
            font-weight: 700;
            margin: 12px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
        }
        
        .school-info p {
            margin: 4px 0;
            color: rgba(255,255,255,0.95);
            font-size: 15px;
            position: relative;
            z-index: 1;
        }
        
        .student-details {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 25px;
            border-bottom: none;
        }
        
        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin: 0;
        }
        
        .detail-item {
            background: white;
            padding: 15px 18px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }
        
        .detail-item strong {
            color: #667eea;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 14px;
            background: white;
        }
        
        .marks-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 12px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .marks-table td {
            padding: 14px 10px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .marks-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .marks-table tbody tr:hover {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        }
        
        .subject-name {
            text-align: left !important;
            font-weight: 700;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .grade {
            font-weight: 700;
            color: #667eea;
            font-size: 15px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .summary-item {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            border-top: 5px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .summary-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .summary-item:hover::before {
            left: 100%;
        }
        
        .summary-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.25);
        }
        
        .summary-item h4 {
            margin: 0 0 12px 0;
            color: #667eea;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .summary-item p {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .footer-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            padding: 30px;
            background: #ffffff;
            border-top: 3px solid #667eea;
        }
        
        .comments, .signatures {
            padding: 0;
        }
        
        .comments h4, .signatures h4 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 18px;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 3px solid #667eea;
        }
        
        .comments p {
            margin: 0 0 12px 0;
            font-size: 14px;
            line-height: 1.6;
            color: #2c3e50;
        }
        
        .comments p strong {
            color: #667eea;
            font-weight: 700;
        }
        
        .signature-line {
            height: 60px;
            border-bottom: 2px solid #bdc3c7;
            margin: 18px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .signature-line:hover {
            border-bottom-color: #667eea;
            color: #667eea;
        }
        
        .no-print {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .no-print button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .no-print button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }
        
        .no-print button:active {
            transform: translateY(-1px);
        }
    }
    
    /* Responsive design for mobile */
    @media (max-width: 768px) {
        body {
            padding: 15px 0;
        }
        
        .student-details-grid {
            grid-template-columns: 1fr;
        }
        
        .summary-section {
            grid-template-columns: repeat(2, 1fr);
            padding: 20px 15px;
        }
        
        .footer-section {
            grid-template-columns: 1fr;
            padding: 20px 15px;
        }
        
        .marks-table {
            font-size: 12px;
        }
        
        .marks-table th, .marks-table td {
            padding: 10px 6px;
        }
        
        .bulletin-container {
            margin: 10px;
            border-radius: 12px;
        }
        
        .bulletin-header {
            padding: 25px 20px;
        }
        
        .school-title {
            font-size: 24px;
        }
        
        .exam-title {
            font-size: 18px;
        }
    }
    
    @media (max-width: 480px) {
        .summary-section {
            grid-template-columns: 1fr;
        }
        
        .student-details-grid {
            grid-template-columns: 1fr;
        }
        
        .bulletin-container {
            margin: 5px;
        }
        
        .marks-table {
            font-size: 11px;
        }
        
        .marks-table th, .marks-table td {
            padding: 8px 4px;
        }
    }
</style>

<div class="bulletin-container">
    <!-- Header -->
    <div class="bulletin-header">
        <div class="school-info">
            <h1 class="school-title">{{ strtoupper(Qs::getSetting('system_name')) }}</h1>
            <p>{{ ucwords($s['address']) }}</p>
            <h2 class="exam-title">{{ $ex->name }}</h2>
            <p>NIVEAU {{ $class_type->name }}</p>
        </div>
    </div>

    <!-- Student Details -->
    <div class="student-details">
        <div class="student-details-grid">
            <div class="detail-item"><strong>NOM & PRÉNOMS:</strong> {{ strtoupper($sr->user->name) }}</div>
            <div class="detail-item"><strong>N° D'ADMISSION:</strong> {{ $sr->adm_no }}</div>
            <div class="detail-item"><strong>CLASSE:</strong> {{ strtoupper($my_class->name) }}</div>
            <div class="detail-item"><strong>SECTION:</strong> {{ strtoupper($sr->section->name ?? 'N/A') }}</div>
            <div class="detail-item"><strong>TRIMESTRE:</strong> {!! strtoupper(Mk::getSuffix($ex->term)) !!}</div>
            <div class="detail-item"><strong>ANNÉE ACADÉMIQUE:</strong> {{ $ex->year }}</div>
            <div class="detail-item"><strong>ÂGE:</strong> {{ $sr->age ?: ($sr->user->dob ? date_diff(date_create($sr->user->dob), date_create('now'))->y : '-') }} ans</div>
        </div>
    </div>

    <!-- Marks Table -->
    <table class="marks-table">
        <thead>
            <tr>
                <th>Matières</th>
                <th>DS1 (20)</th>
                <th>DS2 (20)</th>
                <th>Examen (20)</th>
                <th>Moyenne (/20)</th>
                <th>Coeff</th>
                <th>Total avec Coeff</th>
                <th>Remarques</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $sub)
                <tr>
                    <td class="subject-name">{{ $sub->name }}</td>
                    @php
                        $mk = $marks->where('subject_id', $sub->id)->where('exam_id', $ex->id)->first();
                        if ($mk) {
                            $t1 = $mk->t1 ?? '-';
                            $t2 = $mk->t2 ?? '-';
                            $exm = $mk->exm ?? '-';
                            
                            $values = array_filter([$mk->t1, $mk->t2, $mk->exm], fn($v) => is_numeric($v));
                            $sum = array_sum($values);
                            $count = count($values);
                            $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                            $moyen_avec_coef = $moyen_sans_coef * $sub->coef;

                            $display_comment = $mk->comment ?? ($count > 0 ? \App\Helpers\MarkComment::getComment($moyen_sans_coef) : 'Aucune note');
                        } else {
                            $t1 = '-';
                            $t2 = '-';
                            $exm = '-';
                            $moyen_sans_coef = 0;
                            $moyen_avec_coef = 0;
                            $display_comment = 'Aucune note';
                        }
                    @endphp
                    <td>{{ $t1 }}</td>
                    <td>{{ $t2 }}</td>
                    <td>{{ $exm }}</td>
                    <td class="grade">{{ number_format($moyen_sans_coef, 2) }}</td>
                    <td>{{ $sub->coef }}</td>
                    <td class="grade">{{ number_format($moyen_avec_coef, 2) }}</td>
                    <td>{{ $display_comment }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary-section">
        <div class="summary-item">
            <h4>Total des Points</h4>
            <p>{{ number_format($student_total_points, 2) }}</p>
        </div>
        <div class="summary-item">
            <h4>Moyenne Générale</h4>
            <p>{{ number_format($student_weighted_average, 2) }}/20</p>
        </div>
        <div class="summary-item">
            <h4>Moyenne de la Classe</h4>
            <p>{{ number_format($class_average, 2) }}/20</p>
        </div>
        <div class="summary-item">
            <h4>Position</h4>
            <p>{!! Mk::getSuffix($student_rank) !!}</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-section">
        <div class="comments">
            <h4>Commentaires</h4>
            <p><strong>Professeur Principal:</strong> {{ $rang->t_comment ?? '...' }}</p>
            <p><strong>Directeur:</strong> {{ $rang->p_comment ?? '...' }}</p>
        </div>
        <div class="signatures">
            <h4>Signatures</h4>
            <div class="signature-line">Professeur Principal</div>
            <div class="signature-line">Directeur</div>
            <div class="signature-line">Parent/Tuteur</div>
        </div>
    </div>
    <div class="no-print" style="text-align: center; padding: 20px;">
        <button onclick="window.print()">Imprimer</button>
    </div>
</div>