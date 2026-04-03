<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Radiant Cash Mismatch Alert</title>
<style>
  /* ── Reset ── */
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background:#f0f4f8; color:#1e293b; -webkit-font-smoothing:antialiased; }
  a { text-decoration:none; }
  img { border:0; }

  /* ── Outer shell ── */
  .email-shell { max-width:700px; margin:0 auto; background:#f0f4f8; padding:28px 16px; }

  /* ── TOP BANNER ── */
  .top-banner {
    background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 60%,#1e293b 100%);
    border-radius:18px 18px 0 0;
    padding:32px 36px 28px;
    position:relative;
    overflow:hidden;
  }
  .top-banner::before {
    content:'';
    position:absolute;
    top:-50px; right:-50px;
    width:220px; height:220px; border-radius:50%;
    background:radial-gradient(circle,rgba(245,158,11,.18),transparent 70%);
  }
  .top-banner::after {
    content:'';
    position:absolute;
    bottom:-30px; left:40px;
    width:150px; height:150px; border-radius:50%;
    background:radial-gradient(circle,rgba(244,63,94,.12),transparent 70%);
  }
  .banner-inner { position:relative; z-index:1; }
  .alert-badge {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(244,63,94,.2); border:1px solid rgba(244,63,94,.4);
    border-radius:20px; padding:4px 14px;
    font-size:11px; font-weight:700; color:#fda4af;
    text-transform:uppercase; letter-spacing:1px; margin-bottom:14px;
  }
  .banner-title {
    font-size:22px; font-weight:800; color:#fff;
    letter-spacing:-.3px; line-height:1.2; margin-bottom:6px;
  }
  .banner-subtitle { font-size:13px; color:rgba(255,255,255,.55); }
  .banner-meta {
    display:flex; align-items:center; gap:12px; margin-top:18px; flex-wrap:wrap;
  }
  .meta-chip {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.18);
    border-radius:8px; padding:5px 12px;
    font-size:12px; font-weight:700; color:rgba(255,255,255,.85);
  }
  .meta-chip .lbl { font-weight:400; color:rgba(255,255,255,.5); margin-right:3px; }

  /* ── SUMMARY CARDS ── */
  .summary-bar {
    background:#fff;
    padding:22px 30px;
    display:flex; gap:0;
    border-bottom:1px solid #e2e8f0;
  }
  .sum-card {
    flex:1; text-align:center; padding:0 16px;
    border-right:1px solid #e2e8f0;
  }
  .sum-card:last-child { border-right:none; }
  .sum-num {
    font-size:28px; font-weight:900; letter-spacing:-1px;
    font-family:'Courier New',monospace; line-height:1;
  }
  .sum-num.red  { color:#f43f5e; }
  .sum-num.green{ color:#059669; }
  .sum-num.slate{ color:#0f172a; }
  .sum-label {
    font-size:10px; font-weight:700; text-transform:uppercase;
    letter-spacing:.8px; color:#94a3b8; margin-top:4px;
  }

  /* ── BODY ── */
  .email-body { background:#fff; padding:28px 30px; }

  /* Section heading */
  .sec-heading {
    display:flex; align-items:center; gap:8px;
    margin-bottom:16px; padding-bottom:10px;
    border-bottom:2px solid #f1f5f9;
  }
  .sec-heading-icon {
    width:32px; height:32px; border-radius:9px;
    display:flex; align-items:center; justify-content:center;
    font-size:15px;
  }
  .sec-heading h2 {
    font-size:14px; font-weight:800; color:#0f172a; letter-spacing:-.2px;
  }
  .sec-heading small { font-size:11px; color:#94a3b8; font-weight:600; }

  /* ── MISMATCH CARD ── */
  .mismatch-card {
    border:1.5px solid #e2e8f0; border-radius:14px;
    overflow:hidden; margin-bottom:16px;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
  }
  .mc-header {
    padding:13px 18px;
    display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:8px;
    border-bottom:1px solid #e2e8f0;
  }
  .mc-header.has-mismatch { background:#fff5f5; }
  .mc-header.has-close    { background:#fffbeb; }
  .mc-header.has-nodata   { background:#f8fafc; }
  .mc-location { font-size:14px; font-weight:800; color:#0f172a; }
  .mc-sub { font-size:11px; color:#64748b; margin-top:2px; }
  .mc-region-badge {
    display:inline-flex; align-items:center; gap:4px;
    background:#e2e8f0; border-radius:6px; padding:3px 9px;
    font-size:10px; font-weight:700; color:#475569;
  }

  /* Three-column comparison row */
  .mc-grid {
    display:table; width:100%; border-collapse:collapse;
  }
  .mc-col {
    display:table-cell; width:33.33%; padding:14px 16px;
    vertical-align:top; border-right:1px solid #f1f5f9;
  }
  .mc-col:last-child { border-right:none; }
  .mc-col-title {
    font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.8px;
    color:#94a3b8; margin-bottom:6px;
  }
  .mc-amount {
    font-size:18px; font-weight:900; letter-spacing:-.5px;
    font-family:'Courier New',monospace; line-height:1; margin-bottom:2px;
  }
  .mc-amount.amber { color:#d97706; }
  .mc-amount.blue  { color:#1d4ed8; }
  .mc-amount.green { color:#059669; }
  .mc-amount.rose  { color:#f43f5e; }
  .mc-amount.gray  { color:#94a3b8; }
  .mc-amount-sub { font-size:10px; color:#94a3b8; font-weight:600; }

  /* Status pill */
  .status-pill {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 9px; border-radius:20px;
    font-size:10px; font-weight:800; margin-top:8px;
  }
  .sp-match    { background:#d1fae5; color:#065f46; }
  .sp-close    { background:#fef3c7; color:#92400e; }
  .sp-mismatch { background:#ffe4e6; color:#9f1239; }
  .sp-nodata   { background:#f1f5f9; color:#64748b; }

  /* Diff row */
  .mc-diff {
    font-size:11px; font-weight:700; margin-top:6px; padding:4px 8px;
    border-radius:6px;
  }
  .mc-diff.neg { background:#ffe4e6; color:#f43f5e; }
  .mc-diff.pos { background:#fef3c7; color:#d97706; }
  .mc-diff.zero{ background:#d1fae5; color:#059669; }

  /* ── FOOTER ── */
  .email-footer {
    background:#1e293b;
    border-radius:0 0 18px 18px;
    padding:24px 30px;
    text-align:center;
  }
  .footer-logo {
    font-size:16px; font-weight:800; color:#fff; letter-spacing:-.3px; margin-bottom:6px;
  }
  .footer-text { font-size:11px; color:rgba(255,255,255,.45); line-height:1.6; }
  .footer-disclaimer {
    margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,.1);
    font-size:10px; color:rgba(255,255,255,.3);
  }

  /* ── ACTION CTA ── */
  .cta-wrap { text-align:center; padding:20px 0; }
  .cta-btn {
    display:inline-block;
    background:linear-gradient(135deg,#0f172a,#1e3a5f);
    color:#fff; font-size:13px; font-weight:700;
    padding:12px 28px; border-radius:10px;
    letter-spacing:-.1px;
  }

  /* No mismatch state */
  .all-clear {
    text-align:center; padding:36px 24px;
    background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    border-radius:12px; margin-bottom:16px;
  }
  .all-clear-icon { font-size:36px; margin-bottom:10px; }
  .all-clear h3 { font-size:18px; font-weight:800; color:#065f46; }
  .all-clear p  { font-size:12px; color:#16a34a; margin-top:4px; }

  @media(max-width:600px){
    .top-banner { padding:22px 18px 18px; }
    .summary-bar { padding:16px 10px; }
    .sum-num { font-size:22px; }
    .email-body { padding:18px 14px; }
    .mc-grid { display:block; }
    .mc-col  { display:block; width:100%; border-right:none; border-bottom:1px solid #f1f5f9; }
    .mc-col:last-child { border-bottom:none; }
    .banner-meta { flex-direction:column; gap:6px; }
  }
</style>
</head>
<body>
<div class="email-shell">

  {{-- ══ TOP BANNER ══ --}}
  <div class="top-banner">
    <div class="banner-inner">
      <div class="alert-badge">⚠ Mismatch Alert</div>
      <div class="banner-title">Radiant Cash Pickup<br>Mismatch Report</div>
      <div class="banner-subtitle">3-Way Reconciliation: Radiant · Branch Financial · Bank Statement</div>
      <div class="banner-meta">
        <div class="meta-chip"><span class="lbl">Date</span> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
        <div class="meta-chip"><span class="lbl">Total Records</span> {{ $totalCount }}</div>
        <div class="meta-chip"><span class="lbl">Mismatches</span> {{ $mismatchCount }}</div>
        <div class="meta-chip"><span class="lbl">Triggered by</span> {{ $sentBy }}</div>
      </div>
    </div>
  </div>

  {{-- ══ SUMMARY BAR ══ --}}
  <div class="summary-bar">
    <div class="sum-card">
      <div class="sum-num slate">{{ $totalCount }}</div>
      <div class="sum-label">Total Records</div>
    </div>
    <div class="sum-card">
      <div class="sum-num green">{{ $matchedCount }}</div>
      <div class="sum-label">Matched</div>
    </div>
    <div class="sum-card">
      <div class="sum-num red">{{ $mismatchCount }}</div>
      <div class="sum-label">Mismatches</div>
    </div>
    <div class="sum-card">
      <div class="sum-num" style="color:#d97706;">
        ₹{{ number_format(array_sum(array_column($mismatches, 'rcp_amount')), 0) }}
      </div>
      <div class="sum-label">Mismatch Amount</div>
    </div>
  </div>

  {{-- ══ BODY ══ --}}
  <div class="email-body">

    @if(count($mismatches) === 0)
    <div class="all-clear">
      <div class="all-clear-icon">✅</div>
      <h3>All Records Matched!</h3>
      <p>No discrepancies found for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
    </div>
    @else

    {{-- Section heading --}}
    <div class="sec-heading">
      <div class="sec-heading-icon" style="background:#ffe4e6;">⚠️</div>
      <div>
        <h2>Mismatch Details</h2>
        <small>{{ $mismatchCount }} record(s) require attention</small>
      </div>
    </div>

    @php
      $statusLabel = [
        'match'    => ['cls' => 'sp-match',    'icon' => '✓', 'text' => 'Match'],
        'close'    => ['cls' => 'sp-close',    'icon' => '~', 'text' => 'Close'],
        'mismatch' => ['cls' => 'sp-mismatch', 'icon' => '✗', 'text' => 'Mismatch'],
        'no_data'  => ['cls' => 'sp-nodata',   'icon' => '–', 'text' => 'No Data'],
      ];
      function rInr($n) { return '₹' . number_format($n, 0, '.', ','); }
      function hdrCls($bfr, $bank) {
        if ($bfr === 'mismatch' || $bank === 'mismatch') return 'has-mismatch';
        if ($bfr === 'close'    || $bank === 'close')    return 'has-close';
        return 'has-nodata';
      }
    @endphp

    @foreach($mismatches as $row)
    @php
      $bfrS  = $row['bfr_status'];
      $bankS = $row['bank_status'];
      $diffBfr  = $row['difference_bfr'];
      $diffBank = $row['difference_bank'];
    @endphp
    <div class="mismatch-card">

      {{-- Card Header --}}
      <div class="mc-header {{ hdrCls($bfrS, $bankS) }}">
        <div>
          <div class="mc-location">{{ $row['location'] ?: '—' }}</div>
          <div class="mc-sub">
            {{ $row['date'] }}
            @if($row['hci_slip'])· Slip: {{ $row['hci_slip'] }}@endif
            @if($row['deposit_mode'])· {{ $row['deposit_mode'] }}@endif
          </div>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
          @if($row['region'])
          <span class="mc-region-badge">📍 {{ $row['region'] }}</span>
          @endif
          @if($row['state'])
          <span class="mc-region-badge" style="background:#ede9fe;color:#6d28d9;">{{ $row['state'] }}</span>
          @endif
          @if($row['bfr_location'] && $row['bfr_zone'])
          <span class="mc-region-badge" style="background:#dbeafe;color:#1d4ed8;">🏢 {{ $row['bfr_location'] }} · {{ $row['bfr_zone'] }}</span>
          @endif
        </div>
      </div>

      {{-- Three columns --}}
      <div class="mc-grid">

        {{-- Col 1: RCP --}}
        <div class="mc-col" style="background:#fffbeb;">
          <div class="mc-col-title">🏦 Radiant Cash Pickup</div>
          <div class="mc-amount amber">{{ rInr($row['rcp_amount']) }}</div>
          <div class="mc-amount-sub">Pickup Amount</div>
          <span class="status-pill sp-match">✓ Source</span>
        </div>

        {{-- Col 2: Branch Financial Report --}}
        <div class="mc-col" style="background:#eff6ff;">
          <div class="mc-col-title">📊 Branch Financial Report</div>
          @if($bfrS === 'no_data')
            <div class="mc-amount gray">No Data</div>
            <div class="mc-amount-sub">Not found for this date</div>
          @else
            <div class="mc-amount blue">{{ rInr($row['bfr_amount']) }}</div>
            <div class="mc-amount-sub">{{ $row['bfr_records'] }} report(s)</div>
          @endif
          @php $sl = $statusLabel[$bfrS] ?? $statusLabel['no_data']; @endphp
          <span class="status-pill {{ $sl['cls'] }}">{{ $sl['icon'] }} {{ $sl['text'] }}</span>
          @if($bfrS !== 'no_data')
            @php $d = $diffBfr; @endphp
            <div class="mc-diff {{ $d > 0 ? 'pos' : ($d < 0 ? 'neg' : 'zero') }}">
              Diff: {{ $d > 0 ? '+' : '' }}{{ rInr($d) }}
            </div>
          @endif
        </div>

        {{-- Col 3: Bank Statement --}}
        <div class="mc-col" style="background:#f0fdf4;">
          <div class="mc-col-title">🏛 Bank Statement</div>
          @if($bankS === 'no_data')
            <div class="mc-amount gray">No Data</div>
            <div class="mc-amount-sub">No BY CASH match found</div>
          @else
            <div class="mc-amount green">{{ rInr($row['bank_amount']) }}</div>
            <div class="mc-amount-sub">{{ $row['bank_entries'] }} transaction(s)</div>
          @endif
          @php $sl = $statusLabel[$bankS] ?? $statusLabel['no_data']; @endphp
          <span class="status-pill {{ $sl['cls'] }}">{{ $sl['icon'] }} {{ $sl['text'] }}</span>
          @if($bankS !== 'no_data')
            @php $d = $diffBank; @endphp
            <div class="mc-diff {{ $d > 0 ? 'pos' : ($d < 0 ? 'neg' : 'zero') }}">
              Diff: {{ $d > 0 ? '+' : '' }}{{ rInr($d) }}
            </div>
          @endif
        </div>

      </div>{{-- mc-grid --}}
    </div>{{-- mismatch-card --}}
    @endforeach

    {{-- Action button --}}
    <div class="cta-wrap">
      <a class="cta-btn" href="{{ config('app.url') }}/superadmin/radiant-cash-pickup">
        🔍 View in Radiant Cash Pickup Dashboard
      </a>
    </div>

    @endif

    {{-- Info note --}}
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-top:8px;">
      <p style="font-size:11px;color:#64748b;line-height:1.7;margin:0;">
        <strong style="color:#0f172a;">Match Criteria:</strong>
        ✓ <strong>Match</strong> = within 1% · <strong>Close</strong> = within 10% ·
        <strong>Mismatch</strong> = difference &gt; 10% ·
        <strong>No Data</strong> = record not found in that source.<br>
        Bank Statement is searched using <em>"BY CASH – {location}"</em> within ±1 day of the pickup date.
      </p>
    </div>

  </div>{{-- email-body --}}

  {{-- ══ FOOTER ══ --}}
  <div class="email-footer">
    <div class="footer-logo">HMS — Radiant Cash MIS</div>
    <div class="footer-text">
      This is an automated alert generated on
      {{ now()->format('d M Y, h:i A') }} IST.<br>
      Sent by: <strong style="color:rgba(255,255,255,.7);">{{ $sentBy }}</strong>
    </div>
    <div class="footer-disclaimer">
      This email contains confidential financial information. Do not forward to unauthorised persons.
    </div>
  </div>

</div>{{-- email-shell --}}
</body>
</html>
