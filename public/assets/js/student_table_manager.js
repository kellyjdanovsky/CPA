/**
 * StudentTableManager - Gestionnaire moderne des tables d'étudiants
 * CPA - Gestion Scolaire
 *
 * Fonctionnalités:
 * 1. Visibilité dynamique des colonnes par cases à cocher en temps réel
 * 2. Préréglages rapides (Tout afficher, Vue essentielle, Vue parents, etc.)
 * 3. Sauvegarde automatique des préférences dans localStorage
 * 4. Exportation Excel (.xlsx) stricte des colonnes cochées/visibles avec mise en forme
 * 5. Intégration transparente avec DataTables et l'édition en ligne
 */

(function(window, $) {
    'use strict';

    class StudentTableManager {
        constructor(options = {}) {
            this.options = $.extend({
                tableSelector: '.datatable-button-html5-columns',
                storageKeyPrefix: 'cpa_student_table_cols_',
                excelButtonSelector: '.btn-export-excel-custom',
                colvisContainerSelector: '#column-visibility-checklist',
                colvisBadgeSelector: '#visible-columns-count-badge',
                activeTabSelector: '.nav-tabs .nav-link.active'
            }, options);

            this.tables = [];
            this.activeTable = null;
            this.columnsConfig = [
                { index: 0, key: 'num', label: 'N°', default: true, essential: true, noExport: false, icon: 'icon-hash' },
                { index: 1, key: 'photo', label: 'Photo', default: true, essential: false, noExport: true, icon: 'icon-user' },
                { index: 2, key: 'name', label: 'Nom & Prénom', default: true, essential: true, noExport: false, icon: 'icon-user-check' },
                { index: 3, key: 'adm_no', label: 'N° d\'admission', default: true, essential: true, noExport: false, icon: 'icon-vcard' },
                { index: 4, key: 'class_section', label: 'Classe & Section', default: true, essential: true, noExport: false, icon: 'icon-graduation2' },
                { index: 5, key: 'dob', label: 'Date de naissance', default: true, essential: false, noExport: false, icon: 'icon-calendar3' },
                { index: 6, key: 'age', label: 'Âge', default: true, essential: false, noExport: false, icon: 'icon-hour-glass' },
                { index: 7, key: 'address', label: 'Adresse', default: true, essential: false, noExport: false, icon: 'icon-location4' },
                { index: 8, key: 'religion', label: 'Religion', default: true, essential: false, noExport: false, icon: 'icon-bookmark' },
                { index: 9, key: 'status', label: 'Statut (ADRA/Normal)', default: true, essential: true, noExport: false, icon: 'icon-certificate' },
                { index: 10, key: 'student_type', label: 'Type (Nouveau/Ancien)', default: true, essential: false, noExport: false, icon: 'icon-book' },
                { index: 11, key: 'academic_status', label: 'Statut académique', default: true, essential: false, noExport: false, icon: 'icon-trophy' },
                { index: 12, key: 'gender', label: 'Sexe', default: true, essential: true, noExport: false, icon: 'icon-users' },
                { index: 13, key: 'nom_p', label: 'Père / Tuteur', default: true, essential: false, noExport: false, icon: 'icon-user-tie' },
                { index: 14, key: 'prof_p', label: 'Profession père', default: true, essential: false, noExport: false, icon: 'icon-briefcase' },
                { index: 15, key: 'nom_m', label: 'Mère / Tutrice', default: true, essential: false, noExport: false, icon: 'icon-woman' },
                { index: 16, key: 'prof_m', label: 'Profession mère', default: true, essential: false, noExport: false, icon: 'icon-briefcase' },
                { index: 17, key: 'phone', label: 'Téléphone', default: true, essential: true, noExport: false, icon: 'icon-phone2' },
                { index: 18, key: 'action', label: 'Actions', default: true, essential: true, noExport: true, icon: 'icon-gear' }
            ];

            this.init();
        }

        init() {
            const self = this;

            // Attendre le chargement complet du DOM et de DataTables
            $(document).ready(function() {
                self.initDataTables();
                self.renderCheckboxes();
                self.bindEvents();
                self.loadSavedVisibility();
                self.updateCountBadge();
            });
        }

        /**
         * Initialise les tables DataTables
         */
        initDataTables() {
            const self = this;
            const $tables = $(this.options.tableSelector);

            $tables.each(function(index, el) {
                let dt;
                if ($.fn.DataTable.isDataTable(el)) {
                    dt = $(el).DataTable();
                } else {
                    dt = $(el).DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                        language: {
                            search: '<span><i class="icon-search4 mr-1"></i>Rechercher :</span> _INPUT_',
                            searchPlaceholder: 'Filtrer les élèves...',
                            lengthMenu: '<span>Afficher :</span> _MENU_',
                            paginate: { 'first': 'Premier', 'last': 'Dernier', 'next': 'Suivant &rarr;', 'previous': '&larr; Précédent' },
                            emptyTable: 'Aucun élève trouvé',
                            info: 'Affichage de _START_ à _END_ sur _TOTAL_ élèves',
                            infoEmpty: '0 élève trouvé',
                            infoFiltered: '(filtré sur _MAX_ élèves au total)',
                            zeroRecords: 'Aucun enregistrement correspondant trouvé'
                        },
                        dom: '<"datatable-header d-flex flex-wrap justify-content-between align-items-center mb-2"<"datatable-search-wrapper"f><"datatable-length-wrapper"l>><"datatable-scroll"t><"datatable-footer d-flex flex-wrap justify-content-between align-items-center mt-2"ip>',
                        columnDefs: [
                            { targets: 'no-sort', orderable: false },
                            { targets: 'no-export', className: 'no-export' }
                        ],
                        order: [[2, 'asc']], // Tri par nom par défaut
                        autoWidth: false,
                        responsive: false
                    });
                }
                self.tables.push(dt);
            });

            if (this.tables.length > 0) {
                this.activeTable = this.tables[0];
            }
        }

        /**
         * Génère les cases à cocher pour chaque colonne dans le panneau de contrôle
         */
        renderCheckboxes() {
            const $container = $(this.options.colvisContainerSelector);
            if (!$container.length) return;

            $container.empty();

            // Obtenir le nombre réel de colonnes de la première table
            let tableColCount = 19;
            if (this.activeTable) {
                tableColCount = this.activeTable.columns().count();
            }

            let html = '<div class="row g-2">';

            this.columnsConfig.forEach((col, idx) => {
                if (idx >= tableColCount) return;

                const isChecked = col.default ? 'checked' : '';

                html += `
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-2">
                        <div class="custom-control custom-checkbox custom-checkbox-modern p-2 rounded border bg-white h-100 d-flex align-items-center">
                            <input type="checkbox" class="custom-control-input col-toggle-checkbox" 
                                   id="col-toggle-${col.index}" 
                                   data-col-index="${col.index}" 
                                   data-col-key="${col.key}" 
                                   ${isChecked}>
                            <label class="custom-control-label d-flex align-items-center cursor-pointer mb-0 w-100 font-weight-500 font-size-sm" for="col-toggle-${col.index}">
                                <i class="${col.icon} mr-1 text-primary"></i>
                                <span class="text-truncate">${col.label}</span>
                            </label>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            $container.html(html);
        }

        /**
         * Attache tous les écouteurs d'événements
         */
        bindEvents() {
            const self = this;

            // Écouteur sur changement de case à cocher de colonne
            $(document).on('change', '.col-toggle-checkbox', function() {
                const colIndex = parseInt($(this).data('col-index'), 10);
                const isVisible = $(this).is(':checked');
                self.setColumnVisibility(colIndex, isVisible);
                self.saveVisibilityState();
                self.updateCountBadge();
            });

            // Bouton "Tout cocher" (Tout afficher)
            $(document).on('click', '.btn-col-show-all', function(e) {
                e.preventDefault();
                self.showAllColumns();
            });

            // Bouton "Tout décocher" (Masquer sauf essentiels)
            $(document).on('click', '.btn-col-hide-all', function(e) {
                e.preventDefault();
                self.hideAllColumns();
            });

            // Bouton "Vue Essentielle"
            $(document).on('click', '.btn-col-preset-essential', function(e) {
                e.preventDefault();
                self.applyPreset('essential');
            });

            // Bouton "Vue Parents / Contact"
            $(document).on('click', '.btn-col-preset-parents', function(e) {
                e.preventDefault();
                self.applyPreset('parents');
            });

            // Bouton "Vue Académique"
            $(document).on('click', '.btn-col-preset-academic', function(e) {
                e.preventDefault();
                self.applyPreset('academic');
            });

            // Bouton "Réinitialiser"
            $(document).on('click', '.btn-col-reset', function(e) {
                e.preventDefault();
                self.resetToDefaults();
            });

            // Bouton Exportation Excel
            $(document).on('click', self.options.excelButtonSelector, function(e) {
                e.preventDefault();
                self.exportToExcel();
            });

            // Changement d'onglet
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                const targetTab = $($(e.target).attr('href'));
                const $tabTable = targetTab.find(self.options.tableSelector);
                if ($tabTable.length && $.fn.DataTable.isDataTable($tabTable[0])) {
                    self.activeTable = $tabTable.DataTable();
                    self.activeTable.columns.adjust().draw(false);
                    self.syncCheckboxesWithActiveTable();
                }
            });
        }

        /**
         * Modifie la visibilité d'une colonne sur toutes les instances DataTables
         */
        setColumnVisibility(colIndex, isVisible) {
            this.tables.forEach(dt => {
                try {
                    const col = dt.column(colIndex);
                    if (col) {
                        col.visible(isVisible, false); // false pour éviter redraw immédiat
                    }
                } catch (e) {
                    console.warn(`Impossible de modifier la colonne ${colIndex}:`, e);
                }
            });

            // Redraw les tables actives
            if (this.activeTable) {
                this.activeTable.columns.adjust().draw(false);
            }
        }

        /**
         * Affiche toutes les colonnes
         */
        showAllColumns() {
            $('.col-toggle-checkbox').prop('checked', true);
            this.tables.forEach(dt => {
                dt.columns().visible(true, false);
                dt.columns.adjust().draw(false);
            });
            this.saveVisibilityState();
            this.updateCountBadge();
            this.showNotification('Toutes les colonnes sont maintenant affichées.', 'success');
        }

        /**
         * Masque toutes les colonnes sauf les colonnes vitales (Nom & Prénom, N°, Actions)
         */
        hideAllColumns() {
            $('.col-toggle-checkbox').each(function() {
                const colIdx = parseInt($(this).data('col-index'), 10);
                // Garder N° (0), Nom (2) et Action (18)
                const keep = (colIdx === 0 || colIdx === 2 || colIdx === 18);
                $(this).prop('checked', keep);
            });

            this.tables.forEach(dt => {
                dt.columns().visible(false, false);
                dt.column(0).visible(true, false);
                dt.column(2).visible(true, false);
                if (dt.columns().count() > 18) {
                    dt.column(18).visible(true, false);
                }
                dt.columns.adjust().draw(false);
            });

            this.saveVisibilityState();
            this.updateCountBadge();
            this.showNotification('Seules les colonnes vitales sont affichées.', 'info');
        }

        /**
         * Applique un préréglage de colonnes
         */
        applyPreset(presetName) {
            let activeIndexes = [];

            switch (presetName) {
                case 'essential':
                    // N°, Nom, N° adm, Classe, Sexe, Statut, Téléphone, Action
                    activeIndexes = [0, 2, 3, 4, 9, 12, 17, 18];
                    break;
                case 'parents':
                    // N°, Nom, Classe, Sexe, Père, Prof Père, Mère, Prof Mère, Téléphone, Adresse, Action
                    activeIndexes = [0, 2, 4, 7, 12, 13, 14, 15, 16, 17, 18];
                    break;
                case 'academic':
                    // N°, Nom, N° adm, Classe, DOB, Âge, Statut, Type, Statut académique, Sexe, Action
                    activeIndexes = [0, 2, 3, 4, 5, 6, 9, 10, 11, 12, 18];
                    break;
                default:
                    return;
            }

            $('.col-toggle-checkbox').each(function() {
                const colIdx = parseInt($(this).data('col-index'), 10);
                const isChecked = activeIndexes.includes(colIdx);
                $(this).prop('checked', isChecked);
            });

            this.tables.forEach(dt => {
                const count = dt.columns().count();
                for (let i = 0; i < count; i++) {
                    const shouldShow = activeIndexes.includes(i);
                    dt.column(i).visible(shouldShow, false);
                }
                dt.columns.adjust().draw(false);
            });

            this.saveVisibilityState();
            this.updateCountBadge();
            this.showNotification(`Préréglage "${presetName.toUpperCase()}" appliqué avec succès.`, 'success');
        }

        /**
         * Réinitialise à la configuration par défaut (toutes cochées)
         */
        resetToDefaults() {
            localStorage.removeItem(this.getStorageKey());
            $('.col-toggle-checkbox').each((idx, el) => {
                const colIdx = parseInt($(el).data('col-index'), 10);
                const cfg = this.columnsConfig[colIdx];
                $(el).prop('checked', cfg ? cfg.default : true);
            });

            this.tables.forEach(dt => {
                dt.columns().visible(true, false);
                dt.columns.adjust().draw(false);
            });

            this.updateCountBadge();
            this.showNotification('Visibilité des colonnes réinitialisée par défaut.', 'success');
        }

        /**
         * Met à jour le badge indiquant le nombre de colonnes visibles
         */
        updateCountBadge() {
            const $badge = $(this.options.colvisBadgeSelector);
            if (!$badge.length) return;

            const total = $('.col-toggle-checkbox').length;
            const checked = $('.col-toggle-checkbox:checked').length;

            $badge.text(`${checked} / ${total} colonnes visibles`);
            if (checked === total) {
                $badge.removeClass('badge-warning badge-secondary').addClass('badge-success');
            } else if (checked < 5) {
                $badge.removeClass('badge-success badge-secondary').addClass('badge-warning');
            } else {
                $badge.removeClass('badge-success badge-warning').addClass('badge-info');
            }
        }

        /**
         * Clé de stockage LocalStorage
         */
        getStorageKey() {
            return this.options.storageKeyPrefix + window.location.pathname.replace(/[^a-zA-Z0-9]/g, '_');
        }

        /**
         * Sauvegarde l'état des cases à cocher dans localStorage
         */
        saveVisibilityState() {
            const state = {};
            $('.col-toggle-checkbox').each(function() {
                const colIdx = $(this).data('col-index');
                state[colIdx] = $(this).is(':checked');
            });
            try {
                localStorage.setItem(this.getStorageKey(), JSON.stringify(state));
            } catch (e) {
                console.warn('LocalStorage non accessible:', e);
            }
        }

        /**
         * Charge l'état sauvegardé depuis localStorage
         */
        loadSavedVisibility() {
            try {
                const saved = localStorage.getItem(this.getStorageKey());
                if (!saved) return;

                const state = JSON.parse(saved);
                $('.col-toggle-checkbox').each((idx, el) => {
                    const colIdx = $(el).data('col-index');
                    if (state.hasOwnProperty(colIdx)) {
                        const isVisible = state[colIdx];
                        $(el).prop('checked', isVisible);
                        this.setColumnVisibility(colIdx, isVisible);
                    }
                });

                if (this.activeTable) {
                    this.activeTable.columns.adjust().draw(false);
                }
            } catch (e) {
                console.warn('Erreur lors du chargement des colonnes sauvegardées:', e);
            }
        }

        /**
         * Synchronise les cases à cocher avec la table actuellement active
         */
        syncCheckboxesWithActiveTable() {
            if (!this.activeTable) return;

            this.activeTable.columns().every((idx) => {
                const col = this.activeTable.column(idx);
                $(`#col-toggle-${idx}`).prop('checked', col.visible());
            });

            this.updateCountBadge();
        }

        /**
         * EXPORTATION EXCEL STRICTEMENT DES COLONNES COCHÉES / VISIBLES
         * Utilise SheetJS (XLSX) pour créer un fichier .xlsx professionnel
         */
        exportToExcel() {
            const self = this;

            // Déterminer la table active
            let dt = this.activeTable;
            if (!dt && this.tables.length > 0) {
                dt = this.tables[0];
            }

            if (!dt) {
                this.showNotification('Tableau introuvable pour l\'exportation.', 'error');
                return;
            }

            // Vérifier que la bibliothèque XLSX (SheetJS) est disponible
            if (typeof XLSX === 'undefined') {
                this.showNotification('Génération du fichier Excel en cours...', 'info');
                if (dt.button && dt.button('.buttons-excel').length) {
                    dt.button('.buttons-excel').trigger();
                } else {
                    alert('La bibliothèque SheetJS est en cours de chargement.');
                }
                return;
            }

            // Identifier les colonnes visibles à exporter (en excluant les colonnes .no-export, photo et actions)
            const exportCols = [];
            const headerTitles = [];

            dt.columns().every(function(idx) {
                const col = dt.column(idx);
                const headerNode = $(col.header());
                const isVisible = col.visible();
                const isNoExport = headerNode.hasClass('no-export') || 
                                   headerNode.text().toLowerCase().includes('action') || 
                                   headerNode.text().toLowerCase().includes('photo');

                if (isVisible && !isNoExport) {
                    let title = headerNode.text().trim();
                    title = title.replace(/\s+/g, ' ');
                    exportCols.push(idx);
                    headerTitles.push(title || `Colonne ${idx + 1}`);
                }
            });

            if (exportCols.length === 0) {
                this.showNotification('Aucune colonne exportable n\'est actuellement cochée/visible.', 'warning');
                return;
            }

            // Extraire les lignes du tableau (respecter les filtres de recherche actuels)
            const rowsData = [];
            rowsData.push(headerTitles);

            // Obtenir les lignes filtrées ou toutes les lignes
            const dataTableRows = dt.rows({ search: 'applied' }).nodes();

            $(dataTableRows).each(function(rowIdx, tr) {
                const rowCells = [];
                exportCols.forEach(colIdx => {
                    let cellVal = '';
                    try {
                        const dtCell = dt.cell(tr, colIdx);
                        if (dtCell && dtCell.node()) {
                            const $node = $(dtCell.node()).clone();
                            $node.find('.hidden, .d-none, script, style, .save-indicator, img').remove();
                            cellVal = $node.text().trim();
                        } else {
                            const cell = $(tr).find('td').eq(colIdx);
                            cellVal = cell.text().trim();
                        }
                    } catch (e) {
                        const cell = $(tr).find('td').eq(colIdx);
                        cellVal = cell.text().trim();
                    }

                    cellVal = cellVal.replace(/\s+/g, ' ').trim();
                    rowCells.push(cellVal);
                });
                rowsData.push(rowCells);
            });

            if (rowsData.length <= 1) {
                this.showNotification('Aucune donnée d\'élève à exporter avec les filtres actuels.', 'warning');
                return;
            }

            try {
                // Création du classeur Excel avec SheetJS
                const ws = XLSX.utils.aoa_to_sheet(rowsData);

                // Définir des largeurs de colonnes automatiques adaptées
                const colWidths = headerTitles.map((header, colIndex) => {
                    let maxLength = header.length;
                    for (let r = 1; r < rowsData.length; r++) {
                        const cellText = String(rowsData[r][colIndex] || '');
                        if (cellText.length > maxLength) {
                            maxLength = cellText.length;
                        }
                    }
                    return { wch: Math.min(Math.max(maxLength + 3, 10), 40) };
                });
                ws['!cols'] = colWidths;

                const wb = XLSX.utils.book_new();
                
                // Déterminer le nom de la feuille et du fichier
                const activeTabLink = $(self.options.activeTabSelector);
                let tabName = activeTabLink.text().trim() || 'Liste_Eleves';
                tabName = tabName.replace(/[^a-zA-Z0-9_\-]/g, '_').substring(0, 30);

                XLSX.utils.book_append_sheet(wb, ws, tabName || 'Eleves');

                // Génération du nom de fichier
                const today = new Date().toISOString().slice(0, 10);
                const fileName = `Liste_Eleves_Export_${today}.xlsx`;

                // Téléchargement immédiat
                XLSX.writeFile(wb, fileName);

                this.showNotification(`Export Excel réussi (${rowsData.length - 1} élèves, ${exportCols.length} colonnes cochées).`, 'success');
            } catch (err) {
                console.error('Erreur lors de l\'exportation Excel:', err);
                this.showNotification('Erreur lors de la génération du fichier Excel.', 'error');
            }
        }

        /**
         * Notification utilisateur
         */
        showNotification(message, type = 'info') {
            if (typeof PNotify !== 'undefined') {
                new PNotify({
                    text: message,
                    type: type,
                    delay: 3500,
                    styling: 'bootstrap3'
                });
            } else if (typeof swal !== 'undefined' && type === 'error') {
                swal('Information', message, type);
            } else {
                console.log(`[Notification ${type}]: ${message}`);
            }
        }
    }

    // Exposer l'instance globalement
    window.StudentTableManager = StudentTableManager;

    // Initialisation automatique
    $(document).ready(function() {
        if ($('.datatable-button-html5-columns').length || $('#column-visibility-checklist').length) {
            window.studentTableManager = new StudentTableManager();
        }
    });

})(window, jQuery);
