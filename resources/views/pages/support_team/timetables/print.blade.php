<!DOCTYPE html>
<html>
<head>
    <title>Emploi du temps - {{ $ttr->name.' - '.$ttr->year }}</title>
    <style>
        @media print {

            td, th {
                padding: 20px 5px;
                text-align: center;
                font-size: 14px;
            }

            @page {
                size: landscape;   /* auto est la valeur initiale */
                margin: 0;  /* cela affecte la marge dans les paramètres de l'imprimante */
            }

            html {
                background-color: #FFFFFF;
                margin: 0; /* cela affecte la marge sur le HTML avant l'envoi à l'imprimante */
            }

            body {
                margin: 0 10mm; /* marge que vous souhaitez pour le contenu */
            }
        }

        td {
            text-align: center;
        }

    </style>
</head>
<body>
<div class="container">
    <div id="print" xmlns:margin-top="http://www.w3.org/1999/xhtml">
        {{-- Détails du logo et de l'école --}}
        <table width="100%">
            <tr>
                <td>
                    <strong><span style="color: #1b0c80; font-size: 25px;">{{ strtoupper(config('app.name')) }}</span></strong><br/>
                    {{-- <strong><span style="color: #1b0c80; font-size: 20px;">MINNA, ÉTAT DE NIGER</span></strong><br/> --}}
                    <strong><span style="color: #000; font-size: 15px;"><i>{{ ucwords($s['address']) }}</i></span></strong><br/>
                    <strong><span style="color: #000; text-decoration: underline; font-size: 15px;"><i>{{ config('app.url') }}/i></span></strong>
                    <br /> <br />
                    <strong><span style="color: #000; font-size: 15px;">EMPLOI DU TEMPS POUR {{ strtoupper($my_class->name. ' ('.$ttr->year.')' ) }}
                    </span></strong>
                </td>
            </tr>
        </table>

        {{-- Logo de fond --}}
        <div style="position: relative;  text-align: center; ">
            <img src="{{ $s['logo'] }}"
                 style="max-width: 500px; max-height:600px; margin-top: 60px; position:absolute ; opacity: 0.2; margin-left: auto;margin-right: auto; left: 0; right: 0;" />
        </div>

        {{-- Début des onglets --}}
        <table cellpadding="20" style="width:100%; border-collapse:collapse; border: 1px solid #000; margin: 10px auto;" border="1">
            <thead>
            <tr>
                <th rowspan="2">Heure <i class="icon-arrow-right7 ml-2"></i> <br> Date<i class="icon-arrow-down7 ml-2"></i>
                </th>
                @foreach($time_slots as $tms)
                    <th rowspan="2">{{ $tms->time_from }} <br>
                        {{ $tms->time_to }}
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @foreach($days as $day)
                <tr>
                    @if($ttr->exam_id)
                        <td><strong>{{ date('l', strtotime($day)) }} <br> {{ date('d/m/Y', strtotime($day)) }} </strong></td>
                    @else
                        <td><strong>{{ $day }}</strong></td>
                    @endif
                    @foreach($d_time->where('day', $day) as $dt)
                        <td>{{ $dt['subject'] }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    window.print();
</script>
</body>
</html>
