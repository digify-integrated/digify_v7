import { showNotification } from '../module/notifications.js';
import { handleSystemError } from '../module/system-errors.js';
import { disableButton, enableButton } from './form-buttons.js'; // Assuming the previous code is here

/**
 * Standardized Form Validation Wrapper
 */
export const formValidation = ({
    formId,
    rules = {},
    messages = {},
    autoDisable = true, // Automatically handle button loading states
    onSubmit = async (form) => {}
}) => {
    const $form = $(formId);

    if (!$form.length) {
        console.warn(`formValidation: Form not found -> ${formId}`);
        return;
    }

    // Identifies the correct visual element to highlight (especially for Select2)
    const getValidationTarget = (element) => {
        const $el = $(element);
        
        if ($el.hasClass('select2-hidden-accessible')) {
            return $el.next().find('.select2-selection');
        }
        
        // If it's inside a bootstrap input-group, you might want the group to look ok
        // but usually, adding is-invalid to the input is enough for modern CSS.
        return $el;
    };

    $form.validate({
        rules,
        messages,
        
        // This fires for every field - keep it quiet to avoid spamming notifications
        errorPlacement: (error, element) => {
           showNotification('Action Needed: Issue Detected', error.text(), 'error', 2500);
        },

        highlight: (element) => {
            getValidationTarget(element).addClass('is-invalid').removeClass('is-valid');
        },

        unhighlight: (element) => {
            getValidationTarget(element).removeClass('is-invalid').addClass('is-valid');
        },

        submitHandler: async (form) => {
            const $submitBtn = $(form).find('[type="submit"]');
            
            try {
                if (autoDisable) disableButton($submitBtn);
                
                await onSubmit(form);
                
            } catch (error) {
                handleSystemError(
                    error,
                    'form_submit_error',
                    `Form submission failed: ${error.message}`
                );
            } finally {
                // Always re-enable unless the page is redirecting
                if (autoDisable) enableButton($submitBtn);
            }

            return false; // Prevent native form submission
        }
    });
};