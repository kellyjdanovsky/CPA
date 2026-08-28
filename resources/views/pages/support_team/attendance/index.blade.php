@extends('layouts.master')
@section('page_title', 'Gestion des Présences')

@section('content')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Faire l'appel</h6>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('attendance.mark') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="my_class_id">Classe: <span class="text-danger">*</span></label>
                        <select required data-placeholder="Sélectionner une classe" class="form-control select" name="my_class_id" id="my_class_id">
                            <option value=""></option>
                            @foreach($my_classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date">Date: <span class="text-danger">*</span></label>
                        <input type="date" required name="date" id="date" value="{{ $today }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="period">Période: <span class="text-danger">*</span></label>
                        <select required class="form-control select" name="period" id="period">
                            <option value="journee">Journée</option>
                            <option value="matin">Matin</option>
                            <option value="apres_midi">Après-midi</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Faire l'appel <i class="icon-paperplane ml-2"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Rapport Mensuel</h6>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('attendance.monthly-report') }}">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="rep_class_id">Classe: <span class="text-danger">*</span></label>
                        <select required data-placeholder="Sélectionner une classe" class="form-control select" name="my_class_id" id="rep_class_id">
                            <option value=""></option>
                            @foreach($my_classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label for="month_year">Mois et Année: <span class="text-danger">*</span></label>
                        <input type="month" required name="month_year" id="month_year" value="{{ date('Y-m') }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-2 mt-4">
                    <button type="submit" class="btn btn-success btn-block">Voir le rapport</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
