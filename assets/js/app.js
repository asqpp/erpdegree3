/**
 * Insurance ERP - Main Application JavaScript
 * Includes Alpine.js, AOS, GSAP, Charts
 */

// Import Alpine.js (will be loaded via CDN in production)
// Initialize Alpine.js components
document.addEventListener('alpine:init', () => {

    // Global State Management
    Alpine.store('app', {
        sidebarOpen: true,
        darkMode: false,
        currentUser: null,
        notifications: [],

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            localStorage.setItem('darkMode', this.darkMode);
        },

        addNotification(message, type = 'info') {
            const notification = {
                id: Date.now(),
                message,
                type,
                timestamp: new Date()
            };
            this.notifications.unshift(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                this.removeNotification(notification.id);
            }, 5000);
        },

        removeNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }
    });

    // Dropdown Component
    Alpine.data('dropdown', () => ({
        open: false,

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        }
    }));

    // Modal Component
    Alpine.data('modal', () => ({
        show: false,

        open() {
            this.show = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.show = false;
            document.body.style.overflow = 'auto';
        }
    }));

    // Tabs Component
    Alpine.data('tabs', (defaultTab = 0) => ({
        activeTab: defaultTab,

        setTab(index) {
            this.activeTab = index;
        }
    }));

    // Data Table Component
    Alpine.data('dataTable', (initialData = []) => ({
        data: initialData,
        search: '',
        sortBy: null,
        sortDesc: false,
        page: 1,
        perPage: 10,

        get filteredData() {
            let filtered = this.data;

            // Apply search filter
            if (this.search) {
                const searchLower = this.search.toLowerCase();
                filtered = filtered.filter(item => {
                    return Object.values(item).some(value =>
                        String(value).toLowerCase().includes(searchLower)
                    );
                });
            }

            // Apply sorting
            if (this.sortBy) {
                filtered.sort((a, b) => {
                    let aVal = a[this.sortBy];
                    let bVal = b[this.sortBy];

                    if (aVal < bVal) return this.sortDesc ? 1 : -1;
                    if (aVal > bVal) return this.sortDesc ? -1 : 1;
                    return 0;
                });
            }

            return filtered;
        },

        get paginatedData() {
            const start = (this.page - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredData.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.perPage);
        },

        sort(column) {
            if (this.sortBy === column) {
                this.sortDesc = !this.sortDesc;
            } else {
                this.sortBy = column;
                this.sortDesc = false;
            }
        },

        nextPage() {
            if (this.page < this.totalPages) {
                this.page++;
            }
        },

        prevPage() {
            if (this.page > 1) {
                this.page--;
            }
        }
    }));

    // Form Validation Component
    Alpine.data('formValidation', () => ({
        errors: {},

        validate(field, rules) {
            const value = field.value;
            const errors = [];

            rules.forEach(rule => {
                if (rule === 'required' && !value) {
                    errors.push('This field is required');
                }
                if (rule.startsWith('min:')) {
                    const min = parseInt(rule.split(':')[1]);
                    if (value.length < min) {
                        errors.push(`Minimum ${min} characters required`);
                    }
                }
                if (rule.startsWith('max:')) {
                    const max = parseInt(rule.split(':')[1]);
                    if (value.length > max) {
                        errors.push(`Maximum ${max} characters allowed`);
                    }
                }
                if (rule === 'email' && value && !this.isValidEmail(value)) {
                    errors.push('Invalid email format');
                }
                if (rule === 'numeric' && value && !this.isNumeric(value)) {
                    errors.push('Must be a number');
                }
            });

            this.errors[field.name] = errors;
            return errors.length === 0;
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        isNumeric(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        },

        hasError(fieldName) {
            return this.errors[fieldName] && this.errors[fieldName].length > 0;
        },

        getError(fieldName) {
            return this.errors[fieldName] ? this.errors[fieldName][0] : '';
        }
    }));
});

/**
 * Initialize AOS (Animate On Scroll)
 */
function initAOS() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    }
}

/**
 * Utility Functions
 */
const Utils = {
    // Format currency
    formatCurrency(amount, currency = 'AED') {
        return new Intl.NumberFormat('en-AE', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },

    // Format date
    formatDate(date, format = 'DD/MM/YYYY') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year);
    },

    // Debounce function
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Show toast notification
    showToast(message, type = 'success') {
        if (typeof Toastify !== 'undefined') {
            const colors = {
                success: 'linear-gradient(to right, #22c55e, #16a34a)',
                error: 'linear-gradient(to right, #ef4444, #dc2626)',
                warning: 'linear-gradient(to right, #f59e0b, #d97706)',
                info: 'linear-gradient(to right, #3b82f6, #2563eb)'
            };

            Toastify({
                text: message,
                duration: 3000,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: {
                    background: colors[type] || colors.info,
                },
            }).showToast();
        }
    },

    // Show SweetAlert confirmation
    async confirm(title, text, confirmText = 'Yes, proceed') {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#ef4444',
                confirmButtonText: confirmText
            });
            return result.isConfirmed;
        }
        return confirm(title + '\n\n' + text);
    },

    // Copy to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.showToast('Copied to clipboard!', 'success');
        } catch (err) {
            this.showToast('Failed to copy', 'error');
        }
    },

    // Download as CSV
    downloadCSV(data, filename = 'export.csv') {
        const headers = Object.keys(data[0]);
        const csv = [
            headers.join(','),
            ...data.map(row => headers.map(header =>
                JSON.stringify(row[header] || '')
            ).join(','))
        ].join('\n');

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', filename);
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    },

    // AJAX helper
    async ajax(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = { ...defaults, ...options };

        if (config.data && config.method !== 'GET') {
            config.body = JSON.stringify(config.data);
            delete config.data;
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        } catch (error) {
            this.showToast(error.message, 'error');
            throw error;
        }
    }
};

/**
 * Chart Helper Functions
 */
const ChartHelpers = {
    // Default chart options
    defaultOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    },

    // Create line chart
    createLineChart(ctx, labels, datasets) {
        return new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                ...this.defaultOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    // Create bar chart
    createBarChart(ctx, labels, datasets) {
        return new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets },
            options: {
                ...this.defaultOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    // Create pie chart
    createPieChart(ctx, labels, data, backgroundColors) {
        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: backgroundColors || [
                        '#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#a855f7'
                    ]
                }]
            },
            options: this.defaultOptions
        });
    },

    // Create donut chart
    createDonutChart(ctx, labels, data, backgroundColors) {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: backgroundColors || [
                        '#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#a855f7'
                    ]
                }]
            },
            options: this.defaultOptions
        });
    }
};

// Make utilities globally available
window.Utils = Utils;
window.ChartHelpers = ChartHelpers;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initAOS();

    // Initialize tooltips if needed
    // Add any other initialization here
});

// Export for modules if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { Utils, ChartHelpers };
}
