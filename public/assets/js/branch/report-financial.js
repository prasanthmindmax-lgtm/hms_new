// ================================================
// FINANCIAL REPORTS - COMPLETE JAVASCRIPT
// ================================================

$(document).ready(function () {
    
    // ================================================
    // INITIALIZE SELECT2
    // ================================================
    $('#zoneSelect, #branchSelect').select2({
        placeholder: 'Select',
        allowClear: true,
        width: '100%'
    });
    
    // ================================================
    // FILTER CHANGE HANDLER
    // ================================================
    $('.filter-input').on('change', function () {
        updateAppliedFilters();
        loadFilteredData();
    });
    
    // ================================================
    // CLEAR ALL FILTERS
    // ================================================
    $('#clearFilters').on('click', function () {
        $('.filter-input').val(null).trigger('change');
        $('#appliedFilters').html('');
        loadFilteredData();
    });
    
    // ================================================
    // VIEW FILES BUTTON
    // ================================================
    $(document).on('click', '.view-files-btn', function() {
        const reportId = $(this).data('report-id');
        viewFiles(reportId);
    });
    
    // ================================================
    // VIEW DETAIL BUTTON
    // ================================================
    $(document).on('click', '.view-detail-btn', function() {
        const reportId = $(this).data('report-id');
        viewReportDetail(reportId);
    });
    
    // ================================================
    // PAGINATION CLICK HANDLER
    // ================================================
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadFilteredData(url);
        }
    });
    
});

// ================================================
// LOAD FILTERED DATA VIA AJAX
// ================================================
function loadFilteredData(url = null) {
    // Show loading
    $('#tableContainer').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading reports...</p>
        </div>
    `);
    
    // Build URL
    if (!url) {
        url = financialReportsIndexRoute; // From blade: const financialReportsIndexRoute = "{{ route('financial-reports.index') }}";
    }
    
    // Get filter data
    const formData = {
        start_date: $('input[name="start_date"]').val(),
        end_date: $('input[name="end_date"]').val(),
        zone_id: $('#zoneSelect').val(),
        branch_id: $('#branchSelect').val()
    };
    
    // AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: formData,
        success: function(response) {
            $('#tableContainer').html(response);
            updateStatistics(formData);
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            $('#tableContainer').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading reports. Please try again.
                </div>
            `);
        }
    });
}

// ================================================
// UPDATE STATISTICS VIA AJAX
// ================================================
function updateStatistics(filters) {
    $.ajax({
        url: financialReportsStatsRoute, // From blade
        type: 'GET',
        data: filters,
        success: function(data) {
            // Update stat cards
            updateStatCard('.stat-card-1 h3', data.report_count, '');
            updateStatCard('.stat-card-2 h3', data.total_radiant, '₹');
            updateStatCard('.stat-card-3 h3', data.total_card, '₹');
            updateStatCard('.stat-card-4 h3', data.total_bank, '₹');
            
            // Update additional stats if they exist
            $($('.stat-card')[4]).find('h3').text('₹' + formatNumber(data.total_discount));
            $($('.stat-card')[5]).find('h3').text('₹' + formatNumber(data.total_cancel));
            $($('.stat-card')[6]).find('h3').text('₹' + formatNumber(data.total_refund));
        },
        error: function(xhr) {
            console.error('Error updating statistics:', xhr);
        }
    });
}

// ================================================
// UPDATE STAT CARD HELPER
// ================================================
function updateStatCard(selector, value, prefix) {
    const formattedValue = prefix + formatNumber(value);
    $(selector).text(formattedValue);
}

// ================================================
// FORMAT NUMBER HELPER
// ================================================
function formatNumber(num) {
    if (isNaN(num)) return '0';
    return parseFloat(num).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// ================================================
// UPDATE APPLIED FILTERS TAGS
// ================================================
function updateAppliedFilters() {
    let html = '';
    
    // Start Date
    const startDate = $('input[name="start_date"]').val();
    if (startDate) {
        html += createTag('start_date', startDate, 'From: ' + formatDate(startDate));
    }
    
    // End Date
    const endDate = $('input[name="end_date"]').val();
    if (endDate) {
        html += createTag('end_date', endDate, 'To: ' + formatDate(endDate));
    }
    
    // Zones
    $('#zoneSelect option:selected').each(function () {
        html += createTag('zone_id[]', $(this).val(), 'Zone: ' + $(this).text());
    });
    
    // Branches
    $('#branchSelect option:selected').each(function () {
        html += createTag('branch_id[]', $(this).val(), 'Branch: ' + $(this).text());
    });
    
    // Clear All button
    if (html !== '') {
        html += `
            <span class="filter-tag clear-all-tag">
                Clear All
                <button onclick="clearAll()">×</button>
            </span>
        `;
    }
    
    $('#appliedFilters').html(html);
}

// ================================================
// CREATE FILTER TAG
// ================================================
function createTag(name, value, label) {
    return `
        <span class="filter-tag">
            ${label}
            <button onclick="removeFilter('${name}','${value}')">×</button>
        </span>
    `;
}

// ================================================
// REMOVE SINGLE FILTER
// ================================================
function removeFilter(name, value) {
    if (name === 'start_date' || name === 'end_date') {
        $(`input[name="${name}"]`).val('').trigger('change');
    } else if (name === 'zone_id[]') {
        let values = $('#zoneSelect').val() || [];
        $('#zoneSelect').val(values.filter(v => v != value)).trigger('change');
    } else if (name === 'branch_id[]') {
        let values = $('#branchSelect').val() || [];
        $('#branchSelect').val(values.filter(v => v != value)).trigger('change');
    }
}

// ================================================
// CLEAR ALL FILTERS
// ================================================
function clearAll() {
    $('.filter-input').val(null).trigger('change');
    $('#appliedFilters').html('');
}

// ================================================
// FORMAT DATE HELPER
// ================================================
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-IN', options);
}

// ================================================
// EXPORT TO EXCEL
// ================================================
function exportExcel() {
    const params = new URLSearchParams();
    
    const startDate = $('input[name="start_date"]').val();
    if (startDate) params.append('start_date', startDate);
    
    const endDate = $('input[name="end_date"]').val();
    if (endDate) params.append('end_date', endDate);
    
    const zoneIds = $('#zoneSelect').val();
    if (zoneIds && zoneIds.length > 0) {
        zoneIds.forEach(id => params.append('zone_id[]', id));
    }
    
    const branchIds = $('#branchSelect').val();
    if (branchIds && branchIds.length > 0) {
        branchIds.forEach(id => params.append('branch_id[]', id));
    }
    
    // Construct URL
    const url = financialReportsExportExcelRoute + '?' + params.toString();
    window.location.href = url;
}

// ================================================
// EXPORT TO CSV
// ================================================
function exportCsv() {
    const params = new URLSearchParams();
    
    const startDate = $('input[name="start_date"]').val();
    if (startDate) params.append('start_date', startDate);
    
    const endDate = $('input[name="end_date"]').val();
    if (endDate) params.append('end_date', endDate);
    
    const zoneIds = $('#zoneSelect').val();
    if (zoneIds && zoneIds.length > 0) {
        zoneIds.forEach(id => params.append('zone_id[]', id));
    }
    
    const branchIds = $('#branchSelect').val();
    if (branchIds && branchIds.length > 0) {
        branchIds.forEach(id => params.append('branch_id[]', id));
    }
    
    // Construct URL
    const url = financialReportsExportCsvRoute + '?' + params.toString();
    window.location.href = url;
}

// ================================================
// VIEW FILES MODAL
// ================================================
function viewFiles(reportId) {
    const modal = new bootstrap.Modal(document.getElementById('filesModal'));
    modal.show();
    
    // Show loading
    $('#filesContent').html(`
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    // Construct URL
    const url = financialReportsShowRoute.replace(':id', reportId);
    
    // Fetch files
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let content = '<div class="row">';
            
            // Radiant files
            const radiantFiles = data.radiant_collection_files ? JSON.parse(data.radiant_collection_files) : [];
            if (radiantFiles.length > 0) {
                content += '<div class="col-12 mb-3">';
                content += '<h6 class="text-success"><i class="fas fa-money-bill me-2"></i>Radiant Cash Files</h6>';
                content += generateFileLinks(radiantFiles);
                content += '</div>';
            }
            
            // Card files
            const cardFiles = data.actual_card_files ? JSON.parse(data.actual_card_files) : [];
            if (cardFiles.length > 0) {
                content += '<div class="col-12 mb-3">';
                content += '<h6 class="text-primary"><i class="fas fa-credit-card me-2"></i>Card Files</h6>';
                content += generateFileLinks(cardFiles);
                content += '</div>';
            }
            
            // Bank files
            const bankFiles = data.bank_deposit_files ? JSON.parse(data.bank_deposit_files) : [];
            if (bankFiles.length > 0) {
                content += '<div class="col-12 mb-3">';
                content += '<h6 class="text-warning"><i class="fas fa-university me-2"></i>Bank Deposit Files</h6>';
                content += generateFileLinks(bankFiles);
                content += '</div>';
            }
            
            if (radiantFiles.length === 0 && cardFiles.length === 0 && bankFiles.length === 0) {
                content += '<div class="col-12 text-center py-3">';
                content += '<p class="text-muted">No files attached</p>';
                content += '</div>';
            }
            
            content += '</div>';
            $('#filesContent').html(content);
        },
        error: function(xhr) {
            console.error('Error loading files:', xhr);
            $('#filesContent').html('<p class="text-danger text-center">Error loading files</p>');
        }
    });
}

// ================================================
// GENERATE FILE LINKS
// ================================================
function generateFileLinks(files) {
    let html = '<div class="list-group">';
    
    files.forEach((file, index) => {
        const fileName = file.split('/').pop();
        const ext = fileName.split('.').pop().toLowerCase();
        
        let icon = 'fa-file';
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            icon = 'fa-file-image';
        } else if (ext === 'pdf') {
            icon = 'fa-file-pdf';
        } else if (['doc', 'docx'].includes(ext)) {
            icon = 'fa-file-word';
        } else if (['xls', 'xlsx'].includes(ext)) {
            icon = 'fa-file-excel';
        }
        
        html += `
            <a href="/${file}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fas ${icon} me-2"></i>${fileName}</span>
                <i class="fas fa-external-link-alt text-muted"></i>
            </a>
        `;
    });
    
    html += '</div>';
    return html;
}

// ================================================
// VIEW REPORT DETAIL
// ================================================
function viewReportDetail(reportId) {
    // Show loading alert
    Swal.fire({
        title: 'Loading...',
        html: 'Please wait while we load the report details',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Construct URL
    const url = financialReportsShowRoute.replace(':id', reportId);
    
    // Fetch report details
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayReportDetail(data);
        },
        error: function(xhr) {
            console.error('Error loading report:', xhr);
            Swal.fire('Error', 'Failed to load report details', 'error');
        }
    });
}

// ================================================
// DISPLAY REPORT DETAIL
// ================================================
function displayReportDetail(report) {
    const radiantFiles = report.radiant_collection_files ? JSON.parse(report.radiant_collection_files) : [];
    const cardFiles = report.actual_card_files ? JSON.parse(report.actual_card_files) : [];
    const bankFiles = report.bank_deposit_files ? JSON.parse(report.bank_deposit_files) : [];
    
    const html = `
        <div class="row text-start">
            <div class="col-md-6">
                <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                <p><strong>Report Date:</strong> ${formatDate(report.report_date)}</p>
                <p><strong>Zone:</strong> ${report.zone_name}</p>
                <p><strong>Branch:</strong> ${report.branch_name}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-success border-bottom pb-2">Collection Amounts</h6>
                <p><strong>Radiant:</strong> ₹${formatNumber(report.radiant_collection_amount)}</p>
                <p><strong>Card:</strong> ₹${formatNumber(report.actual_card_amount)}</p>
                <p><strong>Bank:</strong> ₹${formatNumber(report.bank_deposit_amount)}</p>
            </div>
            <div class="col-md-6 mt-3">
                <h6 class="text-danger border-bottom pb-2">Deductions</h6>
                <p><strong>Discount:</strong> ₹${formatNumber(report.today_discount_amount)}</p>
                <p><strong>Cancel:</strong> ₹${formatNumber(report.cancel_bill_amount)}</p>
                <p><strong>Refund:</strong> ₹${formatNumber(report.refund_bill_amount)}</p>
            </div>
            <div class="col-md-6 mt-3">
                <h6 class="text-info border-bottom pb-2">Personnel</h6>
                <p><strong>Placed By:</strong> ${report.placed_by_whom || '-'}</p>
                <p><strong>Locker By:</strong> ${report.locker_by_whom || '-'}</p>
                <p><strong>Gave Cash:</strong> ${report.who_gave_radiant_cash || '-'}</p>
            </div>
            <div class="col-12 mt-3">
                <h6 class="text-secondary border-bottom pb-2">Files</h6>
                <p><strong>Radiant Files:</strong> ${radiantFiles.length} file(s)</p>
                <p><strong>Card Files:</strong> ${cardFiles.length} file(s)</p>
                <p><strong>Bank Files:</strong> ${bankFiles.length} file(s)</p>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'Report Details',
        html: html,
        width: '800px',
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            container: 'swal-wide'
        }
    });
}