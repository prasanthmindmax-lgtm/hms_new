<!doctype html>
<html lang="en">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<style>
  .cs-store-table { table-layout: fixed; width: 100%; border-collapse: separate; border-spacing: 0; }
  .cs-store-table .cs-col-grn { width: 118px; }
  .cs-store-table .cs-col-dept { width: 120px; }
  .cs-store-table .cs-col-zonebranch {
    width: 200px;
    min-width: 180px;
    vertical-align: middle;
  }
  .cs-store-table .cs-col-company { width: 130px; }
  .cs-store-table .cs-col-item,
  .cs-store-table .cs-col-item-cell {
    width: 260px;
    max-width: 260px;
  }
  .cs-store-table .cs-col-item-cell {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
  }
  .cs-store-table .cs-col-price { width: 100px; }
  .cs-store-table .cs-col-qty { width: 88px; }

  /* Premium UI Enhancements */
  .qd-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.025);
    border: 1px solid #e2e8f0;
    padding: 24px;
    margin-bottom: 24px;
    transition: all 0.3s ease;
  }
  
  .qd-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px dashed #e2e8f0;
  }
  
  .qd-header-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .qd-header-title i {
    color: #3b82f6;
    font-size: 1.5rem;
  }
  
  .btn-outline-secondary {
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.2s ease-in-out;
  }
  .btn-outline-secondary:hover {
      background: #f1f5f9;
      color: #0f172a;
      border-color: #cbd5e1;
  }


  /* Search */
  .qd-search-row {
    margin-bottom: 24px;
  }
  
  .qd-search-wrap {
    position: relative;
    max-width: 400px;
  }
  
  .qd-search-wrap i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
  }
  
  .universal_search {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: #f8fafc;
  }
  
  .universal_search:focus {
    background: #ffffff;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
    outline: none;
  }

  /* Table Enhancements */
  .qd-table-wrap {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
  }
  
  table.cs-store-table th {
    background: #f8fafc;
    padding: 14px 16px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
  }
  
  table.cs-store-table td {
    padding: 16px;
    font-size: 0.875rem;
    border-bottom: 1px dashed #f1f5f9;
    vertical-align: middle;
  }
  
  table.cs-store-table tbody tr {
    transition: background-color 0.15s ease;
  }
  
  table.cs-store-table tbody tr:hover {
    background-color: #f8fafc;
  }
  
  /* Badges */
  .qdt-zone-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    margin-bottom: 4px;
  }
  
  .qdt-zone-orange { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
  .qdt-zone-green { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
  .qdt-zone-purple { background: #faf5ff; color: #9333ea; border: 1px solid #f3e8ff; }
  .qdt-zone-teal { background: #f0fdfa; color: #0d9488; border: 1px solid #ccfbf1; }

  .qdt-branch {
    font-size: 0.8125rem;
    color: #64748b;
    font-weight: 500;
  }

  .qd-pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px solid #e2e8f0;
  }
  
  .qd-pagination select.form-control {
      border-radius: 8px;
      background: #f8fafc;
      border: 1px solid #cbd5e1;
  }
</style>

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">

    <div class="qd-card">

      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-boxes"></i>
           {{ $pageTitle ?? 'Consumable Store' }}
        </div>
        <div class="qd-header-actions">
          <a href="{{ route('superadmin.indents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-journal-text me-1"></i>Store indents
          </a>
        </div>
      </div>

      <div class="qd-stats" id="statsSection">
        <div class="qd-stat-card qd-stat-blue qd-stat-active">
          <div class="qd-stat-icon"><i class="bi bi-list-ul"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total lines</div>
            <div class="qd-stat-value" data-stat-key="total">{{ $stats['total'] ?? 0 }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green">
          <div class="qd-stat-icon"><i class="bi bi-calculator"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Sum of qty</div>
            <div class="qd-stat-value" data-stat-key="total_qty">{{ number_format((float) ($stats['total_qty'] ?? 0), 2) }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
      </div>

      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" class="universal_search" placeholder="Search GRN number, item name, department…">
        </div>
      </div>

      <div class="qd-table-wrap">
        <div id="consumable-store-body">
          @include('vendor.partials.table.consumable_store_rows', ['consumableStoreList' => $consumableStoreList, 'perPage' => $perPage])
        </div>
      </div>

    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {
  let filters = { universal_search: '' };

  function loadConsumableStore(page, perPage) {
    page = page || 1;
    perPage = perPage || $('#per_page').val() || 10;
    $.ajax({
      url: '{{ route("superadmin.consumable-store.dashboard") }}',
      type: 'GET',
      data: {
        page: page,
        per_page: perPage,
        universal_search: filters.universal_search || undefined
      },
      success: function (data) {
        if (typeof data === 'object' && data.html !== undefined) {
          $('#consumable-store-body').html(data.html);
          if (data.stats) {
            $('[data-stat-key="total"]').text(data.stats.total);
            var tq = parseFloat(data.stats.total_qty, 10);
            $('[data-stat-key="total_qty"]').text(!isNaN(tq) ? tq.toFixed(2) : data.stats.total_qty);
          }
        } else {
          $('#consumable-store-body').html(data);
        }
      }
    });
  }

  $('.universal_search').on('keyup', function () {
    filters.universal_search = $(this).val();
    loadConsumableStore(1);
  });

  $(document).on('click', '#consumable-store-body .pagination a', function (e) {
    e.preventDefault();
    const href = $(this).attr('href');
    if (!href || href.indexOf('?') === -1) return;
    const params = new URLSearchParams(href.split('?')[1]);
    loadConsumableStore(params.get('page') || 1, $('#per_page').val());
  });

  $(document).on('change', '#per_page', function () {
    loadConsumableStore(1, $(this).val());
  });
});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
