/**
 * =============================================================================
 * CPA - Lazy Loading Helper (Phase 1 Optimisation)
 * Charge les images uniquement quand elles sont visibles
 * Gain attendu: 30-50% de réduction du poids initial de la page
 * =============================================================================
 */

(function () {
    'use strict';

    /**
     * Initialise le lazy loading pour toutes les images avec data-src
     */
    function initLazyLoad() {
        // Images avec l'attribut data-src
        const lazyImages = document.querySelectorAll('img[data-src]');

        // Vérifier si Intersection Observer est supporté
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        loadImage(img);
                        observer.unobserve(img);
                    }
                });
            }, {
                // Charger l'image 200px avant qu'elle n'entre dans le viewport
                rootMargin: '200px 0px',
                threshold: 0.01
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback pour les navigateurs anciens
            lazyImages.forEach(img => loadImage(img));
        }
    }

    /**
     * Charge une image lazy
     */
    function loadImage(img) {
        const src = img.dataset.src;
        const srcset = img.dataset.srcset;

        // Ajouter classe de chargement
        img.classList.add('lazy-loading');

        // Créer une nouvelle image pour précharger
        const tempImage = new Image();

        tempImage.onload = function () {
            img.src = src;
            if (srcset) {
                img.srcset = srcset;
            }
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');

            // Supprimer les attributs data-*
            delete img.dataset.src;
            if (srcset) delete img.dataset.srcset;
        };

        tempImage.onerror = function () {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-error');
            console.warn('Erreur de chargement de l\'image:', src);
        };

        tempImage.src = src;
    }

    /**
     * Helper pour convertir des images existantes en lazy
     */
    window.CPALazyLoad = {
        /**
         * Initialise le lazy loading
         */
        init: function () {
            initLazyLoad();
        },

        /**
         * Convertit une image normale en lazy
         * @param {HTMLImageElement} img - L'élément image
         */
        convertToLazy: function (img) {
            if (img.src && !img.dataset.src) {
                img.dataset.src = img.src;
                img.src = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 3 2\'%3E%3C/svg%3E';
                img.classList.add('lazy');
            }
        },

        /**
         * Initialise toutes les images d'un conteneur en lazy
         * @param {string} selector - Sélecteur CSS du conteneur
         */
        convertContainer: function (selector) {
            const container = document.querySelector(selector);
            if (container) {
                const images = container.querySelectorAll('img:not([data-src])');
                images.forEach(img => this.convertToLazy(img));
                this.init();
            }
        },

        /**
         * Charge immédiatement une image lazy
         * @param {HTMLImageElement} img - L'élément image à charger
         */
        loadNow: function (img) {
            if (img.dataset.src) {
                loadImage(img);
            }
        },

        /**
         * Recharge le lazy loading (après ajout dynamique d'images)
         */
        refresh: function () {
            this.init();
        }
    };

    // Auto-initialisation au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLazyLoad);
    } else {
        initLazyLoad();
    }

    // Réinitialiser après chargement de page AJAX
    window.addEventListener('load', function () {
        // Si jQuery est présent, écouter les événements AJAX
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('ajaxComplete', function () {
                initLazyLoad();
            });
        }
    });

})();

/**
 * =============================================================================
 * UTILISATION:
 * =============================================================================
 * 
 * HTML de base:
 * <img data-src="image.jpg" alt="Description" class="lazy">
 * 
 * Avec srcset responsive:
 * <img data-src="image.jpg" 
 *      data-srcset="image-small.jpg 400w, image-medium.jpg 800w, image-large.jpg 1200w"
 *      sizes="(max-width: 600px) 400px, (max-width: 1000px) 800px, 1200px"
 *      alt="Description" 
 *      class="lazy">
 * 
 * JavaScript:
 * 
 * // Convertir une image en lazy
 * CPALazyLoad.convertToLazy(document.querySelector('#myImage'));
 * 
 * // Convertir toutes les images d'un conteneur
 * CPALazyLoad.convertContainer('.gallery');
 * 
 * // Charger immédiatement une image
 * CPALazyLoad.loadNow(document.querySelector('.urgent-image'));
 * 
 * // Rafraîchir après ajout dynamique d'images
 * CPALazyLoad.refresh();
 * 
 * =============================================================================
 */
