@extends('layouts.master')
@section('page_title', 'Gérer les utilisateurs')
@section('content')
<style>
    .cacher {
        display: none;
    }
</style>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Gérer les utilisateurs</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#new-user" class="nav-link active" data-toggle="tab">Créer un nouvel utilisateur</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Gérer les utilisateurs</a>
                    <div class="dropdown-menu dropdown-menu-right">
                      
                        @foreach($user_types as $ut)
                        @php
                         $display_name = ($ut->name == 'Accountant') ? 'Comptable' : ($ut->name == 'Teacher' ? 'Enseignant' : $ut->name);
                         @endphp
                  
                            <a href="#ut-{{ Qs::hash($ut->id) }}" class="dropdown-item" data-toggle="tab">{{ $display_name }}s</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="new-user">
                    <form method="post" enctype="multipart/form-data" class="wizard-form steps-validation ajax-store" action="{{ route('users.store') }}" data-fouc>
                        @csrf
                    <h6>Données personnelles</h6>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="user_type"> Sélectionner l'utilisateur : <span class="text-danger">*</span></label>
                                        <select required data-placeholder="Sélectionner l'utilisateur" class="form-control select" name="user_type" id="user_type">
                                @foreach($user_types as $ut)
                                @php
                                $display_name = ($ut->name == 'Accountant') ? 'Comptable' : ($ut->name == 'Teacher' ? 'Enseignant' : $ut->name);
                                @endphp
                             <option class="chkmdp" value="{{ Qs::hash($ut->id) }}">{{ $display_name }}</option>
                                @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom complet (pour parent nom de famille): <span class="text-danger">*</span></label>
                                        <input value="{{ old('name') }}" required type="text" name="name" placeholder="Nom complet" class="form-control">
                                    </div>
                                </div>

                                {{--SECTION PARENT--}}
                                <div class="col-md-4 scp cacher">
                                    <div class="form-group">
                                        <label>Nom Pére : <span class="text-danger"></span></label>
                                        <input value="{{ old('name') }}"  type="text" name="nom_p" placeholder="Nom complet" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4 scp cacher">
                                    <div class="form-group">
                                        <label>Nom Mére : <span class="text-danger"></span></label>
                                        <input value="{{ old('name') }}"  type="text" name="nom_m" placeholder="Nom complet" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4 scp cacher">
                                    <div class="form-group">
                                        <label>Profession Mére : <span class="text-danger"></span></label>
                                        <input value="{{ old('name') }}"  type="text" name="prof_m" placeholder="Nom complet" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 scp cacher">
                                    <div class="form-group">
                                        <label>Profession Pére : <span class="text-danger"></span></label>
                                        <input value="{{ old('name') }}"  type="text" name="prof_p" placeholder="Nom complet" class="form-control">
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
                                        <label>Adresse e-mail : </label>
                                        <input value="{{ old('email') }}" type="email" name="email" class="form-control" placeholder="votre@email.com">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nom d'utilisateur : </label>
                                        <input value="{{ old('username') }}" type="text" name="username" class="form-control" placeholder="Nom d'utilisateur">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Téléphone :</label>
                                        <input value="{{ old('phone') }}" type="text" name="phone" class="form-control" placeholder="+2341234567" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Téléphone :</label>
                                        <input value="{{ old('phone2') }}" type="text" name="phone2" class="form-control" placeholder="+2341234567" >
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date d'emploi :</label>
                                        <input autocomplete="off" name="emp_date" value="{{ old('emp_date') }}" type="text" class="form-control date-pick" placeholder="Sélectionner la date...">

                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="password">Mot de passe : </label>
                                        <input id="password" type="password" name="password" class="form-control"  >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gender">Genre : <span class="text-danger">*</span></label>
                                        <select class="select form-control" id="gender" name="gender"  data-fouc data-placeholder="Choisir..">
                                            <option value=""></option>
                                            <option {{ (old('gender') == 'Male') ? 'selected' : '' }} value="Male">Homme</option>
                                            <option {{ (old('gender') == 'Female') ? 'selected' : '' }} value="Female">Femme</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 cacher">
                                    <div class="form-group">
                                        <label for="nal_id">Nationalité : <span class="text-danger">*</span></label>
                                        <select data-placeholder="Choisir..."  name="nal_id" id="nal_id" class="select-search form-control">
                                            <option value=""></option>
                                            @foreach($nationals as $nal)
                                                <option {{ (old('nal_id') == $nal->id ? 'selected' : '') }} value="{{ $nal->id }}">{{ $nal->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{--État--}}
                                <div class="col-md-4 cacher">
                                    <label for="state_id">État : <span class="text-danger">*</span></label>
                                    <select onchange="getLGA(this.value)"  data-placeholder="Choisir.." class="select-search form-control" name="state_id" id="state_id">
                                        <option value=""></option>
                                        @foreach($states as $st)
                                            <option {{ (old('state_id') == $st->id ? 'selected' : '') }} value="{{ $st->id }}">{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--LGA--}}
                                <div class="col-md-4 cacher">
                                    <label for="lga_id">LGA : <span class="text-danger">*</span></label>
                                    <select  data-placeholder="Sélectionner d'abord l'état" class="select-search form-control" name="lga_id" id="lga_id">
                                        <option value=""></option>
                                    </select>
                                </div>
                                {{--GROUPE SANGUIN--}}
                                <div class="col-md-4 cacher">
                                    <div class="form-group">
                                        <label for="bg_id">Groupe sanguin : </label>
                                        <select class="select form-control" id="bg_id" name="bg_id" data-fouc data-placeholder="Choisir..">
                                            <option value=""></option>
                                            @foreach($blood_groups as $bg)
                                                <option {{ (old('bg_id') == $bg->id ? 'selected' : '') }} value="{{ $bg->id }}">{{ $bg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                {{--PHOTO DE PASSEPORT--}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="d-block">Télécharger une photo  :</label>
                                        <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                                        <span class="form-text text-muted">Images acceptées : jpeg, png. Taille maximale du fichier 2 Mo</span>
                                    </div>
                                </div>
                            </div>

                        </fieldset>



                    </form>
                </div>

                @foreach($user_types as $ut)
                    <div class="tab-pane fade" id="ut-{{Qs::hash($ut->id)}}">                         <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>N°</th>
                                <th>Photo</th>
                                <th>Nom</th>
                                <th>Nom d'utilisateur</th>
                                <th>Téléphone</th>
                                <th>E-mail</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->where('user_type', $ut->title) as $u)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $u->photo }}" alt="photo"></td>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->username }}</td>
                                    <td>{{ $u->phone }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    {{--Voir le profil--}}
                                                    <a href="{{ route('users.show', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-eye"></i> Voir le profil</a>
                                                    {{--Modifier--}}
                                                    <a href="{{ route('users.edit', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Modifier</a>
                                                @if(Qs::userIsSuperAdmin())

                                                        <a href="{{ route('users.reset_pass', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Réinitialiser le mot de passe</a>
                                                        {{--Supprimer--}}
                                                        <a id="{{ Qs::hash($u->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Supprimer</a>
                                                        <form method="post" id="item-delete-{{ Qs::hash($u->id) }}" action="{{ route('users.destroy', Qs::hash($u->id)) }}" class="hidden">@csrf @method('delete')</form>
                                                @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    
    {{--Fin de la liste des étudiants--}}

    <script>
   $(document).ready(function() {
    $('#user_type').on('change', function() {
        var selectedOption = $(this).find(':selected');
        var display_name = selectedOption.text();
        
        var usernameInput = $('input[name="username"]');
        var passwordInput = $('input[name="password"]');
        var gender = $('input[name="gender"]');
        var date = $('input[name="emp_date"]');
        var scpElement = $('.scp');
        
        if (display_name === 'Parent') {
            usernameInput.prop('disabled', true);
            passwordInput.prop('disabled', true);
            scpElement.removeClass('cacher'); // Remove the 'cacher' class
            date.prop('disabled', true);
            $('#gender').prop('disabled', true);
        } else if (display_name === 'Enseignant') {
            usernameInput.prop('disabled', true);
            passwordInput.prop('disabled', true);
            scpElement.addClass('cacher'); // Add the 'cacher' class
            $('#gender').prop('disabled', false);
        } else {
            usernameInput.prop('disabled', false);
            passwordInput.prop('disabled', false);
            scpElement.addClass('cacher'); // Add the 'cacher' class
            date.prop('disabled', false);
            $('#gender').prop('disabled', false);
        }
    });
});


        </script>

        

@endsection
