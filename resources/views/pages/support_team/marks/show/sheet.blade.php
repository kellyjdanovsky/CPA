
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
                    <td>{{ $mk->grade ? $mk->grade->remark : '-' }}</td>
                @endforeach
                <script>
                    $(document).ready(function() {
                        var totalpoint = 0;
                        var total_coef = 0;


                        $(".notetotalaveccoef-{{$ex->id}}").each(function() {
                            var value = parseFloat($(this).text());
                            totalpoint += value;
                        });

                        $(".coef-{{$ex->id}}").each(function() {
                            var value = parseFloat($(this).text());
                            total_coef += value;
                        });


                        var moyenne = totalpoint / total_coef

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


{{-- Moyene NOTE AVEC COEF SUR TOUTE MATIERE --}}
