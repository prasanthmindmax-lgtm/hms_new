<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/pettycash.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
    /* ── Tab bar (mirrors pettycash_reports style) ── */
    .report-tabs-wrapper {
        border-bottom: 1.5px solid #e5e7eb;
        background: #fff;
        padding: 0 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .report-tabs {
        display: flex;
        gap: 0;
    }

    .report-tab {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 11px 20px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 13px;
        color: #6b7280;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -1.5px;
        transition: all .15s;
        white-space: nowrap;
    }

    .report-tab svg {
        opacity: .5;
        transition: opacity .15s;
    }

    .report-tab:hover:not(.active) {
        color: #111827;
        background: #f9fafb;
    }

    .report-tab.active {
        color: #4f46e5;
        font-weight: 600;
        border-bottom: 2.5px solid #4f46e5;
    }

    .report-tab.active svg {
        opacity: 1;
    }

    .tab-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 18px;
        padding: 0 5px;
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 700;
        background: #e5e7eb;
        color: #374151;
        margin-left: 2px;
    }

    .report-tab.active .tab-count-badge {
        background: #eef2ff;
        color: #4f46e5;
    }

    .adv-tab-new-advance {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        margin-left: 8px;
        border-radius: 8px;
        color: #2563eb;
        background: #eff6ff;
        text-decoration: none;
        flex-shrink: 0;
        transition: background .15s, color .15s;
    }

    .adv-tab-new-advance:hover {
        color: #1d4ed8;
        background: #dbeafe;
    }

    /* ── Advances page shell ── */
    .adv-dashboard .qd-header {
        padding-bottom: 14px;
        border-bottom: 1px solid #e8ecf1;
        margin-bottom: 4px;
    }

    .adv-dashboard .report-tabs-wrapper {
        margin-top: 4px;
        background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);
        border-radius: 10px 10px 0 0;
        border: 1px solid #e8ecf1;
        border-bottom: none;
        padding: 4px 12px 0;
    }

    .adv-dashboard #advances-rows-wrap {
        border: 1px solid #e8ecf1;
        border-top: none;
        border-radius: 0 0 12px 12px;
        background: #fff;
        overflow: hidden;
    }

    /* ── Pending advances: table layout (header + body) ── */
    .adv-pending-shell {
        border: none;
        border-radius: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #fff;
        box-shadow: none;
    }

    .adv-pending-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: collapse;
        font-size: 14px;
        color: #0f172a;
    }

    .adv-pending-table thead {
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    }

    .adv-pending-table thead th.adv-pth {
        padding: 14px 16px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #e2e8f0;
        border-bottom: 2px solid #020617;
        text-align: left;
        vertical-align: middle;
    }

    .adv-pending-table thead th.adv-pth.text-end {
        text-align: right;
    }

    .adv-pending-table thead th.adv-pth-status {
        text-align: center;
    }

    .adv-pth-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .adv-pth-check {
        width: 48px;
        text-align: center;
    }

    .adv-pth-amount {
        width: 14%;
        min-width: 124px;
    }

    .adv-pth-date {
        width: 104px;
        min-width: 96px;
        white-space: nowrap;
    }

    .adv-pth-ref {
        width: 11%;
        min-width: 108px;
    }

    .adv-pth-status {
        width: 118px;
        min-width: 104px;
    }

    .adv-pth-details {
        min-width: 200px;
    }

    .adv-pth-balance {
        width: 12%;
        min-width: 104px;
    }

    .adv-pth-actions {
        width: 210px;
    }

    .adv-pending-table tbody tr.adv-pending-card td.adv-ptd {
        padding: 14px 16px;
        vertical-align: top;
        border-bottom: 1px solid #e8ecf1;
        background: #fff;
        transition: background 0.15s ease, box-shadow 0.15s ease;
    }

    .adv-pending-table tbody tr.adv-pending-card:nth-child(even) td.adv-ptd {
        background: #fafbfc;
    }

    .adv-pending-table tbody tr.adv-pending-card:hover td.adv-ptd {
        background: #f1f5f9;
    }

    .adv-pending-table tbody tr.adv-pending-card:last-child td.adv-ptd {
        border-bottom: none;
    }

    .adv-ptd-check {
        vertical-align: middle;
        text-align: center;
        width: 48px;
    }

    .adv-pending-table tbody tr.adv-pending-card td.adv-ptd-date,
    .adv-pending-table tbody tr.adv-pending-card td.adv-ptd-ref,
    .adv-pending-table tbody tr.adv-pending-card td.adv-ptd-status {
        vertical-align: middle;
    }

    .adv-pending-table tbody td.adv-ptd.text-center {
        text-align: center;
    }

    .adv-ptd-date {
        white-space: nowrap;
    }

    .adv-ptd-ref {
        max-width: 220px;
    }

    .adv-pc-ref-cell {
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
        line-height: 1.45;
        word-break: break-word;
    }

    .adv-pc-ref-empty,
    .adv-pc-details-empty {
        font-size: 14px;
        color: #94a3b8;
    }

    .adv-pc-info-stack--details {
        gap: 10px;
    }

    .adv-pending-empty-row td {
        padding: 36px 20px !important;
        text-align: center;
        vertical-align: middle !important;
        border-bottom: none !important;
        background: #fafbfc !important;
    }

    .adv-pc-check {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .adv-pc-check input[type="checkbox"] {
        width: 15px;
        height: 15px;
        cursor: pointer;
        accent-color: #4f46e5;
    }

    .adv-pc-amount-val {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .adv-pc-used {
        font-size: 12.5px;
        color: #475569;
        margin-top: 6px;
        font-weight: 500;
    }

    .adv-pc-info-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .adv-pc-info-top {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 6px 16px;
    }

    .adv-pc-ref-inline {
        font-size: 13px;
        color: #334155;
        font-weight: 500;
    }

    .adv-pc-ref-k {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-right: 4px;
    }

    .adv-pc-report-line {
        font-size: 13px;
        line-height: 1.45;
    }

    .adv-pc-report-k {
        color: #64748b;
        font-weight: 600;
        margin-right: 6px;
        font-size: 12px;
    }

    .adv-pc-report-val {
        color: #1d4ed8;
        font-weight: 600;
    }

    .adv-pc-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: fit-content;
        font-size: 12px;
        font-weight: 600;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fcd34d;
        padding: 5px 12px;
        border-radius: 999px;
    }

    .adv-pc-status-pill i {
        font-size: 12px;
        opacity: 0.95;
    }

    .adv-pc-location-block {
        margin-top: 2px;
    }

    .adv-pc-company-line {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.5;
        letter-spacing: -0.01em;
    }

    .adv-pc-zone-sep::before {
        content: ' · ';
        font-weight: 600;
        color: #94a3b8;
    }

    .adv-pc-zone-name {
        font-weight: 600;
        color: #334155;
    }

    .adv-pc-branches {
        margin-top: 8px;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-width: 100%;
    }

    .adv-pc-branches-k {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        margin-bottom: 5px;
    }

    .adv-pc-branches-text {
        display: block;
        font-size: 13px;
        line-height: 1.55;
        color: #1e293b;
        font-weight: 500;
        overflow-wrap: anywhere;
        word-wrap: break-word;
    }

    .adv-pc-branches--collapsible:not(.is-open) .adv-pc-branches-text {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
    }

    .adv-pc-branches-toggle {
        margin-top: 8px;
        padding: 0;
        border: none;
        background: none;
        font-size: 12px;
        font-weight: 600;
        color: #4f46e5;
        cursor: pointer;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .adv-pc-branches-toggle:hover {
        color: #3730a3;
    }

    .adv-pc-trip-val--emphasis {
        font-weight: 700;
        font-size: 16px;
        letter-spacing: -0.02em;
    }

    .adv-pc-action-col--pending {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-end;
        margin-left: auto;
        max-width: 100%;
    }

    .adv-pc-apply-wrap {
        position: relative;
    }

    .adv-pc-hint {
        margin: 0;
        font-size: 12px;
        line-height: 1.5;
        color: #64748b;
        text-align: right;
        max-width: 280px;
    }

    .adv-pc-hint strong {
        color: #475569;
        font-weight: 700;
    }

    .adv-pc-date-link {
        font-size: 14px;
        color: #4338ca;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        display: inline-block;
        border: none;
        background: none;
        padding: 0;
        font: inherit;
    }

    .adv-pc-date-link:hover {
        text-decoration: underline;
    }

    .adv-pc-ref {
        font-size: 12.5px;
        color: #334155;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .adv-pc-trip-val {
        font-size: 13px;
        color: #374151;
    }

    .adv-pc-action-col {
        flex-shrink: 0;
    }

    /* Zoho-style Apply to Report: neutral outline, solid blue when menu open */
    .adv-apply-btn {
        padding: 7px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #1f2937;
        border: 1px solid #d1d5db;
        border-radius: 5px;
        background: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .12s, border-color .12s, color .12s;
    }

    .adv-apply-btn:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .adv-apply-btn .adv-apply-chevron {
        font-size: 11px;
        opacity: 0.8;
        transition: transform .15s ease;
    }

    .adv-apply-btn.is-open {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .adv-apply-btn.is-open:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }

    .adv-apply-btn.is-open .adv-apply-chevron {
        transform: rotate(180deg);
        opacity: 1;
    }

    .adv-table-apply-btn {
        padding: 5px 10px;
        font-size: 11.5px;
        white-space: nowrap;
    }

    .adv-apply-inline-wrap {
        position: relative;
        display: inline-block;
    }

    .adv-apply-inline-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 4px;
        width: 300px;
        max-width: min(300px, 92vw);
        max-height: 280px;
        overflow-y: auto;
        overflow-x: hidden;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .12);
        z-index: 10055;
        text-align: left;
    }

    .adv-apply-inline-dropdown.open {
        display: block;
    }

    /* Viewport-anchored menu (aligned under the trigger button; avoids table/overflow misalignment) */
    .adv-apply-inline-dropdown.adv-apply-inline-dropdown--fixed {
        position: fixed;
        right: auto;
        margin-top: 0;
    }

    /* Pending row open: avoid clipping in table cells while menu is fixed to viewport */
    .adv-pending-card.adv-pending-card-dd-open td {
        overflow: visible;
    }

    /* All-tab row: no position on <tr>; inline Apply menu uses fixed + JS */
    .advance-row.adv-row-dd-open td {
        overflow: visible;
    }

    .adv-apply-dd-loading {
        padding: 14px 16px;
        font-size: 12.5px;
        color: #6b7280;
    }

    .adv-apply-opt-top {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 4px;
    }

    .adv-apply-opt-code {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }

    .adv-apply-opt-amt {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        flex-shrink: 0;
    }

    .adv-apply-opt-name {
        font-size: 12.5px;
        color: #374151;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .adv-apply-opt-range {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
    }

    .adv-apply-inline-item {
        display: block;
        width: 100%;
        padding: 12px 14px;
        border: none;
        border-bottom: 1px solid #ececec;
        background: #fff;
        cursor: pointer;
        text-align: left;
    }

    .adv-apply-inline-item:hover {
        background: #f9fafb;
    }

    .adv-apply-inline-item.adv-apply-inline-new {
        border-bottom: none;
        border-top: 1px solid #e5e7eb;
        margin-top: 0;
        padding-top: 12px;
        font-weight: 600;
        font-size: 12.5px;
        color: #2563eb;
    }

    .adv-apply-inline-item.adv-apply-inline-new:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    /* ── All-advances table tweaks ── */
    .adv-date-link {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
        font-size: 13px;
        border: none;
        background: none;
        padding: 0;
        font: inherit;
        cursor: pointer;
    }

    .adv-date-link:hover {
        text-decoration: underline;
    }

    .adv-report-name {
        font-size: 12.5px;
        color: #374151;
        font-weight: 500;
    }

    .adv-report-code {
        font-size: 11px;
        color: #8a94a6;
    }

    /* ── Empty state ── */
    .adv-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        gap: 12px;
        color: #9ca3af;
    }

    .adv-empty-state i {
        font-size: 36px;
        opacity: .5;
    }

    .adv-empty-state p {
        font-size: 14px;
        margin: 0;
    }

    /* ── Approve / Reject buttons in pending cards ── */
    .adv-approve-btn {
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #16a34a;
        border: 1.5px solid #bbf7d0;
        border-radius: 5px;
        background: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .12s, border-color .12s;
    }

    .adv-approve-btn:hover {
        background: #f0fdf4;
        border-color: #16a34a;
    }

    .adv-reject-btn {
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #dc2626;
        border: 1.5px solid #fecaca;
        border-radius: 5px;
        background: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .12s, border-color .12s;
    }

    .adv-reject-btn:hover {
        background: #fef2f2;
        border-color: #dc2626;
    }

    /* ── Bulk action bar ── */
    .adv-bulk-bar {
        background: #1e1b4b;
        color: #fff;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #312e81;
    }

    .adv-bulk-count {
        font-size: 13px;
        font-weight: 600;
        flex: 1;
    }

    .adv-bulk-btn {
        padding: 6px 16px;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 5px;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .12s;
    }

    .adv-bulk-approve {
        background: #16a34a;
        color: #fff;
    }

    .adv-bulk-approve:hover {
        background: #15803d;
    }

    .adv-bulk-reject {
        background: #dc2626;
        color: #fff;
    }

    .adv-bulk-reject:hover {
        background: #b91c1c;
    }

    .adv-bulk-cancel {
        background: transparent;
        color: #e5e7eb;
        border: 1px solid #4b5563 !important;
    }

    .adv-bulk-cancel:hover {
        background: rgba(255, 255, 255, .1);
    }

    /* ── Reject confirmation modal ── */
    .adv-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .adv-modal {
        background: #fff;
        border-radius: 12px;
        width: 420px;
        max-width: 95vw;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
    }

    .adv-modal-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .adv-modal-header button {
        background: none;
        border: none;
        font-size: 20px;
        color: #6b7280;
        cursor: pointer;
        line-height: 1;
    }

    .adv-modal-body {
        padding: 20px;
    }

    .adv-modal-footer {
        padding: 14px 20px;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    /* ── Stat card – rejected (red) ── */
    .qd-stat-red {
        border-left: 4px solid #dc2626;
    }

    .qd-stat-red .qd-stat-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    /* ── Advance Flow Pipeline bar ── */
    .adv-pipeline-wrap {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        gap: 0;
        background: #fafbff;
        border-bottom: 1px solid #e5e7eb;
        overflow-x: auto;
    }

    .adv-pipeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        padding: 6px 16px;
        border-radius: 10px;
        transition: background .13s;
        min-width: 90px;
        flex-shrink: 0;
    }

    .adv-pipeline-step:hover {
        background: #eef2ff;
    }

    .adv-pipeline-dot {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-bottom: 5px;
        transition: all .15s;
    }

    .adv-pipeline-step:hover .adv-pipeline-dot {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .adv-pl-pending .adv-pipeline-dot {
        background: #fef3c7;
        color: #d97706;
    }

    .adv-pl-approved .adv-pipeline-dot {
        background: #dcfce7;
        color: #16a34a;
    }

    .adv-pl-settled .adv-pipeline-dot {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .adv-pipeline-label {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-align: center;
        white-space: nowrap;
    }

    .adv-pipeline-count {
        font-size: 17px;
        font-weight: 700;
        color: #1a2332;
        line-height: 1.2;
        margin-top: 2px;
    }

    .adv-pipeline-arrow {
        color: #d1d5db;
        font-size: 14px;
        flex-shrink: 0;
        padding: 0 4px;
    }

    /* ── Loader skeleton ── */
    .adv-skeleton {
        padding: 16px 20px;
        display: flex;
        gap: 16px;
        align-items: center;
        border-bottom: 1px solid #f3f4f6;
    }

    .adv-sk-block {
        background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
        background-size: 400% 100%;
        border-radius: 4px;
        animation: skeletonShimmer 1.2s infinite;
    }

    @keyframes skeletonShimmer {
        0% {
            background-position: 100% 50%
        }

        100% {
            background-position: 0% 50%
        }
    }

    /* ── Zoho-style advance detail side panel ── */
    .adv-rpt-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .4);
        backdrop-filter: blur(2px);
        z-index: 1040;
    }

    .adv-rpt-overlay.open {
        display: block;
    }

    .adv-side-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: min(720px, 96vw);
        background: #fff;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        box-shadow: -8px 0 40px rgba(0, 0, 0, .18);
        border-left: 1px solid #e5e7eb;
        transform: translateX(100%);
        transition: transform .28s cubic-bezier(.4, 0, .2, 1);
        overflow: hidden;
    }

    .adv-side-panel.open {
        transform: translateX(0);
    }

    .adv-side-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #2563eb 0%, #4f46e5 50%, #7c3aed 100%);
        z-index: 1;
    }

    .adv-sp-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
        flex-shrink: 0;
        gap: 10px;
    }

    .adv-sp-title {
        font-size: 18px;
        font-weight: 800;
        color: #111827;
    }

    .adv-sp-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    .adv-sp-iconbtn {
        width: 34px;
        height: 34px;
        border: none;
        background: #f3f4f6;
        border-radius: 8px;
        cursor: pointer;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .adv-sp-iconbtn:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .adv-sp-body {
        flex: 1;
        overflow-y: auto;
        padding: 0 18px 20px;
    }

    .adv-sp-banner {
        margin-top: 14px;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 12.5px;
        color: #475569;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .adv-sp-banner i {
        color: #64748b;
        margin-top: 2px;
    }

    .adv-sp-associated {
        margin-top: 14px;
        padding: 12px 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        font-size: 13px;
    }

    .adv-sp-associated-k {
        font-weight: 700;
        color: #1e40af;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .adv-sp-associated a {
        color: #2563eb;
        font-weight: 600;
    }

    .adv-sp-summary {
        margin-top: 16px;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 14px;
    }

    @media (max-width: 480px) {
        .adv-sp-summary {
            grid-template-columns: 1fr;
        }
    }

    .adv-sp-amount-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 18px;
        text-align: center;
        background: #fff;
    }

    .adv-sp-amount-card i {
        font-size: 28px;
        color: #d97706;
        display: block;
        margin-bottom: 6px;
    }

    .adv-sp-amount-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
    }

    .adv-sp-amount-val {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        margin-top: 4px;
    }

    .adv-sp-meta {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13px;
    }

    .adv-sp-meta-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
        gap: 10px;
    }

    .adv-sp-meta-row:last-child {
        border-bottom: none;
    }

    .adv-sp-meta-k {
        color: #6b7280;
        width: 110px;
        flex-shrink: 0;
        font-weight: 500;
    }

    .adv-sp-meta-v {
        color: #111827;
        font-weight: 600;
        word-break: break-word;
        flex: 1;
    }

    .adv-sp-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        vertical-align: middle;
    }

    .adv-sp-tabs {
        display: flex;
        gap: 0;
        border-bottom: 1px solid #e5e7eb;
        margin-top: 18px;
    }

    .adv-sp-tab {
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
    }

    .adv-sp-tab.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }

    .adv-sp-tab-pane {
        display: none;
        padding-top: 14px;
    }

    .adv-sp-tab-pane.active {
        display: block;
    }

    .adv-sp-comment-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .adv-sp-comment-box textarea {
        flex: 1;
        border: none;
        resize: vertical;
        min-height: 72px;
        font-size: 13px;
        outline: none;
    }

    .adv-sp-history-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .adv-sp-history-item:last-child {
        border-bottom: none;
    }

    .adv-sp-hist-av {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .adv-sp-hist-meta {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .adv-sp-hist-msg {
        font-size: 13px;
        color: #1f2937;
        line-height: 1.45;
    }

    .adv-apply-dd-wrap {
        position: relative;
        margin-top: 10px;
    }

    .adv-apply-dd-menu {
        display: none;
        position: absolute;
        left: 0;
        top: 100%;
        margin-top: 4px;
        width: 300px;
        max-width: 100%;
        max-height: 280px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .12);
        z-index: 20;
    }

    .adv-apply-dd-menu.open {
        display: block;
    }

    .adv-apply-dd-item {
        display: block;
        width: 100%;
        padding: 12px 14px;
        border: none;
        border-bottom: 1px solid #ececec;
        cursor: pointer;
        font-size: 12.5px;
        text-align: left;
        background: #fff;
    }

    .adv-apply-dd-item:hover {
        background: #f9fafb;
    }

    .adv-apply-dd-item.adv-apply-dd-new {
        border-bottom: none;
        border-top: 1px solid #e5e7eb;
        font-weight: 600;
        color: #2563eb;
    }

    .adv-apply-dd-item.adv-apply-dd-new:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .adv-pending-table tbody tr.adv-pending-card.adv-row-active td.adv-ptd {
        background: #eef2ff;
    }

    .adv-pending-table tbody tr.adv-pending-card.adv-row-active {
        box-shadow: inset 3px 0 0 #4f46e5;
    }

    .advance-row.adv-row-active {
        background: #eef2ff;
    }

    .qd-stat-slate {
        border-left: 4px solid #64748b;
    }

    .qd-stat-slate .qd-stat-icon {
        background: #f1f5f9;
        color: #64748b;
    }

    .qd-stat-slate .qd-stat-value {
        color: #334155;
    }

    .adv-pipeline-step.adv-pl-draft .adv-pipeline-dot {
        background: #f1f5f9;
        color: #64748b;
    }

</style>

<body style="overflow-x: hidden;">
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="qd-card adv-dashboard">

                {{-- ── Header ── --}}
                <div class="qd-header">
                    <div class="qd-header-title">
                        <div>
                            <div><i class="bi bi-cash-coin"></i> Advances</div>
                            {{-- <div style="font-size:12px;font-weight:500;color:#6b7280;margin-top:4px;max-width:520px;line-height:1.4;">
                                Record cash advances, apply them to petty cash reports, approve and settle — same flow as Zoho Expense.
                                <a href="{{ route('superadmin.getpettycashreports') }}" class="link-primary" style="font-weight:600;white-space:nowrap;">Petty cash reports</a>
                            </div> --}}
                        </div>
                    </div>
                    <div class="qd-header-actions">
                        <button class="btn btn-sm qd-toggle-btn" id="toggleStats">
                            <i class="bi bi-bar-chart-line me-1"></i>Stats
                            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
                        </button>
                        <button class="btn btn-sm qd-toggle-btn" id="toggleFilters">
                            <i class="bi bi-funnel me-1"></i>Filter
                            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
                        </button>
                        <a href="{{ route('superadmin.getadvancescreate') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Record Advance
                        </a>
                    </div>
                </div>

                {{-- ── Stats ── --}}
                <div class="qd-stats" id="statsSection">
                    <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all">
                        <div class="qd-stat-icon"><i class="bi bi-cash-coin"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Total Advances</div>
                            <div class="qd-stat-value" id="stat-total">{{ $advancesStats['total'] }}</div>
                            <div class="qd-stat-sub" id="stat-total-amount">
                                ₹{{ number_format($advancesStats['total_amount'], 2) }}</div>
                        </div>
                    </div>
                    {{-- <div class="qd-stat-card qd-stat-slate" data-stat-filter="draft" title="Filter: Draft">
                        <div class="qd-stat-icon"><i class="bi bi-pencil-square"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Draft</div>
                            <div class="qd-stat-value" id="stat-draft">{{ $advancesStats['draft'] ?? 0 }}</div>
                            <div class="qd-stat-sub" id="stat-draft-amount">
                                ₹{{ number_format($advancesStats['draft_amount'] ?? 0, 2) }}</div>
                        </div>
                    </div> --}}
                    <div class="qd-stat-card qd-stat-orange" data-stat-filter="pending" title="Filter: Pending">
                        <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Pending</div>
                            <div class="qd-stat-value" id="stat-pending">{{ $advancesStats['pending'] }}</div>
                            <div class="qd-stat-sub" id="stat-pending-amount">
                                ₹{{ number_format($advancesStats['pending_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-green" data-stat-filter="applied" title="Filter: Approved / Paid">
                        <div class="qd-stat-icon"><i class="bi bi-cash-stack"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Approved / Paid</div>
                            <div class="qd-stat-value" id="stat-applied">{{ $advancesStats['applied'] }}</div>
                            <div class="qd-stat-sub" id="stat-applied-amount">
                                ₹{{ number_format($advancesStats['applied_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-purple" data-stat-filter="closed" title="Filter: Settled">
                        <div class="qd-stat-icon"><i class="bi bi-patch-check"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Settled</div>
                            <div class="qd-stat-value" id="stat-closed">{{ $advancesStats['closed'] }}</div>
                            <div class="qd-stat-sub" id="stat-closed-amount">
                                ₹{{ number_format($advancesStats['closed_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-red" data-stat-filter="rejected" title="Filter: Rejected">
                        <div class="qd-stat-icon"><i class="bi bi-x-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Rejected</div>
                            <div class="qd-stat-value" id="stat-rejected">{{ $advancesStats['rejected'] }}</div>
                            <div class="qd-stat-sub" id="stat-rejected-amount">
                                ₹{{ number_format($advancesStats['rejected_amount'], 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- ── Advance Flow Pipeline ── --}}
                {{-- <div class="adv-pipeline-wrap" id="statsSection2">
                    <div class="adv-pipeline-step" data-stat-filter="" title="All advances">
                        <div class="adv-pipeline-dot"><i class="bi bi-clipboard-plus"></i></div>
                        <div class="adv-pipeline-label">All</div>
                        <div class="adv-pipeline-count" id="pl-total">{{ $advancesStats['total'] }}</div>
                    </div>
                    <div class="adv-pipeline-arrow"><i class="bi bi-chevron-right"></i></div>
                    <div class="adv-pipeline-step adv-pl-draft" data-stat-filter="draft" title="Draft — not submitted">
                        <div class="adv-pipeline-dot"><i class="bi bi-pencil-square"></i></div>
                        <div class="adv-pipeline-label">Draft</div>
                        <div class="adv-pipeline-count" id="pl-draft">{{ $advancesStats['draft'] ?? 0 }}</div>
                    </div>
                    <div class="adv-pipeline-arrow"><i class="bi bi-chevron-right"></i></div>
                    <div class="adv-pipeline-step adv-pl-pending" data-stat-filter="pending" title="Awaiting approval">
                        <div class="adv-pipeline-dot"><i class="bi bi-hourglass-split"></i></div>
                        <div class="adv-pipeline-label">Pending Approval</div>
                        <div class="adv-pipeline-count" id="pl-pending">{{ $advancesStats['pending'] }}</div>
                    </div>
                    <div class="adv-pipeline-arrow"><i class="bi bi-chevron-right"></i></div>
                    <div class="adv-pipeline-step adv-pl-approved" data-stat-filter="applied" title="Approved & disbursed">
                        <div class="adv-pipeline-dot"><i class="bi bi-cash-stack"></i></div>
                        <div class="adv-pipeline-label">Approved &amp; Paid</div>
                        <div class="adv-pipeline-count" id="pl-applied">{{ $advancesStats['applied'] }}</div>
                    </div>
                    <div class="adv-pipeline-arrow"><i class="bi bi-chevron-right"></i></div>
                    <div class="adv-pipeline-step adv-pl-settled" data-stat-filter="closed" title="Settled & closed">
                        <div class="adv-pipeline-dot"><i class="bi bi-patch-check-fill"></i></div>
                        <div class="adv-pipeline-label">Settled</div>
                        <div class="adv-pipeline-count" id="pl-closed">{{ $advancesStats['closed'] }}</div>
                    </div>
                </div> --}}

                {{-- ── Tab Bar ── --}}
                <div class="report-tabs-wrapper">
                    <div class="report-tabs">
                        <button class="report-tab active" data-adv-tab="pending">
                            <svg width="14" height="14" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            Pending Advances
                            <span class="tab-count-badge" id="tab-badge-pending">0</span>
                        </button>
                        {{-- <button class="report-tab" data-adv-tab="draft">
                            <svg width="14" height="14" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Draft
                            <span class="tab-count-badge" id="tab-badge-draft">0</span>
                        </button> --}}
                        <button class="report-tab" data-adv-tab="all">
                            <svg width="14" height="14" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                                <line x1="3" y1="6" x2="3.01" y2="6" />
                                <line x1="3" y1="12" x2="3.01" y2="12" />
                                <line x1="3" y1="18" x2="3.01" y2="18" />
                            </svg>
                            All Advances
                            <span class="tab-count-badge" id="tab-badge-all">0</span>
                        </button>
                    </div>
                    <a href="{{ route('superadmin.getadvancescreate') }}" class="adv-tab-new-advance"
                        title="New Advance" aria-label="New Advance">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>

                {{-- ── Filters ── --}}
                <div class="qd-filters" id="filtersSection">
                    <div class="qd-filter-row">
                        <div class="qd-filter-group">
                            <label><i class="bi bi-calendar3 me-1"></i>Date Range</label>
                            <div class="qd-date-wrap" id="reportrange">
                                <i class="fa fa-calendar"></i>
                                <span id="data_values">All Dates</span>
                                <i class="fa fa-caret-down" style="margin-left:auto;"></i>
                                <input type="hidden" class="data_values">
                            </div>
                        </div>

                        <div class="qd-filter-group tax-dropdown-wrapper company-section">
                            <label>Company</label>
                            <input type="text" class="form-control company-search-input dropdown-search-input"
                                placeholder="Select Company" readonly>
                            <input type="hidden" name="company_id" class="company_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Company..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect company-list"></div>
                            </div>
                        </div>

                        <div class="qd-filter-group tax-dropdown-wrapper zone-section">
                            <label>Zone</label>
                            <input type="text" class="form-control zone-search-input dropdown-search-input"
                                placeholder="Select Zone" readonly>
                            <input type="hidden" name="zone_id" class="zone_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Zone..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect zone-list"></div>
                            </div>
                        </div>

                        {{-- <div class="qd-filter-row" style="margin-top:10px;"> --}}
                            <div class="qd-filter-group tax-dropdown-wrapper branch-section">
                                <label>Branch</label>
                                <input type="text" class="form-control branch-search-input dropdown-search-input"
                                    placeholder="Select Branch" readonly>
                                <input type="hidden" name="branch_id" class="branch_id">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="inner-search-container"><input type="text" class="inner-search"
                                            placeholder="Search Branch..."></div>
                                    <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary select-all">All</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                    </div>
                                    <div class="dropdown-list multiselect branch-list"></div>
                                </div>
                            </div>
                        {{-- </div> --}}

                        {{-- Status filter is hidden on pending tab (auto-filtered) --}}
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section" id="status-filter-group">
                            <label>Status</label>
                            <input type="text" class="form-control status-search-input dropdown-search-input"
                                placeholder="Select Status" readonly>
                            <input type="hidden" name="status_id" class="status_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Status..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect status-list">
                                    <div data-value="pending" data-id="pending">Pending Approval</div>
                                    <div data-value="applied" data-id="applied">Approved / Paid</div>
                                    <div data-value="closed" data-id="closed">Settled</div>
                                    <div data-value="rejected" data-id="rejected">Rejected</div>
                                    <div data-value="draft" data-id="draft">Draft</div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                {{-- ── Search bar ── --}}
                <div class="qd-search-row">
                    <div class="qd-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="universal_search" placeholder="Search advances...">
                    </div>
                </div>

                {{-- ── Applied filters ── --}}
                <div class="qd-applied-bar">
                    <span class="applied-label">Filters:</span>
                    <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>

                {{-- ── Bulk action bar (visible when checkboxes selected on Pending tab) ── --}}
                <div id="adv-bulk-bar" class="adv-bulk-bar" style="display:none;">
                    <span class="adv-bulk-count"><span id="adv-bulk-count">0</span> advance(s) selected</span>
                    <button id="adv-bulk-approve" class="adv-bulk-btn adv-bulk-approve">
                        <i class="bi bi-check-lg"></i> Approve
                    </button>
                    <button id="adv-bulk-reject" class="adv-bulk-btn adv-bulk-reject">
                        <i class="bi bi-x-lg"></i> Reject
                    </button>
                    <button id="adv-bulk-cancel" class="adv-bulk-btn adv-bulk-cancel">
                        Cancel
                    </button>
                </div>

                {{-- ── Table / Cards ── --}}
                <div class="qd-table-wrap">
                    <div id="advances-body"></div>
                </div>

                {{-- ── Pagination ── --}}
                <div class="qd-pagination" id="advances-pagination">
                    <div id="advances-links"></div>
                    <div>
                        <form class="d-flex align-items-center gap-2">
                            <select id="per_page" class="form-control form-control-sm" style="width:80px;">
                                <option value="10">10</option>
                                <option value="15" selected>15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span style="font-size:12px;color:#8a94a6;">entries</span>
                        </form>
                    </div>
                </div>

            </div>{{-- /qd-card --}}
        </div>
    </div>

    {{-- ── Zoho-style advance detail (slide-over) ── --}}
    <div id="adv-rpt-overlay" class="adv-rpt-overlay" aria-hidden="true"></div>
    <aside id="adv-side-panel" class="adv-side-panel" aria-hidden="true">
        <div class="adv-sp-header">
            <div>
                <div class="adv-sp-title" id="adv-sp-date-title">—</div>
                <div style="font-size:12px;color:#6b7280;margin-top:2px;" id="adv-sp-status-chip"></div>
            </div>
            <div class="adv-sp-actions">
                <a href="#" class="adv-sp-iconbtn" id="adv-sp-edit" title="Edit"><i
                        class="bi bi-pencil"></i></a>
                <button type="button" class="adv-sp-iconbtn" id="adv-sp-close" title="Close"><i
                        class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="adv-sp-body">
            <div id="adv-sp-whatsnext" style="display:none;">
                <div class="adv-sp-banner">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong style="color:#334155;">What's next</strong>
                        <div style="margin-top:4px;">Apply this advance to the petty cash report that includes expenses
                            paid from this advance.</div>
                        <div class="adv-apply-dd-wrap">
                            <button type="button" class="adv-apply-btn" id="adv-apply-dd-toggle">
                                Apply to Report <i class="bi bi-chevron-down adv-apply-chevron"></i>
                            </button>
                            <div class="adv-apply-dd-menu" id="adv-apply-dd-menu"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="adv-sp-associated" style="display:none;">
                <div class="adv-sp-associated">
                    <div class="adv-sp-associated-k"><i class="bi bi-folder2-open"></i> Associated report</div>
                    <a href="#" id="adv-sp-report-link">—</a>
                </div>
            </div>
            <div class="adv-sp-summary">
                <div class="adv-sp-amount-card">
                    <i class="bi bi-coin"></i>
                    <div class="adv-sp-amount-label">Advance amount</div>
                    <div class="adv-sp-amount-val" id="adv-sp-amount-big">—</div>
                </div>
                <div class="adv-sp-meta">
                    <div class="adv-sp-meta-row"><span class="adv-sp-meta-k">Date</span><span class="adv-sp-meta-v"
                            id="adv-sp-meta-date">—</span></div>
                    <div class="adv-sp-meta-row"><span class="adv-sp-meta-k">Reference#</span><span
                            class="adv-sp-meta-v" id="adv-sp-meta-ref">—</span></div>
                    <div class="adv-sp-meta-row"><span class="adv-sp-meta-k">Branch</span><span
                            class="adv-sp-meta-v" id="adv-sp-meta-branch">—</span></div>
                    <div class="adv-sp-meta-row"><span class="adv-sp-meta-k">Recorded by</span><span
                            class="adv-sp-meta-v" id="adv-sp-meta-by">—</span></div>
                    <div class="adv-sp-meta-row"><span class="adv-sp-meta-k">Notes</span><span class="adv-sp-meta-v"
                            id="adv-sp-meta-notes">—</span></div>
                    <div class="adv-sp-meta-row"><span class="adv-sp-meta-k">Paid through</span><span
                            class="adv-sp-meta-v" id="adv-sp-meta-paid">—</span></div>
                </div>
            </div>
            <div class="adv-sp-tabs">
                <button type="button" class="adv-sp-tab active" data-adv-pane="comments">Comments</button>
                <button type="button" class="adv-sp-tab" data-adv-pane="history">History</button>
            </div>
            <div class="adv-sp-tab-pane active" id="adv-pane-comments">
                <div class="adv-sp-comment-box">
                    <span class="adv-sp-avatar" id="adv-comment-av">—</span>
                    <textarea placeholder="Add your comment here (coming soon)" disabled></textarea>
                </div>
            </div>
            <div class="adv-sp-tab-pane" id="adv-pane-history">
                <div id="adv-history-list"></div>
            </div>
        </div>
    </aside>

    @include('superadmin.superadminfooter')

    {{-- ── Rejection confirmation modal ── --}}
    <div id="rejectModal" class="adv-modal-overlay" style="display:none;">
        <div class="adv-modal">
            <div class="adv-modal-header">
                <span><i class="bi bi-x-circle me-2" style="color:#dc2626;"></i>Reject Advance</span>
                <button id="rejectModalClose">&times;</button>
            </div>
            <div class="adv-modal-body">
                <p style="color:#6b7280;margin:0;">Are you sure you want to reject the selected advance(s)? This action
                    cannot be undone.</p>
            </div>
            <div class="adv-modal-footer">
                <button id="rejectModalCancel" class="btn btn-sm btn-outline-secondary">Cancel</button>
                <button id="rejectModalConfirm" class="btn btn-sm btn-danger">
                    <i class="bi bi-x-lg me-1"></i>Confirm Reject
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('/assets/js/vendor/pettycash_search.js') }}"></script>

    <script>
        const TblZonesModel = @json($TblZonesModel);
        const Tblcompany = @json($Tblcompany);

        (function() {
            const zonesArr = Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data ?? []);
            zonesArr.forEach(function(z) {
                $('.zone-list').append($('<div>').attr('data-id', z.id).attr('data-value', z.name).text(z
                .name));
            });
            const companies = Array.isArray(Tblcompany) ? Tblcompany : (Tblcompany.data ?? []);
            companies.forEach(function(c) {
                var name = c.company_name || '';
                $('.company-list').append($('<div>').attr('data-id', c.id).attr('data-value', name).text(name));
            });
        })();

        var currentTab = 'pending';

        var pendingOpenAdvanceId = null;
        try {
            var __advOpen = new URLSearchParams(window.location.search).get('open_advance');
            if (__advOpen && String(__advOpen).match(/^\d+$/)) {
                pendingOpenAdvanceId = parseInt(__advOpen, 10);
            }
        } catch (e) {}

        var filters = {
            date_from: '',
            date_to: '',
            zone_id: '',
            zone_name: '',
            branch_id: '',
            branch_name: '',
            company_id: '',
            company_name: '',
            status_name: '',
            status_id: '',
            universal_search: '',
        };

        function fmt(n) {
            return '₹' + (parseFloat(n) || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function advNumFmt(n) {
            return (parseFloat(n) || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function advEsc(s) {
            if (s === null || s === undefined) return '';
            return $('<div>').text(String(s)).html();
        }

        var advPanelOpenId = null;
        var advDetailUrl = "{{ route('superadmin.advancedetail') }}";
        var advReportsListUrl = "{{ route('superadmin.getreportsforadvance') }}";
        var advLinkReportUrl = "{{ route('superadmin.linkreport') }}";
        var pettyCashReportsBase = "{{ route('superadmin.getpettycashreports') }}";
        var advReportsLoaded = false;
        var advReportsCache = null;
        var advPanelOpenOpts = {};

        function invalidateAdvReportsCache() {
            advReportsCache = null;
            advReportsLoaded = false;
        }

        function positionAdvInlineApplyDropdown($btn, $dd) {
            if (!$btn || !$btn.length || !$dd || !$dd.length) return;
            var el = $btn[0];
            if (!el.getBoundingClientRect) return;
            var r = el.getBoundingClientRect();
            var pad = 4;
            var maxW = Math.min(300, Math.max(200, window.innerWidth * 0.92));
            var left = r.right - maxW;
            var margin = 8;
            if (left < margin) left = margin;
            if (left + maxW > window.innerWidth - margin) {
                left = window.innerWidth - margin - maxW;
            }
            var top = r.bottom + pad;
            $dd.addClass('adv-apply-inline-dropdown--fixed').css({
                top: top + 'px',
                left: left + 'px',
                width: maxW + 'px',
                maxWidth: maxW + 'px',
                zIndex: 10070
            });
        }

        function syncOpenAdvInlineApplyDropdowns() {
            $('.adv-apply-inline-dropdown.open').each(function () {
                var $dd = $(this);
                var $btn = $dd.closest('.adv-apply-inline-wrap').find('.adv-inline-apply-btn').first();
                positionAdvInlineApplyDropdown($btn, $dd);
            });
        }

        function closeInlineApplyMenus() {
            $('.adv-apply-inline-dropdown').each(function () {
                $(this).removeClass('open adv-apply-inline-dropdown--fixed').empty().removeAttr('style');
            });
            $('.adv-inline-apply-btn').removeClass('is-open');
            $('.adv-pending-card').removeClass('adv-pending-card-dd-open');
            $('.advance-row').removeClass('adv-row-dd-open');
        }

        function appendReportOptionsZohoStyle($menu, rows, itemClass) {
            (rows || []).forEach(function(r) {
                var code = r.report_id || ('#' + r.id);
                var name = (r.report_name || '').trim();
                var total = parseFloat(r.expenses_total) || 0;
                var sd = r.start_date ? moment(r.start_date).format('DD/MM/YYYY') : '';
                var ed = r.end_date ? moment(r.end_date).format('DD/MM/YYYY') : '';
                var rangeStr = (sd && ed) ? (sd + '-' + ed) : ((sd || ed) || '');
                var $btn = $('<button type="button"/>').addClass(itemClass);
                var $top = $('<div class="adv-apply-opt-top"/>');
                $top.append($('<span class="adv-apply-opt-code"/>').text(code));
                $top.append($('<span class="adv-apply-opt-amt"/>').text('Rs.' + advNumFmt(total)));
                $btn.append($top);
                if (name) {
                    $btn.append($('<div class="adv-apply-opt-name"/>').text(name));
                }
                if (rangeStr) {
                    $btn.append($('<div class="adv-apply-opt-range"/>').text(rangeStr));
                }
                $btn.attr('data-report-id', r.id);
                $menu.append($btn);
            });
        }

        function closeAdvDetailPanel() {
            $('#adv-rpt-overlay, #adv-side-panel').removeClass('open');
            $('#adv-apply-dd-menu').removeClass('open').empty();
            $('#adv-apply-dd-toggle').removeClass('is-open');
            $('.adv-pending-card, .advance-row').removeClass('adv-row-active');
            advPanelOpenId = null;
        }

        function formatAdvHistAt(at) {
            if (!at) return '';
            try {
                return moment(at).format('DD/MM/YYYY hh:mm A');
            } catch (e) {
                return String(at);
            }
        }

        function renderAdvHistory(list) {
            console.log(list);
            var $w = $('#adv-history-list').empty();

            if (!list || !list.length) {
                $w.append($('<p style="color:#9ca3af;font-size:13px;padding:4px 0 8px;">No history yet.</p>'));
                return;
            }
            list.forEach(function(h) {
                var initials = h.actor_initials || '—';
                var name = h.actor_name || 'System';
                var $row = $('<div class="adv-sp-history-item"/>');
                $row.append($('<div class="adv-sp-hist-av"/>').text(initials));
                var $body = $('<div style="flex:1;min-width:0;"/>');
                $body.append($('<div class="adv-sp-hist-meta"/>').text(name + ' · ' + formatAdvHistAt(h.at)));
                $body.append($('<div class="adv-sp-hist-msg"/>').text(h.message || ''));
                $row.append($body);
                $w.append($row);
            });
        }

        function fillAdvPanel(d) {
            var a = d.advance || {};
            advPanelOpenId = a.id;
            $('#adv-sp-date-title').text(a.advance_date_fmt || '—');
            var chip = (a.status || '').replace(/_/g, ' ');
            $('#adv-sp-status-chip').text(chip ? chip.charAt(0).toUpperCase() + chip.slice(1) : '');
            $('#adv-sp-edit').attr('href', d.edit_url || '#');
            var pref = a.currency_prefix || 'Rs.';
            $('#adv-sp-amount-big').text(pref + advNumFmt(a.advance_amount));
            $('#adv-sp-meta-date').text(a.advance_date_fmt || '—');
            $('#adv-sp-meta-ref').text(a.reference_no || '—');
            $('#adv-sp-meta-branch').text(a.branch_name || '—');
            var by = d.recorded_by_name || '—';
            if (d.recorded_by_initials) {
                $('#adv-sp-meta-by').html('<span class="adv-sp-avatar">' + advEsc(d.recorded_by_initials) + '</span> ' +
                    advEsc(by));
            } else {
                $('#adv-sp-meta-by').text(by);
            }
            $('#adv-comment-av').text((d.recorded_by_initials || '—').toString().substring(0, 2));
            $('#adv-sp-meta-notes').text(a.notes || '—');
            $('#adv-sp-meta-paid').text(a.paid_through || '—');

            if (d.linked_report && d.linked_report.id) {
                $('#adv-sp-whatsnext').hide();
                $('#adv-sp-associated').show();
                var title = d.linked_report.report_name || d.linked_report.report_id || 'Report';
                $('#adv-sp-report-link').text(title).attr('href', pettyCashReportsBase);
            } else {
                $('#adv-sp-associated').hide();
                $('#adv-sp-whatsnext').toggle(!!d.show_apply_banner);
            }

            renderAdvHistory(d.history || []);
        }

        function openAdvanceDetailPanel(id, opts) {
            if (!id) return;
            advPanelOpenId = id;
            advPanelOpenOpts = opts || {};
            closeInlineApplyMenus();
            $('.adv-pending-card, .advance-row').removeClass('adv-row-active');
            $('.adv-pending-card[data-id="' + id + '"], .advance-row[data-id="' + id + '"]').addClass('adv-row-active');
            $('#adv-rpt-overlay, #adv-side-panel').addClass('open');
            $('#adv-apply-dd-menu').removeClass('open');
            $('.adv-sp-tab').removeClass('active').filter('[data-adv-pane="comments"]').addClass('active');
            $('.adv-sp-tab-pane').removeClass('active');
            $('#adv-pane-comments').addClass('active');
            $('#adv-sp-date-title').text('Loading…');
            $.getJSON(advDetailUrl, {
                advance_id: id
            }, function(r) {
                if (!r.success) {
                    toastr.error(r.message || 'Could not load advance.');
                    closeAdvDetailPanel();
                    return;
                }
                fillAdvPanel(r);
                if (advPanelOpenOpts.tab === 'history') {
                    $('.adv-sp-tab').removeClass('active');
                    $('.adv-sp-tab[data-adv-pane="history"]').addClass('active');
                    $('.adv-sp-tab-pane').removeClass('active');
                    $('#adv-pane-history').addClass('active');
                }
            }).fail(function() {
                toastr.error('Could not load advance.');
                closeAdvDetailPanel();
            });
        }

        function loadAdvReportsForDropdown() {
            var $m = $('#adv-apply-dd-menu');

            function paint(rows) {
                $m.empty();
                appendReportOptionsZohoStyle($m, rows, 'adv-apply-dd-item');
                $m.append($(
                        '<button type="button" class="adv-apply-dd-item adv-apply-dd-new" id="adv-apply-new-report"/>'
                    ).html('<i class="bi bi-plus-lg me-1"></i>Add To New Report'));
            }
            if (advReportsCache !== null) {
                paint(advReportsCache);
                advReportsLoaded = true;
                return;
            }
            $.getJSON(advReportsListUrl, function(rows) {
                advReportsCache = rows || [];
                paint(advReportsCache);
                advReportsLoaded = true;
            });
        }

        function setStats(s) {
            if (!s) return;
            $('#stat-total').text(s.total);
            $('#stat-total-amount').text(fmt(s.total_amount));
            $('#stat-draft').text(s.draft || 0);
            $('#stat-draft-amount').text(fmt(s.draft_amount || 0));
            $('#stat-pending').text(s.pending);
            $('#stat-pending-amount').text(fmt(s.pending_amount));
            $('#stat-applied').text(s.applied);
            $('#stat-applied-amount').text(fmt(s.applied_amount));
            $('#stat-closed').text(s.closed);
            $('#stat-closed-amount').text(fmt(s.closed_amount));
            $('#stat-rejected').text(s.rejected || 0);
            $('#stat-rejected-amount').text(fmt(s.rejected_amount || 0));
            $('#tab-badge-pending').text(s.pending || 0);
            $('#tab-badge-draft').text(s.draft || 0);
            $('#tab-badge-all').text(s.total || 0);
            // pipeline counts
            $('#pl-total').text(s.total || 0);
            $('#pl-draft').text(s.draft || 0);
            $('#pl-pending').text(s.pending || 0);
            $('#pl-applied').text(s.applied || 0);
            $('#pl-closed').text(s.closed || 0);
        }

        function switchAdvancesTabToAll() {
            currentTab = 'all';
            $('.report-tab').removeClass('active');
            $('[data-adv-tab="all"]').addClass('active');
            $('#status-filter-group').show();
        }

        function fetchAdvances(page, done) {
            page = page || 1;

            // Show skeleton while loading
            $('#advances-body').html(
                '<div class="adv-skeleton"><div class="adv-sk-block" style="width:16px;height:16px;"></div><div class="adv-sk-block" style="width:140px;height:18px;"></div><div class="adv-sk-block" style="flex:1;height:18px;"></div><div class="adv-sk-block" style="width:100px;height:18px;"></div></div>'
                .repeat(4)
            );

            $.ajax({
                url: '{{ route('superadmin.getadvancesajax') }}',
                type: 'GET',
                data: {
                    page: page,
                    per_page: $('#per_page').val() || 15,
                    tab: currentTab,
                    date_from: filters.date_from || undefined,
                    date_to: filters.date_to || undefined,
                    zone_id: filters.zone_id || undefined,
                    branch_id: filters.branch_id || undefined,
                    company_id: filters.company_id || undefined,
                    status_name: filters.status_name || undefined,
                    universal_search: filters.universal_search || undefined,
                },
                success: function(r) {
                    $('#advances-body').html(r.html ||
                        '<div class="adv-empty-state"><i class="bi bi-inbox"></i><p>No advance records found.</p></div>'
                        );
                    $('#advances-links').html(r.pagination || '');
                    setStats(r.stats);
                    $('#adv-bulk-bar').hide();

                    closeInlineApplyMenus();
                    if (typeof done === 'function') {
                        done(r);
                    }
                },
                error: function() {
                    $('#advances-body').html(
                        '<div class="p-4 text-danger text-center">Unable to load advances. Please try again.</div>'
                        );
                }
            });
        }

        function renderSummary() {
            var html = '';
            if (filters.date_from) html += '<span class="filter-badge remove-icon" data-type="date">' + filters.date_from +
                ' → ' + filters.date_to + '</span>';
            if (filters.zone_id) html += '<span class="filter-badge remove-icon" data-type="zone">' + filters.zone_name +
                '</span>';
            if (filters.branch_id) html += '<span class="filter-badge remove-icon" data-type="branch">' + filters
                .branch_name + '</span>';
            if (filters.company_id) html += '<span class="filter-badge remove-icon" data-type="company">' + filters
                .company_name + '</span>';
            if (filters.status_id) html += '<span class="filter-badge remove-icon" data-type="status">' + filters
                .status_name + '</span>';
            if (html) html += '<span class="filter-badge filter-clear" id="clear-all">Clear all</span>';
            $('#filter-summary').html(html);
        }

        $(document).ready(function() {

            // ── Tab switching ──
            $(document).on('click', '.report-tab[data-adv-tab]', function() {
                currentTab = $(this).data('adv-tab');
                $('.report-tab').removeClass('active');
                $(this).addClass('active');

                // Hide status filter on Pending / Draft tabs (server-side list)
                if (currentTab === 'pending' || currentTab === 'draft') {
                    $('#status-filter-group').hide();
                    filters.status_name = '';
                    filters.status_id = '';
                    $('.status-search-input').val('');
                    $('.status_id').val('');
                    $('.status-list div').removeClass('selected');
                } else {
                    $('#status-filter-group').show();
                }

                fetchAdvances(1);
            });

            // ── Stats card + pipeline step click ──
            $(document).on('click', '.qd-stat-card[data-stat-filter], .adv-pipeline-step[data-stat-filter]',
                function() {
                var val = $(this).data('stat-filter');
                filters.status_name = val ? String(val).toLowerCase() : '';
                    filters.status_id = filters.status_name;
                var label = $(this).find('.qd-stat-label').text().trim();
                $('.status-search-input').val(val ? label : '');
                $('.status_id').val(filters.status_name);
                $('.status-list div').removeClass('selected');
                $('.qd-stat-card').removeClass('qd-stat-active');
                $(this).addClass('qd-stat-active');

                if (val === 'pending') {
                    currentTab = 'pending';
                    $('.report-tab').removeClass('active');
                    $('[data-adv-tab="pending"]').addClass('active');
                    $('#status-filter-group').hide();
                        filters.status_name = '';
                        filters.status_id = '';
                        $('.status-search-input').val('');
                        $('.status_id').val('');
                    } else if (val === 'draft') {
                        currentTab = 'draft';
                        $('.report-tab').removeClass('active');
                        $('[data-adv-tab="draft"]').addClass('active');
                        $('#status-filter-group').hide();
                        filters.status_name = '';
                        filters.status_id = '';
                        $('.status-search-input').val('');
                        $('.status_id').val('');
                    } else if (val === '') {
                        currentTab = 'all';
                        $('.report-tab').removeClass('active');
                        $('[data-adv-tab="all"]').addClass('active');
                        $('#status-filter-group').show();
                        filters.status_name = '';
                        filters.status_id = '';
                        $('.status-search-input').val('');
                        $('.status_id').val('');
                    } else {
                    currentTab = 'all';
                    $('.report-tab').removeClass('active');
                    $('[data-adv-tab="all"]').addClass('active');
                    $('#status-filter-group').show();
                }

                fetchAdvances(1);
                renderSummary();
            });

            // ── Toggle Stats / Filters ──
            $('#toggleStats').on('click', function() {
                $('#statsSection').slideToggle(200);
                $('#statsChevron').toggleClass('bi-chevron-up bi-chevron-down');
            });
            $('#toggleFilters').on('click', function() {
                $('#filtersSection').slideToggle(200);
                $('#filtersChevron').toggleClass('bi-chevron-up bi-chevron-down');
            });

            // ── Dropdown search input open ──
            $(document).on('click', '.dropdown-search-input', function(e) {
                e.stopPropagation();
                $('.dropdown-menu.tax-dropdown').hide();
                var $input = $(this);
                var $dropdown = $input.data('dropdown');
                if (!$dropdown) {
                    $dropdown = $input.siblings('.dropdown-menu').clone(true);
                    $('body').append($dropdown);
                    $input.data('dropdown', $dropdown);
                }
                $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                var offset = $input.offset();
                $dropdown.css({
                    position: 'absolute',
                    top: offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: $input.outerWidth(),
                    zIndex: 9999
                }).show();
                $dropdown.find('.inner-search').focus();
            });

            $(document).on('keyup', '.inner-search', function() {
                var q = $(this).val().toLowerCase();
                $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
                });
            });

            function updateMultiSelection($dropdown) {
                var wrapper = $dropdown.data('wrapper');
                if (!wrapper) return;
                var items = [],
                    ids = [];
                $dropdown.find('.dropdown-list.multiselect div.selected').each(function() {
                    items.push($(this).text().trim());
                    ids.push($(this).data('id'));
                });
                wrapper.find('.dropdown-search-input').val(items.join(', '));
                wrapper.find('input[type="hidden"]').val(ids.join(','));
                wrapper.find('input[type="hidden"]').trigger('click');
            }

            $(document).on('click', '.dropdown-list.multiselect div', function(e) {
                e.stopPropagation();
                $(this).toggleClass('selected');
                updateMultiSelection($(this).closest('.dropdown-menu'));
            });
            $(document).on('click', '.select-all', function(e) {
                e.stopPropagation();
                var $d = $(this).closest('.dropdown-menu');
                $d.find('.dropdown-list.multiselect div').addClass('selected');
                updateMultiSelection($d);
            });
            $(document).on('click', '.deselect-all', function(e) {
                e.stopPropagation();
                var $d = $(this).closest('.dropdown-menu');
                $d.find('.dropdown-list.multiselect div').removeClass('selected');
                updateMultiSelection($d);
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest(
                        '.tax-dropdown').length) {
                    $('.dropdown-menu.tax-dropdown').hide();
                }
            });
            $(document).on('click', '.dropdown-menu.tax-dropdown', function(e) {
                e.stopPropagation();
            });

            // ── Hidden field click handlers ──
            function setupFilterField(hiddenSel, inputSel, filterKey, nameKey) {
                $(document).on('click', hiddenSel, function() {
                    filters[filterKey] = $(this).val();
                    filters[nameKey] = $(inputSel).val();
                    fetchAdvances(1);
                    renderSummary();
                });
            }
            setupFilterField('.zone_id', '.zone-search-input', 'zone_id', 'zone_name');
            setupFilterField('.branch_id', '.branch-search-input', 'branch_id', 'branch_name');
            setupFilterField('.company_id', '.company-search-input', 'company_id', 'company_name');
            setupFilterField('.status_id', '.status-search-input', 'status_id', 'status_name');

            // ── Zone → Branch AJAX ──
            $('.zone_id').on('click', function() {
                var id = $(this).val();
                if (!id) return;
                $.post('{{ route('superadmin.getbranchfetch') }}', {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(res) {
                    $('.branch-list div').remove();
                    (res.branch || []).forEach(function(b) {
                        $('.branch-list').append($('<div>').attr('data-id', b.id).attr(
                            'data-value', b.name).text(b.name));
                    });
                });
            });
            $(document).on('click', '.zone-list div', function() {
                $('.branch-search-input').val('');
                $('.branch_id').val('');
            });

            // ── Date range picker ──
            if (typeof $.fn.daterangepicker !== 'undefined') {
                $('#reportrange').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'DD/MM/YYYY'
                    }
                }, function(start, end) {
                    filters.date_from = start.format('DD/MM/YYYY');
                    filters.date_to = end.format('DD/MM/YYYY');
                    $('#data_values').text(start.format('DD/MM/YYYY') + ' – ' + end.format('DD/MM/YYYY'));
                    fetchAdvances(1);
                    renderSummary();
                });
                $('#reportrange').on('cancel.daterangepicker', function() {
                    filters.date_from = '';
                    filters.date_to = '';
                    $('#data_values').text('All Dates');
                    fetchAdvances(1);
                    renderSummary();
                });
            }

            // ── Universal search ──
            var searchTimer;
            $('.universal_search').on('keyup', function() {
                clearTimeout(searchTimer);
                var q = $(this).val();
                searchTimer = setTimeout(function() {
                    filters.universal_search = q;
                    fetchAdvances(1);
                }, 350);
            });

            // ── Per page ──
            $(document).on('change', '#per_page', function() {
                fetchAdvances(1);
            });

            // ── Pagination ──
            $(document).on('click', '#advances-pagination .pagination a', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                var page = 1;
                try {
                    page = new URL(href, window.location.href).searchParams.get('page') || 1;
                } catch (err) {}
                fetchAdvances(page);
            });

            // ── Filter badge removal ──
            $('#filter-summary').on('click', '.remove-icon', function() {
                var type = $(this).data('type');
                if (type === 'date') {
                    filters.date_from = '';
                    filters.date_to = '';
                    $('#data_values').text('All Dates');
                }
                if (type === 'zone') {
                    filters.zone_id = '';
                    filters.zone_name = '';
                    $('.zone_id').val('');
                    $('.zone-search-input').val('');
                    $('.zone-list div').removeClass('selected');
                }
                if (type === 'branch') {
                    filters.branch_id = '';
                    filters.branch_name = '';
                    $('.branch_id').val('');
                    $('.branch-search-input').val('');
                    $('.branch-list div').removeClass('selected');
                }
                if (type === 'company') {
                    filters.company_id = '';
                    filters.company_name = '';
                    $('.company_id').val('');
                    $('.company-search-input').val('');
                    $('.company-list div').removeClass('selected');
                }
                if (type === 'status') {
                    filters.status_id = '';
                    filters.status_name = '';
                    $('.status_id').val('');
                    $('.status-search-input').val('');
                    $('.status-list div').removeClass('selected');
                }
                fetchAdvances(1);
                renderSummary();
            });

            $('#filter-summary').on('click', '#clear-all', function() {
                filters = {
                    date_from: '',
                    date_to: '',
                    zone_id: '',
                    zone_name: '',
                    branch_id: '',
                    branch_name: '',
                    company_id: '',
                    company_name: '',
                    status_name: '',
                    status_id: '',
                    universal_search: ''
                };
                $('.zone-search-input,.branch-search-input,.company-search-input,.status-search-input').val(
                    '');
                $('.zone_id,.branch_id,.company_id,.status_id').val('');
                $('#data_values').text('All Dates');
                $('.dropdown-list div').removeClass('selected');
                $('.universal_search').val('');
                $('.qd-stat-card').removeClass('qd-stat-active');
                $('.qd-stat-card[data-stat-filter=""]').addClass('qd-stat-active');
                fetchAdvances(1);
                renderSummary();
            });

            // ── Initial state: pending tab active, hide status filter ──
            $('#status-filter-group').hide();
            fetchAdvances(1, function() {
                if (pendingOpenAdvanceId) {
                    var pid = pendingOpenAdvanceId;
                    pendingOpenAdvanceId = null;
                    try {
                        var u = new URL(window.location.href);
                        u.searchParams.delete('open_advance');
                        window.history.replaceState({}, '', u.pathname + (u.search || '') + (u.hash || ''));
                    } catch (e) {}
                    openAdvanceDetailPanel(pid);
                }
            });
            renderSummary();

            // ── Advance detail slide-over (Zoho-style) ───────────────────────
            $('#adv-sp-close, #adv-rpt-overlay').on('click', closeAdvDetailPanel);
            $(document).on('click', '#adv-apply-dd-toggle', function(e) {
                e.stopPropagation();
                if (!advPanelOpenId) return;
                var $m = $('#adv-apply-dd-menu');
                $m.toggleClass('open');
                $('#adv-apply-dd-toggle').toggleClass('is-open', $m.hasClass('open'));
                if ($m.hasClass('open')) loadAdvReportsForDropdown();
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.adv-apply-dd-wrap').length) {
                    $('#adv-apply-dd-menu').removeClass('open');
                    $('#adv-apply-dd-toggle').removeClass('is-open');
                }
                if (!$(e.target).closest('.adv-apply-inline-wrap').length) {
                    closeInlineApplyMenus();
                }
            });
            $(document).on('click', '#adv-apply-dd-menu .adv-apply-dd-item', function() {
                var rid = $(this).data('report-id') || $(this).data('reportId');
                if ($(this).attr('id') === 'adv-apply-new-report') {
                    window.location.href = pettyCashReportsBase;
                    return;
                }
                if (!rid || !advPanelOpenId) return;
                $.post(advLinkReportUrl, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    advance_id: advPanelOpenId,
                    report_id: rid
                }, function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Linked.');
                        $('#adv-apply-dd-menu').removeClass('open');
                        $('#adv-apply-dd-toggle').removeClass('is-open');
                        invalidateAdvReportsCache();
                        var pid = advPanelOpenId;
                        switchAdvancesTabToAll();
                        fetchAdvances(1, function() {
                            if (pid) openAdvanceDetailPanel(pid, {
                                tab: 'history'
                            });
                        });
                    } else {
                        toastr.error(res.message || 'Failed.');
                    }
                }, 'json').fail(function() {
                    toastr.error('Failed to link report.');
                });
            });

            $(document).on('click', '.adv-inline-apply-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $btn = $(this);
                var $wrap = $btn.closest('.adv-apply-inline-wrap');
                var $dd = $wrap.find('.adv-apply-inline-dropdown');
                var wasOpen = $dd.hasClass('open');
                closeInlineApplyMenus();
                if (wasOpen) return;
                $dd.addClass('open');
                $btn.addClass('is-open');
                $wrap.closest('.adv-pending-card').addClass('adv-pending-card-dd-open');
                $wrap.closest('.advance-row').addClass('adv-row-dd-open');
                $dd.html('<div class="adv-apply-dd-loading">Loading reports…</div>');
                requestAnimationFrame(function () {
                    positionAdvInlineApplyDropdown($btn, $dd);
                });

                function paintInline(rows) {
                    $dd.empty();
                    appendReportOptionsZohoStyle($dd, rows, 'adv-apply-inline-item');
                    $dd.append($('<button type="button" class="adv-apply-inline-item adv-apply-inline-new"/>')
                        .html('<i class="bi bi-plus-lg me-1"></i>Add To New Report'));
                    requestAnimationFrame(function () {
                        positionAdvInlineApplyDropdown($btn, $dd);
                    });
                }
                if (advReportsCache !== null) {
                    paintInline(advReportsCache);
                } else {
                    $.getJSON(advReportsListUrl, function(rows) {
                        advReportsCache = rows || [];
                        paintInline(advReportsCache);
                    }).fail(function() {
                        $dd.html('<div class="adv-apply-dd-loading" style="color:#dc2626;">Could not load reports.</div>');
                        requestAnimationFrame(function () {
                            positionAdvInlineApplyDropdown($btn, $dd);
                        });
                    });
                }
            });

            $(document).on('click', '.adv-apply-inline-dropdown .adv-apply-inline-item:not(.adv-apply-inline-new)', function(e) {
                e.stopPropagation();
                var rid = $(this).data('report-id') || $(this).attr('data-report-id');
                var $wrap = $(this).closest('.adv-apply-inline-wrap');
                var aid = $wrap.find('.adv-inline-apply-btn').data('advance-id');
                if (!rid || !aid) return;
                $.post(advLinkReportUrl, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    advance_id: aid,
                    report_id: rid
                }, function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Linked.');
                        invalidateAdvReportsCache();
                        closeInlineApplyMenus();
                        switchAdvancesTabToAll();
                        fetchAdvances(1, function() {
                            openAdvanceDetailPanel(aid, {
                                tab: 'history'
                            });
                        });
                    } else {
                        toastr.error(res.message || 'Failed.');
                    }
                }, 'json').fail(function() {
                    toastr.error('Failed to link report.');
                });
            });

            $(document).on('click', '.adv-apply-inline-new', function(e) {
                e.stopPropagation();
                closeInlineApplyMenus();
                window.location.href = pettyCashReportsBase;
            });

            document.addEventListener('scroll', function () {
                if ($('.adv-apply-inline-dropdown.open').length) {
                    syncOpenAdvInlineApplyDropdowns();
                }
            }, true);
            $(window).on('resize', function () {
                if ($('.adv-apply-inline-dropdown.open').length) {
                    syncOpenAdvInlineApplyDropdowns();
                }
            });

            $(document).on('click', '.adv-sp-tab', function() {
                var pane = $(this).data('adv-pane');
                $('.adv-sp-tab').removeClass('active');
                $(this).addClass('active');
                $('.adv-sp-tab-pane').removeClass('active');
                $('#adv-pane-' + pane).addClass('active');
            });
            $(document).on('click', '.adv-pending-card', function(e) {
                if ($(e.target).closest(
                        '.adv-check, .adv-approve-btn, .adv-reject-btn, .adv-apply-inline-wrap, .adv-inline-apply-btn, .adv-pc-apply-wrap, button, a, label'
                        ).length) return;
                openAdvanceDetailPanel($(this).data('id'));
            });
            $(document).on('click', '.adv-pc-date-link', function(e) {
                e.stopPropagation();
                openAdvanceDetailPanel($(this).closest('.adv-pending-card').data('id'));
            });
            $(document).on('click', '.advance-row', function(e) {
                if ($(e.target).closest('.adv-check, .adv-apply-inline-wrap, .adv-inline-apply-btn, button, a').length) return;
                openAdvanceDetailPanel($(this).data('id'));
            });
            $(document).on('click', '.adv-date-link', function(e) {
                e.stopPropagation();
                openAdvanceDetailPanel($(this).closest('.advance-row').data('id'));
            });

            // ── Approval helpers ─────────────────────────────────────────────
            var approverUrl = '{{ route('superadmin.advanceApprover') }}';
            var pendingRejectIds = [];

            function doAdvanceStatus(ids, value, callback) {
                if (!ids.length) {
                    callback(0, 0);
                    return;
                }
                var done = 0,
                    failed = 0,
                    total = ids.length;
                ids.forEach(function(id) {
                    $.ajax({
                        url: approverUrl,
                        type: 'GET',
                        data: {
                            advance_id: id,
                            value: value
                        },
                        success: function() {
                            done++;
                            if (done + failed === total) callback(done, failed);
                        },
                        error: function() {
                            failed++;
                            if (done + failed === total) callback(done, failed);
                        }
                    });
                });
            }

            // ── Single approve ──────────────────────────────────────────────
            $(document).on('click', '.adv-approve-btn', function() {
                var id = $(this).data('id');
                var $btn = $(this);
                var orig = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                doAdvanceStatus([id], 'applied', function(done, failed) {
                    if (done) {
                        toastr.success('Advance approved & marked as paid. Funds disbursed.');
                        fetchAdvances(1);
                        if (advPanelOpenId == id) openAdvanceDetailPanel(id);
                    } else {
                        toastr.error('Failed to approve advance.');
                        $btn.prop('disabled', false).html(orig);
                    }
                });
            });

            // ── Single reject ───────────────────────────────────────────────
            $(document).on('click', '.adv-reject-btn', function() {
                pendingRejectIds = [$(this).data('id')];
                $('#rejectModal').show();
            });

            // ── Single close (applied → closed) ─────────────────────────────
            $(document).on('click', '.adv-close-btn', function() {
                var id = $(this).data('id');
                var $btn = $(this);
                var orig = $btn.html();
                if (!confirm('Settle & close this advance? This cannot be undone.')) return;
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                doAdvanceStatus([id], 'closed', function(done, failed) {
                    if (done) {
                        toastr.success('Advance settled and closed successfully.');
                        fetchAdvances(1);
                        if (advPanelOpenId == id) openAdvanceDetailPanel(id);
                    } else {
                        toastr.error('Failed to settle advance.');
                        $btn.prop('disabled', false).html(orig);
                    }
                });
            });

            // ── Single submit (draft → pending) ──────────────────────────────
            $(document).on('click', '.adv-submit-btn', function() {
                var id = $(this).data('id');
                var $btn = $(this);
                var orig = $btn.html();
                if (!confirm('Submit this advance for approval?')) return;
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                doAdvanceStatus([id], 'pending', function(done, failed) {
                    if (done) {
                        toastr.success('Advance submitted for approval.');
                        fetchAdvances(1);
                        if (advPanelOpenId == id) openAdvanceDetailPanel(id);
                    } else {
                        toastr.error('Failed to submit advance.');
                        $btn.prop('disabled', false).html(orig);
                    }
                });
            });

            // ── Reject modal handlers ────────────────────────────────────────
            $('#rejectModalClose, #rejectModalCancel').on('click', function() {
                $('#rejectModal').hide();
                pendingRejectIds = [];
            });

            $('#rejectModalConfirm').on('click', function() {
                if (!pendingRejectIds.length) {
                    $('#rejectModal').hide();
                    return;
                }
                var $btn = $(this);
                var orig = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Rejecting...');
                doAdvanceStatus(pendingRejectIds, 'rejected', function(done, failed) {
                    $btn.prop('disabled', false).html(orig);
                    $('#rejectModal').hide();
                    var pid = advPanelOpenId;
                    pendingRejectIds = [];
                    if (done) toastr.success(done + ' advance(s) rejected.');
                    if (failed) toastr.error(failed + ' advance(s) could not be rejected.');
                    fetchAdvances(1);
                    if (pid) openAdvanceDetailPanel(pid);
                });
            });

            // ── Checkbox selection & bulk bar ────────────────────────────────
            function updateBulkBar() {
                var count = $('.adv-check:checked').length;
                if (count > 0 && currentTab === 'pending') {
                    $('#adv-bulk-bar').show();
                    $('#adv-bulk-count').text(count);
                } else {
                    $('#adv-bulk-bar').hide();
                }
            }

            $(document).on('change', '.adv-check', function() {
                updateBulkBar();
            });
            $(document).on('change', '#select-all-adv', function() {
                $('.adv-check').prop('checked', $(this).prop('checked'));
                updateBulkBar();
            });

            $('#adv-bulk-approve').on('click', function() {
                var ids = [];
                $('.adv-check:checked').each(function() {
                    ids.push($(this).data('id'));
                });
                if (!ids.length) return;
                var $btn = $(this);
                var orig = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Approving...');
                doAdvanceStatus(ids, 'applied', function(done, failed) {
                    $btn.prop('disabled', false).html(orig);
                    var pid = advPanelOpenId;
                    if (done) toastr.success(done + ' advance(s) approved & marked as paid.');
                    if (failed) toastr.error(failed + ' advance(s) could not be approved.');
                    fetchAdvances(1);
                    if (pid) openAdvanceDetailPanel(pid);
                });
            });

            $('#adv-bulk-reject').on('click', function() {
                var ids = [];
                $('.adv-check:checked').each(function() {
                    ids.push($(this).data('id'));
                });
                if (!ids.length) return;
                pendingRejectIds = ids;
                $('#rejectModal').show();
            });

            $('#adv-bulk-cancel').on('click', function() {
                $('.adv-check').prop('checked', false);
                $('#select-all-adv').prop('checked', false);
                $('#adv-bulk-bar').hide();
            });
        });
    </script>
</body>

</html>
