@extends('layouts.print')
@section('page_title', 'Impression Rapport Mensuel - ' . $my_class->name)

@section('content')
<div class="container-fluid mb-4 text-center">
    <h2>Rapport de Présence Mensuel</h2>
    <h4>Classe : {{ $my_class->name }} | Mois : {{ date('m/Y', strtotime($month_year)) }}</h4>
</div>

<table class="table table-bordered table-sm text-center" style="font-size: 11px;">
    <thead>
        <tr>
            <th rowspan="2" class="text-left align-middle" style="width: 150px;">Nom de l'élève</th>
            <th colspan="{{ $days_in_month }}">Jours</th>
            <th colspan="4">Total</th>
        </tr>
        <tr>
            @for($i = 1; $i <= $days_in_month; $i++)
                <th style="padding: 2px;">{{ $i }}</th>
            @endfor
            <th style="padding: 2px;">P</th>
            <th style="padding: 2px;">A</th>
            <th style="padding: 2px;">R</th>
            <th style="padding: 2px;">E</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $sr)
        @php 
            $student_att = $attendances->where('student_id', $sr->user_id);
            $p = $student_att->where('status', 'present')->count();
            $a = $student_att->where('status', 'absent')->count();
            $r = $student_att->where('status', 'retard')->count();
            $e = $student_att->where('status', 'excuse')->count();
        @endphp
        <tr>
            <td class="text-left text-nowrap" style="padding: 2px 5px;">{{ $sr->user->name }}</td>
            @for($i = 1; $i <= $days_in_month; $i++)
                @php 
                    $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $day_att = $student_att->where('date', $date_str)->first();
                    $status_badge = '';
                    
                    if($day_att) {
                        if($day_att->status == 'present') { $status_badge = 'P'; }
                        elseif($day_att->status == 'absent') { $status_badge = 'A'; }
                        elseif($day_att->status == 'retard') { $status_badge = 'R'; }
                        elseif($day_att->status == 'excuse') { $status_badge = 'E'; }
                    }
                @endphp
                <td style="padding: 2px;">{{ $status_badge }}</td>
            @endfor
            <td style="padding: 2px;"><strong>{{ $p }}</strong></td>
            <td style="padding: 2px;"><strong>{{ $a }}</strong></td>
            <td style="padding: 2px;"><strong>{{ $r }}</strong></td>
            <td style="padding: 2px;"><strong>{{ $e }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4 row">
    <div class="col-4">
        <p><strong>Légende:</strong><br>
        P = Présent<br>
        A = Absent<br>
        R = Retard<br>
        E = Excusé</p>
    </div>
    <div class="col-4 text-center">
        <p>Date d'impression : {{ date('d/m/Y') }}</p>
    </div>
    <div class="col-4 text-right">
        <p>Signature de la Direction</p>
        <br><br><br>
        <p>_____________________</p>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
@endsection
