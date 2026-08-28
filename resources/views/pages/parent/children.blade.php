@extends('layouts.master')
@section('page_title', 'Portail Parent')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Mes Enfants</h6>
        {!! Qs::getPanelOptions() !!}
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            @foreach($students as $st)
                <li class="nav-item"><a href="#child{{ $st->id }}" class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab">{{ $st->user->name ?? 'Enfant' }}</a></li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($students as $st)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="child{{ $st->id }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Profil</div>
                            <div class="card-body">
                                <p><strong>Nom:</strong> {{ $st->user->name ?? '' }}</p>
                                <p><strong>Classe:</strong> {{ $st->my_class->name ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Finances</div>
                            <div class="card-body">
                                <p>Statut des paiements: À jour</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Assiduité</div>
                            <div class="card-body">
                                <p>Taux de présence: 95%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
