/**
 * Modern Toast Notification System
 * Notifications élégantes avec animations et couleurs modernes
 */

class ModernNotification {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Créer le conteneur si nécessaire
        if (!document.getElementById('notification-container')) {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.className = 'notification-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('notification-container');
        }
    }

    show(message, type = 'info', duration = 4000) {
        const toast = this.createToast(message, type);
        this.container.appendChild(toast);

        // Animation d'entrée
        setTimeout(() => toast.classList.add('show'), 10);

        // Auto-dismiss
        if (duration > 0) {
            setTimeout(() => this.dismiss(toast), duration);
        }

        return toast;
    }

    createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `modern-toast toast-${type}`;

        const icons = {
            success: 'icon-checkmark-circle',
            error: 'icon-cross-circle',
            warning: 'icon-warning',
            info: 'icon-info22'
        };

        const titles = {
            success: 'Succès',
            error: 'Erreur',
            warning: 'Attention',
            info: 'Information'
        };

        toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icons[type] || icons.info}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${titles[type] || titles.info}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.closest('.modern-toast').remove()">
                <i class="icon-cross2"></i>
            </button>
        `;

        return toast;
    }

    dismiss(toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }

    // Notification avec action
    showWithAction(message, type, actionText, actionCallback, duration = 0) {
        const toast = this.createToast(message, type);

        const actionBtn = document.createElement('button');
        actionBtn.className = 'toast-action';
        actionBtn.textContent = actionText;
        actionBtn.onclick = () => {
            actionCallback();
            this.dismiss(toast);
        };

        toast.querySelector('.toast-content').appendChild(actionBtn);
        this.container.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 10);

        if (duration > 0) {
            setTimeout(() => this.dismiss(toast), duration);
        }

        return toast;
    }
}

// Instance globale
window.notify = new ModernNotification();

// Compatibilité avec les anciennes méthodes
window.flash = function (message, type = 'success') {
    window.notify.show(message, type);
};
