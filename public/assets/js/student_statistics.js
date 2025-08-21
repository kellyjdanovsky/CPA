/**
 * Gestionnaire des statistiques détaillées des étudiants
 */

class StudentStatistics {
    constructor() {
        this.currentClassId = 'all';
        this.isLoading = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initAdvancedHoverEffects();
        this.addParticleEffect();
        this.loadStatistics();
    }

    bindEvents() {
        // Changement de classe
        $('#class-selector').on('change', (e) => {
            this.currentClassId = e.target.value;
            this.loadStatistics();
        });

        // Bouton de rafraîchissement
        $('#refresh-stats').on('click', () => {
            this.loadStatistics();
        });

        // Bouton d'export
        $('#export-stats').on('click', () => {
            this.exportStatistics();
        });
    }

    loadStatistics() {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoading();

        $.ajax({
            url: '/students/statistics/detailed',
            method: 'GET',
            data: {
                class_id: this.currentClassId
            },
            success: (response) => {
                if (response.success) {
                    this.renderStatistics(response.data);
                } else {
                    this.showError('Erreur lors du chargement des statistiques');
                }
            },
            error: (xhr) => {
                console.error('Erreur AJAX:', xhr);
                this.showError('Erreur de connexion lors du chargement des statistiques');
            },
            complete: () => {
                this.isLoading = false;
                this.hideLoading();
            }
        });
    }

    renderStatistics(data) {
        // Mettre à jour les informations générales
        this.updateGeneralInfo(data);

        // Mettre à jour les cartes de statistiques
        this.updateStatisticsCards(data.statistics);
    }

    updateGeneralInfo(data) {
        // Animation du compteur pour le total des étudiants
        this.animateCounter('#total-students-count .counter', data.total_students);
        $('#selected-class-name').text(data.class_name);
        $('#stats-generation-time').text(new Date().toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }));
    }

    animateCounter(selector, target) {
        const $element = $(selector);
        const start = 0;
        const duration = 1500;
        const startTime = performance.now();

        const updateCounter = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Utiliser une fonction d'easing pour un effet plus fluide
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(start + (target - start) * easeOutQuart);

            $element.text(current);

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                $element.text(target);
            }
        };

        requestAnimationFrame(updateCounter);
    }

    updateStatisticsCards(statistics) {
        const cardConfigs = [
            { key: 'genre', title: 'Répartition par Genre', class: 'genre' },
            { key: 'statut', title: 'Répartition par Statut', class: 'statut' },
            { key: 'type_etudiant', title: 'Répartition par Type d\'Étudiant', class: 'type-etudiant' },
            { key: 'statut_academique', title: 'Répartition par Statut Académique', class: 'statut-academique' },
            { key: 'religion', title: 'Répartition par Religion', class: 'religion' },
            { key: 'tranche_age', title: 'Répartition par Tranche d\'Âge', class: 'tranche-age' }
        ];

        const container = $('#statistics-cards-container');
        container.empty();

        cardConfigs.forEach((config, index) => {
            const cardHtml = this.createStatCard(config, statistics[config.key]);
            container.append(cardHtml);
        });

        // Animer l'apparition des cartes avec des effets plus fluides
        setTimeout(() => {
            this.animateCardsEntrance();
        }, 200);
    }

    createStatCard(config, data) {
        let tableRows = '';

        data.forEach((item, index) => {
            const progressWidth = Math.max(item.percentage, 2); // Minimum 2% pour la visibilité

            tableRows += `
                <tr data-aos="fade-up" data-aos-delay="${index * 50}">
                    <td class="category-column">
                        <i class="fas fa-circle" style="color: ${this.getColorForIndex(index)}; margin-right: 8px;"></i>
                        ${item.category}
                    </td>
                    <td class="count-column">
                        <span class="count-badge">${item.count}</span>
                    </td>
                    <td class="percentage-column">
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: 0%" data-width="${progressWidth}%">
                                <span class="progress-text">${item.percentage}%</span>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        return `
            <div class="stat-card ${config.class}" data-aos="zoom-in" data-aos-duration="600">
                <div class="stat-card-header">
                    <i class="${this.getIconForType(config.class)}"></i>
                    ${config.title}
                </div>
                <div class="stat-card-body">
                    <table class="stat-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Nombre</th>
                                <th>Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    exportStatistics() {
        if (this.isLoading) {
            this.showNotification('Veuillez attendre la fin du chargement des statistiques', 'warning');
            return;
        }

        // Afficher un message de préparation
        this.showNotification('Préparation de l\'export en cours...', 'info');

        // Créer un lien de téléchargement
        const exportUrl = `/students/statistics/export?class_id=${this.currentClassId}`;
        
        // Créer un élément de téléchargement temporaire
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = `statistiques_etudiants_${this.currentClassId}_${new Date().toISOString().split('T')[0]}.xlsx`;
        
        // Déclencher le téléchargement
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Message de succès
        setTimeout(() => {
            this.showNotification('Export terminé avec succès!', 'success');
        }, 1000);
    }

    showLoading() {
        const container = $('#statistics-cards-container');
        container.addClass('loading-state');
        container.html(`
            <div class="loading-overlay">
                <div class="loading-spinner"></div>
            </div>
        `);

        // Ajouter un effet de transition fluide
        container.css('opacity', '0.7');
    }

    hideLoading() {
        const container = $('#statistics-cards-container');
        container.removeClass('loading-state');
        container.css('opacity', '1');
        $('.loading-overlay').fadeOut(300, function() {
            $(this).remove();
        });
    }

    showError(message) {
        $('#statistics-cards-container').html(`
            <div class="error-message">
                <i class="icon-warning"></i> ${message}
            </div>
        `);
    }

    showNotification(message, type = 'info') {
        if (typeof PNotify !== 'undefined') {
            new PNotify({
                text: message,
                type: type,
                delay: 3000
            });
        } else {
            // Fallback pour les navigateurs sans PNotify
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'warning' ? 'alert-warning' : 
                              type === 'error' ? 'alert-danger' : 'alert-info';
            
            const $alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `);
            
            $('body').append($alert);
            setTimeout(() => $alert.remove(), 3000);
        }
    }

    // Méthode pour mettre à jour les statistiques après modification des données
    updateAfterEdit() {
        // Attendre un peu pour que les modifications soient prises en compte
        setTimeout(() => {
            this.loadStatistics();
        }, 500);
    }

    // Méthode pour obtenir un résumé des statistiques
    getStatisticsSummary() {
        const cards = $('.stat-card');
        const summary = {};

        cards.each((index, card) => {
            const $card = $(card);
            const title = $card.find('.stat-card-header').text().trim();
            const rows = $card.find('tbody tr');
            
            summary[title] = [];
            
            rows.each((rowIndex, row) => {
                const $row = $(row);
                const category = $row.find('.category-column').text().trim();
                const count = parseInt($row.find('.count-column').text().trim());
                const percentage = parseFloat($row.find('.progress-text').text().replace('%', ''));
                
                summary[title].push({
                    category,
                    count,
                    percentage
                });
            });
        });

        return summary;
    }

    // Méthode pour filtrer les statistiques par seuil
    filterByThreshold(threshold = 5) {
        $('.stat-table tbody tr').each((index, row) => {
            const $row = $(row);
            const percentage = parseFloat($row.find('.progress-text').text().replace('%', ''));
            
            if (percentage < threshold) {
                $row.addClass('low-percentage').css('opacity', '0.6');
            } else {
                $row.removeClass('low-percentage').css('opacity', '1');
            }
        });
    }

    // Méthode pour réinitialiser les filtres
    resetFilters() {
        $('.stat-table tbody tr').removeClass('low-percentage').css('opacity', '1');
    }

    // Méthodes utilitaires pour l'amélioration visuelle
    getIconForType(type) {
        const icons = {
            'genre': 'fas fa-users',
            'statut': 'fas fa-flag',
            'type-etudiant': 'fas fa-graduation-cap',
            'statut-academique': 'fas fa-trophy',
            'religion': 'fas fa-pray',
            'tranche-age': 'fas fa-child'
        };
        return icons[type] || 'fas fa-chart-bar';
    }

    getColorForIndex(index) {
        const colors = [
            '#667eea', '#764ba2', '#ff6b6b', '#ee5a24',
            '#4834d4', '#686de0', '#00d2d3', '#54a0ff',
            '#ff9ff3', '#f368e0', '#feca57', '#48dbfb'
        ];
        return colors[index % colors.length];
    }

    animateCardsEntrance() {
        $('.stat-card').each((index, element) => {
            const $card = $(element);

            setTimeout(() => {
                $card.addClass('visible');

                // Animer les barres de progression
                setTimeout(() => {
                    this.animateProgressBars($card);
                }, 300);

            }, index * 150);
        });
    }

    animateProgressBars($card) {
        $card.find('.progress-bar').each((index, bar) => {
            const $bar = $(bar);
            const targetWidth = $bar.data('width');

            setTimeout(() => {
                $bar.css('width', targetWidth);
            }, index * 100);
        });
    }

    // Méthode pour ajouter des effets de particules
    addParticleEffect() {
        if (!$('.particles-container').length) {
            $('.statistics-container').prepend('<div class="particles-container"></div>');

            for (let i = 0; i < 20; i++) {
                const particle = $('<div class="particle"></div>');
                particle.css({
                    left: Math.random() * 100 + '%',
                    animationDelay: Math.random() * 10 + 's',
                    animationDuration: (Math.random() * 10 + 10) + 's'
                });
                $('.particles-container').append(particle);
            }
        }
    }

    // Méthode pour les effets de hover avancés
    initAdvancedHoverEffects() {
        $(document).on('mouseenter', '.stat-card', function() {
            $(this).find('.stat-card-header').addClass('header-glow');
        });

        $(document).on('mouseleave', '.stat-card', function() {
            $(this).find('.stat-card-header').removeClass('header-glow');
        });

        $(document).on('mouseenter', '.progress-bar', function() {
            $(this).addClass('progress-pulse');
        });

        $(document).on('mouseleave', '.progress-bar', function() {
            $(this).removeClass('progress-pulse');
        });
    }
}

// Initialisation automatique
$(document).ready(() => {
    if ($('#statistics-detailed-container').length) {
        window.studentStatistics = new StudentStatistics();
        
        // Intégration avec le système d'édition en ligne
        if (window.inlineEditor) {
            // Écouter les événements de sauvegarde pour mettre à jour les statistiques
            $(document).on('inline-edit:save', () => {
                window.studentStatistics.updateAfterEdit();
            });
        }
    }
});
