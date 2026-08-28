@extends('layouts.master')
@section('page_title', 'Gestion de la Bibliothèque')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Catalogue de la Bibliothèque</h6>
        {!! Qs::getPanelOptions() !!}
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#catalogue" class="nav-link active" data-toggle="tab">Catalogue</a></li>
            <li class="nav-item"><a href="#loans" class="nav-link" data-toggle="tab">Prêts en cours</a></li>
            <li class="nav-item"><a href="#overdue" class="nav-link" data-toggle="tab">Retards</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="catalogue">
                <table class="table datatable-basic">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Catégorie</th>
                            <th>Total</th>
                            <th>Dispo</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $b)
                        <tr>
                            <td>{{ $b->name }}</td>
                            <td>{{ $b->author }}</td>
                            <td>{{ $b->book_type }}</td>
                            <td>{{ $b->total_copies }}</td>
                            <td>{{ $b->total_copies - $b->issued_copies }}</td>
                            <td>
                                <a href="{{ route('library.edit', Qs::hash($b->id)) }}" class="btn btn-primary"><i class="icon-pencil"></i></a>
                                <a href="{{ route('library.loan_form', Qs::hash($b->id)) }}" class="btn btn-success"><i class="icon-book"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="loans">
                <!-- Loans table -->
            </div>
            <div class="tab-pane fade" id="overdue">
                <!-- Overdue table -->
            </div>
        </div>
    </div>
</div>
@endsection
