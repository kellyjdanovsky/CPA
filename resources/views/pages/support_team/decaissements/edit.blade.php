@extends('layouts.master')
@section('page_title', 'Modifier une Dépense')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Modifier une Dépense</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item">
                <a href="{{ route('financial_dashboard.expenses') }}" class="nav-link">
                    <i class="icon-stats-bars2 mr-2"></i> Tableau de Bord des Dépenses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('decaissements.index') }}" class="nav-link">
                    <i class="icon-list3 mr-2"></i> Liste des Dépenses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('decaissements.create') }}" class="nav-link">
                    <i class="icon-plus2 mr-2"></i> Ajouter une Dépense
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="icon-pencil mr-2"></i> Modifier une Dépense
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="edit-decaissement">
                <form method="post" action="{{ route('decaissements.update', $decaissement->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_paiement">Date de paiement <span class="text-danger">*</span></label>
                                <input type="date" name="date_paiement" id="date_paiement" class="form-control @error('date_paiement') is-invalid @enderror" value="{{ old('date_paiement', $decaissement->date_paiement->format('Y-m-d')) }}" required>
                                @error('date_paiement')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="montant">Montant <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant', $decaissement->montant) }}" step="0.01" min="0" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Ar</span>
                                    </div>
                                </div>
                                @error('montant')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="motif">Catégorie de dépense <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="motif" id="motif" class="form-control select @error('motif') is-invalid @enderror" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('motif', $decaissement->motif) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                        <option value="autre">Autre (spécifier)</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light" id="show-new-category">
                                            <i class="icon-plus2"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('motif')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group" id="new-category-group" style="display: none;">
                                <label for="new_category">Nouvelle catégorie</label>
                                <input type="text" id="new_category" class="form-control" placeholder="Entrez une nouvelle catégorie">
                                <button type="button" class="btn btn-sm btn-primary mt-1" id="add-new-category">Ajouter</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="beneficiaire">Bénéficiaire <span class="text-danger">*</span></label>
                                <input type="text" name="beneficiaire" id="beneficiaire" class="form-control @error('beneficiaire') is-invalid @enderror" value="{{ old('beneficiaire', $decaissement->beneficiaire) }}" required>
                                @error('beneficiaire')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="methode_paiement">Méthode de paiement <span class="text-danger">*</span></label>
                                <select name="methode_paiement" id="methode_paiement" class="form-control select @error('methode_paiement') is-invalid @enderror" required>
                                    @foreach($methodes_paiement as $key => $value)
                                        <option value="{{ $key }}" {{ old('methode_paiement', $decaissement->methode_paiement) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('methode_paiement')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference">Référence</label>
                                <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $decaissement->reference) }}" placeholder="N° de chèque, référence de transaction...">
                                @error('reference')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $decaissement->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="piece">Pièce justificative</label>
                                <input type="file" name="piece" id="piece" class="form-control @error('piece') is-invalid @enderror">
                                <small class="form-text text-muted">Formats acceptés: JPG, PNG, PDF. Taille max: 2Mo</small>
                                @if($decaissement->piece)
                                    <div class="mt-2">
                                        <span class="badge badge-info">Pièce actuelle: {{ $decaissement->piece }}</span>
                                        <a href="{{ route('decaissements.download_piece', $decaissement->id) }}" class="btn btn-sm btn-light ml-2">
                                            <i class="icon-file-download"></i> Télécharger
                                        </a>
                                    </div>
                                @endif
                                @error('piece')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="details_bancaires">Détails bancaires</label>
                                <textarea name="details_bancaires" id="details_bancaires" class="form-control @error('details_bancaires') is-invalid @enderror" rows="3">{{ old('details_bancaires', $decaissement->details_bancaires) }}</textarea>
                                @error('details_bancaires')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Statut</label>
                                <select name="status" id="status" class="form-control select @error('status') is-invalid @enderror">
                                    <option value="en_attente" {{ old('status', $decaissement->status) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="approuve" {{ old('status', $decaissement->status) == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                                    <option value="rejete" {{ old('status', $decaissement->status) == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Informations complémentaires</label>
                                <div class="form-control-plaintext">
                                    <div>Créé par: {{ $decaissement->user->name ?? 'Inconnu' }}</div>
                                    <div>Date de création: {{ $decaissement->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-floppy-disk mr-2"></i> Enregistrer les modifications
                        </button>
                        <a href="{{ route('decaissements.show', $decaissement->id) }}" class="btn btn-light ml-2">
                            <i class="icon-arrow-left8 mr-2"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Gestion de l'ajout d'une nouvelle catégorie
        $('#show-new-category').on('click', function() {
            $('#new-category-group').toggle();
        });
        
        $('#add-new-category').on('click', function() {
            var newCategory = $('#new_category').val().trim();
            if (newCategory) {
                // Ajouter la nouvelle catégorie à la liste déroulante
                $('#motif').append($('<option>', {
                    value: newCategory,
                    text: newCategory,
                    selected: true
                }));
                
                // Cacher le groupe de nouvelle catégorie
                $('#new-category-group').hide();
                $('#new_category').val('');
            }
        });
        
        // Afficher les champs pertinents en fonction de la méthode de paiement
        $('#methode_paiement').on('change', function() {
            var method = $(this).val();
            
            // Afficher/masquer les champs en fonction de la méthode
            if (method === 'cheque') {
                $('#reference').attr('placeholder', 'N° de chèque');
            } else if (method === 'virement') {
                $('#reference').attr('placeholder', 'Référence de virement');
            } else if (method === 'mobile_money') {
                $('#reference').attr('placeholder', 'N° de transaction');
            } else if (method === 'carte') {
                $('#reference').attr('placeholder', 'N° d\'autorisation');
            } else {
                $('#reference').attr('placeholder', 'Référence de transaction');
            }
        });
        
        // Déclencher le changement pour initialiser les champs
        $('#methode_paiement').trigger('change');
    });
</script>
@endsection
