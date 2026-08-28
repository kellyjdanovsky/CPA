@extends('layouts.print')
@section('title', 'Rapport de Discipline')
@section('content')

<div style="text-align: center; margin-bottom: 20px;">
    <h2>RAPPORT DE DISCIPLINE</h2>
    <p>Année Scolaire: {{ \App\Helpers\Qs::getCurrentSession() }}</p>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Élève</th>
            <th>Type</th>
            <th>Catégorie</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $rec)
        <tr>
            <td>{{ date('d/m/Y', strtotime($rec->date_incident)) }}</td>
            <td>{{ $rec->student->name }}</td>
            <td>{{ ucfirst($rec->type) }}</td>
            <td>{{ $rec->category }}</td>
            <td>{{ $rec->description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
