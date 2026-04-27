/**
 * No per-blade JS: mark form duration from modals and inject into jQuery FormData on save.
 *
 * 1) Add data-cfd to the modal root (e.g. <div class="sm-modal" data-cfd>).
 * 2) When the modal’s class list gains "show", a session timer starts (restarts each open).
 * 3) On the next jQuery $.ajax/$.post that sends FormData while that modal is visible,
 *    appends create_form_duration_ms (unless the field is already set).
 *
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

  function appendTimeOnFormToToastrMessage(msg) {
    if (typeof msg !== 'string' || msg === '') {
      return msg;
    }
    if (msg.indexOf('Time on form:') !== -1) {
      return msg;
    }
    if (typeof window.formatCreateFormDurationMs !== 'function') {
      return msg;
    }
    var t = getActiveCfdFormDurationMs();
    if (t == null || t <= 0) {
      return msg;
    }
    return msg + ' · Time on form: ' + window.formatCreateFormDurationMs(t);
  }

  function wrapToastrSuccess() {
    if (window.__uapCfdToastrSuccess) {
      return;
    }
    if (!window.toastr || typeof toastr.success !== 'function') {
      return;
    }
    window.__uapCfdToastrSuccess = true;
    var orig = toastr.success;
    toastr.success = function (message) {
      var rest = Array.prototype.slice.call(arguments, 1);
      if (typeof message === 'string') {
        message = appendTimeOnFormToToastrMessage(message);
      }
      return orig.apply(toastr, [message].concat(rest));
    };
  }

  wrapToastrSuccess();
  $(function () { wrapToastrSuccess(); });
  setTimeout(wrapToastrSuccess, 0);
  setTimeout(wrapToastrSuccess, 300);

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
