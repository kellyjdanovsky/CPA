@extends('layouts.master')
@section('page_title', 'Rapport Disciplinaire de Classe')
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('discipline.class_report') }}" method="GET">
            <div class="row">
                <div class="col-md-6">
                    <select name="class_id" class="form-control select" required onchange="this.form.submit()">
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $class_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

@if($class_id)
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Statistiques des incidents</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($stats as $cat => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $cat }}
                            <span class="badge badge-primary badge-pill">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Top 5 - Élevès avec incidents</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($top_students as $st)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $st['student']->name }}
                            <span class="badge badge-danger badge-pill">{{ $st['count'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
