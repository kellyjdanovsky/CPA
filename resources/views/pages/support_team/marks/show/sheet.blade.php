
<table class="table table-bordered table-responsive text-center">
    <thead>
        <tr>
            <th rowspan="2">N°</th>
            <th rowspan="2">MATIÈRES</th>
            <th rowspan="2">CA1<br>(20)</th>
            <th rowspan="2">CA2<br>(20)</th>
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
                    <td>
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
                            
                            // Générer le commentaire basé sur la moyenne
                            $comment = \App\Helpers\MarkComment::getComment($moyen_sans_coef);
                            $commentColor = \App\Helpers\MarkComment::getCommentColor($moyen_sans_coef);
                        @endphp
                        
                        <span class="{{ $commentColor }}">{{ $comment ?: ($mk->comment ?: '-') }}</span>
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
    <div class="card-header bg-light">
        <h5 class="card-title">Commentaire de l'enseignant</h5>
    </div>
    <div class="card-body">
        <div id="commentaire_general_{{ $ex->id }}" class="p-3 bg-light rounded">
            <!-- Le commentaire sera inséré ici par JavaScript -->
        </div>
    </div>
</div>

@if($ex->term == 3)
<!-- Affichage de la moyenne générale annuelle -->
<div class="card mt-3">
    <div class="card-header bg-light">
        <h5 class="card-title">Moyennes annuelles</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Moyenne 1<sup>er</sup> trimestre :</label>
                    <div class="form-control-plaintext">11.64/20</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Moyenne 2<sup>ème</sup> trimestre :</label>
                    <div class="form-control-plaintext">4.00/20</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Moyenne 3<sup>ème</sup> trimestre :</label>
                    <div class="form-control-plaintext" id="moyenne_trimestre3_{{ $ex->id }}">Calcul en cours...</div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <h4 class="font-weight-bold">Moyenne générale annuelle : <span id="moyenne_annuelle_{{ $ex->id }}">Calcul en cours...</span></h4>
            </div>
        </div>
    </div>
</div>

<!-- Section de passage/redoublement (uniquement pour le 3ème trimestre) -->
<div class="card mt-3">
    <div class="card-header bg-light">
        <h5 class="card-title">Décision de fin d'année</h5>
    </div>
    <div class="card-body">
        <form id="decision_form_{{ $ex->id }}" action="{{ route('marks.save_decision') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $sr->user->id }}">
            <input type="hidden" name="exam_id" value="{{ $ex->id }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="decision_{{ $ex->id }}" class="font-weight-bold">Décision :</label>
                        <select id="decision_{{ $ex->id }}" name="decision" class="form-control">
                            <option value="passant">Passant(e)</option>
                            <option value="redoublant">Redoublant(e)</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="next_class_{{ $ex->id }}" class="font-weight-bold">Classe de passage :</label>
                        <select id="next_class_{{ $ex->id }}" name="next_class" class="form-control">
                            <option value="">-- Sélectionner une classe --</option>
                            @foreach($all_classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="observations_{{ $ex->id }}" class="font-weight-bold">Observations :</label>
                <textarea id="observations_{{ $ex->id }}" name="observations" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Enregistrer la décision</button>
            </div>
        </form>
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
            
            // Si c'est le 3ème trimestre, calculer et afficher la moyenne annuelle
            @if($ex->term == 3)
                // Définir les moyennes des trimestres précédents (valeurs fixes)
                var moyenneTrimestre1 = 11.64;
                var moyenneTrimestre2 = 4.00;
                var moyenneTrimestre3 = moyenne_sur_20;
                
                // Mettre à jour l'affichage de la moyenne du 3ème trimestre
                $("#moyenne_trimestre3_{{ $ex->id }}").text(moyenneTrimestre3.toFixed(2) + "/20");
                
                // Calculer la moyenne annuelle
                var moyenneAnnuelle = (moyenneTrimestre1 + moyenneTrimestre2 + moyenneTrimestre3) / 3;
                
                // Afficher la moyenne annuelle
                $("#moyenne_annuelle_{{ $ex->id }}").text(moyenneAnnuelle.toFixed(2) + "/20");
            @endif
        }, 500); // Attendre 500ms pour s'assurer que la moyenne est calculée
    });
</script>

{{-- Moyene NOTE AVEC COEF SUR TOUTE MATIERE --}}
