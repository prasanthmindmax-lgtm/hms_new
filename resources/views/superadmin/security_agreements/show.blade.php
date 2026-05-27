@extends('superadmin.layouts.app')

@section('body_class', 'security-agreement-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/security_agreement.css') }}" />
@endpush

@section('content')
@php
  $r = $record;
  $mapUrlShow = \App\Models\SecurityAgreement::googleMapsSearchUrl($r->zone?->name, $r->branch?->name, (string) $r->address);
  $agreementPeriodDisplay = trim((string) $r->agreement_period);
  if ($agreementPeriodDisplay !== '' && stripos($agreementPeriodDisplay, ' to ') === false) {
      try {
          $agreementPeriodDisplay = \Carbon\Carbon::parse($agreementPeriodDisplay)->format('d M Y');
      } catch (\Throwable $e) {
          // Keep legacy text if it was stored in a non-date format.
      }
  }
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

        <div class="page-body">
          <div class="stats">
            <div class="stat">
              <span class="stat-icon"><i class="bi bi-calendar-event"></i></span>
              <span class="stat-label">Agreement date</span>
              <span class="stat-value" style="font-size:1.2rem;">{{ $r->agreement_date?->format('d M Y') ?: '—' }}</span>
              <span class="stat-hint">Start reference date</span>
            </div>
            <div class="stat">
              <span class="stat-icon"><i class="bi bi-wallet2"></i></span>
              <span class="stat-label">Refundable advance paid</span>
              <span class="stat-value" style="font-size:1.2rem;">{{ number_format((float) $r->advance_amount, 2) }}</span>
              <span class="stat-hint">As recorded on the agreement</span>
            </div>
            <div class="stat">
              <span class="stat-icon"><i class="bi bi-currency-rupee"></i></span>
              <span class="stat-label">Security charge</span>
              <span class="stat-value" style="font-size:1.2rem;">{{ number_format((float) $r->security_charge_amount, 2) }}</span>
              <span class="stat-hint">{{ \App\Models\SecurityAgreement::gstLabel($r->gst_type) }}</span>
            </div>
            <div class="stat">
              <span class="stat-icon"><i class="bi bi-calendar2-x"></i></span>
              <span class="stat-label">Ends on</span>
              <span class="stat-value" style="font-size:1.2rem;">{{ $r->end_of_agreement_date?->format('d M Y') ?: '—' }}</span>
              <span class="stat-hint">Agreement expiry</span>
            </div>
          </div>

          <div class="show-card">
            <div class="show-card-head">
              <i class="bi bi-grid-3x3-gap"></i>
              Agreement snapshot
            </div>
            <div class="show-grid">
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-hash"></i> Agreement Number</div>
                <div class="show-item-value">{{ $r->agreement_number }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-building"></i> Company</div>
                <div class="show-item-value">{{ $r->company?->company_name ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-map"></i> Zone</div>
                <div class="show-item-value">{{ $r->zone?->name ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-diagram-2"></i> Branch</div>
                <div class="show-item-value">{{ $r->branch?->name ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-person-badge"></i> Vendor</div>
                <div class="show-item-value">{{ $r->vendorDisplayName() }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-cash-stack"></i> Security fixed salary</div>
                <div class="show-item-value">{{ $r->security_fixed_salary_amount !== null ? '₹'.number_format((float) $r->security_fixed_salary_amount, 2) : '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-cash-stack"></i> Housekeeping fixed salary</div>
                <div class="show-item-value">{{ $r->housekeeping_fixed_salary_amount !== null ? '₹'.number_format((float) $r->housekeeping_fixed_salary_amount, 2) : '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-calendar-day"></i> Security paid leave</div>
                <div class="show-item-value">
                  @if ($r->security_paid_leave_applicable && $r->security_paid_leave_days)
                    Yes — {{ $r->security_paid_leave_days }} day(s)
                  @else
                    Not applicable
                  @endif
                </div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-calendar-day"></i> Housekeeping paid leave</div>
                <div class="show-item-value">
                  @if ($r->housekeeping_paid_leave_applicable && $r->housekeeping_paid_leave_days)
                    Yes — {{ $r->housekeeping_paid_leave_days }} day(s)
                  @else
                    Not applicable
                  @endif
                </div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-hourglass-split"></i> Agreement Period</div>
                <div class="show-item-value">{{ $agreementPeriodDisplay ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-receipt"></i> GST Type</div>
                <div class="show-item-value">
                  <span class="{{ \App\Models\SecurityAgreement::gstPillClass($r->gst_type) }}">{{ \App\Models\SecurityAgreement::gstLabel($r->gst_type) }}</span>
                </div>
              </div>
              @if (\App\Models\SecurityAgreement::isGstApplicableType($r->gst_type))
                <div class="show-item">
                  <div class="show-item-label"><i class="bi bi-receipt-cutoff"></i> GST tax</div>
                  <div class="show-item-value">{{ $r->gstRateAmountSummary() }}</div>
                </div>
                @php $gstServiceRows = $r->gstServiceBreakdownRows(); @endphp
                @if ($gstServiceRows !== [])
                  <div class="show-item show-item show-item--full">
                    <div class="show-item-label"><i class="bi bi-pie-chart"></i> GST breakdown (by service)</div>
                    <div class="show-item-value">
                      <div class="gst-show-breakdown">
                        @foreach ($gstServiceRows as $row)
                          <div class="gst-show-line gst-show-line--service">
                            <span class="fw-semibold">{{ $row['label'] }}</span>
                            <span>Taxable ₹{{ number_format((float) $row['taxable'], 2) }} · GST ₹{{ number_format((float) $row['gst_amount'], 2) }}</span>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endif
                @php $gstLines = $r->gstSplitLines(); @endphp
                @if ($gstLines !== [])
                  <div class="show-item show-item show-item--full">
                    <div class="show-item-label"><i class="bi bi-pie-chart"></i> GST split (CGST / SGST / IGST)</div>
                    <div class="show-item-value">
                      <div class="gst-show-breakdown">
                        @foreach ($gstLines as $line)
                          <div class="gst-show-line">
                            <span>{{ $line['type'] }} [{{ rtrim(rtrim(number_format((float) $line['rate'], 2), '0'), '.') }}%]</span>
                            <span>₹{{ number_format((float) $line['amount'], 2) }}</span>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endif
              @endif

              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-bank"></i> TDS</div>
                <div class="show-item-value">{{ $r->tdsSummary() }}</div>
              </div>
              @php $tdsServiceRows = $r->tdsServiceBreakdownRows(); @endphp
              @if ($tdsServiceRows !== [])
                <div class="show-item show-item show-item--full">
                  <div class="show-item-label"><i class="bi bi-bank2"></i> TDS breakdown (by service)</div>
                  <div class="show-item-value">
                    <div class="gst-show-breakdown">
                      @foreach ($tdsServiceRows as $row)
                        <div class="gst-show-line gst-show-line--service">
                          <span class="fw-semibold">{{ $row['label'] }}</span>
                          <span>Charge ₹{{ number_format((float) $row['charge_amount'], 2) }} · TDS ₹{{ number_format((float) $row['tds_amount'], 2) }}</span>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endif
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-arrow-left-right"></i> RCM</div>
                <div class="show-item-value">{{ $r->rcmSummary() }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-tools"></i> Housekeeping charge</div>
                <div class="show-item-value">{{ $r->housekeeping_charge_amount !== null ? number_format((float) $r->housekeeping_charge_amount, 2) : '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-signpost-split"></i> Termination Period</div>
                <div class="show-item-value">{{ $r->termination_period ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-person-vcard"></i> PAN Number</div>
                <div class="show-item-value">{{ $r->pan_number ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-person-circle"></i> Contact Person</div>
                <div class="show-item-value">{{ $r->contact_person_name ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-telephone"></i> Contact Number</div>
                <div class="show-item-value">{{ $r->contact_person_number ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-person-workspace"></i> Created By</div>
                <div class="show-item-value">{{ $r->creator?->user_fullname ?: '—' }}</div>
              </div>
              <div class="show-item">
                <div class="show-item-label"><i class="bi bi-clock-history"></i> Created On</div>
                <div class="show-item-value">{{ $r->created_at?->format('d M Y, h:i A') ?: '—' }}</div>
              </div>
              <div class="show-item" style="grid-column: 1 / -1;">
                <div class="show-item-label d-flex align-items-center flex-wrap gap-2 justify-content-between">
                  <span><i class="bi bi-geo-alt"></i> Address</span>
                  <a href="{{ $mapUrlShow }}" target="_blank" rel="noopener noreferrer" class="maps-link">
                    Google Maps <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                  </a>
                </div>
                <div class="show-item-value show-item-value show-item-value--pre">{{ $r->address ?: '—' }}</div>
              </div>
            </div>
          </div>

          <div class="show-card">
            <div class="show-card-head">
              <i class="bi bi-paperclip"></i>
              Documents
            </div>
            <div class="d-flex flex-column gap-3">
              @foreach (\App\Models\SecurityAgreement::FILE_SLOTS as $slot => $fileMeta)
                @php $slotDocuments = $r->documentsForSlot($slot); @endphp
                <div>
                  <div class="fw-semibold mb-1">{{ $fileMeta['label'] }}</div>
                  @if (count($slotDocuments) === 0)
                    <div class="text-muted-inline">No files uploaded.</div>
                  @else
                    <ul class="list-unstyled mb-0">
                      @foreach ($slotDocuments as $doc)
                        <li class="mb-2">
                          <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="file-chip file-chip--{{ $doc['preview_kind'] ?? 'other' }}">
                              <i class="bi {{ $doc['icon'] ?? 'bi-file-earmark' }}" aria-hidden="true"></i>
                              {{ $doc['name'] }}
                            </span>
                            @if (! empty($doc['url']))
                              <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                data-sa-attach-preview
                                data-sa-attach-preview-url="{{ $doc['url'] }}"
                                data-sa-attach-preview-kind="{{ $doc['preview_kind'] ?? 'other' }}"
                                data-sa-attach-preview-title="{{ $doc['name'] }}">
                                <i class="bi bi-eye" aria-hidden="true"></i> View
                              </button>
                              <a href="{{ $doc['url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-link">Open</a>
                            @else
                              <span class="small text-muted">File missing on server</span>
                            @endif
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('modals')
<div class="modal fade preview-modal" id="attachmentPreviewModal" tabindex="-1" aria-labelledby="attachmentPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attachmentPreviewModalLabel">Document preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-light preview-modal-body">
        <iframe id="attachmentPreviewIframe" class="preview-modal-iframe d-none" title="Document preview"></iframe>
        <img id="attachmentPreviewImg" class="preview-modal-img d-none img-fluid d-block mx-auto" alt="" />
        <div id="attachmentPreviewFallback" class="preview-modal-fallback d-none">
          <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
          <p class="mb-2">Preview is not available for this file type.</p>
          <a href="#" id="attachmentPreviewOpenLink" class="btn btn-sm btn-primary" target="_blank" rel="noopener">Open document</a>
        </div>
      </div>
      <div class="modal-footer py-2">
        <a href="#" id="attachmentPreviewFooterLink" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
          <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Open in new tab
        </a>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/security_agreement.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
  @if (session('success'))
    toastr.success(@json(session('success')));
  @endif
  @if (session('error'))
    toastr.error(@json(session('error')));
  @endif
</script>
@endpush
