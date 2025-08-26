@extends('layouts.master')
@section('page_title', 'Gérer les paiements')
@php
    use Illuminate\Support\Str;
@endphp

@section('page_style')
<style>
/* ADRA & TEAM 3 Tab Styling */
.border-left-primary {
    border-left: 4px solid #667eea !important;
}

.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transform: translateY(-2px);
}

.alert-icon {
    flex-shrink: 0;
}

.nav-tabs .nav-link {
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #f8f9fa;
}

.btn-group-vertical .btn {
    transition: all 0.3s ease;
}

.btn-group-vertical .btn:hover {
    transform: translateX(5px);
}

.badge {
    font-size: 0.75em;
}

.rounded-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
}

.flex-shrink-0 {
    flex-shrink: 0;
}

.flex-grow-1 {
    flex-grow: 1;
}

/* ADRA & TEAM 3 Quick Access Card */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-header.bg-gradient-primary {
    border-bottom: none;
}

.badge-light {
    background-color: rgba(255, 255, 255, 0.9);
    color: #333;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection

@section('content')

    <div class="card fade-in">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2 text-primary"></i> Choix de l'année scolaire</h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('payments.select_year') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="year" class="col-form-label font-weight-bold">Choisir l'année scolaire <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar"></i></span>
                                        </div>
                                        <select data-placeholder="Choisir l'année" required id="year" name="year" class="form-control select">
                                            @foreach($years as $yr)
                                                <option {{ ($selected && $year == $yr->year) ? 'selected' : '' }} value="{{ $yr->year }}">{{ $yr->year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-4">
                                <div class="text-right mt-1">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="icon-search4 mr-2"></i> Afficher
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- ADRA & TEAM 3 Quick Access Card -->
    <div class="card fade-in mt-3">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="card-title mb-0">
                <i class="icon-users4 mr-2"></i>
                <span class="badge badge-light mr-2">🏛️</span> ADRA &
                <span class="badge badge-light mr-2">👥</span> TEAM 3
                - Gestion Spécialisée des Paiements
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-3">
                            <div class="bg-primary rounded-circle p-3">
                                <i class="icon-credit-card text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Interface Spécialisée pour Étudiants ADRA & TEAM 3</h6>
                            <p class="text-muted mb-2">
                                Gérez facilement les paiements avec calculs automatiques :
                                <strong>ADRA (75% pris en charge)</strong> et <strong>TEAM 3 (100% pris en charge)</strong>
                            </p>
                            <div class="d-flex flex-wrap">
                                <span class="badge badge-info mr-2 mb-1">📊 Calculs automatiques</span>
                                <span class="badge badge-success mr-2 mb-1">🖨️ Impression thermique</span>
                                <span class="badge badge-warning mr-2 mb-1">📋 Export Excel</span>
                                <span class="badge badge-secondary mr-2 mb-1">📝 Journal intégré</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('payments.adra_team3.filter') }}?class_id=1" class="btn btn-primary btn-lg mb-2">
                            <i class="icon-arrow-right8 mr-2"></i>Accéder à l'Interface
                        </a>
                        <br>
                        <small class="text-muted">Accès direct à la gestion ADRA & TEAM 3</small>
                    </div>
                </div>
            </div>

            @if(isset($my_classes) && $my_classes->count() > 0)
            <hr class="my-3">
            <div class="row">
                <div class="col-md-12">
                    <h6 class="mb-3">Accès Rapide par Classe :</h6>
                    <div class="d-flex flex-wrap">
                        @foreach($my_classes->take(6) as $mc)
                            <a href="{{ route('payments.adra_team3.filter') }}?class_id={{ $mc->id }}"
                               class="btn btn-outline-primary btn-sm mr-2 mb-2">
                                <i class="icon-graduation mr-1"></i>{{ $mc->name }}
                            </a>
                        @endforeach
                        @if($my_classes->count() > 6)
                            <a href="{{ route('payments.adra_team3.filter') }}" class="btn btn-outline-secondary btn-sm mb-2">
                                <i class="icon-more mr-1"></i>Voir toutes les classes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

@if($selected)
    <div class="card fade-in mt-3">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title">
                <i class="icon-coins mr-2 text-success"></i> Paiements pour l'année {{ $year }}
            </h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-payments" class="nav-link active" data-toggle="tab"><i class="icon-grid mr-2"></i> Toutes les Classes</a></li>
                <li class="nav-item">
                    <a href="#adra-team3-payments" class="nav-link" data-toggle="tab">
                        <i class="icon-users4 mr-2"></i>
                        <span class="badge badge-info mr-1">🏛️</span> ADRA &
                        <span class="badge badge-success mr-1">👥</span> TEAM 3
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#encaissements-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-wallet mr-2"></i> Encaissements
                        <span class="badge badge-primary ml-1">Nouveau</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#recettes-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-coins mr-2"></i> Recettes
                        <span class="badge badge-success ml-1">Consolidé</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#decaissements-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-file-text2 mr-2"></i> Décaissements
                        <span class="badge badge-warning ml-1">OP</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="icon-filter3 mr-2"></i> Par Classe</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_classes as $mc)
                            <a href="#pc-{{ $mc->id }}" class="dropdown-item" data-toggle="tab">{{ $mc->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-payments">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Montant</th>
                            <th>Ref_No</th>
                            <th>Classe</th>
                            <th>Méthode</th>
                            <th>Info</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="font-weight-semibold text-primary">{{ $p->title }}</span></td>
                                <td><span class="badge badge-success">{{ number_format($p->amount, 0, ',', ' ') }} Ar</span></td>
                                <td><span class="badge badge-info">{{ $p->ref_no }}</span></td>
                                <td>{{ $p->my_class_id ? $p->my_class->name : '' }}</td>
                                <td>
                                    @php
                                        $methodClass = 'badge-secondary';
                                        if(strtolower($p->method) == 'cash') {
                                            $methodClass = 'badge-success';
                                        } elseif(strtolower($p->method) == 'card') {
                                            $methodClass = 'badge-info';
                                        } elseif(strtolower($p->method) == 'adra') {
                                            $methodClass = 'badge-primary';
                                        }
                                    @endphp
                                    <span class="badge {{ $methodClass }}">{{ ucwords($p->method) }}</span>
                                </td>
                                <td>
                                    @if($p->description)
                                        <span class="text-muted" data-toggle="tooltip" title="{{ $p->description }}">
                                            {{ Str::limit($p->description, 20) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="list-icons action-buttons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right">
                                                {{-- Modifier --}}
                                                <a href="{{ route('payments.edit', $p->id) }}" class="dropdown-item"><i class="icon-pencil text-info"></i> Modifier</a>
                                                {{-- Supprimer --}}
                                                <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash text-danger"></i> Supprimer</a>
                                                <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('payments.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ADRA & TEAM 3 Tab Content -->
                <div class="tab-pane fade" id="adra-team3-payments">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon mr-3">
                                        <i class="icon-info22 text-info" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">Interface Spécialisée ADRA & TEAM 3</h6>
                                        <p class="mb-0">
                                            Gérez les paiements pour les étudiants ADRA (75% pris en charge) et TEAM 3 (100% pris en charge)
                                            avec impression thermique et calculs automatiques.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary rounded-circle p-3">
                                                <i class="icon-users4 text-white" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ml-3">
                                            <h5 class="card-title mb-1">Gestion ADRA & TEAM 3</h5>
                                            <p class="card-text text-muted mb-2">
                                                Interface complète pour la gestion des paiements spécialisés
                                            </p>
                                            <a href="{{ route('payments.adra_team3.filter') }}?class_id=1" class="btn btn-primary btn-sm">
                                                <i class="icon-arrow-right8 mr-2"></i>Accéder à l'interface
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success rounded-circle p-3">
                                                <i class="icon-printer mr-1 text-white" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ml-3">
                                            <h5 class="card-title mb-1">Impression Thermique</h5>
                                            <p class="card-text text-muted mb-2">
                                                Reçus optimisés pour imprimantes 58mm avec calculs automatiques
                                            </p>
                                            <div class="d-flex">
                                                <span class="badge badge-info mr-2">🏛️ ADRA 75%</span>
                                                <span class="badge badge-success">👥 TEAM3 100%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-list mr-2"></i>Fonctionnalités Disponibles
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Filtrage par classe avec sélection dynamique
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Sélection multiple des paiements par étudiant
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Calculs automatiques selon le statut (ADRA/TEAM3)
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Codes de référence éditables en temps réel
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Impression individuelle et par lot
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Export Excel/CSV avec données complètes
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Intégration automatique au journal des paiements
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Interface moderne avec DataTable responsive
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="mb-3">Accès Rapide par Classe</h5>
                                    <div class="btn-group-vertical btn-group-lg" role="group">
                                        @foreach($my_classes as $mc)
                                            <a href="{{ route('payments.adra_team3.filter') }}?class_id={{ $mc->id }}"
                                               class="btn btn-outline-primary mb-2">
                                                <i class="icon-graduation mr-2"></i>{{ $mc->name }}
                                                <span class="badge badge-secondary ml-2">ADRA & TEAM3</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($my_classes as $mc)
                    <div class="tab-pane fade" id="pc-{{ $mc->id }}">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Montant</th>
                                <th>Ref_No</th>
                                <th>Classe</th>
                                <th>Méthode</th>
                                <th>Info</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments->where('my_class_id', $mc->id) as $p)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="font-weight-semibold text-primary">{{ $p->title }}</span></td>
                                    <td><span class="badge badge-success">{{ number_format($p->amount, 0, ',', ' ') }} Ar</span></td>
                                    <td><span class="badge badge-info">{{ $p->ref_no }}</span></td>
                                    <td>{{ $p->my_class_id ? $p->my_class->name : '' }}</td>
                                    <td>
                                        @php
                                            $methodClass = 'badge-secondary';
                                            if(strtolower($p->method) == 'cash') {
                                                $methodClass = 'badge-success';
                                            } elseif(strtolower($p->method) == 'card') {
                                                $methodClass = 'badge-info';
                                            } elseif(strtolower($p->method) == 'adra') {
                                                $methodClass = 'badge-primary';
                                            }
                                        @endphp
                                        <span class="badge {{ $methodClass }}">{{ ucwords($p->method) }}</span>
                                    </td>
                                    <td>
                                        @if($p->description)
                                            <span class="text-muted" data-toggle="tooltip" title="{{ $p->description }}">
                                                {{ Str::limit($p->description, 20) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="list-icons action-buttons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-right">
                                                    {{-- Modifier --}}
                                                    <a href="{{ route('payments.edit', $p->id) }}" class="dropdown-item"><i class="icon-pencil text-info"></i> Modifier</a>
                                                    {{-- Supprimer --}}
                                                    <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash text-danger"></i> Supprimer</a>
                                                    <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('payments.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <!-- Encaissements Tab Content -->
                <div class="tab-pane fade" id="encaissements-tab">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-primary border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon mr-3">
                                        <i class="icon-wallet text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">Module Encaissements</h6>
                                        <p class="mb-0">
                                            Gérez les encaissements spécialisés : <strong>75% ADRA</strong> et <strong>100% TEAM 3</strong>.
                                            Sélectionnez une classe, des paiements et processez automatiquement les encaissements.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-primary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-plus-circle2 text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Nouvel Encaissement</h5>
                                    <p class="card-text text-muted mb-3">Créer un nouvel encaissement ADRA ou TEAM3</p>
                                    <a href="{{ route('payments.encaissements.create') }}" class="btn btn-primary">
                                        <i class="icon-plus3 mr-2"></i>Créer
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-left-success">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-list text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Gérer les Encaissements</h5>
                                    <p class="card-text text-muted mb-3">Voir et gérer tous les encaissements</p>
                                    <a href="{{ route('payments.encaissements.index') }}" class="btn btn-success">
                                        <i class="icon-eye mr-2"></i>Voir tout
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-left-info">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-file-text text-info" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Journal</h5>
                                    <p class="card-text text-muted mb-3">Consulter le journal des encaissements</p>
                                    <a href="{{ route('payments.encaissements.journal') }}" class="btn btn-info">
                                        <i class="icon-book mr-2"></i>Journal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-info22 mr-2"></i>Fonctionnalités des Encaissements
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Calcul automatique des montants (75% ADRA, 100% TEAM3)
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Sélection par classe et type de paiement
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Génération automatique des références
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Intégration automatique aux recettes
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Export Excel et impression de reçus
                                                </li>
                                                <li class="mb-2">
                                                    <i class="icon-checkmark3 text-success mr-2"></i>
                                                    Journal détaillé avec statistiques
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recettes Tab Content -->
                <div class="tab-pane fade" id="recettes-tab">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-success border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon mr-3">
                                        <i class="icon-coins text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">Module Recettes</h6>
                                        <p class="mb-0">
                                            Centralisez tous les encaissements entrants : cash, ADRA, TEAM3, et frais divers.
                                            Vue consolidée avec filtres avancés et exports.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-left-success">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-eye text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Voir Recettes</h5>
                                    <p class="card-text text-muted mb-3">Consulter toutes les recettes</p>
                                    <a href="{{ route('payments.recettes.index') }}" class="btn btn-success">
                                        <i class="icon-list mr-2"></i>Consulter
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-left-info">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-plus-circle2 text-info" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Recette Manuelle</h5>
                                    <p class="card-text text-muted mb-3">Ajouter une recette diverse</p>
                                    <a href="{{ route('payments.recettes.create') }}" class="btn btn-info">
                                        <i class="icon-plus3 mr-2"></i>Ajouter
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-left-warning">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-sync text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Synchronisation</h5>
                                    <p class="card-text text-muted mb-3">Synchroniser avec les reçus</p>
                                    <button class="btn btn-warning" onclick="syncReceipts()">
                                        <i class="icon-sync mr-2"></i>Synchroniser
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-left-primary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-stats-bars text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Tableau de Bord</h5>
                                    <p class="card-text text-muted mb-3">Statistiques détaillées</p>
                                    <a href="{{ route('payments.recettes.dashboard') }}" class="btn btn-primary">
                                        <i class="icon-stats-dots mr-2"></i>Statistiques
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-info22 mr-2"></i>Colonnes et Filtres Disponibles
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Colonnes Principales :</h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Date</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Élève / Bénéficiaire</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Classe</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Paiement concerné</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Montant encaissé</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Mode de paiement</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Référence</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-success">Filtres Disponibles :</h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Par date (période)</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Par classe</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Par statut (Normal, ADRA, TEAM 3)</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Par mode de paiement</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Export Excel/PDF</li>
                                                <li class="mb-1"><i class="icon-arrow-right8 text-muted mr-2"></i>Recherche par nom/référence</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Décaissements Tab Content -->
                <div class="tab-pane fade" id="decaissements-tab">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-warning border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon mr-3">
                                        <i class="icon-file-text2 text-warning" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">Module Décaissements (Ordres de Paiement)</h6>
                                        <p class="mb-0">
                                            Gérez les ordres de paiement (OP) avec workflow d'approbation, pièces justificatives,
                                            et impression professionnelle (2 par page A4).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-left-warning">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-plus-circle2 text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Créer OP</h5>
                                    <p class="card-text text-muted mb-3">Nouveau ordre de paiement</p>
                                    <a href="{{ route('payments.decaissements.create') }}" class="btn btn-warning">
                                        <i class="icon-plus3 mr-2"></i>Créer OP
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-left-primary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-list text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Gérer OP</h5>
                                    <p class="card-text text-muted mb-3">Voir tous les décaissements</p>
                                    <a href="{{ route('payments.decaissements.index') }}" class="btn btn-primary">
                                        <i class="icon-eye mr-2"></i>Gérer
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-left-success">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-printer text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Impression</h5>
                                    <p class="card-text text-muted mb-3">OP avec en-tête et signatures</p>
                                    <a href="{{ route('payments.decaissements.index') }}?print=1" class="btn btn-success">
                                        <i class="icon-printer mr-2"></i>Imprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-left-info">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="icon-file-text text-info" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title">Journal</h5>
                                    <p class="card-text text-muted mb-3">Journal des décaissements</p>
                                    <a href="{{ route('payments.decaissements.journal') }}" class="btn btn-info">
                                        <i class="icon-book mr-2"></i>Journal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-list mr-2"></i>Colonnes Principales
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Date</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Référence OP</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Bénéficiaire</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Montant (chiffres et lettres)</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Motif</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Mode de paiement</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Projet / Rubrique budgétaire</li>
                                        <li class="mb-2"><i class="icon-arrow-right8 text-muted mr-2"></i>Pièce justificative (✔️/❌)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-cog mr-2"></i>Fonctions Disponibles
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Création OP avec formulaire complet</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Téléversement pièces justificatives</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Workflow d'approbation</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Impression OP (2 par page A4)</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>En-tête école et signatures</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Journal avec filtres et exports</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Statuts : En attente, Approuvé, Payé, Annulé</li>
                                        <li class="mb-2"><i class="icon-checkmark3 text-success mr-2"></i>Montants automatiques en lettres</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

    {{--Liste des paiements terminée--}}

@endsection

@section('page_script')
<script>
    // Fonction de synchronisation des recettes
    function syncReceipts() {
        if (confirm('Voulez-vous synchroniser les recettes avec les reçus existants ?')) {
            // Afficher un loader
            let button = event.target;
            let originalText = button.innerHTML;
            button.innerHTML = '<i class="icon-spinner2 spinner mr-2"></i>Synchronisation...';
            button.disabled = true;
            
            // Effectuer la synchronisation via Ajax
            fetch('{{ route("payments.recettes.sync_receipts") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    year: '{{ $year ?? date("Y") }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    showNotification('Succès', data.message, 'success');
                } else {
                    showNotification('Erreur', data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue lors de la synchronisation.', 'danger');
            })
            .finally(() => {
                // Restaurer le bouton
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    }

    // Fonction pour afficher les notifications
    function showNotification(title, message, type) {
        // Créer une notification Bootstrap toast ou alert
        let alertClass = 'alert-' + type;
        let alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <strong>${title}:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Ajouter la notification en haut de la page
        let container = document.querySelector('.content');
        let alertDiv = document.createElement('div');
        alertDiv.innerHTML = alertHtml;
        container.insertBefore(alertDiv, container.firstChild);
        
        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Initialisation des tooltips
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        
        // Animation des cartes au survol
        $('.card').hover(
            function() {
                $(this).addClass('shadow-lg').css('cursor', 'pointer');
            }, 
            function() {
                $(this).removeClass('shadow-lg');
            }
        );
    });
</script>
@endsection
