const TOAST_TYPES = Object.freeze({
    SUCCESS: 'success',
    INFO: 'info',
    WARNING: 'warning',
    ERROR: 'error'
});

const STORAGE_KEY = 'app_notification_payload';

/**
 * Configure global toastr defaults
 */
const setupToastrDefaults = () => {
    if (typeof toastr === 'undefined') return false;

    toastr.options = {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
        positionClass: 'toastr-top-right',
        // Default timeout if not specified in the call
        timeOut: 3000 
    };
    return true;
};

/**
 * Main notification dispatcher
 */
export const showNotification = (arg1, message = '', type = TOAST_TYPES.INFO, timeOut = null) => {
    if (!setupToastrDefaults()) {
        console.warn('Toastr not found. Falling back to console.');
        console.log(`[${type.toUpperCase()}] ${arg1} - ${message}`);
        return;
    }

    // Handle Object vs String arguments
    const config = typeof arg1 === 'object' 
        ? { title: '', message: '', type: TOAST_TYPES.INFO, ...arg1 }
        : { title: arg1, message, type, timeOut };

    if (!Object.values(TOAST_TYPES).includes(config.type)) {
        config.type = TOAST_TYPES.INFO;
    }

    // Call toastr with specific overrides (like timeOut) for this instance
    const options = config.timeOut ? { timeOut: config.timeOut } : {};
    toastr[config.type](config.message, config.title, options);
};

/**
 * Shows a heavy system error modal
 */
export const showErrorDialog = (error = 'An unexpected error occurred.') => {
    const el = document.getElementById('error-dialog');
    if (el) el.textContent = error;

    const $modal = window.$ ? $('#system-error-modal') : null;

    if ($modal?.length) {
        $modal.modal('show');
    } else {
        // Fallback to a cleaner UI if modal isn't present
        console.error('System Error:', error);
        alert(error); 
    }
};

/**
 * Persistence: Queues a notification to be shown after a page reload
 */
export const setNotification = (payload = {}) => {
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
            title: payload.title || '',
            message: payload.message || '',
            type: payload.type || TOAST_TYPES.INFO,
            at: Date.now() // Timestamp to prevent showing stale notifications
        }));
    } catch (e) {
        console.error('Session storage failed', e);
    }
};

/**
 * Check and display any queued notifications (Call this on page load)
 */
export const checkNotification = () => {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (!raw) return;

        const payload = JSON.parse(raw);
        sessionStorage.removeItem(STORAGE_KEY);

        // Optional: Ignore if the notification is older than 30 seconds
        if (Date.now() - payload.at > 30000) return;

        showNotification(payload);
    } catch (e) {
        sessionStorage.removeItem(STORAGE_KEY);
    }
};