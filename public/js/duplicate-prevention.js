/**
 * Duplicate Prevention Utilities for Frontend
 * Prevents double-clicks, multiple form submissions, and concurrent AJAX requests
 */

class DuplicatePreventionManager {
    constructor() {
        this.submittingForms = new Set();
        this.pendingRequests = new Map();
        this.disabledButtons = new Set();
        this.requestTimeouts = new Map();
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initialize());
        } else {
            this.initialize();
        }
    }

    /**
     * Initialize duplicate prevention
     */
    initialize() {
        this.setupFormProtection();
        this.setupButtonProtection();
        this.setupAjaxProtection();
        this.setupLinkProtection();
        
        console.log('Duplicate Prevention Manager initialized');
    }

    /**
     * Set up form submission protection
     */
    setupFormProtection() {
        // Protect all forms with critical operations
        const criticalForms = document.querySelectorAll(
            'form[action*="students"], form[action*="payments"], form[action*="receipts"], ' +
            'form[action*="marks"], form[action*="exam_records"], form.prevent-duplicate'
        );

        criticalForms.forEach(form => {
            this.protectForm(form);
        });

        // Also protect forms with specific classes
        document.querySelectorAll('form.duplicate-protected').forEach(form => {
            this.protectForm(form);
        });
    }

    /**
     * Protect a specific form from duplicate submissions
     */
    protectForm(form) {
        if (form.dataset.duplicateProtected) {
            return; // Already protected
        }

        form.dataset.duplicateProtected = 'true';

        form.addEventListener('submit', (e) => {
            const formId = this.getFormId(form);
            
            if (this.submittingForms.has(formId)) {
                e.preventDefault();
                this.showDuplicateMessage('Cette opération est déjà en cours...');
                return false;
            }

            // Mark form as submitting
            this.submittingForms.add(formId);
            this.disableFormButtons(form);
            this.showLoadingState(form);

            // Set timeout to re-enable form in case of network issues
            setTimeout(() => {
                this.enableForm(form);
            }, 30000); // 30 seconds timeout
        });

        // Re-enable form if user navigates back
        window.addEventListener('pageshow', () => {
            this.enableForm(form);
        });
    }

    /**
     * Set up button protection against double-clicks
     */
    setupButtonProtection() {
        // Protect critical action buttons
        const criticalButtons = document.querySelectorAll(
            'button[type="submit"], input[type="submit"], .btn-primary, .btn-success, ' +
            '.btn-danger, button.prevent-duplicate'
        );

        criticalButtons.forEach(button => {
            this.protectButton(button);
        });
    }

    /**
     * Protect a button from double-clicks
     */
    protectButton(button) {
        if (button.dataset.duplicateProtected) {
            return;
        }

        button.dataset.duplicateProtected = 'true';
        const originalText = button.textContent || button.value;

        button.addEventListener('click', (e) => {
            const buttonId = this.getButtonId(button);

            if (this.disabledButtons.has(buttonId)) {
                e.preventDefault();
                return false;
            }

            // Disable button temporarily
            this.disabledButtons.add(buttonId);
            button.disabled = true;
            
            // Update button text to show loading state
            if (button.textContent) {
                button.textContent = 'Traitement en cours...';
            } else {
                button.value = 'Traitement en cours...';
            }

            // Re-enable after delay
            setTimeout(() => {
                this.enableButton(button, originalText);
            }, 2000);
        });
    }

    /**
     * Set up AJAX request protection
     */
    setupAjaxProtection() {
        // Override jQuery AJAX if available
        if (window.jQuery) {
            this.protectJQueryAjax();
        }

        // Override fetch API
        this.protectFetchAPI();

        // Override XMLHttpRequest
        this.protectXMLHttpRequest();
    }

    /**
     * Protect jQuery AJAX requests
     */
    protectJQueryAjax() {
        const originalAjax = jQuery.ajax;
        const manager = this;

        jQuery.ajax = function(options) {
            const requestKey = manager.generateRequestKey(options);

            if (manager.pendingRequests.has(requestKey)) {
                console.warn('Duplicate AJAX request prevented:', requestKey);
                return manager.pendingRequests.get(requestKey);
            }

            // Create the request
            const xhr = originalAjax.call(this, options);

            // Track the request
            manager.pendingRequests.set(requestKey, xhr);

            // Clean up when done
            xhr.always(() => {
                manager.pendingRequests.delete(requestKey);
            });

            return xhr;
        };
    }

    /**
     * Protect Fetch API
     */
    protectFetchAPI() {
        const originalFetch = window.fetch;
        const manager = this;

        window.fetch = function(url, options = {}) {
            const requestKey = manager.generateRequestKey({ url, ...options });

            if (manager.pendingRequests.has(requestKey)) {
                console.warn('Duplicate fetch request prevented:', requestKey);
                return manager.pendingRequests.get(requestKey);
            }

            // Create the request
            const promise = originalFetch.call(this, url, options);

            // Track the request
            manager.pendingRequests.set(requestKey, promise);

            // Clean up when done
            promise.finally(() => {
                manager.pendingRequests.delete(requestKey);
            });

            return promise;
        };
    }

    /**
     * Protect XMLHttpRequest
     */
    protectXMLHttpRequest() {
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSend = XMLHttpRequest.prototype.send;
        const manager = this;

        XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
            this._duplicatePreventionKey = manager.generateRequestKey({ method, url });
            return originalOpen.call(this, method, url, async, user, password);
        };

        XMLHttpRequest.prototype.send = function(data) {
            const requestKey = this._duplicatePreventionKey;

            if (requestKey && manager.pendingRequests.has(requestKey)) {
                console.warn('Duplicate XMLHttpRequest prevented:', requestKey);
                return;
            }

            if (requestKey) {
                manager.pendingRequests.set(requestKey, this);
            }

            const cleanup = () => {
                if (requestKey) {
                    manager.pendingRequests.delete(requestKey);
                }
            };

            this.addEventListener('load', cleanup);
            this.addEventListener('error', cleanup);
            this.addEventListener('abort', cleanup);

            return originalSend.call(this, data);
        };
    }

    /**
     * Set up link protection for critical actions
     */
    setupLinkProtection() {
        const criticalLinks = document.querySelectorAll(
            'a[href*="delete"], a[href*="remove"], a.btn-danger, a.critical-action'
        );

        criticalLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const linkId = this.getLinkId(link);

                if (this.disabledButtons.has(linkId)) {
                    e.preventDefault();
                    return false;
                }

                this.disabledButtons.add(linkId);
                
                setTimeout(() => {
                    this.disabledButtons.delete(linkId);
                }, 3000);
            });
        });
    }

    /**
     * Generate unique form ID
     */
    getFormId(form) {
        return form.id || form.action + '_' + form.method;
    }

    /**
     * Generate unique button ID
     */
    getButtonId(button) {
        return button.id || button.name || button.textContent || Math.random().toString(36);
    }

    /**
     * Generate unique link ID
     */
    getLinkId(link) {
        return link.id || link.href || Math.random().toString(36);
    }

    /**
     * Generate request key for deduplication
     */
    generateRequestKey(options) {
        const method = options.method || options.type || 'GET';
        const url = options.url || '';
        const data = JSON.stringify(options.data || {});
        
        return `${method}:${url}:${this.hashCode(data)}`;
    }

    /**
     * Generate hash code for string
     */
    hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32-bit integer
        }
        return hash;
    }

    /**
     * Disable all buttons in a form
     */
    disableFormButtons(form) {
        const buttons = form.querySelectorAll('button, input[type="submit"]');
        buttons.forEach(button => {
            button.disabled = true;
            button.dataset.originalText = button.textContent || button.value;
            
            if (button.textContent) {
                button.textContent = 'Traitement en cours...';
            } else {
                button.value = 'Traitement en cours...';
            }
        });
    }

    /**
     * Show loading state for form
     */
    showLoadingState(form) {
        // Add loading class
        form.classList.add('submitting');
        
        // Add loading overlay if one doesn't exist
        if (!form.querySelector('.loading-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <span>Traitement en cours...</span>
                </div>
            `;
            form.style.position = 'relative';
            form.appendChild(overlay);
        }
    }

    /**
     * Enable form after submission
     */
    enableForm(form) {
        const formId = this.getFormId(form);
        this.submittingForms.delete(formId);
        
        form.classList.remove('submitting');
        
        // Remove loading overlay
        const overlay = form.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }

        // Re-enable buttons
        const buttons = form.querySelectorAll('button, input[type="submit"]');
        buttons.forEach(button => {
            button.disabled = false;
            const originalText = button.dataset.originalText;
            if (originalText) {
                if (button.textContent !== undefined) {
                    button.textContent = originalText;
                } else {
                    button.value = originalText;
                }
            }
        });
    }

    /**
     * Enable a specific button
     */
    enableButton(button, originalText) {
        const buttonId = this.getButtonId(button);
        this.disabledButtons.delete(buttonId);
        
        button.disabled = false;
        if (button.textContent !== undefined) {
            button.textContent = originalText;
        } else {
            button.value = originalText;
        }
    }

    /**
     * Show duplicate message to user
     */
    showDuplicateMessage(message) {
        // Try to use existing notification system
        if (window.toastr) {
            toastr.warning(message);
        } else if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: message,
                timer: 3000
            });
        } else {
            // Fallback to alert
            alert(message);
        }
    }

    /**
     * Manual form protection (can be called from other scripts)
     */
    protectFormById(formId) {
        const form = document.getElementById(formId);
        if (form) {
            this.protectForm(form);
        }
    }

    /**
     * Manual button protection
     */
    protectButtonById(buttonId) {
        const button = document.getElementById(buttonId);
        if (button) {
            this.protectButton(button);
        }
    }

    /**
     * Clear all protections (useful for single-page applications)
     */
    clearProtections() {
        this.submittingForms.clear();
        this.pendingRequests.clear();
        this.disabledButtons.clear();
        
        // Clear timeouts
        this.requestTimeouts.forEach(timeout => clearTimeout(timeout));
        this.requestTimeouts.clear();
    }
}

// CSS for loading states
const duplicatePreventionCSS = `
<style>
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.submitting {
    pointer-events: none;
    opacity: 0.7;
}

button:disabled, input[type="submit"]:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
`;

// Inject CSS
document.head.insertAdjacentHTML('beforeend', duplicatePreventionCSS);

// Create global instance
window.DuplicatePreventionManager = DuplicatePreventionManager;
window.duplicatePreventionManager = new DuplicatePreventionManager();

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DuplicatePreventionManager;
}