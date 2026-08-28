@extends('layouts.master')
@section('page_title', 'Détails du Journal d\'Activité')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Informations Générales</h6>
            <div class="header-elements">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm"><i class="icon-arrow-left5 mr-1"></i> Retour</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Date:</th>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Utilisateur:</th>
                            <td>{{ $log->user->name ?? 'Système' }}</td>
                        </tr>
                        <tr>
                            <th>Adresse IP:</th>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>Module:</th>
                            <td>{{ $log->module }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Action:</th>
                            <td>{{ $log->action }}</td>
                        </tr>
                        <tr>
                            <th>Type Modèle:</th>
                            <td>{{ $log->model_type }}</td>
                        </tr>
                        <tr>
                            <th>ID Modèle:</th>
                            <td>{{ $log->model_id }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $log->description }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($log->old_values || $log->new_values)
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Différence des Données</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Anciennes Valeurs</h6>
                    @if($log->old_values)
                        <pre style="background: #f8d7da; padding: 15px; border-radius: 5px;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <div class="alert alert-info">Aucune ancienne valeur.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>Nouvelles Valeurs</h6>
                    @if($log->new_values)
                        <pre style="background: #d4edda; padding: 15px; border-radius: 5px;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <div class="alert alert-info">Aucune nouvelle valeur.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
