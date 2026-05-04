<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<body class="art-bank-detail-page">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div class="pc-container">
<div class="pc-content mw">
@php
/* ============================================================
   DUMMY DATA — keyed by ART-BNK ID
   Replace with $donor passed from controller when backend ready
   ============================================================ */
$all_donors = [
    'ART-2026-001' => [
        'id'            => 'ART-2026-001',
        'status'        => 'Active',
        'reg_date'      => '15 Jan 2026',
        'gender'        => 'Female',
        'location'      => 'Chennai (Velachery)',
        'phone'         => '98XX XXXX X101',
        'age'           => 27,
        'marital'       => 'Married',
        'children'      => 'Have Child',
        'child_age'     => '4 yrs',
        'child_cert'    => 'done',
        'marriage_photo'=> 'done',
        'aadhar'        => 'done',
        'aadhar_no'     => '7XXX XXXX X101',
        'aadhar_ver'    => true,
        'marriage_cert' => 'done',
        'insurance'     => 'done',
        'art_enrol'     => 'ART-E-0012',
        'pan'           => 'done',
        'tv_scan'       => 'done',
        'serology'      => 'done',
        'hb_electrophoresis' => 'done',
        'semen'         => '-',
        'bbt'           => 'done',
        'tft'           => 'done',
        'cardiac'       => 'done',
        'ecg'           => 'done',
        'inf_consent'   => 'done',
        'donor_consent' => 'done',
        'donor_bond'    => 'done',
        'egg_age'       => '27',
        'recip_name'    => 'Mrs. Kavitha R.',
        'recip_mrd'     => 'MRD-8821',
        'anesthesio'    => 'Dr. Ramesh K.',
        'ip_no'         => 'IP-20260115',
        'pre_photo'     => 'done',
        'post_photo'    => 'done',
        'pre_video'     => 'done',
        'post_video'    => 'done',
        'tubbing'       => 'ECIID-04',
        'ot_tech'       => 'Tech-Suresh',
        'opu'           => 'done',
        'zone'          => 'South Zone',
        'center'        => 'Chennai – Anna Nagar',
        'proc_branch'   => 'Chennai – Anna Nagar',
        'rx_pre'        => 'done',
        'rx_during'     => 'done',
        'rx_post'       => 'done',
        'exp_travel'    => '₹ 1,200',
        'exp_food'      => '₹ 600',
        'app_travel'    => '₹ 1,200',
        'app_food'      => '₹ 600',
        'utr'           => 'UTR9832110041',
        'handled_by'    => 'Dr. Ramesh K.',
    ],
    'ART-2026-002' => [
        'id'            => 'ART-2026-002',
        'status'        => 'Active',
        'reg_date'      => '20 Jan 2026',
        'gender'        => 'Female',
        'location'      => 'Madurai',
        'phone'         => '91XX XXXX X202',
        'age'           => 31,
        'marital'       => 'Divorced',
        'children'      => 'Without Child',
        'child_age'     => '-',
        'child_cert'    => '-',
        'marriage_photo'=> 'done',
        'aadhar'        => 'done',
        'aadhar_no'     => '8XXX XXXX X202',
        'aadhar_ver'    => true,
        'marriage_cert' => 'done',
        'insurance'     => 'pending',
        'art_enrol'     => 'ART-E-0019',
        'pan'           => 'done',
        'tv_scan'       => 'done',
        'serology'      => 'pending',
        'hb_electrophoresis' => 'pending',
        'semen'         => '-',
        'bbt'           => 'done',
        'tft'           => 'pending',
        'cardiac'       => 'done',
        'ecg'           => 'done',
        'inf_consent'   => 'done',
        'donor_consent' => 'done',
        'donor_bond'    => 'pending',
        'egg_age'       => '31',
        'recip_name'    => 'Mrs. Priya S.',
        'recip_mrd'     => 'MRD-9134',
        'anesthesio'    => 'Dr. Selvi M.',
        'ip_no'         => 'IP-20260120',
        'pre_photo'     => 'done',
        'post_photo'    => 'missing',
        'pre_video'     => 'done',
        'post_video'    => 'missing',
        'tubbing'       => 'ECIID-07',
        'ot_tech'       => 'Tech-Kumar',
        'opu'           => 'pending',
        'zone'          => 'South Zone',
        'center'        => 'Madurai – Main',
        'proc_branch'   => 'Madurai – Main',
        'rx_pre'        => 'done',
        'rx_during'     => 'pending',
        'rx_post'       => '-',
        'exp_travel'    => '₹ 2,500',
        'exp_food'      => '₹ 800',
        'app_travel'    => '₹ 2,000',
        'app_food'      => '₹ 800',
        'utr'           => 'UTR8800229901',
        'handled_by'    => 'Dr. Selvi M.',
    ],
    'ART-2026-003' => [
        'id'            => 'ART-2026-003',
        'status'        => 'Pending',
        'reg_date'      => '02 Feb 2026',
        'gender'        => 'Male',
        'location'      => 'Trichy',
        'phone'         => '70XX XXXX X303',
        'age'           => 29,
        'marital'       => 'Married',
        'children'      => 'Have Child',
        'child_age'     => '2 yrs',
        'child_cert'    => 'pending',
        'marriage_photo'=> 'done',
        'aadhar'        => 'done',
        'aadhar_no'     => '6XXX XXXX X303',
        'aadhar_ver'    => false,
        'marriage_cert' => 'done',
        'insurance'     => 'missing',
        'art_enrol'     => '-',
        'pan'           => 'pending',
        'tv_scan'       => '-',
        'serology'      => 'done',
        'hb_electrophoresis' => 'pending',
        'semen'         => 'done',
        'bbt'           => '-',
        'tft'           => '-',
        'cardiac'       => 'pending',
        'ecg'           => 'pending',
        'inf_consent'   => 'missing',
        'donor_consent' => 'missing',
        'donor_bond'    => '-',
        'egg_age'       => '-',
        'recip_name'    => '-',
        'recip_mrd'     => '-',
        'anesthesio'    => '-',
        'ip_no'         => '-',
        'pre_photo'     => '-',
        'post_photo'    => '-',
        'pre_video'     => '-',
        'post_video'    => '-',
        'tubbing'       => '-',
        'ot_tech'       => '-',
        'opu'           => '-',
        'zone'          => 'North Zone',
        'center'        => 'Trichy – Central',
        'proc_branch'   => '-',
        'rx_pre'        => '-',
        'rx_during'     => '-',
        'rx_post'       => '-',
        'exp_travel'    => '-',
        'exp_food'      => '-',
        'app_travel'    => '-',
        'app_food'      => '-',
        'utr'           => '-',
        'handled_by'    => '-',
    ],
    'ART-2026-004' => [
        'id'            => 'ART-2026-004',
        'status'        => 'Active',
        'reg_date'      => '10 Feb 2026',
        'gender'        => 'Female',
        'location'      => 'Coimbatore',
        'phone'         => '80XX XXXX X404',
        'age'           => 24,
        'marital'       => 'Unmarried',
        'children'      => '-',
        'child_age'     => '-',
        'child_cert'    => '-',
        'marriage_photo'=> '-',
        'aadhar'        => 'done',
        'aadhar_no'     => '5XXX XXXX X404',
        'aadhar_ver'    => true,
        'marriage_cert' => '-',
        'insurance'     => 'done',
        'art_enrol'     => 'ART-E-0031',
        'pan'           => 'done',
        'tv_scan'       => 'done',
        'serology'      => 'done',
        'hb_electrophoresis' => 'done',
        'semen'         => '-',
        'bbt'           => 'done',
        'tft'           => 'done',
        'cardiac'       => 'done',
        'ecg'           => 'done',
        'inf_consent'   => 'done',
        'donor_consent' => 'done',
        'donor_bond'    => 'done',
        'egg_age'       => '24',
        'recip_name'    => 'Mrs. Divya T.',
        'recip_mrd'     => 'MRD-7712',
        'anesthesio'    => 'Dr. Anand B.',
        'ip_no'         => 'IP-20260210',
        'pre_photo'     => 'done',
        'post_photo'    => 'done',
        'pre_video'     => 'done',
        'post_video'    => 'done',
        'tubbing'       => 'ECIID-03',
        'ot_tech'       => 'Tech-Priya',
        'opu'           => 'done',
        'zone'          => 'West Zone',
        'center'        => 'Coimbatore – RS Puram',
        'proc_branch'   => 'Coimbatore – RS Puram',
        'rx_pre'        => 'done',
        'rx_during'     => 'done',
        'rx_post'       => 'done',
        'exp_travel'    => '₹ 1,800',
        'exp_food'      => '₹ 500',
        'app_travel'    => '₹ 1,800',
        'app_food'      => '₹ 500',
        'utr'           => 'UTR7711338820',
        'handled_by'    => 'Dr. Anand B.',
    ],
    'ART-2026-005' => [
        'id'            => 'ART-2026-005',
        'status'        => 'Inactive',
        'reg_date'      => '18 Mar 2026',
        'gender'        => 'Female',
        'location'      => 'Salem',
        'phone'         => '63XX XXXX X505',
        'age'           => 33,
        'marital'       => 'Married',
        'children'      => 'Have Child',
        'child_age'     => '6 yrs',
        'child_cert'    => 'done',
        'marriage_photo'=> 'done',
        'aadhar'        => 'done',
        'aadhar_no'     => '4XXX XXXX X505',
        'aadhar_ver'    => true,
        'marriage_cert' => 'done',
        'insurance'     => 'done',
        'art_enrol'     => 'ART-E-0044',
        'pan'           => 'done',
        'tv_scan'       => 'done',
        'serology'      => 'done',
        'hb_electrophoresis' => 'missing',
        'semen'         => '-',
        'bbt'           => 'done',
        'tft'           => 'done',
        'cardiac'       => 'done',
        'ecg'           => 'done',
        'inf_consent'   => 'done',
        'donor_consent' => 'done',
        'donor_bond'    => 'done',
        'egg_age'       => '33',
        'recip_name'    => 'Mrs. Nithya P.',
        'recip_mrd'     => 'MRD-6643',
        'anesthesio'    => 'Dr. Mani G.',
        'ip_no'         => 'IP-20260318',
        'pre_photo'     => 'done',
        'post_photo'    => 'done',
        'pre_video'     => 'missing',
        'post_video'    => 'missing',
        'tubbing'       => 'ECIID-09',
        'ot_tech'       => 'Tech-Ravi',
        'opu'           => 'done',
        'zone'          => 'South Zone',
        'center'        => 'Salem – Omalur Road',
        'proc_branch'   => 'Salem – Omalur Road',
        'rx_pre'        => 'done',
        'rx_during'     => 'done',
        'rx_post'       => 'done',
        'exp_travel'    => '₹ 3,000',
        'exp_food'      => '₹ 700',
        'app_travel'    => '₹ 2,500',
        'app_food'      => '₹ 700',
        'utr'           => 'UTR6609887712',
        'handled_by'    => 'Dr. Mani G.',
    ],
    'ART-2026-006' => [
        'id'            => 'ART-2026-006',
        'status'        => 'Active',
        'reg_date'      => '05 Apr 2026',
        'gender'        => 'Male',
        'location'      => 'Tirunelveli',
        'phone'         => '94XX XXXX X606',
        'age'           => 28,
        'marital'       => 'Married',
        'children'      => 'Without Child',
        'child_age'     => '-',
        'child_cert'    => '-',
        'marriage_photo'=> 'done',
        'aadhar'        => 'done',
        'aadhar_no'     => '3XXX XXXX X606',
        'aadhar_ver'    => true,
        'marriage_cert' => 'done',
        'insurance'     => 'done',
        'art_enrol'     => 'ART-E-0058',
        'pan'           => 'done',
        'tv_scan'       => '-',
        'serology'      => 'done',
        'hb_electrophoresis' => '-',
        'semen'         => 'done',
        'bbt'           => '-',
        'tft'           => '-',
        'cardiac'       => 'done',
        'ecg'           => 'done',
        'inf_consent'   => 'done',
        'donor_consent' => 'done',
        'donor_bond'    => 'done',
        'egg_age'       => '-',
        'recip_name'    => 'Mrs. Lavanya K.',
        'recip_mrd'     => 'MRD-5521',
        'anesthesio'    => 'Dr. Bala R.',
        'ip_no'         => 'IP-20260405',
        'pre_photo'     => 'done',
        'post_photo'    => 'done',
        'pre_video'     => 'done',
        'post_video'    => 'done',
        'tubbing'       => 'ECIID-02',
        'ot_tech'       => 'Tech-Deepa',
        'opu'           => 'done',
        'zone'          => 'South Zone',
        'center'        => 'Tirunelveli – Main',
        'proc_branch'   => 'Tirunelveli – Main',
        'rx_pre'        => 'done',
        'rx_during'     => 'done',
        'rx_post'       => 'done',
        'exp_travel'    => '₹ 4,000',
        'exp_food'      => '₹ 900',
        'app_travel'    => '₹ 4,000',
        'app_food'      => '₹ 900',
        'utr'           => 'UTR5500112299',
        'handled_by'    => 'Dr. Bala R.',
    ],
];

$d = isset($all_donors[$donor_id]) ? $all_donors[$donor_id] : null;

/* Helpers (guarded — view may render more than once per request) */
if (!function_exists('detDocBadge')) {
    function detDocBadge($val) {
        if ($val === 'done')    return '<span class="det-badge det-done" onclick="detCantAccess()" style="cursor:pointer;" title="Click to view"><i class="fa fa-check-circle"></i> Done</span>';
        if ($val === 'pending') return '<span class="det-badge det-pend"><i class="fa fa-clock-o"></i> Pending</span>';
        if ($val === 'missing') return '<span class="det-badge det-miss"><i class="fa fa-times-circle"></i> Missing</span>';
        return '<span class="det-badge det-na">—</span>';
    }
}
if (!function_exists('detVal')) {
    function detVal($val, $fallback = '—') {
        return ($val && $val !== '-') ? htmlspecialchars($val) : '<span style="color:#bbb;">'.$fallback.'</span>';
    }
}
@endphp

<style>
body.art-bank-detail-page{background:#f0f7f5!important;}
.mw{padding:20px 24px;}
:root {
    --dp:  #0f7b6c;
    --ddk: #085f53;
    --dlt: #e6f7f5;
    --dac: #13c4a3;
}

/* ---- Header ---- */
.dp-header {
    background: linear-gradient(135deg, var(--dp) 0%, var(--ddk) 100%);
    border-radius: 14px;
    padding: 28px 30px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 6px 24px rgba(15,123,108,.22);
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.dp-header::after {
    content:'';
    position:absolute; right:-50px; top:-50px;
    width:220px; height:220px;
    background:rgba(255,255,255,.06);
    border-radius:50%;
}
.dp-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    border: 3px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; color: rgba(255,255,255,.85);
    flex-shrink: 0;
}
.dp-header-info h3 { margin: 0 0 6px; font-size: 20px; font-weight: 700; }
.dp-header-info p  { margin: 0; font-size: 13px; opacity: .85; }
.dp-header-meta {
    margin-left: auto;
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: flex-end;
}
.dp-id-pill {
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 20px;
    padding: 4px 16px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .04em;
}
.dp-status-pill {
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.dp-s-active   { background: #e6f7f5; color: #0f7b6c; }
.dp-s-inactive { background: #fce4e4; color: #c62828; }
.dp-s-pending  { background: #fff3e0; color: #e65100; }
.dp-back-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.35);
    color: #fff;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background .18s;
    cursor: pointer;
}
.dp-back-btn:hover { background: rgba(255,255,255,.28); color: #fff; text-decoration: none; }

/* ---- Section Cards ---- */
.dp-section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 20px;
    overflow: hidden;
}
.dp-sec-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f5f4;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
}
.dp-sec-body { padding: 18px 20px; }

/* section header colours */
.dsh-basic    { background: linear-gradient(90deg,#0f7b6c,#13c4a3); }
.dsh-family   { background: linear-gradient(90deg,#5c6bc0,#7986cb); }
.dsh-docs     { background: linear-gradient(90deg,#8e24aa,#ab47bc); }
.dsh-medical  { background: linear-gradient(90deg,#e65100,#fb8c00); }
.dsh-consent  { background: linear-gradient(90deg,#2e7d32,#43a047); }
.dsh-recip    { background: linear-gradient(90deg,#1565c0,#1e88e5); }
.dsh-ot       { background: linear-gradient(90deg,#4527a0,#7e57c2); }
.dsh-loc      { background: linear-gradient(90deg,#00838f,#26c6da); }
.dsh-rx       { background: linear-gradient(90deg,#6d4c41,#a1887f); }
.dsh-expense  { background: linear-gradient(90deg,#37474f,#607d8b); }

/* ---- Detail rows ---- */
.dp-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
}
.dp-field {
    flex: 0 0 25%;
    max-width: 25%;
    padding: 10px 14px;
    border-bottom: 1px solid #f4f6f5;
    border-right: 1px solid #f4f6f5;
}
.dp-field.half  { flex: 0 0 50%; max-width: 50%; }
.dp-field.full  { flex: 0 0 100%; max-width: 100%; }
.dp-field:nth-child(4n) { border-right: none; }
@media(max-width:768px) {
    .dp-field, .dp-field.half { flex:0 0 50%; max-width:50%; }
    .dp-field:nth-child(2n) { border-right:none; }
}
.dp-field-lbl {
    font-size: 10px;
    font-weight: 700;
    color: #aaa;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 4px;
}
.dp-field-val {
    font-size: 13px;
    font-weight: 600;
    color: #333;
}

/* ---- Badges ---- */
.det-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 11px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
}
.det-done { background: #e6f7f5; color: #0f7b6c; }
.det-pend { background: #fff3e0; color: #e65100; }
.det-miss { background: #fce4e4; color: #c62828; }
.det-na   { color: #ccc; font-size: 13px; }

.det-verified   { color: #2e7d32; font-weight: 700; font-size: 12px; }
.det-unverified { color: #c62828; font-weight: 700; font-size: 12px; }

/* Gender pill */
.det-gender-f { background:#fce4ec; color:#c2185b; padding:3px 12px; border-radius:12px; font-size:11px; font-weight:700; }
.det-gender-m { background:#e3f2fd; color:#1565c0; padding:3px 12px; border-radius:12px; font-size:11px; font-weight:700; }

/* Marital */
.det-married   { color:#5c6bc0; font-weight:700; }
.det-unmarried { color:#2e7d32; font-weight:700; }
.det-divorced  { color:#e53935; font-weight:700; }

/* Egg age valid */
.det-egg-ok   { background:#e6f7f5; color:#0f7b6c; padding:3px 12px; border-radius:12px; font-size:12px; font-weight:700; }

/* expense rows */
.exp-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}
.exp-row:last-child { border-bottom: none; }
.exp-lbl { color: #666; }
.exp-val { font-weight: 700; color: #333; }
.exp-approved { color: #1565c0; }
.exp-utr {
    font-family: monospace;
    font-size: 13px;
    font-weight: 700;
    color: #0f7b6c;
    background: #e6f7f5;
    padding: 6px 14px;
    border-radius: 7px;
    letter-spacing: .04em;
}

/* Not found */
.dp-not-found {
    text-align: center;
    padding: 80px 20px;
    color: #888;
}
.dp-not-found i { font-size: 48px; color: #ccc; margin-bottom: 16px; display: block; }
</style>

@if(!$d)
    <div class="dp-not-found">
        <i class="fa fa-exclamation-circle"></i>
        <h4>Donor Not Found</h4>
        <p>The ART-BNK ID "<strong>{{ htmlspecialchars($donor_id) }}</strong>" does not exist.</p>
        <a href="{{ route('report.art_bank_module') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
@else

    <!-- ===== PROFILE HEADER ===== -->
    <div class="dp-header">
        <div class="dp-avatar">
            <i class="fa fa-{{ ($d['gender']==='Female') ? 'female' : 'male' }}"></i>
        </div>
        <div class="dp-header-info">
            <h3>Donor Profile
                <span style="font-size:14px;opacity:.8;font-weight:400;margin-left:8px;">
                    {{ ($d['gender']==='Female') ? '♀' : '♂' }} {{ $d['gender'] }}
                </span>
            </h3>
            <p>
                <i class="fa fa-map-marker"></i> {{ $d['location'] }}
                &nbsp;&bull;&nbsp;
                <i class="fa fa-calendar"></i> Registered: {{ $d['reg_date'] }}
                &nbsp;&bull;&nbsp;
                <i class="fa fa-user-md"></i> Handled by: {!! detVal($d['handled_by']) !!}
            </p>
            <div style="margin-top:10px;">
                <a href="{{ route('report.art_bank_module') }}" class="dp-back-btn">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="dp-header-meta">
            <span class="dp-id-pill"><i class="fa fa-id-badge"></i> {{ $d['id'] }}</span>
            @php
            $sc = ['Active'=>'dp-s-active','Inactive'=>'dp-s-inactive','Pending'=>'dp-s-pending'];
            $sc_cls = $sc[$d['status']] ?? 'dp-s-pending';
            @endphp
            <span class="dp-status-pill {{ $sc_cls }}">{{ $d['status'] }}</span>
            @if($d['art_enrol'] !== '-')
            <span style="font-size:12px;opacity:.8;">
                <i class="fa fa-hashtag"></i> {{ $d['art_enrol'] }}
            </span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">

            <!-- ===== 1. BASIC INFORMATION ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-basic">
                    <i class="fa fa-info-circle"></i> Basic Information
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field">
                            <div class="dp-field-lbl">ART-BNK ID</div>
                            <div class="dp-field-val" style="color:var(--dp);font-family:monospace;">{{ $d['id'] }}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Register Date</div>
                            <div class="dp-field-val">{{ $d['reg_date'] }}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Donor Type</div>
                            <div class="dp-field-val">
                                @if($d['gender']==='Female')
                                    <span class="det-gender-f"><i class="fa fa-female"></i> Female</span>
                                @else
                                    <span class="det-gender-m"><i class="fa fa-male"></i> Male</span>
                                @endif
                            </div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Age</div>
                            <div class="dp-field-val">
                                {{ $d['age'] }} yrs
                                @if($d['gender']==='Female' && $d['egg_age']!=='-')
                                    @php $ea = (int)$d['egg_age']; @endphp
                                    @if($ea>=23 && $ea<=35)
                                        <span class="det-egg-ok" style="margin-left:4px;font-size:10px;">Eligible ✓</span>
                                    @else
                                        <span style="background:#fce4e4;color:#c62828;padding:2px 8px;border-radius:10px;font-size:10px;margin-left:4px;">Age Alert</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Location</div>
                            <div class="dp-field-val"><i class="fa fa-map-marker" style="color:var(--dac);margin-right:4px;"></i>{{ $d['location'] }}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Phone</div>
                            <div class="dp-field-val"><i class="fa fa-phone" style="color:#aaa;margin-right:4px;"></i>{{ $d['phone'] }}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">ART Enrolment No.</div>
                            <div class="dp-field-val" style="color:var(--dp);">{!! detVal($d['art_enrol']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Donor Handled By</div>
                            <div class="dp-field-val">{!! detVal($d['handled_by']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 2. MARITAL & FAMILY ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-family">
                    <i class="fa fa-users"></i> Marital &amp; Family
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field">
                            <div class="dp-field-lbl">Marital Status</div>
                            <div class="dp-field-val">
                                @php
                                $mc = ['Married'=>'det-married','Unmarried'=>'det-unmarried','Divorced'=>'det-divorced'];
                                $mc_cls = $mc[$d['marital']] ?? '';
                                @endphp
                                <span class="{{ $mc_cls }}">{{ $d['marital'] }}</span>
                            </div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Children</div>
                            <div class="dp-field-val">{!! detVal($d['children']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Child Age</div>
                            <div class="dp-field-val">{!! detVal($d['child_age']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Child Birth Certificate</div>
                            <div class="dp-field-val">{!! detDocBadge($d['child_cert']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Marriage Certificate</div>
                            <div class="dp-field-val">{!! detDocBadge($d['marriage_cert']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Marriage Photo</div>
                            <div class="dp-field-val">{!! detDocBadge($d['marriage_photo']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 3. DOCUMENTS & PROOFS ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-docs">
                    <i class="fa fa-folder-open"></i> Documents &amp; Proofs
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field">
                            <div class="dp-field-lbl">Aadhar Card</div>
                            <div class="dp-field-val">{!! detDocBadge($d['aadhar']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Aadhar Number</div>
                            <div class="dp-field-val" style="font-family:monospace;font-size:12px;">{!! detVal($d['aadhar_no']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Aadhar Verified</div>
                            <div class="dp-field-val">
                                @if($d['aadhar_ver'])
                                    <span class="det-verified"><i class="fa fa-check-circle"></i> Verified</span>
                                @else
                                    <span class="det-unverified"><i class="fa fa-times-circle"></i> Not Verified</span>
                                @endif
                            </div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Insurance Copy</div>
                            <div class="dp-field-val">{!! detDocBadge($d['insurance']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">PAN Card</div>
                            <div class="dp-field-val">{!! detDocBadge($d['pan']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 4. MEDICAL TESTS ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-medical">
                    <i class="fa fa-stethoscope"></i> Medical Tests
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        @php
                        $tests = [
                            'TV Scan'         => $d['tv_scan'],
                            'Serology'        => $d['serology'],
                            'HB Electrophoresis' => $d['hb_electrophoresis'],
                            'Semen'           => $d['semen'],
                            'BBT'             => $d['bbt'],
                            'TFT'             => $d['tft'],
                            'Cardiac Fitness' => $d['cardiac'],
                            'ECG'             => $d['ecg'],
                            'Informed Consent'=> $d['inf_consent'],
                        ];
                        @endphp
                        @foreach($tests as $lbl => $val)
                        <div class="dp-field">
                            <div class="dp-field-lbl">{{ $lbl }}</div>
                            <div class="dp-field-val">{!! detDocBadge($val) !!}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ===== 5. CONSENTS & BONDS ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-consent">
                    <i class="fa fa-file-text-o"></i> Consents &amp; Bonds
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field">
                            <div class="dp-field-lbl">Donor Consent</div>
                            <div class="dp-field-val">{!! detDocBadge($d['donor_consent']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Donor Bond</div>
                            <div class="dp-field-val">{!! detDocBadge($d['donor_bond']) !!}</div>
                        </div>
                        @if($d['gender']==='Female')
                        <div class="dp-field">
                            <div class="dp-field-lbl">Egg Donor Age (23–35)</div>
                            <div class="dp-field-val">
                                @if($d['egg_age']!=='-')
                                    @php $ea=(int)$d['egg_age']; @endphp
                                    <span class="{{ ($ea>=23&&$ea<=35)?'det-egg-ok':'det-miss' }}">
                                        {{ $d['egg_age'] }} yrs {{ ($ea>=23&&$ea<=35)?'✓':'✗' }}
                                    </span>
                                @else
                                    <span style="color:#bbb;">—</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ===== 6. OT DETAILS ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-ot">
                    <i class="fa fa-hospital-o"></i> OT Details
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field">
                            <div class="dp-field-lbl">IP Number</div>
                            <div class="dp-field-val" style="color:#4527a0;font-family:monospace;">{!! detVal($d['ip_no']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Anesthesiologist</div>
                            <div class="dp-field-val">{!! detVal($d['anesthesio']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Tubbing By (ECIID)</div>
                            <div class="dp-field-val" style="color:#4527a0;font-weight:700;">{!! detVal($d['tubbing']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">OT Technical</div>
                            <div class="dp-field-val">{!! detVal($d['ot_tech']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Pre Pick-Up Photo</div>
                            <div class="dp-field-val">{!! detDocBadge($d['pre_photo']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Post Pick-Up Photo</div>
                            <div class="dp-field-val">{!! detDocBadge($d['post_photo']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Pre Pick-Up Video</div>
                            <div class="dp-field-val">{!! detDocBadge($d['pre_video']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Post Pick-Up Video</div>
                            <div class="dp-field-val">{!! detDocBadge($d['post_video']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">OPU Summary</div>
                            <div class="dp-field-val">{!! detDocBadge($d['opu']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 7. PRESCRIPTION ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-rx">
                    <i class="fa fa-medkit"></i> Prescription
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field">
                            <div class="dp-field-lbl">Pre-Operative</div>
                            <div class="dp-field-val">{!! detDocBadge($d['rx_pre']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">During Operative</div>
                            <div class="dp-field-val">{!! detDocBadge($d['rx_during']) !!}</div>
                        </div>
                        <div class="dp-field">
                            <div class="dp-field-lbl">Post-Operative</div>
                            <div class="dp-field-val">{!! detDocBadge($d['rx_post']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /col-md-8 -->

        <div class="col-md-4">

            <!-- ===== RECIPIENT INFO ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-recip">
                    <i class="fa fa-user-plus"></i> Recipient Info
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field half">
                            <div class="dp-field-lbl">Recipient Name</div>
                            <div class="dp-field-val">{!! detVal($d['recip_name']) !!}</div>
                        </div>
                        <div class="dp-field half">
                            <div class="dp-field-lbl">Recipient MRD</div>
                            <div class="dp-field-val" style="color:#1565c0;font-weight:700;">{!! detVal($d['recip_mrd']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== LOCATION ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-loc">
                    <i class="fa fa-map-marker"></i> Location
                </div>
                <div class="dp-sec-body" style="padding:0;">
                    <div class="dp-row">
                        <div class="dp-field half">
                            <div class="dp-field-lbl">Zone</div>
                            <div class="dp-field-val" style="color:#00838f;">{!! detVal($d['zone']) !!}</div>
                        </div>
                        <div class="dp-field half">
                            <div class="dp-field-lbl">Center</div>
                            <div class="dp-field-val">{!! detVal($d['center']) !!}</div>
                        </div>
                        <div class="dp-field full">
                            <div class="dp-field-lbl">Procedure Done at Branch/Zone</div>
                            <div class="dp-field-val">{!! detVal($d['proc_branch']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== EXPENSES ===== -->
            <div class="dp-section">
                <div class="dp-sec-header dsh-expense">
                    <i class="fa fa-inr"></i> Expenses
                </div>
                <div class="dp-sec-body">
                    <div class="exp-row">
                        <span class="exp-lbl"><i class="fa fa-car" style="color:#aaa;margin-right:5px;"></i>Expected Travel</span>
                        <span class="exp-val">{!! detVal($d['exp_travel']) !!}</span>
                    </div>
                    <div class="exp-row">
                        <span class="exp-lbl"><i class="fa fa-cutlery" style="color:#aaa;margin-right:5px;"></i>Expected Food</span>
                        <span class="exp-val">{!! detVal($d['exp_food']) !!}</span>
                    </div>
                    <div class="exp-row">
                        <span class="exp-lbl"><i class="fa fa-car" style="color:#1565c0;margin-right:5px;"></i>Approved Travel</span>
                        <span class="exp-val exp-approved">{!! detVal($d['app_travel']) !!}</span>
                    </div>
                    <div class="exp-row">
                        <span class="exp-lbl"><i class="fa fa-cutlery" style="color:#1565c0;margin-right:5px;"></i>Approved Food</span>
                        <span class="exp-val exp-approved">{!! detVal($d['app_food']) !!}</span>
                    </div>
                    @if($d['utr'] !== '-')
                    <div style="margin-top:14px;">
                        <div class="dp-field-lbl" style="margin-bottom:6px;">UTR Number</div>
                        <div class="exp-utr"><i class="fa fa-bank" style="margin-right:6px;"></i>{{ $d['utr'] }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- ===== QUICK DOCUMENT CHECKLIST ===== -->
            <div class="dp-section">
                <div class="dp-sec-header" style="background:linear-gradient(90deg,#455a64,#607d8b);">
                    <i class="fa fa-check-square-o"></i> Document Checklist
                </div>
                <div class="dp-sec-body">
                    @php
                    $checklist = [
                        'Aadhar'            => $d['aadhar'],
                        'Insurance Copy'    => $d['insurance'],
                        'PAN Card'          => $d['pan'],
                        'Marriage Cert.'    => $d['marriage_cert'],
                        'Child Birth Cert.' => $d['child_cert'],
                        'HB Electrophoresis'=> $d['hb_electrophoresis'],
                        'Informed Consent'  => $d['inf_consent'],
                        'Donor Consent'     => $d['donor_consent'],
                        'Donor Bond'        => $d['donor_bond'],
                        'OPU Summary'       => $d['opu'],
                    ];
                    $done_count = count(array_filter($checklist, function($v) { return $v === 'done'; }));
                    $total_count = count($checklist);
                    $pct = round(($done_count / $total_count) * 100);
                    $bar_color = $pct >= 80 ? '#2e7d32' : ($pct >= 50 ? '#e65100' : '#c62828');
                    @endphp
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;">
                            <span style="font-weight:600;color:#555;">Completion</span>
                            <span style="font-weight:700;color:{{ $bar_color }};">{{ $pct }}%</span>
                        </div>
                        <div style="background:#f0f0f0;border-radius:10px;height:8px;overflow:hidden;">
                            <div style="width:{{ $pct }}%;height:100%;background:{{ $bar_color }};border-radius:10px;transition:width .5s;"></div>
                        </div>
                        <div style="font-size:11px;color:#aaa;margin-top:4px;">{{ $done_count }} of {{ $total_count }} documents complete</div>
                    </div>
                    @foreach($checklist as $lbl => $val)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f8f8f8;font-size:12px;">
                        <span style="color:#555;">{{ $lbl }}</span>
                        {!! detDocBadge($val) !!}
                    </div>
                    @endforeach
                </div>
            </div>

        </div><!-- /col-md-4 -->
    </div><!-- /row -->

@endif

</div><!-- /pc-content -->
</div><!-- /pc-container -->

<!-- ===== Can't Access Modal ===== -->
<div class="modal fade" id="detCantAccessModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document" style="max-width:340px;">
        <div class="modal-content" style="border-radius:12px;border:none;overflow:hidden;">
            <div class="modal-body text-center" style="padding:36px 28px 28px;">
                <div style="width:60px;height:60px;background:#fce4e4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fa fa-lock" style="font-size:26px;color:#c62828;"></i>
                </div>
                <h5 style="font-weight:700;color:#222;margin-bottom:8px;">Access Restricted</h5>
                <p style="color:#666;font-size:13px;margin-bottom:24px;">You don't have permission to view this document.</p>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="padding:7px 28px;border-radius:7px;font-weight:600;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function detCantAccess() {
    $('#detCantAccessModal').modal('show');
}
</script>
@include('superadmin.superadminfooter')
</body>
</html>