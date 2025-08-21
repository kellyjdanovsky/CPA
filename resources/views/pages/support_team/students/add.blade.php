@extends('layouts.master')
@section('page_title', 'Inscription')
@section('content')
    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Veuillez remplir le formulaire pour inscrire un nouvel étudiant</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <form id="ajax-reg" method="post" enctype="multipart/form-data" class="wizard-form steps-validation" action="{{ route('students.store') }}" data-fouc>
            @csrf
            <h6>Données Personnelles</h6>
            <fieldset>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom complet : <span class="text-danger">*</span></label>
                            <input value="{{ old('name') }}" required type="text" name="name" placeholder="Nom complet" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Adresse : <span class="text-danger">*</span></label>
                            <input value="{{ old('address') }}" class="form-control" placeholder="Adresse" name="address" type="text" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gender">Genre : <span class="text-danger">*</span></label>
                            <select class="select form-control" id="gender" name="gender" required data-fouc data-placeholder="Choisissez...">
                                <option value=""></option>
                                <option {{ (old('gender') == 'Male') ? 'selected' : '' }} value="Male">Masculin</option>
                                <option {{ (old('gender') == 'Female') ? 'selected' : '' }} value="Female">Féminin</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Statut : <span class="text-danger">*</span></label>
                            <select class="select form-control" id="status" name="status" required data-fouc data-placeholder="Choisissez...">
                                <option value=""></option>
                                <option {{ (old('status') == 'Normal') ? 'selected' : '' }} value="Normal">Normal</option>
                                <option {{ (old('status') == 'ADRA') ? 'selected' : '' }} value="ADRA">ADRA</option>
                                <option {{ (old('status') == 'TEAM3') ? 'selected' : '' }} value="TEAM3">TEAM 3</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="student_type">Type d'étudiant : <span class="text-danger">*</span></label>
                            <select class="select form-control" id="student_type" name="student_type" required data-fouc data-placeholder="Choisissez...">
                                <option value=""></option>
                                <option {{ (old('student_type') == 'Nouveau') ? 'selected' : '' }} value="Nouveau">Nouveau</option>
                                <option {{ (old('student_type') == 'Ancien') ? 'selected' : '' }} value="Ancien">Ancien</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="academic_status">Statut académique : <span class="text-danger">*</span></label>
                            <select class="select form-control" id="academic_status" name="academic_status" required data-fouc data-placeholder="Choisissez...">
                                <option value=""></option>
                                <option {{ (old('academic_status') == 'Passant') ? 'selected' : '' }} value="Passant">Passant</option>
                                <option {{ (old('academic_status') == 'Redoublant') ? 'selected' : '' }} value="Redoublant">Redoublant</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Téléphone parent :</label>
                            <input value="{{ old('phone') }}" type="text" name="phone" class="form-control" placeholder="Téléphone du parent/tuteur">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Téléphone parent 2 :</label>
                            <input value="{{ old('phone2') }}" type="text" name="phone2" class="form-control" placeholder="Téléphone secondaire">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date de naissance : <span class="text-danger">*</span></label>
                            <input name="dob" value="{{ old('dob') }}" type="text" class="form-control date-pick" placeholder="Choisir une date..." required id="dob" onchange="calculateAge()">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Âge :</label>
                            <input name="age" value="{{ old('age') }}" type="text" class="form-control" id="age" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 cacher">
                        <div class="form-group">
                            <label for="nal_id">Nationalité : <span class="text-danger">*</span></label>
                            <select data-placeholder="Choisissez..."  name="nal_id" id="nal_id" class="select-search form-control">
                                <option value=""></option>
                                @foreach($nationals as $nal)
                                    <option {{ (old('nal_id') == $nal->id ? 'selected' : '') }} value="1">{{ $nal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 cacher">
                        <label for="state_id">État : <span class="text-danger">*</span></label>
                        <select onchange="getLGA(this.value)"  data-placeholder="Choisissez.." class="select-search form-control" name="state_id" id="state_id">
                            <option value=""></option>
                            @foreach($states as $st)
                                <option {{ (old('state_id') == $st->id ? 'selected' : '') }} value="1">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 cacher">
                        <label for="lga_id">LGA : <span class="text-danger">*</span></label>
                        <select  data-placeholder="Sélectionner d'abord l'État" class="select-search form-control" name="lga_id" id="lga_id">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nom du père/tuteur :</label>
                            <input value="{{ old('nom_p') }}" type="text" name="nom_p" class="form-control" placeholder="Nom du père/tuteur">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Profession du père/tuteur :</label>
                            <input value="{{ old('prof_p') }}" type="text" name="prof_p" class="form-control" placeholder="Profession du père/tuteur">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nom de la mère/tutrice :</label>
                            <input value="{{ old('nom_m') }}" type="text" name="nom_m" class="form-control" placeholder="Nom de la mère/tutrice">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Profession de la mère/tutrice :</label>
                            <input value="{{ old('prof_m') }}" type="text" name="prof_m" class="form-control" placeholder="Profession de la mère/tutrice">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="religion">Religion :</label>
                            <select class="select form-control" id="religion" name="religion" data-fouc data-placeholder="Choisissez...">
                                <option value=""></option>
                                <option {{ (old('religion') == 'FLM') ? 'selected' : '' }} value="FLM">FLM</option>
                                <option {{ (old('religion') == 'FJKM') ? 'selected' : '' }} value="FJKM">FJKM</option>
                                <option {{ (old('religion') == 'Catholique') ? 'selected' : '' }} value="Catholique">Catholique</option>
                                <option {{ (old('religion') == 'Adventiste') ? 'selected' : '' }} value="Adventiste">Adventiste</option>
                                <option {{ (old('religion') == 'Islam') ? 'selected' : '' }} value="Islam">Islam</option>
                                <option {{ (old('religion') == 'Judaïsme') ? 'selected' : '' }} value="Judaïsme">Judaïsme</option>
                                <option {{ (old('religion') == 'Apokalipsy') ? 'selected' : '' }} value="Apokalipsy">Apokalipsy</option>
                                <option {{ (old('religion') == 'Autres') ? 'selected' : '' }} value="Autres">Autres</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 cacher">
                        <div class="form-group">
                            <label for="bg_id">Groupe sanguin :</label>
                            <select class="select form-control" id="bg_id" name="bg_id" data-fouc data-placeholder="Choisissez...">
                                <option value=""></option>
                                @foreach(App\Models\BloodGroup::all() as $bg)
                                    <option {{ (old('bg_id') == $bg->id ? 'selected' : '') }} value="{{ $bg->id }}">{{ $bg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="d-block">Télécharger une photo :</label>
                            <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                            <span class="form-text text-muted">Images acceptées : jpeg, png. Taille maximale du fichier 2 Mo</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <h6>Données de l'étudiant</h6>
            <fieldset>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="my_class_id">Classe : <span class="text-danger">*</span></label>
                            <select onchange="getClassSections(this.value)" data-placeholder="Choisissez..." required name="my_class_id" id="my_class_id" class="select-search form-control">
                                <option value=""></option>
                                @foreach($my_classes as $c)
                                    <option {{ (old('my_class_id') == $c->id ? 'selected' : '') }} value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="section_id">Section : <span class="text-danger">*</span></label>
                            <select data-placeholder="Sélectionner d'abord la classe" required name="section_id" id="section_id" class="select-search form-control">
                                <option {{ (old('section_id')) ? 'selected' : '' }} value="{{ old('section_id') }}">{{ (old('section_id')) ? 'Selected' : '' }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="year_admitted">Année d'admission : <span class="text-danger">*</span></label>
                            <select data-placeholder="Choisissez..." required name="year_admitted" id="year_admitted" class="select-search form-control">
                                <option value=""></option>
                                @for($y=date('Y', strtotime('- 10 years')); $y<=date('Y'); $y++)
                                    <option {{ (old('year_admitted') == $y) ? 'selected' : '' }} value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 cacher">
                        <label for="dorm_id">Dortoir : </label>
                        <select data-placeholder="Choisissez..." name="dorm_id" id="dorm_id" class="select-search form-control">
                            <option value=""></option>
                            @foreach($dorms as $d)
                                <option {{ (old('dorm_id') == $d->id) ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 cacher">
                        <div class="form-group">
                            <label>Numéro de chambre du dortoir :</label>
                            <input type="text" name="dorm_room_no" placeholder="Numéro de chambre du dortoir" class="form-control" value="{{ old('dorm_room_no') }}">
                        </div>
                    </div>

                    <div class="col-md-3 cacher">
                        <div class="form-group">
                            <label>Équipe sportive :</label>
                            <input type="text" name="house" placeholder="Équipe sportive" class="form-control" value="{{ old('house') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Numéro d'Inscription :</label>
                            <input type="text" name="adm_no" placeholder="Numéro d'admission" class="form-control" value="{{ old('adm_no') }}">
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@section('page_scripts')
<script>
    function calculateAge() {
        var dobInput = document.getElementById('dob');
        var ageInput = document.getElementById('age');

        if (dobInput && ageInput && dobInput.value) {
            // Format de date YYYY-MM-DD
            var dobValue = dobInput.value;
            var dob = new Date(dobValue);

            // Vérifier si la date est valide
            if (isNaN(dob.getTime())) {
                console.error('Date de naissance invalide:', dobValue);
                ageInput.value = '';
                return;
            }

            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();

            // Ajuster l'âge si l'anniversaire n'est pas encore passé cette année
            if (today.getMonth() < dob.getMonth() ||
                (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
                age--;
            }

            ageInput.value = age;
            console.log('Âge calculé:', age, 'pour la date:', dobValue);
        } else if (ageInput) {
            ageInput.value = '';
        }
    }

    $(document).ready(function() {
        // Initialiser le calcul de l'âge au chargement de la page
        calculateAge();

        // Améliorer l'initialisation du datepicker pour déclencher le calcul de l'âge
        if ($('#dob').length) {
            // Détruire l'instance existante du datepicker s'il y en a une
            if ($.fn.datepicker && $('#dob').data('datepicker')) {
                $('#dob').datepicker('destroy');
            }

            // Initialiser le datepicker avec notre configuration personnalisée
            $('#dob').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0',
                endDate: new Date(),
            }).on('changeDate', function() {
                calculateAge();
            }).on('change', function() {
                calculateAge();
            });

            // Écouter aussi les événements de saisie manuelle
            $('#dob').on('input blur', function() {
                setTimeout(calculateAge, 100);
            });
        }
    });
</script>
@endsection