<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/indents.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container in-page">
  <div class="pc-content">
    <div class="qd-card tk-tickets-page">
      <div class="tk-hero">
        <div class="tk-hero-inner">
          <h1 class="tk-hero-title"><i class="bi bi-box-seam" aria-hidden="true"></i> New indent request</h1>
        </div>
        <div class="tk-hero-actions">
          <a href="{{ route('superadmin.indents.index') }}" class="tk-btn-export" style="text-decoration:none;"><i class="bi bi-arrow-left"></i> Back to list</a>
        </div>
      </div>

      <div class="tk-dash-body">
        <form id="indentCreateForm" class="in-create-form">
          <div class="tk-filter-shell tk-filter-qd in-create-section">
            <div class="tk-filter-head">
              <div class="tk-filter-title"><i class="bi bi-geo-alt" aria-hidden="true"></i> Location</div>
            </div>
            <div class="qd-filters tk-ticket-qd-filters">
              <div class="qd-filter-row">
                <div class="qd-filter-group">
                  <label>Company</label>
                  <div class="tax-dropdown-wrapper company-section">
                    <input type="text" class="form-control form-control-sm company-search-input" autocomplete="off"
                      autocorrect="off" name="company_display" readonly placeholder="— Optional —" value="">
                    <input type="hidden" name="company_id" id="company_id" class="company_id" value="">
                    <div class="dropdown-menu tax-dropdown">
                      <div class="company-list"></div>
                    </div>
                  </div>
                </div>
                <div class="qd-filter-group">
                  <label>Zone</label>
                  <div class="tax-dropdown-wrapper account-section zone-section">
                    <input type="text" class="form-control form-control-sm zone-search-input" autocomplete="off"
                      autocorrect="off" name="zone_display" placeholder="Select a zone" value="" readonly>
                    <input type="hidden" name="zone_id" id="zone_id" class="zone_id" value="">
                    <div class="dropdown-menu tax-dropdown">
                      <div class="zone-list"></div>
                    </div>
                  </div>
                </div>
                <div class="qd-filter-group">
                  <label>Branch <span class="text-danger">*</span></label>
                  <div class="tax-dropdown-wrapper account-section branch-section">
                    <input type="text" class="form-control form-control-sm branch-search-input" autocomplete="off"
                      autocorrect="off" name="branch_display" readonly placeholder="Select branch" value="">
                    <input type="hidden" name="branch_id" id="branch_id" class="branch_id" value="">
                    <div class="dropdown-menu tax-dropdown branch-menu">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search" placeholder="Search branch…" autocomplete="off">
                      </div>
                      <div class="dropdown-list multiselect branch-list"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tk-filter-shell tk-filter-qd in-create-section">
            <div class="tk-filter-head">
              <div class="tk-filter-title"><i class="bi bi-building" aria-hidden="true"></i> Departments</div>
            </div>
            <div class="qd-filters tk-ticket-qd-filters">
              <div class="d-flex flex-column flex-md-row gap-3 mb-3">
                <div class="qd-filter-group flex-fill" style="width: 100%;">
                  <label>From department <span class="text-danger">*</span></label>
                  <div class="tax-dropdown-wrapper account-section in-dept-from-wrap">
                    <input type="text" class="form-control form-control-sm in-dept-from-input" autocomplete="off"
                      autocorrect="off" readonly placeholder="Select department" value="">
                    <input type="hidden" name="from_department_id" id="from_department_id" class="from_department_id" value="">
                    <div class="dropdown-menu tax-dropdown">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search in-dept-from-filter" placeholder="Search…" autocomplete="off">
                      </div>
                      <div class="in-dept-list department-list"></div>
                    </div>
                  </div>
                </div>
                <div class="qd-filter-group flex-fill" style="width: 100%;">
                  <label>To department <span class="text-danger">*</span></label>
                  <div class="tax-dropdown-wrapper account-section in-dept-to-wrap">
                    <input type="text" class="form-control form-control-sm in-dept-to-input" autocomplete="off"
                      autocorrect="off" readonly placeholder="Select department" value="">
                    <input type="hidden" name="to_department_id" id="to_department_id" class="to_department_id" value="">
                    <div class="dropdown-menu tax-dropdown">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search in-dept-to-filter" placeholder="Search…" autocomplete="off">
                      </div>
                      <div class="in-dept-list in-dept-to-list"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="qd-filter-row">
                <div class="qd-filter-group" style="flex: 1 1 100%; max-width: 100%;">
                  <label>Remarks</label>
                  <textarea name="remarks" id="remarks" class="form-control form-control-sm" rows="2" placeholder="Optional context for approvers…"></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="tk-table-card in-create-lines-card">
            <div class="tk-filter-head border-0 pt-3 px-3 pb-0">
              <div class="tk-filter-title"><i class="bi bi-list-ul" aria-hidden="true"></i> Items <span class="text-danger">*</span></div>
              <span class="small text-muted">Consumable store stock at the selected branch</span>
            </div>
            <div class="p-3 pt-2">
              <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-2 in-create-lines-table" id="linesTable">
                  <thead class="table-light">
                    <tr>
                      <th style="min-width:240px;">Store item</th>
                      <th style="width:110px;" class="text-end">Available</th>
                      <th style="width:120px;">Qty</th>
                      <th style="width:48px;"></th>
                    </tr>
                  </thead>
                  <tbody id="linesBody"></tbody>
                </table>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddLine"><i class="bi bi-plus-lg"></i> Add line</button>
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end pt-3 pb-2">
            <a href="{{ route('superadmin.indents.index') }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="bi bi-send me-1"></i> Submit indent</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function () {
  const routes = {
    stock: @json(route('superadmin.indents.stock')),
    store: @json(route('superadmin.indents.store')),
    index: @json(route('superadmin.indents.index')),
  };
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const inZones = @json($zones);
  const inCompanies = @json($companies);
  const branchFetchUrl = @json(route('superadmin.getbranchfetch'));
  const inDepartments = @json($departments);
  let stockCache = [];
  let lineIdx = 0;

  function inZoneName(zid) {
    if (zid === '' || zid === null || zid === undefined) return '';
    const z = (inZones || []).find(function (x) { return String(x.id) === String(zid); });
    return z ? (z.name || '') : '';
  }

  /** Quotation / petty-cash pattern: branches for zone from tbl_locations via getbranchfetch */
  function inFillBranchList($listEl, zoneVal, onDone) {
    $listEl.empty();
    const z = String(zoneVal || '').trim();
    if (!z) {
      if (typeof onDone === 'function') onDone();
      return;
    }
    $.ajax({
      url: branchFetchUrl,
      method: 'POST',
      data: { _token: csrf, id: z },
      headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
    }).done(function (res) {
      const rows = (res && res.branch) ? res.branch : [];
      rows.forEach(function (b) {
        $listEl.append(
          $('<div>').attr('data-id', b.id).attr('data-zone-id', b.zone_id).text(b.name || '')
        );
      });
      if (typeof onDone === 'function') onDone();
    }).fail(function () {
      toastr.error('Could not load branches for this zone.');
      if (typeof onDone === 'function') onDone();
    });
  }

  function inSyncBranchListsFromZone(zoneVal) {
    const $wrap = $('#indentCreateForm .branch-section');
    const $mainList = $wrap.find('.branch-list');
    inFillBranchList($mainList, zoneVal, function () {
      const $clone = $('#indentCreateForm .branch-search-input').data('dropdown');
      if ($clone && $clone.length) {
        $clone.find('.branch-list').empty();
        $mainList.children().clone(true, true).appendTo($clone.find('.branch-list'));
      }
    });
  }

  function inOpenZoneDropdown($input) {
    $input.val('');
    $('.dropdown-menu.tax-dropdown').hide();
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true, true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
    const offset = $input.offset();
    $dropdown.css({
      position: 'absolute',
      top: offset.top + $input.outerHeight(),
      left: offset.left,
      width: $input.outerWidth(),
      zIndex: 999,
    }).show();
    $input.removeAttr('readonly');
  }

  function inOpenBranchDropdown($input) {
    $('.dropdown-menu.tax-dropdown').hide();
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true, true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
    const offset = $input.offset();
    $dropdown.css({
      position: 'absolute',
      top: offset.top + $input.outerHeight(),
      left: offset.left,
      width: $input.outerWidth(),
      zIndex: 999,
    }).show();
    $dropdown.find('.inner-search').first().val('').focus();
    $dropdown.find('.branch-list div').show();
  }

  function inOpenDeptDropdown($input) {
    $('.dropdown-menu.tax-dropdown').hide();
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true, true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
    const offset = $input.offset();
    $dropdown.css({
      position: 'absolute',
      top: offset.top + $input.outerHeight(),
      left: offset.left,
      width: $input.outerWidth(),
      zIndex: 999,
    }).show();
    const $f = $dropdown.find('.inner-search').first();
    $f.val('');
    $dropdown.find('.in-dept-list div').show();
    $f.focus();
  }

  function tplRow(idx) {
    return (
      '<tr class="in-line-row" data-idx="' + idx + '">' +
      '<td><select class="form-control form-control-sm cs-select" name="consumable_store_id" required><option value="">— Pick branch first —</option></select></td>' +
      '<td class="text-end avail-cell text-muted small">—</td>' +
      '<td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm qty-input" name="quantity_requested" required></td>' +
      '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-line" title="Remove">&times;</button></td>' +
      '</tr>'
    );
  }

  function buildStockSelectOptions() {
    const opts = ['<option value="">— Select item —</option>']
      .concat((stockCache || []).map(function (it) {
        var label = it.item_name + ' (GRN ' + (it.grn_number || '—') + ', avail ' + it.available_qty + ')';
        return '<option value="' + it.id + '" data-avail="' + it.available_qty + '">' +
          $('<div>').text(label).html() +
          '</option>';
      }));
    return opts.join('');
  }

  function refreshStockOptions() {
    const bid = $('#branch_id').val();
    if (!bid) {
      stockCache = [];
      $('#linesBody select.cs-select').html('<option value="">— Select branch —</option>');
      return;
    }
    $.ajax({
      url: routes.stock,
      method: 'GET',
      data: { branch_id: bid, q: '' },
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function (res) {
        if (!res || !res.success) {
          toastr.error((res && res.message) || 'Could not load stock');
          stockCache = [];
          $('#linesBody select.cs-select').html('<option value="">— Error loading stock —</option>');
          return;
        }
        stockCache = res.items || [];
        if (!stockCache.length) {
          toastr.info('No consumable stock at this branch. Post a GRN (Save as Open) first.');
        }
        const html = buildStockSelectOptions();
        $('#linesBody select.cs-select').each(function () {
          const v = $(this).val();
          $(this).html(html);
          if (v) $(this).val(v);
        });
      },
      error: function (xhr) {
        stockCache = [];
        var msg = 'Could not load stock.';
        try {
          var j = xhr.responseJSON;
          if (j && j.message) msg = j.message;
          else if (j && j.errors && j.errors.branch_id) msg = j.errors.branch_id[0];
        } catch (e) {}
        toastr.error(msg);
        $('#linesBody select.cs-select').html('<option value="">— ' + msg + ' —</option>');
      },
    });
  }

  function inPopulateStaticLists() {
    const $c = $('#indentCreateForm .company-list');
    $c.empty();
    $c.append($('<div>').attr('data-id', '').text('— Optional —'));
    (inCompanies || []).forEach(function (co) {
      $c.append($('<div>').attr('data-id', co.id).text(co.company_name || ''));
    });
    const $z = $('#indentCreateForm .zone-list');
    $z.empty();
    $z.append($('<div>').attr('data-id', '').text('Select zone'));
    (inZones || []).forEach(function (z) {
      $z.append($('<div>').attr('data-id', z.id).text(z.name || ''));
    });
    const $df = $('#indentCreateForm .department-list');
    const $dt = $('#indentCreateForm .in-dept-to-list');
    $df.empty();
    $dt.empty();
    (inDepartments || []).forEach(function (d) {
      $df.append($('<div>').attr('data-id', d.id).text(d.name || ''));
      $dt.append($('<div>').attr('data-id', d.id).text(d.name || ''));
    });
    inSyncBranchListsFromZone($('#indentCreateForm .zone_id').val() || '');
  }

  $(document).on('click', '#indentCreateForm .company-search-input', function (e) {
    e.stopPropagation();
    const $w = $(this).closest('.company-section');
    $('.dropdown-menu.tax-dropdown').not($w.find('.dropdown-menu')).hide();
    $w.find('.dropdown-menu.tax-dropdown').first().show();
  });

  $(document).on('click', '#indentCreateForm .company-list div', function (e) {
    e.stopPropagation();
    const id = $(this).data('id');
    const name = $(this).text().trim();
    const $w = $(this).closest('.tax-dropdown-wrapper');
    $w.find('.company-search-input').val(name);
    $w.find('.company_id').val(id === '' || id === undefined ? '' : id);
    $w.find('.dropdown-menu.tax-dropdown').hide();
  });

  $(document).on('click', '#indentCreateForm .zone-search-input', function (e) {
    e.stopPropagation();
    inOpenZoneDropdown($(this));
  });

  $(document).on('click', '#indentCreateForm .branch-search-input', function (e) {
    e.stopPropagation();
    inOpenBranchDropdown($(this));
  });

  $(document).on('click', '#indentCreateForm .in-dept-from-input', function (e) {
    e.stopPropagation();
    inOpenDeptDropdown($(this));
  });

  $(document).on('click', '#indentCreateForm .in-dept-to-input', function (e) {
    e.stopPropagation();
    inOpenDeptDropdown($(this));
  });

  $(document).on('click', '.dropdown-menu.tax-dropdown .zone-list div', function (e) {
    e.stopPropagation();
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length || !$wrapper.closest('#indentCreateForm').length) {
      return;
    }
    const selectedText = $(this).text().trim();
    const selectedId = $(this).data('id');
    const $form = $('#indentCreateForm');
    $wrapper.find('.zone-search-input').val(selectedId === '' || selectedId === undefined ? '' : selectedText);
    $wrapper.find('.zone_id').val(selectedId === '' || selectedId === undefined ? '' : selectedId);

    $form.find('.branch-search-input').val('');
    $form.find('.branch_id').val('');
    $form.find('.branch-section .branch-list div').removeClass('selected');
    const $bCl = $form.find('.branch-search-input').data('dropdown');
    if ($bCl && $bCl.length) {
      $bCl.find('.branch-list div').removeClass('selected');
    }
    inSyncBranchListsFromZone($wrapper.find('.zone_id').val() || '');

    $dropdown.hide();
    $wrapper.find('.zone-search-input').attr('readonly', true);
  });

  $(document).on('keyup', '#indentCreateForm .zone-search-input', function () {
    const searchText = $(this).val().toLowerCase();
    const $dropdown = $(this).data('dropdown');
    if ($dropdown) {
      $dropdown.find('.zone-list div').each(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
      });
    }
  });

  $(document).on('keyup', '.dropdown-menu.tax-dropdown.branch-menu .inner-search', function () {
    const q = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.branch-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });

  $(document).on('click', '.dropdown-menu.tax-dropdown.branch-menu .branch-list div', function (e) {
    e.stopPropagation();
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length || !$wrapper.closest('#indentCreateForm').length) {
      return;
    }
    $(this).siblings().removeClass('selected');
    $(this).addClass('selected');
    const label = $(this).text().trim();
    const bid = $(this).data('id');
    const zid = $(this).data('zone-id');
    $wrapper.find('.branch-search-input').val(label);
    $wrapper.find('.branch_id').val(bid).trigger('change');
    const $form = $('#indentCreateForm');
    if (zid !== undefined && zid !== null && zid !== '') {
      $form.find('.zone_id').val(String(zid));
      $form.find('.zone-search-input').val(inZoneName(zid)).attr('readonly', true);
    }
    $dropdown.hide();
  });

  $(document).on('keyup', '.in-dept-from-filter', function () {
    const q = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.department-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });

  $(document).on('keyup', '.in-dept-to-filter', function () {
    const q = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.in-dept-to-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });

  $(document).on('click', '.department-list div[data-id]', function (e) {
    e.stopPropagation();
    const id = $(this).data('id');
    const label = $(this).text().trim();
    const $menu = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrapper = $menu.data('wrapper');
    if (!$wrapper || !$wrapper.closest('#indentCreateForm').length) {
      return;
    }
    $wrapper.find('.in-dept-from-input').val(label);
    $wrapper.find('.from_department_id').val(id);
    $menu.hide();
  });

  $(document).on('click', '.in-dept-to-list div[data-id]', function (e) {
    e.stopPropagation();
    const id = $(this).data('id');
    const label = $(this).text().trim();
    const $menu = $(this).closest('.dropdown-menu.tax-dropdown');
    const $wrapper = $menu.data('wrapper');
    if (!$wrapper || !$wrapper.closest('#indentCreateForm').length) {
      return;
    }
    $wrapper.find('.in-dept-to-input').val(label);
    $wrapper.find('.to_department_id').val(id);
    $menu.hide();
  });

  $(document).on('click', function (e) {
    if (
      $(e.target).closest('#indentCreateForm .tax-dropdown-wrapper').length ||
      $(e.target).closest('.dropdown-menu.tax-dropdown').length
    ) {
      return;
    }
    $('#indentCreateForm .company-section .dropdown-menu.tax-dropdown').hide();
    $('.dropdown-menu.tax-dropdown').hide();
  });

  inPopulateStaticLists();

  $('#branch_id').on('change', function () {
    if ($('#linesBody tr').length === 0) {
      $('#linesBody').append(tplRow(lineIdx++));
    }
    refreshStockOptions();
  });

  $('#btnAddLine').on('click', function () {
    if (!$('#branch_id').val()) {
      toastr.warning('Select branch first.');
      return;
    }
    $('#linesBody').append(tplRow(lineIdx++));
    refreshStockOptions();
  });

  $(document).on('click', '.btn-remove-line', function () {
    $(this).closest('tr').remove();
  });

  $(document).on('change', 'select.cs-select', function () {
    const opt = $(this).find(':selected');
    const av = parseFloat(opt.data('avail'));
    $(this).closest('tr').find('.avail-cell').text(isNaN(av) ? '—' : av.toFixed(2));
  });

  $('#indentCreateForm').on('submit', function (e) {
    e.preventDefault();
    const lines = [];
    $('#linesBody tr').each(function () {
      const $r = $(this);
      const cid = $r.find('select.cs-select').val();
      const qty = parseFloat($r.find('.qty-input').val());
      const cat = $r.find('input[name=item_category]').val();
      if (cid && !isNaN(qty) && qty > 0) {
        lines.push({
          consumable_store_id: parseInt(cid, 10),
          quantity_requested: qty,
          item_category: cat || null,
        });
      }
    });
    if (!lines.length) {
      toastr.error('Add at least one item line.');
      return;
    }
    if (!$('#branch_id').val()) {
      toastr.error('Select a branch.');
      return;
    }
    const fromD = $('#from_department_id').val();
    const toD = $('#to_department_id').val();
    if (!fromD || !toD) {
      toastr.error('Select both from and to departments.');
      return;
    }
    if (fromD === toD) {
      toastr.warning('From and to departments are the same — continue only if intentional.');
    }
    const payload = {
      company_id: $('#company_id').val() || null,
      zone_id: $('#zone_id').val() || null,
      branch_id: $('#branch_id').val(),
      from_department_id: fromD,
      to_department_id: toD,
      remarks: $('#remarks').val() || null,
      lines: lines,
    };
    $('#btnSubmit').prop('disabled', true);
    $.ajax({
      url: routes.store,
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      contentType: 'application/json',
      data: JSON.stringify(payload),
      success: function (res) {
        if (res.success) {
          toastr.success(res.message || 'Saved');
          window.location.href = routes.index;
        } else {
          toastr.error(res.message || 'Failed');
        }
      },
      error: function (xhr) {
        const j = xhr.responseJSON;
        if (j && j.message) toastr.error(j.message);
        else if (j && j.errors) {
          Object.keys(j.errors).forEach(function (k) {
            toastr.error(j.errors[k][0]);
          });
        } else toastr.error('Request failed');
      },
      complete: function () {
        $('#btnSubmit').prop('disabled', false);
      },
    });
  });
})();
</script>

@include('superadmin.superadminfooter')
</body>
</html>
