@extends('layouts.master')
@section('page_title', 'Gestion des Années Scolaires')
@section('content')

<div class="content-wrapper">
    <div class="content-header header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-calendar mr-2"></i> <span class="font-weight-semibold">Gestion des Années Scolaires</span></h4>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title">Liste des Années Scolaires</h6>
                        <div class="header-elements">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-session-modal">
                                <i class="icon-plus2 mr-2"></i> Nouvelle Année
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Année Scolaire</th>
                                        <th>Date de Début</th>
                                        <th>Date de Fin</th>
                                        <th>Statut</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                    <tr>
                                        <td>
                                            <span class="font-weight-semibold">{{ $session->name }}</span>
                                        </td>
                                        <td>{{ $session->start_date->format('d/m/Y') }}</td>
                                        <td>{{ $session->end_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($session->is_active)
                                                <span class="badge badge-success">Active par défaut</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                            
                                            @if($session->isCurrent())
                                                <span class="badge badge-info ml-1">En cours</span>
                                            @endif
                                        </td>
                                        <td>{{ $session->description }}</td>
                                        <td>
                                            <div class="list-icons">
                                                <div class="dropdown">
                                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                        <i class="icon-menu9"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if(!$session->is_active)
                                                        <form action="{{ route('sessions.set_active', $session->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="icon-checkmark3"></i> Définir comme active
                                                            </button>
                                                        </form>
                                                        @endif
                                                        
                                                        <a href="#" class="dropdown-item edit-session" 
                                                           data-id="{{ $session->id }}"
                                                           data-name="{{ $session->name }}"
                                                           data-start="{{ $session->start_date->format('Y-m-d') }}"
                                                           data-end="{{ $session->end_date->format('Y-m-d') }}"
                                                           data-description="{{ $session->description }}">
                                                            <i class="icon-pencil7"></i> Modifier
                                                        </a>
                                                        
                                                        @if(Qs::userIsSuperAdmin())
                                                        <form action="{{ route('sessions.destroy', $session->id) }}" method="POST" 
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette année scolaire ?')" 
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="icon-trash"></i> Supprimer
                                                            </button>
                                                        </form>
                                                        @endif
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Session -->
<div id="add-session-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Année Scolaire</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="{{ route('sessions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom de l'année scolaire <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: 2025-2026" required>
                        <small class="form-text text-muted">Format recommandé: YYYY-YYYY</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de début <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de fin <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Description optionnelle"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Session -->
<div id="edit-session-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Année Scolaire</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form id="edit-session-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom de l'année scolaire <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de début <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="edit-start" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de fin <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="edit-end" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gérer le clic sur modifier
    $('.edit-session').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var start = $(this).data('start');
        var end = $(this).data('end');
        var description = $(this).data('description');
        
        $('#edit-name').val(name);
        $('#edit-start').val(start);
        $('#edit-end').val(end);
        $('#edit-description').val(description);
        
        var action = '{{ route("sessions.update", ":id") }}';
        action = action.replace(':id', id);
        $('#edit-session-form').attr('action', action);
        
        $('#edit-session-modal').modal('show');
    });
});
</script>

@endsection
