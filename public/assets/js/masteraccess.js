/* ════════════════════════════════════════
   Access Master — JS
   New UI: menu filter, export CSV/XLSX,
   custom am-dd dropdowns, stats, chips
════════════════════════════════════════ */

'use strict';

// ── State ──────────────────────────────
let employeeDataSource  = [];   // raw from server
let currentFilteredData = [];   // after filters
let currentPage         = 1;
let currentPageSize     = 10;

let currentFilters = {
    role:   '',
    zone:   '',
    branch: '',
    name:   '',
    empnum: '',
    menu:   '',     // menu_id display label
    status: '',     // active | inactive | not_used
};
let currentMenuId   = '';  // numeric menu id for export

// ── Helpers ────────────────────────────
function debounce(fn, wait) {
    let t;
    return function (...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), wait); };
}

// ── Custom am-dd dropdown component ───
function initAmDropdown(wrapperId, onSelect) {
    const $w  = $('#' + wrapperId);
    const $in = $w.find('.am-dd-input');
    const $s  = $w.find('.am-dd-search');

    // Toggle open/close
    $in.on('click', function (e) {
        e.stopPropagation();
        const wasOpen = $w.hasClass('open');
        closeAllDropdowns();
        if (!wasOpen) { $w.addClass('open'); $s.val('').trigger('input').focus(); }
    });

    // Live search
    $s.on('input', function () {
        const q = $(this).val().toLowerCase();
        $w.find('.am-dd-opt').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(q));
        });
    }).on('click', function (e) { e.stopPropagation(); });

    // Option click
    $(document).on('click', '#' + wrapperId + ' .am-dd-opt', function (e) {
        e.stopPropagation();
        const val  = $(this).attr('data-value');
        const text = $(this).text();
        $in.val(text);
        $w.find('.am-dd-opt').removeClass('selected');
        $(this).addClass('selected');
        $w.removeClass('open');
        onSelect(val, text);
    });
}

function closeAllDropdowns() {
    $('.am-dd').removeClass('open');
}

$(document).on('click', function (e) {
    if (!$(e.target).closest('.am-dd').length) closeAllDropdowns();
});

// ── Populate am-dd options ─────────────
function setDdOptions(containerId, items) {
    // items: [{value, label}]
    const html = items.map(i =>
        `<div class="am-dd-opt" data-value="${escHtml(String(i.value))}">${escHtml(i.label)}</div>`
    ).join('');
    $('#' + containerId).html(html || '<div class="am-dd-opt text-muted">No options</div>');
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Chips ──────────────────────────────
function renderChips() {
    const labels = {
        role: 'Designation', zone: 'Zone', branch: 'Branch',
        name: 'Employee', empnum: 'Search', menu: 'Menu', status: 'Status'
    };
    const area = $('#amChipsArea');
    area.find('.am-chip').remove();

    let hasAny = false;
    Object.entries(currentFilters).forEach(([k, v]) => {
        if (!v) return;
        hasAny = true;
        const displayVal = k === 'menu'
            ? ($('#menu-views').val() || v)
            : v;
        const chip = $(`
            <span class="am-chip" data-filter="${k}">
                <i class="bi bi-tag-fill" style="font-size:.65rem;"></i>
                <span>${labels[k]}: ${escHtml(displayVal)}</span>
                <span class="am-chip-x" data-filter="${k}">&times;</span>
            </span>`);
        area.prepend(chip);
    });
    $('#amClearAll').toggle(hasAny);
}

$(document).on('click', '.am-chip-x', function (e) {
    e.stopPropagation();
    const key = $(this).attr('data-filter');
    clearFilter(key);
});

$('#amClearAll').on('click', function () {
    Object.keys(currentFilters).forEach(k => { currentFilters[k] = ''; });
    currentMenuId = '';
    $('#role-views,#zone-views,#branch-views,#name-views,#menu-views').val('');
    $('#employee_num').val('');
    $('.am-dd-opt').removeClass('selected');
    $('.am-stat').removeClass('am-stat-active-filter');
    renderChips();
    currentPage = 1;
    applyFilters();
});

function clearFilter(key) {
    currentFilters[key] = '';
    if (key === 'menu') { currentMenuId = ''; $('#menu-views').val(''); }
    else if (key === 'status') { $('.am-stat').removeClass('am-stat-active-filter'); }
    else { $(`#${key}-views`).val(''); }
    if (key === 'zone') {
        currentFilters.branch = '';
        $('#branch-views').val('');
        populateBranchFilter(employeeDataSource);
    }
    if (key === 'empnum') { $('#employee_num').val(''); }
    $('.am-dd-opt').removeClass('selected');
    renderChips();
    currentPage = 1;
    applyFilters();
}

// ── Per-page ───────────────────────────
$('#access-items-per-page').on('change', function () {
    currentPageSize = parseInt($(this).val());
    currentPage = 1;
    renderEmployeeTable();
    renderPagination();
});

// ── Fetch employee data ────────────────
function fetchEmployeeData() {
    $('#employee_details1').show();
    $('#employee_details').hide();

    $.ajax({
        url: amEmployeeUrl,
        type: 'GET',
        success: function (response) {
            $('#employee_details1').hide();
            $('#employee_details').show();

            employeeDataSource = response.data || [];
            const notUsed = employeeDataSource.filter(e => e.active_status === null).length;
            $('#total_access_count').text(response.total || 0);
            $('#total_active_count').text(response.activeCount || 0);
            $('#total_inactive_count').text(response.inactiveCount || 0);
            $('#total_notused_count').text(notUsed);

            if (response.autoInactivated > 0) {
                toastr.info(
                    `${response.autoInactivated} user(s) not found in HRM — automatically marked Inactive.`,
                    'Auto-Inactive Update',
                    { timeOut: 6000 }
                );
            }

            buildFilterOptions(employeeDataSource);
            buildMenuOptions();
            applyFilters();
        },
        error: function () {
            $('#employee_details1').hide();
            $('#employee_details').show().html(
                '<tr><td colspan="11" class="text-center text-danger py-4">Error loading data. Please refresh.</td></tr>'
            );
        }
    });
}

// ── Build filter options ───────────────
function buildFilterOptions(data) {
    // Designation
    const roles = [...new Set(data.map(e => e.role?.name))].filter(Boolean).sort();
    setDdOptions('roles-options', roles.map(r => ({ value: r, label: r })));

    // Zones
    const zones = [...new Set(data.map(e => e.zone_name))].filter(Boolean).sort();
    setDdOptions('zones-options', zones.map(z => ({ value: z, label: z })));

    // Branches (all)
    populateBranchFilter(data);

    // Names (all)
    populateNameFilter(data);
}

function populateBranchFilter(data) {
    const branches = [...new Set(data.map(e => e.branch_name))].filter(Boolean).sort();
    setDdOptions('branches-options', branches.map(b => ({ value: b, label: b })));
}

function populateNameFilter(data) {
    const names = [...new Set(data.map(e => e.user?.name))].filter(Boolean).sort();
    setDdOptions('names-options', names.map(n => ({ value: n, label: n })));
}

function buildMenuOptions() {
    if (typeof amMenus === 'undefined' || !amMenus || !amMenus.length) return;
    // DB column is 'menu_name'
    const opts = amMenus.map(m => ({ value: m.id, label: m.menu_name || m.name || String(m.id) }));
    setDdOptions('menus-options', opts);
}

// ── Init custom dropdowns ──────────────
$(document).ready(function () {
    initAmDropdown('ddRole', function (val) {
        currentFilters.role = val;
        renderChips(); currentPage = 1; applyFilters();
    });
    initAmDropdown('ddZone', function (val) {
        currentFilters.zone = val;
        // narrow branch options
        const filtered = val ? employeeDataSource.filter(e => e.zone_name === val) : employeeDataSource;
        populateBranchFilter(filtered);
        if (!filtered.some(e => e.branch_name === currentFilters.branch)) {
            currentFilters.branch = ''; $('#branch-views').val('');
        }
        renderChips(); currentPage = 1; applyFilters();
    });
    initAmDropdown('ddBranch', function (val) {
        currentFilters.branch = val;
        const filtered = val ? employeeDataSource.filter(e => e.branch_name === val) : employeeDataSource;
        populateNameFilter(filtered);
        if (!filtered.some(e => e.user?.name === currentFilters.name)) {
            currentFilters.name = ''; $('#name-views').val('');
        }
        renderChips(); currentPage = 1; applyFilters();
    });
    initAmDropdown('ddName', function (val) {
        currentFilters.name = val;
        renderChips(); currentPage = 1; applyFilters();
    });
    initAmDropdown('ddMenu', function (val, text) {
        currentFilters.menu = text;
        currentMenuId = val;
        renderChips(); currentPage = 1; applyFilters();
    });

    // Quick search
    $('#employee_num').on('input', debounce(function () {
        currentFilters.empnum = $(this).val().trim();
        renderChips(); currentPage = 1; applyFilters();
    }, 280));

    // Stat card click → filter by status
    $(document).on('click', '.am-stat[data-status]', function () {
        const st = $(this).attr('data-status');
        if (currentFilters.status === st) {
            // toggle off
            currentFilters.status = '';
            $(this).removeClass('am-stat-active-filter');
        } else {
            currentFilters.status = st;
            $('.am-stat').removeClass('am-stat-active-filter');
            $(this).addClass('am-stat-active-filter');
        }
        renderChips();
        currentPage = 1;
        applyFilters();
    });

    // Kick off
    fetchEmployeeData();
});

// ── Apply filters ──────────────────────
function applyFilters() {
    const q = (currentFilters.empnum || '').toLowerCase();
    const menuIdNum = currentMenuId ? parseInt(currentMenuId) : null;

    currentFilteredData = employeeDataSource.filter(emp => {
        if (currentFilters.role   && emp.role?.name   !== currentFilters.role)   return false;
        if (currentFilters.zone   && emp.zone_name    !== currentFilters.zone)   return false;
        if (currentFilters.branch && emp.branch_name  !== currentFilters.branch) return false;
        if (currentFilters.name   && emp.user?.name   !== currentFilters.name)   return false;
        if (currentFilters.status === 'active'   && emp.active_status != 0) return false;
        if (currentFilters.status === 'inactive' && emp.active_status != 1) return false;
        if (currentFilters.status === 'not_used' && emp.active_status !== null) return false;
        if (menuIdNum !== null) {
            const allowed = emp.allowed_menu_ids || [];
            if (!allowed.map(Number).includes(menuIdNum)) return false;
        }
        if (q) {
            const hay = [
                emp.id, emp.user?.name, emp.role?.name,
                emp.branch_name, emp.zone_name, emp.user_fullname,
                emp.username, emp.status_modified_name,
                emp.active_status === null ? 'not used' : emp.active_status == 0 ? 'active' : 'inactive'
            ].join(' ').toLowerCase();
            if (!hay.includes(q)) return false;
        }
        return true;
    });

    renderEmployeeTable();
    renderPagination();
}

// ── Render table ───────────────────────
function renderEmployeeTable() {
    if (!$('#employee_details').length) return;

    const data = currentFilteredData;
    const start = (currentPage - 1) * currentPageSize;
    const page  = data.slice(start, start + currentPageSize);
    const total = data.length;

    $('#access-count').text(total);
    const from = total ? start + 1 : 0;
    const to   = Math.min(start + currentPageSize, total);
    $('#amPageInfo').text(`Showing ${from}–${to} of ${total} records`);

    if (!page.length) {
        $('#employee_details').html('<tr><td colspan="11" class="text-center py-5 text-muted">No records found</td></tr>');
        return;
    }

    let html = '';
    page.forEach((emp, idx) => {
        const statusBadge = emp.active_status === null
            ? `<span class="am-badge badge-notused">Not Used</span>`
            : emp.active_status == 0
                ? `<span class="am-badge badge-active status-badge" data-id="${emp.user_id}" data-status="0" title="Click to change">Active</span>`
                : `<span class="am-badge badge-inactive status-badge" data-id="${emp.user_id}" data-status="1" title="Click to change">Inactive</span>`;

        const menuCount = (emp.allowed_menu_ids || []).length;
        const menuBadge = `<span class="badge" style="background:#ece9ff;color:#4f52c9;font-size:.72rem;">${menuCount} menus</span>`;

        html += `
        <tr>
            <td class="text-muted" style="font-size:.75rem;">${start + idx + 1}</td>
            <td><strong>#${escHtml(emp.id || '')}</strong></td>
            <td>${escHtml(emp.user?.name || 'N/A')}</td>
            <td><span style="font-size:.78rem;color:#6b7280;">${escHtml(emp.role?.name || '—')}</span></td>
            <td>${escHtml(emp.branch_name || '—')}</td>
            <td>${escHtml(emp.zone_name || '—')}</td>
            <td>${statusBadge}</td>
            <td style="font-size:.76rem;">
                ${emp.status_modified_name ? escHtml(emp.status_modified_name) : '—'}<br>
                <span class="text-muted">${emp.status_changed_on ? escHtml(emp.status_changed_on) : ''}</span>
            </td>
            <td>${menuBadge}</td>
            <td>
                <i class="bi bi-key-fill edit-permission"
                   style="cursor:pointer;color:#6a6ee4;font-size:1rem;"
                   data-id="${escHtml(emp.id)}" title="Edit Permissions"></i>
            </td>
            <td>
                <i class="bi bi-eye-fill view-employee"
                   style="cursor:pointer;color:#6a6ee4;font-size:1rem;"
                   data-id="${escHtml(emp.id)}" title="View Details"></i>
            </td>
        </tr>`;
    });

    $('#employee_details').html(html);
}

// ── Render pagination ──────────────────
function renderPagination() {
    const total = currentFilteredData.length;
    const totalPages = Math.ceil(total / currentPageSize);
    let html = '';

    html += `<button class="page-btn" data-page="prev" ${currentPage <= 1 ? 'disabled' : ''}>&laquo;</button>`;
    const max = 5;
    let sp = Math.max(1, currentPage - Math.floor(max / 2));
    let ep = Math.min(totalPages, sp + max - 1);
    if (ep - sp + 1 < max) sp = Math.max(1, ep - max + 1);

    if (sp > 1) { html += `<button class="page-btn" data-page="1">1</button>`; if (sp > 2) html += `<span class="dots">…</span>`; }
    for (let i = sp; i <= ep; i++) {
        html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
    }
    if (ep < totalPages) { if (ep < totalPages - 1) html += `<span class="dots">…</span>`; html += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`; }
    html += `<button class="page-btn" data-page="next" ${currentPage >= totalPages ? 'disabled' : ''}>&raquo;</button>`;

    const $pager = $('#access-pagination');
    $pager.html(html);

    $pager.find('.page-btn').off('click').on('click', function () {
        const p = $(this).attr('data-page');
        const tp = Math.ceil(currentFilteredData.length / currentPageSize);
        if (p === 'prev' && currentPage > 1) currentPage--;
        else if (p === 'next' && currentPage < tp) currentPage++;
        else if (!isNaN(p)) currentPage = parseInt(p);
        renderEmployeeTable();
        renderPagination();
    });
}

// ── Export ─────────────────────────────
function buildExportUrl(format) {
    const params = {
        format: format,
        role:   currentFilters.role,
        zone:   currentFilters.zone,
        branch: currentFilters.branch,
        name:   currentFilters.name,
        empnum: currentFilters.empnum,
        status: currentFilters.status,
    };
    if (currentMenuId) params.menu_id = currentMenuId;
    const qs = Object.entries(params)
        .filter(([, v]) => v)
        .map(([k, v]) => encodeURIComponent(k) + '=' + encodeURIComponent(v))
        .join('&');
    return amExportUrl + (qs ? '?' + qs : '');
}

$('#btnExportCsv').on('click', function () {
    window.location.href = buildExportUrl('csv');
});
$('#btnExportXlsx').on('click', function () {
    window.location.href = buildExportUrl('xlsx');
});

// ── View details ───────────────────────
$(document).on('click', '.view-employee', function (e) {
    e.stopPropagation();
    viewEmployeeDetails($(this).attr('data-id'));
});

function viewEmployeeDetails(employeeId) {
    const emp = employeeDataSource.find(e => e.id == employeeId);
    if (!emp) { alert('Employee not found. Please refresh.'); return; }

    $('#view-access-id').text(emp.id);
    $('#view-employee-id').text(emp.id);
    $('#view-employee-name').text(emp.user?.name || 'N/A');
    $('#view-role').text(emp.role?.name || '—');
    $('#view-branch').text(emp.branch_name || '—');
    $('#view-zone').text(emp.zone_name || '—');
    $('#view-employee-created').text(emp.created_by_username || '—');
    $('#view-employee-modified-time').text(emp.permission_modified_at || '—');

    $.ajax({
        url: 'get-user-details', type: 'GET', data: { employee_id: employeeId },
        success: function (r) {
            $('#view-employee-email').text(r.email || 'N/A');
            $('#view-reporting-manager').text(r.reporting_manager_name || 'Not assigned');
            $('#view-zonal-head').text(r.zonal_head ? 'Yes' : 'No');
            renderViewPermissionsTable(r.menus, r.user_permissions);
            $('#access-view-modal').modal('show');
        },
        error: function () { alert('Error loading employee details.'); }
    });
}

function renderViewPermissionsTable(menus, userPermissions) {
    const menuMap = {};
    menus.forEach(m => { if (m.sub_menus === 0) menuMap[m.id] = { menu: m, subs: [] }; });
    menus.forEach(m => { if (m.sub_menus !== 0 && menuMap[m.sub_menus]) menuMap[m.sub_menus].subs.push(m); });

    let html = '';
    for (const { menu, subs } of Object.values(menuMap)) {
        const has = userPermissions.includes(parseInt(menu.id));
        html += `<tr><td><strong>${escHtml(menu.menu_name)}</strong></td><td></td>
            <td><span class="badge ${has ? 'bg-success' : 'bg-secondary'}">${has ? 'Enabled' : 'Disabled'}</span></td></tr>`;
        subs.forEach(s => {
            const sh = userPermissions.includes(parseInt(s.id));
            html += `<tr><td></td><td>${escHtml(s.menu_name)}</td>
                <td><span class="badge ${sh ? 'bg-success' : 'bg-secondary'}">${sh ? 'Enabled' : 'Disabled'}</span></td></tr>`;
        });
    }
    $('#view-permissions-table').html(html || '<tr><td colspan="3" class="text-center">No permissions assigned</td></tr>');
}

// ── Edit permissions ───────────────────
$(document).on('click', '.edit-permission', function (e) {
    e.stopPropagation();
    openPermissionModal($(this).attr('data-id'));
});

function openPermissionModal(employeeId) {
    const emp = employeeDataSource.find(e => e.id == employeeId);
    if (!emp) { alert('Employee not found'); return; }

    $('#permission-employee-id').text(emp.id);
    $('#perm-employee-id').val(emp.id);
    $('#perm-employee-name').val(emp.user?.name || 'N/A');
    $('#perm-branch').val(emp.branch_name || '—');
    $('#perm-zone').val(emp.zone_name || '—');
    $('#perm-branch-id').val(emp.branch_id ?? '');
    $('#perm-zone-id').val(emp.zone_id ?? '');
    $('#multi-location').val(emp.multi_location_name ?? '');
    $('#location_ids').val(emp.multi_location ?? '');

    fetchUserDetails(emp.id);
    $('#permission-modal').modal('show');
}

function fetchUserDetails(employeeId) {
    $.ajax({
        url: 'get-user-details', type: 'GET', data: { employee_id: employeeId },
        success: function (r) {
            $('#perm-role').val(r.role_id || 3);
            if (r.email) $('#perm-email').val(r.email);
            $('#perm-zonal-head').val(r.zonal_head ? '1' : '0');
            populateReportingManagers(r.managers, r.reporting_manager);
            fetchMenuPermissions(employeeId);
        },
        error: function () { fetchMenuPermissions(employeeId); }
    });
}

function populateReportingManagers(managers, selected) {
    const $s = $('#perm-reporting-manager').empty().append('<option value="">Select Reporting Manager</option>');
    (managers || []).forEach(m => {
        $s.append(`<option value="${m.id}" ${String(m.id) === String(selected) ? 'selected' : ''}>${escHtml(m.user_fullname)} (${escHtml(m.username)})</option>`);
    });
}

function fetchMenuPermissions(employeeId) {
    $.ajax({
        url: 'get-menu-permissions', type: 'GET', data: { employee_id: employeeId },
        success: function (r) {
            $('#perm-role').val(r.role_id || 3);
            renderMenuPermissions(r.menus || [], r.user_permissions || []);
        }
    });
}

function renderMenuPermissions(menus, perms) {
    const menuMap = {};
    menus.forEach(m => { if (m.sub_menus === 0) menuMap[m.id] = { menu: m, subs: [] }; });
    menus.forEach(m => { if (m.sub_menus !== 0 && menuMap[m.sub_menus]) menuMap[m.sub_menus].subs.push(m); });

    let html = '';
    for (const { menu, subs } of Object.values(menuMap)) {
        const chk = perms.includes(parseInt(menu.id));
        html += `<tr><td>${escHtml(menu.menu_name)}</td><td></td>
            <td class="text-center"><input type="checkbox" class="menu-checkbox" data-menu-id="${menu.id}" ${chk ? 'checked' : ''}></td></tr>`;
        subs.forEach(s => {
            const sc = perms.includes(parseInt(s.id));
            html += `<tr><td></td><td>${escHtml(s.menu_name)}</td>
                <td class="text-center"><input type="checkbox" class="menu-checkbox" data-menu-id="${s.id}" ${sc ? 'checked' : ''}></td></tr>`;
        });
    }
    $('#permission-table-body').html(html || '<tr><td colspan="3" class="text-center">No menus available</td></tr>');
}

$('#save-permissions').on('click', function () {
    const menus = [];
    $('.menu-checkbox:checked').each(function () { menus.push($(this).attr('data-menu-id')); });

    $.ajax({
        url: 'save-permissions', type: 'POST',
        data: {
            employee_id: $('#perm-employee-id').val(),
            employee_name: $('#perm-employee-name').val(),
            role_id: $('#perm-role').val(),
            email: $('#perm-email').val(),
            password: $('#perm-password').val(),
            reporting_manager: $('#perm-reporting-manager').val(),
            zonal_head: $('#perm-zonal-head').val(),
            branch_id: $('#perm-branch-id').val(),
            zone_id: $('#perm-zone-id').val(),
            menus: menus,
            multiLocId: $('#location_ids').val(),
            multiLocNames: $('#multi-location').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function () {
            toastr.success('Permissions saved successfully');
            $('#permission-modal').modal('hide');
            fetchEmployeeData();
        },
        error: function () { toastr.error('Error saving permissions.'); }
    });
});

// Select-all menus
$('#selectAllMenus').on('change', function () {
    $('.menu-checkbox').prop('checked', $(this).prop('checked'));
});
$(document).on('change', '.menu-checkbox', function () {
    const total = $('.menu-checkbox').length, checked = $('.menu-checkbox:checked').length;
    $('#selectAllMenus').prop('checked', total === checked);
});

// ── Status badge click ─────────────────
let selectedUserId = null, currentStatus = null;

$(document).on('click', '.status-badge', function () {
    selectedUserId = $(this).attr('data-id');
    currentStatus  = $(this).attr('data-status');
    if (currentStatus == 0) $('#statusActive').prop('checked', true);
    else $('#statusInactive').prop('checked', true);
    $('#statusDate').val('');
    $('#statusModal').modal('show');
});

$('#confirmStatusChange').on('click', function () {
    const ns   = $('input[name="active_status"]:checked').val();
    const date = $('#statusDate').val();
    if (!date) { toastr.warning('Please select an effective date.'); return; }

    $.ajax({
        url: updateStatusUrl, type: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), user_id: selectedUserId, active_status: ns, status_date: date },
        success: function () {
            $('#statusModal').modal('hide');
            toastr.success('User status updated.');
            fetchEmployeeData();
        },
        error: function () { toastr.error('Failed to update user status.'); }
    });
});

// ── Multi-location picker ──────────────
$(document).ready(function () {
    let selectedLocations = new Set();
    let selectedNames     = new Set();

    $('#multi-location').on('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        const $d = $('#locationDropdown');
        const was = $d.hasClass('show');
        $('.dropdown-menu').removeClass('show');
        if (!was) { $d.addClass('show'); $('#locationSearch').focus(); }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('#locationDropdown').removeClass('show');
        }
    });
    $('#locationDropdown').on('click', function (e) { e.stopPropagation(); });

    $(document).on('keyup', '#locationSearch', function () {
        const q = $(this).val().toLowerCase();
        $('.location-item').each(function () {
            $(this).toggleClass('d-none', !$(this).find('.location-name').text().toLowerCase().includes(q));
        });
    });

    window.toggleLocation = function (el) {
        const $el = $(el), id = $el.attr('data-id'), name = $el.attr('data-name');
        if (selectedLocations.has(id)) {
            selectedLocations.delete(id); selectedNames.delete(name);
            $el.find('.tick').hide(); $el.removeClass('bg-light');
        } else {
            selectedLocations.add(id); selectedNames.add(name);
            $el.find('.tick').show(); $el.addClass('bg-light');
        }
        $('#multi-location').val([...selectedNames].join(', '));
        $('#location_ids').val([...selectedLocations].join(','));
        $('#locationDropdown').addClass('show');
    };
});
