export const resetForm = (target) => {
  const form = typeof target === 'string'
    ? document.querySelector(target)
    : target;

  if (!form) {
    console.warn('resetForm: Form not found.', target);
    return;
  }

  form.reset();

  if (window.$) {
    $(form).find('.select2-hidden-accessible')
      .val(null)
      .trigger('change');
  }

  form.querySelectorAll('.is-invalid, .is-valid')
    .forEach(el => el.classList.remove('is-invalid', 'is-valid'));

  if (window.$ && $(form).data('validator')) {
    $(form).validate().resetForm();
  }

  form.querySelectorAll('input[type="hidden"]').forEach(input => {
    if (input.name !== 'csrf_token') {
      input.value = '';
    }
  });
};