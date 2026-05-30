@extends('superadmin.layouts.app')

@section('body_class', 'security-agreement-page pay-pr-show-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/security_agreement.css') }}" />
@endpush

@section('content')
@php
  $r = $record;
  $agreementPeriodDisplay = trim((string) $r->agreement_period);
  if ($agreementPeriodDisplay !== '' && stripos($agreementPeriodDisplay, ' to ') === false) {
      try {
          $agreementPeriodDisplay = \Carbon\Carbon::parse($agreementPeriodDisplay)->format('d M Y');
      } catch (\Throwable $e) {
          // Keep legacy text if it was stored in a non-date format.
      }
  }

  $saAttachSlotIcons = [
      'security_agreement' => 'bi-file-earmark-text',
      'esi_certificate' => 'bi-shield-check',
      'pf_certificate' => 'bi-bank',
  ];

  $saAttachTypeClass = static function (string $kind): string {
      return match ($kind) {
          'pdf' => 'pr-gmail-type-pdf',
          'doc' => 'pr-gmail-type-doc',
          'image' => 'pr-gmail-type-img',
          default => 'pr-gmail-type-file',
      };
  };

  $saAttachTotal = 0;
  foreach (\App\Models\SecurityAgreement::FILE_SLOTS as $slot => $fileMeta) {
      $saAttachTotal += count($r->documentsForSlot($slot));
  }

  $gstApplicable = \App\Models\SecurityAgreement::isGstApplicableType($r->gst_type);
  $gstServiceRows = $gstApplicable ? $r->gstServiceBreakdownRows() : [];
  $gstLines = $gstApplicable ? $r->gstSplitLines() : [];
  $tdsServiceRows = $r->tdsServiceBreakdownRows();

  $fmtMoney = static fn (?float $amount): string => $amount !== null ? '₹'.number_format($amount, 2) : '—';
@endphp
    <div class="security-agreement show">
      <div class="card">
        <header class="hero">
          <div>
            <div class="hero-kicker">
              <i class="bi bi-file-earmark-text"></i>
              {{ $moduleTitle }} · {{ $r->agreement_number }}
            </div>
            <h1 class="hero-title">
              <i class="bi bi-house-check"></i>
              {{ $r->vendorDisplayName() }}
            </h1>
          </div>
          <div class="hero-actions">
            <a href="{{ route($routeNames['index'], array_filter(['category' => request()->query('category')])) }}" class="btn-ghost">
              <i class="bi bi-arrow-left"></i>
              Back to list
            </a>
            <a href="{{ route($routeNames['edit'], ['securityAgreement' => $r]) }}" class="btn-primary">
              <i class="bi bi-pencil-square"></i>
              Edit agreement
            </a>
          </div>
        </header>

        <div class="page-body sa-show-body">
          <div class="stats sa-stats">
            <div class="stat sa-stat sa-stat--date">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-calendar-event"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Agreement date</span>
                <span class="stat-value sa-stat-value">{{ $r->agreement_date?->format('d M Y') ?: '—' }}</span>
                <span class="stat-hint sa-stat-hint">Start reference date</span>
              </div>
            </div>
            <div class="stat sa-stat sa-stat--security">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Security salary</span>
                <span class="stat-value sa-stat-value">{{ $fmtMoney($r->security_fixed_salary_amount !== null ? (float) $r->security_fixed_salary_amount : null) }}</span>
                <span class="stat-hint sa-stat-hint">Monthly fixed salary</span>
              </div>
            </div>
            <div class="stat sa-stat sa-stat--housekeeping">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-stars"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Housekeeping salary</span>
                <span class="stat-value sa-stat-value">{{ $fmtMoney($r->housekeeping_fixed_salary_amount !== null ? (float) $r->housekeeping_fixed_salary_amount : null) }}</span>
                <span class="stat-hint sa-stat-hint">Monthly fixed salary</span>
              </div>
            </div>
            <div class="stat sa-stat sa-stat--expiry">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-calendar2-x"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Ends on</span>
                <span class="stat-value sa-stat-value">{{ $r->end_of_agreement_date?->format('d M Y') ?: '—' }}</span>
                <span class="stat-hint sa-stat-hint">Agreement expiry</span>
              </div>
            </div>
          </div>

          <div class="sa-show-sections">
            <section class="pr-show-section" aria-labelledby="sa-org-heading">
              <h2 id="sa-org-heading" class="pr-show-section-head">
                <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-building"></i></span>
                <span class="pr-show-section-head-text">Organization &amp; agreement</span>
              </h2>
              <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="pr-show-kv h-100 mb-0">
                    <div class="pr-show-kv-lbl"><span class="pr-show-kv-ic" aria-hidden="true"><i class="bi bi-hash"></i></span> Agreement ID</div>
                    <div class="pr-show-kv-val">{{ $r->agreement_number }}</div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="pr-show-kv h-100 mb-0">
                    <div class="pr-show-kv-lbl"><span class="pr-show-kv-ic" aria-hidden="true"><i class="bi bi-building"></i></span> Company</div>
                    <div class="pr-show-kv-val">{{ $r->company?->company_name ?: '—' }}</div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="pr-show-kv h-100 mb-0">
                    <div class="pr-show-kv-lbl"><span class="pr-show-kv-ic" aria-hidden="true"><i class="bi bi-map"></i></span> Zone</div>
                    <div class="pr-show-kv-val">{{ $r->zone?->name ?: '—' }}</div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="pr-show-kv h-100 mb-0">
                    <div class="pr-show-kv-lbl"><span class="pr-show-kv-ic" aria-hidden="true"><i class="bi bi-diagram-2"></i></span> Branch</div>
                    <div class="pr-show-kv-val">{{ $r->branch?->name ?: '—' }}</div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="pr-show-kv h-100 mb-0">
                    <div class="pr-show-kv-lbl"><span class="pr-show-kv-ic" aria-hidden="true"><i class="bi bi-hourglass-split"></i></span> Agreement period</div>
                    <div class="pr-show-kv-val">{{ $agreementPeriodDisplay ?: '—' }}</div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="pr-show-kv h-100 mb-0">
                    <div class="pr-show-kv-lbl"><span class="pr-show-kv-ic" aria-hidden="true"><i class="bi bi-signpost-split"></i></span> Termination period</div>
                    <div class="pr-show-kv-val">{{ $r->termination_period ?: '—' }}</div>
                  </div>
                </div>
              </div>
            </section>

            <section class="pr-show-section sa-show-vendor-section" aria-labelledby="sa-vendor-heading">
              <h2 id="sa-vendor-heading" class="pr-show-section-head">
                <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-person-lines-fill"></i></span>
                <span class="pr-show-section-head-text">Contact &amp; record</span>
              </h2>

              <article class="sa-show-profile-card">
                <div class="sa-show-profile-body sa-show-profile-body--top">
                  <div class="sa-show-profile-cols">
                    <dl class="sa-show-profile-list">
                      <div class="sa-show-profile-item">
                        <dt><i class="bi bi-person-vcard" aria-hidden="true"></i> PAN number</dt>
                        <dd>{{ $r->pan_number ?: '—' }}</dd>
                      </div>
                      <div class="sa-show-profile-item">
                        <dt><i class="bi bi-person-circle" aria-hidden="true"></i> Contact person</dt>
                        <dd>{{ $r->contact_person_name ?: '—' }}</dd>
                      </div>
                      <div class="sa-show-profile-item">
                        <dt><i class="bi bi-telephone" aria-hidden="true"></i> Contact number</dt>
                        <dd>{{ $r->contact_person_number ?: '—' }}</dd>
                      </div>
                    </dl>
                  </div>

                  <div class="sa-show-profile-address">
                    <p class="sa-show-profile-address-label"><i class="bi bi-geo-alt" aria-hidden="true"></i> Address</p>
                    <p class="sa-show-profile-address-text">{{ $r->address ?: '—' }}</p>
                  </div>
                </div>

                <footer class="sa-show-profile-footer">
                  <span class="sa-show-profile-foot-item">
                    <i class="bi bi-person-workspace" aria-hidden="true"></i>
                    <span class="sa-show-profile-foot-label">Created by</span>
                    <strong>{{ $r->creator?->user_fullname ?: '—' }}</strong>
                  </span>
                  <span class="sa-show-profile-foot-sep" aria-hidden="true"></span>
                  <span class="sa-show-profile-foot-item">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    <span class="sa-show-profile-foot-label">Created on</span>
                    <strong>{{ $r->created_at?->format('d M Y, h:i A') ?: '—' }}</strong>
                  </span>
                </footer>
              </article>
            </section>

            <section class="pr-show-section" aria-labelledby="sa-salary-heading">
              <h2 id="sa-salary-heading" class="pr-show-section-head">
                <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-cash-stack"></i></span>
                <span class="pr-show-section-head-text">Salaries &amp; paid leave</span>
              </h2>
              <div class="sa-show-service-grid">
                <article class="sa-show-service-card sa-show-service-card--security">
                  <header class="sa-show-service-card-head">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    <span>Security</span>
                  </header>
                  <dl class="sa-show-service-dl">
                    <div class="sa-show-service-row">
                      <dt>Fixed monthly salary</dt>
                      <dd>{{ $fmtMoney($r->security_fixed_salary_amount !== null ? (float) $r->security_fixed_salary_amount : null) }}</dd>
                    </div>
                  </dl>
                </article>
                <article class="sa-show-service-card sa-show-service-card--housekeeping">
                  <header class="sa-show-service-card-head">
                    <i class="bi bi-stars" aria-hidden="true"></i>
                    <span>Housekeeping</span>
                  </header>
                  <dl class="sa-show-service-dl">
                    <div class="sa-show-service-row">
                      <dt>Fixed monthly salary</dt>
                      <dd>{{ $fmtMoney($r->housekeeping_fixed_salary_amount !== null ? (float) $r->housekeeping_fixed_salary_amount : null) }}</dd>
                    </div>
                    <div class="sa-show-service-row">
                      <dt>Paid leave</dt>
                      <dd>
                        @if ($r->housekeeping_paid_leave_applicable && $r->housekeeping_paid_leave_days)
                          <span class="sa-show-badge sa-show-badge--yes">Applicable</span>
                          {{ $r->housekeeping_paid_leave_days }} day(s)
                        @else
                          <span class="sa-show-badge sa-show-badge--no">Not applicable</span>
                        @endif
                      </dd>
                    </div>
                  </dl>
                </article>
              </div>
            </section>

            <section class="pr-show-section" aria-labelledby="sa-tax-heading">
              <h2 id="sa-tax-heading" class="pr-show-section-head">
                <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></span>
                <span class="pr-show-section-head-text">Tax &amp; compliance</span>
              </h2>

              <div class="sa-show-tax-summary">
                <div class="sa-show-tax-chip">
                  <span class="sa-show-tax-chip-label">GST type</span>
                  <span class="{{ \App\Models\SecurityAgreement::gstPillClass($r->gst_type) }}">{{ \App\Models\SecurityAgreement::gstLabel($r->gst_type) }}</span>
                </div>
                @if ($gstApplicable)
                  <div class="sa-show-tax-chip">
                    <span class="sa-show-tax-chip-label">GST tax</span>
                    <span class="sa-show-tax-chip-value">{{ $r->gstRateAmountSummary() }}</span>
                  </div>
                @endif
                <div class="sa-show-tax-chip">
                  <span class="sa-show-tax-chip-label">TDS</span>
                  <span class="sa-show-tax-chip-value">{{ $r->tdsSummary() }}</span>
                </div>
                <div class="sa-show-tax-chip">
                  <span class="sa-show-tax-chip-label">RCM</span>
                  <span class="sa-show-tax-chip-value">{{ $r->rcmSummary() }}</span>
                </div>
              </div>

              @if ($gstServiceRows !== [] || $gstLines !== [] || $tdsServiceRows !== [])
                <div class="sa-show-breakdown-grid">
                  @if ($gstServiceRows !== [])
                    <div class="sa-show-breakdown-panel">
                      <h3 class="sa-show-breakdown-title"><i class="bi bi-pie-chart" aria-hidden="true"></i> GST by service</h3>
                      <div class="table-responsive">
                        <table class="sa-show-breakdown-table">
                          <thead>
                            <tr>
                              <th scope="col">Service</th>
                              <th scope="col" class="text-end">Taxable amount</th>
                              <th scope="col" class="text-end">GST amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($gstServiceRows as $row)
                              <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-end">₹{{ number_format((float) $row['taxable'], 2) }}</td>
                                <td class="text-end">₹{{ number_format((float) $row['gst_amount'], 2) }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  @endif

                  @if ($gstLines !== [])
                    <div class="sa-show-breakdown-panel">
                      <h3 class="sa-show-breakdown-title"><i class="bi bi-diagram-3" aria-hidden="true"></i> GST split (CGST / SGST / IGST)</h3>
                      <div class="table-responsive">
                        <table class="sa-show-breakdown-table">
                          <thead>
                            <tr>
                              <th scope="col">Component</th>
                              <th scope="col" class="text-end">Rate</th>
                              <th scope="col" class="text-end">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($gstLines as $line)
                              <tr>
                                <td>{{ $line['type'] }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format((float) $line['rate'], 2), '0'), '.') }}%</td>
                                <td class="text-end">₹{{ number_format((float) $line['amount'], 2) }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  @endif

                  @if ($tdsServiceRows !== [])
                    <div class="sa-show-breakdown-panel">
                      <h3 class="sa-show-breakdown-title"><i class="bi bi-bank2" aria-hidden="true"></i> TDS by service</h3>
                      <div class="table-responsive">
                        <table class="sa-show-breakdown-table">
                          <thead>
                            <tr>
                              <th scope="col">Service</th>
                              <th scope="col" class="text-end">Salary amount</th>
                              <th scope="col" class="text-end">TDS amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($tdsServiceRows as $row)
                              <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-end">₹{{ number_format((float) $row['salary_amount'], 2) }}</td>
                                <td class="text-end">₹{{ number_format((float) $row['tds_amount'], 2) }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  @endif
                </div>
              @endif
            </section>

            <section class="pr-show-section pr-show-section--files" aria-labelledby="sa-show-files-heading">
              <h2 id="sa-show-files-heading" class="pr-show-section-head">
                <span class="pr-show-section-head-ic" aria-hidden="true"><i class="bi bi-paperclip"></i></span>
                <span class="pr-show-section-head-text">Documents</span>
                @if ($saAttachTotal > 0)
                  <span class="pr-show-attach-total-badge">{{ $saAttachTotal }} {{ $saAttachTotal === 1 ? 'file' : 'files' }}</span>
                @endif
              </h2>

              @if ($saAttachTotal === 0)
                <p class="text-muted mb-0">No documents uploaded for this agreement.</p>
              @else
                <div class="pr-show-attachments-wrap">
                  @foreach (\App\Models\SecurityAgreement::FILE_SLOTS as $slot => $fileMeta)
                    @php $slotDocuments = $r->documentsForSlot($slot); @endphp
                    @if (count($slotDocuments) > 0)
                      <div class="pr-show-attach-group">
                        <p class="pr-show-attach-group-head mb-2">
                          <i class="bi {{ $saAttachSlotIcons[$slot] ?? 'bi-paperclip' }}" aria-hidden="true"></i>
                          <span class="fw-semibold">{{ $fileMeta['label'] }}</span>
                          <span class="text-muted">· {{ count($slotDocuments) }} {{ count($slotDocuments) === 1 ? 'file' : 'files' }}</span>
                        </p>
                        <div class="pr-gmail-attach-grid pr-gmail-attach-grid--row pr-show-attach-grid" role="list">
                          @foreach ($slotDocuments as $doc)
                            @if (! empty($doc['url']))
                              @php
                                $previewKind = (string) ($doc['preview_kind'] ?? 'other');
                                $previewTitle = (string) ($doc['name'] ?? $fileMeta['label']);
                              @endphp
                              <div class="pr-gmail-attach-card pr-show-attach-card" role="listitem">
                                <button type="button"
                                  class="pr-gmail-attach-thumb"
                                  data-sa-attach-preview
                                  data-sa-attach-preview-url="{{ $doc['url'] }}"
                                  data-sa-attach-preview-kind="{{ $previewKind }}"
                                  data-sa-attach-preview-title="{{ $previewTitle }}"
                                  title="Preview {{ $previewTitle }}">
                                  <span class="pr-gmail-attach-thumb-inner pr-gmail-attach-thumb--{{ $previewKind }}">
                                    @if ($previewKind === 'image')
                                      <img src="{{ $doc['url'] }}" alt="" loading="lazy" class="pr-gmail-attach-thumb-media">
                                    @elseif ($previewKind === 'pdf')
                                      <iframe src="{{ $doc['url'] }}#toolbar=0&navpanes=0&scrollbar=0" title="" class="pr-gmail-attach-thumb-media" loading="lazy"></iframe>
                                    @else
                                      <span class="pr-gmail-attach-thumb-fallback" aria-hidden="true"><i class="bi {{ $doc['icon'] ?? 'bi-file-earmark-text' }}"></i></span>
                                    @endif
                                  </span>
                                </button>
                                <div class="pr-gmail-attach-foot">
                                  <span class="pr-gmail-type-badge {{ $saAttachTypeClass($previewKind) }}">{{ $doc['badge'] ?? 'FILE' }}</span>
                                  <span class="pr-gmail-attach-name" title="{{ $previewTitle }}">{{ $previewTitle }}</span>
                                </div>
                                <span class="pr-gmail-attach-fold" aria-hidden="true"></span>
                              </div>
                            @endif
                          @endforeach
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>
              @endif
            </section>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('modals')
<div class="modal fade preview-modal pr-show-attachment-modal" id="attachmentPreviewModal" tabindex="-1" aria-labelledby="attachmentPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom py-2 py-md-3">
        <h2 class="modal-title h5 mb-0 fw-bold text-truncate pe-2" id="attachmentPreviewModalLabel">Document preview</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-light pr-show-attachment-modal-body preview-modal-body">
        <iframe id="attachmentPreviewIframe" class="preview-modal-iframe pr-show-attachment-iframe d-none" title="Document preview"></iframe>
        <img id="attachmentPreviewImg" class="preview-modal-img pr-show-attachment-img d-none img-fluid d-block mx-auto" alt="" />
        <div id="attachmentPreviewFallback" class="preview-modal-fallback d-none">
          <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
          <p class="mb-2">Preview is not available for this file type.</p>
          <a href="#" id="attachmentPreviewOpenLink" class="btn btn-sm btn-primary" target="_blank" rel="noopener">Open document</a>
        </div>
      </div>
      <div class="modal-footer flex-wrap gap-2 border-top py-2">
        <a href="#" id="attachmentPreviewFooterLink" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
          <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Open in new tab
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@if (session('success') || session('error') || ($errors ?? null)?->any())
<script>
(function () {
  if (window.__saFlashToastShown || typeof window.toastr === 'undefined') {
    return;
  }
  window.__saFlashToastShown = true;
  @if (session('success'))
  toastr.success(@json(session('success')));
  @endif
  @if (session('error'))
  toastr.error(@json(session('error')));
  @endif
  @php $validationMessages = ($errors ?? null)?->all() ?? []; @endphp
  @if (count($validationMessages) > 0)
  if (window.FormFieldValidation && typeof window.FormFieldValidation.showBackendToasts === 'function') {
    FormFieldValidation.showBackendToasts(@json($validationMessages), {
      summary: @json(count($validationMessages) > 1 ? 'Please correct the highlighted fields.' : '')
    });
  } else {
    @foreach ($validationMessages as $idx => $msg)
    setTimeout(function () { toastr.error(@json($msg)); }, {{ (int) $idx * 120 }});
    @endforeach
  }
  @endif
})();
</script>
@endif
<script src="{{ asset('assets/js/security_agreement.js') }}?v={{ @filemtime(public_path('assets/js/security_agreement.js')) }}"></script>
@endpush
