@php
  $r = $record;
  $attachmentUrl = \App\Models\RentalAgreement::attachmentPublicUrl($r->attachment_path);
  $attachmentName = $r->attachment_original_name ?: ($r->attachment_path ? basename((string) $r->attachment_path) : '');
  $buildingPhotoUrl = \App\Models\RentalAgreement::buildingPhotoPublicUrl($r->building_photo_path);
  $buildingPhotoName = $r->building_photo_original_name ?: ($r->building_photo_path ? basename((string) $r->building_photo_path) : '');
  $mapUrlShow = \App\Models\RentalAgreement::googleMapsSearchUrl($r->zone?->name, $r->branch?->name, (string) $r->address);
  $agreementPeriodDisplay = trim((string) $r->agreement_period);
  if ($agreementPeriodDisplay !== '' && stripos($agreementPeriodDisplay, ' to ') === false) {
      try {
          $agreementPeriodDisplay = \Carbon\Carbon::parse($agreementPeriodDisplay)->format('d M Y');
      } catch (\Throwable $e) {
          // Keep legacy text if it was stored in a non-date format.
      }
  }
@endphp
<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}">

<body class="ra-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="ra-shell">
      <div class="ra-card">
        <header class="ra-hero">
          <div>
            <div class="ra-hero-kicker">
              <i class="bi bi-file-earmark-text"></i>
              {{ $moduleTitle }} · {{ $r->agreement_number }}
            </div>
            <h1 class="ra-hero-title">
              <i class="bi bi-house-check"></i>
              {{ $r->owner_name }}
            </h1>
          </div>
          <div class="ra-hero-actions">
            <a href="{{ route($routeNames['index'], array_filter(['category' => request()->query('category')])) }}" class="ra-btn-ghost">
              <i class="bi bi-arrow-left"></i>
              Back to list
            </a>
            <a href="{{ route($routeNames['ownerPayments'], array_filter(array_merge(['rentalAgreement' => $r], ['category' => request()->query('category')]))) }}" class="ra-btn-outline">
              <i class="bi bi-bank2"></i>
              Bill payments &amp; NEFT
            </a>
            <a href="{{ route($routeNames['edit'], ['rentalAgreement' => $r]) }}" class="ra-btn-primary">
              <i class="bi bi-pencil-square"></i>
              Edit agreement
            </a>
          </div>
        </header>

        <div class="ra-body">
          <div class="ra-stats">
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-calendar-event"></i></span>
              <span class="ra-stat-lbl">Agreement date</span>
              <span class="ra-stat-num" style="font-size:1.2rem;">{{ $r->agreement_date?->format('d M Y') ?: '—' }}</span>
              <span class="ra-stat-hint">Start reference date</span>
            </div>
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-wallet2"></i></span>
              <span class="ra-stat-lbl">Refundable advance paid</span>
              <span class="ra-stat-num" style="font-size:1.2rem;">{{ number_format((float) $r->advance_amount, 2) }}</span>
              <span class="ra-stat-hint">As recorded on the agreement</span>
            </div>
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-currency-rupee"></i></span>
              <span class="ra-stat-lbl">Monthly rent</span>
              <span class="ra-stat-num" style="font-size:1.2rem;">{{ number_format((float) $r->monthly_rent_amount, 2) }}</span>
              <span class="ra-stat-hint">{{ \App\Models\RentalAgreement::gstLabel($r->gst_type) }}</span>
            </div>
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-calendar2-x"></i></span>
              <span class="ra-stat-lbl">Ends on</span>
              <span class="ra-stat-num" style="font-size:1.2rem;">{{ $r->end_of_agreement_date?->format('d M Y') ?: '—' }}</span>
              <span class="ra-stat-hint">Agreement expiry</span>
            </div>
          </div>

          <div class="ra-show-card">
            <div class="ra-show-head">
              <i class="bi bi-grid-3x3-gap"></i>
              Agreement snapshot
            </div>
            <div class="ra-show-grid">
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-hash"></i> Agreement Number</div>
                <div class="ra-show-item-value">{{ $r->agreement_number }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-building"></i> Company</div>
                <div class="ra-show-item-value">{{ $r->company?->company_name ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-map"></i> Zone</div>
                <div class="ra-show-item-value">{{ $r->zone?->name ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-diagram-2"></i> Branch</div>
                <div class="ra-show-item-value">{{ $r->branch?->name ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-people"></i> Other parties</div>
                <div class="ra-show-item-value">
                  @forelse ($r->additionalPartyNamesList() as $n)
                    <div>{{ $n }}</div>
                  @empty
                    —
                  @endforelse
                </div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-hourglass-split"></i> Agreement Period</div>
                <div class="ra-show-item-value">{{ $agreementPeriodDisplay ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-receipt"></i> GST Type</div>
                <div class="ra-show-item-value">
                  <span class="{{ \App\Models\RentalAgreement::gstPillClass($r->gst_type) }}">{{ \App\Models\RentalAgreement::gstLabel($r->gst_type) }}</span>
                </div>
              </div>
              @if (\App\Models\RentalAgreement::isGstApplicableType($r->gst_type))
                <div class="ra-show-item">
                  <div class="ra-show-item-label"><i class="bi bi-receipt-cutoff"></i> GST tax</div>
                  <div class="ra-show-item-value">{{ $r->gstRateAmountSummary() }}</div>
                </div>
                @php $gstLines = $r->gstSplitLines(); @endphp
                @if ($gstLines !== [])
                  <div class="ra-show-item ra-show-item--full">
                    <div class="ra-show-item-label"><i class="bi bi-pie-chart"></i> GST breakdown</div>
                    <div class="ra-show-item-value">
                      <div class="ra-gst-show-breakdown">
                        @foreach ($gstLines as $line)
                          <div class="ra-gst-show-line">
                            <span>{{ $line['type'] }} [{{ rtrim(rtrim(number_format((float) $line['rate'], 2), '0'), '.') }}%]</span>
                            <span>₹{{ number_format((float) $line['amount'], 2) }}</span>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endif
              @endif
              
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-bank"></i> TDS</div>
                <div class="ra-show-item-value">{{ $r->tdsSummary() }}</div>
              </div><div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-arrow-left-right"></i> RCM</div>
                <div class="ra-show-item-value">{{ $r->rcmSummary() }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-tools"></i> Maintenance Amount</div>
                <div class="ra-show-item-value">{{ $r->maintenance_amount !== null ? number_format((float) $r->maintenance_amount, 2) : '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-lightning-charge"></i> EB Number</div>
                <div class="ra-show-item-value">{{ $r->eb_number ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-bounding-box"></i> Sq Ft Area</div>
                <div class="ra-show-item-value">{{ $r->sq_ft_area !== null ? number_format((float) $r->sq_ft_area, 2) : '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-arrow-repeat"></i> Rent Revision</div>
                <div class="ra-show-item-value">{{ $r->rent_revision ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-percent"></i> Rent Hike Percentage</div>
                <div class="ra-show-item-value">{{ $r->rent_hike_percentage !== null ? number_format((float) $r->rent_hike_percentage, 2).'%' : '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-calendar-check"></i> Date of Rent Payment</div>
                <div class="ra-show-item-value">{{ $r->date_of_rent_payment ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-signpost-split"></i> Termination Period</div>
                <div class="ra-show-item-value">{{ $r->termination_period ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-person-vcard"></i> PAN Number</div>
                <div class="ra-show-item-value">{{ $r->pan_number ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-person-circle"></i> Contact Person</div>
                <div class="ra-show-item-value">{{ $r->contact_person_name ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-telephone"></i> Contact Number</div>
                <div class="ra-show-item-value">{{ $r->contact_person_number ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-person-workspace"></i> Created By</div>
                <div class="ra-show-item-value">{{ $r->creator?->user_fullname ?: '—' }}</div>
              </div>
              <div class="ra-show-item">
                <div class="ra-show-item-label"><i class="bi bi-clock-history"></i> Created On</div>
                <div class="ra-show-item-value">{{ $r->created_at?->format('d M Y, h:i A') ?: '—' }}</div>
              </div>
              <div class="ra-show-item" style="grid-column: 1 / -1;">
                <div class="ra-show-item-label d-flex align-items-center flex-wrap gap-2 justify-content-between">
                  <span><i class="bi bi-geo-alt"></i> Address</span>
                  <a href="{{ $mapUrlShow }}" target="_blank" rel="noopener noreferrer" class="ra-maps-chip">
                    Google Maps <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                  </a>
                </div>
                <div class="ra-show-item-value ra-show-item-value--pre">{{ $r->address ?: '—' }}</div>
              </div>
            </div>
          </div>

          <div class="ra-show-card">
            <div class="ra-show-head">
              <i class="bi bi-paperclip"></i>
              Attachment
            </div>
            @if ($attachmentUrl)
              <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="ra-file-chip">
                <i class="bi bi-file-earmark-arrow-down"></i>
                {{ $attachmentName ?: 'View uploaded file' }}
              </a>
            @else
              <div class="ra-muted">No attachment uploaded for this agreement.</div>
            @endif
          </div>

          <div class="ra-show-card">
            <div class="ra-show-head">
              <i class="bi bi-image"></i>
              Building photo
            </div>
            @if ($buildingPhotoUrl)
              <a href="{{ $buildingPhotoUrl }}" target="_blank" rel="noopener" class="d-inline-block">
                <img src="{{ $buildingPhotoUrl }}" alt="Building" class="ra-building-show-img" loading="lazy">
              </a>
              @if ($buildingPhotoName)
                <div class="ra-muted small mt-2">{{ $buildingPhotoName }}</div>
              @endif
            @else
              <div class="ra-muted">No building photo uploaded for this agreement.</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
  @if (session('success'))
    toastr.success(@json(session('success')));
  @endif
  @if (session('error'))
    toastr.error(@json(session('error')));
  @endif
</script>
</body>
</html>
