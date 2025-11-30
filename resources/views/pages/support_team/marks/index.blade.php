@extends('layouts.master')
@section('page_title', 'Gérer les notes d\'examens')
@section('content')
    <div class="modern-dashboard-header mb-4" style="padding: 2rem 0; margin: -1.5rem -1.5rem 1.5rem;">
        <div class="header-background">
            <div class="header-overlay"></div>
             <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </div>
        <div class="header-content">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="greeting-text mb-2" style="font-size: 1.8rem;">Gestion des Notes</h1>
                        <p class="greeting-subtitle">Saisie et consultation des résultats</p>
                    </div>
                    <div class="col-md-4 text-right">
                         {!! Qs::getPanelOptions() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-books mr-2"></i> Sélectionner les critères</h5>
        </div>

        <div class="card-body">
            @include('pages.support_team.marks.selector')
        </div>
    </div>
@endsection
