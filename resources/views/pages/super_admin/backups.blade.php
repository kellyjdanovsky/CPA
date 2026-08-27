@extends('layouts.master')
@section('page_title', 'Sauvegarde & Maintenance Locale de la Base de Données')

@section('content')

    {{-- En-tête KPI --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card card-body bg-primary-400 text-white text-center shadow-sm" style="border-radius: 12px;">
                <h6 class="font-weight-semibold text-uppercase mb-1"><i class="icon-database mr-1"></i> Base de Données Locale</h6>
                <h3 class="mb-0 font-weight-bold">{{ $dbName }}</h3>
                <small class="opacity-75">Taille totale : {{ $dbSizeMb }} Mo</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body bg-success-400 text-white text-center shadow-sm" style="border-radius: 12px;">
                <h6 class="font-weight-semibold text-uppercase mb-1"><i class="icon-table2 mr-1"></i> Tables Actives</h6>
                <h3 class="mb-0 font-weight-bold">{{ $tablesCount }} Tables</h3>
                <small class="opacity-75">Structure relationnelle complète</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body bg-indigo-400 text-white text-center shadow-sm" style="border-radius: 12px;">
                <h6 class="font-weight-semibold text-uppercase mb-1"><i class="icon-floppy-disk mr-1"></i> Sauvegardes Disponibles</h6>
                <h3 class="mb-0 font-weight-bold">{{ count($backups) }} Fichiers</h3>
                <small class="opacity-75">Stockage local sécurisé</small>
            </div>
        </div>
    </div>

    {{-- Actions Principales --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="card-title font-weight-bold mb-0 text-dark">
                <i class="icon-shield-check mr-2 text-primary"></i> Actions de Sécurité & Continuité Hors-Ligne
            </h6>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <p class="mb-1 text-muted">
                        <strong>Recommandation de sécurité :</strong> Effectuez une sauvegarde locale quotidienne ou hebdomadaire. 
                        Vous pouvez télécharger le fichier SQL sur une <strong>clé USB</strong> pour protéger l'école contre toute panne matérielle ou coupure de courant.
                    </p>
                </div>
                <div class="col-md-5 text-md-right mt-2 mt-md-0">
                    <form method="POST" action="{{ route('super_admin.backups.create') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success font-weight-semibold shadow-sm">
                            <i class="icon-floppy-disk mr-1"></i> Créer une Sauvegarde Maintenant
                        </button>
                    </form>
                    <form method="POST" action="{{ route('super_admin.backups.clean_cache') }}" class="d-inline ml-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary font-weight-semibold">
                            <i class="icon-brush mr-1"></i> Optimiser le Système
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des Sauvegardes --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-header bg-light">
            <h6 class="card-title font-weight-bold mb-0 text-dark">
                <i class="icon-history mr-2 text-info"></i> Historique des Fichiers de Sauvegarde
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">N°</th>
                            <th>Nom du fichier</th>
                            <th style="width: 150px;" class="text-center">Taille</th>
                            <th style="width: 200px;" class="text-center">Date & Heure de création</th>
                            <th style="width: 180px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $b)
                            <tr>
                                <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <i class="icon-file-text2 mr-2 text-primary"></i>
                                    <strong>{{ $b['name'] }}</strong>
                                </td>
                                <td class="text-center font-weight-semibold">{{ $b['size'] }}</td>
                                <td class="text-center">{{ $b['created_at'] }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('super_admin.backups.download', $b['name']) }}" class="btn btn-sm btn-primary font-weight-semibold shadow-sm" title="Télécharger sur ce PC ou Clé USB">
                                            <i class="icon-download4 mr-1"></i> Télécharger
                                        </a>
                                        <form method="POST" action="{{ route('super_admin.backups.delete', $b['name']) }}" onsubmit="return confirm('Confirmez-vous la suppression de cette sauvegarde ?');" class="d-inline ml-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="icon-info3 mr-1"></i> Aucune sauvegarde locale enregistrée pour le moment. Cliquez sur "Créer une Sauvegarde Maintenant".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
