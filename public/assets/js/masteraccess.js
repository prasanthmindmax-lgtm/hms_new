let employeeDataSource = [];
let currentPage = 1;
let currentPageSize = 10;
const employeeDataUrl = 'employee-data';

let currentFilters = {
    role: '',
    zone: '',
    branch: '',
    name: '',
    empnum: ''
};

$(document).ready(function() {
    document.addEventListener("DOMContentLoaded", function() {
        let el = document.getElementById('name-views');
        if (el) el.value = '';
    });
});

$(document).ready(function() {
    document.addEventListener("DOMContentLoaded", function() {
        let el = document.getElementById('name-views');
        if (el) el.value = '';
    });

    $('.access-values-search').val('');
    $('#name-views').val('');
    $('#employee_num').val('');

    // Initialize only if the required elements exist
    if ($('#employee_details').length && $('#access-items-per-page').length) {
        fetchEmployeeData();

        // Pagination dropdown change handler
        $('#access-items-per-page').change(function() {
            currentPageSize = parseInt($(this).val());
            currentPage = 1;
            renderEmployeeTable();
            renderPagination();
        });

        // Initialize all filters
        initializeFilters();

        // View employee details handler
        $(document).on('click', '.view-employee', function(e) {
            e.stopPropagation();
            const employeeId = $(this).data('id');
            viewEmployeeDetails(employeeId);
        });

        // Edit permissions handler
        $(document).on('click', '.edit-permission', function(e) {
            e.stopPropagation();
            const employeeId = $(this).data('id');
            openPermissionModal(employeeId);
        });

        // Name search with debounce
        if ($('#name-search').length) {
            $('#name-search').on('input', debounce(function() {
                currentFilters.name = $(this).val().trim();
                currentPage = 1;
                applyFilters();
            }, 300));
        }

        // Employee number search with debounce
        if ($('#employee_num').length) {
            $('#employee_num').on('input', debounce(function() {
                currentFilters.empnum = $(this).val().trim();
                currentPage = 1;
                applyFilters();

            }, 300));
        }
    }
});

// Fetch employee data from server with enhanced error handling
function fetchEmployeeData() {
    if (!$('#employee_details1').length || !$('#employee_details').length) return;

    $('#employee_details1').show();
    $('#employee_details').hide();

    $.ajax({
        url: employeeDataUrl,
        type: "GET",
        success: function(response) {
            $('#employee_details1').hide();
            $('#employee_details').show();

            employeeDataSource = response.data || [];
            $('#total_access_count').text(response.total || 0);
            $('#total_active_count').text(response.activeCount || 0);
            $('#total_inactive_count').text(response.inactiveCount || 0);
            $('#access-count').text(response.total || 0);

            populateFilters(response.data);
            applyFilters();
        },
        error: function(xhr, status, error) {
            $('#employee_details1').hide();
            console.error("Error fetching employee data:", error);
            if ($('#employee_details').length) {
                $('#employee_details').html(
                    '<tr><td colspan="7" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                );
            }
        }
    });
}

// Initialize filters with search functionality and element checks
function initializeFilters() {
    $('.access-values-search').val('');
    $('#name-views').val('');
    $('#employee_num').val('');

    if (!$('.access-values-search').length) return;

    // Show dropdown when input is focused - with better targeting
    $('.access-values-search').on('click', function(e) {
        e.stopPropagation(); // Prevent event from bubbling up
        $('.dropdown').removeClass('active'); // Close all other dropdowns first
        $(this).closest('.dropdown').addClass('active');
        const dropdownSearch = $(this).closest('.dropdown').find('.dropdown-search');
        if (dropdownSearch.length) {
            dropdownSearch.focus();
        }
    });

    // Hide dropdown when clicking outside - improved version
    $(document).on('click', function(e) {
        // Check if click is outside any dropdown or its children
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown').removeClass('active');
        }
    });

    // Search within dropdown options - prevent propagation
    $(document).on('input', '.dropdown-search', function(e) {
        e.stopPropagation();
        const target = $(this).data('target');
        const searchTerm = $(this).val().toLowerCase();
        $(`#${target}-options div`).each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    });

    // Clear all filters handler - with propagation prevention
    $(document).on('click', '.clear-all-access-views', function(e) {
        e.stopPropagation();
        // Clear all filters
        $('.access-values-search').val('');
        $('#employee_num').val('');
        currentFilters = { role: '', zone: '', branch: '', name: '', empnum: '' };

        // Reset dropdowns to show all options
        if (window.employeeFullData) {
            populateBranchFilter(window.employeeFullData);
            populateNameFilter(window.employeeFullData);
        }

        // Close all dropdowns
        $('.dropdown').removeClass('active');

        // Update UI and data
        $('.filter-values-container').empty();
        $(this).hide();
        renderEmployeeTable();
        renderPagination();
    });
}

function populateFilters(data) {
    if (!data) return;

    // Store the complete data for filtering
    window.employeeFullData = data;

    // Clear current filters (but keep UI selections)
    const uiState = {
        role: $('#role-views').val(),
        zone: $('#zone-views').val(),
        branch: $('#branch-views').val(),
        name: $('#name-views').val(),
        empnum: $('#employee_num').val(),
    };


    // Repopulate all filters
    populateRoleFilter(data);
    populateZoneFilter(data);

    // Only populate branch filter if zone is selected
    if (uiState.zone) {
        const filteredBranches = data.filter(employee =>
            employee.zone_name === uiState.zone
        );
        populateBranchFilter(filteredBranches);
    } else {
        populateBranchFilter(data);
    }

    // Only populate name filter if branch is selected
    if (uiState.branch) {
        const filteredNames = data.filter(employee =>
            employee.branch_name === uiState.branch
        );
        populateNameFilter(filteredNames);
    } else {
        populateNameFilter(data);
    }

    // Restore UI state
    if (uiState.role) currentFilters.role = uiState.role;
    if (uiState.zone) currentFilters.zone = uiState.zone;
    if (uiState.branch) currentFilters.branch = uiState.branch;
    if (uiState.name) currentFilters.name = uiState.name;
    if (uiState.empnum) currentFilters.empnum = uiState.empnum;

    showActiveFilters();
}

function populateZoneFilter(data) {
    if (!$('#zones-options').length) return;

    const zones = [...new Set(data.map(employee => employee.zone_name))].filter(Boolean);
    let zoneOptions = zones.map(zone => `<div data-value="${zone}">${zone}</div>`).join('');
    $('#zones-options').html(zoneOptions || '<div>No zones available</div>');
    setupFilterHandler('#zones-options div', 'zone', onZoneSelected);
}

function populateRoleFilter(data) {
    if (!$('#roles-options').length) return;

    const roles = [...new Set(data.map(employee => employee.role?.name))].filter(Boolean);
    let roleOptions = roles.map(role => `<div data-value="${role}">${role}</div>`).join('');
    $('#roles-options').html(roleOptions || '<div>No roles available</div>');
    setupFilterHandler('#roles-options div', 'role', applyFilters);
}

function populateBranchFilter(data) {
    if (!$('#branches-options').length) return;

    const branches = [...new Set(data.map(employee => employee.branch_name))].filter(Boolean);
    let branchOptions = branches.map(branch => `<div data-value="${branch}">${branch}</div>`).join('');
    $('#branches-options').html(branchOptions || '<div>No branches available</div>');
    setupFilterHandler('#branches-options div', 'branch', onBranchSelected);
}

function populateNameFilter(data) {
    if (!$('#names-options').length) return;

    const names = [...new Set(data.map(employee => employee.user?.name))].filter(Boolean);
    let nameOptions = names.map(name => `<div data-value="${name}">${name}</div>`).join('');
    $('#names-options').html(nameOptions || '<div>No employees available</div>');
    setupFilterHandler('#names-options div', 'name', applyFilters);
}

function onZoneSelected() {
    // Only reset branch if zone actually changed
    if (currentFilters.zone && window.employeeFullData) {
        const filteredBranches = window.employeeFullData.filter(employee =>
            employee.zone_name === currentFilters.zone
        );

        populateBranchFilter(filteredBranches);

        // Reset branch filter only if the new zone doesn't contain the current branch
        const branchExists = filteredBranches.some(emp =>
            emp.branch_name === currentFilters.branch
        );

        if (!branchExists) {
            currentFilters.branch = '';
            $('#branch-views').val('');
        }
    } else if (window.employeeFullData) {
        populateBranchFilter(window.employeeFullData);
    }

    showActiveFilters();
    debounce(applyFilters, 100)();
}

function onBranchSelected() {
    if (currentFilters.branch && window.employeeFullData) {
        const filteredUsers = window.employeeFullData.filter(employee =>
            employee.branch_name === currentFilters.branch
        );

        populateNameFilter(filteredUsers);

        // Reset name filter only if the new branch doesn't contain the current name
        const nameExists = filteredUsers.some(emp =>
            emp.user?.name === currentFilters.name
        );

        if (!nameExists) {
            currentFilters.name = '';
            $('#name-views').val('');
        }
    } else if (window.employeeFullData) {
        populateNameFilter(window.employeeFullData);
    }

    showActiveFilters();
    debounce(applyFilters, 100)();
}

function setupFilterHandler(selector, filterType, callback = null) {
    $(document).on('click', selector, function() {
        const filterValue = $(this).text();

        // Only proceed if the filter value actually changed
        if (currentFilters[filterType] !== filterValue) {
            currentFilters[filterType] = filterValue;
            $(`#${filterType}-views`).val(filterValue);
            $(this).closest('.dropdown').removeClass('active');

            showActiveFilters();
            $('.clear-all-access-views').show();

            if (callback) {
                callback();
            } else {
                // Debounce the filter application
                debounce(applyFilters, 100)();
            }
        }
    });
}

function showActiveFilters() {
    const container = $('.filter-values-container');
    if (!container.length) return;

    container.empty();

    Object.entries(currentFilters).forEach(([key, value]) => {
        if (value) {
            const filterName = getFilterDisplayName(key);
            container.append(`
                <span class="badge bg-primary me-1 mb-1 filter-badge" data-filter="${key}" style="cursor: pointer;">
                    ${filterName}: ${value}
                    <i class="fas fa-times ms-1" data-filter="${key}"></i>
                </span>
            `);
        }
    });

    $('.clear-all-access-views').toggle(Object.values(currentFilters).some(v => v));
}

function getFilterDisplayName(filterKey) {
    const names = {
        'role': 'Designation',
        'zone': 'Zone',
        'branch': 'Branch',
        'name': 'Employee',
        'empnum': 'Employee Number'
    };
    return names[filterKey] || filterKey;
}

// Update the remove filter handler to work on the entire badge
$(document).on('click', '.filter-badge', function(e) {
    e.stopPropagation();
    const filterKey = $(this).data('filter');

    // Don't remove if clicking specifically on something else inside the badge
    if ($(e.target).hasClass('fa-times') || $(e.target).parent().hasClass('filter-badge')) {
        currentFilters[filterKey] = '';
        $(`#${filterKey}-views`).val('');

        if (filterKey === 'empnum') {
            $('#employee_num').val('');
        }

        if (filterKey === 'zone') {
            currentFilters.branch = '';
            $('#branch-views').val('');
        }

        showActiveFilters();
        applyFilters();
    }
});

$(document).on('click', '.remove-filter', function(e) {
    e.stopPropagation();
    const filterKey = $(this).data('filter');

    currentFilters[filterKey] = '';
    $(`#${filterKey}-views`).val('');

    if (filterKey === 'empnum') {
        $('#employee_num').val('');
    }

    if (filterKey === 'zone') {
        currentFilters.branch = '';
        $('#branch-views').val('');
    }

    showActiveFilters();
    applyFilters();
});

function applyFilters() {
    if (!window.employeeFullData) return;

    const searchText = (currentFilters.empnum || '').toLowerCase().trim();

    currentFilteredData = window.employeeFullData.filter(employee => {
        let statusText = 'not used';
        if (employee.active_status == 0) statusText = 'active';
        else if (employee.active_status == 1) statusText = 'inactive';
        else if (employee.active_status == null) statusText = 'not used';

        const searchTarget = `
            ${employee.id || ''}
            ${employee.user?.name || ''}
            ${employee.role?.name || ''}
            ${employee.branch_name || ''}
            ${employee.zone_name || ''}
            ${employee.user_fullname || ''}
            ${employee.username || ''}
            ${employee.status_modified_name || ''}
            ${statusText}
        `.toLowerCase();

        const roleMatch = !currentFilters.role || (employee.role?.name === currentFilters.role);
        const zoneMatch = !currentFilters.zone || (employee.zone_name === currentFilters.zone);
        const branchMatch = !currentFilters.branch || (employee.branch_name === currentFilters.branch);
        const nameMatch = !currentFilters.name || (employee.user?.name === currentFilters.name);
        const textMatch = !searchText || searchTarget.includes(searchText);

        return roleMatch && zoneMatch && branchMatch && nameMatch && textMatch;
    });

    currentPage = 1; // reset to first page when filters change

    renderEmployeeTable(currentFilteredData);
    renderPagination(currentFilteredData);
}


function renderEmployeeTable(data = null) {
    if (!$('#employee_details').length) return;

    // const displayData = data || employeeDataSource;
    const displayData = data || currentFilteredData.length ? currentFilteredData : employeeDataSource;

    const startIdx = (currentPage - 1) * currentPageSize;
    const endIdx = currentPage * currentPageSize;
    const pageData = displayData.slice(startIdx, endIdx);

    let tableHtml = '';

    if (pageData.length === 0) {
        tableHtml = '<tr><td colspan="7" class="text-center">No employee records found</td></tr>';
    } else {
        pageData.forEach(employee => {
            tableHtml += `
                <tr onclick="rowClick(event)">
                    <td class="tdview">
                        <strong>#${employee.id}</strong>
                    </td>
                    <td class="tdview">
                        ${employee.user?.name || 'N/A'}
                    </td>
                    <td class="tdview">
                        ${employee.role?.name || 'Not specified'}
                    </td>
                    <td class="tdview">
                        ${employee.branch_name || 'Unknown Branch'}
                    </td>
                    <td class="tdview">
                        ${employee.zone_name || 'Unknown Zone'}
                    </td>

                    <td class="tdview" style="cursor:pointer">
                        ${
                            employee.active_status === null
                                ? '<span class="badge bg-secondary">Not Used</span>'
                                : employee.active_status === 0
                                    ? `<span class="badge bg-success status-badge" data-id="${employee.user_id}" data-status="0">Active</span>`
                                    : `<span class="badge bg-danger status-badge" data-id="${employee.user_id}" data-status="1">Inactive</span>`
                        }
                    </td>
                     <td class="tdview">
                        ${employee.status_modified_name !==null && employee.status_modified_name !=='undefined'?employee.status_modified_name : '-'}</br>
                        ${employee.status_changed_on !==null ?employee.status_changed_on : '-'}</br>
                    </td>
                    <td class="tdview">
                        <i class="fas fa-key edit-permission"
                        style="cursor:pointer;color:#6777ef;"
                        data-id="${employee.id}"
                        title="Edit Permissions"></i>
                    </td>
                    <td class="tdview">
                        <i class="fas fa-eye view-employee"
                           style="cursor:pointer;color:#6777ef;"
                           data-id="${employee.id}"
                           title="View Details"></i>
                    </td>
                </tr>
            `;
        });
    }

    $('#employee_details').html(tableHtml);
    if ($('#access-count').length) {
        $('#access-count').text(displayData.length);
    }
}

function renderPagination(data = null) {
    if (!$('#access-pagination').length) return;

    const displayData = data || employeeDataSource;
    const totalItems = displayData.length;
    const totalPages = Math.ceil(totalItems / currentPageSize);
    let paginationHtml = '';

    paginationHtml += `<button class="page-btn" data-page="prev" ${currentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;

    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    if (startPage > 1) {
        paginationHtml += `<button class="page-btn" data-page="1">1</button>`;
        if (startPage > 2) {
            paginationHtml += `<span class="page-dots">...</span>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        paginationHtml += `<button class="page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += `<span class="page-dots">...</span>`;
        }
        paginationHtml += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`;
    }

    paginationHtml += `<button class="page-btn" data-page="next" ${currentPage === totalPages ? 'disabled' : ''}>&raquo;</button>`;

    $('#access-pagination').html(paginationHtml);

    // $('.page-btn').off('click').on('click', function() {
    //     const pageAction = $(this).data('page');

    //     if (pageAction === 'prev' && currentPage > 1) {
    //         currentPage--;
    //     } else if (pageAction === 'next' && currentPage < totalPages) {
    //         currentPage++;
    //     } else if (!isNaN(pageAction)) {
    //         currentPage = parseInt(pageAction);
    //     }

    //     renderEmployeeTable();
    //     renderPagination();
    // });
    $('.page-btn').off('click').on('click', function() {
        const pageAction = $(this).data('page');

        if (pageAction === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (pageAction === 'next' && currentPage < totalPages) {
            currentPage++;
        } else if (!isNaN(pageAction)) {
            currentPage = parseInt(pageAction);
        }

        renderEmployeeTable(currentFilteredData.length ? currentFilteredData : employeeDataSource);
        renderPagination(currentFilteredData.length ? currentFilteredData : employeeDataSource);
    });

}

function viewEmployeeDetails(employeeId) {
    const employee = employeeDataSource.find(item => item.id == employeeId);

    if (employee) {
        // Set basic information
        $('#view-access-id').text(employee.id);
        $('#view-employee-id').text(employee.id);
        $('#view-employee-name').text(employee.user?.name || 'N/A');
        $('#view-role').text(employee.role?.name || 'Not specified');
        $('#view-branch').text(employee.branch_name || 'Unknown Branch');
        $('#view-zone').text(employee.zone_name || 'Unknown Zone');
        $('#view-employee-created').text(employee.created_by_username || 'Unknown Zone');
        $('#view-employee-modified-time').text(employee.permission_modified_at || 'Unknown Zone');

        // Fetch additional details including permissions
        $.ajax({
            url: 'get-user-details',
            type: 'GET',
            data: { employee_id: employeeId },
            success: function(response) {
                // Set additional information
                $('#view-employee-email').text(response.email || 'N/A');
                $('#view-reporting-manager').text(
                    response.reporting_manager_name || 'Not assigned'
                );
                $('#view-zonal-head').text(
                    response.zonal_head ? 'Yes' : 'No'
                );

                // Render permissions table
                renderViewPermissionsTable(response.menus, response.user_permissions);

                // Show the modal
                $('#access-view-modal').modal('show');
            },
            error: function(xhr) {
                console.error('Error fetching user details:', xhr.responseText);
                alert('Error loading employee details. Please try again.');
            }
        });
    } else {
        alert('Employee details not found. Please refresh the page and try again.');
    }
}

function renderViewPermissionsTable(menus, userPermissions) {
    let html = '';
    const menuMap = {};

    // First pass: organize menus and submenus
    menus.forEach(menu => {
        if (menu.sub_menus === 0) {
            menuMap[menu.id] = {
                menu: menu,
                submenus: []
            };
        }
    });

    menus.forEach(menu => {
        if (menu.sub_menus !== 0 && menuMap[menu.sub_menus]) {
            menuMap[menu.sub_menus].submenus.push(menu);
        }
    });

    // Second pass: build the table rows
    for (const [menuId, menuData] of Object.entries(menuMap)) {
        const mainMenu = menuData.menu;
        const submenus = menuData.submenus;
        const hasPermission = userPermissions.includes(parseInt(mainMenu.id));

        // Main menu row
        html += `
            <tr>
                <td><strong>${mainMenu.menu_name}</strong></td>
                <td></td>
                <td>
                    <span class="badge bg-${hasPermission ? 'success' : 'secondary'}">
                        ${hasPermission ? 'Enabled' : 'Disabled'}
                    </span>
                </td>
            </tr>
        `;

        // Submenu rows
        submenus.forEach(submenu => {
            const subHasPermission = userPermissions.includes(parseInt(submenu.id));

            html += `
                <tr>
                    <td></td>
                    <td>${submenu.menu_name}</td>
                    <td>
                        <span class="badge bg-${subHasPermission ? 'success' : 'secondary'}">
                            ${subHasPermission ? 'Enabled' : 'Disabled'}
                        </span>
                    </td>
                </tr>
            `;
        });
    }

    $('#view-permissions-table').html(html || `
        <tr>
            <td colspan="3" class="text-center">No permissions assigned</td>
        </tr>
    `);
}

function openPermissionModal(employeeId) {
    if (!$('#permission-modal').length) return;

    const employee = employeeDataSource.find(item => item.id == employeeId);

    if (!employee) {
        alert('Employee not found');
        return;
    }
    console.log("employee",employee);

    $('#permission-employee-id').text(employee.id);
    $('#perm-employee-id').val(employee.id);
    $('#perm-employee-name').val(employee.user?.name || 'N/A');
    $('#perm-branch').val(employee.branch_name || 'Unknown Branch');
    $('#perm-zone').val(employee.zone_name || 'Unknown Zone');
    $('#perm-branch-id').val(employee.branch_id ?? '');
    $('#perm-zone-id').val(employee.zone_id ?? '');
    $('#multi-location').val(employee.multi_location_name ?? '');
    $('#location_ids').val(employee.multi_location ?? '');

    fetchUserDetails(employee.id);
    $('#permission-modal').modal('show');
}

function fetchUserDetails(employeeId) {
    if (!$('#perm-role').length) return;

    $.ajax({
        url: 'get-user-details',
        type: 'GET',
        data: { employee_id: employeeId },
        success: function(response) {
            $('#perm-role').val(response.role_id || 3);
            if (response.email && $('#perm-email').length) {
                $('#perm-email').val(response.email);
            }
            if (response.zonal_head !== undefined && $('#perm-zonal-head').length) {
                $('#perm-zonal-head').val(response.zonal_head ? '1' : '0');
            }

            populateReportingManagers(response.managers, response.reporting_manager);
            fetchMenuPermissions(employeeId);
        },
        error: function(xhr) {
            console.error('Error fetching user details:', xhr.responseText);
            fetchMenuPermissions(employeeId);
        }
    });
}

function populateReportingManagers(managers, selectedManager) {
    if (!$('#perm-reporting-manager').length) return;

    const $select = $('#perm-reporting-manager');
    $select.empty().append('<option value="">Select Reporting Manager</option>');

    if (managers && managers.length > 0) {
        managers.forEach(manager => {
            const isSelected = manager.id.toString() === (selectedManager || '').toString();
            $select.append(`
                <option value="${manager.id}" ${isSelected ? 'selected' : ''}>
                    ${manager.user_fullname} (${manager.username})
                </option>
            `);
        });
    }
}

function fetchMenuPermissions(employeeId) {
    if (!$('#permission-table-body').length) return;

    $.ajax({
        url: 'get-menu-permissions',
        type: 'GET',
        data: { employee_id: employeeId },
        success: function(response) {
            $('#perm-role').val(response.role_id || 3);
            if (response.menus && response.menus.length > 0) {
                renderMenuPermissions(response.menus, response.user_permissions);
            } else {
                $('#permission-table-body').html(
                    '<tr><td colspan="3" class="text-center">No menu permissions available</td></tr>'
                );
            }
        },
        error: function(xhr) {
            console.error('Error fetching menu permissions:', xhr.responseText);
        }
    });
}

function renderMenuPermissions(menus, userPermissions) {
    if (!$('#permission-table-body').length) return;

    let html = '';
    const menuMap = {};

    menus.forEach(menu => {
        if (menu.sub_menus === 0) {
            menuMap[menu.id] = {
                menu: menu,
                submenus: []
            };
        }
    });

    menus.forEach(menu => {
        if (menu.sub_menus !== 0 && menuMap[menu.sub_menus]) {
            menuMap[menu.sub_menus].submenus.push(menu);
        }
    });

    for (const [menuId, menuData] of Object.entries(menuMap)) {
        const mainMenu = menuData.menu;
        const submenus = menuData.submenus;

        html += `
            <tr>
                <td>${mainMenu.menu_name}</td>
                <td></td>
                <td class="text-center">
                    <input type="checkbox" class="menu-checkbox"
                           data-menu-id="${mainMenu.id}"
                           ${userPermissions.includes(parseInt(mainMenu.id)) ? 'checked' : ''}>
                </td>
            </tr>
        `;

        submenus.forEach(submenu => {
            html += `
                <tr>
                    <td></td>
                    <td>${submenu.menu_name}</td>
                    <td class="text-center">
                        <input type="checkbox" class="menu-checkbox"
                               data-menu-id="${submenu.id}"
                               ${userPermissions.includes(parseInt(submenu.id)) ? 'checked' : ''}>
                    </td>
                </tr>
            `;
        });
    }

    $('#permission-table-body').html(html);
}

$('#save-permissions').click(function() {
    if (!$('#perm-employee-id').length) return;

    const employeeId = $('#perm-employee-id').val();
    const employeeName = $('#perm-employee-name').val();
    const roleId = $('#perm-role').val();
    const email = $('#perm-email').val();
    const password = $('#perm-password').val();
    const reportingManager = $('#perm-reporting-manager').val();
    const zonalHead = $('#perm-zonal-head').val();
    const branchId = $('#perm-branch-id').val();
    const zoneId = $('#perm-zone-id').val();
    const multiLocId = $('#location_ids').val();
    const multiLocNames = $('#multi-location').val();
    const checkedMenus = [];

    $('.menu-checkbox:checked').each(function() {
        checkedMenus.push($(this).data('menu-id'));
    });

    $.ajax({
        url: 'save-permissions',
        type: 'POST',
        data: {
            employee_id: employeeId,
            employee_name: employeeName,
            role_id: roleId,
            email: email,
            password: password,
            reporting_manager: reportingManager,
            zonal_head: zonalHead,
            branch_id: branchId,
            zone_id: zoneId,
            menus: checkedMenus,
            multiLocId: multiLocId,
            multiLocNames: multiLocNames,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert('Permissions saved successfully');
            if ($('#permission-modal').length) {
                $('#permission-modal').modal('hide');
            }
            fetchEmployeeData();
        },
        error: function(xhr) {
            console.error('Error saving permissions:', xhr.responseText);
            alert('Error saving permissions. Please check console for details.');
        }
    });
});

function rowClick(event) {
    const selectedRows = document.querySelectorAll('.selected');
    selectedRows.forEach(row => row.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}

function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

// Initialize any third-party libraries only if their elements exist
function initializeThirdPartyLibs() {
    // SimpleBar initialization
    if (typeof SimpleBar !== 'undefined' && $('.simplebar-container').length) {
        new SimpleBar(document.querySelector('.simplebar-container'));
    }

    // DataTables initialization
    if (typeof exports !== 'undefined' && exports.DataTable && $('#data-table').length) {
        new exports.DataTable(document.getElementById('data-table'));
    }

    // ApexCharts initialization
    // if (typeof ApexCharts !== 'undefined') {
    //     initializeCharts();
    // }
}

$(document).ready(function() {
    initializeThirdPartyLibs();
    $('.access-values-search').val('');
    $('#name-views').val('');
    $('#employee_num').val('');
});

let selectedUserId = null;
let currentStatus = null;

// When opening modal, pass current status and user ID
$(document).on('click', '.status-badge', function () {
    selectedUserId = $(this).data('id');
    currentStatus = $(this).data('status');

    // Preselect the radio button based on current status
    if (currentStatus == 0) {
        $('#statusActive').prop('checked', true);
    } else {
        $('#statusInactive').prop('checked', true);
    }

    $('#statusDate').val(''); // reset date
    $('#statusModal').modal('show');
});

// Confirm status change
$('#confirmStatusChange').on('click', function () {
    const newStatus = $('input[name="active_status"]:checked').val();
    const statusDate = $('#statusDate').val();

    if (!statusDate) {
        toastr.warning('Please select an effective date.');
        return;
    }

    $.ajax({
        url: updateStatusUrl, // Laravel route
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            user_id: selectedUserId,
            active_status: newStatus,
            status_date: statusDate
        },
        success: function (response) {
            $('#statusModal').modal('hide');
            toastr.success('User status updated successfully.');

            // Update UI badge
            const badge = $(`.status-badge[data-id="${selectedUserId}"]`);
            if (newStatus == 0) {
                badge.removeClass('bg-danger').addClass('bg-success').text('Active').data('status', 0);
            } else {
                badge.removeClass('bg-success').addClass('bg-danger').text('Inactive').data('status', 1);
            }

            fetchEmployeeData(); // optional refresh
        },
        error: function () {
            toastr.error('Failed to update user status.');
        }
    });
});
$(document).ready(function () {

    let selectedLocations = new Set();
    let selectedNames = new Set();

    $('#multi-location').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $dropdown = $('#locationDropdown');
        const isShowing = $dropdown.hasClass('show');

        $('.dropdown-menu').removeClass('show');

        if (!isShowing) {
            $dropdown.addClass('show');
            $(this).attr('aria-expanded', 'true');

            setTimeout(() => $('#locationSearch').focus(), 10);
        } else {
            $dropdown.removeClass('show');
            $(this).attr('aria-expanded', 'false');
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('#locationDropdown').removeClass('show');
            $('#multi-location').attr('aria-expanded', 'false');
        }
    });

    $('#locationDropdown').on('click', function (e) {
        e.stopPropagation();
    });

   $(document).on('keyup', '#locationSearch', function () {
        const search = $(this).val().toLowerCase();

        $('.location-item').each(function () {
            const locationName = $(this)
                .find('.location-name')
                .text()
                .toLowerCase();

            if (locationName.includes(search)) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    window.toggleLocation = function (el) {
        const $el = $(el);
        const id = $el.data('id');
        const name = $el.data('name');
        const $tick = $el.find('.tick');

        if (selectedLocations.has(id)) {
            selectedLocations.delete(id);
            selectedNames.delete(name);
            $tick.hide();
            $el.removeClass('bg-light');
        } else {
            selectedLocations.add(id);
            selectedNames.add(name);
            $tick.show();
            $el.addClass('bg-light');
        }

        $('#multi-location').val([...selectedNames].join(', '));
        $('#location_ids').val([...selectedLocations].join(','));

        $('#locationDropdown').addClass('show');
        $('#multi-location').attr('aria-expanded', 'true');
    };

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('#locationDropdown').removeClass('show');
            $('#multi-location').attr('aria-expanded', 'false');
        }
    });
 // 🔹 Select All → check/uncheck all
    $('#selectAllMenus').on('change', function () {
        const isChecked = $(this).prop('checked');
        $('.menu-checkbox').prop('checked', isChecked);
    });

    // 🔹 Individual checkbox change
    $(document).on('change', '.menu-checkbox', function () {
        const total = $('.menu-checkbox').length;
        const checked = $('.menu-checkbox:checked').length;

        // If all checked → select all checked
        $('#selectAllMenus').prop('checked', total === checked);
    });
});
