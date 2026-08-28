@extends('layouts.master')
@section('page_title', 'Rapport de Fin d\'Année')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Bilan de l'Année Scolaire {{ $session }}</h6>
        <div class="header-elements">
            <a href="{{ route('reports.annual.print') }}" class="btn btn-primary" target="_blank"><i class="icon-printer mr-2"></i> Imprimer le Rapport</a>
            <a href="{{ route('reports.annual.excel') }}" class="btn btn-success ml-2"><i class="icon-file-excel mr-2"></i> Export Excel</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-teal-400">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $effectifs }}</h3>
                        Effectif Total
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-blue-400">
                    <div class="card-body">
                        <h3 class="mb-0">{{ number_format($recettes, 0, ',', ' ') }} Ar</h3>
                        Recettes Totales
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger-400">
                    <div class="card-body">
                        <h3 class="mb-0">{{ number_format($impayes, 0, ',', ' ') }} Ar</h3>
                        Impayés
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection