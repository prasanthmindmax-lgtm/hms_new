@php
  $o = function ($key, $default = null) {
    $v = old($key);
    if ($v !== null && $v !== '') {
        return $v;
    }
    return $default;
  };
  $cid = $o('company_id');
  $zid = $o('zone_id');
  $bid = $o('branch_id');
  $dispCompany = $cid ? $companies->firstWhere('id', (int) $cid)?->company_name ?? '' : '';
  $dispZone = $zid ? $zones->firstWhere('id', (int) $zid)?->name ?? '' : '';
  $dispBranch = $bid ? $branches->firstWhere('id', (int) $bid)?->name ?? '' : '';
  $vid = $o('vendor_id');
  $dispVendor = '';
  if ($vid) {
      $vRow = $vendors->firstWhere('id', (int) $vid);
      if ($vRow) {
          $dispVendor = trim((string) ($vRow->display_name ?? '')) !== '' ? (string) $vRow->display_name : (string) ($vRow->company_name ?? '');
      }
  }
  $formAction = route('superadmin.payment-requests.store');
  $lookupUrl = route('superadmin.payment-requests.lookup-po');
@endphp
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

<body style="overflow-x: hidden;">
  <div class="page-loader">
    <div class="bar"></div>
  </div>

  @include('superadmin.superadminnav')
  @include('superadmin.superadminheader')

  <div class="pc-container">
    <div class="pc-content">

<div class="pr-pay-module pr-form-page pr-pay-form-page pay-create-surface pay-req-tight">
  <header class="pay-create-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
      <div>
        <h1>
          <i class="bi bi-cash-coin me-1" style="color:#a5b4fc;" aria-hidden="true"></i>
          New payment request
        </h1>
      </div>
      <a href="{{ route('superadmin.payment-requests.index') }}" class="pay-btn-ghost align-self-center">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to list
      </a>
    </div>
  </header>

  <div class="card pr-form-page-card pay-create-form-card border-0 shadow">
    <div class="row g-0 pay-layout-row">
      <div class="col-12 col-xl-8 pay-layout-main">
        <div class="pr-form-card-body p-3 p-lg-4">
          <form method="post" action="{{ $formAction }}" enctype="multipart/form-data" id="pay-req-form" class="pr-form-premium" novalidate
            data-create-form-duration="1">
            @csrf
            <input type="hidden" name="{{ config('create_form_duration.input_name', 'create_form_duration_ms') }}" value="0" id="pay-create-form-duration-ms" autocomplete="off" />

            <div class="pr-pay-sec pr-pay-sec--location pr-pay-form-section">

                <div class="pr-form-section-title pr-pay-form-section-title">
                  <i class="bi bi-geo-alt" aria-hidden="true"></i>
                  <span>Location &amp; Vendor</span>
                </div>
                <div class="row mb-0" id="payLocationStrip">
                  <div class="col-lg-3 col-md-6 mb-3">
                    <div class="tax-dropdown-wrapper company-section pr-dd-wrap">
                      <label class="form-label mb-0" for="pay_dd_company">Company <span class="text-danger">*</span></label>
                      <input id="pay_dd_company" type="text"
                        class="form-control company-search-input pr-dd-input pr-loc-dd-input @error('company_id') is-invalid @enderror"
                        readonly autocomplete="off" placeholder="Select company"
                        value="{{ e($dispCompany) }}">
                      <input type="hidden" name="company_id" class="company_id" value="{{ $cid ?: '' }}">
                      <div class="tax-dropdown pr-dd-panel">
                        <div class="inner-search-container">
                          <input type="text" class="inner-search form-control form-control-sm" placeholder="Search company…" autocomplete="off">
                        </div>
                        <div class="company-list">
                          @foreach ($companies as $co)
                            <div data-value="{{ $co->company_name }}" data-id="{{ $co->id }}">{{ $co->company_name }}</div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    @error('company_id')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-lg-3 col-md-6 mb-3">
                    <div class="tax-dropdown-wrapper account-section pr-dd-wrap">
                      <label class="form-label mb-0" for="pay_dd_zone">Zone <span class="text-danger">*</span></label>
                      <input id="pay_dd_zone" type="text"
                        class="form-control zone-search-input pr-dd-input pr-loc-dd-input @error('zone_id') is-invalid @enderror"
                        readonly autocomplete="off" placeholder="Select zone"
                        value="{{ e($dispZone) }}">
                      <input type="hidden" name="zone_id" class="zone_id" value="{{ $zid ?: '' }}">
                      <div class="tax-dropdown pr-dd-panel">
                        <div class="inner-search-container">
                          <input type="text" class="inner-search form-control form-control-sm" placeholder="Search zone…" autocomplete="off">
                        </div>
                        <div class="zone-list">
                          @foreach ($zones as $z)
                            <div data-id="{{ $z->id }}" data-value="{{ $z->name }}">{{ $z->name }}</div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    @error('zone_id')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-lg-3 col-md-6 mb-3">
                    <div class="tax-dropdown-wrapper account-section pr-dd-wrap">
                      <label class="form-label mb-0" for="pay_dd_branch">Branch <span class="text-danger">*</span></label>
                      <input id="pay_dd_branch" type="text"
                        class="form-control branch-search-input pr-dd-input pr-loc-dd-input @error('branch_id') is-invalid @enderror"
                        readonly autocomplete="off" placeholder="Select branch (after zone)"
                        value="{{ e($dispBranch) }}">
                      <input type="hidden" name="branch_id" class="branch_id" value="{{ $bid ?: '' }}">
                      <div class="tax-dropdown pr-dd-panel">
                        <div class="inner-search-container">
                          <input type="text" class="inner-search form-control form-control-sm" placeholder="Search branch…" autocomplete="off">
                        </div>
                        <div class="branch-list"></div>
                      </div>
                    </div>
                    @error('branch_id')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-lg-3 col-md-6 mb-3">
                    <div class="tax-dropdown-wrapper vendor-section pr-dd-wrap">
                      <label class="form-label mb-0" for="pay_vendor_name">Vendor <span class="text-danger">*</span></label>
                      <input id="pay_vendor_name" type="text" maxlength="255"
                        class="form-control vendor-search-input pr-dd-input pr-loc-dd-input @error('vendor_id') is-invalid @enderror"
                        readonly autocomplete="off" placeholder="Select vendor"
                        value="{{ e($dispVendor) }}">
                      <input type="hidden" name="vendor_id" id="pay_vendor_id" class="vendor_id" value="{{ $vid ?: '' }}">
                      <div class="tax-dropdown pr-dd-panel">
                        <div class="inner-search-container">
                          <input type="text" class="inner-search form-control form-control-sm" placeholder="Search vendor…" autocomplete="off">
                        </div>
                        <div class="vendor-list">
                          @foreach($vendors as $v)
                            @php
                              $label = trim((string) ($v->display_name ?? '')) !== '' ? $v->display_name : (string) ($v->company_name ?? '');
                            @endphp
                            <div data-value="{{ e($label) }}" data-id="{{ $v->id }}">{{ $label }}</div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    @error('vendor_id')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
            </div>

            <div class="pr-pay-sec pr-pay-form-section">
              <div class="pr-pay-form-section-title">
                <i class="bi bi-ui-checks-grid" aria-hidden="true"></i> Type &amp; amount
              </div>
              <div class="row g-3 pay-type-amount-row" id="pay-type-amount-row">
                <div class="col-md-6 pay-type-dd-col">
                  <div class="tax-dropdown-wrapper account-section pr-dd-wrap">
                    <label class="form-label mb-0" for="pay_type_search">Payment type <span class="text-danger">*</span></label>
                    @php
                      $ptOld = old('payment_type');
                      $ptLabels = [
                          'advance' => 'Advance',
                          'part_payment' => 'Part Payment',
                          'settlement' => 'Settlement',
                          'petty_cash_advance' => 'Petty Cash Advance',
                          'reimbursement' => 'Reimbursement',
                          'refund' => 'Ref Payment',
                          'patient_refund' => 'Patient Refund',
                          'instant_payment' => 'Insta Payment',
                          'miscellaneous' => 'Miscellaneous Payment'
                      ];
                      $ptDisp = $ptOld && isset($ptLabels[$ptOld]) ? $ptLabels[$ptOld] : '';
                    @endphp
                    <input id="pay_type_search" type="text"
                      class="form-control type-search-input pr-dd-input pr-loc-dd-input"
                      readonly autocomplete="off" placeholder="Select payment type"
                      value="{{ $ptDisp }}">
                    <input type="hidden" name="payment_type" id="pay_type" class="type_id" value="{{ $ptOld }}">
                    <div class="tax-dropdown pr-dd-panel pr-dd-panel--type">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search form-control form-control-sm" placeholder="Search type…" autocomplete="off">
                      </div>
                      <div class="type-list">
                        @foreach($ptLabels as $val => $label)
                          <div data-value="{{ $label }}" data-id="{{ $val }}">{{ $label }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="pay_amount">Request amount <span class="text-danger">*</span></label>
                  <div class="input-group pay-input-money">
                    <span class="input-group-text">₹</span>
                    <input type="number" name="amount" id="pay_amount" class="form-control" required min="0.01" step="0.01" value="{{ old('amount') }}" placeholder="0.00">
                  </div>
                </div>
              </div>
            </div>

            <div class="pr-form-section pr-pay-sec pr-pay-sec--accent d-none" id="pay-po-block">
              <div class="pay-po-hero">
                <div class="pay-po-hero-text">
                  <div class="pr-pay-form-section-title mb-2" style="border:0; padding:0; margin:0;">
                    <i class="bi bi-file-earmark-ruled" aria-hidden="true"></i> Linked purchase order
                  </div>
                  <p class="lead text-muted">Enter the <strong>generated PO number</strong>, tap <strong>Load</strong> to see totals, then upload the <strong>PO</strong> file. Required for Advance, Part Payment, and Settlement.</p>
                </div>
              </div>
              <div class="row align-items-stretch g-3">
                <div class="col-md-5">
                  <label class="form-label" for="pay_po_id">PO number (purchase_gen_order) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="text" class="form-control rounded-3 @error('purchase_gen_order') is-invalid @enderror" name="purchase_gen_order" id="pay_po_id" value="{{ old('purchase_gen_order') }}" placeholder="e.g. PO-00082" autocomplete="off">
                    <button type="button" class="btn btn-primary rounded-3" id="pay_po_load" title="Load PO from server">
                      <i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Load
                    </button>
                  </div>
                  @error('purchase_gen_order')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-7" id="pay-po-balance-wrap">
                  <div id="pay-po-balance-panel" class="d-none">
                    <div class="row-line"><span class="lbl">PO ref</span><span class="val" id="pay_po_ref">—</span></div>
                    <div class="row-line"><span class="lbl">PO total</span><span class="val" id="pay_po_total">0.00</span></div>
                    <div class="row-line"><span class="lbl">Paid (other requests)</span><span class="val" id="pay_po_paid">0.00</span></div>
                    <div class="row-line"><span class="lbl">Remaining before this</span><span class="val" id="pay_po_rem">0.00</span></div>
                  </div>
                </div>
              </div>
              <div class="mt-3 pr-pay-attachment-zone">
                <label class="form-label">PO attachment (PDF, image, or document) <span class="text-danger">*</span></label>
                <div class="pr-pay-upload-box" id="pay-po-upload-box">
                  <div class="pr-pay-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                  <div class="pr-pay-upload-text">Drag & drop or <span>browse files</span></div>
                  <p class="pr-pay-upload-hint">Support for PDF, Images, Word documents</p>
                  <input type="file" name="po_attachment" id="pay_po_file" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,application/pdf,image/*">
                </div>
                <div class="pr-pay-preview-bar-custom" id="pay-po-preview-bar" hidden>
                  <div class="preview-icon-wrap"><i class="bi bi-file-earmark-check"></i></div>
                  <div class="file-info">
                    <span class="file-name" id="pay-po-preview-name" title=""></span>
                    <div class="file-meta">
                      <span class="file-size" id="pay-po-preview-size"></span>
                    </div>
                  </div>
                  <button type="button" class="btn btn-preview" id="btn-preview-po">
                    <i class="bi bi-eye" aria-hidden="true"></i> View
                  </button>
                </div>
                <p class="small text-muted mt-2 mb-0">Attach a clear copy of the purchase order for approvers.</p>
              </div>
            </div>

            <div class="pr-form-section pr-pay-sec d-none" id="pay-doc-block">
              <div class="pr-pay-form-section-title">
                <i class="bi bi-paperclip" aria-hidden="true"></i> Supporting document
              </div>
              <div class="pr-pay-attachment-zone">
                <label class="form-label">Document <span class="text-danger">*</span></label>
                <div class="pr-pay-upload-box" id="pay-doc-upload-box">
                  <div class="pr-pay-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                  <div class="pr-pay-upload-text">Drag & drop or <span>browse files</span></div>
                  <p class="pr-pay-upload-hint">Support for PDF, Images, Word documents</p>
                  <input type="file" name="document_attachment" id="pay_doc_file" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,application/pdf,image/*">
                </div>
                <div class="pr-pay-preview-bar-custom" id="pay-doc-preview-bar" hidden>
                  <div class="preview-icon-wrap"><i class="bi bi-file-earmark-check"></i></div>
                  <div class="file-info">
                    <span class="file-name" id="pay-doc-preview-name" title=""></span>
                    <div class="file-meta">
                      <span class="file-size" id="pay-doc-preview-size"></span>
                    </div>
                  </div>
                  <button type="button" class="btn btn-preview" id="btn-preview-doc">
                    <i class="bi bi-eye" aria-hidden="true"></i> View
                  </button>
                </div>
              </div>

              <div class="mt-4 pt-3 border-top border-secondary border-opacity-25" id="pay-bank-fields">
                <div class="pr-pay-form-section-title mb-2">
                  <i class="bi bi-bank" aria-hidden="true"></i> Payee bank details
                </div>
                <p class="text-muted small mb-3">Required for Petty Cash Advance, Reimbursement, Ref Payment, Patient Refund, Insta Payment, and Miscellaneous Payment.</p>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="pay_bank_account">Bank account number <span class="text-danger pay-bank-req">*</span></label>
                    <input type="text" name="bank_account_number" id="pay_bank_account" class="form-control @error('bank_account_number') is-invalid @enderror" maxlength="64" value="{{ old('bank_account_number') }}" autocomplete="off" placeholder="Account number">
                    @error('bank_account_number')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="pay_bank_ifsc">IFSC code <span class="text-danger pay-bank-req">*</span></label>
                    <input type="text" name="bank_ifsc_code" id="pay_bank_ifsc" class="form-control text-uppercase @error('bank_ifsc_code') is-invalid @enderror" maxlength="11" value="{{ old('bank_ifsc_code') }}" autocomplete="off" placeholder="e.g. HDFC0001234">
                    @error('bank_ifsc_code')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="pay_bank_branch">Branch details <span class="text-danger pay-bank-req">*</span></label>
                    <textarea name="bank_branch_details" id="pay_bank_branch" class="form-control @error('bank_branch_details') is-invalid @enderror" rows="2" maxlength="5000" placeholder="Bank branch name and address">{{ old('bank_branch_details') }}</textarea>
                    @error('bank_branch_details')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12">
                    <label class="form-label">Bank document <span class="text-danger pay-bank-req">*</span></label>
                    <div class="pr-pay-upload-box" id="pay-bank-upload-box">
                      <div class="pr-pay-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                      <div class="pr-pay-upload-text">Drag & drop or <span>browse files</span></div>
                      <p class="pr-pay-upload-hint">Cancelled cheque, bank statement header, or passbook scan (PDF or image)</p>
                      <input type="file" name="bank_document" id="pay_bank_file" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,application/pdf,image/*">
                    </div>
                    <div class="pr-pay-preview-bar-custom" id="pay-bank-preview-bar" hidden>
                      <div class="preview-icon-wrap"><i class="bi bi-file-earmark-check"></i></div>
                      <div class="file-info">
                        <span class="file-name" id="pay-bank-preview-name" title=""></span>
                        <div class="file-meta">
                          <span class="file-size" id="pay-bank-preview-size"></span>
                        </div>
                      </div>
                      <button type="button" class="btn btn-preview" id="btn-preview-bank">
                        <i class="bi bi-eye" aria-hidden="true"></i> View
                      </button>
                    </div>
                    @error('bank_document')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>

            <div class="pr-pay-sec pr-pay-form-section">
              <div class="pr-pay-form-section-title">
                <i class="bi bi-chat-dots" aria-hidden="true"></i> Remarks
              </div>
              <textarea name="remarks" class="form-control rounded-3" rows="3" maxlength="10000" placeholder="Optional context for finance (invoice ref., patient id, project code…)">{{ old('remarks') }}</textarea>
            </div>

            <div class="pay-form-footer pr-pay-form-footer--enhanced">
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <a href="{{ route('superadmin.payment-requests.index') }}" class="btn btn-outline-secondary rounded-3 border-0 bg-light">Cancel</a>
                <button type="submit" class="btn btn-pr-submit px-4 py-2 rounded-3 fw-bold shadow">
                  <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Submit payment request
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <aside class="col-12 col-xl-4 pay-aside p-3 p-lg-4" aria-label="Form hints">
        <div class="pay-hint-card pay-hint-card--accent">
          <h3><i class="bi bi-shield-check" aria-hidden="true"></i> Review</h3>
          <p class="mb-0 small">Choose the correct <strong>payment type</strong> and attach the <strong>PO</strong> or a <strong>supporting document</strong> as required. Approvers will check amount vs PO and attachments.</p>
        </div>
        <div class="pay-hint-card">
          <h3><i class="bi bi-paperclip" aria-hidden="true"></i> Files</h3>
          <p class="mb-0 small">Use the upload area to pick one file, or drop a file. Supported: PDF, images, Word. Max ~10 MB.</p>
        </div>
        <div class="pay-hint-card" style="margin-bottom:0;">
          <h3><i class="bi bi-question-circle" aria-hidden="true"></i> PO number</h3>
          <p class="mb-0 small">For Advance, Part, and Settlement, use the <strong>generated</strong> PO number (<code>purchase_gen_order</code>).</p>
        </div>
      </aside>
    </div>
  </div>
</div>

  </div>
</div>

<div class="modal fade" id="payUploadPreviewModal" tabindex="-1" aria-labelledby="payPreviewModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable pay-upload-preview-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="payPreviewModalTitle">Document preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-body-secondary text-center pay-upload-preview-body position-relative" style="min-height: 200px;">
        <iframe class="d-none w-100 pay-preview-frame" id="payPreviewIframe" title="Document preview"></iframe>
        <img class="d-none img-fluid p-2 pay-preview-img" id="payPreviewImg" alt="" style="max-height: 75vh; object-fit: contain;" />
        <div class="d-none p-4 pay-preview-fallback" id="payPreviewFallback">
          <i class="bi bi-file-earmark-zip display-4 text-secondary d-block mb-2" aria-hidden="true"></i>
          <p class="mb-1 fw-semibold">In-browser preview is not available for this file type</p>
          <p class="text-muted small mb-0" id="payPreviewFallbackName"></p>
        </div>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function() {
  @if ($errors->any())
  if (typeof toastr !== 'undefined') {
    @foreach ($errors->all() as $err)
    toastr.error(@json($err));
    @endforeach
  }
  @endif

  var root = document.getElementById('pay-req-form');
  if (!root) return;
  var branchFetchUrl = @json($branchFetchUrl);
  var lookupUrl = @json($lookupUrl);
  var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  var strip = root.querySelector('#payLocationStrip');
  var typeSel = document.getElementById('pay_type');
  var poBlock = document.getElementById('pay-po-block');
  var docBlock = document.getElementById('pay-doc-block');
  var payPoFile = document.getElementById('pay_po_file');
  var payDocFile = document.getElementById('pay_doc_file');
  var payBankFile = document.getElementById('pay_bank_file');
  var payBankAccount = document.getElementById('pay_bank_account');
  var payBankIfsc = document.getElementById('pay_bank_ifsc');
  var payBankBranch = document.getElementById('pay_bank_branch');
  var PO = ['advance', 'part_payment', 'settlement'];
  var DOC = ['petty_cash_advance', 'reimbursement', 'refund', 'patient_refund', 'instant_payment', 'miscellaneous'];
  var payPreviewBarPo = document.getElementById('pay-po-preview-bar');
  var payPreviewBarDoc = document.getElementById('pay-doc-preview-bar');
  var payPreviewBarBank = document.getElementById('pay-bank-preview-bar');
  var payPreviewModalEl = document.getElementById('payUploadPreviewModal');
  var payPreviewBlob = null;

  function revokePayPreview() {
    if (payPreviewBlob) {
      try { URL.revokeObjectURL(payPreviewBlob); } catch (e) { /* ignore */ }
      payPreviewBlob = null;
    }
  }

  function payPreviewFileKind(file) {
    if (!file) { return 'other'; }
    var t = (file.type || '');
    var n = (file.name || '').toLowerCase();
    if (t === 'application/pdf' || n.endsWith('.pdf')) { return 'pdf'; }
    if (t.indexOf('image/') === 0 || /\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(n)) { return 'image'; }
    return 'other';
  }

  function showPayFilePreview(file) {
    if (!file) {
      if (window.toastr) toastr.error('No file selected.');
      return;
    }
    if (!payPreviewModalEl) {
      if (window.toastr) toastr.error('Preview is unavailable on this page.');
      return;
    }
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
      if (window.toastr) toastr.error('UI library not loaded. Please refresh the page.');
      return;
    }
    revokePayPreview();
    var kind = payPreviewFileKind(file);
    if (kind === 'pdf' || kind === 'image') {
      payPreviewBlob = URL.createObjectURL(file);
    }
    var mt = document.getElementById('payPreviewModalTitle');
    if (mt) { mt.textContent = file.name || 'Document preview'; }
    var iframe = document.getElementById('payPreviewIframe');
    var img = document.getElementById('payPreviewImg');
    var fb = document.getElementById('payPreviewFallback');
    var fbname = document.getElementById('payPreviewFallbackName');
    if (iframe) { iframe.classList.add('d-none'); iframe.removeAttribute('src'); }
    if (img) { img.classList.add('d-none'); img.removeAttribute('src'); }
    if (fb) { fb.classList.add('d-none'); }
    if (kind === 'pdf' && iframe) {
      iframe.classList.remove('d-none');
      iframe.src = payPreviewBlob;
    } else if (kind === 'image' && img) {
      img.classList.remove('d-none');
      img.src = payPreviewBlob;
      img.alt = file.name || '';
    } else if (fb) {
      fb.classList.remove('d-none');
      if (fbname) { fbname.textContent = file.name || ''; }
    }
    try {
      bootstrap.Modal.getOrCreateInstance(payPreviewModalEl).show();
    } catch (err) {
      revokePayPreview();
      if (window.toastr) toastr.error('Could not open the preview window.');
    }
  }

  if (payPreviewModalEl) {
    payPreviewModalEl.addEventListener('hidden.bs.modal', function() {
      var iframe = document.getElementById('payPreviewIframe');
      var img = document.getElementById('payPreviewImg');
      if (iframe) { iframe.classList.add('d-none'); iframe.removeAttribute('src'); }
      if (img) { img.classList.add('d-none'); img.removeAttribute('src'); }
      var fb = document.getElementById('payPreviewFallback');
      if (fb) { fb.classList.add('d-none'); }
      revokePayPreview();
    });
  }

  var lastPayTypeForUi = null;
  function syncTypeUi() {
    var t = (typeSel && typeSel.value) || '';
    var p = PO.indexOf(t) !== -1;
    var d = DOC.indexOf(t) !== -1;
    var typeChanged = lastPayTypeForUi !== null && lastPayTypeForUi !== t;
    if (poBlock) { poBlock.classList.toggle('d-none', !p); }
    if (docBlock) { docBlock.classList.toggle('d-none', !d); }
    if (payPoFile) {
      payPoFile.required = p;
      if (!p && typeChanged) { payPoFile.value = ''; payPoFile.dispatchEvent(new Event('change', { bubbles: true })); }
    }
    if (payDocFile) {
      payDocFile.required = d;
      if (!d && typeChanged) { payDocFile.value = ''; payDocFile.dispatchEvent(new Event('change', { bubbles: true })); }
    }
    if (payBankFile) {
      payBankFile.required = d;
      if (!d && typeChanged) { payBankFile.value = ''; payBankFile.dispatchEvent(new Event('change', { bubbles: true })); }
    }
    if (payBankAccount) {
      payBankAccount.required = d;
      if (!d && typeChanged) { payBankAccount.value = ''; }
    }
    if (payBankIfsc) {
      payBankIfsc.required = d;
      if (!d && typeChanged) { payBankIfsc.value = ''; }
    }
    if (payBankBranch) {
      payBankBranch.required = d;
      if (!d && typeChanged) { payBankBranch.value = ''; }
    }
    if (payPreviewBarPo && !p && typeChanged) { payPreviewBarPo.setAttribute('hidden', ''); }
    if (payPreviewBarDoc && !d && typeChanged) { payPreviewBarDoc.setAttribute('hidden', ''); }
    if (payPreviewBarBank && !d && typeChanged) { payPreviewBarBank.setAttribute('hidden', ''); }
    if (!p && typeChanged) {
      var poid = document.getElementById('pay_po_id');
      if (poid) poid.value = '';
    }
    lastPayTypeForUi = t;
  }
  if (typeSel) {
    typeSel.addEventListener('change', syncTypeUi);
    syncTypeUi();
  }

  var PAY_PR_MAX_FILE_BYTES = 10 * 1024 * 1024;
  function payClearFieldMessages(formRoot) {
    formRoot.querySelectorAll('.pay-pr-field-msg').forEach(function(n) { n.remove(); });
    formRoot.querySelectorAll('.pay-pr-field-invalid').forEach(function(el) {
      el.classList.remove('pay-pr-field-invalid');
    });
    formRoot.querySelectorAll('.pay-pr-wrap-invalid').forEach(function(el) {
      el.classList.remove('pay-pr-wrap-invalid');
    });
    formRoot.querySelectorAll('.pay-pr-upload-err').forEach(function(el) {
      el.classList.remove('pay-pr-upload-err');
    });
  }
  function payAppendFieldMsg(host, msg) {
    if (!host) return;
    var div = document.createElement('div');
    div.className = 'text-danger small mt-1 pay-pr-field-msg';
    div.setAttribute('role', 'alert');
    div.textContent = msg;
    host.appendChild(div);
  }
  function payFieldErrorHost(anchorEl) {
    if (!anchorEl) return null;
    var wrap = anchorEl.closest('.tax-dropdown-wrapper.pr-dd-wrap');
    if (wrap && wrap.parentElement) {
      return wrap.parentElement;
    }
    return anchorEl.closest('.col-lg-3') || anchorEl.closest('.col-md-6') || anchorEl.closest('.col-md-5')
      || anchorEl.closest('.mb-3') || anchorEl.closest('.col-12') || anchorEl.parentElement;
  }
  function payShowErrorForAnchor(anchorEl, msg) {
    if (!anchorEl) {
      if (window.toastr) toastr.error(msg);
      return;
    }
    anchorEl.classList.add('pay-pr-field-invalid');
    var wrap = anchorEl.closest('.tax-dropdown-wrapper.pr-dd-wrap');
    if (wrap) {
      wrap.classList.add('pay-pr-wrap-invalid');
    }
    var host = payFieldErrorHost(anchorEl);
    payAppendFieldMsg(host, msg);
  }
  function payShowErrorForUploadBox(boxEl, msg) {
    if (!boxEl) return;
    boxEl.classList.add('pay-pr-upload-err');
    var host = boxEl.closest('.pr-pay-attachment-zone') || boxEl.closest('.mb-3') || boxEl.parentElement;
    payAppendFieldMsg(host, msg);
  }
  function payFirstErrorHost(problems) {
    if (!problems || !problems.length) return null;
    var p0 = problems[0];
    var el = p0.u || p0.a;
    return el;
  }
  function payFlushProblems(problems) {
    problems.forEach(function(p) {
      if (p.u) {
        payShowErrorForUploadBox(p.u, p.msg);
      } else {
        payShowErrorForAnchor(p.a, p.msg);
      }
    });
    var scrollEl = payFirstErrorHost(problems);
    if (scrollEl && scrollEl.scrollIntoView) {
      scrollEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    var focusEl = problems[0] && problems[0].a;
    if (focusEl && typeof focusEl.focus === 'function') {
      try { focusEl.focus(); } catch (e) { /* ignore */ }
    }
  }
  function payRunClientValidation() {
    payClearFieldMessages(root);
    var problems = [];
    var companyHid = strip ? strip.querySelector('.company_id') : null;
    var zoneHid = strip ? strip.querySelector('.zone_id') : null;
    var branchHid = strip ? strip.querySelector('.branch_id') : null;
    var cid = companyHid ? String(companyHid.value || '').trim() : '';
    var zid = zoneHid ? String(zoneHid.value || '').trim() : '';
    var bid = branchHid ? String(branchHid.value || '').trim() : '';
    if (!cid) {
      problems.push({ a: document.getElementById('pay_dd_company'), msg: 'Please select a company.', u: null });
    }
    if (!zid) {
      problems.push({ a: document.getElementById('pay_dd_zone'), msg: 'Please select a zone.', u: null });
    }
    if (!bid) {
      problems.push({ a: document.getElementById('pay_dd_branch'), msg: 'Please select a branch.', u: null });
    }
    var vendorNameEl = document.getElementById('pay_vendor_name');
    var vendorHid = document.getElementById('pay_vendor_id');
    var vendorIdRaw = vendorHid ? String(vendorHid.value || '').trim() : '';
    var vendorIdNum = parseInt(vendorIdRaw, 10);
    var vendorDisp = vendorNameEl ? String(vendorNameEl.value || '').trim() : '';
    if (!vendorIdRaw || !Number.isFinite(vendorIdNum) || vendorIdNum < 1 || !vendorDisp) {
      problems.push({ a: vendorNameEl, msg: 'Please select a vendor from the list.', u: null });
    }
    var t = (typeSel && typeSel.value) || '';
    var typeSearch = document.getElementById('pay_type_search');
    if (!t) {
      problems.push({ a: typeSearch, msg: 'Please select a payment type.', u: null });
    } else if (PO.indexOf(t) === -1 && DOC.indexOf(t) === -1) {
      problems.push({ a: typeSearch, msg: 'Please select a valid payment type.', u: null });
    }
    var amtEl = document.getElementById('pay_amount');
    var amtRaw = amtEl ? String(amtEl.value || '').trim() : '';
    var amt = amtRaw ? parseFloat(amtRaw, 10) : NaN;
    if (!amtEl || !amtRaw || isNaN(amt) || amt < 0.01) {
      problems.push({ a: amtEl, msg: 'Please enter a request amount of at least ₹0.01.', u: null });
    }
    var p = PO.indexOf(t) !== -1;
    var d = DOC.indexOf(t) !== -1;
    if (p) {
      var poIdEl = document.getElementById('pay_po_id');
      var poNum = poIdEl ? String(poIdEl.value || '').trim() : '';
      if (!poNum) {
        problems.push({ a: poIdEl, msg: 'Please enter the PO number (purchase_gen_order).', u: null });
      }
      if (!payPoFile || !payPoFile.files || !payPoFile.files.length) {
        problems.push({ a: null, msg: 'Please attach the PO document (PDF, image, or Word).', u: document.getElementById('pay-po-upload-box') });
      } else if (payPoFile.files[0].size > PAY_PR_MAX_FILE_BYTES) {
        problems.push({ a: null, msg: 'PO attachment must be 10 MB or smaller.', u: document.getElementById('pay-po-upload-box') });
      }
    } else if (d) {
      if (!payDocFile || !payDocFile.files || !payDocFile.files.length) {
        problems.push({ a: null, msg: 'Please attach a supporting document.', u: document.getElementById('pay-doc-upload-box') });
      } else if (payDocFile.files[0].size > PAY_PR_MAX_FILE_BYTES) {
        problems.push({ a: null, msg: 'Supporting document must be 10 MB or smaller.', u: document.getElementById('pay-doc-upload-box') });
      }
      var acct = payBankAccount ? String(payBankAccount.value || '').trim() : '';
      var ifsc = payBankIfsc ? String(payBankIfsc.value || '').trim().toUpperCase() : '';
      var br = payBankBranch ? String(payBankBranch.value || '').trim() : '';
      if (!acct) {
        problems.push({ a: payBankAccount, msg: 'Please enter the payee bank account number.', u: null });
      }
      if (!ifsc || !/^[A-Z]{4}0[A-Z0-9]{6}$/.test(ifsc)) {
        problems.push({ a: payBankIfsc, msg: 'Please enter a valid 11-character IFSC (e.g. HDFC0001234).', u: null });
      }
      if (!br) {
        problems.push({ a: payBankBranch, msg: 'Please enter bank branch details.', u: null });
      }
      if (!payBankFile || !payBankFile.files || !payBankFile.files.length) {
        problems.push({ a: null, msg: 'Please attach the bank document (cheque / statement / passbook).', u: document.getElementById('pay-bank-upload-box') });
      } else if (payBankFile.files[0].size > PAY_PR_MAX_FILE_BYTES) {
        problems.push({ a: null, msg: 'Bank document must be 10 MB or smaller.', u: document.getElementById('pay-bank-upload-box') });
      }
    }
    if (problems.length) {
      payFlushProblems(problems);
      return false;
    }
    return true;
  }
  function payAnchorForServerField(key) {
    var map = {
      company_id: 'pay_dd_company',
      zone_id: 'pay_dd_zone',
      branch_id: 'pay_dd_branch',
      vendor_id: 'pay_vendor_name',
      payment_type: 'pay_type_search',
      amount: 'pay_amount',
      purchase_gen_order: 'pay_po_id',
      bank_account_number: 'pay_bank_account',
      bank_ifsc_code: 'pay_bank_ifsc',
      bank_branch_details: 'pay_bank_branch'
    };
    var id = map[key];
    return id ? document.getElementById(id) : null;
  }
  function payBoxForServerFileField(key) {
    if (key === 'po_attachment') return document.getElementById('pay-po-upload-box');
    if (key === 'document_attachment') return document.getElementById('pay-doc-upload-box');
    if (key === 'bank_document') return document.getElementById('pay-bank-upload-box');
    return null;
  }
  function payApplyServerValidationErrors(errors) {
    var orphans = [];
    var mapped = false;
    var firstScroll = null;
    Object.keys(errors || {}).forEach(function(key) {
      var raw = errors[key];
      var msg = Array.isArray(raw) ? String(raw[0] || '') : String(raw || '');
      if (!msg) return;
      var box = payBoxForServerFileField(key);
      if (box) {
        mapped = true;
        payShowErrorForUploadBox(box, msg);
        if (!firstScroll) firstScroll = box;
        return;
      }
      var anchor = payAnchorForServerField(key);
      if (anchor) {
        mapped = true;
        payShowErrorForAnchor(anchor, msg);
        if (!firstScroll) firstScroll = anchor;
        return;
      }
      orphans.push(msg);
    });
    if (firstScroll && firstScroll.scrollIntoView) {
      firstScroll.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return { orphans: orphans, mapped: mapped };
  }

  var payReqSubmitting = false;
  root.addEventListener('submit', function(ev) {
    ev.preventDefault();
    if (payReqSubmitting) {
      return;
    }
    if (!payRunClientValidation()) {
      if (window.toastr) toastr.warning('Please fix the highlighted fields, then submit again.');
      return;
    }
    payReqSubmitting = true;
    var submitBtn = root.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
    }
    var action = root.getAttribute('action') || window.location.href;
    window.setTimeout(function() {
      var fd = new FormData(root);
      fetch(action, {
        method: 'POST',
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        credentials: 'same-origin',
        redirect: 'manual'
      }).then(function(r) {
        if (r.status === 302 || r.status === 301 || r.status === 303) {
          var loc = r.headers.get('Location');
          if (loc) {
            window.location.href = loc;
          } else {
            window.location.reload();
          }
          return null;
        }
        if (r.status === 419) {
          if (window.toastr) toastr.error('Your session has expired. Refresh the page and try again.');
          return null;
        }
        var ct = (r.headers.get('Content-Type') || '').toLowerCase();
        if (ct.indexOf('application/json') !== -1) {
          return r.json().then(function(body) {
            return { r: r, body: body };
          });
        }
        return r.text().then(function(text) {
          return { r: r, body: text };
        });
      }).then(function(x) {
        if (!x) {
          return;
        }
        if (x.r.ok && x.r.status === 200) {
          if (window.toastr) toastr.success('Saved.');
          window.location.reload();
          return;
        }
        if (x.r.status === 422 && x.body && typeof x.body === 'object') {
          payClearFieldMessages(root);
          var srvErrs = x.body.errors || {};
          var keys = Object.keys(srvErrs);
          if (keys.length) {
            var applied = payApplyServerValidationErrors(srvErrs);
            (applied.orphans || []).forEach(function(m) {
              if (window.toastr) toastr.error(m);
            });
            if (applied.mapped && window.toastr) {
              toastr.error('Please correct the errors highlighted on the form.');
            }
          } else if (x.body.message && window.toastr) {
            toastr.error(String(x.body.message));
          } else if (window.toastr) {
            toastr.error('Validation failed. Please check your input.');
          }
          return;
        }
        if (window.toastr) {
          toastr.error('Could not submit the form. Please try again or refresh the page.');
        }
      }).catch(function() {
        if (window.toastr) toastr.error('Network error. Check your connection and try again.');
      }).finally(function() {
        payReqSubmitting = false;
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      });
    }, 0);
  });

  function formatBytes(bytes, decimals = 2) {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
  }

  function setupDragAndDrop(input, box, bar, nameEl, sizeEl) {
    if (!box || !input) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
      box.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(function(eventName) {
      box.addEventListener(eventName, function() { box.classList.add('dragover'); }, false);
    });

    ['dragleave', 'drop'].forEach(function(eventName) {
      box.addEventListener(eventName, function() { box.classList.remove('dragover'); }, false);
    });

    box.addEventListener('drop', function(e) {
      var dt = e.dataTransfer;
      if (!dt || !dt.files || !dt.files.length) return;
      var f = dt.files[0];
      try {
        var d = new DataTransfer();
        d.items.add(f);
        input.files = d.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      } catch (err) {
        if (window.toastr) toastr.error('Could not attach dropped file. Use Browse / click the area to pick a file.');
      }
    }, false);

    input.addEventListener('change', function() {
      var f = this.files && this.files[0];
      if (f && bar) {
        bar.removeAttribute('hidden');
        if (nameEl) { nameEl.textContent = f.name; nameEl.setAttribute('title', f.name); }
        if (sizeEl) { sizeEl.textContent = formatBytes(f.size); }
      } else if (bar) {
        bar.setAttribute('hidden', '');
      }
    });
  }

  setupDragAndDrop(payPoFile, document.getElementById('pay-po-upload-box'), payPreviewBarPo, document.getElementById('pay-po-preview-name'), document.getElementById('pay-po-preview-size'));
  setupDragAndDrop(payDocFile, document.getElementById('pay-doc-upload-box'), payPreviewBarDoc, document.getElementById('pay-doc-preview-name'), document.getElementById('pay-doc-preview-size'));
  setupDragAndDrop(payBankFile, document.getElementById('pay-bank-upload-box'), payPreviewBarBank, document.getElementById('pay-bank-preview-name'), document.getElementById('pay-bank-preview-size'));
  var btnPreviewPo = document.getElementById('btn-preview-po');
  if (btnPreviewPo && payPoFile) {
    btnPreviewPo.addEventListener('click', function() {
      showPayFilePreview(payPoFile.files && payPoFile.files[0]);
    });
  }
  var btnPreviewDoc = document.getElementById('btn-preview-doc');
  if (btnPreviewDoc && payDocFile) {
    btnPreviewDoc.addEventListener('click', function() {
      showPayFilePreview(payDocFile.files && payDocFile.files[0]);
    });
  }
  var btnPreviewBank = document.getElementById('btn-preview-bank');
  if (btnPreviewBank && payBankFile) {
    btnPreviewBank.addEventListener('click', function() {
      showPayFilePreview(payBankFile.files && payBankFile.files[0]);
    });
  }
  if (payBankIfsc) {
    payBankIfsc.addEventListener('blur', function() {
      this.value = String(this.value || '').trim().toUpperCase();
    });
  }

  var vendorName = document.getElementById('pay_vendor_name');
  var vendorHid = document.getElementById('pay_vendor_id');

  function closeAllPanels() {
    root.querySelectorAll('.pr-dd-panel').forEach(function(p) { p.classList.remove('show'); });
  }
  function filterList(panel, q) {
    q = (q || '').toLowerCase();
    var list = panel.querySelector('.company-list') || panel.querySelector('.zone-list') || panel.querySelector('.branch-list') || panel.querySelector('.vendor-list') || panel.querySelector('.type-list');
    if (!list) return;
    list.querySelectorAll('div').forEach(function(el) {
      el.style.display = !q || (el.textContent || '').toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
  }
  var payBranchLoadToken = 0;
  function loadBranchesForZone(zoneId, done) {
    var branchPanel = strip ? strip.querySelector('.branch-list') : null;
    if (!branchPanel) { if (done) done(); return; }
    zoneId = String(zoneId || '').trim();
    branchPanel.innerHTML = '';
    if (!zoneId) { if (done) done(); return; }
    var loadToken = ++payBranchLoadToken;
    var fd = new FormData();
    fd.append('id', zoneId);
    fd.append('_token', csrf);
    fetch(branchFetchUrl, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      credentials: 'same-origin'
    }).then(function(r) { return r.json(); }).then(function(data) {
      if (loadToken !== payBranchLoadToken) {
        return;
      }
      (data.branch || []).forEach(function(branch) {
        var zid = parseInt(zoneId, 10);
        if (branch.zone_id != null && parseInt(branch.zone_id, 10) !== zid) {
          return;
        }
        var div = document.createElement('div');
        div.setAttribute('data-id', branch.id);
        div.setAttribute('data-value', branch.name || '');
        div.textContent = branch.name || '';
        branchPanel.appendChild(div);
      });
      if (done) done();
    }).catch(function() {
      if (loadToken !== payBranchLoadToken) {
        return;
      }
      if (window.toastr) toastr.error('Could not load branches for this zone.');
      if (done) done();
    });
  }
  root.addEventListener('click', function(e) { if (!e.target.closest('.pr-dd-wrap')) closeAllPanels(); });
  root.querySelectorAll('.pr-dd-input').forEach(function(inp) {
    inp.addEventListener('click', function(ev) {
      ev.stopPropagation();
      closeAllPanels();
      var panel = this.closest('.pr-dd-wrap').querySelector('.pr-dd-panel');
      if (panel) {
        panel.classList.add('show');
        var inner = panel.querySelector('.inner-search');
        if (inner) { inner.value = ''; filterList(panel, ''); inner.focus(); }
      }
    });
  });

  root.querySelectorAll('.pr-dd-panel .inner-search').forEach(function(inner) {
    inner.addEventListener('input', function() { filterList(this.closest('.pr-dd-panel'), this.value); });
    inner.addEventListener('click', function(ev) { ev.stopPropagation(); });
  });
  root.querySelectorAll('.company-list div').forEach(function(div) {
    div.addEventListener('click', function(ev) {
      ev.stopPropagation();
      var wrap = this.closest('.pr-dd-wrap');
      wrap.querySelector('.company-search-input').value = this.getAttribute('data-value') || this.textContent.trim();
      wrap.querySelector('.company_id').value = this.getAttribute('data-id') || '';
      this.closest('.pr-dd-panel').classList.remove('show');
      if (strip) {
        var zInp = strip.querySelector('.zone-search-input');
        var zHid = strip.querySelector('.zone_id');
        var bInp = strip.querySelector('.branch-search-input');
        var bHid = strip.querySelector('.branch_id');
        var bp = strip.querySelector('.branch-list');
        if (zInp) { zInp.value = ''; }
        if (zHid) { zHid.value = ''; }
        if (bInp) { bInp.value = ''; }
        if (bHid) { bHid.value = ''; }
        if (bp) { bp.innerHTML = ''; }
        payBranchLoadToken++;
      }
    });
  });
  root.querySelectorAll('.zone-list div').forEach(function(div) {
    div.addEventListener('click', function(ev) {
      ev.stopPropagation();
      var wrap = this.closest('.pr-dd-wrap');
      var zidRaw = this.getAttribute('data-id') || '';
      var zi = parseInt(String(zidRaw).trim(), 10);
      wrap.querySelector('.zone-search-input').value = this.getAttribute('data-value') || this.textContent.trim();
      wrap.querySelector('.zone_id').value = !isNaN(zi) ? String(zi) : '';
      this.closest('.pr-dd-panel').classList.remove('show');
      if (strip) {
        var bInp = strip.querySelector('.branch-search-input');
        var bHid = strip.querySelector('.branch_id');
        if (bInp) bInp.value = '';
        if (bHid) bHid.value = '';
        loadBranchesForZone(wrap.querySelector('.zone_id').value);
      }
    });
  });

  root.querySelectorAll('.vendor-list div').forEach(function(div) {
    div.addEventListener('click', function(ev) {
      ev.stopPropagation();
      var wrap = this.closest('.pr-dd-wrap');
      var val = this.getAttribute('data-value') || this.textContent.trim();
      wrap.querySelector('.vendor-search-input').value = val;
      wrap.querySelector('.vendor_id').value = this.getAttribute('data-id') || '';
      if (vendorName) { vendorName.value = val; }
      this.closest('.pr-dd-panel').classList.remove('show');
    });
  });

  root.querySelectorAll('.type-list div').forEach(function(div) {
    div.addEventListener('click', function(ev) {
      ev.stopPropagation();
      var wrap = this.closest('.pr-dd-wrap');
      wrap.querySelector('.type-search-input').value = this.getAttribute('data-value') || this.textContent.trim();
      var hid = wrap.querySelector('.type_id');
      if (hid) {
        hid.value = this.getAttribute('data-id') || '';
        hid.dispatchEvent(new Event('change'));
      }
      this.closest('.pr-dd-panel').classList.remove('show');
    });
  });

  if (strip) {
    strip.addEventListener('click', function(ev) {
      var item = ev.target.closest('.branch-list div[data-id]');
      if (!item) return;
      ev.stopPropagation();
      var wrap = item.closest('.pr-dd-wrap');
      wrap.querySelector('.branch-search-input').value = item.getAttribute('data-value') || item.textContent.trim();
      wrap.querySelector('.branch_id').value = item.getAttribute('data-id') || '';
      var panel = wrap.querySelector('.pr-dd-panel');
      if (panel) panel.classList.remove('show');
    });
  }
  var zEl = strip ? strip.querySelector('.zone_id') : null;
  var bHidInit = strip ? strip.querySelector('.branch_id') : null;
  var preBranchId = bHidInit ? String(bHidInit.value || '').trim() : '';
  if (zEl && zEl.value) {
    loadBranchesForZone(zEl.value, function() {
      if (!preBranchId || !strip) return;
      var bp = strip.querySelector('.branch-list');
      var bInp = strip.querySelector('.branch-search-input');
      if (!bp || !bInp) return;
      var found = null;
      bp.querySelectorAll('div[data-id]').forEach(function(el) {
        if (String(el.getAttribute('data-id')) === preBranchId) found = el;
      });
      if (found) { bInp.value = found.getAttribute('data-value') || found.textContent.trim(); }
    });
  }

  var pl = document.getElementById('pay-po-balance-panel');
  var lBtn = document.getElementById('pay_po_load');
  var poid = document.getElementById('pay_po_id');
  if (lBtn && poid) {
    lBtn.addEventListener('click', function() {
      var v = poid.value.trim();
      if (!v) { if (window.toastr) toastr.error('Enter the PO number (purchase_gen_order) first.'); return; }
      var h = document.getElementById('pay-po-lookup-hint');
      if (h) h.textContent = 'Loading…';
      var uu = new URL(lookupUrl, window.location.origin);
      uu.searchParams.set('purchase_gen_order', v);
      fetch(uu.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(function(r) { return r.json().then(function(d) { return { r: r, d: d }; }); })
        .then(function(x) {
          if (!x.r.ok || !x.d.ok) {
            var msg = (x.d && x.d.message) || (x.d && x.d.errors && x.d.errors.purchase_gen_order && x.d.errors.purchase_gen_order[0]) || 'Lookup failed';
            throw new Error(msg);
          }
          var d = x.d;
          if (d.vendor_id && vendorHid && !String(vendorHid.value || '').trim()) {
            vendorHid.value = d.vendor_id;
            if (vendorName) vendorName.value = d.vendor_name || 'Vendor #' + d.vendor_id;
          } else if (d.vendor_name && vendorName && !String(vendorName.value || '').trim()) {
            vendorName.value = d.vendor_name;
          }
          if (pl) {
            pl.classList.remove('d-none');
            var ref = d.purchase_gen_order || d.order_number || v;
            document.getElementById('pay_po_ref').textContent = String(ref);
            document.getElementById('pay_po_total').textContent = Number(d.po_total).toFixed(2);
            document.getElementById('pay_po_paid').textContent = Number(d.amount_paid_before).toFixed(2);
            document.getElementById('pay_po_rem').textContent = Number(d.remaining_before_new).toFixed(2);
          }
        })
        .catch(function(err) {
          if (h) h.textContent = (err && err.message) ? String(err.message) : 'Could not load PO. Check the number.';
          if (window.toastr) toastr.error((err && err.message) ? String(err.message) : 'PO lookup failed.');
        });
    });
  }
})();
</script>

</body>
</html>
