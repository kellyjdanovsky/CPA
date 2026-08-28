@extends('layouts.print')
@section('title', 'Calendrier Annuel')
@section('content')

<div style="text-align: center; margin-bottom: 20px;">
    <h2>CALENDRIER SCOLAIRE</h2>
    <p>Année Scolaire: {{ \App\Helpers\Qs::getCurrentSession() }}</p>
</div>

<div class="row">
    @for($m=1; $m<=12; $m++)
    <div class="col-3" style="width: 25%; float: left; padding: 5px;">
        <h5 style="text-align: center; font-size: 12px; margin-bottom: 2px;">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</h5>
        <table class="table table-bordered" style="font-size: 9px; text-align: center; width: 100%;">
            <tr><th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th><th>D</th></tr>
            @php
                $start = \Carbon\Carbon::createFromDate(null, $m, 1)->startOfWeek();
                $end = \Carbon\Carbon::createFromDate(null, $m, 1)->endOfMonth()->endOfWeek();
                $curr = $start->copy();
            @endphp
            @while($curr <= $end)
                @if($curr->dayOfWeekIso == 1) <tr> @endif
                @php
                    $style = '';
                    if($curr->month == $m) {
                        foreach($events as $e) {
                            if($curr->between($e->start_date, $e->end_date)) {
                                // Simplified color matching for print
                                if($e->event_type == 'cours') $style = 'background-color: #e0f2f1;';
                                else if($e->event_type == 'vacances') $style = 'background-color: #c8e6c9;';
                                else if($e->event_type == 'examen') $style = 'background-color: #ffcdd2;';
                                else $style = 'background-color: #fff9c4;';
                                break;
                            }
                        }
                    } else {
                        $style = 'color: #ccc;';
                    }
                @endphp
                <td style="{{ $style }}">{{ $curr->day }}</td>
                @if($curr->dayOfWeekIso == 7) </tr> @endif
                @php $curr->addDay(); @endphp
            @endwhile
        </table>
    </div>
    @endfor
</div>
<div style="clear: both;"></div>

<div style="margin-top: 20px; font-size: 11px;">
    <strong>Légende: </strong>
    <span style="background-color: #e0f2f1; padding: 2px 5px; margin-right: 5px;">Cours</span>
    <span style="background-color: #ffcdd2; padding: 2px 5px; margin-right: 5px;">Examen</span>
    <span style="background-color: #c8e6c9; padding: 2px 5px; margin-right: 5px;">Vacances</span>
    <span style="background-color: #fff9c4; padding: 2px 5px; margin-right: 5px;">Autres</span>
</div>

@endsection
