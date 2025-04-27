@extends('layouts.master')
@section('page_title', 'Profil de l\'étudiant - '.$sr->user->name)



@section('content')
<div class="row">
    <div class="col-md-3 text-center">
        <div class="card">
            <div class="card-body">
                <img style="width: 90%; height:90%" src="{{ $sr->user->photo }}" alt="photo" class="rounded-circle">
                <br>
                <h3 class="mt-3">{{ $sr->user->name }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#basic-info" class="nav-link active" data-toggle="tab">Informations personnelles</a>
                    </li>
                    <li class="nav-item">
                        <a href="#report-cards" class="nav-link" data-toggle="tab">Bulletins de notes</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Informations de base--}}
                    <div class="tab-pane fade show active" id="basic-info">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td class="font-weight-bold">Nom</td>
                                <td>{{ $sr->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">ADM_NO</td>
                                <td>{{ $sr->adm_no }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Classe</td>
                                <td>{{ $sr->my_class->name.' '.$sr->section->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Statut</td>
                                <td>{{ $sr->user->status ?? 'Normal' }}</td>
                            </tr>
                            @if($sr->my_parent_id)
                                <tr>
                                    <td class="font-weight-bold">Parent</td>
                                    <td>
                                        <span><a target="_blank" href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></span>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="font-weight-bold">Père/Tuteur</td>
                                <td>{{ $sr->user->nom_p }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Profession du père/tuteur</td>
                                <td>{{ $sr->user->prof_p }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Mère/Tutrice</td>
                                <td>{{ $sr->user->nom_m }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Profession de la mère/tutrice</td>
                                <td>{{ $sr->user->prof_m }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Année d'admission</td>
                                <td>{{ $sr->year_admitted }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Genre</td>
                                <td>{{ $sr->user->gender }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Adresse</td>
                                <td>{{ $sr->user->address }}</td>
                            </tr>
                            @if($sr->user->email)
                            <tr>
                                <td class="font-weight-bold">E-mail</td>
                                <td>{{$sr->user->email }}</td>
                            </tr>
                            @endif
                            @if($sr->user->phone)
                                <tr>
                                    <td class="font-weight-bold">Téléphone</td>
                                    <td>{{$sr->user->phone.' '.$sr->user->phone2 }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="font-weight-bold">Date de naissance</td>
                                <td>{{$sr->user->dob }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Âge</td>
                                <td>{{ Qs::calculateAge($sr->user->dob) }}</td>
                            </tr>
                            @if($sr->user->bg_id)
                            <tr>
                                <td class="font-weight-bold">Groupe sanguin</td>
                                <td>{{$sr->user->blood_group->name }}</td>
                            </tr>
                            @endif
                            @if($sr->user->nal_id)
                            <tr>
                                <td class="font-weight-bold">Nationalité</td>
                                <td>{{$sr->user->nationality->name }}</td>
                            </tr>
                            @endif
                            @if($sr->user->state_id)
                            <tr>
                                <td class="font-weight-bold">État</td>
                                <td>{{$sr->user->state->name }}</td>
                            </tr>
                            @endif
                            @if($sr->user->lga_id)
                            <tr>
                                <td class="font-weight-bold">LGA</td>
                                <td>{{$sr->user->lga->name }}</td>
                            </tr>
                            @endif
                            @if($sr->dorm_id)
                                <tr>
                                    <td class="font-weight-bold">Dortoir</td>
                                    <td>{{$sr->dorm->name.' '.$sr->dorm_room_no }}</td>
                                </tr>
                            @endif

                            </tbody>
                        </table>
                    </div>

                    {{--Bulletins de notes--}}
                    <div class="tab-pane fade" id="report-cards">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">Bulletins de notes disponibles</h5>
                                    <p>Cliquez sur un bulletin pour le consulter ou l'imprimer.</p>
                                </div>

                                <div class="text-center mb-3">
                                    <a href="{{ route('marks.year_selector', Qs::hash($sr->user_id)) }}" class="btn btn-primary btn-lg">
                                        <i class="icon-book mr-2"></i> Voir tous les bulletins de notes
                                    </a>
                                </div>

                                <div class="list-group">
                                    @php
                                        $years = DB::table('marks')->where('student_id', $sr->user_id)->distinct()->pluck('year');
                                    @endphp

                                    @if($years->count() > 0)
                                        @foreach($years as $year)
                                            <a href="{{ route('marks.show', [Qs::hash($sr->user_id), $year]) }}" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">Année scolaire {{ $year }}</h5>
                                                </div>
                                                <p class="mb-1">Bulletins de notes pour l'année scolaire {{ $year }}</p>
                                                <small>Cliquez pour voir les détails</small>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning">
                                            <h5 class="alert-heading">Aucun bulletin disponible</h5>
                                            <p>Aucun bulletin de notes n'est disponible pour cet élève.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


    {{--Fin du profil de l'étudiant--}}

@endsection
