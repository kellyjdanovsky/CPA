@extends('layouts.print')
@section('title', 'Rapport Annuel')
@section('content')
<div class="text-center mb-4">
    <h2>Rapport Annuel de Clôture</h2>
    <h4>Année Scolaire {{ $session }}</h4>
</div>
<table class="table table-bordered datatable-basic">
    <tr><th>Effectif Total</th><td>{{ $effectifs }} élèves</td></tr>
    <tr><th>Recettes Totales</th><td>{{ number_format($recettes, 0, ',', ' ') }} Ar</td></tr>
</table>
@endsection