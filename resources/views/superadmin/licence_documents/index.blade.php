<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('/assets/css/licence_documents.css') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<body class="ld-licence-index-page" style="overflow-x: hidden;"
  data-ld-flash-success="{{ session('success') ? e(session('success')) : '' }}"
  data-ld-flash-error="{{ session('error') ? e(session('error')) : '' }}">
  <div class="page-loader">
    <div class="bar"></div>
  </div>

  @include('superadmin.superadminnav')
  @include('superadmin.superadminheader')

  <div class="pc-container">
    <div class="pc-content">
      <div class="qd-card tk-tickets-page ld-licence-dash-card">
        <div class="tk-hero">
          <div class="tk-hero-inner">
            <h1 class="tk-hero-title">
              <i class="bi bi-folder2-open" aria-hidden="true"></i>
              Licence documents
            </h1>
          </div>
          <div class="tk-hero-actions">
            <a class="tk-btn-export" href="{{ route('superadmin.licence_documents.catalog.index') }}"
              style="text-decoration: none;">
              <i class="bi bi-sliders" aria-hidden="true"></i>
              Document master
            </a>
          </div>
        </div>

        <div class="tk-dash-body">

          @php
          $renewalNotices = $licenceRenewalNotifications ?? [];
          $renewalNoticesShow = array_slice($renewalNotices, 0, 15);
          $renewalNoticesMore = max(0, count($renewalNotices) - count($renewalNoticesShow));
          @endphp
          @if(count($renewalNotices) > 0)
          <div class="ld-renewal-banner" role="region" aria-label="Licence renewal reminders">
            <div class="ld-renewal-banner-head">
              <i class="bi bi-bell-fill ld-renewal-banner-icon" aria-hidden="true"></i>
              <div>
                <div class="ld-renewal-banner-title">Renewal due date</div>
              </div>
            </div>
            <ul class="ld-renewal-banner-list">
              @foreach($renewalNoticesShow as $rn)
              <li class="ld-renewal-banner-item ld-renewal-banner-item--{{ $rn['kind'] }}">
                <a class="ld-renewal-banner-link" href="{{ route('superadmin.licence_documents.branch', $rn['branch_id']) }}">
                  <span class="ld-renewal-banner-branch">{{ $rn['branch_name'] }}</span>
                  <span class="ld-renewal-banner-doc">{{ $rn['document_label'] }}</span>
                  <span class="ld-renewal-banner-date">{{ $rn['renewal_date']->format('d M Y') }}</span>
                  <span class="ld-renewal-banner-note">{{ $rn['days_note'] }}</span>
                </a>
              </li>
              @endforeach
            </ul>
            @if($renewalNoticesMore > 0)
            <p class="ld-renewal-banner-more">+ {{ $renewalNoticesMore }} more not shown — filter branches below or open each location.</p>
            @endif
          </div>
          @endif

          @php
          $hasAnyBranch = collect($levelBlocks)->sum('branch_count') > 0;
          $st = $licenceStats ?? [];
          $l1b = (int) ($st['l1_branches'] ?? 0);
          $l2b = (int) ($st['l2_branches'] ?? 0);
          $l1c = (int) ($st['l1_complete_branches'] ?? 0);
          $l2c = (int) ($st['l2_complete_branches'] ?? 0);
          $l1dt = (int) ($st['l1_doc_types'] ?? 0);
          $l2dt = (int) ($st['l2_doc_types'] ?? 0);
          $l1p = (int) ($st['l1_pending_branches'] ?? 0);
          $l2p = (int) ($st['l2_pending_branches'] ?? 0);
          $branchFilter = $branchFilter ?? 'all';
          $ldQuery = ($searchQuery ?? '') !== '' ? ['q' => $searchQuery] : [];
          @endphp

          @if($hasAnyBranch && !empty($st))
          <div class="ld-dash-four-cards" role="region" aria-label="Licence documents — pending and level summary">
            <article class="ld-stat-card ld-stat-card--l1 @if ($viewLevel === 1) is-active @endif" data-ld-card-level="1">
              <a class="ld-stat-card-hit ld-stat-card-hit--main"
                href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 1, 'ld_filter' => 'all'], $ldQuery)) }}"
                aria-label="Level 1: show all branches in the table">
                <div class="ld-stat-card-icon" aria-hidden="true"><i class="bi bi-layers"></i></div>
                <div class="ld-stat-card-body">
                  <div class="ld-stat-card-kicker">Level 1</div>
                  <div class="ld-stat-card-value">{{ $l1b }} <span class="ld-stat-card-unit">branches</span></div>
                </div>
              </a>
              <div class="ld-stat-card-foot ld-stat-card-foot--links">
                <a class="ld-stat-pill ld-stat-pill--ok ld-stat-pill--as-link @if ($viewLevel === 1 && $branchFilter === 'complete') is-filter-active @endif"
                  href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 1, 'ld_filter' => 'complete'], $ldQuery)) }}">{{ $l1c }} fully complete</a>
                @if($l1b > 0 && ($l1b - $l1c) > 0)
                <a class="ld-stat-pill ld-stat-pill--muted ld-stat-pill--as-link @if ($viewLevel === 1 && $branchFilter === 'incomplete') is-filter-active @endif"
                  href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 1, 'ld_filter' => 'incomplete'], $ldQuery)) }}">{{ $l1b - $l1c }} remaining</a>
                @endif
              </div>
            </article>
            <article class="ld-stat-card ld-stat-card--l2 @if ($viewLevel === 2) is-active @endif" data-ld-card-level="2">
              <a class="ld-stat-card-hit ld-stat-card-hit--main"
                href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 2, 'ld_filter' => 'all'], $ldQuery)) }}"
                aria-label="Level 2: show all branches in the table">
                <div class="ld-stat-card-icon" aria-hidden="true"><i class="bi bi-layers-half"></i></div>
                <div class="ld-stat-card-body">
                  <div class="ld-stat-card-kicker">Level 2</div>
                  <div class="ld-stat-card-value">{{ $l2b }} <span class="ld-stat-card-unit">branches</span></div>
                </div>
              </a>
              <div class="ld-stat-card-foot ld-stat-card-foot--links">
                <a class="ld-stat-pill ld-stat-pill--ok ld-stat-pill--as-link @if ($viewLevel === 2 && $branchFilter === 'complete') is-filter-active @endif"
                  href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 2, 'ld_filter' => 'complete'], $ldQuery)) }}">{{ $l2c }} fully complete</a>
                @if($l2b > 0 && ($l2b - $l2c) > 0)
                <a class="ld-stat-pill ld-stat-pill--muted ld-stat-pill--as-link @if ($viewLevel === 2 && $branchFilter === 'incomplete') is-filter-active @endif"
                  href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 2, 'ld_filter' => 'incomplete'], $ldQuery)) }}">{{ $l2b - $l2c }} remaining</a>
                @endif
              </div>
            </article>
            <a class="ld-pending-card ld-pending-card--l1 ld-pending-card--link @if ($viewLevel === 1) is-active @endif @if ($viewLevel === 1 && $branchFilter === 'incomplete') is-filter-active @endif"
              data-ld-card-level="1"
              href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 1, 'ld_filter' => 'incomplete'], $ldQuery)) }}"
              aria-label="Level 1 pending — {{ $l1p }} branches below required uploads; open filtered table">
              <div class="ld-pending-card-icon" aria-hidden="true"><i class="bi bi-hourglass-split"></i></div>
              <div class="ld-pending-card-body">
                <div class="ld-pending-card-title ld-pending-card-title--l1">Level 1 pending</div>
                <div class="ld-pending-card-value">{{ $l1p }} <span class="ld-stat-card-unit">branches</span></div>
              </div>
            </a>
            <a class="ld-pending-card ld-pending-card--l2 ld-pending-card--link @if ($viewLevel === 2) is-active @endif @if ($viewLevel === 2 && $branchFilter === 'incomplete') is-filter-active @endif"
              data-ld-card-level="2"
              href="{{ route('superadmin.licence_documents.index', array_merge(['level' => 2, 'ld_filter' => 'incomplete'], $ldQuery)) }}"
              aria-label="Level 2 pending — {{ $l2p }} branches below required uploads; open filtered table">
              <div class="ld-pending-card-icon" aria-hidden="true"><i class="bi bi-hourglass-bottom"></i></div>
              <div class="ld-pending-card-body">
                <div class="ld-pending-card-title ld-pending-card-title--l2">Level 2 pending</div>
                <div class="ld-pending-card-value">{{ $l2p }} <span class="ld-stat-card-unit">branches</span></div>
              </div>
            </a>
          </div>{{-- .ld-dash-four-cards --}}
          @endif

          @if(!$hasAnyBranch)
          <div class="ld-index-empty-state" role="status">
            <div class="ld-index-empty-icon" aria-hidden="true"><i class="bi bi-geo-alt"></i></div>
            <h2 class="ld-index-empty-title">No branches in scope</h2>
            <p class="ld-index-empty-text">Add locations under your regions in Location Master and set each branch to Level&nbsp;1 or Level&nbsp;2 for licence documents.</p>
            <a class="ld-index-empty-btn" href="{{ route('superadmin.locationmaster.index') }}">
              <i class="bi bi-building-add" aria-hidden="true"></i>
              Open Location Master
            </a>
          </div>
          @else
          @php
          $blk1 = $levelBlocks[0];
          $blk2 = $levelBlocks[1];
          $branchFilter = $branchFilter ?? 'all';
          $ldNote = match ($branchFilter) {
              'complete' => ' Showing fully complete branches only.',
              'incomplete' => ' Showing branches still below required uploads.',
              default => '',
          };
          $ldCatalogUrl = route('superadmin.licence_documents.catalog.index');
          $ldClearL1 = route('superadmin.licence_documents.index', array_merge(['level' => 1, 'ld_filter' => 'all'], ($searchQuery ?? '') !== '' ? ['q' => $searchQuery] : []));
          $ldClearL2 = route('superadmin.licence_documents.index', array_merge(['level' => 2, 'ld_filter' => 'all'], ($searchQuery ?? '') !== '' ? ['q' => $searchQuery] : []));
          $lockLevelNav =
          request()->has('level') ||
          request()->has('page_l1') ||
          request()->has('page_l2') ||
          (request()->filled('ld_filter') && (string) request('ld_filter') !== 'all') ||
          ($searchQuery ?? '') !== '';
          @endphp
          <div class="ld-dash-workspace" id="ld-licence-workspace"
            data-default-level="{{ $viewLevel }}"
            data-lock-level="{{ $lockLevelNav ? '1' : '0' }}">
            <div class="ld-dash-panel">
              <div class="ld-dash-panel-top">
                <div class="ld-level-tabs" role="tablist" aria-label="Licence document level">
                  <button type="button" class="ld-level-tab @if ($viewLevel === 1) is-active @endif" role="tab"
                    id="ld-tab-level-1" data-level="1" aria-selected="{{ $viewLevel === 1 ? 'true' : 'false' }}"
                    aria-controls="licence-level-1" tabindex="{{ $viewLevel === 1 ? '0' : '-1' }}">
                    <i class="bi bi-layers" aria-hidden="true"></i><span>Level 1</span>
                  </button>
                  <button type="button"
                    class="ld-level-tab ld-level-tab--l2 @if ($viewLevel === 2) is-active @endif" role="tab"
                    id="ld-tab-level-2" data-level="2" aria-selected="{{ $viewLevel === 2 ? 'true' : 'false' }}"
                    aria-controls="licence-level-2" tabindex="{{ $viewLevel === 2 ? '0' : '-1' }}">
                    <i class="bi bi-layers-half" aria-hidden="true"></i><span>Level 2</span>
                  </button>
                </div>
                <form method="get" action="{{ route('superadmin.licence_documents.index') }}" class="ld-dash-search-form">
                  <input type="hidden" name="level" id="ld-form-level" value="{{ $viewLevel }}" />
                  <input type="hidden" name="ld_filter" id="ld-form-filter" value="{{ $branchFilter }}" />
                  <div class="ld-dash-search-wrap">
                    <label class="visually-hidden" for="ld-branch-filter">Search region or branch</label>
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search" name="q" id="ld-branch-filter" value="{{ $searchQuery ?? '' }}" autocomplete="off"
                      placeholder="Search region or branch…" />
                  </div>
                </form>
              </div>
              <p class="ld-dash-level-summary ld-dash-readability" id="ld-dash-level-summary" role="status">
                <span id="ld-dash-summary-1" class="ld-dash-summary-line" @if ($viewLevel !== 1) hidden @endif>
                  <a href="{{ $ldCatalogUrl }}" class="ld-dash-summary-catalog-link">{{ (int) $blk1['doc_total'] }} document types</a><span
                    class="ld-dash-summary-sep"> · </span><span>{{ (int) $blk1['branch_count'] }} branch(es) at this level</span>
                  @if($branchFilter !== 'all')
                  <span class="ld-dash-summary-filter-note">{{ $ldNote }}</span>
                  <a class="ld-dash-filter-clear" href="{{ $ldClearL1 }}">Show all branches</a>
                  @endif
                </span>
                <span id="ld-dash-summary-2" class="ld-dash-summary-line" @if ($viewLevel !== 2) hidden @endif>
                  <a href="{{ $ldCatalogUrl }}" class="ld-dash-summary-catalog-link">{{ (int) $blk2['doc_total'] }} document types</a><span
                    class="ld-dash-summary-sep"> · </span><span>{{ (int) $blk2['branch_count'] }} branch(es) at this level</span>
                  @if($branchFilter !== 'all')
                  <span class="ld-dash-summary-filter-note">{{ $ldNote }}</span>
                  <a class="ld-dash-filter-clear" href="{{ $ldClearL2 }}">Show all branches</a>
                  @endif
                </span>
              </p>
              <div
                class="ld-dash-table-shell ld-level-table-area ld-dash-table-wrap ld-dash-table-wrap--premium @if ($viewLevel === 2) ld-dash-table-wrap--lvl2 @endif"
                id="ld-dash-table-shell" data-active-level="{{ $viewLevel }}">
                <table class="ld-dash-table ld-dash-table--premium ld-dash-readability">
                  <thead>
                    <tr>
                      <th class="ld-dash-col-num" scope="col"><span class="ld-dash-th"><i class="bi bi-hash"
                            aria-hidden="true"></i><span class="ld-dash-th-text">#</span></span></th>
                      <th class="ld-dash-col-region" scope="col"><span class="ld-dash-th"><i class="bi bi-geo-alt"
                            aria-hidden="true"></i><span class="ld-dash-th-text">Region</span></span></th>
                      <th class="ld-dash-col-branch" scope="col"><span class="ld-dash-th"><i class="bi bi-building"
                            aria-hidden="true"></i><span class="ld-dash-th-text">Branch</span></span></th>
                      <th class="ld-dash-col-docs" scope="col"><span class="ld-dash-th"><i
                            class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i><span
                            class="ld-dash-th-text">Document status</span></span></th>
                      <th class="ld-dash-col-action" scope="col"><span class="ld-dash-th"><i class="bi bi-lightning"
                            aria-hidden="true"></i><span class="ld-dash-th-text">Actions</span></span></th>
                    </tr>
                  </thead>
                  @php
                  $l1Block = $levelBlocks[0];
                  $l2Block = $levelBlocks[1];
                  $l1DocTotal = (int) $l1Block['doc_total'];
                  $l2DocTotal = (int) $l2Block['doc_total'];
                  $l1BranchCount = (int) $l1Block['branch_count'];
                  $l2BranchCount = (int) $l2Block['branch_count'];
                  $l1Zones = $l1Block['zones'];
                  $l2Zones = $l2Block['zones'];
                  @endphp
                  <tbody id="licence-level-1" class="ld-dash-tbody" role="tabpanel"
                    aria-labelledby="ld-tab-level-1" data-level="1"
                    @if ($viewLevel !==1) hidden @endif>
                    @if ($l1BranchCount === 0)
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No branches are assigned to Level 1 in your scope.</div>
                      </td>
                    </tr>
                    @elseif(empty($l1Zones))
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No regions contain Level 1 branches.</div>
                      </td>
                    </tr>
                    @elseif($l1DocTotal === 0 && ($branchFilter ?? 'all') !== 'all')
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No document types are configured for Level 1 in the catalogue, so completion filters do not apply.</div>
                      </td>
                    </tr>
                    @elseif($branchPaginatorL1->total() === 0 && ($searchQuery ?? '') !== '')
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No branches match your search for Level 1.</div>
                      </td>
                    </tr>
                    @elseif($branchPaginatorL1->total() === 0)
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">
                          @if(($branchFilter ?? 'all') === 'complete')
                            No Level 1 branches are fully complete yet.
                          @elseif(($branchFilter ?? 'all') === 'incomplete')
                            No Level 1 branches are below required uploads.
                          @else
                            No branches to show.
                          @endif
                        </div>
                      </td>
                    </tr>
                    @else
                    @foreach ($branchPaginatorL1 as $row)
                    @php
                    $zone = $row['zone'];
                    $b = $row['branch'];
                    $bid = (int) $b->id;
                    $uploaded = (int) ($counts[$bid] ?? 0);
                    $pct = $l1DocTotal > 0 ? (int) round(min(100, ($uploaded / $l1DocTotal) * 100)) : 0;
                    if ($l1DocTotal === 0) {
                    $tier = 'none';
                    $tierLabel = '—';
                    } else {
                    $tier = $uploaded >= $l1DocTotal ? 'complete' : ($uploaded > 0 ? 'partial' : 'none');
                    $tierLabel = $uploaded >= $l1DocTotal ? 'Complete' : ($uploaded > 0 ? 'In progress' : 'Not started');
                    }
                    $rowNum = $branchPaginatorL1->firstItem() ? $branchPaginatorL1->firstItem() + $loop->index : $loop->iteration;
                    @endphp
                    <tr class="ld-dash-row ld-dash-row--{{ $tier }} ld-dash-row--premium">
                      <td class="ld-dash-col-num">
                        <span class="ld-dash-idx"
                          aria-hidden="true">{{ str_pad((string) $rowNum, 2, '0', STR_PAD_LEFT) }}</span>
                      </td>
                      <td class="ld-dash-col-region">
                        <span class="ld-dash-region"><span class="ld-dash-region-dot" aria-hidden="true"></span><span
                            class="ld-dash-region-text">{{ $zone->name }}</span></span>
                      </td>
                      <td class="ld-dash-col-branch">
                        <span class="ld-dash-branch-cell"><i class="bi bi-building ld-dash-branch-ico"
                            aria-hidden="true"></i><span class="ld-dash-branch-name">{{ $b->name }}</span></span>
                      </td>
                      <td class="ld-dash-col-docs">
                        <div class="ld-dash-doc-cell">
                          <div class="ld-dash-progress ld-dash-progress--premium" role="progressbar"
                            aria-valuenow="{{ $uploaded }}" aria-valuemin="0" aria-valuemax="{{ $l1DocTotal }}"
                            aria-label="{{ $uploaded }} of {{ $l1DocTotal }} documents on file">
                            <div class="ld-dash-progress-bar" style="--ld-pct: {{ $pct }}%;"></div>
                          </div>
                          <div class="ld-dash-doc-meta">
                            <span class="ld-dash-ratio"><strong>{{ $uploaded }}</strong><span
                                class="ld-dash-ratio-sep">/</span>{{ $l1DocTotal }}</span>
                            <span class="ld-dash-tier ld-dash-tier--{{ $tier }}">{{ $tierLabel }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="ld-dash-col-action">
                        <a class="ld-dash-btn-open"
                          href="{{ route('superadmin.licence_documents.branch', ['branch' => $bid]) }}">
                          <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span>Open</span>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                  <tbody id="licence-level-2" class="ld-dash-tbody" role="tabpanel"
                    aria-labelledby="ld-tab-level-2" data-level="2"
                    @if ($viewLevel !==2) hidden @endif>
                    @if ($l2BranchCount === 0)
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No branches are assigned to Level 2 in your scope.</div>
                      </td>
                    </tr>
                    @elseif(empty($l2Zones))
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No regions contain Level 2 branches.</div>
                      </td>
                    </tr>
                    @elseif($l2DocTotal === 0 && ($branchFilter ?? 'all') !== 'all')
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No document types are configured for Level 2 in the catalogue, so completion filters do not apply.</div>
                      </td>
                    </tr>
                    @elseif($branchPaginatorL2->total() === 0 && ($searchQuery ?? '') !== '')
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">No branches match your search for Level 2.</div>
                      </td>
                    </tr>
                    @elseif($branchPaginatorL2->total() === 0)
                    <tr class="ld-dash-empty-row">
                      <td colspan="5">
                        <div class="ld-dash-empty-msg">
                          @if(($branchFilter ?? 'all') === 'complete')
                            No Level 2 branches are fully complete yet.
                          @elseif(($branchFilter ?? 'all') === 'incomplete')
                            No Level 2 branches are below required uploads.
                          @else
                            No branches to show.
                          @endif
                        </div>
                      </td>
                    </tr>
                    @else
                    @foreach ($branchPaginatorL2 as $row)
                    @php
                    $zone = $row['zone'];
                    $b = $row['branch'];
                    $bid = (int) $b->id;
                    $uploaded = (int) ($counts[$bid] ?? 0);
                    $pct = $l2DocTotal > 0 ? (int) round(min(100, ($uploaded / $l2DocTotal) * 100)) : 0;
                    if ($l2DocTotal === 0) {
                    $tier = 'none';
                    $tierLabel = '—';
                    } else {
                    $tier = $uploaded >= $l2DocTotal ? 'complete' : ($uploaded > 0 ? 'partial' : 'none');
                    $tierLabel = $uploaded >= $l2DocTotal ? 'Complete' : ($uploaded > 0 ? 'In progress' : 'Not started');
                    }
                    $rowNum = $branchPaginatorL2->firstItem() ? $branchPaginatorL2->firstItem() + $loop->index : $loop->iteration;
                    @endphp
                    <tr class="ld-dash-row ld-dash-row--{{ $tier }} ld-dash-row--premium">
                      <td class="ld-dash-col-num">
                        <span class="ld-dash-idx"
                          aria-hidden="true">{{ str_pad((string) $rowNum, 2, '0', STR_PAD_LEFT) }}</span>
                      </td>
                      <td class="ld-dash-col-region">
                        <span class="ld-dash-region"><span class="ld-dash-region-dot" aria-hidden="true"></span><span
                            class="ld-dash-region-text">{{ $zone->name }}</span></span>
                      </td>
                      <td class="ld-dash-col-branch">
                        <span class="ld-dash-branch-cell"><i class="bi bi-building ld-dash-branch-ico"
                            aria-hidden="true"></i><span class="ld-dash-branch-name">{{ $b->name }}</span></span>
                      </td>
                      <td class="ld-dash-col-docs">
                        <div class="ld-dash-doc-cell">
                          <div class="ld-dash-progress ld-dash-progress--premium" role="progressbar"
                            aria-valuenow="{{ $uploaded }}" aria-valuemin="0" aria-valuemax="{{ $l2DocTotal }}"
                            aria-label="{{ $uploaded }} of {{ $l2DocTotal }} documents on file">
                            <div class="ld-dash-progress-bar" style="--ld-pct: {{ $pct }}%;"></div>
                          </div>
                          <div class="ld-dash-doc-meta">
                            <span class="ld-dash-ratio"><strong>{{ $uploaded }}</strong><span
                                class="ld-dash-ratio-sep">/</span>{{ $l2DocTotal }}</span>
                            <span class="ld-dash-tier ld-dash-tier--{{ $tier }}">{{ $tierLabel }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="ld-dash-col-action">
                        <a class="ld-dash-btn-open"
                          href="{{ route('superadmin.licence_documents.branch', ['branch' => $bid]) }}">
                          <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span>Open</span>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
              <div class="ld-dash-pagination-wrap" data-level="1" role="navigation" aria-label="Level 1 pages"
                @if ($viewLevel !==1) hidden @endif>
                @if ($l1BranchCount > 0 && $branchPaginatorL1->hasPages())
                {{ $branchPaginatorL1->links('vendor.pagination.bootstrap-5') }}
                @endif
              </div>
              <div class="ld-dash-pagination-wrap" data-level="2" role="navigation" aria-label="Level 2 pages"
                @if ($viewLevel !==2) hidden @endif>
                @if ($l2BranchCount > 0 && $branchPaginatorL2->hasPages())
                {{ $branchPaginatorL2->links('vendor.pagination.bootstrap-5') }}
                @endif
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  @include('superadmin.superadminfooter')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    (function() {
      if (typeof toastr === 'undefined') {
        return;
      }
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000,
      };
      var body = document.body;
      var ok = body.getAttribute('data-ld-flash-success') || '';
      var err = body.getAttribute('data-ld-flash-error') || '';
      if (ok) {
        toastr.success(ok);
      }
      if (err) {
        toastr.error(err);
      }
    })();
  </script>
  <script>
    (function() {
      var workspace = document.getElementById('ld-licence-workspace');
      if (!workspace) {
        return;
      }
      var shell = document.getElementById('ld-dash-table-shell');
      var levelField = document.getElementById('ld-form-level');

      function syncStatCards(lv) {
        document.querySelectorAll('[data-ld-card-level]').forEach(function(card) {
          var on = card.getAttribute('data-ld-card-level') === lv;
          card.classList.toggle('is-active', on);
        });
      }

      function setLevel(lv) {
        lv = String(lv);
        if (lv !== '1' && lv !== '2') {
          return;
        }
        workspace.querySelectorAll('.ld-level-tab[role="tab"]').forEach(function(tab) {
          var on = tab.getAttribute('data-level') === lv;
          tab.classList.toggle('is-active', on);
          tab.setAttribute('aria-selected', on ? 'true' : 'false');
          tab.setAttribute('tabindex', on ? '0' : '-1');
        });
        workspace.querySelectorAll('.ld-dash-tbody[role="tabpanel"]').forEach(function(panel) {
          panel.hidden = panel.getAttribute('data-level') !== lv;
        });
        workspace.querySelectorAll('.ld-dash-pagination-wrap[data-level]').forEach(function(wrap) {
          wrap.hidden = wrap.getAttribute('data-level') !== lv;
        });
        if (shell) {
          shell.setAttribute('data-active-level', lv);
          shell.classList.toggle('ld-dash-table-wrap--lvl2', lv === '2');
        }
        var s1 = document.getElementById('ld-dash-summary-1');
        var s2 = document.getElementById('ld-dash-summary-2');
        if (s1) {
          s1.hidden = lv !== '1';
        }
        if (s2) {
          s2.hidden = lv !== '2';
        }
        syncStatCards(lv);
        if (levelField) {
          levelField.value = lv;
        }
        try {
          if (history.replaceState) {
            // Must keep pathname + ?query — a hash-only URL strips level, ld_filter, page_l*, q on refresh.
            var path = window.location.pathname || '';
            var search = window.location.search || '';
            history.replaceState(null, '', path + search + '#licence-level-' + lv);
          }
        } catch (e) {
          /* ignore */
        }
      }

      function resolveInitialLevel() {
        if (workspace.getAttribute('data-lock-level') === '1') {
          return String(workspace.getAttribute('data-default-level') || '1');
        }
        var h = (window.location.hash || '').toLowerCase();
        if (h === '#licence-level-2' || h === '#l2' || h === '#level-2') {
          return '2';
        }
        if (h === '#licence-level-1' || h === '#l1' || h === '#level-1') {
          return '1';
        }
        return String(workspace.getAttribute('data-default-level') || '1');
      }

      var tablist = workspace.querySelector('.ld-level-tabs');
      if (tablist) {
        tablist.addEventListener('keydown', function(ev) {
          if (ev.key !== 'ArrowRight' && ev.key !== 'ArrowLeft') {
            return;
          }
          var t = ev.target.closest('.ld-level-tab');
          if (!t) {
            return;
          }
          ev.preventDefault();
          var go = ev.key === 'ArrowRight' ? '2' : '1';
          setLevel(go);
          var next = workspace.querySelector('.ld-level-tab[data-level="' + go + '"]');
          if (next) {
            next.focus();
          }
        });
      }
      workspace.querySelectorAll('.ld-level-tab[role="tab"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
          setLevel(btn.getAttribute('data-level'));
        });
      });

      window.addEventListener('hashchange', function() {
        if (workspace.getAttribute('data-lock-level') === '1') {
          return;
        }
        setLevel(resolveInitialLevel());
      });

      setLevel(resolveInitialLevel());
    })();
  </script>
</body>

</html>
