/**
 * ==========================================================================
 * CPA - Gestion du Mode Sombre/Clair
 * Toggle et persistance du thème
 * ==========================================================================
 */

(function () {
    'use strict';

    // ========== 1. CONFIGURATION ========== //
    const THEME_KEY = 'cpa-theme';
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    // ========== 2. DÉTECTION DU THÈME ========== //

    /**
     * Récupérer le thème actuel
     */
    function getCurrentTheme() {
        // 1. Vérifier le localStorage
        const savedTheme = localStorage.getItem(THEME_KEY);
        if (savedTheme) {
            return savedTheme;
        }

        // 2. Vérifier la préférence système
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return THEME_DARK;
        }

        // 3. Par défaut: mode clair
        return THEME_LIGHT;
    }

    /**
     * Appliquer un thème
     */
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);

        // Émettre un événement personnalisé
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));

        console.log(`%c🎨 Thème "${theme}" appliqué`, 'color: #6366f1; font-weight: bold;');
    }

    /**
     * Basculer entre les thèmes
     */
    function toggleTheme() {
        const currentTheme = getCurrentTheme();
        const newTheme = currentTheme === THEME_LIGHT ? THEME_DARK : THEME_LIGHT;
        applyTheme(newTheme);

        // Animation du toggle
        const toggleButton = document.querySelector('.theme-toggle');
        if (toggleButton) {
            toggleButton.classList.add('toggle-animation');
            setTimeout(() => {
                toggleButton.classList.remove('toggle-animation');
            }, 300);
        }

        // Notification
        if (typeof CPAModern !== 'undefined' && CPAModern.showToast) {
            const message = newTheme === THEME_DARK ?
                '🌙 Mode sombre activé' :
                '☀️ Mode clair activé';
            CPAModern.showToast(message, 'info', 2000);
        }
    }

    // ========== 3. INITIALISATION ========== //

    /**
     * Initialiser le thème au chargement de la page
     */
    function initTheme() {
        const theme = getCurrentTheme();
        applyTheme(theme);
    }

    /**
     * Créer le bouton de toggle
     */
    function createThemeToggle() {
        const toggle = document.createElement('div');
        toggle.className = 'theme-toggle';
        toggle.id = 'theme-toggle';
        toggle.setAttribute('role', 'button');
        toggle.setAttribute('aria-label', 'Changer de thème');
        toggle.innerHTML = `
            <span class="theme-toggle-icon sun">
                <i class="icon-sun"></i>
            </span>
            <div class="theme-toggle-switch"></div>
            <span class="theme-toggle-icon moon">
                <i class="icon-moon"></i>
            </span>
        `;

        toggle.addEventListener('click', toggleTheme);

        return toggle;
    }

    /**
     * Insérer le toggle dans le header
     */
    function insertThemeToggle() {
        // Chercher le conteneur approprié
        const headerElements = document.querySelector('.header-elements');

        if (headerElements) {
            const toggle = createThemeToggle();
            const firstChild = headerElements.querySelector('.d-flex, div');

            if (firstChild) {
                firstChild.insertBefore(toggle, firstChild.firstChild);
            } else {
                headerElements.appendChild(toggle);
            }
        } else {
            // Fallback: ajouter dans la navbar si le header-elements n'existe pas
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                const toggle = createThemeToggle();
                toggle.style.position = 'fixed';
                toggle.style.bottom = '20px';
                toggle.style.right = '20px';
                toggle.style.zIndex = '9999';
                toggle.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                document.body.appendChild(toggle);
            }
        }
    }

    // ========== 4. ÉCOUTE DES CHANGEMENTS SYSTÈME ========== //

    /**
     * Écouter les changements de préférence système
     */
    function watchSystemTheme() {
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

            darkModeQuery.addEventListener('change', (e) => {
                // Seulement si l'utilisateur n'a pas défini de préférence manuelle
                if (!localStorage.getItem(THEME_KEY)) {
                    const newTheme = e.matches ? THEME_DARK : THEME_LIGHT;
                    applyTheme(newTheme);
                }
            });
        }
    }

    // ========== 5. API PUBLIQUE ========== //

    window.ThemeManager = {
        /**
         * Obtenir le thème actuel
         */
        getTheme: function () {
            return getCurrentTheme();
        },

        /**
         * Définir un thème spécifique
         */
        setTheme: function (theme) {
            if (theme === THEME_LIGHT || theme === THEME_DARK) {
                applyTheme(theme);
            } else {
                console.error('Thème invalide. Utilisez "light" ou "dark".');
            }
        },

        /**
         * Basculer entre les thèmes
         */
        toggleTheme: toggleTheme,

        /**
         * Vérifier si le mode sombre est actif
         */
        isDark: function () {
            return getCurrentTheme() === THEME_DARK;
        },

        /**
         * Vérifier si le mode clair est actif
         */
        isLight: function () {
            return getCurrentTheme() === THEME_LIGHT;
        },

        /**
         * Réinitialiser au thème système
         */
        resetToSystem: function () {
            localStorage.removeItem(THEME_KEY);
            initTheme();
        }
    };

    // ========== 6. INITIALISATION AUTOMATIQUE ========== //

    // Appliquer le thème immédiatement (avant le DOM)
    initTheme();

    // Attendre le DOM pour insérer le toggle
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            insertThemeToggle();
            watchSystemTheme();
        });
    } else {
        insertThemeToggle();
        watchSystemTheme();
    }

    // ========== 7. RACCOURCI CLAVIER ========== //

    document.addEventListener('keydown', function (e) {
        // Ctrl + Shift + D pour toggle (ou Cmd + Shift + D sur Mac)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
            e.preventDefault();
            toggleTheme();
        }
    });

    // ========== 8. HELPERS POUR LES DÉVELOPPEURS ========== //

    /**
     * Logger l'état du thème dans la console
     */
    console.log(
        '%c🎨 Theme Manager chargé\n' +
        'Thème actuel: ' + getCurrentTheme() + '\n' +
        'Raccourci: Ctrl+Shift+D pour changer',
        'color: #6366f1; font-size: 12px;'
    );

})();
