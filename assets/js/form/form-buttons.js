/**
 * Normalizes various input types into an array of HTMLElements
 */
const normalizeElements = (targets) => {
    if (!targets) return [];
    if (typeof targets === 'string') return Array.from(document.querySelectorAll(targets));
    if (targets instanceof Element) return [targets];
    if (targets instanceof NodeList || Array.isArray(targets)) return Array.from(targets);
    return [];
};

/**
 * Core utility to manage button loading states
 */
const setButtonState = (targets, isDisabled, options = {}) => {
    const {
        showSpinner = true,
        spinnerHTML = '<span class="spinner-border spinner-border-sm align-middle"></span>',
        preserveText = false,
        text = null,
        keepSpinner = false
    } = options;

    const elements = normalizeElements(targets);

    elements.forEach((btn) => {
        if (!(btn instanceof HTMLButtonElement || btn instanceof HTMLAnchorElement)) return;

        if (isDisabled) {
            // Prevent double-disabling and losing original content
            if (btn.dataset.loading === 'true') return;

            btn.dataset.originalText = btn.innerHTML;
            btn.dataset.loading = 'true';
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');

            if (text !== null) {
                btn.innerHTML = text;
            } else if (showSpinner) {
                btn.innerHTML = preserveText 
                    ? `${spinnerHTML} ${btn.dataset.originalText}` 
                    : spinnerHTML;
            }
        } else {
            // Enable
            btn.disabled = false;
            btn.removeAttribute('aria-busy');

            if (text !== null) {
                btn.innerHTML = text;
            } else if (btn.dataset.originalText) {
                btn.innerHTML = btn.dataset.originalText;
            }

            if (!keepSpinner) {
                delete btn.dataset.loading;
                delete btn.dataset.originalText;
            }
        }
    });
};

export const disableButton = (targets, options = {}) => setButtonState(targets, true, options);
export const enableButton = (targets, options = {}) => setButtonState(targets, false, options);

/**
 * Password visibility toggle utility (Vanilla JS)
 */
export const passwordAddOn = (container = document) => {
    const selector = '.password-addon';
    
    // 1. Setup accessibility attributes for any existing addons
    container.querySelectorAll(selector).forEach(addon => {
        addon.setAttribute('tabindex', '0');
        addon.setAttribute('role', 'button');
        addon.setAttribute('aria-label', 'Toggle password visibility');
        addon.setAttribute('aria-pressed', 'false');
    });

    const toggle = (addon) => {
        const parent = addon.closest('.position-relative, .input-group, .form-group');
        if (!parent) return;

        const input = parent.querySelector('input[type="password"], input[type="text"]');
        const icon = addon.querySelector('i');
        if (!input || !icon) return;

        const isPassword = input.type === 'password';
        
        // Toggle type
        input.type = isPassword ? 'text' : 'password';
        
        // Toggle Icons
        icon.classList.toggle('ki-eye-slash', !isPassword);
        icon.classList.toggle('ki-eye', isPassword);
        
        // Accessibility
        addon.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
    };

    // 2. Event Delegation (More efficient than .each())
    container.addEventListener('click', (e) => {
        const addon = e.target.closest(selector);
        if (addon) toggle(addon);
    });

    container.addEventListener('keydown', (e) => {
        const addon = e.target.closest(selector);
        if (addon && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            toggle(addon);
        }
    });
};