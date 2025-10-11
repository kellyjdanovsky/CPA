@extends('layouts.app')
@section('title', 'Fiche de notes')

@section('content')
@php
    // Helper function to safely get object properties
    function prop($obj, $prop, $default = '') {
        return (is_object($obj) && isset($obj->$prop)) ? $obj->$prop : $default;
    }

    // Ensure school variable is properly defined
    $school = $school ?? session('school') ?? [];

    // Calculations for student's marks and class statistics
    if ($sr && $sr->user) {
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

        // Calculate weighted averages for ranking and student statistics
        $student_averages = [];
        $class_averages_collection = collect();
        $student_total_points = 0;
        $student_total_coefficients = 0;
        
        foreach($all_subjects as $subject) {
            $markRecord = $all_marks->where('student_id', $sr->user->id)->where('subject_id', $subject->id)->first();
            if ($markRecord) {
                $t1 = $markRecord->t1 ?: 0;
                $t2 = $markRecord->t2 ?: 0;
                $exm = $markRecord->exm ?: 0;
                
                $values = [$t1, $t2, $exm];
                $sum = array_sum($values);
                $count = count(array_filter($values, function($value) { return $value > 0; }));
                $moyenne_sur_20 = $count > 0 ? $sum / $count : 0;
                
                if($moyenne_sur_20 > 0) {
                    $totalAvecCoef = $moyenne_sur_20 * $subject->coef;
                    $student_total_points += $totalAvecCoef;
                    $student_total_coefficients += $subject->coef;
                }
            }
        }
        
        $student_weighted_average = $student_total_coefficients > 0 ? $student_total_points / $student_total_coefficients : 0;

        foreach($all_students as $student) {
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
                    $count = count(array_filter($values, function($value) { return $value > 0; }));
                    $moyenne_sur_20 = $count > 0 ? $sum / $count : 0;
                    
                    if($moyenne_sur_20 > 0) {
                        $totalAvecCoef = $moyenne_sur_20 * $subject->coef;
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

        arsort($student_averages);
        $student_rank = array_search($sr->user->id, array_keys($student_averages)) + 1;

        $class_average = $class_averages_collection->count() > 0 ? ($class_averages_collection->sum() / $class_averages_collection->count()) : 0;
        $total_students = $class_averages_collection->count();
    } else {
        // Set default values if $sr or $sr->user is not available
        $rang = null;
        $student_rank = 'N/A';
        $total_students = 'N/A';
        $student_total_points = 0;
        $student_weighted_average = 0;
        $class_average = 0;
        $subjects = collect();
        $marks = collect();
    }
@endphp

<style>
    @page {
        size: landscape;
        margin: 10mm;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
    }
    .bulletin-container {
        max-width: 1100px; /* Adjusted for landscape */
        margin: 0 auto;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .exam-title {
        text-align: center;
        color: #333;
        margin: 15px 0;
        font-size: 1.6rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .student-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 8px;
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        font-size: 0.85rem;
    }
    .detail-item {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .detail-item strong {
        color: #007bff;
        margin-right: 5px;
    }
    .marks-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }
    .marks-table th {
        background-color: #007bff;
        color: white;
        padding: 10px;
        text-align: center;
        font-weight: bold;
    }
    .marks-table td {
        padding: 7px;
        text-align: center;
        border: 1px solid #ddd;
    }
    .marks-table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    .subject-name {
        text-align: left !important;
        font-weight: 500;
    }
    .grade {
        font-weight: bold;
        color: #0056b3;
    }
    .summary-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .summary-item {
        text-align: center;
        padding: 12px;
        background-color: #e9f7fe;
        border-radius: 5px;
    }
    .summary-item h4 {
        margin: 0 0 8px 0;
        color: #007bff;
        font-size: 1rem;
    }
    .summary-item p {
        margin: 0;
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
    }
    .footer-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-top: 25px;
        padding-top: 15px;
        border-top: 2px solid #eee;
    }
    .comments h4, .signatures h4 {
        color: #007bff;
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    .signature-line {
        height: 50px;
        border-bottom: 1px solid #ccc;
        margin-top: 20px;
        font-size: 0.8rem;
        text-align: center;
    }
    .no-print {
        margin-top: 20px;
        text-align: center;
    }
    .no-print button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
    }
    @media print {
        body { background-color: #fff; }
        .no-print { display: none; }
        .bulletin-container { box-shadow: none; padding: 0; border: none; }
    }
</style>

    <div class="bulletin-container">
    <h2 class="exam-title">BULLETIN DE NOTES - {{ prop($ex, 'name') }} - {{ $year }}</h2>    @if ($sr && $sr->user)
        <div class="student-details">
            <div class="detail-item"><strong>ÉTUDIANT:</strong> {{ strtoupper(prop($sr->user, 'name')) }}</div>
            <div class="detail-item"><strong>CLASSE:</strong> {{ strtoupper(prop($my_class, 'name')) }}</div>
            <div class="detail-item"><strong>SECTION:</strong> {{ strtoupper(prop($sr->section, 'name', 'N/A')) }}</div>
            <div class="detail-item"><strong>N° MATRICULE:</strong> {{ prop($sr, 'adm_no') }}</div>
            <div class="detail-item"><strong>TRIMESTRE:</strong> {!! strtoupper(Mk::getSuffix(prop($ex, 'term'))) !!}</div>
            <div class="detail-item"><strong>ANNÉE SCOLAIRE:</strong> {{ $year }}</div>
        </div>

        <table class="marks-table">
            <thead>
                <tr>
                    <th>Matières</th>
                    <th>DS1 (20)</th>
                    <th>DS2 (20)</th>
                    <th>Examen (20)</th>
                    <th>Moyenne (/20)</th>
                    <th>Coeff</th>
                    <th>Total</th>
                    <th>Appréciations</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $sub)
                    <tr>
                        <td class="subject-name">{{ prop($sub, 'name') }}</td>
                        @php
                            $mk = $marks->where('subject_id', prop($sub, 'id'))->where('exam_id', prop($ex, 'id'))->first();
                            $t1 = '-'; $t2 = '-'; $exm = '-';
                            $moyen_sans_coef = 0;
                            $moyen_avec_coef = 0;
                            $display_comment = 'Aucune note';

                            if ($mk) {
                                $t1 = is_numeric($mk->t1) ? number_format($mk->t1, 2) : '-';
                                $t2 = is_numeric($mk->t2) ? number_format($mk->t2, 2) : '-';
                                $exm = is_numeric($mk->exm) ? number_format($mk->exm, 2) : '-';
                                
                                $values = array_filter([$mk->t1, $mk->t2, $mk->exm], 'is_numeric');
                                $sum = array_sum($values);
                                $count = count($values);
                                $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                                $moyen_avec_coef = $moyen_sans_coef * prop($sub, 'coef', 0);
                                $display_comment = $mk->comment ?? ($count > 0 ? \App\Helpers\MarkComment::getComment($moyen_sans_coef) : 'Aucune note');
                            }
                        @endphp
                        <td>{{ $t1 }}</td>
                        <td>{{ $t2 }}</td>
                        <td>{{ $exm }}</td>
                        <td class="grade">{{ number_format($moyen_sans_coef, 2) }}</td>
                        <td>{{ prop($sub, 'coef') }}</td>
                        <td class="grade">{{ number_format($moyen_avec_coef, 2) }}</td>
                        <td>{{ $display_comment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                <p>{!! Mk::getSuffix($student_rank) !!} / {{ $total_students }}</p>
            </div>
        </div>

        <div class="footer-section">
            <div class="comments">
                <h4>Commentaires du Conseil de Classe</h4>
                <p><strong>Professeur Principal:</strong> {{ prop($rang, 't_comment', '...') }}</p>
                <p><strong>Proviseur/Directeur:</strong> {{ prop($rang, 'p_comment', '...') }}</p>
            </div>
            <div class="signatures">
                <h4>Signatures</h4>
                <div class="signature-line">Parent/Tuteur</div>
                <div class="signature-line">Prof. Principal</div>
                <div class="signature-line">Le Proviseur</div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">Impossible de charger les données de l'étudiant.</div>
    @endif

    <div class="no-print">
        <button onclick="window.print()">🖨️ Imprimer le bulletin</button>
    </div>
</div>
@endsection
