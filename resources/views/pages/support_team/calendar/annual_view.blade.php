@extends('layouts.master')
@section('page_title', 'Vue Annuelle')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Calendrier Annuel {{ \App\Helpers\Qs::getCurrentSession() }}</h6>
        <div class="header-elements">
            <a href="{{ route('calendar.print_annual') }}" class="btn btn-info"><i class="icon-printer mr-2"></i> Imprimer</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @for($m=1; $m<=12; $m++)
            <div class="col-md-3 mb-4">
                <h6 class="text-center font-weight-bold">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</h6>
                <table class="table table-sm table-bordered text-center" style="font-size: 10px;">
                    <tr><th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th><th>D</th></tr>
                    @php
                        $start = \Carbon\Carbon::createFromDate(null, $m, 1)->startOfWeek();
                        $end = \Carbon\Carbon::createFromDate(null, $m, 1)->endOfMonth()->endOfWeek();
                        $curr = $start->copy();
                    @endphp
                    @while($curr <= $end)
                        @if($curr->dayOfWeekIso == 1) <tr> @endif
                        @php
                            $bg = '';
                            if($curr->month == $m) {
                                foreach($events as $e) {
                                    if($curr->between($e->start_date, $e->end_date)) {
                                        $bg = 'bg-'.$e->event_color;
                                        break;
                                    }
                                }
                            } else {
                                $bg = 'bg-light text-muted';
                            }
                        @endphp
                        <td class="{{ $bg }}">{{ $curr->day }}</td>
                        @if($curr->dayOfWeekIso == 7) </tr> @endif
                        @php $curr->addDay(); @endphp
                    @endwhile
                </table>
            </div>
            @endfor
        </div>
    </div>
</div>

@endsection
