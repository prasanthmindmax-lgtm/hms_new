<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/pharmacy_audit.css') }}">

<body class="phau-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

@php
  $r = $record;
  $r->loadMissing(['items', 'creator']);
  $totalVal = (float) $r->items->sum(fn ($i) => (float) $i->val);
@endphp

<div class="pc-container">
  <div class="pc-content">
    <div class="phau-shell">
      <div class="phau-card">
        <header class="phau-hero">
          <div class="phau-hero-inner">
            <h1 class="phau-hero-title"><i class="bi bi-capsule-pill"></i> {{ $r->audit_number }}</h1>
            <p class="phau-hero-sub mb-0">{{ $r->company?->company_name }} · {{ $r->audit_date?->format('d M Y') }}</p>
          </div>
          <div class="phau-hero-actions">
            <a href="{{ route('pharmacy-audits.index') }}" class="phau-btn-outline"><i class="bi bi-list-ul"></i> List</a>
            <a href="{{ route('pharmacy-audits.edit', $r) }}" class="phau-btn-new"><i class="bi bi-pencil-square"></i> Edit</a>
          </div>
        </header>
        <div class="phau-body phau-body--detail">
          <div class="row g-3 mb-4 phau-detail-grid">
            <div class="col-md-3">
              <div class="phau-detail-card">
                <div class="phau-detail-label">Zone</div>
                <div class="phau-detail-value">{{ $r->zone?->name ?: '—' }}</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="phau-detail-card">
                <div class="phau-detail-label">Branch</div>
                <div class="phau-detail-value">{{ $r->branch?->name ?: '—' }}</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="phau-detail-card">
                <div class="phau-detail-label">Created by</div>
                <div class="phau-detail-value">{{ $r->creator?->user_fullname ?? '—' }}</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="phau-detail-card">
                <div class="phau-detail-label">Recorded</div>
                <div class="phau-detail-value">{{ $r->created_at?->format('d M Y, h:i A') }}</div>
              </div>
            </div>
            @if ($r->notes)
              <div class="col-12">
                <div class="phau-detail-card phau-detail-card--notes">
                  <div class="phau-detail-label">Notes</div>
                  <div class="phau-detail-value phau-detail-value--notes">{{ $r->notes }}</div>
                </div>
              </div>
            @endif
          </div>

          <div class="phau-items-card">
            <div class="phau-items-head"><i class="bi bi-table"></i> Audit items</div>
            <div class="phau-items-table-wrap phau-table-wrap--show">
              <table class="phau-items-table">
              <thead>
                <tr>
                  <th class="phau-th-nowrap">S.No</th>
                  <th class="phau-th-nowrap">Name</th>
                  <th class="phau-th-nowrap">Batch</th>
                  <th class="phau-th-nowrap">Expiry</th>
                  <th class="text-end phau-th-nowrap">MRP</th>
                  <th class="text-end phau-th-qty">System Quantity<span class="phau-th-hint">Stock on system</span></th>
                  <th class="text-end phau-th-qty">Manual Quantity<span class="phau-th-hint">Physical count</span></th>
                  <th class="text-end phau-th-nowrap">Diff</th>
                  <th class="text-end phau-th-nowrap">Val</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($r->items as $item)
                  <tr>
                    <td>{{ $item->line_no }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->batch_no ?: '—' }}</td>
                    <td>
                      @php
                        $__ex = trim((string) ($item->expiry ?? ''));
                        $__disp = '—';
                        if ($__ex !== '') {
                            if (preg_match('/^\d{4}-\d{2}$/', $__ex)) {
                                try {
                                    $__disp = \Carbon\Carbon::createFromFormat('Y-m', $__ex)->format('M Y');
                                } catch (\Throwable $e) {
                                    $__disp = $__ex;
                                }
                            } else {
                                $__disp = $__ex;
                            }
                        }
                      @endphp
                      {{ $__disp }}
                    </td>
                    <td class="text-end font-monospace">{{ number_format((float) $item->mrp, 2) }}</td>
                    <td class="text-end font-monospace">{{ number_format($item->system_qty) }}</td>
                    <td class="text-end font-monospace">{{ number_format($item->manual_qty) }}</td>
                    <td class="text-end font-monospace fw-semibold">{{ number_format($item->diff_qty) }}</td>
                    <td class="text-end font-monospace fw-semibold @if ((float) $item->val < 0) text-danger @endif">{{ number_format((float) $item->val, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr class="phau-total-row">
                  <td colspan="8" class="text-end">TOTAL</td>
                  <td class="text-end phau-total-val">{{ number_format($totalVal, 2) }}</td>
                </tr>
              </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
</body>
</html>
