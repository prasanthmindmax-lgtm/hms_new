<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/pettycash.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
    /* ── Page Tabs ── */
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
        border-bottom-color: #4f46e5;
    }

    .report-tab.active svg {
        opacity: 1;
    }

    /* Workflow sub-tabs (All Reports / summary view) */
    .report-workflow-tabs {
        display: none;
        flex-wrap: wrap;
        gap: 6px;
        padding: 8px 10px 10px;
        border-bottom: 1px solid #e5e7eb;
        background: #fafafa;
    }

    .report-workflow-tabs.visible {
        display: flex;
    }

    .report-wf-tab {
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        cursor: pointer;
        transition: all .15s;
    }

    .report-wf-tab:hover {
        border-color: #c7d2fe;
        color: #4338ca;
    }

    .report-wf-tab.active {
        border-color: #4f46e5;
        color: #4f46e5;
        background: #eef2ff;
    }

    .report-wf-tab .wf-cnt {
        display: inline-block;
        margin-left: 4px;
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
    }

    .report-wf-tab.active .wf-cnt {
        color: #4f46e5;
    }

    /* Summary list: submitter / approver (Zoho-style) */
    .rpt-person-cell {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
    }

    .rpt-person-avatar {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
    }

    .rpt-person-text {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .rpt-person-name {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        line-height: 1.25;
        word-break: break-word;
    }

    .rpt-person-sub {
        font-size: 11px;
        color: #9ca3af;
    }

    .rpt-report-title {
        font-size: 13px;
        font-weight: 600;
        color: #2563eb;
    }

    .rpt-report-period-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 2px;
    }

    .rpt-status-stack {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .rpt-due-hint {
        font-size: 11px;
        color: #6b7280;
    }

    .rpt-action-btn {
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        white-space: nowrap;
    }

    .rpt-action-btn.primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }

    .rpt-action-btn.primary:hover {
        background: #1d4ed8;
    }

    .rpt-action-btn.danger {
        border-color: #fecaca;
        color: #b91c1c;
        background: #fef2f2;
    }

    .rpt-warn-box {
        margin: 0;
        padding: 12px 14px;
        border-radius: 8px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        font-size: 13px;
        color: #92400e;
        line-height: 1.45;
    }

    .rpt-warn-box strong {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #b45309;
    }

    .rpt-warn-box strong::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f59e0b;
        flex-shrink: 0;
    }

    .rpt-warn-box ul {
        margin: 8px 0 0 0;
        padding-left: 1.25rem;
        color: #78350f;
    }

    .rpt-warn-box li {
        margin-bottom: 4px;
    }

    .rpt-warnings-block {
        margin-top: 14px;
    }

    .rpt-advance-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .35);
        z-index: 1080;
    }

    .rpt-advance-overlay.open {
        display: block;
    }

    .rpt-advance-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: 400px;
        max-width: 94vw;
        height: 100%;
        background: #fff;
        z-index: 1090;
        box-shadow: -8px 0 40px rgba(0, 0, 0, .15);
        transform: translateX(100%);
        transition: transform .28s ease;
        display: flex;
        flex-direction: column;
    }

    .rpt-advance-panel.open {
        transform: translateX(0);
    }

    .rpt-advance-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
        font-size: 14px;
    }

    .rpt-advance-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px 16px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 12px;
    }

    .rpt-advance-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px 16px 20px;
    }

    .rpt-advance-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 10px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
    }

    .rpt-advance-card.selected {
        border-color: #2563eb;
        box-shadow: 0 0 0 1px #2563eb;
    }

    .rpt-advance-card-body {
        flex: 1;
        min-width: 0;
    }

    .rpt-advance-card-top {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 700;
    }

    .rpt-advance-meta {
        font-size: 11px;
        color: #6b7280;
        margin-top: 6px;
    }

    .rpt-history-row {
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 12px;
    }

    .rpt-history-row:last-child {
        border-bottom: none;
    }

    .rpt-history-label {
        font-weight: 700;
        color: #1f2937;
    }

    .rpt-history-meta {
        color: #9ca3af;
        font-size: 11px;
        margin-top: 2px;
    }

    /* ═══════════════════════════════════════════════════════════════
       EXPENSE DETAIL MODAL
    ═══════════════════════════════════════════════════════════════ */
    .exp-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .6);
        backdrop-filter: blur(3px);
        z-index: 1060;
    }

    .exp-modal-overlay.open {
        display: block;
    }

    .exp-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(.96);
        width: 960px;
        max-width: 96vw;
        max-height: 92vh;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, .28), 0 0 0 1px rgba(0, 0, 0, .06);
        border-top: 3px solid #4f46e5;
        z-index: 1070;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        transition: opacity .22s ease, transform .22s ease;
    }

    .exp-modal.open {
        display: flex;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    /* Modal Header */
    .exp-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 20px;
        border-bottom: 1px solid #ede9fe;
        background: linear-gradient(135deg, #f8f7ff 0%, #fff 100%);
        flex-shrink: 0;
    }

    .exp-modal-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .exp-modal-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #ede9fe;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        font-size: 15px;
        flex-shrink: 0;
    }

    .exp-modal-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #1a2332;
        line-height: 1.2;
    }

    .exp-modal-subtitle {
        font-size: 11.5px;
        color: #8a94a6;
        margin-top: 1px;
    }

    .exp-modal-close {
        width: 32px;
        height: 32px;
        border: none;
        background: #f3f4f6;
        cursor: pointer;
        color: #6b7280;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all .14s;
        flex-shrink: 0;
    }

    .exp-modal-close:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Modal Body */
    .exp-modal-body {
        display: flex;
        flex: 1;
        min-height: 0;
        height: 540px;
        position: relative;
        overflow: hidden;
    }

    /* Nav arrows */
    .exp-nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 38px;
        height: 38px;
        background: rgba(255, 255, 255, .95);
        border: 1.5px solid #e5e7eb;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #374151;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .12);
        transition: all .15s;
    }

    .exp-nav-arrow:hover {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
        box-shadow: 0 4px 18px rgba(79, 70, 229, .35);
    }

    .exp-nav-arrow.disabled {
        opacity: .25;
        pointer-events: none;
    }

    .exp-nav-arrow.left {
        left: 10px;
    }

    .exp-nav-arrow.right {
        right: 10px;
    }

    /* ── Left: Receipt panel ── */
    .exp-receipt-panel {
        width: 48%;
        background: linear-gradient(160deg, #0f172a 0%, #1e293b 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .exp-receipt-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 40%, rgba(79, 70, 229, .15) 0%, transparent 60%);
        pointer-events: none;
    }

    .exp-receipt-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        position: relative;
        z-index: 1;
    }

    .exp-no-receipt-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        position: relative;
        z-index: 1;
        border: 2px dashed rgba(255, 255, 255, .12);
        border-radius: 16px;
        padding: 36px 32px;
        margin: 20px;
        text-align: center;
    }

    .exp-no-receipt-placeholder i {
        font-size: 44px;
        opacity: .35;
        color: #94a3b8;
    }

    .exp-no-receipt-placeholder span {
        color: #94a3b8;
    }

    .exp-no-receipt-placeholder small {
        font-size: 11.5px;
        color: #475569;
        display: block;
        margin-top: 4px;
    }

    /* ── Right: Detail panel ── */
    .exp-detail-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        overflow: hidden;
    }

    /* Detail top summary */
    .exp-detail-top {
        padding: 20px 22px 16px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
        background: #fafafa;
    }

    .exp-detail-date-line {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .exp-date-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4f46e5;
        flex-shrink: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .18);
    }

    .exp-detail-date-text {
        font-size: 12.5px;
        color: #475569;
        font-weight: 500;
    }

    .exp-detail-status-badge {
        margin-left: auto;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 11px;
        border-radius: 20px;
    }

    .exp-category-line {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .exp-category-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #10b981;
        flex-shrink: 0;
    }

    .exp-category-name {
        font-size: 12.5px;
        font-weight: 700;
        background: #ecfdf5;
        color: #065f46;
        padding: 2px 10px;
        border-radius: 20px;
        border: 1px solid #d1fae5;
    }

    .exp-merchant-line {
        font-size: 14.5px;
        font-weight: 700;
        color: #1a2332;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .exp-merchant-line small {
        display: block;
        font-size: 11.5px;
        font-weight: 400;
        color: #8a94a6;
        margin-top: 1px;
    }

    .exp-reimbursable-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 700;
        color: #2563eb;
        letter-spacing: .4px;
        margin-bottom: 10px;
        background: #eff6ff;
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid #bfdbfe;
    }

    .exp-reimbursable-tag i {
        font-size: 11px;
    }

    .exp-detail-amount-big {
        font-size: 26px;
        font-weight: 800;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
    }

    /* Detail tabs */
    .exp-detail-tabs {
        display: flex;
        border-bottom: 1.5px solid #e5e7eb;
        flex-shrink: 0;
        padding: 0 22px;
        background: #fff;
    }

    .exp-detail-tab {
        padding: 10px 14px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 12.5px;
        font-weight: 600;
        color: #9ca3af;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -1.5px;
        transition: color .12s;
    }

    .exp-detail-tab:hover:not(.active) {
        color: #374151;
    }

    .exp-detail-tab.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
    }

    /* Tab body */
    .exp-tab-body {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .exp-tab-pane {
        display: none;
        padding: 16px 22px;
    }

    .exp-tab-pane.active {
        display: block;
    }

    /* Info rows — 2-column grid layout */
    .exp-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .exp-info-row {
        padding: 11px 0;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .exp-info-row.full-width {
        grid-column: span 2;
    }

    .exp-info-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .exp-info-value {
        font-size: 13px;
        color: #1a2332;
        font-weight: 500;
        line-height: 1.4;
    }

    .exp-info-value.empty {
        color: #d1d5db;
        font-style: italic;
        font-weight: 400;
    }

    /* Items sub-table */
    .exp-items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        font-size: 12.5px;
        border-radius: 8px;
        overflow: hidden;
    }

    .exp-items-table th {
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        color: #8a94a6;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 8px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .exp-items-table td {
        padding: 9px 10px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        vertical-align: top;
    }

    .exp-items-table tr:last-child td {
        border-bottom: none;
    }

    .exp-items-table td:last-child {
        text-align: right;
        font-weight: 700;
        color: #1a2332;
    }

    .exp-empty-tab {
        color: #c4c9d4;
        font-size: 13px;
        text-align: center;
        padding: 28px 0;
    }

    /* ═══════════════════════════════════════════════════════════════
       REPORT SIDE PANEL
    ═══════════════════════════════════════════════════════════════ */
    .rpt-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .4);
        backdrop-filter: blur(2px);
        z-index: 1040;
    }

    .rpt-overlay.open {
        display: block;
    }

    .rpt-detail-layout {
        display: flex;
        align-items: stretch;
        flex: 1;
        min-height: 0;
    }

    .rpt-detail-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: #fafbfc;
    }

    .rpt-meta-sidebar {
        width: 200px;
        flex-shrink: 0;
        border-left: 1px solid #e5e7eb;
        background: #fafafa;
        padding: 14px 14px 24px;
        font-size: 12px;
    }

    .rpt-meta-sidebar .rpt-meta-block {
        margin-bottom: 14px;
    }

    .rpt-meta-sidebar .rpt-meta-k {
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }

    .rpt-meta-sidebar .rpt-meta-v {
        color: #111827;
        font-weight: 600;
        word-break: break-word;
    }

    .rpt-hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .rpt-hero-headline {
        flex: 1;
        min-width: 0;
    }

    .rpt-hero-figures {
        text-align: right;
        flex-shrink: 0;
    }

    .rpt-hero-fig-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .rpt-hero-fig-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        font-variant-numeric: tabular-nums;
        margin-top: 2px;
    }

    .rpt-view-summary {
        border: none;
        background: none;
        color: #64748b;
        font-size: 11px;
        font-weight: 500;
        padding: 6px 0 0;
        cursor: pointer;
        margin-top: 4px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .rpt-view-summary:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .rpt-summary-pop {
        display: none;
        margin-top: 8px;
        padding: 10px 12px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 12px;
        color: #374151;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
        text-align: left;
    }

    .rpt-summary-pop.open {
        display: block;
    }

    .rpt-summary-section {
        padding: 0 22px 16px;
    }

    .rpt-summary-section-head {
        margin-bottom: 12px;
    }

    .rpt-summary-title-group {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .rpt-summary-section-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .rpt-summary-section-count {
        font-size: 12px;
        font-weight: 700;
        color: #4338ca;
        background: #eef2ff;
        border: 1px solid #e0e7ff;
        border-radius: 999px;
        padding: 3px 11px;
        min-width: 28px;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }

    .rpt-summary-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 4px 0;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    .rpt-summary-breakdown-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 16px;
        padding: 10px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #475569;
    }

    .rpt-summary-breakdown-row span:first-child {
        flex: 1;
        min-width: 0;
        line-height: 1.4;
    }

    .rpt-summary-breakdown-row span:last-child {
        font-weight: 600;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .rpt-summary-breakdown-row:last-child {
        border-bottom: none;
        background: #f8fafc;
        margin: 4px 0 0;
        padding-top: 12px;
        padding-bottom: 12px;
        border-radius: 0 0 9px 9px;
        font-weight: 700;
    }

    .rpt-summary-breakdown-row:last-child span {
        color: #0f172a;
        font-weight: 700;
    }

    .rpt-zoho-inline {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13px;
        margin-top: 12px;
        gap: 6px;
    }

    .rpt-zoho-inline-cell {
        flex: 1;
        text-align: center;
        min-width: 0;
    }

    .rpt-zoho-inline-cell small {
        color: #64748b;
        display: block;
        margin-bottom: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .rpt-zoho-inline-cell div {
        font-weight: 700;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
    }

    .rpt-zoho-inline-op {
        font-weight: 700;
        color: #cbd5e1;
        align-self: center;
        flex-shrink: 0;
        padding: 0 2px;
    }

    .rpt-history-row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .rpt-history-row:last-child {
        border-bottom: none;
    }

    .rpt-history-row .rpt-history-meta {
        font-weight: 500;
        color: #94a3b8;
        font-size: 12px;
    }

    .rpt-history-row-msg {
        margin-top: 4px;
        font-size: 13px;
        color: #334155;
        line-height: 1.45;
    }

    .rpt-history-avatar {
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

    .rpt-advances-subhead {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #64748b;
        padding: 14px 22px 8px;
        margin-bottom: 0;
    }

    /* Advances table — light header (avoid global qdt dark style) */
    .rpt-adv-wrap {
        overflow-x: auto;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        margin: 0 22px 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    .rpt-advances-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 520px;
    }

    .rpt-advances-table thead tr {
        background: #f8fafc;
    }

    .rpt-advances-table thead th {
        padding: 10px 14px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
    }

    .rpt-advances-table thead th.text-end {
        text-align: right;
    }

    .rpt-advances-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .rpt-advances-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rpt-advances-table tbody tr:hover {
        background: #fafafa;
    }

    .rpt-empty-state {
        margin: 12px 22px 24px;
        padding: 28px 20px;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        color: #64748b;
    }

    .rpt-empty-state>i {
        font-size: 28px;
        color: #94a3b8;
        display: block;
        margin-bottom: 10px;
    }

    .rpt-empty-state-title {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }

    .rpt-empty-state-text {
        font-size: 13px;
        margin: 0;
        line-height: 1.45;
        max-width: 320px;
        margin-left: auto;
        margin-right: auto;
    }

    .rpt-side-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 960px;
        max-width: 98vw;
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

    .rpt-side-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 50%, #06b6d4 100%);
        z-index: 1;
    }

    .rpt-side-panel.open {
        transform: translateX(0);
    }

    .rpt-side-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 18px 22px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
        flex-shrink: 0;
        gap: 12px;
    }

    .rpt-side-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .rpt-panel-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        font-size: 16px;
        flex-shrink: 0;
    }

    .rpt-report-code {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .rpt-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .rpt-close-btn {
        width: 34px;
        height: 34px;
        border: none;
        background: #f3f4f6;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 15px;
        transition: all .15s;
    }

    .rpt-close-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .rpt-side-body {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .rpt-actual-inner {
        flex: 1;
        min-height: 0;
        display: none;
        flex-direction: column;
    }

    .rpt-actual-inner.is-visible {
        display: flex;
    }

    /* Hero — clean readable header (list detail) */
    .rpt-hero {
        padding: 22px 22px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .rpt-hero-name {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
        line-height: 1.25;
        letter-spacing: -0.02em;
    }

    .rpt-hero-duration {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rpt-hero-duration i {
        color: #94a3b8;
        font-size: 14px;
    }

    .rpt-hero-meta {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
        line-height: 1.5;
    }

    .rpt-hero-meta strong {
        color: #334155;
        font-weight: 600;
    }

    .rpt-hero-amount-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 18px;
        min-width: 200px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
    }

    /* Panel Tabs */
    .rpt-panel-tabs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px 0;
        border-bottom: 1px solid #e2e8f0;
        background: #fafbfc;
        flex-shrink: 0;
        padding: 8px 22px 0;
    }

    .rpt-panel-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 12px 14px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        white-space: nowrap;
        transition: color .15s, border-color .15s;
        letter-spacing: 0;
    }

    .rpt-panel-tab:hover:not(.active) {
        color: #334155;
    }

    .rpt-panel-tab.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: linear-gradient(to bottom, transparent 0%, rgba(79, 70, 229, .04) 100%);
    }

    .rpt-panel-tabs .rpt-record-advance {
        margin-left: auto;
        margin-bottom: 8px;
        align-self: center;
    }

    .rpt-panel-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e5e7eb;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        border-radius: 10px;
        padding: 1px 7px;
        min-width: 20px;
    }

    .rpt-panel-tab.active .rpt-panel-tab-badge {
        background: #ede9fe;
        color: #4f46e5;
    }

    .rpt-panel-content {
        display: none;
        flex: 1;
        overflow-y: auto;
        background: #f8fafc;
        padding: 12px 0 24px;
    }

    .rpt-panel-content.active {
        display: block;
    }

    #rpt-tab-expenses {
        padding-left: 22px;
        padding-right: 22px;
    }

    #rpt-tab-advances,
    #rpt-tab-history {
        background: #f8fafc;
    }

    #rpt-tab-history .rpt-history-list-inner {
        background: #fff;
        margin: 0 22px 16px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 8px 16px 16px;
    }

    /* Expense entry — card style */
    .rpt-expense-entry {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px 14px;
        margin-bottom: 12px;
        position: relative;
        transition: box-shadow .15s, border-color .15s;
        cursor: pointer;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    .rpt-expense-entry:last-child {
        margin-bottom: 0;
    }

    .rpt-expense-entry::before {
        display: none;
    }

    .rpt-expense-entry:hover {
        border-color: #c7d2fe;
        box-shadow: 0 4px 14px rgba(79, 70, 229, .08);
    }

    .rpt-expense-entry:hover .rpt-entry-vendor {
        color: #4338ca;
    }

    .rpt-entry-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 6px;
    }

    .rpt-entry-left {
        flex: 1;
        min-width: 0;
    }

    .rpt-entry-date {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .rpt-entry-vendor {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
        transition: color .14s;
        line-height: 1.35;
    }

    .rpt-entry-location {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .rpt-entry-location i {
        font-size: 10px;
    }

    .rpt-entry-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        flex-shrink: 0;
    }

    .rpt-entry-amount {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .rpt-entry-status {}

    /* Items sub-list */
    .rpt-entry-items {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .rpt-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 12px;
        color: #374151;
        gap: 8px;
    }

    .rpt-item-category {
        display: inline-flex;
        align-items: center;
        background: #f0f4ff;
        color: #4f46e5;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 10.5px;
        font-weight: 700;
        flex-shrink: 0;
        border: 1px solid #e0e7ff;
    }

    .rpt-item-desc {
        flex: 1;
        color: #6b7280;
        font-size: 12px;
        min-width: 0;
    }

    .rpt-item-amount {
        font-weight: 700;
        color: #1a2332;
        white-space: nowrap;
        font-size: 12px;
    }

    /* Notes snippet */
    .rpt-entry-note {
        font-size: 11.5px;
        color: #9ca3af;
        margin-top: 4px;
        font-style: italic;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .rpt-entry-note i {
        color: #c4b5fd;
        font-size: 11px;
    }

    /* Clickable hint icon */
    .rpt-entry-click-hint {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }

    .rpt-entry-click-hint i {
        color: #6366f1;
        font-size: 13px;
    }

    /* Empty / loading */
    .rpt-panel-empty {
        padding: 32px 22px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        line-height: 1.5;
    }

    .rpt-panel-empty i {
        font-size: 30px;
        display: block;
        margin-bottom: 10px;
        color: #cbd5e1;
    }

    .rpt-panel-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        gap: 10px;
        color: #8a94a6;
        font-size: 13.5px;
    }

    /* Skeleton shimmer */
    @keyframes rpt-shimmer {
        0% {
            background-position: -700px 0
        }

        100% {
            background-position: 700px 0
        }
    }

    .rpt-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e4e4e4 50%, #f0f0f0 75%);
        background-size: 700px 100%;
        animation: rpt-shimmer 1.4s infinite;
        border-radius: 6px;
    }

    .report-drawer {
        position: fixed;
        top: 0;
        right: -500px;
        width: 420px;
        height: 100%;
        background: #fff;
        box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
        z-index: 9999;
    }

    .report-drawer.open {
        right: 0;
    }

    .drawer-content {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .drawer-header {
        display: flex;
        justify-content: space-between;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .drawer-summary {
        display: flex;
        gap: 10px;
        padding: 15px;
    }

    .summary-box {
        flex: 1;
        background: #f8fafc;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }

    .drawer-tabs {
        display: flex;
        border-bottom: 1px solid #eee;
    }

    .drawer-tabs button {
        flex: 1;
        padding: 10px;
        border: none;
        background: none;
    }

    .drawer-tabs .active {
        border-bottom: 2px solid blue;
        font-weight: bold;
    }

    .drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    /* Expense Card */
    .expense-card {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
</style>


<body style="overflow-x:hidden;">
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="qd-card">

                {{-- Header --}}
                <div class="qd-header">
                    <div class="qd-header-title">
                        <i class="bi bi-graph-up"></i>
                        Petty Cash Reports
                    </div>
                    <div class="qd-header-actions">
                        {{-- Toggle: Stats --}}
                        <button class="btn btn-sm qd-toggle-btn" id="toggleStats" title="Toggle Stats">
                            <i class="bi bi-bar-chart-line me-1"></i>Stats
                            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
                        </button>
                        {{-- Toggle: Filters --}}
                        <button class="btn btn-sm qd-toggle-btn" id="toggleFilters" title="Toggle Filters">
                            <i class="bi bi-funnel me-1"></i>Filter
                            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
                        </button>

                        {{-- <a href="{{ route('superadmin.getadvances') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-wallet2 me-1"></i>Advances
                        </a>
                        <a href="{{ route('superadmin.getadvancescreate') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-plus-lg me-1"></i>Record advance
                        </a> --}}
                        <button class="btn btn-sm btn-success" id="exportReports">
                            <i class="bi bi-download me-1"></i>Export CSV
                        </button>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="qd-stats" id="reportStatsSection">
                    <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all">
                        <div class="qd-stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Total</div>
                            <div class="qd-stat-value" id="stat-total">0</div>
                            <div class="qd-stat-sub" id="stat-total-amount">₹0.00</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-green" data-stat-filter="Approved" title="Filter: Approved">
                        <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Approved</div>
                            <div class="qd-stat-value" id="stat-approved">0</div>
                            <div class="qd-stat-sub" id="stat-approved-amount">₹0.00</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-orange" data-stat-filter="Pending" title="Filter: Pending">
                        <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Pending</div>
                            <div class="qd-stat-value" id="stat-pending">0</div>
                            <div class="qd-stat-sub" id="stat-pending-amount">₹0.00</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-red" data-stat-filter="rejected" title="Filter: Rejected">
                        <div class="qd-stat-icon"><i class="bi bi-x-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Rejected</div>
                            <div class="qd-stat-value" id="stat-rejected">0</div>
                            <div class="qd-stat-sub" id="stat-rejected-amount">₹0.00</div>
                        </div>
                    </div>

                </div>

                {{-- Filters --}}
                {{-- ── Filters ── --}}
                <div class="qd-filters">
                    {{-- Row 1: Date, Company, State, Zone --}}
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

                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>State</label>
                            <input type="text" class="form-control state-search-input dropdown-search-input"
                                placeholder="Select State" readonly>
                            <input type="hidden" name="state_id" class="state_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search State..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect state-list">
                                    <div data-value="Tamil Nadu" data-id="1">Tamil Nadu</div>
                                    <div data-value="Karnataka" data-id="2">Karnataka</div>
                                    <div data-value="Kerala" data-id="3">Kerala</div>
                                    <div data-value="Andra Pradesh" data-id="4">Andra Pradesh</div>
                                    <div data-value="International" data-id="5">International</div>
                                </div>
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
                    </div>

                    {{-- Row 2: Branch, Vendor, Nature, Status --}}
                    <div class="qd-filter-row" style="margin-top:10px;">
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

                        {{-- <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Vendor</label>
                            <input type="text" class="form-control vendor-search-input dropdown-search-input" placeholder="Select Vendor" readonly>
                            <input type="hidden" name="vendor_id" class="vendor_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Vendor..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect vendor-list"></div>
                            </div>
                        </div> --}}

                        {{-- <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Nature of Payment</label>
                            <input type="text" class="form-control nature-search-input dropdown-search-input" placeholder="Select Nature" readonly>
                            <input type="hidden" name="nature_id" class="nature_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Nature..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect account-list"></div>
                            </div>
                        </div> --}}

                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
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
                                    <div data-value="save" data-id="1">Save</div>
                                    <div data-value="draft" data-id="2">Draft</div>
                                    <div data-value="pending" data-id="3">Pending</div>
                                    <div data-value="approved" data-id="4">Approved</div>
                                    <div data-value="reject" data-id="5">Reject</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Search bar ── --}}
                <div class="qd-search-row">
                    <div class="qd-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="universal_search" placeholder="Search petty cash...">
                    </div>
                </div>

                {{-- ── Applied filters ── --}}
                <div class="qd-applied-bar">
                    <span class="applied-label">Filters:</span>
                    <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>

                {{-- Table --}}
                <div class="qd-table-wrap">
                    <div class="report-tabs-wrapper">
                        <div class="report-tabs">

                            <button class="report-tab active" data-view="pending">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                                    stroke="currentColor" stroke-width="1.6">
                                    <circle cx="8" cy="8" r="6.5" />
                                    <path d="M8 5v3.5l2 1.5" />
                                </svg>
                                Pending Reports
                            </button>

                            <button class="report-tab" data-view="summary">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                                    stroke="currentColor" stroke-width="1.6">
                                    <rect x="1" y="1" width="14" height="14" rx="2" />
                                    <path d="M1 5h14M5 5v10" />
                                </svg>
                                All Reports
                            </button>

                        </div>
                    </div>

                    <div id="report-workflow-tabs" class="report-workflow-tabs">
                        <button type="button" class="report-wf-tab active" data-wf-tab="all">All<span
                                class="wf-cnt" data-c="all">0</span></button>
                        <button type="button" class="report-wf-tab" data-wf-tab="pending_approval">Awaiting
                            approval<span class="wf-cnt" data-c="pending_approval">0</span></button>
                        <button type="button" class="report-wf-tab" data-wf-tab="approved">Awaiting
                            reimbursement<span class="wf-cnt" data-c="approved">0</span></button>
                        <button type="button" class="report-wf-tab" data-wf-tab="reimbursed">Reimbursed<span
                                class="wf-cnt" data-c="reimbursed">0</span></button>
                    </div>

                    <div id="pettycash-report-body"></div>


                </div>

                {{-- Pagination --}}
                <div class="qd-pagination" id="pettycash-report-pagination">
                    <div id="pettycash-report-links"></div>
                    <div>
                        <form class="d-flex align-items-center gap-2">
                            <select id="per_page" class="form-control form-control-sm" style="width:80px;">
                                <option value="10" selected>10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span style="font-size:12px;color:#8a94a6;">entries</span>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Report Detail Side Panel ── --}}
    <div class="rpt-overlay" id="rptOverlay"></div>

    <div class="rpt-side-panel" id="rptSidePanel">

        {{-- Header --}}
        <div class="rpt-side-header">
            <div class="rpt-side-header-left">
                <div class="rpt-panel-icon">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <div>
                    <div class="rpt-report-code" id="rpt-code">—</div>
                    <div style="margin-top:3px;" id="rpt-status-badge"></div>
                </div>
            </div>
            <div class="rpt-header-actions" style="flex-wrap:wrap;justify-content:flex-end;">
                <button type="button" class="rpt-action-btn" id="rptBtnSubmit" style="display:none;"
                    title="Submit for approval">Submit</button>
                <button type="button" class="rpt-action-btn primary" id="rptBtnApprove" style="display:none;"
                    title="Approve report">Approve</button>
                <button type="button" class="rpt-action-btn danger" id="rptBtnReject" style="display:none;"
                    title="Reject report">Reject</button>
                <button type="button" class="rpt-action-btn primary" id="rptBtnReimburse" style="display:none;"
                    title="Mark as reimbursed">Reimbursed</button>
                <button type="button" class="rpt-action-btn" id="rptBtnApplyAdvance" style="display:none;"
                    title="Apply advance">Apply advance</button>
                <button class="rpt-close-btn" id="rptCloseBtn" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="rpt-side-body" id="rptSideBody">

            {{-- Loading state --}}
            <div id="rpt-loading" class="rpt-panel-loading" style="display:none;">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <span>Loading report details…</span>
            </div>

            {{-- Actual content (hidden until loaded) --}}
            <div id="rpt-actual-content" class="rpt-actual-inner">

                <div class="rpt-hero">
                    <div class="rpt-hero-top">
                        <div class="rpt-hero-headline">
                            <div class="rpt-hero-name" id="rpt-name">—</div>
                            <div class="rpt-hero-duration" id="rpt-duration">
                                <i class="bi bi-calendar3"></i><span>—</span>
                            </div>
                            <div class="rpt-hero-meta" id="rpt-submitter-line" style="display:none;"></div>
                        </div>
                        <div class="rpt-hero-figures rpt-hero-amount-card">
                            <div class="rpt-hero-fig-label">Report total</div>
                            <div class="rpt-hero-fig-val" id="rpt-hero-total">₹0.00</div>
                            <div class="rpt-hero-fig-label" style="margin-top:12px;">Amount to be reimbursed</div>
                            <div class="rpt-hero-fig-val" id="rpt-hero-reimburse" style="font-size:18px;">₹0.00</div>
                            <button type="button" class="rpt-view-summary" id="rptViewSummaryBtn"
                                title="Breakdown of reimbursable total"><i class="bi bi-info-circle"></i> How totals are calculated</button>
                            <div class="rpt-summary-pop" id="rptSummaryPop"></div>
                        </div>
                    </div>
                    <div id="rpt-warnings-wrap" class="rpt-warnings-block" style="display:none;"></div>
                </div>

                <div class="rpt-detail-layout">
                    <div class="rpt-detail-main">

                        <div class="rpt-summary-section">
                            <div class="rpt-summary-section-head">
                                <div class="rpt-summary-title-group">
                                    <span class="rpt-summary-section-title">Expense summary</span>
                                    <span class="rpt-summary-section-count" id="rpt-summary-exp-count">0</span>
                                </div>
                            </div>
                            <div class="rpt-summary-card">
                                <div id="rpt-breakdown-lines"></div>
                            </div>
                            <div class="rpt-zoho-inline" id="rpt-zoho-inline" style="display:none;">
                                <div class="rpt-zoho-inline-cell">
                                    <small>Reimbursable</small>
                                    <div id="rpt-reimbursable">₹0.00</div>
                                </div>
                                <span class="rpt-zoho-inline-op">−</span>
                                <div class="rpt-zoho-inline-cell">
                                    <small>Advance</small>
                                    <div id="rpt-advance">₹0.00</div>
                                </div>
                                <span class="rpt-zoho-inline-op">=</span>
                                <div class="rpt-zoho-inline-cell">
                                    <small>To reimburse</small>
                                    <div id="rpt-balance">₹0.00</div>
                                </div>
                            </div>
                        </div>

                        <div class="rpt-panel-tabs">
                            <button type="button" class="rpt-panel-tab active" data-rpt-tab="expenses">
                                <i class="bi bi-receipt me-1"></i>Expenses
                                <span class="rpt-panel-tab-badge" id="rpt-exp-badge">0</span>
                            </button>
                            <button type="button" class="rpt-panel-tab" data-rpt-tab="advances">
                                <i class="bi bi-wallet2 me-1"></i>Advances &amp; refunds
                                <span class="rpt-panel-tab-badge" id="rpt-adv-tab-badge">0</span>
                            </button>
                            <button type="button" class="rpt-panel-tab" data-rpt-tab="history">
                                <i class="bi bi-clock-history me-1"></i>Report history
                            </button>
                            <a href="{{ route('superadmin.getadvancescreate') }}" class="rpt-action-btn primary rpt-record-advance"
                                id="rptRecordAdvanceLink" style="text-decoration:none;">Record advance</a>
                        </div>

                        <div class="rpt-panel-content active" id="rpt-tab-expenses">
                            <div id="rpt-expenses-list"></div>
                        </div>

                        <div class="rpt-panel-content" id="rpt-tab-advances">
                            <div class="rpt-advances-subhead" id="rpt-advances-subhead" style="display:none;">
                                Advances <span style="color:#94a3b8;font-weight:600;">·</span>
                                <span id="rpt-adv-section-count">0</span> linked
                            </div>
                            <div id="rpt-advances-table-wrap" class="rpt-adv-wrap" style="display:none;">
                                <table class="rpt-advances-table">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;" aria-hidden="true"></th>
                                            <th>Date</th>
                                            <th>Recorded by</th>
                                            <th>Reference #</th>
                                            <th class="text-end">Amount</th>
                                            <th style="width:44px;" aria-hidden="true"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="rpt-advances-tbody"></tbody>
                                </table>
                            </div>
                            <div class="rpt-empty-state" id="rpt-advances-empty" style="display:none;">
                                <i class="bi bi-wallet2" aria-hidden="true"></i>
                                <div class="rpt-empty-state-title">No advances linked to this report</div>
                                <p class="rpt-empty-state-text">Use <strong>Record advance</strong> to add one, then apply it from the workflow actions when available.</p>
                            </div>
                        </div>

                        <div class="rpt-panel-content" id="rpt-tab-history">
                            <div id="rpt-history-list" class="rpt-history-list-inner"></div>
                            <div class="rpt-empty-state" id="rpt-history-empty" style="display:none;">
                                <i class="bi bi-clock-history" aria-hidden="true"></i>
                                <div class="rpt-empty-state-title">No report history yet</div>
                                <p class="rpt-empty-state-text">Submissions, approvals, and other changes will show up here.</p>
                            </div>
                        </div>

                    </div>

                    {{-- <aside class="rpt-meta-sidebar" id="rptMetaSidebar">
                        <div class="rpt-meta-block">
                            <div class="rpt-meta-k">Policy</div>
                            <div class="rpt-meta-v" id="rpt-meta-policy">—</div>
                        </div>
                        <div class="rpt-meta-block">
                            <div class="rpt-meta-k">Business purpose</div>
                            <div class="rpt-meta-v" id="rpt-meta-biz">—</div>
                        </div>
                        <div class="rpt-meta-block">
                            <div class="rpt-meta-k">Trip</div>
                            <div class="rpt-meta-v" id="rpt-meta-trip">—</div>
                        </div>
                        <div class="rpt-meta-block">
                            <div class="rpt-meta-k">Documents</div>
                            <div class="rpt-meta-v" id="rpt-meta-docs">— <i class="bi bi-plus-lg"
                                    style="opacity:.4;"></i></div>
                        </div>
                    </aside> --}}
                </div>

            </div>{{-- /actual-content --}}
        </div>{{-- /rpt-side-body --}}
    </div>{{-- /rpt-side-panel --}}

    <div class="rpt-advance-overlay" id="rptAdvanceOverlay"></div>
    <div class="rpt-advance-panel" id="rptAdvancePanel">
        <div class="rpt-advance-head">
            <span>Apply advance</span>
            <button type="button" class="rpt-close-btn" id="rptAdvanceClose" title="Close"><i
                    class="bi bi-x-lg"></i></button>
        </div>
        <div class="rpt-advance-toolbar">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;">
                <input type="checkbox" id="rptAdvanceSelectAll"> Select all
            </label>
            <button type="button" class="rpt-action-btn primary" id="rptAdvanceApplyBtn">Apply</button>
            <button type="button" class="rpt-action-btn" id="rptAdvanceClearBtn" style="border:none;background:transparent;color:#2563eb;">Clear
                selection</button>
        </div>
        <div class="rpt-advance-list" id="rptAdvanceList"></div>
    </div>

    {{-- ── Expense Detail Centered Modal ── --}}
    <div class="exp-modal-overlay" id="expModalOverlay"></div>

    <div class="exp-modal" id="expModal">

        {{-- Modal Header --}}
        <div class="exp-modal-header">
            <div class="exp-modal-header-left">
                <div class="exp-modal-header-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <div class="exp-modal-title" id="expModalTitle">Expense Details</div>
                    <div class="exp-modal-subtitle">Click arrows or use keyboard ← → to navigate</div>
                </div>
            </div>
            <button class="exp-modal-close" id="expModalClose" title="Close (Esc)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="exp-modal-body">

            {{-- Left nav arrow --}}
            <button class="exp-nav-arrow left" id="expNavPrev" title="Previous expense">
                <i class="bi bi-chevron-left"></i>
            </button>

            {{-- Receipt panel --}}
            <div class="exp-receipt-panel">
                <img class="exp-receipt-img" id="expReceiptImg" src="" alt="Receipt"
                    style="display:none;">
                <div class="exp-no-receipt-placeholder" id="expNoReceipt">
                    <i class="bi bi-file-image"></i>
                    <span>No Receipt Attached</span>
                    <small>Receipt will appear here once uploaded</small>
                </div>
            </div>

            {{-- Detail panel --}}
            <div class="exp-detail-panel">

                {{-- Top summary --}}
                <div class="exp-detail-top">
                    <div class="exp-detail-date-line">
                        <span class="exp-date-dot"></span>
                        <span class="exp-detail-date-text" id="expDate">--</span>
                        <span class="exp-detail-status-badge badge" id="expStatusBadge">--</span>
                    </div>
                    <div class="exp-category-line">
                        <span class="exp-category-dot"></span>
                        <span class="exp-category-name" id="expCategory">--</span>
                    </div>
                    <div class="exp-merchant-line" id="expMerchantWrap">
                        <span id="expMerchant">--</span>
                        <small>Merchant / Vendor</small>
                    </div>
                    <div class="exp-reimbursable-tag" id="expReimbursableTag" style="display:none;">
                        <i class="bi bi-arrow-repeat"></i> Reimbursable
                    </div>
                    <div class="exp-detail-amount-big" id="expAmount">--</div>
                </div>

                {{-- Tabs --}}
                <div class="exp-detail-tabs">
                    <button class="exp-detail-tab active" data-etab="details">
                        <i class="bi bi-info-circle me-1"></i>Details
                    </button>
                    <button class="exp-detail-tab" data-etab="comments">
                        <i class="bi bi-chat-text me-1"></i>Comments
                    </button>
                    <button class="exp-detail-tab" data-etab="history">
                        <i class="bi bi-clock-history me-1"></i>History
                    </button>
                </div>

                {{-- Tab bodies --}}
                <div class="exp-tab-body">

                    {{-- Details Tab --}}
                    <div class="exp-tab-pane active" id="etab-details">
                        <div class="exp-info-grid">
                            <div class="exp-info-row">
                                <div class="exp-info-label">Invoice #</div>
                                <div class="exp-info-value" id="expInvoice">—</div>
                            </div>
                            <div class="exp-info-row">
                                <div class="exp-info-label">Zone / Branch</div>
                                <div class="exp-info-value" id="expZoneBranch">—</div>
                            </div>
                            <div class="exp-info-row">
                                <div class="exp-info-label">Company</div>
                                <div class="exp-info-value" id="expPolicy">—</div>
                            </div>
                            <div class="exp-info-row">
                                <div class="exp-info-label">Notes</div>
                                <div class="exp-info-value" id="expNotes">—</div>
                            </div>
                            <div class="exp-info-row full-width">
                                <div class="exp-info-label">Description</div>
                                <div class="exp-info-value" id="expDescription">—</div>
                            </div>
                        </div>

                        <div id="expItemsSection" style="margin-top:6px;">
                            <div class="exp-info-label" style="margin-bottom:6px;">Expense Items</div>
                            <table class="exp-items-table">
                                <thead>
                                    <tr>
                                        <th style="width:40%">Category</th>
                                        <th style="width:42%">Description</th>
                                        <th style="width:18%">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="expItemsTbody"></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Comments Tab --}}
                    <div class="exp-tab-pane" id="etab-comments">
                        <div class="exp-empty-tab">
                            <i class="bi bi-chat-text"
                                style="font-size:28px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No comments available.
                        </div>
                    </div>

                    {{-- History Tab --}}
                    <div class="exp-tab-pane" id="etab-history">
                        <div class="exp-empty-tab">
                            <i class="bi bi-clock-history"
                                style="font-size:28px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No history available.
                        </div>
                    </div>

                </div>

            </div>{{-- /exp-detail-panel --}}

            {{-- Right nav arrow --}}
            <button class="exp-nav-arrow right" id="expNavNext" title="Next expense">
                <i class="bi bi-chevron-right"></i>
            </button>

        </div>{{-- /exp-modal-body --}}
    </div>{{-- /exp-modal --}}

    <div id="reportDrawer" class="report-drawer">
        <div class="drawer-content">

            <!-- Header -->
            <div class="drawer-header">
                <div>
                    <h5 id="drawerReportTitle">Report</h5>
                    <span class="badge bg-warning" id="drawerStatus">Pending</span>
                </div>
                <button onclick="closeDrawer()">✕</button>
            </div>

            <!-- Summary -->
            <div class="drawer-summary">
                <div class="summary-box">
                    <small>Total Amount</small>
                    <h4 id="drawerTotal">₹0.00</h4>
                </div>
                <div class="summary-box">
                    <small>Expenses</small>
                    <h4 id="drawerCount">0</h4>
                </div>
            </div>

            <!-- Zoho Style Reimbursement Summary -->


            <!-- Tabs -->
            <div class="drawer-tabs">
                <button class="active">Expenses</button>
                <button>History</button>
            </div>

            <!-- Expense List -->
            <div id="drawerExpenseList" class="drawer-body">
            </div>

        </div>
    </div>

    @include('superadmin.superadminfooter')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('/assets/js/vendor/pettycash_search.js') }}"></script>

    <script>
        const TblZonesModel = @json($TblZonesModel);
        const Tblcompany = @json($Tblcompany);

        (function() {
            const zonesArr = Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel && TblZonesModel.data ?
                TblZonesModel.data : []);
            zonesArr.forEach(function(loc) {
                $('.zone-list').append($('<div data-id="' + loc.id + '">' + (loc.name || '') + '</div>'));
            });
            const companies = (Tblcompany && Tblcompany.data) ? Tblcompany.data : (Array.isArray(Tblcompany) ?
                Tblcompany : []);
            companies.forEach(function(c) {
                var name = c.company_name || '';
                $('.company-list').append(
                    $('<div></div>').attr('data-value', name).attr('data-id', c.id).text(name));
            });
        })();

        let reportFilters = {
            date_from: '',
            date_to: '',
            zone_name: '',
            zone_id: '',
            branch_name: '',
            branch_id: '',
            company_id: '',
            company_name: '',
            vendor_name: '',
            vendor_id: '',
            status_name: '',
            status_id: '',
            state_id: '',
            state_name: '',
            universal_search: '',
        };

        function fmt(n) {
            return '₹' + (parseFloat(n) || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        var rptReceiptBase = @json(rtrim(url('/'), '/'));
        var rptCurrentErId = null;
        var rptRoutes = {
            submit: @json(route('superadmin.expensereportsubmit')),
            approve: @json(route('superadmin.expensereportapprove')),
            reject: @json(route('superadmin.expensereportreject')),
            reimburse: @json(route('superadmin.expensereportreimburse')),
            advances: @json(route('superadmin.expensereportadvances')),
            applyAdvances: @json(route('superadmin.expensereportapplyadvances')),
        };

        function rptPostJson(url, payload) {
            return $.ajax({
                url: url,
                type: 'POST',
                data: Object.assign({}, payload, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }),
            });
        }

        function toggleReportWorkflowTabs() {
            var v = $('.report-tab.active').data('view');
            if (v === 'summary') {
                $('#report-workflow-tabs').addClass('visible');
            } else {
                $('#report-workflow-tabs').removeClass('visible');
            }
        }

        function setReportStats(s) {
            if (!s) return;
            $('#stat-total').text(s.total);
            $('#stat-total-amount').text(fmt(s.total_amount));
            $('#stat-approved').text(s.approved);
            $('#stat-approved-amount').text(fmt(s.approved_amount));
            $('#stat-pending').text(s.pending);
            $('#stat-pending-amount').text(fmt(s.pending_amount));
            $('#stat-rejected').text(s.rejected);
            $('#stat-rejected-amount').text(fmt(s.rejected_amount));
            $('#stat-draft').text(s.draft);
            $('#stat-draft-amount').text(fmt(s.draft_amount));
        }

        function fetchPettyCashReport(page) {
            page = page || 1;
            var perPage = $('#per_page').val() || 15;
            var reportView = $('.report-tab.active').data('view');
            var pendingOnly = $('#pendingApprovalsOnly').is(':checked') ? 1 : 0;
            var wfTab = 'all';
            if (reportView === 'summary') {
                wfTab = $('.report-wf-tab.active').data('wf-tab') || 'all';
            }
            $.ajax({
                url: '{{ route('superadmin.getpettycashreportsajax') }}',
                type: 'GET',
                data: {
                    page: page,
                    per_page: perPage,
                    report_view: reportView,
                    report_workflow_tab: reportView === 'summary' ? wfTab : undefined,
                    pending_approvals_only: pendingOnly,
                    date_from: reportFilters.date_from || undefined,
                    date_to: reportFilters.date_to || undefined,
                    zone_id: reportFilters.zone_id || undefined,
                    company_id: reportFilters.company_id || undefined,
                    branch_id: reportFilters.branch_id || undefined,
                    vendor_id: reportFilters.vendor_id || undefined,
                    status_name: reportFilters.status_name || undefined,
                    state_name: reportFilters.state_name || undefined,
                    universal_search: reportFilters.universal_search || undefined,
                },
                success: function(r) {
                    $('#pettycash-report-body').html(r.html ||
                        '<div class="p-4 text-muted text-center">No data found</div>');
                    $('#pettycash-report-links').html(r.pagination || '');
                    setReportStats(r.stats);
                    if (r.workflow_counts) {
                        var wc = r.workflow_counts;
                        $('.wf-cnt[data-c="all"]').text(wc.all != null ? wc.all : 0);
                        $('.wf-cnt[data-c="pending_approval"]').text(wc.pending_approval != null ?
                            wc.pending_approval : 0);
                        $('.wf-cnt[data-c="approved"]').text(wc.approved != null ? wc.approved : 0);
                        $('.wf-cnt[data-c="reimbursed"]').text(wc.reimbursed != null ? wc.reimbursed :
                            0);
                    }
                },
                error: function() {
                    $('#pettycash-report-body').html(
                        '<div class="p-4 text-danger">Unable to load reports.</div>');
                    $('#pettycash-report-pagination').html('');
                }
            });
        }

        function renderReportSummary() {
            var summaryHtml = '';
            if (reportFilters.date_from && reportFilters.date_to) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="date">' + reportFilters
                    .date_from + ' → ' + reportFilters.date_to + '</span>';
            }
            if (reportFilters.zone_id) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="zone">' + reportFilters
                    .zone_name + '</span>';
            }
            if (reportFilters.branch_id) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="branch">' + reportFilters
                    .branch_name + '</span>';
            }
            if (reportFilters.company_id) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="company">' + reportFilters
                    .company_name + '</span>';
            }
            if (reportFilters.vendor_id) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="vendor">' + reportFilters
                    .vendor_name + '</span>';
            }
            if (reportFilters.status_id) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="status">' + reportFilters
                    .status_name + '</span>';
            }
            if (reportFilters.state_id) {
                summaryHtml += '<span class="filter-badge remove-icon" data-type="state">' + reportFilters
                    .state_name + '</span>';
            }
            if (summaryHtml) {
                summaryHtml +=
                    '<span class="filter-badge filter-clear" id="report-clear-all">Clear all</span>';
            }
            $('#filter-summary').html(summaryHtml || '');
        }

        $(document).ready(function() {
            function setupMultiSelect(selectorInput, selectorHidden) {
                var ns = 'click.rptms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
                $(document).off(ns, selectorHidden).on(ns, selectorHidden, function() {
                    if (selectorHidden === '.zone_id') {
                        reportFilters.zone_id = $(this).val();
                        reportFilters.zone_name = $(selectorInput).val();
                    } else if (selectorHidden === '.branch_id') {
                        reportFilters.branch_id = $(this).val();
                        reportFilters.branch_name = $(selectorInput).val();
                    } else if (selectorHidden === '.company_id') {
                        reportFilters.company_id = $(this).val();
                        reportFilters.company_name = $(selectorInput).val();
                    } else if (selectorHidden === '.vendor_id') {
                        reportFilters.vendor_id = $(this).val();
                        reportFilters.vendor_name = $(selectorInput).val();
                    } else if (selectorHidden === '.status_id') {
                        reportFilters.status_id = $(this).val();
                        reportFilters.status_name = $(selectorInput).val();
                    } else if (selectorHidden === '.state_id') {
                        reportFilters.state_id = $(this).val();
                        reportFilters.state_name = $(selectorInput).val();
                    }
                    fetchPettyCashReport(1);
                });
            }
            setupMultiSelect('.zone-search-input', '.zone_id');
            setupMultiSelect('.branch-search-input', '.branch_id');
            setupMultiSelect('.company-search-input', '.company_id');
            setupMultiSelect('.status-search-input', '.status_id');
            setupMultiSelect('.state-search-input', '.state_id');

            $('.data_values').on('change', function() {
                var dateRange = ($(this).val() || '').trim();
                if (!dateRange || dateRange.indexOf(' to ') === -1) {
                    reportFilters.date_from = '';
                    reportFilters.date_to = '';
                } else {
                    var parts = dateRange.split(' to ');
                    reportFilters.date_from = (parts[0] || '').trim();
                    reportFilters.date_to = (parts[1] || '').trim();
                }
                fetchPettyCashReport(1);
                renderReportSummary();
            });

            var reportSearchTimer = null;
            $('.universal_search').on('keyup', function() {
                clearTimeout(reportSearchTimer);
                var q = $(this).val();
                reportSearchTimer = setTimeout(function() {
                    reportFilters.universal_search = q;
                    fetchPettyCashReport(1);
                }, 350);
            });

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
                    zIndex: 999
                }).show();
                $dropdown.find('.inner-search').focus();
            });

            $(document).on('keyup', '.inner-search', function() {
                var searchVal = $(this).val().toLowerCase();
                $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchVal) > -1);
                });
            });

            function updateMultiSelection($dropdown) {
                var wrapper = $dropdown.data('wrapper');
                if (!wrapper) return;
                var selectedItems = [];
                var selectedIds = [];
                $dropdown.find('.dropdown-list.multiselect div.selected').each(function() {
                    selectedItems.push($(this).text().trim());
                    selectedIds.push($(this).data('id'));
                });
                wrapper.find('.dropdown-search-input').val(selectedItems.join(', '));
                wrapper.find('input[type="hidden"]').val(selectedIds.join(','));
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

            $('.zone_id').on('click', function() {
                var id = $('.zone_id').val();
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: '{{ route('superadmin.getbranchfetch') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $(
                            'input[name="_token"]').val()
                    },
                    success: function(response) {
                        $('.branch-list div').remove();
                        if (response && response.branch && response.branch.length) {
                            response.branch.forEach(function(branch) {
                                $('.branch-list').append(
                                    $('<div data-id="' + branch.id + '">' + (branch
                                            .name || '') +
                                        '</div>'));
                            });
                        }
                    }
                });
            });
            $(document).on('click', '.zone-list div', function() {
                $('.branch-search-input').val('');
                $('.branch_id').val('');
            });

            $('#reportViewMode').on('change', function() {
                fetchPettyCashReport(1);
            });
            $('#pendingApprovalsOnly').on('change', function() {
                if ($(this).is(':checked')) {
                    reportFilters.status_name = '';
                    reportFilters.status_id = '';
                    $('.status-search-input').val('');
                    $('.status_id').val('');
                    $('.status-list div').removeClass('selected');
                    $('.qd-stat-card').removeClass('qd-stat-active');
                }
                fetchPettyCashReport(1);
                renderReportSummary();
            });
            $(document).on('change', '#per_page', function() {
                fetchPettyCashReport(1);
            });

            $(document).on('click', '.qd-stat-card[data-stat-filter]', function() {
                $('#pendingApprovalsOnly').prop('checked', false);
                var val = $(this).data('stat-filter');
                if (val === undefined || val === null || val === '') {
                    reportFilters.status_name = '';
                    reportFilters.status_id = '';
                    $('.status-search-input').val('');
                    $('.status_id').val('');
                } else {
                    reportFilters.status_name = String(val).toLowerCase();
                    reportFilters.status_id = reportFilters.status_name;
                    var label = $(this).find('.qd-stat-label').first().text().trim();
                    $('.status-search-input').val(label);
                    $('.status_id').val(reportFilters.status_name);
                }
                $('.qd-stat-card').removeClass('qd-stat-active');
                $(this).addClass('qd-stat-active');
                fetchPettyCashReport(1);
                renderReportSummary();
            });

            $('#filter-summary').on('click', '.remove-icon', function() {
                var type = $(this).data('type');
                if (type === 'date') {
                    reportFilters.date_from = '';
                    reportFilters.date_to = '';
                    $('.data_values').val('');
                    $('#data_values').text('All Dates');
                } else if (type === 'zone') {
                    reportFilters.zone_id = '';
                    reportFilters.zone_name = '';
                    $('.zone_id').val('');
                    $('.zone-search-input').val('');
                    $('.zone-list div').removeClass('selected');
                } else if (type === 'branch') {
                    reportFilters.branch_id = '';
                    reportFilters.branch_name = '';
                    $('.branch_id').val('');
                    $('.branch-search-input').val('');
                    $('.branch-list div').removeClass('selected');
                } else if (type === 'company') {
                    reportFilters.company_id = '';
                    reportFilters.company_name = '';
                    $('.company_id').val('');
                    $('.company-search-input').val('');
                    $('.company-list div').removeClass('selected');
                } else if (type === 'vendor') {
                    reportFilters.vendor_id = '';
                    reportFilters.vendor_name = '';
                    $('.vendor_id').val('');
                    $('.vendor-search-input').val('');
                } else if (type === 'status') {
                    reportFilters.status_id = '';
                    reportFilters.status_name = '';
                    $('.status_id').val('');
                    $('.status-search-input').val('');
                    $('.status-list div').removeClass('selected');
                } else if (type === 'state') {
                    reportFilters.state_id = '';
                    reportFilters.state_name = '';
                    $('.state_id').val('');
                    $('.state-search-input').val('');
                    $('.state-list div').removeClass('selected');
                }
                fetchPettyCashReport(1);
                renderReportSummary();
            });

            $('#filter-summary').on('click', '#report-clear-all', function() {
                reportFilters = {
                    date_from: '',
                    date_to: '',
                    zone_name: '',
                    zone_id: '',
                    branch_name: '',
                    branch_id: '',
                    company_id: '',
                    company_name: '',
                    vendor_name: '',
                    vendor_id: '',
                    status_name: '',
                    status_id: '',
                    state_id: '',
                    state_name: '',
                    universal_search: '',
                };
                $('.zone-search-input, .branch-search-input, .company-search-input, .vendor-search-input, .status-search-input, .state-search-input')
                    .val('');
                $('.zone_id, .branch_id, .company_id, .vendor_id, .status_id, .state_id').val('');
                $('.data_values').val('');
                $('#data_values').text('All Dates');
                $('.dropdown-list div').removeClass('selected');
                $('.universal_search').val('');
                $('#pendingApprovalsOnly').prop('checked', false);
                $('.qd-stat-card').removeClass('qd-stat-active');
                $('.qd-stat-card[data-stat-filter=""]').addClass('qd-stat-active');
                fetchPettyCashReport(1);
                renderReportSummary();
            });

            $(document).on('click', '#pettycash-report-pagination .pagination a', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                var page = 1;
                try {
                    page = new URL(href, window.location.href).searchParams.get('page') || 1;
                } catch (err) {
                    /* ignore */
                }
                fetchPettyCashReport(page);
            });

            $('#exportReports').on('click', function() {
                var reportView = $('.report-tab.active').data('view')
                var pendingOnly = $('#pendingApprovalsOnly').is(':checked') ? 1 : 0;
                var exportPayload = {
                    report_view: reportView === 'pending' ? 'pending' : reportView,
                    pending_approvals_only: pendingOnly,
                    date_from: reportFilters.date_from || undefined,
                    date_to: reportFilters.date_to || undefined,
                    zone_id: reportFilters.zone_id || undefined,
                    company_id: reportFilters.company_id || undefined,
                    branch_id: reportFilters.branch_id || undefined,
                    vendor_id: reportFilters.vendor_id || undefined,
                    status_name: reportFilters.status_name || undefined,
                    universal_search: reportFilters.universal_search || undefined,
                };
                if (reportView === 'summary') {
                    exportPayload.report_workflow_tab = $('.report-wf-tab.active').data('wf-tab') ||
                        'all';
                }
                window.location.href = '{{ route('superadmin.exportpettycashreports') }}?' + $.param(
                    exportPayload);
            });

            fetchPettyCashReport(1);
            renderReportSummary();

            $(document).on('click', '.report-tab', function() {
                $('.report-tab').removeClass('active');
                $(this).addClass('active');
                toggleReportWorkflowTabs();
                fetchPettyCashReport(1);
            });

            $(document).on('click', '.report-wf-tab', function() {
                $('.report-wf-tab').removeClass('active');
                $(this).addClass('active');
                fetchPettyCashReport(1);
            });

            toggleReportWorkflowTabs();

            // ── Report Side Panel ──────────────────────────────────────────
            var detailUrl = '{{ route('superadmin.getexpensereportdetail') }}';

            // Holds the entries array of the currently-open report (used by the expense modal)
            var currentPanelEntries = [];
            var currentExpIdx = 0;

            function fmtMoney(n) {
                return '₹' + (parseFloat(n) || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function fmtDate(d) {
                if (!d) return '—';
                var m = moment(d, ['YYYY-MM-DD', 'DD/MM/YYYY']);
                return m.isValid() ? m.format('DD MMM YYYY') : d;
            }

            function fmtDateTime(d) {
                if (!d) return '—';
                var m = moment(d);
                return m.isValid() ? m.format('DD/MM/YYYY hh:mm A') : d;
            }

            function statusBadgeHtml(st) {
                st = (st || '').toLowerCase();
                var map = {
                    approved: {
                        bg: '#d1fae5',
                        color: '#065f46',
                        label: 'Approved'
                    },
                    pending_approval: {
                        bg: '#ffedd5',
                        color: '#9a3412',
                        label: 'Pending approval'
                    },
                    pending: {
                        bg: '#fef3c7',
                        color: '#92400e',
                        label: 'Pending'
                    },
                    reimbursed: {
                        bg: '#047857',
                        color: '#fff',
                        label: 'Reimbursed'
                    },
                    rejected: {
                        bg: '#fee2e2',
                        color: '#991b1b',
                        label: 'Rejected'
                    },
                    draft: {
                        bg: '#f3f4f6',
                        color: '#6b7280',
                        label: 'Draft'
                    },
                };
                var s = map[st] || {
                    bg: '#f3f4f6',
                    color: '#374151',
                    label: st ? st.replace(/_/g, ' ') : '—'
                };
                return '<span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:12px;font-size:11.5px;font-weight:700;background:' +
                    s.bg + ';color:' + s.color + ';">' + s.label + '</span>';
            }

            function rptEsc(s) {
                return $('<div/>').text(s == null ? '' : String(s)).html();
            }

            function openRptPanel(erId) {
                rptCurrentErId = erId;
                $('#rptOverlay').addClass('open');
                $('#rptSidePanel').addClass('open');
                $('body').css('overflow', 'hidden');
                $('#rptSummaryPop').removeClass('open').empty();

                // reset
                $('#rpt-loading').show();
                $('#rpt-actual-content').removeClass('is-visible');
                $('#rpt-code').text('…');
                $('#rpt-status-badge').html('');

                $.ajax({
                    url: detailUrl,
                    type: 'GET',
                    data: {
                        er_id: erId
                    },
                    success: function(r) {
                        if (!r.success) {
                            $('#rpt-loading').hide();
                            $('#rpt-actual-content').addClass('is-visible');
                            $('#rpt-expenses-list').html(
                                '<div class="rpt-panel-empty">Unable to load report details.</div>');
                            return;
                        }
                        populateRptPanel(r);
                    },
                    error: function() {
                        $('#rpt-loading').hide();
                        $('#rpt-actual-content').addClass('is-visible');
                        $('#rpt-expenses-list').html(
                            '<div class="rpt-panel-empty text-danger">Failed to load report details.</div>'
                        );
                    }
                });
            }

            function populateRptPanel(r) {
                var report = r.report || {};
                var entries = r.entries || [];
                var wf = (r.workflow_status || '').toLowerCase();

                var totalExp = parseFloat(r.total_amount || 0);
                var reimb = parseFloat(r.reimbursable_amount != null ? r.reimbursable_amount : totalExp);
                var nonReimb = parseFloat(r.non_reimbursable_amount != null ? r.non_reimbursable_amount :
                    Math.max(0, totalExp - reimb));
                var advApp = parseFloat(r.advance_applied_total || 0);
                var toReimb = parseFloat(r.to_reimburse_amount != null ? r.to_reimburse_amount : Math.max(
                    0, reimb - advApp));

                var rb = document.getElementById('rpt-reimbursable');
                if (rb) rb.innerText = fmtMoney(reimb);
                document.getElementById('rpt-advance').innerText = fmtMoney(advApp);
                var balanceEl = document.getElementById('rpt-balance');
                balanceEl.innerText = fmtMoney(toReimb);
                balanceEl.style.color = toReimb < 0 ? '#dc2626' : '#16a34a';

                $('#rpt-hero-total').text(fmtMoney(totalExp));
                $('#rpt-hero-reimburse').text(fmtMoney(toReimb));

                var bd = '';
                bd += '<div class="rpt-summary-breakdown-row"><span>Total expense amount</span><span>' +
                    fmtMoney(totalExp) + '</span></div>';
                bd += '<div class="rpt-summary-breakdown-row"><span>(–) Non-reimbursable amount</span><span>' +
                    fmtMoney(nonReimb) + '</span></div>';
                bd += '<div class="rpt-summary-breakdown-row"><span>(–) Applied advance amount</span><span>' +
                    fmtMoney(advApp) + '</span></div>';
                bd += '<div class="rpt-summary-breakdown-row"><span>Amount to be reimbursed</span><span>' +
                    fmtMoney(toReimb) + '</span></div>';
                $('#rpt-breakdown-lines').html(bd);

                $('#rptSummaryPop').html(
                    'Reimbursable amount (' + fmtMoney(reimb) + ') − Applied advance (' + fmtMoney(
                        advApp) + ') = Amount to be reimbursed (' + fmtMoney(toReimb) + ')');

                $('#rpt-meta-policy').text(r.policy_name || '—');
                $('#rpt-meta-biz').html(rptEsc(r.business_purpose_label || '—'));
                $('#rpt-meta-trip').text(r.trip_label || '—');

                var erNumericId = report.id || rptCurrentErId;
                $('#rptRecordAdvanceLink').attr('href', @json(route('superadmin.getadvancescreate')) +
                    (erNumericId ? ('?expense_report_id=' + encodeURIComponent(erNumericId)) : ''));

                $('#rpt-code').text(report.report_id || '—');
                $('#rpt-status-badge').html(statusBadgeHtml(wf || 'draft'));

                var metaParts = [];
                if (r.submitter_name) {
                    metaParts.push('Submitter: <strong>' + rptEsc(r.submitter_name) + '</strong>');
                }
                if (report.submitted_at) {
                    metaParts.push('Submitted: ' + rptEsc(fmtDate(report.submitted_at)));
                }
                if (wf === 'pending_approval' || wf === 'approved') {
                    metaParts.push('Approver: <strong>' + rptEsc(report.approver_name || '—') +
                        '</strong>');
                }
                if (metaParts.length) {
                    $('#rpt-submitter-line').html(metaParts.join(' · ')).show();
                } else {
                    $('#rpt-submitter-line').empty().hide();
                }

                var warns = r.warnings || [];
                if (warns.length) {
                    var wh = '<div class="rpt-warn-box"><strong>Warning violations (' + warns.length +
                        ')</strong><ul>';
                    warns.forEach(function(w) {
                        wh += '<li>' + rptEsc(w.message || '') + '</li>';
                    });
                    wh += '</ul></div>';
                    $('#rpt-warnings-wrap').html(wh).show();
                } else {
                    $('#rpt-warnings-wrap').hide().empty();
                }

                $('#rptBtnSubmit').toggle(wf === 'draft' || wf === 'rejected');
                $('#rptBtnApprove,#rptBtnReject').toggle(wf === 'pending_approval');
                $('#rptBtnReimburse').toggle(wf === 'approved');
                $('#rptBtnApplyAdvance').toggle(wf === 'pending_approval' || wf === 'approved');

                $('#rpt-name').text(report.report_name || '—');
                var dur = '—';
                if (report.start_date && report.end_date) {
                    dur = fmtDate(report.start_date) + ' – ' + fmtDate(report.end_date);
                }
                $('#rpt-duration span').text(dur);
                var ec = r.entry_count || 0;
                $('#rpt-summary-exp-count').text(ec);
                $('#rpt-exp-badge').text(ec);

                var advs = r.linked_advances || [];
                $('#rpt-adv-tab-badge').text(advs.length);
                $('#rpt-adv-section-count').text(advs.length);
                if (advs.length) {
                    $('#rpt-advances-empty').hide();
                    $('#rpt-advances-table-wrap').show();
                    $('#rpt-advances-subhead').show();
                    var tb = '';
                    advs.forEach(function(a) {
                        tb += '<tr>';
                        tb += '<td><input type="checkbox" disabled style="opacity:.35;" /></td>';
                        tb += '<td>' + rptEsc(fmtDate(a.advance_date)) + '</td>';
                        tb += '<td><div style="display:flex;align-items:center;gap:8px;">';
                        tb += '<span class="rpt-history-avatar" style="width:30px;height:30px;font-size:10px;">' +
                            rptEsc(a.recorded_by_initials || '?') + '</span>';
                        tb += '<span>' + rptEsc(a.recorded_by_name || '—') + '</span></div></td>';
                        tb += '<td>' + rptEsc(a.reference_no && String(a.reference_no).trim() !== '' ? a
                            .reference_no : '—') + '</td>';
                        tb += '<td class="text-end">' + fmtMoney(a.advance_amount) + '</td>';
                        tb +=
                            '<td class="text-center"><i class="bi bi-three-dots-vertical text-muted" style="cursor:default;"></i></td>';
                        tb += '</tr>';
                    });
                    $('#rpt-advances-tbody').html(tb);
                } else {
                    $('#rpt-advances-tbody').empty();
                    $('#rpt-advances-table-wrap').hide();
                    $('#rpt-advances-subhead').hide();
                    $('#rpt-advances-empty').show();
                }

                var hist = r.history || [];
                if (hist.length) {
                    $('#rpt-history-empty').hide();
                    var hh = '';
                    hist.forEach(function(h) {
                        hh += '<div class="rpt-history-row">';
                        hh += '<div class="rpt-history-avatar" style="width:34px;height:34px;font-size:11px;">' +
                            rptEsc(h.actor_initials || '?') + '</div>';
                        hh += '<div style="flex:1;min-width:0;">';
                        hh += '<div style="font-weight:700;color:#0f172a;font-size:13px;">' + rptEsc(h
                            .actor_name || '') + ' <span class="rpt-history-meta">' +
                            rptEsc(fmtDateTime(h.at)) + '</span></div>';
                        hh += '<div class="rpt-history-row-msg">' + rptEsc(h.message || '') + '</div>';
                        hh += '</div></div>';
                    });
                    $('#rpt-history-list').html(hh).show();
                } else {
                    $('#rpt-history-list').empty().hide();
                    $('#rpt-history-empty').show();
                }

                currentPanelEntries = entries;

                // Build expenses list
                var html = '';
                if (!entries.length) {
                    html = '<div class="rpt-empty-state" style="margin:12px 22px 20px;">' +
                        '<i class="bi bi-receipt" aria-hidden="true"></i>' +
                        '<div class="rpt-empty-state-title">No expenses in this report</div>' +
                        '<p class="rpt-empty-state-text">Add petty cash lines from the Petty Cash screen or attach them when creating the report.</p></div>';
                } else {
                    entries.forEach(function(e, idx) {
                        html += '<div class="rpt-expense-entry" data-entry-idx="' + idx +
                            '" title="Click to view full details">';
                        html += '<div class="rpt-entry-header">';

                        // Left column
                        html += '<div class="rpt-entry-left">';
                        html +=
                            '<div class="rpt-entry-date"><i class="bi bi-calendar3" style="font-size:10px;margin-right:4px;"></i>' +
                            fmtDate(e.expense_date) + '</div>';
                        html += '<div class="rpt-entry-vendor">' + (e.vendor_name || '—') + '</div>';

                        var locationParts = [];
                        if (e.zone_name) locationParts.push(e.zone_name);
                        if (e.company_name) locationParts.push(e.company_name);
                        if (e.branch_name) locationParts.push(e.branch_name);
                        if (locationParts.length) {
                            html += '<div class="rpt-entry-location"><i class="bi bi-geo-alt"></i>' +
                                locationParts.join(' &bull; ') + '</div>';
                        }
                        if (e.notes) {
                            html += '<div class="rpt-entry-note"><i class="bi bi-sticky"></i>' + e.notes +
                                '</div>';
                        }
                        html += '</div>';

                        // Right column: amount + status
                        html += '<div class="rpt-entry-right">';
                        html += '<div class="rpt-entry-amount">' + 'Rs.' + ' ' + parseFloat(e
                            .total_amount || 0).toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + '</div>';
                        html += '<div class="rpt-entry-status">' + statusBadgeHtml(e.status) + '</div>';
                        html += '</div>';

                        html += '</div>';

                        // Items sub-list
                        if (e.items && e.items.length) {
                            html += '<div class="rpt-entry-items">';
                            e.items.forEach(function(it) {
                                html += '<div class="rpt-item-row">';
                                html += '<span class="rpt-item-category">' + (it.category_name ||
                                    'Other') + '</span>';
                                html += '<span class="rpt-item-desc">' + (it.description || '') +
                                    '</span>';
                                html += '<span class="rpt-item-amount">₹' + parseFloat(it.amount ||
                                    0).toLocaleString('en-IN', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + '</span>';
                                html += '</div>';
                            });
                            html += '</div>';
                        }

                        html +=
                            '<div class="rpt-entry-click-hint"><i class="bi bi-box-arrow-up-right"></i> Click to expand details</div>';
                        html += '</div>';
                    });
                }
                $('#rpt-expenses-list').html(html);

                $('#rpt-loading').hide();
                $('#rpt-actual-content').addClass('is-visible');

                // reset to expenses tab
                $('.rpt-panel-tab').removeClass('active');
                $('.rpt-panel-content').removeClass('active');
                $('[data-rpt-tab="expenses"]').addClass('active');
                $('#rpt-tab-expenses').addClass('active');
            }

            function closeRptAdvancePanel() {
                $('#rptAdvanceOverlay').removeClass('open');
                $('#rptAdvancePanel').removeClass('open');
                $('#rptAdvanceSelectAll').prop('checked', false);
            }

            function closeRptPanel() {
                closeRptAdvancePanel();
                $('#rptOverlay').removeClass('open');
                $('#rptSidePanel').removeClass('open');
                $('body').css('overflow', '');
            }

            function refreshOpenReportPanel() {
                if (rptCurrentErId) {
                    openRptPanel(rptCurrentErId);
                }
            }

            function updateRptApplyAdvanceBtnLabel() {
                var n = $('.rpt-advance-cb:checked').length;
                $('#rptAdvanceApplyBtn').text(n ? ('Apply ' + n + ' advance' + (n > 1 ? 's' : '')) :
                    'Apply');
            }

            function renderRptAdvanceList(rows) {
                var html = '';
                (rows || []).forEach(function(a) {
                    var linked = parseInt(a.report_id, 10) === parseInt(rptCurrentErId, 10);
                    html += '<div class="rpt-advance-card' + (linked ? ' selected' : '') +
                        '" data-advance-id="' + a.id + '">';
                    html += '<input type="checkbox" class="rpt-advance-cb" data-id="' + a.id + '"' + (
                        linked ? ' checked' : '') + ' />';
                    html += '<div class="rpt-advance-card-body">';
                    html += '<div class="rpt-advance-card-top"><span>' + rptEsc(fmtDate(a
                        .advance_date)) + '</span><span>₹' + parseFloat(a.advance_amount || 0)
                        .toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + '</span></div>';
                    html += '<div class="rpt-advance-meta">Reference#: ' + rptEsc(a.reference_no ||
                        '—') + '</div>';
                    html += '<div class="rpt-advance-meta">Notes: ' + rptEsc(a.notes || '—') +
                        '</div>';
                    html += '</div></div>';
                });
                if (!html) {
                    html = '<div class="text-muted" style="padding:12px;font-size:12px;">No advances available.</div>';
                }
                $('#rptAdvanceList').html(html);
                updateRptApplyAdvanceBtnLabel();
            }

            function openRptAdvancePanel() {
                if (!rptCurrentErId) return;
                $.get(rptRoutes.advances, {
                    er_id: rptCurrentErId
                }, function(res) {
                    if (!res.success) {
                        if (window.toastr) toastr.error('Could not load advances.');
                        return;
                    }
                    renderRptAdvanceList(res.advances || []);
                    $('#rptAdvanceOverlay').addClass('open');
                    $('#rptAdvancePanel').addClass('open');
                }).fail(function() {
                    if (window.toastr) toastr.error('Could not load advances.');
                });
            }

            function rptAjaxMessage(xhr) {
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    return xhr.responseJSON.message;
                }
                return 'Request failed.';
            }

            $(document).on('change', '.rpt-advance-cb', function() {
                $(this).closest('.rpt-advance-card').toggleClass('selected', $(this).is(':checked'));
                updateRptApplyAdvanceBtnLabel();
            });

            $(document).on('click', '.rpt-advance-card', function(e) {
                if ($(e.target).is('input')) return;
                var cb = $(this).find('.rpt-advance-cb');
                cb.prop('checked', !cb.prop('checked')).trigger('change');
            });

            $('#rptAdvanceSelectAll').on('change', function() {
                var on = $(this).is(':checked');
                $('.rpt-advance-cb').prop('checked', on).trigger('change');
            });

            $('#rptAdvanceClearBtn').on('click', function() {
                $('.rpt-advance-cb').prop('checked', false).trigger('change');
                $('#rptAdvanceSelectAll').prop('checked', false);
            });

            $('#rptAdvanceApplyBtn').on('click', function() {
                var ids = $('.rpt-advance-cb:checked').map(function() {
                    return $(this).data('id');
                }).get();
                if (!ids.length) {
                    if (window.toastr) toastr.warning('Select at least one advance.');
                    return;
                }
                rptPostJson(rptRoutes.applyAdvances, {
                    er_id: rptCurrentErId,
                    advance_ids: ids
                }).done(function(res) {
                    if (res.success) {
                        if (window.toastr) toastr.success(res.message || 'Done.');
                        closeRptAdvancePanel();
                        refreshOpenReportPanel();
                        fetchPettyCashReport(1);
                    } else {
                        if (window.toastr) toastr.error(res.message || 'Failed.');
                    }
                }).fail(function(xhr) {
                    if (window.toastr) toastr.error(rptAjaxMessage(xhr));
                });
            });

            $('#rptAdvanceClose').on('click', function() {
                closeRptAdvancePanel();
            });
            $('#rptAdvanceOverlay').on('click', function() {
                closeRptAdvancePanel();
            });

            $('#rptBtnSubmit').on('click', function() {
                rptPostJson(rptRoutes.submit, {
                    er_id: rptCurrentErId
                }).done(function(res) {
                    if (res.success) {
                        if (window.toastr) toastr.success(res.message || 'Submitted.');
                        refreshOpenReportPanel();
                        fetchPettyCashReport(1);
                    } else {
                        if (window.toastr) toastr.error(res.message || 'Failed.');
                    }
                }).fail(function(xhr) {
                    if (window.toastr) toastr.error(rptAjaxMessage(xhr));
                });
            });

            $('#rptBtnApprove').on('click', function() {
                rptPostJson(rptRoutes.approve, {
                    er_id: rptCurrentErId
                }).done(function(res) {
                    if (res.success) {
                        if (window.toastr) toastr.success(res.message || 'Approved.');
                        refreshOpenReportPanel();
                        fetchPettyCashReport(1);
                    } else {
                        if (window.toastr) toastr.error(res.message || 'Failed.');
                    }
                }).fail(function(xhr) {
                    if (window.toastr) toastr.error(rptAjaxMessage(xhr));
                });
            });

            $('#rptBtnReject').on('click', function() {
                if (!confirm('Reject this expense report?')) return;
                rptPostJson(rptRoutes.reject, {
                    er_id: rptCurrentErId
                }).done(function(res) {
                    if (res.success) {
                        if (window.toastr) toastr.success(res.message || 'Rejected.');
                        refreshOpenReportPanel();
                        fetchPettyCashReport(1);
                    } else {
                        if (window.toastr) toastr.error(res.message || 'Failed.');
                    }
                }).fail(function(xhr) {
                    if (window.toastr) toastr.error(rptAjaxMessage(xhr));
                });
            });

            $('#rptBtnReimburse').on('click', function() {
                rptPostJson(rptRoutes.reimburse, {
                    er_id: rptCurrentErId
                }).done(function(res) {
                    if (res.success) {
                        if (window.toastr) toastr.success(res.message || 'Updated.');
                        refreshOpenReportPanel();
                        fetchPettyCashReport(1);
                    } else {
                        if (window.toastr) toastr.error(res.message || 'Failed.');
                    }
                }).fail(function(xhr) {
                    if (window.toastr) toastr.error(rptAjaxMessage(xhr));
                });
            });

            $('#rptBtnApplyAdvance').on('click', function() {
                openRptAdvancePanel();
            });

            // ── Expense Detail Modal ───────────────────────────────────────

            function openExpenseModal(idx) {
                if (!currentPanelEntries.length) return;
                currentExpIdx = idx;
                renderExpenseModal(currentExpIdx);
                $('#expModalOverlay').addClass('open');
                $('#expModal').addClass('open');
            }

            function closeExpenseModal() {
                $('#expModalOverlay').removeClass('open');
                $('#expModal').removeClass('open');
                // reset to Details tab
                $('.exp-detail-tab').removeClass('active');
                $('[data-etab="details"]').addClass('active');
                $('.exp-tab-pane').removeClass('active');
                $('#etab-details').addClass('active');
            }

            function renderExpenseModal(idx) {
                var e = currentPanelEntries[idx];
                var total = currentPanelEntries.length;

                // ── Title: "Expense Details (X of N)"
                $('#expModalTitle').text('Expense Details (' + (idx + 1) + ' of ' + total + ')');

                // ── Nav arrow states (also update subtitle for keyboard hint visibility)

                $('#expNavPrev').toggleClass('disabled', idx === 0);
                $('#expNavNext').toggleClass('disabled', idx === total - 1);

                // ── Date row
                $('#expDate').text(fmtDate(e.expense_date));

                // ── Status badge
                $('#expStatusBadge').replaceWith(
                    '<span class="exp-detail-status-badge badge" id="expStatusBadge">' + statusBadgeHtml(e
                        .status) + '</span>'
                );

                // ── Category (use first item's category, or top-level if available)
                var catName = '—';
                if (e.items && e.items.length && e.items[0].category_name) {
                    catName = e.items[0].category_name;
                } else if (e.category_name) {
                    catName = e.category_name;
                }
                $('#expCategory').text(catName);

                // ── Merchant
                $('#expMerchant').text(e.vendor_name || '—');

                // ── Reimbursable
                var isReimb = parseInt(e.claim_reimbursement || 0) === 1;
                $('#expReimbursableTag').toggle(isReimb);

                // ── Amount
                $('#expAmount').text((e.currency || 'Rs.') + parseFloat(e.total_amount || 0).toLocaleString(
                    'en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                // ── Receipt image (show if receipt_path exists, else placeholder)
                if (e.receipt_path) {
                    var rp = String(e.receipt_path).replace(/^\/+/, '');
                    $('#expReceiptImg').attr('src', rptReceiptBase + '/' + rp).show();
                    $('#expNoReceipt').hide();
                } else {
                    $('#expReceiptImg').hide().attr('src', '');
                    $('#expNoReceipt').show();
                }

                // ── Details tab content
                $('#expDescription').text(
                    (e.items && e.items.map(function(it) {
                        return it.description;
                    }).filter(Boolean).join(', ')) || '—'
                );
                $('#expNotes').text(e.notes || '—');
                $('#expPolicy').text(e.company_name || '—');
                $('#expInvoice').text(e.reference_no || '—');

                var zoneBranch = [e.zone_name, e.branch_name].filter(Boolean).join(' / ') || '—';
                $('#expZoneBranch').text(zoneBranch);

                // ── Expense items sub-table
                var itemsHtml = '';
                if (e.items && e.items.length) {
                    e.items.forEach(function(it) {
                        itemsHtml += '<tr>' +
                            '<td>' + (it.category_name || '—') + '</td>' +
                            '<td>' + (it.description || '—') + '</td>' +
                            '<td>₹' + parseFloat(it.amount || 0).toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + '</td>' +
                            '</tr>';
                    });
                    $('#expItemsSection').show();
                } else {
                    itemsHtml =
                        '<tr><td colspan="3" style="color:#aaa;text-align:center;padding:10px 0;">No items</td></tr>';
                    $('#expItemsSection').hide();
                }
                $('#expItemsTbody').html(itemsHtml);

                // ── Reset to Details tab on every open
                $('.exp-detail-tab').removeClass('active');
                $('[data-etab="details"]').addClass('active');
                $('.exp-tab-pane').removeClass('active');
                $('#etab-details').addClass('active');
            }

            // Open when an expense entry inside the side panel is clicked
            $(document).on('click', '.rpt-expense-entry', function(e) {
                var idx = parseInt($(this).data('entry-idx'));
                if (isNaN(idx)) return;
                openExpenseModal(idx);
            });

            // Close modal
            $(document).on('click', '#expModalClose, #expModalOverlay', closeExpenseModal);

            // Prev / Next navigation
            $(document).on('click', '#expNavPrev', function() {
                if (currentExpIdx > 0) openExpenseModal(currentExpIdx - 1);
            });
            $(document).on('click', '#expNavNext', function() {
                if (currentExpIdx < currentPanelEntries.length - 1) openExpenseModal(currentExpIdx + 1);
            });

            // Keyboard arrow navigation
            $(document).on('keydown.expModal', function(e) {
                if (!$('#expModal').hasClass('open')) return;
                if (e.key === 'ArrowLeft') {
                    if (currentExpIdx > 0) openExpenseModal(currentExpIdx - 1);
                }
                if (e.key === 'ArrowRight') {
                    if (currentExpIdx < currentPanelEntries.length - 1) openExpenseModal(currentExpIdx + 1);
                }
                if (e.key === 'Escape') {
                    closeExpenseModal();
                }
            });

            // Tab switching inside the expense modal
            $(document).on('click', '.exp-detail-tab', function() {
                var tab = $(this).data('etab');
                $('.exp-detail-tab').removeClass('active');
                $(this).addClass('active');
                $('.exp-tab-pane').removeClass('active');
                $('#etab-' + tab).addClass('active');
            });

            // Open on row click (summary rows: Pending Reports + All Reports)
            $(document).on('click', '.report-summary-row', function(e) {
                if ($(e.target).closest('.view-report-link').length) return;
                var erId = $(this).data('er-id');
                if (!erId) return;
                openRptPanel(erId);
            });

            // Close
            $(document).on('click', '#rptCloseBtn, #rptOverlay', closeRptPanel);

            // Tab switching inside panel
            $(document).on('click', '.rpt-panel-tab', function() {
                var tab = $(this).data('rpt-tab');
                $('.rpt-panel-tab').removeClass('active');
                $('.rpt-panel-content').removeClass('active');
                $(this).addClass('active');
                $('#rpt-tab-' + tab).addClass('active');
            });

            $(document).on('click', '#rptViewSummaryBtn', function(e) {
                e.preventDefault();
                $('#rptSummaryPop').toggleClass('open');
            });
            // ─────────────────────────────────────────────────────────────

            // =================== TOGGLE: STATS & FILTERS ===================
            (function() {
                var statsVisible = true;
                var filtersVisible = true;

                $('#toggleStats').on('click', function() {
                    var $btn = $(this);
                    var $chev = $('#statsChevron');
                    if (statsVisible) {
                        $('.qd-stats').addClass('qd-section-hidden');
                        $btn.addClass('qd-toggle-active');
                        $chev.addClass('rotated');
                    } else {
                        $('.qd-stats').removeClass('qd-section-hidden');
                        $btn.removeClass('qd-toggle-active');
                        $chev.removeClass('rotated');
                    }
                    statsVisible = !statsVisible;
                });

                $('#toggleFilters').on('click', function() {
                    var $btn = $(this);
                    var $chev = $('#filtersChevron');
                    if (filtersVisible) {
                        $('.qd-filters, .qd-search-row').addClass('qd-section-hidden');
                        $btn.addClass('qd-toggle-active');
                        $chev.addClass('rotated');
                    } else {
                        $('.qd-filters, .qd-search-row').removeClass('qd-section-hidden');
                        $btn.removeClass('qd-toggle-active');
                        $chev.removeClass('rotated');
                    }
                    filtersVisible = !filtersVisible;
                });
            })();

            function openDrawer(er_id) {

                fetch(`/get-expense-report-detail?er_id=${er_id}`)
                    .then(res => res.json())
                    .then(data => {

                        if (!data.success) return;

                        // Header
                        document.getElementById('drawerReportTitle').innerText =
                            data.report.report_name;

                        document.getElementById('drawerTotal').innerText =
                            '₹' + parseFloat(data.total_amount).toFixed(2);

                        document.getElementById('drawerCount').innerText =
                            data.entry_count;

                        // ===============================
                        // 🔥 ZOHO CALCULATION LOGIC
                        // ===============================

                        let reimbursable = 0;
                        let advance = 0;

                        data.entries.forEach(item => {

                            let amt = parseFloat(item.total_amount || 0);

                            if (parseInt(item.claim_reimbursement) === 1) {
                                reimbursable += amt;
                            } else {
                                advance += amt;
                            }
                        });

                        let balance = reimbursable - advance;

                        document.getElementById('drawerReimbursable').innerText =
                            '₹' + reimbursable.toFixed(2);

                        document.getElementById('drawerAdvance').innerText =
                            '₹' + advance.toFixed(2);

                        let balanceEl = document.getElementById('drawerBalance');

                        balanceEl.innerText = '₹' + balance.toFixed(2);

                        // 🔥 Color logic like Zoho
                        if (balance < 0) {
                            balanceEl.style.color = '#dc2626'; // red
                        } else {
                            balanceEl.style.color = '#16a34a'; // green
                        }

                        // ===============================
                        // Expense List
                        // ===============================

                        let html = '';

                        data.entries.forEach(item => {

                            html += `
                    <div class="expense-card">
                        <div>
                            <div><b>${item.vendor_name ?? '-'}</b></div>
                            <small>${item.category_name ?? '-'}</small>
                        </div>
                        <div>
                            ₹${parseFloat(item.total_amount).toFixed(2)}
                        </div>
                    </div>
                `;
                        });

                        document.getElementById('drawerExpenseList').innerHTML = html;

                        // Open drawer
                        document.getElementById('reportDrawer').classList.add('open');
                    });
            }

            function closeDrawer() {
                document.getElementById('reportDrawer').classList.remove('open');
            }

            // Open report side panel when arriving with ?open_er_id= (e.g. from Petty Cash dashboard, new tab)
            (function openReportFromQueryString() {
                try {
                    var params = new URLSearchParams(window.location.search);
                    var raw = params.get('open_er_id') || params.get('er_id');
                    if (!raw) {
                        return;
                    }
                    var id = parseInt(raw, 10);
                    if (isNaN(id) || id <= 0) {
                        return;
                    }
                    openRptPanel(id);
                    params.delete('open_er_id');
                    params.delete('er_id');
                    var qs = params.toString();
                    var clean = window.location.pathname + (qs ? '?' + qs : '') + window.location.hash;
                    window.history.replaceState({}, '', clean);
                } catch (err) { /* ignore */ }
            })();
        });
    </script>
</body>

</html>
