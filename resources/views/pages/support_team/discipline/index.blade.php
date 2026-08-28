@extends('layouts.master')
@section('page_title', 'Gestion de la Discipline')
@section('content')

<div class="row">
    <div class="col-sm-3">
        <div class="card card-body bg-danger-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $total_incidents }}</h3>
                    <span class="text-uppercase font-size-xs">Incidents ce mois</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-warning22 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card card-body bg-warning-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $total_sanctions }}</h3>
                    <span class="text-uppercase font-size-xs">Sanctions</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-hammer icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card card-body bg-success-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $total_recompenses }}</h3>
                    <span class="text-uppercase font-size-xs">Récompenses</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-medal-star icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card card-body bg-info-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $eleves_concernes }}</h3>
                    <span class="text-uppercase font-size-xs">Élèves concernés</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-users4 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Enregistrements Disciplinaires</h6>
        <div class="header-elements">
            <a href="{{ route('discipline.create') }}" class="btn btn-primary"><i class="icon-plus3 mr-2"></i> Ajouter</a>
            <a href="{{ route('discipline.class_report') }}" class="btn btn-info ml-2"><i class="icon-stats-bars mr-2"></i> Rapport par Classe</a>
            <a href="{{ route('discipline.export') }}" class="btn btn-success ml-2"><i class="icon-file-excel mr-2"></i> Exporter Excel</a>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#tous" class="nav-link active" data-toggle="tab">Tous</a></li>
            <li class="nav-item"><a href="#incidents" class="nav-link" data-toggle="tab">Incidents</a></li>
            <li class="nav-item"><a href="#sanctions" class="nav-link" data-toggle="tab">Sanctions</a></li>
            <li class="nav-item"><a href="#recompenses" class="nav-link" data-toggle="tab">Récompenses</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tous">
                <table class="table datatable-basic">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Élève</th>
                            <th>Classe</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Gravité</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $rec)
                        <tr>
                            <td>{{ $rec->date_incident }}</td>
                            <td>{{ $rec->student->name }}</td>
                            <td>{{ $rec->student->studentRecord->my_class->name ?? '' }}</td>
                            <td>
                                @if($rec->type == 'incident') <span class="badge badge-danger">Incident</span>
                                @elseif($rec->type == 'sanction') <span class="badge badge-warning">Sanction</span>
                                @else <span class="badge badge-success">Récompense</span> @endif
                            </td>
                            <td>{{ $rec->category }}</td>
                            <td>{!! $rec->severity_badge !!}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('discipline.student_history', Qs::hash($rec->student_id)) }}" class="dropdown-item"><i class="icon-history"></i> Historique élève</a>
                                            <form action="{{ route('discipline.destroy', Qs::hash($rec->id)) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item" onclick="return confirm('Êtes-vous sûr ?')"><i class="icon-trash"></i> Supprimer</button>
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
            
            <div class="tab-pane fade" id="incidents">
                <!-- Similar table filtered by incident -->
            </div>
            <div class="tab-pane fade" id="sanctions">
                <!-- Similar table filtered by sanction -->
            </div>
            <div class="tab-pane fade" id="recompenses">
                <!-- Similar table filtered by recompense -->
            </div>
        </div>
    </div>
</div>
@endsection
