@extends('layouts.master')
@section('page_title', 'Vérification des Impayés')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2"></i>Vérification des Impayés</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="get" action="{{ route('payments.check_unpaid') }}">
                @csrf
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="my_class_id" class="col-form-label font-weight-bold">Classe :</label>
                                    <select required id="my_class_id" name="my_class_id" class="form-control select">
                                        <option value="">Choisir une Classe</option>
                                        @foreach($class as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="payments" class="col-form-label font-weight-bold">Motifs de paiement :</label>
                                    <select required id="payments" name="my_payments_id[]" class="form-control select" multiple="multiple">
                                        <option value="">Sélectionner un ou plusieurs motifs de paiement</option>
                                    </select>
                                    <small class="form-text text-muted">Les motifs correspondent aux paiements créés pour cette classe ou pour toutes les classes</small>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label font-weight-bold">Filtrer par statut :</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="status[]" id="status_normal" value="Normal" checked>
                                        <label class="form-check-label" for="status_normal">Normal</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="status[]" id="status_adra" value="ADRA" checked>
                                        <label class="form-check-label" for="status_adra">ADRA</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="status[]" id="status_team3" value="TEAM3">
                                        <label class="form-check-label" for="status_team3">TEAM3</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label font-weight-bold">Type d'élèves à afficher :</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="student_type" id="type_unpaid" value="unpaid" checked>
                                        <label class="form-check-label" for="type_unpaid">Non payés</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="student_type" id="type_all" value="all">
                                        <label class="form-check-label" for="type_all">Tous</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="payment_deadline" class="col-form-label font-weight-bold">Date limite de paiement :</label>
                                    <input type="date" id="payment_deadline" name="payment_deadline" class="form-control" required>
                                    <small class="form-text text-muted">Cette date sera utilisée pour générer les lettres d'avis de paiement (10 avis par page A4)</small>
                                </div>
                            </div>

                            <div class="col-md-2 mt-4">
                                <div class="text-right mt-1">
                                    <button type="submit" class="btn btn-primary">Vérifier <i class="icon-search4 ml-2"></i></button>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="button" id="preview-notifications" class="btn btn-info btn-block" disabled>
                                        <i class="icon-eye mr-2"></i>Aperçu
                                    </button>
                                </div>
                                <div class="text-right mt-1">
                                    <button type="button" id="generate-notifications" class="btn btn-success btn-block" disabled>
                                        <i class="icon-file-pdf mr-2"></i>Avis de Paiement
                                    </button>
                                </div>
                                <div class="text-right mt-1">
                                    <button type="button" id="export-excel" class="btn btn-secondary btn-block" disabled>
                                        <i class="icon-file-excel mr-2"></i>Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            // Initialiser le select multiple
            $('#payments').select2({
                placeholder: 'Sélectionner un ou plusieurs motifs de paiement',
                allowClear: true
            });

            $('#my_class_id').change(function(){
                var classId = $(this).val();
                if(classId){
                    $.ajax({
                        type:"GET",
                        url:"{{ route('payments.select') }}", // Route without parameter
                        data: { class_id: classId }, // Pass class_id as data
                        success:function(res){
                            if(res){
                                $("#payments").empty();
                                $.each(res,function(key,value){
                                    $("#payments").append('<option value="'+value.id+'">'+value.title+'</option>');
                                });
                                updateGenerateButton();
                            }else{
                                $("#payments").empty();
                                updateGenerateButton();
                            }
                        }
                    });
                }else{
                   alert("Classe non trouvée");
                }
            });

            // Vérifier si le bouton de génération peut être activé
            function updateGenerateButton() {
                var classId = $('#my_class_id').val();
                var paymentIds = $('#payments').val();
                var paymentDeadline = $('#payment_deadline').val();
                
                if (classId && paymentIds && paymentIds.length > 0 && paymentDeadline) {
                    $('#generate-notifications').prop('disabled', false);
                    $('#preview-notifications').prop('disabled', false);
                    $('#export-excel').prop('disabled', false);
                } else {
                    $('#generate-notifications').prop('disabled', true);
                    $('#preview-notifications').prop('disabled', true);
                    $('#export-excel').prop('disabled', true);
                }
            }

            // Événements pour mettre à jour le bouton
            $('#payments, #payment_deadline').change(updateGenerateButton);

            // Fonction commune pour générer les données du formulaire
            function generateFormData(action) {
                var classId = $('#my_class_id').val();
                var paymentIds = $('#payments').val();
                var paymentDeadline = $('#payment_deadline').val();
                var statuses = [];
                
                $('input[name="status[]"]:checked').each(function() {
                    statuses.push($(this).val());
                });
                
                if (!classId || !paymentIds || paymentIds.length === 0 || !paymentDeadline) {
                    alert('Veuillez sélectionner une classe, des motifs de paiement et une date limite.');
                    return null;
                }
                
                return {
                    classId: classId,
                    paymentIds: paymentIds,
                    paymentDeadline: paymentDeadline,
                    statuses: statuses,
                    action: action
                };
            }

            // Aperçu des avis de paiement
            $('#preview-notifications').click(function() {
                var formData = generateFormData('preview');
                if (!formData) return;
                
                // Créer et soumettre le formulaire pour l'aperçu
                var form = $('<form method="POST" action="{{ route('payments.generate_notifications') }}">').appendTo('body');
                form.append('@csrf');
                form.append('<input name="my_class_id" value="' + formData.classId + '">');
                form.append('<input name="payment_deadline" value="' + formData.paymentDeadline + '">');
                form.append('<input name="action" value="preview">');
                
                // Ajouter les IDs de paiement
                formData.paymentIds.forEach(function(id) {
                    form.append('<input name="my_payments_id[]" value="' + id + '">');
                });
                
                // Ajouter les statuts
                formData.statuses.forEach(function(status) {
                    form.append('<input name="status[]" value="' + status + '">');
                });
                
                form.submit();
            });

            // Générer les avis de paiement (PDF)
            $('#generate-notifications').click(function() {
                var formData = generateFormData('download');
                if (!formData) return;
                
                // Créer et soumettre le formulaire pour le téléchargement
                var form = $('<form method="POST" action="{{ route('payments.generate_notifications') }}">').appendTo('body');
                form.append('@csrf');
                form.append('<input name="my_class_id" value="' + formData.classId + '">');
                form.append('<input name="payment_deadline" value="' + formData.paymentDeadline + '">');
                form.append('<input name="action" value="download">');
                
                // Ajouter les IDs de paiement
                formData.paymentIds.forEach(function(id) {
                    form.append('<input name="my_payments_id[]" value="' + id + '">');
                });
                
                // Ajouter les statuts
                formData.statuses.forEach(function(status) {
                    form.append('<input name="status[]" value="' + status + '">');
                });
                
                form.submit();
            });

            // Export Excel
            $('#export-excel').click(function() {
                var formData = generateFormData('excel');
                if (!formData) return;
                
                // Créer et soumettre le formulaire pour l'export Excel
                var form = $('<form method="POST" action="{{ route('payments.export_notifications_excel') }}">').appendTo('body');
                form.append('@csrf');
                form.append('<input name="my_class_id" value="' + formData.classId + '">');
                form.append('<input name="payment_deadline" value="' + formData.paymentDeadline + '">');
                
                // Ajouter les IDs de paiement
                formData.paymentIds.forEach(function(id) {
                    form.append('<input name="my_payments_id[]" value="' + id + '">');
                });
                
                // Ajouter les statuts
                formData.statuses.forEach(function(status) {
                    form.append('<input name="status[]" value="' + status + '">');
                });
                
                form.submit();
            });
        });
    </script>
@endsection
