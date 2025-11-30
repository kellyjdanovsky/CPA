/**
 * ==========================================================================
 * CPA - Gestion du Barème avec Remarques Modifiables
 * Système CRUD pour les remarques du barème
 * ==========================================================================
 */

(function ($) {
    'use strict';

    // ========== 1. CONFIGURATION ========== //
    const STORAGE_KEY = 'cpa-bareme-remarques';

    // Barème par défaut
    const DEFAULT_BAREME = [
        { min: 90, max: 100, mention: 'Excellent', remarque: 'Félicitations ! Travail exemplaire.', color: '#22c55e' },
        { min: 80, max: 89.99, mention: 'Très Bien', remarque: 'Très bon travail, continuez ainsi.', color: '#3b82f6' },
        { min: 70, max: 79.99, mention: 'Bien', remarque: 'Bon travail, quelques améliorations possibles.', color: '#6366f1' },
        { min: 60, max: 69.99, mention: 'Assez Bien', remarque: 'Travail satisfaisant, peut mieux faire.', color: '#f59e0b' },
        { min: 50, max: 59.99, mention: 'Passable', remarque: 'Résultats moyens, efforts nécessaires.', color: '#fb923c' },
        { min: 40, max: 49.99, mention: 'Médiocre', remarque: 'Résultats insuffisants, redoublement d\'efforts requis.', color: '#f87171' },
        { min: 0, max: 39.99, mention: 'Échec', remarque: 'Résultats très insuffisants, travail sérieux exigé.', color: '#dc2626' }
    ];

    // ========== 2. GESTION DU STOCKAGE ========== //

    /**
     * Charger le barème depuis le localStorage
     */
    function loadBareme() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                return JSON.parse(stored);
            }
        } catch (e) {
            console.error('Erreur chargement barème:', e);
        }
        return DEFAULT_BAREME;
    }

    /**
     * Sauvegarder le barème
     */
    function saveBareme(bareme) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(bareme));
            console.log('✓ Barème sauvegardé');
            return true;
        } catch (e) {
            console.error('Erreur sauvegarde barème:', e);
            return false;
        }
    }

    /**
     * Réinitialiser au barème par défaut
     */
    function resetBareme() {
        saveBareme(DEFAULT_BAREME);
        return DEFAULT_BAREME;
    }

    // ========== 3. CALCUL ET RÉCUPÉRATION ========== //

    /**
     * Obtenir la mention et remarque pour une note
     */
    function getMentionForNote(note) {
        const bareme = loadBareme();
        const noteNum = parseFloat(note);

        for (let i = 0; i < bareme.length; i++) {
            const range = bareme[i];
            if (noteNum >= range.min && noteNum <= range.max) {
                return range;
            }
        }

        // Par défaut si rien ne correspond
        return bareme[bareme.length - 1];
    }

    /**
     * Obtenir la mention pour une moyenne
     */
    function getMoyenneGenerale(moyenne) {
        return getMentionForNote(moyenne);
    }

    // ========== 4. INTERFACE D'ÉDITION ========== //

    /**
     * Créer l'interface d'édition du barème
     */
    function createBaremeEditor() {
        const bareme = loadBareme();

        let html = `
            <div class="bareme-editor">
                <div class="bareme-editor-header">
                    <h5>
                        <i class="icon-medal mr-2"></i>
                        Configuration du Barème
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="BaremeManager.reset()">
                        <i class="icon-reload"></i>
                        Réinitialiser
                    </button>
                </div>
                <div class="bareme-editor-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 15%">Min</th>
                                    <th style="width: 15%">Max</th>
                                    <th style="width: 20%">Mention</th>
                                    <th style="width: 40%">Remarque</th>
                                    <th style="width: 10%">Couleur</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        bareme.forEach((range, index) => {
            html += `
                <tr>
                    <td>
                        <input 
                            type="number" 
                            class="form-control form-control-sm" 
                            value="${range.min}" 
                            step="0.01"
                            data-index="${index}"
                            data-field="min"
                            onchange="BaremeManager.updateField(this)"
                        >
                    </td>
                    <td>
                        <input 
                            type="number" 
                            class="form-control form-control-sm" 
                            value="${range.max}" 
                            step="0.01"
                            data-index="${index}"
                            data-field="max"
                            onchange="BaremeManager.updateField(this)"
                        >
                    </td>
                    <td>
                        <input 
                            type="text" 
                            class="form-control form-control-sm" 
                            value="${range.mention}" 
                            data-index="${index}"
                            data-field="mention"
                            onchange="BaremeManager.updateField(this)"
                        >
                    </td>
                    <td>
                        <textarea 
                            class="form-control form-control-sm" 
                            rows="2"
                            data-index="${index}"
                            data-field="remarque"
                            onchange="BaremeManager.updateField(this)"
                        >${range.remarque}</textarea>
                    </td>
                    <td>
                        <input 
                            type="color" 
                            class="form-control form-control-sm" 
                            value="${range.color}" 
                            data-index="${index}"
                            data-field="color"
                            onchange="BaremeManager.updateField(this)"
                            style="height: 40px; padding: 2px;"
                        >
                    </td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bareme-editor-footer">
                    <button class="btn btn-success" onclick="BaremeManager.save()">
                        <i class="icon-checkmark"></i>
                        Sauvegarder
                    </button>
                    <button class="btn btn-outline-secondary" onclick="BaremeManager.cancel()">
                        <i class="icon-cross"></i>
                        Annuler
                    </button>
                </div>
            </div>
        `;

        return html;
    }

    /**
     * Afficher l'éditeur de barème dans une modal
     */
    function showBaremeEditor() {
        if (typeof CPAModern !== 'undefined' && CPAModern.showModernModal) {
            const content = createBaremeEditor();
            CPAModern.showModernModal('Configuration du Barème', content, []);
        } else {
            alert('Veuillez charger modern-ui.js pour utiliser l\'éditeur de barème');
        }
    }

    /**
     * Mettre à jour un champ du barème
     */
    function updateField(input) {
        const index = parseInt(input.dataset.index);
        const field = input.dataset.field;
        const value = input.value;

        const bareme = loadBareme();

        if (field === 'min' || field === 'max') {
            bareme[index][field] = parseFloat(value);
        } else {
            bareme[index][field] = value;
        }

        // Sauvegarde temporaire (auto-save)
        saveBareme(bareme);
    }

    /**
     * Sauvegarder et fermer
     */
    function save() {
        if (typeof CPAModern !== 'undefined' && CPAModern.showToast) {
            CPAModern.showToast('Barème sauvegardé avec succès !', 'success', 2000);
        }

        // Fermer la modal si elle existe
        $('.modern-modal-overlay').fadeOut(300, function () {
            $(this).remove();
        });

        // Rafraîchir l'affichage si nécessaire
        window.dispatchEvent(new Event('baremeUpdated'));
    }

    /**
     * Annuler les modifications
     */
    function cancel() {
        $('.modern-modal-overlay').fadeOut(300, function () {
            $(this).remove();
        });
    }

    /**
     * Réinitialiser le barème
     */
    function reset() {
        if (confirm('Voulez-vous vraiment réinitialiser le barème aux valeurs par défaut ?')) {
            resetBareme();

            if (typeof CPAModern !== 'undefined' && CPAModern.showToast) {
                CPAModern.showToast('Barème réinitialisé', 'info', 2000);
            }

            // Rafraîchir l'éditeur
            showBaremeEditor();
        }
    }

    // ========== 5. AFFICHAGE DU BARÈME ========== //

    /**
     * Créer un tableau d'affichage du barème
     */
    function createBaremeDisplay() {
        const bareme = loadBareme();

        let html = `
            <div class="bareme-display">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Note</th>
                                <th>Mention</th>
                                <th>Remarque</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        bareme.forEach(range => {
            html += `
                <tr>
                    <td>
                        <span class="badge" style="background-color: ${range.color}; color: white;">
                            ${range.min} - ${range.max}
                        </span>
                    </td>
                    <td><strong>${range.mention}</strong></td>
                    <td><em>${range.remarque}</em></td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        return html;
    }

    /**
     * Insérer le barème dans un élément
     */
    function displayBaremeIn(selector) {
        const html = createBaremeDisplay();
        $(selector).html(html);
    }

    // ========== 6. HELPER POUR BULLETINS ========== //

    /**
     * Ajouter la mention et remarque à un bulletin
     */
    function applyToMoyenne(moyenne, targetElement) {
        const mention = getMentionForNote(moyenne);

        if (targetElement) {
            $(targetElement).html(`
                <div class="mention-display" style="text-align: center;">
                    <div class="mention-badge" style="
                        display: inline-block;
                        padding: 0.5rem 1rem;
                        background-color: ${mention.color};
                        color: white;
                        border-radius: 0.5rem;
                        font-weight: bold;
                        margin-bottom: 0.5rem;
                    ">
                        ${mention.mention}
                    </div>
                    <div class="mention-remarque" style="
                        font-style: italic;
                        color: #6b7280;
                        font-size: 0.875rem;
                    ">
                        ${mention.remarque}
                    </div>
                </div>
            `);
        }

        return mention;
    }

    // ========== 7. API PUBLIQUE ========== //

    window.BaremeManager = {
        // Obtenir le barème actuel
        getBareme: loadBareme,

        // Sauvegarder le barème
        saveBareme: saveBareme,

        // Réinitialiser
        resetBareme: resetBareme,
        reset: reset,

        // Obtenir mention pour une note
        getMention: getMentionForNote,

        // Obtenir mention pour moyenne générale
        getMoyenneGenerale: getMoyenneGenerale,

        // Afficher l'éditeur
        showEditor: showBaremeEditor,

        // Afficher le barème
        displayIn: displayBaremeIn,

        // Appliquer à une moyenne
        applyToMoyenne: applyToMoyenne,

        // Méthodes internes (pour les callbacks HTML)
        updateField: updateField,
        save: save,
        cancel: cancel,

        // Créer le HTML d'affichage
        createDisplay: createBaremeDisplay,

        // Créer le HTML d'édition
        createEditor: createBaremeEditor
    };

    // ========== 8. INITIALISATION ========== //

    $(document).ready(function () {
        // Ajouter un bouton dans le header si on est sur une page de notes
        if (window.location.pathname.includes('/marks/') ||
            window.location.pathname.includes('/bulletin')) {

            const headerElements = $('.header-elements .d-flex');
            if (headerElements.length > 0) {
                const baremeBtn = $(`
                    <button class="btn btn-outline-primary btn-sm ml-2" onclick="BaremeManager.showEditor()">
                        <i class="icon-medal"></i>
                        Barème
                    </button>
                `);
                headerElements.append(baremeBtn);
            }
        }

        // Auto-appliquer les mentions si des éléments avec data-moyenne existent
        $('[data-moyenne]').each(function () {
            const moyenne = parseFloat($(this).data('moyenne'));
            const targetSelector = $(this).data('mention-target');

            if (targetSelector) {
                BaremeManager.applyToMoyenne(moyenne, targetSelector);
            }
        });

        console.log('%c📊 Barème Manager chargé', 'color: #6366f1; font-weight: bold;');
    });

})(jQuery);
