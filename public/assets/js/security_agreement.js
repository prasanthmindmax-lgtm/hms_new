/**
 * Security agreements & landlord payments — consolidated client scripts.
 * Requires jQuery, moment/daterangepicker (filters/create/register), Chart.js (landlord payment reports only), FormFieldValidation (create).
 */

/* ========== Form validation ========== */
(function () {
  'use strict';

  const form = document.querySelector('form.js-sa-validate');
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
    { field: 'agreement_date', required: true, message: 'Security agreement date is required.' },
    {
      field: 'vendor_id',
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
    { field: 'security_charge_amount', required: true, min: 0, message: 'Security charge amount is required.' },
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
 * Security agreement — GST & TDS (Bill module tax dropdown pattern).
 */
(function () {
  const GST_INCLUDING = 'including_gst';
  const GST_EXCLUDING = 'excluding_gst';

  const gstApplicableEl = document.getElementById('sa_gst_applicable');
  const gstCardEl = document.getElementById('sa_gst_card');
  const gstDetailWrapEl = document.getElementById('sa_gst_detail_wrap');
  const gstDetailFields = gstCardEl
    ? gstCardEl.querySelectorAll('.gst-detail-field')
    : [];
  const typeEl = document.getElementById('sa_gst_type');
  const wrapEl = document.getElementById('sa_gst_fields_wrap');
  const rentEl = document.getElementById('sa_security_charge_amount');
  const maintEl = document.getElementById('sa_housekeeping_charge_amount');
  const securitySalaryEl = document.getElementById('sa_security_fixed_salary');
  const housekeepingSalaryEl = document.getElementById('sa_housekeeping_fixed_salary');

  const gstSearchEl = document.getElementById('sa_gst_search_input');
  const gstNameEl = document.getElementById('sa_gst_tax_name');
  const gstPctEl = document.getElementById('sa_gst_percentage');
  const gstTaxTypeEl = document.getElementById('sa_gst_tax_type');
  const gstTaxIdEl = document.getElementById('sa_gst_tax_id');
  const gstAmtEl = document.getElementById('sa_gst_amount');
  const cgstEl = document.getElementById('sa_cgst_amount');
  const sgstEl = document.getElementById('sa_sgst_amount');
  const igstEl = document.getElementById('sa_igst_amount');
  const gstRentSummaryEl = document.getElementById('sa_gst_breakdown_rent');
  const gstMaintSummaryEl = document.getElementById('sa_gst_breakdown_maintenance');
  const gstTotalSummaryEl = document.getElementById('sa_gst_breakdown_total');
  const gstFieldWraps = document.querySelectorAll('.form-block.form-block--tax .gst-fields');

  const tdsSearchEl = document.getElementById('sa_tds_search_input');
  const tdsNameEl = document.getElementById('sa_tds_tax_name');
  const tdsRateEl = document.getElementById('sa_tds_rate');
  const tdsTaxIdEl = document.getElementById('sa_tds_tax_id');
  const tdsSectionIdEl = document.getElementById('sa_tds_section_id');
  const tdsSectionEl = document.getElementById('sa_tds_section');
  const tdsSectionDisplayEl = document.getElementById('sa_tds_section_display');
  const tdsAmtEl = document.getElementById('sa_tds_amount');
  const tdsSecuritySummaryEl = document.getElementById('sa_tds_breakdown_security');
  const tdsHousekeepingSummaryEl = document.getElementById('sa_tds_breakdown_housekeeping');
  const tdsTotalSummaryEl = document.getElementById('sa_tds_breakdown_total');

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

  /** Mirrors SecurityAgreement::computeGstBreakdownForBase (per amount, then combined). */
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

  const parsePercentFromLabel = function (label) {
    const match = String(label || '').match(/([\d.]+)\s*%/);
    return match ? num(match[1]) : 0;
  };

  const serviceTaxBase = function (chargeEl, salaryEl) {
    const charge = chargeEl ? num(chargeEl.value) : 0;
    if (charge > 0) {
      return charge;
    }
    return salaryEl ? num(salaryEl.value) : 0;
  };

  const securityTaxBase = function () {
    return serviceTaxBase(rentEl, securitySalaryEl);
  };

  const housekeepingTaxBase = function () {
    return serviceTaxBase(maintEl, housekeepingSalaryEl);
  };

  const rentAmount = function () {
    return securityTaxBase();
  };

  const maintenanceAmount = function () {
    return housekeepingTaxBase();
  };

  const gstPercentValue = function () {
    const hidden = gstPctEl ? num(gstPctEl.value) : 0;
    if (hidden > 0) {
      return hidden;
    }
    return gstSearchEl ? parsePercentFromLabel(gstSearchEl.value) : 0;
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
    if (!tdsRateEl) {
      return tdsSearchEl ? parsePercentFromLabel(tdsSearchEl.value) : 0;
    }
    const raw = num(tdsRateEl.value);
    if (raw > 0) {
      return raw <= 1 ? raw * 100 : raw;
    }
    return tdsSearchEl ? parsePercentFromLabel(tdsSearchEl.value) : 0;
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
      el.classList.add('breakdown-box--empty');
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
      container.classList.add('breakdown-box--empty');
      return;
    }
    container.classList.remove('breakdown-box--empty');
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
          '<div class="breakdown-item' +
          (item.highlight ? ' breakdown-item--total' : '') +
          '">' +
          '<span class="breakdown-label">' +
          item.label +
          '</span>' +
          '<span class="breakdown-value">&#8377; ' +
          formatMoney(item.amount) +
          '</span>' +
          '</div>'
        );
      })
      .join('');
  };

  const raGstCalculate = function () {
    if (!isGstApplicable() || !hasTaxMode()) {
      return;
    }

    syncTaxModeFromSelect();

    const gstPercent = gstPercentValue();
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

  const renderTdsBreakdown = function (container, payload) {
    if (!container) return;
    const charge = payload.chargeAmount || 0;
    const tdsAmount = payload.tdsAmount || 0;
    const rate = payload.ratePercent || 0;
    if (charge <= 0 || tdsAmount <= 0) {
      container.innerHTML = '';
      container.classList.add('breakdown-box--empty');
      return;
    }
    container.classList.remove('breakdown-box--empty');
    const rateLabel = rate % 1 === 0 ? String(rate) : rate.toFixed(2).replace(/\.?0+$/, '');
    const items = [
      { label: 'Charge amount', amount: charge, highlight: false },
      { label: 'TDS (' + rateLabel + '%)', amount: tdsAmount, highlight: true },
    ];
    container.innerHTML = items
      .map(function (item) {
        return (
          '<div class="breakdown-item' +
          (item.highlight ? ' breakdown-item--total' : '') +
          '">' +
          '<span class="breakdown-label">' +
          item.label +
          '</span>' +
          '<span class="breakdown-value">&#8377; ' +
          formatMoney(item.amount) +
          '</span>' +
          '</div>'
        );
      })
      .join('');
  };

  const raTdsCalculate = function () {
    const securityCharge = rentAmount();
    const housekeepingCharge = maintenanceAmount();
    const pct = tdsRatePercent();
    let securityTds = 0;
    let housekeepingTds = 0;
    if (pct > 0) {
      if (securityCharge > 0) {
        securityTds = round2((securityCharge * pct) / 100);
      }
      if (housekeepingCharge > 0) {
        housekeepingTds = round2((housekeepingCharge * pct) / 100);
      }
    }
    const tdsAmount = round2(securityTds + housekeepingTds);

    if (tdsAmtEl) tdsAmtEl.value = tdsAmount > 0 ? tdsAmount.toFixed(2) : '';

    const tdsPayload = function (charge, amount) {
      return {
        chargeAmount: charge,
        tdsAmount: amount,
        ratePercent: pct,
      };
    };

    renderTdsBreakdown(tdsSecuritySummaryEl, tdsPayload(securityCharge, securityTds));
    renderTdsBreakdown(tdsHousekeepingSummaryEl, tdsPayload(housekeepingCharge, housekeepingTds));
    renderTdsBreakdown(tdsTotalSummaryEl, tdsPayload(round2(securityCharge + housekeepingCharge), tdsAmount));
  };

  const syncGstCalcVisibility = function () {
    const showCalc = isGstApplicable() && hasTaxMode();
    if (gstCardEl) {
      gstCardEl.classList.toggle('gst-card--with-breakdown', showCalc);
    }
    gstFieldWraps.forEach(function (el) {
      el.classList.toggle('gst-fields--hidden', !showCalc);
    });
    document.querySelectorAll('.form-block.form-block--tax .gst-req').forEach(function (el) {
      el.classList.toggle('d-none', !showCalc);
    });
    document.querySelectorAll('.form-block.form-block--tax .gst-tax-mode-req').forEach(function (el) {
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
      gstCardEl.classList.toggle('gst-card--expanded', on);
    }
    gstDetailFields.forEach(function (el) {
      el.classList.toggle('gst-detail-wrap--hidden', !on);
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

  bindDropdownOpen(gstSearchEl, '.gst-dropdown-menu');
  bindDropdownOpen(tdsSearchEl, '.tds-dropdown-menu');

  document.addEventListener('mousedown', function (e) {
    const gstItem = e.target.closest('#sa_tax_gst_list div[data-id]');
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

    const tdsItem = e.target.closest('#sa_tax_tds_list div[data-id]');
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

    if (!e.target.closest('.gst-dropdown') && !e.target.closest('.tds-dropdown')) {
      document.querySelectorAll('.gst-dropdown-menu, .tds-dropdown-menu').forEach(function (m) {
        m.classList.remove('show');
      });
    }
  });

  [rentEl, maintEl, securitySalaryEl, housekeepingSalaryEl].forEach(function (el) {
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
  raGstCalculate();
  raTdsCalculate();
})();


/* ========== Register filters ========== */
/**
 * Security agreements register — multi-select filters (same pattern as payment requests / bill module).
 */
(function ($) {
  const filterFormConfigs = [
    { selector: '#filter-form', branchesNodeId: 'saBranchesData' },
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
      emptyEl.className = 'dropdown-empty';
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
    const $selected = $dropdown.find('.dropdown-list.multiselect div.selected').not('.dropdown-empty').first();
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
      if (!$(this).hasClass('selected') || $(this).hasClass('dropdown-empty')) {
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
    if ($(this).hasClass('dropdown-empty')) {
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
    $dropdown.find('.dropdown-list.multiselect div').not('.dropdown-empty').addClass('selected');
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

  $('#universal_search').on('input', function () {
    const $raForm = $('#filter-form');
    if ($raForm.length) {
      scheduleSubmit($raForm);
    }
  });

  activeForms.forEach(function (ctx) {
    renderBranchList(ctx);
  });

  const $raForm = $('#filter-form');
  if ($raForm.length) {
    window.saRegisterFilters = {
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
 * Security agreement create/edit — period picker, location dropdowns, attachments, RCM, flash toasts.
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
  const flashData = document.getElementById('saFlashData');
  const branchesDataNode = document.getElementById('saBranchesData');

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
        wrap.querySelector('.company-search-input').value = this.textContent.trim();
        wrap.querySelector('.company_id').value = this.getAttribute('data-id') || '';
        this.closest('.pr-dd-panel').classList.remove('show');
      });
    });

    root.querySelectorAll('.vendor-list div').forEach(function (div) {
      div.addEventListener('click', function (ev) {
        ev.stopPropagation();
        const wrap = this.closest('.pr-dd-wrap');
        const val = this.textContent.trim();
        wrap.querySelector('.vendor-search-input').value = val;
        wrap.querySelector('.vendor_id').value = this.getAttribute('data-id') || '';
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
        wrap.querySelector('.zone-search-input').value = this.textContent.trim();
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
      wrap.querySelector('.branch-search-input').value = item.textContent.trim();
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
            branchInput.value = found.textContent.trim();
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

  const rcmSelect = document.getElementById('sa_rcm_applicable');
  const rcmWrap = document.getElementById('sa_rcm_value_wrap');
  const rcmInput = document.getElementById('sa_rcm_value');
  if (!rcmSelect || !rcmWrap || !rcmInput) {
    return;
  }

  const syncRcm = function () {
    const yes = rcmSelect.value === '1';
    rcmWrap.classList.toggle('rcm-value--hidden', !yes);
    rcmInput.disabled = !yes;
    rcmInput.required = yes;
    if (!yes) {
      rcmInput.value = '';
    }
  };

  rcmSelect.addEventListener('change', syncRcm);
  syncRcm();
})();

(function () {
  'use strict';

  function syncPaidLeavePair(pairEl) {
    if (!pairEl) {
      return;
    }
    const select = pairEl.querySelector('[data-paid-leave-toggle]');
    const daysWrap = pairEl.querySelector('[data-paid-leave-days-wrap]');
    const daysInput = pairEl.querySelector('[data-paid-leave-days]');
    if (!select || !daysWrap) {
      return;
    }
    const applicable = select.value === '1';
    const grid = pairEl.matches('.form-grid form-grid--3') ? pairEl : pairEl.querySelector('.form-grid form-grid--3');
    daysWrap.classList.toggle('sa-paid-leave-days-wrap--hidden', !applicable);
    if (grid) {
      grid.classList.toggle('form-grid--two', !applicable);
    }
    if (daysInput) {
      daysInput.disabled = !applicable;
      daysInput.required = applicable;
      if (!applicable) {
        daysInput.value = '';
      }
    }
  }

  document.querySelectorAll('[data-paid-leave-role]').forEach(function (pairEl) {
    const select = pairEl.querySelector('[data-paid-leave-toggle]');
    if (select) {
      select.addEventListener('change', function () {
        syncPaidLeavePair(pairEl);
      });
    }
    syncPaidLeavePair(pairEl);
  });
})();


/* ========== Register page ========== */
/**
 * Security agreement register — date range, expandable rows, period popover.
 */
(function ($) {
  'use strict';

  const flashData = document.getElementById('saFlashData');
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

  const $form = $('#filter-form');

  function submitFilterForm() {
    if (window.saRegisterFilters && typeof window.saRegisterFilters.submitNow === 'function') {
      window.saRegisterFilters.submitNow();
      return;
    }
    if ($form.length && $form[0]) {
      $form[0].submit();
    }
  }

  const $dateWrap = $('#reportRange');
  if (typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined' && $dateWrap.length) {
    const df = $('#sa_date_from').val();
    const dt = $('#sa_date_to').val();
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
      $('#sa_date_from').val(picker.startDate.format('YYYY-MM-DD'));
      $('#sa_date_to').val(picker.endDate.format('YYYY-MM-DD'));
      $('#dateLabel').text(
        picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY')
      );
      submitFilterForm();
    });

    $dateWrap.on('cancel.daterangepicker', function () {
      $('#sa_date_from').val('');
      $('#sa_date_to').val('');
      $('#dateLabel').text('All dates');
      submitFilterForm();
    });
  }

  $('#pay-pr-per-page').on('change', function () {
    submitFilterForm();
  });

  function toggleDetailRow($row) {
    const id = $row.data('sa-row-id');
    if (!id) {
      return;
    }
    const $detail = $('tr.detail-row[data-sa-detail-for="' + id + '"]');
    if (!$detail.length) {
      return;
    }
    const opening = $detail.hasClass('d-none');
    $('tr.detail-row').addClass('d-none');
    $('tr.pay-pr-row').removeClass('row-expanded');
    if (opening) {
      $detail.removeClass('d-none');
      $row.addClass('row-expanded');
    }
  }

  $(document).on('click keydown', '.row-expand-trigger', function (e) {
    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
      return;
    }
    if ($(e.target).closest('a').length) {
      return;
    }
    e.preventDefault();
    toggleDetailRow($(this).closest('tr.pay-pr-row'));
  });

  const $periodPopover = $('#periodPopover');
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
          '<tr><td class="period-popover-label">' +
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
        ? '<p class="period-popover-note mb-0">Hike: <strong>' +
          hike +
          '%</strong> per year on base ' +
          formatInr(baseRent) +
          '.</p>'
        : '<p class="period-popover-note mb-0">Flat rent ' + formatInr(baseRent) + ' (no hike % set).</p>';

    return (
      '<div class="period-popover-head"><strong>Agreement period</strong><span>' +
      period +
      '</span></div>' +
      '<table class="period-popover-table"><thead><tr><th>Year</th><th>Period</th><th class="text-end">Monthly rent</th></tr></thead><tbody>' +
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
      activePeriodBtn.removeClass('period-link period-link--active');
    }
    activePeriodBtn = null;
  }

  $(document).on('click', '.period-link', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $btn = $(this);
    if (activePeriodBtn && activePeriodBtn[0] === $btn[0]) {
      closePeriodPopover();
      return;
    }
    closePeriodPopover();
    activePeriodBtn = $btn;
    $btn.addClass('period-link period-link--active');
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
    if ($(e.target).closest('.period-link, #periodPopover').length) {
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


/* ========== Security agreement document file inputs ========== */
(function () {
  'use strict';

  const formatBytes = function (bytes) {
    if (!bytes || bytes <= 0) {
      return '';
    }
    if (bytes < 1024) {
      return bytes + ' B';
    }
    if (bytes < 1024 * 1024) {
      return (bytes / 1024).toFixed(1) + ' KB';
    }
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  };

  const previewKindFromName = function (name) {
    const ext = (name.split('.').pop() || '').toLowerCase();
    if (ext === 'pdf') {
      return 'pdf';
    }
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'].indexOf(ext) !== -1) {
      return 'image';
    }
    return 'other';
  };

  const saSetupDocUploadBox = function (input, box) {
    if (!input || !box) {
      return;
    }

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
      box.addEventListener(eventName, preventDefaults, false);
    });
    ['dragenter', 'dragover'].forEach(function (eventName) {
      box.addEventListener(eventName, function () {
        box.classList.add('dragover');
      }, false);
    });
    ['dragleave', 'drop'].forEach(function (eventName) {
      box.addEventListener(eventName, function () {
        box.classList.remove('dragover');
      }, false);
    });
    box.addEventListener('drop', function (e) {
      const dt = e.dataTransfer;
      if (!dt || !dt.files || !dt.files.length) {
        return;
      }
      try {
        const fileTransfer = new DataTransfer();
        Array.from(dt.files).forEach(function (file) {
          fileTransfer.items.add(file);
        });
        input.files = fileTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      } catch (err) {
        if (window.toastr) {
          toastr.error('Could not add the dropped file(s).');
        }
      }
    }, false);
  };

  document.querySelectorAll('.sa-doc-file-input').forEach(function (input) {
    const boxId = input.getAttribute('data-sa-doc-upload-box');
    const box = boxId ? document.getElementById(boxId) : input.closest('.sa-doc-upload-box');
    saSetupDocUploadBox(input, box);

    const previewId = input.getAttribute('data-sa-doc-preview');
    const previewEl = previewId ? document.getElementById(previewId) : null;
    if (!previewEl) {
      return;
    }

    input.addEventListener('change', function () {
      const files = Array.from(input.files || []);
      previewEl.innerHTML = '';
      if (!files.length) {
        previewEl.classList.add('d-none');
        return;
      }

      previewEl.classList.remove('d-none');
      const list = document.createElement('ul');
      list.className = 'list-unstyled mb-0';

      files.forEach(function (file, index) {
        const li = document.createElement('li');
        li.className = 'mb-2';
        const row = document.createElement('div');
        row.className = 'd-flex align-items-center gap-2 flex-wrap';

        const label = document.createElement('span');
        label.className = 'file-chip';
        label.textContent = file.name + (file.size ? ' (' + formatBytes(file.size) + ')' : '');
        row.appendChild(label);

        const objectUrl = URL.createObjectURL(file);
        const kind = previewKindFromName(file.name);
        const viewBtn = document.createElement('button');
        viewBtn.type = 'button';
        viewBtn.className = 'btn btn-sm btn-outline-primary';
        viewBtn.innerHTML = '<i class="bi bi-eye" aria-hidden="true"></i> Preview';
        viewBtn.setAttribute('data-sa-attach-preview', '');
        viewBtn.setAttribute('data-sa-attach-preview-url', objectUrl);
        viewBtn.setAttribute('data-sa-attach-preview-kind', kind);
        viewBtn.setAttribute('data-sa-attach-preview-title', file.name);
        viewBtn.setAttribute('data-sa-object-url', objectUrl);
        row.appendChild(viewBtn);

        li.appendChild(row);
        list.appendChild(li);

        viewBtn.addEventListener('click', function () {
          viewBtn._saRevokeTimer = setTimeout(function () {
            if (viewBtn._saRevokeTimer) {
              clearTimeout(viewBtn._saRevokeTimer);
            }
          }, 60000);
        });
      });

      previewEl.appendChild(list);
    });
  });

  document.addEventListener('hidden.bs.modal', function (e) {
    if (!e.target || e.target.id !== 'attachmentPreviewModal') {
      return;
    }
    document.querySelectorAll('[data-sa-object-url]').forEach(function (btn) {
      const url = btn.getAttribute('data-sa-object-url');
      if (url) {
        try {
          URL.revokeObjectURL(url);
        } catch (err) {
          // ignore
        }
      }
    });
  });
})();


/* ========== Register attachment modal ========== */
/**
 * Security agreement register — attachment preview modal.
 */
(function () {
  'use strict';

  const modalEl = document.getElementById('attachmentPreviewModal');
  if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
    return;
  }

  const iframe = document.getElementById('attachmentPreviewIframe');
  const img = document.getElementById('attachmentPreviewImg');
  const fallback = document.getElementById('attachmentPreviewFallback');
  const openLink = document.getElementById('attachmentPreviewOpenLink');
  const footerLink = document.getElementById('attachmentPreviewFooterLink');
  const titleEl = document.getElementById('attachmentPreviewModalLabel');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  const hideAll = function () {
    if (iframe) {
      iframe.classList.add('d-none');
      iframe.removeAttribute('src');
    }
    if (img) {
      img.classList.add('d-none');
      img.removeAttribute('src');
      img.alt = '';
    }
    if (fallback) {
      fallback.classList.add('d-none');
    }
  };

  const openPreview = function (url, kind, title) {
    if (!url) {
      return;
    }
    hideAll();
    if (titleEl) {
      titleEl.textContent = title || 'Attachment preview';
    }
    if (footerLink) {
      footerLink.href = url;
    }
    if (openLink) {
      openLink.href = url;
    }
    if (kind === 'image' && img) {
      img.src = url;
      img.alt = title || 'Attachment';
      img.classList.remove('d-none');
    } else if (kind === 'pdf' && iframe) {
      iframe.src = url;
      iframe.classList.remove('d-none');
    } else if (fallback) {
      fallback.classList.remove('d-none');
    }
    modal.show();
  };

  modalEl.addEventListener('hidden.bs.modal', hideAll);

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-sa-attach-preview]');
    if (!btn) {
      return;
    }
    e.preventDefault();
    e.stopPropagation();
    openPreview(
      btn.getAttribute('data-sa-attach-preview-url') || '',
      btn.getAttribute('data-sa-attach-preview-kind') || 'other',
      btn.getAttribute('data-sa-attach-preview-title') || 'Attachment'
    );
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
    var pill = document.querySelector('.pay-pr-filter-head-meta .showing-count');
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

    addDetailItem('TDS section', tdsLabel !== '—' ? tdsLabel : '—');
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