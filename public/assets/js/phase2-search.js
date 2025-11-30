/**
 * Phase 2 - Global Search
 * Recherche universelle avec résultats groupés et navigation clavier
 */

class GlobalSearch {
    constructor(options = {}) {
        this.options = {
            searchUrl: '/api/search',
            minChars: 2,
            debounceDelay: 300,
            maxResults: 20,
            saveRecent: true,
            shortcutKey: 'k',
            ...options
        };

        this.searchInput = null;
        this.searchResults = null;
        this.backdrop = null;
        this.selectedIndex = -1;
        this.currentResults = [];
        this.recentSearches = this.loadRecentSearches();
        this.debounceTimer = null;

        this.init();
    }

    init() {
        this.createSearchUI();
        this.bindEvents();
        this.registerShortcut();
    }

    createSearchUI() {
        // Créer le conteneur principal
        const container = document.createElement('div');
        container.className = 'modern-global-search';
        container.innerHTML = `
            <div class="modern-search-input-container">
                <i class="modern-search-icon icon-search4"></i>
                <input 
                    type="text" 
                    class="modern-search-input" 
                    placeholder="Rechercher des élèves, enseignants, pages..."
                    autocomplete="off"
                >
                <div class="modern-search-shortcut">
                    <span class="modern-search-key">Ctrl</span>
                    <span class="modern-search-key">K</span>
                </div>
            </div>
            <div class="modern-search-results">
                <div class="modern-search-results-scroll"></div>
                <div class="modern-search-footer">
                    <span>Recherche rapide</span>
                    <div class="modern-search-footer-shortcuts">
                        <div class="modern-search-footer-shortcut">
                            <span class="modern-search-key">↑↓</span>
                            <span>Navigation</span>
                        </div>
                        <div class="modern-search-footer-shortcut">
                            <span class="modern-search-key">Enter</span>
                            <span>Sélectionner</span>
                        </div>
                        <div class="modern-search-footer-shortcut">
                            <span class="modern-search-key">Esc</span>
                            <span>Fermer</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Créer le backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modern-search-backdrop';

        // Ajouter au DOM
        document.body.appendChild(backdrop);

        // Trouver ou créer un conteneur dans la navbar
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            const navbarContent = navbar.querySelector('.navbar-collapse') || navbar;
            navbarContent.insertBefore(container, navbarContent.firstChild);
        } else {
            document.body.appendChild(container);
        }

        this.container = container;
        this.searchInput = container.querySelector('.modern-search-input');
        this.searchResults = container.querySelector('.modern-search-results');
        this.resultsScroll = container.querySelector('.modern-search-results-scroll');
        this.backdrop = backdrop;
    }

    bindEvents() {
        // Input events
        this.searchInput.addEventListener('input', (e) => this.handleInput(e.target.value));
        this.searchInput.addEventListener('focus', () => this.handleFocus());
        this.searchInput.addEventListener('blur', (e) => this.handleBlur(e));

        // Keyboard navigation
        this.searchInput.addEventListener('keydown', (e) => this.handleKeydown(e));

        // Backdrop click
        this.backdrop.addEventListener('click', () => this.close());
    }

    handleInput(query) {
        clearTimeout(this.debounceTimer);

        if (query.length < this.options.minChars) {
            this.showRecentSearches();
            return;
        }

        // Afficher loading
        this.showLoading();

        // Debounce search
        this.debounceTimer = setTimeout(() => {
            this.performSearch(query);
        }, this.options.debounceDelay);
    }

    handleFocus() {
        this.open();
        if (this.searchInput.value.length < this.options.minChars) {
            this.showRecentSearches();
        }
    }

    handleBlur(e) {
        // Ne pas fermer si le clic est dans les résultats
        if (e.relatedTarget && this.searchResults.contains(e.relatedTarget)) {
            return;
        }
        // Délai pour permettre le clic sur les résultats
        setTimeout(() => {
            if (!this.searchResults.matches(':hover')) {
                this.close();
            }
        }, 200);
    }

    handleKeydown(e) {
        const items = this.resultsScroll.querySelectorAll('.modern-search-item');

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.updateSelection(items);
                break;

            case 'ArrowUp':
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.updateSelection(items);
                break;

            case 'Enter':
                e.preventDefault();
                if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
                    items[this.selectedIndex].click();
                }
                break;

            case 'Escape':
                e.preventDefault();
                this.close();
                break;
        }
    }

    updateSelection(items) {
        items.forEach((item, index) => {
            item.classList.toggle('selected', index === this.selectedIndex);
            if (index === this.selectedIndex) {
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        });
    }

    async performSearch(query) {
        try {
            const response = await fetch(`${this.options.searchUrl}?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            this.currentResults = data;
            this.displayResults(data);

            // Sauvegarder dans les recherches récentes
            if (this.options.saveRecent && data.total > 0) {
                this.addRecentSearch(query);
            }
        } catch (error) {
            console.error('Erreur de recherche:', error);
            this.showError();
        }
    }

    displayResults(data) {
        this.selectedIndex = -1;

        if (!data || data.total === 0) {
            this.showEmpty();
            return;
        }

        let html = '';

        // Grouper les résultats par type
        const groups = {
            students: { title: 'Élèves', icon: 'students', items: data.students || [] },
            teachers: { title: 'Enseignants', icon: 'teachers', items: data.teachers || [] },
            classes: { title: 'Classes', icon: 'classes', items: data.classes || [] },
            pages: { title: 'Pages', icon: 'pages', items: data.pages || [] }
        };

        Object.entries(groups).forEach(([key, group]) => {
            if (group.items.length > 0) {
                html += this.renderGroup(group.title, group.icon, group.items);
            }
        });

        this.resultsScroll.innerHTML = html;
        this.searchResults.classList.add('active');

        // Bind click events
        this.resultsScroll.querySelectorAll('.modern-search-item').forEach(item => {
            item.addEventListener('click', () => {
                const url = item.dataset.url;
                if (url) {
                    window.location.href = url;
                }
            });
        });
    }

    renderGroup(title, icon, items) {
        return `
            <div class="modern-search-group">
                <div class="modern-search-group-title">
                    <span class="modern-search-group-icon ${icon}">
                        <i class="icon-${icon === 'students' ? 'users' : icon === 'teachers' ? 'user-tie' : icon === 'classes' ? 'graduation' : 'file-text'}"></i>
                    </span>
                    ${title}
                </div>
                ${items.map(item => this.renderItem(item)).join('')}
            </div>
        `;
    }

    renderItem(item) {
        const avatar = item.photo ?
            `<img src="${item.photo}" alt="${item.name}" class="modern-search-item-avatar">` :
            `<div class="modern-search-item-avatar placeholder">${item.name.charAt(0)}</div>`;

        return `
            <div class="modern-search-item" data-url="${item.url}" tabindex="0">
                ${avatar}
                <div class="modern-search-item-content">
                    <div class="modern-search-item-title">${this.highlightMatch(item.name, this.searchInput.value)}</div>
                    ${item.subtitle ? `<div class="modern-search-item-subtitle">${item.subtitle}</div>` : ''}
                </div>
                <div class="modern-search-item-action">
                    <i class="icon-arrow-right8"></i>
                </div>
            </div>
        `;
    }

    highlightMatch(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    showLoading() {
        this.resultsScroll.innerHTML = `
            <div class="modern-search-loading">
                <div class="modern-search-spinner"></div>
            </div>
        `;
        this.searchResults.classList.add('active');
    }

    showEmpty() {
        this.resultsScroll.innerHTML = `
            <div class="modern-search-empty">
                <div class="modern-search-empty-icon">
                    <i class="icon-search4"></i>
                </div>
                <div class="modern-search-empty-title">Aucun résultat</div>
                <div class="modern-search-empty-text">Essayez une autre recherche</div>
            </div>
        `;
    }

    showError() {
        this.resultsScroll.innerHTML = `
            <div class="modern-search-empty">
                <div class="modern-search-empty-icon">
                    <i class="icon-warning"></i>
                </div>
                <div class="modern-search-empty-title">Erreur</div>
                <div class="modern-search-empty-text">Une erreur est survenue</div>
            </div>
        `;
    }

    showRecentSearches() {
        if (this.recentSearches.length === 0) {
            this.resultsScroll.innerHTML = '';
            this.searchResults.classList.remove('active');
            return;
        }

        let html = `
            <div class="modern-recent-searches">
                <div class="modern-recent-title">Recherches récentes</div>
                ${this.recentSearches.map((search, index) => `
                    <div class="modern-recent-item" data-search="${search}">
                        <div class="modern-recent-text">
                            <i class="modern-recent-icon icon-history"></i>
                            <span>${search}</span>
                        </div>
                        <button class="modern-recent-remove" data-index="${index}">
                            <i class="icon-x"></i>
                        </button>
                    </div>
                `).join('')}
            </div>
        `;

        this.resultsScroll.innerHTML = html;
        this.searchResults.classList.add('active');

        // Bind events
        this.resultsScroll.querySelectorAll('.modern-recent-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (!e.target.closest('.modern-recent-remove')) {
                    this.searchInput.value = item.dataset.search;
                    this.handleInput(item.dataset.search);
                }
            });
        });

        this.resultsScroll.querySelectorAll('.modern-recent-remove').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.removeRecentSearch(parseInt(btn.dataset.index));
            });
        });
    }

    addRecentSearch(query) {
        // Éviter les doublons
        this.recentSearches = this.recentSearches.filter(s => s !== query);
        this.recentSearches.unshift(query);
        // Limiter à 5
        this.recentSearches = this.recentSearches.slice(0, 5);
        this.saveRecentSearches();
    }

    removeRecentSearch(index) {
        this.recentSearches.splice(index, 1);
        this.saveRecentSearches();
        this.showRecentSearches();
    }

    loadRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem('cpa-recent-searches') || '[]');
        } catch {
            return [];
        }
    }

    saveRecentSearches() {
        localStorage.setItem('cpa-recent-searches', JSON.stringify(this.recentSearches));
    }

    registerShortcut() {
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === this.options.shortcutKey) {
                e.preventDefault();
                this.toggle();
            }
        });
    }

    open() {
        this.backdrop.classList.add('active');
        this.searchResults.classList.add('active');
        this.searchInput.focus();
    }

    close() {
        this.backdrop.classList.remove('active');
        this.searchResults.classList.remove('active');
        this.searchInput.blur();
        this.selectedIndex = -1;
    }

    toggle() {
        if (this.backdrop.classList.contains('active')) {
            this.close();
        } else {
            this.open();
        }
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    window.globalSearch = new GlobalSearch({
        searchUrl: '/ajax/global-search'
    });
});

// Export
window.GlobalSearch = GlobalSearch;
