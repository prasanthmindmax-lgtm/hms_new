<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary UTR Master</title>
    @include('superadmin.superadminhead')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/bank-reconciliation.css') }}">
    <style>
        /* ────────────────────────────────────────
           BASE
        ──────────────────────────────────────── */
        body { background: #eef2f7; }

        /* ────────────────────────────────────────
           HEADER BANNER
        ──────────────────────────────────────── */
        .sm-banner {
            background: linear-gradient(135deg, #0a4a33 0%, #157a54 55%, #22a878 100%);
            padding: 1.5rem 2rem 3.5rem;
            position: relative;
            overflow: hidden;
        }
        .sm-banner::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 90% 10%, rgba(255,255,255,.07) 0%, transparent 50%),
                radial-gradient(ellipse at 5% 90%,  rgba(255,255,255,.04) 0%, transparent 45%);
            pointer-events: none;
        }
        .sm-banner-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 .25rem;
            letter-spacing: -.02em;
        }
        .sm-banner-sub { color: rgba(255,255,255,.7); font-size: .87rem; margin: 0; }

        .sm-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: .45rem 1.1rem;
            border-radius: 10px;
            font-size: .82rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all .18s;
        }
        .sm-nav-btn-white {
            background: #fff;
            color: #0a4a33;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
        }
        .sm-nav-btn-white:hover { background: #f0fdf4; color: #0a4a33; box-shadow: 0 4px 14px rgba(0,0,0,.18); }
        .sm-nav-btn-ghost {
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,.35);
        }
        .sm-nav-btn-ghost:hover { background: rgba(255,255,255,.28); color: #fff; border-color: rgba(255,255,255,.6); }

        /* ────────────────────────────────────────
           STATS STRIP  (overlapping the banner)
        ──────────────────────────────────────── */
        .sm-stats-row {
            margin-top: -2.2rem;
            position: relative;
            z-index: 10;
            padding: 0 1.5rem;
        }
        .sm-stat {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(10,74,51,.14), 0 1px 3px rgba(0,0,0,.07);
            padding: .9rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .sm-stat-ico {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .sm-stat-ico-a { background: #e6f4f0; color: #0a4a33; }
        .sm-stat-ico-b { background: #d1fae5; color: #059669; }
        .sm-stat-ico-c { background: #fef3c7; color: #d97706; }
        .sm-stat-ico-d { background: #f1f5f9; color: #64748b; }
        .sm-stat-num { font-size: 1.55rem; font-weight: 900; color: #111827; line-height: 1; }
        .sm-stat-lbl { font-size: .68rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-top: 3px; }

        /* ────────────────────────────────────────
           FILTER PANEL
        ──────────────────────────────────────── */
        .sm-filter-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 14px rgba(0,0,0,.06);
            padding: 1.4rem 1.6rem 1.2rem;
            margin-top: 1.25rem;
            overflow: visible; /* do not clip multi-select dropdowns */
        }
        .sm-filter-title {
            font-size: .68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #9ca3af;
            margin-bottom: 1rem;
        }

        /* Labels */
        .sm-fl {
            display: block;
            font-size: .71rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #4b5563;
            margin-bottom: 5px;
        }

        /* ── Custom multi-select trigger ── */
        .sm-ms-wrap { position: relative; }
        .sm-ms-trigger {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 12px 6px 10px;
            font-size: .84rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            transition: border-color .15s, background .15s, box-shadow .15s;
            user-select: none;
            box-sizing: border-box;
        }
        .sm-ms-trigger:hover {
            border-color: #22a878;
            background: #f0fdf8;
        }
        .sm-ms-trigger.open {
            border-color: #157a54;
            background: #f0fdf8;
            box-shadow: 0 0 0 3px rgba(34,168,120,.15);
        }
        .sm-ms-trigger-text {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #6b7280;
            font-size: .83rem;
        }
        .sm-ms-trigger-text.has-value { color: #111827; font-weight: 600; }
        .sm-ms-trigger-arrow {
            color: #94a3b8;
            font-size: .7rem;
            flex-shrink: 0;
            transition: transform .18s;
        }
        .sm-ms-trigger.open .sm-ms-trigger-arrow { transform: rotate(180deg); }
        .sm-ms-count {
            background: #157a54;
            color: #fff;
            border-radius: 99px;
            font-size: .63rem;
            font-weight: 800;
            padding: 1px 8px;
            flex-shrink: 0;
            line-height: 1.6;
        }

        /* ── Dropdown list ── */
        .sm-ms-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,.16);
            z-index: 2000;
            max-height: 240px;
            overflow-y: auto;
        }
        .sm-ms-dropdown.open { display: block; }
        .sm-ms-search-box { padding: 10px 10px 6px; border-bottom: 1px solid #f1f5f9; }
        .sm-ms-search-inp {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: .8rem;
            outline: none;
            background: #f8fafc;
        }
        .sm-ms-search-inp:focus { border-color: #157a54; background: #fff; }
        .sm-ms-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            font-size: .84rem;
            color: #374151;
            border-bottom: 1px solid #f8fafc;
            transition: background .1s;
        }
        .sm-ms-option:last-child { border-bottom: none; }
        .sm-ms-option:hover, .sm-ms-option.selected { background: #f0fdf8; }
        .sm-ms-option-all {
            font-weight: 700;
            color: #0a4a33;
            background: #f8fffe;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .sm-ms-option-all:hover { background: #ecfdf5; }
        .sm-ms-option input[type=checkbox] {
            accent-color: #157a54;
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            cursor: pointer;
        }
        .sm-ms-option label { cursor: pointer; flex: 1; margin: 0; }

        /* ── Native inputs ── */
        .sm-sel, .sm-inp {
            width: 100%;
            height: 38px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 10px;
            font-size: .84rem;
            color: #374151;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            box-sizing: border-box;
        }
        .sm-sel:focus, .sm-inp:focus {
            border-color: #157a54;
            box-shadow: 0 0 0 3px rgba(34,168,120,.15);
            background: #fff;
        }

        /* ── Active filter chips ── */
        .sm-chips-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding-top: 12px;
            margin-top: 2px;
            min-height: 0;
        }
        .sm-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #ecfdf5;
            border: 1.5px solid rgba(21,122,84,.25);
            color: #065f46;
            border-radius: 99px;
            font-size: .74rem;
            font-weight: 600;
            padding: 3px 8px 3px 10px;
            white-space: nowrap;
        }
        .sm-chip-x {
            background: none;
            border: none;
            padding: 0 0 0 3px;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            font-size: .7rem;
            line-height: 1;
        }
        .sm-chip-x:hover { color: #dc2626; }

        /* ── Action bar ── */
        .sm-action-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            padding-top: 14px;
            margin-top: 4px;
            border-top: 1.5px solid #f1f5f9;
        }
        .sm-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 10px;
            font-size: .83rem;
            font-weight: 700;
            padding: 8px 18px;
            cursor: pointer;
            border: none;
            transition: all .18s;
            white-space: nowrap;
        }
        .sm-btn-primary {
            background: linear-gradient(135deg, #0a4a33, #157a54);
            color: #fff;
            box-shadow: 0 2px 10px rgba(10,74,51,.28);
        }
        .sm-btn-primary:hover { opacity: .9; box-shadow: 0 4px 16px rgba(10,74,51,.38); }
        .sm-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #e2e8f0;
        }
        .sm-btn-secondary:hover { background: #e2e8f0; }
        .sm-export-wrap { margin-left: auto; display: flex; gap: 8px; }
        .sm-btn-csv {
            background: #fff;
            color: #157a54;
            border: 2px solid rgba(21,122,84,.3);
        }
        .sm-btn-csv:hover { background: #f0fdf8; border-color: #157a54; }
        .sm-btn-xlsx {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            box-shadow: 0 2px 8px rgba(5,150,105,.28);
        }
        .sm-btn-xlsx:hover { opacity: .9; }

        /* ────────────────────────────────────────
           TABLE CARD
        ──────────────────────────────────────── */
        .sm-tbl-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 14px rgba(0,0,0,.07);
            overflow: hidden;
            margin-top: 1.25rem;
        }
        .sm-tbl-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 1.4rem;
            border-bottom: 1.5px solid #f1f5f9;
            gap: 12px;
            flex-wrap: wrap;
        }
        .sm-tbl-title {
            font-size: .9rem;
            font-weight: 800;
            color: #0a4a33;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sm-tbl-controls { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .sm-perpage {
            height: 34px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 8px;
            font-size: .8rem;
            outline: none;
            background: #f8fafc;
            color: #374151;
        }
        .sm-perpage:focus { border-color: #157a54; }
        .sm-search-box {
            height: 34px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 12px;
            font-size: .82rem;
            outline: none;
            width: 200px;
            background: #f8fafc;
            color: #374151;
        }
        .sm-search-box:focus { border-color: #157a54; background: #fff; }

        .sm-tbl-scroll { overflow: auto; max-height: calc(100vh - 360px); min-height: 180px; }
        .sm-tbl { width: 100%; border-collapse: collapse; }
        .sm-tbl thead th {
            background: #f8fafb;
            position: sticky;
            top: 0;
            z-index: 3;
            padding: 10px 12px;
            font-size: .68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .sm-tbl tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: .82rem;
            color: #374151;
        }
        .sm-tbl tbody tr:hover td { background: #f8fffe; }
        .sm-tbl tbody tr:last-child td { border-bottom: none; }

        /* Cell pieces */
        .sm-utr { font-family: monospace; font-size: .77rem; min-width: 125px;background: #f0fdf4; color: #065f46; padding: 2px 7px; border-radius: 5px; word-break: break-all; display: inline-block; }
        .sm-stmt-id { font-family: monospace; font-size: .77rem; background: #eff6ff; color: #1d4ed8; padding: 2px 7px; border-radius: 5px; }
        .sm-file-name { font-size: .77rem; color: #374151; max-width: 180px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
        .sm-row-id { font-size: .72rem; color: #94a3b8; }
        .sm-by-name { font-size: .8rem; }
        .sm-by-user { font-size: .73rem; color: #9ca3af; }
        .sm-net { font-weight: 700; white-space: nowrap; }
        .sm-net-symbol { color: #157a54; }

        /* Badges */
        .sm-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: .71rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .sm-badge-ok   { background: #d1fae5; color: #065f46; border: 1px solid rgba(6,95,70,.18); }
        .sm-badge-warn { background: #fef3c7; color: #92400e; border: 1px solid rgba(146,64,14,.18); }
        .sm-badge-grey { background: #f1f5f9; color: #475569; border: 1px solid rgba(71,85,105,.14); }

        /* ────────────────────────────────────────
           PAGINATION
        ──────────────────────────────────────── */
        .sm-pager-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 1.4rem;
            border-top: 1.5px solid #f1f5f9;
            gap: 10px;
            flex-wrap: wrap;
        }
        .sm-pager-meta { font-size: .8rem; color: #94a3b8; font-weight: 500; }
        .sm-pager-btns { display: flex; gap: 4px; flex-wrap: wrap; }
        .sm-pager-btn {
            min-width: 34px;
            height: 34px;
            border: 2px solid #e2e8f0;
            background: #fff;
            color: #374151;
            border-radius: 9px;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }
        .sm-pager-btn:hover { border-color: #157a54; color: #157a54; background: #f0fdf8; }
        .sm-pager-btn.active { background: #157a54; color: #fff; border-color: #157a54; }
        .sm-pager-btn:disabled { opacity: .35; cursor: not-allowed; }
        .sm-pager-ellipsis { display: flex; align-items: center; padding: 0 4px; color: #94a3b8; font-size: .8rem; }

        /* ────────────────────────────────────────
           RESPONSIVE
        ──────────────────────────────────────── */
        @media (max-width: 991px) {
            .sm-banner { padding: 1.2rem 1rem 3rem; }
            .sm-stats-row { padding: 0 .75rem; }
        }
        @media (max-width: 767px) {
            .sm-filter-card { padding: 1rem; }
            .sm-export-wrap { margin-left: 0; }
            .sm-search-box { width: 150px; }
        }
    </style>
</head>
<body>
    <div class="page-loader"><div class="bar"></div></div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content" style="padding:0">

            {{-- ─── BANNER ─────────────────────────────── --}}
            <div class="sm-banner">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h1 class="sm-banner-title"><i class="bi bi-table me-2"></i>Salary UTR Master</h1>
                        <p class="sm-banner-sub">All rows from every salary Excel upload — filter by zone, branch, uploader &amp; status. Export exactly what you see.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('bank-reconciliation.index') }}" class="sm-nav-btn sm-nav-btn-white">
                            <i class="bi bi-arrow-left-circle"></i> Bank reconciliation
                        </a>
                        <a href="{{ route('bank-reconciliation.index') }}" class="sm-nav-btn sm-nav-btn-ghost">
                            <i class="bi bi-file-earmark-excel"></i> Salary UTR upload
                        </a>
                    </div>
                </div>
            </div>

            {{-- ─── STATS STRIP ─────────────────────────── --}}
            <div class="sm-stats-row">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="sm-stat">
                            <div class="sm-stat-ico sm-stat-ico-a"><i class="bi bi-list-columns-reverse"></i></div>
                            <div>
                                <div class="sm-stat-num" id="smStatTotal">—</div>
                                <div class="sm-stat-lbl">Total rows</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sm-stat">
                            <div class="sm-stat-ico sm-stat-ico-b"><i class="bi bi-check-circle-fill"></i></div>
                            <div>
                                <div class="sm-stat-num" id="smStatMatched">—</div>
                                <div class="sm-stat-lbl">Matched</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sm-stat">
                            <div class="sm-stat-ico sm-stat-ico-c"><i class="bi bi-question-circle-fill"></i></div>
                            <div>
                                <div class="sm-stat-num" id="smStatNotFound">—</div>
                                <div class="sm-stat-lbl">Not found</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sm-stat">
                            <div class="sm-stat-ico sm-stat-ico-d"><i class="bi bi-dash-circle"></i></div>
                            <div>
                                <div class="sm-stat-num" id="smStatUnmatched">—</div>
                                <div class="sm-stat-lbl">Unmatched</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── FILTER PANEL ────────────────────────── --}}
            <div class="px-3">
                <div class="sm-filter-card">
                    <div class="sm-filter-title"><i class="bi bi-funnel-fill me-1"></i>Filters</div>

                    <div class="row g-3 align-items-end" style="overflow: visible">

                        {{-- Zone --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="sm-fl"><i class="bi bi-map me-1"></i>Zone</label>
                            <div class="sm-ms-wrap">
                                <div class="sm-ms-trigger" id="smZoneTrigger" tabindex="0">
                                    <span class="sm-ms-trigger-text" id="smZoneText">All zones</span>
                                    <i class="bi bi-chevron-down sm-ms-trigger-arrow"></i>
                                </div>
                                <div class="sm-ms-dropdown" id="smZoneDropdown">
                                    <div class="sm-ms-search-box">
                                        <input type="text" class="sm-ms-search-inp" id="smZoneSearch" placeholder="Search zones…">
                                    </div>
                                    <div class="sm-ms-option sm-ms-option-all" data-ms="smZone" data-val="">
                                        <input type="checkbox" id="smZoneAll" class="sm-ms-all-chk">
                                        <label for="smZoneAll">All zones</label>
                                    </div>
                                    @foreach($zones as $z)
                                    <div class="sm-ms-option" data-ms="smZone" data-val="{{ $z->id }}" data-label="{{ $z->name }}">
                                        <input type="checkbox" id="smZ_{{ $z->id }}" value="{{ $z->id }}">
                                        <label for="smZ_{{ $z->id }}">{{ $z->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <select id="smZone" multiple class="d-none"></select>
                        </div>

                        {{-- Branch --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="sm-fl"><i class="bi bi-building me-1"></i>Branch</label>
                            <div class="sm-ms-wrap">
                                <div class="sm-ms-trigger" id="smBranchTrigger" tabindex="0">
                                    <span class="sm-ms-trigger-text" id="smBranchText">All branches</span>
                                    <i class="bi bi-chevron-down sm-ms-trigger-arrow"></i>
                                </div>
                                <div class="sm-ms-dropdown" id="smBranchDropdown">
                                    <div class="sm-ms-search-box">
                                        <input type="text" class="sm-ms-search-inp" id="smBranchSearch" placeholder="Search branches…">
                                    </div>
                                    <div class="sm-ms-option sm-ms-option-all" data-ms="smBranch" data-val="">
                                        <input type="checkbox" id="smBranchAll" class="sm-ms-all-chk">
                                        <label for="smBranchAll">All branches</label>
                                    </div>
                                    <div id="smBranchOptions">
                                        <div class="sm-ms-option" style="color:#9ca3af;font-size:.8rem;cursor:default;">
                                            <i class="bi bi-arrow-up-short me-1"></i>Select a zone first
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <select id="smBranch" multiple class="d-none"></select>
                        </div>

                        {{-- Uploaded by --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="sm-fl"><i class="bi bi-person me-1"></i>Uploaded by</label>
                            <select class="sm-sel" id="smUser">
                                <option value="">All users</option>
                                @foreach($uploadUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}@if($u->username) ({{ $u->username }})@endif</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Match status --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="sm-fl"><i class="bi bi-circle-half me-1"></i>Match status</label>
                            <div class="sm-ms-wrap">
                                <div class="sm-ms-trigger" id="smMatchTrigger" tabindex="0">
                                    <span class="sm-ms-trigger-text" id="smMatchText">All statuses</span>
                                    <i class="bi bi-chevron-down sm-ms-trigger-arrow"></i>
                                </div>
                                <div class="sm-ms-dropdown" id="smMatchDropdown">
                                    <div class="sm-ms-option sm-ms-option-all" data-ms="smMatch" data-val="">
                                        <input type="checkbox" id="smMatchAll" class="sm-ms-all-chk">
                                        <label for="smMatchAll">All statuses</label>
                                    </div>
                                    <div class="sm-ms-option" data-ms="smMatch" data-val="matched" data-label="Matched">
                                        <input type="checkbox" id="smMS_matched" value="matched">
                                        <label for="smMS_matched">✅ Matched</label>
                                    </div>
                                    <div class="sm-ms-option" data-ms="smMatch" data-val="not_found" data-label="Not found">
                                        <input type="checkbox" id="smMS_notfound" value="not_found">
                                        <label for="smMS_notfound">⚠️ Not found</label>
                                    </div>
                                    <div class="sm-ms-option" data-ms="smMatch" data-val="unmatched" data-label="Unmatched">
                                        <input type="checkbox" id="smMS_unmatched" value="unmatched">
                                        <label for="smMS_unmatched">○ Unmatched</label>
                                    </div>
                                </div>
                            </div>
                            <select id="smMatch" multiple class="d-none"></select>
                        </div>

                        {{-- Upload from --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="sm-fl"><i class="bi bi-calendar me-1"></i>Uploaded from</label>
                            <input type="date" class="sm-inp" id="smUpFrom">
                        </div>

                        {{-- Upload to --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="sm-fl"><i class="bi bi-calendar-check me-1"></i>Uploaded to</label>
                            <input type="date" class="sm-inp" id="smUpTo">
                        </div>

                    </div>{{-- /row --}}

                    {{-- Active filter chips --}}
                    <div class="sm-chips-row" id="smActiveChips"></div>

                    {{-- Action bar --}}
                    <div class="sm-action-bar">
                        <button type="button" class="sm-btn sm-btn-primary" id="smApply">
                            <i class="bi bi-funnel-fill"></i> Apply filters
                        </button>
                        <button type="button" class="sm-btn sm-btn-secondary" id="smReset">
                            <i class="bi bi-x"></i> Reset
                        </button>
                        <div class="sm-export-wrap">
                            <button type="button" class="sm-btn sm-btn-csv" id="smExportCsv">
                                <i class="bi bi-filetype-csv"></i> CSV
                            </button>
                            <button type="button" class="sm-btn sm-btn-xlsx" id="smExportXlsx">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </div>
                    </div>

                </div>{{-- /sm-filter-card --}}
            </div>

            {{-- ─── TABLE CARD ──────────────────────────── --}}
            <div class="px-3 pb-4">
                <div class="sm-tbl-card">

                    <div class="sm-tbl-topbar">
                        <h6 class="sm-tbl-title">
                            <i class="bi bi-table text-success"></i> Salary rows
                        </h6>
                        <div class="sm-tbl-controls">
                            <select class="sm-perpage" id="smPerPage" title="Rows per page">
                                <option value="25">25 / page</option>
                                <option value="50">50 / page</option>
                                <option value="100">100 / page</option>
                                <option value="200">200 / page</option>
                            </select>
                            <input type="text" class="sm-search-box" id="smSearch" placeholder="Search UTR, name, EC…">
                        </div>
                    </div>

                    <div class="sm-tbl-scroll">
                        <table class="sm-tbl" id="smTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Uploaded</th>
                                    <th>By</th>
                                    <th>UTR</th>
                                    <th>EC ID</th>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Branch</th>
                                    <th class="text-end">Net paid</th>
                                    <th>Status</th>
                                    <th>Stmt ID</th>
                                    <th>Stmt date</th>
                                </tr>
                            </thead>
                            <tbody id="smTbody">
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-5" style="font-size:.9rem;">
                                        <i class="bi bi-arrow-up-circle me-2 text-success"></i>
                                        Click <strong>Apply filters</strong> to load data.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="sm-pager-bar">
                        <div class="sm-pager-meta" id="smMeta">—</div>
                        <div class="sm-pager-btns" id="smPager"></div>
                    </div>

                </div>{{-- /sm-tbl-card --}}
            </div>

        </div>{{-- /pc-content --}}
    </div>{{-- /pc-container --}}

    @include('superadmin.superadminfooter')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        window.bankReconSalaryMaster = {
            dataUrl:   "{{ route('bank-reconciliation.salary-master.data') }}",
            exportUrl: "{{ route('bank-reconciliation.salary-master.export') }}",
            qfUrl:     "{{ route('bank-reconciliation.quick-filter-options') }}",
            indexUrl:  "{{ route('bank-reconciliation.index') }}",
        };
    </script>
    <script src="{{ asset('/assets/js/bank-reconciliation/salary-master.js') }}"></script>
</body>
</html>
