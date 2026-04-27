(function () {
  'use strict';

  function cfd() {
    return window.__CREATE_FORM_DURATION || {};
  }

  function smConfig() {
    var d = cfd();
    return d.smModalMark || {};
  }

  function getPath() {
    return (window.location && window.location.pathname) ? String(window.location.pathname) : '';
  }

  function pathHasExcludedSubstrings(pathLower, list) {
    for (var i = 0; i < (list || []).length; i++) {
      var s = String(list[i] || '').toLowerCase();
      if (s !== '' && pathLower.indexOf(s) !== -1) {
        return true;
      }
    }
    return false;
  }

  function pathMatchesIncludeSubstrings(pathLower, list) {
    for (var j = 0; j < (list || []).length; j++) {
      var t = String(list[j] || '').toLowerCase();
      if (t !== '' && pathLower.indexOf(t) !== -1) {
        return true;
      }
    }
    return false;
  }

  function pathMatchesAnyRegex(path, list) {
    for (var k = 0; k < (list || []).length; k++) {
      var src = String(list[k] || '').trim();
      if (src === '') {
        continue;
      }
      try {
        if (new RegExp(src, 'i').test(path)) {
          return true;
        }
      } catch (e) {
        /* invalid pattern in .env: skip */
      }
    }
    return false;
  }

  /** @returns {boolean|undefined} undefined = not using legacy */
  function pathAllowedLegacy() {
    var raw = window.__CFD_MARK_MODAL_PATHS;
    if (raw == null) {
      return undefined;
    }
    if (Object.prototype.toString.call(raw) === '[object RegExp]') {
      return raw.test(getPath());
    }
    if (!raw.length) {
      return true;
    }
    var p = getPath();
    for (var i = 0; i < raw.length; i++) {
      var it = raw[i];
      if (it && Object.prototype.toString.call(it) === '[object RegExp]') {
        if (it.test(p)) {
          return true;
        }
        continue;
      }
      if (typeof it === 'string' && it !== '') {
        try {
          if (new RegExp(it, 'i').test(p)) {
            return true;
          }
        } catch (e) {
          /* fall through */
        }
      }
    }
    return false;
  }

  function shouldMark() {
    if (window.__CFD_MARK_ALL_SM_MODALS === false) {
      return false;
    }

    var leg = pathAllowedLegacy();
    if (leg !== undefined) {
      return leg;
    }

    var sm = smConfig();
    if (sm.enabled === false) {
      return false;
    }

    var path = getPath();
    var pl = path.toLowerCase();

    if (pathHasExcludedSubstrings(pl, sm.excludeSubstrings)) {
      return false;
    }

    var sub = sm.includeSubstrings;
    var regs = sm.includeRegexes;
    var hasSub = sub && sub.length;
    var hasReg = regs && regs.length;
    if (!hasSub && !hasReg) {
      return true;
    }

    if (hasSub && pathMatchesIncludeSubstrings(pl, sub)) {
      return true;
    }
    if (hasReg && pathMatchesAnyRegex(path, regs)) {
      return true;
    }
    return false;
  }

  function mark() {
    if (!shouldMark()) {
      return;
    }
    document.querySelectorAll('.sm-modal').forEach(function (el) {
      if (!el.hasAttribute('data-cfd')) {
        el.setAttribute('data-cfd', '');
      }
    });
  }

  function boot() {
    mark();
    if (typeof window.MutationObserver === 'undefined' || !document.body) {
      return;
    }
    var t = null;
    var mo = new window.MutationObserver(function () {
      if (t) {
        return;
      }
      t = window.setTimeout(function () {
        t = null;
        mark();
      }, 0);
    });
    mo.observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
