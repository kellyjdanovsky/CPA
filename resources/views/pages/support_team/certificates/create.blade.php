@extends('layouts.master')
@section('page_title', 'Générer un Certificat')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Générer un Nouveau Certificat</h6>
        </div>

        <div class="card-body">
            <form action="{{ url('certificates/generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Élève: <span class="text-danger">*</span></label>
                        <select name="student_id" required class="form-control select-search" data-placeholder="Sélectionner l'élève">
                            <option value=""></option>
                            <!-- Assume loaded via ajax or similar, we can just put a mock loop if needed, but select2 needs options -->
                            <!-- Here you would iterate over active students in a real scenario -->
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Type de certificat: <span class="text-danger">*</span></label>
                        <select name="type" required class="form-control select" id="cert_type">
                            <option value="">Sélectionner le type</option>
                            @foreach($types as $key => $val)
                                <option value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Date de délivrance: <span class="text-danger">*</span></label>
                        <input type="date" name="date_issued" value="{{ date('Y-m-d') }}" required class="form-control">
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Année scolaire: <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" value="{{ App\Helpers\Qs::getCurrentSession() ?? '2024-2025' }}" required class="form-control">
                    </div>
                </div>
                
                <div class="row" id="extra_fields_container" style="display:none;">
                    <div class="col-md-12 form-group">
                        <label>Détails / Observations (optionnel):</label>
                        <textarea name="details[observations]" class="form-control" rows="3" placeholder="Informations additionnelles..."></textarea>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Générer et Imprimer <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('cert_type').addEventListener('change', function() {
            var val = this.value;
            if(val == 'reussite' || val == 'paiement') {
                document.getElementById('extra_fields_container').style.display = 'block';
            } else {
                document.getElementById('extra_fields_container').style.display = 'none';
            }
        });
    </script>
@endsection
