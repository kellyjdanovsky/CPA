<!DOCTYPE html>
<html>
<head>
    <title>Bulletin de notes de l'élève - {{ $sr->user->name }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/my_print.css') }}" />
    <style>
        .page-break {
            page-break-after: always;
        }
        .exam-header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div id="print" xmlns:margin-top="http://www.w3.org/1999/xhtml">
        {{-- Logo et détails de l'école --}}
        <table width="100%">
            <tr>
                <td><img src="{{ $s['logo'] }}" style="max-height : 100px;"></td>

                <td style="text-align: center; ">
                    <strong><span style="color: #1b0c80; font-size: 25px;">{{ strtoupper(Qs::getSetting('system_name')) }}</span></strong><br/>
                    <strong><span
                                style="color: #000; font-size: 15px;"><i>{{ ucwords($s['address']) }}</i></span></strong><br/>
                    <strong><span style="color: #000; font-size: 15px;"> BULLETIN DE NOTES {{ '('.strtoupper($class_type->name).')' }}
                    </span></strong>
                </td>
                <td style="width: 100px; height: 100px; float: left;">
                    {{-- <img src="{{ $sr->user->photo }}"
                         alt="..."  width="100" height="100">--}} 
                </td>
            </tr>
        </table>
        <br/>

        {{-- Logo de fond --}}
        <div style="position: relative; text-align: center; ">
            <img src="{{ $s['logo'] }}"
                 style="max-width: 500px; max-height:600px; margin-top: 60px; position:absolute; opacity: 0.2; margin-left: auto;margin-right: auto; left: 0; right: 0;" />
        </div>

        {{-- <!--NOM, CLASSE ET AUTRES INFORMATIONS --> --}}
        <table style="width:100%; border-collapse:collapse; ">
            <tbody>
                <tr>
                    <td><strong>NOM:</strong> {{ strtoupper($sr->user->name) }}</td>
                    <td><strong>NUMÉRO D'ADMISSION:</strong> {{ $sr->adm_no }}</td>
                    <td><strong>CLASSE:</strong> {{ strtoupper($my_class->name) }}</td>
                </tr>
                <tr>
                    <td><strong>BULLETIN DE NOTES POUR</strong> ANNÉE SCOLAIRE</td>
                    <td><strong>ANNÉE ACADÉMIQUE:</strong> {{ $year }}</td>
                    <td><strong>ÂGE:</strong>
                        {{ $sr->age ?: ($sr->user->dob ? date_diff(date_create($sr->user->dob), date_create('now'))->y : '-') }}
                    </td>
                </tr>
            </tbody>
        </table>

        @foreach($exams as $exam)
            @if(isset($all_marks[$exam->id]) && isset($all_exam_records[$exam->id]))
                <div class="exam-header">
                    {{ strtoupper($exam->name) }} - {{ Mk::getSuffix($exam->term) }} TRIMESTRE
                </div>

                <table style="width:100%; border-collapse:collapse; border: 1px solid #000; margin: 10px auto;" border="1">
                    {{-- Tableau des examens --}}
                    <thead>
                        <tr>
                            <th rowspan="2">N°</th>
                            <th rowspan="2">MATIÈRES</th>
                            <th rowspan="2">DS1<br>(20)</th>
                            <th rowspan="2">DS2<br>(20)</th>
                            <th rowspan="2">EXAMENS<br>(20)</th>
                            <th rowspan="2">Moyenne (/20)<br></th>
                            <th rowspan="2">coefficient</th>
                            <th rowspan="2">total avec Coef</th>
                            <th rowspan="2">REMARQUES</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $marks = $all_marks[$exam->id];
                            $exr = $all_exam_records[$exam->id];
                            $totalPoints = 0;
                            $totalCoef = 0;
                        @endphp

                        @foreach ($subjects as $sub)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sub->name }}</td>

                                @php
                                    $subjectMark = $marks->where('subject_id', $sub->id)->where('exam_id', $exam->id)->first();
                                @endphp

                                @if($subjectMark)
                                    <td>{{ $subjectMark->t1 ?: '-' }}</td>
                                    <td>{{ $subjectMark->t2 ?: '-' }}</td>
                                    <td>{{ $subjectMark->exm ?: '-' }}</td>

                                    @php
                                        // Récupérer les valeurs de t1, t2, et exm
                                        $t1 = $subjectMark->t1 ?: 0;
                                        $t2 = $subjectMark->t2 ?: 0;
                                        $exm = $subjectMark->exm ?: 0;

                                        // Calcul de la moyenne sans coefficient
                                        $values = [$t1, $t2, $exm];
                                        $sum = array_sum($values);
                                        $count = count(array_filter($values, fn($value) => $value > 0));

                                        // Si count est supérieur à 0, on calcule la moyenne
                                        $moyenSansCoef = $count > 0 ? $sum / $count : 0;
                                        $moyenAvecCoef = $moyenSansCoef * $sub->coef;
                                        
                                        // Ajouter au total
                                        $totalPoints += $moyenAvecCoef;
                                        $totalCoef += $sub->coef;
                                    @endphp

                                    <td>{{ number_format($moyenSansCoef, 2) }}</td>
                                    <td>{{ $sub->coef }}</td>
                                    <td>{{ number_format($moyenAvecCoef, 2) }}</td>
                                    <td>
                                        @php
                                            // Récupérer les valeurs de t1, t2, et exm
                                            $t1 = $subjectMark->t1 ?: 0;
                                            $t2 = $subjectMark->t2 ?: 0;
                                            $exm = $subjectMark->exm ?: 0;

                                            // Calcul de la moyenne sans coefficient
                                            $values = [$t1, $t2, $exm];
                                            $sum = array_sum($values);
                                            $count = count(array_filter($values, fn($value) => $value > 0));
                                            $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                                            
                                            // Générer le commentaire basé sur la moyenne
                                            $comment = \App\Helpers\MarkComment::getComment($moyen_sans_coef);
                                            $commentColor = \App\Helpers\MarkComment::getCommentColor($moyen_sans_coef);
                                        @endphp
                                        
                                        <span class="{{ $commentColor }}">{{ $comment ?: ($subjectMark->comment ?: '-') }}</span>
                                    </td>
                                @else
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>{{ $sub->coef }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                @endif
                            </tr>
                        @endforeach

                        @php
                            $totalEleve = \App\Models\StudentRecord::where('my_class_id', $my_class->id)
                                ->where('section_id', $section_id)
                                ->where('grad_date', null)
                                ->count();
                            
                            $moyenne = $totalCoef > 0 ? $totalPoints / $totalCoef : 0;
                        @endphp

                        <tr>
                            <td colspan="4"><strong>TOTAL DES POINTS OBTENUS : {{ number_format($totalPoints, 2) }}</strong></td>
                            <td colspan="3"><strong>MOYENNE FINALE : {{ number_format($moyenne, 2) }}</strong></td>
                            <td colspan="2"><strong>RANG: {{ $exr->pos }} / {{ $totalEleve }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                {{-- Commentaires pour cet examen --}}
                <table style="width:100%; border-collapse:collapse; border: 1px solid #000; margin: 10px auto;" border="1">
                    <tbody>
                        <tr>
                            <td style="width:50%"><strong>COMMENTAIRE DU PROFESSEUR:</strong></td>
                            <td style="width:50%"><strong>COMMENTAIRE DU DIRECTEUR:</strong></td>
                        </tr>
                        <tr>
                            <td style="height:50px; padding: 10px;">
                                @php
                                    // Calculer la moyenne générale pour cet examen
                                    $totalPoints = 0;
                                    $totalCoef = 0;
                                    
                                    foreach ($subjects as $sub) {
                                        if (isset($all_marks[$exam->id])) {
                                            $subjectMarks = $all_marks[$exam->id]->where('subject_id', $sub->id);
                                            if ($subjectMarks->count() > 0) {
                                                $subjectMark = $subjectMarks->first();
                                                
                                                // Calculer la moyenne sans coefficient
                                                $t1 = $subjectMark->t1 ?: 0;
                                                $t2 = $subjectMark->t2 ?: 0;
                                                $exm = $subjectMark->exm ?: 0;
                                                
                                                $values = [$t1, $t2, $exm];
                                                $sum = array_sum($values);
                                                $count = count(array_filter($values, fn($value) => $value > 0));
                                                $moyenSansCoef = $count > 0 ? $sum / $count : 0;
                                                
                                                // Appliquer le coefficient
                                                $moyenAvecCoef = $moyenSansCoef * $sub->coef;
                                                
                                                $totalPoints += $moyenAvecCoef;
                                                $totalCoef += $sub->coef;
                                            }
                                        }
                                    }
                                    
                                    $moyenneGenerale = $totalCoef > 0 ? $totalPoints / $totalCoef : 0;
                                    
                                    // Générer le commentaire basé sur la moyenne générale
                                    $commentaire = \App\Helpers\MarkComment::getGeneralComment($moyenneGenerale);
                                    $commentaireClass = \App\Helpers\MarkComment::getCommentColor($moyenneGenerale);
                                @endphp
                                
                                <span style="font-weight: bold;">{{ $commentaire }}</span>
                                <br>
                                <span style="font-style: italic; font-size: 0.9em;">Moyenne générale: {{ number_format($moyenneGenerale, 2) }}/20</span>
                            </td>
                            <td style="height:50px; padding: 10px;">{{ $exr->p_comment }}</td>
                        </tr>
                    </tbody>
                </table>

                @if(!$loop->last)
                    <div class="page-break"></div>
                @endif
            @endif
        @endforeach

        {{-- TRAITS - PSYCHOMOTEURS ET AFFECTIFS --}}
        @if(isset($skills) && $skills->count() > 0)
            <div class="page-break"></div>
            <div class="exam-header">
                COMPÉTENCES PSYCHOMOTRICES ET AFFECTIVES
            </div>
            @include('pages.support_team.marks.print.skills')
        @endif

        <div style="margin-top: 25px; clear: both;"></div>

        {{-- Signature --}}
        <table style="width:100%; border-collapse:collapse; margin-top: 20px;">
            <tr>
                <td style="width:33.3%; text-align:center;">
                    <div style="margin-top:40px;">_________________________</div>
                    <div>Signature du Professeur</div>
                </td>
                <td style="width:33.3%; text-align:center;">
                    <div style="margin-top:40px;">_________________________</div>
                    <div>Signature du Directeur</div>
                </td>
                <td style="width:33.3%; text-align:center;">
                    <div style="margin-top:40px;">_________________________</div>
                    <div>Signature du Parent</div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    window.print();
</script>
</body>
</html>