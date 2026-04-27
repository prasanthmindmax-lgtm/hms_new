/**
 * Form open → submit duration (ms) for user-activity and module reporting.
 *
 * 1) Explicit: form[data-create-form-duration="1" or "true"] (any method — legacy)
 * 2) Global: all <form method="post"> except data-no-form-duration, login/logout, etc.
 *
 * Config (optional, before this script): window.__CREATE_FORM_DURATION = {
 *   autoTrackPostForms: true,  // set false to disable (2) — only explicit forms tracked
 *   excludedPathSubstrings: ['/login', '/logout', ...],
 *   maxMs, inputName  // (optional) caps + field name; footer injects from config/create_form_duration.php
 * };
 *
 * For modals / fetch / $.ajax (no real form submit), after load of this file:
 *   - window.createFormDurationSession()  → { mark, getMs, appendToFormData } — use one session per dialog if needed
 *   - window.createFormDurationManual     → default single-session (same API)
 * On open: mark(). On send: appendToFormData(formData) or getMs() and append yourself.
 */
(function () {
  'use strict';

  var DEFAULT_FIELD = 'create_form_duration_ms';
  var DEFAULT_MAX_MS = 172800000;

  function tNow() {
    if (window.performance && typeof performance.now === 'function') {
      return performance.now();
    }
    return Date.now();
  }

  function getCfg() {
    if (window.__CREATE_FORM_DURATION && typeof window.__CREATE_FORM_DURATION === 'object') {
      return window.__CREATE_FORM_DURATION;
    }
    return {};
  }

  function defaultExcluded() {
    return ['/login', 'login?', '/logout', '/register', 'password/reset', 'forgot-password', '/password/'];
  }

  function isExcludedByAction(action) {
    if (!action || typeof action !== 'string') {
      return false;
    }
    var a = action.toLowerCase();
    var ex = getCfg().excludedPathSubstrings;
    if (!ex || !ex.length) {
      ex = defaultExcluded();
    }
    for (var i = 0; i < ex.length; i++) {
      if (a.indexOf(String(ex[i]).toLowerCase()) >= 0) {
        return true;
      }
    }
    return false;
  }

  function isNoTrack(form) {
    var v = form.getAttribute('data-no-form-duration');
    return v === '1' || v === 'true' || v === 'yes';
  }

  function shouldTrackPostForm(form) {
    if (isNoTrack(form)) {
      return false;
    }
    if (form.getAttribute('data-create-form-duration') === '0' || form.getAttribute('data-create-form-duration') === 'false') {
      return false;
    }
    var m = (form.getAttribute('method') || 'get').toString().toLowerCase();
    if (m !== 'post') {
      return false;
    }
    if (isExcludedByAction(form.getAttribute('action') || '')) {
      return false;
    }
    return true;
  }

  function shouldTrackExplicitForm(form) {
    if (isNoTrack(form)) {
      return false;
    }
    if (isExcludedByAction(form.getAttribute('action') || '')) {
      return false;
    }
    return true;
  }

  function autoTrackPostOn() {
    if (getCfg().autoTrackPostForms === false) {
      return false;
    }
    return true;
  }

  function getFieldName(form) {
    return (form.getAttribute('data-create-form-field') || DEFAULT_FIELD).toString();
  }

  function getMaxMs(form) {
    var cap = parseInt(form.getAttribute('data-create-form-max-ms') || String(DEFAULT_MAX_MS), 10);
    return isFinite(cap) && cap >= 0 ? cap : DEFAULT_MAX_MS;
  }

  function formHasField(form, name) {
    var all = form.querySelectorAll('input');
    for (var j = 0; j < all.length; j++) {
      if (all[j].getAttribute('name') === name) {
        return true;
      }
    }
    return false;
  }

  function ensureHiddenInput(form) {
    var name = getFieldName(form);
    if (formHasField(form, name)) {
      return;
    }
    var inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = name;
    inp.value = '0';
    inp.setAttribute('autocomplete', 'off');
    if (form.firstChild) {
      form.insertBefore(inp, form.firstChild);
    } else {
      form.appendChild(inp);
    }
  }

  function bindSubmit(form) {
    if (form.getAttribute('data-ffd-wired') === '1') {
      return;
    }
    form.setAttribute('data-ffd-wired', '1');
    var start = tNow();
    var fieldName = getFieldName(form);
    var cap = getMaxMs(form);
    form.addEventListener('submit', function () {
      var inp;
      var all = form.querySelectorAll('input');
      for (var j = 0; j < all.length; j++) {
        if (all[j].getAttribute('name') === fieldName) {
          inp = all[j];
          break;
        }
      }
      if (!inp) {
        return;
      }
      var dur = Math.max(0, Math.round(tNow() - start));
      if (dur > cap) {
        dur = cap;
      }
      inp.value = String(dur);
    });
  }

  function run() {
    document.querySelectorAll('form[data-create-form-duration]').forEach(function (form) {
      var raw = (form.getAttribute('data-create-form-duration') + '').toLowerCase();
      if (raw === '0' || raw === 'false' || raw === 'no' || raw === 'off') {
        return;
      }
      if (!shouldTrackExplicitForm(form)) {
        return;
      }
      ensureHiddenInput(form);
      bindSubmit(form);
    });
    if (!autoTrackPostOn()) {
      return;
    }
    document.querySelectorAll('form').forEach(function (form) {
      if (form.getAttribute('data-ffd-wired') === '1') {
        return;
      }
      if (!shouldTrackPostForm(form)) {
        return;
      }
      ensureHiddenInput(form);
      bindSubmit(form);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();

/**
 * Manual timing for AJAX / modals (not covered by <form> submit).
 * Uses window.__CREATE_FORM_DURATION.maxMs and .inputName when set (superadminfooter).
 */
(function () {
  'use strict';

  function tNow() {
    if (window.performance && typeof performance.now === 'function') {
      return performance.now();
    }
    return Date.now();
  }

  function getCfg() {
    return (window.__CREATE_FORM_DURATION && typeof window.__CREATE_FORM_DURATION === 'object')
      ? window.__CREATE_FORM_DURATION
      : {};
  }

  function capMs() {
    var c = getCfg().maxMs;
    if (typeof c === 'number' && c >= 0) {
      return c;
    }
    return 172800000;
  }

  function inputName() {
    var n = getCfg().inputName;
    return n ? String(n) : 'create_form_duration_ms';
  }

  function createFormDurationSession() {
    var start = null;
    return {
      mark: function () {
        start = tNow();
      },
      getMs: function () {
        if (start == null) {
          return null;
        }
        var c = capMs();
        var d = Math.max(0, Math.round(tNow() - start));
        return d > c ? c : d;
      },
      appendToFormData: function (fd) {
        if (!fd || typeof fd.append !== 'function') {
          return;
        }
        var m = this.getMs();
        if (m == null) {
          return;
        }
        fd.append(inputName(), String(m));
      },
    };
  }

  function formatCreateFormDurationMs(ms) {
    if (ms == null || isNaN(ms) || ms < 0) {
      return '—';
    }
    var n = Math.floor(Number(ms));
    if (n < 1000) {
      return n + 'ms';
    }
    var s = Math.floor(n / 1000);
    if (s < 60) {
      return s + 's';
    }
    var m = Math.floor(s / 60);
    var rem = s % 60;
    if (m < 60) {
      return m + 'm ' + (rem < 10 ? '0' : '') + rem + 's';
    }
    var h = Math.floor(m / 60);
    var m2 = m % 60;
    return h + 'h ' + m2 + 'm';
  }

  window.createFormDurationSession = createFormDurationSession;
  window.createFormDurationManual = createFormDurationSession();
  window.formatCreateFormDurationMs = formatCreateFormDurationMs;
})();
