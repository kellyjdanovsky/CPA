@extends('layouts.master')
@section('page_title', 'Historique Disciplinaire: ' . $student->name)
@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="card card-body text-center">
            <h6 class="font-weight-semibold mb-0">{{ $student->name }}</h6>
            <span class="d-block text-muted">Total: {{ $records->count() }} dossiers</span>
        </div>
        <div class="card">
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">Incidents: <span class="badge badge-danger float-right">{{ $total_incidents }}</span></li>
                    <li class="mb-2">Sanctions: <span class="badge badge-warning float-right">{{ $total_sanctions }}</span></li>
                    <li>Récompenses: <span class="badge badge-success float-right">{{ $total_recompenses }}</span></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Chronologie</h6>
            </div>
            <div class="card-body">
                <div class="list-feed">
                    @foreach($records as $rec)
                        <div class="list-feed-item {{ $rec->type == 'incident' ? 'border-danger' : ($rec->type == 'sanction' ? 'border-warning' : 'border-success') }}">
                            <div class="text-muted font-size-sm mb-1">{{ date('d/m/Y', strtotime($rec->date_incident)) }}</div>
                            <h6 class="mb-1">{{ $rec->category }} {!! $rec->severity_badge !!}</h6>
                            <p class="mb-1">{{ $rec->description }}</p>
                            @if($rec->action_taken)
                                <p class="text-muted font-size-sm mb-0">Action: {{ $rec->action_taken }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
