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
    let statementSortBy = 'transaction_date';
    let statementSortDir = 'desc';
    let perPage = 25;
    let currentBestMatches = [];
    let currentPossibleMatches = [];
    var quickFilterListsLoaded = false;
    var qfBranchLoadTimer = null;

    // ============================================================
    // QF MULTI-SELECT DROPDOWN MANAGER
    // ============================================================
    var _qfReg = {};
    var _qfInited = false;

    function _qfEsc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function qfRegister(selectId, btnId, menuId, placeholder) {
        var reg = {
            id: selectId,
            placeholder: placeholder || 'All',
            $btn: $('#' + btnId),
            $menu: $('#' + menuId),
            $select: $('#' + selectId)
        };
        _qfReg[selectId] = reg;

        // Sync pre-checked checkboxes → hidden select on init
        var initVals = [];
        reg.$menu.find('.qf-options-inner input[type="checkbox"]:checked').each(function () {
            initVals.push($(this).val());
        });
        if (initVals.length) {
            reg.$select.val(initVals);
            reg.$menu.find('.qf-all-chk').prop('checked', false);
        }

        // "All" row click
        reg.$menu.on('click', '.qf-menu-item-all', function (e) {
            var $allChk = reg.$menu.find('.qf-all-chk');
            if ($(e.target).is('input[type="checkbox"]')) {
                if (!$allChk.prop('checked')) {
                    $allChk.prop('checked', true);
                }
            }
            reg.$menu.find('.qf-options-inner input[type="checkbox"]').prop('checked', false);
            reg.$select.val(null);
            _qfSync(reg);
        });
        // Prevent "All" checkbox unchecking when nothing else selected
        reg.$menu.on('change', '.qf-all-chk', function () {
            if (!$(this).prop('checked')) {
                $(this).prop('checked', true);
            }
        });

        // Option checkboxes
        reg.$menu.on('change', '.qf-options-inner input[type="checkbox"]', function () {
            var vals = [];
            reg.$menu.find('.qf-options-inner input[type="checkbox"]:checked').each(function () {
                vals.push($(this).val());
            });
            reg.$select.val(vals.length ? vals : null);
            reg.$menu.find('.qf-all-chk').prop('checked', vals.length === 0);
            _qfSync(reg);
        });

        // Inline search
        reg.$menu.on('input', '.qf-search-input', function () {
            var q = $(this).val().toLowerCase().trim();
            reg.$menu.find('.qf-menu-item:not(.qf-menu-item-all)').each(function () {
                $(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) >= 0);
            });
        });

        _qfSync(reg);
        return reg;
    }

    function _qfSync(reg) {
        var vals = reg.$select.val() || [];
        var $txt = reg.$btn.find('.qf-btn-text');
        if (!vals || vals.length === 0) {
            $txt.text(reg.placeholder);
            reg.$btn.removeClass('qf-has-value');
        } else if (vals.length === 1) {
            var lbl = '';
            reg.$menu.find('.qf-options-inner .qf-menu-item').each(function () {
                var $inp = $(this).find('input[type="checkbox"]');
                if (String($inp.val()) === String(vals[0])) {
                    lbl = $(this).find('.qf-menu-item-text').first().text().trim();
                    if (!lbl) {
                        lbl = $(this).find('label, span').first().text().trim();
                    }
                    return false;
                }
            });
            $txt.text(lbl || vals[0]);
            reg.$btn.addClass('qf-has-value');
        } else {
            $txt.text(vals.length + ' selected');
            reg.$btn.addClass('qf-has-value');
        }
        reg.$menu.find('.qf-all-chk').prop('checked', !vals || vals.length === 0);
    }

    /** Stable DOM id for option row (checkbox outside label text — avoids theme hiding span inside label) */
    function qfOptionRowId(selectId, index) {
        return 'brqf_' + String(selectId).replace(/[^a-zA-Z0-9_-]/g, '_') + '_opt_' + index;
    }

    function qfPopulate(selectId, options, keepSel) {
        var reg = _qfReg[selectId];
        if (!reg) return;
        var prev = keepSel ? (reg.$select.val() || []) : [];
        var optHtml = '', selHtml = '';
        options.forEach(function (o, idx) {
            var rawVal = o.value != null ? o.value : o.id;
            var v = _qfEsc(String(rawVal));
            var l = _qfEsc(String(o.label != null ? o.label : o.name));
            var chk = prev.indexOf(String(rawVal)) >= 0 ? ' checked' : '';
            var oid = qfOptionRowId(selectId, idx);
            optHtml += '<div class="qf-menu-item">' +
                '<input type="checkbox" class="qf-opt-chk" id="' + oid + '" value="' + v + '"' + chk + '>' +
                '<label class="qf-menu-item-text" for="' + oid + '">' + l + '</label></div>';
            selHtml += '<option value="' + v + '">' + l + '</option>';
        });
        reg.$menu.find('.qf-options-inner').html(optHtml);
        reg.$select.html(selHtml);
        if (prev.length) {
            reg.$select.val(prev);
            reg.$menu.find('.qf-all-chk').prop('checked', false);
            reg.$menu.find('.qf-options-inner input[type="checkbox"]').each(function () {
                $(this).prop('checked', prev.indexOf($(this).val()) >= 0);
            });
        }
        _qfSync(reg);
    }

    function qfSetVals(selectId, vals) {
        var reg = _qfReg[selectId];
        if (!reg) return;
        var v = vals && vals.length ? vals : null;
        reg.$menu.find('.qf-options-inner input[type="checkbox"]').prop('checked', false);
        reg.$select.val(v);
        if (v) {
            v.forEach(function (val) {
                reg.$menu.find('.qf-options-inner input[value="' + _qfEsc(String(val)) + '"]').prop('checked', true);
            });
        }
        _qfSync(reg);
    }

    function qfResetOne(id) { qfSetVals(id, []); }
    function qfResetAll() { Object.keys(_qfReg).forEach(qfResetOne); }

    function initQfDropdowns() {
        if (_qfInited) return;
        _qfInited = true;
        qfRegister('qfFinancialYear', 'qfBtn-financialYear', 'qfMenu-financialYear', 'All years');
        if ($('#qfBtn-bankCompany').length) {
            qfRegister('qfBankCompany', 'qfBtn-bankCompany', 'qfMenu-bankCompany', 'All companies');
        }
        if ($('#qfBtn-bankAccount').length) {
            qfRegister('qfBankAccount', 'qfBtn-bankAccount', 'qfMenu-bankAccount', 'All accounts');
        }
        qfRegister('qfZone',         'qfBtn-zone',         'qfMenu-zone',         'All zones');
        qfRegister('qfBranch',       'qfBtn-branch',       'qfMenu-branch',       'All branches');
        qfRegister('qfCategory',     'qfBtn-category',     'qfMenu-category',     'All');
        qfRegister('qfTxnType',      'qfBtn-txnType',      'qfMenu-txnType',      'All types');
        qfRegister('qfExpenseMatch', 'qfBtn-expenseMatch', 'qfMenu-expenseMatch', 'All statuses');
        qfRegister('qfRadiantMatch', 'qfBtn-radiantMatch', 'qfMenu-radiantMatch', 'All');
        qfRegister('qfIncomeMatch',  'qfBtn-incomeMatch',  'qfMenu-incomeMatch',  'All');
        qfRegister('qfMatchedBy',    'qfBtn-matchedBy',    'qfMenu-matchedBy',    'Anyone');
        qfRegister('qfVendor',       'qfBtn-vendor',       'qfMenu-vendor',       'All vendors');
    }

    /** Return first selected company id (string) from the qf multi-select, or '' */
    function getQfBankCompanyVal() {
        var v = $('#qfBankCompany').val();
        if (Array.isArray(v)) { return (v[0] || '').toString().trim(); }
        return (v || '').toString().trim();
    }

    $('#qfResetBtn').on('click', function () {
        clearAllFilters();
    });

    function bankAccountsOn() {
        return typeof window.bankAccountsEnabled !== 'undefined' && window.bankAccountsEnabled;
    }

    function getIndianFinancialYearRangeMoment() {
        var ref = moment();
        var y = ref.month() >= 3 ? ref.year() : ref.year() - 1;
        return {
            from: moment({ year: y, month: 3, day: 1 }),
            to: moment({ year: y + 1, month: 2, day: 31 })
        };
    }

    function mergeDefaultListParamsIntoFilters() {
        if (!(typeof window.bankReconDateFrom === 'string' && window.bankReconDateFrom
            && typeof window.bankReconDateTo === 'string' && window.bankReconDateTo)) {
            var fyFallback = getIndianFinancialYearRangeMoment();
            window.bankReconDateFrom = fyFallback.from.format('YYYY-MM-DD');
            window.bankReconDateTo = fyFallback.to.format('YYYY-MM-DD');
        }
        currentFilters.date_from = window.bankReconDateFrom;
        currentFilters.date_to = window.bankReconDateTo;
        currentFilters.sort_by = statementSortBy;
        currentFilters.sort_dir = statementSortDir;
    }

    function updateBankReconSortHeaderVisual() {
        var $btns = $('#statementsTable thead .bank-recon-sort-btns');
        if (!$btns.length) return;
        $btns.find('.bank-recon-sort-btn').removeClass('is-active');
        $btns.find('.bank-recon-sort-btn[data-sort-dir="' + statementSortDir + '"]').addClass('is-active');
    }

    mergeDefaultListParamsIntoFilters();
    updateBankReconSortHeaderVisual();

    var lastModalAccountsList = [];
    var lastToolbarAccountsList = [];
    var lastBankAccountsFullList = [];
    var lastCompaniesList = [];

    function companyListOptionsHtml(includeAllOption, allLabel) {
        var opts = '';
        if (includeAllOption) {
            opts += '<option value="">' + (allLabel || 'All companies') + '</option>';
        } else {
            opts += '<option value="">Select company…</option>';
        }
        (lastCompaniesList || []).forEach(function (c) {
            opts += '<option value="' + c.id + '">' + $('<div>').text(c.company_name || '').html() + '</option>';
        });
        return opts;
    }

    function filterAccountsByCompanyId(list, cid) {
        var c = (cid || '').toString().trim();
        if (!c) {
            return (list || []).slice();
        }
        return (list || []).filter(function (a) {
            return String(a.company_id) === c;
        });
    }

    function replaceBankAccountSelectOptions($sel, list, placeholderOptText, selectedVal) {
        var inner = '<option value="">' + $('<div>').text(placeholderOptText).html() + '</option>';
        (list || []).forEach(function (a) {
            var label = (a.account_number || '') + (a.bank_name ? ' — ' + a.bank_name : '');
            inner += '<option value="' + a.id + '">' + $('<div>').text(label).html() + '</option>';
        });
        $sel.html(inner);
        if (selectedVal && (list || []).some(function (a) { return String(a.id) === String(selectedVal); })) {
            $sel.val(String(selectedVal));
        } else {
            $sel.val('');
        }
    }

    function syncAllBankAccountDropdowns() {
        if (!bankAccountsOn()) {
            return;
        }
        var qfCo = getQfBankCompanyVal();
        var filtCo = ($('#filterBankCompany').val() || '').trim();
        var mainCo = ($('#mainUploadCompany').val() || '').trim();
        var modalCo = ($('#modalUploadCompany').val() || '').trim();

        var savedQfAcc = $('#qfBankAccount').val() || [];
        var savedFilterAcc = $('#filterBankAccount').val();
        var savedMainAcc = $('#mainUploadBankAccount').val();
        var savedModalAcc = $('#modalUploadBankAccount').val();

        var qfList = filterAccountsByCompanyId(lastBankAccountsFullList, qfCo);
        lastToolbarAccountsList = qfList;
        if (_qfReg['qfBankAccount']) {
            var qfAccOptions = qfList.map(function (a) {
                var num = (a.account_number || '').toString().trim();
                var bank = (a.bank_name || '').toString().trim();
                return { value: a.id, label: num + (bank ? ' — ' + bank : '') };
            });
            qfPopulate('qfBankAccount', qfAccOptions, true);
            if (savedQfAcc && savedQfAcc.length) {
                var keep = savedQfAcc.filter(function (id) {
                    return qfList.some(function (a) { return String(a.id) === String(id); });
                });
                if (keep.length) {
                    qfSetVals('qfBankAccount', keep);
                }
            }
        }

        replaceBankAccountSelectOptions(
            $('#filterBankAccount'),
            filterAccountsByCompanyId(lastBankAccountsFullList, filtCo),
            'All accounts',
            savedFilterAcc
        );

        replaceBankAccountSelectOptions(
            $('#mainUploadBankAccount'),
            filterAccountsByCompanyId(lastBankAccountsFullList, mainCo),
            mainCo ? 'Select account…' : 'Select company first…',
            savedMainAcc
        );
        $('#mainUploadBankAccount').prop('disabled', !mainCo);

        replaceBankAccountSelectOptions(
            $('#modalUploadBankAccount'),
            filterAccountsByCompanyId(lastBankAccountsFullList, modalCo),
            modalCo ? 'Select account…' : 'Select company first…',
            savedModalAcc
        );
        $('#modalUploadBankAccount').prop('disabled', !modalCo);
    }
    window.syncAllBankAccountDropdowns = syncAllBankAccountDropdowns;

    function accModalEsc(s) {
        if (s == null || s === '') return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;');
    }

    function renderToolbarAccountChips() {
        var $wrap = $('#bankReconToolbarAccountChips');
        if (!$wrap.length) return;
        var list = lastToolbarAccountsList || [];
        var activeId = (currentFilters.bank_account_id != null && String(currentFilters.bank_account_id) !== '')
            ? String(currentFilters.bank_account_id)
            : '';
        var parts = [];
        parts.push(
            '<button type="button" class="bank-recon-toolbar-account-chip' + (activeId === '' ? ' is-active' : '') + '" data-account-id="" title="All accounts">All</button>'
        );
        list.forEach(function (a) {
            var id = String(a.id);
            var num = accModalEsc(a.account_number || '—');
            var title = (a.account_number || '') + (a.bank_name ? ' — ' + a.bank_name : '');
            parts.push(
                '<button type="button" class="bank-recon-toolbar-account-chip' + (activeId === id ? ' is-active' : '') + '" data-account-id="' + accModalEsc(id) + '" title="' + accModalEsc(title) + '">' + num + '</button>'
            );
        });
        $wrap.html(parts.join(''));
    }

    function loadBankAccounts(selectedId) {
        if (!bankAccountsOn() || !routes.accounts) return;
        $.get(routes.accounts, function (res) {
            lastBankAccountsFullList = (res && res.data) ? res.data : [];
            lastCompaniesList = (res && res.companies) ? res.companies : [];

            var preserve = {
                qfCo: getQfBankCompanyVal(),
                filtCo: $('#filterBankCompany').val(),
                mainCo: $('#mainUploadCompany').val(),
                modalCo: $('#modalUploadCompany').val(),
                newCo: $('#newAccCompanyId').val()
            };

            var coOptions = (lastCompaniesList || []).map(function (c) {
                return { value: c.id, label: c.company_name || '' };
            });
            var coMandatory = companyListOptionsHtml(false);
            var coAll = companyListOptionsHtml(true, 'All companies');
            $('#newAccCompanyId').html(coMandatory);
            $('#mainUploadCompany').html(coMandatory);
            $('#modalUploadCompany').html(coMandatory);
            $('#filterBankCompany').html(coAll);

            if (_qfReg['qfBankCompany']) {
                qfPopulate('qfBankCompany', coOptions, false);
                if (preserve.qfCo) {
                    qfSetVals('qfBankCompany', [String(preserve.qfCo)]);
                }
            }

            if (preserve.filtCo) {
                $('#filterBankCompany').val(preserve.filtCo);
            }
            if (preserve.mainCo) {
                $('#mainUploadCompany').val(preserve.mainCo);
            }
            if (preserve.modalCo) {
                $('#modalUploadCompany').val(preserve.modalCo);
            }
            if (preserve.newCo) {
                $('#newAccCompanyId').val(preserve.newCo);
            }

            syncAllBankAccountDropdowns();

            if (selectedId) {
                var accPick = lastBankAccountsFullList.find(function (x) { return String(x.id) === String(selectedId); });
                if (accPick && accPick.company_id) {
                    $('#mainUploadCompany').val(String(accPick.company_id));
                    $('#modalUploadCompany').val(String(accPick.company_id));
                }
                syncAllBankAccountDropdowns();
                qfSetVals('qfBankAccount', [String(selectedId)]);
                $('.bank-account-select').each(function () {
                    var sid = $(this).attr('id');
                    if (sid === 'qfBankAccount') {
                        return;
                    }
                    $(this).val(String(selectedId));
                });
            }
            if (currentFilters.bank_account_id && !selectedId) {
                var accCf = lastBankAccountsFullList.find(function (x) {
                    return String(x.id) === String(currentFilters.bank_account_id);
                });
                if (accCf && accCf.company_id) {
                    qfSetVals('qfBankCompany', [String(accCf.company_id)]);
                    $('#filterBankCompany').val(String(accCf.company_id));
                }
                syncAllBankAccountDropdowns();
                $('#filterBankAccount').val(String(currentFilters.bank_account_id));
                qfSetVals('qfBankAccount', [String(currentFilters.bank_account_id)]);
            }
            renderToolbarAccountChips();
        });
    }

    function pruneEmptyFilterKeys(obj) {
        Object.keys(obj).forEach(function (k) {
            var v = obj[k];
            if (v === '' || v === undefined || v === null) {
                delete obj[k];
            } else if (Array.isArray(v) && v.length === 0) {
                delete obj[k];
            }
        });
        return obj;
    }

    function buildStatementFiltersFromDom() {
        var f = {
            sort_by: statementSortBy,
            sort_dir: statementSortDir
        };
        var desc = ($('#filterDescription').val() || '').trim();
        if (desc) {
            f.search = desc;
        }
        var ref = ($('#filterReference').val() || '').trim();
        if (ref) {
            f.reference_number = ref;
        }
        var amin = ($('#filterAmountMin').val() || '').trim();
        if (amin) {
            f.amount_min = amin;
        }
        var amax = ($('#filterAmountMax').val() || '').trim();
        if (amax) {
            f.amount_max = amax;
        }
        if (window.bankReconMatchedDateFrom) {
            f.matched_date_from = window.bankReconMatchedDateFrom;
        }
        if (window.bankReconMatchedDateTo) {
            f.matched_date_to = window.bankReconMatchedDateTo;
        }

        var fyMulti = $('#qfFinancialYear').length ? ($('#qfFinancialYear').val() || []) : [];
        if (fyMulti && fyMulti.length) {
            f.financial_year_ranges = fyMulti;
        } else {
            if (window.bankReconDateFrom) {
                f.date_from = window.bankReconDateFrom;
            }
            if (window.bankReconDateTo) {
                f.date_to = window.bankReconDateTo;
            }
        }

        if (bankAccountsOn() && $('#qfBankAccount').length) {
            var bMulti = $('#qfBankAccount').val() || [];
            if (bMulti.length === 1) {
                f.bank_account_id = String(bMulti[0]);
            } else if (bMulti.length > 1) {
                f.bank_account_ids = bMulti.map(String);
            } else if ($('#filterBankAccount').length && $('#filterBankAccount').val()) {
                f.bank_account_id = $('#filterBankAccount').val();
            } else {
                var qc = getQfBankCompanyVal() || ($('#filterBankCompany').val() || '').trim();
                if (qc) {
                    f.company_id = qc;
                }
            }
        } else if ($('#filterBankAccount').length && $('#filterBankAccount').val()) {
            f.bank_account_id = $('#filterBankAccount').val();
        } else if (bankAccountsOn()) {
            var qcOnly = ($('#filterBankCompany').val() || '').trim();
            if (qcOnly) {
                f.company_id = qcOnly;
            }
        }

        var zoneIds = $('#qfZone').length ? ($('#qfZone').val() || []) : [];
        zoneIds = zoneIds.map(function (x) { return parseInt(x, 10); }).filter(function (x) { return x > 0; });
        if (zoneIds.length) {
            f.zone_ids = zoneIds;
        }

        var branchIds = $('#qfBranch').length ? ($('#qfBranch').val() || []) : [];
        branchIds = branchIds.map(function (x) { return parseInt(x, 10); }).filter(function (x) { return x > 0; });
        if (branchIds.length) {
            f.branch_ids = branchIds;
        }

        var cats = $('#qfCategory').length ? ($('#qfCategory').val() || []) : [];
        if (cats.length) {
            f.categories = cats;
        }

        var txn = $('#qfTxnType').length ? ($('#qfTxnType').val() || []) : [];
        if (txn.length) {
            f.txn_types = txn;
        }

        var rad = $('#qfRadiantMatch').length ? ($('#qfRadiantMatch').val() || []) : [];
        if (rad.length) {
            f.radiant_matches = rad;
        } else if ($('#filterRadiantMatch').length && $('#filterRadiantMatch').val()) {
            f.radiant_match = $('#filterRadiantMatch').val();
        }

        var exp = $('#qfExpenseMatch').length ? ($('#qfExpenseMatch').val() || []) : [];
        if (exp.length) {
            f.match_statuses = exp;
        } else if ($('#filterMatchStatus').val()) {
            f.match_status = $('#filterMatchStatus').val();
        }

        var incm = $('#qfIncomeMatch').length ? ($('#qfIncomeMatch').val() || []) : [];
        if (incm.length) {
            f.income_matches = incm;
        } else if ($('#filterIncomeMatch').val()) {
            f.income_match = $('#filterIncomeMatch').val();
        }

        var vend = $('#qfVendor').length ? ($('#qfVendor').val() || []) : [];
        if (vend.length) {
            f.vendor_names = vend;
        }

        var mb = $('#qfMatchedBy').length ? ($('#qfMatchedBy').val() || []) : [];
        mb = mb.map(function (x) { return parseInt(x, 10); }).filter(function (x) { return x > 0; });
        if (mb.length === 1) {
            f.matched_by_user_id = String(mb[0]);
        } else if (mb.length > 1) {
            f.matched_by_user_ids = mb;
        } else if ($('#filterMatchedByUser').length && $('#filterMatchedByUser').val()) {
            f.matched_by_user_id = $('#filterMatchedByUser').val();
        }

        return pruneEmptyFilterKeys(f);
    }

    window.applyBankReconDomFiltersToCurrent = function () {
        currentFilters = buildStatementFiltersFromDom();
    };

    /** After clearing the transaction date picker, restore window range from FY dropdowns if possible */
    window.restoreBankReconWindowDatesAfterClearingTxnDay = function () {
        var qQuick = $('#qfFinancialYear').val() || [];
        if (qQuick.length === 1) {
            var p = String(qQuick[0]).split('|');
            if (p.length === 2) {
                window.bankReconDateFrom = p[0];
                window.bankReconDateTo = p[1];
                return;
            }
        }
        var mf = $('#filterFinancialYear').val();
        if (mf) {
            var p2 = String(mf).split('|');
            if (p2.length === 2) {
                window.bankReconDateFrom = p2[0];
                window.bankReconDateTo = p2[1];
                return;
            }
        }
        window.bankReconDateFrom = null;
        window.bankReconDateTo = null;
    };

    /**
     * Modal "Transaction date": flatpickr range mode — reflects window.bankReconDateFrom / To.
     * Uses bankReconSkipFpChange so programmatic setDate does not recurse into onChange.
     */
    window.syncBankReconTransactionDatePickers = function () {
        var el = document.getElementById('filterDateRange');
        if (!el || !el._flatpickr) return;
        var from = window.bankReconDateFrom;
        var to = window.bankReconDateTo;
        window.bankReconSkipFpChange = true;
        try {
            if (from && to) {
                el._flatpickr.setDate([
                    moment(from, 'YYYY-MM-DD').toDate(),
                    moment(to, 'YYYY-MM-DD').toDate()
                ], false);
            } else if (from && !to) {
                el._flatpickr.setDate([moment(from, 'YYYY-MM-DD').toDate()], false);
            } else {
                el._flatpickr.clear();
                $(el).val('');
            }
        } finally {
            setTimeout(function () { window.bankReconSkipFpChange = false; }, 0);
        }
    };

    function syncModalWidgetsFromQuickFilterFields() {
        var exp = $('#qfExpenseMatch').val() || [];
        $('#filterMatchStatus').val(exp.length === 1 ? exp[0] : '');

        var inc = $('#qfIncomeMatch').val() || [];
        $('#filterIncomeMatch').val(inc.length === 1 ? inc[0] : '');

        var rad = $('#qfRadiantMatch').val() || [];
        $('#filterRadiantMatch').val(rad.length === 1 ? rad[0] : '');

        var mb = $('#qfMatchedBy').val() || [];
        $('#filterMatchedByUser').val(mb.length === 1 ? String(mb[0]) : '');

        if (bankAccountsOn() && $('#filterBankCompany').length) {
            $('#filterBankCompany').val(getQfBankCompanyVal());
        }
        if (bankAccountsOn()) {
            syncAllBankAccountDropdowns();
        }
        if (bankAccountsOn() && $('#filterBankAccount').length) {
            var b = $('#qfBankAccount').val() || [];
            $('#filterBankAccount').val(b.length === 1 ? String(b[0]) : '');
        }
    }

    function copyModalMatchFiltersIntoQuickRow() {
        var m = $('#filterMatchStatus').val();
        qfSetVals('qfExpenseMatch', m ? [m] : []);

        var im = $('#filterIncomeMatch').val();
        qfSetVals('qfIncomeMatch', im ? [im] : []);

        var r = $('#filterRadiantMatch').val();
        qfSetVals('qfRadiantMatch', r ? [r] : []);

        var u = $('#filterMatchedByUser').val();
        qfSetVals('qfMatchedBy', u ? [String(u)] : []);

        if ($('#filterBankCompany').length) {
            var coFromModal = ($('#filterBankCompany').val() || '').trim();
            qfSetVals('qfBankCompany', coFromModal ? [coFromModal] : []);
        }
        if (bankAccountsOn()) {
            syncAllBankAccountDropdowns();
        }
        if ($('#filterBankAccount').length) {
            var b = $('#filterBankAccount').val();
            qfSetVals('qfBankAccount', b ? [String(b)] : []);
        }

        if ($('#filterFinancialYear').length) {
            var fy = $('#filterFinancialYear').val();
            qfSetVals('qfFinancialYear', fy ? [fy] : []);
        }
    }

    function applyStatementFiltersFromDomAndReload(closeModal) {
        if (closeModal) {
            copyModalMatchFiltersIntoQuickRow();
        }
        currentFilters = buildStatementFiltersFromDom();
        syncModalWidgetsFromQuickFilterFields();
        if (closeModal) {
            $('#filterModal').modal('hide');
        }
        renderToolbarAccountChips();
        loadStatements(1);
    }

    function ensureQuickFilterListsLoaded() {
        if (!routes.quickFilterOptions) {
            if (typeof window.applyBankReconDomFiltersToCurrent === 'function') {
                window.applyBankReconDomFiltersToCurrent();
            }
            return;
        }
        if (quickFilterListsLoaded) {
            refreshQuickFilterBranches();
            if (typeof window.applyBankReconDomFiltersToCurrent === 'function') {
                window.applyBankReconDomFiltersToCurrent();
            }
            return;
        }
        quickFilterListsLoaded = true;
        $.get(routes.quickFilterOptions, function (res) {
            var cats = [
                { value: 'categorized', label: 'Categorized' },
                { value: 'uncategorized', label: 'Uncategorized' }
            ];
            qfPopulate('qfCategory', cats, true);

            var vendors = (res.vendor_names || []).map(function (v) { return { value: v, label: v }; });
            qfPopulate('qfVendor', vendors, true);

            var zones = (res.zones || []).map(function (z) { return { value: z.id, label: z.name }; });
            qfPopulate('qfZone', zones, true);
            refreshQuickFilterBranches();
            if (typeof window.applyBankReconDomFiltersToCurrent === 'function') {
                window.applyBankReconDomFiltersToCurrent();
            }
        }).fail(function () {
            quickFilterListsLoaded = false;
        });
    }

    function refreshQuickFilterBranches() {
        if (!routes.quickFilterOptions) return;
        var zoneIds = $('#qfZone').val() || [];
        var params = zoneIds.length ? { zone_ids: zoneIds } : {};
        $.get(routes.quickFilterOptions, params, function (res) {
            var branches = (res.branches || []).map(function (b) { return { value: b.id, label: b.name }; });
            qfPopulate('qfBranch', branches, true);
        });
    }

    $(document).on('click', '#bankReconToolbarAccountChips .bank-recon-toolbar-account-chip', function (e) {
        e.preventDefault();
        var raw = $(this).attr('data-account-id');
        if (raw === undefined || raw === '') {
            delete currentFilters.bank_account_id;
            delete currentFilters.bank_account_ids;
            if ($('#filterBankAccount').length) { $('#filterBankAccount').val(''); }
            qfSetVals('qfBankAccount', []);
        } else {
            currentFilters.bank_account_id = String(raw);
            if ($('#filterBankAccount').length) { $('#filterBankAccount').val(String(raw)); }
            qfSetVals('qfBankAccount', [String(raw)]);
        }
        renderToolbarAccountChips();
        loadStatements(1);
    });

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
        if ($('#newAccCompanyId').length) {
            $('#newAccCompanyId').val(acc.company_id ? String(acc.company_id) : '');
        }
        $('#newAccNumber').val(acc.account_number || '');
        $('#newAccBank').val(acc.bank_name || '');
        $('#newAccBranch').val(acc.branch_name || '');
        $('#newAccIfsc').val(acc.ifsc_code || '');
        $('#newAccHolder').val(acc.account_holder_name || '');
        $('#newAccNotes').val(acc.notes || '');
        $('#newAccountTabLabel').text('Edit account');
        $('#btnSaveNewAccountLabel').text('Update account');
        $('#newAccountFormHint').text('Update details and save. Account number must be unique within the company.');
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
            tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">No accounts yet. Use <strong>New account</strong> to add one.</td></tr>');
            return;
        }
        lastModalAccountsList.forEach(function (a) {
            var row = '<tr>' +
                '<td><small>' + accModalEsc(a.company_name || '—') + '</small></td>' +
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
        tbody.html('<tr><td colspan="7" class="text-center text-muted py-3">Loading…</td></tr>');
        $.get(routes.accounts, function (res) {
            lastBankAccountsFullList = (res && res.data) ? res.data : [];
            lastCompaniesList = (res && res.companies) ? res.companies : [];
            renderModalAccountList(lastBankAccountsFullList);
            syncAllBankAccountDropdowns();
        }).fail(function () {
            tbody.html('<tr><td colspan="7" class="text-center text-danger py-3">Could not load accounts.</td></tr>');
        });
    }

    if (bankAccountsOn()) {
        loadBankAccounts();
    }

    function applyQfBankCompanyFilter() {
        var v = getQfBankCompanyVal();
        if ($('#filterBankCompany').length) {
            $('#filterBankCompany').val(v);
        }
        syncAllBankAccountDropdowns();
        qfSetVals('qfBankAccount', []);
        currentFilters = buildStatementFiltersFromDom();
        renderToolbarAccountChips();
        loadStatements(1);
    }

    $(document).on('change', '#qfMenu-bankCompany .qf-options-inner input[type="checkbox"]', function () {
        setTimeout(applyQfBankCompanyFilter, 0);
    });
    $(document).on('click', '#qfMenu-bankCompany .qf-menu-item-all', function () {
        setTimeout(applyQfBankCompanyFilter, 0);
    });

    $(document).on('change', '#filterBankCompany', function () {
        var cid = ($(this).val() || '').trim();
        replaceBankAccountSelectOptions(
            $('#filterBankAccount'),
            filterAccountsByCompanyId(lastBankAccountsFullList, cid),
            'All accounts',
            ''
        );
    });

    $(document).on('change', '#mainUploadCompany', function () {
        syncAllBankAccountDropdowns();
    });

    $(document).on('change', '#modalUploadCompany', function () {
        syncAllBankAccountDropdowns();
    });

    $(document).on('click', '#statementsTable thead .bank-recon-sort-btn', function (e) {
        e.preventDefault();
        var dir = $(this).data('sort-dir');
        if (dir !== 'asc' && dir !== 'desc') return;
        statementSortDir = dir;
        statementSortBy = 'transaction_date';
        currentFilters.sort_by = statementSortBy;
        currentFilters.sort_dir = statementSortDir;
        updateBankReconSortHeaderVisual();
        loadStatements(1);
    });

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
        if (bankAccountsOn()) {
            var coM = $('#modalUploadCompany').val();
            if (!coM) {
                toastr.error('Select a company');
                return;
            }
            $('#modalUploadBankAccount').prop('disabled', false);
            if (!$('#modalUploadBankAccount').val()) {
                toastr.error('Select a bank account for this upload');
                $('#modalUploadBankAccount').prop('disabled', !coM);
                return;
            }
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
        initQfDropdowns();
        if (bankAccountsOn()) {
            loadBankAccounts();
        }
        if (routes.matchedByOptions) {
            loadBankReconciliationMatchedByOptions();
        }
        if (typeof window.applyBankReconDomFiltersToCurrent === 'function') {
            window.applyBankReconDomFiltersToCurrent();
        }
        ensureQuickFilterListsLoaded();
    }

    function showBatchUploadSection() {
        $('#importSection').hide();
        $('#statementsSection').hide();
        $('#batchUploadSection').stop(true, true).fadeIn(150);
        if (bankAccountsOn() && routes.uploadBatches && $('#batchTableBody').length) {
            loadBatchesInline(1);
        }
    }
    
    $('#backToStatementBtn').on('click', function () {
        showStatementsSection();
        loadStatements(currentPage || 1);
    });

    $('#headerUploadBtn').on('click', function () {
        showImportSection();
        var el = document.getElementById('importSection');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    $('#btnOpenBatchUploadView').on('click', function () {
        showBatchUploadSection();
    });

    $('#btnCloseBatchUploadView').on('click', function () {
        showStatementsSection();
        loadStatements(currentPage || 1);
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

    /**
     * Sync FY dropdown(s) from window dates. Pass { skipQf: true } when only refreshing the modal
     * (e.g. on open) so we do not clear or overwrite the quick-filter FY row.
     */
    window.syncBankReconFinancialYearSelect = function (opts) {
        var skipQf = opts && opts.skipQf;
        var $sel = $('#filterFinancialYear');
        if (!$sel.length) return;
        var from = window.bankReconDateFrom;
        var to = window.bankReconDateTo;
        if (!from || !to) {
            $sel.val('');
            if (!skipQf && $('#qfFinancialYear').length) {
                $('#qfFinancialYear').val([]);
            }
            return;
        }
        var needle = String(from) + '|' + String(to);
        var found = false;
        $sel.find('option').each(function () {
            var v = $(this).attr('value');
            if (v && v === needle) {
                $sel.val(v);
                found = true;
                return false;
            }
        });
        if (!found) {
            $sel.val('');
        }
        if (!skipQf) {
            qfSetVals('qfFinancialYear', found ? [needle] : []);
        }
    };

    function loadBankReconciliationMatchedByOptions() {
        if (!routes.matchedByOptions) return;
        var prev = $('#filterMatchedByUser').val();
        var prevQf = $('#qfMatchedBy').length ? ($('#qfMatchedBy').val() || []) : [];
        $.get(routes.matchedByOptions, function (res) {
            var list = (res && res.data) ? res.data : [];
            var $sel = $('#filterMatchedByUser');
            if (!$sel.length) return;
            var inner = '<option value="">Anyone</option>';
            list.forEach(function (u) {
                var label = u.name || ('User #' + u.id);
                inner += '<option value="' + String(u.id) + '">' + $('<div>').text(label).html() + '</option>';
            });
            $sel.html(inner);
            if (prev) {
                var has = false;
                $sel.find('option').each(function () {
                    if ($(this).val() === String(prev)) {
                        has = true;
                        return false;
                    }
                });
                if (has) {
                    $sel.val(String(prev));
                }
            }
            if (_qfReg['qfMatchedBy']) {
                var qfMbOptions = list.map(function (u) {
                    return { value: u.id, label: u.name || ('User #' + u.id) };
                });
                qfPopulate('qfMatchedBy', qfMbOptions, true);
            }
        });
    }

    $(document).on('change', '#filterFinancialYear', function () {
        var v = $(this).val();
        if (!v) return;
        var parts = v.split('|');
        if (parts.length !== 2) return;
        window.bankReconDateFrom = parts[0];
        window.bankReconDateTo = parts[1];
        if (typeof window.syncBankReconTransactionDatePickers === 'function') {
            window.syncBankReconTransactionDatePickers();
        }
        qfSetVals('qfFinancialYear', [v]);
    });

    $(document).on('change', '#qfFinancialYear', function () {
        var vals = $(this).val() || [];
        if (!vals || vals.length !== 1) { return; }
        var v = vals[0];
        var parts = String(v).split('|');
        if (parts.length !== 2) return;
        window.bankReconDateFrom = parts[0];
        window.bankReconDateTo = parts[1];
        if ($('#filterFinancialYear').length) {
            $('#filterFinancialYear').val(v);
        }
        if (typeof window.syncBankReconTransactionDatePickers === 'function') {
            window.syncBankReconTransactionDatePickers();
        }
    });

    $(document).on('change', '#qfZone', function () {
        clearTimeout(qfBranchLoadTimer);
        qfBranchLoadTimer = setTimeout(refreshQuickFilterBranches, 200);
    });

    $('#qfOpenFullFilterBtn').on('click', function () {
        $('#filterModal').modal('show');
    });

    $('#qfApplyBtn').on('click', function () {
        applyStatementFiltersFromDomAndReload(false);
    });

    $('#filterModal').on('show.bs.modal', function () {
        loadBankReconciliationMatchedByOptions();
        if (typeof window.syncBankReconTransactionDatePickers === 'function') {
            window.syncBankReconTransactionDatePickers();
        }
        if (typeof window.syncBankReconFinancialYearSelect === 'function') {
            window.syncBankReconFinancialYearSelect({ skipQf: true });
        }
        if (bankAccountsOn() && $('#filterBankCompany').length) {
            $('#filterBankCompany').val(getQfBankCompanyVal());
            syncAllBankAccountDropdowns();
            var b = $('#qfBankAccount').val() || [];
            if ($('#filterBankAccount').length) {
                $('#filterBankAccount').val(b.length === 1 ? String(b[0]) : '');
            }
        }
    });

    function bindStatCardFilterKeydown($el) {
        $el.on('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });
    }

    /** Clear quick stat filters (match / income / radiant) but keep date range, account, search, amounts, etc. */
    function clearStatQuickFiltersFromCurrentFilters() {
        delete currentFilters.match_status;
        delete currentFilters.match_statuses;
        delete currentFilters.income_match;
        delete currentFilters.income_matches;
        delete currentFilters.radiant_match;
        delete currentFilters.radiant_matches;
        if ($('#filterMatchStatus').length) {
            $('#filterMatchStatus').val('');
        }
        if ($('#filterIncomeMatch').length) {
            $('#filterIncomeMatch').val('');
        }
        if ($('#filterRadiantMatch').length) {
            $('#filterRadiantMatch').val('');
        }
        qfSetVals('qfExpenseMatch', []);
        qfSetVals('qfIncomeMatch', []);
        qfSetVals('qfRadiantMatch', []);
    }

    $('#statCardFilterAll').on('click', function () {
        clearStatQuickFiltersFromCurrentFilters();
        loadStatements(1);
    });
    bindStatCardFilterKeydown($('#statCardFilterAll'));

    $('#statCardFilterMatched').on('click', function () {
        delete currentFilters.match_statuses;
        currentFilters.match_status = 'matched';
        delete currentFilters.income_match;
        delete currentFilters.income_matches;
        if ($('#filterMatchStatus').length) {
            $('#filterMatchStatus').val('matched');
        }
        if ($('#filterIncomeMatch').length) {
            $('#filterIncomeMatch').val('');
        }
        qfSetVals('qfExpenseMatch', ['matched']);
        qfSetVals('qfIncomeMatch', []);
        loadStatements(1);
    });
    bindStatCardFilterKeydown($('#statCardFilterMatched'));

    $('#statCardFilterIncomeMatched').on('click', function () {
        delete currentFilters.match_status;
        delete currentFilters.match_statuses;
        delete currentFilters.income_matches;
        currentFilters.income_match = 'income_matched';
        if ($('#filterMatchStatus').length) {
            $('#filterMatchStatus').val('');
        }
        if ($('#filterIncomeMatch').length) {
            $('#filterIncomeMatch').val('income_matched');
        }
        qfSetVals('qfExpenseMatch', []);
        qfSetVals('qfIncomeMatch', ['income_matched']);
        loadStatements(1);
    });
    bindStatCardFilterKeydown($('#statCardFilterIncomeMatched'));

    function clearAllFilters() {
        var fy = getIndianFinancialYearRangeMoment();
        window.bankReconDateFrom = fy.from.format('YYYY-MM-DD');
        window.bankReconDateTo = fy.to.format('YYYY-MM-DD');
        statementSortBy = 'transaction_date';
        statementSortDir = 'desc';

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
        qfSetVals('qfBankCompany', []);
        if ($('#filterBankCompany').length) {
            $('#filterBankCompany').val('');
        }
        if ($('#mainUploadCompany').length) {
            $('#mainUploadCompany').val('');
        }
        if ($('#modalUploadCompany').length) {
            $('#modalUploadCompany').val('');
        }
        if (bankAccountsOn()) {
            syncAllBankAccountDropdowns();
        }
        var matchedEl = document.getElementById('filterMatchedDateRange');
        if (matchedEl) {
            if (matchedEl._flatpickr) {
                matchedEl._flatpickr.clear();
            }
            $(matchedEl).val('');
        }
        window.bankReconMatchedDateFrom = null;
        window.bankReconMatchedDateTo = null;

        if ($('#filterMatchedByUser').length) {
            $('#filterMatchedByUser').val('');
        }

        qfResetAll();

        var fyVal2 = fy.from.format('YYYY-MM-DD') + '|' + fy.to.format('YYYY-MM-DD');
        if ($('#filterFinancialYear').length) {
            var $fyModal = $('#filterFinancialYear');
            var fyOk = false;
            $fyModal.find('option').each(function () {
                if ($(this).val() === fyVal2) {
                    fyOk = true;
                    return false;
                }
            });
            $fyModal.val(fyOk ? fyVal2 : '');
        }
        var fyPick = [];
        $('#qfFinancialYear option').each(function () {
            if ($(this).val() === fyVal2) {
                fyPick = [fyVal2];
                return false;
            }
        });
        qfSetVals('qfFinancialYear', fyPick);

        if (typeof window.syncBankReconTransactionDatePickers === 'function') {
            window.syncBankReconTransactionDatePickers();
        }

        currentFilters = buildStatementFiltersFromDom();
        updateBankReconSortHeaderVisual();
        renderToolbarAccountChips();
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
    $('#applyFiltersBtn').on('click', function () {
        applyStatementFiltersFromDomAndReload(true);
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
            var coMain = $('#mainUploadCompany').val();
            if (!coMain) {
                toastr.error('Select a company');
                return;
            }
            $('#mainUploadBankAccount').prop('disabled', false);
            var accMain = $('#mainUploadBankAccount').val();
            if (!accMain) {
                toastr.error('Select the bank account this statement belongs to');
                $('#mainUploadBankAccount').prop('disabled', !coMain);
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
                if (response.dashboard) {
                    applyDashboardStats(response.dashboard);
                } else {
                    applyDashboardStats(response);
                }
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

    window.bankReconMatchFiles = window.bankReconMatchFiles || {};

    function parseStmtMatchAttachments(stmt) {
        try {
            var raw = stmt.match_attachments_json != null ? stmt.match_attachments_json : stmt.attachments_json;
            if (raw == null || raw === '') return [];
            if (typeof raw === 'string') return JSON.parse(raw);
            return Array.isArray(raw) ? raw : [];
        } catch (e) {
            return [];
        }
    }

    function buildNatureFilesCell(stmt) {
        var att = parseStmtMatchAttachments(stmt);
        window.bankReconMatchFiles[stmt.id] = att;
        var names = (stmt.resolved_br_nature_account_names || '').toString().trim();
        if (!names && !att.length) {
            return '<span class="text-muted">—</span>';
        }
        var html = '<div class="bank-recon-nature-files small">';
        if (names) {
            html += '<div class="text-muted">' + escapeAttr(names) + '</div>';
        }
        if (att.length) {
            html += '<button type="button" class="btn btn-link btn-sm p-0 align-baseline bank-recon-att-view" data-stmt-id="' + String(stmt.id) + '" title="View attachments"><i class="bi bi-paperclip"></i> ' + att.length + '</button>';
        }
        html += '</div>';
        return html;
    }

    /** Matched bill first line: e.g. AUG-25 · BILL-GEN-123 when both exist */
    function buildMatchedBillTitleHtml(stmt) {
        var rBillNum = (stmt.resolved_bill_number || '').toString().trim();
        var rBillGen = (stmt.resolved_bill_gen_number || '').toString().trim();
        var hydratedBillNo = (stmt.bill_number || '').toString().trim();
        var parts = [];
        if (rBillNum) parts.push(rBillNum);
        if (rBillGen && rBillGen !== rBillNum) parts.push(rBillGen);
        if (!parts.length && hydratedBillNo) parts.push(hydratedBillNo);
        if (!parts.length && rBillGen) parts.push(rBillGen);
        if (!parts.length) return '';
        return parts.map(function (p) { return escapeAttr(p); }).join(' <span class="text-muted">·</span> ');
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
                    <td colspan="17" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-3">No statements found</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        statements.forEach(function(stmt) {
            const matchStatusBadge = getMatchStatusBadge(stmt);
            const categoryBadge = getCategoryBadge(stmt);
            const amount = stmt.withdrawal > 0 ? stmt.withdrawal : stmt.deposit;
            const amountType = stmt.withdrawal > 0 ? 'withdrawal' : 'deposit';
            
            const matchedBillTitle = buildMatchedBillTitleHtml(stmt);
            const resolvedVendor = (stmt.resolved_vendor_name || stmt.vendor_name || '').toString().trim();
            const lineAccountNames = (stmt.bill_line_account_names || '').toString().trim();
            const zbParts = [stmt.resolved_bill_zone_name, stmt.resolved_bill_branch_name].map(function (x) {
                return (x || '').toString().trim();
            }).filter(Boolean);
            const zoneBranchText = zbParts.join(' · ');
            let matchedBillInfo = '-';
            if (stmt.match_status !== 'unmatched' && matchedBillTitle) {
                const natureRow = lineAccountNames
                    ? `<small class="text-muted d-block bank-recon-bill-nature" title="Nature of payment (bill line accounts)">${escapeAttr(lineAccountNames)}</small>`
                    : '';
                const zoneRow = zoneBranchText
                    ? `<small class="text-muted d-block bank-recon-bill-zonebranch">${escapeAttr(zoneBranchText)}</small>`
                    : '';
                matchedBillInfo = `
                    <div class="matched-bill-info">
                        <strong class="bank-recon-matched-bill-title">${matchedBillTitle}</strong><br>
                        <small class="text-muted">${escapeAttr(resolvedVendor)}</small>
                        ${natureRow}
                        ${zoneRow}
                        <small class="text-success">₹${formatNumber(stmt.bill_amount || 0)}</small>
                    </div>
                `;
            }
            let matchedbyInfo = '<span class="text-muted">—</span>';
            var billName = (stmt.bbm_matched_by_name || stmt.matched_by_name || '').toString().trim();
            var billUser = (stmt.bbm_matched_by_username || stmt.matched_by_username || '').toString().trim();
            var incomeByName = (stmt.income_matched_by_name || '').toString().trim();
            var mbParts = [];
            if (billName) {
                mbParts.push(
                    '<div class="small mb-1"><span class="text-muted">Expense</span><br><strong>' +
                        escapeAttr(billName) +
                        '</strong><br><small class="text-muted">' +
                        escapeAttr(billUser) +
                        '</small></div>'
                );
            }
            if (incomeByName) {
                mbParts.push(
                    '<div class="small"><span class="text-muted">Income</span><br><strong>' +
                        escapeAttr(incomeByName) +
                        '</strong></div>'
                );
            }
            if (mbParts.length) {
                matchedbyInfo = '<div class="matched-bill-info">' + mbParts.join('') + '</div>';
            }

            // Income reconciliation tag details
            const incomeTagged = stmt.income_match_status === 'income_matched';
            let incomeTagCell = '<span class="text-muted small"><i class="bi bi-dash"></i> Not tagged</span>';
            if (incomeTagged) {
                incomeTagCell = `
                    <div>
                        <span class="badge bg-info text-white mb-1">
                            <i class="bi bi-arrow-left-right me-1"></i>Income Tagged
                        </span><br>
                        <small class="text-dark fw-semibold">${escapeAttr(stmt.income_matched_branch || '')}</small><br>
                        ${buildIncomeTaggedDateAmountRowsHtml(stmt)}
                        <small class="text-muted">By: <strong>${escapeAttr(stmt.income_matched_by_name || '')}</strong></small>
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
                    data-match-status="${escapeAttr(stmt.match_status)}"
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
                        ${buildNatureFilesCell(stmt)}
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
    
    /** 'withdrawal' = bill match tab only; 'deposit' = income tag + radiant only */
    var bankReconModalTxnMode = 'deposit';

    function resetMatchModalTabsVisibility() {
        $('#matchTabs .nav-item').removeClass('d-none');
        $('#matchTabContent .tab-pane').removeClass('d-none');
        $('#matchTabs .nav-link').removeClass('active');
        $('#matchTabContent .tab-pane').removeClass('show active');
        $('#categorize-tab').addClass('active');
        $('#categorize-content').addClass('show active');
    }

    function applyMatchModalLayout(mode) {
        bankReconModalTxnMode = mode;
        var $lis = $('#matchTabs .nav-item');
        var $matchLi = $lis.eq(0);
        var $incomeLi = $lis.eq(1);
        var $radiantLi = $lis.eq(2);
        $('#matchTabs .nav-link').removeClass('active');
        $('#matchTabContent .tab-pane').removeClass('show active');

        if (mode === 'withdrawal') {
            $matchLi.removeClass('d-none');
            $incomeLi.addClass('d-none');
            $radiantLi.addClass('d-none');
            $('#match-content').removeClass('d-none');
            $('#categorize-content, #radiant-match-content').addClass('d-none');
            $('#match-tab').addClass('active');
            $('#match-content').addClass('show active');
        } else {
            $matchLi.addClass('d-none');
            $incomeLi.removeClass('d-none');
            $radiantLi.removeClass('d-none');
            $('#match-content').addClass('d-none');
            $('#categorize-content, #radiant-match-content').removeClass('d-none');
            $('#categorize-tab').addClass('active');
            $('#categorize-content').addClass('show active');
        }
    }

    // ============================================
    // ROW CLICK TO OPEN MATCH MODAL
    // ============================================
    $(document).on('click', '.statement-row-clickable', function(e) {
        if ($(e.target).closest('.action-buttons').length) {
            return;
        }

        var matchStatus = ($(this).attr('data-match-status') || '').toString();
        if (matchStatus === 'matched') {
            return;
        }

        var txnType = ($(this).data('type') || $(this).attr('data-type') || '').toString();

        currentStatementId = $(this).data('id');
        currentTxnAmount = parseFloat($(this).data('amount'));

        currentTxnData = {
            date: $(this).data('date'),
            reference: $(this).data('reference'),
            description: $(this).data('description'),
            amount: currentTxnAmount
        };

        $('#txnDate').text(formatDate(currentTxnData.date));
        $('#txnReference').text(currentTxnData.reference || '-');
        $('#txnDescription').text(currentTxnData.description);
        $('#txnAmount').text('₹' + formatNumber(currentTxnAmount));
        $('#pendingAmount').text(formatNumber(currentTxnAmount));
        $('#radiantMatchAgainstInput').val($(this).attr('data-radiant-match') || '');
        $('#radiantCashPickupIdInput').val($(this).attr('data-radiant-pickup-id') || '');

        selectedBills = [];

        if (txnType === 'withdrawal') {
            applyMatchModalLayout('withdrawal');
        } else {
            applyMatchModalLayout('deposit');
        }

        $('#matchTransactionModal').modal('show');

        if (txnType === 'withdrawal') {
            searchMatchingBills(currentTxnAmount);
        }
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
    // DIRECT MATCH: open details modal (nature + files) then POST
    // ============================================
    var pendingBillMatch = null;

    /** Bootstrap 5 has no jQuery .modal(); use native API. */
    function bankReconShowModal(el) {
        if (!el) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                bootstrap.Modal.getOrCreateInstance(el).show();
            } else {
                new bootstrap.Modal(el).show();
            }
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(el).modal('show');
        }
    }

    function bankReconHideModal(el) {
        if (!el) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && typeof bootstrap.Modal.getOrCreateInstance === 'function') {
            bootstrap.Modal.getOrCreateInstance(el).hide();
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(el).modal('hide');
        }
    }

    function teardownBankMatchNatureDropdown() {
        var $inp = $('.br-bank-match-nature .dropdown-search-input');
        if (!$inp.length) return;
        var $dd = $inp.data('dropdown');
        if ($dd && $dd.length) {
            $dd.remove();
            $inp.removeData('dropdown');
        }
    }

    function rebuildBankMatchNatureList() {
        var $list = $('#bankMatchNatureAccountList');
        if (!$list.length) return;
        $list.empty();
        var raw = window.BANK_RECON_CHART_ACCOUNTS || [];
        if (!raw.length) {
            $list.append(
                '<div class="br-bank-match-nature-empty text-muted small px-2 py-3">No chart accounts in account_tbl.</div>'
            );
            return;
        }
        raw.forEach(function (a) {
            var id = a.id;
            var text = a.text || 'Account #' + id;
            $list.append(
                $('<div/>')
                    .attr('data-id', id)
                    .attr('data-value', text)
                    .text(text)
            );
        });
    }

    function updateBankMatchNatureSelection($dropdown) {
        var wrapper = $dropdown.data('wrapper');
        if (!wrapper || !wrapper.length) return;
        var selectedItems = [];
        var selectedIds = [];
        $dropdown.find('.dropdown-list.multiselect div').not('.br-bank-match-nature-empty').each(function () {
            var $row = $(this);
            if ($row.hasClass('selected')) {
                selectedItems.push($row.text().trim());
                selectedIds.push($row.data('id'));
            }
        });
        wrapper.find('.br-bank-match-nature-input').val(selectedItems.join(', '));
        wrapper.find('#bankMatchNatureIds').val(selectedIds.join(','));
    }

    /** Filter nature list rows (shared by keyup + input so paste/search works). */
    function filterBankMatchNatureList($innerSearch) {
        var q = ($innerSearch.val() || '').toLowerCase().trim();
        $innerSearch
            .closest('.dropdown-menu')
            .find('.dropdown-list.multiselect div')
            .not('.br-bank-match-nature-empty')
            .each(function () {
                var t = $(this).text().toLowerCase();
                $(this).toggle(q === '' || t.indexOf(q) > -1);
            });
    }

    $(document).on('click', '.br-bank-match-nature .dropdown-search-input', function (e) {
        e.stopPropagation();
        // Keep dropdown inside the modal so Bootstrap's focus trap does not steal focus from .inner-search
        $('#bankMatchDetailsModal .dropdown-menu.tax-dropdown.br-bank-match-dd').hide();

        var $input = $(this);
        var $wrap = $input.closest('.br-bank-match-nature');
        var $dropdown = $input.data('dropdown');

        if (!$dropdown || !$dropdown.length) {
            $dropdown = $input.siblings('.dropdown-menu').clone(true, true);
            $dropdown.removeClass('br-bank-match-dd-template');
            $dropdown.addClass('br-bank-match-dd');
            $wrap.append($dropdown);
            $input.data('dropdown', $dropdown);
        }

        $dropdown.data('wrapper', $wrap);

        $dropdown.css({
            position: 'absolute',
            top: '100%',
            left: 0,
            right: 0,
            width: '100%',
            minWidth: 280,
            marginTop: 4,
            zIndex: 2000
        }).show();

        $dropdown.find('.inner-search').val('');
        filterBankMatchNatureList($dropdown.find('.inner-search'));
        setTimeout(function () {
            $dropdown.find('.inner-search').trigger('focus');
        }, 0);
    });

    $(document).on('keyup input', '.br-bank-match-dd .inner-search', function () {
        filterBankMatchNatureList($(this));
    });

    $(document).on('click', '.br-bank-match-dd .dropdown-list.multiselect div', function (e) {
        e.stopPropagation();
        if ($(this).hasClass('br-bank-match-nature-empty')) return;
        $(this).toggleClass('selected');
        updateBankMatchNatureSelection($(this).closest('.tax-dropdown'));
    });

    $(document).on('click', '.br-bank-match-dd .br-bank-match-select-all', function (e) {
        e.stopPropagation();
        var $dropdown = $(this).closest('.tax-dropdown');
        $dropdown.find('.dropdown-list.multiselect div').not('.br-bank-match-nature-empty').addClass('selected');
        updateBankMatchNatureSelection($dropdown);
    });

    $(document).on('click', '.br-bank-match-dd .br-bank-match-clear', function (e) {
        e.stopPropagation();
        var $dropdown = $(this).closest('.tax-dropdown');
        $dropdown.find('.dropdown-list.multiselect div').not('.br-bank-match-nature-empty').removeClass('selected');
        updateBankMatchNatureSelection($dropdown);
    });

    $(document).on('click', function (e) {
        if (
            !$(e.target).closest('.br-bank-match-nature').length &&
            !$(e.target).closest('.br-bank-match-dd').length
        ) {
            $('#bankMatchDetailsModal .dropdown-menu.tax-dropdown.br-bank-match-dd').hide();
        }
    });

    function openBankMatchDetailsModal(billId, billAmount, $btn) {
        pendingBillMatch = {
            billId: billId,
            billAmount: billAmount,
            $btn: $btn || null
        };
        $('#bankMatchAttachmentsInput').val('');
        teardownBankMatchNatureDropdown();
        rebuildBankMatchNatureList();
        $('.br-bank-match-nature').find('#bankMatchNatureIds').val('');
        $('.br-bank-match-nature .br-bank-match-nature-input').val('');
        bankReconShowModal(document.getElementById('bankMatchDetailsModal'));
    }

    $('#bankMatchDetailsModal').on('hidden.bs.modal', function () {
        teardownBankMatchNatureDropdown();
        pendingBillMatch = null;
    });

    $('#btnConfirmBankMatchDetails').on('click', function () {
        if (!pendingBillMatch || !currentStatementId) return;
        var matchType = Math.abs((parseFloat(pendingBillMatch.billAmount) || 0) - currentTxnAmount) < 1 ? 'full' : 'partial';
        var natureCsv = ($('#bankMatchNatureIds').val() || '').trim();
        var ids = natureCsv ? natureCsv.split(',').filter(function (x) {
            return String(x).trim() !== '';
        }) : [];
        if (!ids.length) {
            toastr.warning('Please select at least one Nature of payment (chart account).');
            return;
        }

        var fd = new FormData();
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        fd.append('bank_statement_id', currentStatementId);
        fd.append('bill_id', pendingBillMatch.billId);
        fd.append('matched_amount', currentTxnAmount);
        fd.append('match_type', matchType);
        fd.append('notes', '');
        ids.forEach(function (id) {
            fd.append('nature_account_ids[]', id);
        });
        var filesEl = document.getElementById('bankMatchAttachmentsInput');
        if (filesEl && filesEl.files) {
            for (var i = 0; i < filesEl.files.length; i++) {
                fd.append('attachments[]', filesEl.files[i]);
            }
        }

        var $confirm = $(this);
        var $origBtn = pendingBillMatch.$btn;
        $confirm.prop('disabled', true);
        if ($origBtn && $origBtn.length) {
            $origBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Matching...');
        }

        $.ajax({
            url: routes.match,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success('Bill matched successfully - Status changed to Categorized');
                    bankReconHideModal(document.getElementById('bankMatchDetailsModal'));
                    bankReconHideModal(document.getElementById('matchTransactionModal'));
                    loadStatements(currentPage);
                    updateStatistics();
                } else {
                    toastr.error(response.message || 'Failed to match');
                }
            },
            error: function (xhr) {
                var message = 'Error matching bill';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors && xhr.responseJSON.errors.nature_account_ids) {
                        var ne = xhr.responseJSON.errors.nature_account_ids;
                        message = Array.isArray(ne) ? ne[0] : ne;
                    }
                }
                toastr.error(message);
            },
            complete: function () {
                $confirm.prop('disabled', false);
                if ($origBtn && $origBtn.length) {
                    $origBtn.prop('disabled', false).text('Match');
                }
            }
        });
    });

    function bankReconResolveAttachmentUrl(f) {
        var u = (f && f.url) ? String(f.url).trim() : '';
        if (!u || u === '#') {
            return '#';
        }
        if (/^https?:\/\//i.test(u)) {
            return u;
        }
        if (u.charAt(0) === '/') {
            return (typeof window.location !== 'undefined' ? window.location.origin : '') + u;
        }
        return u;
    }

    $(document).on('click', '.bank-recon-att-view', function (e) {
        e.preventDefault();
        var sid = $(this).data('stmt-id');
        var files = (window.bankReconMatchFiles && window.bankReconMatchFiles[sid]) ? window.bankReconMatchFiles[sid] : [];
        var $body = $('#bankMatchAttachmentsViewerBody');
        $body.empty();
        if (!files.length) {
            $body.html('<p class="text-muted mb-0">No files</p>');
        } else {
            files.forEach(function (f) {
                var url = bankReconResolveAttachmentUrl(f);
                var name = (f && f.name) ? String(f.name) : 'File';
                var ext = name.split('.').pop().toLowerCase();
                var preview = '';
                if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].indexOf(ext) !== -1) {
                    preview = '<div class="mb-2 border rounded p-2 text-center bg-light"><img src="' + escapeAttr(url) + '" alt="" class="img-fluid rounded" style="max-height:420px;"></div>';
                } else if (ext === 'pdf') {
                    preview = '<div class="mb-2 border rounded overflow-hidden bg-secondary bg-opacity-10"><embed src="' + escapeAttr(url) + '" type="application/pdf" width="100%" height="480px" class="d-block"></embed></div>';
                } else {
                    preview = '<div class="mb-2 border rounded p-4 bg-light text-center"><i class="bi bi-file-earmark fs-1 text-secondary"></i><p class="small text-muted mb-0 mt-2">Preview not available — use Open to download.</p></div>';
                }
                $body.append(
                    '<div class="bank-recon-att-item mb-4 pb-4 border-bottom">' +
                    '<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">' +
                    '<span class="small text-break fw-semibold">' + escapeAttr(name) + '</span>' +
                    '<a href="' + escapeAttr(url) + '" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary shrink-0"><i class="bi bi-box-arrow-up-right"></i> Open</a>' +
                    '</div>' + preview + '</div>'
                );
            });
        }
        bankReconShowModal(document.getElementById('bankMatchAttachmentsViewerModal'));
    });

    function doMatchWithBill(billId, billAmount, $btn) {
        openBankMatchDetailsModal(billId, billAmount, $btn);
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
        openBankMatchDetailsModal(firstBill.id, firstBill.amount, null);
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
    // DASHBOARD STATS (from list response or stats_only AJAX)
    // ============================================
    function applyDashboardStats(payload) {
        if (!payload || payload.total === undefined) {
            return;
        }
        $('#totalStatements').text(payload.total || 0);
        $('#matchedStatements').text(payload.matched || 0);
        $('#unmatchedStatements').text(payload.unmatched || 0);

        const totalAmount = payload.total_amount || 0;
        $('#totalAmount').text('₹' + formatNumber(totalAmount));

        $('#incomeMatchedCount').text(payload.income_matched || 0);
        $('#incomeUnmatchedCount').text(payload.income_unmatched || 0);

        $('#radiantMatchedCount').text(payload.radiant_matched || 0);
        $('#radiantKeywordOnlyCount').text(payload.radiant_keyword_only || 0);
        $('#radiantUnmatchedCount').text(payload.radiant_unmatched || 0);
    }

    /** Use only when the table was not just loaded (e.g. after match/unmatch without reload). */
    function updateStatistics() {
        var statParams = $.extend({ stats_only: true }, currentFilters);
        Object.keys(statParams).forEach(function (k) {
            if (statParams[k] === '' || statParams[k] === undefined || statParams[k] === null) {
                delete statParams[k];
            }
        });
        $.ajax({
            url: routes.statements,
            type: 'GET',
            data: statParams,
            success: function(response) {
                applyDashboardStats(response);
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

    /** Parsed multi-date income tag payload from bank_statements.income_match_split_json */
    function parseIncomeMatchSplitJson(stmt) {
        var raw = stmt.income_match_split_json;
        if (raw == null || raw === '') {
            return null;
        }
        if (typeof raw === 'object' && raw !== null && !Array.isArray(raw)) {
            return raw;
        }
        if (typeof raw === 'string') {
            try {
                return JSON.parse(raw);
            } catch (e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Collection line(s): single date → "DD/MM/YYYY — ₹amount".
     * Split tag → one line per date with amount; tag time appended on the last line only.
     */
    function buildIncomeTaggedDateAmountRowsHtml(stmt) {
        var tagRaw = stmt.income_matched_at;
        var tagLine = tagRaw ? formatDateTime(tagRaw) : '';
        var w = parseFloat(stmt.withdrawal) || 0;
        var d = parseFloat(stmt.deposit) || 0;
        var lineAmt = w > 0 ? w : d;

        var split = parseIncomeMatchSplitJson(stmt);
        var ymds = split && Array.isArray(split.dates_ymd) ? split.dates_ymd.slice().sort() : [];
        var amts = split && split.amounts_ymd ? split.amounts_ymd : null;

        if (ymds.length > 1 && amts) {
            var html = '';
            ymds.forEach(function (ymd, idx) {
                var m = moment(ymd, 'YYYY-MM-DD', true);
                var dmy = m.isValid() ? m.format('DD/MM/YYYY') : String(ymd);
                var part = parseFloat(amts[ymd]);
                if (!isFinite(part)) {
                    part = 0;
                }
                var isLast = idx === ymds.length - 1;
                var timeSuffix = (isLast && tagLine)
                    ? ' <span class="text-muted fw-normal">· ' + escapeAttr(tagLine) + '</span>'
                    : '';
                html += '<small class="text-muted d-block">' +'DOC: '+ 
                    escapeAttr(dmy) + ' — ₹' + formatNumber(part)  + '</small>'+
                    '<small class="text-muted d-block">'+timeSuffix+'</small>';
            });
            return html;
        }

        var coll = (stmt.income_matched_date || '').toString().trim() || '—';
        return '<small class="text-muted d-block">' +'DOC: '+
            escapeAttr(coll) + ' — ₹' + formatNumber(lineAmt) + '</small>';
    }
    
    function truncateText(text, length) {
        if (!text) return '-';
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
    
    function getMatchStatusBadge(stmt) {
        var status = stmt.match_status;
        const badges = {
            'unmatched': '<span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Unmatched</span>',
            'matched': '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Matched</span>',
            'partially_matched': '<span class="badge bg-info"><i class="bi bi-dash-circle me-1"></i>Partial</span>'
        };
        if (status === 'matched' || status === 'partially_matched') {
            return badges[status];
        }
        if (stmt.income_match_status === 'income_matched') {
            return '<span class="badge bg-info text-dark"><i class="bi bi-arrow-left-right me-1"></i>Income matched</span>';
        }
        return badges['unmatched'];
    }

    function getCategoryBadge(stmt) {
        var billCategorized = stmt.match_status && stmt.match_status !== 'unmatched';
        var incomeCategorized = stmt.income_match_status === 'income_matched';
        if (billCategorized || incomeCategorized) {
            return '<span class="category-badge categorized"><i class="bi bi-check-circle me-1"></i>Categorized</span>';
        }
        return '<span class="category-badge uncategorized"><i class="bi bi-question-circle me-1"></i>Uncategorized</span>';
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

    function resetIncomeTagDateToToday() {
        if (incomeTagFp) {
            incomeTagFp.setDate([new Date()], true);
            rebuildIncomeTagSplitRows();
        }
    }

    /** Sorted Y-m-d list from flatpickr (multi-date). */
    function getIncomeTagSelectedYmdSorted() {
        if (!incomeTagFp || !incomeTagFp.selectedDates || !incomeTagFp.selectedDates.length) {
            return [];
        }
        var out = incomeTagFp.selectedDates.map(function (d) {
            return moment(d).format('YYYY-MM-DD');
        });
        out.sort();
        return out;
    }

    /** When 2+ collection dates: show per-date bank split (defaults to equal parts of this line). */
    function rebuildIncomeTagSplitRows() {
        var $wrap = $('#incomeTagDateSplitWrap');
        if (!$wrap.length) {
            return;
        }
        var dates = getIncomeTagSelectedYmdSorted();
        if (dates.length < 2) {
            $wrap.hide().empty();
            return;
        }
        var total = parseFloat(currentTxnData.amount);
        if (!isFinite(total) || total <= 0) {
            total = 0;
        }
        var n = dates.length;
        var each = n ? Math.round((total / n) * 100) / 100 : 0;
        var html = '<p class="small text-muted mb-1">Split this bank line across dates (sum must equal ₹' + formatNumber(total) + '). Edit if needed.</p>';
        html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Date</th><th>Amount (₹)</th></tr></thead><tbody>';
        var sumFirst = 0;
        dates.forEach(function (ymd, i) {
            var amt = (i === n - 1) ? Math.round((total - sumFirst) * 100) / 100 : each;
            if (i < n - 1) {
                sumFirst += each;
            }
            html += '<tr><td class="small">' + moment(ymd, 'YYYY-MM-DD').format('DD/MM/YYYY') + '</td><td>';
            html += '<input type="number" step="0.01" min="0" class="form-control form-control-sm income-tag-split-amt" data-ymd="' + ymd + '" value="' + amt + '">';
            html += '</td></tr>';
        });
        html += '</tbody></table></div>';
        $wrap.html(html).show();
    }

    function collectIncomeTagDateAmountsMap() {
        var map = {};
        $('.income-tag-split-amt').each(function () {
            var ymd = $(this).data('ymd');
            if (ymd) {
                map[ymd] = parseFloat($(this).val()) || 0;
            }
        });
        return map;
    }

    // ---- Initialize when modal opens (deposit: income tag + radiant; withdrawal: bill match only) ----
    $('#matchTransactionModal').on('shown.bs.modal', function () {
        if (bankReconModalTxnMode === 'deposit') {
            loadIncomeTagZones();
            initIncomeTagFlatpickr();
            resetIncomeTagDateToToday();
            rebuildIncomeTagSplitRows();
            updateIncomeTagSummary();

            if (currentTxnData.description) {
                autoResolveIncomeTagFromDescription(currentTxnData.description, currentTxnData.date);
            }
        }
    });

    // ---- Reset any inline styles on zone/branch (e.g. legacy lock) ----
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
        $('#incomeTagDateSplitWrap').hide().empty();
        $('.income-tag-mode-btn').removeClass('selected');
        unlockIncomeTagSelects();
        updateIncomeTagSummary();
        resetMatchModalTabsVisibility();
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
                // Date of collection defaults to today (set on modal open); do not override from API/txn.

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
                resetIncomeTagDateToToday();
                updateIncomeTagSummary();
            }
        });
    }

    function initIncomeTagFlatpickr() {
        var el = document.getElementById('incomeTagDate');
        if (!el || el._flatpickr) return;
        incomeTagFp = flatpickr(el, {
            mode: 'multiple',
            dateFormat: 'd/m/Y',
            maxDate: 'today',
            defaultDate: [new Date()],
            allowInput: true,
            clickOpens: true,
            onChange: function () {
                rebuildIncomeTagSplitRows();
                updateIncomeTagSummary();
            }
        });
        $(el).prop('readonly', false).attr({
            autocomplete: 'off',
            autocorrect: 'off',
            spellcheck: 'false'
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
            unlockIncomeTagSelects();
            resetIncomeTagDateToToday();
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
        var dateStr = '';
        if (incomeTagFp && incomeTagFp.selectedDates && incomeTagFp.selectedDates.length) {
            dateStr = getIncomeTagSelectedYmdSorted().map(function (ymd) {
                return moment(ymd, 'YYYY-MM-DD').format('DD/MM/YYYY');
            }).join(', ');
        }
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

    $(document).on('input change', '.income-tag-split-amt', function () {
        updateIncomeTagSummary();
    });

    $(document).on('click', '#applyIncomeTagBtn', function () {
        if (incomeTagInFlight) return;

        var zoneName   = $('#incomeTagZoneName').val();
        var branchName = $('#incomeTagBranchName').val();
        var modes      = Array.from(incomeTagSelectedModes);

        var dates = getIncomeTagSelectedYmdSorted();
        if (!dates.length && currentTxnData.date) {
            dates = [moment(currentTxnData.date).format('YYYY-MM-DD')];
        }

        if (!zoneName)           { toastr.warning('Please select a Zone'); return; }
        if (!branchName)         { toastr.warning('Please select a Branch'); return; }
        if (!dates.length)       { toastr.warning('Select at least one collection date'); return; }
        if (!modes.length)       { toastr.warning('Please select at least one Mode of Collection'); return; }
        if (!currentStatementId) { toastr.warning('No bank statement selected'); return; }

        var postData = {
            _token:            $('meta[name="csrf-token"]').attr('content'),
            bank_statement_id: currentStatementId,
            zone:              zoneName,
            branch:            branchName,
            dates:             dates,
            modes:             modes
        };

        if (dates.length > 1) {
            postData.date_amounts = collectIncomeTagDateAmountsMap();
            var lineTotal = parseFloat(currentTxnData.amount) || 0;
            var splitSum = 0;
            dates.forEach(function (d) {
                splitSum += parseFloat(postData.date_amounts[d]) || 0;
            });
            if (Math.abs(splitSum - lineTotal) > 0.05) {
                toastr.warning('Per-date amounts must sum to the bank line (₹' + formatNumber(lineTotal) + '). Current sum: ₹' + formatNumber(splitSum));
                return;
            }
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Applying...');
        incomeTagInFlight = true;

        $.ajax({
            url:  routes.incomeTag,
            type: 'POST',
            data: postData
        }).done(function (r) {
            if (r && r.success) {
                toastr.success(r.message || 'Income tag applied');
                $('#matchTransactionModal').modal('hide');
                loadStatements(currentPage);
                updateStatistics();
            } else {
                toastr.error((r && r.message) ? r.message : 'Income tag failed');
            }
        }).fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error applying income tag';
            toastr.error(msg);
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="bi bi-tag me-1"></i>Apply Income Tag');
            incomeTagInFlight = false;
        });
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
