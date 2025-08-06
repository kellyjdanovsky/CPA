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
        // Configuration DataTable optimisée pour l'édition en ligne
        const tableConfig = {
            processing: true,
            stateSave: true,
            stateDuration: 60 * 60 * 24, // 24 heures
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            buttons: [
                {
                    extend: 'copyHtml5',
                    className: 'btn btn-light',
                    text: '<i class="icon-copy mr-2"></i>Copier'
                },
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-light',
                    text: '<i class="icon-file-excel mr-2"></i>Excel'
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-light',
                    text: '<i class="icon-file-pdf mr-2"></i>PDF'
                },
                {
                    text: '<i class="icon-plus mr-2"></i>Ajouter élève',
                    className: 'btn btn-success',
                    action: () => this.showAddStudentModal()
                }
            ],
            dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
            columnDefs: [
                {
                    targets: 'no-sort',
                    orderable: false
                },
                {
                    targets: 'editable',
                    className: 'editable-cell'
                }
            ],
            drawCallback: () => {
                this.bindEditableEvents();
                this.calculateAllAges();
            }
        };

        // Initialiser DataTable
        if ($.fn.DataTable.isDataTable('.datatable-button-html5-columns')) {
            $('.datatable-button-html5-columns').DataTable().destroy();
        }
        
        this.dataTable = $('.datatable-button-html5-columns').DataTable(tableConfig);
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
}

// Initialisation automatique
$(document).ready(() => {
    if ($('.datatable-button-html5-columns').length) {
        window.inlineEditor = new InlineEditor();
    }
});
