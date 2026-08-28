@extends('layouts.master')
@section('page_title', 'Gestion du Personnel')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Liste du Personnel</h6>
        {!! Qs::getPanelOptions() !!}
    </div>
    <div class="card-body">
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Poste</th>
                    <th>Département</th>
                    <th>Type Contrat</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $s)
                <tr>
                    <td>{{ $s->user->name ?? '' }}</td>
                    <td>{{ $s->poste }}</td>
                    <td>{{ $s->departement }}</td>
                    <td>{{ $s->type_contrat }}</td>
                    <td>
                        <a href="{{ route('staff.show', Qs::hash($s->id)) }}" class="btn btn-info"><i class="icon-eye"></i></a>
                        <a href="{{ route('staff.edit', Qs::hash($s->id)) }}" class="btn btn-primary"><i class="icon-pencil"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
