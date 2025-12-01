@extends('layouts.master')
@section('page_title', 'PASCOMA - Attestations d\'Assurance')

@section('page_styles')
<style>
    .pascoma-table {
        font-size: 14px;
    }
    .pascoma-table th {
        background-color: #E8F4F8;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    .pascoma-table td {
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    .attestation-badge {
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
    }
    .attestation-female {
        background-color: #fce4ec;
        color: #c2185b;
    }
    .attestation-male {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    .print-button {
        margin-bottom: 15px;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .pascoma-table {
            font-size: 12px;
        }
        .card-header {
            background-color: #E8F4F8 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
    .session-badge {
        background-color: #4caf50;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">
            <i class="icon-clipboard3 mr-2"></i>
            PASCOMA - Attestations d'Assurance
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="session-badge no-print">
            <i class="icon-calendar mr-1"></i>
            Année scolaire : {{ $current_session }}
        </div>
        
        <div class="mb-3 no-print">
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <i class="icon-info-circle mr-2"></i>
                        <strong>Total d'élèves inscrits :</strong> {{ $students->count() }}
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="icon-printer mr-2"></i> Imprimer
                    </button>
                    <a href="{{ route('pascoma.export') }}" class="btn btn-success">
                        <i class="icon-file-excel mr-2"></i> Exporter Excel
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered pascoma-table datatable-basic">
                <thead>
                    <tr>
                        <th style="width: 50px;">N°</th>
                        <th style="width: 250px;">Nom et Prénoms de l'élève</th>
                        <th style="width: 120px;">Date de naissance</th>
                        <th style="width: 100px;">Classe</th>
                        <th style="width: 150px;">N° Attestation d'Assurance</th>
                        <th style="width: 80px;">Sexe</th>
                        <th style="width: 120px;">Somme payée</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $student->user->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $student->user->dob ?? 'N/A' }}</td>
                        <td class="text-center">
                            @php
                                // Extraire uniquement le nom de la classe (sans section)
                                $class_full = $student->my_class->name ?? 'N/A';
                                $class_only = trim(explode(' ', $class_full)[0]);
                            @endphp
                            {{ $class_only }}
                        </td>
                        <td class="text-center">
                            <span class="attestation-badge {{ $student->user->gender === 'Female' ? 'attestation-female' : 'attestation-male' }}">
                                {{ $student->attestation_no }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($student->user->gender === 'Female')
                                <i class="icon-woman text-danger mr-1"></i> Féminin
                            @else
                                <i class="icon-user-tie text-primary mr-1"></i> Masculin
                            @endif
                        </td>
                        <td class="text-center">{{ $student->somme_payee }} Ar</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="icon-info3 mr-2"></i>
                            Aucun élève inscrit pour l'année scolaire {{ $current_session }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($students->count() > 0)
                <tfoot>
                    <tr class="bg-light font-weight-bold">
                        <td colspan="6" class="text-right">TOTAL</td>
                        <td class="text-center">{{ $students->count() * 200 }} Ar</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="mt-3 no-print">
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="icon-woman text-danger mr-2"></i>
                                Total Filles
                            </h6>
                            <h3 class="mb-0">{{ $students->where('user.gender', 'Female')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="icon-user-tie text-primary mr-2"></i>
                                Total Garçons
                            </h6>
                            <h3 class="mb-0">{{ $students->where('user.gender', 'Male')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with specific options
        $('.datatable-basic').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
            },
            pageLength: 50,
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tous"]],
            order: [], // Désactiver le tri par défaut pour respecter l'ordre du serveur (PHP)
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="icon-copy3 mr-2"></i> Copier',
                    className: 'btn btn-light no-print'
                },
                {
                    extend: 'excel',
                    text: '<i class="icon-file-excel mr-2"></i> Excel',
                    className: 'btn btn-success no-print',
                    title: 'PASCOMA_{{ $current_session }}',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="icon-file-pdf mr-2"></i> PDF',
                    className: 'btn btn-danger no-print',
                    title: 'PASCOMA - {{ $current_session }}',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="icon-printer mr-2"></i> Imprimer',
                    className: 'btn btn-primary no-print',
                    title: 'PASCOMA - Attestations d\'Assurance<br>Année scolaire: {{ $current_session }}',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ]
        });
    });
</script>
@endsection
