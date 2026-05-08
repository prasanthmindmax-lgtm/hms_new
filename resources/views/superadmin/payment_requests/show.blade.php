<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />

<body class="pay-pr-show-page" style="overflow-x: hidden;">
  <div class="page-loader">
    <div class="bar"></div>
  </div>

  @include('superadmin.superadminnav')
  @include('superadmin.superadminheader')

  <div class="pc-container">
    <div class="pc-content">

@php
  $po = $r->legacyPurchaseOrder;
  $isPo = \App\Models\PaymentRequest::requiresPoAttachment($r->payment_type);
  $st = (string) ($r->status ?? \App\Models\PaymentRequest::STATUS_PENDING);
  $pending = $r->isPendingReview();
  $canApproveReject = $pending && (int) (($admin->access_limits ?? 0)) === 1;
@endphp
<div class="pr-pay-module w-100 mb-4">
  <div class="qd-card tk-tickets-page pr-show-surface">
    <div class="tk-hero">
      <div class="tk-hero-inner">
        <h1 class="tk-hero-title m-0">
          <i class="bi bi-receipt-cutoff" aria-hidden="true"></i> {{ $r->request_no }}
        </h1>
      </div>
      <div class="tk-hero-actions flex-wrap pr-show-hero-actions">
        <a href="{{ route('superadmin.payment-requests.index') }}" class="tk-btn-export text-decoration-none"><i class="bi bi-arrow-left-short" aria-hidden="true"></i> Back to list</a>
        @if($canApproveReject)
          <button type="button" class="pr-show-btn-approve" data-bs-toggle="modal" data-bs-target="#payReqApproveModal" title="Approve for processing">
            <i class="bi bi-check2-circle" aria-hidden="true"></i> Approve
          </button>
          <button type="button" class="pr-show-btn-reject" data-bs-toggle="modal" data-bs-target="#payReqRejectModal" title="Reject with a reason">
            <i class="bi bi-x-circle" aria-hidden="true"></i> Reject
          </button>
        @endif
      </div>
    </div>

    <div class="tk-dash-body">
      @if($r->reviewed_at)
        @php
          $reviewerName = ($r->relationLoaded('reviewer') && $r->reviewer) ? ($r->reviewer->user_fullname ?? '—') : '—';
          $reviewedFmt = $r->reviewed_at?->format('M j, Y · H:i');
        @endphp
        @if($st === \App\Models\PaymentRequest::STATUS_APPROVED)
          <div class="pr-show-review-bar pr-show-review-bar--approved mb-3 small rounded-3 px-3 py-2 d-flex flex-wrap align-items-center gap-2">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span><strong>Approved by</strong> {{ $reviewerName }}</span>
            <span class="text-body-secondary">·</span>
            <time datetime="{{ $r->reviewed_at?->toIso8601String() }}">{{ $reviewedFmt }}</time>
          </div>
        @elseif($st === \App\Models\PaymentRequest::STATUS_REJECTED)
          <div class="pr-show-review-bar pr-show-review-bar--rejected mb-3 small rounded-3 px-3 py-2 d-flex flex-wrap align-items-center gap-2">
            <i class="bi bi-x-octagon-fill" aria-hidden="true"></i>
            <span><strong>Rejected by</strong> {{ $reviewerName }}</span>
            <span class="text-body-secondary">·</span>
            <time datetime="{{ $r->reviewed_at?->toIso8601String() }}">{{ $reviewedFmt }}</time>
          </div>
        @else
          <div class="pr-show-review-bar mb-3 small rounded-3 px-3 py-2 d-flex flex-wrap align-items-center gap-2 border bg-light">
            <i class="bi bi-person-check text-secondary" aria-hidden="true"></i>
            <span class="text-body-secondary">Reviewed</span>
            <strong>{{ $reviewedFmt }}</strong>
            <span class="text-body-secondary">·</span>
            <span>{{ $reviewerName }}</span>
          </div>
        @endif
      @endif
      @if($st === \App\Models\PaymentRequest::STATUS_REJECTED && $r->rejection_reason)
        <div class="alert alert-danger border-0 rounded-3 pr-show-reject-box mb-3" role="status">
          <div class="fw-bold mb-1"><i class="bi bi-slash-circle me-1" aria-hidden="true"></i> Rejection reason</div>
          <p class="mb-0 small">{!! nl2br(e($r->rejection_reason)) !!}</p>
        </div>
      @endif

      <section class="pr-show-section" aria-labelledby="pr-show-details-heading">
        <h2 id="pr-show-details-heading" class="pr-show-section-head">
          <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-info-square"></i></span>
          <span class="pr-show-section-head-text">Request Details</span>
        </h2>
        <div class="row g-4 align-items-start">
          <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border: 1px solid #e2e8f0 !important; background: #ffffff;">
              <div class="card-body">
                <div class="row gy-4 gx-lg-5">
                  <!-- Company -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-building"></i>
                        </span>
                        company_id
                      </div>
                      <div class="fw-bold text-dark fs-6">{{ $r->company?->company_name ?? '—' }}</div>
                    </div>
                  </div>
                  <!-- Zone -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-map"></i>
                        </span>
                        zone_id
                      </div>
                      <div class="fw-bold text-dark fs-6">{{ $r->zone?->name ?? '—' }}</div>
                    </div>
                  </div>
                  <!-- Branch -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-diagram-2"></i>
                        </span>
                        branch_id
                      </div>
                      <div class="fw-bold text-dark fs-6">{{ $r->branch?->name ?? '—' }}</div>
                    </div>
                  </div>
                  @php
                    $vendorShowName = trim((string) ($r->vendor_display_name ?? ''));
                  @endphp
                  @if($r->vendor_id || $vendorShowName !== '')
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-shop"></i>
                        </span>
                        Vendor
                      </div>
                      <div class="fw-bold text-primary fs-6">{{ $vendorShowName !== '' ? $vendorShowName : ($r->vendor_id ? 'Vendor #'.$r->vendor_id : '—') }}</div>
                    </div>
                  </div>
                  @endif
                  <!-- Created Date -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-calendar"></i>
                        </span>
                        created_at
                      </div>
                      <div class="fw-bold text-dark fs-6">{{ $r->created_at?->format('M j, Y') }}</div>
                    </div>
                  </div>
                  <!-- Created By -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-person"></i>
                        </span>
                        created_by
                      </div>
                      <div class="fw-bold text-dark fs-6">{{ $r->creator->user_fullname ?? '—' }}</div>
                    </div>
                  </div>
                  <!-- Payment Type -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-credit-card"></i>
                        </span>
                        payment_type
                      </div>
                      <div class="fw-bold mt-1">
                        <span class="badge rounded-pill {{ \App\Models\PaymentRequest::typePillClass($r->payment_type) }} px-3 py-2 fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.02em;">{{ \App\Models\PaymentRequest::typeLabel($r->payment_type) }}</span>
                      </div>
                    </div>
                  </div>
                  <!-- Status -->
                  <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex flex-column h-100">
                      <div class="text-secondary fw-bold text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.06em; font-size: 0.7rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded" style="width: 26px; height: 26px; font-size: 0.9rem;">
                          <i class="bi bi-info-circle"></i>
                        </span>
                        status
                      </div>
                      <div class="fw-bold mt-1">
                        <span class="badge rounded-pill text-uppercase {{ \App\Models\PaymentRequest::statusPillClass($st) }} px-3 py-2 fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.05em;">{{ \App\Models\PaymentRequest::statusLabel($st) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-xl-4">
            <div class="pr-show-amount-card h-100 mb-0">
              <div class="pr-show-amount-card-inner">
                <div class="pr-show-amount-top">
                  <span class="pr-show-amount-lbl">amount</span>
                  <span class="pr-show-amount-currency">INR</span>
                </div>
                <div class="pr-show-amount-fig" aria-label="Amount in Indian rupees">
                  <span class="pr-show-amount-symbol">₹</span><span class="pr-show-amount-num">{{ number_format((float) $r->amount, 2) }}</span>
                </div>
                <p class="pr-show-amount-note mb-0">Single-line entry as submitted on the form</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      @php
        $billDisburse = $r->billDisbursementState();
        $linkBills = $r->linkedBills;
        $billSumGrand = $linkBills->sum(static fn ($b) => (float) ($b->grand_total_amount ?? 0));
        $billSumBalance = $linkBills->sum(static fn ($b) => max(0.0, (float) ($b->balance_amount ?? 0)));
        $billSumPaid = max(0.0, $billSumGrand - $billSumBalance);
        $billPanelSrc = $bill_settlement_source ?? null;
        $billPanelViaLinked = ! empty($bill_settlement_via_linked_bill);
        $billPanelRedundant = ! empty($bill_panel_redundant_with_po);
        $showBillSettlement = ! empty($show_bill_settlement) && $show_bill_settlement && $billPanelSrc;
        $renderBillInnerPanel = $showBillSettlement && ! $billPanelRedundant;
        $billHeadRef = $billPanelSrc
            ? ($billPanelSrc->bill_gen_number ?: $billPanelSrc->bill_number ?: ('#'.$billPanelSrc->id))
            : '';
      @endphp
      <section class="pr-show-section pr-show-section--bill{{ $showBillSettlement ? ' pr-show-section--bill-settled' : '' }}" aria-labelledby="pr-show-bill-pay-heading">
        <h2 id="pr-show-bill-pay-heading" class="pr-show-section-head">
          <span class="pr-show-section-head-ic pr-show-section-head-ic--bill" aria-hidden="true"><i class="bi bi-wallet2"></i></span>
          <span class="pr-show-section-head-text">
            Vendor bill payment
            @if($showBillSettlement && $billHeadRef !== '')
              <span class="text-body-secondary fw-normal text-uppercase" style="font-size:0.68rem;letter-spacing:0.04em;"> · {{ $billHeadRef }}</span>
            @endif
          </span>
        </h2>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
          <span class="pr-show-bill-pay-badge pr-show-bill-pay-badge--{{ $billDisburse }}">
            {{ \App\Models\PaymentRequest::billDisbursementLabel($billDisburse) }}
          </span>
        </div>
        @if($showBillSettlement && $billPanelRedundant)
          <p class="small text-body-secondary mb-2 px-1">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            This bill ({{ $billHeadRef }}) is on the same purchase order — see <strong>PO settlement</strong> below for the combined totals, payment history, and payment requests.
          </p>
        @endif

        @if($renderBillInnerPanel)
          @php
            $__billTotal = (float) $bill_total_snap;
            $__billPaid = (float) ($bill_previously_paid_total ?? 0);
            $__billBalance = (float) ($bill_remaining_after ?? max(0.0, $__billTotal - $__billPaid));
            $__billPastRows = $bill_past_payments ?? [];
            $__billPrRows = $bill_pr_request_rows ?? [];
            $__billPrTot = (float) ($bill_pr_requests_total ?? 0);
            $__billPastTot = round(array_sum(array_column($__billPastRows, 'amount')), 2);
          @endphp
          <div class="pr-show-po-panel pr-show-bill-panel pr-pay-overview-panel mb-3" id="pr-show-bill-settlement-panel">
            @if($st === \App\Models\PaymentRequest::STATUS_REJECTED)
              <p class="small text-danger mb-2">
                <i class="bi bi-exclamation-octagon me-1" aria-hidden="true"></i>
                This request was <strong>rejected</strong>. Figures are from submission; rejected lines do not reduce the live bill balance.
              </p>
            @endif
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pr-show-po-title-row">
              <div class="pr-show-po-settlement-title">
                Bill settlement
                @if($billPanelViaLinked)
                  <span class="badge rounded-pill text-bg-light text-dark ms-1" style="font-size:0.62rem;font-weight:700;letter-spacing:0.04em;">Bill raised against this PR</span>
                @endif
              </div>
              <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end">
                <a href="{{ route('superadmin.getbillcreate', ['id' => $billPanelSrc->id]) }}" class="pr-show-po-ref pr-show-po-ref--link pr-show-po-ref--bill text-decoration-none" target="_blank" rel="noopener noreferrer">
                  <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Open bill
                </a>
                <button type="button"
                  class="pr-show-po-ref pr-show-po-ref--link pr-show-po-ref--bill"
                  data-pr-file-preview-url="{{ route('superadmin.getbillprint', ['id' => $billPanelSrc->id]) }}"
                  data-pr-file-preview-title="Bill PDF — {{ $billHeadRef }}">
                  <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> Bill PDF
                </button>
              </div>
            </div>

            <div class="pr-pay-stat-grid mb-3">
              <div class="pr-pay-stat-tile pr-pay-stat-tile--total">
                <span class="pr-pay-stat-label"><i class="bi bi-receipt me-1" aria-hidden="true"></i>Bill total</span>
                <span class="pr-pay-stat-value">₹{{ number_format($__billTotal, 2) }}</span>
                <span class="pr-pay-stat-hint">At submission</span>
              </div>
              <div class="pr-pay-stat-tile pr-pay-stat-tile--paid">
                <span class="pr-pay-stat-label"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Paid so far</span>
                <span class="pr-pay-stat-value">₹{{ number_format($__billPaid, 2) }}</span>
                <span class="pr-pay-stat-hint">On file for this bill</span>
              </div>
              <div class="pr-pay-stat-tile pr-pay-stat-tile--balance">
                <span class="pr-pay-stat-label"><i class="bi bi-wallet2 me-1" aria-hidden="true"></i>Balance</span>
                <span class="pr-pay-stat-value">₹{{ number_format(max(0.0, $__billBalance), 2) }}</span>
                <span class="pr-pay-stat-hint">Still on bill</span>
              </div>
            </div>

            <div class="row g-3 pr-pay-overview-row">
              <div class="col-12 col-xl-7">
                <div class="pr-pay-history-card h-100">
                  <header class="pr-pay-card-head">
                    <span class="pr-pay-card-head-ic"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                    <span class="pr-pay-card-head-title">Payment history</span>
                    <span class="pr-pay-card-head-meta">
                      {{ count($__billPastRows) }} entr{{ count($__billPastRows) === 1 ? 'y' : 'ies' }} · ₹{{ number_format($__billPastTot, 2) }}
                    </span>
                  </header>
                  @if(count($__billPastRows) > 0)
                    <ul class="pr-pay-history-list">
                      @foreach($__billPastRows as $__row)
                        <li class="pr-pay-history-item">
                          <span class="pr-pay-history-date">
                            <i class="bi bi-calendar3" aria-hidden="true"></i> {{ $__row['date'] ?? '—' }}
                          </span>
                          <span class="pr-pay-history-caption">{{ $__row['caption'] ?? 'Bill / NEFT payment' }}</span>
                          <span class="pr-pay-history-amt">₹{{ number_format((float) ($__row['amount'] ?? 0), 2) }}</span>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <div class="pr-pay-card-empty">
                      <i class="bi bi-inbox" aria-hidden="true"></i>
                      <span>No bill / bank payments recorded against this bill yet.</span>
                    </div>
                  @endif
                </div>
              </div>
              <div class="col-12 col-xl-5">
                <div class="pr-pay-pr-card h-100">
                  <header class="pr-pay-card-head">
                    <span class="pr-pay-card-head-ic pr-pay-card-head-ic--alt"><i class="bi bi-cash-stack" aria-hidden="true"></i></span>
                    <span class="pr-pay-card-head-title">Payment requests</span>
                    <span class="pr-pay-card-head-meta">
                      {{ count($__billPrRows) }} · ₹{{ number_format($__billPrTot, 2) }}
                    </span>
                  </header>
                  @if(count($__billPrRows) > 0)
                    <ul class="pr-pay-pr-list">
                      @foreach($__billPrRows as $__row)
                        @php
                          $__rowStatus = (string) ($__row['status'] ?? '');
                          $__rowStatusLabel = $__row['status_label'] ?? ucfirst($__rowStatus ?: 'Pending');
                          $__rowType = trim((string) ($__row['type_label'] ?? ''));
                          $__rowRef = trim((string) ($__row['ref'] ?? ''));
                          $__rowCaption = trim(implode(' · ', array_filter([$__rowType, $__rowRef])));
                          $__rowAnchor = (string) ($__row['anchor'] ?? '');
                          $__rowAnchorLabel = trim((string) ($__row['anchor_label'] ?? ''));
                        @endphp
                        <li class="pr-pay-pr-item">
                          <div class="pr-pay-pr-item-main">
                            <div class="pr-pay-pr-line">
                              <span class="pr-pay-pr-date">
                                <i class="bi bi-calendar3" aria-hidden="true"></i> {{ $__row['date'] ?? '—' }}
                              </span>
                              @if($__rowStatus !== '')
                                <span class="pr-pay-pr-status pr-pay-pr-status--{{ $__rowStatus }}">{{ $__rowStatusLabel }}</span>
                              @endif
                              @if($__rowAnchor !== '' && $__rowAnchorLabel !== '')
                                <span class="pr-pay-pr-anchor pr-pay-pr-anchor--{{ $__rowAnchor }}" title="{{ $__rowAnchorLabel }}">
                                  <i class="bi {{ $__rowAnchor === 'bill' ? 'bi-receipt' : 'bi-file-earmark-text' }}" aria-hidden="true"></i>
                                  <span>{{ $__rowAnchorLabel }}</span>
                                </span>
                              @endif
                              @if($__rowCaption !== '')
                                <span class="pr-pay-pr-caption">{{ $__rowCaption }}</span>
                              @endif
                            </div>
                          </div>
                          <span class="pr-pay-pr-amt">₹{{ number_format((float) ($__row['amount'] ?? 0), 2) }}</span>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <div class="pr-pay-card-empty">
                      <i class="bi bi-inbox" aria-hidden="true"></i>
                      <span>No payment requests against this bill yet.</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            @if($r->payment_type === \App\Models\PaymentRequest::TYPE_PART_PAYMENT)
              <p class="mb-0 mt-2 small text-body-secondary"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Part payment: the limit includes every request on this bill.</p>
            @endif
          </div>
        @elseif($linkBills->isEmpty())
          <p class="small text-muted mb-0 px-1">No vendor bill has been linked to this payment request yet.</p>
        @endif
        @if($linkBills->isNotEmpty())
          <div class="row g-2 mb-2 small text-body-secondary px-1">
            <div class="col-auto">Bill total</div>
            <div class="col-auto fw-semibold text-body">₹{{ number_format($billSumGrand, 2) }}</div>
            <div class="col-auto">·</div>
            <div class="col-auto">Paid toward bill(s)</div>
            <div class="col-auto fw-semibold text-body">₹{{ number_format($billSumPaid, 2) }}</div>
            <div class="col-auto">·</div>
            <div class="col-auto">Balance due</div>
            <div class="col-auto fw-semibold text-body">₹{{ number_format($billSumBalance, 2) }}</div>
          </div>
          <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important;">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">Bill</th>
                  <th scope="col" class="text-end">Total</th>
                  <th scope="col" class="text-end">Balance</th>
                  <th scope="col" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($linkBills as $bill)
                  <tr>
                    <td>
                      <a href="{{ route('superadmin.getbillcreate', ['id' => $bill->id]) }}" class="fw-semibold text-decoration-none" target="_blank" rel="noopener noreferrer">
                        {{ $bill->bill_gen_number ?: $bill->bill_number ?: ('#'.$bill->id) }}
                      </a>
                    </td>
                    <td class="text-end text-nowrap">₹{{ number_format((float) ($bill->grand_total_amount ?? 0), 2) }}</td>
                    <td class="text-end text-nowrap">₹{{ number_format(max(0, (float) ($bill->balance_amount ?? 0)), 2) }}</td>
                    <td class="text-end text-nowrap">
                      <div class="d-inline-flex flex-wrap align-items-center justify-content-end gap-1">
                        <button type="button"
                          class="btn btn-sm btn-outline-danger"
                          data-pr-file-preview-url="{{ route('superadmin.getbillprint', ['id' => $bill->id]) }}"
                          data-pr-file-preview-title="Bill PDF — {{ $bill->bill_gen_number ?: $bill->bill_number ?: ('#'.$bill->id) }}">
                          <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                          <span class="d-none d-sm-inline">Open PDF</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>

      @if($isPo && $r->po_total_snapshot !== null)
        <section class="pr-show-section pr-show-section--po" aria-labelledby="pr-show-po-heading">
          <h2 id="pr-show-po-heading" class="pr-show-section-head">
            <span class="pr-show-section-head-ic pr-show-section-head-ic--po" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
            <span class="pr-show-section-head-text">
              Purchase order
              @if($po)
                <span class="text-body-secondary fw-normal text-uppercase" style="font-size:0.68rem;letter-spacing:0.04em;"> · {{ $po->purchase_gen_order ?? $po->purchase_order_number ?? 'PO #'.$po->id }}</span>
              @endif
            </span>
          </h2>
          @php
            $__poTotal = (float) $r->po_total_snapshot;
            $__poPaid = (float) ($po_previously_paid_total ?? 0);
            $__poBalance = (float) ($po_remaining_after ?? max(0.0, $__poTotal - $__poPaid));
            $__poBillRows = $po_bill_pay_line_rows ?? [];
            $__poPrRows = $po_pr_request_rows ?? [];
            $__poPrTot = (float) ($po_pr_requests_total ?? 0);
            $__poBillTot = round(array_sum(array_column($__poBillRows, 'amount')), 2);
          @endphp
          <div class="pr-show-po-panel pr-pay-overview-panel" id="pay-po-balance-panel">
            @if($st === \App\Models\PaymentRequest::STATUS_REJECTED)
              <p class="small text-danger mb-2">
                <i class="bi bi-exclamation-octagon me-1" aria-hidden="true"></i>
                This request was <strong>rejected</strong>. Figures are from submission; rejected lines do not reduce the live PO balance.
              </p>
            @endif
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pr-show-po-title-row">
              <div class="pr-show-po-settlement-title">PO settlement</div>
              <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end">
                @if($po)
                  <button type="button"
                    class="pr-show-po-ref pr-show-po-ref--link"
                    data-pr-file-preview-url="{{ route('superadmin.getpurchaseprint', ['id' => $po->id]) }}"
                    data-pr-file-preview-title="PO PDF — {{ $po->purchase_gen_order ?? $po->order_number ?? $po->purchase_order_number ?? 'PO #'.$po->id }}">
                    <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> PO PDF
                  </button>
                @endif
                @foreach($linkBills as $bill)
                  <button type="button"
                    class="pr-show-po-ref pr-show-po-ref--link pr-show-po-ref--bill"
                    data-pr-file-preview-url="{{ route('superadmin.getbillprint', ['id' => $bill->id]) }}"
                    data-pr-file-preview-title="Bill PDF — {{ $bill->bill_gen_number ?: $bill->bill_number ?: ('#'.$bill->id) }}">
                    <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> {{ $bill->bill_gen_number ?: $bill->bill_number ?: ('Bill #'.$bill->id) }}
                  </button>
                @endforeach
              </div>
            </div>

            <div class="pr-pay-stat-grid mb-3">
              <div class="pr-pay-stat-tile pr-pay-stat-tile--total">
                <span class="pr-pay-stat-label"><i class="bi bi-receipt-cutoff me-1" aria-hidden="true"></i>PO total</span>
                <span class="pr-pay-stat-value">₹{{ number_format($__poTotal, 2) }}</span>
                <span class="pr-pay-stat-hint">At submission</span>
              </div>
              <div class="pr-pay-stat-tile pr-pay-stat-tile--paid">
                <span class="pr-pay-stat-label"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Paid so far</span>
                <span class="pr-pay-stat-value">₹{{ number_format($__poPaid, 2) }}</span>
                <span class="pr-pay-stat-hint">Bills + payment requests</span>
              </div>
              <div class="pr-pay-stat-tile pr-pay-stat-tile--balance">
                <span class="pr-pay-stat-label"><i class="bi bi-wallet2 me-1" aria-hidden="true"></i>Balance</span>
                <span class="pr-pay-stat-value">₹{{ number_format(max(0.0, $__poBalance), 2) }}</span>
                <span class="pr-pay-stat-hint">Still on PO</span>
              </div>
            </div>

            <div class="row g-3 pr-pay-overview-row">
              <div class="col-12 col-xl-6">
                <div class="pr-pay-history-card h-100">
                  <header class="pr-pay-card-head">
                    <span class="pr-pay-card-head-ic"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                    <span class="pr-pay-card-head-title">Payment history</span>
                    <span class="pr-pay-card-head-meta">
                      {{ count($__poBillRows) }} entr{{ count($__poBillRows) === 1 ? 'y' : 'ies' }} · ₹{{ number_format($__poBillTot, 2) }}
                    </span>
                  </header>
                  @if(count($__poBillRows) > 0)
                    <ul class="pr-pay-history-list">
                      @foreach($__poBillRows as $__row)
                        <li class="pr-pay-history-item">
                          <span class="pr-pay-history-date">
                            <i class="bi bi-calendar3" aria-hidden="true"></i> {{ $__row['date'] ?? '—' }}
                          </span>
                          <span class="pr-pay-history-caption">{{ $__row['caption'] ?? 'Bill / NEFT payment' }}</span>
                          <span class="pr-pay-history-amt">₹{{ number_format((float) ($__row['amount'] ?? 0), 2) }}</span>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <div class="pr-pay-card-empty">
                      <i class="bi bi-inbox" aria-hidden="true"></i>
                      <span>No bill / bank payments recorded against this PO yet.</span>
                    </div>
                  @endif
                </div>
              </div>
              <div class="col-12 col-xl-6">
                <div class="pr-pay-pr-card h-100">
                  <header class="pr-pay-card-head">
                    <span class="pr-pay-card-head-ic pr-pay-card-head-ic--alt"><i class="bi bi-cash-stack" aria-hidden="true"></i></span>
                    <span class="pr-pay-card-head-title">Payment requests</span>
                    <span class="pr-pay-card-head-meta">
                      {{ count($__poPrRows) }} · ₹{{ number_format($__poPrTot, 2) }}
                    </span>
                  </header>
                  @if(count($__poPrRows) > 0)
                    <ul class="pr-pay-pr-list">
                      @foreach($__poPrRows as $__row)
                        @php
                          $__rowStatus = (string) ($__row['status'] ?? '');
                          $__rowStatusLabel = $__row['status_label'] ?? ucfirst($__rowStatus ?: 'Pending');
                          $__rowType = trim((string) ($__row['type_label'] ?? ''));
                          $__rowRef = trim((string) ($__row['ref'] ?? ''));
                          $__rowCaption = trim(implode(' · ', array_filter([$__rowType, $__rowRef])));
                          $__rowAnchor = (string) ($__row['anchor'] ?? '');
                          $__rowAnchorLabel = trim((string) ($__row['anchor_label'] ?? ''));
                        @endphp
                        <li class="pr-pay-pr-item">
                          <div class="pr-pay-pr-item-main">
                            <div class="pr-pay-pr-line">
                              <span class="pr-pay-pr-date">
                                <i class="bi bi-calendar3" aria-hidden="true"></i> {{ $__row['date'] ?? '—' }}
                              </span>
                              @if($__rowStatus !== '')
                                <span class="pr-pay-pr-status pr-pay-pr-status--{{ $__rowStatus }}">{{ $__rowStatusLabel }}</span>
                              @endif
                              @if($__rowAnchor !== '' && $__rowAnchorLabel !== '')
                                <span class="pr-pay-pr-anchor pr-pay-pr-anchor--{{ $__rowAnchor }}" title="{{ $__rowAnchorLabel }}">
                                  <i class="bi {{ $__rowAnchor === 'bill' ? 'bi-receipt' : 'bi-file-earmark-text' }}" aria-hidden="true"></i>
                                  <span>{{ $__rowAnchorLabel }}</span>
                                </span>
                              @endif
                              @if($__rowCaption !== '')
                                <span class="pr-pay-pr-caption">{{ $__rowCaption }}</span>
                              @endif
                            </div>
                          </div>
                          <span class="pr-pay-pr-amt">₹{{ number_format((float) ($__row['amount'] ?? 0), 2) }}</span>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <div class="pr-pay-card-empty">
                      <i class="bi bi-inbox" aria-hidden="true"></i>
                      <span>No payment requests against this PO yet.</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            @if($r->payment_type === \App\Models\PaymentRequest::TYPE_PART_PAYMENT)
              <p class="mb-0 mt-2 small text-body-secondary"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Part payment: the limit includes every request on this PO.</p>
            @endif
          </div>
        </section>
      @endif

      @if($po && (! $isPo || $r->po_total_snapshot === null))
        <p class="small text-muted mt-2 mb-0 px-1">Linked PO:
          <button type="button"
            class="fw-semibold text-body text-decoration-none pr-show-po-inline-link btn btn-link p-0 align-baseline border-0"
            data-pr-file-preview-url="{{ route('superadmin.getpurchaseprint', ['id' => $po->id]) }}"
            data-pr-file-preview-title="PO PDF — {{ $po->purchase_gen_order ?? $po->order_number ?? $po->purchase_order_number ?? 'ID '.$po->id }}">
            {{ $po->purchase_gen_order ?? $po->order_number ?? $po->purchase_order_number ?? 'ID '.$po->id }}
          </button>
          <span class="text-muted">(PDF)</span>
        </p>
      @endif

      @if($r->bill_id && $r->sourceBill && (int) ($r->sourceBill->delete_status ?? 0) === 0 && empty($show_bill_settlement))
        <p class="small text-muted mt-2 mb-0 px-1">
          Raised against bill:
          <a href="{{ route('superadmin.getbillcreate', ['id' => $r->sourceBill->id]) }}" class="fw-semibold text-decoration-none" target="_blank" rel="noopener noreferrer">
            {{ $r->sourceBill->bill_gen_number ?: $r->sourceBill->bill_number ?: ('#'.$r->sourceBill->id) }}
          </a>
        </p>
      @endif

      @if($r->bank_account_number || $r->bank_ifsc_code || $r->bank_branch_details || $r->bank_document_path)
        <section class="pr-show-section" aria-labelledby="pr-show-bank-heading">
          <h2 id="pr-show-bank-heading" class="pr-show-section-head">
            <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-bank"></i></span>
            <span class="pr-show-section-head-text">Payee bank details</span>
          </h2>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="pr-show-kv h-100 mb-0">
                <div class="pr-show-kv-lbl">Account number</div>
                <div class="pr-show-kv-val">{{ $r->bank_account_number ? e($r->bank_account_number) : '—' }}</div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="pr-show-kv h-100 mb-0">
                <div class="pr-show-kv-lbl">IFSC</div>
                <div class="pr-show-kv-val">{{ $r->bank_ifsc_code ? e($r->bank_ifsc_code) : '—' }}</div>
              </div>
            </div>
            <div class="col-12">
              <div class="pr-show-kv mb-0">
                <div class="pr-show-kv-lbl">Branch details</div>
                <div class="pr-show-kv-val">{!! $r->bank_branch_details ? nl2br(e($r->bank_branch_details)) : '—' !!}</div>
              </div>
            </div>
          </div>
        </section>
      @endif

      @if($r->po_attachment_path || $r->document_attachment_path || $r->bank_document_path)
      <section class="pr-show-section pr-show-section--files" aria-labelledby="pr-show-files-heading">
        <h2 id="pr-show-files-heading" class="pr-show-section-head">
          <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-paperclip"></i></span>
          <span class="pr-show-section-head-text">Attachments</span>
        </h2>
        <div class="d-flex flex-wrap gap-3 pr-show-files">
          @if($r->po_attachment_path)
            @php
              $poAttUrl = \App\Models\PaymentRequest::attachmentPublicUrl($r->po_attachment_path);
              $poAttName = basename(str_replace('\\', '/', $r->po_attachment_path));
            @endphp
            @if($poAttUrl)
            <button type="button" class="pr-show-file-tile" data-pr-file-preview-url="{{ $poAttUrl }}" data-pr-file-preview-title="{{ $poAttName !== '' ? $poAttName : 'PO attachment' }}">
              <span class="pr-show-file-ic" aria-hidden="true"><i class="bi bi-file-earmark-pdf"></i></span>
              <span>
                <span class="pr-show-file-title">PO attachment</span>
                <span class="pr-show-file-sub text-truncate d-inline-block" style="max-width: 12rem;">{{ $poAttName !== '' ? $poAttName : 'View in window' }}</span>
              </span>
              <i class="bi bi-arrows-fullscreen pr-show-file-go" aria-hidden="true"></i>
            </button>
            @endif
          @endif
          @if($r->document_attachment_path)
            @php
              $docAttUrl = \App\Models\PaymentRequest::attachmentPublicUrl($r->document_attachment_path);
              $docAttName = basename(str_replace('\\', '/', $r->document_attachment_path));
            @endphp
            @if($docAttUrl)
            <button type="button" class="pr-show-file-tile" data-pr-file-preview-url="{{ $docAttUrl }}" data-pr-file-preview-title="{{ $docAttName !== '' ? $docAttName : 'Supporting document' }}">
              <span class="pr-show-file-ic" aria-hidden="true"><i class="bi bi-paperclip"></i></span>
              <span>
                <span class="pr-show-file-title">Supporting document</span>
                <span class="pr-show-file-sub text-truncate d-inline-block" style="max-width: 12rem;">{{ $docAttName !== '' ? $docAttName : 'View in window' }}</span>
              </span>
              <i class="bi bi-arrows-fullscreen pr-show-file-go" aria-hidden="true"></i>
            </button>
            @endif
          @endif
          @if($r->bank_document_path)
            @php
              $bankAttUrl = \App\Models\PaymentRequest::attachmentPublicUrl($r->bank_document_path);
              $bankAttName = basename(str_replace('\\', '/', $r->bank_document_path));
            @endphp
            @if($bankAttUrl)
            <button type="button" class="pr-show-file-tile" data-pr-file-preview-url="{{ $bankAttUrl }}" data-pr-file-preview-title="{{ $bankAttName !== '' ? $bankAttName : 'Bank document' }}">
              <span class="pr-show-file-ic" aria-hidden="true"><i class="bi bi-bank"></i></span>
              <span>
                <span class="pr-show-file-title">Bank document</span>
                <span class="pr-show-file-sub text-truncate d-inline-block" style="max-width: 12rem;">{{ $bankAttName !== '' ? $bankAttName : 'View in window' }}</span>
              </span>
              <i class="bi bi-arrows-fullscreen pr-show-file-go" aria-hidden="true"></i>
            </button>
            @endif
          @endif
        </div>
      </section>
      @endif

      @if($r->remarks)
        <div class="pr-show-remarks mt-4">
          <div class="pr-pay-form-section-title mb-2" style="border:0; padding:0; margin:0;">Remarks</div>
          <p class="pr-show-remarks-body mb-0">{!! nl2br(e($r->remarks)) !!}</p>
        </div>
      @endif
    </div>
  </div>
</div>

  </div>
</div>

@if($canApproveReject)
<div class="modal fade pr-pay-approve-modal" id="payReqApproveModal" tabindex="-1" aria-labelledby="payReqApproveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered pr-pay-approve-modal__dialog">
    <div class="modal-content pr-pay-approve-modal__content border-0 shadow-lg">
      <form method="post" action="{{ route('superadmin.payment-requests.approve', $r) }}" class="pr-pay-approve-modal__form">
        @csrf
        <div class="modal-header pr-pay-approve-modal__header flex-nowrap border-0 pb-0">
          <div class="d-flex flex-grow-1 min-w-0 pr-pay-modal__head-main">
            <div class="pr-pay-approve-modal__icon" aria-hidden="true">
              <i class="bi bi-check2-circle"></i>
            </div>
            <div class="pr-pay-approve-modal__head-text">
              <h2 class="modal-title h5 mb-1 fw-bold text-dark" id="payReqApproveModalLabel">Approve payment request</h2>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pr-pay-approve-modal__body">
          <div class="pr-pay-approve-modal__notice" role="note">
            <span class="pr-pay-approve-modal__notice-ic" aria-hidden="true"><i class="bi bi-info-circle-fill"></i></span>
            <span>Request <strong>{{ $r->request_no }}</strong> will be marked <strong>approved</strong> and can be used for billing where applicable.</span>
          </div>
        </div>
        <div class="modal-footer pr-pay-approve-modal__footer border-0 pt-2 flex-wrap">
          <button type="button" class="btn pr-pay-approve-modal__btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn pr-pay-approve-modal__btn-submit">
            <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Confirm approval
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade pr-pay-reject-modal" id="payReqRejectModal" tabindex="-1" aria-labelledby="payReqRejectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered pr-pay-reject-modal__dialog">
    <div class="modal-content pr-pay-reject-modal__content border-0 shadow-lg">
      <form method="post" action="{{ route('superadmin.payment-requests.reject', $r) }}" class="pr-pay-reject-modal__form">
        @csrf
        <div class="modal-header pr-pay-reject-modal__header flex-nowrap border-0 pb-0">
          <div class="d-flex flex-grow-1 min-w-0 pr-pay-modal__head-main">
            <div class="pr-pay-reject-modal__icon" aria-hidden="true">
              <i class="bi bi-x-octagon-fill"></i>
            </div>
            <div class="pr-pay-reject-modal__head-text">
              <h2 class="modal-title h5 mb-1 fw-bold text-dark" id="payReqRejectModalLabel">Reject payment request</h2>
            </div>
          </div>
          <button type="button" class="btn-close pr-pay-reject-modal__close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pr-pay-reject-modal__body pt-3">
          <label class="form-label fw-semibold mb-2" for="pay_reject_reason">Reason for rejection <span class="text-danger">*</span></label>
          <textarea class="form-control pr-pay-reject-modal__textarea" name="rejection_reason" id="pay_reject_reason" rows="5" required maxlength="5000" placeholder="Be specific (e.g. amount does not match PO, bank document missing or unreadable, wrong vendor or branch)…"></textarea>
        </div>
        <div class="modal-footer pr-pay-reject-modal__footer border-0 pt-2 gap-2 flex-wrap">
          <button type="button" class="btn pr-pay-reject-modal__btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn pr-pay-reject-modal__btn-submit">
            <i class="bi bi-slash-circle me-1" aria-hidden="true"></i> Reject request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<div class="modal fade pr-show-attachment-modal" id="payPrAttachmentModal" tabindex="-1" aria-labelledby="payPrAttachmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom py-2 py-md-3">
        <h2 class="modal-title h5 mb-0 fw-bold text-truncate pe-2" id="payPrAttachmentModalLabel">Preview</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-light pr-show-attachment-modal-body">
        <iframe id="payPrAttachmentIframe" class="pr-show-attachment-iframe d-none" title="Document preview"></iframe>
        <img id="payPrAttachmentImg" class="pr-show-attachment-img d-none img-fluid d-block mx-auto" alt="" />
      </div>
      <div class="modal-footer flex-wrap gap-2 border-top py-2">
        <a id="payPrAttachmentNewTab" class="btn btn-outline-secondary btn-sm" href="#" target="_blank" rel="noopener noreferrer">
          <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Open in new tab
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function() {
  if (typeof toastr === 'undefined') return;
  toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3500 };
  @if(session('success')) toastr.success(@json(session('success'))); @endif
  @if(session('error')) toastr.error(@json(session('error'))); @endif
})();
</script>
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) return;
    var modalEl = document.getElementById('payPrAttachmentModal');
    if (!modalEl) return;
    var iframe = document.getElementById('payPrAttachmentIframe');
    var img = document.getElementById('payPrAttachmentImg');
    var titleEl = document.getElementById('payPrAttachmentModalLabel');
    var newTab = document.getElementById('payPrAttachmentNewTab');
    var imageRe = /\.(jpe?g|png|gif|webp|bmp|svg)(\?|#|$)/i;

    function clearViewer() {
      iframe.classList.add('d-none');
      iframe.removeAttribute('src');
      img.classList.add('d-none');
      img.removeAttribute('src');
    }

    modalEl.addEventListener('hidden.bs.modal', clearViewer);

    function openPreview(url, title) {
      if (!url) return;
      titleEl.textContent = title || 'Preview';
      newTab.href = url;
      clearViewer();
      if (imageRe.test(url)) {
        img.src = url;
        img.alt = title || 'Preview';
        img.classList.remove('d-none');
      } else {
        iframe.src = url;
        iframe.classList.remove('d-none');
      }
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    document.querySelectorAll('[data-pr-file-preview-url]').forEach(function(el) {
      el.addEventListener('click', function() {
        openPreview(el.getAttribute('data-pr-file-preview-url'), el.getAttribute('data-pr-file-preview-title'));
      });
    });
  });
})();
</script>

</body>
</html>
