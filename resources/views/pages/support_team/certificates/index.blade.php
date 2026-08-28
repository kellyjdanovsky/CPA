@extends('layouts.master')
@section('page_title', 'Certificats & Attestations')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Filtres de Recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url('certificates') }}">
                <div class="row">
                    <div class="col-md-3">
                        <select name="type" class="form-control select">
                            <option value="">Tous les Types</option>
                            @foreach($types as $key => $val)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="academic_year" class="form-control select">
                            <option value="">Toutes les Années</option>
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ request('academic_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="student_id" placeholder="Rechercher par ID Elève" class="form-control" value="{{ request('student_id') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="icon-search4"></i> Filtrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Liste des Certificats Générés</h6>
            <div class="header-elements">
                <a href="{{ url('certificates/create') }}" class="btn btn-success mr-2">Générer un Certificat</a>
                <a href="{{ url('certificates/batch-generate') }}" class="btn btn-info mr-2">Génération par Lot</a>
                <a href="{{ url('certificates/export') }}" class="btn btn-secondary"><i class="icon-file-excel"></i> Exporter</a>
            </div>
        </div>

        <div class="card-body">
            <table class="table datatable-basic">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Élève</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Année Scolaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($certificates as $cert)
                        <tr>
                            <td>{{ $cert->reference_no }}</td>
                            <td>{{ $cert->student->name ?? 'N/A' }}</td>
                            <td>{{ $types[$cert->type] ?? $cert->type }}</td>
                            <td>{{ date('d/m/Y', strtotime($cert->date_issued)) }}</td>
                            <td>{{ $cert->academic_year }}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ url('certificates/print', App\Helpers\Qs::hash($cert->id)) }}" class="dropdown-item" target="_blank"><i class="icon-printer"></i> Imprimer</a>
                                            
                                            <form action="{{ url('certificates', App\Helpers\Qs::hash($cert->id)) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce certificat ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="icon-trash"></i> Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
