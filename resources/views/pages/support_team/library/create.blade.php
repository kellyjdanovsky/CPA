@extends('layouts.master')
@section('page_title', 'Ajouter un Livre')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Ajouter un Livre</h6>
        {!! Qs::getPanelOptions() !!}
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('library.store') }}">
            @csrf
            <div class="form-group">
                <label>Titre</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <!-- Other fields... -->
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
</div>
@endsection
