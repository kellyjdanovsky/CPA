/**
 * Modern Dashboard JavaScript
 * Interactions et animations pour le tableau de bord moderne
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Animation des compteurs
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-animated');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
            const duration = 2000; // 2 secondes
            const increment = target / (duration / 16); // 60 FPS
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                // Formater le nombre selon le type
                if (counter.textContent.includes('%')) {
                    counter.innerHTML = Math.floor(current) + '<span class="stat-unit">%</span>';
                } else {
                    counter.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        });
    }
    
    // Animation des barres de progression
    function animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    }
    
    // Effet parallax léger pour les formes flottantes
    function initParallax() {
        const shapes = document.querySelectorAll('.shape');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.3;
                shape.style.transform = `translateY(${rate * speed}px) rotate(${scrolled * 0.1}deg)`;
            });
        });
    }
    
    // Animation d'apparition des cartes
    function initScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Observer les cartes de statistiques
        document.querySelectorAll('.modern-stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
        
        // Observer les autres sections
        document.querySelectorAll('.modern-school-banner, .metric-card').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(element);
        });
    }
    
    // Effet hover interactif pour les cartes
    function initCardHoverEffects() {
        const cards = document.querySelectorAll('.modern-stat-card, .metric-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            });
        });
    }
    
    // Mise à jour de l'heure en temps réel
    function updateTime() {
        const timeElements = document.querySelectorAll('.stat-number');
        const now = new Date();
        
        // Mettre à jour la date dans le header
        const dateElement = document.querySelector('.stat-number');
        if (dateElement && dateElement.textContent.length <= 2) {
            dateElement.textContent = now.getDate();
        }
    }
    
    // Animation de typing pour le texte de bienvenue
    function initTypingAnimation() {
        const greetingText = document.querySelector('.greeting-text');
        if (greetingText) {
            const originalText = greetingText.textContent;
            greetingText.textContent = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < originalText.length) {
                    greetingText.textContent += originalText.charAt(i);
                    i++;
                    setTimeout(typeWriter, 50);
                }
            };
            
            setTimeout(typeWriter, 500);
        }
    }
    
    // Effet de particules pour le background
    function initParticleEffect() {
        const header = document.querySelector('.modern-dashboard-header');
        if (!header) return;
        
        // Créer des particules flottantes
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                pointer-events: none;
                animation: float-particle ${5 + Math.random() * 5}s linear infinite;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation-delay: ${Math.random() * 5}s;
            `;
            
            header.appendChild(particle);
        }
        
        // Ajouter l'animation CSS pour les particules
        if (!document.querySelector('#particle-styles')) {
            const style = document.createElement('style');
            style.id = 'particle-styles';
            style.textContent = `
                @keyframes float-particle {
                    0% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0; }
                    10% { opacity: 1; }
                    90% { opacity: 1; }
                    100% { transform: translateY(-100px) translateX(50px) rotate(360deg); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Gestion du thème sombre/clair
    function initThemeToggle() {
        // Détecter la préférence système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Récupérer le thème sauvegardé ou utiliser la préférence système
        const savedTheme = localStorage.getItem('dashboard-theme') || (prefersDark ? 'dark' : 'light');

        // Appliquer le thème initial
        document.documentElement.setAttribute('data-theme', savedTheme);

        // Créer le bouton de basculement
        const themeToggle = document.createElement('button');
        themeToggle.className = 'theme-toggle';
        themeToggle.setAttribute('aria-label', 'Basculer le thème');
        themeToggle.setAttribute('title', 'Changer le thème');

        // Fonction pour mettre à jour l'icône
        function updateIcon(theme) {
            themeToggle.innerHTML = theme === 'dark'
                ? '<i class="icon-sun"></i>'
                : '<i class="icon-moon"></i>';
        }

        updateIcon(savedTheme);

        // Gestionnaire de clic
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            // Appliquer le nouveau thème
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('dashboard-theme', newTheme);
            updateIcon(newTheme);

            // Animation de transition
            document.body.style.transition = 'background 0.3s ease, color 0.3s ease';
            setTimeout(() => {
                document.body.style.transition = '';
            }, 300);

            // Feedback visuel
            themeToggle.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
            }, 150);
        });

        // Ajouter le bouton au DOM
        document.body.appendChild(themeToggle);

        // Écouter les changements de préférence système
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('dashboard-theme')) {
                const newTheme = e.matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', newTheme);
                updateIcon(newTheme);
            }
        });
    }
    
    // Initialisation de toutes les fonctionnalités
    function init() {
        // Délai pour permettre au DOM de se charger complètement
        setTimeout(() => {
            animateCounters();
            animateProgressBars();
            initScrollAnimations();
            initCardHoverEffects();
            initParallax();
            initTypingAnimation();
            initParticleEffect();
            initThemeToggle();
        }, 100);
        
        // Mise à jour périodique
        setInterval(updateTime, 60000); // Chaque minute
    }
    
    // Lancer l'initialisation
    init();
    
    // Réinitialiser les animations lors du redimensionnement
    window.addEventListener('resize', () => {
        setTimeout(() => {
            animateProgressBars();
        }, 300);
    });
    
    // Ajouter des effets de survol pour les badges
    document.querySelectorAll('.modern-badge, .info-badge').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Console log pour confirmer le chargement
    console.log('🎨 Modern Dashboard initialized successfully!');
});
