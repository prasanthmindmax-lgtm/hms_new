$(document).ready(function () {
  menu_access_view();
});

/* ── Fetch menu data ── */
function menu_access_view() {
  $.ajax({
    url: menuaccessurl,
    type: 'GET',
    success: handleSuccessmenu,
    error: handleErrormenu,
  });
}

/* ── Chevron SVG ── */
var CHEVRON =
  '<svg class="menu-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">' +
    '<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938' +
    'a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"' +
    ' clip-rule="evenodd"/></svg>';

/* ════════════════════════════════════════
   LEVEL-1 handler
   ════════════════════════════════════════ */
function handleSuccessmenu(data) {
  if (!data || data.length === 0) { console.warn('No menu data.'); return; }

  var html      = '';
  var lastGroup = null;

  $.each(data, function (_, menu) {

    /* Section label */
    var group = menu.group_name || menu.category || menu.section || null;
    if (group && group !== lastGroup) {
      if (lastGroup !== null) {
        html += '<li class="pc-item"><div class="sidebar-section-divider"></div></li>';
      }
      html += '<li class="pc-item"><div class="sidebar-section-label">' + esc(group) + '</div></li>';
      lastGroup = group;
    }

    var iconUrl = '/hms/assets/images/' + (menu.icon || 'default-icon.png');
    var hasKids = menu.children && menu.children.length > 0;

    /* ── Level-1 WITH children ── */
    if (menu.dropdown == 1 && hasKids) {
      html +=
        '<li class="pc-item pc-hasmenu lvl1">' +
          '<a href="javascript:void(0);" class="pc-link">' +
            '<span class="pc-micon"><img src="' + iconUrl + '" alt=""></span>' +
            '<span class="pc-mtext">' + esc(menu.menu_name) + '</span>' +
            CHEVRON +
          '</a>' +
          '<ul class="pc-submenu">' +
            buildLevel2(menu.children) +
          '</ul>' +
        '</li>';

    /* ── Level-1 leaf ── */
    } else {
      var href = menuRouteBase + '/' + (menu.route || '').replace(/\./g, '/');
      html +=
        '<li class="pc-item lvl1">' +
          '<a href="' + href + '" class="pc-link menu-leaf">' +
            '<span class="pc-micon"><img src="' + iconUrl + '" alt=""></span>' +
            '<span class="pc-mtext">' + esc(menu.menu_name) + '</span>' +
          '</a>' +
        '</li>';
    }
  });

  /* Inject */
  var $c = $('ul.menuupdatesall');
  if (!$c.length) $c = $('.menuupdatesall');
  if (!$c.length) { console.error('Menu container not found'); return; }
  $c.html(html);

  /* Activate correct item AFTER injection */
  activateCurrentPage($c);

  /* Bind events */
  bindMenuEvents($c);
}

/* ════════════════════════════════════════
   LEVEL-2 builder  (NO icon)
   ════════════════════════════════════════ */
function buildLevel2(children) {
  var html = '';
  $.each(children, function (_, child) {
    var hasKids = child.children && child.children.length > 0;

    if (hasKids) {
      html +=
        '<li class="pc-item pc-hasmenu lvl2">' +
          '<a href="javascript:void(0);" class="pc-link">' +
            '<span class="pc-mtext">' + esc(child.menu_name) + '</span>' +
            CHEVRON +
          '</a>' +
          '<ul class="pc-submenu pc-submenu-lvl3">' +
            buildLevel3(child.children) +
          '</ul>' +
        '</li>';
    } else {
      var href = menuRouteBase + '/' + (child.route || '').replace(/\./g, '/');
      html +=
        '<li class="pc-item lvl2">' +
          '<a href="' + href + '" class="pc-link menu-leaf">' +
            '<span class="pc-mtext">' + esc(child.menu_name) + '</span>' +
          '</a>' +
        '</li>';
    }
  });
  return html;
}

/* ════════════════════════════════════════
   LEVEL-3 builder  (NO icon)
   ════════════════════════════════════════ */
function buildLevel3(subChildren) {
  var html = '';
  $.each(subChildren, function (_, sub) {
    var href = menuRouteBase + '/' + (sub.route || '').replace(/\./g, '/');
    html +=
      '<li class="pc-item lvl3">' +
        '<a href="' + href + '" class="pc-link menu-leaf">' +
          '<span class="pc-mtext">' + esc(sub.menu_name) + '</span>' +
        '</a>' +
      '</li>';
  });
  return html;
}

/* ════════════════════════════════════════
   ACTIVE PAGE DETECTION
   Runs AFTER menu HTML is injected.
   Strategy (in order):
   1. Compare each link's pathname with window.location.pathname
   2. Fallback: check sessionStorage for the last clicked href
   ════════════════════════════════════════ */
function activateCurrentPage($c) {
  var locationPath = window.location.pathname;   /* e.g. /hms/superadmin/dashboard */
  var foundLink    = null;

  /* --- Strategy 1: pathname match --- */
  $c.find('a.menu-leaf').each(function () {
    try {
      var linkPath = new URL(this.href).pathname;  /* e.g. /hms/superadmin/dashboard */
      /* exact match OR current is a sub-path of the link */
      if (locationPath === linkPath ||
          locationPath.indexOf(linkPath + '/') === 0) {
        foundLink = this;
        return false; /* break $.each */
      }
    } catch (e) { /* ignore malformed href */ }
  });

  /* --- Strategy 2: sessionStorage fallback --- */
  if (!foundLink) {
    var saved = sessionStorage.getItem('menu_active_href');
    if (saved) {
      $c.find('a.menu-leaf').each(function () {
        if (this.href === saved) {
          foundLink = this;
          return false;
        }
      });
    }
  }

  /* --- Strategy 3: longest partial match (handles query-string pages) --- */
  if (!foundLink) {
    var bestLen = 0;
    $c.find('a.menu-leaf').each(function () {
      try {
        var linkPath = new URL(this.href).pathname;
        if (linkPath !== '/' && locationPath.indexOf(linkPath) === 0 && linkPath.length > bestLen) {
          bestLen   = linkPath.length;
          foundLink = this;
        }
      } catch (e) {}
    });
  }

  /* --- Apply active state --- */
  if (foundLink) {
    activateLink($(foundLink), $c);
    /* Keep sessionStorage in sync */
    sessionStorage.setItem('menu_active_href', foundLink.href);
  }
}

/* ════════════════════════════════════════
   APPLY ACTIVE TO A LEAF LINK
   ════════════════════════════════════════ */
function activateLink($link, $c) {
  /* Clear all active states first */
  $c.find('.pc-item.active').removeClass('active');
  $c.find('.pc-hasmenu.open').removeClass('open')
    .children('.pc-submenu, .pc-submenu-lvl3').hide();

  /* Mark this leaf item active */
  $link.closest('.pc-item').addClass('active');

  /* Walk up: open every ancestor hasmenu */
  $link.parents('.pc-item.pc-hasmenu').each(function () {
    $(this).addClass('active open');
    $(this).children('.pc-submenu, .pc-submenu-lvl3').show();
  });

  /* Build dynamic breadcrumb and expose as window._currentModule */
  var parts = [];
  /* Ancestors from outermost → innermost */
  var $ancestors = $link.parents('.pc-item.pc-hasmenu');
  $ancestors.toArray().reverse().forEach(function (el) {
    var txt = $(el).children('.pc-link').find('.pc-mtext').text().trim();
    if (txt) parts.push(txt);
  });
  /* Leaf label */
  var leafTxt = $link.find('.pc-mtext').text().trim();
  if (leafTxt) parts.push(leafTxt);

  window._currentModule = parts.length ? parts.join(' > ') : (leafTxt || 'Dashboard');
}

/* ════════════════════════════════════════
   EVENT BINDING
   ════════════════════════════════════════ */
function bindMenuEvents($c) {

  /* Dropdown accordion toggle */
  $c.off('click.menudrop').on('click.menudrop', '.pc-hasmenu > .pc-link', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $item    = $(this).parent('.pc-hasmenu');
    var $submenu = $item.children('.pc-submenu, .pc-submenu-lvl3');
    var isOpen   = $item.hasClass('open');

    /* Close siblings at the same depth only */
    $item.siblings('.pc-hasmenu.open')
         .removeClass('open')
         .children('.pc-submenu, .pc-submenu-lvl3').slideUp(160);

    if (isOpen) {
      $item.removeClass('open');
      $submenu.slideUp(160);
    } else {
      $item.addClass('open');
      $submenu.slideDown(160);
    }
  });

  /* Leaf click: save to sessionStorage + instant highlight */
  $c.off('click.menuactive').on('click.menuactive', 'a.menu-leaf', function () {
    sessionStorage.setItem('menu_active_href', this.href);
    activateLink($(this), $c);
    /* Let browser navigate normally (no e.preventDefault) */
  });
}

/* ── Helpers ── */
function esc(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function handleErrormenu(xhr, status, error) {
  console.error('Menu AJAX Error:', status, error);
}
