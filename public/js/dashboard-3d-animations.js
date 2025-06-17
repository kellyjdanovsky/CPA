/**
 * Dashboard 3D Animations
 * Animations élégantes pour les éléments du tableau de bord
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les animations
    initStatisticsAnimations();
    initChartAnimations();
    initTableAnimations();
    initCardAnimations();
    initStatusAnimations();
    
    // Ajouter des écouteurs pour les mises à jour dynamiques
    setupDynamicUpdates();
    
    console.log('✨ Animations 3D du tableau de bord initialisées');
});

/**
 * Anime les statistiques et chiffres avec des effets 3D
 */
function initStatisticsAnimations() {
    // Sélectionner tous les éléments de statistiques
    const statElements = document.querySelectorAll('.stat-value, .kpi-value, .metric-value');
    
    statElements.forEach(element => {
        // Ajouter une classe pour le style
        element.classList.add('stat-3d');
        
        // Stocker la valeur originale
        const originalValue = element.textContent;
        
        // Vérifier si c'est un nombre ou un pourcentage
        if (/^[0-9]+(\.[0-9]+)?%?$/.test(originalValue)) {
            // Extraire le nombre
            const isPercentage = originalValue.includes('%');
            const numericValue = parseFloat(originalValue.replace('%', ''));
            
            // Commencer à zéro
            let startValue = 0;
            element.textContent = isPercentage ? '0%' : '0';
            
            // Animer jusqu'à la valeur finale
            const duration = 2000; // 2 secondes
            const steps = 60;
            const increment = numericValue / steps;
            const stepTime = duration / steps;
            
            let currentStep = 0;
            
            const counter = setInterval(() => {
                currentStep++;
                startValue += increment;
                
                if (currentStep >= steps) {
                    clearInterval(counter);
                    element.textContent = originalValue;
                } else {
                    element.textContent = isPercentage 
                        ? Math.round(startValue) + '%' 
                        : Math.round(startValue);
                }
            }, stepTime);
        }
        
        // Ajouter des effets 3D au survol
        element.addEventListener('mouseover', function() {
            this.style.transform = 'perspective(500px) translateZ(30px)';
            this.style.textShadow = '0 10px 20px rgba(0,0,0,0.2)';
        });
        
        element.addEventListener('mouseout', function() {
            this.style.transform = 'perspective(500px) translateZ(0)';
            this.style.textShadow = 'none';
        });
    });
    
    // Ajouter des styles CSS pour les statistiques
    addStyleIfNotExists('statistics-styles', `
        .stat-3d {
            transition: all 0.3s ease;
            display: inline-block;
            transform-style: preserve-3d;
        }
        
        .stat-value, .kpi-value, .metric-value {
            position: relative;
        }
        
        .stat-value:after, .kpi-value:after, .metric-value:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, currentColor, transparent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-value:hover:after, .kpi-value:hover:after, .metric-value:hover:after {
            transform: scaleX(1);
        }
    `);
}

/**
 * Anime les graphiques avec des effets 3D
 */
function initChartAnimations() {
    // Sélectionner tous les conteneurs de graphiques
    const chartContainers = document.querySelectorAll('.chart-container, .chart-body, [id*="chart"]');
    
    chartContainers.forEach(container => {
        // Ajouter une classe pour le style
        container.classList.add('chart-3d');
        
        // Ajouter un effet de profondeur au survol
        container.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Calculer la rotation en fonction de la position de la souris
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 30;
            const rotateY = (centerX - x) / 30;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
            
            // Ajouter un effet de lumière
            const shine = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 80%)`;
            this.style.backgroundImage = shine;
        });
        
        // Réinitialiser la transformation lorsque la souris quitte le graphique
        container.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
            this.style.backgroundImage = 'none';
        });
    });
    
    // Ajouter des styles CSS pour les graphiques
    addStyleIfNotExists('chart-styles', `
        .chart-3d {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .chart-3d canvas {
            transition: all 0.3s ease;
        }
        
        .chart-3d:hover canvas {
            transform: translateZ(20px);
        }
    `);
}

/**
 * Anime les tableaux de données avec des effets 3D
 */
function initTableAnimations() {
    // Sélectionner tous les tableaux
    const tables = document.querySelectorAll('.table');
    
    tables.forEach(table => {
        // Ajouter une classe pour le style
        table.classList.add('table-3d');
        
        // Animer les en-têtes de tableau
        const headers = table.querySelectorAll('th');
        headers.forEach(header => {
            header.classList.add('th-3d');
            
            header.addEventListener('mouseover', function() {
                this.style.transform = 'translateZ(10px)';
                this.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
            });
            
            header.addEventListener('mouseout', function() {
                this.style.transform = 'translateZ(0)';
                this.style.backgroundColor = '';
            });
        });
        
        // Animer les lignes du tableau
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.classList.add('tr-3d');
            
            row.addEventListener('mouseover', function() {
                this.style.transform = 'scale(1.02) translateX(5px)';
                this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                this.style.zIndex = '1';
                this.style.position = 'relative';
                this.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
            });
            
            row.addEventListener('mouseout', function() {
                this.style.transform = 'scale(1) translateX(0)';
                this.style.boxShadow = 'none';
                this.style.zIndex = '0';
                this.style.backgroundColor = '';
            });
        });
        
        // Animer les cellules du tableau
        const cells = table.querySelectorAll('td');
        cells.forEach(cell => {
            cell.classList.add('td-3d');
            
            cell.addEventListener('mouseover', function() {
                this.style.transform = 'translateZ(5px)';
            });
            
            cell.addEventListener('mouseout', function() {
                this.style.transform = 'translateZ(0)';
            });
        });
    });
    
    // Ajouter des styles CSS pour les tableaux
    addStyleIfNotExists('table-styles', `
        .table-3d {
            border-collapse: separate;
            border-spacing: 0;
            perspective: 1000px;
            width: 100%;
            margin-bottom: 1rem;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .th-3d, .td-3d {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
            position: relative;
        }
        
        .tr-3d {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
    `);
}

/**
 * Anime les cartes et widgets avec des effets 3D
 */
function initCardAnimations() {
    // Sélectionner toutes les cartes
    const cards = document.querySelectorAll('.card, .dashboard-card, .kpi-card, .quick-stat-card, .info-card');
    
    cards.forEach(card => {
        // Ajouter une classe pour le style
        card.classList.add('card-3d');
        
        // Créer un effet de profondeur 3D au survol
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Calculer la rotation en fonction de la position de la souris
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
            
            // Effet de profondeur pour les éléments internes
            const cardHeader = this.querySelector('.card-header');
            const cardBody = this.querySelector('.card-body');
            const cardFooter = this.querySelector('.card-footer');
            
            if (cardHeader) cardHeader.style.transform = 'translateZ(20px)';
            if (cardBody) cardBody.style.transform = 'translateZ(10px)';
            if (cardFooter) cardFooter.style.transform = 'translateZ(15px)';
        });
        
        // Réinitialiser la transformation lorsque la souris quitte la carte
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
            
            const cardHeader = this.querySelector('.card-header');
            const cardBody = this.querySelector('.card-body');
            const cardFooter = this.querySelector('.card-footer');
            
            if (cardHeader) cardHeader.style.transform = 'translateZ(0)';
            if (cardBody) cardBody.style.transform = 'translateZ(0)';
            if (cardFooter) cardFooter.style.transform = 'translateZ(0)';
        });
    });
    
    // Ajouter des styles CSS pour les cartes
    addStyleIfNotExists('card-styles', `
        .card-3d {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
            perspective: 1000px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .card-3d .card-header,
        .card-3d .card-body,
        .card-3d .card-footer {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
    `);
}

/**
 * Anime les badges de statut avec des effets 3D
 */
function initStatusAnimations() {
    // Sélectionner tous les badges
    const badges = document.querySelectorAll('.badge');
    
    badges.forEach(badge => {
        // Ajouter une classe pour le style
        badge.classList.add('badge-3d');
        
        // Ajouter des événements de survol pour l'effet 3D
        badge.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-5px) rotateX(10deg)';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
        });
        
        badge.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0) rotateX(0)';
            this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        });
    });
    
    // Ajouter des styles CSS pour les badges
    addStyleIfNotExists('badge-styles', `
        .badge-3d {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
            perspective: 1000px;
            position: relative;
            overflow: hidden;
        }
        
        .badge-3d::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            20%, 100% { transform: translateX(100%) rotate(45deg); }
        }
    `);
}

/**
 * Configure les mises à jour dynamiques pour le tableau de bord
 */
function setupDynamicUpdates() {
    // Fonction pour mettre à jour une valeur avec animation
    window.updateDashboardValue = function(selector, newValue) {
        const element = document.querySelector(selector);
        
        if (element) {
            // Sauvegarder l'ancienne valeur
            const oldValue = element.textContent;
            
            // Créer une animation de transition
            element.style.animation = 'value-update 0.5s ease';
            
            // Mettre à jour la valeur après un court délai
            setTimeout(() => {
                element.textContent = newValue;
            }, 250);
            
            // Supprimer l'animation après qu'elle soit terminée
            setTimeout(() => {
                element.style.animation = '';
            }, 500);
            
            // Retourner l'ancienne valeur pour référence
            return oldValue;
        }
        
        return null;
    };
    
    // Fonction pour mettre à jour une section entière
    window.updateDashboardSection = function(sectionId, newContent) {
        const section = document.getElementById(sectionId);
        
        if (section) {
            // Ajouter une animation de sortie
            section.style.animation = 'section-exit 0.5s ease forwards';
            
            // Mettre à jour le contenu après l'animation de sortie
            setTimeout(() => {
                section.innerHTML = newContent;
                
                // Réinitialiser les animations pour les nouveaux éléments
                initStatisticsAnimations();
                initChartAnimations();
                initTableAnimations();
                initCardAnimations();
                initStatusAnimations();
                
                // Ajouter une animation d'entrée
                section.style.animation = 'section-enter 0.5s ease forwards';
            }, 500);
            
            // Supprimer l'animation après qu'elle soit terminée
            setTimeout(() => {
                section.style.animation = '';
            }, 1000);
        }
    };
    
    // Ajouter des styles CSS pour les animations de mise à jour
    addStyleIfNotExists('update-animations', `
        @keyframes value-update {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        @keyframes section-exit {
            0% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }
        
        @keyframes section-enter {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    `);
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