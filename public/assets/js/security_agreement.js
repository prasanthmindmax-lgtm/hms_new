/**
 * Security agreements â€” client scripts (create, register, attachments).
 * Requires jQuery, moment/daterangepicker (filters/create), FormFieldValidation (create).
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

  function housekeepingPaidLeaveApplicable() {
    const el = form.querySelector('[name="housekeeping_paid_leave_applicable"]');
    return el ? el.value === '1' : false;
  }

  function validateRequiredDocuments() {
    const fileSlots = [
      { field: 'security_agreement_files', label: 'Security agreement file' },
      { field: 'esi_certificate_files', label: 'ESI certificate' },
      { field: 'pf_certificate_files', label: 'PF certificate' },
    ];
    let valid = true;
    let firstInvalid = null;

    fileSlots.forEach(function (slot) {
      const container = form.querySelector('[data-field="' + slot.field + '"]');
      if (!container) {
        return;
      }
      FormFieldValidation.clearField(container);
      const fileInput = container.querySelector('input[type="file"]');
      const keepChecked = container.querySelectorAll('input[type="checkbox"][name^="keep_"]:checked');
      const hasFiles = fileInput && fileInput.files && fileInput.files.length > 0;
      const hasKept = keepChecked.length > 0;
      if (!hasFiles && !hasKept) {
        FormFieldValidation.setError(container, 'Please upload at least one ' + slot.label + '.');
        valid = false;
        if (!firstInvalid) {
          firstInvalid = container;
        }
      }
    });

    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return valid;
  }

  const rules = [
    { field: 'agreement_type', required: true, message: 'Category is required.' },
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
    { field: 'termination_period', required: true, message: 'Termination period is required.' },
    { field: 'end_of_agreement_date', required: true, message: 'End of agreement date is required.' },
    {
      field: 'security_fixed_salary_amount',
      required: true,
      min: 0,
      message: 'Security fixed salary amount is required.',
      minMessage: 'Enter a valid security fixed salary amount.',
    },
    {
      field: 'housekeeping_fixed_salary_amount',
      required: true,
      min: 0,
      message: 'Housekeeping fixed salary amount is required.',
      minMessage: 'Enter a valid housekeeping fixed salary amount.',
    },
    { field: 'housekeeping_paid_leave_applicable', required: true, message: 'Select whether housekeeping paid leave applies.' },
    {
      field: 'housekeeping_paid_leave_days',
      validate: function (val) {
        if (!housekeepingPaidLeaveApplicable()) {
          return '';
        }
        if (String(val).trim() === '') {
          return 'Enter housekeeping paid leave days when applicable.';
        }
        const days = parseInt(val, 10);
        if (!Number.isFinite(days) || days < 1) {
          return 'Enter valid housekeeping paid leave days.';
        }
        return '';
      },
    },
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
    {
      field: 'gst_amount',
      validate: function (val) {
        if (!gstApplicable() || !hasGstTaxMode()) {
          return '';
        }
        if (String(val).trim() === '') {
          return 'GST amount is required when GST is applicable.';
        }
        const num = parseFloat(val);
        if (!Number.isFinite(num) || num < 0) {
          return 'Enter a valid GST amount.';
        }
        return '';
      },
    },
    {
      field: 'tds_tax_id',
      required: true,
      message: 'Select a TDS tax from the list.',
    },
    { field: 'pan_number', required: true, message: 'PAN number is required.' },
    { field: 'contact_person_name', required: true, message: 'Contact person name is required.' },
    { field: 'contact_person_number', required: true, message: 'Contact person number is required.' },
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
    const fieldsOk = FormFieldValidation.validateForm(form, rules);
    const filesOk = validateRequiredDocuments();
    if (!fieldsOk || !filesOk) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
})();


/* ========== GST and TDS ========== */
/**
 * Security agreement â€” GST & TDS (Bill module tax dropdown pattern).
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
  const tdsRateEl = document.getElementById('sa_tds_rate');
  const tdsTaxIdEl = document.getElementById('sa_tds_tax_id');
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

  const securityTaxBase = function () {
    return securitySalaryEl ? num(securitySalaryEl.value) : 0;
  };

  const housekeepingTaxBase = function () {
    return housekeepingSalaryEl ? num(housekeepingSalaryEl.value) : 0;
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

  const formatTdsOptionLabel = function (name, rate, sectionName) {
    const pct = num(rate);
    const displayPct = pct <= 1 && pct > 0 ? pct * 100 : pct;
    if (displayPct <= 0 && !name) {
      return '';
    }
    const display = displayPct % 1 === 0 ? String(displayPct) : displayPct.toFixed(2).replace(/\.?0+$/, '');
    let label = name ? name + ' [' + display + '%]' : display + '% TDS';
    const section = (sectionName || '').trim();
    if (section) {
      label += ' — ' + section;
    }
    return label;
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
      { label: 'Salary amount', amount: charge, highlight: false },
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
      const sectionName = tdsItem.getAttribute('data-section-name') || '';

      if (tdsSearchEl) {
        tdsSearchEl.value = formatTdsOptionLabel(name, rate, sectionName) || tdsItem.textContent.trim();
      }
      if (tdsRateEl) tdsRateEl.value = rate;
      if (tdsTaxIdEl) tdsTaxIdEl.value = id;

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

  [securitySalaryEl, housekeepingSalaryEl].forEach(function (el) {
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
 * Security agreements register â€” multi-select filters (same pattern as payment requests / bill module).
 */
(function ($) {
  const filterFormConfigs = [
    { selector: '#filter-form', branchesNodeId: 'saBranchesData' },
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
  let registerLoading = false;

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

  const ARRAY_FILTER_PARAMS = [
    'company_id',
    'state_id',
    'zone_id',
    'branch_id',
    'vendor_type_name',
    'rcm_applicable',
    'vendor_id',
  ];

  function buildRegisterUrl($form) {
    const action = $form.attr('action') || window.location.pathname;
    const qs = $form.serialize();
    return qs ? action + '?' + qs : action;
  }

  function arrayParamValues(params, name) {
    const bracketed = params.getAll(name + '[]');
    if (bracketed.length) {
      return bracketed;
    }
    return params.getAll(name);
  }

  function syncDateLabelFromFields() {
    const df = $('#sa_date_from').val();
    const dt = $('#sa_date_to').val();
    if (df && dt && typeof moment !== 'undefined') {
      $('#dateLabel').text(
        moment(df).format('MMM D, YYYY') + ' – ' + moment(dt).format('MMM D, YYYY')
      );
      return;
    }
    $('#dateLabel').text('All dates');
  }

  function syncDropdownFromIds($form, paramName, ids) {
    const $wrap = $form.find('.pay-pr-dd[data-filter-param="' + paramName + '"]').first();
    if (!$wrap.length) {
      return;
    }
    const emptyLbl = $wrap.data('empty-label') || 'All';
    const idSet = ids.map(String);
    const texts = [];
    $wrap.find('.dropdown-list.multiselect div').not('.dropdown-empty').each(function () {
      const id = String($(this).attr('data-id') || '');
      const selected = idSet.indexOf(id) > -1;
      $(this).toggleClass('selected', selected);
      if (selected) {
        texts.push($(this).text().trim());
      }
    });
    $wrap.find('.pay-pr-dd-input').val(texts.length ? texts.join(', ') : emptyLbl);
    syncRaArray($form, paramName, ids);
  }

  function syncFormFromUrl(urlString) {
    const $form = $('#filter-form');
    if (!$form.length) {
      return;
    }
    let url;
    try {
      url = new URL(urlString, window.location.origin);
    } catch (e) {
      return;
    }
    const params = url.searchParams;

    $('#sa_date_from').val(params.get('date_from') || '');
    $('#sa_date_to').val(params.get('date_to') || '');
    syncDateLabelFromFields();

    $('#category_filter').val(params.get('category') || 'all');
    $('#universal_search').val(params.get('search') || '');
    const perPage = params.get('per_page');
    if (perPage) {
      $('#pay-pr-per-page').val(perPage);
    }

    ARRAY_FILTER_PARAMS.forEach(function (paramName) {
      syncDropdownFromIds($form, paramName, arrayParamValues(params, paramName));
    });

    const ctx = ctxForForm($form);
    if (ctx) {
      renderBranchList(ctx);
    }
  }

  function syncRegisterMeta(panelEl) {
    if (!panelEl) {
      return;
    }
    const range = panelEl.getAttribute('data-row-range') || '0';
    const total = panelEl.getAttribute('data-total-rows') || '0';
    const $count = $('.pay-pr-filter-head-meta .showing-count');
    if ($count.length) {
      $count.html('Rows <strong>' + range + '</strong> of <strong>' + total + '</strong>');
    }
  }

  function applyRegisterHtml(html, sourceUrl, pushHistory) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const partIds = ['sa-register-stats', 'sa-register-chips-slot', 'sa-register-panel'];
    let replaced = false;

    partIds.forEach(function (id) {
      const newEl = doc.getElementById(id);
      const cur = document.getElementById(id);
      if (newEl && cur) {
        cur.replaceWith(document.importNode(newEl, true));
        replaced = true;
      }
    });

    if (!replaced) {
      window.location.href = sourceUrl;
      return;
    }

    syncRegisterMeta(document.getElementById('sa-register-panel'));
    if (sourceUrl) {
      syncFormFromUrl(sourceUrl);
    }
    if (pushHistory && sourceUrl) {
      window.history.pushState({ saRegister: true }, '', sourceUrl);
    }
  }

  function setRegisterLoading(active) {
    const overlay = document.getElementById('sa-register-loading-overlay');
    const statsEl = document.getElementById('sa-register-stats');
    const panelEl = document.getElementById('sa-register-panel');
    document.body.classList.toggle('sa-register-is-loading', !!active);
    if (overlay) {
      overlay.hidden = !active;
      overlay.setAttribute('aria-busy', active ? 'true' : 'false');
    }
    if (statsEl) {
      statsEl.classList.toggle('is-loading', !!active);
    }
    if (panelEl) {
      panelEl.classList.toggle('is-loading', !!active);
      if (active) {
        panelEl.setAttribute('aria-busy', 'true');
      } else {
        panelEl.removeAttribute('aria-busy');
      }
    }
  }

  function loadRegisterFromUrl(url, pushHistory) {
    if (registerLoading || !document.getElementById('sa-register-panel')) {
      return;
    }
    registerLoading = true;
    setRegisterLoading(true);

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
        applyRegisterHtml(html, url, pushHistory);
      })
      .catch(function () {
        window.location.href = url;
      })
      .finally(function () {
        registerLoading = false;
        setRegisterLoading(false);
      });
  }

  function submitNow($form) {
    const key = $form.attr('id') || 'filter-form';
    if (submitTimers[key]) {
      clearTimeout(submitTimers[key]);
      submitTimers[key] = null;
    }
    if (!document.getElementById('sa-register-panel')) {
      if ($form[0]) {
        $form[0].submit();
      }
      return;
    }
    loadRegisterFromUrl(buildRegisterUrl($form), true);
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
    $raForm.on('submit', function (e) {
      e.preventDefault();
      submitNow($raForm);
    });

    $('#category_filter').on('change', function () {
      scheduleSubmit($raForm);
    });

    window.saRegisterFilters = {
      submitNow: function () { submitNow($raForm); },
      scheduleSubmit: function () { scheduleSubmit($raForm); },
      loadFromUrl: function (url, pushHistory) { loadRegisterFromUrl(url, pushHistory !== false); },
    };
  }

  function shouldHandleRegisterLink(anchor) {
    if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) {
      return false;
    }
    if (!document.getElementById('sa-register-panel')) {
      return false;
    }
    try {
      const linkUrl = new URL(anchor.href, window.location.origin);
      return linkUrl.origin === window.location.origin
        && linkUrl.pathname === window.location.pathname;
    } catch (e) {
      return false;
    }
  }

  $(document).on('click', '#sa-register-chips-slot a, #sa-register-panel .pagination a', function (e) {
    if (!shouldHandleRegisterLink(this)) {
      return;
    }
    e.preventDefault();
    loadRegisterFromUrl(this.href, true);
  });

  window.addEventListener('popstate', function (ev) {
    if (!document.getElementById('sa-register-panel')) {
      return;
    }
    if (ev.state && ev.state.saRegister) {
      loadRegisterFromUrl(window.location.href, false);
      return;
    }
    if (window.location.pathname === document.getElementById('filter-form')?.getAttribute('action')) {
      loadRegisterFromUrl(window.location.href, false);
    }
  });
})(jQuery);


/* ========== Create form page ========== */
/**
 * Security agreement create/edit â€” period picker, location dropdowns, attachments, RCM, flash toasts.
 */
(function ($) {
  'use strict';

  const picker = $('#agreementPeriodPicker');
  const startInput = $('#agreementPeriodStart');
  const endInput = $('#agreementPeriodEnd');
  const endAgreementDate = $('#endAgreementDate');
  const root = document.getElementById('payLocationStrip');
  const branchesDataNode = document.getElementById('saBranchesData');

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
    const grid = pairEl.matches('.form-grid') ? pairEl : pairEl.querySelector('.form-grid');
    daysWrap.classList.toggle('sa-paid-leave-days-wrap--hidden', !applicable);
    if (grid) {
      grid.classList.toggle('sa-salary-grid--three', !applicable);
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
 * Security agreement register â€” date range, expandable rows, period popover.
 */
(function ($) {
  'use strict';

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
      locale: { format: 'YYYY-MM-DD', separator: ' â€“ ', cancelLabel: 'Clear', applyLabel: 'Apply' },
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
        picker.startDate.format('MMM D, YYYY') + ' â€“ ' + picker.endDate.format('MMM D, YYYY')
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
})(jQuery);


/* ========== Security agreement document file inputs ========== */
(function () {
  'use strict';

  const previewKindFromName = function (name) {
    const ext = (name.split('.').pop() || '').toLowerCase();
    if (ext === 'pdf') {
      return 'pdf';
    }
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'].indexOf(ext) !== -1) {
      return 'image';
    }
    if (ext === 'doc' || ext === 'docx') {
      return 'doc';
    }
    return 'other';
  };

  const fileTypeMeta = function (name) {
    const ext = (String(name || '').split('.').pop() || '').toLowerCase();
    const kind = previewKindFromName(name);
    if (kind === 'pdf') {
      return { kind: 'pdf', badge: 'PDF', cls: 'pr-gmail-type-pdf' };
    }
    if (kind === 'doc') {
      return { kind: 'doc', badge: 'DOC', cls: 'pr-gmail-type-doc' };
    }
    if (kind === 'image') {
      return { kind: 'image', badge: ext === 'jpeg' ? 'JPG' : ext.toUpperCase(), cls: 'pr-gmail-type-img' };
    }
    return { kind: 'other', badge: ext ? ext.toUpperCase() : 'FILE', cls: 'pr-gmail-type-file' };
  };

  const buildGmailAttachCard = function (opts) {
    const meta = fileTypeMeta(opts.name);
    const card = document.createElement('div');
    card.className = 'pr-gmail-attach-card';
    card.setAttribute('role', 'listitem');
    if (opts.pending) {
      card.setAttribute('data-sa-pending-attach-card', '');
    }

    let thumbHtml = '';
    if (meta.kind === 'image' && opts.previewSrc) {
      thumbHtml = '<img src="' + opts.previewSrc + '" alt="" class="pr-gmail-attach-thumb-media">';
    } else if (meta.kind === 'pdf' && opts.previewSrc) {
      thumbHtml = '<iframe src="' + opts.previewSrc + '#toolbar=0&navpanes=0&scrollbar=0" title="" class="pr-gmail-attach-thumb-media"></iframe>';
    } else {
      thumbHtml = '<span class="pr-gmail-attach-thumb-fallback" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>';
    }

    const safeName = String(opts.name || 'File').replace(/"/g, '&quot;');
    const safeUrl = String(opts.previewSrc || '').replace(/"/g, '&quot;');
    card.innerHTML =
      '<button type="button" class="pr-gmail-attach-thumb" data-sa-attach-preview data-sa-attach-preview-url="' + safeUrl + '" data-sa-attach-preview-kind="' + meta.kind + '" data-sa-attach-preview-title="' + safeName + '" title="Preview ' + safeName + '">' +
      '<span class="pr-gmail-attach-thumb-inner pr-gmail-attach-thumb--' + meta.kind + '">' + thumbHtml + '</span></button>' +
      '<div class="pr-gmail-attach-foot"><span class="pr-gmail-type-badge ' + meta.cls + '">' + meta.badge + '</span>' +
      '<span class="pr-gmail-attach-name" title="' + safeName + '">' + (opts.name || 'File') + '</span></div>' +
      '<span class="pr-gmail-attach-fold" aria-hidden="true"></span>';

    const thumbBtn = card.querySelector('.pr-gmail-attach-thumb');
    if (thumbBtn && opts.previewSrc && opts.objectUrl) {
      thumbBtn.setAttribute('data-sa-object-url', opts.objectUrl);
    }

    return card;
  };

  const bindExistingAttachCards = function (scope) {
    (scope || document).querySelectorAll('[data-sa-existing-attach-card]').forEach(function (card) {
      if (card.dataset.saAttachBound === '1') {
        return;
      }
      card.dataset.saAttachBound = '1';
      const keep = card.querySelector('.sa-attach-keep');
      const removeBtn = card.querySelector('.pr-gmail-attach-remove');
      if (removeBtn && keep) {
        removeBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          keep.checked = false;
          card.classList.add('pr-gmail-attach-card--removed');
          window.setTimeout(function () {
            if (!keep.checked) {
              card.style.display = 'none';
            }
          }, 220);
        });
      }
    });
  };

  bindExistingAttachCards();

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
      const grid = document.createElement('div');
      grid.className = 'pr-gmail-attach-grid pr-gmail-attach-grid--row';
      grid.setAttribute('role', 'list');

      files.forEach(function (file) {
        const objectUrl = URL.createObjectURL(file);
        const card = buildGmailAttachCard({
          name: file.name,
          previewSrc: objectUrl,
          objectUrl: objectUrl,
          pending: true,
        });
        grid.appendChild(card);
      });

      previewEl.appendChild(grid);
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
 * Security agreement register â€” attachment preview modal.
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
