@extends('layouts.master')
@section('page_title', 'Journal d\'Activités')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Filtres de Recherche</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('activity-logs.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Utilisateur:</label>
                            <select name="user_id" class="form-control select">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Module:</label>
                            <select name="module" class="form-control select">
                                <option value="">Tous</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Action:</label>
                            <select name="action" class="form-control select">
                                <option value="">Toutes</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date de début:</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date de fin:</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group" style="margin-top: 28px;">
                            <button type="submit" class="btn btn-primary btn-block"><i class="icon-search4"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Journal d'Activités</h6>
            <div class="header-elements">
                <a href="{{ route('activity-logs.export', request()->all()) }}" class="btn btn-success btn-sm mr-2"><i class="icon-file-excel mr-1"></i> Exporter CSV</a>
                <form action="{{ route('activity-logs.cleanup') }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir nettoyer les anciens journaux ?');">
                    @csrf
                    <input type="hidden" name="days" value="30">
                    <button type="submit" class="btn btn-danger btn-sm"><i class="icon-trash mr-1"></i> Nettoyer (>30j)</button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <table class="table datatable-basic table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user->name ?? 'Système' }}</td>
                            <td><span class="badge badge-info">{{ $log->module }}</span></td>
                            <td><span class="badge badge-secondary">{{ $log->action }}</span></td>
                            <td>{{ Str::limit($log->description, 50) }}</td>
                            <td class="text-center">
                                <a href="{{ route('activity-logs.show', Qs::hash($log->id)) }}" class="btn btn-primary btn-sm"><i class="icon-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
