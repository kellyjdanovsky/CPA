@extends('layouts.master')
@section('page_title', 'Calendrier Scolaire')
@section('content')

<div class="row">
    <div class="col-sm-4">
        <div class="card card-body bg-success-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $cours_days }}</h3>
                    <span class="text-uppercase font-size-xs">Jours de cours enregistrés</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-book icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-body bg-info-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $vacances_days }}</h3>
                    <span class="text-uppercase font-size-xs">Jours de vacances</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-sun3 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-body bg-warning-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $upcoming->count() }}</h3>
                    <span class="text-uppercase font-size-xs">Prochains événements</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-calendar icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Calendrier - {{ config('app.locale') == 'fr' ? $date->translatedFormat('F Y') : $date->format('F Y') }}</h6>
        <div class="header-elements">
            <a href="{{ route('calendar.index', ['month' => $date->copy()->subMonth()->month, 'year' => $date->copy()->subMonth()->year]) }}" class="btn btn-light btn-icon"><i class="icon-arrow-left15"></i></a>
            <span class="mx-3 font-weight-bold">{{ $date->format('F Y') }}</span>
            <a href="{{ route('calendar.index', ['month' => $date->copy()->addMonth()->month, 'year' => $date->copy()->addMonth()->year]) }}" class="btn btn-light btn-icon"><i class="icon-arrow-right15"></i></a>
            
            <a href="{{ route('calendar.create') }}" class="btn btn-primary ml-4"><i class="icon-plus3 mr-2"></i> Ajouter</a>
            <a href="{{ route('calendar.annual_view') }}" class="btn btn-info ml-2"><i class="icon-calendar22 mr-2"></i> Vue Annuelle</a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th><th>Dim</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startOfCalendar = $date->copy()->startOfMonth()->startOfWeek();
                    $endOfCalendar = $date->copy()->endOfMonth()->endOfWeek();
                    $current = $startOfCalendar->copy();
                @endphp
                
                @while($current <= $endOfCalendar)
                    @if($current->dayOfWeekIso == 1) <tr> @endif
                    
                    <td class="{{ $current->month != $date->month ? 'bg-light text-muted' : '' }} {{ $current->isToday() ? 'table-primary' : '' }}" style="height: 100px; width: 14%; vertical-align: top;">
                        <div class="text-right mb-1">{{ $current->day }}</div>
                        
                        @foreach($events as $event)
                            @if($current->between($event->start_date, $event->end_date))
                                <div class="badge badge-{{ $event->event_color }} d-block mb-1 text-truncate" title="{{ $event->title }}">
                                    {{ $event->title }}
                                </div>
                            @endif
                        @endforeach
                    </td>
                    
                    @if($current->dayOfWeekIso == 7) </tr> @endif
                    @php $current->addDay(); @endphp
                @endwhile
            </tbody>
        </table>
        
        <div class="mt-3">
            <strong>Légende: </strong>
            <span class="badge badge-primary mr-2">Cours</span>
            <span class="badge badge-danger mr-2">Examen</span>
            <span class="badge badge-success mr-2">Vacances</span>
            <span class="badge badge-warning mr-2">Fête</span>
            <span class="badge badge-info mr-2">Réunion</span>
            <span class="badge badge-purple mr-2">Conseil</span>
            <span class="badge badge-teal mr-2">Pédagogique</span>
            <span class="badge badge-secondary mr-2">Autre</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h6 class="card-title">Prochains Événements</h6></div>
    <div class="card-body">
        <ul class="list-unstyled">
            @foreach($upcoming as $up)
                <li class="mb-2">
                    <span class="badge badge-{{ $up->event_color }} mr-2">{{ date('d/m/Y', strtotime($up->start_date)) }}</span>
                    {{ $up->title }}
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
