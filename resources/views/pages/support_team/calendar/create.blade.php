@extends('layouts.master')
@section('page_title', 'Ajouter un Événement')
@section('content')

<div class="card">
    <div class="card-header"><h6 class="card-title">Nouvel Événement</h6></div>
    <div class="card-body">
        <form method="post" action="{{ route('calendar.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Titre: <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Type: <span class="text-danger">*</span></label>
                        <select name="event_type" class="form-control select" required>
                            <option value="cours">Cours</option>
                            <option value="examen">Examen</option>
                            <option value="vacances">Vacances</option>
                            <option value="fete">Fête</option>
                            <option value="reunion">Réunion</option>
                            <option value="conseil">Conseil</option>
                            <option value="pedagogique">Journée Pédagogique</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date de début: <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date de fin: <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Classe (Optionnel):</label>
                        <select name="class_id" class="form-control select">
                            <option value="">Toute l'école</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mt-4">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="all_day" id="all_day" class="custom-control-input" value="1" checked>
                            <label class="custom-control-label" for="all_day">Toute la journée</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Enregistrer <i class="icon-paperplane ml-2"></i></button>
            </div>
        </form>
    </div>
</div>

@endsection
