@extends('layouts.master')
@section('page_title', 'Enregistrer un Événement Disciplinaire')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Nouveau Dossier Disciplinaire</h6>
    </div>

    <div class="card-body">
        <form method="post" action="{{ route('discipline.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Élève: <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="student_id" required>
                            <option value="">Sélectionner un élève</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date de l'événement: <span class="text-danger">*</span></label>
                        <input type="date" name="date_incident" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="d-block font-weight-semibold">Type: <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="type_incident" name="type" value="incident" class="custom-control-input" required onchange="updateCategories()">
                            <label class="custom-control-label" for="type_incident">Incident</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="type_sanction" name="type" value="sanction" class="custom-control-input" required onchange="updateCategories()">
                            <label class="custom-control-label" for="type_sanction">Sanction</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="type_recompense" name="type" value="recompense" class="custom-control-input" required onchange="updateCategories()">
                            <label class="custom-control-label" for="type_recompense">Récompense</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Catégorie: <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="">Sélectionner d'abord le type</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6" id="severity_div">
                    <div class="form-group">
                        <label>Gravité (Optionnel):</label>
                        <select name="severity" class="form-control">
                            <option value="">Non spécifié</option>
                            <option value="mineur">Mineur</option>
                            <option value="moyen">Moyen</option>
                            <option value="grave">Grave</option>
                            <option value="tres_grave">Très grave</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Description: <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Action prise (Optionnel):</label>
                        <textarea name="action_taken" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="parent_notified" class="custom-control-input" id="parent_notified" value="1">
                    <label class="custom-control-label" for="parent_notified">Parent notifié</label>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Enregistrer <i class="icon-paperplane ml-2"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
    const categories = @json($categories);

    function updateCategories() {
        const type = document.querySelector('input[name="type"]:checked').value;
        const categorySelect = document.getElementById('category');
        const severityDiv = document.getElementById('severity_div');
        
        categorySelect.innerHTML = '<option value="">Sélectionner...</option>';
        
        if (categories[type]) {
            categories[type].forEach(cat => {
                let opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                categorySelect.appendChild(opt);
            });
        }
        
        if (type === 'recompense') {
            severityDiv.style.display = 'none';
        } else {
            severityDiv.style.display = 'block';
        }
    }
</script>
@endsection
