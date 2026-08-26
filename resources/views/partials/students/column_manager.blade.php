{{-- Panneau moderne de gestion de visibilité des colonnes et barre d'outils d'exportation --}}
<div class="column-manager-card">
    <div class="column-manager-header">
        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            <h6 class="column-manager-title">
                <i class="icon-grid6"></i> Visibilité des colonnes
            </h6>
            <span id="visible-columns-count-badge" class="badge badge-info badge-pill font-size-sm font-weight-semibold">
                19 / 19 colonnes visibles
            </span>
            <button type="button" class="btn btn-sm btn-light border" data-toggle="collapse" data-target="#column-visibility-panel-body" aria-expanded="true">
                <i class="icon-sliders mr-1"></i> <span class="d-none d-sm-inline">Personnaliser les cases</span> <i class="icon-arrow-down22 ml-1"></i>
            </button>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            {{-- Bouton d'exportation Excel --}}
            <button type="button" class="btn btn-export-excel-custom" title="Exporter uniquement les colonnes actuellement cochées">
                <i class="icon-file-excel"></i>
                <span>Exporter en Excel (.xlsx)</span>
            </button>
        </div>
    </div>

    {{-- Corps pliable du sélecteur de colonnes --}}
    <div id="column-visibility-panel-body" class="collapse show">
        <div class="column-manager-body">
            {{-- Boutons d'actions rapides / Préréglages --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom" style="gap: 8px;">
                <div class="d-flex flex-wrap align-items-center" style="gap: 6px;">
                    <span class="text-muted font-size-xs font-weight-bold text-uppercase mr-1">
                        <i class="icon-magic-wand mr-1"></i>Vues rapides :
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-col-show-all">
                        <i class="icon-eye mr-1"></i> Tout cocher
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-col-preset-essential">
                        <i class="icon-star-full2 mr-1"></i> Essentielle
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info btn-col-preset-parents">
                        <i class="icon-users4 mr-1"></i> Parents / Tuteurs
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning btn-col-preset-academic">
                        <i class="icon-graduation2 mr-1"></i> Pédagogique
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-col-hide-all">
                        <i class="icon-eye-blocked mr-1"></i> Masquer optionnels
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-link text-muted btn-col-reset" title="Restaurer l'affichage initial">
                        <i class="icon-reset mr-1"></i> Réinitialiser
                    </button>
                </div>
            </div>

            {{-- Conteneur dynamique des cases à cocher --}}
            <div id="column-visibility-checklist">
                {{-- Rempli dynamiquement par StudentTableManager ou initialisé par défaut --}}
            </div>
        </div>
    </div>
</div>
