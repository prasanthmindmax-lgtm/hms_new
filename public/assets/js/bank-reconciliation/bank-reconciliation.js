// ============================================
// BANK RECONCILIATION - UPDATED JAVASCRIPT
// WITH FILTER MODAL, ROW CLICK, MATCH POPUP
// ============================================

$(document).ready(function() {
    
    // Global variables
    let currentStatementId = null;
    let currentTxnAmount = 0;
    let currentTxnData = {};
    let currentPage = 1;
    let currentFilters = {};
    let perPage = 25;
    let currentBestMatches = [];
    let currentPossibleMatches = [];

    function bankAccountsOn() {
        return typeof window.bankAccountsEnabled !== 'undefined' && window.bankAccountsEnabled;
    }

    var lastModalAccountsList = [];

    function accModalEsc(s) {
        if (s == null || s === '') return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;');
    }

    function loadBankAccounts(selectedId) {
        if (!bankAccountsOn() || !routes.accounts) return;
        $.get(routes.accounts, function (res) {
            var list = (res && res.data) ? res.data : [];
            $('.bank-account-select').each(function () {
                var id = $(this).attr('id');
                var keep = (id === 'filterBankAccount')
                    ? '<option value="">All accounts</option>'
                    : '<option value="">Select account…</option>';
                var inner = keep;
                list.forEach(function (a) {
                    var label = a.account_number + (a.bank_name ? ' — ' + a.bank_name : '');
                    inner += '<option value="' + a.id + '">' + $('<div>').text(label).html() + '</option>';
                });
                $(this).html(inner);
            });
            if (selectedId) {
                $('.bank-account-select').val(String(selectedId));
            }
        });
    }

    function resetAccountFormToCreate() {
        $('#editBankAccountId').val('');
        $('#formNewBankAccount')[0].reset();
        $('#newAccountTabLabel').text('New account');
        $('#btnSaveNewAccountLabel').text('Save account');
        $('#newAccountFormHint').text('Add a new bank account for statement uploads.');
        $('#btnCancelEditAccount').hide();
    }

    function fillAccountFormForEdit(acc) {
        if (!acc) return;
        $('#editBankAccountId').val(String(acc.id));
        $('#newAccNumber').val(acc.account_number || '');
        $('#newAccBank').val(acc.bank_name || '');
        $('#newAccBranch').val(acc.branch_name || '');
        $('#newAccIfsc').val(acc.ifsc_code || '');
        $('#newAccHolder').val(acc.account_holder_name || '');
        $('#newAccNotes').val(acc.notes || '');
        $('#newAccountTabLabel').text('Edit account');
        $('#btnSaveNewAccountLabel').text('Update account');
        $('#newAccountFormHint').text('Update details and save. Account number must stay unique.');
        $('#btnCancelEditAccount').show();
    }

    function showAccountModalTabAll() {
        var el = document.getElementById('tabBtnAllAccounts');
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(el).show();
        } else if (el) {
            $(el).tab('show');
        }
    }

    function showAccountModalTabNew() {
        var el = document.getElementById('tabBtnNewAccount');
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(el).show();
        } else if (el) {
            $(el).tab('show');
        }
    }

    function renderModalAccountList(list) {
        lastModalAccountsList = list || [];
        var tbody = $('#bankAccountsModalTableBody');
        if (!tbody.length) return;
        tbody.empty();
        if (!lastModalAccountsList.length) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">No accounts yet. Use <strong>New account</strong> to add one.</td></tr>');
            return;
        }
        lastModalAccountsList.forEach(function (a) {
            var row = '<tr>' +
                '<td class="fw-semibold">' + accModalEsc(a.account_number) + '</td>' +
                '<td><small>' + accModalEsc(a.bank_name || '—') + '</small></td>' +
                '<td><small>' + accModalEsc(a.branch_name || '—') + '</small></td>' +
                '<td><code class="small">' + accModalEsc(a.ifsc_code || '—') + '</code></td>' +
                '<td><small>' + accModalEsc(a.account_holder_name || '—') + '</small></td>' +
                '<td class="text-end">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-modal-account" data-account-id="' + accModalEsc(a.id) + '">' +
                '<i class="bi bi-pencil"></i></button>' +
                '</td></tr>';
            tbody.append(row);
        });
    }

    function refreshModalAccountList() {
        if (!bankAccountsOn() || !routes.accounts) return;
        var tbody = $('#bankAccountsModalTableBody');
        if (!tbody.length) return;
        tbody.html('<tr><td colspan="6" class="text-center text-muted py-3">Loading…</td></tr>');
        $.get(routes.accounts, function (res) {
            var list = (res && res.data) ? res.data : [];
            renderModalAccountList(list);
        }).fail(function () {
            tbody.html('<tr><td colspan="6" class="text-center text-danger py-3">Could not load accounts.</td></tr>');
        });
    }

    if (bankAccountsOn()) {
        loadBankAccounts();
    }

    $('#accountDetailsModalBtn').on('click', function () {
        $('#accountDetailsModal').modal('show');
    });

    $('#accountDetailsModal').on('shown.bs.modal', function () {
        if (!bankAccountsOn()) return;
        resetAccountFormToCreate();
        showAccountModalTabAll();
        refreshModalAccountList();
    });

    $('#btnRefreshModalAccountList').on('click', function () {
        refreshModalAccountList();
    });

    $(document).on('click', '.btn-edit-modal-account', function () {
        var aid = $(this).data('account-id');
        var acc = lastModalAccountsList.find(function (x) { return String(x.id) === String(aid); });
        if (!acc) return;
        fillAccountFormForEdit(acc);
        showAccountModalTabNew();
    });

    $('#btnCancelEditAccount').on('click', function () {
        resetAccountFormToCreate();
        showAccountModalTabAll();
    });

    $(document).on('shown.bs.tab', '#tabBtnNewAccount', function () {
        if (!$('#editBankAccountId').val()) {
            $('#formNewBankAccount')[0].reset();
            $('#newAccountTabLabel').text('New account');
            $('#btnSaveNewAccountLabel').text('Save account');
            $('#newAccountFormHint').text('Add a new bank account for statement uploads.');
            $('#btnCancelEditAccount').hide();
        }
    });

    $('#formNewBankAccount').on('submit', function (e) {
        e.preventDefault();
        if (!routes.accountsStore) return;
        var $btn = $('#btnSaveNewAccount');
        var editId = ($('#editBankAccountId').val() || '').trim();
        var isEdit = !!editId;
        var url = isEdit ? (routes.accountsUpdateBase + '/' + encodeURIComponent(editId)) : routes.accountsStore;
        var payload = $(this).serialize();
        if (isEdit) {
            payload += (payload.length ? '&' : '') + '_method=PUT';
        }
        $btn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message || (isEdit ? 'Account updated' : 'Account saved'));
                    loadBankAccounts(res.account ? res.account.id : null);
                    refreshModalAccountList();
                    if (isEdit) {
                        resetAccountFormToCreate();
                        showAccountModalTabAll();
                    } else {
                        $('#formNewBankAccount')[0].reset();
                        $('#accountDetailsModal').modal('hide');
                    }
                } else {
                    toastr.error(res.message || 'Could not save');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Save failed';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                }
                toastr.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    $('#uploadFormModal').on('submit', function (e) {
        e.preventDefault();
        var fileEl = document.getElementById('modalExcelFile');
        if (!fileEl || !fileEl.files || !fileEl.files[0]) {
            toastr.error('Please select an Excel file');
            return;
        }
        if (bankAccountsOn() && !$('#modalUploadBankAccount').val()) {
            toastr.error('Select a bank account for this upload');
            return;
        }
        var formData = new FormData(this);
        $('#processingOverlay').addClass('active');
        $('#processingStatus').text('Uploading file...');
        $('#modalUploadSubmit').prop('disabled', true);
        $.ajax({
            url: routes.upload,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    $('#processingStatus').text('Import completed!');
                    setTimeout(function () {
                        $('#processingOverlay').removeClass('active');
                        toastr.success(response.message + ' - All marked as Uncategorized');
                        $('#uploadFormModal')[0].reset();
                        $('#accountDetailsModal').modal('hide');
                        showStatementsSection();
                        loadStatements();
                        updateStatistics();
                    }, 600);
                } else {
                    $('#processingOverlay').removeClass('active');
                    toastr.error(response.message || 'Upload failed');
                }
            },
            error: function (xhr) {
                $('#processingOverlay').removeClass('active');
                var message = 'Error uploading file';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            },
            complete: function () {
                $('#modalUploadSubmit').prop('disabled', false);
            }
        });
    });
    
    // Check if statements exist on load
    checkStatementsExist();
    
    // ============================================
    // CHECK IF STATEMENTS EXIST
    // ============================================
    
    // ============================================
    // CHECK IF STATEMENTS EXIST
    // ============================================
    function checkStatementsExist() {
        $.ajax({
            url: routes.statements,
            type: 'GET',
            data: { stats_only: true },
            success: function(response) {
                if (response.total > 0) {
                    showStatementsSection();
                    loadStatements();
                    updateStatistics();
                } else {
                    showImportSection();
                }
            },
            error: function() {
                showImportSection();
            }
        });
    }
    
    // ============================================
    // TOGGLE SECTIONS
    // ============================================
    function showImportSection() {
        $('#batchUploadSection').hide();
        $('#importSection').fadeIn();
        $('#statementsSection').hide();
    }
    
    function showStatementsSection() {
        $('#batchUploadSection').hide();
        $('#importSection').hide();
        $('#statementsSection').fadeIn();
    }

    function showBatchUploadSection() {
        $('#importSection').hide();
        $('#statementsSection').hide();
        $('#batchUploadSection').stop(true, true).fadeIn(150);
        if (bankAccountsOn() && routes.uploadBatches && $('#batchTableBody').length) {
            loadBatchesInline(1);
        }
    }
    
    $('#viewStatementsBtn').on('click', function() {
        showStatementsSection();
        loadStatements();
        updateStatistics();
    });
    
    $('#uploadBtn').on('click', function() {
        showImportSection();
    });

    $('#btnOpenBatchUploadView').on('click', function () {
        showBatchUploadSection();
    });

    $('#btnCloseBatchUploadView').on('click', function () {
        showStatementsSection();
        loadStatements(currentPage || 1);
        updateStatistics();
    });
    
    $('#perPageSelect').on('change', function() {
        perPage = parseInt($(this).val(), 10);
        loadStatements(1);
    });
    
    // ============================================
    // OPEN FILTER MODAL
    // ============================================
    $('#openFilterBtn').on('click', function() {
        $('#filterModal').modal('show');
    });

    function clearAllFilters() {
        currentFilters = {};
        $('#filterMatchStatus').val('');
        $('#filterIncomeMatch').val('');
        $('#filterRadiantMatch').val('');
        $('#filterAmountMin').val('');
        $('#filterAmountMax').val('');
        $('#filterReference').val('');
        $('#filterDescription').val('');
        if ($('#filterBankAccount').length) {
            $('#filterBankAccount').val('');
        }
        var dateEl = document.getElementById('filterDateRange');
        if (dateEl) {
            if (dateEl._flatpickr) {
                dateEl._flatpickr.clear();
            }
            $(dateEl).val('');
        }
        window.bankReconDateFrom = null;
        window.bankReconDateTo = null;
        var matchedEl = document.getElementById('filterMatchedDateRange');
        if (matchedEl) {
            if (matchedEl._flatpickr) {
                matchedEl._flatpickr.clear();
            }
            $(matchedEl).val('');
        }
        window.bankReconMatchedDateFrom = null;
        window.bankReconMatchedDateTo = null;
        $('#filterModal').modal('hide');
        loadStatements(1);
    }

    $('#clearAllFiltersBtn').on('click', function() {
        clearAllFilters();
    });

    function buildExportQueryString(format) {
        var p = $.extend({ format: format }, currentFilters);
        Object.keys(p).forEach(function (k) {
            if (p[k] === '' || p[k] === undefined || p[k] === null) {
                delete p[k];
            }
        });
        return $.param(p);
    }

    $(document).on('click', '#btnExportStatementsCsv', function (e) {
        e.preventDefault();
        if (!routes.statementsExport) return;
        window.location.href = routes.statementsExport + '?' + buildExportQueryString('csv');
    });

    $(document).on('click', '#btnExportStatementsXlsx', function (e) {
        e.preventDefault();
        if (!routes.statementsExport) return;
        window.location.href = routes.statementsExport + '?' + buildExportQueryString('xlsx');
    });

    // ============================================
    // APPLY FILTERS FROM MODAL
    // ============================================
    $('#applyFiltersBtn').on('click', function() {
        currentFilters = {
            match_status:    $('#filterMatchStatus').val(),
            income_match:    $('#filterIncomeMatch').val(),
            radiant_match:   $('#filterRadiantMatch').val(),
            amount_min:      $('#filterAmountMin').val(),
            amount_max:      $('#filterAmountMax').val(),
            reference_number:$('#filterReference').val(),
            search:          $('#filterDescription').val()
        };
        if ($('#filterBankAccount').length && $('#filterBankAccount').val()) {
            currentFilters.bank_account_id = $('#filterBankAccount').val();
        }
        if (window.bankReconDateFrom) currentFilters.date_from = window.bankReconDateFrom;
        if (window.bankReconDateTo) currentFilters.date_to = window.bankReconDateTo;
        if (window.bankReconMatchedDateFrom) currentFilters.matched_date_from = window.bankReconMatchedDateFrom;
        if (window.bankReconMatchedDateTo) currentFilters.matched_date_to = window.bankReconMatchedDateTo;
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] === '' || currentFilters[key] === undefined) delete currentFilters[key];
        });
        $('#filterModal').modal('hide');
        loadStatements(1);
    });

    // ============================================
    // FILE UPLOAD
    // ============================================
    const fileInput = $('#excelFileInput');
    const browseBtn = $('#browseBtn');
    const uploadForm = $('#uploadFormMain');
    
    browseBtn.on('click', function() {
        fileInput.click();
    });
    
    fileInput.on('change', function() {
        handleFileSelect();
    });
    
    function handleFileSelect() {
        const file = fileInput[0].files[0];
        
        if (!file) return;
        
        const validExtensions = ['xlsx', 'xls'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!validExtensions.includes(fileExtension)) {
            toastr.error('Please select a valid Excel file (.xlsx or .xls)');
            fileInput.val('');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            toastr.error('File size exceeds 10MB limit');
            fileInput.val('');
            return;
        }
        
        $('#fileName').text(file.name);
        $('#fileNameDisplay').fadeIn();
        $('#uploadSubmitBtn').prop('disabled', false);
    }
    
    $('#removeFileBtn').on('click', function(e) {
        e.stopPropagation();
        fileInput.val('');
        $('#fileNameDisplay').hide();
        $('#uploadSubmitBtn').prop('disabled', true);
    });
    
    // ============================================
    // UPLOAD FORM SUBMIT
    // ============================================
    uploadForm.on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const file = fileInput[0].files[0];
        
        if (!file) {
            toastr.error('Please select an Excel file');
            return;
        }

        if (bankAccountsOn()) {
            var accMain = $('#mainUploadBankAccount').val();
            if (!accMain) {
                toastr.error('Select the bank account this statement belongs to');
                return;
            }
        }
        
        $('#processingOverlay').addClass('active');
        $('#processingStatus').text('Uploading file...');
        $('#uploadSubmitBtn').prop('disabled', true);
        
        setTimeout(() => $('#processingStatus').text('Reading Excel data...'), 1000);
        setTimeout(() => $('#processingStatus').text('Parsing transactions...'), 2000);
        setTimeout(() => $('#processingStatus').text('Saving to database...'), 3000);
        
        $.ajax({
            url: routes.upload,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#processingStatus').text('Import completed!');
                    
                    setTimeout(function() {
                        $('#processingOverlay').removeClass('active');
                        toastr.success(response.message + ' - All marked as Uncategorized');
                        
                        uploadForm[0].reset();
                        $('#fileNameDisplay').hide();
                        $('#uploadSubmitBtn').prop('disabled', true);
                        if (bankAccountsOn()) {
                            loadBankAccounts();
                        }
                        
                        showStatementsSection();
                        loadStatements();
                        updateStatistics();
                    }, 1000);
                } else {
                    $('#processingOverlay').removeClass('active');
                    toastr.error(response.message || 'Upload failed');
                    $('#uploadSubmitBtn').prop('disabled', false);
                }
            },
            error: function(xhr) {
                $('#processingOverlay').removeClass('active');
                
                let message = 'Error uploading file';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                toastr.error(message);
                $('#uploadSubmitBtn').prop('disabled', false);
            }
        });
    });
    
    // ============================================
    // LOAD BANK STATEMENTS
    // ============================================
    function loadStatements(page = 1) {
        currentPage = page;
        
        const params = {
            page: page,
            per_page: perPage,
            ...currentFilters
        };
        
        $.ajax({
            url: routes.statements,
            type: 'GET',
            data: params,
            success: function(response) {
                renderStatementsTable(response.data);
                renderPagination(response);
                updateStatistics();
            },
            error: function(xhr) {
                console.error('Error loading statements:', xhr);
                toastr.error('Failed to load statements');
            }
        });
    }
    
    function escapeAttr(str) {
        if (str == null || str === '') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/\r?\n/g, ' ');
    }

    // ============================================
    // RENDER STATEMENTS TABLE
    // ============================================
    function renderStatementsTable(statements) {
        const tbody = $('#statementsTableBody');
        tbody.empty();
        
        if (!statements || statements.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="16" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-3">No statements found</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        statements.forEach(function(stmt) {
            const matchStatusBadge = getMatchStatusBadge(stmt.match_status);
            const categoryBadge = getCategoryBadge(stmt.match_status);
            const amount = stmt.withdrawal > 0 ? stmt.withdrawal : stmt.deposit;
            const amountType = stmt.withdrawal > 0 ? 'withdrawal' : 'deposit';
            
            const resolvedBillNo = (stmt.resolved_bill_number || stmt.resolved_bill_gen_number || stmt.bill_number || '').toString().trim();
            const resolvedVendor = (stmt.resolved_vendor_name || stmt.vendor_name || '').toString().trim();
            let matchedBillInfo = '-';
            if (stmt.match_status !== 'unmatched' && resolvedBillNo) {
                matchedBillInfo = `
                    <div class="matched-bill-info">
                        <strong>${escapeAttr(resolvedBillNo)}</strong><br>
                        <small class="text-muted">${escapeAttr(resolvedVendor)}</small><br>
                        <small class="text-success">₹${formatNumber(stmt.bill_amount || 0)}</small>
                    </div>
                `;
            }
            let matchedbyInfo = '-';
            const matchDispName = stmt.bbm_matched_by_name || stmt.matched_by_name;
            const matchDispUser = stmt.bbm_matched_by_username || stmt.matched_by_username;
            if (matchDispName != null && matchDispName !== '') {
                matchedbyInfo = `
                    <div class="matched-bill-info">
                        <strong>${escapeAttr(matchDispName)}</strong><br>
                        <small class="text-muted">${escapeAttr(matchDispUser || '')}</small><br>
                    </div>
                `;
            }

            // Income reconciliation tag details
            const incomeTagged = stmt.income_match_status === 'income_matched';
            let incomeTagCell = '<span class="text-muted small"><i class="bi bi-dash"></i> Not tagged</span>';
            if (incomeTagged) {
                const taggedAt = stmt.income_matched_at
                    ? formatDate(stmt.income_matched_at.substring(0, 10))
                    : '';
                incomeTagCell = `
                    <div>
                        <span class="badge bg-info text-white mb-1">
                            <i class="bi bi-arrow-left-right me-1"></i>Income Tagged
                        </span><br>
                        <small class="text-dark fw-semibold">${stmt.income_matched_branch || ''}</small><br>
                        <small class="text-muted">${stmt.income_matched_date || ''}</small><br>
                        <small class="text-muted">By: <strong>${stmt.income_matched_by_name || ''}</strong></small>
                        ${taggedAt ? '<br><small class="text-muted">' + taggedAt + '</small>' : ''}
                    </div>`;
            }

            const radiantLinked = stmt.radiant_match_status === 'radiant_matched';
            let radiantTagCell = '<span class="text-muted small"><i class="bi bi-dash"></i> Not linked</span>';
            if (radiantLinked) {
                const rTaggedAt = stmt.radiant_matched_at
                    ? formatDate(stmt.radiant_matched_at.substring(0, 10))
                    : '';
                radiantTagCell = `
                    <div>
                        <span class="badge bg-warning text-dark mb-1">
                            <i class="bi bi-brightness-high me-1"></i>Radiant linked
                        </span><br>
                        <small class="text-dark fw-semibold">${stmt.radiant_matched_location || ''}</small><br>
                        <small class="text-muted">${stmt.radiant_matched_pickup_date || ''}</small><br>
                        ${stmt.radiant_cash_pickup_id ? '<small class="text-muted">Pickup #' + stmt.radiant_cash_pickup_id + '</small><br>' : ''}
                        <small class="text-muted">By: <strong>${stmt.radiant_matched_by_name || ''}</strong></small>
                        ${rTaggedAt ? '<br><small class="text-muted">' + rTaggedAt + '</small>' : ''}
                        ${stmt.radiant_match_against ? '<br><small class="text-muted">Keyword: ' + escapeAttr(stmt.radiant_match_against) + '</small>' : ''}
                    </div>`;
            } else if (stmt.radiant_match_against) {
                radiantTagCell = `
                    <div>
                        <span class="badge bg-secondary mb-1">Keyword only</span><br>
                        <small class="text-muted">${escapeAttr(stmt.radiant_match_against)}</small>
                    </div>`;
            }
            
            const row = `
                <tr class="statement-row-clickable ${stmt.match_status}" 
                    data-id="${stmt.id}" 
                    data-amount="${amount}" 
                    data-type="${amountType}"
                    data-date="${stmt.transaction_date}"
                    data-reference="${stmt.reference_number || ''}"
                    data-description="${stmt.description || ''}"
                    data-radiant-match="${escapeAttr(stmt.radiant_match_against)}"
                    data-radiant-status="${escapeAttr(stmt.radiant_match_status)}"
                    data-radiant-pickup-id="${stmt.radiant_cash_pickup_id || ''}">
                    <td>
                        <div class="date-cell">
                            ${formatDate(stmt.transaction_date)}
                            ${stmt.value_date !== stmt.transaction_date ? '<br><small class="text-muted">Value: ' + formatDate(stmt.value_date) + '</small>' : ''}
                        </div>
                    </td>
                    <td>
                        <small class="fw-semibold">${stmt.bank_account_number ? escapeAttr(stmt.bank_account_number) : '-'}</small>
                        ${stmt.bank_account_bank_name ? '<br><span class="text-muted small">' + escapeAttr(stmt.bank_account_bank_name) + '</span>' : ''}
                    </td>
                    <td>
                        <div class="description-cell">
                            ${stmt.description}
                        </div>
                    </td>
                    <td>
                        <code class="reference-code">${stmt.reference_number || '-'}</code>
                    </td>
                    <td>
                        <code class="">${stmt.transaction_id || '-'}</code>
                    </td>
                    <td class="text-end ${stmt.withdrawal > 0 ? 'text-danger' : ''}">
                        ${stmt.withdrawal > 0 ? '₹' + formatNumber(stmt.withdrawal) : '-'}
                    </td>
                    <td class="text-end ${stmt.deposit > 0 ? 'text-success' : ''}">
                        ${stmt.deposit > 0 ? '₹' + formatNumber(stmt.deposit) : '-'}
                    </td>
                    <td class="text-end">
                        <strong>₹${formatNumber(stmt.balance)}</strong>
                    </td>
                    <td>
                        ${categoryBadge}
                    </td>
                    <td>
                        ${matchStatusBadge}
                    </td>
                    <td>
                        ${matchedBillInfo}
                    </td>
                    <td>
                        ${matchedbyInfo}
                    </td>
                    <td>
                        <small class="text-muted">${(stmt.matched_date || stmt.bank_match_matched_at) ? formatDateTime(stmt.matched_date || stmt.bank_match_matched_at) : '—'}</small>
                    </td>
                    <td>
                        ${incomeTagCell}
                    </td>
                    <td>
                        ${radiantTagCell}
                    </td>
                    <td>
                        <div class="action-buttons">
                            ${!incomeTagged ? (stmt.match_status === 'unmatched' ? `
                                <button class="btn btn-sm btn-success btn-match" data-id="${stmt.id}" title="Match Bill">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            ` : `
                                <button class="btn btn-sm btn-warning btn-unmatch" data-id="${stmt.id}" title="Unmatch Bill">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            `) : ''}
                            ${incomeTagged ? `
                                <button class="btn btn-sm btn-outline-danger btn-income-unmatch mt-1" data-id="${stmt.id}" title="Remove Income Tag">
                                    <i class="bi bi-tag-x"></i>Unmatch Income
                                </button>
                            ` : ''}
                            ${radiantLinked ? `
                                <button class="btn btn-sm btn-outline-warning btn-radiant-unmatch mt-1" data-id="${stmt.id}" title="Remove Radiant pickup link">
                                    <i class="bi bi-brightness-high"></i> Unmatch Radiant
                                </button>
                            ` : ''}
                            <button class="btn btn-sm btn-danger btn-delete" style="display:none;" data-id="${stmt.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                        </div>
                    </td>
                </tr>
            `;
            
            tbody.append(row);
        });
    }
    
    // ============================================
    // ROW CLICK TO OPEN MATCH MODAL
    // ============================================
    $(document).on('click', '.statement-row-clickable', function() {
        currentStatementId = $(this).data('id');
        currentTxnAmount = parseFloat($(this).data('amount'));
        
        currentTxnData = {
            date: $(this).data('date'),
            reference: $(this).data('reference'),
            description: $(this).data('description'),
            amount: currentTxnAmount
        };
        
        // Update modal details
        $('#txnDate').text(formatDate(currentTxnData.date));
        $('#txnReference').text(currentTxnData.reference || '-');
        $('#txnDescription').text(currentTxnData.description);
        $('#txnAmount').text('₹' + formatNumber(currentTxnAmount));
        $('#pendingAmount').text(formatNumber(currentTxnAmount));
        $('#radiantMatchAgainstInput').val($(this).attr('data-radiant-match') || '');
        $('#radiantCashPickupIdInput').val($(this).attr('data-radiant-pickup-id') || '');
        
        // Reset
        selectedBills = [];
        
        // Show modal
        $('#matchTransactionModal').modal('show');
        
        // Search for matches
        searchMatchingBills(currentTxnAmount);
    });
    
    // ============================================
    // MATCH BUTTON CLICK (Alternative)
    // ============================================
    $(document).on('click', '.btn-match', function(e) {
        e.stopPropagation();
        $(this).closest('tr').click();
    });
    
    // ============================================
    // SEARCH MATCHING BILLS
    // ============================================
    function searchMatchingBills(amount, filters = {}) {
        $.ajax({
            url: routes.searchBills,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                amount: amount,
                tolerance: 100,
                ...filters
            },
            success: function(response) {
                if (response.success) {
                    currentBestMatches = response.best_matches || [];
                    currentPossibleMatches = response.possible_matches || [];
                    renderBestMatches(currentBestMatches);
                    renderPossibleMatches(currentPossibleMatches);
                    $('#bestMatchesCount').text(currentBestMatches.length);
                }
            },
            error: function(xhr) {
                console.error('Error searching bills:', xhr);
                toastr.error('Failed to search bills');
            }
        });
    }
    
    // ============================================
    // RENDER BEST MATCHES
    // ============================================
    function renderBestMatches(bills) {
        const container = $('#bestMatchesList');
        container.empty();
        
        if (!bills || bills.length === 0) {
            container.html('<p class="text-muted text-center py-3">No best matches found</p>');
            return;
        }
        
        bills.forEach(function(bill) {
            const difference = Math.abs(bill.balance_amount - currentTxnAmount);
            const matchCard = `
                <div class="bill-match-card-new" data-bill-id="${bill.id}" data-bill-amount="${bill.balance_amount}" data-bill-number="${bill.bill_number}" data-vendor="${bill.vendor_name}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>Bill for Rs.${formatNumber(bill.balance_amount)}</strong>
                            <div class="text-muted small">${bill.bill_gen_number}</div>
                            <div class="text-muted small">Dated ${formatDate(bill.bill_date)}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary btn-select-match">Match</button>
                    </div>
                    <div class="text-muted small">
                        ${bill.vendor_name} | Ref# ${bill.bill_number}
                        ${bill.zone_name ? ' | ' + bill.zone_name : ''}
                        ${bill.branch_name ? ' - ' + bill.branch_name : ''}
                    </div>
                    ${difference > 0 ? `<div class="text-warning small mt-1">Diff: ₹${formatNumber(difference)}</div>` : ''}
                </div>
            `;
            container.append(matchCard);
        });
    }
    
    // ============================================
    // RENDER POSSIBLE MATCHES
    // ============================================
    function renderPossibleMatches(bills) {
        const container = $('#possibleMatchesList');
        container.empty();
        
        if (!bills || bills.length === 0) {
            container.html('<p class="text-muted text-center py-3">No possible matches found</p>');
            return;
        }
        
        bills.forEach(function(bill) {
            const difference = Math.abs(bill.balance_amount - currentTxnAmount);
            const matchCard = `
                <div class="bill-match-card-new" data-bill-id="${bill.id}" data-bill-amount="${bill.balance_amount}" data-bill-number="${bill.bill_number}" data-vendor="${bill.vendor_name}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>Bill for Rs.${formatNumber(bill.balance_amount)}</strong>
                            <div class="text-muted small">${bill.bill_gen_number}</div>
                            <div class="text-muted small">Dated ${formatDate(bill.bill_date)}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-select-match">Match</button>
                    </div>
                    <div class="text-muted small">
                        ${bill.vendor_name} | Ref# ${bill.bill_number}
                        ${bill.zone_name ? ' | ' + bill.zone_name : ''}
                        ${bill.branch_name ? ' - ' + bill.branch_name : ''}
                    </div>
                    <div class="text-warning small mt-1">Diff: ₹${formatNumber(difference)}</div>
                </div>
            `;
            container.append(matchCard);
        });
    }
    
    // ============================================
    // DIRECT MATCH: card "Match" button calls API immediately
    // ============================================
    function doMatchWithBill(billId, billAmount, $btn) {
        const matchType = Math.abs((parseFloat(billAmount) || 0) - currentTxnAmount) < 1 ? 'full' : 'partial';
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Matching...');
        $.ajax({
            url: routes.match,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                bank_statement_id: currentStatementId,
                bill_id: billId,
                matched_amount: currentTxnAmount,
                match_type: matchType,
                notes: ''
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Bill matched successfully - Status changed to Categorized');
                    $('#matchTransactionModal').modal('hide');
                    loadStatements(currentPage);
                    updateStatistics();
                } else {
                    toastr.error(response.message || 'Failed to match');
                    $btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error matching bill';
                toastr.error(message);
                $btn.prop('disabled', false).html(originalHtml);
            },
            complete: function() {
                if ($btn.prop('disabled')) $btn.prop('disabled', false).html(originalHtml);
            }
        });
    }
    
    $(document).on('click', '.btn-select-match', function(e) {
        e.stopPropagation();
        const card = $(this).closest('.bill-match-card-new');
        const billId = card.data('bill-id');
        const billAmount = card.data('bill-amount');
        if (!billId) return;
        doMatchWithBill(billId, billAmount, $(this));
    });
    
    // ============================================
    // TOGGLE BEST MATCHES
    // ============================================
    $('#toggleBestMatches').on('click', function() {
        $('#bestMatchesList').slideToggle();
        $(this).find('i').toggleClass('bi-chevron-down bi-chevron-up');
    });
    
    // ============================================
    // TOGGLE POSSIBLE FILTER
    // ============================================
    $('#togglePossibleFilter').on('click', function() {
        $('#possibleFilterBox').slideToggle();
    });
    
    $('#cancelPossibleFilter').on('click', function() {
        $('#possibleFilterBox').slideUp();
    });

    function clearAllPossibleFilter() {
        $('#possibleAmountMin').val('1');
        $('#possibleAmountMax').val('');
        $('#possibleContact').val('');
        $('#possibleType').val('');
        $('#possibleReference').val('');
        $('#includeDeposits').prop('checked', false);
        var fromEl = document.getElementById('possibleDateFrom');
        var toEl = document.getElementById('possibleDateTo');
        if (fromEl && fromEl._flatpickr) {
            fromEl._flatpickr.clear();
        }
        if (toEl && toEl._flatpickr) {
            toEl._flatpickr.clear();
        }
        $('#possibleDateFrom').val('');
        $('#possibleDateTo').val('');
        searchMatchingBills(currentTxnAmount);
    }

    $('#clearAllPossibleFilterBtn').on('click', function() {
        clearAllPossibleFilter();
        $('#possibleFilterBox').slideUp();
    });
    
    $('#applyPossibleFilter').on('click', function() {
        const filters = {
            amount_min: $('#possibleAmountMin').val(),
            amount_max: $('#possibleAmountMax').val(),
            vendor_name: $('#possibleContact').val(),
            bill_status: $('#possibleType').val(),
            billno: $('#possiblebillno').val(),
        };
        
        // Convert dates if they exist
        const dateFrom = $('#possibleDateFrom').val();
        const dateTo = $('#possibleDateTo').val();
        
        if (dateFrom) {
            filters.date_from = moment(dateFrom, 'DD/MM/YYYY').format('YYYY-MM-DD');
        }
        if (dateTo) {
            filters.date_to = moment(dateTo, 'DD/MM/YYYY').format('YYYY-MM-DD');
        }
        
        searchMatchingBills(currentTxnAmount, filters);
        $('#possibleFilterBox').slideUp();
    });
    
    // ============================================
    // CONFIRM MATCH - Direct match: use first best or first possible if none selected
    // ============================================
    $('#confirmMatchBtnNew').on('click', function() {
        let firstBill = null;
        if (selectedBills.length > 0) {
            firstBill = selectedBills[0];
        } else {
            if (currentBestMatches.length > 0) {
                firstBill = { id: currentBestMatches[0].id, amount: currentBestMatches[0].balance_amount };
            } else if (currentPossibleMatches.length > 0) {
                firstBill = { id: currentPossibleMatches[0].id, amount: currentPossibleMatches[0].balance_amount };
            }
        }
        if (!firstBill) {
            toastr.warning('No bills to match. Try adjusting filters or add possible matches.');
            return;
        }
        const matchType = Math.abs((firstBill.amount || 0) - currentTxnAmount) < 1 ? 'full' : 'partial';
        $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Matching...');
        $.ajax({
            url: routes.match,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                bank_statement_id: currentStatementId,
                bill_id: firstBill.id,
                matched_amount: currentTxnAmount,
                match_type: matchType,
                notes: ''
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Bill matched successfully - Status changed to Categorized');
                    $('#matchTransactionModal').modal('hide');
                    loadStatements(currentPage);
                    updateStatistics();
                } else {
                    toastr.error(response.message || 'Failed to match');
                }
            },
            error: function(xhr) {
                let message = 'Error matching bill';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            },
            complete: function() {
                $('#confirmMatchBtnNew').prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Match');
            }
        });
    });
    $(document).click(function(e){
        console.log("Clicked:", e.target);
    });
    // ============================================
    // UNMATCH BUTTON
    // ============================================
    $(document).on('click', '.btn-unmatch', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        Swal.fire({
            title: 'Unmatch Transaction?',
            text: "This will restore the bill balance and change status back to Uncategorized.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Unmatch'
        }).then((result) => {
            if (result.isConfirmed) {
                unmatchStatement(id);
            }
        });
    });
    
    function unmatchStatement(id) {
        const url = routes.unmatch.replace(':id', id);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Transaction unmatched - Status changed to Uncategorized');
                    loadStatements(currentPage);
                    updateStatistics();
                } else {
                    toastr.error(response.message || 'Failed to unmatch');
                }
            },
            error: function(xhr) {
                console.error('Error unmatching:', xhr);
                toastr.error('Failed to unmatch transaction');
            }
        });
    }
    
    // ============================================
    // INCOME UNMATCH BUTTON
    // ============================================
    $(document).on('click', '.btn-income-unmatch', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        Swal.fire({
            title: 'Remove Income Tag?',
            text: 'This will clear the bank reference and recalculate differences in the income reconciliation record.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Remove Tag'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = routes.incomeUnmatch.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Income tag removed successfully');
                            loadStatements(currentPage);
                            updateStatistics();
                        } else {
                            toastr.error(response.message || 'Failed to remove income tag');
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error removing income tag';
                        toastr.error(msg);
                    }
                });
            }
        });
    });

    // ============================================
    // RADIANT UNMATCH (pickup link only; keyword kept)
    // ============================================
    $(document).on('click', '.btn-radiant-unmatch', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        Swal.fire({
            title: 'Remove Radiant pickup link?',
            text: 'The match keyword on this row will be kept. Only the pickup link and “Radiant linked” status are cleared.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove link'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = routes.radiantUnmatch.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Radiant link removed');
                            loadStatements(currentPage);
                            updateStatistics();
                        } else {
                            toastr.error(response.message || 'Failed to remove Radiant link');
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error removing Radiant link';
                        toastr.error(msg);
                    }
                });
            }
        });
    });

    // ============================================
    // DELETE BUTTON
    // ============================================
    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Delete Statement?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteStatement(id);
            }
        });
    });
    
    function deleteStatement(id) {
        const url = routes.destroy.replace(':id', id);
        
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Statement deleted successfully');
                    loadStatements(currentPage);
                    updateStatistics();
                } else {
                    toastr.error(response.message || 'Failed to delete');
                }
            },
            error: function(xhr) {
                let message = 'Failed to delete statement';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    }
    
    // ============================================
    // UPDATE STATISTICS
    // ============================================
    function updateStatistics() {
        $.ajax({
            url: routes.statements,
            type: 'GET',
            data: { stats_only: true },
            success: function(response) {
                if (response.total !== undefined) {
                    $('#totalStatements').text(response.total || 0);
                    $('#matchedStatements').text(response.matched || 0);
                    $('#unmatchedStatements').text(response.unmatched || 0);

                    const totalAmount = response.total_amount || 0;
                    $('#totalAmount').text('₹' + formatNumber(totalAmount));

                    // Income reconciliation stats
                    $('#incomeMatchedCount').text(response.income_matched || 0);
                    $('#incomeUnmatchedCount').text(response.income_unmatched || 0);

                    $('#radiantMatchedCount').text(response.radiant_matched || 0);
                    $('#radiantKeywordOnlyCount').text(response.radiant_keyword_only || 0);
                    $('#radiantUnmatchedCount').text(response.radiant_unmatched || 0);
                }
            }
        });
    }
    
    // ============================================
    // PAGINATION (compact: Prev, few pages, Next + per page)
    // ============================================
    function renderPagination(response) {
        const container = $('#paginationContainer');
        container.empty();
        if (!response.last_page || response.last_page <= 1) return;
        var cur = response.current_page;
        var last = response.last_page;
        var total = response.total || 0;
        var maxPages = 5;
        var from = Math.max(1, cur - Math.floor(maxPages / 2));
        var to = Math.min(last, from + maxPages - 1);
        if (to - from + 1 < maxPages) from = Math.max(1, to - maxPages + 1);
        var html = '<nav class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3"><div class="small text-muted">Page ' + cur + ' of ' + last + ' &bull; ' + total + ' total</div>';
        html += '<ul class="pagination pagination-sm mb-0">';
        html += '<li class="page-item' + (cur === 1 ? ' disabled' : '') + '"><a class="page-link" href="#" data-page="' + (cur - 1) + '">Prev</a></li>';
        if (from > 1) {
            html += '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
            if (from > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
        for (var i = from; i <= to; i++) {
            html += '<li class="page-item' + (i === cur ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
        if (to < last) {
            if (to < last - 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
            html += '<li class="page-item"><a class="page-link" href="#" data-page="' + last + '">' + last + '</a></li>';
        }
        html += '<li class="page-item' + (cur === last ? ' disabled' : '') + '"><a class="page-link" href="#" data-page="' + (cur + 1) + '">Next</a></li>';
        html += '</ul></nav>';
        container.html(html);
    }
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        if ($(this).closest('.page-item').hasClass('disabled')) return;
        var page = parseInt($(this).attr('data-page'), 10);
        if (page) loadStatements(page);
    });
    
    // ============================================
    // HELPER FUNCTIONS
    // ============================================
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        var m = moment(dateString);
        if (!m.isValid()) return '-';
        return m.format('DD MMM YYYY');
    }

    function formatDateTime(dateString) {
        if (!dateString) return '—';
        var m = moment(dateString);
        if (!m.isValid()) return '—';
        return m.format('DD MMM YYYY, HH:mm');
    }
    
    function truncateText(text, length) {
        if (!text) return '-';
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
    
    function getMatchStatusBadge(status) {
        const badges = {
            'unmatched': '<span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Unmatched</span>',
            'matched': '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Matched</span>',
            'partially_matched': '<span class="badge bg-info"><i class="bi bi-dash-circle me-1"></i>Partial</span>'
        };
        return badges[status] || badges['unmatched'];
    }
    
    function getCategoryBadge(status) {
        if (status === 'unmatched') {
            return '<span class="category-badge uncategorized"><i class="bi bi-question-circle me-1"></i>Uncategorized</span>';
        } else {
            return '<span class="category-badge categorized"><i class="bi bi-check-circle me-1"></i>Categorized</span>';
        }
    }
    
    // ============================================
    // TOASTR CONFIGURATION
    // ============================================
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // ============================================
    // INCOME TAG — zone → branch (by zone_id) + date picker + multi-mode
    // ============================================

    var incomeTagSelectedModes = new Set();
    var incomeTagFp = null; // flatpickr instance for income tag date

    // ---- Initialize when modal opens — Income Tag tab is active by default ----
    $('#matchTransactionModal').on('shown.bs.modal', function () {
        loadIncomeTagZones();
        initIncomeTagFlatpickr();
        updateIncomeTagSummary();

        // Auto-resolve description immediately (no need to click the tab)
        if (currentTxnData.description) {
            autoResolveIncomeTagFromDescription(currentTxnData.description, currentTxnData.date);
        } else if (currentTxnData.date && incomeTagFp && !incomeTagFp.selectedDates.length) {
            incomeTagFp.setDate(currentTxnData.date, true, 'Y-m-d');
        }
    });

    // ---- Readonly lock / unlock helpers for auto-resolved selects ----
    function lockIncomeTagSelects() {
        $('#incomeTagZone, #incomeTagBranch').css({
            'pointer-events': 'none',
            'background-color': '#f1f5f9',
            'opacity': '1',
            'border-color': '#cbd5e1',
            'cursor': 'not-allowed'
        }).attr('data-auto-locked', '1');
    }
    function unlockIncomeTagSelects() {
        $('#incomeTagZone, #incomeTagBranch').css({
            'pointer-events': '',
            'background-color': '',
            'opacity': '',
            'border-color': '',
            'cursor': ''
        }).removeAttr('data-auto-locked');
    }

    // ---- Reset when modal closes ----
    $('#matchTransactionModal').on('hidden.bs.modal', function () {
        incomeTagSelectedModes.clear();
        incomeTagInFlight = false; // reset guard so next open works cleanly
        $('#incomeTagZone').val('');
        $('#incomeTagZoneName').val('');
        $('#incomeTagBranch').html('<option value="">Select zone first...</option>').prop('disabled', true);
        $('#incomeTagBranchName').val('');
        if (incomeTagFp) incomeTagFp.clear();
        $('.income-tag-mode-btn').removeClass('selected');
        unlockIncomeTagSelects();
        updateIncomeTagSummary();
    });

    // ---- When Income Tag tab becomes active (user switches manually) ----
    $('#categorize-tab').on('shown.bs.tab', function () {
        updateIncomeTagSummary();
    });

    /**
     * Call the resolve-description endpoint and auto-populate zone/branch/mode/date.
     * Only fills fields that are currently empty (so manual edits are not overwritten).
     */
    function autoResolveIncomeTagFromDescription(description, txnDate) {
        $.ajax({
            url: routes.incomeTagResolve,
            type: 'GET',
            data: { description: description, txn_date: txnDate },
            success: function (res) {
                // ---- DATE: always set collection date (txn date - 1 day) ----
                if (res.date && incomeTagFp) {
                    incomeTagFp.setDate(res.date, true, 'd/m/Y');
                } else if (txnDate && incomeTagFp && !incomeTagFp.selectedDates.length) {
                    incomeTagFp.setDate(txnDate, true, 'Y-m-d');
                }

                // ---- MODE: auto-select detected mode button(s) — may be array ----
                if (res.mode) {
                    var modes = Array.isArray(res.mode) ? res.mode : [res.mode];
                    modes.forEach(function (m) {
                        var $modeBtn = $('.income-tag-mode-btn[data-mode="' + m + '"]');
                        if ($modeBtn.length && !$modeBtn.hasClass('selected')) {
                            $modeBtn.trigger('click');
                        }
                    });
                }

                if (!res.zone_id || !res.branch_id) {
                    updateIncomeTagSummary();
                    return; // branch not resolved — user fills manually
                }

                // ---- ZONE: set dropdown + hidden name field ----
                // Zones might not be loaded yet; wait until loaded then set
                function applyZoneAndBranch() {
                    var $zone = $('#incomeTagZone');
                    if ($zone.find('option[value="' + res.zone_id + '"]').length) {
                        $zone.val(res.zone_id).trigger('change');
                        $('#incomeTagZoneName').val(res.zone_name || '');

                        // ---- BRANCH: fetch branches for zone then set ----
                        $.ajax({
                            url: routes.incomeTagBranches,
                            type: 'GET',
                            data: { zone_id: res.zone_id },
                            success: function (branches) {
                                var opts = '<option value="">Select branch...</option>';
                                (branches || []).forEach(function (b) {
                                    opts += '<option value="' + b.id + '" data-name="' + b.name + '">' + b.name + '</option>';
                                });
                                var $branch = $('#incomeTagBranch');
                                $branch.html(opts).prop('disabled', false);

                                // Select the resolved branch
                                $branch.val(res.branch_id);
                                if ($branch.val() == res.branch_id) {
                                    $('#incomeTagBranchName').val(res.branch_name || '');
                                }

                                // Lock both dropdowns as readonly
                                lockIncomeTagSelects();
                                updateIncomeTagSummary();
                            }
                        });
                    } else {
                        // Zone options not rendered yet — retry after short delay
                        setTimeout(applyZoneAndBranch, 300);
                    }
                }
                applyZoneAndBranch();
            },
            error: function () {
                // Fallback: just set the date
                if (txnDate && incomeTagFp && !incomeTagFp.selectedDates.length) {
                    incomeTagFp.setDate(txnDate, true, 'Y-m-d');
                }
                updateIncomeTagSummary();
            }
        });
    }

    function initIncomeTagFlatpickr() {
        var el = document.getElementById('incomeTagDate');
        if (!el || el._flatpickr) return;
        incomeTagFp = flatpickr(el, {
            dateFormat: 'd/m/Y',
            maxDate: 'today',
            onChange: function () { updateIncomeTagSummary(); }
        });
    }

    // ---- Load zones (once) — value = zone_id, text = zone name ----
    function loadIncomeTagZones() {
        if ($('#incomeTagZone option').length > 1) return;
        $.get(routes.incomeTagZones, function (zones) {
            var opts = '<option value="">Select zone...</option>';
            (zones || []).forEach(function (z) {
                opts += '<option value="' + z.id + '" data-name="' + z.name + '">' + z.name + '</option>';
            });
            $('#incomeTagZone').html(opts);
        });
    }

    // ---- Zone change → fetch branches by zone_id (VendorController pattern) ----
    $(document).on('change', '#incomeTagZone', function () {
        var zoneId   = $(this).val();
        var zoneName = $(this).find('option:selected').data('name') || '';
        $('#incomeTagZoneName').val(zoneName);

        $('#incomeTagBranch')
            .html('<option value="">Loading branches...</option>')
            .prop('disabled', true);
        $('#incomeTagBranchName').val('');

        if (!zoneId) {
            $('#incomeTagBranch').html('<option value="">Select zone first...</option>');
            updateIncomeTagSummary();
            return;
        }

        // Use the same endpoint as VendorController's getbranchfetch
        $.ajax({
            url: routes.incomeTagBranches,
            type: 'GET',
            data: { zone_id: zoneId },
            success: function (branches) {
                var opts = '<option value="">Select branch...</option>';
                (branches || []).forEach(function (b) {
                    opts += '<option value="' + b.id + '" data-name="' + b.name + '">' + b.name + '</option>';
                });
                $('#incomeTagBranch').html(opts).prop('disabled', false);
            },
            error: function () {
                $('#incomeTagBranch').html('<option value="">Failed to load</option>');
                toastr.error('Could not load branches');
            }
        });
        updateIncomeTagSummary();
    });

    // ---- Branch change → store name ----
    $(document).on('change', '#incomeTagBranch', function () {
        var branchName = $(this).find('option:selected').data('name') || '';
        $('#incomeTagBranchName').val(branchName);
        updateIncomeTagSummary();
    });

    // ---- Mode buttons: toggle multi-select ----
    $(document).on('click', '.income-tag-mode-btn', function () {
        var mode = $(this).data('mode');
        if (incomeTagSelectedModes.has(mode)) {
            incomeTagSelectedModes.delete(mode);
            $(this).removeClass('selected');
        } else {
            incomeTagSelectedModes.add(mode);
            $(this).addClass('selected');
        }
        updateIncomeTagSummary();
    });

    // ---- Summary line ----
    function updateIncomeTagSummary() {
        var zoneName   = $('#incomeTagZoneName').val() || $('#incomeTagZone option:selected').data('name') || '';
        var branchName = $('#incomeTagBranchName').val() || '';
        var dateStr    = (incomeTagFp && incomeTagFp.selectedDates.length)
                            ? incomeTagFp.input.value : '';
        var modes      = incomeTagSelectedModes.size
                            ? Array.from(incomeTagSelectedModes).map(function(m){ return m.toUpperCase(); }).join(', ')
                            : '';

        var parts = [];
        if (zoneName)   parts.push('Zone: ' + zoneName);
        if (branchName) parts.push('Branch: ' + branchName);
        if (dateStr)    parts.push('Date: ' + dateStr);
        if (modes)      parts.push('Mode: ' + modes);

        $('#incomeTagFilterSummary').text(parts.length ? parts.join(' | ') : 'No filters applied yet');
    }

    // ---- Apply Income Tag ----
    // Use a flag to prevent double-submit (e.g. fast double-click)
    var incomeTagInFlight = false;

    $(document).on('click', '#applyIncomeTagBtn', function () {
        if (incomeTagInFlight) return; // guard against double-click

        var zoneName   = $('#incomeTagZoneName').val();
        var branchName = $('#incomeTagBranchName').val();
        var modes      = Array.from(incomeTagSelectedModes);

        // Date: prefer picker selection, fall back to transaction date
        var date = currentTxnData.date;
        if (incomeTagFp && incomeTagFp.selectedDates.length) {
            date = moment(incomeTagFp.selectedDates[0]).format('YYYY-MM-DD');
        }

        if (!zoneName)            { toastr.warning('Please select a Zone'); return; }
        if (!branchName)          { toastr.warning('Please select a Branch'); return; }
        if (!date)                { toastr.warning('No date selected'); return; }
        if (!modes.length)        { toastr.warning('Please select at least one Mode of Collection'); return; }
        if (!currentStatementId)  { toastr.warning('No bank statement selected'); return; }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Applying...');
        incomeTagInFlight = true;

        // ---- Fire requests SEQUENTIALLY (one after another) ----
        // Parallel firing causes a race condition: both card+upi requests see no
        // existing row at the same time and both INSERT, creating duplicates.
        // Sequential ensures the first mode creates the row; subsequent modes UPDATE it.
        var results = [];

        function fireNext(idx) {
            if (idx >= modes.length) {
                // All modes done — show result and refresh
                var created = results.filter(function (r) { return r && r.action === 'created'; }).length;
                var updated = results.filter(function (r) { return r && r.action === 'updated'; }).length;
                var msg = '';
                if (created) msg += created + ' record(s) created. ';
                if (updated) msg += updated + ' record(s) updated.';
                toastr.success((msg || 'Income tag applied — ') + 'Linked for: ' + modes.map(function (m) { return m.toUpperCase(); }).join(', '));
                $btn.prop('disabled', false).html('<i class="bi bi-tag me-1"></i>Apply Income Tag');
                incomeTagInFlight = false;
                $('#matchTransactionModal').modal('hide');
                loadStatements(currentPage);
                updateStatistics();
                return;
            }

            $.ajax({
                url:  routes.incomeTag,
                type: 'POST',
                data: {
                    _token:            $('meta[name="csrf-token"]').attr('content'),
                    bank_statement_id: currentStatementId,
                    zone:              zoneName,
                    branch:            branchName,
                    date:              date,
                    mode:              modes[idx]
                }
            }).done(function (r) {
                results.push(r);
                fireNext(idx + 1); // next mode only after this one completes
            }).fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error applying income tag';
                toastr.error(msg);
                $btn.prop('disabled', false).html('<i class="bi bi-tag me-1"></i>Apply Income Tag');
                incomeTagInFlight = false;
            });
        }

        fireNext(0);
    });

    // ---- Radiant match keyword (bank_statements.radiant_match_against) ----
    var radiantMatchInFlight = false;

    $(document).on('click', '#clearRadiantMatchBtn', function () {
        $('#radiantMatchAgainstInput').val('');
    });

    $(document).on('click', '#saveRadiantMatchBtn', function () {
        if (radiantMatchInFlight) return;
        if (!currentStatementId) {
            toastr.warning('No bank statement selected');
            return;
        }
        var val = ($('#radiantMatchAgainstInput').val() || '').trim();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Saving...');
        radiantMatchInFlight = true;

        var pickupVal = ($('#radiantCashPickupIdInput').val() || '').trim();

        $.ajax({
            url: routes.radiantMatchAgainst,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                bank_statement_id: currentStatementId,
                radiant_match_against: val,
                radiant_cash_pickup_id: pickupVal
            }
        }).done(function (r) {
            if (r.success) {
                toastr.success(r.message || 'Saved');
                loadStatements(currentPage);
                updateStatistics();
            } else {
                toastr.error(r.message || 'Save failed');
            }
        }).fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error saving Radiant match keyword';
            toastr.error(msg);
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save');
            radiantMatchInFlight = false;
        });
    });

    // ============================================
    // EMBEDDED BATCH UPLOAD PANEL (AJAX only — no full page load)
    // ============================================
    var batchInlinePage = 1;
    var batchInlineFilters = {};
    var batchInlinePerPage = 25;

    function escBatchCell(s) {
        if (s == null || s === '') return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;');
    }

    function fmtBatchTs(iso) {
        if (!iso) return '-';
        try {
            var d = new Date(String(iso).replace(' ', 'T'));
            return isNaN(d.getTime()) ? iso : d.toLocaleString();
        } catch (e) {
            return iso;
        }
    }

    function readBatchInlineFilters() {
        batchInlineFilters = {};
        var acc = ($('#fltAccount').val() || '').trim();
        var fn = ($('#fltFile').val() || '').trim();
        var u = ($('#fltUser').val() || '').trim();
        var df = $('#fltDateFrom').val();
        var dt = $('#fltDateTo').val();
        if (acc) batchInlineFilters.account_number = acc;
        if (fn) batchInlineFilters.file_name = fn;
        if (u) batchInlineFilters.uploaded_by = u;
        if (df) batchInlineFilters.date_from = df;
        if (dt) batchInlineFilters.date_to = dt;
        batchInlinePerPage = parseInt($('#fltPerPage').val(), 10) || 25;
    }

    function renderBatchInlineTable(res) {
        var rows = res.data || [];
        var tbody = $('#batchTableBody');
        if (!tbody.length) return;
        tbody.empty();
        if (!rows.length) {
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">No batches match your filters.</td></tr>');
            var ztot = parseInt(res.total, 10) || 0;
            $('#batchTotalHint').text(ztot ? 'Total: ' + ztot : 'Total: 0');
            $('#batchPageInfo').text('');
            return;
        }
        var total = parseInt(res.total, 10) || 0;
        var from = res.from != null ? parseInt(res.from, 10) : null;
        var to = res.to != null ? parseInt(res.to, 10) : null;
        $('#batchTotalHint').text(total ? 'Total: ' + total : 'Total: 0');
        if (!total) {
            $('#batchPageInfo').text('');
        } else if (from != null && to != null && !isNaN(from) && !isNaN(to)) {
            $('#batchPageInfo').text('Showing ' + from + '–' + to + ' of ' + total + ' · page ' + (parseInt(res.current_page, 10) || 1) + ' / ' + (parseInt(res.last_page, 10) || 1));
        } else {
            $('#batchPageInfo').text('Page ' + (parseInt(res.current_page, 10) || 1) + ' of ' + (parseInt(res.last_page, 10) || 1) + ' · ' + total + ' total');
        }

        rows.forEach(function (b) {
            var uid = escBatchCell(b.upload_batch_id);
            var dl = routes.batchFile + '/' + encodeURIComponent(b.upload_batch_id);
            var by = b.uploaded_by_name || b.uploaded_by_username || '-';
            var tr = '<tr>' +
                '<td><span class="badge bg-secondary">' + escBatchCell(b.id) + '</span></td>' +
                '<td><small>' + escBatchCell(fmtBatchTs(b.created_at)) + '</small></td>' +
                '<td><strong>' + escBatchCell(b.account_number) + '</strong>' +
                (b.bank_name ? '<br><small class="text-muted">' + escBatchCell(b.bank_name) + '</small>' : '') + '</td>' +
                '<td><small>' + escBatchCell(b.original_file_name) + '</small><br><code class="small">' + escBatchCell(b.upload_batch_id) + '</code></td>' +
                '<td>' + escBatchCell(b.rows_imported) + '</td>' +
                '<td>' + escBatchCell(b.duplicates) + '</td>' +
                '<td>' + escBatchCell(b.skipped) + '</td>' +
                '<td><small>' + escBatchCell(by) + '</small></td>' +
                '<td class="text-end text-nowrap">' +
                '<a class="btn btn-sm btn-outline-primary me-1" href="' + dl + '" title="Download"><i class="bi bi-download"></i></a>' +
                '<button type="button" class="btn btn-sm btn-outline-secondary btn-batch-preview" data-batch="' + uid + '">' +
                '<i class="bi bi-eye"></i></button>' +
                '</td></tr>';
            tbody.append(tr);
        });
    }

    function renderBatchInlinePagination(res) {
        var ul = $('#batchPagination');
        if (!ul.length) return;
        ul.empty();
        var last = parseInt(res.last_page, 10) || 1;
        var cur = parseInt(res.current_page, 10) || 1;
        var total = parseInt(res.total, 10) || 0;
        if (total === 0 || last <= 1) {
            return;
        }

        // Prefer Laravel paginator "links" (Next/Previous + page window) when present.
        if (res.links && Array.isArray(res.links) && res.links.length) {
            res.links.forEach(function (lnk) {
                var isActive = !!lnk.active;
                var hasUrl = !!(lnk.url && String(lnk.url).trim());
                var li = $('<li>').addClass('page-item').toggleClass('active', isActive).toggleClass('disabled', !hasUrl && !isActive);
                var labelHtml = String(lnk.label == null ? '' : lnk.label);
                var a = $('<a class="page-link" href="#">').attr('href', '#').html(labelHtml);
                if (hasUrl && !isActive) {
                    a.on('click', function (e) {
                        e.preventDefault();
                        try {
                            var u = new URL(lnk.url, window.location.origin);
                            var p = parseInt(u.searchParams.get('page'), 10) || 1;
                            loadBatchesInline(p);
                        } catch (err) {
                            loadBatchesInline(cur);
                        }
                    });
                } else {
                    a.on('click', function (e) { e.preventDefault(); });
                }
                li.append(a);
                ul.append(li);
            });
            return;
        }

        function addLi(label, p, disabled, active) {
            var li = $('<li class="page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '') + '">');
            var a = $('<a class="page-link" href="#">').text(label);
            if (!disabled && !active) {
                a.on('click', function (e) {
                    e.preventDefault();
                    loadBatchesInline(p);
                });
            }
            li.append(a);
            ul.append(li);
        }

        addLi('«', cur - 1, cur <= 1, false);
        var start = Math.max(1, cur - 2);
        var end = Math.min(last, cur + 2);
        for (var p = start; p <= end; p++) {
            addLi(String(p), p, false, p === cur);
        }
        addLi('»', cur + 1, cur >= last, false);
    }

    function loadBatchesInline(page) {
        if (!$('#batchTableBody').length || !routes.uploadBatches) return;
        batchInlinePage = page || 1;
        readBatchInlineFilters();
        var params = $.extend({ page: batchInlinePage, per_page: batchInlinePerPage }, batchInlineFilters);
        $('#batchTableBody').html('<tr><td colspan="9" class="text-center py-4 text-muted">Loading…</td></tr>');
        $.get(routes.uploadBatches, params, function (res) {
            renderBatchInlineTable(res);
            renderBatchInlinePagination(res);
        }).fail(function () {
            toastr.error('Could not load batches');
            $('#batchTableBody').html('<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load</td></tr>');
        });
    }

    $(document).on('click', '#btnApplyBatchFilters', function () {
        if ($('#batchUploadSection').is(':visible')) {
            loadBatchesInline(1);
        }
    });

    $(document).on('click', '#btnClearBatchFilters', function () {
        if (!$('#batchUploadSection').is(':visible')) return;
        $('#fltAccount,#fltFile,#fltUser').val('');
        $('#fltDateFrom,#fltDateTo').val('');
        $('#fltPerPage').val('25');
        batchInlineFilters = {};
        loadBatchesInline(1);
    });

    $(document).on('change', '#fltPerPage', function () {
        if ($('#batchUploadSection').is(':visible')) {
            loadBatchesInline(1);
        }
    });

});
