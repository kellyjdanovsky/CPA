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
                    <td>
                        @if ($ex->term === 10)
                        {{ number_format($mk->tex1 / $sub->coef, 1) }}
                    @elseif ($ex->term === 2)
                 
                        {{ number_format($mk->tex2 / $sub->coef, 1) }}
                    @elseif ($ex->term === 3)
                        {{ number_format($mk->tex3 / $sub->coef, 1) }}
                    @else
                        {{ '-' }}
                    @endif
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

                    <td class="coef">{{ $sub->coef }} sdfsd</td>
                    <td class="notetotalaveccoef">
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
                        {{ number_format($multipliedValue, 2) }}
                    </td>
                    <td>{{ $mk->grade ? $mk->grade->remark : '-' }}</td>
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
         @if ($rang->pos == 1)
            <td colspan="2"><strong>RANG: {!! $rang->pos !!} er / {!! $total_eleve !!}</td>
             
         @else
            <td colspan="2"><strong>RANG: {!! $rang->pos !!} ème / {!! $total_eleve !!}</td>
             
         @endif
        </tr>
    </tbody>
</table>

{{-- TOTAL NOTE AVEC COEF SUR TOUTE MATIERE --}}
<script>
    $(document).ready(function() {
        var totalpoint = 0;
        var total_coef = 0;
        //alert("Merci")
        $(".notetotalaveccoef").each(function() {
            var value = parseFloat($(this).text());
            totalpoint += value;
        });

        $(".coef").each(function() {
            var value = parseFloat($(this).text());
            total_coef += value;
        });
        //alert(total_coef)
        //alert(totalpoint)

        var moyenne = totalpoint / total_coef

        var soratra = "TOTALE DES POINTS OBTENUS: " + totalpoint.toFixed(2);
        var soratra2 = "Moyenne " + moyenne.toFixed(2);

        // Display the concatenated total value wherever you want
        $(".P_totalpoi").text(soratra);
        $(".P_moyenne").text(soratra2);
    });
</script>
