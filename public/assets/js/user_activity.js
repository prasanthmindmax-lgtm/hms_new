/**
 * User activity (UAP) — combined bundle.
 * 1) Beacon logout on tab close / navigation away (uses window.__UAP_BEACON_END, set in layout before this file).
 * 2) Event log modal row builder (uses jQuery, exposes window.uapEventLogBuildRow for dashboard / user detail modals).
 */
(function () {
  'use strict';
  var cfg = window.__UAP_BEACON_END;
  if (!cfg || typeof cfg !== 'string' || !cfg.length) {
    return;
  }
  var tokenEl = document.querySelector('meta[name="csrf-token"]');
  if (!tokenEl || !tokenEl.getAttribute('content')) {
    return;
  }

  function isReloadOrBfNav() {
    try {
      var n = performance.getEntriesByType('navigation')[0];
      if (n && (n.type === 'reload' || n.type === 'back_forward')) {
        return true;
      }
    } catch (e) {}
    return false;
  }

  function markInternalNav() {
    try {
      sessionStorage.setItem('uapInternalNav', '1');
    } catch (e) {}
  }

  document.addEventListener(
    'click',
    function (e) {
      var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
      if (!a || !a.getAttribute) {
        return;
      }
      var href = a.getAttribute('href') || '';
      if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) {
        return;
      }
      try {
        var u = new URL(a.href, window.location.href);
        if (u.origin === window.location.origin) {
          markInternalNav();
        }
      } catch (err) {}
    },
    true
  );

  document.addEventListener(
    'submit',
    function (e) {
      var f = e.target;
      if (!f || !f.getAttribute) {
        return;
      }
      var act = (f.getAttribute('action') || '').toLowerCase();
      if (act.indexOf('logout') !== -1) {
        try {
          sessionStorage.setItem('uapExplicitLogout', '1');
        } catch (err) {}
        return;
      }
      markInternalNav();
    },
    true
  );

  window.addEventListener('pagehide', function (ev) {
    if (ev && ev.persisted) {
      return;
    }
    if (isReloadOrBfNav()) {
      return;
    }
    try {
      if (sessionStorage.getItem('uapExplicitLogout')) {
        sessionStorage.removeItem('uapExplicitLogout');
        return;
      }
      if (sessionStorage.getItem('uapInternalNav')) {
        sessionStorage.removeItem('uapInternalNav');
        return;
      }
    } catch (e) {}

    var token = tokenEl.getAttribute('content');
    var fd = new FormData();
    fd.append('_token', token);
    if (typeof navigator !== 'undefined' && typeof navigator.sendBeacon === 'function') {
      if (navigator.sendBeacon(cfg, fd)) {
        return;
      }
    }
    try {
      fetch(cfg, { method: 'POST', body: fd, credentials: 'same-origin', keepalive: true });
    } catch (e) {}
  });
})();

/**
 * Renders a single "event log" modal row (user activity by type / by module modals).
 * Expects jQuery. Expects each row: module_label, module, date, start_time, end_time,
 * reference, link_url, link_label, total, form_duration_label, server_duration_label.
 */
(function (window) {
  'use strict';
  if (typeof window.jQuery === 'undefined') {
    return;
  }
  var $ = window.jQuery;

  function elModule(row) {
    var full = (row.module && String(row.module) !== '') ? String(row.module) : String(row.module_label || '');
    return $('<td class="uap-activity-td uap-activity-td--module">')
      .append(
        $('<div class="uap-elm-mod">')
          .attr('title', full && full.length ? full : (row.module_label || ''))
          .text(row.module_label || '—')
      );
  }

  function elRef(row) {
    var $ref = $('<td class="uap-activity-td uap-activity-td--ref">');
    if (row.link_url) {
      $ref.append(
        $('<a class="uap-ref-link uap-ref-link--accent" rel="noopener" target="_blank" title="Open in new tab">')
          .attr('href', row.link_url)
          .text(
            (row.reference && row.reference !== '—') ? row.reference : (row.link_label || 'Open')
          )
      );
    } else {
      var v = row.reference;
      v = (v != null && String(v) !== '') ? String(v) : '—';
      $ref.append(
        v === '—' ? $('<span class="uap-ref-empty text-muted">—</span>') : $('<span class="uap-ref-txt">').text(v)
      );
    }
    return $ref;
  }

  function elDate(row) {
    return $('<td class="uap-activity-td uap-activity-td--date uap-evt-td-date text-nowrap">').text(row.date || '—');
  }

  function elFromTime(row) {
    var time = (row.start_time && row.start_time !== '—') ? row.start_time : '—';
    return $('<td class="uap-activity-td uap-activity-td--time uap-evt-th-start text-nowrap">').text(time);
  }

  function elToTime(row) {
    var time = (row.end_time && row.end_time !== '—') ? row.end_time : '—';
    return $('<td class="uap-activity-td uap-activity-td--time uap-evt-th-end text-nowrap">').text(time);
  }

  function elDuration(row) {
    var $td = $('<td class="uap-activity-td uap-activity-td--duration">');
    var $w = $('<div class="uap-elm-dur uap-elm-dur--stack" role="group">');
    var has = false;
    if (row.form_duration_label) {
      has = true;
      $w.append(
        $('<p class="uap-evt-dur-line uap-evt-dur-line--form mb-0" role="status">')
          .attr('title', 'Time from opening the form to submit')
          .append(
            $('<span class="uap-evt-dur-lbl">').text('Time on form: '),
            $('<span class="uap-evt-dur-val">').text(row.form_duration_label)
          )
      );
    }
    if (row.server_duration_label) {
      has = true;
      $w.append(
        $('<p class="uap-evt-dur-line uap-evt-dur-line--srv mb-0" role="status">')
          .attr('title', 'Time spent handling the request on the server')
          .append(
            $('<span class="uap-evt-dur-lbl">').text('Request: '),
            $('<span class="uap-evt-dur-val">').text(row.server_duration_label)
          )
      );
    }
    if (!has) {
      var t = (row.total != null && String(row.total) !== '') ? String(row.total) : '—';
      $w.append(
        t.indexOf('no duration') !== -1
          ? $('<p class="text-muted small uap-elm-na mb-0">').text(t)
          : $('<p class="uap-elm-tot-fallback mb-0">').text(t)
      );
    }
    $td.append($w);
    return $td;
  }

  /**
   * @param {object} row
   * @returns {jQuery}
   */
  window.uapEventLogBuildRow = function (row) {
    return $('<tr>').addClass('uap-event-log-tr').append(
      elModule(row),
      elRef(row),
      elDate(row),
      elFromTime(row),
      elToTime(row),
      elDuration(row)
    );
  };
})(window);
