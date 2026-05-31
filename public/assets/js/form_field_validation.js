/**
 * Client-side field validation — messages below inputs; pair with toastr for server errors.
 */
(function (global) {
  'use strict';

  function ensureFeedback(container) {
    let el = container.querySelector('.js-field-feedback');
    if (!el) {
      el = document.createElement('div');
      el.className = 'invalid-feedback d-block js-field-feedback';
      el.setAttribute('role', 'alert');
      container.appendChild(el);
    }
    return el;
  }

  function markInvalid(container) {
    container.classList.add('is-invalid');
    container.querySelectorAll('input, select, textarea').forEach(function (inp) {
      if (!inp.classList.contains('inner-search')) {
        inp.classList.add('is-invalid');
      }
    });
  }

  function clearField(container) {
    container.classList.remove('is-invalid');
    container.querySelectorAll('input, select, textarea').forEach(function (inp) {
      inp.classList.remove('is-invalid');
    });
    const fb = container.querySelector('.js-field-feedback');
    if (fb) {
      fb.textContent = '';
    }
  }

  function setError(container, message) {
    if (!container) {
      return;
    }
    markInvalid(container);
    const fb = ensureFeedback(container);
    fb.textContent = message || 'This field is required.';
  }

  function getValue(container, rule) {
    if (rule.getValue) {
      return rule.getValue(container);
    }
    const name = rule.field || rule.name;
    if (name) {
      const named = container.querySelector('[name="' + name + '"]');
      if (named) {
        if (named.type === 'checkbox') {
          return named.checked ? named.value : '';
        }
        return named.value;
      }
    }
    const hidden = container.querySelector('input[type="hidden"]');
    if (hidden) {
      return hidden.value;
    }
    const input = container.querySelector('input, select, textarea');
    return input ? input.value : '';
  }

  function validateRule(container, rule) {
    const value = getValue(container, rule);
    const str = value === null || value === undefined ? '' : String(value).trim();

    if (rule.required && str === '') {
      return rule.message || 'This field is required.';
    }

    if (str !== '' && rule.min !== undefined) {
      const num = parseFloat(str);
      if (!Number.isFinite(num) || num < rule.min) {
        return rule.minMessage || 'Enter a value of at least ' + rule.min + '.';
      }
    }

    if (str !== '' && rule.max !== undefined) {
      const num = parseFloat(str);
      if (!Number.isFinite(num) || num > rule.max) {
        return rule.maxMessage || 'Enter a value no greater than ' + rule.max + '.';
      }
    }

    if (str !== '' && rule.pattern && !rule.pattern.test(str)) {
      return rule.patternMessage || 'Enter a valid value.';
    }

    if (rule.validate) {
      const custom = rule.validate(str, container);
      if (custom) {
        return custom;
      }
    }

    return '';
  }

  function validateForm(form, rules) {
    let valid = true;
    let firstInvalid = null;

    rules.forEach(function (rule) {
      const selector = rule.container || '[data-field="' + rule.field + '"]';
      const container = form.querySelector(selector);
      if (!container) {
        return;
      }
      clearField(container);
      const err = validateRule(container, rule);
      if (err) {
        valid = false;
        setError(container, err);
        if (!firstInvalid) {
          firstInvalid = container;
        }
      }
    });

    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const focusEl = firstInvalid.querySelector(
        'input:not([type="hidden"]):not([readonly]), select, textarea'
      );
      if (focusEl) {
        focusEl.focus();
      }
    }

    return valid;
  }

  function bindClearOnInput(form) {
    form.querySelectorAll('[data-field]').forEach(function (container) {
      const clear = function () {
        clearField(container);
      };
      container.addEventListener('input', clear, true);
      container.addEventListener('change', clear, true);
    });
  }

  function showBackendToasts(errors, options) {
    if (!errors || !errors.length || !global.toastr) {
      return;
    }
    const opts = options || {};
    const delay = opts.delay || 5000;
    errors.forEach(function (msg, idx) {
      setTimeout(function () {
        global.toastr.error(msg, '', { timeOut: delay, closeButton: true });
      }, idx * 150);
    });
  }

  global.FormFieldValidation = {
    setError: setError,
    clearField: clearField,
    validateForm: validateForm,
    bindClearOnInput: bindClearOnInput,
    showBackendToasts: showBackendToasts,
  };
})(window);
