@extends('layouts.master')
@section('page_title', 'Centre d\'Impression Unifié')
@section('content')

    <div class="row">
        <!-- 🎓 Académique -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title"><i class="icon-graduation2 mr-2"></i> Académique</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Bulletins individuels</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Bulletins par classe</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Tabulation des notes</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Notes pondérées</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Certificats de scolarité</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Attestations de réussite</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 👥 Élèves & Présences -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title"><i class="icon-users mr-2"></i> Élèves & Présences</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Listes d'élèves par classe</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Cartes scolaires</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Feuilles de présence mensuelles</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 💰 Finance & Comptabilité -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h6 class="card-title"><i class="icon-coin-dollar mr-2"></i> Finance & Comptabilité</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Reçus de paiement</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Avis d'impayés 2x5</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Ordres de paiement OP</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Journal de caisse / PV de clôture</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Reçus thermiques 58mm</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 📅 Organisation & Rapports -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title"><i class="icon-calendar mr-2"></i> Organisation & Rapports</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Calendrier scolaire A4</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Emplois du temps par classe</a></li>
                        <li class="mb-2"><a href="#" class="text-default"><i class="icon-printer mr-2"></i> Rapport annuel de l'école</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Selectors (Exemple pour les classes) -->
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Sélecteur Rapide</h6>
        </div>
        <div class="card-body">
            <form action="#" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Classe:</label>
                            <select name="class_id" class="form-control select">
                                <option value="">Sélectionnez une classe</option>
                                @foreach($my_classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Examen:</label>
                            <select name="exam_id" class="form-control select">
                                <option value="">Sélectionnez un examen</option>
                                @foreach($exams as $e)
                                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Année:</label>
                            <select name="year" class="form-control select">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ ($y == App\Helpers\Qs::getSetting('current_session')) ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
