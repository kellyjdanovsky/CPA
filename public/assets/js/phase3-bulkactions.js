/**
 * Phase 3 - Bulk Actions & Advanced Exports
 * Gestion des actions en masse et exports personnalisés
 */

class BulkActionsManager {
    constructor(tableSelector, options = {}) {
        this.table = document.querySelector(tableSelector);
        if (!this.table) return;

        this.options = {
            selectAllCheckbox: '.bulk-select-all',
            itemCheckbox: '.bulk-select-item',
            bulkActionsBar: '.modern-bulk-actions',
            actionsContainer: '.modern-bulk-actions-buttons',
            ...options
        };

        this.selectedItems = new Set();
        this.init();
    }

    init() {
        this.bindEvents();
        this.createBulkActionsBar();
    }

    bindEvents() {
        // Select all checkbox
        const selectAll = this.table.querySelector(this.options.selectAllCheckbox);
        if (selectAll) {
            selectAll.addEventListener('change', (e) => this.handleSelectAll(e.target.checked));
        }

        // Individual checkboxes
        this.table.querySelectorAll(this.options.itemCheckbox).forEach(checkbox => {
            checkbox.addEventListener('change', (e) => this.handleItemSelect(e.target));
        });
    }

    createBulkActionsBar() {
        if (document.querySelector(this.options.bulkActionsBar)) return;

        const bar = document.createElement('div');
        bar.className = 'modern-bulk-actions';
        bar.innerHTML = `
            <div class="modern-bulk-actions-text">
                <span class="selected-count">0</span> élément(s) sélectionné(s)
            </div>
            <div class="modern-bulk-actions-buttons">
                <button class="modern-btn modern-btn-sm modern-btn-outline" onclick="window.bulkActions.exportSelected()">
                    <i class="icon-download"></i> Exporter
                </button>
                <button class="modern-btn modern-btn-sm modern-btn-danger" onclick="window.bulkActions.deleteSelected()">
                    <i class="icon-trash"></i> Supprimer
                </button>
                <button class="modern-btn modern-btn-sm modern-btn-secondary" onclick="window.bulkActions.clearSelection()">
                    <i class="icon-x"></i> Annuler
                </button>
            </div>
        `;

        this.table.parentElement.insertBefore(bar, this.table.parentElement.firstChild);
        this.bulkActionsBar = bar;
    }

    handleSelectAll(checked) {
        this.table.querySelectorAll(this.options.itemCheckbox).forEach(checkbox => {
            checkbox.checked = checked;
            this.handleItemSelect(checkbox, true);
        });
        this.updateBulkActionsBar();
    }

    handleItemSelect(checkbox, skipUpdate = false) {
        const itemId = checkbox.value || checkbox.dataset.id;

        if (checkbox.checked) {
            this.selectedItems.add(itemId);
        } else {
            this.selectedItems.delete(itemId);
        }

        if (!skipUpdate) {
            this.updateBulkActionsBar();
        }
    }

    updateBulkActionsBar() {
        const count = this.selectedItems.size;

        if (count > 0) {
            this.bulkActionsBar.classList.add('active');
            this.bulkActionsBar.querySelector('.selected-count').textContent = count;
        } else {
            this.bulkActionsBar.classList.remove('active');
        }

        // Update select all checkbox
        const selectAll = this.table.querySelector(this.options.selectAllCheckbox);
        const allItems = this.table.querySelectorAll(this.options.itemCheckbox);
        if (selectAll && allItems.length > 0) {
            selectAll.checked = this.selectedItems.size === allItems.length;
            selectAll.indeterminate = this.selectedItems.size > 0 && this.selectedItems.size < allItems.length;
        }
    }

    getSelectedIds() {
        return Array.from(this.selectedItems);
    }

    clearSelection() {
        this.selectedItems.clear();
        this.table.querySelectorAll(this.options.itemCheckbox).forEach(checkbox => {
            checkbox.checked = false;
        });
        this.updateBulkActionsBar();
    }

    async exportSelected(format = 'excel') {
        const ids = this.getSelectedIds();

        if (ids.length === 0) {
            if (window.notify) {
                notify.warning('Aucun élément sélectionné');
            }
            return;
        }

        if (window.notify) {
            notify.info(`Export de ${ids.length} élément(s) en cours...`);
        }

        // Créer et soumettre le formulaire
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.options.exportUrl || '/export';

        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // IDs
        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = JSON.stringify(ids);
        form.appendChild(idsInput);

        // Format
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    async deleteSelected() {
        const ids = this.getSelectedIds();

        if (ids.length === 0) {
            if (window.notify) {
                notify.warning('Aucun élément sélectionné');
            }
            return;
        }

        // Confirmation
        const confirmed = await this.confirmAction(
            `Supprimer ${ids.length} élément(s) ?`,
            'Cette action est irréversible'
        );

        if (!confirmed) return;

        try {
            const response = await fetch(this.options.deleteUrl || '/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ ids })
            });

            const data = await response.json();

            if (data.success) {
                if (window.notify) {
                    notify.success(`${ids.length} élément(s) supprimé(s)`);
                }
                // Recharger ou supprimer les lignes
                this.removeSelectedRows();
                this.clearSelection();
            } else {
                throw new Error(data.message || 'Erreur lors de la suppression');
            }
        } catch (error) {
            if (window.notify) {
                notify.error(error.message);
            }
        }
    }

    removeSelectedRows() {
        this.selectedItems.forEach(id => {
            const checkbox = this.table.querySelector(`${this.options.itemCheckbox}[value="${id}"]`);
            if (checkbox) {
                const row = checkbox.closest('tr');
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }
            }
        });
    }

    async confirmAction(title, message) {
        // Utiliser une modale moderne si disponible
        return new Promise((resolve) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: #6b7280'
                }).then((result) => {
                        resolve(result.isConfirmed);
                    });
            } else {
                resolve(confirm(`${title}\n${message}`));
            }
        });
    }

    // Actions personnalisées
    async performCustomAction(action, url) {
        const ids = this.getSelectedIds();

        if (ids.length === 0) {
            if (window.notify) {
                notify.warning('Aucun élément sélectionné');
            }
            return;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ ids, action })
            });

            const data = await response.json();

            if (data.success) {
                if (window.notify) {
                    notify.success(data.message || 'Action effectuée avec succès');
                }
                this.clearSelection();
                // Recharger si nécessaire
                if (data.reload) {
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Erreur lors de l\'action');
            }
        } catch (error) {
            if (window.notify) {
                notify.error(error.message);
            }
        }
    }
}

/**
 * Advanced Export Manager
 */
class AdvancedExportManager {
    constructor(options = {}) {
        this.options = {
            exportUrl: '/export',
            formats: ['excel', 'pdf', 'csv'],
            ...options
        };
    }

    /**
     * Exporter des données avec options personnalisées
     */
    async export(data, format = 'excel', options = {}) {
        const exportOptions = {
            filename: `export_${new Date().toISOString().slice(0, 10)}`,
            columns: 'all',
            filters: {},
            ...options
        };

        if (window.notify) {
            notify.info('Génération de l\'export en cours...');
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.options.exportUrl;

        // CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            this.addFormInput(form, '_token', csrfToken);
        }

        // Data
        this.addFormInput(form, 'data', JSON.stringify(data));
        this.addFormInput(form, 'format', format);
        this.addFormInput(form, 'options', JSON.stringify(exportOptions));

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    addFormInput(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    /**
     * Ouvrir le modal d'export personnalisé
     */
    openExportModal(data) {
        // Créer une modale avec options d'export
        const modal = document.createElement('div');
        modal.className = 'modern-modal active';
        modal.innerHTML = `
            <div class="modern-modal-content">
                <div class="modern-modal-header">
                    <h3 class="modern-modal-title">Exporter les données</h3>
                    <button class="modal-close" onclick="this.closest('.modern-modal').remove()">
                        <i class="icon-x"></i>
                    </button>
                </div>
                <div class="modern-modal-body">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Format</label>
                        <select class="modern-input modern-select" id="export-format">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="modern-form-group">
                        <label class="modern-form-label">Nom du fichier</label>
                        <input type="text" class="modern-input" id="export-filename" 
                               value="export_${new Date().toISOString().slice(0, 10)}">
                    </div>
                </div>
                <div class="modern-modal-footer">
                    <button class="modern-btn modern-btn-secondary" onclick="this.closest('.modern-modal').remove()">
                        Annuler
                    </button>
                    <button class="modern-btn modern-btn-primary" onclick="window.exportManager.executeExport()">
                        <i class="icon-download"></i> Exporter
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.currentExportData = data;
    }

    executeExport() {
        const format = document.getElementById('export-format').value;
        const filename = document.getElementById('export-filename').value;

        this.export(this.currentExportData, format, { filename });

        // Fermer la modale
        document.querySelector('.modern-modal').remove();
    }
}

// Initialisation globale
window.BulkActionsManager = BulkActionsManager;
window.AdvancedExportManager = AdvancedExportManager;
window.exportManager = new AdvancedExportManager();
