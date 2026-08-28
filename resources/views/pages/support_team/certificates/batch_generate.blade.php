@extends('layouts.master')
@section('page_title', 'Génération par Lot')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Générer des Certificats pour une Classe Entière</h6>
        </div>

        <div class="card-body">
            <form action="{{ url('certificates/batch-generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Type de certificat: <span class="text-danger">*</span></label>
                        <select name="type" required class="form-control select">
                            <option value="">Sélectionner le type</option>
                            @foreach($types as $key => $val)
                                <option value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Date de délivrance: <span class="text-danger">*</span></label>
                        <input type="date" name="date_issued" value="{{ date('Y-m-d') }}" required class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Année scolaire: <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" value="{{ App\Helpers\Qs::getCurrentSession() ?? '2024-2025' }}" required class="form-control">
                    </div>
                </div>
                
                <div class="alert alert-info border-0 alert-dismissible">
                    Sélectionnez les élèves pour lesquels générer les certificats. 
                </div>

                <!-- Assume student list would be fetched here by some AJAX or just showing a mock selector -->
                <div class="form-group">
                    <label>Sélectionner les Élèves (Maintenez Ctrl pour en sélectionner plusieurs):</label>
                    <select name="student_ids[]" multiple required class="form-control select" style="height: 200px;">
                        <!-- Options would be populated dynamically -->
                    </select>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary">Générer pour la sélection <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
