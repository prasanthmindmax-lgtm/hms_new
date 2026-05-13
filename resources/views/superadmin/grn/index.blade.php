<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
  body.grnpr-page { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f1f5f9; }
  body.grnpr-page .pc-container .pc-content {
    max-width: 100%; width: 100%; box-sizing: border-box;
    padding-left: 14px; padding-right: 16px;
  }

  .grnpr-shell { width: 100%; max-width: 100%; margin: 0; }

  /* ── Hero ── */
  .grnpr-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }
  .grnpr-hero {
    position: relative;
    padding: 22px 26px;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #4338ca 100%);
    color: #f8fafc;
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;
    overflow: hidden;
  }
  .grnpr-hero::before {
    content: '';
    position: absolute; top: -55%; right: -8%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(99,102,241,0.32) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
  }
  .grnpr-hero::after {
    content: '';
    position: absolute; bottom: -65%; left: 8%;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(56,189,248,0.18) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
  }
  .grnpr-hero-inner { position: relative; z-index: 1; }
  .grnpr-hero-title {
    margin: 0 0 6px; font-size: 1.45rem; font-weight: 800; letter-spacing: -0.02em;
    color: #f8fafc; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
  }
  .grnpr-hero-title i { color: #a5b4fc; }
  .grnpr-hero-sub { margin: 0; font-size: 0.85rem; color: rgba(226, 232, 240, 0.85); max-width: 56ch; }
  .grnpr-hero-actions { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: 10px; }
  .grnpr-btn-new {
    display: inline-flex; align-items: center; gap: 0.45rem;
    padding: 0.55rem 1.05rem; border-radius: 999px;
    font-size: 0.85rem; font-weight: 700; line-height: 1.2;
    color: #1e1b4b !important; text-decoration: none !important; white-space: nowrap;
    background: #f8fafc;
    border: 1px solid rgba(248,250,252,0.6);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .grnpr-btn-new:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(15,23,42,0.25); color: #1e1b4b !important; }

  /* ── Body ── */
  .grnpr-body { padding: 18px 22px 22px; }

  /* ── Stat cards ── */
  .grnpr-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 14px;
    margin-bottom: 18px;
  }
  .grnpr-stat {
    position: relative;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
    box-shadow: 0 4px 14px rgba(15,23,42,0.04);
    transition: transform 0.15s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    display: flex; flex-direction: column; gap: 4px;
  }
  .grnpr-stat:hover { transform: translateY(-2px); border-color: #c7d2fe; box-shadow: 0 10px 28px rgba(79,70,229,0.10); }
  .grnpr-stat-ic {
    position: absolute; top: 12px; right: 12px;
    width: 34px; height: 34px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    background: #eef2ff; color: #4f46e5; font-size: 1rem;
  }
  .grnpr-stat-lbl { font-size: 0.66rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b; }
  .grnpr-stat-num { font-size: 1.5rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; line-height: 1.1; }
  .grnpr-stat-hint { font-size: 0.7rem; color: #94a3b8; }
  .grnpr-stat--pending  .grnpr-stat-ic { background: #fef3c7; color: #b45309; }
  .grnpr-stat--pending  .grnpr-stat-num { color: #b45309; }
  .grnpr-stat--approved .grnpr-stat-ic { background: #dcfce7; color: #15803d; }
  .grnpr-stat--approved .grnpr-stat-num { color: #15803d; }
  .grnpr-stat--rejected .grnpr-stat-ic { background: #fee2e2; color: #b91c1c; }
  .grnpr-stat--rejected .grnpr-stat-num { color: #b91c1c; }

  /* ── Filter shell ── */
  .grnpr-filter {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px 16px;
    margin-bottom: 16px;
    box-shadow: 0 4px 16px rgba(15,23,42,0.04);
  }
  .grnpr-filter-head {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
    gap: 10px; margin-bottom: 12px;
  }
  .grnpr-filter-title {
    font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
    color: #475569; display: inline-flex; align-items: center; gap: 8px;
  }
  .grnpr-filter-title i { color: #4f46e5; }
  .grnpr-showing {
    background: #eef2ff; color: #3730a3;
    border-radius: 999px; padding: 4px 12px;
    font-size: 0.74rem; font-weight: 700;
  }
  .grnpr-showing strong { font-weight: 800; }

  .grnpr-filter-grid {
    display: grid; gap: 12px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }
  .grnpr-fg label {
    display: block;
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    color: #475569; margin-bottom: 4px;
  }
  .grnpr-fg label i { color: #4f46e5; }

  /* date input */
  .grnpr-date-wrap {
    display: flex; align-items: center; gap: 8px;
    height: calc(1.5em + 1.05rem + 2px);
    padding: 0 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.84rem; color: #1e293b;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .grnpr-date-wrap:hover, .grnpr-date-wrap:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); outline: none; }
  .grnpr-date-wrap .grnpr-date-lbl { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #1e293b; }
  .grnpr-date-wrap i { color: #4f46e5; }

  /* multi-select dropdown */
  .grnpr-dd { position: relative; }
  .grnpr-dd-input {
    width: 100%;
    height: calc(1.5em + 1.05rem + 2px);
    padding: 0.5rem 2.1rem 0.5rem 0.75rem;
    background: #fff !important;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.84rem; color: #1e293b;
    cursor: pointer;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  .grnpr-dd-input::placeholder { color: #94a3b8; }
  .grnpr-dd-input:focus { outline: none; border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
  .grnpr-dd::after {
    content: '\F282'; font-family: 'bootstrap-icons';
    position: absolute; right: 12px; top: calc(100% - (1.5em + 1.05rem + 2px) / 2 - 9px);
    color: #94a3b8; pointer-events: none; font-size: 0.85rem;
  }

  .grnpr-floating {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
    overflow: hidden;
    display: none;
    min-width: 240px;
    max-height: 360px;
    overflow-y: auto;
  }
  .grnpr-floating.show { display: block; }
  .grnpr-floating .grnpr-search-wrap {
    position: sticky; top: 0;
    padding: 8px 10px;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
  }
  .grnpr-floating .grnpr-search-input {
    width: 100%; padding: 6px 10px;
    font-size: 0.82rem;
    border: 1px solid #e2e8f0; border-radius: 8px;
  }
  .grnpr-floating .grnpr-search-input:focus { outline: none; border-color: #818cf8; box-shadow: 0 0 0 2px rgba(99,102,241,0.12); }
  .grnpr-floating .grnpr-actions {
    display: flex; justify-content: space-between; gap: 8px;
    padding: 6px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
  }
  .grnpr-floating .grnpr-btn-mini {
    font-size: 0.7rem; padding: 4px 10px; border-radius: 6px;
    font-weight: 700; cursor: pointer; line-height: 1;
    background: transparent; border: 1px solid transparent;
  }
  .grnpr-floating .grnpr-btn-all { color: #4f46e5; border-color: #c7d2fe; background: #eef2ff; }
  .grnpr-floating .grnpr-btn-clear { color: #475569; border-color: #e2e8f0; background: #fff; }
  .grnpr-floating .grnpr-list { padding: 4px 0; }
  .grnpr-floating .grnpr-opt {
    padding: 7px 12px;
    font-size: 0.83rem; color: #1e293b; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    border-bottom: 1px solid #f8fafc;
  }
  .grnpr-floating .grnpr-opt:hover { background: #f8fafc; }
  .grnpr-floating .grnpr-opt::before {
    content: ''; width: 14px; height: 14px;
    border: 1.5px solid #cbd5e1; border-radius: 4px;
    flex: 0 0 14px; transition: background 0.15s ease, border-color 0.15s ease;
  }
  .grnpr-floating .grnpr-opt.selected { background: #eef2ff; color: #3730a3; font-weight: 600; }
  .grnpr-floating .grnpr-opt.selected::before {
    background: #4f46e5 url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3E%3Cpath d='M13.485 4.515a1 1 0 0 0-1.414 0L6.5 10.086 3.929 7.515a1 1 0 0 0-1.414 1.414l3.293 3.293a1 1 0 0 0 1.414 0l6.263-6.293a1 1 0 0 0 0-1.414z'/%3E%3C/svg%3E") center/12px no-repeat;
    border-color: #4f46e5;
  }
  .grnpr-floating .grnpr-empty {
    padding: 12px; font-size: 0.8rem; color: #94a3b8; text-align: center; font-style: italic;
  }

  /* applied chips */
  .grnpr-chips {
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
    margin-top: 14px; padding-top: 12px;
    border-top: 1px dashed #e2e8f0;
  }
  .grnpr-chips-label {
    font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em;
  }
  .grnpr-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 999px;
    background: #4f46e5; color: #fff !important;
    font-size: 0.74rem; font-weight: 600;
    text-decoration: none !important;
    max-width: 280px;
  }
  .grnpr-chip i { font-size: 0.78rem; }
  .grnpr-chip span:first-of-type { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .grnpr-chip .grnpr-chip-x { font-size: 0.95rem; font-weight: 700; line-height: 1; opacity: 0.85; }
  .grnpr-chip:hover .grnpr-chip-x { opacity: 1; }
  .grnpr-chip--clear { background: #fff; color: #b91c1c !important; border: 1px solid #fecaca; margin-left: auto; }
  .grnpr-chip--clear:hover { background: #fef2f2; }

  /* toolbar (search + per-page) */
  .grnpr-toolbar {
    display: flex; align-items: end; flex-wrap: wrap; gap: 12px;
    margin-top: 14px;
  }
  .grnpr-search {
    position: relative;
    width: 100%;
    max-width: 420px;
  }
  .grnpr-search > i {
    position: absolute; top: 50%; left: 12px; transform: translateY(-50%);
    color: #94a3b8; font-size: 0.95rem;
  }
  .grnpr-search input {
    width: 100%;
    height: calc(1.5em + 1.05rem + 2px);
    padding: 0 12px 0 36px;
    border: 1px solid #e2e8f0; border-radius: 10px;
    background: #fff; font-size: 0.86rem;
  }
  .grnpr-search input:focus { outline: none; border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
  .grnpr-perpage { display: flex; align-items: center; gap: 8px; }
  .grnpr-perpage label { font-size: 0.74rem; font-weight: 700; color: #475569; margin: 0; }
  .grnpr-perpage select {
    height: calc(1.5em + 1.05rem + 2px);
    padding: 0 28px 0 10px;
    border: 1px solid #e2e8f0; border-radius: 10px;
    background: #fff; font-size: 0.84rem;
  }
  .grnpr-perpage select:focus { outline: none; border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }

  /* ──────────────────────────────────────────────────────────
     Table card — clean look matching Payment Request style
     (single dark thead bar, white rows, simple hover, pastel pills)
     ────────────────────────────────────────────────────────── */
  .grnpr-table-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow:
      0 1px 3px rgba(15, 23, 42, 0.06),
      0 10px 28px rgba(49, 46, 129, 0.06);
    overflow: hidden;
  }

  .grnpr-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-color: #cbd5e1 #f8fafc;
  }
  .grnpr-table-wrap::-webkit-scrollbar { height: 8px; }
  .grnpr-table-wrap::-webkit-scrollbar-track { background: #f8fafc; }
  .grnpr-table-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
  .grnpr-table-wrap::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

  .grnpr-table {
    width: 100%; min-width: 1080px;
    border-collapse: separate; border-spacing: 0;
    font-size: 0.875rem; color: #1e293b; background: #fff;
  }
  .grnpr-table th,
  .grnpr-table td { white-space: nowrap; vertical-align: middle; }

  /* ── Single dark navy thead (matches Payment Request) ── */
  .grnpr-table thead tr { background: #1a2232; }
  .grnpr-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: #1a2232;
    color: #c8d0e7;
    font-size: 0.68rem; font-weight: 800;
    letter-spacing: 0.05em; text-transform: uppercase;
    padding: 0.85rem 0.95rem; text-align: left;
    border-bottom: 0;
  }
  .grnpr-table thead th.text-end { text-align: right; }
  .grnpr-table thead th .grnpr-th-ic {
    color: #94a3b8; margin-right: 6px; font-size: 0.82rem; opacity: 0.85;
  }

  /* ── Body rows: clean white, simple hover ── */
  .grnpr-table tbody tr {
    background: #fff;
    transition: background 0.14s ease, box-shadow 0.14s ease;
  }
  .grnpr-table tbody tr:hover { background: #f8fafc; }
  .grnpr-table tbody td {
    padding: 0.85rem 0.95rem;
    border-bottom: 1px solid #e2e8f0;
    color: #1e293b;
    line-height: 1.5;
  }
  .grnpr-table tbody tr:last-child td { border-bottom: none; }

  /* Clickable rows — entire row opens the side-panel. We add a soft cursor cue,
     a stronger hover tint, and a subtle inset accent on the left so the user
     knows the row is interactive without it screaming for attention. */
  .grnpr-table tbody tr.grnpr-row-clickable { cursor: pointer; outline: none; }
  .grnpr-table tbody tr.grnpr-row-clickable:hover {
    background: #f1f5ff;
    box-shadow: inset 3px 0 0 0 #6366f1;
  }
  .grnpr-table tbody tr.grnpr-row-clickable:focus-visible {
    background: #eef2ff;
    box-shadow: inset 3px 0 0 0 #4f46e5, 0 0 0 2px rgba(99, 102, 241, 0.18);
  }
  /* Inside a clickable row, links/buttons/attachments must keep their own
     cursor and not be overridden by the row's pointer style. */
  .grnpr-table tbody tr.grnpr-row-clickable a,
  .grnpr-table tbody tr.grnpr-row-clickable button,
  .grnpr-table tbody tr.grnpr-row-clickable .grnpr-attach,
  .grnpr-table tbody tr.grnpr-row-clickable .grnpr-actions-tray,
  .grnpr-table tbody tr.grnpr-row-clickable .grnpr-edit-btn {
    cursor: pointer;
  }
  /* Read-only markers (status icon, em-dash, hourglass) keep the row cursor. */
  .grnpr-table tbody tr.grnpr-row-clickable .grnpr-status-icon,
  .grnpr-table tbody tr.grnpr-row-clickable .grnpr-edit-na,
  .grnpr-table tbody tr.grnpr-row-clickable .grnpr-actions-pending { cursor: pointer; }

  /* truncation utility — applied per column where useful */
  .grnpr-trunc {
    display: inline-block;
    max-width: 100%;
    overflow: hidden; text-overflow: ellipsis; vertical-align: middle;
  }
  .grnpr-cell-company { max-width: 180px; }
  .grnpr-cell-vendor  { max-width: 200px; }
  .grnpr-cell-recvby  { max-width: 160px; }
  .grnpr-cell-remark  { max-width: 220px; }

  /* Ref — simple indigo text link (matches Payment Request) */
  .grnpr-ref {
    display: inline-flex; align-items: center; gap: 5px;
    color: #4f46e5; font-weight: 700; font-size: 0.8125rem;
    font-variant-numeric: tabular-nums;
    text-decoration: none;
    transition: color 0.15s ease;
  }
  .grnpr-ref:hover { color: #4338ca; text-decoration: underline; }
  .grnpr-ref i { font-size: 0.82rem; opacity: 0.7; }

  /* Date text */
  .grnpr-date-txt {
    font-size: 0.82rem; color: #334155; font-weight: 500;
    font-variant-numeric: tabular-nums;
  }
  .grnpr-date-txt--muted { color: #64748b; font-weight: 500; }

  /* Company name (no avatar — clean like Payment Request) */
  .grnpr-company-name {
    font-weight: 600; color: #1e293b; font-size: 0.82rem;
  }

  /* ── Combined Location cell (zone pill on top, branch under) ── */
  .grnpr-loc {
    display: flex; flex-direction: column; align-items: flex-start; gap: 4px;
    min-width: 0; max-width: 200px;
  }
  .grnpr-zone-pill {
    display: inline-flex; align-items: center;
    max-width: 100%;
    padding: 0.28em 0.7em; border-radius: 6px;
    font-size: 0.66rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;
    line-height: 1.25;
    border: 1px solid transparent;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    background: #f1f5f9; color: #475569; border-color: #e2e8f0;
  }
  /* Soft pastel rotation — picked deterministically by zone name in the view */
  .grnpr-zone-pill--c1 { background: #ffedd5; color: #9a3412; border-color: #fed7aa; }
  .grnpr-zone-pill--c2 { background: #ccfbf1; color: #0f766e; border-color: #99f6e4; }
  .grnpr-zone-pill--c3 { background: #ede9fe; color: #6d28d9; border-color: #ddd6fe; }
  .grnpr-zone-pill--c4 { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
  .grnpr-zone-pill--c5 { background: #fce7f3; color: #be185d; border-color: #fbcfe8; }
  .grnpr-zone-pill--c6 { background: #fef9c3; color: #854d0e; border-color: #fde68a; }
  .grnpr-zone-pill--c7 { background: #d1fae5; color: #047857; border-color: #a7f3d0; }
  .grnpr-zone-pill--c8 { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

  .grnpr-branch-txt {
    font-size: 0.82rem; color: #475569; font-weight: 500;
    max-width: 100%;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }

  .grnpr-vendor-name {
    font-weight: 600; color: #1e293b; font-size: 0.82rem;
  }

  .grnpr-invoice-no {
    display: inline-block;
    font-family: 'SF Mono', Menlo, Consolas, 'JetBrains Mono', monospace;
    font-size: 0.76rem; font-weight: 600; color: #475569;
    background: #f8fafc; border: 1px solid #e2e8f0;
    padding: 2px 8px; border-radius: 6px;
    line-height: 1.4;
  }

  .grnpr-receiver {
    font-size: 0.82rem; color: #334155; font-weight: 500;
  }

  .grnpr-dash { color: #cbd5e1; font-style: italic; font-weight: 500; }

  /* Status pill — pastel (matches Payment Request) */
  .grnpr-status {
    display: inline-flex; align-items: center;
    font-size: 0.7rem; font-weight: 700;
    padding: 0.42em 0.85em; line-height: 1.25;
    color: #475569; background: #f1f5f9;
    border: 1px solid #e2e8f0; border-radius: 999px;
    letter-spacing: 0.03em; text-transform: none;
  }
  .grnpr-status--pending  { background: #fef3c7; color: #92400e; border-color: #fde68a; }
  .grnpr-status--approved { background: #d1fae5; color: #047857; border-color: #a7f3d0; }
  .grnpr-status--rejected { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

  /* Attachments — pastel chips */
  .grnpr-attach {
    display: inline-flex; align-items: center; gap: 6px;
  }
  .grnpr-attach-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 0.32em 0.7em; border-radius: 999px;
    font-size: 0.7rem; font-weight: 700;
    text-decoration: none !important;
    border: 1px solid transparent;
    line-height: 1.3;
    transition: background 0.14s ease, color 0.14s ease, border-color 0.14s ease;
  }
  .grnpr-attach-btn i { font-size: 0.78rem; }
  .grnpr-attach-btn--pdf { color: #b91c1c; background: #fee2e2; border-color: #fecaca; }
  .grnpr-attach-btn--pdf:hover { background: #fecaca; color: #7f1d1d; }
  .grnpr-attach-btn--vid { color: #0f766e; background: #ccfbf1; border-color: #99f6e4; }
  .grnpr-attach-btn--vid:hover { background: #99f6e4; color: #134e4a; }
  .grnpr-attach-btn.is-disabled { opacity: 0.55; cursor: not-allowed; }

  /* Remarks cell */
  .grnpr-remark {
    color: #64748b; font-size: 0.8rem;
    cursor: help;
  }

  /* ─────────────────────────────────────────────────────────────────
     Action buttons — premium dual-tier layout.
     Row controls are split across TWO dedicated columns:

        ┌──────────┐   ┌────────────────────────────────────────────┐
        │   Edit   │   │                  Actions                   │
        └──────────┘   └────────────────────────────────────────────┘
        ✎ icon          • Pending  → [✓ Approve | ✗ Reject] tray
                        • Approved → ✓ Approved status icon
                        • Rejected → ✗ Rejected status icon

     • The Edit icon appears ONLY while the GRN is pending and the user
       is allowed to edit; otherwise an em-dash placeholder keeps the
       column visually aligned.
     • The Actions column carries the audit decision controls while the
       record is pending; once approved or rejected, those controls are
       replaced by a clear status icon so the audit outcome stays in the
       same column at a glance.
     ───────────────────────────────────────────────────────────────── */
  .grnpr-actions {
    display: inline-flex; align-items: center; gap: 6px;
    flex-wrap: nowrap; justify-content: center;
  }

  /* ── Edit column · standalone icon button ─────────────────────── */
  .grnpr-table tbody td.grnpr-edit-cell { padding-left: 6px; padding-right: 6px; width: 64px; }
  .grnpr-edit-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
    border-radius: 9px;
    text-decoration: none !important;
    transition: background 0.14s ease, color 0.14s ease, transform 0.12s ease, box-shadow 0.14s ease, border-color 0.14s ease;
  }
  .grnpr-edit-btn i { font-size: 0.95rem; line-height: 1; }
  .grnpr-edit-btn:hover {
    background: #4f46e5; color: #fff; border-color: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79,70,229,0.25);
  }
  .grnpr-edit-btn:active { transform: translateY(0); }
  .grnpr-edit-btn:focus-visible {
    outline: 0; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.28);
  }
  .grnpr-edit-na {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    color: #cbd5e1; font-weight: 600; font-size: 1rem;
    cursor: help;
  }

  /* ── Actions column · audit decision tray (pending only) ──────── */
  .grnpr-actions-tray {
    display: inline-flex; align-items: center;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 3px;
    gap: 2px;
    position: relative;
  }
  .grnpr-actions-tray--audit .grnpr-iconbtn + .grnpr-iconbtn {
    margin-left: 2px;
    position: relative;
  }
  .grnpr-actions-tray--audit .grnpr-iconbtn + .grnpr-iconbtn::before {
    content: ''; position: absolute; left: -2px; top: 18%; bottom: 18%;
    width: 1px; background: #cbd5e1;
  }

  .grnpr-iconbtn {
    width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center;
    background: transparent; border: 0; border-radius: 7px;
    color: #64748b; cursor: pointer;
    text-decoration: none !important;
    transition: background 0.14s ease, color 0.14s ease, transform 0.12s ease, box-shadow 0.14s ease;
  }
  .grnpr-iconbtn i { font-size: 0.95rem; line-height: 1; }
  .grnpr-iconbtn:hover { transform: translateY(-1px); }
  .grnpr-iconbtn:active { transform: translateY(0); }
  .grnpr-iconbtn:focus-visible {
    outline: 0; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
  }
  .grnpr-iconbtn--approve       { color: #047857; }
  .grnpr-iconbtn--approve:hover { background: #fff; color: #065f46; box-shadow: 0 2px 6px rgba(4,120,87,0.22); }
  .grnpr-iconbtn--reject        { color: #b91c1c; }
  .grnpr-iconbtn--reject:hover  { background: #fff; color: #991b1b; box-shadow: 0 2px 6px rgba(185,28,28,0.22); }

  /* ── Actions column · status icons (after approve / reject) ───── */
  .grnpr-status-icon {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.015em;
    line-height: 1; white-space: nowrap;
  }
  .grnpr-status-icon i { font-size: 0.95rem; line-height: 1; }
  .grnpr-status-icon--approved {
    color: #065f46;
    background: #d1fae5;
    border: 1px solid #a7f3d0;
  }
  .grnpr-status-icon--approved i { color: #047857; }
  .grnpr-status-icon--rejected {
    color: #991b1b;
    background: #fee2e2;
    border: 1px solid #fecaca;
  }
  .grnpr-status-icon--rejected i { color: #b91c1c; }

  /* Pending viewer (no audit permission) — subtle hourglass marker */
  .grnpr-actions-pending {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    color: #94a3b8;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 999px;
  }
  .grnpr-actions-pending i { font-size: 0.95rem; line-height: 1; }

  .grnpr-table tbody td.grnpr-actions-cell { padding-left: 8px; padding-right: 14px; }

  @media (max-width: 991.98px) {
    .grnpr-edit-btn,
    .grnpr-edit-na,
    .grnpr-actions-pending { width: 28px; height: 28px; }
    .grnpr-iconbtn { width: 28px; height: 28px; }
    .grnpr-iconbtn i { font-size: 0.88rem; }
    .grnpr-status-icon { font-size: 0.68rem; padding: 4px 9px; }
    .grnpr-status-icon-txt { display: none; }
  }

  /* Empty state */
  .grnpr-empty { text-align: center; padding: 48px 16px; color: #64748b; white-space: normal; }
  .grnpr-empty-ic-wrap {
    width: 72px; height: 72px; border-radius: 50%;
    background: #f1f5f9;
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 12px;
  }
  .grnpr-empty-ic { font-size: 2.2rem; color: #94a3b8; }
  .grnpr-empty-title { font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-top: 2px; }
  .grnpr-empty-hint { font-size: 0.82rem; color: #94a3b8; margin: 6px 0 16px; }

  /* Pagination */
  .grnpr-pagination {
    padding: 12px 18px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
  }
  .grnpr-pagination .pagination { margin: 0; justify-content: flex-end; }
  .grnpr-pagination .pagination .page-link {
    border: 1px solid #e2e8f0; color: #475569;
    border-radius: 8px; margin: 0 2px;
    font-size: 0.82rem; font-weight: 600;
    transition: background 0.14s ease, color 0.14s ease, border-color 0.14s ease;
  }
  .grnpr-pagination .pagination .page-link:hover { background: #f8fafc; color: #4338ca; border-color: #c7d2fe; }
  .grnpr-pagination .pagination .active .page-link {
    background: #4f46e5; border-color: #4f46e5; color: #fff;
    box-shadow: 0 2px 6px rgba(79,70,229,0.25);
  }
  .grnpr-pagination .pagination .disabled .page-link {
    background: #fff; color: #cbd5e1; border-color: #e2e8f0;
  }

  /* ── Reviewer line — block element placed BELOW the status pill ── */
  .grnpr-audit-cell { display: flex; flex-direction: column; align-items: flex-start; gap: 6px; }
  .grnpr-reviewer {
    display: flex; align-items: center; gap: 5px;
    max-width: 240px;
    font-size: 0.72rem; color: #475569; line-height: 1.25;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  .grnpr-reviewer i { color: #64748b; font-size: 0.78rem; flex: 0 0 auto; }
  .grnpr-reviewer-label { color: #64748b; font-weight: 500; flex: 0 0 auto; }
  .grnpr-reviewer-name  {
    color: #1e293b; font-weight: 700;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    min-width: 0;
  }
  .grnpr-reviewer-time {
    color: #94a3b8; font-weight: 500; font-size: 0.68rem;
    margin-left: 2px; flex: 0 0 auto;
  }

  /* ── Modals (Reject, Approve, Preview) ── */
  .grnpr-modal-content { border: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 24px 60px rgba(15,23,42,0.25); }
  .grnpr-modal-header {
    background: linear-gradient(120deg, #b91c1c, #ef4444);
    color: #fff; border: 0; padding: 0.85rem 1.1rem;
  }
  .grnpr-modal-header .modal-title { color: #fff; font-weight: 700; }
  .grnpr-modal-header .btn-close { filter: invert(1) brightness(2); opacity: 0.9; }
  .grnpr-modal-header--approve { background: linear-gradient(120deg, #047857, #10b981); }
  .grnpr-modal-header--preview {
    background: linear-gradient(120deg, #1e293b, #334155);
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
  }

  /* Approve confirmation modal body */
  .grnpr-approve-lead { color: #334155; font-size: 0.9rem; margin-bottom: 14px; }
  .grnpr-approve-lead strong { color: #047857; }
  .grnpr-approve-meta {
    list-style: none; margin: 0; padding: 0;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    overflow: hidden;
  }
  .grnpr-approve-meta li {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 14px; font-size: 0.84rem;
    border-bottom: 1px solid #e2e8f0;
  }
  .grnpr-approve-meta li:last-child { border-bottom: 0; }
  .grnpr-approve-meta li span { color: #64748b; font-weight: 600; }
  .grnpr-approve-meta li strong { color: #0f172a; font-weight: 700; max-width: 65%; text-align: right; word-break: break-word; }

  /* Preview modal */
  .grnpr-preview-dialog { max-width: min(1100px, 96vw); }
  .grnpr-preview-content { background: #0b1220; }
  .grnpr-preview-tools { display: inline-flex; align-items: center; gap: 6px; }
  .grnpr-preview-tools .btn-light {
    background: rgba(255,255,255,0.92); color: #1e293b; border: 0;
    font-weight: 600; font-size: 0.78rem;
  }
  .grnpr-preview-tools .btn-light:hover { background: #fff; color: #0f172a; }
  .grnpr-preview-body { background: #0b1220; min-height: 60vh; max-height: 82vh; overflow: hidden; }
  .grnpr-preview-slot { width: 100%; height: 78vh; min-height: 60vh; display: flex; align-items: center; justify-content: center; }
  .grnpr-preview-slot iframe {
    width: 100%; height: 100%; border: 0; background: #fff;
  }
  .grnpr-preview-slot video {
    width: 100%; height: 100%; max-height: 78vh; background: #000; outline: 0;
  }
  .grnpr-preview-slot .grnpr-preview-fallback {
    color: #cbd5e1; padding: 30px 24px; text-align: center; font-size: 0.9rem;
  }
  @media (max-width: 767.98px) {
    .grnpr-preview-slot { height: 65vh; }
    .grnpr-preview-tools .btn-light { padding: 0.25rem 0.55rem; font-size: 0.72rem; }
  }

  /* daterangepicker theme override (no shared file) */
  .daterangepicker.grnpr-drp { font-family: inherit; border-radius: 12px; box-shadow: 0 16px 40px rgba(15,23,42,0.15); border: 1px solid #e2e8f0; }
  .daterangepicker.grnpr-drp td.active, .daterangepicker.grnpr-drp td.active:hover { background: #4f46e5; color: #fff; }
  .daterangepicker.grnpr-drp td.in-range { background: #eef2ff; color: #312e81; }
  .daterangepicker.grnpr-drp .btn-primary { background: #4f46e5; border: 0; }

  @media (max-width: 1199.98px) {
    .grnpr-table thead th, .grnpr-table tbody td { padding: 0.85rem 0.95rem; font-size: 0.82rem; }
    .grnpr-cell-company { max-width: 140px; }
    .grnpr-cell-vendor  { max-width: 160px; }
    .grnpr-cell-recvby  { max-width: 130px; }
    .grnpr-cell-remark  { max-width: 160px; }
  }
  @media (max-width: 767.98px) {
    .grnpr-table thead th, .grnpr-table tbody td { padding: 0.75rem 0.85rem; font-size: 0.78rem; }
  }
  @media (max-width: 575.98px) {
    .grnpr-hero { padding: 18px 18px; border-radius: 0; }
    .grnpr-body { padding: 14px 12px 16px; }
    .grnpr-hero-title { font-size: 1.2rem; }
  }

  /* ─────────────────────────────────────────────────────────────────
     View slide-over panel — premium, calm, document-style layout.
     Structure:
       ┌─ Header (sticky)  : ref + status pill + close/edit/open
       ├─ Body (scrolls)
       │   ├─ Audit accent strip (only when reviewed/pending)
       │   ├─ Section: Vendor & Invoice          ── 2-col label/value
       │   ├─ Section: Location & Receipt        ── 2-col label/value
       │   ├─ Section: Remarks                   ── only if non-empty
       │   └─ Section: Attachments               ── tile cards
       └─ Footer (sticky)  : Open full · Close
     ───────────────────────────────────────────────────────────────── */
  .grnpr-sp-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(3px);
    z-index: 1060;
    opacity: 0; transition: opacity .25s ease;
  }
  .grnpr-sp-overlay.show { display: block; opacity: 1; }

  .grnpr-sp {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: min(680px, 96vw);
    background: #fff;
    z-index: 1065;
    display: flex; flex-direction: column;
    box-shadow: -12px 0 56px rgba(15, 23, 42, 0.24);
    border-left: 1px solid #e5e7eb;
    transform: translateX(100%);
    transition: transform .3s cubic-bezier(.4, 0, .2, 1);
    overflow: hidden;
  }
  .grnpr-sp.show { transform: translateX(0); }

  /* ── Sticky header (clean white with indigo brand accent) ── */
  .grnpr-sp-hd {
    position: relative;
    padding: 18px 22px 14px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    flex-shrink: 0;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 12px;
  }
  .grnpr-sp-hd::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #4f46e5 0%, #6366f1 100%);
  }
  .grnpr-sp-hd-left { min-width: 0; flex: 1; padding-left: 4px; }
  .grnpr-sp-eyebrow {
    font-size: 0.66rem; font-weight: 800; color: #6366f1;
    text-transform: uppercase; letter-spacing: 0.1em;
    display: inline-flex; align-items: center; gap: 6px;
  }
  .grnpr-sp-eyebrow i { font-size: 0.85rem; color: #818cf8; }
  .grnpr-sp-title {
    margin-top: 4px;
    font-size: 1.4rem; font-weight: 800; color: #0f172a;
    letter-spacing: -0.01em; line-height: 1.2;
    word-break: break-word;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
  }
  .grnpr-sp-statuspill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.04em;
    border: 1px solid transparent;
    line-height: 1;
  }
  .grnpr-sp-statuspill .dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: currentColor;
  }
  .grnpr-sp-statuspill--pending  { background: #fef3c7; color: #92400e; border-color: #fde68a; }
  .grnpr-sp-statuspill--approved { background: #d1fae5; color: #047857; border-color: #a7f3d0; }
  .grnpr-sp-statuspill--rejected { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

  .grnpr-sp-submeta {
    margin-top: 8px;
    font-size: 0.76rem; color: #64748b; font-weight: 500;
    display: flex; flex-wrap: wrap; align-items: center; gap: 4px 14px;
  }
  .grnpr-sp-submeta-i { display: inline-flex; align-items: center; gap: 5px; }
  .grnpr-sp-submeta-i i { color: #94a3b8; font-size: 0.85rem; }

  .grnpr-sp-hd-actions { display: flex; gap: 6px; flex-shrink: 0; }
  .grnpr-sp-iconbtn {
    width: 34px; height: 34px;
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 8px;
    color: #475569; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    transition: background .15s, border-color .15s, color .15s;
    text-decoration: none !important;
    font-size: 0.95rem;
  }
  .grnpr-sp-iconbtn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
  }
  .grnpr-sp-iconbtn--close { color: #ef4444; border-color: #fecaca; background: #fef2f2; }
  .grnpr-sp-iconbtn--close:hover { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }

  /* ── Body ── */
  .grnpr-sp-body {
    flex: 1; overflow-y: auto;
    padding: 0;
    background: #fff;
  }

  /* Audit strip — high-visibility but tasteful, full-width band at the top of the body */
  .grnpr-sp-audit {
    margin: 0;
    padding: 14px 22px;
    display: flex; align-items: flex-start; gap: 12px;
    border-bottom: 1px solid #eef2f7;
  }
  .grnpr-sp-audit--pending  { background: #fffbeb; border-bottom-color: #fde68a; }
  .grnpr-sp-audit--approved { background: #f0fdf4; border-bottom-color: #bbf7d0; }
  .grnpr-sp-audit--rejected { background: #fef2f2; border-bottom-color: #fecaca; }
  .grnpr-sp-audit-ic {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem; background: #fff;
  }
  .grnpr-sp-audit--pending  .grnpr-sp-audit-ic { color: #c2410c; box-shadow: 0 0 0 1px #fed7aa; }
  .grnpr-sp-audit--approved .grnpr-sp-audit-ic { color: #047857; box-shadow: 0 0 0 1px #bbf7d0; }
  .grnpr-sp-audit--rejected .grnpr-sp-audit-ic { color: #b91c1c; box-shadow: 0 0 0 1px #fecaca; }
  .grnpr-sp-audit-text { min-width: 0; flex: 1; }
  .grnpr-sp-audit-line {
    font-size: 0.86rem; font-weight: 700; color: #0f172a;
    line-height: 1.35;
  }
  .grnpr-sp-audit-meta {
    margin-top: 2px;
    font-size: 0.76rem; color: #64748b; font-weight: 500;
  }
  .grnpr-sp-audit-reason {
    margin-top: 8px; padding: 8px 10px;
    background: #fff; border: 1px solid #fecaca; border-radius: 7px;
    font-size: 0.78rem; color: #991b1b; line-height: 1.5;
  }
  .grnpr-sp-audit-reason strong { color: #7f1d1d; font-weight: 700; }

  /* Section block */
  .grnpr-sp-sec {
    padding: 18px 22px;
    border-bottom: 1px solid #eef2f7;
  }
  .grnpr-sp-sec:last-child { border-bottom: 0; }
  .grnpr-sp-sec-h {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 14px;
    font-size: 0.7rem; font-weight: 800; color: #475569;
    text-transform: uppercase; letter-spacing: 0.08em;
  }
  .grnpr-sp-sec-h i {
    width: 24px; height: 24px; border-radius: 6px; flex-shrink: 0;
    background: #eef2ff; color: #4338ca;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
  }

  /* 2-column label/value grid (collapses on small panels) */
  .grnpr-sp-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px 22px;
  }
  .grnpr-sp-grid--full > * { grid-column: 1 / -1; }
  .grnpr-sp-field { min-width: 0; }
  .grnpr-sp-lbl {
    font-size: 0.68rem; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.06em;
    margin-bottom: 4px;
    display: inline-flex; align-items: center; gap: 5px;
  }
  .grnpr-sp-lbl i { font-size: 0.8rem; color: #cbd5e1; }
  .grnpr-sp-val {
    font-size: 0.9rem; color: #0f172a; font-weight: 600;
    line-height: 1.4; word-break: break-word;
  }
  .grnpr-sp-val--mono {
    font-variant-numeric: tabular-nums;
    letter-spacing: 0.01em;
  }
  .grnpr-sp-val--muted { color: #cbd5e1; font-weight: 500; font-style: italic; }

  /* Location value (zone pill + branch stacked OR inline depending on width) */
  #grnprSidePanel .grnpr-sp-loc {
    display: flex; flex-direction: column; align-items: flex-start; gap: 5px;
  }

  /* Remarks block */
  .grnpr-sp-remarks {
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    font-size: 0.86rem; color: #334155; line-height: 1.6;
    white-space: pre-wrap; word-break: break-word;
  }

  /* Attachments tiles */
  .grnpr-sp-files {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 10px;
  }
  .grnpr-sp-file {
    border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 12px 14px; background: #fff;
    display: flex; align-items: center; gap: 12px;
    text-align: left; cursor: pointer; width: 100%;
    transition: border-color .15s, transform .15s, box-shadow .15s;
  }
  .grnpr-sp-file:hover {
    border-color: #c7d2fe;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(15,23,42,0.06);
  }
  .grnpr-sp-file-ic {
    width: 38px; height: 38px; border-radius: 9px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
  }
  .grnpr-sp-file--pdf .grnpr-sp-file-ic { background: #fee2e2; color: #b91c1c; }
  .grnpr-sp-file--vid .grnpr-sp-file-ic { background: #ccfbf1; color: #0f766e; }
  .grnpr-sp-file-meta { min-width: 0; flex: 1; }
  .grnpr-sp-file-title {
    font-size: 0.85rem; font-weight: 700; color: #0f172a;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  .grnpr-sp-file-sub {
    margin-top: 2px; font-size: 0.72rem; color: #64748b;
    display: inline-flex; align-items: center; gap: 4px;
  }
  .grnpr-sp-file-cta {
    color: #4f46e5; font-size: 1.1rem; flex-shrink: 0;
    transition: transform .15s;
  }
  .grnpr-sp-file:hover .grnpr-sp-file-cta { transform: translateX(2px); }
  .grnpr-sp-empty {
    padding: 14px; color: #94a3b8; font-style: italic;
    font-size: 0.82rem; text-align: center;
    background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 9px;
  }

  /* Sticky footer */
  .grnpr-sp-ft {
    flex-shrink: 0;
    padding: 12px 22px;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
    display: flex; justify-content: flex-end; gap: 8px;
  }
  .grnpr-sp-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem; font-weight: 700;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569; cursor: pointer;
    text-decoration: none !important;
    transition: background .14s, color .14s, border-color .14s;
  }
  .grnpr-sp-btn:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }
  .grnpr-sp-btn--primary {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #fff; border-color: #4338ca;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
  }
  .grnpr-sp-btn--primary:hover {
    background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
    color: #fff; border-color: #3730a3;
  }

  @media (max-width: 575.98px) {
    .grnpr-sp { width: 100vw; }
    .grnpr-sp-hd { padding: 14px 16px 12px; }
    .grnpr-sp-title { font-size: 1.18rem; }
    .grnpr-sp-audit, .grnpr-sp-sec { padding-left: 16px; padding-right: 16px; }
    .grnpr-sp-grid { grid-template-columns: 1fr; gap: 12px; }
    .grnpr-sp-ft { padding: 10px 16px; }
  }
</style>

<body class="grnpr-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    @php
      $allowedStatuses = [
        \App\Models\GrnRecord::STATUS_PENDING,
        \App\Models\GrnRecord::STATUS_APPROVED,
        \App\Models\GrnRecord::STATUS_REJECTED,
      ];

      $grnpr_int_list = function ($v): array {
        if ($v === null || $v === '') return [];
        $items = is_array($v) ? $v : explode(',', (string) $v);
        $out = [];
        foreach ($items as $i) {
          $n = (int) trim((string) $i);
          if ($n > 0) $out[$n] = true;
        }
        return array_keys($out);
      };
      $grnpr_str_list = function ($v, array $allow = []): array {
        if ($v === null || $v === '') return [];
        $items = is_array($v) ? $v : explode(',', (string) $v);
        $out = [];
        foreach ($items as $i) {
          $s = trim((string) $i);
          if ($s === '') continue;
          if ($allow && ! in_array($s, $allow, true)) continue;
          $out[$s] = true;
        }
        return array_keys($out);
      };

      $selCompanyIds = $grnpr_int_list(request('company_id'));
      $selZoneIds    = $grnpr_int_list(request('zone_id'));
      $selBranchIds  = $grnpr_int_list(request('branch_id'));
      $selVendorIds  = $grnpr_int_list(request('vendor_id'));
      $selStatuses   = $grnpr_str_list(request('status'), $allowedStatuses);

      $df = request('date_from');
      $dt = request('date_to');
      $dateLabel = 'All dates';
      if ($df && $dt) {
        try {
          $dateLabel = \Carbon\Carbon::parse($df)->format('M j, Y').' – '.\Carbon\Carbon::parse($dt)->format('M j, Y');
        } catch (\Throwable $e) {
          $dateLabel = trim(($df ?: '…').' – '.($dt ?: '…'));
        }
      } elseif ($df || $dt) {
        $dateLabel = trim(($df ?: '…').' – '.($dt ?: '…'));
      }

      $vendorLabel = function ($v) {
        $t = trim((string) (($v->display_name ?: '') ?: ($v->company_name ?? '')));
        return $t !== '' ? $t : 'Vendor #'.$v->id;
      };

      $joinDisp = function (array $ids, $collection, string $idKey, string $labelKey, string $emptyLabel, ?\Closure $fmt = null) {
        if ($ids === []) return $emptyLabel;
        $labels = $collection->whereIn($idKey, $ids)
          ->map(function ($x) use ($labelKey, $fmt) { return $fmt ? $fmt($x) : (string) $x->$labelKey; })
          ->filter()->values();
        return $labels->isNotEmpty() ? $labels->implode(', ') : $emptyLabel;
      };

      $companyDisp = $joinDisp($selCompanyIds, $companies, 'id', 'company_name', 'All companies');
      $zoneDisp    = $joinDisp($selZoneIds,    $zones,     'id', 'name',         'All zones');
      $branchDisp  = $joinDisp($selBranchIds,  $branches,  'id', 'name',         'All branches');
      $vendorDisp  = $selVendorIds === []
        ? 'All vendors'
        : $vendors->filter(fn ($v) => in_array((int) $v->id, $selVendorIds, true))->map($vendorLabel)->implode(', ');
      $statusDisp  = $selStatuses === []
        ? 'All statuses'
        : collect($selStatuses)->map(fn ($k) => $statusLabels[$k] ?? \App\Models\GrnRecord::statusLabel($k))->implode(', ');

      $searchTrim = trim((string) request('universal_search', ''));
      $hasChips = ($df && $dt)
        || $selCompanyIds !== []
        || $selZoneIds !== []
        || $selBranchIds !== []
        || $selVendorIds !== []
        || $selStatuses !== []
        || $searchTrim !== '';

      $chipUrl = function (array $without) {
        $keys = array_merge($without, ['page']);
        return route('grn.index', request()->except($keys));
      };

      $rowFrom = $records->firstItem();
      $rowTo   = $records->lastItem();
      $rowRange = ($rowFrom && $rowTo) ? ($rowFrom.'–'.$rowTo) : '0';

      $stats = $stats ?? ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
      $perPageChoices = $grnPerPageChoices ?? [10, 15, 25, 50, 100];
      $perPage = (int) ($grnPerPage ?? 25);
    @endphp

    <div class="grnpr-shell">
      <div class="grnpr-card">
        <header class="grnpr-hero">
          <div class="grnpr-hero-inner">
            <h1 class="grnpr-hero-title"><i class="bi bi-box-seam"></i> GRN dashboard</h1>
          </div>
          <div class="grnpr-hero-actions">
            <a href="{{ route('grn.create') }}" class="grnpr-btn-new"><i class="bi bi-plus-lg"></i> New GRN</a>
          </div>
        </header>

        <div class="grnpr-body">
          {{-- ─── Stats ─── --}}
          <div class="grnpr-stats" role="group" aria-label="GRN summary">
            <div class="grnpr-stat">
              <span class="grnpr-stat-ic"><i class="bi bi-layers"></i></span>
              <span class="grnpr-stat-lbl">Total</span>
              <span class="grnpr-stat-num">{{ number_format($stats['total']) }}</span>
              <span class="grnpr-stat-hint">All matching records</span>
            </div>
            <div class="grnpr-stat grnpr-stat--pending">
              <span class="grnpr-stat-ic"><i class="bi bi-hourglass-split"></i></span>
              <span class="grnpr-stat-lbl">Pending</span>
              <span class="grnpr-stat-num">{{ number_format($stats['pending']) }}</span>
              <span class="grnpr-stat-hint">Awaiting auditor</span>
            </div>
            <div class="grnpr-stat grnpr-stat--approved">
              <span class="grnpr-stat-ic"><i class="bi bi-check2-circle"></i></span>
              <span class="grnpr-stat-lbl">Approved</span>
              <span class="grnpr-stat-num">{{ number_format($stats['approved']) }}</span>
              <span class="grnpr-stat-hint">Cleared audit</span>
            </div>
            <div class="grnpr-stat grnpr-stat--rejected">
              <span class="grnpr-stat-ic"><i class="bi bi-x-circle"></i></span>
              <span class="grnpr-stat-lbl">Rejected</span>
              <span class="grnpr-stat-num">{{ number_format($stats['rejected']) }}</span>
              <span class="grnpr-stat-hint">Returned with reason</span>
            </div>
          </div>

          {{-- ─── Filter shell ─── --}}
          <div class="grnpr-filter">
            <div class="grnpr-filter-head">
              <span class="grnpr-filter-title"><i class="bi bi-sliders2"></i> Refine list</span>
              <span class="grnpr-showing">Rows <strong>{{ $rowRange }}</strong> of <strong>{{ $records->total() }}</strong></span>
            </div>

            <form method="get" action="{{ route('grn.index') }}" id="grnprFilterForm" autocomplete="off">
              <input type="hidden" name="date_from" id="grnprDateFrom" value="{{ request('date_from') }}">
              <input type="hidden" name="date_to" id="grnprDateTo" value="{{ request('date_to') }}">

              <div class="grnpr-array-hiddens" data-array-name="company_id" hidden>
                @foreach ($selCompanyIds as $cid) <input type="hidden" name="company_id[]" value="{{ $cid }}"> @endforeach
              </div>
              <div class="grnpr-array-hiddens" data-array-name="zone_id" hidden>
                @foreach ($selZoneIds as $zid) <input type="hidden" name="zone_id[]" value="{{ $zid }}"> @endforeach
              </div>
              <div class="grnpr-array-hiddens" data-array-name="branch_id" hidden>
                @foreach ($selBranchIds as $bid) <input type="hidden" name="branch_id[]" value="{{ $bid }}"> @endforeach
              </div>
              <div class="grnpr-array-hiddens" data-array-name="vendor_id" hidden>
                @foreach ($selVendorIds as $vid) <input type="hidden" name="vendor_id[]" value="{{ $vid }}"> @endforeach
              </div>
              <div class="grnpr-array-hiddens" data-array-name="status" hidden>
                @foreach ($selStatuses as $sk) <input type="hidden" name="status[]" value="{{ $sk }}"> @endforeach
              </div>

              <div class="grnpr-filter-grid">
                <div class="grnpr-fg">
                  <label>Date range</label>
                  <div class="grnpr-date-wrap" id="grnprReportRange" role="button" tabindex="0">
                    <i class="bi bi-calendar3"></i>
                    <span class="grnpr-date-lbl" id="grnprDateLabel">{{ $dateLabel }}</span>
                    <i class="bi bi-caret-down-fill" style="font-size:0.7rem;"></i>
                  </div>
                </div>

                <div class="grnpr-fg">
                  <label>Company</label>
                  <div class="grnpr-dd" data-filter-param="company_id" data-empty-label="All companies">
                    <input type="text" class="grnpr-dd-input" placeholder="Select company" value="{{ $companyDisp }}" readonly>
                    <template>
                      @foreach ($companies as $co)
                        <div class="grnpr-opt @if(in_array((int) $co->id, $selCompanyIds, true)) selected @endif" data-id="{{ $co->id }}" data-label="{{ $co->company_name }}">{{ $co->company_name }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>

                <div class="grnpr-fg">
                  <label>Zone</label>
                  <div class="grnpr-dd" data-filter-param="zone_id" data-empty-label="All zones">
                    <input type="text" class="grnpr-dd-input" placeholder="Select zone" value="{{ $zoneDisp }}" readonly>
                    <template>
                      @foreach ($zones as $z)
                        <div class="grnpr-opt @if(in_array((int) $z->id, $selZoneIds, true)) selected @endif" data-id="{{ $z->id }}" data-label="{{ $z->name }}">{{ $z->name }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>

                <div class="grnpr-fg">
                  <label>Branch</label>
                  <div class="grnpr-dd" data-filter-param="branch_id" data-empty-label="All branches">
                    <input type="text" class="grnpr-dd-input" placeholder="Select branch" value="{{ $branchDisp }}" readonly>
                    <template>
                      @foreach ($branches as $b)
                        <div class="grnpr-opt @if(in_array((int) $b->id, $selBranchIds, true)) selected @endif" data-id="{{ $b->id }}" data-label="{{ $b->name }}" data-zone="{{ $b->zone_id }}">{{ $b->name }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>

                <div class="grnpr-fg">
                  <label>Vendor</label>
                  <div class="grnpr-dd" data-filter-param="vendor_id" data-empty-label="All vendors">
                    <input type="text" class="grnpr-dd-input" placeholder="Select vendor" value="{{ $vendorDisp }}" readonly>
                    <template>
                      @foreach ($vendors as $v)
                        @php $vL = $vendorLabel($v); @endphp
                        <div class="grnpr-opt @if(in_array((int) $v->id, $selVendorIds, true)) selected @endif" data-id="{{ $v->id }}" data-label="{{ $vL }}">{{ $vL }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>

                <div class="grnpr-fg">
                  <label>Audit status</label>
                  <div class="grnpr-dd" data-filter-param="status" data-empty-label="All statuses">
                    <input type="text" class="grnpr-dd-input" placeholder="Select status" value="{{ $statusDisp }}" readonly>
                    <template>
                      @foreach ($statusLabels as $sk => $sl)
                        <div class="grnpr-opt @if(in_array($sk, $selStatuses, true)) selected @endif" data-id="{{ $sk }}" data-label="{{ $sl }}">{{ $sl }}</div>
                      @endforeach
                    </template>
                  </div>
                </div>
              </div>

              @if($hasChips)
                <div class="grnpr-chips">
                  <span class="grnpr-chips-label">Filters:</span>
                  @if($df && $dt)
                    <a href="{{ $chipUrl(['date_from', 'date_to']) }}" class="grnpr-chip"><i class="bi bi-calendar3"></i><span>{{ $dateLabel }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  @if($selCompanyIds !== [])
                    <a href="{{ $chipUrl(['company_id']) }}" class="grnpr-chip"><span>{{ $companyDisp }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  @if($selZoneIds !== [])
                    <a href="{{ $chipUrl(['zone_id']) }}" class="grnpr-chip"><span>{{ $zoneDisp }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  @if($selBranchIds !== [])
                    <a href="{{ $chipUrl(['branch_id']) }}" class="grnpr-chip"><span>{{ $branchDisp }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  @if($selVendorIds !== [])
                    <a href="{{ $chipUrl(['vendor_id']) }}" class="grnpr-chip"><span>{{ $vendorDisp }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  @if($selStatuses !== [])
                    <a href="{{ $chipUrl(['status']) }}" class="grnpr-chip"><span>{{ $statusDisp }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  @if($searchTrim !== '')
                    <a href="{{ $chipUrl(['universal_search']) }}" class="grnpr-chip"><i class="bi bi-search"></i><span>{{ \Illuminate\Support\Str::limit($searchTrim, 48) }}</span><span class="grnpr-chip-x">&times;</span></a>
                  @endif
                  <a href="{{ route('grn.index') }}" class="grnpr-chip grnpr-chip--clear">Clear all</a>
                </div>
              @endif

              <div class="row align-items-end justify-content-between grnpr-toolbar mt-3 g-2 g-md-3 mx-0 w-100">
                <div class="col-12 col-md-4">
                  <div class="grnpr-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="universal_search" id="grnprSearch" maxlength="100"
                           placeholder="Search GRN records"
                           value="{{ request('universal_search') }}">
                  </div>
                </div>
                <div class="col-12 col-md-auto">
                  <div class="grnpr-perpage">
                    <label for="grnprPerPage">Rows per page</label>
                    <select id="grnprPerPage" name="per_page">
                      @foreach ($perPageChoices as $pp)
                        <option value="{{ $pp }}" @selected($perPage === (int) $pp)>{{ $pp }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>

          {{-- ─── Table card ─── --}}
          <div class="grnpr-table-card">
            <div class="grnpr-table-wrap">
              <table class="grnpr-table">
                <thead>
                  <tr>
                    <th><i class="bi bi-hash grnpr-th-ic"></i>Ref</th>
                    <th><i class="bi bi-calendar3 grnpr-th-ic"></i>Created</th>
                    <th><i class="bi bi-buildings grnpr-th-ic"></i>Company</th>
                    <th><i class="bi bi-geo-alt grnpr-th-ic"></i>Location</th>
                    <th><i class="bi bi-person-badge grnpr-th-ic"></i>Vendor</th>
                    <th><i class="bi bi-receipt grnpr-th-ic"></i>Invoice No.</th>
                    <th><i class="bi bi-calendar-event grnpr-th-ic"></i>Invoice Date</th>
                    <th><i class="bi bi-truck grnpr-th-ic"></i>Received Date</th>
                    <th><i class="bi bi-person-check grnpr-th-ic"></i>Received by</th>
                    <th><i class="bi bi-paperclip grnpr-th-ic"></i>Files</th>
                    <th><i class="bi bi-shield-check grnpr-th-ic"></i>Audit</th>
                    <th class="text-center"><i class="bi bi-pencil-square grnpr-th-ic"></i>Edit</th>
                    <th class="text-center"><i class="bi bi-gear grnpr-th-ic"></i>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($records as $r)
                    @php
                      $canEditRow = $r->isPending() && (
                          ($canAudit ?? false)
                          || (int) $r->created_by === (int) auth()->id()
                      );
                      $st = $r->audit_approval_status ?: 'pending';
                    @endphp
                    <tr class="is-{{ $st }} grnpr-row-clickable" data-grnpr-view-target="grnpr-data-{{ $r->id }}" tabindex="0" role="button" aria-label="Open GRN {{ $r->grn_number }}">
                      {{-- Ref --}}
                      <td>
                        <a class="grnpr-ref" href="{{ route('grn.show', $r) }}" title="View {{ $r->grn_number }}">
                          <i class="bi bi-box-seam"></i>{{ $r->grn_number }}
                        </a>
                      </td>

                      {{-- Created --}}
                      <td>
                        @if($r->created_at)
                          <span class="grnpr-date-txt" title="{{ $r->created_at->format('d M Y · h:i A') }}">{{ $r->created_at->format('d M Y') }}</span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Company --}}
                      <td>
                        @if($r->company_name)
                          <span class="grnpr-company grnpr-cell-company" title="{{ $r->company_name }}">
                            <span class="grnpr-company-name grnpr-trunc">{{ $r->company_name }}</span>
                          </span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Location (zone pill on top, branch below) --}}
                      <td>
                        @if($r->zone_name || $r->branch_name)
                          @php
                            // Deterministic colour per zone so each zone reads as a different pastel.
                            $zoneKey = strtolower(trim((string) $r->zone_name));
                            $zoneIdx = $zoneKey === '' ? 0 : (crc32($zoneKey) % 8) + 1;
                            $zoneTip = $r->zone_name ?: '';
                            $branchTip = $r->branch_name ?: '';
                            $locTip = trim($zoneTip.($zoneTip && $branchTip ? ' · ' : '').$branchTip);
                          @endphp
                          <div class="grnpr-loc" title="{{ $locTip }}">
                            @if($r->zone_name)
                              <span class="grnpr-zone-pill grnpr-zone-pill--c{{ $zoneIdx }}">{{ $r->zone_name }}</span>
                            @endif
                            @if($r->branch_name)
                              <span class="grnpr-branch-txt">{{ $r->branch_name }}</span>
                            @endif
                          </div>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Vendor --}}
                      <td>
                        @if($r->vendor_name)
                          <span class="grnpr-vendor-name grnpr-cell-vendor grnpr-trunc" title="{{ $r->vendor_name }}">{{ $r->vendor_name }}</span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Invoice # --}}
                      <td>
                        @if($r->invoice_number)
                          <span class="grnpr-invoice-no" title="Invoice number">{{ $r->invoice_number }}</span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Invoice date --}}
                      <td>
                        @if($r->invoice_date)
                          <span class="grnpr-date-txt grnpr-date-txt--muted">{{ $r->invoice_date->format('d M Y') }}</span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Received date --}}
                      <td>
                        @if($r->received_date)
                          <span class="grnpr-date-txt grnpr-date-txt--muted">{{ $r->received_date->format('d M Y') }}</span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>
                      <td>
                        @php
                          $receiverName = trim((string) $r->received_by);
                          if ($receiverName !== '') {
                              $cleaned = preg_replace('/\s*(?:[\(\[]\s*|[-·]\s*)(?:EMP(?:LOYEE)?[-\s]?)?[A-Za-z0-9_-]+\s*[\)\]]?\s*$/iu', '', $receiverName);
                              if (is_string($cleaned)) { $receiverName = $cleaned; }
                              $cleaned = preg_replace('/(?<=\D)\s*\d{3,}\s*$/u', '', $receiverName);
                              if (is_string($cleaned) && trim($cleaned) !== '') { $receiverName = $cleaned; }
                              $receiverName = trim($receiverName);
                          }
                        @endphp
                        @if($receiverName !== '')
                          <span class="grnpr-receiver grnpr-cell-recvby grnpr-trunc" title="{{ $receiverName }}">{{ $receiverName }}</span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Files (PDF + GPS — open in preview modal) --}}
                      <td>
                        @if($r->invoice_copy_path || $r->gps_video_uploaded)
                          <span class="grnpr-attach">
                            @if($r->invoice_copy_path)
                              <button type="button"
                                class="grnpr-attach-btn grnpr-attach-btn--pdf grnpr-preview-btn"
                                data-grnpr-preview-type="pdf"
                                data-grnpr-preview-url="{{ asset('public/' . $r->invoice_copy_path) }}"
                                data-grnpr-preview-title="Invoice — {{ e($r->grn_number) }}"
                                title="Preview invoice PDF">
                                <i class="bi bi-filetype-pdf"></i>PDF
                              </button>
                            @endif
                            @if($r->gps_video_uploaded && $r->gps_video_path)
                              <button type="button"
                                class="grnpr-attach-btn grnpr-attach-btn--vid grnpr-preview-btn"
                                data-grnpr-preview-type="video"
                                data-grnpr-preview-url="{{ asset('public/' . $r->gps_video_path) }}"
                                data-grnpr-preview-title="GPS video — {{ e($r->grn_number) }}"
                                title="Preview GPS video">
                                <i class="bi bi-play-btn"></i>GPS
                              </button>
                            @elseif($r->gps_video_uploaded)
                              <span class="grnpr-attach-btn grnpr-attach-btn--vid is-disabled" title="GPS video flagged but file unavailable">
                                <i class="bi bi-camera-video"></i>GPS
                              </span>
                            @endif
                          </span>
                        @else
                          <span class="grnpr-dash">—</span>
                        @endif
                      </td>

                      {{-- Audit status + reviewer (when reviewed) --}}
                      <td>
                        <div class="grnpr-audit-cell">
                          <span class="grnpr-status grnpr-status--{{ $st }}">{{ \App\Models\GrnRecord::statusLabel($r->audit_approval_status) }}</span>
                          @if(! $r->isPending() && $r->reviewed_by)
                            @php
                              $reviewerName = $r->reviewerDisplayName();
                              $reviewedWhen = $r->reviewed_at ? $r->reviewed_at->format('d M Y · h:i A') : '';
                              $reviewedShort = $r->reviewed_at ? $r->reviewed_at->format('d M Y') : '';
                              $reviewedVerb = $r->audit_approval_status === \App\Models\GrnRecord::STATUS_APPROVED ? 'Approved by' : 'Rejected by';
                            @endphp
                            <div class="grnpr-reviewer" title="{{ $reviewedVerb }} {{ $reviewerName }}{{ $reviewedWhen ? ' on '.$reviewedWhen : '' }}">
                              <i class="bi bi-person-check"></i>
                              <span class="grnpr-reviewer-label">{{ $reviewedVerb }}</span>
                              <span class="grnpr-reviewer-name">{{ $reviewerName }}</span>
                            </div>
                          @endif
                        </div>
                      </td>

                      @php
                          $grnpr_zone_idx = $r->zone_name
                              ? (crc32(strtolower(trim((string) $r->zone_name))) % 8) + 1
                              : 0;
                          $grnpr_view_payload = [
                              'id'             => $r->id,
                              'ref'            => $r->grn_number,
                              'status'         => $st,
                              'status_label'   => \App\Models\GrnRecord::statusLabel($r->audit_approval_status),
                              'created_at'     => $r->created_at ? $r->created_at->format('d M Y · h:i A') : null,
                              'created_by'     => method_exists($r, 'creatorDisplayName') ? $r->creatorDisplayName() : null,
                              'company'        => $r->company_name,
                              'zone'           => $r->zone_name,
                              'zone_idx'       => $grnpr_zone_idx,
                              'branch'         => $r->branch_name,
                              'vendor'         => $r->vendor_name,
                              'invoice_no'     => $r->invoice_number,
                              'invoice_date'   => $r->invoice_date ? $r->invoice_date->format('d M Y') : null,
                              'received_date'  => $r->received_date ? $r->received_date->format('d M Y') : null,
                              'received_by'    => $r->received_by,
                              'remarks'        => $r->remarks,
                              'reviewer'       => (! $r->isPending() && $r->reviewed_by) ? $r->reviewerDisplayName() : null,
                              'reviewed_at'    => $r->reviewed_at ? $r->reviewed_at->format('d M Y · h:i A') : null,
                              'reviewed_verb'  => $r->audit_approval_status === \App\Models\GrnRecord::STATUS_APPROVED ? 'Approved' : ($r->audit_approval_status === \App\Models\GrnRecord::STATUS_REJECTED ? 'Rejected' : null),
                              'reject_reason'  => $r->rejection_reason ?? null,
                              'edit_url'       => $canEditRow ? route('grn.edit', $r) : null,
                              'show_url'       => route('grn.show', $r),
                              'pdf_url'        => $r->invoice_copy_path ? asset('public/' . $r->invoice_copy_path) : null,
                              'video_url'      => ($r->gps_video_uploaded && $r->gps_video_path) ? asset('public/' . $r->gps_video_path) : null,
                              'video_flagged_unavailable' => ($r->gps_video_uploaded && ! $r->gps_video_path),
                          ];
                          $hasEdit       = (bool) $canEditRow;
                          $hasAudit      = ($canAudit ?? false) && $r->isPending();
                          $isApproved    = $r->audit_approval_status === \App\Models\GrnRecord::STATUS_APPROVED;
                          $isRejected    = $r->audit_approval_status === \App\Models\GrnRecord::STATUS_REJECTED;
                          $reviewerNameA = $r->reviewerDisplayName();
                          $reviewedWhenA = $r->reviewed_at ? $r->reviewed_at->format('d M Y · h:i A') : '';
                      @endphp

                      <td class="text-center grnpr-edit-cell">
                        @if($hasEdit)
                          <a class="grnpr-edit-btn"
                             href="{{ route('grn.edit', $r) }}"
                             title="Edit GRN {{ $r->grn_number }}"
                             aria-label="Edit GRN {{ $r->grn_number }}">
                            <i class="bi bi-pencil-square"></i>
                          </a>
                        @else
                          <span class="grnpr-edit-na"
                                title="{{ $isApproved ? 'Approved records cannot be edited' : ($isRejected ? 'Rejected records cannot be edited' : 'You do not have permission to edit this record') }}"
                                aria-label="Edit not available">—</span>
                        @endif
                      </td>

                      {{-- ── Actions (Approve / Reject while pending,
                              status icon once audited) ──────────────── --}}
                      <td class="text-center grnpr-actions-cell">
                        <span class="grnpr-actions" role="group" aria-label="GRN row actions">
                          {{-- JSON payload consumed by the row-click → side-panel handler --}}
                          <script type="application/json" id="grnpr-data-{{ $r->id }}">@json($grnpr_view_payload)</script>

                          @if($r->isPending())
                            @if($hasAudit)
                              {{-- Auditor sees the decision tray --}}
                              <span class="grnpr-actions-tray grnpr-actions-tray--audit"
                                    role="group"
                                    data-grnpr-action-group="audit"
                                    aria-label="Audit decision"
                                    title="Audit decision">
                                <button type="button"
                                  class="grnpr-iconbtn grnpr-iconbtn--approve grnpr-approve-btn"
                                  data-bs-toggle="modal"
                                  data-bs-target="#grnprApproveModal"
                                  data-grnpr-approve-url="{{ route('grn.approve', $r) }}"
                                  data-grnpr-approve-ref="{{ $r->grn_number }}"
                                  data-grnpr-approve-vendor="{{ $r->vendor_name }}"
                                  data-grnpr-approve-invoice="{{ $r->invoice_number }}"
                                  title="Approve audit" aria-label="Approve audit">
                                  <i class="bi bi-check2-circle"></i>
                                </button>
                                <button type="button"
                                  class="grnpr-iconbtn grnpr-iconbtn--reject"
                                  data-bs-toggle="modal"
                                  data-bs-target="#grnprRejectModal{{ $r->id }}"
                                  title="Reject audit" aria-label="Reject audit">
                                  <i class="bi bi-x-octagon"></i>
                                </button>
                              </span>
                            @else
                              {{-- Pending but the viewer is not an auditor --}}
                              <span class="grnpr-actions-pending"
                                    title="Awaiting audit decision"
                                    aria-label="Awaiting audit decision">
                                <i class="bi bi-hourglass-split"></i>
                              </span>
                            @endif
                          @else
                            {{-- Audit decision already made → status icon stays in this column --}}
                            @if($isApproved)
                              <span class="grnpr-status-icon grnpr-status-icon--approved"
                                    title="Approved{{ $reviewerNameA ? ' by '.$reviewerNameA : '' }}{{ $reviewedWhenA ? ' on '.$reviewedWhenA : '' }}"
                                    aria-label="Approved">
                                <i class="bi bi-patch-check-fill"></i>

                              </span>
                            @elseif($isRejected)
                              <span class="grnpr-status-icon grnpr-status-icon--rejected"
                                    title="Rejected{{ $reviewerNameA ? ' by '.$reviewerNameA : '' }}{{ $reviewedWhenA ? ' on '.$reviewedWhenA : '' }}"
                                    aria-label="Rejected">
                                <i class="bi bi-x-octagon-fill"></i>

                              </span>
                            @endif
                          @endif
                        </span>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="13" class="grnpr-empty">
                        <span class="grnpr-empty-ic-wrap">
                          <i class="bi bi-inbox grnpr-empty-ic"></i>
                        </span>
                        <div class="grnpr-empty-title">No GRN records match these filters</div>
                        <p class="grnpr-empty-hint">Try clearing some filters, or create a new goods received note.</p>
                        <a href="{{ route('grn.create') }}" class="grnpr-btn-new" style="display:inline-flex;"><i class="bi bi-plus-lg"></i> New GRN</a>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($records->hasPages())
              <div class="grnpr-pagination">{{ $records->links('vendor.pagination.bootstrap-5') }}</div>
            @endif
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

@if($canAudit ?? false)
  @foreach($records as $r)
    @if($r->isPending())
      <div class="modal fade" id="grnprRejectModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content grnpr-modal-content">
            <form method="post" action="{{ route('grn.reject', $r) }}">
              @csrf
              <div class="modal-header grnpr-modal-header">
                <h5 class="modal-title"><i class="bi bi-x-octagon me-2"></i>Reject audit — {{ $r->grn_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="rejection_reason" class="form-control" rows="3" required maxlength="2000" placeholder="Explain why this GRN is being returned…"></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif
  @endforeach

  {{-- Shared Approve confirmation modal --}}
  <div class="modal fade" id="grnprApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content grnpr-modal-content">
        <form method="post" action="" id="grnprApproveForm">
          @csrf
          <div class="modal-header grnpr-modal-header grnpr-modal-header--approve">
            <h5 class="modal-title"><i class="bi bi-shield-check me-2"></i>Confirm approval</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="grnpr-approve-lead">You are about to <strong>approve</strong> the audit for this GRN. This action cannot be undone from this screen.</p>
            <ul class="grnpr-approve-meta">
              <li><span>Reference</span><strong id="grnprApproveRef">—</strong></li>
              <li><span>Vendor</span><strong id="grnprApproveVendor">—</strong></li>
              <li><span>Invoice #</span><strong id="grnprApproveInvoice">—</strong></li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" id="grnprApproveSubmit">
              <i class="bi bi-check2-circle me-1"></i> Yes, approve
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

{{-- ──────────── Slide-over: GRN details (View action) ──────────── --}}
<div id="grnprSpOverlay" class="grnpr-sp-overlay" aria-hidden="true"></div>
<aside id="grnprSidePanel" class="grnpr-sp" role="dialog" aria-modal="true" aria-labelledby="grnprSpTitle" aria-hidden="true">

  {{-- ── Sticky header ── --}}
  <header class="grnpr-sp-hd">
    <div class="grnpr-sp-hd-left">
      <span class="grnpr-sp-eyebrow"><i class="bi bi-box-seam"></i> Goods Received Note</span>
      <div class="grnpr-sp-title">
        <span id="grnprSpTitle">—</span>
        <span class="grnpr-sp-statuspill grnpr-sp-statuspill--pending" id="grnprSpStatus">
          <span class="dot"></span><span id="grnprSpStatusText">Pending</span>
        </span>
      </div>
      <div class="grnpr-sp-submeta">
        <span class="grnpr-sp-submeta-i" id="grnprSpRecordedWrap"><i class="bi bi-calendar3"></i> <span id="grnprSpRecorded">—</span></span>
        <span class="grnpr-sp-submeta-i" id="grnprSpCreatedByWrap"><i class="bi bi-person-circle"></i> <span id="grnprSpCreatedBy">—</span></span>
      </div>
    </div>
    <div class="grnpr-sp-hd-actions">
      <a href="#" class="grnpr-sp-iconbtn" id="grnprSpEdit" title="Edit GRN" style="display:none;">
        <i class="bi bi-pencil-square"></i>
      </a>
      <button type="button" class="grnpr-sp-iconbtn grnpr-sp-iconbtn--close" id="grnprSpClose" title="Close" aria-label="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </header>

  {{-- ── Body ── --}}
  <div class="grnpr-sp-body">

    {{-- Audit accent strip (always visible; tone reflects status) --}}
    <div class="grnpr-sp-audit grnpr-sp-audit--pending" id="grnprSpAuditWrap">
      <span class="grnpr-sp-audit-ic" id="grnprSpAuditIcon"><i class="bi bi-hourglass-split"></i></span>
      <div class="grnpr-sp-audit-text">
        <div class="grnpr-sp-audit-line" id="grnprSpAuditLine">—</div>
        <div class="grnpr-sp-audit-meta" id="grnprSpAuditMetaText">—</div>
        <div class="grnpr-sp-audit-reason" id="grnprSpAuditReason" style="display:none;"></div>
      </div>
    </div>

    {{-- Vendor & Invoice --}}
    <section class="grnpr-sp-sec">
      <div class="grnpr-sp-sec-h"><i class="bi bi-receipt"></i> Vendor &amp; Invoice</div>
      <div class="grnpr-sp-grid">
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-person-badge"></i> Vendor</div>
          <div class="grnpr-sp-val" id="grnprSpVendor">—</div>
        </div>
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-buildings"></i> Company</div>
          <div class="grnpr-sp-val" id="grnprSpCompany">—</div>
        </div>
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-hash"></i> Invoice number</div>
          <div class="grnpr-sp-val grnpr-sp-val--mono" id="grnprSpInvoiceNo">—</div>
        </div>
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-calendar-event"></i> Invoice date</div>
          <div class="grnpr-sp-val" id="grnprSpInvoiceDate">—</div>
        </div>
      </div>
    </section>

    {{-- Location & Receipt --}}
    <section class="grnpr-sp-sec">
      <div class="grnpr-sp-sec-h"><i class="bi bi-geo-alt"></i> Location &amp; Receipt</div>
      <div class="grnpr-sp-grid">
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-geo"></i> Location</div>
          <div class="grnpr-sp-val">
            <div class="grnpr-sp-loc" id="grnprSpLocation">
              <span class="grnpr-zone-pill" id="grnprSpZone" style="display:none;">—</span>
              <span class="grnpr-branch-txt" id="grnprSpBranch" style="display:none;">—</span>
              <span class="grnpr-sp-val--muted" id="grnprSpLocationDash">—</span>
            </div>
          </div>
        </div>
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-truck"></i> Received date</div>
          <div class="grnpr-sp-val" id="grnprSpReceivedDate">—</div>
        </div>
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-person-check"></i> Received by</div>
          <div class="grnpr-sp-val" id="grnprSpReceivedBy">—</div>
        </div>
        <div class="grnpr-sp-field">
          <div class="grnpr-sp-lbl"><i class="bi bi-clock-history"></i> Recorded</div>
          <div class="grnpr-sp-val" id="grnprSpRecordedDetail">—</div>
        </div>
      </div>
    </section>

    {{-- Remarks (only when present) --}}
    <section class="grnpr-sp-sec" id="grnprSpRemarksWrap" style="display:none;">
      <div class="grnpr-sp-sec-h"><i class="bi bi-chat-left-text"></i> Remarks</div>
      <div class="grnpr-sp-remarks" id="grnprSpRemarks"></div>
    </section>

    {{-- Attachments --}}
    <section class="grnpr-sp-sec">
      <div class="grnpr-sp-sec-h"><i class="bi bi-paperclip"></i> Attachments</div>
      <div class="grnpr-sp-files" id="grnprSpFiles">
        <div class="grnpr-sp-empty">No attachments uploaded.</div>
      </div>
    </section>

  </div>
</aside>

{{-- Shared Preview modal (PDF / Video) --}}
<div class="modal fade" id="grnprPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl grnpr-preview-dialog">
    <div class="modal-content grnpr-modal-content grnpr-preview-content">
      <div class="modal-header grnpr-modal-header grnpr-modal-header--preview">
        <h5 class="modal-title">
          <i class="bi bi-eye me-2" id="grnprPreviewIcon"></i>
          <span id="grnprPreviewTitle">Preview</span>
        </h5>
        <div class="grnpr-preview-tools">
          <a href="#" class="btn btn-sm btn-light grnpr-preview-open" id="grnprPreviewOpen" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right me-1"></i> Open
          </a>
          <a href="#" class="btn btn-sm btn-light grnpr-preview-download" id="grnprPreviewDownload" download>
            <i class="bi bi-download me-1"></i> Download
          </a>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body p-0 grnpr-preview-body">
        <div id="grnprPreviewSlot" class="grnpr-preview-slot"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
(function () {
  if (typeof toastr !== 'undefined') {
    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3500 };
    @if(session('success')) toastr.success(@json(session('success'))); @endif
    @if(session('error'))   toastr.error(@json(session('error')));   @endif
  }
})();
</script>

{{-- Approve confirmation modal wiring --}}
<script>
(function () {
  var approveModalEl = document.getElementById('grnprApproveModal');
  if (!approveModalEl) return;

  var form          = document.getElementById('grnprApproveForm');
  var refOut        = document.getElementById('grnprApproveRef');
  var vendorOut     = document.getElementById('grnprApproveVendor');
  var invoiceOut    = document.getElementById('grnprApproveInvoice');
  var submitBtn     = document.getElementById('grnprApproveSubmit');
  var defaultLabel  = submitBtn ? submitBtn.innerHTML : '';

  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('.grnpr-approve-btn');
    if (!btn) return;
    if (form)       form.action = btn.getAttribute('data-grnpr-approve-url') || '';
    if (refOut)     refOut.textContent     = btn.getAttribute('data-grnpr-approve-ref')     || '—';
    if (vendorOut)  vendorOut.textContent  = btn.getAttribute('data-grnpr-approve-vendor')  || '—';
    if (invoiceOut) invoiceOut.textContent = btn.getAttribute('data-grnpr-approve-invoice') || '—';
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = defaultLabel;
    }
  });

  if (form) {
    form.addEventListener('submit', function () {
      if (!submitBtn) return;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Approving…';
    });
  }
})();
</script>

{{-- Preview modal (PDF / GPS video) wiring --}}
<script>
(function () {
  var previewEl = document.getElementById('grnprPreviewModal');
  if (!previewEl) return;

  var titleOut    = document.getElementById('grnprPreviewTitle');
  var iconOut     = document.getElementById('grnprPreviewIcon');
  var openLink    = document.getElementById('grnprPreviewOpen');
  var dlLink      = document.getElementById('grnprPreviewDownload');
  var slot        = document.getElementById('grnprPreviewSlot');
  var modal       = (typeof bootstrap !== 'undefined' && bootstrap.Modal)
                      ? bootstrap.Modal.getOrCreateInstance(previewEl)
                      : null;

  function clearSlot() {
    if (!slot) return;
    var v = slot.querySelector('video');
    if (v) { try { v.pause(); v.removeAttribute('src'); v.load(); } catch (err) {} }
    slot.innerHTML = '';
  }

  function buildPdf(url) {
    var iframe = document.createElement('iframe');
    iframe.src = url + '#toolbar=1&navpanes=0&view=FitH';
    iframe.setAttribute('title', 'PDF preview');
    iframe.setAttribute('allowfullscreen', '');
    return iframe;
  }

  function buildVideo(url) {
    var video = document.createElement('video');
    video.controls = true;
    video.preload  = 'metadata';
    video.playsInline = true;
    var src = document.createElement('source');
    src.src = url;
    var lower = url.toLowerCase();
    if      (lower.indexOf('.mp4')  > -1) src.type = 'video/mp4';
    else if (lower.indexOf('.webm') > -1) src.type = 'video/webm';
    else if (lower.indexOf('.ogg')  > -1) src.type = 'video/ogg';
    else if (lower.indexOf('.mov')  > -1) src.type = 'video/quicktime';
    video.appendChild(src);
    var fb = document.createElement('div');
    fb.className = 'grnpr-preview-fallback';
    fb.innerHTML = 'Your browser cannot play this video. <a href="' + url + '" target="_blank" rel="noopener" style="color:#67e8f9;">Open it in a new tab</a>.';
    video.appendChild(fb);
    return video;
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('.grnpr-preview-btn');
    if (!btn) return;
    e.preventDefault();

    var url   = btn.getAttribute('data-grnpr-preview-url')   || '';
    var type  = (btn.getAttribute('data-grnpr-preview-type') || 'pdf').toLowerCase();
    var title = btn.getAttribute('data-grnpr-preview-title') || 'Preview';

    if (titleOut) titleOut.textContent = title;
    if (iconOut)  iconOut.className    = (type === 'video' ? 'bi bi-play-btn me-2' : 'bi bi-filetype-pdf me-2');
    if (openLink) openLink.href        = url;
    if (dlLink)   dlLink.href          = url;

    clearSlot();
    if (slot) {
      if (type === 'video') slot.appendChild(buildVideo(url));
      else                  slot.appendChild(buildPdf(url));
    }

    if (modal) modal.show();
  });

  previewEl.addEventListener('hidden.bs.modal', clearSlot);
})();
</script>

{{-- ──────────── Side-panel (View) wiring ──────────── --}}
<script>
(function () {
  var panel    = document.getElementById('grnprSidePanel');
  var overlay  = document.getElementById('grnprSpOverlay');
  if (!panel || !overlay) return;

  // Header
  var elTitle        = document.getElementById('grnprSpTitle');
  var elStatus       = document.getElementById('grnprSpStatus');
  var elStatusText   = document.getElementById('grnprSpStatusText');
  var elRecorded     = document.getElementById('grnprSpRecorded');
  var elCreatedBy    = document.getElementById('grnprSpCreatedBy');
  var elCreatedByWrap = document.getElementById('grnprSpCreatedByWrap');
  var elRecordedWrap  = document.getElementById('grnprSpRecordedWrap');
  var elEdit         = document.getElementById('grnprSpEdit');
  var elOpenFull     = document.getElementById('grnprSpOpenFull');
  var elClose        = document.getElementById('grnprSpClose');
  var elCloseFt      = document.getElementById('grnprSpCloseFt');

  // Audit strip
  var elAuditWrap    = document.getElementById('grnprSpAuditWrap');
  var elAuditIcon    = document.getElementById('grnprSpAuditIcon');
  var elAuditLine    = document.getElementById('grnprSpAuditLine');
  var elAuditMetaText = document.getElementById('grnprSpAuditMetaText');
  var elAuditReason  = document.getElementById('grnprSpAuditReason');

  // Spotlight
  var elVendor       = document.getElementById('grnprSpVendor');
  var elCompany      = document.getElementById('grnprSpCompany');
  var elInvoiceNo    = document.getElementById('grnprSpInvoiceNo');
  var elInvoiceDate  = document.getElementById('grnprSpInvoiceDate');

  // Location & receipt
  var elZone         = document.getElementById('grnprSpZone');
  var elBranch       = document.getElementById('grnprSpBranch');
  var elLocDash      = document.getElementById('grnprSpLocationDash');
  var elReceivedDate = document.getElementById('grnprSpReceivedDate');
  var elReceivedBy   = document.getElementById('grnprSpReceivedBy');
  var elRecordedDetail = document.getElementById('grnprSpRecordedDetail');

  // Remarks / files
  var elRemarksWrap  = document.getElementById('grnprSpRemarksWrap');
  var elRemarks      = document.getElementById('grnprSpRemarks');
  var elFiles        = document.getElementById('grnprSpFiles');

  var lastFocus = null;

  function isFilled(val) {
    return val !== null && val !== undefined && String(val).trim() !== '';
  }

  function setText(el, val, fallbackMuted) {
    if (!el) return;
    if (isFilled(val)) {
      el.textContent = String(val);
      el.classList.remove('grnpr-sp-val--muted');
    } else {
      el.textContent = fallbackMuted || '—';
      el.classList.add('grnpr-sp-val--muted');
    }
  }

  /**
   * Strip a trailing employee-id artefact so the side-panel only shows the
   * human-readable name. Handles every observed format:
   *   • "Aravind (11811)" / "Aravind [11811]"    → bracketed form
   *   • "Aravind - 11811" / "Aravind · 11811"   → punctuated form
   *   • "Aravind 11811"                          → plain space separator
   *   • "Kalaiselvi10022"                        → no separator at all
   * The (?<=\D) lookbehind on step 2 makes sure an all-digit value (rare
   * legacy data) is preserved instead of being blanked out.
   */
  function nameOnly(s) {
    if (!isFilled(s)) return '';
    var raw = String(s).trim();
    // Step 1 — bracket / dash / dot wrapped IDs.
    var out = raw.replace(/\s*(?:[\(\[]\s*|[-·]\s*)(?:EMP(?:LOYEE)?[-\s]?)?[A-Za-z0-9_-]+\s*[\)\]]?\s*$/iu, '');
    // Step 2 — trailing 3+ digit employee-id glued to the name.
    var step2 = out.replace(/(?<=\D)\s*\d{3,}\s*$/u, '');
    if (step2.trim() !== '') out = step2;
    return out.trim() || raw;
  }

  function buildFileButton(type, url, refLabel) {
    var isPdf = type === 'pdf';
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'grnpr-sp-file ' + (isPdf ? 'grnpr-sp-file--pdf' : 'grnpr-sp-file--vid')
                  + ' grnpr-preview-btn';
    btn.setAttribute('data-grnpr-preview-type', isPdf ? 'pdf' : 'video');
    btn.setAttribute('data-grnpr-preview-url', url);
    btn.setAttribute('data-grnpr-preview-title',
        (isPdf ? 'Invoice — ' : 'GPS video — ') + (refLabel || ''));
    btn.title = isPdf ? 'Open invoice PDF' : 'Open GPS video';

    btn.innerHTML =
        '<span class="grnpr-sp-file-ic"><i class="bi ' + (isPdf ? 'bi-filetype-pdf' : 'bi-play-btn-fill') + '"></i></span>' +
        '<span class="grnpr-sp-file-meta">' +
          '<span class="grnpr-sp-file-title">' + (isPdf ? 'Invoice copy (PDF)' : 'GPS receipt video') + '</span>' +
          '<span class="grnpr-sp-file-sub"><i class="bi bi-eye"></i> Click to preview</span>' +
        '</span>' +
        '<span class="grnpr-sp-file-cta"><i class="bi bi-arrow-right-short"></i></span>';
    return btn;
  }

  function buildUnavailableTile() {
    var div = document.createElement('div');
    div.className = 'grnpr-sp-file grnpr-sp-file--vid';
    div.style.cursor = 'not-allowed';
    div.style.opacity = '0.7';
    div.innerHTML =
        '<span class="grnpr-sp-file-ic"><i class="bi bi-exclamation-triangle"></i></span>' +
        '<span class="grnpr-sp-file-meta">' +
          '<span class="grnpr-sp-file-title">GPS receipt video</span>' +
          '<span class="grnpr-sp-file-sub" style="color:#b45309;">Flagged but file unavailable</span>' +
        '</span>';
    return div;
  }

  function setStatusPill(status, label) {
    if (!elStatus) return;
    var safe = (status || 'pending').toLowerCase();
    elStatus.className = 'grnpr-sp-statuspill grnpr-sp-statuspill--' + safe;
    if (elStatusText) elStatusText.textContent = label || (safe.charAt(0).toUpperCase() + safe.slice(1));
  }

  function renderAudit(data) {
    if (!elAuditWrap) return;
    var status = (data.status || 'pending').toLowerCase();
    elAuditWrap.classList.remove('grnpr-sp-audit--pending', 'grnpr-sp-audit--approved', 'grnpr-sp-audit--rejected');
    elAuditWrap.classList.add('grnpr-sp-audit--' + status);

    var iconHtml, line, meta;
    if (status === 'approved') {
      iconHtml = '<i class="bi bi-check2-circle"></i>';
      line = (data.reviewed_verb || 'Approved') + (data.reviewer ? ' by ' + nameOnly(data.reviewer) : '');
      meta = data.reviewed_at ? 'On ' + data.reviewed_at : 'Date not recorded';
    } else if (status === 'rejected') {
      iconHtml = '<i class="bi bi-x-octagon"></i>';
      line = (data.reviewed_verb || 'Rejected') + (data.reviewer ? ' by ' + nameOnly(data.reviewer) : '');
      meta = data.reviewed_at ? 'On ' + data.reviewed_at : 'Date not recorded';
    } else {
      iconHtml = '<i class="bi bi-hourglass-split"></i>';
      line = 'Awaiting audit review';
      meta = 'No reviewer has acted on this GRN yet.';
    }
    elAuditIcon.innerHTML = iconHtml;
    elAuditLine.textContent = line;
    if (elAuditMetaText) elAuditMetaText.textContent = meta;

    if (status === 'rejected' && isFilled(data.reject_reason)) {
      elAuditReason.style.display = '';
      elAuditReason.innerHTML = '<strong>Rejection reason:</strong> ' + escapeHtml(data.reject_reason);
    } else {
      elAuditReason.style.display = 'none';
      elAuditReason.textContent = '';
    }
  }

  function escapeHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function renderLocation(data) {
    var hasZone = isFilled(data.zone);
    var hasBranch = isFilled(data.branch);

    if (hasZone) {
      elZone.style.display = '';
      elZone.textContent = data.zone;
      elZone.className = 'grnpr-zone-pill grnpr-zone-pill--c' + (data.zone_idx || 1);
    } else {
      elZone.style.display = 'none';
    }
    if (hasBranch) {
      elBranch.style.display = '';
      elBranch.textContent = data.branch;
    } else {
      elBranch.style.display = 'none';
    }
    elLocDash.style.display = (hasZone || hasBranch) ? 'none' : '';
  }

  function renderFiles(data) {
    elFiles.innerHTML = '';
    var any = false;
    if (isFilled(data.pdf_url)) {
      elFiles.appendChild(buildFileButton('pdf', data.pdf_url, data.ref || ''));
      any = true;
    }
    if (isFilled(data.video_url)) {
      elFiles.appendChild(buildFileButton('video', data.video_url, data.ref || ''));
      any = true;
    } else if (data.video_flagged_unavailable) {
      elFiles.appendChild(buildUnavailableTile());
      any = true;
    }
    if (!any) {
      var empty = document.createElement('div');
      empty.className = 'grnpr-sp-empty';
      empty.textContent = 'No attachments uploaded.';
      elFiles.appendChild(empty);
    }
  }

  function populate(data) {
    setText(elTitle, data.ref || '—');
    setStatusPill(data.status, data.status_label);

    // Hero meta — hide each chip when its value is missing.
    if (elRecorded) elRecorded.textContent = isFilled(data.created_at) ? data.created_at : '—';
    if (elRecordedWrap) elRecordedWrap.style.display = isFilled(data.created_at) ? '' : 'none';
    if (elRecordedDetail) elRecordedDetail.textContent = isFilled(data.created_at) ? data.created_at : '—';
    var creator = nameOnly(data.created_by);
    if (elCreatedBy) elCreatedBy.textContent = isFilled(creator) ? ('by ' + creator) : '';
    if (elCreatedByWrap) elCreatedByWrap.style.display = isFilled(creator) ? '' : 'none';

    if (elEdit) {
      if (isFilled(data.edit_url)) { elEdit.style.display = ''; elEdit.href = data.edit_url; }
      else                         { elEdit.style.display = 'none'; elEdit.removeAttribute('href'); }
    }
    if (elOpenFull) elOpenFull.href = data.show_url || '#';

    renderAudit(data);

    setText(elVendor,       data.vendor);
    setText(elCompany,      data.company);
    setText(elInvoiceNo,    data.invoice_no);
    setText(elInvoiceDate,  data.invoice_date);
    setText(elReceivedDate, data.received_date);
    setText(elReceivedBy,   nameOnly(data.received_by)); // Show only the name.

    renderLocation(data);

    if (isFilled(data.remarks)) {
      elRemarksWrap.style.display = '';
      elRemarks.textContent = data.remarks;
    } else {
      elRemarksWrap.style.display = 'none';
      elRemarks.textContent = '';
    }

    renderFiles(data);
  }

  function open(data) {
    populate(data);
    panel.setAttribute('aria-hidden', 'false');
    overlay.setAttribute('aria-hidden', 'false');
    overlay.classList.add('show');
    panel.classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(function () { if (elClose) elClose.focus(); }, 60);
  }

  function close() {
    panel.classList.remove('show');
    overlay.classList.remove('show');
    panel.setAttribute('aria-hidden', 'true');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastFocus && typeof lastFocus.focus === 'function') {
      try { lastFocus.focus(); } catch (e) {}
    }
  }

  function openFromTarget(triggerEl, targetId) {
    if (!targetId) return;
    var script = document.getElementById(targetId);
    if (!script) return;
    var data;
    try { data = JSON.parse(script.textContent || '{}'); }
    catch (err) { console.error('GRN side-panel JSON parse failed', err); return; }
    lastFocus = triggerEl || null;
    open(data);
  }

  // Selectors that should NEVER trigger the panel from a row click (links, action
  // buttons, attachments preview, dropdowns, etc.). The View pill itself IS allowed
  // — it routes through grnpr-view-btn below.
  var ROW_IGNORE_SEL = 'a, button:not(.grnpr-view-btn), input, select, textarea, label, .grnpr-attach, .grnpr-actions-tray, .grnpr-preview-btn, [data-bs-toggle], [data-bs-target]';

  document.addEventListener('click', function (e) {
    // 1) Explicit "View" pill — always opens.
    var btn = e.target.closest && e.target.closest('.grnpr-view-btn');
    if (btn) {
      e.preventDefault();
      openFromTarget(btn, btn.getAttribute('data-grnpr-view-target'));
      return;
    }

    // 2) Row click anywhere except interactive children.
    var row = e.target.closest && e.target.closest('tr.grnpr-row-clickable');
    if (!row) return;
    if (e.target.closest(ROW_IGNORE_SEL)) return;
    // Ignore text-selection clicks (user dragged out a selection in the row).
    var selection = window.getSelection && window.getSelection();
    if (selection && selection.toString().length > 0 && row.contains(selection.anchorNode)) return;

    e.preventDefault();
    openFromTarget(row, row.getAttribute('data-grnpr-view-target'));
  });

  // Keyboard: Enter/Space on a focused row also opens the panel.
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    var row = e.target.closest && e.target.closest('tr.grnpr-row-clickable');
    if (!row || row !== document.activeElement) return;
    e.preventDefault();
    openFromTarget(row, row.getAttribute('data-grnpr-view-target'));
  });

  if (elClose)    elClose.addEventListener('click', close);
  if (elCloseFt)  elCloseFt.addEventListener('click', close);
  if (overlay)    overlay.addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (!panel.classList.contains('show')) return;
    // If a Bootstrap modal (preview, approve, reject) is on top, let it handle Escape first.
    if (document.querySelector('.modal.show')) return;
    close();
  });
})();
</script>

<script>
(function ($) {
  const $form = $('#grnprFilterForm');
  if (!$form.length) return;

  let submitTimer = null;
  function submitNow() {
    if (submitTimer) { clearTimeout(submitTimer); submitTimer = null; }
    const el = $form[0];
    if (el) el.submit();
  }
  function scheduleSubmit() {
    if (submitTimer) clearTimeout(submitTimer);
    submitTimer = setTimeout(function () { submitTimer = null; submitNow(); }, 380);
  }

  function syncHidden(name, ids) {
    const $box = $form.find('.grnpr-array-hiddens[data-array-name="' + name + '"]');
    $box.empty();
    ids.forEach(function (id) {
      if (id === '' || id == null) return;
      $box.append($('<input>', { type: 'hidden', name: name + '[]', value: String(id) }));
    });
  }

  function refreshDdInputFromState($dd) {
    const $float = $dd.data('float');
    if (!$float) return;
    const param = $dd.data('filter-param');
    const empty = $dd.data('empty-label') || 'All';
    const labels = [], ids = [];
    $float.find('.grnpr-opt.selected').each(function () {
      labels.push($(this).attr('data-label') || $(this).text().trim());
      ids.push($(this).attr('data-id'));
    });
    $dd.find('.grnpr-dd-input').val(labels.length ? labels.join(', ') : empty);
    syncHidden(param, ids);
    scheduleSubmit();
  }

  function position($input, $float) {
    const r = $input[0].getBoundingClientRect();
    const w = Math.max(r.width, 240);
    const vw = window.innerWidth || document.documentElement.clientWidth || 0;
    let left = r.left;
    if (left + w > vw - 8) left = Math.max(8, vw - w - 8);
    $float.css({ position: 'fixed', top: r.bottom + 4, left: left, width: w, zIndex: 10050 });
  }

  function buildFloating($dd) {
    let $float = $dd.data('float');
    if ($float) return $float;

    const tplHtml = $dd.find('template').html() || '';
    $float = $('<div class="grnpr-floating"></div>').append(
      '<div class="grnpr-search-wrap"><input type="text" class="grnpr-search-input" placeholder="Search…"></div>' +
      '<div class="grnpr-actions">' +
        '<button type="button" class="grnpr-btn-mini grnpr-btn-all">Select all</button>' +
        '<button type="button" class="grnpr-btn-mini grnpr-btn-clear">Clear</button>' +
      '</div>' +
      '<div class="grnpr-list">' + tplHtml + '</div>'
    );
    $('body').append($float);
    $float.data('owner', $dd);
    $dd.data('float', $float);

    $float.on('click', '.grnpr-opt', function (e) {
      e.stopPropagation();
      $(this).toggleClass('selected');
      refreshDdInputFromState($dd);
    });
    $float.on('click', '.grnpr-btn-all', function (e) {
      e.stopPropagation();
      $float.find('.grnpr-opt:visible').addClass('selected');
      refreshDdInputFromState($dd);
    });
    $float.on('click', '.grnpr-btn-clear', function (e) {
      e.stopPropagation();
      $float.find('.grnpr-opt').removeClass('selected');
      refreshDdInputFromState($dd);
    });
    $float.on('keyup input', '.grnpr-search-input', function () {
      const q = ($(this).val() || '').toLowerCase();
      $float.find('.grnpr-opt').each(function () {
        $(this).toggle(($(this).text() || '').toLowerCase().indexOf(q) > -1);
      });
    });
    $float.on('click', function (e) { e.stopPropagation(); });

    return $float;
  }

  $(document).on('click', '.grnpr-dd .grnpr-dd-input', function (e) {
    e.stopPropagation();
    $('.grnpr-floating.show').removeClass('show').hide();

    const $input = $(this);
    const $dd = $input.closest('.grnpr-dd');
    const $float = buildFloating($dd);

    position($input, $float);
    $float.addClass('show').show();
    $float.find('.grnpr-search-input').val('').focus();
    $float.find('.grnpr-opt').show();
  });

  $(document).on('keydown', '.grnpr-dd .grnpr-dd-input', function (e) {
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
      e.preventDefault();
      $(this).trigger('click');
    }
  });

  $(window).on('scroll resize', function () {
    $('.grnpr-floating.show').each(function () {
      const $float = $(this);
      const $dd = $float.data('owner');
      if (!$dd || !$dd.length) return;
      position($dd.find('.grnpr-dd-input').first(), $float);
    });
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('.grnpr-dd, .grnpr-floating').length) return;
    $('.grnpr-floating.show').removeClass('show').hide();
  });

  // ── search & per-page ──
  $('#grnprSearch').on('input', function () { scheduleSubmit(); });
  $('#grnprPerPage').on('change', function () { submitNow(); });

  // ── Date range picker ──
  if (typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined') {
    const $dr = $('#grnprReportRange');
    const df = $('#grnprDateFrom').val();
    const dt = $('#grnprDateTo').val();
    const opts = {
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD', separator: ' – ', cancelLabel: 'Clear', applyLabel: 'Apply' },
      opens: 'left',
      drops: 'down'
    };
    if (df && dt) { opts.startDate = moment(df); opts.endDate = moment(dt); }
    $dr.daterangepicker(opts);
    // theme the picker without depending on shared CSS
    const drp = $dr.data('daterangepicker');
    if (drp && drp.container) drp.container.addClass('grnpr-drp');

    $dr.on('apply.daterangepicker', function (ev, picker) {
      $('#grnprDateFrom').val(picker.startDate.format('YYYY-MM-DD'));
      $('#grnprDateTo').val(picker.endDate.format('YYYY-MM-DD'));
      $('#grnprDateLabel').text(picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY'));
      submitNow();
    });
    $dr.on('cancel.daterangepicker', function () {
      $('#grnprDateFrom').val('');
      $('#grnprDateTo').val('');
      $('#grnprDateLabel').text('All dates');
      submitNow();
    });
    $dr.on('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); $(this).trigger('click'); }
    });
  }
})(jQuery);
</script>

@include('superadmin.superadminfooter')
</body>
</html>
