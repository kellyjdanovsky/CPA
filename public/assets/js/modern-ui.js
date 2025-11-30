/**
 * ==========================================================================
 * CPA - Script UI Moderne et Interactif
 * Fonctionnalités avancées pour une expérience utilisateur optimale
 * ==========================================================================
 */

(function($) {
    'use strict';

    // ========== 1. ANIMATIONS DES COMPTEURS ========== //
    function animateCounter(element) {
        const target = parseInt(element.text().replace(/[^0-9]/g, ''));
        const duration = 2000;
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                element.text(target);
                clearInterval(timer);
            } else {
                element.text(Math.floor(current));
            }
        }, duration / steps);
    }

    // ========== 2. NOTIFICATIONS TOAST MODERNES ========== //
    window.showToast = function(message, type = 'info', duration = 3000) {
        const icons = {
            success: 'icon-checkmark-circle',
            error: 'icon-cross-circle',
            warning: 'icon-warning',
            info: 'icon-info'
        };

        const colors = {
            success: '#22c55e',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const toast = $(`
            <div class="modern-toast modern-toast-${type}" style="display: none;">
                <div class="toast-icon">
                    <i class="${icons[type] || icons.info}"></i>
                </div>
                <div class="toast-content">
                    <p>${message}</p>
                </div>
                <button class="toast-close">
                    <i class="icon-cross2"></i>
                </button>
            </div>
        `);

        // Ajouter au conteneur (créer si nécessaire)
        let container = $('.toast-container');
        if (container.length === 0) {
            container = $('<div class="toast-container"></div>').appendTo('body');
        }

        container.append(toast);
        toast.fadeIn(300);

        // Fermer au clic
        toast.find('.toast-close').on('click', function() {
            toast.fadeOut(300, function() {
                toast.remove();
            });
        });

        // Auto-fermer
        if (duration > 0) {
            setTimeout(function() {
                toast.fadeOut(300, function() {
                    toast.remove();
                });
            }, duration);
        }
    };

    // ========== 3. MODAL MODERNE ========== //
    window.showModernModal = function(title, content, buttons = []) {
        const buttonHTML = buttons.map(btn => {
            const btnClass = btn.class || 'btn-primary';
            const btnText = btn.text || 'OK';
            const btnAction = btn.action || function() {};
            return `<button class="btn ${btnClass} modern-modal-btn" data-action="${btn.id || 'default'}">${btnText}</button>`;
        }).join('');

        const modal = $(`
            <div class="modern-modal-overlay">
                <div class="modern-modal">
                    <div class="modern-modal-header">
                        <h5>${title}</h5>
                        <button class="modern-modal-close">
                            <i class="icon-cross2"></i>
                        </button>
                    </div>
                    <div class="modern-modal-body">
                        ${content}
                    </div>
                    ${buttons.length > 0 ? `<div class="modern-modal-footer">${buttonHTML}</div>` : ''}
                </div>
            </div>
        `);

        $('body').append(modal);
        modal.fadeIn(300);

        // Fermer la modal
        modal.find('.modern-modal-close, .modern-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                modal.fadeOut(300, function() {
                    modal.remove();
                });
            }
        });

        // Gérer les boutons personnalisés
        buttons.forEach(btn => {
            modal.find(`[data-action="${btn.id || 'default'}"]`).on('click', function() {
                if (btn.action) btn.action();
                if (btn.closeAfter !== false) {
                    modal.fadeOut(300, function() {
                        modal.remove();
                    });
                }
            });
        });

        return modal;
    };

    // ========== 4. LOADING STATES ========== //
    $.fn.setLoading = function(loading = true) {
        if (loading) {
            this.prop('disabled', true);
            this.data('original-text', this.html());
            this.html('<i class="icon-spinner2 spinner mr-2"></i> Chargement...');
        } else {
            this.prop('disabled', false);
            this.html(this.data('original-text') || this.html());
        }
        return this;
    };

    // ========== 5. RECHERCHE INSTANTANÉE ========== //
    window.initInstantSearch = function(inputSelector, targetSelector, searchKeys) {
        $(inputSelector).on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $(targetSelector).each(function() {
                let text = '';
                searchKeys.forEach(key => {
                    text += $(this).find(key).text().toLowerCase() + ' ';
                });
                
                if (text.includes(searchTerm)) {
                    $(this).fadeIn(200);
                } else {
                    $(this).fadeOut(200);
                }
            });
        });
    };

    // ========== 6. CONFIRMATION MODERNE ========== //
    window.confirmModern = function(message, onConfirm, onCancel) {
        return showModernModal(
            'Confirmation',
            `<p>${message}</p>`,
            [
                {
                    id: 'cancel',
                    text: 'Annuler',
                    class: 'btn-outline-secondary',
                    action: onCancel
                },
                {
                    id: 'confirm',
                    text: 'Confirmer',
                    class: 'btn-primary',
                    action: onConfirm
                }
            ]
        );
    };

    // ========== 7. COPIER DANS LE PRESSE-PAPIERS ========== //
    window.copyToClipboard = function(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('Copié dans le presse-papiers', 'success', 2000);
    };

    // ========== 8. VALIDATION DES FORMULAIRES ========== //
    $.fn.modernValidate = function() {
        let isValid = true;
        
        this.find('[required]').each(function() {
            const $field = $(this);
            const value = $field.val();
            
            // Supprimer les messages d'erreur existants
            $field.siblings('.field-error').remove();
            $field.removeClass('is-invalid');
            
            if (!value || value.trim() === '') {
                isValid = false;
                $field.addClass('is-invalid');
                $field.after('<div class="field-error">Ce champ est requis</div>');
            }
        });
        
        return isValid;
    };

    // ========== 9. DRAG & DROP POUR UPLOAD ========== //
    window.initDragDrop = function(dropZoneSelector, inputSelector, callback) {
        const dropZone = $(dropZoneSelector);
        const input = $(inputSelector);
        
        dropZone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        
        dropZone.on('dragover dragenter', function() {
            dropZone.addClass('drag-over');
        });
        
        dropZone.on('dragleave dragend drop', function() {
            dropZone.removeClass('drag-over');
        });
        
        dropZone.on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            input[0].files = files;
            if (callback) callback(files);
        });
        
        dropZone.on('click', function() {
            input.click();
        });
        
        input.on('change', function() {
            if (callback) callback(this.files);
        });
    };

    // ========== 10. TABLE SORT MODERNE ========== //
    $.fn.modernSort = function() {
        const $table = this;
        
        $table.find('th[data-sortable]').css('cursor', 'pointer').each(function() {
            $(this).append(' <i class="icon-arrow-down5 sort-icon"></i>');
        });
        
        $table.find('th[data-sortable]').on('click', function() {
            const $th = $(this);
            const columnIndex = $th.index();
            const $tbody = $table.find('tbody');
            const rows = $tbody.find('tr').toArray();
            const isAsc = $th.hasClass('sort-asc');
            
            // Reset all sort indicators
            $table.find('th').removeClass('sort-asc sort-desc');
            
            // Sort rows
            rows.sort(function(a, b) {
                const aValue = $(a).find('td').eq(columnIndex).text();
                const bValue = $(b).find('td').eq(columnIndex).text();
                
                if (isAsc) {
                    return aValue.localeCompare(bValue);
                } else {
                    return bValue.localeCompare(aValue);
                }
            });
            
            // Update sort indicator
            $th.addClass(isAsc ? 'sort-desc' : 'sort-asc');
            
            // Reorder rows
            $tbody.html(rows);
        });
        
        return this;
    };

    // ========== 11. AUTO-SAVE ========== //
    window.initAutoSave = function(formSelector, saveCallback, interval = 30000) {
        const $form = $(formSelector);
        let autoSaveTimer;
        let hasChanges = false;
        
        $form.on('change input', function() {
            hasChanges = true;
            clearTimeout(autoSaveTimer);
            
            autoSaveTimer = setTimeout(function() {
                if (hasChanges && saveCallback) {
                    saveCallback($form.serialize());
                    hasChanges = false;
                    showToast('Sauvegarde automatique effectuée', 'info', 2000);
                }
            }, interval);
        });
    };

    // ========== 12. INITIALISATION AU CHARGEMENT ========== //
    $(document).ready(function() {
        
        // Animer les compteurs au scroll
        const observerOptions = {
            threshold: 0.5
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !$(entry.target).data('animated')) {
                    animateCounter($(entry.target));
                    $(entry.target).data('animated', true);
                }
            });
        }, observerOptions);
        
        $('.counter-animated').each(function() {
            observer.observe(this);
        });
        
        // Tooltips automatiques
        $('[data-toggle="tooltip"]').tooltip();
        
        // Popovers automatiques
        $('[data-toggle="popover"]').popover();
        
        // Smooth scroll pour les ancres
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 600);
            }
        });
        
        // Fermer les alertes automatiquement
        $('.alert-auto-close').each(function() {
            const $alert = $(this);
            setTimeout(function() {
                $alert.fadeOut(300, function() {
                    $alert.remove();
                });
            }, 5000);
        });
        
        // Initialiser le tri moderne pour les tables avec class modern-table
        $('.modern-table').modernSort();
        
        // Console message
        console.log('%c🎓 CPA - Module UI Moderne chargé avec succès!', 'color: #667eea; font-size: 16px; font-weight: bold;');
    });

    // ========== 13. EXPORT DES FONCTIONS GLOBALES ========== //
    window.CPAModern = {
        showToast: window.showToast,
        showModernModal: window.showModernModal,
        confirmModern: window.confirmModern,
        copyToClipboard: window.copyToClipboard,
        initInstantSearch: window.initInstantSearch,
        initDragDrop: window.initDragDrop,
        initAutoSave: window.initAutoSave
    };

})(jQuery);
