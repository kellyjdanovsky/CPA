/**
 * Phase 2 - Form Validation & Enhancement
 * Validation en temps réel, autocomplete, file upload avec preview
 */

class ModernFormValidator {
    constructor(formSelector, options = {}) {
        this.form = document.querySelector(formSelector);
        if (!this.form) return;

        this.options = {
            validateOnBlur: true,
            validateOnInput: false,
            showSuccessState: true,
            ...options
        };

        this.rules = {};
        this.init();
    }

    init() {
        this.bindEvents();
        this.initFileUploads();
        this.initAutocomplete();
        this.initCharacterCounters();
    }

    // Définir les règles de validation
    setRules(rules) {
        this.rules = rules;
        return this;
    }

    // Lier les événements
    bindEvents() {
        if (this.options.validateOnBlur) {
            this.form.querySelectorAll('.modern-input, .modern-select, .modern-textarea').forEach(input => {
                input.addEventListener('blur', (e) => this.validateField(e.target));
            });
        }

        if (this.options.validateOnInput) {
            this.form.querySelectorAll('.modern-input, .modern-textarea').forEach(input => {
                input.addEventListener('input', (e) => this.validateField(e.target));
            });
        }

        // Validation à la soumission
        this.form.addEventListener('submit', (e) => {
            if (!this.validateAll()) {
                e.preventDefault();
                this.focusFirstError();
            }
        });
    }

    // Valider un champ
    validateField(field) {
        const name = field.name || field.id;
        const rule = this.rules[name];

        if (!rule) return true;

        const value = field.value.trim();
        const errors = [];

        // Required
        if (rule.required && !value) {
            errors.push(rule.messages?.required || 'Ce champ est requis');
        }

        // Min length
        if (rule.minLength && value.length < rule.minLength) {
            errors.push(rule.messages?.minLength || `Minimum ${rule.minLength} caractères`);
        }

        // Max length
        if (rule.maxLength && value.length > rule.maxLength) {
            errors.push(rule.messages?.maxLength || `Maximum ${rule.maxLength} caractères`);
        }

        // Email
        if (rule.email && value && !this.isValidEmail(value)) {
            errors.push(rule.messages?.email || 'Email invalide');
        }

        // Pattern
        if (rule.pattern && value && !rule.pattern.test(value)) {
            errors.push(rule.messages?.pattern || 'Format invalide');
        }

        // Custom validator
        if (rule.custom && typeof rule.custom === 'function') {
            const customResult = rule.custom(value, field);
            if (customResult !== true) {
                errors.push(customResult);
            }
        }

        // Afficher le résultat
        if (errors.length > 0) {
            this.setFieldState(field, 'invalid', errors[0]);
            return false;
        } else if (this.options.showSuccessState && value) {
            this.setFieldState(field, 'valid');
            return true;
        } else {
            this.setFieldState(field, 'neutral');
            return true;
        }
    }

    // Valider tous les champs
    validateAll() {
        let isValid = true;
        const fields = this.form.querySelectorAll('.modern-input, .modern-select, .modern-textarea');

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    // Définir l'état d'un champ
    setFieldState(field, state, message = '') {
        const feedback = field.parentElement.querySelector('.modern-feedback') ||
            this.createFeedback(field);

        // Retirer les classes précédentes
        field.classList.remove('is-valid', 'is-invalid');
        feedback.classList.remove('valid', 'invalid', 'hint');

        if (state === 'valid') {
            field.classList.add('is-valid');
            feedback.classList.add('valid');
            feedback.textContent = message || '✓ Valide';
        } else if (state === 'invalid') {
            field.classList.add('is-invalid');
            feedback.classList.add('invalid');
            feedback.textContent = message;
        } else {
            feedback.textContent = '';
        }
    }

    // Créer un élément de feedback
    createFeedback(field) {
        const feedback = document.createElement('span');
        feedback.className = 'modern-feedback';
        field.parentElement.appendChild(feedback);
        return feedback;
    }

    // Focus sur la première erreur
    focusFirstError() {
        const firstError = this.form.querySelector('.is-invalid');
        if (firstError) {
            firstError.focus();
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Valider un email
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Initialiser les file uploads
    initFileUploads() {
        this.form.querySelectorAll('.modern-file-input').forEach(input => {
            input.addEventListener('change', (e) => this.handleFileUpload(e.target));
        });
    }

    // Gérer l'upload de fichier
    handleFileUpload(input) {
        const file = input.files[0];
        const label = document.querySelector(`label[for="${input.id}"]`);

        if (!file) return;

        // Mettre à jour le label
        if (label) {
            label.classList.add('has-file');
            const textElement = label.querySelector('.modern-file-text-main');
            if (textElement) {
                textElement.textContent = file.name;
            }
        }

        // Prévisualiser l'image si c'est une image
        if (file.type.startsWith('image/')) {
            this.previewImage(file, input);
        }

        // Valider la taille du fichier
        const maxSize = input.dataset.maxSize || 5 * 1024 * 1024; // 5MB par défaut
        if (file.size > maxSize) {
            if (window.notify) {
                notify.error(`Le fichier est trop volumineux (max: ${this.formatFileSize(maxSize)})`);
            }
            input.value = '';
            return;
        }
    }

    // Prévisualiser une image
    previewImage(file, input) {
        const reader = new FileReader();

        reader.onload = (e) => {
            let preview = input.parentElement.querySelector('.modern-file-preview');

            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'modern-file-preview';
                input.parentElement.appendChild(preview);
            }

            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="modern-file-preview-image">
                <button type="button" class="modern-btn modern-btn-sm modern-btn-danger mt-2" onclick="this.closest('.modern-file-upload').querySelector('input').value=''; this.closest('.modern-file-preview').classList.remove('active');">
                    <i class="icon-trash"></i> Supprimer
                </button>
            `;
            preview.classList.add('active');
        };

        reader.readAsDataURL(file);
    }

    // Formater la taille d'un fichier
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Initialiser l'autocomplete
    initAutocomplete() {
        this.form.querySelectorAll('.modern-autocomplete input').forEach(input => {
            const container = input.closest('.modern-autocomplete');
            const resultsDiv = container.querySelector('.modern-autocomplete-results');

            if (!resultsDiv) return;

            input.addEventListener('input', (e) => {
                const query = e.target.value;
                if (query.length < 2) {
                    resultsDiv.classList.remove('active');
                    return;
                }

                // Appeler la fonction de recherche (à définir par l'utilisateur)
                if (input.dataset.autocompleteUrl) {
                    this.fetchAutocompleteResults(input.dataset.autocompleteUrl, query, resultsDiv);
                }
            });

            // Fermer au clic extérieur
            document.addEventListener('click', (e) => {
                if (!container.contains(e.target)) {
                    resultsDiv.classList.remove('active');
                }
            });
        });
    }

    // Récupérer les résultats d'autocomplete
    async fetchAutocompleteResults(url, query, resultsDiv) {
        try {
            const response = await fetch(`${url}?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            this.displayAutocompleteResults(data, resultsDiv);
        } catch (error) {
            console.error('Erreur autocomplete:', error);
        }
    }

    // Afficher les résultats d'autocomplete
    displayAutocompleteResults(results, resultsDiv) {
        if (!results || results.length === 0) {
            resultsDiv.classList.remove('active');
            return;
        }

        resultsDiv.innerHTML = results.map(item => `
            <div class="modern-autocomplete-item" data-value="${item.value}">
                <div class="modern-autocomplete-item-text">${item.label}</div>
                ${item.sub ? `<div class="modern-autocomplete-item-sub">${item.sub}</div>` : ''}
            </div>
        `).join('');

        resultsDiv.classList.add('active');

        // Gérer la sélection
        resultsDiv.querySelectorAll('.modern-autocomplete-item').forEach(item => {
            item.addEventListener('click', () => {
                const input = resultsDiv.previousElementSibling;
                input.value = item.querySelector('.modern-autocomplete-item-text').textContent;
                input.dataset.selectedValue = item.dataset.value;
                resultsDiv.classList.remove('active');
            });
        });
    }

    // Initialiser les compteurs de caractères
    initCharacterCounters() {
        this.form.querySelectorAll('.modern-textarea[maxlength]').forEach(textarea => {
            const maxLength = textarea.getAttribute('maxlength');
            const counter = document.createElement('div');
            counter.className = 'modern-textarea-counter';

            textarea.parentElement.appendChild(counter);

            const updateCounter = () => {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${textarea.value.length}/${maxLength}`;
                counter.classList.toggle('limit-reached', remaining <= 10);
            };

            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
    }

    // Réinitialiser le formulaire
    reset() {
        this.form.reset();
        this.form.querySelectorAll('.modern-input, .modern-select, .modern-textarea').forEach(field => {
            this.setFieldState(field, 'neutral');
        });
    }
}

// Fonction helper pour créer rapidement un validateur
window.createFormValidator = function (selector, rules, options = {}) {
    const validator = new ModernFormValidator(selector, options);
    validator.setRules(rules);
    return validator;
};

// Export
window.ModernFormValidator = ModernFormValidator;
