<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Complete</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.5.0/tabler-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:linear-gradient(135deg,#0f2d4a 0%,#1a7f64 100%);min-height:100vh;font-family:'Inter',system-ui,sans-serif;display:flex;align-items:center;justify-content:center;padding:20px}
.ty-card{background:#fff;border-radius:24px;padding:40px 32px;max-width:480px;width:100%;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.25)}
.ty-icon{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:36px}
.ty-icon.success{background:#dcfce7;color:#16a34a}
.ty-icon.error{background:#fee2e2;color:#dc2626}
.ty-title{font-size:22px;font-weight:700;margin-bottom:8px}
.ty-sub{font-size:13px;color:#64748b;line-height:1.6;margin-bottom:24px}
.badge-no{display:inline-block;background:#f0fdf4;border:2px solid #86efac;border-radius:12px;padding:12px 24px;font-size:24px;font-weight:800;color:#1a7f64;letter-spacing:0.08em;margin-bottom:20px}
.steps-info{background:#f8fafc;border-radius:14px;padding:16px;text-align:left;margin-bottom:24px}
.step-row{display:flex;align-items:center;gap:12px;padding:6px 0;font-size:12.5px;color:#475569}
.step-row i{font-size:18px;color:#1a7f64;flex-shrink:0}
.ty-footer{font-size:11px;color:#94a3b8;margin-top:8px}
</style>
</head>
<body>

<div class="ty-card">
  @if(isset($blacklisted) && $blacklisted)
  <div class="ty-icon error"><i class="ti ti-shield-exclamation"></i></div>
  <div class="ty-title" style="color:#dc2626">Entry Denied</div>
  <div class="ty-sub">
    Your visitor record has been flagged by our security system.<br>
    Please contact the reception desk for assistance.
  </div>
  @else
  <div class="ty-icon success"><i class="ti ti-circle-check"></i></div>
  <div class="ty-title" style="color:#1a7f64">Registration Successful!</div>
  <div class="ty-sub">
    Your visit request has been submitted and is pending approval.<br>
    Please wait at the reception area.
  </div>

  @if(isset($visitor) && $visitor->badge_number)
  <div class="badge-no">{{ $visitor->badge_number }}</div>
  <div style="font-size:12px;color:#64748b;margin-bottom:16px">Your visitor badge number</div>
  @endif

  <div class="steps-info">
    <div class="step-row"><i class="ti ti-clock"></i> Wait for approval from the reception desk</div>
    <div class="step-row"><i class="ti ti-id-badge"></i> You will be issued a visitor pass on approval</div>
    <div class="step-row"><i class="ti ti-map-pin"></i> Proceed only to the designated area mentioned in your pass</div>
    <div class="step-row"><i class="ti ti-door-exit"></i> Please check-out at reception before leaving</div>
  </div>
  @endif

  <div class="ty-footer">
    {{ $settings['hospital_name'] ?? "Dr. Aravind's IVF & Pregnancy Centre" }} — Visitor Management System
  </div>
</div>

</body>
</html>
