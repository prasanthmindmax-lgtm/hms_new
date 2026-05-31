/**
 * Rental agreements & landlord payments — consolidated client scripts.
 * Requires jQuery, moment/daterangepicker (filters/create/register), Chart.js (register & chart report), FormFieldValidation (create).
 */

/* ========== Form validation ========== */
(function () {
  'use strict';

  const form = document.querySelector('form.js-ra-validate');
  if (!form || !window.FormFieldValidation) {
    return;
  }

  const GST_INCLUDING = 'including_gst';
  const GST_EXCLUDING = 'excluding_gst';

  function gstApplicable() {
    const el = form.querySelector('[name="gst_applicable"]');
    return el ? el.value === '1' : false;
  }

  function gstType() {
    const el = form.querySelector('[name="gst_type"]');
    return el ? el.value : '';
  }

  function hasGstTaxMode() {
    const t = gstType();
    return t === GST_INCLUDING || t === GST_EXCLUDING;
  }

  function rcmApplicable() {
    const el = form.querySelector('[name="rcm_applicable"]');
    return el ? el.value : '0';
  }

  const rules = [
    { field: 'company_id', required: true, message: 'Company is required.' },
    { field: 'zone_id', required: true, message: 'Zone is required.' },
    { field: 'branch_id', required: true, message: 'Location is required.' },
    { field: 'agreement_date', required: true, message: 'Rental agreement date is required.' },
    {
      field: 'owner_name',
      required: true,
      message: 'Landlord name is required.',
      validate: function (val) {
        const vendorId = form.querySelector('[name="vendor_id"]');
        if (!val && vendorId && String(vendorId.value).trim() === '') {
          return 'Please select a landlord.';
        }
        return '';
      },
    },
    {
      field: 'agreement_period',
      required: true,
      message: 'Agreement period is required.',
      validate: function () {
        const start = form.querySelector('[name="agreement_period_start"]');
        const end = form.querySelector('[name="agreement_period_end"]');
        if (!start || !end || !String(start.value).trim() || !String(end.value).trim()) {
          return 'Select a valid agreement period range.';
        }
        return '';
      },
    },
    { field: 'address', required: true, message: 'Address is required.' },
    { field: 'advance_amount', required: true, min: 0, message: 'Advance amount is required.' },
    { field: 'monthly_rent_amount', required: true, min: 0, message: 'Monthly rent is required.' },
    { field: 'end_of_agreement_date', required: true, message: 'End of agreement date is required.' },
    { field: 'gst_applicable', required: true, message: 'Select whether GST is applicable.' },
    {
      field: 'gst_type',
      validate: function (val) {
        if (!gstApplicable()) {
          return '';
        }
        if (!val || !hasGstTaxMode()) {
          return 'Select tax mode (Including or Excluding GST).';
        }
        return '';
      },
    },
    {
      field: 'gst_tax_id',
      validate: function () {
        if (!gstApplicable() || !hasGstTaxMode()) {
          return '';
        }
        const id = form.querySelector('[name="gst_tax_id"]');
        if (!id || String(id.value).trim() === '') {
          return 'Select a GST rate from the list.';
        }
        return '';
      },
    },
    { field: 'tds_tax_id', required: true, message: 'TDS tax is required.' },
    { field: 'rcm_applicable', required: true, message: 'RCM selection is required.' },
    {
      field: 'rcm_value',
      validate: function (val) {
        if (rcmApplicable() !== '1') {
          return '';
        }
        if (String(val).trim() === '') {
          return 'RCM value is required when RCM is applicable.';
        }
        const num = parseFloat(val);
        if (!Number.isFinite(num) || num < 0) {
          return 'Enter a valid RCM value.';
        }
        return '';
      },
    },
  ];

  FormFieldValidation.bindClearOnInput(form);

  form.addEventListener('submit', function (e) {
    if (!FormFieldValidation.validateForm(form, rules)) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
})();


/* ========== GST and TDS ========== */
/**
 * Rental agreement — GST & TDS (Bill module tax dropdown pattern).
 */
(function () {
  const GST_INCLUDING = 'including_gst';
  const GST_EXCLUDING = 'excluding_gst';

  const gstApplicableEl = document.getElementById('ra_gst_applicable');
  const gstCardEl = document.getElementById('ra_gst_card');
  const gstDetailWrapEl = document.getElementById('ra_gst_detail_wrap');
  const gstDetailFields = gstCardEl
    ? gstCardEl.querySelectorAll('.ra-gst-detail-field')
    : [];
  const typeEl = document.getElementById('ra_gst_type');
  const wrapEl = document.getElementById('ra_gst_fields_wrap');
  const rentEl = document.getElementById('ra_monthly_rent_amount');
  const maintEl = document.getElementById('ra_maintenance_amount');

  const gstSearchEl = document.getElementById('ra_gst_search_input');
  const gstNameEl = document.getElementById('ra_gst_tax_name');
  const gstPctEl = document.getElementById('ra_gst_percentage');
  const gstTaxTypeEl = document.getElementById('ra_gst_tax_type');
  const gstTaxIdEl = document.getElementById('ra_gst_tax_id');
  const gstAmtEl = document.getElementById('ra_gst_amount');
  const cgstEl = document.getElementById('ra_cgst_amount');
  const sgstEl = document.getElementById('ra_sgst_amount');
  const igstEl = document.getElementById('ra_igst_amount');
  const gstRentSummaryEl = document.getElementById('ra_gst_breakdown_rent');
  const gstMaintSummaryEl = document.getElementById('ra_gst_breakdown_maintenance');
  const gstTotalSummaryEl = document.getElementById('ra_gst_breakdown_total');
  const gstFieldWraps = document.querySelectorAll('.ra-rent-block--tax .ra-gst-fields-wrap');

  const tdsSearchEl = document.getElementById('ra_tds_search_input');
  const tdsNameEl = document.getElementById('ra_tds_tax_name');
  const tdsRateEl = document.getElementById('ra_tds_rate');
  const tdsTaxIdEl = document.getElementById('ra_tds_tax_id');
  const tdsSectionIdEl = document.getElementById('ra_tds_section_id');
  const tdsSectionEl = document.getElementById('ra_tds_section');
  const tdsSectionDisplayEl = document.getElementById('ra_tds_section_display');
  const tdsAmtEl = document.getElementById('ra_tds_amount');
  const tdsDisplayEl = document.getElementById('ra_tds_display');

  if (!typeEl || !gstApplicableEl) {
    return;
  }

  let taxMode = typeEl.value === GST_INCLUDING ? 'inclusive' : 'exclusive';

  const isGstApplicable = function () {
    return gstApplicableEl.value === '1';
  };

  const hasTaxMode = function () {
    const v = typeEl.value;
    return v === GST_INCLUDING || v === GST_EXCLUDING;
  };

  const num = function (v) {
    const n = parseFloat(v);
    return Number.isFinite(n) ? n : 0;
  };

  /** Mirrors RentalAgreement::computeGstBreakdownForBase (per amount, then combined). */
  const round2 = function (n) {
    return Math.round((n + Number.EPSILON) * 100) / 100;
  };

  const computeInclusiveGstBreakdown = function (base, gstPercent, taxType) {
    const empty = {
      taxable: 0,
      gst_amount: 0,
      cgst_amount: 0,
      sgst_amount: 0,
      igst_amount: 0,
    };
    if (base <= 0 || gstPercent <= 0) {
      return empty;
    }

    const rate = gstPercent / 100;
    const taxable = round2(base / (1 + rate));
    const gstAmount = round2(base - taxable);
    const kind = (taxType || 'GST').toUpperCase();

    if (kind === 'IGST') {
      return {
        taxable: taxable,
        gst_amount: gstAmount,
        cgst_amount: 0,
        sgst_amount: 0,
        igst_amount: gstAmount,
      };
    }

    const sgst = round2(gstAmount / 2);
    const cgst = round2(gstAmount - sgst);

    return {
      taxable: taxable,
      gst_amount: gstAmount,
      cgst_amount: cgst,
      sgst_amount: sgst,
      igst_amount: 0,
    };
  };

  const rentAmount = function () {
    return rentEl ? num(rentEl.value) : 0;
  };

  const maintenanceAmount = function () {
    return maintEl ? num(maintEl.value) : 0;
  };

  const computeGstBreakdownForBase = function (base, gstPercent, taxType) {
    if (taxMode === 'inclusive') {
      return computeInclusiveGstBreakdown(base, gstPercent, taxType);
    }

    const empty = {
      taxable: 0,
      gst_amount: 0,
      cgst_amount: 0,
      sgst_amount: 0,
      igst_amount: 0,
    };
    if (base <= 0 || gstPercent <= 0) {
      return empty;
    }

    const gstAmount = round2((base * gstPercent) / 100);
    const taxable = round2(base);
    const kind = (taxType || 'GST').toUpperCase();

    if (kind === 'IGST') {
      return {
        taxable: taxable,
        gst_amount: gstAmount,
        cgst_amount: 0,
        sgst_amount: 0,
        igst_amount: gstAmount,
      };
    }

    const sgst = round2(gstAmount / 2);
    const cgst = round2(gstAmount - sgst);

    return {
      taxable: taxable,
      gst_amount: gstAmount,
      cgst_amount: cgst,
      sgst_amount: sgst,
      igst_amount: 0,
    };
  };

  const sumGstBreakdowns = function (a, b) {
    return {
      taxable: round2((a.taxable || 0) + (b.taxable || 0)),
      gst_amount: round2((a.gst_amount || 0) + (b.gst_amount || 0)),
      cgst_amount: round2((a.cgst_amount || 0) + (b.cgst_amount || 0)),
      sgst_amount: round2((a.sgst_amount || 0) + (b.sgst_amount || 0)),
      igst_amount: round2((a.igst_amount || 0) + (b.igst_amount || 0)),
    };
  };

  const isIncluding = function () {
    return typeEl.value === GST_INCLUDING;
  };

  const syncTaxModeFromSelect = function () {
    taxMode = typeEl.value === GST_INCLUDING ? 'inclusive' : 'exclusive';
  };

  const tdsRatePercent = function () {
    if (!tdsRateEl) return 0;
    const raw = num(tdsRateEl.value);
    if (raw <= 0) return 0;
    return raw <= 1 ? raw * 100 : raw;
  };

  const clearGstSelection = function () {
    if (gstSearchEl) gstSearchEl.value = '';
    if (gstNameEl) gstNameEl.value = '';
    if (gstPctEl) gstPctEl.value = '';
    if (gstTaxTypeEl) gstTaxTypeEl.value = 'GST';
    if (gstTaxIdEl) gstTaxIdEl.value = '';
    if (gstAmtEl) gstAmtEl.value = '';
    if (cgstEl) cgstEl.value = '0';
    if (sgstEl) sgstEl.value = '0';
    if (igstEl) igstEl.value = '0';
    [gstRentSummaryEl, gstMaintSummaryEl, gstTotalSummaryEl].forEach(function (el) {
      if (!el) return;
      el.innerHTML = '';
      el.classList.add('ra-gst-breakdown-box--empty');
    });
  };

  const formatMoney = function (amount) {
    return Number(amount).toLocaleString('en-IN', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  };

  const formatGstSearchLabel = function (rate) {
    const pct = num(rate);
    if (pct <= 0) return '';
    const display = pct % 1 === 0 ? String(pct) : pct.toFixed(2).replace(/\.?0+$/, '');
    return display + '% GST';
  };

  const formatTdsSearchLabel = function (rate) {
    const pct = num(rate);
    const displayPct = pct <= 1 && pct > 0 ? pct * 100 : pct;
    if (displayPct <= 0) return '';
    const display = displayPct % 1 === 0 ? String(displayPct) : displayPct.toFixed(2).replace(/\.?0+$/, '');
    return display + '% TDS';
  };

  const formatTdsSectionDisplay = function (sectionName, taxName) {
    const section = (sectionName || '').trim();
    const name = (taxName || '').trim();
    if (!section) return name;
    if (!name || section.toLowerCase().indexOf(name.toLowerCase()) !== -1) {
      return section;
    }
    return section + ' - ' + name;
  };

  const renderGstBreakdown = function (container, payload) {
    if (!container) return;
    const taxable = payload.taxable || 0;
    const totalGst = payload.totalGst || 0;
    if (taxable <= 0 && totalGst <= 0) {
      container.innerHTML = '';
      container.classList.add('ra-gst-breakdown-box--empty');
      return;
    }
    container.classList.remove('ra-gst-breakdown-box--empty');
    const items = [{ label: 'Taxable Amount', amount: taxable, highlight: false }];
    const halfPct =
      payload.gstPercent > 0
        ? (payload.gstPercent / 2).toFixed(2).replace(/\.?0+$/, '')
        : '0';
    if (payload.gstType === 'GST' && (payload.cgst > 0 || payload.sgst > 0)) {
      items.push({ label: 'CGST (' + halfPct + '%)', amount: payload.cgst, highlight: false });
      items.push({ label: 'SGST (' + halfPct + '%)', amount: payload.sgst, highlight: false });
    } else if (payload.igst > 0) {
      const igstPct =
        payload.gstPercent > 0
          ? payload.gstPercent.toFixed(2).replace(/\.?0+$/, '')
          : '0';
      items.push({
        label: 'IGST (' + igstPct + '%)',
        amount: payload.igst,
        highlight: false,
      });
    }
    items.push({ label: 'Total GST', amount: totalGst, highlight: true });
    container.innerHTML = items
      .map(function (item) {
        return (
          '<div class="ra-gst-breakdown-item' +
          (item.highlight ? ' ra-gst-breakdown-item--total' : '') +
          '">' +
          '<span class="ra-gst-breakdown-label">' +
          item.label +
          '</span>' +
          '<span class="ra-gst-breakdown-value">&#8377; ' +
          formatMoney(item.amount) +
          '</span>' +
          '</div>'
        );
      })
      .join('');
  };

  const raGstCalculate = function () {
    if (!wrapEl) return;
    if (!isGstApplicable() || !hasTaxMode()) {
      return;
    }

    syncTaxModeFromSelect();

    const gstPercent = gstPctEl ? num(gstPctEl.value) : 0;
    const gstType = (gstTaxTypeEl && gstTaxTypeEl.value ? gstTaxTypeEl.value : 'GST').toUpperCase();

    const rentBreakdown = computeGstBreakdownForBase(rentAmount(), gstPercent, gstType);
    const maintBreakdown = computeGstBreakdownForBase(maintenanceAmount(), gstPercent, gstType);
    const breakdown = sumGstBreakdowns(rentBreakdown, maintBreakdown);

    const gstAmount = breakdown.gst_amount;
    const cgst = breakdown.cgst_amount;
    const sgst = breakdown.sgst_amount;
    const igst = breakdown.igst_amount;
    const taxable = breakdown.taxable;

    if (gstAmtEl) gstAmtEl.value = gstAmount > 0 ? gstAmount.toFixed(2) : '';
    if (cgstEl) cgstEl.value = cgst.toFixed(2);
    if (sgstEl) sgstEl.value = sgst.toFixed(2);
    if (igstEl) igstEl.value = igst.toFixed(2);

    const renderPayload = function (part) {
      return {
        taxable: part.taxable,
        cgst: part.cgst_amount,
        sgst: part.sgst_amount,
        igst: part.igst_amount,
        totalGst: part.gst_amount,
        gstPercent: gstPercent,
        gstType: gstType,
      };
    };

    renderGstBreakdown(gstRentSummaryEl, renderPayload(rentBreakdown));
    renderGstBreakdown(gstMaintSummaryEl, renderPayload(maintBreakdown));
    renderGstBreakdown(gstTotalSummaryEl, {
      taxable: taxable,
      cgst: cgst,
      sgst: sgst,
      igst: igst,
      totalGst: gstAmount,
      gstPercent: gstPercent,
      gstType: gstType,
    });
  };

  const raTdsCalculate = function () {
    const rent = rentAmount();
    const pct = tdsRatePercent();
    let tdsAmount = 0;
    if (rent > 0 && pct > 0) {
      tdsAmount = (rent * pct) / 100;
    }
    if (tdsAmtEl) tdsAmtEl.value = tdsAmount > 0 ? tdsAmount.toFixed(2) : '';
    if (tdsDisplayEl) {
      tdsDisplayEl.value = tdsAmount > 0 ? formatMoney(tdsAmount) : '';
    }
  };

  const syncGstCalcVisibility = function () {
    const showCalc = isGstApplicable() && hasTaxMode();
    if (gstCardEl) {
      gstCardEl.classList.toggle('ra-gst-card--with-breakdown', showCalc);
    }
    gstFieldWraps.forEach(function (el) {
      el.classList.toggle('ra-gst-fields--hidden', !showCalc);
    });
    document.querySelectorAll('.ra-rent-block--tax .ra-gst-req').forEach(function (el) {
      el.classList.toggle('d-none', !showCalc);
    });
    document.querySelectorAll('.ra-rent-block--tax .ra-gst-tax-mode-req').forEach(function (el) {
      el.classList.toggle('d-none', !isGstApplicable());
    });
    if (!showCalc) {
      if (!isGstApplicable()) {
        clearGstSelection();
        if (typeEl) {
          typeEl.value = '';
        }
      }
      return;
    }
    raGstCalculate();
  };

  const syncGstApplicable = function () {
    const on = isGstApplicable();
    if (gstCardEl) {
      gstCardEl.classList.toggle('ra-gst-card--expanded', on);
    }
    gstDetailFields.forEach(function (el) {
      el.classList.toggle('ra-gst-detail-wrap--hidden', !on);
    });
    if (typeEl) {
      typeEl.disabled = !on;
      if (!on) {
        typeEl.value = '';
      }
    }
    if (!on) {
      clearGstSelection();
    }
    syncGstCalcVisibility();
  };

  gstApplicableEl.addEventListener('change', syncGstApplicable);
  typeEl.addEventListener('change', syncGstCalcVisibility);

  const bindDropdownOpen = function (searchEl, menuSelector) {
    if (!searchEl) return;
    searchEl.addEventListener('click', function (e) {
      e.stopPropagation();
      const menu = searchEl.closest('.tax-dropdown-wrapper')?.querySelector(menuSelector);
      if (!menu) return;
      document.querySelectorAll(menuSelector).forEach(function (m) {
        if (m !== menu) m.classList.remove('show');
      });
      menu.classList.toggle('show');
      searchEl.removeAttribute('readonly');
    });
    searchEl.addEventListener('blur', function () {
      setTimeout(function () {
        const wrap = searchEl.closest('.tax-dropdown-wrapper');
        if (wrap && wrap.contains(document.activeElement)) {
          return;
        }
        searchEl.setAttribute('readonly', 'readonly');
      }, 250);
    });
  };

  bindDropdownOpen(gstSearchEl, '.ra-gst-tax-dropdown-menu');
  bindDropdownOpen(tdsSearchEl, '.ra-tds-tax-dropdown-menu');

  document.addEventListener('mousedown', function (e) {
    const gstItem = e.target.closest('#ra_tax_gst_list div[data-id]');
    if (gstItem) {
      e.preventDefault();
      const name = gstItem.getAttribute('data-name') || gstItem.textContent.trim();
      const rate = gstItem.getAttribute('data-value') || '';
      const id = gstItem.getAttribute('data-id') || '';
      const type = gstItem.getAttribute('data-type') || 'GST';

      if (gstSearchEl) gstSearchEl.value = formatGstSearchLabel(rate) || name;
      if (gstNameEl) gstNameEl.value = name;
      if (gstPctEl) gstPctEl.value = rate;
      if (gstTaxIdEl) gstTaxIdEl.value = id;
      if (gstTaxTypeEl) gstTaxTypeEl.value = type;

      gstItem.closest('.tax-dropdown')?.classList.remove('show');
      if (gstSearchEl) gstSearchEl.setAttribute('readonly', 'readonly');
      raGstCalculate();
      return;
    }

    const tdsItem = e.target.closest('#ra_tax_tds_list div[data-id]');
    if (tdsItem) {
      e.preventDefault();
      const name = tdsItem.getAttribute('data-name') || '';
      const rate = tdsItem.getAttribute('data-value') || '';
      const id = tdsItem.getAttribute('data-id') || '';
      const sectionId = tdsItem.getAttribute('data-section-id') || '';
      const sectionName = tdsItem.getAttribute('data-section-name') || '';

      if (tdsSearchEl) tdsSearchEl.value = formatTdsSearchLabel(rate) || tdsItem.textContent.trim();
      if (tdsNameEl) tdsNameEl.value = name;
      if (tdsRateEl) tdsRateEl.value = rate;
      if (tdsTaxIdEl) tdsTaxIdEl.value = id;
      if (tdsSectionIdEl) tdsSectionIdEl.value = sectionId;
      if (tdsSectionEl) tdsSectionEl.value = sectionName;
      if (tdsSectionDisplayEl) {
        tdsSectionDisplayEl.value = formatTdsSectionDisplay(sectionName, name);
      }

      tdsItem.closest('.tax-dropdown')?.classList.remove('show');
      if (tdsSearchEl) tdsSearchEl.setAttribute('readonly', 'readonly');
      raTdsCalculate();
      return;
    }

    if (!e.target.closest('.ra-gst-dropdown') && !e.target.closest('.ra-tds-dropdown')) {
      document.querySelectorAll('.ra-gst-tax-dropdown-menu, .ra-tds-tax-dropdown-menu').forEach(function (m) {
        m.classList.remove('show');
      });
    }
  });

  [rentEl, maintEl].forEach(function (el) {
    if (!el) return;
    el.addEventListener('input', function () {
      raGstCalculate();
      raTdsCalculate();
    });
    el.addEventListener('change', function () {
      raGstCalculate();
      raTdsCalculate();
    });
  });

  syncGstApplicable();
  raTdsCalculate();
})();


/* ========== Register filters ========== */
/**
 * Rental agreements register — multi-select filters (same pattern as payment requests / bill module).
 */
(function ($) {
  const filterFormConfigs = [
    { selector: '#ra-filter-form', branchesNodeId: 'raBranchesData' },
    { selector: '#llp-report-filter-form', branchesNodeId: 'llpReportBranchesData' },
    { selector: '#llp-payments-filter-form', branchesNodeId: 'llpPaymentsBranchesData' },
  ];
  const activeForms = filterFormConfigs
    .map(function (cfg) {
      const $form = $(cfg.selector);
      if (!$form.length) {
        return null;
      }
      const branchDataNode = document.getElementById(cfg.branchesNodeId);
      const branches = branchDataNode ? JSON.parse(branchDataNode.textContent || '[]') : [];
      return { $form: $form, branches: branches, selector: cfg.selector };
    })
    .filter(Boolean);

  if (!activeForms.length) {
    return;
  }

  const submitTimers = {};
  const formSelector = activeForms.map(function (f) { return f.selector; }).join(', ');

  function ctxForForm($form) {
    const id = $form.attr('id');
    for (let i = 0; i < activeForms.length; i++) {
      if (activeForms[i].$form.attr('id') === id) {
        return activeForms[i];
      }
    }
    return null;
  }

  function ctxForDropdown($dropdown) {
    const $wrap = $dropdown.data('wrapper');
    if ($wrap && $wrap.length) {
      return ctxForForm($wrap.closest('form'));
    }
    return null;
  }

  function submitNow($form) {
    const key = $form.attr('id') || 'filter-form';
    if (submitTimers[key]) {
      clearTimeout(submitTimers[key]);
      submitTimers[key] = null;
    }
    if ($form[0]) {
      $form[0].submit();
    }
  }

  function scheduleSubmit($form) {
    const key = $form.attr('id') || 'filter-form';
    if (submitTimers[key]) {
      clearTimeout(submitTimers[key]);
    }
    submitTimers[key] = setTimeout(function () {
      submitTimers[key] = null;
      submitNow($form);
    }, 380);
  }

  function syncRaArray($form, paramName, ids) {
    const $box = $form.find('.pay-pr-array-hiddens[data-array-name="' + paramName + '"]');
    $box.empty();
    $.each(ids, function (_, id) {
      if (id === '' || id === null || id === undefined) {
        return;
      }
      $box.append($('<input>', { type: 'hidden', name: paramName + '[]', value: String(id) }));
    });
  }

  function selectedIdsFor($form, paramName) {
    const ids = [];
    $form.find('.pay-pr-array-hiddens[data-array-name="' + paramName + '"] input').each(function () {
      ids.push(String($(this).val()));
    });
    return ids;
  }

  function renderBranchList(ctx, $targetList) {
    const $form = ctx.$form;
    const branches = ctx.branches;
    const $branchWrap = $form.find('.pay-pr-dd[data-filter-param="branch_id"]').first();
    const $branchList = $targetList && $targetList.length
      ? $targetList
      : $branchWrap.find('.branch-list').first();
    if (!$branchList.length) {
      return;
    }

    const zoneIds = selectedIdsFor($form, 'zone_id');
    const currentBranchIds = selectedIdsFor($form, 'branch_id');
    const filteredBranches = zoneIds.length === 0
      ? branches
      : branches.filter(function (branch) {
        return zoneIds.indexOf(String(branch.zone_id)) > -1;
      });

    $branchList.empty();

    if (!filteredBranches.length) {
      const emptyText = 'No branches found';
      const emptyEl = document.createElement('div');
      emptyEl.className = 'ra-dd-empty';
      emptyEl.textContent = emptyText;
      $branchList.append(emptyEl);
      return;
    }

    filteredBranches.forEach(function (branch) {
      const id = String(branch.id);
      $branchList.append($('<div>', {
        'data-id': id,
        'data-value': branch.name,
        'class': currentBranchIds.indexOf(id) > -1 ? 'selected' : '',
        text: branch.name
      }));
    });
  }

  function isSingleSelectWrapper($wrapper) {
    if (!$wrapper || !$wrapper.length) {
      return false;
    }
    return String($wrapper.data('single-select') || $wrapper.attr('data-single-select') || '') === '1';
  }

  function updateSingleSelectDropdown($dropdown) {
    const $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length) {
      return;
    }
    const ctx = ctxForDropdown($dropdown);
    if (!ctx) {
      return;
    }
    const $form = ctx.$form;
    const param = String($wrapper.data('filter-param') || '');
    const emptyLbl = $wrapper.data('empty-label') || 'All';
    const $selected = $dropdown.find('.dropdown-list.multiselect div.selected').not('.ra-dd-empty').first();
    const id = $selected.length ? String($selected.attr('data-id') || 'all') : 'all';
    const text = $selected.length ? $selected.text().trim() : emptyLbl;

    $wrapper.find('.pay-pr-dd-input').val(text);
    $form.find('input[name="' + param + '"]').val(id);
    scheduleSubmit($form);
  }

  function updateRaDropdown($dropdown) {
    const ctx = ctxForDropdown($dropdown);
    if (!ctx) {
      return;
    }
    const $form = ctx.$form;
    const branches = ctx.branches;
    const $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length) {
      return;
    }

    if (isSingleSelectWrapper($wrapper)) {
      updateSingleSelectDropdown($dropdown);
      return;
    }

    const param = $wrapper.data('filter-param');
    const emptyLbl = $wrapper.data('empty-label') || 'All';
    const texts = [];
    const ids = [];

    $dropdown.find('.dropdown-list.multiselect div').each(function () {
      if (!$(this).hasClass('selected') || $(this).hasClass('ra-dd-empty')) {
        return;
      }
      texts.push($(this).text().trim());
      ids.push(String($(this).attr('data-id')));
    });

    $wrapper.find('.pay-pr-dd-input').val(texts.length ? texts.join(', ') : emptyLbl);
    syncRaArray($form, param, ids);

    if (param === 'zone_id') {
      const allowedBranchIds = [];
      const zoneIds = ids;
      const filteredBranches = zoneIds.length === 0
        ? branches
        : branches.filter(function (branch) {
          return zoneIds.indexOf(String(branch.zone_id)) > -1;
        });
      filteredBranches.forEach(function (branch) {
        allowedBranchIds.push(String(branch.id));
      });

      const keptBranchIds = selectedIdsFor($form, 'branch_id').filter(function (bid) {
        return allowedBranchIds.indexOf(bid) > -1;
      });
      syncRaArray($form, 'branch_id', keptBranchIds);

      const branchTexts = [];
      keptBranchIds.forEach(function (bid) {
        const match = branches.find(function (b) { return String(b.id) === bid; });
        if (match) {
          branchTexts.push(match.name);
        }
      });
      const $branchWrap = $form.find('.pay-pr-dd[data-filter-param="branch_id"]').first();
      const branchEmpty = $branchWrap.data('empty-label') || 'All branches';
      $branchWrap.find('.pay-pr-dd-input').val(branchTexts.length ? branchTexts.join(', ') : branchEmpty);
      $branchWrap.find('.branch-list div').each(function () {
        const id = String($(this).attr('data-id') || '');
        $(this).toggleClass('selected', keptBranchIds.indexOf(id) > -1);
      });
      renderBranchList(ctx);
    }

    scheduleSubmit($form);
  }

  function positionRaDropdown($input, $dropdown) {
    const el = $input[0];
    if (!el || !$dropdown || !$dropdown.length) {
      return;
    }
    const rect = el.getBoundingClientRect();
    const width = Math.max(rect.width, 260);
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth || 0;
    let left = rect.left;
    if (left + width > viewportWidth - 8) {
      left = Math.max(8, viewportWidth - width - 8);
    }
    $dropdown.css({
      position: 'fixed',
      top: rect.bottom + 4,
      left: left,
      width: width,
      zIndex: 10050
    });
  }

  $(window).on('scroll.raFilterDd resize.raFilterDd', function () {
    $('.dropdown-menu.tax-dropdown.pay-pr-tax-dd:visible').each(function () {
      const $dd = $(this);
      const $wrap = $dd.data('wrapper');
      if (!$wrap || !$wrap.length) {
        return;
      }
      const $inp = $wrap.find('.pay-pr-dd-input').first();
      if ($inp.length) {
        positionRaDropdown($inp, $dd);
      }
    });
  });

  $(document).on('click', formSelector + ' .pay-pr-dd-input', function (e) {
    e.stopPropagation();
    const ctx = ctxForForm($(this).closest('form'));
    if (!ctx) {
      return;
    }
    $('.dropdown-menu.tax-dropdown.pay-pr-tax-dd').hide();

    const $input = $(this);
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }

    $dropdown.addClass('pay-pr-tax-dd');
    $dropdown.data('wrapper', $input.closest('.pay-pr-dd'));

    const param = $input.closest('.pay-pr-dd').data('filter-param');
    if (param === 'branch_id') {
      renderBranchList(ctx, $dropdown.find('.branch-list'));
    }

    positionRaDropdown($input, $dropdown);
    $dropdown.show();
    $dropdown.find('.inner-search').first().val('');
    $dropdown.find('.dropdown-list.multiselect div').show();
    $dropdown.find('.inner-search').first().focus();
  });

  $(document).on('keyup', '.pay-pr-tax-dd .inner-search', function () {
    const q = ($(this).val() || '').toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list.multiselect div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });

  $(document).on('click', '.pay-pr-tax-dd .dropdown-list.multiselect div', function (e) {
    e.stopPropagation();
    if ($(this).hasClass('ra-dd-empty')) {
      return;
    }
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrap = $dropdown.data('wrapper');
    if (isSingleSelectWrapper($wrap)) {
      $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
      $(this).addClass('selected');
      updateSingleSelectDropdown($dropdown);
      $dropdown.hide();
      return;
    }
    $(this).toggleClass('selected');
    updateRaDropdown($dropdown);
  });

  $(document).on('click', '.pay-pr-tax-dd .select-all', function (e) {
    e.stopPropagation();
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrap = $dropdown.data('wrapper');
    if (isSingleSelectWrapper($wrap)) {
      $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
      $dropdown.find('.dropdown-list.multiselect div[data-id="all"]').addClass('selected');
      updateSingleSelectDropdown($dropdown);
      return;
    }
    $dropdown.find('.dropdown-list.multiselect div').not('.ra-dd-empty').addClass('selected');
    updateRaDropdown($dropdown);
  });

  $(document).on('click', '.pay-pr-tax-dd .deselect-all', function (e) {
    e.stopPropagation();
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrap = $dropdown.data('wrapper');
    if (isSingleSelectWrapper($wrap)) {
      $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
      $dropdown.find('.dropdown-list.multiselect div[data-id="all"]').addClass('selected');
      updateSingleSelectDropdown($dropdown);
      $dropdown.hide();
      return;
    }
    $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
    updateRaDropdown($dropdown);
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest(formSelector + ' .tax-dropdown-wrapper').length) {
      return;
    }
    if ($(e.target).closest('.dropdown-menu.tax-dropdown.pay-pr-tax-dd').length) {
      return;
    }
    $('.dropdown-menu.tax-dropdown.pay-pr-tax-dd').hide();
  });

  $('#ra_universal_search').on('input', function () {
    const $raForm = $('#ra-filter-form');
    if ($raForm.length) {
      scheduleSubmit($raForm);
    }
  });

  activeForms.forEach(function (ctx) {
    renderBranchList(ctx);
  });

  const $raForm = $('#ra-filter-form');
  if ($raForm.length) {
    window.raRegisterFilters = {
      submitNow: function () { submitNow($raForm); },
      scheduleSubmit: function () { scheduleSubmit($raForm); },
    };
  }

  function bindLlpDateRange($form, $dateWrap, $fromInput, $toInput, $labelEl) {
    if (typeof $.fn.daterangepicker !== 'function' || typeof moment === 'undefined' || !$dateWrap.length) {
      return;
    }
    const df = $fromInput.val();
    const dt = $toInput.val();
    const opts = {
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD', separator: ' – ', cancelLabel: 'Clear', applyLabel: 'Apply' },
      opens: 'left',
      drops: 'down',
    };
    if (df && dt) {
      opts.startDate = moment(df);
      opts.endDate = moment(dt);
    }
    $dateWrap.daterangepicker(opts);
    $dateWrap.on('apply.daterangepicker', function (ev, picker) {
      $fromInput.val(picker.startDate.format('YYYY-MM-DD'));
      $toInput.val(picker.endDate.format('YYYY-MM-DD'));
      $labelEl.text(picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY'));
      submitNow($form);
    });
    $dateWrap.on('cancel.daterangepicker', function () {
      $fromInput.val('');
      $toInput.val('');
      $labelEl.text('All dates');
      submitNow($form);
    });
  }

  const $llpForm = $('#llp-report-filter-form');
  if ($llpForm.length) {
    window.llpReportFilters = {
      submitNow: function () { submitNow($llpForm); },
      scheduleSubmit: function () { scheduleSubmit($llpForm); },
    };
    $llpForm.find('#llp_report_billing_month').on('change', function () {
      submitNow($llpForm);
    });
    bindLlpDateRange(
      $llpForm,
      $('#llpReportDateRange'),
      $('#llp_report_date_from'),
      $('#llp_report_date_to'),
      $('#llpReportDateLabel')
    );
  }

  const $llpPaymentsForm = $('#llp-payments-filter-form');
  if ($llpPaymentsForm.length) {
    window.llpPaymentsFilters = {
      submitNow: function () { submitNow($llpPaymentsForm); },
      scheduleSubmit: function () { scheduleSubmit($llpPaymentsForm); },
    };
    bindLlpDateRange(
      $llpPaymentsForm,
      $('#llpPaymentsDateRange'),
      $('#llp_payments_date_from'),
      $('#llp_payments_date_to'),
      $('#llpPaymentsDateLabel')
    );
  }
})(jQuery);


/* ========== Create form page ========== */
/**
 * Rental agreement create/edit — period picker, location dropdowns, attachments, RCM, flash toasts.
 */
(function ($) {
  'use strict';

  const picker = $('#agreementPeriodPicker');
  const startInput = $('#agreementPeriodStart');
  const endInput = $('#agreementPeriodEnd');
  const endAgreementDate = $('#endAgreementDate');
  const attachmentInput = document.getElementById('rental_attachment_file');
  const attachmentPreviewBar = document.getElementById('rental-attachment-preview-bar');
  const attachmentPreviewName = document.getElementById('rental-attachment-preview-name');
  const attachmentPreviewSize = document.getElementById('rental-attachment-preview-size');
  const root = document.getElementById('payLocationStrip');
  const flashData = document.getElementById('rentalFlashData');
  const branchesDataNode = document.getElementById('rentalBranchesData');

  if (flashData) {
    const successMessage = flashData.getAttribute('data-success') || '';
    const errorMessage = flashData.getAttribute('data-error') || '';
    if (successMessage && window.toastr) {
      toastr.success(successMessage);
    }
    if (errorMessage && window.toastr) {
      toastr.error(errorMessage);
    }

    let validationErrors = [];
    try {
      validationErrors = JSON.parse(flashData.getAttribute('data-validation-errors') || '[]');
    } catch (e) {
      validationErrors = [];
    }
    if (validationErrors.length) {
      if (window.FormFieldValidation) {
        FormFieldValidation.showBackendToasts(validationErrors, {
          summary: validationErrors.length > 1 ? 'Please correct the highlighted fields.' : '',
        });
      } else if (window.toastr) {
        validationErrors.forEach(function (msg, idx) {
          setTimeout(function () {
            toastr.error(msg);
          }, idx * 120);
        });
      }
    }
  }

  function bindFilePreview(input, previewBar, previewName, previewSize) {
    if (!input || !previewBar || !previewName || !previewSize) {
      return;
    }
    input.addEventListener('change', function () {
      const file = this.files && this.files[0] ? this.files[0] : null;
      if (!file) {
        previewBar.hidden = true;
        previewName.textContent = '';
        previewName.title = '';
        previewSize.textContent = '';
        return;
      }
      const sizeInKb = file.size / 1024;
      const sizeLabel =
        sizeInKb >= 1024
          ? (sizeInKb / 1024).toFixed(2) + ' MB'
          : Math.max(1, Math.round(sizeInKb)) + ' KB';
      previewName.textContent = file.name;
      previewName.title = file.name;
      previewSize.textContent = sizeLabel;
      previewBar.hidden = false;
    });
  }

  bindFilePreview(
    attachmentInput,
    attachmentPreviewBar,
    attachmentPreviewName,
    attachmentPreviewSize
  );
  bindFilePreview(
    document.getElementById('rental_building_photo_file'),
    document.getElementById('rental-building-photo-preview-bar'),
    document.getElementById('rental-building-photo-preview-name'),
    document.getElementById('rental-building-photo-preview-size')
  );

  if (root) {
    const branches = branchesDataNode ? JSON.parse(branchesDataNode.textContent || '[]') : [];
    const strip = root;
    const closeAllPanels = function () {
      root.querySelectorAll('.pr-dd-panel').forEach(function (panel) {
        panel.classList.remove('show');
      });
    };
    const filterList = function (panel, term) {
      const query = String(term || '').trim().toLowerCase();
      const list =
        panel.querySelector('.company-list') ||
        panel.querySelector('.zone-list') ||
        panel.querySelector('.branch-list') ||
        panel.querySelector('.vendor-list');
      if (!list) {
        return;
      }
      list.querySelectorAll('div[data-id]').forEach(function (item) {
        const text = (item.getAttribute('data-value') || item.textContent || '').toLowerCase();
        item.style.display = !query || text.indexOf(query) !== -1 ? '' : 'none';
      });
    };
    const loadBranchesForZone = function (zoneId) {
      const branchPanel = strip.querySelector('.branch-list');
      if (!branchPanel) {
        return;
      }
      branchPanel.innerHTML = '';
      const zid = String(zoneId || '').trim();
      if (!zid) {
        return;
      }
      branches.forEach(function (branch) {
        if (String(branch.zone_id) !== zid) {
          return;
        }
        const div = document.createElement('div');
        div.setAttribute('data-id', String(branch.id));
        div.setAttribute('data-value', branch.name);
        div.textContent = branch.name;
        branchPanel.appendChild(div);
      });
    };

    root.addEventListener('click', function (e) {
      if (!e.target.closest('.pr-dd-wrap')) {
        closeAllPanels();
      }
    });

    root.querySelectorAll('.pr-dd-input').forEach(function (inp) {
      inp.addEventListener('click', function (ev) {
        ev.stopPropagation();
        closeAllPanels();
        const panel = this.closest('.pr-dd-wrap').querySelector('.pr-dd-panel');
        if (panel) {
          panel.classList.add('show');
          const inner = panel.querySelector('.inner-search');
          if (inner) {
            inner.value = '';
            filterList(panel, '');
            inner.focus();
          }
        }
      });
    });

    root.querySelectorAll('.pr-dd-panel .inner-search').forEach(function (inner) {
      inner.addEventListener('input', function () {
        filterList(this.closest('.pr-dd-panel'), this.value);
      });
      inner.addEventListener('click', function (ev) {
        ev.stopPropagation();
      });
    });

    root.querySelectorAll('.company-list div').forEach(function (div) {
      div.addEventListener('click', function (ev) {
        ev.stopPropagation();
        const wrap = this.closest('.pr-dd-wrap');
        wrap.querySelector('.company-search-input').value =
          this.getAttribute('data-value') || this.textContent.trim();
        wrap.querySelector('.company_id').value = this.getAttribute('data-id') || '';
        this.closest('.pr-dd-panel').classList.remove('show');
      });
    });

    root.querySelectorAll('.vendor-list div').forEach(function (div) {
      div.addEventListener('click', function (ev) {
        ev.stopPropagation();
        const wrap = this.closest('.pr-dd-wrap');
        const val = this.getAttribute('data-value') || this.textContent.trim();
        wrap.querySelector('.vendor-search-input').value = val;
        wrap.querySelector('.vendor_id').value = this.getAttribute('data-id') || '';
        const ownerHidden = wrap.querySelector('.owner_name') || document.getElementById('rental_owner_name');
        if (ownerHidden) {
          ownerHidden.value = val;
        }
        const pan = this.getAttribute('data-pan') || '';
        const panInput = document.querySelector('input[name="pan_number"]');
        if (panInput && pan && !String(panInput.value || '').trim()) {
          panInput.value = pan;
        }
        this.closest('.pr-dd-panel').classList.remove('show');
      });
    });

    root.querySelectorAll('.zone-list div').forEach(function (div) {
      div.addEventListener('click', function (ev) {
        ev.stopPropagation();
        const wrap = this.closest('.pr-dd-wrap');
        const zoneId = this.getAttribute('data-id') || '';
        wrap.querySelector('.zone-search-input').value =
          this.getAttribute('data-value') || this.textContent.trim();
        wrap.querySelector('.zone_id').value = zoneId;
        this.closest('.pr-dd-panel').classList.remove('show');

        const branchInput = strip.querySelector('.branch-search-input');
        const branchHidden = strip.querySelector('.branch_id');
        if (branchInput) {
          branchInput.value = '';
        }
        if (branchHidden) {
          branchHidden.value = '';
        }
        loadBranchesForZone(zoneId);
      });
    });

    strip.addEventListener('click', function (ev) {
      const item = ev.target.closest('.branch-list div[data-id]');
      if (!item) {
        return;
      }
      ev.stopPropagation();
      const wrap = item.closest('.pr-dd-wrap');
      wrap.querySelector('.branch-search-input').value =
        item.getAttribute('data-value') || item.textContent.trim();
      wrap.querySelector('.branch_id').value = item.getAttribute('data-id') || '';
      const panel = wrap.querySelector('.pr-dd-panel');
      if (panel) {
        panel.classList.remove('show');
      }
    });

    const zoneHidden = strip.querySelector('.zone_id');
    const branchHidden = strip.querySelector('.branch_id');
    const preBranchId = branchHidden ? String(branchHidden.value || '').trim() : '';
    if (zoneHidden && zoneHidden.value) {
      loadBranchesForZone(zoneHidden.value);
      if (preBranchId) {
        const branchPanel = strip.querySelector('.branch-list');
        const branchInput = strip.querySelector('.branch-search-input');
        if (branchPanel && branchInput) {
          let found = null;
          branchPanel.querySelectorAll('div[data-id]').forEach(function (el) {
            if (String(el.getAttribute('data-id')) === preBranchId) {
              found = el;
            }
          });
          if (found) {
            branchInput.value = found.getAttribute('data-value') || found.textContent.trim();
          }
        }
      }
    }
  }

  if (!picker.length || typeof picker.daterangepicker !== 'function') {
    return;
  }

  const startDate = startInput.val() ? moment(startInput.val(), 'YYYY-MM-DD') : moment();
  const endDate = endInput.val() ? moment(endInput.val(), 'YYYY-MM-DD') : startDate.clone();

  const syncRange = function (start, end) {
    picker.val(start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
    startInput.val(start.format('YYYY-MM-DD'));
    endInput.val(end.format('YYYY-MM-DD'));
    endAgreementDate.val(end.format('YYYY-MM-DD'));
  };

  picker.daterangepicker({
    autoUpdateInput: false,
    alwaysShowCalendars: true,
    opens: 'left',
    startDate: startDate,
    endDate: endDate,
    locale: {
      format: 'DD-MM-YYYY',
      cancelLabel: 'Clear',
    },
  });

  if (startInput.val() && endInput.val()) {
    syncRange(startDate, endDate);
  }

  picker.on('apply.daterangepicker', function (ev, drp) {
    syncRange(drp.startDate, drp.endDate);
  });

  picker.on('cancel.daterangepicker', function () {
    picker.val('');
    startInput.val('');
    endInput.val('');
  });
})(jQuery);

(function () {
  'use strict';

  const rcmSelect = document.getElementById('ra_rcm_applicable');
  const rcmWrap = document.getElementById('ra_rcm_value_wrap');
  const rcmInput = document.getElementById('ra_rcm_value');
  if (!rcmSelect || !rcmWrap || !rcmInput) {
    return;
  }

  const syncRcm = function () {
    const yes = rcmSelect.value === '1';
    rcmWrap.classList.toggle('ra-rcm-value--hidden', !yes);
    rcmInput.disabled = !yes;
    rcmInput.required = yes;
    if (!yes) {
      rcmInput.value = '';
    }
  };

  rcmSelect.addEventListener('change', syncRcm);
  syncRcm();
})();


/* ========== Register page ========== */
/**
 * Rental agreement register — date range, charts, expandable rows, period popover.
 */
(function ($) {
  'use strict';

  const flashData = document.getElementById('raFlashData');
  if (flashData) {
    const successMessage = flashData.dataset.success || '';
    const errorMessage = flashData.dataset.error || '';
    if (successMessage && window.toastr) {
      toastr.success(successMessage);
    }
    if (errorMessage && window.toastr) {
      toastr.error(errorMessage);
    }
  }

  const $form = $('#ra-filter-form');

  function submitFilterForm() {
    if (window.raRegisterFilters && typeof window.raRegisterFilters.submitNow === 'function') {
      window.raRegisterFilters.submitNow();
      return;
    }
    if ($form.length && $form[0]) {
      $form[0].submit();
    }
  }

  const $dateWrap = $('#raReportRange');
  if (typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined' && $dateWrap.length) {
    const df = $('#ra_date_from').val();
    const dt = $('#ra_date_to').val();
    const opts = {
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD', separator: ' – ', cancelLabel: 'Clear', applyLabel: 'Apply' },
      opens: 'left',
      drops: 'down',
    };

    if (df && dt) {
      opts.startDate = moment(df);
      opts.endDate = moment(dt);
    }

    $dateWrap.daterangepicker(opts);
    $dateWrap.on('apply.daterangepicker', function (ev, picker) {
      $('#ra_date_from').val(picker.startDate.format('YYYY-MM-DD'));
      $('#ra_date_to').val(picker.endDate.format('YYYY-MM-DD'));
      $('#raDateLabel').text(
        picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY')
      );
      submitFilterForm();
    });

    $dateWrap.on('cancel.daterangepicker', function () {
      $('#ra_date_from').val('');
      $('#ra_date_to').val('');
      $('#raDateLabel').text('All dates');
      submitFilterForm();
    });
  }

  $('#pay-pr-per-page').on('change', function () {
    submitFilterForm();
  });

  const $chartPanel = $('#raChartPanel');
  const $chartLayout = $('#raChartLayout');
  const $chartToggle = $('#raChartToggle');
  let rentByTypeChart = null;
  let topOwnersChart = null;

  function readChartData() {
    const node = document.getElementById('raChartData');
    if (!node) {
      return null;
    }
    try {
      return JSON.parse(node.textContent || '{}');
    } catch (e) {
      return null;
    }
  }

  function initCharts() {
    if (typeof Chart === 'undefined') {
      return;
    }
    const data = readChartData();
    if (!data) {
      return;
    }

    const rentByType = data.rentByType || {};
    const typeLabels = data.typeLabels || { hospital: 'Hospital', hostel: 'Hostel' };
    const rentLabels = [typeLabels.hospital || 'Hospital', typeLabels.hostel || 'Hostel'];
    const rentValues = [Number(rentByType.hospital || 0), Number(rentByType.hostel || 0)];

    const rentCanvas = document.getElementById('raRentByTypeChart');
    if (rentCanvas) {
      if (rentByTypeChart) {
        rentByTypeChart.destroy();
      }
      rentByTypeChart = new Chart(rentCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: rentLabels,
          datasets: [
            {
              data: rentValues,
              backgroundColor: ['#6366f1', '#0ea5e9'],
              borderWidth: 0,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' },
            title: { display: true, text: 'Monthly rent by category', font: { size: 13, weight: '600' } },
          },
        },
      });
    }

    const topOwners = Array.isArray(data.topOwners) ? data.topOwners : [];
    const ownersCanvas = document.getElementById('raTopOwnersChart');
    if (ownersCanvas && topOwners.length) {
      if (topOwnersChart) {
        topOwnersChart.destroy();
      }
      topOwnersChart = new Chart(ownersCanvas.getContext('2d'), {
        type: 'bar',
        data: {
          labels: topOwners.map(function (o) {
            const name = String(o.owner || '');
            return name.length > 18 ? name.slice(0, 16) + '…' : name;
          }),
          datasets: [
            {
              label: 'Monthly rent',
              data: topOwners.map(function (o) {
                return Number(o.rent || 0);
              }),
              backgroundColor: '#818cf8',
              borderRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          plugins: {
            legend: { display: false },
            title: { display: true, text: 'Top owners by rent', font: { size: 13, weight: '600' } },
          },
          scales: {
            x: {
              ticks: {
                callback: function (v) {
                  return '₹' + Number(v).toLocaleString('en-IN');
                },
              },
            },
          },
        },
      });
    }
  }

  if ($chartToggle.length && $chartPanel.length) {
    $chartToggle.on('click', function () {
      const open = $chartPanel.hasClass('d-none');
      $chartPanel.toggleClass('d-none', !open);
      $chartLayout.toggleClass('ra-chart-layout--open', open);
      $chartToggle.attr('aria-expanded', open ? 'true' : 'false');
      if (open) {
        window.setTimeout(initCharts, 60);
      }
    });
  }

  function toggleDetailRow($row) {
    const id = $row.data('ra-row-id');
    if (!id) {
      return;
    }
    const $detail = $('tr.ra-detail-row[data-ra-detail-for="' + id + '"]');
    if (!$detail.length) {
      return;
    }
    const opening = $detail.hasClass('d-none');
    $('tr.ra-detail-row').addClass('d-none');
    $('tr.pay-pr-row').removeClass('ra-row-expanded');
    if (opening) {
      $detail.removeClass('d-none');
      $row.addClass('ra-row-expanded');
    }
  }

  $(document).on('click keydown', '.ra-expand-cell', function (e) {
    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
      return;
    }
    if ($(e.target).closest('a').length) {
      return;
    }
    e.preventDefault();
    toggleDetailRow($(this).closest('tr.pay-pr-row'));
  });

  const $periodPopover = $('#raPeriodPopover');
  let activePeriodBtn = null;

  function formatInr(n) {
    return '₹' + Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function buildPeriodPopoverHtml(period, schedule, hike, baseRent) {
    let rows = '';
    if (Array.isArray(schedule) && schedule.length) {
      schedule.forEach(function (yr, idx) {
        const tag = idx === 0 ? 'Current yr' : idx === 1 ? 'Next yr' : yr.label;
        rows +=
          '<tr><td class="ra-period-pop-lbl">' +
          tag +
          '</td><td>' +
          (yr.period_label || '—') +
          '</td><td class="text-end fw-semibold">' +
          formatInr(yr.monthly_rent) +
          '</td></tr>';
      });
    } else {
      rows =
        '<tr><td colspan="3" class="text-muted">No year breakdown — check agreement period and rent hike %.</td></tr>';
    }
    const hikeNote =
      Number(hike) > 0
        ? '<p class="ra-period-pop-note mb-0">Hike: <strong>' +
          hike +
          '%</strong> per year on base ' +
          formatInr(baseRent) +
          '.</p>'
        : '<p class="ra-period-pop-note mb-0">Flat rent ' + formatInr(baseRent) + ' (no hike % set).</p>';

    return (
      '<div class="ra-period-pop-head"><strong>Agreement period</strong><span>' +
      period +
      '</span></div>' +
      '<table class="ra-period-pop-table"><thead><tr><th>Year</th><th>Period</th><th class="text-end">Monthly rent</th></tr></thead><tbody>' +
      rows +
      '</tbody></table>' +
      hikeNote
    );
  }

  function positionPeriodPopover($btn) {
    if (!$periodPopover.length || !$btn.length) {
      return;
    }
    const rect = $btn[0].getBoundingClientRect();
    const popW = Math.min(420, window.innerWidth - 16);
    let left = rect.left;
    if (left + popW > window.innerWidth - 8) {
      left = window.innerWidth - popW - 8;
    }
    $periodPopover.css({
      position: 'fixed',
      top: rect.bottom + 8,
      left: Math.max(8, left),
      width: popW,
      zIndex: 10060,
    });
  }

  function closePeriodPopover() {
    if ($periodPopover.length) {
      $periodPopover.addClass('d-none').empty();
    }
    if (activePeriodBtn) {
      activePeriodBtn.removeClass('ra-period-link--active');
    }
    activePeriodBtn = null;
  }

  $(document).on('click', '.ra-period-link', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $btn = $(this);
    if (activePeriodBtn && activePeriodBtn[0] === $btn[0]) {
      closePeriodPopover();
      return;
    }
    closePeriodPopover();
    activePeriodBtn = $btn;
    $btn.addClass('ra-period-link--active');
    let schedule = [];
    try {
      schedule = JSON.parse($btn.attr('data-schedule') || '[]');
    } catch (err) {
      schedule = [];
    }
    $periodPopover
      .html(
        buildPeriodPopoverHtml($btn.data('period') || '', schedule, $btn.data('hike') || 0, $btn.data('base-rent') || 0)
      )
      .removeClass('d-none');
    positionPeriodPopover($btn);
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('.ra-period-link, #raPeriodPopover').length) {
      return;
    }
    closePeriodPopover();
  });

  $(window).on('scroll resize', function () {
    if (activePeriodBtn) {
      positionPeriodPopover(activePeriodBtn);
    }
  });
})(jQuery);


/* ========== Rental agreement modal ========== */
(function () {
  'use strict';

  var raPreviewModalEl = document.getElementById('raUploadPreviewModal');
  var raPreviewBlob = null;
  var raBlobUrls = [];

  function raRevokePreviewBlob() {
    if (raPreviewBlob) {
      try { URL.revokeObjectURL(raPreviewBlob); } catch (err) {}
      raPreviewBlob = null;
    }
  }

  function raRevokeCardBlob(thumbBtn) {
    if (thumbBtn && thumbBtn._raBlobUrl) {
      try { URL.revokeObjectURL(thumbBtn._raBlobUrl); } catch (err) {}
      thumbBtn._raBlobUrl = null;
    }
  }

  function raPreviewFileKind(file) {
    if (!file) return 'other';
    var type = String(file.type || '');
    var name = String(file.name || '').toLowerCase();
    if (type === 'application/pdf' || name.endsWith('.pdf')) return 'pdf';
    if (type.indexOf('image/') === 0 || /\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(name)) return 'image';
    return 'other';
  }

  function raFileTypeMeta(name) {
    var n = String(name || '').toLowerCase();
    var ext = n.indexOf('.') !== -1 ? n.split('.').pop() : '';
    if (ext === 'pdf') return { badge: 'PDF', cls: 'ra-attach-type-pdf', kind: 'pdf' };
    if (ext === 'doc' || ext === 'docx') return { badge: 'DOC', cls: 'ra-attach-type-doc', kind: 'doc' };
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'].indexOf(ext) !== -1) {
      return { badge: ext === 'jpeg' ? 'JPG' : ext.toUpperCase(), cls: 'ra-attach-type-img', kind: 'image' };
    }
    return { badge: ext ? ext.toUpperCase() : 'FILE', cls: 'ra-attach-type-file', kind: 'other' };
  }

  function raShowUrlPreview(url, title) {
    if (!url) { if (window.toastr) toastr.error('Preview is unavailable.'); return; }
    if (!raPreviewModalEl) { if (window.toastr) toastr.error('Preview is unavailable on this page.'); return; }
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
      if (window.toastr) toastr.error('UI library not loaded. Please refresh the page.');
      return;
    }
    raRevokePreviewBlob();
    var n = String(title || url || '').toLowerCase();
    var kind = 'other';
    if (/\.pdf($|\?)/i.test(n) || n.indexOf('.pdf') !== -1) kind = 'pdf';
    else if (/\.(jpe?g|png|gif|webp|bmp|svg)($|\?)/i.test(n)) kind = 'image';
    var mt = document.getElementById('raPreviewModalTitle');
    if (mt) mt.textContent = title || 'Document preview';
    var iframe = document.getElementById('raPreviewIframe');
    var img = document.getElementById('raPreviewImg');
    var fb = document.getElementById('raPreviewFallback');
    var fbname = document.getElementById('raPreviewFallbackName');
    if (iframe) { iframe.classList.add('d-none'); iframe.removeAttribute('src'); }
    if (img) { img.classList.add('d-none'); img.removeAttribute('src'); }
    if (fb) fb.classList.add('d-none');
    if (kind === 'pdf' && iframe) { iframe.classList.remove('d-none'); iframe.src = url; }
    else if (kind === 'image' && img) { img.classList.remove('d-none'); img.src = url; img.alt = title || ''; }
    else if (fb) { fb.classList.remove('d-none'); if (fbname) fbname.textContent = title || ''; }
    try { bootstrap.Modal.getOrCreateInstance(raPreviewModalEl).show(); }
    catch (err) { if (window.toastr) toastr.error('Could not open the preview window.'); }
  }

  function raShowFilePreview(file) {
    if (!file) { if (window.toastr) toastr.error('No file selected.'); return; }
    if (!raPreviewModalEl) { if (window.toastr) toastr.error('Preview is unavailable on this page.'); return; }
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
      if (window.toastr) toastr.error('UI library not loaded. Please refresh the page.');
      return;
    }
    raRevokePreviewBlob();
    var kind = raPreviewFileKind(file);
    if (kind === 'pdf' || kind === 'image') raPreviewBlob = URL.createObjectURL(file);
    var mt = document.getElementById('raPreviewModalTitle');
    if (mt) mt.textContent = file.name || 'Document preview';
    var iframe = document.getElementById('raPreviewIframe');
    var img = document.getElementById('raPreviewImg');
    var fb = document.getElementById('raPreviewFallback');
    var fbname = document.getElementById('raPreviewFallbackName');
    if (iframe) { iframe.classList.add('d-none'); iframe.removeAttribute('src'); }
    if (img) { img.classList.add('d-none'); img.removeAttribute('src'); }
    if (fb) fb.classList.add('d-none');
    if (kind === 'pdf' && iframe) { iframe.classList.remove('d-none'); iframe.src = raPreviewBlob; }
    else if (kind === 'image' && img) { img.classList.remove('d-none'); img.src = raPreviewBlob; img.alt = file.name || ''; }
    else if (fb) { fb.classList.remove('d-none'); if (fbname) fbname.textContent = file.name || ''; }
    try { bootstrap.Modal.getOrCreateInstance(raPreviewModalEl).show(); }
    catch (err) { raRevokePreviewBlob(); if (window.toastr) toastr.error('Could not open the preview window.'); }
  }

  function raBuildAttachCard(opts) {
    var meta = raFileTypeMeta(opts.name);
    var card = document.createElement('div');
    card.className = 'ra-attach-card';
    card.setAttribute('role', 'listitem');
    card.setAttribute('data-ra-new-attach-card', '');
    card.dataset.fileName = opts.name || '';
    var thumbHtml = '';
    if (meta.kind === 'image' && opts.previewSrc) {
      thumbHtml = '<img src="' + opts.previewSrc + '" alt="" class="ra-attach-thumb-media">';
    } else if (meta.kind === 'pdf' && opts.previewSrc) {
      thumbHtml = '<iframe src="' + opts.previewSrc + '#toolbar=0&navpanes=0&scrollbar=0" title="" class="ra-attach-thumb-media"></iframe>';
    } else {
      thumbHtml = '<span class="ra-attach-thumb-fallback" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>';
    }
    card.innerHTML =
      '<button type="button" class="ra-attach-remove" title="Remove attachment" aria-label="Remove ' + String(opts.name || 'file').replace(/"/g, '&quot;') + '">' +
      '<i class="bi bi-x-lg" aria-hidden="true"></i></button>' +
      '<button type="button" class="ra-attach-thumb">' +
      '<span class="ra-attach-thumb-inner ra-attach-thumb--' + meta.kind + '">' + thumbHtml + '</span></button>' +
      '<div class="ra-attach-foot"><span class="ra-attach-type-badge ' + meta.cls + '">' + meta.badge + '</span>' +
      '<span class="ra-attach-name" title="' + String(opts.name || '').replace(/"/g, '&quot;') + '">' + (opts.name || 'File') + '</span></div>' +
      '<span class="ra-attach-fold" aria-hidden="true"></span>';
    card.querySelector('.ra-attach-remove').addEventListener('click', function (e) {
      e.preventDefault(); e.stopPropagation();
      if (typeof opts.onRemove === 'function') opts.onRemove();
    });
    var thumbBtn = card.querySelector('.ra-attach-thumb');
    if (thumbBtn) {
      if (opts.previewSrc) { thumbBtn._raBlobUrl = opts.previewSrc; raBlobUrls.push(opts.previewSrc); }
      thumbBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (opts.file) raShowFilePreview(opts.file);
        else if (opts.previewSrc) raShowUrlPreview(opts.previewSrc, opts.name);
      });
    }
    return card;
  }

  function raUpdateAttachCount(sectionEl) {
    if (!sectionEl) return;
    var countEl = sectionEl.querySelector('[data-ra-attach-count]');
    if (!countEl) return;
    var gallery = sectionEl.querySelector('.ra-attach-grid');
    var visible = 0;
    if (gallery) {
      gallery.querySelectorAll('.ra-attach-card').forEach(function (card) {
        if (card.style.display !== 'none') visible += 1;
      });
    }
    countEl.classList.toggle('d-none', visible === 0);
    var numEl = countEl.querySelector('.fw-semibold');
    if (numEl) numEl.textContent = visible + (visible === 1 ? ' attachment' : ' attachments');
  }

  function raClearNewAttachCards(galleryEl) {
    if (!galleryEl) return;
    galleryEl.querySelectorAll('[data-ra-new-attach-card]').forEach(function (el) {
      raRevokeCardBlob(el.querySelector('.ra-attach-thumb'));
      el.remove();
    });
  }

  function raRenderSingleFile(input, galleryEl, sectionEl) {
    raClearNewAttachCards(galleryEl);
    if (!input || !galleryEl) return;
    var file = input.files && input.files[0] ? input.files[0] : null;
    galleryEl.querySelectorAll('[data-ra-existing-attach-card]').forEach(function (card) {
      card.style.display = file ? 'none' : '';
    });
    if (!file) { raUpdateAttachCount(sectionEl); return; }
    galleryEl.classList.add('ra-attach-grid');
    var kind = raPreviewFileKind(file);
    var previewSrc = kind === 'pdf' || kind === 'image' ? URL.createObjectURL(file) : '';
    var card = raBuildAttachCard({
      name: file.name,
      previewSrc: previewSrc,
      file: file,
      onRemove: function () {
        try {
          if (previewSrc) URL.revokeObjectURL(previewSrc);
          input.value = '';
          input.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (err) { if (window.toastr) toastr.error('Could not remove file.'); }
      }
    });
    galleryEl.appendChild(card);
    raUpdateAttachCount(sectionEl);
  }

  function raBindExistingAttachmentCards(scope) {
    (scope || document).querySelectorAll('[data-ra-existing-attach-card]').forEach(function (card) {
      if (card.dataset.raAttachBound === '1') return;
      card.dataset.raAttachBound = '1';
      var thumbBtn = card.querySelector('.ra-attach-thumb');
      if (thumbBtn) {
        thumbBtn.addEventListener('click', function (e) {
          e.preventDefault();
          raShowUrlPreview(thumbBtn.getAttribute('data-ra-preview-url'), card.dataset.fileName || '');
        });
      }
    });
  }

  function raSetupSingleFileUpload(input, box, galleryEl, sectionEl) {
    if (!box || !input) return;
    function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
      box.addEventListener(eventName, preventDefaults, false);
    });
    ['dragenter', 'dragover'].forEach(function (eventName) {
      box.addEventListener(eventName, function () { box.classList.add('dragover'); }, false);
    });
    ['dragleave', 'drop'].forEach(function (eventName) {
      box.addEventListener(eventName, function () { box.classList.remove('dragover'); }, false);
    });
    box.addEventListener('drop', function (e) {
      var dt = e.dataTransfer;
      if (!dt || !dt.files || !dt.files.length) return;
      try {
        var fileTransfer = new DataTransfer();
        fileTransfer.items.add(dt.files[0]);
        input.files = fileTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      } catch (err) { if (window.toastr) toastr.error('Could not add the dropped file.'); }
    }, false);
    input.addEventListener('change', function () { raRenderSingleFile(input, galleryEl, sectionEl); });
  }

  var raBuildingInput = document.getElementById('rental_building_photo_file');
  var raAttachmentInput = document.getElementById('rental_attachment_file');
  if (raBuildingInput || raAttachmentInput) {
    raSetupSingleFileUpload(raBuildingInput, document.getElementById('rental-building-photo-upload-box'), document.getElementById('rental-building-photo-gallery'), document.getElementById('rental-building-photo-attach-area'));
    raSetupSingleFileUpload(raAttachmentInput, document.getElementById('rental-attachment-upload-box'), document.getElementById('rental-attachment-gallery'), document.getElementById('rental-attachment-attach-area'));
    raBindExistingAttachmentCards(document);
  }
  if (raPreviewModalEl) raPreviewModalEl.addEventListener('hidden.bs.modal', raRevokePreviewBlob);
  window.addEventListener('beforeunload', function () {
    raRevokePreviewBlob();
    raBlobUrls.forEach(function (url) { try { URL.revokeObjectURL(url); } catch (err) {} });
  });
})();


/* ========== Landlord payment report — stats charts ========== */
(function () {
  'use strict';

  const chartDataNode = document.getElementById('llpReportChartData');
  const chartToggle = document.getElementById('llpReportChartToggle');
  const chartPanel = document.getElementById('llpReportChartPanel');
  const chartLayout = document.getElementById('llpReportChartLayout');

  if (!chartDataNode || !chartToggle || !chartPanel) {
    return;
  }

  let chartInstances = [];

  function readChartData() {
    try {
      return JSON.parse(chartDataNode.textContent || '{}');
    } catch (e) {
      return {};
    }
  }

  function destroyCharts() {
    chartInstances.forEach(function (chart) {
      if (chart) {
        chart.destroy();
      }
    });
    chartInstances = [];
  }

  function moneyTick(value) {
    return '₹' + Number(value).toLocaleString('en-IN');
  }

  function makeChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') {
      return null;
    }
    const chart = new Chart(canvas.getContext('2d'), config);
    chartInstances.push(chart);
    return chart;
  }

  function initPaymentCharts(data) {
    const byMonth = data.byMonth || {};
    if (byMonth.labels && byMonth.labels.length) {
      makeChart('llpReportPayableMonthChart', {
        type: 'bar',
        data: {
          labels: byMonth.labels,
          datasets: [
            {
              label: 'Final payable',
              data: byMonth.final || [],
              backgroundColor: '#6366f1',
              borderRadius: 6,
            },
            {
              label: 'Gross',
              data: byMonth.gross || [],
              backgroundColor: '#94a3b8',
              borderRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: { display: true, text: 'Payable by billing month', font: { size: 13, weight: '600' } },
            legend: { position: 'bottom' },
          },
          scales: {
            y: { ticks: { callback: moneyTick } },
          },
        },
      });
    }

    const byCharge = data.byCharge;
    if (byCharge && byCharge.labels && byCharge.labels.length) {
      makeChart('llpReportChargeChart', {
        type: 'doughnut',
        data: {
          labels: byCharge.labels,
          datasets: [
            {
              data: byCharge.values || [],
              backgroundColor: ['#6366f1', '#0ea5e9', '#f59e0b'],
              borderWidth: 0,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: { display: true, text: 'Amount by charge type', font: { size: 13, weight: '600' } },
            legend: { position: 'bottom' },
          },
        },
      });
    }

    const byStatus = data.byStatus || {};
    if (byStatus.labels && byStatus.labels.length) {
      makeChart('llpReportStatusChart', {
        type: 'bar',
        data: {
          labels: byStatus.labels,
          datasets: [
            {
              label: 'Final payable',
              data: byStatus.amounts || [],
              backgroundColor: '#818cf8',
              borderRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          plugins: {
            title: { display: true, text: 'By approval status', font: { size: 13, weight: '600' } },
            legend: { display: false },
          },
          scales: {
            x: { ticks: { callback: moneyTick } },
          },
        },
      });
    }

    const byZone = data.byZone || {};
    if (byZone.labels && byZone.labels.length) {
      makeChart('llpReportZoneChart', {
        type: 'bar',
        data: {
          labels: byZone.labels.map(function (label) {
            const name = String(label || '');
            return name.length > 20 ? name.slice(0, 18) + '…' : name;
          }),
          datasets: [
            {
              label: 'Final payable',
              data: byZone.amounts || [],
              backgroundColor: '#4f46e5',
              borderRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          plugins: {
            title: { display: true, text: 'Top zones by payable', font: { size: 13, weight: '600' } },
            legend: { display: false },
          },
          scales: {
            x: { ticks: { callback: moneyTick } },
          },
        },
      });
    }
  }

  function initAdvanceCharts(data) {
    const byMonth = data.byMonth || {};
    if (byMonth.labels && byMonth.labels.length) {
      makeChart('llpReportAdvanceMonthChart', {
        type: 'line',
        data: {
          labels: byMonth.labels,
          datasets: [
            {
              label: 'Paid amount',
              data: byMonth.paid || [],
              borderColor: '#6366f1',
              backgroundColor: 'rgba(99, 102, 241, 0.12)',
              fill: true,
              tension: 0.3,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: { display: true, text: 'Advance paid by month', font: { size: 13, weight: '600' } },
          },
          scales: {
            y: { ticks: { callback: moneyTick } },
          },
        },
      });
    }

    const byZone = data.byZone || {};
    if (byZone.labels && byZone.labels.length) {
      makeChart('llpReportAdvanceZoneChart', {
        type: 'bar',
        data: {
          labels: byZone.labels.map(function (label) {
            const name = String(label || '');
            return name.length > 20 ? name.slice(0, 18) + '…' : name;
          }),
          datasets: [
            {
              label: 'Paid',
              data: byZone.amounts || [],
              backgroundColor: '#0ea5e9',
              borderRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          plugins: {
            title: { display: true, text: 'Advance paid by zone', font: { size: 13, weight: '600' } },
            legend: { display: false },
          },
          scales: {
            x: { ticks: { callback: moneyTick } },
          },
        },
      });
    }
  }

  function initCharts() {
    destroyCharts();
    if (typeof Chart === 'undefined') {
      return;
    }
    const data = readChartData();
    if (data.rowType === 'advance') {
      initAdvanceCharts(data);
    } else {
      initPaymentCharts(data);
    }
  }

  chartToggle.addEventListener('click', function () {
    const open = chartPanel.classList.contains('d-none');
    chartPanel.classList.toggle('d-none', !open);
    if (chartLayout) {
      chartLayout.classList.toggle('llp-report-chart-layout--open', open);
    }
    chartToggle.classList.toggle('llp-report-chart-toggle--active', open);
    chartToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    chartToggle.innerHTML = open
      ? '<i class="bi bi-table" aria-hidden="true"></i> Table view'
      : '<i class="bi bi-bar-chart-line" aria-hidden="true"></i> Chart view';
    if (open) {
      window.setTimeout(initCharts, 60);
    } else {
      destroyCharts();
    }
  });
})();



/* ========== Landlord payments — AJAX tabs & KPI filters ========== */
/**
 * Landlord payments — AJAX tab switching, GST/TDS KPI filters, and pagination.
 */
(function () {
  'use strict';

  var loading = false;

  function getPanel() {
    return document.getElementById('llp-payments-panel');
  }

  function getStats() {
    return document.getElementById('llp-payments-stats');
  }

  function panelUrl(href) {
    try {
      var u = new URL(href, window.location.origin);
      return u.pathname + u.search;
    } catch (e) {
      return href;
    }
  }

  function currentQueryParams() {
    return new URLSearchParams(window.location.search);
  }

  function buildPanelUrl(overrides) {
    var params = currentQueryParams();
    Object.keys(overrides).forEach(function (key) {
      var value = overrides[key];
      if (value === null || value === undefined || value === '') {
        params.delete(key);
      } else {
        params.set(key, value);
      }
    });
    params.delete('page');
    return window.location.pathname + '?' + params.toString();
  }

  function syncTabs(activeTab) {
    document.querySelectorAll('[data-llp-tabs] .llp-payments-seg__item').forEach(function (link) {
      var isActive = link.getAttribute('data-llp-tab') === activeTab;
      link.classList.toggle('is-active', isActive);
      if (isActive) {
        link.setAttribute('aria-current', 'page');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  }

  function syncTaxFilterButtons(activeTax) {
    document.querySelectorAll('[data-llp-tax-filter]').forEach(function (btn) {
      var val = btn.getAttribute('data-llp-tax-filter');
      var isActive = !!activeTax && val === activeTax;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  function syncTaxFilterInput(activeTax) {
    var form = document.getElementById('llp-payments-filter-form');
    if (!form) {
      return;
    }
    var input = form.querySelector('input[name="tax_filter"]');
    if (activeTax) {
      if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tax_filter';
        input.id = 'llp_payments_tax_filter';
        var tabInput = form.querySelector('input[name="tab"]');
        if (tabInput && tabInput.parentNode) {
          tabInput.parentNode.insertBefore(input, tabInput.nextSibling);
        } else {
          form.appendChild(input);
        }
      }
      input.value = activeTax;
    } else if (input) {
      input.remove();
    }
  }

  function syncToolbar(panel) {
    if (!panel) {
      return;
    }
    var toolbar = document.querySelector('.llp-payments-card__toolbar');
    if (!toolbar) {
      return;
    }
    var pairs = [
      ['chart', panel.getAttribute('data-chart-url')],
      ['excel', panel.getAttribute('data-export-excel')],
      ['csv', panel.getAttribute('data-export-csv')],
    ];
    pairs.forEach(function (pair) {
      var link = toolbar.querySelector('[data-llp-action="' + pair[0] + '"]');
      if (link && pair[1]) {
        link.href = pair[1];
      }
    });
  }

  function syncFilterMeta(panel, statsEl) {
    if (!panel) {
      return;
    }
    var activeTab = panel.getAttribute('data-active-tab');
    var activeTax = panel.getAttribute('data-active-tax-filter') || '';
    if (!activeTax && statsEl) {
      activeTax = statsEl.getAttribute('data-active-tax-filter') || '';
    }

    var tabInput = document.querySelector('#llp-payments-filter-form input[name="tab"]');
    if (tabInput && activeTab) {
      tabInput.value = activeTab;
    }
    syncTaxFilterInput(activeTax || null);
    syncTaxFilterButtons(activeTax || null);

    var range = panel.getAttribute('data-row-range') || '0';
    var total = panel.getAttribute('data-total-rows') || '0';
    var pill = document.querySelector('.pay-pr-filter-head-meta .tk-showing-pill');
    if (pill) {
      pill.innerHTML = 'Rows <strong>' + range + '</strong> of <strong>' + total + '</strong>';
    }
    var clearLink = document.querySelector('.llp-payments-filters-slot .filter-clear');
    if (clearLink && activeTab) {
      try {
        var u = new URL(clearLink.href, window.location.origin);
        u.searchParams.set('tab', activeTab);
        u.searchParams.delete('tax_filter');
        clearLink.href = u.pathname + u.search;
      } catch (e) { /* ignore */ }
    }
  }

  function applyPanelHtml(html, pushUrl, sourceUrl) {
    var parser = new DOMParser();
    var doc = parser.parseFromString(html, 'text/html');
    var newPanel = doc.getElementById('llp-payments-panel');
    var newStats = doc.getElementById('llp-payments-stats');
    var current = getPanel();
    if (!newPanel || !current) {
      window.location.href = sourceUrl;
      return;
    }
    current.replaceWith(newPanel);
    var currentStats = getStats();
    if (newStats && currentStats) {
      currentStats.replaceWith(newStats);
    }
    var activeTab = newPanel.getAttribute('data-active-tab');
    if (activeTab) {
      syncTabs(activeTab);
    }
    syncToolbar(newPanel);
    syncFilterMeta(newPanel, newStats || getStats());
    if (pushUrl) {
      window.history.pushState({ llpPayments: true }, '', sourceUrl);
    }
  }

  function loadPanel(href, pushUrl) {
    if (loading) {
      return;
    }
    var url = panelUrl(href);
    var panel = getPanel();
    if (!panel) {
      window.location.href = href;
      return;
    }
    loading = true;
    panel.classList.add('is-loading');
    panel.setAttribute('aria-busy', 'true');
    var statsEl = getStats();
    if (statsEl) {
      statsEl.classList.add('is-loading');
    }

    fetch(url, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'text/html',
      },
    })
      .then(function (res) {
        if (!res.ok) {
          throw new Error('HTTP ' + res.status);
        }
        return res.text();
      })
      .then(function (html) {
        applyPanelHtml(html, pushUrl, url);
      })
      .catch(function () {
        window.location.href = href;
      })
      .finally(function () {
        loading = false;
        var p = getPanel();
        if (p) {
          p.classList.remove('is-loading');
          p.removeAttribute('aria-busy');
        }
        var statsDone = getStats();
        if (statsDone) {
          statsDone.classList.remove('is-loading');
        }
      });
  }

  function shouldHandleLink(anchor, panel) {
    if (!anchor || !panel || anchor.target === '_blank') {
      return false;
    }
    if (anchor.hasAttribute('download')) {
      return false;
    }
    try {
      var linkUrl = new URL(anchor.href, window.location.origin);
      if (linkUrl.origin !== window.location.origin) {
        return false;
      }
      return linkUrl.pathname === window.location.pathname;
    } catch (e) {
      return false;
    }
  }

  document.addEventListener('click', function (ev) {
    var taxBtn = ev.target.closest('[data-llp-tax-filter]');
    if (taxBtn) {
      ev.preventDefault();
      var filter = taxBtn.getAttribute('data-llp-tax-filter');
      if (!filter) {
        return;
      }
      var params = currentQueryParams();
      var current = params.get('tax_filter');
      var next = current === filter ? null : filter;
      loadPanel(buildPanelUrl({ tax_filter: next }), true);
      return;
    }

    var tabLink = ev.target.closest('[data-llp-tabs] .llp-payments-seg__item');
    if (tabLink) {
      if (tabLink.classList.contains('is-active')) {
        ev.preventDefault();
        return;
      }
      ev.preventDefault();
      loadPanel(tabLink.href, true);
      return;
    }

    var pageLink = ev.target.closest('#llp-payments-panel .pagination a');
    if (pageLink && shouldHandleLink(pageLink, getPanel())) {
      ev.preventDefault();
      loadPanel(pageLink.href, true);
    }
  });

  window.addEventListener('popstate', function () {
    if (!getPanel()) {
      return;
    }
    loadPanel(window.location.href, false);
  });

  document.addEventListener('DOMContentLoaded', function () {
    var panel = getPanel();
    var statsEl = getStats();
    if (panel) {
      syncFilterMeta(panel, statsEl);
    }
  });
})();


/* ========== Landlord payments — chart report page ========== */
(function () {
  'use strict';

  const isChartReportPage = document.body.classList.contains('llp-chart-report-page');
  const charts = {
    status: null,
    topLandlords: null,
    gstSplit: null,
    financialTotals: null,
    nature: null,
  };

  function readChartData() {
    const node = document.getElementById('llpChartData');
    if (!node) {
      return null;
    }
    try {
      return JSON.parse(node.textContent || '{}');
    } catch (e) {
      return null;
    }
  }

  function formatInr(value) {
    return '₹' + Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
  }

  function emptyMessage(canvas, text) {
    if (!canvas || !canvas.parentElement) {
      return;
    }
    const wrap = canvas.parentElement;
    wrap.innerHTML =
      '<p class="llp-chart-report-empty text-muted small mb-0 text-center py-5">' +
      (text || 'No data for current filters.') +
      '</p>';
  }

  function initStatusChart(data) {
    const canvas = document.getElementById('llpStatusChart');
    if (!canvas || typeof Chart === 'undefined') {
      return;
    }

    const breakdown = data.statusBreakdown || {};
    const labels = data.statusLabels || { paid: 'Paid', pending: 'Pending / due' };
    const paid = Number(breakdown.paid || 0);
    const pending = Number(breakdown.pending || 0);

    if (paid <= 0 && pending <= 0) {
      emptyMessage(canvas, 'No payable amounts in the current filter.');
      return;
    }

    if (charts.status) {
      charts.status.destroy();
    }

    charts.status = new Chart(canvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: [labels.paid || 'Paid', labels.pending || 'Pending / due'],
        datasets: [
          {
            data: [paid, pending],
            backgroundColor: ['#10b981', '#f59e0b'],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return ctx.label + ': ' + formatInr(ctx.parsed);
              },
            },
          },
        },
      },
    });
  }

  function initNatureChart(data) {
    const canvas = document.getElementById('llpNatureChart');
    if (!canvas || typeof Chart === 'undefined') {
      return;
    }

    const natureBreakdown = Array.isArray(data.natureBreakdown) ? data.natureBreakdown : [];
    const withAmount = natureBreakdown.filter(function (o) {
      return Number(o.amount || 0) > 0.009;
    });

    if (!withAmount.length) {
      emptyMessage(canvas, 'No charge-type totals for the current filter.');
      return;
    }

    if (charts.nature) {
      charts.nature.destroy();
    }

    const colors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#94a3b8'];

    charts.nature = new Chart(canvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: withAmount.map(function (o) {
          return String(o.label || o.key || '');
        }),
        datasets: [
          {
            data: withAmount.map(function (o) {
              return Number(o.amount || 0);
            }),
            backgroundColor: withAmount.map(function (o, i) {
              return colors[i % colors.length];
            }),
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return ctx.label + ': ' + formatInr(ctx.parsed);
              },
            },
          },
        },
      },
    });
  }

  function initTopLandlordsChart(data) {
    const canvas = document.getElementById('llpTopLandlordsChart');
    if (!canvas || typeof Chart === 'undefined') {
      return;
    }

    const topLandlords = Array.isArray(data.topLandlords) ? data.topLandlords : [];
    if (!topLandlords.length) {
      emptyMessage(canvas, 'No landlord totals for the current filter.');
      return;
    }

    if (charts.topLandlords) {
      charts.topLandlords.destroy();
    }

    charts.topLandlords = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: topLandlords.map(function (o) {
          const name = String(o.owner || '');
          return name.length > 22 ? name.slice(0, 20) + '…' : name;
        }),
        datasets: [
          {
            label: 'Final NEFT',
            data: topLandlords.map(function (o) {
              return Number(o.amount || 0);
            }),
            backgroundColor: '#6366f1',
            borderRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return formatInr(ctx.parsed.x);
              },
            },
          },
        },
        scales: {
          x: {
            ticks: {
              callback: function (v) {
                return formatInr(v);
              },
            },
          },
        },
      },
    });
  }

  function initGstSplitChart(data) {
    const canvas = document.getElementById('llpGstSplitChart');
    if (!canvas || typeof Chart === 'undefined') {
      return;
    }

    const gstSplit = Array.isArray(data.gstSplit) ? data.gstSplit : [];
    if (!gstSplit.length) {
      emptyMessage(canvas, 'No GST amounts in the current filter.');
      return;
    }

    if (charts.gstSplit) {
      charts.gstSplit.destroy();
    }

    charts.gstSplit = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: gstSplit.map(function (o) {
          return String(o.label || '');
        }),
        datasets: [
          {
            label: 'GST amount',
            data: gstSplit.map(function (o) {
              return Number(o.amount || 0);
            }),
            backgroundColor: ['#0d9488', '#14b8a6', '#5eead4'],
            borderRadius: 8,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return formatInr(ctx.parsed.y);
              },
            },
          },
        },
        scales: {
          y: {
            ticks: {
              callback: function (v) {
                return formatInr(v);
              },
            },
          },
        },
      },
    });
  }

  function initFinancialTotalsChart(data) {
    const canvas = document.getElementById('llpFinancialTotalsChart');
    if (!canvas || typeof Chart === 'undefined') {
      return;
    }

    const totals = Array.isArray(data.financialTotals) ? data.financialTotals : [];
    const withAmount = totals.filter(function (o) {
      return Number(o.amount || 0) > 0.009;
    });

    if (!withAmount.length) {
      emptyMessage(canvas, 'No financial totals for the current filter.');
      return;
    }

    if (charts.financialTotals) {
      charts.financialTotals.destroy();
    }

    charts.financialTotals = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: withAmount.map(function (o) {
          return String(o.label || '');
        }),
        datasets: [
          {
            label: 'Amount',
            data: withAmount.map(function (o) {
              return Number(o.amount || 0);
            }),
            backgroundColor: '#0284c7',
            borderRadius: 8,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return formatInr(ctx.parsed.x);
              },
            },
          },
        },
        scales: {
          x: {
            ticks: {
              callback: function (v) {
                return formatInr(v);
              },
            },
          },
        },
      },
    });
  }

  function initChartReportPage() {
    const data = readChartData();
    if (!data) {
      return;
    }
    initStatusChart(data);
    initNatureChart(data);
    initTopLandlordsChart(data);
    initGstSplitChart(data);
    initFinancialTotalsChart(data);
  }

  function bootChartReport() {
    if (!isChartReportPage) {
      return;
    }
    if (typeof Chart === 'undefined') {
      return;
    }
    initChartReportPage();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootChartReport);
  } else {
    bootChartReport();
  }
})();


/* ========== Vendor owner payments — breakdown drawer & ledger tabs ========== */
(function () {
  'use strict';

  var drawer = document.getElementById('vlBreakdownDrawer');
  if (!drawer) {
    return;
  }

  var backdrop = drawer.querySelector('.vl-breakdown-drawer__backdrop');
  var grid = document.getElementById('vlBreakdownGrid');
  var viewBillLink = document.getElementById('vlDrawerViewBill');
  var activeBtn = null;

  function vlMoney(value) {
    if (value === null || value === undefined || value === '' || value === '—') {
      return '—';
    }
    var n = Number(value);
    if (!isFinite(n)) {
      return '—';
    }
    if (Math.abs(n) < 0.009) {
      return '0.00';
    }
    return n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function setText(id, text) {
    var el = document.getElementById(id);
    if (el) {
      el.textContent = text;
    }
  }

  function addDetailItem(label, value, opts) {
    opts = opts || {};
    var item = document.createElement('div');
    item.className = 'vl-detail-item';

    var lbl = document.createElement('span');
    lbl.className = 'vl-detail-label';
    lbl.textContent = label;

    var val = document.createElement('span');
    val.className = 'vl-detail-value' + (opts.mono ? ' vl-detail-value--mono' : '');
    val.textContent = value;

    item.appendChild(lbl);
    item.appendChild(val);
    grid.appendChild(item);
  }

  function addMoneyItem(label, amount) {
    addDetailItem(label, amount === '—' ? '—' : '\u20B9' + amount);
  }

  function populateDrawer(row) {
    if (!grid) {
      return;
    }

    grid.innerHTML = '';

    var billRef = row.bill_ref || row.agreement_number || '—';
    var toPay = Number(row.net_payable ?? row.pending_balance ?? 0);
    var paidAmt = Number(row.amount_sent ?? 0);
    var dueDate = row.due_date || row.payment_month || '—';
    var tdsLabel = String(row.tds_label || 'TDS').trim() || 'TDS';

    setText('vlDrawerBill', billRef);
    setText('vlDrawerMonth', row.payment_month || '—');
    setText('vlDrawerPurpose', row.payment_purpose || '—');
    setText('vlDrawerStatus', String(row.status_label || '—').toUpperCase());
    setText('vlDrawerToPay', vlMoney(toPay));
    setText('vlDrawerPaid', vlMoney(paidAmt));
    setText('vlDrawerDue', dueDate);

    if (viewBillLink) {
      if (row.detail_url) {
        viewBillLink.href = row.detail_url;
        viewBillLink.classList.remove('d-none');
      } else {
        viewBillLink.href = '#';
        viewBillLink.classList.add('d-none');
      }
    }

    addMoneyItem('Sub total', vlMoney(row.sub_total));
    addMoneyItem('GST', vlMoney(row.gst_amount));
    addMoneyItem('CGST', vlMoney(row.cgst_amount));
    addMoneyItem('SGST', vlMoney(row.sgst_amount));
    if (Number(row.igst_amount || 0) > 0.009) {
      addMoneyItem('IGST', vlMoney(row.igst_amount));
    }
    addMoneyItem('TDS', vlMoney(row.tds_amount));
    addMoneyItem('Gross', vlMoney(row.gross_amount));
    addMoneyItem('Other deductions', vlMoney(row.other_deductions));

    var esiPf = Number(row.esi_amount || 0) + Number(row.pf_amount || 0);
    if (esiPf > 0.009) {
      addMoneyItem('ESI / PF', vlMoney(esiPf));
    }

    addDetailItem('TDS section', tdsLabel !== '—' ? (tdsLabel !== 'TDS' ? tdsLabel : 'TDS') : '—');
    addDetailItem('Payment mode', row.payment_mode || '—');
    addDetailItem('UTR / Reference', row.utr || '—', { mono: true });

    if (row.bill_payment_made && row.bill_payment_made !== '—') {
      addDetailItem('Bill paid on', row.bill_payment_made);
    }
  }

  function openDrawer(btn, row) {
    if (activeBtn && activeBtn !== btn) {
      activeBtn.setAttribute('aria-expanded', 'false');
      activeBtn.classList.remove('is-open');
    }

    activeBtn = btn;
    btn.setAttribute('aria-expanded', 'true');
    btn.classList.add('is-open');

    populateDrawer(row);
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    document.body.classList.add('vl-drawer-open');

    var closeBtn = drawer.querySelector('.vl-breakdown-drawer__close');
    if (closeBtn) {
      closeBtn.focus();
    }
  }

  function closeDrawer() {
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('vl-drawer-open');

    if (activeBtn) {
      activeBtn.setAttribute('aria-expanded', 'false');
      activeBtn.classList.remove('is-open');
      activeBtn = null;
    }
  }

  document.querySelectorAll('.vl-more-btn[data-vl-row]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var raw = btn.getAttribute('data-vl-row');
      if (!raw) {
        return;
      }

      var row;
      try {
        row = JSON.parse(raw);
      } catch (e) {
        return;
      }

      if (drawer.classList.contains('is-open') && activeBtn === btn) {
        closeDrawer();
        return;
      }

      openDrawer(btn, row);
    });
  });

  drawer.querySelectorAll('[data-vl-drawer-close]').forEach(function (el) {
    el.addEventListener('click', closeDrawer);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
      closeDrawer();
    }
  });
})();

(function () {
  'use strict';
  var tabs = document.querySelectorAll('[data-vl-tab]');
  if (!tabs.length) return;
  var panels = document.querySelectorAll('[data-vl-panel]');
  var statCards = document.querySelectorAll('[data-vl-stat-tab]');
  function activate(slug) {
    tabs.forEach(function (tab) {
      var on = tab.getAttribute('data-vl-tab') === slug;
      tab.classList.toggle('is-active', on);
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    panels.forEach(function (panel) {
      var on = panel.getAttribute('data-vl-panel') === slug;
      panel.classList.toggle('is-active', on);
      if (on) panel.removeAttribute('hidden'); else panel.setAttribute('hidden', '');
    });
    statCards.forEach(function (card) {
      card.classList.toggle('is-vl-active', card.getAttribute('data-vl-stat-tab') === slug);
    });
  }
  var initialTab = document.querySelector('[data-vl-tab].is-active');
  if (initialTab) activate(initialTab.getAttribute('data-vl-tab'));
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () { activate(tab.getAttribute('data-vl-tab')); });
  });
  statCards.forEach(function (card) {
    var slug = card.getAttribute('data-vl-stat-tab');
    var go = function () { activate(slug); };
    card.addEventListener('click', go);
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); }
    });
  });
})();

