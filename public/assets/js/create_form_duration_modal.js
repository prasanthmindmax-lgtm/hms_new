/**
 * No per-blade JS: mark form duration from modals and inject into jQuery FormData on save.
 * Requires: create_form_duration.js (createFormDurationSession) + superadminfooter __CREATE_FORM_DURATION.
 */
(function () {
  'use strict';

  if (typeof window.jQuery === 'undefined' || typeof window.createFormDurationSession !== 'function') {
    return;
  }

  var $ = window.jQuery;
  var sessions = new WeakMap();
  var INPUT = function () {
    if (window.__CREATE_FORM_DURATION && window.__CREATE_FORM_DURATION.inputName) {
      return String(window.__CREATE_FORM_DURATION.inputName);
    }
    return 'create_form_duration_ms';
  };

  function sessionForModal(modal) {
    if (!sessions.has(modal)) {
      sessions.set(modal, window.createFormDurationSession());
    }
    return sessions.get(modal);
  }

  function isModalVisible(modal) {
    return modal && modal.classList && modal.classList.contains('show');
  }

  function onClassChange(modal) {
    if (!modal || !modal.hasAttribute || !modal.hasAttribute('data-cfd')) {
      return;
    }
    if (isModalVisible(modal)) {
      sessionForModal(modal).mark();
    }
  }

  var classMo = new MutationObserver(function (mutations) {
    mutations.forEach(function (m) {
      if (m.type === 'attributes' && m.attributeName === 'class') {
        onClassChange(m.target);
      }
    });
  });

  function bindModalEl(modal) {
    onClassChange(modal);
    classMo.observe(modal, { attributes: true, attributeFilter: ['class'] });
  }

  function scan(root) {
    var r = root || document;
    r.querySelectorAll('.sm-modal[data-cfd]').forEach(bindModalEl);
  }

  function getActiveCfdModal() {
    return document.querySelector('.sm-modal[data-cfd].show');
  }

  /**
   * @returns {number|null} ms on this modal (since last mark when opened), or null
   */
  function getActiveCfdFormDurationMs() {
    var modal = getActiveCfdModal();
    if (!modal) {
      return null;
    }
    var s = sessionForModal(modal);
    return s.getMs();
  }

  window.getActiveCfdFormDurationMs = getActiveCfdFormDurationMs;

  function appendCfdToFormData(fd) {
    if (!fd || typeof fd.append !== 'function') {
      return;
    }
    var n = INPUT();
    if (typeof fd.has === 'function' && fd.has(n)) {
      return;
    }
    var modal = getActiveCfdModal();
    if (!modal) {
      return;
    }
    sessionForModal(modal).appendToFormData(fd);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { scan(document); });
  } else {
    scan(document);
  }

  $(document).ajaxSend(function (event, jqXhr, settings) {
    if (settings == null) {
      return;
    }
    if (!(settings.data instanceof window.FormData)) {
      return;
    }
    appendCfdToFormData(settings.data);
  });
})();
