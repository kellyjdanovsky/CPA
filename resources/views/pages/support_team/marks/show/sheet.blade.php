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
.bulletin-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.bulletin-header {
    text-align: center;
    border-bottom: 3px solid #007bff;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.school-title {
    color: #007bff;
    margin: 0 0 5px 0;
    font-size: 28px;
    font-weight: bold;
}

.exam-title {
    color: #333;
    margin: 10px 0;
    font-size: 24px;
    font-weight: bold;
    text-transform: uppercase;
}

.school-info p {
    margin: 3px 0;
    color: #555;
    font-size: 16px;
}

.student-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.detail-item {
    padding: 8px;
    font-size: 14px;
}

.detail-item strong {
    color: #007bff;
}

.marks-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    font-size: 20px; /* Further increased base font size */
}

.marks-table th {
    background-color: #007bff;
    color: white;
    padding: 20px; /* Increased padding */
    text-align: center;
    font-weight: bold;
    font-size: 22px; /* Larger header font */
}

.marks-table td {
    padding: 15px; /* Increased padding */
    text-align: center;
    border-bottom: 1px solid #ddd;
    font-size: 20px; /* Larger cell font */
}

.marks-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.marks-table tbody tr:hover {
    background-color: #e9ecef;
}

.subject-name {
    text-align: left !important;
    font-weight: bold;
    font-size: 21px; /* Larger subject name font */
}

.grade {
    font-weight: bold;
    color: #007bff;
    font-size: 21px; /* Larger grade font */
}

.summary-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.summary-item {
    text-align: center;
    padding: 15px;
    background-color: #e9f7fe;
    border-radius: 5px;
    box-shadow: 0 0 5px rgba(0,0,0,0.05);
}

.summary-item h4 {
    margin: 0 0 10px 0;
    color: #007bff;
    font-size: 18px;
}

.summary-item p {
    margin: 0;
    font-size: 22px;
    font-weight: bold;
    color: #333;
}

.footer-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #eee;
}

.comments, .signatures {
    padding: 15px;
}

.comments h4, .signatures h4 {
    color: #007bff;
    margin-top: 0;
    font-size: 20px;
}

.signature-line {
    height: 60px;
    border-bottom: 1px solid #999;
    margin: 20px 0;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    color: #777;
    font-size: 16px;
}

.no-print {
    margin-top: 20px;
}

.no-print button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.no-print button:hover {
    background-color: #0056b3;
}

@media print {
    .no-print {
        display: none;
    }
    
    .bulletin-container {
        box-shadow: none;
        padding: 0;
    }
    
    .marks-table {
        font-size: 18px;
    }
    
    .marks-table th {
        font-size: 20px;
        padding: 15px;
    }
    
    .marks-table td {
        font-size: 18px;
        padding: 12px;
    }
    
    .subject-name, .grade {
        font-size: 19px;
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