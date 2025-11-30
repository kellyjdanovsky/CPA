/**
 * Dark Mode Toggle & Persistence
 * Gère le basculement entre mode clair et sombre avec sauvegarde
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'cpa-dark-mode';
    const THEME_DARK = 'dark';
    const THEME_LIGHT = 'light';

    // Initialisation au chargement
    function initDarkMode() {
        const savedTheme = localStorage.getItem(STORAGE_KEY);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (prefersDark ? THEME_DARK : THEME_LIGHT);

        applyTheme(theme);
        updateToggleButton(theme);
    }

    // Appliquer le thème
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        document.body.classList.toggle('dark-mode', theme === THEME_DARK);
    }

    // Mettre à jour le bouton toggle
    function updateToggleButton(theme) {
        const toggleBtn = document.getElementById('dark-mode-toggle');
        if (!toggleBtn) return;

        const icon = toggleBtn.querySelector('i');
        const text = toggleBtn.querySelector('.toggle-text');

        if (theme === THEME_DARK) {
            icon.className = 'icon-sun2';
            if (text) text.textContent = 'Mode Clair';
            toggleBtn.setAttribute('title', 'Activer le mode clair');
        } else {
            icon.className = 'icon-moon';
            if (text) text.textContent = 'Mode Sombre';
            toggleBtn.setAttribute('title', 'Activer le mode sombre');
        }
    }

    // Basculer le thème
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === THEME_DARK ? THEME_LIGHT : THEME_DARK;

        applyTheme(newTheme);
        updateToggleButton(newTheme);
        localStorage.setItem(STORAGE_KEY, newTheme);

        // Animation smooth
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }

    // Écouter les changements de préférence système
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches ? THEME_DARK : THEME_LIGHT);
        }
    });

    // Exposer les fonctions globalement
    window.darkMode = {
        init: initDarkMode,
        toggle: toggleTheme,
        setTheme: function (theme) {
            applyTheme(theme);
            updateToggleButton(theme);
            localStorage.setItem(STORAGE_KEY, theme);
        },
        getTheme: function () {
            return document.documentElement.getAttribute('data-theme');
        }
    };

    // Auto-init si DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDarkMode);
    } else {
        initDarkMode();
    }
})();
