/**
 * Dashboard Interactif
 * Permet la modification des données directement depuis le tableau de bord
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les fonctionnalités interactives
    initEditableStats();
    initEditableStatus();
    initCounterAnimations();
    initDashboardUpdates();
    
    console.log('✅ Dashboard interactif initialisé');
});

/**
 * Initialise les statistiques modifiables
 */
function initEditableStats() {
    // Sélectionner tous les éléments de statistiques modifiables
    const editableStats = document.querySelectorAll('.stat-editable');
    
    editableStats.forEach(stat => {
        // Ajouter un indicateur visuel pour montrer que c'est modifiable
        stat.classList.add('is-editable');
        
        // Ajouter l'événement de clic pour modifier
        stat.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Récupérer les informations de l'élément
            const currentValue = this.textContent;
            const statId = this.id;
            const statType = this.dataset.type || 'number';
            const statLabel = this.dataset.label || 'Valeur';
            
            // Créer la boîte de dialogue de modification
            showEditDialog(statId, currentValue, statType, statLabel, (newValue) => {
                // Mettre à jour la valeur avec animation
                updateStatWithAnimation(statId, newValue);
                
                // Envoyer la mise à jour au serveur (simulation)
                simulateServerUpdate(statId, newValue);
            });
        });
    });
    
    // Ajouter des styles pour les éléments modifiables
    addStyleIfNotExists('editable-styles', `
        .is-editable {
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .is-editable:hover {
            color: #2196F3;
        }
        
        .is-editable::after {
            content: '✏️';
            font-size: 0.7em;
            position: absolute;
            top: -5px;
            right: -15px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .is-editable:hover::after {
            opacity: 1;
        }
        
        .edit-dialog {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .edit-dialog-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            max-width: 90%;
        }
        
        .edit-dialog-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .edit-dialog-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }
        
        .edit-dialog-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        .edit-dialog-body {
            margin-bottom: 20px;
        }
        
        .edit-dialog-footer {
            display: flex;
            justify-content: flex-end;
        }
        
        .edit-dialog-footer button {
            margin-left: 10px;
        }
    `);
}

/**
 * Initialise les statuts modifiables
 */
function initEditableStatus() {
    // Sélectionner tous les éléments de statut modifiables
    const editableStatus = document.querySelectorAll('.status-editable');
    
    editableStatus.forEach(status => {
        // Ajouter un indicateur visuel pour montrer que c'est modifiable
        status.classList.add('is-editable');
        
        // Ajouter l'événement de clic pour modifier
        status.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Récupérer les informations de l'élément
            const currentValue = this.textContent;
            const statusId = this.id;
            const statusOptions = JSON.parse(this.dataset.options || '["Actif", "Inactif", "En attente"]');
            const statusLabel = this.dataset.label || 'Statut';
            
            // Créer la boîte de dialogue de modification avec options
            showStatusDialog(statusId, currentValue, statusOptions, statusLabel, (newValue) => {
                // Mettre à jour la valeur avec animation
                updateStatusWithAnimation(statusId, newValue);
                
                // Envoyer la mise à jour au serveur (simulation)
                simulateServerUpdate(statusId, newValue);
            });
        });
    });
}

/**
 * Initialise les animations de compteur pour les statistiques
 */
function initCounterAnimations() {
    // Sélectionner tous les éléments de compteur
    const counters = document.querySelectorAll('.counter-animated');
    
    counters.forEach(counter => {
        // Obtenir la valeur finale
        const finalValue = counter.textContent;
        
        // Vérifier si c'est un nombre ou un pourcentage
        if (/^[0-9]+(\.[0-9]+)?%?$/.test(finalValue)) {
            // Extraire le nombre
            const isPercentage = finalValue.includes('%');
            const numericValue = parseFloat(finalValue.replace('%', ''));
            
            // Commencer à zéro
            let startValue = 0;
            counter.textContent = isPercentage ? '0%' : '0';
            
            // Animer jusqu'à la valeur finale
            const duration = 1500; // 1.5 secondes
            const steps = 60;
            const increment = numericValue / steps;
            const stepTime = duration / steps;
            
            let currentStep = 0;
            
            const counterAnimation = setInterval(() => {
                currentStep++;
                startValue += increment;
                
                if (currentStep >= steps) {
                    clearInterval(counterAnimation);
                    counter.textContent = finalValue;
                } else {
                    counter.textContent = isPercentage 
                        ? Math.round(startValue) + '%' 
                        : Math.round(startValue);
                }
            }, stepTime);
        }
    });
}

/**
 * Initialise les mises à jour du tableau de bord
 */
function initDashboardUpdates() {
    // Fonction pour mettre à jour une section du tableau de bord
    window.updateDashboardSection = function(sectionId, newContent) {
        const section = document.getElementById(sectionId);
        
        if (section) {
            // Ajouter une animation de sortie
            section.style.animation = 'section-fade-out 0.3s ease forwards';
            
            // Mettre à jour le contenu après l'animation de sortie
            setTimeout(() => {
                section.innerHTML = newContent;
                
                // Réinitialiser les fonctionnalités interactives
                initEditableStats();
                initEditableStatus();
                initCounterAnimations();
                
                // Ajouter une animation d'entrée
                section.style.animation = 'section-fade-in 0.3s ease forwards';
            }, 300);
            
            // Supprimer l'animation après qu'elle soit terminée
            setTimeout(() => {
                section.style.animation = '';
            }, 600);
        }
    };
    
    // Ajouter des styles pour les animations de section
    addStyleIfNotExists('section-animations', `
        @keyframes section-fade-out {
            0% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-10px); }
        }
        
        @keyframes section-fade-in {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    `);
}

/**
 * Affiche une boîte de dialogue pour modifier une valeur
 * @param {string} id - L'ID de l'élément à modifier
 * @param {string} currentValue - La valeur actuelle
 * @param {string} type - Le type de valeur (number, text, etc.)
 * @param {string} label - Le libellé de la valeur
 * @param {Function} callback - La fonction de rappel à exécuter après la modification
 */
function showEditDialog(id, currentValue, type, label, callback) {
    // Créer la boîte de dialogue
    const dialog = document.createElement('div');
    dialog.className = 'edit-dialog';
    
    // Nettoyer la valeur actuelle (enlever les espaces, les symboles de devise, etc.)
    let cleanValue = currentValue.replace(/[^\d.-]/g, '');
    
    dialog.innerHTML = `
        <div class="edit-dialog-content">
            <div class="edit-dialog-header">
                <h3 class="edit-dialog-title">Modifier ${label}</h3>
                <button type="button" class="edit-dialog-close">&times;</button>
            </div>
            <div class="edit-dialog-body">
                <div class="form-group">
                    <label for="edit-value">Nouvelle valeur :</label>
                    <input type="${type === 'number' ? 'number' : 'text'}" class="form-control" id="edit-value" value="${cleanValue}">
                </div>
            </div>
            <div class="edit-dialog-footer">
                <button type="button" class="btn btn-secondary" id="edit-cancel">Annuler</button>
                <button type="button" class="btn btn-primary" id="edit-save">Enregistrer</button>
            </div>
        </div>
    `;
    
    // Ajouter la boîte de dialogue au document
    document.body.appendChild(dialog);
    
    // Mettre le focus sur le champ de saisie
    setTimeout(() => {
        document.getElementById('edit-value').focus();
    }, 100);
    
    // Gérer les événements
    document.getElementById('edit-cancel').addEventListener('click', () => {
        document.body.removeChild(dialog);
    });
    
    document.querySelector('.edit-dialog-close').addEventListener('click', () => {
        document.body.removeChild(dialog);
    });
    
    document.getElementById('edit-save').addEventListener('click', () => {
        const newValue = document.getElementById('edit-value').value;
        document.body.removeChild(dialog);
        
        if (callback && typeof callback === 'function') {
            callback(newValue);
        }
    });
    
    // Permettre de fermer la boîte de dialogue en appuyant sur Échap
    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
            document.body.removeChild(dialog);
            document.removeEventListener('keydown', escHandler);
        }
    });
}

/**
 * Affiche une boîte de dialogue pour modifier un statut
 * @param {string} id - L'ID de l'élément à modifier
 * @param {string} currentValue - La valeur actuelle
 * @param {Array} options - Les options disponibles
 * @param {string} label - Le libellé du statut
 * @param {Function} callback - La fonction de rappel à exécuter après la modification
 */
function showStatusDialog(id, currentValue, options, label, callback) {
    // Créer la boîte de dialogue
    const dialog = document.createElement('div');
    dialog.className = 'edit-dialog';
    
    // Créer les options du sélecteur
    const optionsHtml = options.map(option => 
        `<option value="${option}" ${option === currentValue ? 'selected' : ''}>${option}</option>`
    ).join('');
    
    dialog.innerHTML = `
        <div class="edit-dialog-content">
            <div class="edit-dialog-header">
                <h3 class="edit-dialog-title">Modifier ${label}</h3>
                <button type="button" class="edit-dialog-close">&times;</button>
            </div>
            <div class="edit-dialog-body">
                <div class="form-group">
                    <label for="edit-status">Nouveau statut :</label>
                    <select class="form-control" id="edit-status">
                        ${optionsHtml}
                    </select>
                </div>
            </div>
            <div class="edit-dialog-footer">
                <button type="button" class="btn btn-secondary" id="edit-cancel">Annuler</button>
                <button type="button" class="btn btn-primary" id="edit-save">Enregistrer</button>
            </div>
        </div>
    `;
    
    // Ajouter la boîte de dialogue au document
    document.body.appendChild(dialog);
    
    // Gérer les événements
    document.getElementById('edit-cancel').addEventListener('click', () => {
        document.body.removeChild(dialog);
    });
    
    document.querySelector('.edit-dialog-close').addEventListener('click', () => {
        document.body.removeChild(dialog);
    });
    
    document.getElementById('edit-save').addEventListener('click', () => {
        const newValue = document.getElementById('edit-status').value;
        document.body.removeChild(dialog);
        
        if (callback && typeof callback === 'function') {
            callback(newValue);
        }
    });
    
    // Permettre de fermer la boîte de dialogue en appuyant sur Échap
    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
            document.body.removeChild(dialog);
            document.removeEventListener('keydown', escHandler);
        }
    });
}

/**
 * Met à jour une statistique avec animation
 * @param {string} id - L'ID de l'élément à mettre à jour
 * @param {string} newValue - La nouvelle valeur
 */
function updateStatWithAnimation(id, newValue) {
    const element = document.getElementById(id);
    
    if (element) {
        // Formater la nouvelle valeur si nécessaire
        let formattedValue = newValue;
        
        // Si l'élément a un attribut data-format, l'utiliser pour formater la valeur
        if (element.dataset.format) {
            switch (element.dataset.format) {
                case 'number':
                    formattedValue = parseInt(newValue).toLocaleString();
                    break;
                case 'currency':
                    formattedValue = parseInt(newValue).toLocaleString() + ' Ar';
                    break;
                case 'percentage':
                    formattedValue = newValue + '%';
                    break;
            }
        }
        
        // Ajouter une animation de mise à jour
        element.style.animation = 'value-update 0.5s ease';
        
        // Mettre à jour la valeur après un court délai
        setTimeout(() => {
            element.textContent = formattedValue;
        }, 250);
        
        // Supprimer l'animation après qu'elle soit terminée
        setTimeout(() => {
            element.style.animation = '';
        }, 500);
        
        // Afficher une notification de mise à jour
        showUpdateNotification(`${element.dataset.label || 'Valeur'} mise à jour avec succès.`);
    }
}

/**
 * Met à jour un statut avec animation
 * @param {string} id - L'ID de l'élément à mettre à jour
 * @param {string} newValue - La nouvelle valeur
 */
function updateStatusWithAnimation(id, newValue) {
    const element = document.getElementById(id);
    
    if (element) {
        // Ajouter une animation de mise à jour
        element.style.animation = 'value-update 0.5s ease';
        
        // Mettre à jour la valeur après un court délai
        setTimeout(() => {
            element.textContent = newValue;
            
            // Mettre à jour les classes en fonction du nouveau statut
            updateStatusClasses(element, newValue);
        }, 250);
        
        // Supprimer l'animation après qu'elle soit terminée
        setTimeout(() => {
            element.style.animation = '';
        }, 500);
        
        // Afficher une notification de mise à jour
        showUpdateNotification(`${element.dataset.label || 'Statut'} mis à jour avec succès.`);
    }
}

/**
 * Met à jour les classes d'un élément de statut en fonction de sa valeur
 * @param {HTMLElement} element - L'élément à mettre à jour
 * @param {string} status - Le nouveau statut
 */
function updateStatusClasses(element, status) {
    // Supprimer toutes les classes de statut existantes
    element.classList.remove('badge-success', 'badge-danger', 'badge-warning', 'badge-info', 'badge-secondary');
    
    // Ajouter la classe appropriée en fonction du statut
    switch (status.toLowerCase()) {
        case 'actif':
            element.classList.add('badge-success');
            break;
        case 'inactif':
            element.classList.add('badge-danger');
            break;
        case 'en attente':
            element.classList.add('badge-warning');
            break;
        case 'suspendu':
            element.classList.add('badge-info');
            break;
        default:
            element.classList.add('badge-secondary');
    }
}

/**
 * Simule une mise à jour côté serveur
 * @param {string} id - L'ID de l'élément mis à jour
 * @param {string} value - La nouvelle valeur
 */
function simulateServerUpdate(id, value) {
    console.log(`Mise à jour de ${id} avec la valeur ${value} envoyée au serveur.`);
    
    // Simuler un délai de traitement
    setTimeout(() => {
        console.log(`Mise à jour de ${id} traitée avec succès par le serveur.`);
    }, 500);
}

/**
 * Affiche une notification de mise à jour
 * @param {string} message - Le message à afficher
 * @param {string} type - Le type de notification (success, warning, error, info)
 */
function showUpdateNotification(message, type = 'success') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `update-notification notification-${type}`;
    
    // Définir l'icône en fonction du type
    let icon = '';
    switch (type) {
        case 'success':
            icon = 'icon-checkmark';
            break;
        case 'warning':
            icon = 'icon-warning';
            break;
        case 'error':
            icon = 'icon-cross';
            break;
        case 'info':
            icon = 'icon-info';
            break;
    }
    
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="${icon}"></i>
        </div>
        <div class="notification-content">
            <p>${message}</p>
        </div>
    `;
    
    // Ajouter la notification au document
    document.body.appendChild(notification);
    
    // Ajouter une animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 10);
    
    // Supprimer la notification après un délai
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        // Supprimer l'élément après l'animation
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}

/**
 * Ajoute une feuille de style si elle n'existe pas déjà
 * @param {string} id - L'ID de la feuille de style
 * @param {string} css - Le contenu CSS
 */
function addStyleIfNotExists(id, css) {
    if (!document.getElementById(id)) {
        const style = document.createElement('style');
        style.id = id;
        style.textContent = css;
        document.head.appendChild(style);
    }
}

// Ajouter des styles pour les notifications
addStyleIfNotExists('notification-styles', `
    .update-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        padding: 15px;
        display: flex;
        align-items: center;
        z-index: 9999;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
        max-width: 300px;
    }
    
    .notification-icon {
        margin-right: 15px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .notification-success .notification-icon {
        background: #28a745;
    }
    
    .notification-warning .notification-icon {
        background: #ffc107;
    }
    
    .notification-error .notification-icon {
        background: #dc3545;
    }
    
    .notification-info .notification-icon {
        background: #17a2b8;
    }
    
    .notification-content p {
        margin: 0;
        font-size: 14px;
    }
    
    @keyframes value-update {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1); }
    }
`);