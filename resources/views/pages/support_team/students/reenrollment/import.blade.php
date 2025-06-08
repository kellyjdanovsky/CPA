@extends('layouts.master')
@section('page_title', 'Importation d\'élèves pour réinscription')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline bg-primary text-white">
            <h5 class="card-title"><i class="icon-upload mr-2"></i> Importation d\'élèves pour réinscription</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info border-0 alert-dismissible bg-info-100">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <span class="font-weight-semibold"><i class="icon-info22 mr-2"></i> Cette page vous permet d'importer une liste d'élèves à partir d'un fichier Excel pour les réinscrire en masse.</span>
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

            <div class="card border-left-primary border-left-3">
                <div class="card-header bg-light">
                    <h6 class="card-title font-weight-semibold"><i class="icon-file-excel mr-2"></i>Importation de fichier Excel</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('students.reenrollment.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="card-title mb-0"><i class="icon-calendar mr-2"></i>Année actuelle ({{ Qs::getSetting('current_session') }})</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="my_class_id"><i class="icon-office mr-1"></i>Nouvelle classe:</label>
                                                    <select required class="form-control select" name="my_class_id" id="my_class_id">
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
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file"><i class="icon-file-excel mr-1"></i>Fichier Excel:</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                                        <label class="custom-file-label" for="file">Choisir un fichier</label>
                                    </div>
                                    <span class="form-text text-muted">Formats acceptés: .xlsx, .xls, .csv</span>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="icon-info22 mr-2"></i> Le fichier Excel doit contenir les colonnes suivantes:
                                    <ul class="mt-2">
                                        <li><strong>id</strong> - ID de l'élève</li>
                                        <li><strong>nom_de_leleve</strong> - Nom de l'élève (pour information seulement)</li>
                                        <li><strong>numero_dadmission</strong> - Numéro d'admission (pour information seulement)</li>
                                    </ul>
                                    <p>Vous pouvez d'abord exporter la liste des élèves depuis la page de réinscription, puis utiliser ce fichier pour l'importation.</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="icon-upload mr-2"></i> Importer et réinscrire les élèves
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Afficher le nom du fichier sélectionné
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>

@endsection