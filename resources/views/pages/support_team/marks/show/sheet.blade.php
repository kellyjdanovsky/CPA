
<table class="table table-bordered table-responsive text-center">
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
                    <td class="notetotalaveccoef-{{$ex->id}}">
                        {{-- @php
                            $multipliedValue = 0;
                            if ($ex->term === 1) {
                                $multipliedValue = ($mk->tex1 / 3) * $sub->coef;
                            } elseif ($ex->term === 2) {
                                $multipliedValue = ($mk->tex2 / 3) * $sub->coef;
                            } elseif ($ex->term === 3) {
                                $multipliedValue = ($mk->tex3 / 3) * $sub->coef;
                            }
                        @endphp --}}

                        @php
                            $multipliedValue = 0;
                            if ($ex->term === 1) {
                                $multipliedValue = $mk->tex1;
                            } elseif ($ex->term === 2) {
                                $multipliedValue = $mk->tex2;
                            } elseif ($ex->term === 3) {
                                $multipliedValue = $mk->tex3;
                            }
                        @endphp
                        {{ number_format($multipliedValue, 1) }}
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
                        var totalpoint = 0;
                        var total_coef = 0;

                        // Parcourir chaque ligne de matière
                        $(".notetotalaveccoef-{{$ex->id}}").each(function(index) {
                            var value = parseFloat($(this).text());
                            
                            // Vérifier si la valeur est différente de zéro (matière avec au moins une note)
                            if (!isNaN(value) && value > 0) {
                                totalpoint += value;
                                
                                // Récupérer le coefficient correspondant à cette matière
                                var coef = parseFloat($(".coef-{{$ex->id}}").eq(index).text());
                                if (!isNaN(coef)) {
                                    total_coef += coef;
                                }
                            }
                        });

                        // Calculer la moyenne seulement si le total des coefficients est supérieur à zéro
                        var moyenne = (total_coef > 0) ? (totalpoint / total_coef) : 0;

                        var soratra = "TOTALE DES POINTS OBTENUS: " + totalpoint.toFixed(2);
                        var soratra2 = "Moyenne " + moyenne.toFixed(2);

                        // Display the concatenated total value wherever you want
                        $(".P_totalpoi-{{$ex->id}}").text(soratra);
                        $(".P_moyenne-{{$ex->id}}").text(soratra2);
                    });
                </script>
            </tr>
            @endforeach
            <tr>
                @php
                    use App\Models\StudentRecord;
                    use App\Models\ExamRecord;


                $total_eleve = StudentRecord::where('my_class_id', $my_class->id)->count();
                $position = Mk::getSuffix($exr->where('student_id', $sr->user->id)->first()->pos) ?: '-';

                //dd($exr->where('student_id', $sr->user->id));




                $rang = ExamRecord::where('my_class_id', $my_class->id)->where('student_id', $sr->user->id)->where('exam_id',$ex->id)->first();



                $positionEnFrancais = '';

                // Tableau de correspondance pour les numéros ordinaux
                $positionsEnFrancais = [
                    '1st' => '1er',
                    '2nd' => '2e',
                    '3rd' => '3e',
                    '4th' => '4e',
                    '5th' => '5e',
                    '6th' => '6e',
                    '7th' => '7e',
                    '8th' => '8e',
                    '9th' => '9e',
                    '10th' => '10e',
                    '11th' => '11e',
                    '12th' => '12e',
                    '13th' => '13e',
                    '14th' => '14e',
                    '15th' => '15e',
                    '16th' => '16e',
                    '17th' => '17e',
                    '18th' => '18e',
                    '19th' => '19e',
                    '20th' => '20e',
                ];

                // Vérifiez si la position est dans le tableau de correspondance
                if (array_key_exists($position, $positionsEnFrancais)) {
                    $positionEnFrancais = $positionsEnFrancais[$position];
                } else {
                    $positionEnFrancais = str_replace(['st', 'nd', 'rd', 'th'], ['er', 'éme', 'éme', 'éme'], $position);
                }
            @endphp

            <td colspan="4" class="P_totalpoi-{{$ex->id}}"><strong>TOTAL DES POINTS OBTENUS :</strong></td>
            <td colspan="3" class="P_moyenne-{{$ex->id}}"><strong>MOYENNE FINALE :</strong> {{ $exr->ave }}</td>
            <td colspan="2"><strong>RANG: {!! $positionEnFrancais !!} / {!! $total_eleve !!}</strong></td>
        </tr>
    </tbody>
</table>

{{-- TOTAL NOTE AVEC COEF SUR TOUTE MATIERE --}}

<!-- Commentaire général de l'enseignant -->
<div class="card mt-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Commentaire de l'enseignant</h5>
        @if(Qs::userIsTeamSAT())
            <button class="btn btn-sm btn-outline-primary edit-general-comment" data-exam-id="{{ $ex->id }}" data-comment-type="teacher">
                <i class="icon-pencil"></i> Modifier
            </button>
        @endif
    </div>
    <div class="card-body">
        <div id="commentaire_general_display_{{ $ex->id }}" class="p-3 bg-light rounded">
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
<div class="card mt-3">
    <div class="card-header bg-light">
        <h5 class="card-title">Moyennes annuelles</h5>
    </div>
    <div class="card-body">
        @php
            // Récupérer tous les examens créés pour cette année
            $all_exams = \App\Models\Exam::where('year', $year)->orderBy('term')->get();
        @endphp

        <div class="row">
            @foreach($all_exams as $exam)
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Moyenne {{ $exam->name }} :</label>
                        <div class="form-control-plaintext exam-average" data-exam-id="{{ $exam->id }}" id="moyenne_exam_{{ $exam->id }}">
                            Calcul en cours...
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-3">
            <div class="col-12 text-center">
                <h4 class="font-weight-bold">Moyenne générale annuelle : <span id="moyenne_annuelle_finale">Calcul en cours...</span></h4>
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
