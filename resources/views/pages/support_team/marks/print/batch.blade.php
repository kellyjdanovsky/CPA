@extends('layouts.print')
@section('title', 'Impression en Lot - Bulletins')
@section('content')
<style>
    .page-break { page-break-after: always; }
</style>
<div class="text-center mb-4">
    <h2>Impression en Lot des Bulletins</h2>
    <h4>Classe: {{ $my_class->name }}</h4>
</div>

@foreach($students as $st)
    <div class="bulletin-container">
        <h3>Bulletin de l'élève: {{ $st->user->name }}</h3>
        <!-- Here goes the inclusion of the actual mark sheet, mocking for now -->
        <p>Les notes et résultats de {{ $st->user->name }} apparaîtront ici.</p>
    </div>
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach
@endsection
