import { passwordAddOn } from '../form/form-buttons.js';
import { formValidation } from '../form/form-validation.js';
import { showNotification } from '../module/notifications.js';

document.addEventListener('DOMContentLoaded', () => {
    passwordAddOn();
    
    formValidation({
        formId: '#login_form',
        rules: {
            email: { required: true, email: true },
            password: { required: true }
        },
        messages: {
            email: { required: 'Enter the email', email: 'Enter a valid email' },
            password: { required: 'Enter the password' }
        },
        onSubmit: async (form) => {
            const formData = new URLSearchParams(new FormData(form));
            formData.append('transaction', 'authenticate');

            const response = await fetch('./app/Controllers/AuthenticationController.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw response;

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect_link;
            } else {
                showNotification({
                    title: data.title,
                    message: data.message,
                    type: data.message_type
                });
            }
        }
    });
});
