<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  /* ── Page surface — minimal side gutters, no wasted space ── */
  body.grn-show-page { background: #f6f7fb; }
  body.grn-show-page .pc-container .pc-content {
    max-width: 100%;
    width: 100%;
    margin: 0;
    padding: 0 4px 24px 4px;
    box-sizing: border-box;
  }
  @media (min-width: 768px) {
    body.grn-show-page .pc-container .pc-content {
      padding-left: 8px;
      padding-right: 8px;
    }
  }

  /* ── Document surface ── */
  .gd-doc {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }

  /* ── Header ── */
  .gd-head {
    padding: 18px 22px 16px;
    border-bottom: 1px solid #eef0f4;
    display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between;
    gap: 14px 18px;
  }
  .gd-back {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.78rem; font-weight: 600; color: #6b7280;
    text-decoration: none; margin-bottom: 8px;
  }
  .gd-back:hover { color: #4f46e5; }
  .gd-head-title {
    margin: 0;
    font-size: clamp(1.25rem, 1.7vw, 1.5rem);
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    font-variant-numeric: tabular-nums;
    display: flex; flex-wrap: wrap; align-items: center; gap: 12px;
    line-height: 1.2;
  }
  .gd-head-sub {
    margin: 6px 0 0; font-size: 0.82rem; color: #6b7280;
  }
  .gd-status {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.02em;
    padding: 4px 10px 4px 8px; border-radius: 6px;
    border: 1px solid transparent;
  }
  .gd-status::before {
    content: ''; width: 6px; height: 6px; border-radius: 50%;
    background: currentColor; opacity: 0.85;
  }
  .gd-status--pending  { background: #fef3c7; color: #92400e; border-color: #fde68a; }
  .gd-status--approved { background: #d1fae5; color: #047857; border-color: #a7f3d0; }
  .gd-status--rejected { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

  .gd-head-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
  .gd-btn {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.46rem 0.9rem; border-radius: 8px;
    font-size: 0.8125rem; font-weight: 600; line-height: 1.2;
    text-decoration: none !important; border: 1px solid #d1d5db;
    background: #fff; color: #374151 !important;
    transition: background 0.12s ease, border-color 0.12s ease, color 0.12s ease;
  }
  .gd-btn:hover { background: #f9fafb; border-color: #9ca3af; color: #111827 !important; }
  .gd-btn--primary {
    background: #4f46e5; border-color: #4f46e5; color: #fff !important;
  }
  .gd-btn--primary:hover { background: #4338ca; border-color: #4338ca; color: #fff !important; }
  .gd-btn i { font-size: 0.95em; }

  /* ── Audit strip ── */
  .gd-audit {
    padding: 11px 22px;
    font-size: 0.8125rem;
    border-bottom: 1px solid #eef0f4;
    display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px;
  }
  .gd-audit i { font-size: 0.95rem; }
  .gd-audit b { font-weight: 700; }
  .gd-audit-meta { color: #94a3b8; font-size: 0.78rem; margin-left: 4px; }
  .gd-audit--pending  { background: #fffbeb; color: #92400e; border-bottom-color: #fde68a; }
  .gd-audit--approved { background: #ecfdf5; color: #065f46; border-bottom-color: #bbf7d0; }
  .gd-audit--rejected { background: #fef2f2; color: #991b1b; border-bottom-color: #fecaca; }
  .gd-audit-reason {
    display: block; width: 100%;
    margin-top: 6px; padding-top: 6px;
    font-size: 0.78rem; color: #7f1d1d;
    border-top: 1px dashed rgba(185, 28, 28, 0.25);
    white-space: pre-wrap;
  }

  /* ── Section titles ── */
  .gd-section-h {
    padding: 14px 22px 6px;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
    color: #6b7280;
    display: flex; align-items: center; gap: 8px;
  }
  .gd-section-h i { color: #9ca3af; font-size: 0.95rem; }

  /* ── Definition list (the workhorse) ── */
  .gd-dl {
    margin: 0; padding: 4px 22px 18px;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px 28px;
  }
  @media (max-width: 991.98px) { .gd-dl { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 479.98px) { .gd-dl { grid-template-columns: 1fr; } }
  .gd-dl-item { min-width: 0; }
  .gd-dl-lbl {
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    color: #9ca3af; margin: 0 0 4px;
  }
  .gd-dl-val {
    margin: 0;
    font-size: 0.9rem; font-weight: 600;
    color: #111827; line-height: 1.4;
    word-break: break-word;
  }
  .gd-dl-val--mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    font-size: 0.85rem; font-variant-numeric: tabular-nums;
  }
  .gd-dl-sub {
    margin: 4px 0 0; font-size: 0.75rem; color: #6b7280; font-weight: 500;
  }

  /* ── Remarks ── */
  .gd-remarks {
    padding: 14px 22px 18px;
    border-top: 1px solid #eef0f4;
    background: #fafbfc;
  }
  .gd-remarks-h {
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
    color: #6b7280; margin: 0 0 8px;
  }
  .gd-remarks p {
    margin: 0; font-size: 0.875rem; color: #374151; line-height: 1.6;
    white-space: pre-wrap;
  }

  /* ── Attachments band ── */
  .gd-files {
    border-top: 1px solid #eef0f4;
    padding: 14px 22px 22px;
  }
  .gd-files-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }
  @media (max-width: 991.98px) { .gd-files-grid { grid-template-columns: 1fr; } }

  .gd-file {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    display: flex; flex-direction: column;
  }
  .gd-file-h {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    flex-wrap: wrap;
    padding: 9px 13px;
    background: #f9fafb;
    border-bottom: 1px solid #eef0f4;
    font-size: 0.78rem; font-weight: 700;
    color: #374151;
  }
  .gd-file-h span { display: inline-flex; align-items: center; gap: 7px; }
  .gd-file-h--pdf i { color: #dc2626; }
  .gd-file-h--vid i { color: #0d9488; }
  .gd-file-tools { display: inline-flex; gap: 5px; }
  .gd-file-tools a {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 9px; border-radius: 6px;
    font-size: 0.72rem; font-weight: 600;
    color: #4f46e5 !important; background: #fff;
    border: 1px solid #e5e7eb;
    text-decoration: none !important;
  }
  .gd-file-tools a:hover { background: #eef2ff; border-color: #c7d2fe; }
  .gd-file-tools a i { font-size: 0.78rem; }

  .gd-embed {
    background: #f3f4f6;
    min-height: 360px;
    height: min(55vh, 520px);
    position: relative;
  }
  .gd-embed iframe,
  .gd-embed object,
  .gd-embed video {
    width: 100%;
    height: 100%;
    min-height: 360px;
    border: 0;
    display: block;
    position: absolute;
    inset: 0;
  }
  .gd-embed video { background: #0f172a; object-fit: contain; }
  .gd-embed--pdf object { background: #e5e7eb; }
  .gd-file-foot {
    padding: 7px 13px; font-size: 0.72rem; color: #9ca3af;
    background: #fafbfc; border-top: 1px solid #eef0f4;
  }
  .gd-file-empty {
    padding: 20px 14px; text-align: center; color: #9ca3af; font-size: 0.85rem;
    background: #fafbfc;
  }
</style>
<body class="grn-show-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    @php
      $r = $record;
      $st = $r->audit_approval_status ?: 'pending';
      $invDate = $r->invoice_date ? $r->invoice_date->format('d M Y') : '—';
      $recvDate = $r->received_date ? $r->received_date->format('d M Y') : '—';
      $createdAt = $r->created_at ? $r->created_at->format('d M Y · h:i A') : '—';
      $creatorDisplay = $r->creatorDisplayName();
      $hasFiles = $r->invoice_copy_path
                  || ($r->gps_video_uploaded && $r->gps_video_path)
                  || ($r->gps_video_uploaded && ! $r->gps_video_path);
    @endphp

    <article class="gd-doc">
      {{-- ── Header ── --}}
      <header class="gd-head">
        <div>
          <a href="{{ route('grn.index') }}" class="gd-back"><i class="bi bi-arrow-left"></i> Back to GRN list</a>
          <h1 class="gd-head-title">
            <span>{{ $r->grn_number }}</span>
            <span class="gd-status gd-status--{{ $st }}">{{ \App\Models\GrnRecord::statusLabel($st) }}</span>
          </h1>
          <p class="gd-head-sub">
            Recorded {{ $createdAt }}@if($creatorDisplay !== '—') &nbsp;·&nbsp; by {{ $creatorDisplay }}@endif
          </p>
        </div>
        <div class="gd-head-actions">
          @if($canEdit ?? false)
            <a class="gd-btn gd-btn--primary" href="{{ route('grn.edit', $r) }}"><i class="bi bi-pencil-square"></i> Edit</a>
          @endif
          <a class="gd-btn" href="{{ route('grn.index') }}"><i class="bi bi-list-ul"></i> All records</a>
        </div>
      </header>

      {{-- ── Audit strip ── --}}
      @if($st === \App\Models\GrnRecord::STATUS_PENDING)
        <div class="gd-audit gd-audit--pending">
          <i class="bi bi-hourglass-split"></i>
          <span><b>Pending audit.</b> This GRN is waiting for review.</span>
        </div>
      @elseif(! $r->isPending() && $r->reviewed_by)
        @php
          $reviewerName = $r->reviewerDisplayName();
          $reviewedFmt = $r->reviewed_at ? $r->reviewed_at->format('d M Y · h:i A') : '';
        @endphp
        @if($st === \App\Models\GrnRecord::STATUS_APPROVED)
          <div class="gd-audit gd-audit--approved">
            <i class="bi bi-check-circle-fill"></i>
            <span><b>Approved by</b> {{ $reviewerName }}</span>
            @if($reviewedFmt)<span class="gd-audit-meta">· {{ $reviewedFmt }}</span>@endif
          </div>
        @else
          <div class="gd-audit gd-audit--rejected">
            <i class="bi bi-x-octagon-fill"></i>
            <span><b>Rejected by</b> {{ $reviewerName }}</span>
            @if($reviewedFmt)<span class="gd-audit-meta">· {{ $reviewedFmt }}</span>@endif
            @if(filled($r->rejection_reason))
              <span class="gd-audit-reason">{{ $r->rejection_reason }}</span>
            @endif
          </div>
        @endif
      @endif

      {{-- ── Vendor & invoice ── --}}
      <h2 class="gd-section-h"><i class="bi bi-receipt"></i> Vendor &amp; invoice</h2>
      <dl class="gd-dl">
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Vendor</p>
          <p class="gd-dl-val">{{ $r->vendor_name ?: '—' }}</p>
        </div>
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Invoice number</p>
          <p class="gd-dl-val gd-dl-val--mono">{{ $r->invoice_number ?: '—' }}</p>
        </div>
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Invoice date</p>
          <p class="gd-dl-val gd-dl-val--mono">{{ $invDate }}</p>
        </div>
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Received date</p>
          <p class="gd-dl-val gd-dl-val--mono">{{ $recvDate }}</p>
        </div>
      </dl>

      {{-- ── Location & receipt ── --}}
      <h2 class="gd-section-h"><i class="bi bi-geo-alt"></i> Location &amp; receipt</h2>
      <dl class="gd-dl">
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Company</p>
          <p class="gd-dl-val">{{ $r->company_name ?: '—' }}</p>
        </div>
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Zone</p>
          <p class="gd-dl-val">{{ $r->zone_name ?: '—' }}</p>
        </div>
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Branch</p>
          <p class="gd-dl-val">{{ $r->branch_name ?: '—' }}</p>
        </div>
        <div class="gd-dl-item">
          <p class="gd-dl-lbl">Received by</p>
          <p class="gd-dl-val">{{ $r->received_by ?: '—' }}</p>
        </div>
      </dl>

      {{-- ── Remarks ── --}}
      @if(filled($r->remarks))
        <div class="gd-remarks">
          <h3 class="gd-remarks-h">Remarks</h3>
          <p>{{ $r->remarks }}</p>
        </div>
      @endif

      {{-- ── Attachments ── --}}
      @if($hasFiles)
        <div class="gd-files">
          <h2 class="gd-section-h" style="padding: 0 0 10px;"><i class="bi bi-paperclip"></i> Attachments</h2>
          <div class="gd-files-grid">
            @if($r->invoice_copy_path)
              <article class="gd-file">
                <div class="gd-file-h gd-file-h--pdf">
                  <span><i class="bi bi-filetype-pdf"></i> Invoice copy</span>
                  <div class="gd-file-tools">
                    <a href="{{ asset('public/' . $r->invoice_copy_path) }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Open</a>
                    <a href="{{ asset('public/' . $r->invoice_copy_path) }}" download><i class="bi bi-download"></i> Save</a>
                  </div>
                </div>
                <div class="gd-embed">
                  <iframe src="{{ asset('public/' . $r->invoice_copy_path) }}#toolbar=1&navpanes=0&view=FitH" title="Invoice PDF preview"></iframe>
                </div>
                <div class="gd-file-foot">PDF preview rendered in page.</div>
              </article>
            @endif

            @if($r->gps_video_uploaded && $r->gps_video_path)
              <article class="gd-file">
                <div class="gd-file-h gd-file-h--vid">
                  <span><i class="bi bi-camera-video"></i> GPS verification</span>
                  <div class="gd-file-tools">
                    <a href="{{ asset('public/' . $r->gps_video_path) }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Open</a>
                    <a href="{{ asset('public/' . $r->gps_video_path) }}" download><i class="bi bi-download"></i> Save</a>
                  </div>
                </div>
                <div class="gd-embed">
                  <video controls playsinline preload="metadata" src="{{ asset('public/' . $r->gps_video_path) }}">
                    Your browser cannot play this video inline.
                    <a href="{{ asset('public/' . $r->gps_video_path) }}" target="_blank" rel="noopener">Open file</a>.
                  </video>  
                </div>
                <div class="gd-file-foot">Built-in video player.</div>
              </article>
            @elseif($r->gps_video_uploaded)
              <article class="gd-file">
                <div class="gd-file-h gd-file-h--vid">
                  <span><i class="bi bi-camera-video-off"></i> GPS verification</span>
                </div>
                <div class="gd-file-empty">Video was marked as uploaded but the file is not available on the server.</div>
              </article>
            @endif
          </div>
        </div>
      @endif
    </article>
  </div>
</div>

@include('superadmin.superadminfooter')
</body>
</html>
