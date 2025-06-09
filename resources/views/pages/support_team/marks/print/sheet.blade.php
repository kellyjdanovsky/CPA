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
                        
                        // Générer le commentaire basé sur la moyenne
                        $comment = \App\Helpers\MarkComment::getComment($moyen_sans_coef);
                        $commentColor = \App\Helpers\MarkComment::getCommentColor($moyen_sans_coef);
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
            <td colspan="3" class="P_moyenne"><strong>MOYENNE FINALE :</strong></td>
            <td colspan="2"><strong>RANG: {!! $rang->pos !!} / {!! $total_eleve !!}</strong></td>
        </tr>
        
        @if($ex->term == 3)
        <tr>
            <td colspan="9" style="text-align: center; padding: 10px; background-color: #f5f5f5;">
                <div style="padding: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <div style="flex: 1; text-align: left; font-size: 10px;">
                            <strong>MOY. 1<sup>er</sup> TRIM:</strong> 11.64/20
                        </div>
                        <div style="flex: 1; text-align: center; font-size: 10px;">
                            <strong>MOY. 2<sup>ème</sup> TRIM:</strong> 4.00/20
                        </div>
                        <div style="flex: 1; text-align: right; font-size: 10px;">
                            <strong>MOY. 3<sup>ème</sup> TRIM:</strong> <span class="current_term_average">0.00</span>/20
                        </div>
                    </div>
                    
                    <div style="text-align: center; font-weight: bold; color: #333; border-top: 1px solid #ddd; padding-top: 5px; font-size: 11px;">
                        <strong>MOYENNE GÉNÉRALE ANNUELLE:</strong> 
                        <span class="annual_average">Calcul en cours...</span>
                    </div>
                    
                    <script>
                        $(document).ready(function() {
                            // Attendre que la moyenne du trimestre actuel soit calculée
                            setTimeout(function() {
                                // Récupérer la moyenne du trimestre actuel
                                var moyenneTrimestre3 = window.moyenne_generale || 0;
                                
                                // Définir les moyennes des trimestres précédents (valeurs fixes)
                                var moyenneTrimestre1 = 11.64;
                                var moyenneTrimestre2 = 4.00;
                                
                                // Mettre à jour l'affichage de la moyenne du 3ème trimestre
                                $(".current_term_average").text(moyenneTrimestre3.toFixed(2));
                                
                                // Calculer la moyenne annuelle
                                var moyenneAnnuelle = (moyenneTrimestre1 + moyenneTrimestre2 + moyenneTrimestre3) / 3;
                                
                                // Afficher la moyenne annuelle
                                $(".annual_average").text(moyenneAnnuelle.toFixed(2) + "/20");
                            }, 600); // Attendre un peu plus que le script principal (500ms)
                        });
                    </script>
                </div>
            </td>
        </tr>
        @endif
    </tbody>
</table>

{{-- TOTAL NOTE AVEC COEF SUR TOUTE MATIERE --}}
<script>
    $(document).ready(function() {
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

        var soratra = "TOTALE DES POINTS OBTENUS: " + totalpoint.toFixed(2);
        var soratra2 = "Moyenne " + moyenne.toFixed(2);

        // Display the concatenated total value wherever you want
        $(".P_totalpoi").text(soratra);
        $(".P_moyenne").text(soratra2);
        
        // Stocker la moyenne pour le commentaire général
        window.moyenne_generale = moyenne;
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
<div style="margin-top: 5px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 5px; font-size: 11px;">Décision de fin d'année</h4>
    
    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
        <div style="width: 45%;">
            <div style="font-weight: bold; font-size: 10px; margin-bottom: 2px;">Décision :</div>
            <div style="font-size: 10px; padding: 2px; border: 1px solid #eee; border-radius: 4px; background-color: #f9f9f9;">
                @if(isset($rang->decision) && $rang->decision == 'passant')
                    Passant(e)
                @elseif(isset($rang->decision) && $rang->decision == 'redoublant')
                    Redoublant(e)
                @else
                    Non définie
                @endif
            </div>
        </div>
        
        <div style="width: 45%;">
            <div style="font-weight: bold; font-size: 10px; margin-bottom: 2px;">Classe de passage :</div>
            <div style="font-size: 10px; padding: 2px; border: 1px solid #eee; border-radius: 4px; background-color: #f9f9f9;">
                @if(isset($rang->next_class_id))
                    @php
                        $next_class = \App\Models\MyClass::find($rang->next_class_id);
                    @endphp
                    {{ $next_class ? $next_class->name : 'Non définie' }}
                @else
                    Non définie
                @endif
            </div>
        </div>
    </div>
    
    @if(isset($rang->observations) && !empty($rang->observations))
    <div>
        <div style="font-weight: bold; font-size: 10px; margin-bottom: 2px;">Observations :</div>
        <div style="font-size: 10px; padding: 2px; border: 1px solid #eee; border-radius: 4px; background-color: #f9f9f9; min-height: 20px;">
            {{ $rang->observations }}
        </div>
    </div>
    @endif
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

