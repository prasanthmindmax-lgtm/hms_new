<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/pharmacy_audit.css') }}">

<body class="phau-page pay-pr-index-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    @php
      $phau_int_list = function ($v): array {
        if ($v === null || $v === '') return [];
        $items = is_array($v) ? $v : explode(',', (string) $v);
        $out = [];
        foreach ($items as $i) {
          $n = (int) trim((string) $i);
          if ($n > 0) $out[$n] = true;
        }
        return array_keys($out);
      };
      $selCompanyIds = $phau_int_list(request('company_id'));
      $selZoneIds = $phau_int_list(request('zone_id'));
      $selBranchIds = $phau_int_list(request('branch_id'));
      $df = request('date_from');
      $dt = request('date_to');
      $dateLabel = 'All dates';
      if ($df && $dt) {
        try {
          $dateLabel = \Carbon\Carbon::parse($df)->format('M j, Y').' – '.\Carbon\Carbon::parse($dt)->format('M j, Y');
        } catch (\Throwable $e) {
          $dateLabel = trim(($df ?: '…').' – '.($dt ?: '…'));
        }
      } elseif ($df || $dt) {
        $dateLabel = trim(($df ?: '…').' – '.($dt ?: '…'));
      }
      $joinDisp = function (array $ids, $collection, string $idKey, string $labelKey, string $emptyLabel) {
        if ($ids === []) return $emptyLabel;
        $labels = $collection->whereIn($idKey, $ids)->pluck($labelKey)->filter()->values();
        return $labels->isNotEmpty() ? $labels->implode(', ') : $emptyLabel;
      };
      $companyDisp = $joinDisp($selCompanyIds, $companies, 'id', 'company_name', 'All companies');
      $zoneDisp = $joinDisp($selZoneIds, $zones, 'id', 'name', 'All zones');
      $branchDisp = $joinDisp($selBranchIds, $branches, 'id', 'name', 'All branches');
      $searchTrim = trim((string) request('universal_search', ''));
      $hasChips = ($df && $dt) || $selCompanyIds !== [] || $selZoneIds !== [] || $selBranchIds !== [] || $searchTrim !== '';
      $chipUrl = function (array $without) {
        return route('pharmacy-audits.index', request()->except(array_merge($without, ['page'])));
      };
      $rowFrom = $records->firstItem();
      $rowTo = $records->lastItem();
      $rowRange = ($rowFrom && $rowTo) ? ($rowFrom.'–'.$rowTo) : '0';
      $stats = $stats ?? ['total_audits' => 0, 'total_lines' => 0, 'total_variance' => 0.0];
      $exportQs = http_build_query(request()->except(['page']));
    @endphp

    <div class="phau-shell phau-index tk-pr-index">
      <div class="phau-card phau-card--index qd-card tk-tickets-page">
        <header class="phau-hero phau-hero--index">
          <div class="phau-hero-inner">
            <h1 class="phau-hero-title"><i class="bi bi-capsule-pill"></i> Pharmacy audit</h1>
          </div>
          <div class="phau-hero-toolbar" role="toolbar" aria-label="Audit actions">
            <a href="{{ route('pharmacy-audits.create') }}" class="phau-btn-new phau-btn-new--primary"><i class="bi bi-plus-lg"></i> New audit</a>
            <button type="button" class="phau-btn-outline" data-bs-toggle="modal" data-bs-target="#phauImportModal"><i class="bi bi-upload"></i> Import</button>
            <a href="{{ route('pharmacy-audits.export') }}?{{ $exportQs }}" class="phau-btn-outline"><i class="bi bi-file-earmark-spreadsheet"></i> Export</a>
          </div>
        </header>

        <div class="phau-body phau-body--index">
          <div class="phau-stats phau-stats--index" role="group" aria-label="Summary">
            <div class="phau-stat phau-stat--index">
              <span class="phau-stat-ic" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
              <div class="phau-stat-body">
                <span class="phau-stat-lbl">Total audits</span>
                <span class="phau-stat-num">{{ number_format($stats['total_audits']) }}</span>
                <span class="phau-stat-hint">Matching current filters</span>
              </div>
            </div>
            <div class="phau-stat phau-stat--index">
              <span class="phau-stat-ic" aria-hidden="true"><i class="bi bi-list-ol"></i></span>
              <div class="phau-stat-body">
                <span class="phau-stat-lbl">Line items</span>
                <span class="phau-stat-num">{{ number_format($stats['total_lines']) }}</span>
                <span class="phau-stat-hint">Across listed audits</span>
              </div>
            </div>
            <div class="phau-stat phau-stat--index phau-stat--var">
              <span class="phau-stat-ic" aria-hidden="true"><i class="bi bi-currency-rupee"></i></span>
              <div class="phau-stat-body">
                <span class="phau-stat-lbl">Total variance (₹)</span>
                <span class="phau-stat-num">{{ number_format($stats['total_variance'], 2) }}</span>
                <span class="phau-stat-hint">Sum of line values</span>
              </div>
            </div>
          </div>

          <form method="get" action="{{ route('pharmacy-audits.index') }}" id="pay-pr-filter-form" autocomplete="off">
            <div class="tk-filter-shell tk-filter-qd pay-pr-filter-shell">
              <div class="tk-filter-head pay-pr-filter-head">
                <div class="tk-filter-title">
                  <i class="bi bi-sliders2" aria-hidden="true"></i> Refine list
                </div>
                <div class="pay-pr-filter-head-meta d-flex flex-wrap align-items-center gap-2 justify-content-end">
                  <span class="tk-showing-pill mb-0">
                    Rows <strong>{{ $rowRange }}</strong> of <strong>{{ $records->total() }}</strong>
                  </span>
                </div>
              </div>

              <input type="hidden" name="date_from" id="pay_pr_date_from" value="{{ request('date_from') }}">
              <input type="hidden" name="date_to" id="pay_pr_date_to" value="{{ request('date_to') }}">

              <div class="pay-pr-array-hiddens" data-array-name="company_id" aria-hidden="true">
                @foreach ($selCompanyIds as $cid)
                  <input type="hidden" name="company_id[]" value="{{ $cid }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="zone_id" aria-hidden="true">
                @foreach ($selZoneIds as $zid)
                  <input type="hidden" name="zone_id[]" value="{{ $zid }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="branch_id" aria-hidden="true">
                @foreach ($selBranchIds as $bid)
                  <input type="hidden" name="branch_id[]" value="{{ $bid }}">
                @endforeach
              </div>

              <div class="qd-filters pay-pr-qd-filters">
                <div class="qd-filter-row pay-pr-qd-filter-row">
                  <div class="qd-filter-group">
                    <label><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>Date range</label>
                    <div class="qd-date-wrap" id="payPrReportRange" role="button" tabindex="0">
                      <i class="bi bi-calendar3" aria-hidden="true"></i>
                      <span id="payPrDateLabel">{{ $dateLabel }}</span>
                      <i class="bi bi-caret-down-fill" style="margin-left:auto;" aria-hidden="true"></i>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper company-section pay-pr-dd" data-filter-param="company_id" data-empty-label="All companies">
                    <label>Company</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Company" value="{{ $companyDisp }}" readonly autocomplete="off">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search company..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect company-list">
                        @foreach ($companies as $co)
                          <div data-value="{{ $co->company_name }}" data-id="{{ $co->id }}" @class(['selected' => in_array((int) $co->id, $selCompanyIds, true)])>{{ $co->company_name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper zone-section pay-pr-dd" data-filter-param="zone_id" data-empty-label="All zones">
                    <label>Zone</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Zone" value="{{ $zoneDisp }}" readonly autocomplete="off">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search zone..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect zone-list">
                        @foreach ($zones as $z)
                          <div data-value="{{ $z->name }}" data-id="{{ $z->id }}" @class(['selected' => in_array((int) $z->id, $selZoneIds, true)])>{{ $z->name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper branch-section pay-pr-dd" data-filter-param="branch_id" data-empty-label="All branches">
                    <label>Branch</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Branch" value="{{ $branchDisp }}" readonly autocomplete="off">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search branch..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect branch-list">
                        @foreach ($branches as $b)
                          <div data-value="{{ $b->name }}" data-id="{{ $b->id }}" @class(['selected' => in_array((int) $b->id, $selBranchIds, true)])>{{ $b->name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              @if ($hasChips)
                <div class="qd-applied-bar pay-pr-applied-bar">
                  <span class="applied-label">Filters:</span>
                  <div class="pay-pr-filter-chips d-flex flex-wrap align-items-center" style="gap:6px;flex:1;min-width:0;">
                    @if ($df && $dt)
                      <a href="{{ $chipUrl(['date_from', 'date_to']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                        <i class="bi bi-calendar3" aria-hidden="true"></i><span>{{ $dateLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                      </a>
                    @endif
                    @if ($selCompanyIds !== [])
                      <a href="{{ $chipUrl(['company_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                        <span>{{ $companyDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                      </a>
                    @endif
                    @if ($selZoneIds !== [])
                      <a href="{{ $chipUrl(['zone_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                        <span>{{ $zoneDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                      </a>
                    @endif
                    @if ($selBranchIds !== [])
                      <a href="{{ $chipUrl(['branch_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                        <span>{{ $branchDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                      </a>
                    @endif
                    @if ($searchTrim !== '')
                      <a href="{{ $chipUrl(['universal_search']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                        <i class="bi bi-search" aria-hidden="true"></i><span>{{ \Illuminate\Support\Str::limit($searchTrim, 48) }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                      </a>
                    @endif
                  </div>
                  <a href="{{ route('pharmacy-audits.index') }}" class="filter-badge filter-clear text-decoration-none ms-auto">Clear all</a>
                </div>
              @endif

              <div class="row align-items-end justify-content-between pay-pr-table-toolbar mt-3 g-2 g-md-3 mx-0 w-100">
                <div class="col-12 col-md-4 pay-pr-table-toolbar-search">
                  <div class="qd-search-row pay-pr-toolbar-search-row mb-0">
                    <div class="qd-search-wrap pay-pr-toolbar-search-wrap w-100">
                      <i class="bi bi-search" aria-hidden="true"></i>
                      <input type="text"
                        name="universal_search"
                        id="pay_pr_universal_search"
                        value="{{ request('universal_search') }}"
                        maxlength="200"
                        placeholder="Search audits, items, batch..."
                        autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-auto pay-pr-per-page-field pay-pr-per-page-field--toolbar">
                  <label class="form-label pay-pr-per-page-label mb-1 d-block text-md-end" for="pay-pr-per-page">Rows per page</label>
                  <select id="pay-pr-per-page" name="per_page" class="form-select form-select-sm pay-pr-per-page-select" autocomplete="off" aria-label="Rows per page">
                    @foreach ($perPageChoices as $n)
                      <option value="{{ $n }}" @selected((int) $perPage === (int) $n)>{{ $n }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </form>

          <div class="phau-table-panel">
            <div class="phau-table-panel-head">
              <span class="phau-table-panel-title"><i class="bi bi-table me-2"></i>Audit register</span>
              <span class="phau-table-panel-meta">{{ number_format($records->total()) }} record(s)</span>
            </div>
            <div class="phau-table-wrap phau-table-wrap--index">
              <table class="phau-table phau-table--index">
                <thead>
                  <tr>
                    <th>Audit #</th>
                    <th>Date</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th class="phau-num text-center">Items</th>
                    <th class="phau-num text-center">Total (₹)</th>
                    <th>Created by</th>
                    <th class="phau-th-actions">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($records as $r)
                    <tr class="phau-tr">
                      <td>
                        <a class="phau-ref phau-ref--audit" href="{{ route('pharmacy-audits.show', $r) }}">{{ $r->audit_number }}</a>
                      </td>
                      <td><span class="phau-cell-muted">{{ $r->audit_date?->format('d M Y') }}</span></td>
                      <td><span class="phau-cell-strong">{{ $r->company?->company_name ?: '—' }}</span></td>
                      <td>
                        <div class="phau-loc">
                          <span class="phau-loc-zone"><i class="bi bi-geo-alt"></i>{{ $r->zone?->name ?: '—' }}</span>
                          <span class="phau-loc-branch">{{ $r->branch?->name ?: '—' }}</span>
                        </div>
                      </td>
                      <td class="phau-num text-center">{{ number_format($r->total_lines) }}</td>
                      <td class="phau-num phau-num--val text-center @if((float) $r->total_val < 0) phau-num--neg @endif">{{ number_format((float) $r->total_val, 2) }}</td>
                      <td><span class="phau-cell-person">{{ $r->creator?->user_fullname ?? '—' }}</span></td>
                      <td class="phau-td-actions">
                        <div class="phau-actions justify-content-center">
                          <a href="{{ route('pharmacy-audits.show', $r) }}" class="phau-iconbtn" title="View"><i class="bi bi-eye"></i></a>
                          <a href="{{ route('pharmacy-audits.edit', $r) }}" class="phau-iconbtn phau-iconbtn--alt" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="phau-empty phau-empty--index">
                        <div class="phau-empty-visual" aria-hidden="true"><i class="bi bi-inbox"></i></div>
                        <p class="phau-empty-title">No records found.</p>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <div class="phau-pagination phau-pagination--index">
            {{ $records->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="phauImportModal" tabindex="-1" aria-labelledby="phauImportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content phau-modal">
      <form method="post" action="{{ route('pharmacy-audits.import') }}" enctype="multipart/form-data" id="phauImportForm">
        @csrf
        <div class="modal-header phau-modal-header">
          <div>
            <h5 class="modal-title phau-modal-title" id="phauImportModalLabel"><i class="bi bi-cloud-arrow-up me-2"></i>Import audits</h5>
            <p class="phau-modal-lead mb-0">Excel (.xlsx, .xls) or CSV — one file per upload</p>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body phau-modal-body">
          <div class="phau-modal-template-row">
            <div class="phau-modal-template-copy">
              <span class="phau-modal-template-label"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Sample file</span>
              <span class="phau-modal-template-desc">Download the official import layout, fill your data, then upload below.</span>
            </div>
            <a href="{{ route('pharmacy-audits.import.template') }}" class="phau-modal-template-btn">
              <i class="bi bi-download"></i> Download template
            </a>
          </div>
          <div class="phau-modal-upload-block mt-4">
            <label class="phau-modal-file-label" for="phauImportModalFile">Upload file <span class="text-danger">*</span></label>
            <div class="phau-modal-file-row">
              <label for="phauImportModalFile" class="phau-upload-surface" id="phauImportDropzone">
                <span class="phau-upload-icon" aria-hidden="true"><i class="bi bi-cloud-arrow-up"></i></span>
                <span class="phau-upload-copy">
                  <span class="phau-upload-title">Drop your import file here or click to browse</span>
                  <span class="phau-upload-subtitle">Accepted formats: `.xlsx`, `.xls`, `.csv`</span>
                </span>
                <span class="phau-upload-cta"><i class="bi bi-paperclip"></i> Choose file</span>
              </label>
              <input type="file" name="import_file" id="phauImportModalFile" class="phau-modal-file-input" accept=".xlsx,.xls,.csv" required>
            </div>
            <div class="phau-selected-file" id="phauSelectedFileBox" hidden>
              <span class="phau-selected-file-label"><i class="bi bi-file-earmark-text"></i> Selected file</span>
              <span class="phau-modal-file-note" id="phauImportModalFileName" aria-live="polite">No file selected</span>
            </div>
          </div>
        </div>
        <div class="modal-footer phau-modal-footer">
          <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary phau-modal-submit"><i class="bi bi-upload me-1"></i> Upload &amp; import</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="application/json" id="phau-toast-data">@json([
  'success' => session('success'),
  'error' => session('error'),
])</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function ($) {
  if (typeof toastr !== 'undefined') {
    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3500 };
  }

  const $form = $('#pay-pr-filter-form');
  if (!$form.length) return;

  let submitTimer = null;
  function submitNow() {
    if (submitTimer) { clearTimeout(submitTimer); submitTimer = null; }
    if ($form[0]) $form[0].submit();
  }
  function scheduleSubmit() {
    if (submitTimer) clearTimeout(submitTimer);
    submitTimer = setTimeout(function () { submitTimer = null; submitNow(); }, 380);
  }
  function syncHidden(name, ids) {
    const $box = $form.find('.pay-pr-array-hiddens[data-array-name="' + name + '"]');
    $box.empty();
    ids.forEach(function (id) {
      if (id === '' || id == null) return;
      $box.append($('<input>', { type: 'hidden', name: name + '[]', value: String(id) }));
    });
  }

  function updateDropdown($dropdown) {
    const $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length) return;
    const param = $wrapper.data('filter-param');
    const empty = $wrapper.data('empty-label') || 'All';
    const labels = [];
    const ids = [];
    $dropdown.find('.dropdown-list.multiselect div').each(function () {
      if (!$(this).hasClass('selected')) return;
      labels.push($(this).text().trim());
      ids.push($(this).attr('data-id'));
    });
    $wrapper.find('.pay-pr-dd-input').val(labels.length ? labels.join(', ') : empty);
    syncHidden(param, ids);
    scheduleSubmit();
  }

  function positionDropdown($input, $dropdown) {
    const rect = $input[0].getBoundingClientRect();
    const width = Math.max(rect.width, 260);
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth || 0;
    let left = rect.left;
    if (left + width > viewportWidth - 8) left = Math.max(8, viewportWidth - width - 8);
    $dropdown.css({ position: 'fixed', top: rect.bottom + 4, left: left, width: width, zIndex: 10050 });
  }

  $(window).on('scroll.phauPayPrFilter resize.phauPayPrFilter', function () {
    $('.dropdown-menu.tax-dropdown.pay-pr-tax-dd:visible').each(function () {
      const $dropdown = $(this);
      const $wrapper = $dropdown.data('wrapper');
      if (!$wrapper || !$wrapper.length) return;
      const $input = $wrapper.find('.pay-pr-dd-input').first();
      if ($input.length) positionDropdown($input, $dropdown);
    });
  });

  $(document).on('click', '#pay-pr-filter-form .pay-pr-dd-input', function (e) {
    e.stopPropagation();
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
    positionDropdown($input, $dropdown);
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
    $(this).toggleClass('selected');
    updateDropdown($(this).closest('.dropdown-menu.tax-dropdown'));
  });

  $(document).on('click', '.pay-pr-tax-dd .select-all', function (e) {
    e.stopPropagation();
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    $dropdown.find('.dropdown-list.multiselect div').addClass('selected');
    updateDropdown($dropdown);
  });

  $(document).on('click', '.pay-pr-tax-dd .deselect-all', function (e) {
    e.stopPropagation();
    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
    updateDropdown($dropdown);
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('#pay-pr-filter-form .tax-dropdown-wrapper').length) return;
    if ($(e.target).closest('.dropdown-menu.tax-dropdown.pay-pr-tax-dd').length) return;
    $('.dropdown-menu.tax-dropdown.pay-pr-tax-dd').hide();
  });

  $('#pay_pr_universal_search').on('input', function () { scheduleSubmit(); });
  $('#pay-pr-per-page').on('change', function () { submitNow(); });

  $('#phauImportModalFile').on('change', function () {
    var f = this.files && this.files[0];
    var $n = $('#phauImportModalFileName');
    var $dropzone = $('#phauImportDropzone');
    var $fileBox = $('#phauSelectedFileBox');
    if (!$n.length) return;
    $n.text(f ? f.name : 'No file selected');
    if ($fileBox.length) {
      $fileBox.prop('hidden', !f);
    }
    if ($dropzone.length) {
      $dropzone.toggleClass('has-file', !!f);
    }
  });
  $('#phauImportModal').on('hidden.bs.modal', function () {
    var form = document.getElementById('phauImportForm');
    if (form) form.reset();
    $('#phauImportModalFileName').text('No file selected');
    $('#phauSelectedFileBox').prop('hidden', true);
    $('#phauImportDropzone').removeClass('has-file is-dragover');
  });

  var $importDropzone = $('#phauImportDropzone');
  var $importFileInput = $('#phauImportModalFile');
  if ($importDropzone.length && $importFileInput.length) {
    $importDropzone.on('dragenter dragover', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $importDropzone.addClass('is-dragover');
    });
    $importDropzone.on('dragleave dragend drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $importDropzone.removeClass('is-dragover');
    });
    $importDropzone.on('drop', function (e) {
      var files = e.originalEvent && e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
      if (!files || !files.length) return;
      try {
        $importFileInput[0].files = files;
        $importFileInput.trigger('change');
      } catch (_err) {
        if (window.toastr) toastr.info('Drag-drop selected the file. If it is not attached, click "Choose file" once.');
      }
    });
  }

  if (typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined') {
    const $dr = $('#payPrReportRange');
    const df = $('#pay_pr_date_from').val();
    const dt = $('#pay_pr_date_to').val();
    const opts = {
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD', separator: ' – ', cancelLabel: 'Clear', applyLabel: 'Apply' },
      opens: 'left',
      drops: 'down'
    };
    if (df && dt) {
      opts.startDate = moment(df, 'YYYY-MM-DD');
      opts.endDate = moment(dt, 'YYYY-MM-DD');
    }
    $dr.daterangepicker(opts);
    $dr.on('apply.daterangepicker', function (ev, picker) {
      $('#pay_pr_date_from').val(picker.startDate.format('YYYY-MM-DD'));
      $('#pay_pr_date_to').val(picker.endDate.format('YYYY-MM-DD'));
      $('#payPrDateLabel').text(picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY'));
      submitNow();
    });
    $dr.on('cancel.daterangepicker', function () {
      $('#pay_pr_date_from').val('');
      $('#pay_pr_date_to').val('');
      $('#payPrDateLabel').text('All dates');
      submitNow();
    });
  }

  let phauToastPayload = {};
  const phauToastData = document.getElementById('phau-toast-data');
  if (phauToastData) {
    try {
      phauToastPayload = JSON.parse(phauToastData.textContent || '{}') || {};
    } catch (error) {
      phauToastPayload = {};
    }
  }
  if (typeof toastr !== 'undefined') {
    if (phauToastPayload.success) toastr.success(phauToastPayload.success);
    if (phauToastPayload.error) toastr.error(phauToastPayload.error);
  }
})(jQuery);
</script>
@include('superadmin.superadminfooter')
</body>
</html>
