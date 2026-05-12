<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('assets/css/pharmacy_audit.css') }}">

<body class="phau-page" style="overflow-x: hidden;">
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

    <div class="phau-shell phau-index">
      <div class="phau-card phau-card--index">
        <header class="phau-hero phau-hero--index">
          <div class="phau-hero-inner">
            <span class="phau-hero-kicker"><i class="bi bi-clipboard2-pulse me-1"></i> Stock reconciliation</span>
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

          <form method="get" action="{{ route('pharmacy-audits.index') }}" id="phauFilterForm" autocomplete="off">
          <div class="phau-filter phau-filter--index">
            <div class="phau-filter-head">
              <div class="phau-filter-head-left">
                <span class="phau-filter-title"><i class="bi bi-funnel"></i> Filters</span>
              </div>
              <span class="phau-showing">Showing <strong>{{ $rowRange }}</strong> of <strong>{{ $records->total() }}</strong></span>
            </div>

              <input type="hidden" name="date_from" id="phauDateFrom" value="{{ request('date_from') }}">
              <input type="hidden" name="date_to" id="phauDateTo" value="{{ request('date_to') }}">

              <div class="grnpr-array-hiddens" data-array-name="company_id" hidden>
                @foreach ($selCompanyIds as $cid)
                  <input type="hidden" name="company_id[]" value="{{ $cid }}">
                @endforeach
              </div>
              <div class="grnpr-array-hiddens" data-array-name="zone_id" hidden>
                @foreach ($selZoneIds as $zid)
                  <input type="hidden" name="zone_id[]" value="{{ $zid }}">
                @endforeach
              </div>
              <div class="grnpr-array-hiddens" data-array-name="branch_id" hidden>
                @foreach ($selBranchIds as $bid)
                  <input type="hidden" name="branch_id[]" value="{{ $bid }}">
                @endforeach
              </div>

              <div class="phau-filter-grid">
                <div class="phau-fg">
                  <label><i class="bi bi-calendar3 me-1"></i>Date range</label>
                  <div class="phau-date-wrap" id="phauReportRange" role="button" tabindex="0">
                    <i class="bi bi-calendar3"></i>
                    <span class="flex-grow-1 text-start" id="phauDateLabel">{{ $dateLabel }}</span>
                    <i class="bi bi-caret-down-fill" style="font-size:0.7rem;"></i>
                  </div>
                </div>
                <div class="phau-fg">
                  <label>Company</label>
                  <div class="grnpr-dd" data-filter-param="company_id" data-empty-label="All companies">
                    <input type="text" class="grnpr-dd-input" placeholder="Select company" value="{{ $companyDisp }}" readonly>
                    <template>
                      @foreach ($companies as $co)
                        <div class="grnpr-opt @if(in_array((int) $co->id, $selCompanyIds, true)) selected @endif" data-id="{{ $co->id }}" data-label="{{ $co->company_name }}">{{ $co->company_name }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>
                <div class="phau-fg">
                  <label>Zone</label>
                  <div class="grnpr-dd" data-filter-param="zone_id" data-empty-label="All zones">
                    <input type="text" class="grnpr-dd-input" placeholder="Select zone" value="{{ $zoneDisp }}" readonly>
                    <template>
                      @foreach ($zones as $z)
                        <div class="grnpr-opt @if(in_array((int) $z->id, $selZoneIds, true)) selected @endif" data-id="{{ $z->id }}" data-label="{{ $z->name }}">{{ $z->name }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>
                <div class="phau-fg">
                  <label>Branch</label>
                  <div class="grnpr-dd" data-filter-param="branch_id" data-empty-label="All branches">
                    <input type="text" class="grnpr-dd-input" placeholder="Select branch" value="{{ $branchDisp }}" readonly>
                    <template>
                      @foreach ($branches as $b)
                        <div class="grnpr-opt @if(in_array((int) $b->id, $selBranchIds, true)) selected @endif" data-id="{{ $b->id }}" data-label="{{ $b->name }}" data-zone="{{ $b->zone_id }}">{{ $b->name }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>
              </div>

              @if ($hasChips)
                <div class="phau-chips">
                  <span class="phau-chips-label">Filters:</span>
                  @if ($df && $dt)
                    <a href="{{ $chipUrl(['date_from', 'date_to']) }}" class="phau-chip"><i class="bi bi-calendar3"></i><span>{{ $dateLabel }}</span><span class="phau-chip-x">&times;</span></a>
                  @endif
                  @if ($selCompanyIds !== [])
                    <a href="{{ $chipUrl(['company_id']) }}" class="phau-chip"><span>{{ $companyDisp }}</span><span class="phau-chip-x">&times;</span></a>
                  @endif
                  @if ($selZoneIds !== [])
                    <a href="{{ $chipUrl(['zone_id']) }}" class="phau-chip"><span>{{ $zoneDisp }}</span><span class="phau-chip-x">&times;</span></a>
                  @endif
                  @if ($selBranchIds !== [])
                    <a href="{{ $chipUrl(['branch_id']) }}" class="phau-chip"><span>{{ $branchDisp }}</span><span class="phau-chip-x">&times;</span></a>
                  @endif
                  @if ($searchTrim !== '')
                    <a href="{{ $chipUrl(['universal_search']) }}" class="phau-chip"><span>Search: {{ $searchTrim }}</span><span class="phau-chip-x">&times;</span></a>
                  @endif
                  <a href="{{ route('pharmacy-audits.index') }}" class="phau-chip phau-chip--clear">Clear all</a>
                </div>
              @endif
          </div>

          <div class="phau-table-toolbar phau-table-toolbar--index">
            <div class="phau-table-toolbar__field phau-table-toolbar__field--search">
              <label class="phau-table-toolbar__label" for="phauSearch">Search</label>
              <div class="phau-table-toolbar__search-wrap">
                <i class="bi bi-search phau-table-toolbar__search-icon" aria-hidden="true"></i>
                <input type="search" name="universal_search" id="phauSearch" class="form-control phau-form-ctl phau-table-toolbar__search-input" placeholder="Item, batch, audit #…" value="{{ request('universal_search') }}" autocomplete="off">
              </div>
            </div>
            <div class="phau-table-toolbar__field phau-table-toolbar__field--pagesize">
              <label class="phau-table-toolbar__label" for="phauPerPage">Rows per page</label>
              <select name="per_page" id="phauPerPage" class="form-select phau-form-ctl phau-table-toolbar__select">
                @foreach ($perPageChoices as $n)
                  <option value="{{ $n }}" @selected((int) $perPage === (int) $n)>{{ $n }}</option>
                @endforeach
              </select>
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
                    <th class="phau-num text-center">Lines</th>
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
                        <p class="phau-empty-title">No audits to display</p>
                        <p class="phau-empty-text">Adjust filters or create a new audit. You can import multiple rows from an Excel or CSV file using <strong>Import</strong> above.</p>
                        <div class="phau-empty-actions">
                          <a href="{{ route('pharmacy-audits.create') }}" class="phau-empty-btn phau-empty-btn--solid"><i class="bi bi-plus-lg"></i> New audit</a>
                          <button type="button" class="phau-empty-btn phau-empty-btn--ghost" data-bs-toggle="modal" data-bs-target="#phauImportModal"><i class="bi bi-upload"></i> Import</button>
                        </div>
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
          <p class="phau-modal-hint mb-3">Rows are grouped by <strong>Company</strong>, <strong>Zone</strong>, <strong>Branch</strong>, and <strong>Audit date</strong>. Each group becomes one audit record. Use the sample file so column headers match.</p>
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
              <input type="file" name="import_file" id="phauImportModalFile" class="form-control phau-form-ctl phau-modal-file-input" accept=".xlsx,.xls,.csv" required>
              <span class="phau-modal-file-note" id="phauImportModalFileName" aria-live="polite"></span>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function ($) {
  const $form = $('#phauFilterForm');
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
    const $box = $form.find('.grnpr-array-hiddens[data-array-name="' + name + '"]');
    $box.empty();
    ids.forEach(function (id) {
      if (id === '' || id == null) return;
      $box.append($('<input>', { type: 'hidden', name: name + '[]', value: String(id) }));
    });
  }
  function refreshDdInputFromState($dd) {
    const $float = $dd.data('float');
    if (!$float) return;
    const param = $dd.data('filter-param');
    const empty = $dd.data('empty-label') || 'All';
    const labels = [], ids = [];
    $float.find('.grnpr-opt.selected').each(function () {
      labels.push($(this).attr('data-label') || $(this).text().trim());
      ids.push($(this).attr('data-id'));
    });
    $dd.find('.grnpr-dd-input').val(labels.length ? labels.join(', ') : empty);
    syncHidden(param, ids);
    scheduleSubmit();
  }
  function position($input, $float) {
    const r = $input[0].getBoundingClientRect();
    const w = Math.max(r.width, 240);
    const vw = window.innerWidth || document.documentElement.clientWidth || 0;
    let left = r.left;
    if (left + w > vw - 8) left = Math.max(8, vw - w - 8);
    $float.css({ position: 'fixed', top: r.bottom + 4, left: left, width: w, zIndex: 10050 });
  }
  function buildFloating($dd) {
    let $float = $dd.data('float');
    if ($float) return $float;
    const tplHtml = $dd.find('template').html() || '';
    $float = $('<div class="grnpr-floating"></div>').append(
      '<div class="grnpr-search-wrap"><input type="text" class="grnpr-search-input" placeholder="Search…"></div>' +
      '<div class="grnpr-actions">' +
        '<button type="button" class="grnpr-btn-mini grnpr-btn-all">Select all</button>' +
        '<button type="button" class="grnpr-btn-mini grnpr-btn-clear">Clear</button>' +
      '</div>' +
      '<div class="grnpr-list">' + tplHtml + '</div>'
    );
    $('body').append($float);
    $float.data('owner', $dd);
    $dd.data('float', $float);
    $float.on('click', '.grnpr-opt', function (e) {
      e.stopPropagation();
      $(this).toggleClass('selected');
      refreshDdInputFromState($dd);
    });
    $float.on('click', '.grnpr-btn-all', function (e) {
      e.stopPropagation();
      $float.find('.grnpr-opt:visible').addClass('selected');
      refreshDdInputFromState($dd);
    });
    $float.on('click', '.grnpr-btn-clear', function (e) {
      e.stopPropagation();
      $float.find('.grnpr-opt').removeClass('selected');
      refreshDdInputFromState($dd);
    });
    $float.on('keyup input', '.grnpr-search-input', function () {
      const q = ($(this).val() || '').toLowerCase();
      $float.find('.grnpr-opt').each(function () {
        $(this).toggle(($(this).text() || '').toLowerCase().indexOf(q) > -1);
      });
    });
    $float.on('click', function (e) { e.stopPropagation(); });
    return $float;
  }
  $(document).on('click', '.grnpr-dd .grnpr-dd-input', function (e) {
    e.stopPropagation();
    $('.grnpr-floating.show').removeClass('show').hide();
    const $input = $(this);
    const $dd = $input.closest('.grnpr-dd');
    const $float = buildFloating($dd);
    position($input, $float);
    $float.addClass('show').show();
    $float.find('.grnpr-search-input').val('').focus();
    $float.find('.grnpr-opt').show();
  });
  $(document).on('keydown', '.grnpr-dd .grnpr-dd-input', function (e) {
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
      e.preventDefault();
      $(this).trigger('click');
    }
  });
  $(window).on('scroll resize', function () {
    $('.grnpr-floating.show').each(function () {
      const $float = $(this);
      const $dd = $float.data('owner');
      if (!$dd || !$dd.length) return;
      position($dd.find('.grnpr-dd-input').first(), $float);
    });
  });
  $(document).on('click', function (e) {
    if ($(e.target).closest('.grnpr-dd, .grnpr-floating').length) return;
    $('.grnpr-floating.show').removeClass('show').hide();
  });
  $('#phauSearch').on('input', function () { scheduleSubmit(); });
  $('#phauPerPage').on('change', function () { submitNow(); });

  $('#phauImportModalFile').on('change', function () {
    var f = this.files && this.files[0];
    var $n = $('#phauImportModalFileName');
    if (!$n.length) return;
    $n.text(f ? f.name : '');
  });
  $('#phauImportModal').on('hidden.bs.modal', function () {
    var form = document.getElementById('phauImportForm');
    if (form) form.reset();
    $('#phauImportModalFileName').text('');
  });

  if (typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined') {
    const $dr = $('#phauReportRange');
    const df = $('#phauDateFrom').val();
    const dt = $('#phauDateTo').val();
    const opts = {
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD', separator: ' – ', cancelLabel: 'Clear', applyLabel: 'Apply' },
      opens: 'left',
      parentEl: 'body',
    };
    if (df && dt) {
      opts.startDate = moment(df, 'YYYY-MM-DD');
      opts.endDate = moment(dt, 'YYYY-MM-DD');
    }
    $dr.daterangepicker(opts);
    $dr.on('apply.daterangepicker', function (ev, picker) {
      $('#phauDateFrom').val(picker.startDate.format('YYYY-MM-DD'));
      $('#phauDateTo').val(picker.endDate.format('YYYY-MM-DD'));
      $('#phauDateLabel').text(picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY'));
      submitNow();
    });
    $dr.on('cancel.daterangepicker', function () {
      $('#phauDateFrom').val('');
      $('#phauDateTo').val('');
      $('#phauDateLabel').text('All dates');
      submitNow();
    });
  }
})(jQuery);

@if (session('success'))
  toastr.success(@json(session('success')));
@endif
@if (session('error'))
  toastr.error(@json(session('error')));
@endif
</script>
@include('superadmin.superadminfooter')
</body>
</html>
