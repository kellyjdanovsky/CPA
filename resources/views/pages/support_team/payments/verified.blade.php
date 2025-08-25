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
                            </div>

                            <div class="col-md-2 mt-4">
                                <div class="text-right mt-1">
                                    <button type="submit" class="btn btn-primary">Vérifier <i class="icon-search4 ml-2"></i></button>
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
                            }else{
                                $("#payments").empty();
                            }
                        }
                    });
                }else{
                   alert("Classe non trouvée");
                }
            });
        });
    </script>
@endsection
