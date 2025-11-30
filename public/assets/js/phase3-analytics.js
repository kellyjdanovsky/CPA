/**
 * Phase 3 - Analytics & Charts
 * Wrapper simplifié pour Chart.js avec widgets statistiques
 */

class AnalyticsDashboard {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        // Initialiser les animations de compteurs
        this.initCounterAnimations();

        // Initialiser les progress bars animées
        this.initProgressBars();
    }

    /**
     * Créer un graphique en ligne
     */
    createLineChart(canvasId, data, options = {}) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        };

        const config = {
            type: 'line',
            data: data,
            options: { ...defaultOptions, ...options }
        };

        if (typeof Chart !== 'undefined') {
            this.charts[canvasId] = new Chart(ctx, config);
            return this.charts[canvasId];
        } else {
            console.warn('Chart.js non chargé');
            return null;
        }
    }

    /**
     * Créer un graphique en barres
     */
    createBarChart(canvasId, data, options = {}) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        const config = {
            type: 'bar',
            data: data,
            options: { ...defaultOptions, ...options }
        };

        if (typeof Chart !== 'undefined') {
            this.charts[canvasId] = new Chart(ctx, config);
            return this.charts[canvasId];
        }
        return null;
    }

    /**
     * Créer un graphique circulaire (doughnut)
     */
    createDoughnutChart(canvasId, data, options = {}) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                }
            },
            cutout: '70%'
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: { ...defaultOptions, ...options }
        };

        if (typeof Chart !== 'undefined') {
            this.charts[canvasId] = new Chart(ctx, config);
            return this.charts[canvasId];
        }
        return null;
    }

    /**
     * Créer un graphique de progression
     */
    createProgressChart(canvasId, percentage, color = '#667eea') {
        const ctx = document.getElementById(canvasId);
        if (!ctx || typeof Chart === 'undefined') return null;

        const data = {
            datasets: [{
                data: [percentage, 100 - percentage],
                backgroundColor: [color, '#f3f4f6'],
                borderWidth: 0
            }]
        };

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '85%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        };

        this.charts[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options
        });

        return this.charts[canvasId];
    }

    /**
     * Mettre à jour un graphique
     */
    updateChart(canvasId, newData) {
        const chart = this.charts[canvasId];
        if (!chart) return;

        chart.data = newData;
        chart.update();
    }

    /**
     * Détruire un graphique
     */
    destroyChart(canvasId) {
        const chart = this.charts[canvasId];
        if (chart) {
            chart.destroy();
            delete this.charts[canvasId];
        }
    }

    /**
     * Animer les compteurs
     */
    initCounterAnimations() {
        const counters = document.querySelectorAll('.stats-widget-value, .metric-value');

        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            if (counter.dataset.animated !== 'true') {
                observer.observe(counter);
            }
        });
    }

    /**
     * Animer un compteur
     */
    animateCounter(element) {
        const target = parseInt(element.textContent.replace(/[^0-9]/g, ''));
        if (isNaN(target)) return;

        const duration = 1500;
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = this.formatNumber(target);
                clearInterval(timer);
                element.dataset.animated = 'true';
            } else {
                element.textContent = this.formatNumber(Math.floor(current));
            }
        }, 16);
    }

    /**
     * Formater un nombre
     */
    formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    /**
     * Initialiser les progress bars animées
     */
    initProgressBars() {
        const progressBars = document.querySelectorAll('.progress-widget-fill');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const width = entry.target.dataset.width || '0%';
                    setTimeout(() => {
                        entry.target.style.width = width;
                    }, 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        progressBars.forEach(bar => observer.observe(bar));
    }

    /**
     * Créer un widget de statistiques
     */
    createStatsWidget(container, stats) {
        const html = `
            <div class="stats-widget ${stats.variant || 'primary'}">
                <div class="stats-widget-header">
                    <div class="stats-widget-icon">
                        <i class="${stats.icon}"></i>
                    </div>
                    ${stats.trend ? `
                        <div class="stats-widget-trend ${stats.trend.direction}">
                            <i class="icon-${stats.trend.direction === 'up' ? 'arrow-up' : 'arrow-down'}8"></i>
                            ${stats.trend.value}
                        </div>
                    ` : ''}
                </div>
                <div class="stats-widget-body">
                    <div class="stats-widget-value" data-value="${stats.value}">
                        ${stats.value}
                    </div>
                    <div class="stats-widget-label">${stats.label}</div>
                </div>
                ${stats.footer ? `
                    <div class="stats-widget-footer">${stats.footer}</div>
                ` : ''}
            </div>
        `;

        if (typeof container === 'string') {
            document.querySelector(container).innerHTML = html;
        } else {
            container.innerHTML = html;
        }

        // Animer le compteur
        setTimeout(() => this.initCounterAnimations(), 100);
    }

    /**
     * Créer un timeline widget
     */
    createTimeline(container, events) {
        const html = `
            <div class="timeline-widget">
                ${events.map(event => `
                    <div class="timeline-item">
                        <div class="timeline-dot ${event.variant || 'primary'}">
                            <i class="${event.icon || 'icon-checkmark'}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">${event.title}</div>
                            <div class="timeline-text">${event.text}</div>
                        </div>
                        <div class="timeline-time">${event.time}</div>
                    </div>
                `).join('')}
            </div>
        `;

        if (typeof container === 'string') {
            document.querySelector(container).innerHTML = html;
        } else {
            container.innerHTML = html;
        }
    }

    /**
     * Créer un progress widget
     */
    createProgressWidget(container, items) {
        const html = `
            <div class="progress-widget">
                ${items.map(item => `
                    <div class="progress-widget-item ${item.variant || 'primary'}">
                        <div class="progress-widget-header">
                            <span class="progress-widget-label">${item.label}</span>
                            <span class="progress-widget-value">${item.value}%</span>
                        </div>
                        <div class="progress-widget-bar">
                            <div class="progress-widget-fill" data-width="${item.value}%"></div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        if (typeof container === 'string') {
            document.querySelector(container).innerHTML = html;
        } else {
            container.innerHTML = html;
        }

        // Animer les progress bars
        setTimeout(() => this.initProgressBars(), 100);
    }

    /**
     * Obtenir des couleurs de graphique prédéfinies
     */
    getChartColors() {
        return {
            primary: {
                background: 'rgba(102, 126, 234, 0.1)',
                border: 'rgb(102, 126, 234)'
            },
            success: {
                background: 'rgba(16, 185, 129, 0.1)',
                border: 'rgb(16, 185, 129)'
            },
            warning: {
                background: 'rgba(245, 158, 11, 0.1)',
                border: 'rgb(245, 158, 11)'
            },
            danger: {
                background: 'rgba(239, 68, 68, 0.1)',
                border: 'rgb(239, 68, 68)'
            },
            info: {
                background: 'rgba(59, 130, 246, 0.1)',
                border: 'rgb(59, 130, 246)'
            }
        };
    }

    /**
     * Créer un gradient pour Chart.js
     */
    createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    window.analytics = new AnalyticsDashboard();
});

// Export
window.AnalyticsDashboard = AnalyticsDashboard;
