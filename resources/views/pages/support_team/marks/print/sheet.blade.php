{{-- <!--NOM, CLASSE ET AUTRES INFORMATIONS --> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<table style="width:100%; border-collapse:collapse; ">
    <tbody>
        <tr>
            <td><strong>NOM:</strong> {{ strtoupper($sr->user->name) }}</td>


            <td><strong>NUMÉRO D'ADMISSION:</strong> {{ $sr->adm_no }}</td>
            <td><strong>CLASSE:</strong> {{ strtoupper($my_class->name) }}</td>
        </tr>
        <tr>
            <td><strong>BULLETIN DE NOTES POUR</strong> {!! strtoupper(Mk::getSuffix($ex->term)) !!} TRIMESTRE</td>
            <td><strong>ANNÉE ACADÉMIQUE:</strong> {{ $ex->year }}</td>
            <td><strong>ÂGE:</strong>
                {{ $sr->age ?: ($sr->user->dob ? date_diff(date_create($sr->user->dob), date_create('now'))->y : '-') }}
            </td>
        </tr>

    </tbody>
</table>


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

            {{-- @if ($ex->term == 3) --}}{{-- 3e trimestre --}}{{--
            <th rowspan="2">TOTAL <br>(100%) 3<sup>e</sup> TRIMESTRE</th>
            <th rowspan="2">1<sup>er</sup> <br> TRIMESTRE</th>
            <th rowspan="2">2<sup>e</sup> <br> TRIMESTRE</th>
            <th rowspan="2">CUM (300%) <br> 1<sup>er</sup> + 2<sup>e</sup> + 3<sup>e</sup></th>
            <th rowspan="2">CUM MOY</th>
            @endif --}}

            <th rowspan="2">coefficient</th>
            <th rowspan="2">total avec Coef</th>
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

                <!-- Calcul de la moyenne sans coefficient -->
                <td class="moyen_sans_coef">
                    @php
                        // Récupérer les valeurs de t1, t2, et exm
                        $t1 = $mk->t1 ?: 0; // Si t1 est null, on le considère comme 0
                        $t2 = $mk->t2 ?: 0; // Si t2 est null, on le considère comme 0
                        $exm = $mk->exm ?: 0; // Si exm est null, on le considère comme 0

                        // Calcul de la moyenne sans coefficient (en ne divisant que si on a des valeurs)
                        $values = [$t1, $t2, $exm];
                        $sum = array_sum($values); // Additionner toutes les notes
                        $count = count(array_filter($values, fn($value) => $value > 0)); // Compter combien de valeurs sont supérieures à 0

                        // Si count est supérieur à 0 (au moins une note), on calcule la moyenne
                        $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                    @endphp

                    {{ number_format($moyen_sans_coef, 2) }} <!-- Afficher la moyenne, formatée avec 2 décimales -->
                </td>

                <td class="coef">{{ $sub->coef }}</td>

                <td class="notetotalaveccoef">
                    @php
                    // Récupérer les valeurs de t1, t2, et exm
                    $t1 = $mk->t1 ?: 0; // Si t1 est null, on le considère comme 0
                    $t2 = $mk->t2 ?: 0; // Si t2 est null, on le considère comme 0
                    $exm = $mk->exm ?: 0; // Si exm est null, on le considère comme 0

                    // Calcul de la moyenne sans coefficient (en ne divisant que si on a des valeurs)
                    $values = [$t1, $t2, $exm];
                    $sum = array_sum($values); // Additionner toutes les notes
                    $count = count(array_filter($values, fn($value) => $value > 0)); // Compter combien de valeurs sont supérieures à 0

                    // Si count est supérieur à 0 (au moins une note), on calcule la moyenne
                    $moyen_sans_coef = $count > 0 ? $sum / $count : 0;
                    $moyen_sans_coef = $moyen_sans_coef * $sub->coef
                @endphp

                {{ number_format($moyen_sans_coef, 2) }} <!-- Afficher la moyenne, formatée avec 2 décimales -->

                </td>

                <td>
                    @php
                        // Récupérer les valeurs de t1, t2, et exm
                        $t1 = $mk->t1 ?: 0;
                        $t2 = $mk->t2 ?: 0;
                        $exm = $mk->exm ?: 0;

                        // Calcul de la moyenne sans coefficient
                        $values = [$t1, $t2, $exm];
                        $sum = array_sum($values);
                        $count = count(array_filter($values, fn($value) => $value > 0));
                        $moyen_sans_coef = $count > 0 ? $sum / $count : 0;

                        // Générer le commentaire basé sur la moyenne seulement si l'étudiant a des notes
                        $comment = '';
                        $commentColor = 'text-muted';

                        // Afficher les remarques seulement si l'étudiant a au moins une note dans DS1, DS2, ou examen
                        if ($count > 0) {
                            $comment = \App\Helpers\MarkComment::getComment($moyen_sans_coef);
                            $commentColor = \App\Helpers\MarkComment::getCommentColor($moyen_sans_coef);
                        }
                    @endphp

                    <span class="{{ $commentColor }}">{{ $comment ?: ($mk->comment ?: '-') }}</span>
                </td>
            @endforeach
        </tr>
    @endforeach


        {{-- TOTAL ELEVE POUR RANG --}}

        @php

            use App\Models\StudentRecord;
            use App\Models\ExamRecord;

            use App\Models\Section;

            // $section = Section::where('my_class_id',$my_class->id)->get();



            $section =  $sr->section_id;


            $total_eleve = StudentRecord::where('my_class_id', $my_class->id)->where('section_id',$section)->where('grad_date',null)->count();
            $rang = ExamRecord::where('my_class_id', $my_class->id)->where('student_id', $sr->user->id)->where('exam_id',$ex->id)->first();
            // dd($total_eleve);
        @endphp

        <tr>
            <td colspan="4" class="P_totalpoi"><strong>TOTAL DES POINTS OBTENUS :</strong></td>
            <td colspan="3" class="P_moyenne-{{$ex->id}}"><strong>MOYENNE FINALE :</strong></td>
            <td colspan="2"><strong>RANG: {!! $rang->pos !!} / {!! $total_eleve !!}</strong></td>
        </tr>
        
        @if($ex->term == 3)
        <tr>
            <td colspan="9" style="padding: 8px; background-color: #f8f9fa; border: 1px solid #ddd;">
                <div style="background-color: white; border: 1px solid #ccc; border-radius: 4px; padding: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="background-color: #e9ecef; padding: 6px; border-radius: 3px; margin-bottom: 8px; text-align: center;">
                        <h5 style="margin: 0; font-size: 11px; font-weight: bold; color: #495057;">MOYENNES ANNUELLES</h5>
                    </div>
                    <div style="padding: 6px;">
                        @php
                            // Récupérer tous les examens créés pour cette année
                            $all_exams_print = \App\Models\Exam::where('year', $year)->orderBy('term')->get();
                        @endphp

                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            @foreach($all_exams_print as $exam_print)
                                <div style="flex: 1; margin: 0 4px; text-align: center;">
                                    <div style="margin-bottom: 3px;">
                                        <label style="font-weight: bold; font-size: 9px; color: #495057; display: block;">{{ $exam_print->name }} :</label>
                                        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 4px; border-radius: 3px; font-size: 10px; font-weight: bold; color: #212529;" class="exam-average" data-exam-id="{{ $exam_print->id }}" id="moyenne_exam_{{ $exam_print->id }}">
                                            Calcul...
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div style="text-align: center; border-top: 1px solid #dee2e6; padding-top: 6px;">
                            <h4 style="margin: 0; font-size: 10px; font-weight: bold; color: #495057;">
                                MOYENNE GÉNÉRALE ANNUELLE :
                                <span id="moyenne_annuelle_finale" style="color: #007bff; font-size: 11px;">Calcul...</span>
                            </h4>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        @endif
    </tbody>
</table>

{{-- TOTAL NOTE AVEC COEF SUR TOUTE MATIERE --}}
<script>
    $(document).ready(function() {
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

            var soratra = "TOTALE DES POINTS OBTENUS: " + totalpoint.toFixed(2);
            var soratra2 = "Moyenne " + moyenne.toFixed(2);

            // Display the concatenated total value wherever you want
            $(".P_totalpoi").text(soratra);
            $(".P_moyenne-{{$ex->id}}").text(soratra2);

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

                    console.log("=== RÉSUMÉ DES MOYENNES ===");
                    console.log("Examens valides: " + validExams);
                    console.log("Total des moyennes: " + totalAverage.toFixed(2));
                    console.log("Moyennes individuelles: " + examAverages.map(avg => avg.toFixed(2)).join(", "));
                    console.log("Calcul: (" + examAverages.map(avg => avg.toFixed(2)).join(" + ") + ") / " + validExams + " = " + (totalAverage / validExams).toFixed(2));

                    // Calculer et afficher la moyenne annuelle
                    if (validExams > 0) {
                        var annualAverage = validateAverage(totalAverage / validExams, "Moyenne annuelle");

                        console.log("Moyenne annuelle finale (PRINT): " + annualAverage.toFixed(2));
                        $("#moyenne_annuelle_finale").text(annualAverage.toFixed(2) + "/20");

                        // Stocker la moyenne annuelle pour utilisation ultérieure et comparaison
                        window.moyenne_annuelle_finale_print = annualAverage;
                        window.moyenne_annuelle_finale = annualAverage;

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
    });
</script>

<!-- Commentaire général de l'enseignant -->
<div style="margin-top: 10px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 5px; font-size: 11px;">Commentaire de l'enseignant</h4>
    <div id="commentaire_general" style="font-style: italic; padding: 5px; background-color: #f9f9f9; border-radius: 5px; font-size: 10px;">
        <!-- Le commentaire sera inséré ici par JavaScript -->
    </div>
    
    <script>
        $(document).ready(function() {
            // Attendre que la moyenne soit calculée
            setTimeout(function() {
                var moyenne = window.moyenne_generale || 0;
                var commentaire = "";
                var commentaireClass = "";
                
                // Déterminer le commentaire en fonction de la moyenne
                if (moyenne >= 0 && moyenne < 5) {
                    commentaire = "Moyenne très faible. Il faut persévérer, chaque progrès compte.";
                    commentaireClass = "text-danger";
                } else if (moyenne >= 5 && moyenne < 8) {
                    commentaire = "Résultats insuffisants, mais des efforts peuvent relancer la dynamique.";
                    commentaireClass = "text-warning";
                } else if (moyenne >= 8 && moyenne < 10) {
                    commentaire = "Moyenne fragile. Du potentiel à développer avec plus de régularité.";
                    commentaireClass = "text-warning";
                } else if (moyenne >= 10 && moyenne < 12) {
                    commentaire = "Moyenne juste. Des bases sont posées, il faut les renforcer.";
                    commentaireClass = "text-primary";
                } else if (moyenne >= 12 && moyenne < 14) {
                    commentaire = "Moyenne correcte. Il faut continuer à s'investir pour consolider les acquis.";
                    commentaireClass = "text-primary";
                } else if (moyenne >= 14 && moyenne < 16) {
                    commentaire = "Bon travail. Un bel investissement à maintenir.";
                    commentaireClass = "text-success";
                } else if (moyenne >= 16 && moyenne < 18) {
                    commentaire = "Très bon niveau. L'élève est sérieux et régulier.";
                    commentaireClass = "text-success";
                } else if (moyenne >= 18 && moyenne <= 20) {
                    commentaire = "Excellent parcours. Un exemple à suivre, bravo !";
                    commentaireClass = "text-purple";
                }
                
                // Afficher le commentaire avec la classe de couleur appropriée
                $("#commentaire_general").html('<span class="' + commentaireClass + '">' + commentaire + '</span>');
            }, 500); // Attendre 500ms pour s'assurer que la moyenne est calculée
        });
    </script>
</div>

<!-- Commentaire du directeur/directrice -->
<div style="margin-top: 5px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 5px; font-size: 11px;">Commentaire du directeur/directrice</h4>
    <div id="commentaire_directeur" style="font-style: italic; padding: 5px; background-color: #f9f9f9; border-radius: 5px; font-weight: bold; font-size: 10px;">
        <!-- Le commentaire sera inséré ici par JavaScript -->
    </div>
    
    <script>
        $(document).ready(function() {
            // Attendre que la moyenne soit calculée
            setTimeout(function() {
                var moyenne = window.moyenne_generale || 0;
                var commentaire = "";
                var commentaireClass = "";
                
                // Déterminer le commentaire du directeur en fonction de la moyenne
                if (moyenne >= 0 && moyenne < 5) {
                    commentaire = "Ne lâche rien, chaque effort t'aidera à avancer.";
                    commentaireClass = "text-danger";
                } else if (moyenne >= 5 && moyenne < 8) {
                    commentaire = "Courage et persévérance mèneront à la réussite.";
                    commentaireClass = "text-warning";
                } else if (moyenne >= 8 && moyenne < 10) {
                    commentaire = "Continue à progresser, tu en es capable.";
                    commentaireClass = "text-warning";
                } else if (moyenne >= 10 && moyenne < 12) {
                    commentaire = "Des bases posées, à renforcer pas à pas.";
                    commentaireClass = "text-primary";
                } else if (moyenne >= 12 && moyenne < 14) {
                    commentaire = "Des efforts visibles, poursuis dans cette voie.";
                    commentaireClass = "text-primary";
                } else if (moyenne >= 14 && moyenne < 16) {
                    commentaire = "Bon travail, garde cette motivation.";
                    commentaireClass = "text-success";
                } else if (moyenne >= 16 && moyenne < 18) {
                    commentaire = "Excellente implication, continue ainsi !";
                    commentaireClass = "text-success";
                } else if (moyenne >= 18 && moyenne <= 20) {
                    commentaire = "Félicitations ! Un parcours remarquable.";
                    commentaireClass = "text-purple";
                }
                
                // Afficher le commentaire avec la classe de couleur appropriée
                $("#commentaire_directeur").html('<span class="' + commentaireClass + '">' + commentaire + '</span>');
            }, 500); // Attendre 500ms pour s'assurer que la moyenne est calculée
        });
    </script>
</div>

@if($ex->term == 3)
<!-- Section de passage/redoublement (uniquement pour le 3ème trimestre) -->
<div style="margin-top: 5px; border: 1px solid #ddd; padding: 8px; border-radius: 5px;">
    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; font-size: 11px; text-align: center;">Décision de fin d'année</h4>

    <div style="margin-bottom: 8px;">
        <span style="font-weight: bold; font-size: 10px;">Décision finale :</span>
        <span style="margin-left: 10px; font-size: 10px;">Passant</span>
        <span style="margin-left: 15px; border-bottom: 1px solid #000; display: inline-block; width: 15px; height: 12px;"></span>
        <span style="margin-left: 15px; font-size: 10px;">ou</span>
        <span style="margin-left: 15px; font-size: 10px;">Redoublant</span>
        <span style="margin-left: 15px; border-bottom: 1px solid #000; display: inline-block; width: 15px; height: 12px;"></span>
    </div>

    <div style="margin-bottom: 8px;">
        <span style="font-weight: bold; font-size: 10px;">Classe :</span>
        <span style="margin-left: 10px; border-bottom: 1px solid #000; display: inline-block; width: 200px; height: 12px;"></span>
    </div>
</div>
@endif

<!-- Signature de l'enseignant et du parent -->
<div style="margin-top: 5px; display: flex; justify-content: space-between;">
    <div style="width: 30%; text-align: center;">
        <div style="border-bottom: 1px solid #000; height: 20px;"></div>
        <p style="margin-top: 2px; font-size: 9px;">Signature de l'enseignant</p>
    </div>
    <div style="width: 30%; text-align: center;">
        <div style="border-bottom: 1px solid #000; height: 20px;"></div>
        <p style="margin-top: 2px; font-size: 9px;">Signature du directeur</p>
    </div>
    <div style="width: 30%; text-align: center;">
        <div style="border-bottom: 1px solid #000; height: 20px;"></div>
        <p style="margin-top: 2px; font-size: 9px;">Signature du parent</p>
    </div>
</div>

