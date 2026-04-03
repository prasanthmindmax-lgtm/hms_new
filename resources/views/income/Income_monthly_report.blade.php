<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
  <script type="text/javascript">
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
        });
    </script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<style>
%shared{
  box-shadow:2px 2px 10px 5px #b8b8b8;
  border-radius:10px;
}
#thumbnails{
  text-align:center;
  img{
    width:100px;
    height:100px;
    margin:10px;
    cursor:pointer;
      @media only screen and (max-width:480px){
    width:50px;
    height:50px;
  }
    @extend %shared;
    &:hover{
      transform:scale(1.05)
    }
  }
}
#main{
  width:50%;
  height:400px;
  object-fit:cover;
  display:block;
  margin:20px auto;
  @extend %shared;
  @media only screen and (max-width:480px){
    width:100%;
  }
}
.hidden{
  opacity:0;
}
/* ================================
   CONTAINER (ONLY X SCROLL)
================================ */
/* ================================
   SCROLL CONTAINER
================================ */
.table-container {
    width: 100%;
    max-height: calc(100vh - 200px); /* adjust to your header height */
    overflow-y: auto;                /* REQUIRED for sticky */
    overflow-x: auto;                /* horizontal scroll */
    position: relative;
    background: #fff;
}

/* ================================
   TABLE BASE
================================ */
.tblvis {
    border-collapse: separate;       /* REQUIRED for sticky */
    border-spacing: 0;
    min-width: 2400px;               /* wide table */
}

/* ================================
   CELLS
================================ */
.tblvis th,
.tblvis td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    white-space: nowrap;
}

/* ================================
   HEADER BASE STYLE
================================ */
.tblvis thead th {
    background-color: #5a5ee0;
    color: #fff;
    z-index: 55;
}

/* ================================
   STICKY HEADER (2 ROWS)
================================ */

/* Header row 1 */
.tblvis thead tr:nth-child(1) th {
    position: sticky;
    top: 0;
}

/* Header row 2 */
.tblvis thead tr:nth-child(2) th {
    position: sticky;
    top: 40px;
    z-index: 55;
}

/* ================================
   FREEZE FIRST 4 COLUMNS
================================ */

/* 1️⃣ Sl.no */
.tblvis th:nth-child(1),
.tblvis td:nth-child(1) {
    position: sticky;
    left: 0;
    min-width: 70px;
    background: #fff;
    z-index: 50;
}

/* 2️⃣ Date */
.tblvis th:nth-child(2),
.tblvis td:nth-child(2) {
    position: sticky;
    left: 70px;
    min-width: 120px;
    background: #fff;
    z-index: 40;
}

/* 3️⃣ Zone/Location (combined) */
.tblvis th:nth-child(3),
.tblvis td:nth-child(3) {
    position: sticky;
    left: 178px; /* 70 + 120 */
    min-width: 220px;
    max-width: 280px;
    background: #fff;
    z-index: 40;
    word-break: break-word;
    white-space: normal;
    box-shadow: 2px 0 6px rgba(0, 0, 0, 0.15);
}

/* ================================
   HEADER + FROZEN COLUMN INTERSECTION
================================ */
.tblvis thead th:nth-child(-n+3) {
    background-color: #5a5ee0;
    z-index: 90;
}

/* ================================
   ZEBRA ROWS
================================ */
.tblvis tbody tr:nth-child(even) td {
    background-color: #f9f9f9;
}
.modal-dialog-scrollable .modal-body {
    overflow: clip !important;
}
/* ================================
   SHADOW FOR FROZEN COLUMN EDGE
   (applied in 3️⃣ Zone/Location above)
================================ */

        .edit-btn,.save-btn,.apply-btn,.back-btn{
            background-color: #6b6fe5;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
.save-btn {
            background-color: #6b6fe5;
            color: white;
            border: none;
            margin-top:10px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
.selected {
    border: 2px solid #b163a6;
    background-color: #ffffff;
}
        .new-badge {
            background: #d4f8d4;
            color: #228b22;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 9px;
        }
        .ship-now {
            background: #b163a6;
            color: #fff;
            border: none;
            padding: 7px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .ship-now:hover {
            background: #df64ce;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #f3f3f3;
            border-top: 1px solid #ddd;
            font-size: 12px;
            width: 100%;
        }
        .pagination {
            display: flex;
            gap: 5px;
        }
        .pagination button {
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .pagination button:hover {
            background: #eaeaea;
        }
        .pagination button.active {
            background: #b163a6;
            color: #fff;
            border-color: #b163a6;
        }
        @media only screen and (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .footer {
                flex-direction: column;
                gap: 20px;
            }
        }
        .stat-card {
  transition: transform 0.3s ease-in-out;
}
.stat-card:hover {
  transform: translateY(-5px);
}
.stat-card i {
  transition: transform 0.3s ease-in-out;
}
.stat-card:hover i {
  transform: scale(1.1);
}
.multiselect-container {
            position: relative;
            width: 300px;
        }
        .multiselect-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            cursor: pointer;
            background: #f9f9f9;
        }
        .multiselect-options {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            border: 1px solid #ccc;
            background: #fff;
            z-index: 10;
            max-height: 150px;
            overflow-y: auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .multiselect-container:focus-within .multiselect-options {
            display: block;
        }
        .multiselect-options label {
            display: block;
            padding: 8px 10px;
            cursor: pointer;
        }
        .multiselect-options label:hover {
            background: #f0f0f0;
        }
        .multiselect-options input {
            margin-right: 10px;
        }
        .pc-container .page-header + .row {
    padding-top: -5px;
    margin-top: -22px;
}
 /* =========================
       DROPDOWN STYLES
    ========================= */
    .dropdown, .loct-dropdown, .myloct-dropdown, .allloct-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .dropdown input, .loct-dropdown input, .myloct-dropdown input, .allloct-dropdown input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-sizing: border-box;
        cursor: pointer;
        font-size: 12px;
        background: white;
        transition: border-color 0.2s ease;
    }

    .dropdown input:focus, .loct-dropdown input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .dropdown-options, .loct-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-top: none;
        background: white;
        display: none;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 0 0 6px 6px;
        font-size: 12px;
    }

    .dropdown-options div, .loct-dropdown-options div {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .dropdown-options div:hover, .loct-dropdown-options div:hover {
        background-color: #f0f9ff;
        color: #0c4a6e;
    }

    .dropdown.active .dropdown-options,
    .loct-dropdown.active .loct-dropdown-options {
        display: block;
    }

    /* Selected items */
    .dropdown-options div.selected,
    .loct-dropdown-options div.selected {
        background-color: #6366f1 !important;
        color: white !important;
        font-weight: 500;
    }

    /* =========================
       SELECT/DESELECT BUTTONS
    ========================= */
    .dropdown-actions {
        padding: 8px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        gap: 8px;
    }

    .select-all, .deselect-all {
        padding: 6px 12px;
        font-size: 11px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background-color: white;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
        flex: 1;
    }

    .select-all:hover {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
    }

    .deselect-all:hover {
        background-color: #ef4444;
        border-color: #ef4444;
        color: white;
    }
    .text-green { color: green; }
    .text-red   { color: red; }
    /* ==================== ENHANCED STATISTICS CARDS ==================== */
    .stat-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    }

    .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #6b6fe5, #b163a6);
    }

    .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-color: #6b6fe5;
    }

    .stat-card h3 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #2d3748;
    }

    .stat-card p {
    font-size: 11px;
    font-weight: 600;
    color: #718096;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    }
      .stat-card {
        transition: transform 0.3s ease-in-out;
        }
        .stat-card:hover {
        transform: translateY(-5px);
        }
        .stat-card i {
        transition: transform 0.3s ease-in-out;
        }
        .stat-card:hover i {
        transform: scale(1.1);
        }
        .diff-zero {
            color: #28a745 !important;
            background-color: #d4f8d4 !important;
            font-weight: 600 !important;
            border-radius: 4px;
            padding: 2px 6px !important;
        }

        .diff-positive {
            color: #17a2b8 !important;
            background-color: #d1ecf1 !important;
            font-weight: 600 !important;
            border-radius: 4px;
            padding: 2px 6px !important;
        }

        .diff-negative {
            color: #dc3545 !important;
            background-color: #f8d7da !important;
            font-weight: 600 !important;
            border-radius: 4px;
            padding: 2px 6px !important;
        }
        .apply-btn-icon i {
            color: #28a745; /* green tick */
            margin-right: 6px;
        }
        /* ===== Sticky header + freeze first 2 columns (like reconciliation overview) ===== */
        .table-container {
            width: 100%;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            overflow-x: auto;
            position: relative;
            background: #fff;
        }
        .tblvis {
            border-collapse: separate;
            border-spacing: 0;
            min-width: 100%;
        }
        .tblvis th,
        .tblvis td {
            white-space: nowrap;
        }
        .tblvis thead th {
            background-color: #6b6fe5;
            color: #fff;
            z-index: 55;
        }
        .tblvis thead tr:nth-child(1) th {
            position: sticky;
            top: 0;
        }
        .tblvis thead tr:nth-child(2) th {
            position: sticky;
            top: 40px;
            z-index: 55;
        }
        /* Freeze first 2 columns: S.no, Zone */
        .tblvis th:nth-child(1),
        .tblvis td:nth-child(1) {
            position: sticky;
            left: 0;
            min-width: 60px;
            background: #fff;
            z-index: 50;
        }
        .tblvis th:nth-child(2),
        .tblvis td:nth-child(2) {
            position: sticky;
            left: 60px;
            min-width: 120px;
            background: #fff;
            z-index: 40;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
        }
        .tblvis thead th:nth-child(-n+2) {
            background-color: #6b6fe5;
            z-index: 90;
        }
        .tblvis tbody tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .stat-click { cursor: pointer; }
        #mocdocModal .amount-with-attachment,
        #mocdocModal .utr-with-attachment {
            cursor: pointer;
            text-decoration: underline;
            color: #0d6efd !important;
        }
        #mocdocModal .amount-with-attachment:hover,
        #mocdocModal .utr-with-attachment:hover {
            color: #0a58ca !important;
        }
        #mocdocModal .mocdoc-utr-cell {
            max-width: 100px;
            min-width: 70px;
            word-break: break-word;
            white-space: normal;
            font-size: 12px;
            line-height: 1.3;
        }
        #mocdocModal .mocdoc-branch-total {
            cursor: pointer;
            font-size: 11px;
        }
.table-container::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #6b6fe5 0%, #5a5fd8 100%);
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a5fd8 0%, #4a4fcb 100%);
}

/* For Firefox */
.table-container {
    scrollbar-width: thin;
    scrollbar-color: #6b6fe5 #f1f1f1;
}
    .modal-input {
    display: none;
}

.file-view{
    font-size: 13px;
}
.plus-icon {
 display: none;
 font-size:10px;
    cursor: pointer;

    pointer-events: auto;
}

.edit-text {
    display: inline-block;
    min-width: 40px;
}
.calendar-icon{
  cursor:pointer;
  color:#007bff;
}

.calendar-icon:hover{
  color:#0056b3;
}
.info-icon{
  cursor:pointer;
  color:#007bff;
  margin-left:6px;
}
.tdview {
    position: relative;
}

.tooltip-text ,.calander-text{
    position: absolute;
    top: 1px;
    left: 10px;
    background: #333;
    color: #fff;
    padding: 2px 3px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 999;

}
/* show tooltip on hover */
.info-icon:hover + .tooltip-text,.calendar-icon:hover + .calander-text{
  display:inline-block;
}

.custom-tooltip {
    position: absolute;
    background: #ffffff;
    border: none;
    padding: 0;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12),
                0 2px 8px rgba(0, 0, 0, 0.08);
    z-index: 9999;
    font-size: 13px;
    min-width: 320px;
    overflow: hidden;
     display:none;
    animation: tooltipSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes tooltipSlideIn {
    from {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Decorative top border */
.custom-tooltip::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
}

.custom-tooltip table {
    width: 100%;
    background: #ffffff;
    margin: 0;
    border-spacing: 0;
    padding: 8px 0;
}

.custom-tooltip td {
    padding: 10px 20px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    font-size: 13px;
}

.custom-tooltip tr:hover:not(.total) td {
    background: #f8fafc;
}

.custom-tooltip tr:last-child:not(.total) td {
    border-bottom: 1px solid #e2e8f0;
}

.custom-tooltip td:first-child {
    font-weight: 500;
    color: #475569;
}

.custom-tooltip td:last-child {
    font-weight: 600;
    color: #0f172a;
    font-family: 'Segoe UI', system-ui, sans-serif;
    text-align: right;
}

/* Enhanced Total Row */
.custom-tooltip .total {
    background: none !important;
}


.custom-tooltip .total td {
    color: #ffffff;
    padding: 16px 20px;
    border-bottom: none;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    background: none !important;
}

.custom-tooltip .total td:first-child {
    color: #000000;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
}

.custom-tooltip .total:hover td {
    background: transparent;
}

/* Date Section Styling */
.custom-tooltip > span {
    display: inline-block;
    padding: 14px 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
    width: 100%;
    box-sizing: border-box;
    border-top: 1px solid #e2e8f0;
}

.custom-tooltip > span:first-of-type {
    color: #475569;
    font-weight: 600;
    padding-right: 8px;
    background: transparent;
    border: none;
    width: auto;
    display: inline;
}

.custom-tooltip > span:last-of-type {
    color: #0f172a;
    font-weight: 600;
    padding-left: 0;
    background: transparent;
    width: auto;
}

/* Date container wrapper */
.custom-tooltip > span:first-of-type::before {
    content: '📅';
    margin-right: 6px;
    font-size: 14px;
}

/* Icon hover effects */
.info-icon, .calendar-icon {
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-size: 12px;
    display: inline-block;
}

.info-icon:hover {
    transform: scale(1.15) rotate(12deg);
    color: #6366f1;
    filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.3));
}

.calendar-icon:hover {
    transform: scale(1.15);
    color: #8b5cf6;
    filter: drop-shadow(0 2px 4px rgba(139, 92, 246, 0.3));
}
.remark_value{
        font-size: 20px;
    font-weight: 600;
}
.remark_viewer{
    padding: 10px;
    font-size: 15px;
}
.tooltip-box{
  position:absolute;
  background:#333;
  color:#fff;
  padding:8px 12px;
  border-radius:6px;
  white-space: normal;     /* <<< allow wrapping */
  max-width:260px;         /* <<< optional limit width */
  font-size:12px;
  z-index:99999;
  display:none;
  line-height:1.4;
  box-shadow:0 3px 8px rgba(0,0,0,.25);
}


.disabled{
    pointer-events:none;
    opacity:0.5;   /* optional */
    cursor:not-allowed;
}
.allign{
    display: flex;

}
 .reconciliation-section {
        background: #fff;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .section-header {
        margin-bottom: 8px;
    }

    .header-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 6px;
        color: white;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .header-badge i {
        font-size: 12px;
    }

    /* Stat Card - Compact Design */
    .stat-card {
        background: #fafafa;
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        padding: 10px 12px;
        transition: all 0.2s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        border-color: #d0d0d0;
    }

    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }

    .stat-header i {
        font-size: 14px;
        margin-right: 6px;
        opacity: 0.8;
    }

    .stat-label {
        font-size: 11px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1;
    }

    /* Total Cards */
    .stat-total {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);

    }

    .stat-total:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    /* Difference Cards */
    .stat-diff {
        background: #fffbf0;
        border-color: #ffe4a0;
    }

    .diff-value {
        font-family: 'Courier New', monospace;
    }

    /* Positive/Negative Colors */
    .text-positive {
        color: #10b981 !important;
    }

    .text-negative {
        color: #ef4444 !important;
    }

    .bg-positive {
        background-color: #d1fae5 !important;
        border-color: #86efac !important;
    }

    .bg-negative {
        background-color: #fee2e2 !important;
        border-color: #fca5a5 !important;
    }

    /* Custom Tabs */
    .custom-tabs {
        border-bottom: 2px solid #e9ecef;
    }

    .custom-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 13px;
        background: transparent;
    }

    .custom-tabs .nav-link.active {
        color: #495057;
        border-bottom: 3px solid #6366f1;
        background: transparent;
    }

    .custom-tabs .nav-link:hover {
        color: #6366f1;
        border-color: transparent;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .stat-value {
            font-size: 16px;
        }

        .header-badge {
            font-size: 12px;
            padding: 3px 10px;
        }

        .reconciliation-section {
            padding: 10px;
        }
    }

    /* Print Styles */
    @media print {
        .stat-card {
            break-inside: avoid;
        }
    }
    .remarks-table td {
        white-space: normal !important;
        word-break: break-word;
        max-width: 600px;   /* adjust as needed */
    }

    </style>
    <script>
        function rowClick(event) {
            // Remove the 'selected' class from any currently selected row
            const selectedRows = document.querySelectorAll('.selected');
            selectedRows.forEach(row => row.classList.remove('selected'));
            // Add the 'selected' class to the clicked row
            const clickedRow = event.currentTarget;
            clickedRow.classList.add('selected');
        }
    </script>
  <body style="overflow-x: hidden;">
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
<!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->

<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
              <!-- [ Main Content ] start -->
              <div class="container-fluid px-4" style="margin-top: -20px;">
                <!-- MocDoc Row -->
                <div class="reconciliation-section mb-2">
                    <div class="section-header d-flex align-items-center mb-2">
                        <div class="header-badge bg-primary">
                            <i class="fas fa-desktop me-2"></i>MocDoc
                        </div>
                        <small class="text-muted ms-2">System Data</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="moc_cash_amt">
                                <div class="stat-header">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                    <span class="stat-label">Cash</span>
                                </div>
                                <div class="stat-value" id="total_cash">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="moc_card_amt">
                                <div class="stat-header">
                                    <i class="fas fa-credit-card text-info"></i>
                                    <span class="stat-label">Card</span>
                                </div>
                                <div class="stat-value" id="total_card">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="moc_neft_amt">
                                <div class="stat-header">
                                    <i class="fas fa-university text-primary"></i>
                                    <span class="stat-label">NEFT</span>
                                </div>
                                <div class="stat-value" id="total_neft">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="moc_upi_amt">
                                <div class="stat-header">
                                    <i class="fas fa-mobile-alt text-warning"></i>
                                    <span class="stat-label">UPI</span>
                                </div>
                                <div class="stat-value" id="total_upi">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="moc_other_amt">
                                <div class="stat-header">
                                    <i class="fas fa-wallet text-secondary"></i>
                                    <span class="stat-label">Other</span>
                                </div>
                                <div class="stat-value" id="total_other">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <i class="fas fa-wallet text-secondary"></i>
                                    <span class="stat-label">Total UPI/Card</span>
                                </div>
                                <div class="stat-value" id="total_upi_card">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-total stat-click" data-type="moc_overall_total">
                                <div class="stat-header">
                                    <i class="fas fa-chart-line text-primary"></i>
                                    <span class="stat-label">Total</span>
                                </div>
                                <div class="stat-value text-primary fw-bold" id="total_amount">0.00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actual Row -->
                <div class="reconciliation-section mb-2">
                    <div class="section-header d-flex align-items-center mb-2">
                        <div class="header-badge bg-danger">
                            <i class="fas fa-hand-holding-usd me-2"></i>Actual
                        </div>
                        <small class="text-muted ms-2">Manual Entry</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="deposite_amount">
                                <div class="stat-header">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                    <span class="stat-label">Cash</span>
                                </div>
                                <div class="stat-value" id="actuall_cash">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="mespos_card">
                                <div class="stat-header">
                                    <i class="fas fa-credit-card text-info"></i>
                                    <span class="stat-label">Card</span>
                                </div>
                                <div class="stat-value" id="actuall_card">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="bank_neft">
                                <div class="stat-header">
                                    <i class="fas fa-university text-primary"></i>
                                    <span class="stat-label">NEFT</span>
                                </div>
                                <div class="stat-value" id="actuall_neft">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="mespos_upi">
                                <div class="stat-header">
                                    <i class="fas fa-mobile-alt text-warning"></i>
                                    <span class="stat-label">UPI</span>
                                </div>
                                <div class="stat-value" id="actuall_upi">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-click" data-type="bank_others">
                                <div class="stat-header">
                                    <i class="fas fa-wallet text-secondary"></i>
                                    <span class="stat-label">Other</span>
                                </div>
                                <div class="stat-value" id="actuall_other">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <i class="fas fa-wallet text-secondary"></i>
                                    <span class="stat-label">Bank Chargers </br> UPI/Card </span>
                                </div>
                                <div class="stat-value" id="actuall_bank_chagers">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <i class="fas fa-wallet text-secondary"></i>
                                    <span class="stat-label">Total UPI/Card</span>
                                </div>
                                <div class="stat-value" id="actuall_total_upi_card">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-total stat-click" data-type="actual_total">
                                <div class="stat-header">
                                    <i class="fas fa-chart-line text-danger"></i>
                                    <span class="stat-label">Total</span>
                                </div>
                                <div class="stat-value text-danger fw-bold" id="actuall_amount">0.00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Difference Row -->
                <div class="reconciliation-section mb-3">
                    <div class="section-header d-flex align-items-center mb-2">
                        <div class="header-badge bg-warning">
                            <i class="fas fa-balance-scale me-2"></i>Difference
                        </div>
                        <small class="text-muted ms-2">Variance Analysis</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-diff stat-click" data-type="diff_cash">
                                <div class="stat-header">
                                    <i class="fas fa-exchange-alt text-success"></i>
                                    <span class="stat-label">Cash</span>
                                </div>
                                <div class="stat-value diff-value" id="actuall_cash_diff">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-diff stat-click" data-type="diff_card">
                                <div class="stat-header">
                                    <i class="fas fa-exchange-alt text-info"></i>
                                    <span class="stat-label">Card</span>
                                </div>
                                <div class="stat-value diff-value" id="actuall_card_diff">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-diff stat-click" data-type="diff_neft">
                                <div class="stat-header">
                                    <i class="fas fa-exchange-alt text-primary"></i>
                                    <span class="stat-label">NEFT</span>
                                </div>
                                <div class="stat-value diff-value" id="actuall_neft_diff">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-diff stat-click" data-type="diff_upi">
                                <div class="stat-header">
                                    <i class="fas fa-exchange-alt text-warning"></i>
                                    <span class="stat-label">UPI</span>
                                </div>
                                <div class="stat-value diff-value" id="actuall_upi_diff">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-diff stat-click" data-type="diff_other">
                                <div class="stat-header">
                                    <i class="fas fa-exchange-alt text-secondary"></i>
                                    <span class="stat-label">Other</span>
                                </div>
                                <div class="stat-value diff-value" id="actuall_other_diff">0.00</div>
                            </div>
                        </div>

                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-diff stat-click" data-type="diff_upicard">
                                <div class="stat-header">
                                    <i class="fas fa-exchange-alt text-secondary"></i>
                                    <span class="stat-label">Total UPI/Card</span>
                                </div>
                                <div class="stat-value diff-value" id="actuall_total_upicard_diff">0.00</div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="stat-card stat-total stat-diff stat-click" data-type="diff_total">
                                <div class="stat-header">
                                    <i class="fas fa-calculator text-warning"></i>
                                    <span class="stat-label">Total</span>
                                </div>
                                <div class="stat-value text-warning fw-bold diff-value" id="actuall_amount_diff">0.00</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- [ Main Content ] end -->
        <!-- [ Main Content ] end -->
        <div class="row">
            <div class="col-xl-12 col-md-12" >
                <div class="card-body border-bottom pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                        <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                            class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab" data-bs-target="#analytics-tab-1-pane" type="button"
                            role="tab"
                            aria-controls="analytics-tab-1-pane"
                            aria-selected="true">Income Reconciliation Monthly Report</button>
                        </li>
                        </ul>
                    </div>
            </div>
        </div><br>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">
                <div class="allign">
                    <div class="row col-xl-10">
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>

                                <span style="display:none;"  id="dateviewsall"></span>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4">
                            <div class="card">
                                <div class="dropdown">
                                        <input type="text" class="searchZone multi_search checkvalues_search" name="tblzones.name" id="izone_views" placeholder="Select Zone" autocomplete="off">
                                    <div class="dropdown-options multi_search options_branch">
                                        <div class="dropdown-actions">
                                            <button type="button" class="select-all">Select All</button>
                                            <button type="button" class="deselect-all">Deselect All</button>
                                        </div>

                                        @if($zones)
                                            @foreach($zones as $zone)
                                            <div data-value="{{$zone->name}}">{{$zone->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchZone multi_search checkvalues_search" name="tbl_locations.name" id="ibranch_views" placeholder="Select Branch" autocomplete="off">
                                    <div class="dropdown-options multi_search options_branch branch_viewsall">
                                        <div class="dropdown-actions">
                                            <button type="button" class="select-all">Select All</button>
                                            <button type="button" class="deselect-all">Deselect All</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-2">
                        <div class="dropdown">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" id="downloadExcelBtn" class="dropdown-item">
                                        <i class="fa fa-file-excel text-success me-2"></i> Download XLSX
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="downloadCsvBtn" class="dropdown-item">
                                        <i class="fa fa-file-csv text-primary me-2"></i> Download CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <p style="margin-top: -9px;" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="dcounts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
                    <span class="cincome_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
                    <span style="cursor: pointer;" id="cbranch_search" class="badge bg-success value_views_mainsearch"></span>
                    <span style="cursor: pointer;" id="czone_search" class="badge bg-success value_views_mainsearch"></span>
                    <span style="cursor: pointer;" id="income_search" class="badge bg-success value_views_mainsearch"></span>
                    <span  class="badge bg-danger clear_views" style="display:none;">Clear all</span>
                </p>
                <!-- <div class="btn-group btn-group-sm mb-2" role="group">
                    <button type="button" class="btn btn-primary view-mode-btn active" data-mode="summary">Summary</button>
                    <button type="button" class="btn btn-outline-primary view-mode-btn" data-mode="datewise">Date-wise</button>
                </div> -->
                <br>
                <div class="col-sm-12">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="tblvis" id="monthly_report_table">
                                <thead id="monthly_report_thead">
                                    <tr>
                                        <th rowspan="2">S.no</th>
                                        <th rowspan="2">Zone</th>
                                        <th rowspan="2">Branch</th>
                                        <th colspan="6">As per Mocdoc Sale</th>
                                        <th colspan="5">Actual Collection As per Bank Statement</th>
                                        <th colspan="5">Difference</th>
                                    </tr>
                                    <tr>
                                        <th>Cash</th> <th>Card</th> <th>Upi</th><th>Total card/Upi</th> <th>NEFT</th><th>Other</th>
                                        <th>Cash</th> <th>CARD&UPI Bank charges</th> <th>Total Card / UPI</th> <th>NEFT</th><th>Other</th>
                                        <th>Cash</th> <th>Total Card / UPI</th> <th>NEFT</th><th>Other</th> <th>Remark</th>

                                    </tr>
                                </thead>
                                <!-- sample data  -->
                                <tbody id="loader_row">
                                    <tr>
                                    <td colspan="22">
                                        <div id="loader-container">
                                        <div id="progress-bar">Loading: 0%</div>
                                        <div id="error-message" style="color: red; display: none;"></div>
                                        </div>
                                    </td>
                                    </tr>
                                </tbody>
                                <tbody id="daily_details_recon" style="display:none;">

                                </tbody>


                            </table>
                        </div>
                        <div class="footer">
                            <div>
                                Items per page:
                                <select id="itemsPerPageSelect">
                                    <option>20</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </div>
                            <div class="pagination" id="ticketpagination"></div>
                        </div>
                    </div>
                </div>
            </div>




<div class="modal fade" id="cashRadiantModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Amount</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- CASH -->
        <input type="file" id="modalCashRadiant" class="form-control modal-input" data-field="cash_radiant" accept=".pdf,image/*" />
        <input type="file" id="modalCashBank" class="form-control modal-input mt-2" data-field="cash_bank"  accept=".pdf,image/*"/>

        <!-- CARD -->
        <input type="file" id="modalCardRadiant" class="form-control modal-input mt-2" data-field="card_radiant"  accept=".pdf,image/*"/>
        <input type="file" id="modalCardBank" class="form-control modal-input mt-2" data-field="card_bank" accept=".pdf,image/*" />

        <!-- UPI -->
        <input type="file" id="modalUpiRadiant" class="form-control modal-input mt-2" data-field="upi_radiant"  accept=".pdf,image/*"/>
        <input type="file" id="modalUpiBank" class="form-control modal-input mt-2" data-field="upi_bank" accept=".pdf,image/*" />

        <!-- NEFT -->
        <input type="file" id="modalNeftBank" class="form-control modal-input mt-2" data-field="neft_bank"  accept=".pdf,image/*"/>

      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" id="modalSaveCash">Save</button>
      </div>

    </div>
  </div>
</div>

<!-- file preview model -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" style="z-index: 2000;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">File Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center" id="filePreviewBody">
        <!-- dynamic content -->
      </div>

      <div class="modal-footer">
        <a href="#" id="downloadFileBtn" class="btn btn-success" download>
          Download
        </a>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- edit apply btn logic -->
<!-- Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Date Range</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="fromDate" class="form-label">From</label>
            <input type="date" id="fromDate" class="form-control">
        </div>
        <div class="mb-3">
            <label for="toDate" class="form-label">To</label>
            <input type="date" id="toDate" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="applyDates" class="btn btn-primary">Apply</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="remarksModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Date-wise Remarks</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered remarks-table">
          <thead>
            <tr>
              <th style="width:150px">Date</th>
              <th>Remark</th>
            </tr>
          </thead>
          <tbody id="remarksTableBody"></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- Split-up modal: date-wise breakdown when clicking a stat card -->
<div class="modal fade" id="mocdocModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mocdocModalTitle">Split-up – Date Wise</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="mocdocModalBranchTotals" class="border rounded p-2 mb-2 bg-light"></div>
        <p id="mocdocModalBackLink" class="mb-2" style="display:none;"><a href="#" class="btn btn-sm btn-outline-secondary mocdoc-back-to-all"><i class="fa fa-arrow-left"></i> Back to all branches</a></p>
        <div class="table-responsive" style="max-height: 70vh;">
          <table class="table table-bordered table-sm">
            <thead id="mocdocModalHead"></thead>
            <tbody id="mocdocModalBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- income related script start -->

 <script src="{{ asset('/assets/income_new/Income_monthly_report.js') }}"></script>

<script type="text/javascript">

	const incomefetchUrl = "{{ route('superadmin.incomereportfetch') }}";
    const incomeBranchUrlfitter = "{{ route('superadmin.incomereportfilter') }}";
	const dateOverviewIncomeUrl = "{{ route('superadmin.incomeMonthlyDateFilter') }}";
	const incomeMonthlyDatafitter = "{{ route('superadmin.IncomeMontlyReportData') }}";
	const incomestore = "{{ route('superadmin.incomestore') }}";
	const incomeradiantfetch = "{{ route('superadmin.incomeradiantfetch') }}";
	const incomeuploadFile = "{{ route('superadmin.incomeuploadFile') }}";
	const incomedatecheck = "{{ route('superadmin.recon.check') }}";
    const downloadIncomeUrl = "{{ route('income.downloadincome_montly_report') }}";
    var filterTriggerTimer = null;

        // Financial Year Helpers (India: Apr 1 – Mar 31)
        function getFinancialYearRange(offset = 0) {
            const today = moment().add(offset, 'year');

            const fyStart = moment({
                year: today.month() >= 3 ? today.year() : today.year() - 1,
                month: 3, // April (0-based)
                day: 1
            });

            const fyEnd = fyStart.clone().add(1, 'year').subtract(1, 'day');

            return [fyStart, fyEnd];
        }

        // Default start & end (This Month)
        let start = moment().startOf('month');
        let end   = moment().endOf('month');

        // Callback function to update the span text with the selected date range
        function cb(start, end) {
            $("#dateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#dateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#reportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#reportrange span').html('Yesterday');
                } else {
                    $('#reportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }


        // Initialize daterangepicker
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            alwaysShowCalendars: true,
            ranges: {

                'This Month': [
                    moment().startOf('month'),
                    moment().endOf('month')
                ],

                'Last Month': [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().subtract(1, 'month').endOf('month')
                ],

                'Last 2 Months': [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().endOf('month')
                ],

                'Last 3 Months': [
                    moment().subtract(2, 'month').startOf('month'),
                    moment().endOf('month')
                ],

                'This Year': [
                    moment().startOf('year'),
                    moment().endOf('year')
                ],

                'Last Year': [
                    moment().subtract(1, 'year').startOf('year'),
                    moment().subtract(1, 'year').endOf('year')
                ],

                'This Financial Year': getFinancialYearRange(0),

                'Last Financial Year': getFinancialYearRange(-1)
            }
        }, cb);

        // Set initial date range text
        cb(start, end);
    </script>

	<script>


        $(document).ready(function () {
         /* =========================
            OPEN DROPDOWN
            ========================= */
            $(document).on("focus click", ".searchZone", function (e) {
                e.stopPropagation();
                $(this).closest(".dropdown").addClass("active");
            });

            /* =========================
            SEARCH FILTER
            ========================= */
            $(document).on("input", ".searchZone", function () {
                const searchText = $(this).val().toLowerCase().split(",").pop().trim();
                $(this).siblings(".dropdown-options").find("div[data-value]").each(function () {
                    $(this).toggle($(this).text().toLowerCase().includes(searchText));
                });
            });

            /* =========================
            SELECT / DESELECT SINGLE VALUE
            ========================= */
          $(document).on("click", ".dropdown-options div[data-value]", function (e) {
                e.stopPropagation();

                const input = $(this).closest(".dropdown").find(".searchZone");
                const selectedValue = $(this).text().trim();

                let values = input.data("values");

                if (!Array.isArray(values)) {
                    values = [];
                    input.data("values", values);   // ⭐ IMPORTANT
                }

                if (values.includes(selectedValue)) {
                    values = values.filter(v => v !== selectedValue);
                    $(this).removeClass("selected");
                } else {
                    values.push(selectedValue);
                    $(this).addClass("selected");
                }

                input.data("values", values);  // ⭐ ALWAYS update
                input.val(values.join(", "));
            });


            /* =========================
            KEEP SELECTION ON FOCUS
            ========================= */
            $(document).on("focus", ".searchZone", function () {
                const input = $(this);
                const values = input.data("values") || [];
                input.siblings(".dropdown-options").find("div[data-value]").each(function () {
                    $(this).toggleClass("selected", values.includes($(this).text().trim()));
                });
            });

            /* =========================
            BLUR VALIDATION
            ========================= */
            $(document).on("blur", ".multi_search", function () {
                const input = $(this);
                const values = input.data("values") || [];
                input.val(values.join(", "));
            });

            /* =========================
            LOCATION DROPDOWN (UNCHANGED)
            ========================= */
            $(document).on("focus click", ".searchLocation", function (event) {
                event.stopPropagation();
                const inputField = $(this);
                const dropdown = inputField.closest(".loct-dropdown");
                const options = dropdown.find(".loct-dropdown-options");
                $(".loct-dropdown-options").hide();
                options.show();
                dropdown.addClass("active");
                options.find("div").show();
                const selectedValue = inputField.val().trim();
                options.find("div").each(function () {
                    $(this).toggleClass("selected", $(this).text().trim() === selectedValue);
                });
            });

            $(document).on("input", ".searchLocation", function () {
                const searchText = $(this).val().toLowerCase();
                $(this).siblings(".loct-dropdown-options").find("div").each(function () {
                    $(this).toggle($(this).text().toLowerCase().includes(searchText));
                });
            });

            $(document).on("click", ".loct-dropdown-options div", function (event) {
                event.stopPropagation();
                const selectedValue = $(this).text();
                const inputField = $(this).closest(".loct-dropdown").find(".searchLocation");
                inputField.val(selectedValue);
                $(this).addClass("selected").siblings().removeClass("selected");
                $(".loct-dropdown-options").hide();
                $(".loct-dropdown").removeClass("active");
            });

            /* =========================
            CLOSE DROPDOWN
            ========================= */
            $(document).on("click", function () {
                $(".dropdown").removeClass("active");
                $(".loct-dropdown-options").hide();
                $(".loct-dropdown").removeClass("active");
            });
        });



	</script>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
