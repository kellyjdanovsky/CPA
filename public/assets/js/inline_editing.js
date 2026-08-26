/**
 * Système d'édition en ligne pour les tableaux DataTables
 * Optimisé pour les listes d'élèves
 */

class InlineEditor {
    constructor(options = {}) {
        this.options = {
            saveUrl: '/ajax/update_student_field',
            addUrl: '/students/store',
            csrfToken: $('meta[name="csrf-token"]').attr('content'),
            ...options
        };

        this.currentEditingCell = null;
        this.dataTable = null;
        this.init();
    }

    init() {
        this.initializeDataTable();
        this.bindEvents();
        this.calculateAllAges();
    }

    initializeDataTable() {
        // Configuration DataTable optimisée avec support responsive
        const tableConfig = {
            processing: true,
            stateSave: true,
            stateDuration: 60 * 60 * 24, // 24 heures
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            responsive: {
                details: {
                    type: 'column',
                    target: 0,
                    renderer: function (api, rowIdx, columns) {
                        let data = $.map(columns, function (col, i) {
                            return col.hidden ?
                                '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                '<td class="font-weight-bold">' + col.title + ':</td> ' +
                                '<td>' + col.data + '</td>' +
                                '</tr>' :
                                '';
                        }).join('');
                        return data ? $('<table class="table table-sm mb-0"/>').append(data) : false;
                    }
                }
            },
            autoWidth: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
                search: '<span>Filtrer:</span> _INPUT_',
                searchPlaceholder: 'Rechercher...',
                lengthMenu: '<span>Afficher:</span> _MENU_',
                paginate: {
                    first: 'Premier',
                    last: 'Dernier',
                    next: 'Suivant',
                    previous: 'Précédent'
                },
                emptyTable: "Aucune donnée disponible",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
                infoFiltered: "(filtré de _MAX_ entrées au total)",
                zeroRecords: "Aucun résultat trouvé"
            },
            buttons: [
                {
                    extend: 'colvis',
                    className: 'btn btn-primary dropdown-toggle',
                    text: '<i class="icon-eye mr-2"></i>Visibilité colonnes',
                    columns: ':not(.no-vis)',
                    collectionLayout: 'fixed columns',
                    collectionTitle: 'Choisir les colonnes à afficher',
                    postfixButtons: [
                        {
                            extend: 'colvisRestore',
                            text: '<i class="icon-reset mr-2"></i>Réinitialiser'
                        },
                        {
                            extend: 'colvisGroup',
                            text: '<i class="icon-eye mr-2"></i>Tout afficher',
                            show: ':hidden'
                        },
                        {
                            extend: 'colvisGroup',
                            text: '<i class="icon-eye-blocked mr-2"></i>Masquer optionnels',
                            hide: [7, 8, 9, 10, 11, 14, 15, 16, 17] // Colonnes optionnelles
                        }
                    ]
                },
                {
                    extend: 'copyHtml5',
                    className: 'btn btn-light',
                    text: '<i class="icon-copy mr-2"></i>Copier',
                    exportOptions: {
                        columns: ':visible:not(.no-export)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-success',
                    text: '<i class="icon-file-excel mr-2"></i>Excel',
                    exportOptions: {
                        columns: ':visible:not(.no-export)'
                    },
                    title: 'Export_Eleves_' + new Date().toISOString().slice(0, 10)
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-danger',
                    text: '<i class="icon-file-pdf mr-2"></i>PDF',
                    exportOptions: {
                        columns: ':visible:not(.no-export)'
                    },
                    title: 'Export_Eleves_' + new Date().toISOString().slice(0, 10),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.fillColor = '#3498db';
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-info',
                    text: '<i class="icon-printer mr-2"></i>Imprimer',
                    exportOptions: {
                        columns: ':visible:not(.no-export)'
                    }
                },
                {
                    text: '<i class="icon-plus mr-2"></i>Ajouter',
                    className: 'btn btn-success',
                    action: () => this.showAddStudentModal()
                }
            ],
            dom: '<"datatable-header d-flex flex-wrap justify-content-between align-items-center"<"datatable-search"f><"datatable-buttons"B><"datatable-length"l>><"datatable-scroll"t><"datatable-footer d-flex justify-content-between"ip>',
            columnDefs: [
                {
                    targets: 0,
                    className: 'dtr-control'
                },
                {
                    targets: 'no-sort',
                    orderable: false
                },
                {
                    targets: 'editable',
                    className: 'editable-cell'
                },
                {
                    targets: 'no-export',
                    className: 'no-export'
                },
                {
                    targets: 'no-vis',
                    className: 'no-vis'
                },
                // Priorités responsive - les colonnes importantes restent visibles
                { responsivePriority: 1, targets: [0, 2, 18] }, // N°, Nom, Action
                { responsivePriority: 2, targets: [3, 4] }, // Admission, Classe
                { responsivePriority: 3, targets: [5, 6, 9] }, // DOB, Age, Statut
                { responsivePriority: 4, targets: [12] }, // Sexe
                { responsivePriority: 5, targets: [10, 11] }, // Type, Statut académique
                { responsivePriority: 10, targets: '_all' } // Autres colonnes en dernier
            ],
            order: [[2, 'asc']], // Trier par nom par défaut
            drawCallback: () => {
                this.bindEditableEvents();
                this.calculateAllAges();
                this.updateColumnVisibilityIndicator();
                this.initActionButtons();
            },
            stateSaveCallback: function (settings, data) {
                localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data));
            },
            stateLoadCallback: function (settings) {
                try {
                    return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance));
                } catch (e) {
                    return null;
                }
            }
        };

        // Initialiser DataTable
        if ($.fn.DataTable.isDataTable('.datatable-button-html5-columns')) {
            $('.datatable-button-html5-columns').DataTable().destroy();
        }

        this.dataTable = $('.datatable-button-html5-columns').DataTable(tableConfig);
    }

    initActionButtons() {
        // Initialiser les tooltips sur les boutons d'action
        $('[data-toggle="tooltip"]').tooltip();

        // Animation pour les boutons d'action
        $('.action-btn').hover(
            function () { $(this).addClass('shadow-sm'); },
            function () { $(this).removeClass('shadow-sm'); }
        );
    }

    bindEvents() {
        // Événement global pour fermer l'édition en cliquant ailleurs
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.editing, .datepicker').length && this.currentEditingCell) {
                this.cancelEdit();
            }
        });

        // Événement pour la touche Escape
        $(document).on('keydown', (e) => {
            if (e.key === 'Escape' && this.currentEditingCell) {
                this.cancelEdit();
            }
        });

        // Événement pour les changements de visibilité des colonnes
        $(document).on('column-visibility.dt', (e, settings, column, state) => {
            // Mettre à jour l'indicateur de colonnes cachées
            this.updateColumnVisibilityIndicator();
        });
    }

    bindEditableEvents() {
        // Lier les événements aux cellules éditables
        $('.editable').off('click.inline-edit').on('click.inline-edit', (e) => {
            e.stopPropagation();
            this.startEdit($(e.currentTarget));
        });
    }

    startEdit($cell) {
        // Ne pas éditer si déjà en cours d'édition
        if ($cell.hasClass('editing') || this.currentEditingCell) {
            return;
        }

        const field = $cell.data('field');
        const studentId = $cell.data('student-id');
        const currentValue = $cell.text().trim();

        this.currentEditingCell = $cell;
        $cell.addClass('editing');

        // Créer l'élément d'édition selon le type de champ
        const editElement = this.createEditElement(field, currentValue);
        $cell.html(editElement);

        // Configurer l'élément d'édition
        this.configureEditElement($cell, field, studentId);
    }

    createEditElement(field, currentValue) {
        const baseClasses = 'form-control edit-input';

        switch (field) {
            case 'dob':
                return `
                    <input type="text" class="${baseClasses} datepicker" value="${currentValue}" placeholder="YYYY-MM-DD">
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;

            case 'status':
                return `
                    <select class="${baseClasses}">
                        <option value="Normal" ${currentValue === 'Normal' ? 'selected' : ''}>Normal</option>
                        <option value="ADRA" ${currentValue === 'ADRA' ? 'selected' : ''}>ADRA</option>
                        <option value="TEAM3" ${currentValue === 'TEAM3' ? 'selected' : ''}>TEAM3</option>
                    </select>
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;

            case 'student_type':
                return `
                    <select class="${baseClasses}">
                        <option value="Nouveau" ${currentValue === 'Nouveau' ? 'selected' : ''}>Nouveau</option>
                        <option value="Ancien" ${currentValue === 'Ancien' ? 'selected' : ''}>Ancien</option>
                    </select>
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;

            case 'academic_status':
                return `
                    <select class="${baseClasses}">
                        <option value="Passant" ${currentValue === 'Passant' ? 'selected' : ''}>Passant</option>
                        <option value="Redoublant" ${currentValue === 'Redoublant' ? 'selected' : ''}>Redoublant</option>
                    </select>
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;

            case 'religion':
                return `
                    <select class="${baseClasses}">
                        <option value="">Choisir...</option>
                        <option value="FLM" ${currentValue === 'FLM' ? 'selected' : ''}>FLM</option>
                        <option value="FJKM" ${currentValue === 'FJKM' ? 'selected' : ''}>FJKM</option>
                        <option value="Catholique" ${currentValue === 'Catholique' ? 'selected' : ''}>Catholique</option>
                        <option value="Adventiste" ${currentValue === 'Adventiste' ? 'selected' : ''}>Adventiste</option>
                        <option value="Islam" ${currentValue === 'Islam' ? 'selected' : ''}>Islam</option>
                        <option value="Judaïsme" ${currentValue === 'Judaïsme' ? 'selected' : ''}>Judaïsme</option>
                        <option value="Apokalipsy" ${currentValue === 'Apokalipsy' ? 'selected' : ''}>Apokalipsy</option>
                        <option value="Autres" ${currentValue === 'Autres' ? 'selected' : ''}>Autres</option>
                    </select>
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;

            case 'gender':
                return `
                    <select class="${baseClasses}">
                        <option value="">Choisir...</option>
                        <option value="Male" ${currentValue === 'Male' ? 'selected' : ''}>Masculin</option>
                        <option value="Female" ${currentValue === 'Female' ? 'selected' : ''}>Féminin</option>
                    </select>
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;

            default:
                return `
                    <input type="text" class="${baseClasses}" value="${currentValue}">
                    <span class="save-indicator d-none"><i class="icon-spinner2 spinner"></i></span>
                `;
        }
    }

    configureEditElement($cell, field, studentId) {
        const $input = $cell.find('.edit-input');

        // Focus sur l'input
        $input.focus();

        // Sélectionner le texte pour les inputs texte
        if ($input.is('input[type="text"]')) {
            $input.select();
        }

        // Configurer le datepicker
        if (field === 'dob') {
            $input.datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                endDate: new Date()
            }).on('changeDate', () => {
                this.saveField(field, $input.val(), studentId, $cell);
            });
        }

        // Événements de sauvegarde
        $input.on('blur', () => {
            if (!$input.is('select')) {
                this.saveField(field, $input.val(), studentId, $cell);
            }
        });

        $input.on('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.saveField(field, $input.val(), studentId, $cell);
            }
        });

        $input.on('change', () => {
            if ($input.is('select')) {
                this.saveField(field, $input.val(), studentId, $cell);
            }
        });
    }

    saveField(field, value, studentId, $cell) {
        // Afficher l'indicateur de sauvegarde
        $cell.find('.save-indicator').removeClass('d-none');

        $.ajax({
            url: this.options.saveUrl,
            type: 'POST',
            data: {
                _token: this.options.csrfToken,
                student_id: studentId,
                field_name: field,
                field_value: value
            },
            success: (response) => {
                if (response.ok) {
                    // Mettre à jour l'affichage
                    $cell.removeClass('editing').html(value || '-');

                    // Mettre à jour l'âge si c'est une date de naissance
                    if (field === 'dob') {
                        const age = this.calculateAge(value);
                        $(`.age-display[data-student-id="${studentId}"]`).text(age);
                    }

                    // Message de succès
                    this.showNotification('Modification enregistrée avec succès', 'success');
                } else {
                    // Restaurer la valeur précédente
                    $cell.removeClass('editing').html($cell.data('original-value') || '-');
                    this.showNotification(response.msg || 'Erreur lors de la sauvegarde', 'error');
                }

                this.currentEditingCell = null;
            },
            error: () => {
                // Restaurer la valeur précédente
                $cell.removeClass('editing').html($cell.data('original-value') || '-');
                this.showNotification('Erreur de connexion', 'error');
                this.currentEditingCell = null;
            }
        });
    }

    cancelEdit() {
        if (this.currentEditingCell) {
            const originalValue = this.currentEditingCell.data('original-value') || this.currentEditingCell.text().trim();
            this.currentEditingCell.removeClass('editing').html(originalValue);
            this.currentEditingCell = null;
        }
    }

    calculateAge(dob) {
        if (!dob) return '';

        const birthDate = new Date(dob);
        if (isNaN(birthDate.getTime())) return '';

        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();

        if (today.getMonth() < birthDate.getMonth() ||
            (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age;
    }

    calculateAllAges() {
        $('.editable[data-field="dob"]').each((index, element) => {
            const $element = $(element);
            const dob = $element.text().trim();
            const studentId = $element.data('student-id');
            const age = this.calculateAge(dob);

            if (age !== '') {
                $(`.age-display[data-student-id="${studentId}"]`).text(age);
            }
        });
    }

    showNotification(message, type) {
        if (typeof PNotify !== 'undefined') {
            new PNotify({
                text: message,
                type: type,
                delay: 3000
            });
        } else {
            // Fallback pour les navigateurs sans PNotify
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const $alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `);

            $('body').prepend($alert);
            setTimeout(() => $alert.remove(), 3000);
        }
    }

    showAddStudentModal() {
        // Rediriger vers la page d'ajout d'élève
        window.location.href = '/students/create';
    }

    // Fonction pour mettre à jour l'indicateur de visibilité des colonnes
    updateColumnVisibilityIndicator() {
        if (!this.dataTable) return;

        const hiddenColumns = this.dataTable.columns(':hidden').count();
        const totalColumns = this.dataTable.columns().count();
        const visibleColumns = totalColumns - hiddenColumns;

        // Mettre à jour l'indicateur dans l'interface
        const $header = $('.datatable-header');
        const $indicator = $('.hidden-columns-indicator');
        const $count = $('#hidden-columns-count');

        if (hiddenColumns > 0) {
            // Ajouter une classe pour indiquer qu'il y a des colonnes cachées
            $header.addClass('has-hidden-columns');

            // Afficher l'indicateur et mettre à jour le compte
            $indicator.removeClass('d-none');
            $count.text(hiddenColumns);
        } else {
            // Retirer la classe si toutes les colonnes sont visibles
            $header.removeClass('has-hidden-columns');

            // Cacher l'indicateur
            $indicator.addClass('d-none');
        }
    }

    // Fonction pour exporter uniquement les colonnes sélectionnées
    exportSelectedColumns(format = 'excel') {
        if (!this.dataTable) return;

        // Obtenir les colonnes actuellement visibles
        const visibleColumns = this.dataTable.columns(':visible:not(.no-export)').indexes().toArray();

        if (visibleColumns.length === 0) {
            this.showNotification('Aucune colonne visible à exporter', 'warning');
            return;
        }

        // Configurer les options d'export
        const exportOptions = {
            columns: visibleColumns,
            title: 'Export_Eleves_' + new Date().toISOString().slice(0, 10)
        };

        // Effectuer l'export selon le format
        switch (format) {
            case 'excel':
                this.dataTable.button('.buttons-excel').trigger();
                break;
            case 'pdf':
                this.dataTable.button('.buttons-pdf').trigger();
                break;
            case 'copy':
                this.dataTable.button('.buttons-copy').trigger();
                break;
            default:
                this.showNotification('Format d\'export non supporté', 'error');
        }
    // Fonction pour réinitialiser la visibilité des colonnes
    resetColumnVisibility() {
        if (window.studentTableManager && typeof window.studentTableManager.resetToDefaults === 'function') {
            window.studentTableManager.resetToDefaults();
            return;
        }
        if (this.dataTable) {
            this.dataTable.columns().visible(true);
            this.dataTable.columns('.no-export').visible(false);
            this.updateColumnVisibilityIndicator();
            this.showNotification('Visibilité des colonnes réinitialisée', 'success');
        }
    }

    // Fonction pour afficher toutes les colonnes
    showAllColumns() {
        if (window.studentTableManager && typeof window.studentTableManager.showAllColumns === 'function') {
            window.studentTableManager.showAllColumns();
            return;
        }
        if (this.dataTable) {
            this.dataTable.columns().visible(true);
            this.updateColumnVisibilityIndicator();
            this.showNotification('Toutes les colonnes sont maintenant visibles', 'success');
        }
    }

    // Fonction pour masquer toutes les colonnes sauf les essentielles
    hideAllColumns() {
        if (window.studentTableManager && typeof window.studentTableManager.hideAllColumns === 'function') {
            window.studentTableManager.hideAllColumns();
            return;
        }
        if (this.dataTable) {
            this.dataTable.columns().visible(false);
            this.dataTable.columns([2, 12]).visible(true); // Nom et Sexe
            this.dataTable.columns('.no-export').visible(false);
            this.updateColumnVisibilityIndicator();
            this.showNotification('Colonnes masquées sauf les essentielles', 'success');
        }
    }

    // Méthode pour exporter avec les colonnes visibles
    exportCustomExcel() {
        if (window.studentTableManager && typeof window.studentTableManager.exportToExcel === 'function') {
            window.studentTableManager.exportToExcel();
        } else if (this.dataTable && this.dataTable.button('.buttons-excel').length) {
            this.dataTable.button('.buttons-excel').trigger();
        }
    }
}

// Initialisation automatique
$(document).ready(() => {
    if ($('.datatable-button-html5-columns').length) {
        window.inlineEditor = new InlineEditor();
    }
});

// Fonction globale pour exporter avec les colonnes visibles (compatibilité)
function exportCustomExcel() {
    if (window.studentTableManager && typeof window.studentTableManager.exportToExcel === 'function') {
        window.studentTableManager.exportToExcel();
    } else if (window.inlineEditor && window.inlineEditor.dataTable) {
        window.inlineEditor.exportSelectedColumns('excel');
    }
}
window.exportCustomExcel = exportCustomExcel;

