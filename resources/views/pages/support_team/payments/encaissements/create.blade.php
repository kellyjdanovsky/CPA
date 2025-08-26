@extends('layouts.master')

@section('page_title', 'Nouvel Encaissement')

@section('content')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h5 class="card-title">
            <i class="icon-plus-circle2 mr-2 text-success"></i> 
            Nouvel Encaissement
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('payments.encaissements.index') }}" class="btn btn-outline-primary">
                    <i class="icon-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info">
            <i class="icon-info22 mr-2"></i>
            <strong>Information :</strong> Le formulaire de création d'encaissement est intégré dans la page principale des encaissements. 
            Cliquez sur le bouton ci-dessous pour accéder au formulaire de création.
        </div>
        
        <div class="text-center">
            <a href="{{ route('payments.encaissements.index') . '#nouveau-encaissement' }}" 
               class="btn btn-primary btn-lg">
                <i class="icon-plus-circle2 mr-2"></i>Créer un nouvel encaissement
            </a>
        </div>
    </div>
</div>
@endsection

@section('page_script')
<script>
// Redirection automatique après 3 secondes
setTimeout(function() {
    window.location.href = "{{ route('payments.encaissements.index') }}#nouveau-encaissement";
}, 3000);
</script>
@endsection