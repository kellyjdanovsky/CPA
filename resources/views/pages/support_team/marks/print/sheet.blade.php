<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Notes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5cm 0.6cm;
        }
        
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #000;
        }
        
        .bulletin-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        
        /* Filigrane central */
        .bulletin-wrapper::before {
            content: '';
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
            height: 320px;
            background-image: url('/images/logo_avar.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }
        
        /* Logo en haut à droite */
        .school-logo {
            position: absolute;
            top: 5px;
            right: 8px;
            width: 75px;
            height: 75px;
            z-index: 10;
        }
        
        .school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .bulletin-container {
            position: relative;
            z-index: 1;
        }
        
        .student-details {
            background: #f8f8f8;
            padding: 4px 6px;
            border: 1px solid #333;
            margin-bottom: 4px;
        }
        
        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }
        
        .detail-item {
            background: white;
            padding: 3px 5px;
            border-left: 3px solid #333;
            font-size: 8.5px;
            line-height: 1.3;
        }
        
        .detail-item strong {
            font-weight: 700;
            font-size: 8px;
            display: block;
            margin-bottom: 2px;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            background: white;
            margin-bottom: 4px;
        }
        
        .marks-table th {
            background: white;
            color: #000;
            padding: 5px 3px;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            border: 2px solid #000;
            line-height: 1.2;
        }
        
        .marks-table td {
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #333;
            line-height: 1.2;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .subject-name {
            text-align: left !important;
            font-weight: 600;
            font-size: 9.5px;
            padding-left: 5px !important;
        }
        
        .grade {
            font-weight: 700;
            font-size: 9.5px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            padding: 4px 6px;
            background: #f8f8f8;
            margin-bottom: 4px;
            border: 1px solid #333;
        }
        
        .summary-item {
            background: white;
            padding: 5px;
            text-align: center;
            border-top: 3px solid #333;
        }
        
        .summary-item h4 {
            margin: 0 0 2px 0;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .summary-item p {
            margin: 0;
            font-size: 11px;
            font-weight: 800;
        }
        
        .footer-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 6px;
            padding: 4px 6px;
            border-top: 2px solid #333;
        }
        
        .comments h4, .signatures h4 {
            margin: 0 0 3px 0;
            font-size: 9px;
            font-weight: 700;
            padding-bottom: 2px;
            border-bottom: 2px solid #333;
        }
        
        .comments p {
            margin: 0 0 2px 0;
            font-size: 8.5px;
            line-height: 1.3;
        }
        
        .signature-line {
            height: 18px;
            border-bottom: 1px solid #333;
            margin: 3px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 600;
        }
        
        .no-print {
            display: none;
        }
        
        @media screen {
            body {
                background: #f5f5f5;
                padding: 10px;
            }
            
            .bulletin-wrapper {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                padding: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            
            .no-print {
                display: block;
                text-align: center;
                padding: 10px;
            }
            
            .no-print button {
                background: #667eea;
                color: white;
                border: none;
                padding: 10px 25px;
                font-size: 14px;
                font-weight: 700;
                border-radius: 25px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>
@php
    function prop($obj, $prop, $default = '') {
        return (is_object($obj) && isset($obj->$prop)) ? $obj->$prop : $default;
    }

    $school = $s ?? session('school') ?? [];

    if (isset($sr) && $sr && isset($sr->user) && $sr->user) {
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

<div class="bulletin-wrapper">
    <div class="school-logo">
        <img src="/images/logo_avar.png" alt="Logo">
    </div>
    
    <div class="bulletin-container">
    @if (isset($sr) && $sr && isset($sr->user) && $sr->user)
        <div class="student-details">
            <div class="student-details-grid">
                <div class="detail-item"><strong>ÉTUDIANT:</strong> {{ strtoupper(prop($sr->user, 'name')) }}</div>
                <div class="detail-item"><strong>CLASSE:</strong> {{ strtoupper(prop($my_class, 'name')) }}</div>
                <div class="detail-item"><strong>SECTION:</strong> {{ strtoupper(prop($sr->section, 'name', 'N/A')) }}</div>
                <div class="detail-item"><strong>TRIMESTRE:</strong> {!! strtoupper(Mk::getSuffix(prop($ex, 'term'))) !!} - {{ $year ?? date('Y') }}</div>
            </div>
        </div>

        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Matières</th>
                    <th style="width: 8%;">DS1<br>(20)</th>
                    <th style="width: 8%;">DS2<br>(20)</th>
                    <th style="width: 8%;">Ex.<br>(20)</th>
                    <th style="width: 10%;">Moy.<br>(/20)</th>
                    <th style="width: 7%;">Cf</th>
                    <th style="width: 10%;">Total</th>
                    <th style="width: 29%;">Appréciations</th>
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
                                $t1 = is_numeric($mk->t1) ? number_format($mk->t1, 1) : '-';
                                $t2 = is_numeric($mk->t2) ? number_format($mk->t2, 1) : '-';
                                $exm = is_numeric($mk->exm) ? number_format($mk->exm, 1) : '-';
                                
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
                        <td style="font-size: 8px;">{{ $display_comment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-item">
                <h4>Total Points</h4>
                <p>{{ number_format($student_total_points, 2) }}</p>
            </div>
            <div class="summary-item">
                <h4>Moy. Générale</h4>
                <p>{{ number_format($student_weighted_average, 2) }}/20</p>
            </div>
            <div class="summary-item">
                <h4>Moy. Classe</h4>
                <p>{{ number_format($class_average, 2) }}/20</p>
            </div>
            <div class="summary-item">
                <h4>Position</h4>
                <p>{!! Mk::getSuffix($student_rank) !!} / {{ $total_students }}</p>
            </div>
        </div>

        <div class="footer-section">
            <div class="comments">
                <h4>Commentaires</h4>
                <p><strong>Prof. Principal:</strong> {{ prop($rang, 't_comment', '...') }}</p>
                <p><strong>Directeur:</strong> {{ prop($rang, 'p_comment', '...') }}</p>
            </div>
            <div class="signatures">
                <h4>Signatures</h4>
                <div class="signature-line">Parent/Tuteur</div>
                <div class="signature-line">Prof. Principal</div>
                <div class="signature-line">Proviseur</div>
            </div>
        </div>

        {{-- Barème de Notation --}}
        @if(isset($grades) && $grades->count() > 0)
        <div class="grades-scale" style="margin-top: 6px; border: 1px solid #333; padding: 4px; background: #fff;">
            <h4 style="margin: 0 0 4px 0; font-size: 9px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #333; padding-bottom: 2px;">Barème de Notation</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach($grades as $gr)
                    @if(!$gr->class_type_id || $gr->class_type_id == $class_type->id)
                    <div style="font-size: 8px; flex: 1 0 auto;">
                        <strong>{{ $gr->name }}</strong> ({{ $gr->mark_from }} - {{ $gr->mark_to }}): <em>{{ $gr->remark }}</em>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    @else
        <div style="padding: 20px; text-align: center;">
            <strong>Impossible de charger les données de l'étudiant.</strong>
        </div>
    @endif
    </div>
    
    <div class="no-print">
        <button onclick="window.print()">🖨️ Imprimer le Bulletin</button>
    </div>
</div>
</body>
</html>
