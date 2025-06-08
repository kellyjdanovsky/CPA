@extends('layouts.master')
@section('page_title', 'Réinscription en masse d\'élèves')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline bg-success text-white">
            <h5 class="card-title"><i class="icon-users4 mr-2"></i> Réinscription en masse d'élèves</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible bg-info-100">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span class="font-weight-semibold"><i class="icon-info22 mr-2"></i> Vous êtes sur le point de réinscrire {{ count($student_ids) }} élève(s) pour l'année scolaire {{ Qs::getSetting('current_session') }}.</span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <a href="{{ route('students.reenrollment') }}" class="btn btn-secondary">
                        <i class="icon-arrow-left7 mr-2"></i> Retour à la page de réinscription
                    </a>
                </div>
            </div>

            <div class="card border-left-success border-left-3">
                <div class="card-header bg-light">
                    <h6 class="card-title font-weight-semibold"><i class="icon-graduation2 mr-2"></i>Sélection de la classe de destination</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('students.reenrollment.batch_reenroll_submit') }}">
                        @csrf
                        <input type="hidden" name="student_ids" value="{{ json_encode($student_ids) }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class_id"><i class="icon-office mr-1"></i>Nouvelle classe:</label>
                                    <select required class="form-control select" name="class_id" id="class_id">
                                        <option value="">Sélectionner une classe</option>
                                        @foreach($my_classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="section_id"><i class="icon-grid52 mr-1"></i>Nouvelle section:</label>
                                    <select required class="form-control select" name="section_id" id="section_id">
                                        <option value="">Sélectionner une section</option>
                                        @foreach($sections as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="icon-user-plus mr-2"></i> Réinscrire les {{ count($student_ids) }} élèves sélectionnés <i class="icon-checkmark-circle ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection