@extends('layouts.master')
@section('page_title', 'Clôture de Session Automatisée')
@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Assistant de Clôture - {{ $session }}</h6>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#step1" class="nav-link active" data-toggle="tab">1. Vérification Notes</a></li>
            <li class="nav-item"><a href="#step2" class="nav-link" data-toggle="tab">2. Décisions de Passage</a></li>
            <li class="nav-item"><a href="#step3" class="nav-link" data-toggle="tab">3. Soldes Financiers</a></li>
            <li class="nav-item"><a href="#step4" class="nav-link" data-toggle="tab">4. Sauvegarde Base</a></li>
            <li class="nav-item"><a href="#step5" class="nav-link" data-toggle="tab">5. Nouvelle Année</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="step1">
                <p>Vérification de la saisie des notes pour tous les élèves.</p>
                <button class="btn btn-success validate-step" data-step="1">Valider Étape 1</button>
            </div>
            <!-- Autres étapes de même structure... -->
            <div class="tab-pane fade" id="step5">
                <p>Création et activation de la nouvelle année scolaire.</p>
                <a href="{{ route('sessions.closure.print') }}" class="btn btn-primary" target="_blank">Clôturer et Imprimer PV</a>
            </div>
        </div>
    </div>
</div>
@endsection