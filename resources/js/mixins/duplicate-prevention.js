/**
 * Vue.js Mixin for Duplicate Prevention
 * Use this mixin in Vue components that need duplicate protection
 */

const DuplicatePreventionMixin = {
    data() {
        return {
            submitting: false,
            pendingRequests: new Set(),
            lastSubmissionTime: 0,
            minSubmissionInterval: 1000 // 1 second minimum between submissions
        };
    },

    methods: {
        /**
         * Prevent duplicate form submissions
         */
        async submitFormSafely(submitFunction, operationUuid = null) {
            if (this.submitting) {
                this.showDuplicateWarning('Opération déjà en cours...');
                return false;
            }

            // Check minimum interval
            const now = Date.now();
            if (now - this.lastSubmissionTime < this.minSubmissionInterval) {
                this.showDuplicateWarning('Veuillez patienter avant de soumettre à nouveau...');
                return false;
            }

            this.submitting = true;
            this.lastSubmissionTime = now;

            try {
                // Generate operation UUID if not provided
                if (!operationUuid) {
                    operationUuid = this.generateUuid();
                }

                // Add operation UUID to form data if it's an object
                if (typeof submitFunction === 'function') {
                    const result = await submitFunction(operationUuid);
                    return result;
                }

                return true;
            } catch (error) {
                console.error('Form submission error:', error);
                
                // Check if it's a duplicate error
                if (this.isDuplicateError(error)) {
                    this.showDuplicateWarning('Cette opération a déjà été effectuée.');
                } else {
                    throw error;
                }
                
                return false;
            } finally {
                this.submitting = false;
            }
        },

        /**
         * Make a safe AJAX request with duplicate prevention
         */
        async makeRequestSafely(requestConfig) {
            const requestKey = this.generateRequestKey(requestConfig);

            if (this.pendingRequests.has(requestKey)) {
                console.warn('Duplicate request prevented:', requestKey);
                return null;
            }

            this.pendingRequests.add(requestKey);

            try {
                // Use axios if available, fallback to fetch
                let response;
                if (this.$http) {
                    response = await this.$http(requestConfig);
                } else if (window.axios) {
                    response = await axios(requestConfig);
                } else {
                    response = await fetch(requestConfig.url, {
                        method: requestConfig.method || 'GET',
                        headers: requestConfig.headers,
                        body: requestConfig.data ? JSON.stringify(requestConfig.data) : undefined
                    });
                }

                return response;
            } catch (error) {
                if (this.isDuplicateError(error)) {
                    this.showDuplicateWarning('Requête dupliquée détectée.');
                } else {
                    throw error;
                }
            } finally {
                this.pendingRequests.delete(requestKey);
            }
        },

        /**
         * Safe button click handler
         */
        handleClickSafely(clickHandler, buttonRef = null) {
            return async (event) => {
                if (buttonRef && buttonRef.disabled) {
                    event.preventDefault();
                    return;
                }

                if (buttonRef) {
                    this.disableButton(buttonRef);
                }

                try {
                    await clickHandler(event);
                } catch (error) {
                    console.error('Click handler error:', error);
                    throw error;
                } finally {
                    if (buttonRef) {
                        setTimeout(() => this.enableButton(buttonRef), 1000);
                    }
                }
            };
        },

        /**
         * Generate a UUID for operation tracking
         */
        generateUuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },

        /**
         * Generate request key for deduplication
         */
        generateRequestKey(config) {
            const method = config.method || 'GET';
            const url = config.url || '';
            const data = JSON.stringify(config.data || {});
            return `${method}:${url}:${this.hashString(data)}`;
        },

        /**
         * Hash string for deduplication
         */
        hashString(str) {
            let hash = 0;
            if (str.length === 0) return hash;
            
            for (let i = 0; i < str.length; i++) {
                const char = str.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash;
            }
            return hash;
        },

        /**
         * Check if error is a duplicate error
         */
        isDuplicateError(error) {
            if (!error) return false;
            
            const message = error.message || error.toString();
            const status = error.status || error.response?.status;
            
            // Check for common duplicate error indicators
            return status === 409 || 
                   status === 423 ||
                   message.includes('duplicate') ||
                   message.includes('already exists') ||
                   message.includes('DUPLICATE_REQUEST') ||
                   message.includes('RESOURCE_LOCKED');
        },

        /**
         * Show duplicate warning to user
         */
        showDuplicateWarning(message) {
            // Try different notification methods
            if (this.$toast) {
                this.$toast.warning(message);
            } else if (this.$message) {
                this.$message.warning(message);
            } else if (this.$notify) {
                this.$notify({
                    type: 'warning',
                    title: 'Attention',
                    message: message
                });
            } else if (window.toastr) {
                toastr.warning(message);
            } else if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention',
                    text: message,
                    timer: 3000
                });
            } else {
                console.warn(message);
                alert(message);
            }
        },

        /**
         * Disable button temporarily
         */
        disableButton(buttonRef) {
            if (buttonRef) {
                buttonRef.disabled = true;
                buttonRef.originalText = buttonRef.textContent || buttonRef.innerText;
                buttonRef.textContent = 'Traitement...';
            }
        },

        /**
         * Re-enable button
         */
        enableButton(buttonRef) {
            if (buttonRef) {
                buttonRef.disabled = false;
                if (buttonRef.originalText) {
                    buttonRef.textContent = buttonRef.originalText;
                }
            }
        },

        /**
         * Create a debounced function
         */
        createDebouncedFunction(func, delay = 300) {
            let timeoutId;
            return (...args) => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        },

        /**
         * Create a throttled function
         */
        createThrottledFunction(func, limit = 1000) {
            let inThrottle;
            return (...args) => {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }
    },

    computed: {
        /**
         * Check if any operation is in progress
         */
        isOperationInProgress() {
            return this.submitting || this.pendingRequests.size > 0;
        },

        /**
         * Get button classes for disable state
         */
        buttonClasses() {
            return {
                'disabled': this.isOperationInProgress,
                'loading': this.submitting
            };
        }
    },

    beforeDestroy() {
        // Clean up pending requests
        this.pendingRequests.clear();
    }
};

// Vue component for duplicate-safe form
const DuplicateSafeForm = {
    mixins: [DuplicatePreventionMixin],
    
    template: `
        <form @submit.prevent="handleSubmit" :class="{ 'submitting': submitting }">
            <slot></slot>
            <div v-if="submitting" class="loading-overlay">
                <div class="spinner"></div>
                <span>Traitement en cours...</span>
            </div>
        </form>
    `,

    props: {
        submitHandler: {
            type: Function,
            required: true
        },
        operationUuid: {
            type: String,
            default: null
        }
    },

    methods: {
        async handleSubmit() {
            await this.submitFormSafely(this.submitHandler, this.operationUuid);
        }
    }
};

// Vue component for duplicate-safe button
const DuplicateSafeButton = {
    mixins: [DuplicatePreventionMixin],
    
    template: `
        <button 
            :disabled="disabled || submitting" 
            :class="buttonClasses"
            @click="handleClick"
            v-bind="$attrs"
        >
            <span v-if="submitting && loadingText">{{ loadingText }}</span>
            <span v-else><slot></slot></span>
        </button>
    `,

    props: {
        clickHandler: {
            type: Function,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        loadingText: {
            type: String,
            default: 'Traitement...'
        }
    },

    methods: {
        async handleClick(event) {
            await this.submitFormSafely(this.clickHandler);
        }
    }
};

// Register components globally if Vue is available
if (typeof Vue !== 'undefined') {
    Vue.component('duplicate-safe-form', DuplicateSafeForm);
    Vue.component('duplicate-safe-button', DuplicateSafeButton);
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        DuplicatePreventionMixin,
        DuplicateSafeForm,
        DuplicateSafeButton
    };
}

// Global availability
window.DuplicatePreventionMixin = DuplicatePreventionMixin;
window.DuplicateSafeForm = DuplicateSafeForm;
window.DuplicateSafeButton = DuplicateSafeButton;