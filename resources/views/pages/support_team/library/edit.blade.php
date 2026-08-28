@extends('layouts.master')
@section('page_title', 'Modifier le Livre')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Modifier le Livre</h6>
        {!! Qs::getPanelOptions() !!}
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('library.update', Qs::hash($book->id)) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Titre</label>
                <input type="text" name="name" value="{{ $book->name }}" class="form-control" required>
            </div>
            <!-- Other fields... -->
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
</div>
@endsection
