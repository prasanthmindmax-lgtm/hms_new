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
    var routes = typeof window.bankReconRoutes !== 'undefined' ? window.bankReconRoutes : {};
    var matchAttachmentTypesAdminRows = [];

    function bankReconStatementColCount() {
        return (typeof window.bankReconSuperAdmin !== 'undefined' && window.bankReconSuperAdmin) ? 19 : 18;
    }

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
        if ($('#qfBtn-salaryTag').length) {
            qfRegister('qfSalaryTag', 'qfBtn-salaryTag', 'qfMenu-salaryTag', 'All');
            (function () {
                var regSal = _qfReg['qfSalaryTag'];
                if (!regSal) {
                    return;
                }
                regSal.$menu.find('.qf-options-inner input[type="checkbox"]').on('change.brSalExclusive', function () {
                    var $t = $(this);
                    if ($t.is(':checked')) {
                        regSal.$menu.find('.qf-options-inner input[type="checkbox"]').not($t).prop('checked', false);
                    }
                    var vals = [];
                    regSal.$menu.find('.qf-options-inner input[type="checkbox"]:checked').each(function () {
                        vals.push($(this).val());
                    });
                    regSal.$select.val(vals.length ? vals : null);
                    regSal.$menu.find('.qf-all-chk').prop('checked', vals.length === 0);
                    _qfSync(regSal);
                });
            })();
        }
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
        var salaryCo = $('#salaryUploadCompany').length
            ? ($('#salaryUploadCompany').val() || '').toString().trim()
            : '';
        var savedSalaryAcc = $('#salaryUploadBankAccount').length ? $('#salaryUploadBankAccount').val() : null;

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

        if ($('#salaryUploadBankAccount').length) {
            replaceBankAccountSelectOptions(
                $('#salaryUploadBankAccount'),
                filterAccountsByCompanyId(lastBankAccountsFullList, salaryCo),
                salaryCo ? 'Select account…' : 'Select company first…',
                savedSalaryAcc
            );
            $('#salaryUploadBankAccount').prop('disabled', !salaryCo);
        }
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
                newCo: $('#newAccCompanyId').val(),
                salaryCo: $('#salaryUploadCompany').length ? $('#salaryUploadCompany').val() : null
            };

            var coOptions = (lastCompaniesList || []).map(function (c) {
                return { value: c.id, label: c.company_name || '' };
            });
            var coMandatory = companyListOptionsHtml(false);
            var coAll = companyListOptionsHtml(true, 'All companies');
            $('#newAccCompanyId').html(coMandatory);
            $('#mainUploadCompany').html(coMandatory);
            $('#modalUploadCompany').html(coMandatory);
            if ($('#salaryUploadCompany').length) {
                $('#salaryUploadCompany').html(coMandatory);
            }
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
            if (preserve.salaryCo) {
                $('#salaryUploadCompany').val(preserve.salaryCo);
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

        if (window.bankReconQfIncomeCollectionFp && window.bankReconQfIncomeCollectionFp.selectedDates
            && window.bankReconQfIncomeCollectionFp.selectedDates.length) {
            f.income_collection_dates = window.bankReconQfIncomeCollectionFp.selectedDates.map(function (d) {
                return moment(d).format('YYYY-MM-DD');
            });
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

        var salUtr = $('#qfSalaryTag').length ? ($('#qfSalaryTag').val() || []) : [];
        if (salUtr.length) {
            if (salUtr.indexOf('tagged') >= 0 && salUtr.indexOf('not_tagged') >= 0) {
                f.salary_tag = 'tagged';
            } else {
                f.salary_tag = salUtr[0];
            }
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

    $(document).on('change', '#salaryUploadCompany', function () {
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

    function resetMatchAttachmentTypeForm() {
        $('#matchAttTypeEditId').val('');
        $('#matchAttTypeName').val('');
        $('#matchAttTypeContext').val('both');
        $('#matchAttTypeSort').val('0');
        $('#matchAttTypeSample').val('');
        $('#btnSaveMatchAttachmentTypeLabel').text('Add');
        $('#btnCancelMatchAttachmentTypeEdit').hide();
        $('#matchAttTypeFormHint').html(
            '<i class="bi bi-plus-circle me-1"></i>Add a label; optionally attach a sample file (template or example).'
        );
    }

    function refreshMatchAttachmentTypesAdmin() {
        if (!routes.matchAttachmentTypes) {
            return;
        }
        var $tb = $('#matchAttachmentTypesTableBody');
        if (!$tb.length) {
            return;
        }
        $tb.html('<tr><td colspan="6" class="text-center text-muted py-3">Loading…</td></tr>');
        $.getJSON(routes.matchAttachmentTypes + '?admin=1')
            .done(function (rows) {
                matchAttachmentTypesAdminRows = Array.isArray(rows) ? rows : [];
                $tb.empty();
                if (!matchAttachmentTypesAdminRows.length) {
                    $tb.html(
                        '<tr><td colspan="6" class="text-center text-muted py-3">No types yet. Add one above.</td></tr>'
                    );
                    return;
                }
                matchAttachmentTypesAdminRows.forEach(function (r) {
                    var sample = r.sample_url
                        ? '<a href="' +
                          escapeAttr(String(r.sample_url)) +
                          '" target="_blank" rel="noopener">View</a>'
                        : '—';
                    var ctx = (r.match_context || 'both').toString().toLowerCase();
                    var ctxBadge =
                        ctx === 'bill'
                            ? '<span class="badge bg-primary">Bill</span>'
                            : ctx === 'income'
                              ? '<span class="badge bg-info text-dark">Income</span>'
                              : '<span class="badge bg-secondary">Both</span>';
                    var actBadge = r.is_active
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-secondary">No</span>';
                    var toggleBtn = r.is_active
                        ? '<button type="button" class="btn btn-link btn-sm p-0 br-mat-toggle-active" data-id="' +
                          r.id +
                          '" data-next-active="0">Deactivate</button>'
                        : '<button type="button" class="btn btn-link btn-sm p-0 br-mat-toggle-active" data-id="' +
                          r.id +
                          '" data-next-active="1">Activate</button>';
                    $tb.append(
                        '<tr data-type-id="' +
                            r.id +
                            '">' +
                            '<td class="small fw-semibold">' +
                            escapeAttr(String(r.name || '')) +
                            '</td>' +
                            '<td class="small">' +
                            ctxBadge +
                            '</td>' +
                            '<td class="text-end small">' +
                            String(r.sort_order != null ? r.sort_order : '') +
                            '</td>' +
                            '<td class="small">' +
                            actBadge +
                            ' ' +
                            toggleBtn +
                            '</td>' +
                            '<td class="small">' +
                            sample +
                            '</td>' +
                            '<td class="text-end">' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary br-mat-edit-type me-1" data-id="' +
                            r.id +
                            '"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger br-mat-del-type" data-id="' +
                            r.id +
                            '"><i class="bi bi-trash"></i></button>' +
                            '</td></tr>'
                    );
                });
            })
            .fail(function () {
                $tb.html(
                    '<tr><td colspan="6" class="text-center text-danger py-3">Could not load types.</td></tr>'
                );
            });
    }

    $('#accountDetailsModal button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target && e.target.id === 'tabBtnAttachmentTypes') {
            refreshMatchAttachmentTypesAdmin();
        }
    });

    $('#btnRefreshMatchAttachmentTypes').on('click', function () {
        refreshMatchAttachmentTypesAdmin();
    });

    $('#btnCancelMatchAttachmentTypeEdit').on('click', function () {
        resetMatchAttachmentTypeForm();
    });

    $('#formMatchAttachmentType').on('submit', function (e) {
        e.preventDefault();
        if (!routes.matchAttachmentTypesStore) {
            return;
        }
        var editId = ($('#matchAttTypeEditId').val() || '').trim();
        var fd = new FormData();
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        fd.append('name', ($('#matchAttTypeName').val() || '').trim());
        fd.append('match_context', ($('#matchAttTypeContext').val() || 'both').toString());
        fd.append('sort_order', $('#matchAttTypeSort').val() || '0');
        var sampleEl = document.getElementById('matchAttTypeSample');
        if (sampleEl && sampleEl.files && sampleEl.files[0]) {
            fd.append('sample_file', sampleEl.files[0]);
        }
        var url = routes.matchAttachmentTypesStore;
        if (editId) {
            url = routes.matchAttachmentTypesUpdateBase + '/' + encodeURIComponent(editId);
        }
        var $btn = $('#btnSaveMatchAttachmentType');
        $btn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res && res.success) {
                    toastr.success(res.message || 'Saved');
                    resetMatchAttachmentTypeForm();
                    refreshMatchAttachmentTypesAdmin();
                    loadBankReconAttachmentTypes(null, 'bill');
                    loadBankReconAttachmentTypes(null, 'income');
                } else {
                    toastr.error((res && res.message) || 'Save failed');
                }
            },
            error: function (xhr) {
                var msg = 'Save failed';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join(' ');
                    }
                }
                toastr.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.br-mat-toggle-active', function () {
        var id = $(this).data('id');
        var next = $(this).data('next-active');
        if (!routes.matchAttachmentTypesUpdateBase) {
            return;
        }
        $.post(
            routes.matchAttachmentTypesUpdateBase + '/' + encodeURIComponent(id),
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                is_active: next ? 1 : 0
            },
            function (res) {
                if (res && res.success) {
                    toastr.success('Updated');
                    refreshMatchAttachmentTypesAdmin();
                    loadBankReconAttachmentTypes(null, 'bill');
                    loadBankReconAttachmentTypes(null, 'income');
                } else {
                    toastr.error((res && res.message) || 'Failed');
                }
            }
        ).fail(function (xhr) {
            var msg =
                xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Update failed';
            toastr.error(msg);
        });
    });

    $(document).on('click', '.br-mat-edit-type', function () {
        var id = parseInt($(this).data('id'), 10);
        var row = matchAttachmentTypesAdminRows.filter(function (x) {
            return Number(x.id) === id;
        })[0];
        if (!row) {
            return;
        }
        $('#matchAttTypeEditId').val(String(row.id));
        $('#matchAttTypeName').val(row.name || '');
        $('#matchAttTypeContext').val(row.match_context || 'both');
        $('#matchAttTypeSort').val(row.sort_order != null ? row.sort_order : 0);
        $('#matchAttTypeSample').val('');
        $('#btnSaveMatchAttachmentTypeLabel').text('Save');
        $('#btnCancelMatchAttachmentTypeEdit').show();
        $('#matchAttTypeFormHint').text(
            'Editing “' + (row.name || '') + '”. Leave sample empty to keep the current file.'
        );
    });

    $(document).on('click', '.br-mat-del-type', function () {
        var rawId = $(this).data('id');
        if (!routes.matchAttachmentTypesDestroy) {
            return;
        }
        if (!window.confirm('Delete this document type?')) {
            return;
        }
        var id = String(rawId);
        var url = routes.matchAttachmentTypesDestroy.replace(':id', id);
        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res && res.success) {
                    toastr.success('Deleted');
                    refreshMatchAttachmentTypesAdmin();
                    loadBankReconAttachmentTypes(null, 'bill');
                    loadBankReconAttachmentTypes(null, 'income');
                } else {
                    toastr.error((res && res.message) || 'Delete failed');
                }
            },
            error: function (xhr) {
                var msg =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Delete failed';
                toastr.error(msg);
            }
        });
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
        if (typeof window.bankReconInitQfIncomeCollectionFlatpickr === 'function') {
            window.bankReconInitQfIncomeCollectionFlatpickr();
        }
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

        if (window.bankReconQfIncomeCollectionFp) {
            window.bankReconQfIncomeCollectionFp.clear();
        }

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
    function showTableSkeleton() {
        var cols = bankReconStatementColCount();
        var widths = [40, 60, 80, 50, 65, 35, 35, 45, 55, 50, 70, 55, 50, 60, 50, 48, 52, 45, 50, 45, 45];
        if (cols === 18) {
            widths = widths.slice(0, 18);
        } else if (cols === 19) {
            widths = widths.slice(0, 19);
        }
        var html = '';
        for (var r = 0; r < 8; r++) {
            html += '<tr class="br-loading-row">';
            for (var c = 0; c < cols; c++) {
                var w = widths[c] || 50;
                /* stagger animation delay for a wave effect */
                var delay = ((r * 0.08) + (c * 0.04)).toFixed(2);
                html += '<td><span class="br-skel-cell" style="width:' + w + '%;animation-delay:' + delay + 's;"></span></td>';
            }
            html += '</tr>';
        }
        $('#statementsTableBody').html(html);
    }

    function loadStatements(page = 1) {
        currentPage = page;
        
        const params = {
            page: page,
            per_page: perPage,
            ...currentFilters
        };

        showTableSkeleton();
        
        $.ajax({
            url: routes.statements,
            type: 'GET',
            data: params,
            success: function(response) {
                function applyResponse() {
                    renderStatementsTable(response.data);
                    renderPagination(response);
                    if (response.dashboard) {
                        applyDashboardStats(response.dashboard);
                    } else {
                        applyDashboardStats(response);
                    }
                }
                if (
                    routes.matchAttachmentTypes &&
                    (!bankReconMatchAttachmentTypesIncome || !bankReconMatchAttachmentTypesIncome.length)
                ) {
                    loadBankReconAttachmentTypes(applyResponse, 'income');
                } else {
                    applyResponse();
                }
            },
            error: function(xhr) {
                console.error('Error loading statements:', xhr);
                toastr.error('Failed to load statements');
                $('#statementsTableBody').html(
                    '<tr><td colspan="' + bankReconStatementColCount() + '" class="text-center py-5 text-danger">' +
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i>Failed to load statements</td></tr>'
                );
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

    /** Staged files before bill match: { id, file, tagTypeId, otherNote, url } */
    var bankMatchStagingFiles = [];

    /** Staged files before income tag (same shape) */
    var incomeTagStagingFiles = [];

    /** Document types: bill match vs income tag (from /bank-reconciliation/match-attachment-types?scope=) */
    var bankReconMatchAttachmentTypesBill = [];
    var bankReconMatchAttachmentTypesIncome = [];

    function loadBankReconAttachmentTypes(done, scope) {
        if (typeof routes === 'undefined' || !routes.matchAttachmentTypes) {
            bankReconMatchAttachmentTypesBill = [];
            bankReconMatchAttachmentTypesIncome = [];
            if (typeof done === 'function') {
                done();
            }
            return;
        }
        var params = {};
        if (scope === 'bill' || scope === 'income') {
            params.scope = scope;
        }
        $.ajax({
            url: routes.matchAttachmentTypes,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function (data) {
                var arr = Array.isArray(data) ? data : [];
                if (scope === 'income') {
                    bankReconMatchAttachmentTypesIncome = arr;
                } else if (scope === 'bill') {
                    bankReconMatchAttachmentTypesBill = arr;
                } else {
                    bankReconMatchAttachmentTypesBill = arr;
                    bankReconMatchAttachmentTypesIncome = arr;
                }
                if (typeof done === 'function') {
                    done();
                }
            },
            error: function () {
                if (scope === 'income') {
                    bankReconMatchAttachmentTypesIncome = [];
                } else if (scope === 'bill') {
                    bankReconMatchAttachmentTypesBill = [];
                } else {
                    bankReconMatchAttachmentTypesBill = [];
                    bankReconMatchAttachmentTypesIncome = [];
                }
                if (typeof done === 'function') {
                    done();
                }
            }
        });
    }

    function bankMatchDefaultTagTypeId() {
        var t = bankReconMatchAttachmentTypesBill;
        if (!t || !t.length) {
            return null;
        }
        return t[0].id;
    }

    function incomeTagDefaultTagTypeId() {
        var t = bankReconMatchAttachmentTypesIncome;
        if (!t || !t.length) {
            return null;
        }
        var m = t.filter(function (o) {
            return incomeAttachmentSlotFromTypeName(o.name) === 'mocdoc';
        })[0];
        return m ? m.id : t[0].id;
    }

    /** Cash as the only selected mode → MOCDOC + Radiant + ledger; otherwise MOCDOC only. */
    function incomeTagRequiresCashFullAttachmentPack() {
        return incomeTagSelectedModes.size === 1 && incomeTagSelectedModes.has('cash');
    }

    function incomeStagingHasMandatorySlots() {
        var fullPack = incomeTagRequiresCashFullAttachmentPack();
        var slots = {};
        incomeTagStagingFiles.forEach(function (it) {
            if (!it || !it.file) {
                return;
            }
            var slot = null;
            if (it.tagTypeId != null && it.tagTypeId !== '' && bankReconMatchAttachmentTypesIncome.length) {
                var row = bankReconMatchAttachmentTypesIncome.filter(function (x) {
                    return String(x.id) === String(it.tagTypeId);
                })[0];
                if (row) {
                    slot = incomeAttachmentSlotFromTypeName(row.name);
                }
            }
            if (!slot) {
                slot = incomeAttachmentSlotFromTypeName(bankMatchStagingTagLabel(it, bankReconMatchAttachmentTypesIncome));
            }
            if (slot) {
                slots[slot] = true;
            }
        });
        if (!slots.mocdoc) {
            return false;
        }
        if (fullPack) {
            return !!(slots.radiant && slots.ledger);
        }
        return true;
    }

    function bankMatchTypeIsOtherById(tagTypeId, typesList) {
        var list = typesList || bankReconMatchAttachmentTypesBill;
        var row = list.filter(function (x) {
            return String(x.id) === String(tagTypeId);
        })[0];
        return row && (row.name || '').trim().toLowerCase() === 'other';
    }

    function bankMatchIsImageName(name) {
        var ext = String(name || '').split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].indexOf(ext) !== -1;
    }

    function bankMatchIsPdfName(name) {
        return String(name || '').split('.').pop().toLowerCase() === 'pdf';
    }

    function clearBankMatchAttachmentStaging() {
        bankMatchStagingFiles.forEach(function (it) {
            if (it.url) {
                try {
                    URL.revokeObjectURL(it.url);
                } catch (eRev) { /* ignore */ }
            }
        });
        bankMatchStagingFiles = [];
        var fin = document.getElementById('bankMatchAttachmentsInput');
        if (fin) {
            fin.value = '';
        }
        $('#bankMatchAttachmentStaging').empty();
    }

    function bankMatchStagingTagLabel(it, typesList) {
        var list = typesList || bankReconMatchAttachmentTypesBill;
        var row = list.filter(function (x) {
            return String(x.id) === String(it.tagTypeId);
        })[0];
        var name = row ? (row.name || '').trim() : '';
        if (name.toLowerCase() === 'other') {
            var n = (it.otherNote || '').trim();
            return n ? ('Other: ' + n) : 'Other';
        }
        if (name) {
            return name;
        }
        return 'Unspecified';
    }

    function renderBankMatchAttachmentStaging() {
        var $box = $('#bankMatchAttachmentStaging');
        $box.empty();
        if (!bankMatchStagingFiles.length) {
            $box.html('<p class="text-muted small mb-0">No files yet — at least one attachment is <strong>required</strong> to match. Use <strong>Add files</strong> above.</p>');
            return;
        }
        bankMatchStagingFiles.forEach(function (it) {
            var $row = $('<div class="bank-match-att-staging-item mb-2 d-flex flex-wrap align-items-start gap-2"></div>')
                .attr('data-staging-id', it.id);

            var $prev = $('<div class="bank-match-att-staging-preview"></div>');
            if (bankMatchIsImageName(it.file.name)) {
                $prev.append($('<img alt="">').attr('src', it.url));
            } else if (bankMatchIsPdfName(it.file.name)) {
                $prev.html('<span class="text-danger fs-2"><i class="bi bi-file-earmark-pdf"></i></span>');
            } else {
                $prev.html('<span class="text-secondary fs-2"><i class="bi bi-file-earmark"></i></span>');
            }

            var $sel = $('<select class="form-select form-select-sm bank-match-att-tag-sel"></select>')
                .attr('data-staging-id', it.id);
            if (bankReconMatchAttachmentTypesBill.length) {
                bankReconMatchAttachmentTypesBill.forEach(function (o) {
                    $sel.append(
                        $('<option></option>')
                            .val(String(o.id))
                            .text(o.name || ('#' + o.id))
                            .prop('selected', String(it.tagTypeId) === String(o.id))
                    );
                });
            } else {
                $sel.append(
                    $('<option></option>')
                        .val('')
                        .text('Add “Bill” or “Both” types under Bank Accounts → Attachment types')
                );
            }

            var showOther = bankMatchTypeIsOtherById(it.tagTypeId, bankReconMatchAttachmentTypesBill);
            var $other = $('<input type="text" class="form-control form-control-sm bank-match-att-other mt-1" maxlength="160" placeholder="Describe this document"/>')
                .attr('data-staging-id', it.id)
                .val(it.otherNote || '')
                .css('display', showOther ? '' : 'none');

            var $meta = $('<div class="bank-match-att-staging-meta flex-grow-1"></div>');
            $meta.append($('<div class="small text-break fw-semibold mb-1"></div>').text(it.file.name));
            $meta.append($('<label class="form-label small text-muted mb-0">Document type</label>'));
            $meta.append($sel);
            $meta.append($other);

            var $rm = $('<button type="button" class="btn btn-sm btn-outline-danger shrink-0 bank-match-att-remove" title="Remove"></button>')
                .attr('data-staging-id', it.id)
                .html('<i class="bi bi-trash"></i>');

            $row.append($prev, $meta, $rm);
            $box.append($row);
        });
    }

    function clearIncomeTagAttachmentStaging() {
        incomeTagStagingFiles.forEach(function (it) {
            if (it.url) {
                try {
                    URL.revokeObjectURL(it.url);
                } catch (eRev) { /* ignore */ }
            }
        });
        incomeTagStagingFiles = [];
        var fin = document.getElementById('incomeTagAttachmentsInput');
        if (fin) {
            fin.value = '';
        }
        $('#incomeTagAttachmentStaging').empty();
    }

    function renderIncomeTagAttachmentStaging() {
        var $box = $('#incomeTagAttachmentStaging');
        $box.empty();
        if (!incomeTagStagingFiles.length) {
            $box.html(
                '<p class="text-muted small mb-0">Add <strong>three</strong> files (MOCDOC COLLECTION SCREEN SHOT, RADIANT SLIP, COLLECTION LEDGER) and set each document type. Types must exist under <em>Bank Accounts → Attachment types</em> (Income or Both).</p>'
            );
            return;
        }
        incomeTagStagingFiles.forEach(function (it) {
            var $row = $('<div class="bank-match-att-staging-item mb-2 d-flex flex-wrap align-items-start gap-2"></div>')
                .attr('data-staging-id', it.id)
                .attr('data-income-staging', '1');

            var $prev = $('<div class="bank-match-att-staging-preview"></div>');
            if (bankMatchIsImageName(it.file.name)) {
                $prev.append($('<img alt="">').attr('src', it.url));
            } else if (bankMatchIsPdfName(it.file.name)) {
                $prev.html('<span class="text-danger fs-2"><i class="bi bi-file-earmark-pdf"></i></span>');
            } else {
                $prev.html('<span class="text-secondary fs-2"><i class="bi bi-file-earmark"></i></span>');
            }

            var $sel = $('<select class="form-select form-select-sm bank-match-att-tag-sel"></select>')
                .attr('data-staging-id', it.id)
                .attr('data-income-staging', '1');
            if (bankReconMatchAttachmentTypesIncome.length) {
                bankReconMatchAttachmentTypesIncome.forEach(function (o) {
                    $sel.append(
                        $('<option></option>')
                            .val(String(o.id))
                            .text(o.name || ('#' + o.id))
                            .prop('selected', String(it.tagTypeId) === String(o.id))
                    );
                });
            } else {
                $sel.append(
                    $('<option></option>')
                        .val('')
                        .text('Add “Income” or “Both” types under Bank Accounts → Attachment types')
                );
            }

            var showOther = bankMatchTypeIsOtherById(it.tagTypeId, bankReconMatchAttachmentTypesIncome);
            var $other = $('<input type="text" class="form-control form-control-sm bank-match-att-other mt-1" maxlength="160" placeholder="Describe this document"/>')
                .attr('data-staging-id', it.id)
                .attr('data-income-staging', '1')
                .val(it.otherNote || '')
                .css('display', showOther ? '' : 'none');

            var $meta = $('<div class="bank-match-att-staging-meta flex-grow-1"></div>');
            $meta.append($('<div class="small text-break fw-semibold mb-1"></div>').text(it.file.name));
            $meta.append($('<label class="form-label small text-muted mb-0">Document type</label>'));
            $meta.append($sel);
            $meta.append($other);

            var $rm = $('<button type="button" class="btn btn-sm btn-outline-danger shrink-0 bank-match-att-remove" title="Remove"></button>')
                .attr('data-staging-id', it.id)
                .attr('data-income-staging', '1')
                .html('<i class="bi bi-trash"></i>');

            $row.append($prev, $meta, $rm);
            $box.append($row);
        });
    }

    $(document).on('change', '#bankMatchAttachmentsInput', function () {
        var el = this;
        if (!el.files || !el.files.length) {
            return;
        }
        for (var i = 0; i < el.files.length; i++) {
            var file = el.files[i];
            bankMatchStagingFiles.push({
                id: 'bm_' + Date.now() + '_' + i + '_' + Math.random().toString(36).slice(2, 9),
                file: file,
                tagTypeId: bankMatchDefaultTagTypeId(),
                otherNote: '',
                url: URL.createObjectURL(file)
            });
        }
        el.value = '';
        renderBankMatchAttachmentStaging();
    });

    $(document).on('change', '#incomeTagAttachmentsInput', function () {
        var el = this;
        if (!el.files || !el.files.length) {
            return;
        }
        for (var i = 0; i < el.files.length; i++) {
            var file = el.files[i];
            incomeTagStagingFiles.push({
                id: 'it_' + Date.now() + '_' + i + '_' + Math.random().toString(36).slice(2, 9),
                file: file,
                tagTypeId: incomeTagDefaultTagTypeId(),
                otherNote: '',
                url: URL.createObjectURL(file)
            });
        }
        el.value = '';
        renderIncomeTagAttachmentStaging();
    });

    $(document).on('change', '.bank-match-att-tag-sel', function () {
        var id = $(this).attr('data-staging-id');
        var v = $(this).val();
        var isIncome = $(this).attr('data-income-staging') === '1';
        var arr = isIncome ? incomeTagStagingFiles : bankMatchStagingFiles;
        var types = isIncome ? bankReconMatchAttachmentTypesIncome : bankReconMatchAttachmentTypesBill;
        var it = arr.filter(function (x) {
            return x.id === id;
        })[0];
        if (!it) {
            return;
        }
        it.tagTypeId = v === '' ? null : parseInt(v, 10);
        var showO = bankMatchTypeIsOtherById(it.tagTypeId, types);
        $(this).closest('.bank-match-att-staging-item').find('.bank-match-att-other').css('display', showO ? '' : 'none');
    });

    $(document).on('input', '.bank-match-att-other', function () {
        var id = $(this).attr('data-staging-id');
        var isIncome = $(this).attr('data-income-staging') === '1';
        var arr = isIncome ? incomeTagStagingFiles : bankMatchStagingFiles;
        var it = arr.filter(function (x) {
            return x.id === id;
        })[0];
        if (it) {
            it.otherNote = $(this).val();
        }
    });

    $(document).on('click', '.bank-match-att-remove', function () {
        var id = $(this).attr('data-staging-id');
        var isIncome = $(this).attr('data-income-staging') === '1';
        var arr = isIncome ? incomeTagStagingFiles : bankMatchStagingFiles;
        var idx = -1;
        for (var j = 0; j < arr.length; j++) {
            if (arr[j].id === id) {
                idx = j;
                break;
            }
        }
        if (idx === -1) {
            return;
        }
        var removed = arr.splice(idx, 1)[0];
        if (removed && removed.url) {
            try {
                URL.revokeObjectURL(removed.url);
            } catch (e2) { /* ignore */ }
        }
        if (isIncome) {
            renderIncomeTagAttachmentStaging();
        } else {
            renderBankMatchAttachmentStaging();
        }
    });

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

    /** Same rules as App\Support\BankReconIncomeRequiredAttachments::slotForTypeName */
    function incomeAttachmentSlotFromTypeName(name) {
        var n = String(name || '')
            .toUpperCase()
            .replace(/\s+/g, '');
        if (!n) {
            return null;
        }
        if (n.indexOf('MOCDOC') !== -1 && (n.indexOf('SCREEN') !== -1 || n.indexOf('SHOT') !== -1)) {
            return 'mocdoc';
        }
        if (n.indexOf('RADIANT') !== -1) {
            return 'radiant';
        }
        if (n.indexOf('LEDGER') !== -1 && (n.indexOf('COLLECTION') !== -1 || n.indexOf('BRANCH') !== -1)) {
            return 'ledger';
        }
        return null;
    }

    function incomeAttachmentSlotForRow(f, typesIncome) {
        var list = typesIncome || bankReconMatchAttachmentTypesIncome || [];
        var tid = f && (f.tag_type_id != null || f.tagTypeId != null) ? f.tag_type_id || f.tagTypeId : null;
        if (tid != null && String(tid) !== '' && list.length) {
            var row = list.filter(function (x) {
                return String(x.id) === String(tid);
            })[0];
            if (row) {
                var s = incomeAttachmentSlotFromTypeName(row.name);
                if (s) {
                    return s;
                }
            }
        }
        return incomeAttachmentSlotFromTypeName((f && f.tag) || '');
    }

    function incomeFindFirstAttachmentForSlot(attList, slot, typesIncome) {
        if (!attList || !attList.length) {
            return null;
        }
        for (var i = 0; i < attList.length; i++) {
            if (incomeAttachmentSlotForRow(attList[i], typesIncome) === slot) {
                return attList[i];
            }
        }
        return null;
    }

    function buildIncomeNatureFilesCell(stmt, att) {
        var slots = [
            {
                slot: 'mocdoc',
                title: 'MOCDOC COLLECTION SCREEN SHOT',
                icon: 'bi-heart-pulse',
                cls: 'br-income-slot-mocdoc',
            },
            {
                slot: 'radiant',
                title: 'RADIANT SLIP',
                icon: 'bi-bank',
                cls: 'br-income-slot-radiant',
            },
            {
                slot: 'ledger',
                title: 'COLLECTION LEDGER',
                icon: 'bi-journal-text',
                cls: 'br-income-slot-ledger',
            },
        ];
        var typesIncome = bankReconMatchAttachmentTypesIncome || [];
        var html =
            '<div class="bank-recon-nature-files bank-recon-income-nature small">' +
            '<div class="br-income-nature-title">OP/IP/Pharmacy Income</div>' +
            '<div class="br-income-nature-icons d-flex flex-wrap align-items-center gap-1 mt-1">';
        slots.forEach(function (s) {
            var f = incomeFindFirstAttachmentForSlot(att, s.slot, typesIncome);
            var url = f ? bankReconResolveAttachmentUrl(f) : '#';
            var has = f && url && url !== '#';
            if (has) {
                html +=
                    '<a href="' +
                    escapeAttr(url) +
                    '" target="_blank" rel="noopener" class="br-income-slot-att ' +
                    s.cls +
                    '" title="' +
                    escapeAttr(s.title) +
                    '"><i class="bi ' +
                    s.icon +
                    '"></i></a>';
            } else {
                html +=
                    '<span class="br-income-slot-miss ' +
                    s.cls +
                    '" title="' +
                    escapeAttr(s.title + ' — not uploaded') +
                    '"><i class="bi ' +
                    s.icon +
                    '"></i></span>';
            }
        });
        html += '</div>';
        if (att.length) {
            html +=
                '<button type="button" class="btn btn-link btn-sm p-0 align-baseline bank-recon-att-view mt-1" data-stmt-id="' +
                String(stmt.id) +
                '" title="View all attachments"><i class="bi bi-paperclip"></i> ' +
                att.length +
                '</button>';
        }
        html += '</div>';
        return html;
    }

    function buildNatureFilesCell(stmt) {
        var att = parseStmtMatchAttachments(stmt);
        window.bankReconMatchFiles[stmt.id] = att;
        var incomeTagged = stmt.income_match_status === 'income_matched';
        if (incomeTagged) {
            return buildIncomeNatureFilesCell(stmt, att);
        }
        var names = (stmt.resolved_br_nature_account_names || '').toString().trim();
        var natureIds = (stmt.resolved_br_nature_account_ids || '').toString().trim();
        if (!names && !att.length) {
            return '<span class="text-muted">—</span>';
        }
        var html = '<div class="bank-recon-nature-files small">';
        if (names) {
            if (natureIds) {
                html += '<div class="text-muted"><a href="#" class="br-drill-nature br-link-drill" data-nature-ids="' + escapeAttr(natureIds) + '" data-label="' + escapeAttr(names) + '">' + escapeAttr(names) + '</a></div>';
            } else {
                html += '<div class="text-muted">' + escapeAttr(names) + '</div>';
            }
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

    /** MESPOS lines are excluded from “cash/CAM → Radiant-only” description routing. */
    function bankReconDescriptionLooksMespos(desc) {
        return /\bMESPOS\b/i.test(String(desc || ''));
    }

    /**
     * BY CASH / CAM / “cash” in narrative: after income is already tagged, open Radiant-only match modal;
     * if income is not tagged yet, use the normal deposit modal (Income tag + Radiant).
     */
    function bankReconDescriptionRadiantOnlyCashOrCam(desc) {
        if (bankReconDescriptionLooksMespos(desc)) {
            return false;
        }
        var s = String(desc || '');
        if (/\bBY\s+CASH\b/i.test(s)) {
            return true;
        }
        return /\bCASH\b/i.test(s) || /\bCAM\b/i.test(s);
    }

    /** Lowercased income-tag modes used to filter billing_list rows (aligned with PHP query). */
    function bankReconIncomeTagBillingModesLower(stmt) {
        if (stmt.income_tag_billing_modes && Array.isArray(stmt.income_tag_billing_modes) && stmt.income_tag_billing_modes.length) {
            return stmt.income_tag_billing_modes
                .map(function (m) {
                    return String(m).toLowerCase().trim();
                })
                .filter(Boolean);
        }
        var split = parseIncomeMatchSplitJson(stmt);
        if (split && Array.isArray(split.modes) && split.modes.length) {
            return split.modes
                .map(function (m) {
                    return String(m).toLowerCase().trim();
                })
                .filter(Boolean);
        }
        return [];
    }

    function bankReconPaymentTypeMatchesIncomeModes(paymentType, modesLower) {
        if (!modesLower || !modesLower.length) {
            return true;
        }
        var p = String(paymentType || '')
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .trim();
        var allowed = {};
        modesLower.forEach(function (m) {
            if (m === 'cash') {
                allowed.cash = true;
            }
            if (m === 'card') {
                allowed.card = true;
            }
            if (m === 'upi') {
                allowed.upi = true;
            }
            if (m === 'neft') {
                allowed.neft = true;
            }
            if (m === 'other') {
                allowed.cheque = true;
                allowed.dd = true;
                allowed.credit = true;
            }
        });
        if (!Object.keys(allowed).length) {
            return true;
        }
        if (p === 'cash') {
            return !!allowed.cash;
        }
        if (p === 'card') {
            return !!allowed.card;
        }
        if (p === 'upi') {
            return !!allowed.upi;
        }
        if (p === 'neft') {
            return !!allowed.neft;
        }
        if (p === 'cheque' || p === 'dd' || p === 'credit') {
            return !!(allowed.cheque || allowed.dd || allowed.credit);
        }
        return false;
    }

    function bankReconFilterIncomeBillingRows(rows, modesLower) {
        if (!rows || !rows.length) {
            return [];
        }
        if (!modesLower || !modesLower.length) {
            return rows.slice();
        }
        return rows.filter(function (r) {
            return bankReconPaymentTypeMatchesIncomeModes(r.paymenttype, modesLower);
        });
    }

    /** billing_list.billdate often YYYYMMDDHHMMSS — show compact d/m/Y for modal/table */
    function formatBillingListBillDateRaw(raw) {
        if (raw == null || raw === '') {
            return '—';
        }
        var s = String(raw).replace(/\D/g, '');
        if (s.length >= 8) {
            var y = s.slice(0, 4);
            var m = s.slice(4, 6);
            var d = s.slice(6, 8);
            return d + '/' + m + '/' + y;
        }
        return String(raw);
    }

    function buildIncomeBillingListCardHtml(stmt) {
        var allRows = stmt.income_billing_list;
        if (!allRows || !allRows.length) {
            return '';
        }
        var modesLower = bankReconIncomeTagBillingModesLower(stmt);
        var rows = bankReconFilterIncomeBillingRows(allRows, modesLower);
        if (!rows.length) {
            rows = allRows.slice();
            modesLower = [];
        }
        var rowSum = rows.reduce(function (s, x) {
            return s + (parseFloat(x.amount) || 0);
        }, 0);
        var serverTotal = stmt.income_billing_list_total != null ? parseFloat(stmt.income_billing_list_total) : NaN;
        var totalVal =
            modesLower.length && rows.length < allRows.length
                ? rowSum
                : isFinite(serverTotal)
                  ? serverTotal
                  : rowSum;
        window.bankReconIncomeBillingCache[stmt.id] = {
            rows: rows,
            total: totalVal,
            branch: (stmt.income_matched_branch || '').toString(),
        };
        var preview = '';
        rows.slice(0, 3).forEach(function (r) {
            preview +=
                '<div class="br-income-billing-line small text-break">' +
                '<span class="fw-semibold">' +
                escapeAttr(r.billno || '—') +
                '</span>' +
                ' <span class="text-muted">·</span> ' +
                '<span class="text-muted">' +
                escapeAttr(r.paymenttype || '') +
                '</span>' +
                ' <span class="text-muted">·</span> ' +
                '<span class="text-dark">₹' +
                formatNumber(r.amount != null ? r.amount : 0) +
                '</span>' +
                '</div>';
        });
        var more =
            rows.length > 3
                ? '<div class="text-muted small mt-1">+' + (rows.length - 3) + ' more in table…</div>'
                : '';
        var tot = formatNumber(totalVal);
        var amtLine = stmt.withdrawal > 0 ? stmt.withdrawal : stmt.deposit;
        var amtTypeLine = stmt.withdrawal > 0 ? 'withdrawal' : 'deposit';
        var billingRoEnc = '';
        if (stmt.income_match_status === 'income_matched') {
            try {
                billingRoEnc = encodeURIComponent(
                    JSON.stringify(buildBankReconIncomeTagReadonlyPayload(stmt, amtLine, amtTypeLine))
                );
            } catch (eBillRo) {
                billingRoEnc = '';
            }
        }
        var roBtn =
            billingRoEnc !== ''
                ? '<button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 br-income-billing-in-card-ro-btn" data-income-ro="' +
                  billingRoEnc +
                  '" title="Income tag (read-only)">' +
                  '<i class="bi bi-eye"></i><span class="ms-1 d-none d-xl-inline small">Tag view</span>' +
                  '</button>'
                : '';
        return (
            '<div class="br-income-billing-card br-income-billing-card--interactive" role="button" tabindex="0" data-stmt-id="' +
            String(stmt.id) +
            '" aria-label="Open income bills list" title="Click to open income bills">' +
            '<div class="br-income-billing-card-hd">' +
            '<span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">billing_list</span>' +
            '<span class="br-income-billing-card-title">Income bills</span>' +
            '</div>' +
            preview +
            more +
            '<div class="br-income-billing-card-ft d-flex align-items-center justify-content-between gap-2 mt-1">' +
            '<span class="small text-muted">Total <strong class="text-dark">₹' +
            tot +
            '</strong></span>' +
            '<div class="d-flex align-items-center gap-1 flex-shrink-0">' +
            roBtn +
            '<span class="br-income-billing-open-hint text-primary" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></span>' +
            '</div>' +
            '</div>' +
            '</div>'
        );
    }

    // ============================================
    // RENDER STATEMENTS TABLE
    // ============================================
    function buildStatementRowHtml(stmt) {
            const matchStatusBadge = getMatchStatusBadge(stmt);
            const categoryBadge = getCategoryBadge(stmt);
            const amount = stmt.withdrawal > 0 ? stmt.withdrawal : stmt.deposit;
            const amountType = stmt.withdrawal > 0 ? 'withdrawal' : 'deposit';

            const incomeTagged = stmt.income_match_status === 'income_matched';
            const radiantLinked = stmt.radiant_match_status === 'radiant_matched';
            var descRules = String(stmt.description || '');
            var radiantOnlyCashCam = bankReconDescriptionRadiantOnlyCashOrCam(descRules);
            const allowBrActions = typeof window.bankReconSuperAdmin !== 'undefined' && window.bankReconSuperAdmin;
            
            const matchedBillTitle = buildMatchedBillTitleHtml(stmt);
            const resolvedVendor = (stmt.resolved_vendor_name || stmt.vendor_name || '').toString().trim();
            const lineAccountNames = (stmt.bill_line_account_names || '').toString().trim();
            const brNatureNames = (stmt.resolved_br_nature_account_names || '').toString().trim();
            const brNatureIds = (stmt.resolved_br_nature_account_ids || '').toString().trim();
            const zbParts = [stmt.resolved_bill_zone_name, stmt.resolved_bill_branch_name].map(function (x) {
                return (x || '').toString().trim();
            }).filter(Boolean);
            const zoneBranchText = zbParts.join(' · ');
            const billIdForLinks = stmt.resolved_bill_id != null && String(stmt.resolved_bill_id) !== '' ? parseInt(stmt.resolved_bill_id, 10) : 0;
            const vendorIdForLinks = stmt.resolved_vendor_id != null && String(stmt.resolved_vendor_id) !== '' ? parseInt(stmt.resolved_vendor_id, 10) : 0;
            const zoneIdNum = stmt.resolved_bill_zone_id != null && String(stmt.resolved_bill_zone_id) !== '' ? parseInt(stmt.resolved_bill_zone_id, 10) : 0;
            const branchIdNum = stmt.resolved_bill_branch_id != null && String(stmt.resolved_bill_branch_id) !== '' ? parseInt(stmt.resolved_bill_branch_id, 10) : 0;

            /* ---- Matched Bill: expense bill (bill_tbl) + income billing_list ---- */
            var hasExpenseBillMatch = stmt.match_status !== 'unmatched' && !!matchedBillTitle;
            var incomeBillingCardHtml = buildIncomeBillingListCardHtml(stmt);
            var stackParts = [];
            if (hasExpenseBillMatch) {
                var refLineHtml = matchedBillTitle;
                if (billIdForLinks > 0 && routes.billPrint) {
                    refLineHtml =
                        '<a href="' +
                        escapeAttr(routes.billPrint + '?id=' + billIdForLinks) +
                        '" target="_blank" rel="noopener" class="br-bill-ref-link">' +
                        matchedBillTitle +
                        '</a>';
                }
                var vendorLineInner = escapeAttr(resolvedVendor);
                if (vendorIdForLinks > 0 && routes.billDashboard) {
                    vendorLineInner =
                        '<a href="' +
                        escapeAttr(routes.vendorDashboard + '?id=' + vendorIdForLinks) +
                        '" target="_blank" rel="noopener" class="br-bill-vendor-link"><i class="bi bi-building"></i>' +
                        escapeAttr(resolvedVendor) +
                        '</a>';
                } else {
                    vendorLineInner = '<i class="bi bi-building"></i>' + escapeAttr(resolvedVendor);
                }
                var displayNature = brNatureNames || lineAccountNames;
                var natureRow = '';
                if (displayNature) {
                    var natureInner = escapeAttr(displayNature);
                    if (brNatureIds) {
                        natureInner =
                            '<a href="#" class="br-drill-nature br-bill-nature-link br-link-drill" data-nature-ids="' +
                            escapeAttr(brNatureIds) +
                            '" data-label="' +
                            escapeAttr(displayNature) +
                            '">' +
                            escapeAttr(displayNature) +
                            '</a>';
                    }
                    natureRow =
                        '<div class="br-bill-nature" title="Nature of payment (chart accounts)">' +
                        '<i class="bi bi-journals"></i>' +
                        natureInner +
                        '</div>';
                }
                var zoneRow = '';
                if (zoneBranchText) {
                    var zoneInner = escapeAttr(zoneBranchText);
                    if (zoneIdNum > 0) {
                        zoneInner =
                            '<a href="#" class="br-drill-zone br-bill-zone-link br-link-drill" data-zone-id="' +
                            zoneIdNum +
                            '" data-branch-id="' +
                            (branchIdNum > 0 ? String(branchIdNum) : '') +
                            '" data-label="' +
                            escapeAttr(zoneBranchText) +
                            '">' +
                            escapeAttr(zoneBranchText) +
                            '</a>';
                    }
                    zoneRow = '<div class="br-bill-zone"><i class="bi bi-geo-alt"></i>' + zoneInner + '</div>';
                }
                stackParts.push(
                    '<div class="br-matched-bill-card br-matched-bill-card--expense">' +
                        '<div class="br-bill-sec-label text-muted small">Expense match</div>' +
                        '<div class="br-bill-ref">' +
                        refLineHtml +
                        '</div>' +
                        '<div class="br-bill-vendor">' +
                        vendorLineInner +
                        '</div>' +
                        natureRow +
                        zoneRow +
                        '<div class="br-bill-amount">₹' +
                        formatNumber(stmt.bill_amount || 0) +
                        '</div>' +
                    '</div>'
                );
            }
            if (incomeBillingCardHtml) {
                stackParts.push(incomeBillingCardHtml);
            }
            /* Deposits: income tag does not create a vendor bill match — explain empty "Matched bill" cell */
            if (!stackParts.length && incomeTagged && amountType === 'deposit') {
                stackParts.push(
                    '<div class="br-matched-bill-card br-matched-bill-card--income-only">' +
                        '<div class="br-bill-sec-label text-muted small">Matched bill</div>' +
                        '<div class="br-income-only-hint">' +
                            '<i class="bi bi-info-circle me-1"></i>' +
                            '<span>No vendor expense bill on this line. Tagging is <strong>income reconciliation</strong> (see Income Tag column).</span>' +
                        '</div>' +
                        (stmt.income_matched_branch
                            ? '<div class="br-income-only-branch small mt-1"><i class="bi bi-geo-alt"></i> ' +
                              escapeAttr(stmt.income_matched_branch) +
                              '</div>'
                            : '') +
                    '</div>'
                );
            }
            var matchedBillInfo =
                stackParts.length > 0
                    ? '<div class="br-matched-bill-stack">' + stackParts.join('') + '</div>'
                    : '<span class="br-cell-empty"><i class="bi bi-dash"></i></span>';

            /* ---- Matched By card ---- */
            let matchedbyInfo = '<span class="br-cell-empty"><i class="bi bi-dash"></i></span>';
            var billName   = (stmt.bbm_matched_by_name     || stmt.matched_by_name     || '').toString().trim();
            var billUser   = (stmt.bbm_matched_by_username || stmt.matched_by_username || '').toString().trim();
            var incomeByName = (stmt.income_matched_by_name || '').toString().trim();
            var mbCards = [];
            if (billName) {
                mbCards.push(
                    '<div class="br-matched-by-chip br-matched-by-expense">' +
                        '<span class="br-matched-by-chip-label">Expense</span>' +
                        '<span class="br-matched-by-chip-name">' + escapeAttr(billName) + '</span>' +
                        (billUser ? '<span class="br-matched-by-chip-user">' + escapeAttr(billUser) + '</span>' : '') +
                    '</div>'
                );
            }
            if (incomeByName) {
                mbCards.push(
                    '<div class="br-matched-by-chip br-matched-by-income">' +
                        '<span class="br-matched-by-chip-label">Income</span>' +
                        '<span class="br-matched-by-chip-name">' + escapeAttr(incomeByName) + '</span>' +
                    '</div>'
                );
            }
            if (radiantLinked) {
                var radiantByName = (stmt.radiant_matched_by_name || '').toString().trim();
                mbCards.push(
                    '<div class="br-matched-by-chip br-matched-by-radiant">' +
                        '<span class="br-matched-by-chip-label">Radiant</span>' +
                        '<span class="br-matched-by-chip-name">' +
                        escapeAttr(radiantByName || '—') +
                        '</span>' +
                    '</div>'
                );
            }
            if (bankReconHasSalaryTag(stmt)) {
                var salByN = (stmt.salary_stmt_matched_by_name || stmt.salary_uploaded_by_name || '').toString().trim();
                var salByU = (stmt.salary_stmt_matched_by_username || stmt.salary_uploaded_by_username || '').toString().trim();
                mbCards.push(
                    '<div class="br-matched-by-chip br-matched-by-salary">' +
                        '<span class="br-matched-by-chip-label">Salary</span>' +
                        '<span class="br-matched-by-chip-name">' + escapeAttr(salByN || '—') + '</span>' +
                        (salByU
                            ? '<span class="br-matched-by-chip-user">' + escapeAttr(salByU) + '</span>'
                            : '') +
                    '</div>'
                );
            }
            if (mbCards.length) {
                matchedbyInfo = '<div class="br-matched-by-stack">' + mbCards.join('') + '</div>';
            }

            /* ---- Matched Date cell: expense bill time + income tag time + radiant link time ---- */
            var billDateRaw = stmt.matched_date || stmt.bank_match_matched_at || '';
            var incomeAtRaw = incomeTagged ? (stmt.income_matched_at || '') : '';
            var radiantAtRaw = radiantLinked ? (stmt.radiant_matched_at || '') : '';
            var matchedDateHtml = '<span class="br-cell-empty"><i class="bi bi-dash"></i></span>';
            var dateLines = [];
            if (billDateRaw) {
                dateLines.push(
                    '<div class="br-matched-date-line br-matched-date-line--bill">' +
                        '<span class="br-matched-date-tag">Bill</span>' +
                        '<span class="br-matched-date-val">' + escapeAttr(formatDateTime(billDateRaw)) + '</span>' +
                    '</div>'
                );
            }
            if (incomeAtRaw) {
                dateLines.push(
                    '<div class="br-matched-date-line br-matched-date-line--income">' +
                        '<span class="br-matched-date-tag">Income</span>' +
                        '<span class="br-matched-date-val">' + escapeAttr(formatDateTime(incomeAtRaw)) + '</span>' +
                    '</div>'
                );
            }
            if (radiantAtRaw) {
                dateLines.push(
                    '<div class="br-matched-date-line br-matched-date-line--radiant">' +
                        '<span class="br-matched-date-tag">Radiant</span>' +
                        '<span class="br-matched-date-val">' + escapeAttr(formatDateTime(radiantAtRaw)) + '</span>' +
                    '</div>'
                );
            }
            var salAtDisplay = bankReconSalaryDisplayAt(stmt);
            if (salAtDisplay) {
                dateLines.push(
                    '<div class="br-matched-date-line br-matched-date-line--salary">' +
                        '<span class="br-matched-date-tag">Salary</span>' +
                        '<span class="br-matched-date-val">' + escapeAttr(formatDateTime(salAtDisplay)) + '</span>' +
                    '</div>'
                );
            }
            if (dateLines.length) {
                matchedDateHtml = '<div class="br-matched-date-stack">' + dateLines.join('') + '</div>';
            }

            // Income reconciliation tag details
            let incomeTagCell = '<span class="br-income-not-tagged"><i class="bi bi-dash"></i> Not tagged</span>';
            var incomeRoQuickEnc = '';
            if (incomeTagged && radiantOnlyCashCam && !radiantLinked) {
                try {
                    incomeRoQuickEnc = encodeURIComponent(
                        JSON.stringify(buildBankReconIncomeTagReadonlyPayload(stmt, amount, amountType))
                    );
                } catch (eRoQ) {
                    incomeRoQuickEnc = '';
                }
            }
            if (incomeTagged) {
                window.bankReconIncomeDetailCache[stmt.id] = {
                    remark: (stmt.income_tag_mismatch_remark || '').toString(),
                    branch: (stmt.income_matched_branch || '').toString(),
                    matchedDate: (stmt.income_matched_date || '').toString(),
                    matchedAt: (stmt.income_matched_at || '').toString(),
                    byName: (stmt.income_matched_by_name || '').toString(),
                    byUser: (stmt.income_matched_by_username || '').toString(),
                };
                var hasMismatchRemark = !!(stmt.income_tag_mismatch_remark && String(stmt.income_tag_mismatch_remark).trim());
                incomeTagCell =
                    '<div class="br-income-tag-card-wrap">' +
                    '<div class="br-income-tag-card-actions">' +
                    (incomeRoQuickEnc
                        ? '<button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 br-income-tag-ro-btn" data-income-ro="' +
                          incomeRoQuickEnc +
                          '" title="Income tag (read-only)">' +
                          '<i class="bi bi-eye"></i><span class="ms-1 d-none d-xxl-inline">Tag view</span>' +
                          '</button>'
                        : '') +
                    '<button type="button" class="br-income-tag-detail-btn" data-stmt-id="' +
                        String(stmt.id) +
                        '" title="View income tag details &amp; remark">' +
                        '<i class="bi bi-info-circle-fill"></i>' +
                        (hasMismatchRemark ? '<span class="br-income-tag-detail-dot" aria-hidden="true"></span>' : '') +
                    '</button>' +
                    '</div>' +
                    '<div class="br-income-tag-card">' +
                        '<div class="br-income-tag-badge">' +
                            '<i class="bi bi-arrow-left-right"></i> Income Tagged' +
                        '</div>' +
                        '<div class="br-income-tag-branch">' +
                            '<i class="bi bi-geo-alt-fill br-income-tag-branch-icon"></i>' +
                            escapeAttr(stmt.income_matched_branch || '') +
                        '</div>' +
                        '<div class="br-income-tag-doc-rows">' +
                            buildIncomeTaggedDateAmountRowsHtml(stmt) +
                            buildIncomeTagModesLineHtml(stmt) +
                        '</div>' +
                        '<div class="br-income-tag-by">' +
                            '<i class="bi bi-person-fill"></i> ' +
                            escapeAttr(stmt.income_matched_by_name || '') +
                        '</div>' +
                    '</div></div>';
            }

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

            var salaryTagCell = '<span class="text-muted small br-salary-cell-empty">—</span>';
            if (stmt.salary_recon_row_id) {
                var salCellDisplayAt = bankReconSalaryDisplayAt(stmt);
                var salNet = stmt.salary_net_paid != null ? formatNumber(stmt.salary_net_paid) : '—';
                var salEc = (stmt.salary_ec_id || '').toString().trim();
                var salUtr = (stmt.salary_utr || '').toString().trim();
                salaryTagCell =
                    '<div class="br-salary-tag-card small" title="From salary UTR file">' +
                    '<div class="br-salary-tag-badge">Salary</div>' +
                    (salEc
                        ? '<div class="br-salary-line"><span class="text-muted">EC</span> ' + escapeAttr(salEc) + '</div>'
                        : '') +
                    '<div class="br-salary-line br-salary-line--name fw-semibold">' +
                    escapeAttr((stmt.salary_employee_name || '').toString()) +
                    '</div>' +
                    (stmt.salary_designation
                        ? '<div class="text-muted br-salary-line small">' + escapeAttr(String(stmt.salary_designation)) + '</div>'
                        : '') +
                    (stmt.salary_branch
                        ? '<div class="br-salary-line text-muted small"><i class="bi bi-geo-alt"></i> ' + escapeAttr(String(stmt.salary_branch)) + '</div>'
                        : '') +
                    (salUtr
                        ? '<div class="br-salary-line text-break small"><span class="text-muted">UTR</span> ' + escapeAttr(salUtr) + '</div>'
                        : '') +
                    '<div class="br-salary-line">Net <strong class="text-success">₹' + salNet + '</strong></div>' +
                    (stmt.salary_stmt_matched_by_name || stmt.salary_uploaded_by_name
                        ? '<div class="br-salary-line text-muted small mt-1"><i class="bi bi-person"></i> ' +
                          escapeAttr(
                              String(
                                  stmt.salary_stmt_matched_by_name || stmt.salary_uploaded_by_name || ''
                              )
                          ) +
                          (stmt.salary_stmt_matched_by_username || stmt.salary_uploaded_by_username
                              ? ' <span class="font-monospace">@' +
                                escapeAttr(
                                    String(
                                        stmt.salary_stmt_matched_by_username
                                            || stmt.salary_uploaded_by_username
                                            || ''
                                    )
                                ) +
                                '</span>'
                              : '') +
                          '</div>'
                        : '') +
                    (salCellDisplayAt
                        ? '<div class="br-salary-line text-muted small"><i class="bi bi-link-45deg"></i> Matched ' +
                          escapeAttr(formatDateTime(String(salCellDisplayAt))) +
                          '</div>'
                        : '') +
                    (stmt.salary_upload_file_name
                        ? '<div class="br-salary-line text-muted small" title="Source file"><i class="bi bi-file-earmark"></i> ' +
                          escapeAttr(String(stmt.salary_upload_file_name)) +
                          '</div>'
                        : '') +
                    '</div>';
            }

            var incomeViewAttr = '';
            var incomeRowReadonly =
                incomeTagged && (radiantLinked || !radiantOnlyCashCam);
            if (incomeRowReadonly) {
                try {
                    var roPayloadReadonly = buildBankReconIncomeTagReadonlyPayload(stmt, amount, amountType);
                    var roTitle = radiantLinked
                        ? 'View income &amp; Radiant tags (read-only)'
                        : 'View income tag (read-only)';
                    incomeViewAttr =
                        ' data-income-readonly="1" data-income-ro="' +
                        encodeURIComponent(JSON.stringify(roPayloadReadonly)) +
                        '" title="' +
                        roTitle +
                        '"';
                } catch (eRo) {
                    incomeViewAttr = '';
                }
            }

            var salaryViewAttr = '';
            if (bankReconHasSalaryTag(stmt)) {
                try {
                    var salRoPayload = buildBankReconSalaryReadonlyPayload(stmt, amount, amountType);
                    salaryViewAttr =
                        ' data-salary-readonly="1" data-salary-ro="' +
                        encodeURIComponent(JSON.stringify(salRoPayload)) +
                        '" title="View salary UTR match (read-only)"';
                } catch (eSalRo) {
                    salaryViewAttr = '';
                }
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
                    data-radiant-pickup-id="${stmt.radiant_cash_pickup_id || ''}"
                    data-income-tagged="${incomeTagged ? '1' : '0'}"
                    data-radiant-linked="${radiantLinked ? '1' : '0'}"
                    ${incomeViewAttr}
                    ${salaryViewAttr}>
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
                        ${matchedDateHtml}
                    </td>
                    <td>
                        ${buildNatureFilesCell(stmt)}
                    </td>
                    <td>
                        ${incomeTagCell}
                    </td>
                    <td class="text-nowrap small br-mocdoc-date-cell">
                        ${
                            incomeTagged && stmt.income_matched_at
                                ? escapeAttr(formatDateTime(stmt.income_matched_at))
                                : '<span class="text-muted">—</span>'
                        }
                    </td>
                    <td>
                        ${radiantTagCell}
                    </td>
                    <td>
                        ${salaryTagCell}
                    </td>
                    ${allowBrActions ? `
                    <td>
                        <div class="action-buttons">
                            ${!incomeTagged && !bankReconHasSalaryTag(stmt) ? (stmt.match_status === 'unmatched' ? `
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
                    ` : ''}
                </tr>
            `;

            return row;
    }

    function renderStatementsTable(statements) {
        const tbody = $('#statementsTableBody');
        tbody.empty();
        window.bankReconIncomeDetailCache = {};
        window.bankReconIncomeBillingCache = {};

        if (!statements || statements.length === 0) {
            tbody.html(
                '<tr>' +
                    '<td colspan="' + bankReconStatementColCount() + '" class="text-center py-5">' +
                        '<i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>' +
                        '<p class="text-muted mt-3">No statements found</p>' +
                    '</td>' +
                '</tr>'
            );
            return;
        }

        statements.forEach(function (stmt) {
            tbody.append(buildStatementRowHtml(stmt));
        });
    }

    function refreshStatementRow(statementId, done) {
        var sid = statementId != null ? String(statementId) : '';
        if (!sid || typeof routes === 'undefined' || !routes.statementShow) {
            loadStatements(currentPage);
            if (typeof done === 'function') {
                done(false);
            }
            return;
        }
        var url = routes.statementShow.replace(':id', sid);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
        })
            .done(function (response) {
                if (!response || !response.success || !response.data) {
                    loadStatements(currentPage);
                    if (typeof done === 'function') {
                        done(false);
                    }
                    return;
                }
                var stmt = response.data;
                if (window.bankReconIncomeDetailCache) {
                    delete window.bankReconIncomeDetailCache[sid];
                }
                if (window.bankReconIncomeBillingCache) {
                    delete window.bankReconIncomeBillingCache[sid];
                }
                if (window.bankReconMatchFiles) {
                    delete window.bankReconMatchFiles[sid];
                }
                var html = buildStatementRowHtml(stmt);
                var $tr = $('tr.statement-row-clickable[data-id="' + sid + '"]');
                if ($tr.length) {
                    $tr.replaceWith(html);
                } else {
                    loadStatements(currentPage);
                    if (typeof done === 'function') {
                        done(false);
                    }
                    return;
                }
                if (typeof done === 'function') {
                    done(true);
                }
            })
            .fail(function () {
                toastr.error('Could not refresh statement row');
                loadStatements(currentPage);
                if (typeof done === 'function') {
                    done(false);
                }
            });
    }
    
    /** 'withdrawal' = bill match tab only; 'deposit' = income tag + radiant only */
    var bankReconModalTxnMode = 'deposit';
    /** When true, deposit modal shows Radiant tab only (no income-tag tab init). */
    var bankReconDepositRadiantOnly = false;

    function resetMatchModalTabsVisibility() {
        bankReconDepositRadiantOnly = false;
        $('#matchTabs .nav-item').removeClass('d-none');
        $('#matchTabContent .tab-pane').removeClass('d-none');
        $('#matchTabs .nav-link').removeClass('active');
        $('#matchTabContent .tab-pane').removeClass('show active');
        $('#categorize-tab').addClass('active');
        $('#categorize-content').addClass('show active');
    }

    function applyMatchModalLayout(mode) {
        bankReconDepositRadiantOnly = false;
        if (mode === 'deposit_radiant_only') {
            bankReconModalTxnMode = 'deposit';
            bankReconDepositRadiantOnly = true;
        } else {
            bankReconModalTxnMode = mode;
        }
        var $lis = $('#matchTabs .nav-item');
        var $matchLi = $lis.eq(0);
        var $incomeLi = $lis.eq(1);
        var $radiantLi = $lis.eq(2);
        $('#matchTabs .nav-link').removeClass('active');
        $('#matchTabContent .tab-pane').removeClass('show active');

        if (mode === 'deposit_radiant_only') {
            $matchLi.addClass('d-none');
            $incomeLi.addClass('d-none');
            $radiantLi.removeClass('d-none');
            $('#match-content, #categorize-content').addClass('d-none');
            $('#radiant-match-content').removeClass('d-none');
            $('#radiant-match-tab').addClass('active');
            $('#radiant-match-content').addClass('show active');
            return;
        }

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

        var $row = $(this);
        if ($row.attr('data-salary-readonly') === '1') {
            var encSal = $row.attr('data-salary-ro') || '';
            if (encSal) {
                try {
                    openBankReconSalaryReadonlyModal(JSON.parse(decodeURIComponent(encSal)));
                } catch (eSal) {
                    toastr.error('Could not open salary details. Try refreshing the page.');
                }
            } else {
                toastr.warning('Salary tag details are missing. Refresh the list and try again.');
            }
            return;
        }
        if ($row.attr('data-income-readonly') === '1' || $row.attr('data-both-tags-readonly') === '1') {
            var enc = $row.attr('data-income-ro') || '';
            if (enc) {
                try {
                    openIncomeTagReadonlyModal(JSON.parse(decodeURIComponent(enc)));
                } catch (eView) {
                    toastr.error('Could not open tag details. Try refreshing the page.');
                }
            } else {
                toastr.warning('Tag details are missing. Refresh the list and try again.');
            }
            return;
        }

        var matchStatusEarly = ($row.attr('data-match-status') || '').toString();
        if (matchStatusEarly === 'matched') {
            return;
        }

        var txnType = ($row.data('type') || $row.attr('data-type') || '').toString();
        var incomeTaggedRow = $row.attr('data-income-tagged') === '1';
        var radiantLinkedRow = $row.attr('data-radiant-linked') === '1';
        var descForRouting = String($row.data('description') || $row.attr('data-description') || '');
        var radiantOnlyDesc = bankReconDescriptionRadiantOnlyCashOrCam(descForRouting);

        if (incomeTaggedRow && !radiantLinkedRow && txnType === 'deposit') {
            currentStatementId = $row.data('id');
            currentTxnAmount = parseFloat($row.data('amount'));
            currentTxnData = {
                date: $row.data('date'),
                reference: $row.data('reference'),
                description: $row.data('description'),
                amount: currentTxnAmount,
            };
            $('#txnDate').text(formatDate(currentTxnData.date));
            $('#txnReference').text(currentTxnData.reference || '-');
            $('#txnDescription').text(currentTxnData.description);
            $('#txnAmount').text('₹' + formatNumber(currentTxnAmount));
            $('#pendingAmount').text(formatNumber(currentTxnAmount));
            $('#radiantMatchAgainstInput').val($row.attr('data-radiant-match') || '');
            window._brPendingRadiantPickupId = ($row.attr('data-radiant-pickup-id') || '').toString().trim();
            selectedBills = [];
            applyMatchModalLayout(radiantOnlyDesc ? 'deposit_radiant_only' : 'deposit');
            if (radiantOnlyDesc) {
                $('#matchTransactionModal').one('shown.bs.modal', function brFocusRadiantTabCashCam() {
                    var tabEl = document.getElementById('radiant-match-tab');
                    if (tabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        bootstrap.Tab.getOrCreateInstance(tabEl).show();
                    } else if (typeof $ !== 'undefined' && $('#radiant-match-tab').length) {
                        $('#radiant-match-tab').tab('show');
                    }
                });
            }
            $('#matchTransactionModal').modal('show');
            clearBankMatchAttachmentStaging();
            loadBankReconAttachmentTypes(null, 'income');
            renderIncomeTagAttachmentStaging();
            return;
        }

        currentStatementId = $row.data('id');
        currentTxnAmount = parseFloat($row.data('amount'));

        currentTxnData = {
            date: $row.data('date'),
            reference: $row.data('reference'),
            description: $row.data('description'),
            amount: currentTxnAmount
        };

        $('#txnDate').text(formatDate(currentTxnData.date));
        $('#txnReference').text(currentTxnData.reference || '-');
        $('#txnDescription').text(currentTxnData.description);
        $('#txnAmount').text('₹' + formatNumber(currentTxnAmount));
        $('#pendingAmount').text(formatNumber(currentTxnAmount));
        $('#radiantMatchAgainstInput').val($row.attr('data-radiant-match') || '');
        window._brPendingRadiantPickupId = ($row.attr('data-radiant-pickup-id') || '').toString().trim();

        selectedBills = [];

        if (txnType === 'withdrawal') {
            applyMatchModalLayout('withdrawal');
        } else {
            applyMatchModalLayout('deposit');
        }

        $('#matchTransactionModal').modal('show');

        if (txnType === 'withdrawal') {
            clearIncomeTagAttachmentStaging();
            loadBankReconAttachmentTypes(null, 'bill');
            searchMatchingBills(currentTxnAmount);
        } else {
            clearBankMatchAttachmentStaging();
            loadBankReconAttachmentTypes(null, 'income');
            renderIncomeTagAttachmentStaging();
        }
    });

    $(document).on('click', '.br-income-tag-ro-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var enc = $(this).attr('data-income-ro') || '';
        if (!enc) {
            toastr.warning('Tag details are missing. Refresh the list and try again.');
            return;
        }
        try {
            openIncomeTagReadonlyModal(JSON.parse(decodeURIComponent(enc)));
        } catch (eRoBtn) {
            toastr.error('Could not open tag details. Try refreshing the page.');
        }
    });

    $(document).on('click', '.br-income-tag-detail-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var sid = String($(this).data('stmt-id') || '');
        var c = window.bankReconIncomeDetailCache && sid ? window.bankReconIncomeDetailCache[sid] : null;
        if (!c) {
            toastr.warning('Details not available. Refresh the list.');
            return;
        }
        $('#brIncomeDetailBranch').text(c.branch || '—');
        $('#brIncomeDetailDate').text(c.matchedDate || '—');
        $('#brIncomeDetailAt').text(
            c.matchedAt && typeof moment !== 'undefined'
                ? (moment(c.matchedAt).isValid() ? moment(c.matchedAt).format('DD/MM/YYYY HH:mm') : c.matchedAt)
                : c.matchedAt || '—'
        );
        var byLine = (c.byName || '').trim();
        if ((c.byUser || '').trim()) {
            byLine = byLine ? byLine + ' (' + c.byUser + ')' : c.byUser;
        }
        $('#brIncomeDetailBy').text(byLine || '—');
        $('#brIncomeDetailRemark').text((c.remark || '').trim() || '—');
        bankReconShowModal(document.getElementById('bankReconIncomeTagDetailModal'));
    });

    function brOpenIncomeBillingListModal(sid) {
        var id = sid != null ? String(sid) : '';
        if (!id) {
            return;
        }
        var pack = window.bankReconIncomeBillingCache && window.bankReconIncomeBillingCache[id];
        if (!pack || !pack.rows || !pack.rows.length) {
            toastr.warning('Billing list data not found. Refresh the list and try again.');
            return;
        }
        var $tb = $('#incomeBillingListModalTableBody');
        if (!$tb.length) {
            return;
        }
        $tb.empty();
        pack.rows.forEach(function (r) {
            $tb.append(
                '<tr>' +
                    '<td><code class="small text-break">' +
                    escapeAttr(r.id) +
                    '</code></td>' +
                    '<td class="fw-semibold">' +
                    escapeAttr(r.billno || '—') +
                    '</td>' +
                    '<td class="text-nowrap small">' +
                    escapeAttr(formatBillingListBillDateRaw(r.billdate)) +
                    '</td>' +
                    '<td><span class="badge bg-warning text-dark">' +
                    escapeAttr(r.paymenttype || '') +
                    '</span></td>' +
                    '<td class="small text-muted">' +
                    escapeAttr(r.type || '—') +
                    '</td>' +
                    '<td class="text-end">₹' +
                    formatNumber(r.amount != null ? r.amount : 0) +
                    '</td>' +
                    '</tr>'
            );
        });
        $('#incomeBillingListModalBranch').text(pack.branch || '—');
        $('#incomeBillingListModalCount').text(String(pack.rows.length));
        var sum =
            pack.total != null
                ? pack.total
                : pack.rows.reduce(function (a, x) {
                      return a + (parseFloat(x.amount) || 0);
                  }, 0);
        $('#incomeBillingListModalTotal').text('₹' + formatNumber(sum));
        bankReconShowModal(document.getElementById('incomeBillingListModal'));
    }

    $(document).on('click', '.br-income-billing-card--interactive', function (e) {
        if ($(e.target).closest('.br-income-billing-in-card-ro-btn').length) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var sid = $(this).data('stmt-id');
        brOpenIncomeBillingListModal(sid);
    });

    $(document).on('keydown', '.br-income-billing-card--interactive', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') {
            return;
        }
        if ($(e.target).closest('.br-income-billing-in-card-ro-btn').length) {
            return;
        }
        e.preventDefault();
        brOpenIncomeBillingListModal($(this).data('stmt-id'));
    });

    $(document).on('click', '.br-income-billing-in-card-ro-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var enc = $(this).attr('data-income-ro') || '';
        if (!enc) {
            toastr.warning('Tag details are missing. Refresh the list and try again.');
            return;
        }
        try {
            openIncomeTagReadonlyModal(JSON.parse(decodeURIComponent(enc)));
        } catch (eBillRo) {
            toastr.error('Could not open tag details. Try refreshing the page.');
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

    function bankReconShowOffcanvas(el) {
        if (!el) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas && typeof bootstrap.Offcanvas.getOrCreateInstance === 'function') {
            bootstrap.Offcanvas.getOrCreateInstance(el).show();
        }
    }

    var bankReconDrilldownState = { mode: null, page: 1 };

    function bankReconDrilldownFilterPayload() {
        return $.extend({}, currentFilters);
    }

    function bankReconRenderDrilldownRows(rows, total) {
        var $body = $('#bankReconDrilldownBody');
        $body.empty();
        if (!rows || !rows.length) {
            $('#bankReconDrilldownEmpty').show().find('.br-drill-empty-text').text('No matching statements found');
            $('#bankReconDrilldownTableWrap').hide();
            $('#bankReconDrilldownPagination').empty();
            return;
        }
        $('#bankReconDrilldownEmpty').hide();
        $('#bankReconDrilldownTableWrap').show();
        /* Update subtitle with total count */
        if (total != null) {
            var $sub = $('#bankReconDrilldownSubtitle');
            var baseLabel = bankReconDrilldownState._label || '';
            $sub.html(escapeAttr(baseLabel) + '<span class="br-drill-count-badge ms-2">' + total + ' statement' + (total !== 1 ? 's' : '') + '</span>');
        }
        rows.forEach(function (stmt) {
            var w = parseFloat(stmt.withdrawal) || 0;
            var d = parseFloat(stmt.deposit) || 0;
            var billNo = (stmt.resolved_bill_gen_number || stmt.resolved_bill_number || stmt.bill_number || '').toString().trim();
            var vendor = (stmt.resolved_vendor_name || stmt.vendor_name || '').toString().trim();
            var acct = (stmt.bank_account_number || '').toString().trim();
            var bankNm = (stmt.bank_account_bank_name || '').toString().trim();
            var acctDisp = acct ? ('<span class="br-drill-acct-num">' + escapeAttr(acct) + '</span>' + (bankNm ? '<br><span class="br-drill-acct-bank">' + escapeAttr(bankNm) + '</span>' : '')) : '—';
            var wHtml = w > 0 ? '<span class="br-drill-withdrawal">₹' + formatNumber(w) + '</span>' : '<span class="text-muted">—</span>';
            var dHtml = d > 0 ? '<span class="br-drill-deposit">₹' + formatNumber(d) + '</span>' : '<span class="text-muted">—</span>';
            var tr = $('<tr>');
            tr.append($('<td>').addClass('br-drill-td-date').html('<span class="br-drill-date">' + escapeAttr((stmt.transaction_date || '').toString()) + '</span>'));
            tr.append($('<td>').addClass('br-drill-td-desc').html('<span class="br-drill-desc">' + escapeAttr((stmt.description || '').toString().substring(0, 80)) + '</span>'));
            tr.append($('<td>').addClass('br-drill-td-ref').html('<code class="br-drill-ref-code">' + escapeAttr((stmt.reference_number || '').toString()) + '</code>'));
            tr.append($('<td class="text-end">').html(wHtml));
            tr.append($('<td class="text-end">').html(dHtml));
            tr.append($('<td>').html(billNo ? '<span class="br-drill-bill-no">' + escapeAttr(billNo) + '</span>' : '<span class="text-muted">—</span>'));
            tr.append($('<td>').html(vendor ? '<span class="br-drill-vendor">' + escapeAttr(vendor) + '</span>' : '<span class="text-muted">—</span>'));
            tr.append($('<td>').html(acctDisp));
            $body.append(tr);
        });
    }

    function bankReconRenderDrilldownPagination(res) {
        var $nav = $('#bankReconDrilldownPagination');
        $nav.empty();
        var cur = res.current_page || 1;
        var last = res.last_page || 1;
        var total = res.total || 0;
        if (last <= 1) {
            if (total > 0) {
                $nav.html('<div class="text-muted small">' + total + ' record' + (total !== 1 ? 's' : '') + '</div>');
            }
            return;
        }
        var ul = $('<ul class="pagination pagination-sm mb-0">');
        ul.append($('<li class="page-item' + (cur <= 1 ? ' disabled' : '') + '">').append(
            $('<a class="page-link br-drill-page" href="#">').html('<i class="bi bi-chevron-left"></i>').attr('data-page', String(cur - 1))
        ));
        var start = Math.max(1, cur - 2);
        var end = Math.min(last, cur + 2);
        if (start > 1) {
            ul.append($('<li class="page-item disabled">').append($('<span class="page-link">').text('…')));
        }
        for (var p = start; p <= end; p++) {
            ul.append($('<li class="page-item' + (p === cur ? ' active' : '') + '">').append(
                $('<a class="page-link br-drill-page" href="#">').text(String(p)).attr('data-page', String(p))
            ));
        }
        if (end < last) {
            ul.append($('<li class="page-item disabled">').append($('<span class="page-link">').text('…')));
        }
        ul.append($('<li class="page-item' + (cur >= last ? ' disabled' : '') + '">').append(
            $('<a class="page-link br-drill-page" href="#">').html('<i class="bi bi-chevron-right"></i>').attr('data-page', String(cur + 1))
        ));
        var wrap = $('<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">');
        wrap.append($('<div class="text-muted small">').text('Page ' + cur + ' of ' + last + ' · ' + total + ' records'));
        wrap.append(ul);
        $nav.append(wrap);
    }

    function bankReconLoadDrilldownNature(page) {
        if (!routes.drilldownByNature) {
            toastr.error('Drill-down route not configured');
            return;
        }
        var ids = (bankReconDrilldownState.natureIds || '').toString().trim();
        if (!ids) {
            return;
        }
        bankReconDrilldownState.page = page || 1;
        $('#bankReconDrilldownBody').empty();
        $('#bankReconDrilldownPagination').empty();
        $('#bankReconDrilldownLoading').show();
        $('#bankReconDrilldownEmpty').hide();
        $('#bankReconDrilldownTableWrap').show();
        $.ajax({
            url: routes.drilldownByNature,
            type: 'GET',
            dataType: 'json',
            data: $.extend({ page: bankReconDrilldownState.page, per_page: 25, nature_account_ids: ids }, bankReconDrilldownFilterPayload()),
            success: function (res) {
                $('#bankReconDrilldownLoading').hide();
                bankReconRenderDrilldownRows(res.data, res.total);
                bankReconRenderDrilldownPagination(res);
            },
            error: function (xhr) {
                $('#bankReconDrilldownLoading').hide();
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load';
                toastr.error(msg);
            }
        });
    }

    function bankReconLoadDrilldownZone(page) {
        if (!routes.drilldownByZone) {
            toastr.error('Drill-down route not configured');
            return;
        }
        bankReconDrilldownState.page = page || 1;
        var z = parseInt(bankReconDrilldownState.zoneId, 10) || 0;
        var b = parseInt(bankReconDrilldownState.branchId, 10) || 0;
        if (z <= 0 && b <= 0) {
            toastr.warning('Select zone or branch');
            return;
        }
        $('#bankReconDrilldownBody').empty();
        $('#bankReconDrilldownPagination').empty();
        $('#bankReconDrilldownLoading').show();
        $('#bankReconDrilldownEmpty').hide();
        $('#bankReconDrilldownTableWrap').show();
        $.ajax({
            url: routes.drilldownByZone,
            type: 'GET',
            dataType: 'json',
            data: $.extend({
                page: bankReconDrilldownState.page,
                per_page: 25,
                zone_id: z > 0 ? z : '',
                branch_id: b > 0 ? b : ''
            }, bankReconDrilldownFilterPayload()),
            success: function (res) {
                $('#bankReconDrilldownLoading').hide();
                bankReconRenderDrilldownRows(res.data, res.total);
                bankReconRenderDrilldownPagination(res);
            },
            error: function (xhr) {
                $('#bankReconDrilldownLoading').hide();
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load';
                toastr.error(msg);
            }
        });
    }

    function bankReconFillDrilldownZoneSelects(done) {
        var $z = $('#bankReconDrilldownZoneSelect');
        if (!$z.length || $z.data('br-loaded')) {
            if (typeof done === 'function') {
                done();
            }
            return;
        }
        if (!routes.incomeTagZones) {
            $z.data('br-loaded', true);
            if (typeof done === 'function') {
                done();
            }
            return;
        }
        $.get(routes.incomeTagZones, function (zones) {
            $z.empty();
            $z.append($('<option value="">').text('All zones'));
            (zones || []).forEach(function (z) {
                $z.append($('<option>').val(String(z.id)).text(z.name || z.id));
            });
            $z.data('br-loaded', true);
            if (typeof done === 'function') {
                done();
            }
        }).fail(function () {
            $z.data('br-loaded', true);
            if (typeof done === 'function') {
                done();
            }
        });
    }

    function bankReconLoadDrilldownBranches(zoneId, selectBranchId, done) {
        var $b = $('#bankReconDrilldownBranchSelect');
        $b.empty().append($('<option value="">').text('All branches'));
        if (!zoneId || !routes.incomeTagBranches) {
            if (typeof done === 'function') {
                done();
            }
            return;
        }
        $.get(routes.incomeTagBranches, { zone_id: zoneId }, function (branches) {
            (branches || []).forEach(function (br) {
                $b.append($('<option>').val(String(br.id)).text(br.name || br.id));
            });
            if (selectBranchId) {
                $b.val(String(selectBranchId));
            }
            if (typeof done === 'function') {
                done();
            }
        }).fail(function () {
            if (typeof done === 'function') {
                done();
            }
        });
    }

    $(document).on('click', '.br-drill-nature', function (e) {
        e.preventDefault();
        var ids = ($(this).attr('data-nature-ids') || '').toString().trim();
        var label = ($(this).attr('data-label') || '').toString().trim() || 'Nature of payment';
        if (!ids) { return; }
        bankReconDrilldownState = { mode: 'nature', page: 1, natureIds: ids, _label: label };
        $('#bankReconDrilldownZoneFilters').hide();
        $('#bankReconDrilldownHeaderIcon').html('<i class="bi bi-journals"></i>');
        $('#bankReconDrilldownTitle').text('Same nature of payment');
        $('#bankReconDrilldownSubtitle').text(label);
        bankReconShowOffcanvas(document.getElementById('bankReconDrilldownOffcanvas'));
        bankReconLoadDrilldownNature(1);
    });

    $(document).on('click', '.br-drill-zone', function (e) {
        e.preventDefault();
        var z = parseInt($(this).attr('data-zone-id'), 10) || 0;
        var bStr = ($(this).attr('data-branch-id') || '').toString().trim();
        var b = parseInt(bStr, 10) || 0;
        var label = ($(this).attr('data-label') || '').toString().trim() || 'Zone / branch';
        bankReconDrilldownState = { mode: 'zone', page: 1, zoneId: z, branchId: b, _label: label };
        $('#bankReconDrilldownHeaderIcon').html('<i class="bi bi-geo-alt-fill"></i>');
        $('#bankReconDrilldownTitle').text('Bills in zone / branch');
        $('#bankReconDrilldownSubtitle').text(label);
        $('#bankReconDrilldownZoneFilters').show();
        bankReconFillDrilldownZoneSelects(function () {
            if (z > 0) {
                $('#bankReconDrilldownZoneSelect').val(String(z));
                bankReconLoadDrilldownBranches(z, b > 0 ? b : null, null);
            }
        });
        bankReconShowOffcanvas(document.getElementById('bankReconDrilldownOffcanvas'));
        bankReconLoadDrilldownZone(1);
    });

    $(document).on('click', '.br-drill-page', function (e) {
        e.preventDefault();
        var p = parseInt($(this).attr('data-page'), 10);
        if (!p || p < 1) {
            return;
        }
        if (bankReconDrilldownState.mode === 'nature') {
            bankReconLoadDrilldownNature(p);
        } else if (bankReconDrilldownState.mode === 'zone') {
            bankReconLoadDrilldownZone(p);
        }
    });

    $(document).on('change', '#bankReconDrilldownZoneSelect', function () {
        var zid = $(this).val();
        bankReconLoadDrilldownBranches(zid, null, null);
    });

    $(document).on('click', '#bankReconDrilldownApplyZone', function () {
        var z = parseInt($('#bankReconDrilldownZoneSelect').val(), 10) || 0;
        var b = parseInt($('#bankReconDrilldownBranchSelect').val(), 10) || 0;
        bankReconDrilldownState.zoneId = z;
        bankReconDrilldownState.branchId = b;
        bankReconDrilldownState.mode = 'zone';
        bankReconLoadDrilldownZone(1);
    });

    $(document).on('click', '.br-income-tag-detail-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var sid = $(this).data('stmt-id');
        var o = window.bankReconIncomeDetailCache && window.bankReconIncomeDetailCache[sid];
        if (!o) {
            return;
        }
        $('#brIncomeDetailBranch').text(o.branch || '—');
        $('#brIncomeDetailDate').text(o.matchedDate || '—');
        var atRaw = (o.matchedAt || '').toString().trim();
        $('#brIncomeDetailAt').text(atRaw ? formatDateTime(atRaw) : '—');
        var by = (o.byName || '').trim();
        var u = (o.byUser || '').trim();
        var byLine = by;
        if (u) {
            byLine = byLine ? byLine + ' · @' + u : '@' + u;
        }
        $('#brIncomeDetailBy').text(byLine || '—');
        var rem = (o.remark || '').trim();
        $('#brIncomeDetailRemark').text(rem || '—');
        bankReconShowModal(document.getElementById('bankReconIncomeTagDetailModal'));
    });

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
            zIndex: 2005
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
        clearBankMatchAttachmentStaging();
        teardownBankMatchNatureDropdown();
        rebuildBankMatchNatureList();
        $('.br-bank-match-nature').find('#bankMatchNatureIds').val('');
        $('.br-bank-match-nature .br-bank-match-nature-input').val('');
        var $m = document.getElementById('bankMatchDetailsModal');
        function showNow() {
            bankReconShowModal($m);
        }
        if (typeof routes !== 'undefined' && routes.matchAttachmentTypes) {
            loadBankReconAttachmentTypes(showNow, 'bill');
        } else {
            showNow();
        }
    }

    $('#bankMatchDetailsModal').on('hidden.bs.modal', function () {
        teardownBankMatchNatureDropdown();
        clearBankMatchAttachmentStaging();
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

        var hasFile = bankMatchStagingFiles.some(function (it) {
            return it && it.file;
        });
        if (!hasFile) {
            toastr.warning('Please add at least one attachment before matching (Attachments tab).');
            var tabFiles = document.getElementById('br-match-tab-files');
            if (tabFiles && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(tabFiles).show();
            }
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
        bankMatchStagingFiles.forEach(function (it) {
            fd.append('attachments[]', it.file);
            fd.append('attachment_tags[]', bankMatchStagingTagLabel(it));
            if (it.tagTypeId != null && it.tagTypeId !== '' && !isNaN(Number(it.tagTypeId))) {
                fd.append('attachment_type_ids[]', String(it.tagTypeId));
            } else {
                fd.append('attachment_type_ids[]', '');
            }
        });

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
                    refreshStatementRow(currentStatementId);
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
                    } else if (xhr.responseJSON.errors) {
                        var err = xhr.responseJSON.errors;
                        if (err.nature_account_ids) {
                            var ne = err.nature_account_ids;
                            message = Array.isArray(ne) ? ne[0] : ne;
                        } else if (err.attachments) {
                            var ae = err.attachments;
                            message = Array.isArray(ae) ? ae[0] : ae;
                        } else {
                            var firstKey = Object.keys(err)[0];
                            if (firstKey && err[firstKey]) {
                                var ev = err[firstKey];
                                message = Array.isArray(ev) ? ev[0] : ev;
                            }
                        }
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

    function bankReconNormalizePublicPath(p) {
        if (!p) {
            return '';
        }
        var s = String(p).trim().replace(/\\/g, '/');
        s = s.replace(/^(\.\/)+/, '');
        s = s.replace(/^\/+/, '');
        if (s.toLowerCase().indexOf('public/') === 0) {
            s = s.slice(7);
        }
        return s;
    }

    /**
     * Stored URLs sometimes omit /public/ (e.g. /hms/bank_recon_match_files/...) — rewrite to /hms/public/...
     * Only when a single path segment precedes bank_recon_match_files (avoids breaking docroot=public installs).
     */
    function bankReconFixPathnameForMatchFiles(pathname) {
        if (!pathname || pathname.indexOf('/public/bank_recon_match_files') !== -1) {
            return pathname;
        }
        var fixed = pathname.replace(/^\/([^/]+)\/(bank_recon_match_files.*)$/, '/$1/public/$2');
        return fixed === pathname ? pathname : fixed;
    }

    /**
     * Build a browser URL for files under public/ using DB path (e.g. bank_recon_match_files/…).
     * Uses BANK_RECON_PUBLIC_PREFIX from the page (Laravel base path) so subfolder installs resolve correctly.
     */
    function bankReconBuildPublicFileUrl(relativePath) {
        var rel = bankReconNormalizePublicPath(relativePath);
        if (!rel) {
            return '#';
        }
        var origin = typeof window.location !== 'undefined' ? window.location.origin : '';
        var base = (typeof window.BANK_RECON_PUBLIC_PREFIX !== 'undefined' && window.BANK_RECON_PUBLIC_PREFIX)
            ? String(window.BANK_RECON_PUBLIC_PREFIX).replace(/\/+$/, '')
            : '';
        if (!base) {
            return origin + '/' + rel;
        }
        return origin + base + '/' + rel;
    }

    /**
     * Resolve stored attachment row for img/embed/href: prefer `path`, then rebase full `url` to current origin.
     */
    function bankReconResolveAttachmentUrl(f) {
        if (!f) {
            return '#';
        }
        var pathRaw = f.path != null ? String(f.path).trim() : '';
        var urlRaw = f.url != null ? String(f.url).trim() : '';

        if (pathRaw && pathRaw !== '#') {
            return bankReconBuildPublicFileUrl(pathRaw);
        }

        if (urlRaw && /^https?:\/\//i.test(urlRaw)) {
            try {
                var parsed = new URL(urlRaw);
                var origin = typeof window.location !== 'undefined' ? window.location.origin : parsed.origin;
                var pathFixed = bankReconFixPathnameForMatchFiles(parsed.pathname);
                return origin + pathFixed + (parsed.search || '') + (parsed.hash || '');
            } catch (e1) {
                return urlRaw;
            }
        }

        if (!urlRaw || urlRaw === '#') {
            return '#';
        }
        if (urlRaw.charAt(0) === '/') {
            var originRoot = typeof window.location !== 'undefined' ? window.location.origin : '';
            return originRoot + bankReconFixPathnameForMatchFiles(urlRaw);
        }

        return bankReconBuildPublicFileUrl(urlRaw);
    }

    function brAttFileIconClass(ext) {
        if (['jpg','jpeg','png','gif','webp','bmp','svg'].indexOf(ext) !== -1) return 'bi-file-earmark-image br-att-icon-img';
        if (ext === 'pdf') return 'bi-file-earmark-pdf br-att-icon-pdf';
        if (['doc','docx'].indexOf(ext) !== -1) return 'bi-file-earmark-word br-att-icon-word';
        if (['xls','xlsx','csv'].indexOf(ext) !== -1) return 'bi-file-earmark-spreadsheet br-att-icon-excel';
        return 'bi-file-earmark br-att-icon-generic';
    }

    $(document).on('click', '.br-income-slot-att', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '.bank-recon-att-view', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var sid = $(this).data('stmt-id');
        var files = (window.bankReconMatchFiles && window.bankReconMatchFiles[sid]) ? window.bankReconMatchFiles[sid] : [];
        var $body = $('#bankMatchAttachmentsViewerBody');
        $body.empty();
        if (!files.length) {
            $body.html(
                '<div class="br-att-empty">' +
                '<i class="bi bi-folder2-open br-att-empty-icon"></i>' +
                '<div class="br-att-empty-text">No attachments uploaded</div>' +
                '</div>'
            );
        } else {
            files.forEach(function (f, idx) {
                var url = bankReconResolveAttachmentUrl(f);
                var name = (f && f.name) ? String(f.name) : 'File';
                var tag = (f && f.tag) ? String(f.tag).trim() : '';
                var ext = name.split('.').pop().toLowerCase();
                var iconClass = brAttFileIconClass(ext);
                var tagChip = tag
                    ? '<span class="br-att-tag-chip">' + escapeAttr(tag) + '</span>'
                    : '';
                var preview = '';
                if (['jpg','jpeg','png','gif','webp','bmp','svg'].indexOf(ext) !== -1) {
                    preview = '<div class="br-att-preview-box">' +
                        '<img src="' + escapeAttr(url) + '" alt="" class="br-att-preview-img">' +
                        '</div>';
                } else if (ext === 'pdf') {
                    preview = '<div class="br-att-preview-box br-att-preview-pdf">' +
                        '<embed src="' + escapeAttr(url) + '" type="application/pdf" width="100%" height="460px" class="d-block"></embed>' +
                        '</div>';
                } else {
                    preview = '<div class="br-att-preview-box br-att-preview-unavail">' +
                        '<i class="bi bi-download br-att-unavail-icon"></i>' +
                        '<div class="br-att-unavail-text">Preview not available</div>' +
                        '<div class="br-att-unavail-sub">Use the Open button to download this file</div>' +
                        '</div>';
                }
                $body.append(
                    '<div class="br-att-item' + (idx > 0 ? ' br-att-item-sep' : '') + '">' +
                    '<div class="br-att-item-header">' +
                    '<div class="br-att-file-meta">' +
                    '<span class="br-att-file-icon"><i class="bi ' + iconClass + '"></i></span>' +
                    '<div class="br-att-file-info">' +
                    '<div class="br-att-file-name">' + escapeAttr(name) + '</div>' +
                    (tagChip ? '<div class="br-att-file-tag">' + tagChip + '</div>' : '') +
                    '</div>' +
                    '</div>' +
                    '<a href="' + escapeAttr(url) + '" target="_blank" rel="noopener" class="br-att-open-btn"><i class="bi bi-box-arrow-up-right me-1"></i>Open</a>' +
                    '</div>' +
                    preview +
                    '</div>'
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
                    refreshStatementRow(id);
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
                            refreshStatementRow(id);
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
                            refreshStatementRow(id);
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

    /** Payload for read-only income tag modal (row attr or quick button on card). */
    function buildBankReconIncomeTagReadonlyPayload(stmt, amount, amountType) {
        var splitRo = parseIncomeMatchSplitJson(stmt);
        var modesDisp = [];
        if (
            stmt.income_tag_billing_modes &&
            Array.isArray(stmt.income_tag_billing_modes) &&
            stmt.income_tag_billing_modes.length
        ) {
            modesDisp = stmt.income_tag_billing_modes
                .map(function (m) {
                    return String(m).trim();
                })
                .filter(Boolean);
        } else if (splitRo && Array.isArray(splitRo.modes)) {
            modesDisp = splitRo.modes
                .map(function (m) {
                    return String(m).trim();
                })
                .filter(Boolean);
        }
        return {
            branch: stmt.income_matched_branch || '',
            matchedDate: stmt.income_matched_date || '',
            matchedAt: stmt.income_matched_at || '',
            byName: stmt.income_matched_by_name || '',
            reconId: stmt.income_reconciliation_id,
            txnDate: stmt.transaction_date,
            ref: stmt.reference_number || '',
            desc: String(stmt.description || '').substring(0, 500),
            amount: amount,
            amountType: amountType,
            split: splitRo,
            modesDisplay: modesDisp,
            radiantLocation: stmt.radiant_matched_location || '',
            radiantPickupDate: stmt.radiant_matched_pickup_date || '',
            radiantMatchedAt: stmt.radiant_matched_at || '',
            radiantByName: stmt.radiant_matched_by_name || '',
            radiantPickupId: stmt.radiant_cash_pickup_id || '',
            radiantKeyword: stmt.radiant_match_against || '',
        };
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
                var tagSuffix = (isLast && tagLine)
                    ? '<span class="br-income-doc-tag-time">' + escapeAttr(tagLine) + '</span>'
                    : '';
                html +=
                    '<div class="br-income-doc-row">' +
                        '<span class="br-income-doc-label">MDOC</span>' +
                        '<span class="br-income-doc-date">' + escapeAttr(dmy) + '</span>' +
                        '<span class="br-income-doc-sep">—</span>' +
                        '<span class="br-income-doc-amt">₹' + formatNumber(part) + '</span>' +
                    '</div>' +
                    (tagSuffix ? '<div class="br-income-doc-tag-row">' + tagSuffix + '</div>' : '');
            });
            return html;
        }

        var coll = (stmt.income_matched_date || '').toString().trim() || '—';
        return '<div class="br-income-doc-row">' +
                    '<span class="br-income-doc-label">MDOC</span>' +
                    '<span class="br-income-doc-date">' + escapeAttr(coll) + '</span>' +
                    '<span class="br-income-doc-sep">—</span>' +
                    '<span class="br-income-doc-amt">₹' + formatNumber(lineAmt) + '</span>' +
               '</div>';
    }

    /** Compact “Mode: CASH · UPI” line for the income tag card (uses server modes when present). */
    function buildIncomeTagModesLineHtml(stmt) {
        var modes = [];
        if (stmt.income_tag_billing_modes && Array.isArray(stmt.income_tag_billing_modes)) {
            modes = stmt.income_tag_billing_modes
                .map(function (m) {
                    return String(m).trim();
                })
                .filter(Boolean);
        }
        if (!modes.length) {
            var sp = parseIncomeMatchSplitJson(stmt);
            if (sp && Array.isArray(sp.modes)) {
                modes = sp.modes
                    .map(function (m) {
                        return String(m).trim();
                    })
                    .filter(Boolean);
            }
        }
        if (!modes.length) {
            return '';
        }
        var label = modes
            .map(function (m) {
                return String(m).toUpperCase();
            })
            .join(' · ');
        return (
            '<div class="br-income-tag-modes br-income-tag-modes--highlight small mt-1">' +
            '<span class="br-income-tag-modes-lbl"><i class="bi bi-wallet2 me-1"></i>Mode:</span> ' +
            '<span class="br-income-tag-modes-val">' +
            escapeAttr(label) +
            '</span></div>'
        );
    }

    /** Fill and show read-only income tag modal (payload from row data-income-ro). */
    function openIncomeTagReadonlyModal(payload) {
        if (!payload) {
            toastr.warning('No income tag data');
            return;
        }
        var lineAmt = parseFloat(payload.amount) || 0;
        var split = payload.split || null;
        var ymds = split && Array.isArray(split.dates_ymd) ? split.dates_ymd.slice().sort() : [];
        var amts = split && split.amounts_ymd ? split.amounts_ymd : null;
        var modes = [];
        if (payload.modesDisplay && Array.isArray(payload.modesDisplay) && payload.modesDisplay.length) {
            modes = payload.modesDisplay.map(function (m) {
                return String(m).trim();
            }).filter(Boolean);
        } else if (split && Array.isArray(split.modes)) {
            modes = split.modes.map(function (m) {
                return String(m).trim();
            }).filter(Boolean);
        }

        var html = '';
        html += '<div class="br-income-ro">';
        html += '<section class="br-income-ro-summary" aria-label="Bank line summary">';
        html += '<div class="br-income-ro-summary-grid">';
        html += '<div class="br-income-ro-summary-cell">';
        html += '<span class="br-income-ro-summary-lbl">Bank transaction date</span>';
        html += '<strong class="br-income-ro-summary-val">' + escapeAttr(formatDate(payload.txnDate)) + '</strong>';
        html += '</div>';
        html += '<div class="br-income-ro-summary-cell br-income-ro-summary-cell--amt">';
        html += '<span class="br-income-ro-summary-lbl">Line amount</span>';
        html += '<strong class="br-income-ro-summary-val br-income-ro-summary-val--lg">₹' + formatNumber(lineAmt) + '</strong>';
        html += '</div>';
        html += '</div>';
        html += '<div class="br-income-ro-summary-row">';
        html += '<span class="br-income-ro-summary-lbl">Reference</span>';
        html += '<span class="br-income-ro-summary-txt text-break">' + escapeAttr(payload.ref || '—') + '</span>';
        html += '</div>';
        html += '<div class="br-income-ro-summary-row">';
        html += '<span class="br-income-ro-summary-lbl">Description</span>';
        html += '<span class="br-income-ro-summary-txt text-break">' + escapeAttr(payload.desc || '—') + '</span>';
        html += '</div>';
        html += '</section>';

        html += '<div class="br-income-ro-card">';
        html += '<label class="income-tag-label br-income-ro-sec-label"><span class="income-tag-label-dot" style="background:#10b981;"></span>BRANCH</label>';
        html += '<input type="text" class="form-control form-control-sm br-income-ro-input" readonly value="' +
            escapeAttr(payload.branch || '') + '">';
        html += '</div>';

        html += '<div class="br-income-ro-card">';
        html += '<label class="income-tag-label br-income-ro-sec-label"><span class="income-tag-label-dot" style="background:#f59e0b;"></span>COLLECTION DATE(S) &amp; SPLIT</label>';
        if (ymds.length > 1 && amts) {
            html += '<div class="table-responsive br-income-ro-table-wrap">';
            html += '<table class="table table-sm mb-0 br-income-ro-table">';
            html += '<thead><tr><th>Collection date</th><th class="text-end">Bank split (₹)</th></tr></thead><tbody>';
            ymds.forEach(function (ymd) {
                var m = moment(ymd, 'YYYY-MM-DD', true);
                var dmy = m.isValid() ? m.format('DD/MM/YYYY') : String(ymd);
                var part = parseFloat(amts[ymd]);
                if (!isFinite(part)) {
                    part = 0;
                }
                html += '<tr><td>' + escapeAttr(dmy) + '</td><td class="text-end">₹' + formatNumber(part) + '</td></tr>';
            });
            html += '</tbody></table></div>';
        } else {
            var singleLine = (payload.matchedDate || '').toString().trim() + ' — ₹' + formatNumber(lineAmt);
            html += '<input type="text" class="form-control form-control-sm br-income-ro-input" readonly value="' +
                escapeAttr(singleLine) + '">';
        }
        html += '</div>';

        html += '<div class="br-income-ro-card br-income-ro-card--last">';
        html += '<label class="income-tag-label br-income-ro-sec-label"><span class="income-tag-label-dot" style="background:#22c55e;"></span>MODE OF COLLECTION</label>';
        html += '<div class="br-income-ro-modes">';
        if (modes.length) {
            modes.forEach(function (m) {
                html += '<span class="badge br-income-ro-mode-badge">' + escapeAttr(String(m).toUpperCase()) + '</span>';
            });
        } else {
            html += '<input type="text" class="form-control form-control-sm br-income-ro-input" readonly value="—">';
        }
        html += '</div>';
        html += '</div>';

        html += '<div class="br-income-ro-meta-highlight">';
        html += '<div class="br-income-ro-meta-item">';
        html += '<span class="br-income-ro-meta-ico" aria-hidden="true"><i class="bi bi-clock-history"></i></span>';
        html += '<div class="br-income-ro-meta-body">';
        html += '<span class="br-income-ro-meta-k">Income tagged at</span>';
        html += '<span class="br-income-ro-meta-v">' +
            escapeAttr(payload.matchedAt ? formatDateTime(payload.matchedAt) : '—') + '</span>';
        html += '</div></div>';
        html += '<div class="br-income-ro-meta-item">';
        html += '<span class="br-income-ro-meta-ico" aria-hidden="true"><i class="bi bi-person-check"></i></span>';
        html += '<div class="br-income-ro-meta-body">';
        html += '<span class="br-income-ro-meta-k">Income tagged by</span>';
        html += '<span class="br-income-ro-meta-v">' + escapeAttr(payload.byName || '—') + '</span>';
        html += '</div></div>';
        html += '</div>';

        if (payload.radiantMatchedAt || payload.radiantByName || payload.radiantLocation || payload.radiantPickupId) {
            var slipLine = [payload.radiantLocation, payload.radiantPickupDate]
                .map(function (x) {
                    return (x || '').toString().trim();
                })
                .filter(Boolean)
                .join(' · ');
            html += '<section class="br-income-ro-radiant-sec" aria-label="Radiant pickup">';
            html += '<div class="br-income-ro-radiant-hd"><i class="bi bi-brightness-high-fill me-2"></i>Radiant pickup</div>';
            html += '<div class="br-income-ro-card">';
            html += '<label class="income-tag-label br-income-ro-sec-label"><span class="income-tag-label-dot" style="background:#f97316;"></span>LOCATION / SLIP</label>';
            html +=
                '<input type="text" class="form-control form-control-sm br-income-ro-input" readonly value="' +
                escapeAttr(slipLine || '—') +
                '">';
            if (payload.radiantPickupId) {
                html +=
                    '<div class="small text-muted mt-1">Pickup ID: <code>' +
                    escapeAttr(String(payload.radiantPickupId)) +
                    '</code></div>';
            }
            if (payload.radiantKeyword) {
                html +=
                    '<div class="small text-muted mt-1">Keyword: ' +
                    escapeAttr(String(payload.radiantKeyword)) +
                    '</div>';
            }
            html += '</div>';
            html += '<div class="br-income-ro-meta-highlight br-income-ro-meta-highlight--radiant">';
            html += '<div class="br-income-ro-meta-item">';
            html += '<span class="br-income-ro-meta-ico" aria-hidden="true"><i class="bi bi-clock-history"></i></span>';
            html += '<div class="br-income-ro-meta-body">';
            html += '<span class="br-income-ro-meta-k">Radiant linked at</span>';
            html +=
                '<span class="br-income-ro-meta-v">' +
                escapeAttr(payload.radiantMatchedAt ? formatDateTime(payload.radiantMatchedAt) : '—') +
                '</span>';
            html += '</div></div>';
            html += '<div class="br-income-ro-meta-item">';
            html += '<span class="br-income-ro-meta-ico" aria-hidden="true"><i class="bi bi-person-check"></i></span>';
            html += '<div class="br-income-ro-meta-body">';
            html += '<span class="br-income-ro-meta-k">Radiant linked by</span>';
            html += '<span class="br-income-ro-meta-v">' + escapeAttr(payload.radiantByName || '—') + '</span>';
            html += '</div></div>';
            html += '</div>';
            html += '</section>';
        }

        html += '</div>';

        $('#incomeTagReadonlyBody').html(html);
        $('#incomeTagReadonlyModal .modal-title').html(
            '<span class="br-income-ro-header-icon"><i class="bi bi-shield-lock"></i></span>' +
                '<span>Income &amp; Radiant <span class="small fw-normal opacity-90">(read-only)</span></span>'
        );
        $('#incomeTagReadonlyModal').modal('show');
    }

    $('#incomeTagReadonlyModal').on('hidden.bs.modal', function () {
        $('#incomeTagReadonlyModal .modal-title').html(
            '<span class="br-income-ro-header-icon"><i class="bi bi-tag-fill"></i></span>Income tag details'
        );
    });
    
    function truncateText(text, length) {
        if (!text) return '-';
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
    
    function bankReconHasSalaryTag(stmt) {
        if (!stmt) {
            return false;
        }
        return !!(
            stmt.salary_recon_row_id
            || stmt.bank_recon_salary_row_id
            || stmt.salary_matched_at
            || stmt.salary_row_matched_at
        );
    }

    function bankReconSalaryDisplayAt(stmt) {
        if (!stmt) {
            return '';
        }
        if (stmt.salary_matched_at) {
            return String(stmt.salary_matched_at);
        }
        if (stmt.salary_row_matched_at) {
            return String(stmt.salary_row_matched_at);
        }
        return '';
    }

    function buildBankReconSalaryReadonlyPayload(stmt, amount, amountType) {
        var at = bankReconSalaryDisplayAt(stmt);
        var byName = (stmt.salary_stmt_matched_by_name || stmt.salary_uploaded_by_name || '').toString().trim();
        var byUser = (stmt.salary_stmt_matched_by_username || stmt.salary_uploaded_by_username || '').toString().trim();
        return {
            id: stmt.id,
            accountNumber: (stmt.bank_account_number || '').toString(),
            bankName: (stmt.bank_account_bank_name || '').toString(),
            transactionDate: (stmt.transaction_date || '').toString(),
            valueDate: (stmt.value_date || '').toString(),
            description: (stmt.description || '').toString(),
            amount: amount,
            amountType: amountType,
            netPaid: stmt.salary_net_paid,
            utr: (stmt.salary_utr || '').toString(),
            ecId: (stmt.salary_ec_id || '').toString(),
            employeeName: (stmt.salary_employee_name || '').toString(),
            designation: (stmt.salary_designation || '').toString(),
            branch: (stmt.salary_branch || '').toString(),
            employeeCategory: (stmt.salary_employee_category || '').toString(),
            fileName: (stmt.salary_upload_file_name || '').toString(),
            fileUploadedAt: (stmt.salary_uploaded_at || '').toString(),
            matchedByName: byName,
            matchedByUsername: byUser,
            matchedAt: at,
            category: (stmt.category || '').toString(),
        };
    }

    function openBankReconSalaryReadonlyModal(p) {
        if (!p || !$('#bankReconSalaryReadonlyBody').length) {
            toastr.error('Could not show salary view.');
            return;
        }
        var mAt    = p.matchedAt      ? formatDateTime(p.matchedAt)      : '—';
        var fuAt   = p.fileUploadedAt ? formatDateTime(p.fileUploadedAt) : '—';
        var net    = p.netPaid != null ? formatNumber(p.netPaid) : '0';
        var empName = escapeAttr(p.employeeName || '—');

        // Update subtitle / title
        if ($('#bankReconSalaryReadonlySubtitle').length) {
            $('#bankReconSalaryReadonlySubtitle').text(empName + ' · ₹' + net);
        }

        var html = '';

        // ── Employee hero banner ──
        html += '<div class="br-salary-ro-hero">' +
            '<div class="br-salary-ro-hero-left">' +
                '<div class="br-salary-ro-avatar"><i class="bi bi-person-badge"></i></div>' +
                '<div>' +
                    '<div class="br-salary-ro-name">' + empName + '</div>' +
                    '<div class="br-salary-ro-meta">' +
                        (p.designation ? '<span class="br-salary-ro-tag">' + escapeAttr(p.designation) + '</span>' : '') +
                        (p.branch      ? '<span class="br-salary-ro-tag"><i class="bi bi-geo-alt me-1"></i>' + escapeAttr(p.branch) + '</span>' : '') +
                        (p.employeeCategory ? '<span class="br-salary-ro-tag">' + escapeAttr(p.employeeCategory) + '</span>' : '') +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="br-salary-ro-amount">₹' + net + '<div class="br-salary-ro-amount-lbl">Net paid</div></div>' +
        '</div>';

        // ── Identity row ──
        html += '<div class="br-salary-ro-grid mt-3">';
        if (p.ecId) {
            html += '<div class="br-salary-ro-kv"><div class="br-salary-ro-k">EC ID</div><div class="br-salary-ro-v"><code>' + escapeAttr(p.ecId) + '</code></div></div>';
        }
        if (p.utr) {
            html += '<div class="br-salary-ro-kv br-salary-ro-kv--wide"><div class="br-salary-ro-k">UTR</div><div class="br-salary-ro-v"><code class="br-utr-code">' + escapeAttr(p.utr) + '</code></div></div>';
        }
        html += '</div>';

        // ── Bank transaction ──
        html += '<div class="br-salary-ro-sec mt-3">' +
            '<div class="br-salary-ro-sec-t"><i class="bi bi-bank2 me-1"></i>Bank transaction</div>' +
            '<div class="br-salary-ro-grid">' +
                '<div class="br-salary-ro-kv"><div class="br-salary-ro-k">Date</div><div class="br-salary-ro-v">' + escapeAttr(p.transactionDate || '—') + '</div></div>' +
                '<div class="br-salary-ro-kv"><div class="br-salary-ro-k">Amount</div><div class="br-salary-ro-v text-success fw-bold">₹' + formatNumber(p.amount) + ' <span class="text-muted small">(' + escapeAttr(p.amountType) + ')</span></div></div>' +
                '<div class="br-salary-ro-kv br-salary-ro-kv--wide"><div class="br-salary-ro-k">Description</div><div class="br-salary-ro-v small text-muted">' + escapeAttr(p.description || '—') + '</div></div>' +
            '</div>' +
        '</div>';

        // ── Tagging info ──
        html += '<div class="br-salary-ro-sec mt-3">' +
            '<div class="br-salary-ro-sec-t"><i class="bi bi-person-check me-1"></i>Matched by</div>' +
            '<div class="br-salary-ro-matched-row">' +
                '<div class="br-salary-ro-matched-who">' +
                    '<i class="bi bi-person-circle me-1 text-muted"></i>' +
                    '<strong>' + escapeAttr(p.matchedByName || '—') + '</strong>' +
                    (p.matchedByUsername ? ' <span class="text-muted small font-monospace">@' + escapeAttr(p.matchedByUsername) + '</span>' : '') +
                '</div>' +
                '<div class="br-salary-ro-matched-at text-muted small"><i class="bi bi-clock me-1"></i>' + escapeAttr(mAt) + '</div>' +
            '</div>' +
        '</div>';

        // ── Source file ──
        if (p.fileName) {
            html += '<div class="br-salary-ro-file-row mt-2">' +
                '<i class="bi bi-file-earmark-excel text-success me-1"></i>' +
                '<span class="small">' + escapeAttr(p.fileName) + '</span>' +
                (fuAt !== '—' ? '<span class="text-muted small ms-2">(uploaded ' + escapeAttr(fuAt) + ')</span>' : '') +
            '</div>';
        }

        $('#bankReconSalaryReadonlyBody').html(html);

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var el = document.getElementById('bankReconSalaryReadonlyModal');
            if (el) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            }
        } else {
            $('#bankReconSalaryReadonlyModal').modal('show');
        }
    }

    function getMatchStatusBadge(stmt) {
        if (bankReconHasSalaryTag(stmt)) {
            return '<span class="badge br-badge-salary-status text-white"><i class="bi bi-cash-coin me-1"></i>Salary matched</span>';
        }
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
        if (bankReconHasSalaryTag(stmt) || (stmt.category && String(stmt.category).toLowerCase() === 'salary')) {
            return '<span class="category-badge br-cat-salary text-white"><i class="bi bi-cash-coin me-1"></i>Salary</span>';
        }
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
            var preRadiant = window._brPendingRadiantPickupId || '';
            window._brPendingRadiantPickupId = '';
            if (bankReconDepositRadiantOnly) {
                loadRadiantCashPickupOptionsForCurrentTxn(preRadiant);
                return;
            }
            loadIncomeTagZones();
            initIncomeTagFlatpickr();
            resetIncomeTagDateToToday();
            rebuildIncomeTagSplitRows();
            updateIncomeTagSummary();

            if (currentTxnData.description) {
                autoResolveIncomeTagFromDescription(currentTxnData.description, currentTxnData.date);
            }

            loadRadiantCashPickupOptionsForCurrentTxn(preRadiant);
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
        bankReconDepositRadiantOnly = false;
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
        destroyRadiantPickupSelect2();
        var $rSel = $('#radiantCashPickupSelect');
        if ($rSel.length) {
            $rSel.empty()
                .append($('<option>', { value: '', text: '— Open a deposit row to load pickups —' }))
                .prop('disabled', true)
                .show();
        }
        var $rLoading = $('#radiantPickupLoading');
        if ($rLoading.length) {
            $rLoading.hide();
        }
        var $rMeta = $('#radiantPickupMeta');
        if ($rMeta.length) {
            $rMeta.hide();
        }
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

    /** While Swal is open, Bootstrap’s #matchTransactionModal can still paint above it (inline z-index / stacking). */
    var brIncomeTagSwalZStash = null;

    function brPushBootstrapLayersBehindIncomeTagSwal() {
        brIncomeTagSwalZStash = [];
        var pushEl = function (el) {
            if (!el || !el.style) {
                return;
            }
            brIncomeTagSwalZStash.push({
                el: el,
                z: el.style.zIndex,
                prio: el.style.getPropertyPriority('z-index'),
            });
            el.style.setProperty('z-index', '1030', 'important');
        };
        pushEl(document.getElementById('matchTransactionModal'));
        document.querySelectorAll('.modal-backdrop.show').forEach(function (bd) {
            pushEl(bd);
        });
    }

    function brRestoreBootstrapLayersAfterIncomeTagSwal() {
        if (!brIncomeTagSwalZStash || !brIncomeTagSwalZStash.length) {
            return;
        }
        brIncomeTagSwalZStash.forEach(function (x) {
            if (!x.el) {
                return;
            }
            if (x.z === '' || x.z == null) {
                x.el.style.removeProperty('z-index');
            } else {
                x.el.style.setProperty('z-index', x.z, x.prio || '');
            }
        });
        brIncomeTagSwalZStash = null;
    }

    function brRaiseIncomeTagSwalContainer() {
        var c = document.querySelector('.swal2-container');
        if (c) {
            c.style.setProperty('z-index', '2147483647', 'important');
        }
    }

    /**
     * Bootstrap 5’s modal focus trap keeps focus inside #matchTransactionModal. SweetAlert2 mounts on
     * <body>, so clicks on the MOC remark textarea never receive focus — pause the trap while Swal is open.
     */
    var brMatchModalFocusTrapResume = null;

    function brPauseMatchModalFocusTrap() {
        brMatchModalFocusTrapResume = null;
        var el = document.getElementById('matchTransactionModal');
        if (!el) {
            return;
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inst = bootstrap.Modal.getInstance(el);
            if (inst) {
                var ft = inst._focustrap;
                if (ft && typeof ft.deactivate === 'function') {
                    try {
                        ft.deactivate();
                        brMatchModalFocusTrapResume = function () {
                            try {
                                if (typeof ft.activate === 'function') {
                                    ft.activate();
                                }
                            } catch (eAct) { /* ignore */ }
                        };
                        return;
                    } catch (e) { /* ignore */ }
                }
            }
        }
        /* Fallback: BS build without _focustrap or trap not exposed — block modal subtree from grabbing focus */
        if (!el.hasAttribute('inert')) {
            el.setAttribute('inert', '');
            brMatchModalFocusTrapResume = function () {
                el.removeAttribute('inert');
            };
        }
    }

    function brResumeMatchModalFocusTrap() {
        if (typeof brMatchModalFocusTrapResume === 'function') {
            try {
                brMatchModalFocusTrapResume();
            } catch (e) { /* ignore */ }
        }
        brMatchModalFocusTrapResume = null;
    }

    function showIncomeTagMocMismatchDialog(mismatches, message) {
        if (!mismatches || !mismatches.length) {
            toastr.error(message || 'MOC amount check failed');
            return;
        }

        if (typeof Swal === 'undefined') {
            if (!window.confirm(
                'MOC DOC amount differs from the bank tag amount.\n\nYou must confirm and enter a remark to proceed.'
            )) {
                return;
            }
            var fbRemark = window.prompt('Enter a remark (required, stored on income reconciliation for this date):', '');
            fbRemark = (fbRemark || '').trim();
            if (!fbRemark.length) {
                toastr.error('Remark is required when acknowledging a MOC amount mismatch.');
                return;
            }
            submitIncomeTagWithData(true, fbRemark);
            return;
        }

        /* ---- build mismatch cards ---- */
        var modeIconMap = {
            cash: 'bi-cash-stack',
            card: 'bi-credit-card-2-front',
            upi:  'bi-phone',
            neft: 'bi-bank',
            other:'bi-three-dots',
        };
        var modeColorMap = {
            cash:  '#10b981',
            card:  '#6366f1',
            upi:   '#f59e0b',
            neft:  '#0ea5e9',
            other: '#94a3b8',
        };

        var cards = mismatches.map(function (m) {
            var mode   = String(m.mode || '').toLowerCase();
            var modeUp = mode.toUpperCase();
            var icon   = modeIconMap[mode]  || 'bi-currency-rupee';
            var color  = modeColorMap[mode] || '#6366f1';
            var diff   = m.diff;
            var diffSign = diff >= 0 ? '+' : '';
            var diffColor = diff === 0 ? '#94a3b8' : (diff > 0 ? '#f59e0b' : '#ef4444');
            var diffLabel = diff > 0
                ? 'Bank amount is <strong>higher</strong> than MOC'
                : (diff < 0 ? 'Bank amount is <strong>lower</strong> than MOC' : 'No difference');

            return (
                '<div class="br-moc-mismatch-card">' +
                  '<div class="br-moc-card-header" style="--br-moc-color:' + color + '">' +
                    '<span class="br-moc-card-mode-icon"><i class="bi ' + icon + '"></i></span>' +
                    '<span class="br-moc-card-mode-label">' + escapeAttr(modeUp) + '</span>' +
                    '<span class="br-moc-card-date"><i class="bi bi-calendar3 me-1"></i>' +
                      escapeAttr(m.date_display || m.date_ymd || '') +
                    '</span>' +
                  '</div>' +
                  '<div class="br-moc-card-body">' +
                    '<div class="br-moc-amt-row">' +
                      '<div class="br-moc-amt-block br-moc-amt-moc">' +
                        '<span class="br-moc-amt-label">MOC DOC</span>' +
                        '<span class="br-moc-amt-value">₹' + formatNumber(m.moc_amount) + '</span>' +
                      '</div>' +
                      '<div class="br-moc-vs-divider"><i class="bi bi-arrow-left-right"></i></div>' +
                      '<div class="br-moc-amt-block br-moc-amt-bank">' +
                        '<span class="br-moc-amt-label">Tag (Bank)</span>' +
                        '<span class="br-moc-amt-value">₹' + formatNumber(m.tag_amount) + '</span>' +
                      '</div>' +
                    '</div>' +
                    '<div class="br-moc-diff-row" style="color:' + diffColor + '">' +
                      '<span class="br-moc-diff-chip" style="background:' + diffColor + '20;color:' + diffColor + ';border-color:' + diffColor + '40">' +
                        'Diff: ' + diffSign + '₹' + formatNumber(Math.abs(diff)) +
                      '</span>' +
                      '<span class="br-moc-diff-note">' + diffLabel + '</span>' +
                    '</div>' +
                  '</div>' +
                '</div>'
            );
        }).join('');

        var html =
            '<div class="br-moc-swal-wrap">' +
              '<div class="br-moc-swal-intro">' +
                '<i class="bi bi-exclamation-triangle-fill br-moc-swal-warn-icon"></i>' +
                '<div>' +
                  '<p class="br-moc-swal-intro-title">Amount mismatch detected</p>' +
                  '<p class="br-moc-swal-intro-sub">The <strong>MOC DOC</strong> total does not match the <strong>bank line</strong> you are tagging. Review each difference below, then decide whether to proceed.</p>' +
                '</div>' +
              '</div>' +
              '<div class="br-moc-cards-list">' + cards + '</div>' +
              '<div class="br-moc-swal-footer-note">' +
                '<i class="bi bi-shield-exclamation"></i>' +
                'Proceeding will tag this bank line against the MOC figures shown above.' +
              '</div>' +
              '<div class="br-moc-ack-section">' +
                '<label class="br-moc-ack-label" for="br-moc-ack-cb">' +
                  '<input type="checkbox" id="br-moc-ack-cb" class="form-check-input br-moc-ack-cb">' +
                  '<span>I understand that MOC DOC amounts differ from the bank amounts I am tagging, and I agree to proceed.</span>' +
                '</label>' +
                '<div id="br-moc-remark-wrap" class="br-moc-remark-wrap" style="display:none;">' +
                  '<label for="br-moc-remark-ta" class="br-moc-remark-label">Remark <span class="text-danger">*</span></label>' +
                  '<textarea id="br-moc-remark-ta" class="form-control form-control-sm br-moc-remark-ta" rows="3" maxlength="2000" placeholder="Required: explain why you are tagging despite the mismatch. This is stored on the income reconciliation row for this collection date."></textarea>' +
                  '<div class="form-text br-moc-remark-hint">Max 2000 characters. Required for audit trail.</div>' +
                '</div>' +
              '</div>' +
            '</div>';

        Swal.fire({
            title: '',
            html: html,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check2-circle me-1"></i>Apply income tag',
            cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Cancel',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            focusCancel: false,
            heightAuto: false,
            width: '660px',
            padding: '0',
            scrollbarPadding: false,
            customClass: {
                popup:   'br-moc-swal-popup',
                actions: 'br-moc-swal-actions',
                confirmButton: 'br-moc-swal-confirm-btn',
                cancelButton:  'br-moc-swal-cancel-btn',
            },
            showClass: { popup: 'swal2-show', backdrop: 'swal2-backdrop-show' },
            zIndex: 2147483647,
            allowOutsideClick: false,
            willOpen: function () {
                brPushBootstrapLayersBehindIncomeTagSwal();
            },
            didOpen: function () {
                brRaiseIncomeTagSwalContainer();
                /* Must run after Swal mounts so Bootstrap modal trap releases the textarea */
                brPauseMatchModalFocusTrap();
                var popup = typeof Swal.getPopup === 'function' ? Swal.getPopup() : null;
                if (popup) {
                    popup.style.overflow = 'visible';
                }
                var htc = typeof Swal.getHtmlContainer === 'function' ? Swal.getHtmlContainer() : null;
                if (htc) {
                    htc.style.overflow = 'visible';
                    htc.style.maxHeight = 'none';
                }
                var stopSwalBubble = function (e) {
                    e.stopPropagation();
                };
                var cb = document.getElementById('br-moc-ack-cb');
                var wrap = document.getElementById('br-moc-remark-wrap');
                var ta = document.getElementById('br-moc-remark-ta');
                if (ta) {
                    ta.removeAttribute('readonly');
                    ta.removeAttribute('disabled');
                    ta.setAttribute('tabindex', '0');
                    ta.addEventListener('mousedown', stopSwalBubble, true);
                    ta.addEventListener('touchstart', stopSwalBubble, { passive: true, capture: true });
                }
                if (cb) {
                    cb.addEventListener('mousedown', stopSwalBubble, true);
                }
                if (cb && wrap) {
                    cb.addEventListener('change', function () {
                        wrap.style.display = cb.checked ? 'block' : 'none';
                        if (!cb.checked && ta) {
                            ta.value = '';
                        }
                        if (typeof Swal.resetValidationMessage === 'function') {
                            Swal.resetValidationMessage();
                        }
                        if (cb.checked && ta) {
                            setTimeout(function () {
                                try {
                                    ta.focus();
                                } catch (eF) { /* ignore */ }
                            }, 50);
                        }
                    });
                }
            },
            didClose: function () {
                brResumeMatchModalFocusTrap();
                brRestoreBootstrapLayersAfterIncomeTagSwal();
            },
            preConfirm: function () {
                var cb = document.getElementById('br-moc-ack-cb');
                var ta = document.getElementById('br-moc-remark-ta');
                if (!cb || !cb.checked) {
                    if (typeof Swal.showValidationMessage === 'function') {
                        Swal.showValidationMessage('Tick the agreement checkbox to continue.');
                    }
                    return false;
                }
                var r = (ta && ta.value ? ta.value : '').trim();
                if (!r.length) {
                    if (typeof Swal.showValidationMessage === 'function') {
                        Swal.showValidationMessage('Enter a remark before applying the income tag.');
                    }
                    return false;
                }
                return r;
            },
        }).then(function (result) {
            if (result.isConfirmed && result.value) {
                submitIncomeTagWithData(true, result.value);
            }
        });
    }

    function submitIncomeTagWithData(acknowledgeMismatch, mismatchRemark) {
        if (incomeTagInFlight) {
            return;
        }
        var zoneName = ($('#incomeTagZoneName').val() || '').trim();
        var branchName = ($('#incomeTagBranchName').val() || '').trim();
        var modes = Array.from(incomeTagSelectedModes);
        var dates = getIncomeTagSelectedYmdSorted();
        if (!dates.length && currentTxnData.date) {
            dates = [moment(currentTxnData.date).format('YYYY-MM-DD')];
        }

        if (!zoneName) {
            toastr.warning('Please select a Zone');
            return;
        }
        if (!branchName) {
            toastr.warning('Please select a Branch');
            return;
        }
        if (!dates.length) {
            toastr.warning('Select at least one collection date');
            return;
        }
        if (!modes.length) {
            toastr.warning('Please select at least one Mode of Collection');
            return;
        }
        if (!currentStatementId) {
            toastr.warning('No bank statement selected');
            return;
        }
        if (!incomeTagStagingFiles.length || !incomeTagStagingFiles.some(function (it) { return it && it.file; })) {
            toastr.warning('Add supporting documents for income tag.');
            return;
        }
        if (!incomeStagingHasMandatorySlots()) {
            toastr.warning(
                incomeTagRequiresCashFullAttachmentPack()
                    ? 'Cash only: add three files and set types — MOCDOC COLLECTION SCREEN SHOT, RADIANT SLIP, COLLECTION LEDGER (one each).'
                    : 'Add a MOCDOC COLLECTION SCREEN SHOT file and set its document type (Radiant slip and ledger are not required unless mode is Cash only).'
            );
            return;
        }

        if (dates.length > 1) {
            var damap = collectIncomeTagDateAmountsMap();
            var lineTotal = parseFloat(currentTxnData.amount) || 0;
            var splitSum = 0;
            dates.forEach(function (d) {
                splitSum += parseFloat(damap[d]) || 0;
            });
            if (Math.abs(splitSum - lineTotal) > 0.05) {
                toastr.warning(
                    'Per-date amounts must sum to the bank line (₹' +
                        formatNumber(lineTotal) +
                        '). Current sum: ₹' +
                        formatNumber(splitSum)
                );
                return;
            }
        }

        var fd = new FormData();
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        fd.append('bank_statement_id', currentStatementId);
        fd.append('zone', zoneName);
        fd.append('branch', branchName);
        dates.forEach(function (d) {
            fd.append('dates[]', d);
        });
        modes.forEach(function (m) {
            fd.append('modes[]', m);
        });
        if (dates.length > 1) {
            var dateAmounts = collectIncomeTagDateAmountsMap();
            Object.keys(dateAmounts).forEach(function (k) {
                fd.append('date_amounts[' + k + ']', dateAmounts[k]);
            });
        }
        if (acknowledgeMismatch) {
            fd.append('acknowledge_income_amount_mismatch', '1');
            if (mismatchRemark) {
                fd.append('income_amount_mismatch_remark', mismatchRemark);
            }
        }
        incomeTagStagingFiles.forEach(function (it) {
            fd.append('attachments[]', it.file);
            fd.append('attachment_tags[]', bankMatchStagingTagLabel(it, bankReconMatchAttachmentTypesIncome));
            fd.append(
                'attachment_type_ids[]',
                it.tagTypeId != null && it.tagTypeId !== '' && !isNaN(Number(it.tagTypeId))
                    ? String(it.tagTypeId)
                    : '0'
            );
        });

        var $btn = $('#applyIncomeTagBtn');
        incomeTagInFlight = true;
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Applying...');

        $.ajax({
            url: routes.incomeTag,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
        })
            .done(function (r) {
                if (r && r.success) {
                    toastr.success(r.message || 'Income tag applied');
                    clearIncomeTagAttachmentStaging();
                    $('#matchTransactionModal').modal('hide');
                    refreshStatementRow(currentStatementId);
                    updateStatistics();
                } else {
                    toastr.error(r && r.message ? r.message : 'Income tag failed');
                }
            })
            .fail(function (xhr) {
                if (
                    xhr.status === 409 &&
                    xhr.responseJSON &&
                    xhr.responseJSON.code === 'income_tag_moc_amount_mismatch'
                ) {
                    showIncomeTagMocMismatchDialog(
                        xhr.responseJSON.mismatches || [],
                        xhr.responseJSON.message || ''
                    );
                    return;
                }
                var msg =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Error applying income tag';
                toastr.error(msg);
            })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="bi bi-tag me-1"></i>Apply Income Tag');
                incomeTagInFlight = false;
            });
    }

    $(document).on('click', '#applyIncomeTagBtn', function () {
        submitIncomeTagWithData(false, null);
    });

    function destroyRadiantPickupSelect2() {
        var $s = $('#radiantCashPickupSelect');
        if ($s.length && $s.data('select2')) {
            try {
                $s.select2('destroy');
            } catch (eRd) {
                /* ignore */
            }
        }
    }

    function setupRadiantPickupSelect2() {
        var $s = $('#radiantCashPickupSelect');
        if (!$s.length || typeof $.fn.select2 !== 'function') {
            return;
        }
        destroyRadiantPickupSelect2();
        var $modal = $('#matchTransactionModal');
        $s.select2({
            width: '100%',
            placeholder: 'Type to filter branch or amount…',
            allowClear: true,
            minimumResultsForSearch: 0,
            dropdownParent: $modal.length ? $modal : $(document.body),
        });
    }

    function loadRadiantCashPickupOptionsForCurrentTxn(preselectPickupId) {
        if (typeof routes === 'undefined' || !routes.radiantCashPickupsForDate) {
            return;
        }
        if (bankReconModalTxnMode !== 'deposit') {
            return;
        }
        var d = currentTxnData && currentTxnData.date;
        if (!d) {
            return;
        }
        var ymd = moment(d).format('YYYY-MM-DD');
        var $sel = $('#radiantCashPickupSelect');
        var $loading = $('#radiantPickupLoading');
        var $meta    = $('#radiantPickupMeta');
        var $count   = $('#radiantPickupCount');
        if (!$sel.length) {
            return;
        }

        destroyRadiantPickupSelect2();
        $sel.prop('disabled', true).hide();
        $meta.hide();
        if ($loading.length) {
            $loading.show();
        }

        $.ajax({
            url: routes.radiantCashPickupsForDate,
            type: 'GET',
            data: { transaction_date: ymd },
        })
            .done(function (res) {
                var rows = res && res.data ? res.data : [];
                $sel.empty();
                $sel.append($('<option>', { value: '', text: '— None / clear pickup link —' }));
                rows.forEach(function (r) {
                    if (!r || r.id == null) {
                        return;
                    }
                    $sel.append(
                        $('<option></option>')
                            .val(String(r.id))
                            .text(r.label || 'Pickup #' + r.id)
                    );
                });
                var pre = (preselectPickupId || '').toString().trim();
                if (pre) {
                    var foundPre = false;
                    $sel.find('option').each(function () {
                        if (String($(this).attr('value')) === pre) {
                            foundPre = true;
                            return false;
                        }
                    });
                    $sel.val(foundPre ? pre : '');
                } else {
                    $sel.val('');
                }
                $sel.prop('disabled', false).show();
                if ($loading.length) {
                    $loading.hide();
                }
                if ($count.length) {
                    $count.text(String(rows.length));
                }
                if ($meta.length) {
                    $meta.show();
                }
                setupRadiantPickupSelect2();
            })
            .fail(function () {
                $sel.empty()
                    .append($('<option>', { value: '', text: '— Could not load pickups —' }))
                    .prop('disabled', false)
                    .show();
                if ($loading.length) {
                    $loading.hide();
                }
                setupRadiantPickupSelect2();
                toastr.warning('Could not load Radiant pickups for this transaction date.');
            });
    }

    // ---- Radiant match keyword (bank_statements.radiant_match_against) ----
    var radiantMatchInFlight = false;

    $(document).on('click', '#clearRadiantMatchBtn', function () {
        $('#radiantMatchAgainstInput').val('');
        var $sel = $('#radiantCashPickupSelect');
        if ($sel.length) {
            $sel.val('').trigger('change');
        }
    });

    $(document).on('click', '#saveRadiantMatchBtn', function () {
        if (radiantMatchInFlight) return;
        if (!currentStatementId) {
            toastr.warning('No bank statement selected');
            return;
        }
        var val = ($('#radiantMatchAgainstInput').val() || '').trim();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Saving…');
        $('#clearRadiantMatchBtn').prop('disabled', true);
        radiantMatchInFlight = true;

        var pickupVal = ($('#radiantCashPickupSelect').val() || '').trim();

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
                refreshStatementRow(currentStatementId);
                updateStatistics();
            } else {
                toastr.error(r.message || 'Save failed');
            }
        }).fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error saving Radiant match keyword';
            toastr.error(msg);
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i>Save &amp; Link');
            $('#clearRadiantMatchBtn').prop('disabled', false);
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

    if (routes.matchAttachmentTypes) {
        loadBankReconAttachmentTypes(null, 'bill');
        loadBankReconAttachmentTypes(null, 'income');
    }

    // ============================================================
    // SALARY UTR upload + log
    // ============================================================
    function brSalaryUtrGetAccountIdsForScope() {
        var ids = [];
        if ($('#qfBankAccount').length) {
            var v = $('#qfBankAccount').val();
            if (Array.isArray(v) && v.length) {
                v.forEach(function (x) {
                    if (x) {
                        ids.push(String(x));
                    }
                });
            } else if (v) {
                ids.push(String(v));
            }
        }
        return ids;
    }

    function brSalaryUploadRefreshSubmitState() {
        var f = document.getElementById('bankReconSalaryFile');
        var hasFile = !!(f && f.files && f.files[0]);
        if (bankAccountsOn() && $('#salaryUploadCompany').length) {
            var co = ($('#salaryUploadCompany').val() || '').toString().trim();
            var acc = ($('#salaryUploadBankAccount').val() || '').toString().trim();
            $('#bankReconSalaryUploadSubmit').prop('disabled', !hasFile || !co || !acc);
        } else {
            $('#bankReconSalaryUploadSubmit').prop('disabled', !hasFile);
        }
    }

    function brLoadSalaryUploadList() {
        if (!routes.salaryUtrUploads) {
            return;
        }
        var params = {
            per_page: 20,
            search: ($('#brSalaryHistSearch').val() || '').trim(),
            date_from: ($('#brSalaryHistFrom').val() || '').trim(),
            date_to: ($('#brSalaryHistTo').val() || '').trim(),
        };
        $('#brSalaryHistBody').html(
            '<tr><td colspan="7" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>'
        );
        $.get(routes.salaryUtrUploads, params)
            .done(function (res) {
                var rows = (res && res.data) || [];
                if (!rows.length) {
                    $('#brSalaryHistBody').html(
                        '<tr><td colspan="7" class="text-center text-muted py-4">No uploads found.</td></tr>'
                    );
                    return;
                }
                var html = '';
                rows.forEach(function (r) {
                    var by = (r.uploaded_by_name || '—') + (r.uploaded_by_username ? ' @' + r.uploaded_by_username : '');
                    var dt = r.created_at ? formatDateTime(String(r.created_at)) : '—';
                    var matchedPct = (r.row_count && r.row_count > 0)
                        ? Math.round((r.matched_count / r.row_count) * 100) : 0;
                    var pctColor = matchedPct >= 90 ? 'bg-success' : (matchedPct >= 50 ? 'bg-warning' : 'bg-danger');
                    var deleteBtn = window.bankReconSuperAdmin
                        ? '<button type="button" class="btn btn-sm btn-outline-danger br-salary-delete-upload ms-1" ' +
                          'data-id="' + String(r.id) + '" data-file="' + escapeAttr(String(r.file_name || '')) + '" ' +
                          'title="Delete this salary upload and unlink matched statements">' +
                          '<i class="bi bi-trash3"></i></button>'
                        : '';
                    html +=
                        '<tr data-upload-id="' + escapeAttr(String(r.id)) + '">' +
                        '<td class="text-center small text-muted">' + r.id + '</td>' +
                        '<td class="small text-break"><i class="bi bi-file-earmark-excel text-success me-1"></i>' + escapeAttr(String(r.file_name || '—')) + '</td>' +
                        '<td class="text-center">' + (r.row_count != null ? r.row_count : '—') + '</td>' +
                        '<td class="text-center">' +
                            '<strong class="text-success">' + (r.matched_count != null ? r.matched_count : '—') + '</strong>' +
                            '<div class="progress mt-1" style="height:4px;min-width:50px">' +
                              '<div class="progress-bar ' + pctColor + '" style="width:' + matchedPct + '%"></div>' +
                            '</div>' +
                        '</td>' +
                        '<td class="small text-nowrap">' + escapeAttr(dt) + '</td>' +
                        '<td class="small">' + escapeAttr(by) + '</td>' +
                        '<td class="text-nowrap">' +
                          '<button type="button" class="btn btn-sm btn-outline-primary br-salary-view-rows" data-id="' + String(r.id) + '">' +
                          '<i class="bi bi-list-columns me-1"></i>Rows</button>' +
                          deleteBtn +
                        '</td></tr>';
                });
                $('#brSalaryHistBody').html(html);
            })
            .fail(function () {
                $('#brSalaryHistBody').html(
                    '<tr><td colspan="7" class="text-center text-danger py-4">Failed to load salary uploads.</td></tr>'
                );
            });
    }

    $('#btnBankReconSalaryUpload').on('click', function () {
        if (!routes.salaryUtrUpload) {
            toastr.error('Salary UTR is not configured.');
            return;
        }
        if (bankAccountsOn() && typeof loadBankAccounts === 'function' && routes.accounts) {
            loadBankAccounts();
        }
        var el = document.getElementById('bankReconSalaryUploadModal');
        if (el) {
            new bootstrap.Modal(el).show();
        }
    });

    $(document).on('shown.bs.modal', '#bankReconSalaryUploadModal', function () {
        if (bankAccountsOn() && $('#salaryUploadCompany').length) {
            if (!$('#salaryUploadCompany').val() && $('#mainUploadCompany').val()) {
                $('#salaryUploadCompany').val($('#mainUploadCompany').val());
            }
            syncAllBankAccountDropdowns();
            if (
                $('#salaryUploadBankAccount').length &&
                !$('#salaryUploadBankAccount').val() &&
                $('#mainUploadBankAccount').val()
            ) {
                $('#salaryUploadBankAccount').val($('#mainUploadBankAccount').val());
            }
        }
        brSalaryUploadRefreshSubmitState();
    });

    $(document).on('change', '#salaryUploadBankAccount', function () {
        brSalaryUploadRefreshSubmitState();
    });

    $(document).on('click', '#bankReconSalaryBrowseBtn', function (e) {
        e.preventDefault();
        var inp = document.getElementById('bankReconSalaryFile');
        if (inp) {
            inp.click();
        }
    });

    $(document).on('change', '#bankReconSalaryFile', function () {
        if (this.files && this.files[0]) {
            $('#bankReconSalaryFileName').text(this.files[0].name);
            $('#bankReconSalaryFileNameWrap').show();
        }
        brSalaryUploadRefreshSubmitState();
    });

    $(document).on('click', '#bankReconSalaryFileRemove', function (e) {
        e.preventDefault();
        var inp = document.getElementById('bankReconSalaryFile');
        if (inp) {
            inp.value = '';
        }
        $('#bankReconSalaryFileNameWrap').hide();
        $('#bankReconSalaryFileName').text('');
        brSalaryUploadRefreshSubmitState();
    });

    $('#btnBankReconSalaryHistory').on('click', function () {
        if (!routes.salaryUtrUploads) {
            toastr.error('Salary UTR is not configured.');
            return;
        }
        var el2 = document.getElementById('bankReconSalaryHistoryModal');
        if (el2) {
            new bootstrap.Modal(el2).show();
        }
        brLoadSalaryUploadList();
    });

    $('#brSalaryHistApply').on('click', function () {
        brLoadSalaryUploadList();
    });

    $('#bankReconSalaryUploadForm').on('submit', function (e) {
        e.preventDefault();
        if (!routes.salaryUtrUpload) {
            return;
        }
        var f = document.getElementById('bankReconSalaryFile');
        if (!f || !f.files || !f.files[0]) {
            toastr.warning('Select an Excel file');
            return;
        }
        if (bankAccountsOn() && $('#salaryUploadCompany').length) {
            var coM = ($('#salaryUploadCompany').val() || '').toString().trim();
            var accM = ($('#salaryUploadBankAccount').val() || '').toString().trim();
            if (!coM) {
                toastr.error('Select a company');
                return;
            }
            if (!accM) {
                toastr.error('Select a bank account number');
                return;
            }
        }
        var fd = new FormData();
        fd.append('salary_file', f.files[0]);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        if (bankAccountsOn() && $('#salaryUploadCompany').length) {
            fd.append('company_id', ($('#salaryUploadCompany').val() || '').toString());
            fd.append('bank_account_id', ($('#salaryUploadBankAccount').val() || '').toString());
        } else {
            brSalaryUtrGetAccountIdsForScope().forEach(function (id) {
                fd.append('bank_account_ids[]', id);
            });
        }
        var $btn = $('#bankReconSalaryUploadSubmit');
        $btn.prop('disabled', true);
        $.ajax({
            url: routes.salaryUtrUpload,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
        })
            .done(function (res) {
                if (res && res.success) {
                    var msg = res.message || 'Done';
                    if (res.skipped > 0) {
                        toastr.warning(msg, 'Salary Upload', { timeOut: 6000 });
                    } else {
                        toastr.success(msg);
                    }
                    var m = document.getElementById('bankReconSalaryUploadModal');
                    if (m) {
                        var inst = bootstrap.Modal.getInstance(m);
                        if (inst) {
                            inst.hide();
                        }
                    }
                    var sform = document.getElementById('bankReconSalaryUploadForm');
                    if (sform) {
                        sform.reset();
                    }
                    $('#bankReconSalaryFileNameWrap').hide();
                    $('#bankReconSalaryFileName').text('');
                    if (bankAccountsOn() && $('#salaryUploadCompany').length) {
                        syncAllBankAccountDropdowns();
                    }
                    brSalaryUploadRefreshSubmitState();
                    if (typeof loadStatements === 'function') {
                        loadStatements(currentPage);
                    }
                } else {
                    toastr.error((res && res.message) || 'Upload failed');
                }
            })
            .fail(function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Upload failed';
                toastr.error(msg);
            })
            .always(function () {
                $btn.prop('disabled', false);
                brSalaryUploadRefreshSubmitState();
            });
    });

    $(document).on('click', '.br-salary-delete-upload', function () {
        var id = $(this).data('id');
        var fileName = $(this).data('file') || '#' + id;
        if (!id || !routes.salaryUtrUploadDelete) {
            toastr.error('Delete route not configured.');
            return;
        }
        if (!confirm('Delete salary upload "' + fileName + '"?\n\nThis will:\n• Remove all rows from this upload.\n• Unlink matched bank statements (they will become Unmatched again).\n\nThis cannot be undone.')) {
            return;
        }
        var url = routes.salaryUtrUploadDelete.replace(':id', String(id));
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
        })
        .done(function (res) {
            if (res && res.success) {
                toastr.success(res.message || 'Deleted');
                brLoadSalaryUploadList();
                if (typeof loadStatements === 'function') {
                    loadStatements(currentPage);
                }
            } else {
                toastr.error((res && res.message) || 'Delete failed');
                $btn.prop('disabled', false).html('<i class="bi bi-trash3"></i>');
            }
        })
        .fail(function (xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed';
            toastr.error(msg);
            $btn.prop('disabled', false).html('<i class="bi bi-trash3"></i>');
        });
    });

    $(document).on('click', '.br-salary-view-rows', function () {
        var id = $(this).data('id');
        if (!id || !routes.salaryUtrUploadRows) {
            return;
        }
        var url = routes.salaryUtrUploadRows.replace(':id', String(id));
        $.get(url)
            .done(function (res) {
                if (!res || !res.success) {
                    toastr.error('Could not load rows');
                    return;
                }
                var list = res.rows || [];
                var h = '';
                if (!list.length) {
                    h = '<tr><td colspan="6" class="text-center text-muted">No rows</td></tr>';
                } else {
                    list.forEach(function (r) {
                        var st = (r.match_status || '').toString();
                        var stCls = st === 'matched' ? 'text-success' : 'text-warning';
                        var sm =
                            (r.stmt_deposit && parseFloat(r.stmt_deposit) > 0) ? '₹' + formatNumber(r.stmt_deposit) : '—';
                        var sdesc = (r.stmt_description || '—').toString();
                        if (sdesc.length > 60) {
                            sdesc = sdesc.slice(0, 60) + '…';
                        }
                        h +=
                            '<tr><td class="text-break small">' +
                            escapeAttr((r.utr || '—').toString()) +
                            '</td><td class="small">' +
                            escapeAttr((r.employee_name || '—').toString()) +
                            '</td><td class="small">' +
                            escapeAttr((r.branch || '—').toString()) +
                            '</td><td>₹' +
                            (r.net_paid != null ? formatNumber(r.net_paid) : '—') +
                            '</td><td class="small ' +
                            stCls +
                            '"><strong>' +
                            escapeAttr(st) +
                            '</strong></td><td class="small" title="' +
                            escapeAttr((r.stmt_description || '').toString()) +
                            '"><span class="d-block text-break">' +
                            escapeAttr(sdesc) +
                            '</span><div class="text-muted">' +
                            sm +
                            '</div></td></tr>';
                    });
                }
                $('#brSalaryDetailBody').html(h);
                var m3 = document.getElementById('bankReconSalaryDetailModal');
                if (m3) {
                    new bootstrap.Modal(m3).show();
                }
            })
            .fail(function () {
                toastr.error('Could not load row details');
            });
    });

    // ============================================================
    // BANK RECON USER HISTORY MODAL  (Super Admin only)
    // ============================================================
    if (window.bankReconSuperAdmin) {
        var brHistoryCurrentPage = 1;
        var brHistoryLoading     = false;

        // ------ Action metadata ----------------------------------------
        var BR_HISTORY_ACTIONS = {
            import_statement : { label: 'Import Statement', icon: 'bi-upload',          cls: 'badge-br-import'   },
            match_bill       : { label: 'Match Bill',        icon: 'bi-link-45deg',      cls: 'badge-br-match'    },
            unmatch_bill     : { label: 'Unmatch Bill',      icon: 'bi-x-circle',        cls: 'badge-br-unmatch'  },
            income_tag       : { label: 'Income Tag',        icon: 'bi-tag-fill',        cls: 'badge-br-income'   },
            income_unmatch   : { label: 'Income Unmatch',    icon: 'bi-tag-x',           cls: 'badge-br-iunmatch' },
            radiant_match    : { label: 'Radiant Match',     icon: 'bi-brightness-high', cls: 'badge-br-radiant'  },
            radiant_unmatch  : { label: 'Radiant Unmatch',   icon: 'bi-sun-x',           cls: 'badge-br-runmatch' },
            delete_statement : { label: 'Delete Statement',  icon: 'bi-trash-fill',      cls: 'badge-br-delete'   },
            delete_batch     : { label: 'Delete Batch',      icon: 'bi-trash3-fill',     cls: 'badge-br-delete'   },
            salary_upload    : { label: 'Salary UTR',        icon: 'bi-file-earmark-excel', cls: 'badge-br-income' },
        };

        // ------ Helper: format datetime --------------------------------
        function brHistoryFmtDate(iso) {
            if (!iso) return '-';
            try {
                var d = new Date(String(iso).replace(' ', 'T'));
                if (isNaN(d.getTime())) return iso;
                var pad = function(n){ return String(n).padStart(2, '0'); };
                return pad(d.getDate()) + '/' + pad(d.getMonth()+1) + '/' + d.getFullYear()
                     + '<br><small class="text-muted">' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()) + '</small>';
            } catch(e) { return iso; }
        }

        // ------ Helper: render details cell ----------------------------
        function brHistoryRenderDetails(action, details) {
            if (!details || typeof details !== 'object') return '<span class="text-muted">—</span>';
            var parts = [];
            switch (action) {
                case 'import_statement':
                    if (details.file_name)  parts.push('<span class="br-hist-detail-key">File:</span> <code>' + $('<div>').text(details.file_name).html() + '</code>');
                    if (details.imported != null) parts.push('<span class="br-hist-detail-key">Imported:</span> <strong>' + details.imported + '</strong>');
                    if (details.duplicates) parts.push('<span class="br-hist-detail-key">Duplicates:</span> ' + details.duplicates);
                    if (details.skipped)    parts.push('<span class="br-hist-detail-key">Skipped:</span> ' + details.skipped);
                    break;
                case 'match_bill':
                    if (details.bill_id)        parts.push('<span class="br-hist-detail-key">Bill ID:</span> <strong>#' + details.bill_id + '</strong>');
                    if (details.matched_amount) parts.push('<span class="br-hist-detail-key">Amount:</span> ₹' + Number(details.matched_amount).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    if (details.match_type)     parts.push('<span class="br-hist-detail-key">Type:</span> ' + details.match_type);
                    break;
                case 'unmatch_bill':
                    if (details.bill_id)        parts.push('<span class="br-hist-detail-key">Bill ID:</span> <strong>#' + details.bill_id + '</strong>');
                    if (details.matched_amount) parts.push('<span class="br-hist-detail-key">Amount:</span> ₹' + Number(details.matched_amount).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    break;
                case 'income_tag':
                    if (details.branch) parts.push('<span class="br-hist-detail-key">Branch:</span> ' + $('<div>').text(details.branch).html());
                    if (Array.isArray(details.modes) && details.modes.length) {
                        parts.push(
                            '<span class="br-hist-detail-key">Modes:</span> <span class="br-hist-modes-val">' +
                                details.modes
                                    .map(function (m) {
                                        return $('<div>').text(String(m)).html();
                                    })
                                    .join(', ') +
                                '</span>'
                        );
                    }
                    if (details.created != null) parts.push('<span class="br-hist-detail-key">Created:</span> ' + details.created);
                    if (details.updated != null) parts.push('<span class="br-hist-detail-key">Updated:</span> ' + details.updated);
                    break;
                case 'income_unmatch':
                    if (details.branch) parts.push('<span class="br-hist-detail-key">Branch:</span> ' + $('<div>').text(details.branch).html());
                    if (details.date)   parts.push('<span class="br-hist-detail-key">Date:</span> ' + details.date);
                    if (details.amount != null) parts.push('<span class="br-hist-detail-key">Amount:</span> ₹' + Number(details.amount).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    break;
                case 'radiant_match':
                    if (details.keyword)                parts.push('<span class="br-hist-detail-key">Keyword:</span> ' + $('<div>').text(details.keyword).html());
                    if (details.radiant_cash_pickup_id) parts.push('<span class="br-hist-detail-key">Pickup ID:</span> #' + details.radiant_cash_pickup_id);
                    break;
                case 'radiant_unmatch':
                    if (details.keyword)                parts.push('<span class="br-hist-detail-key">Keyword:</span> ' + $('<div>').text(details.keyword).html());
                    if (details.radiant_cash_pickup_id) parts.push('<span class="br-hist-detail-key">Pickup ID:</span> #' + details.radiant_cash_pickup_id);
                    if (details.location)               parts.push('<span class="br-hist-detail-key">Location:</span> ' + $('<div>').text(details.location).html());
                    break;
                case 'delete_statement':
                    if (details.description) parts.push('<span class="br-hist-detail-key">Desc:</span> ' + $('<div>').text(details.description).html());
                    if (details.amount != null) parts.push('<span class="br-hist-detail-key">Amount:</span> ₹' + Number(details.amount).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    break;
                case 'delete_batch':
                    if (details.batch_id)  parts.push('<span class="br-hist-detail-key">Batch:</span> <code>' + $('<div>').text(details.batch_id).html() + '</code>');
                    if (details.deleted != null) parts.push('<span class="br-hist-detail-key">Deleted:</span> ' + details.deleted + ' rows');
                    break;
                case 'salary_upload':
                    if (details.file)   parts.push('<span class="br-hist-detail-key">File:</span> <code>' + $('<div>').text(details.file).html() + '</code>');
                    if (details.rows != null)       parts.push('<span class="br-hist-detail-key">Rows:</span> ' + details.rows);
                    if (details.matched != null)    parts.push('<span class="br-hist-detail-key">Matched:</span> <strong class="text-success">' + details.matched + '</strong>');
                    if (details.unmatched != null)  parts.push('<span class="br-hist-detail-key">Unmatched:</span> ' + details.unmatched);
                    if (details.upload_id != null)  parts.push('<span class="br-hist-detail-key">Upload #:</span> ' + details.upload_id);
                    break;
                default:
                    $.each(details, function(k, v) {
                        if (v !== null && v !== '') {
                            parts.push('<span class="br-hist-detail-key">' + $('<div>').text(k).html() + ':</span> ' + $('<div>').text(String(v)).html());
                        }
                    });
            }
            return parts.length
                ? '<div class="br-history-details-inner">' +
                  parts.map(function (p) {
                      return '<div class="br-hist-detail-row">' + p + '</div>';
                  }).join('') +
                  '</div>'
                : '<span class="text-muted">—</span>';
        }

        // ------ Render rows into tbody ---------------------------------
        function brHistoryRenderRows(rows, total, currentPage, lastPage, perPage) {
            var tbody = $('#bankReconUserHistoryBody');
            tbody.empty();

            $('#brHistoryResultCount').html(
                '<i class="bi bi-list-ul me-1"></i>Total <strong>' + total + '</strong> record(s)'
            );

            if (!rows || !rows.length) {
                tbody.html('<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox me-2 fs-4"></i><br>No history records found for the selected filters.</td></tr>');
                $('#bankReconUserHistoryPager').html('');
                return;
            }

            var offset = (currentPage - 1) * perPage;
            $.each(rows, function(i, row) {
                var meta = BR_HISTORY_ACTIONS[row.action] || { label: row.action, icon: 'bi-circle', cls: 'badge-br-default' };
                var actionBadge = '<span class="br-action-badge ' + meta.cls + '"><i class="bi ' + meta.icon + ' me-1"></i>' + meta.label + '</span>';

                // Statement cell — show description + txn date + amount
                var stmtCell = '';
                if (row.bank_statement_id) {
                    stmtCell += '<div class="br-history-stmt-cell-inner">';
                    stmtCell += '<span class="badge bg-light text-secondary border flex-shrink-0 align-self-start">#' + row.bank_statement_id + '</span>';
                    stmtCell += '<div style="min-width:0;flex:1 1 0;">';
                    if (row.stmt_description) {
                        stmtCell += '<div class="fw-semibold br-stmt-desc" title="' + $('<div>').text(row.stmt_description).html() + '">'
                                  + $('<div>').text(row.stmt_description).html() + '</div>';
                    }
                    var stmtMeta = [];
                    if (row.stmt_transaction_date) {
                        stmtMeta.push('<span><i class="bi bi-calendar3 me-1"></i>' + $('<div>').text(row.stmt_transaction_date).html() + '</span>');
                    }
                    if (row.stmt_deposit && row.stmt_deposit > 0) {
                        stmtMeta.push('<span class="text-success"><i class="bi bi-arrow-down-circle me-1"></i>₹' + Number(row.stmt_deposit).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>');
                    }
                    if (row.stmt_withdrawal && row.stmt_withdrawal > 0) {
                        stmtMeta.push('<span class="text-danger"><i class="bi bi-arrow-up-circle me-1"></i>₹' + Number(row.stmt_withdrawal).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>');
                    }
                    if (stmtMeta.length) {
                        stmtCell += '<small class="text-muted d-block mt-1">' + stmtMeta.join('<br>') + '</small>';
                    }
                    stmtCell += '</div></div>';
                } else {
                    stmtCell = '<span class="text-muted">—</span>';
                }

                var userHtml = '<div class="fw-semibold">' + $('<div>').text(row.user_fullname || 'Unknown').html() + '</div>'
                             + '<small class="text-muted">@' + $('<div>').text(row.username || '').html() + '</small>';

                tbody.append(
                    '<tr>' +
                    '<td class="text-center text-muted small">' + (offset + i + 1) + '</td>' +
                    '<td class="small">' + brHistoryFmtDate(row.created_at) + '</td>' +
                    '<td>' + userHtml + '</td>' +
                    '<td>' + actionBadge + '</td>' +
                    '<td class="small br-history-stmt-cell">' + stmtCell + '</td>' +
                    '<td class="small br-history-details-cell">' + brHistoryRenderDetails(row.action, row.details) + '</td>' +
                    '<td class="small text-muted font-monospace br-history-ip-cell">' + $('<div>').text(row.ip_address || '—').html() + '</td>' +
                    '</tr>'
                );
            });

            // Build pagination
            brHistoryRenderPager(currentPage, lastPage);
        }

        function brHistoryRenderPager(currentPage, lastPage) {
            var pager = $('#bankReconUserHistoryPager');
            pager.empty();
            if (lastPage <= 1) return;

            var html = '<div class="d-flex align-items-center justify-content-between w-100">';
            html += '<small class="text-muted">Page ' + currentPage + ' of ' + lastPage + '</small>';
            html += '<ul class="pagination pagination-sm mb-0">';

            html += '<li class="page-item' + (currentPage <= 1 ? ' disabled' : '') + '">'
                  + '<a class="page-link br-hist-page" href="#" data-page="' + (currentPage - 1) + '">&#8249; Prev</a></li>';

            var start = Math.max(1, currentPage - 2);
            var end   = Math.min(lastPage, currentPage + 2);
            if (start > 1) { html += '<li class="page-item disabled"><span class="page-link">…</span></li>'; }
            for (var p = start; p <= end; p++) {
                html += '<li class="page-item' + (p === currentPage ? ' active' : '') + '">'
                      + '<a class="page-link br-hist-page" href="#" data-page="' + p + '">' + p + '</a></li>';
            }
            if (end < lastPage) { html += '<li class="page-item disabled"><span class="page-link">…</span></li>'; }

            html += '<li class="page-item' + (currentPage >= lastPage ? ' disabled' : '') + '">'
                  + '<a class="page-link br-hist-page" href="#" data-page="' + (currentPage + 1) + '">Next &#8250;</a></li>';
            html += '</ul></div>';
            pager.html(html);
        }

        // ------ Load history from API ----------------------------------
        function loadBrHistory(page) {
            if (brHistoryLoading) return;
            brHistoryLoading = true;
            brHistoryCurrentPage = page || 1;

            var params = {
                page     : brHistoryCurrentPage,
                per_page : 30,
                search   : ($('#brHistorySearch').val() || '').trim(),
                action   : ($('#brHistoryActionFilter').val() || '').trim(),
                date_from: ($('#brHistoryDateFrom').val() || '').trim(),
                date_to  : ($('#brHistoryDateTo').val() || '').trim(),
            };

            $('#bankReconUserHistoryBody').html(
                '<tr><td colspan="7" class="text-center py-5 text-muted">' +
                '<span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>'
            );
            $('#bankReconUserHistoryPager').html('');
            $('#brHistoryResultCount').text('');

            $.get(routes.userHistory, params)
                .done(function(res) {
                    if (res && Array.isArray(res.data)) {
                        brHistoryRenderRows(
                            res.data,
                            parseInt(res.total, 10) || 0,
                            parseInt(res.current_page, 10) || 1,
                            parseInt(res.last_page, 10) || 1,
                            parseInt(res.per_page, 10) || 30
                        );
                    } else {
                        $('#bankReconUserHistoryBody').html(
                            '<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load history. Try again.</td></tr>'
                        );
                    }
                })
                .fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error';
                    $('#bankReconUserHistoryBody').html(
                        '<tr><td colspan="7" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i>' + msg + '</td></tr>'
                    );
                })
                .always(function() { brHistoryLoading = false; });
        }

        // ------ Event bindings ----------------------------------------
        $('#btnBankReconUserHistory').on('click', function() {
            brHistoryCurrentPage = 1;
            // Reset filters when opening fresh
            $('#brHistorySearch').val('');
            $('#brHistoryActionFilter').val('');
            $('#brHistoryDateFrom').val('');
            $('#brHistoryDateTo').val('');
            var modal = new bootstrap.Modal(document.getElementById('bankReconUserHistoryModal'));
            modal.show();
            loadBrHistory(1);
        });

        $('#brHistorySearchBtn').on('click', function() {
            loadBrHistory(1);
        });

        $('#brHistoryClearBtn').on('click', function() {
            $('#brHistorySearch').val('');
            $('#brHistoryActionFilter').val('');
            $('#brHistoryDateFrom').val('');
            $('#brHistoryDateTo').val('');
            loadBrHistory(1);
        });

        // Search on Enter key in the search box
        $('#brHistorySearch').on('keydown', function(e) {
            if (e.key === 'Enter') { loadBrHistory(1); }
        });

        // Pagination click
        $(document).on('click', '.br-hist-page', function(e) {
            e.preventDefault();
            var pg = parseInt($(this).data('page'), 10);
            if (!isNaN(pg) && pg >= 1) { loadBrHistory(pg); }
        });
    }
    // ============================================================
    // END USER HISTORY MODAL
    // ============================================================
});
