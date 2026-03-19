<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<style>
 #loader-container {
      width: 100%;
      background: #eee;
      border: 1px solid #ccc;
    }

    #progress-bar {
      height: 30px;
      width: 0%;
      background-color: #4caf50;
      color: white;
      font-weight: bold;
      text-align: center;
      line-height: 30px;
      transition: width 0.3s ease;
    }

 #daily_details {
            display: none;
        }
.loading-bar {
  width: 20px;
  height: 15px;
  margin: 0 5px;
  background-color: #3498db;
  border-radius: 5px;
  animation: loading-wave-animation 1s ease-in-out infinite;
}

.loading-bar:nth-child(2) {
  animation-delay: 0.1s;
}

.loading-bar:nth-child(3) {
  animation-delay: 0.2s;
}

.loading-bar:nth-child(4) {
  animation-delay: 0.3s;
}

 %shared {
        box-shadow: 2px 2px 10px 5px #b8b8b8;
        border-radius: 10px;
    }

    #thumbnails {
        text-align: center;

        img {
            width: 100px;
            height: 100px;
            margin: 10px;
            cursor: pointer;

            @media only screen and (max-width:480px) {
                width: 50px;
                height: 50px;
            }

            @extend %shared;

            &:hover {
                transform: scale(1.05)
            }
        }
    }

    #main {
        width: 50%;
        height: 400px;
        object-fit: cover;
        display: block;
        margin: 20px auto;
        @extend %shared;

        @media only screen and (max-width:480px) {
            width: 100%;
        }
    }

    .hidden {
        opacity: 0;
    }

    .table-container {
        width: 104%;
        padding: 0px;
        font-size: 12px;
        position: relative;
        overflow-x: auto;
        /* Enable horizontal scrolling */
        overflow-y: auto;
        /* Enable vertical scrolling */
        max-height: 450px;
        /* Adjust as necessary */
    }

    /* Thin scrollbar for modern browsers */
    .table-container::-webkit-scrollbar {
        width: 6px;
        /* Width of vertical scrollbar */
        height: 6px;
        /* Height of horizontal scrollbar */
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #b163a6;
        /* Color of the scrollbar handle */
        border-radius: 4px;
        /* Rounded corners */
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #df64ce;
        /* Color when hovered */
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* Background of the scrollbar track */
    }

    /* For Firefox */
    .table-container {
        scrollbar-width: thin;
        /* Thin scrollbar */
        scrollbar-color:#bbbee5 #f1f1f1;
        /* Handle color and track color */
    }

    .tbl {
        width: 100%;
        border-collapse: collapse;
        /* table-layout: fixed; Ensures consistent column widths */
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .thd {
        position: sticky;
        /* Keeps the header fixed within the container */
        top: -1px;
        /* Aligns it to the top of the container */
        z-index: 10;
        /* Ensures it stays above other elements */
        background: #f8f8f8;
        /* Prevent transparency during scrolling */
        box-shadow: 12px -1px 0px rgba(0, 0, 0, 0.1);
        /* Adds a subtle shadow for better visibility */
    }

    .thview,
    .tdview {
        padding: 9px;
        text-align: left;
        border-bottom: 11px solid #ddd;
    }

    .thview {
        font-weight: bold;
        font-size: 12px;
        color: #333;
    }

    .tdview {
        font-size: 12px;
        color: #000000;
		height: 75px;
    }

    .trview:last-child .tdview {
        border-bottom: none;
    }

    .selected {
        border: 2px solid #080fd399;
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
        width: 104%;
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


    .dept-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .dept-dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .dept-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 296px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-top: none;
        background: #fff;
        display: none;
        z-index: 9999;
        /* Increased z-index */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
		font-size:12px
    }

    .dept-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .dept-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .dept-dropdown.active .dept-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.dept-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}

.loct-dropdown,.myloct-dropdown,.allloct-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .loct-dropdown input, .myloct-dropdown input, .allloct-dropdown input{
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .loct-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 296px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-top: none;
        background: #fff;
        display: none;
        z-index: 9999;
        /* Increased z-index */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
		font-size:12px
    }

    .loct-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .loct-dropdown-options div:hover{
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .loct-dropdown.active .loct-dropdown-options{
        display: block;
    }

    /* Highlight selected values */
.loct-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}


    .dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 296px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-top: none;
        background: #fff;
        display: none;
        z-index: 9999;
        /* Increased z-index */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .dropdown.active .dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.dropdown-option<style>
    /* =========================
       LOADER & PROGRESS BAR
    ========================= */
    #loader-container {
        width: 100%;
        background: #eee;
        border: 1px solid #ccc;
    }

    #progress-bar {
        height: 30px;
        width: 0%;
        background-color: #4caf50;
        color: white;
        font-weight: bold;
        text-align: center;
        line-height: 30px;
        transition: width 0.3s ease;
    }

    #daily_details {
        display: none;
    }

    .loading-bar {
        width: 20px;
        height: 15px;
        margin: 0 5px;
        background-color: #3498db;
        border-radius: 5px;
        animation: loading-wave-animation 1s ease-in-out infinite;
    }

    .loading-bar:nth-child(2) {
        animation-delay: 0.1s;
    }

    .loading-bar:nth-child(3) {
        animation-delay: 0.2s;
    }

    .loading-bar:nth-child(4) {
        animation-delay: 0.3s;
    }

    /* =========================
       TABLE STYLES - MODERN DESIGN
    ========================= */
    .table-container {
        width: 100%;
        padding: 0px;
        font-size: 12px;
        position: relative;
        overflow-x: auto;
        overflow-y: auto;
        max-height: 450px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Thin scrollbar */
    .table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #b163a6;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #df64ce;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-container {
        scrollbar-width: thin;
        scrollbar-color: #bbbee5 #f1f1f1;
    }

    /* Table styling */
    .tbl {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .thd {
        position: sticky;
        top: -1px;
        z-index: 10;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .thview {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        font-size: 11px;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .thview:last-child {
        border-right: none;
    }

    .tdview {
        padding: 10px 15px;
        text-align: left;
        font-size: 12px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        height: 50px;
        transition: background-color 0.2s ease;
    }

    .trview:hover .tdview {
        background-color: #f8fafc;
    }

    .trview:last-child .tdview {
        border-bottom: none;
    }

    /* Alternating row colors */
    .trview:nth-child(even) .tdview {
        background-color: #f9fafb;
    }

    .trview:nth-child(even):hover .tdview {
        background-color: #f1f5f9;
    }

    /* =========================
       STAT CARDS DESIGN
    ========================= */
    .stat-card {
        transition: all 0.3s ease-in-out;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px 10px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #6366f1;
    }

    .stat-card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 5px;
    }

    .stat-card p {
        font-size: 10px;
        font-weight: 600;
        color: #6b7280;
        margin: 0;
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

    /* =========================
       SEARCH & FILTER BADGES
    ========================= */
    .text-muted.f-12 {
        padding: 10px 0;
        font-size: 12px;
    }

    .cincome_view {
        color: #dc2626;
        font-size: 12px;
        font-weight: 600;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        margin-right: 5px;
    }

    .badge.bg-success {
        background-color: #10b981 !important;
    }

    .badge.bg-danger {
        background-color: #ef4444 !important;
        cursor: pointer;
    }

    .badge.bg-danger:hover {
        background-color: #dc2626 !important;
    }

    /* =========================
       PAGINATION
    ========================= */
    .footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        background: transparent;
        border-top: 1px solid #e5e7eb;
        font-size: 12px;
        width: 100%;
        margin-top: 15px;
    }

    .pagination {
        display: flex;
        gap: 4px;
    }

    .pagination button {
        background: white;
        border: 1px solid #d1d5db;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 11px;
        transition: all 0.2s ease;
    }

    .pagination button:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .pagination button.active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }

    #itemsPerPageSelect {
        padding: 4px 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 12px;
        background: white;
    }

    /* =========================
       DATE RANGE PICKER
    ========================= */
    #reportrange {
        background: white;
        cursor: pointer;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        width: 100%;
        font-size: 12px;
        transition: border-color 0.2s ease;
    }

    #reportrange:hover {
        border-color: #9ca3af;
    }

    /* =========================
       LOADING WAVE ANIMATION
    ========================= */
    .loading-wave {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        margin-right: -450%;
    }

    @keyframes loading-wave-animation {
        0% {
            height: 10px;
        }
        50% {
            height: 30px;
        }
        100% {
            height: 10px;
        }
    }

    /* =========================
       MISC STYLES
    ========================= */
    .btn-primary.d-inline-flex:hover {
        background-color: white !important;
        border-color: #4b4fc5 !important;
        color: #6a6ee4;
    }

    ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    ul li {
        border-radius: 3px;
        margin: 0;
    }

    ul li label {
        display: flex;
        flex-grow: 1;
        justify-content: space-between;
    }

    /* =========================
       CARD STYLES
    ========================= */
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* =========================
       TAB STYLES
    ========================= */
    .nav-tabs .nav-link {
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        border: none;
        background: transparent;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link.active {
        color: #6366f1;
        border-bottom: 2px solid #6366f1;
        background: transparent;
    }

    .nav-tabs .nav-link:hover {
        color: #4f46e5;
    }

    /* =========================
       RESPONSIVE DESIGN
    ========================= */
    @media only screen and (max-width: 768px) {
        .table-container {
            max-height: 400px;
        }

        .thview, .tdview {
            padding: 8px 10px;
            font-size: 11px;
        }

        .stat-card {
            padding: 12px 8px;
        }

        .stat-card h3 {
            font-size: 14px;
        }

        .footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .pagination {
            flex-wrap: wrap;
        }
    }

    @media only screen and (max-width: 480px) {
        .table-container {
            max-height: 350px;
        }

        .dropdown-actions {
            flex-direction: column;
        }

        .select-all, .deselect-all {
            width: 100%;
            margin-bottom: 5px;
        }
    }
/* tbody row hover effect */
tbody tr {
    transition: background-color 0.25s ease, transform 0.25s ease;
}

tbody tr:hover {
    background-color: #f4f6fb;   /* soft light background */
    transform: scale(1.01);      /* slight zoom */
    z-index: 1;
}
/* Stat Cards Container */
.row.g-4 {
    margin-bottom: 2rem;
}

/* Base Stat Card Styling */
.stat-card {
    position: relative;
    overflow: hidden;
    border: none !important;
    border-radius: 12px !important;
    padding: 1.2rem 1rem !important;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    min-height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}

/* Individual gradient colors matching your screenshot */
.col-md-1:nth-child(1) .stat-card,
.col-sm-3:nth-child(1) .stat-card {
    background: linear-gradient(135deg, #ffe4d6 0%, #ffc9a8 100%);
}

.col-md-1:nth-child(2) .stat-card,
.col-sm-3:nth-child(2) .stat-card {
    background: linear-gradient(135deg, #dcd6ff 0%, #b8a9ff 100%);
}

.col-md-1:nth-child(3) .stat-card,
.col-sm-3:nth-child(3) .stat-card {
    background: linear-gradient(135deg, #c8f4f3 0%, #9de5e3 100%);
}

.col-md-1:nth-child(4) .stat-card,
.col-sm-3:nth-child(4) .stat-card {
    background: linear-gradient(135deg, #b8f0e8 0%, #8de3d8 100%);
}

.col-md-1:nth-child(5) .stat-card,
.col-sm-3:nth-child(5) .stat-card {
    background: linear-gradient(135deg, #e3d6ff 0%, #c9b3ff 100%);
}

.col-md-1:nth-child(6) .stat-card,
.col-sm-3:nth-child(6) .stat-card {
    background: linear-gradient(135deg, #ffd6f0 0%, #ffb3e0 100%);
}

.col-md-1:nth-child(7) .stat-card,
.col-sm-3:nth-child(7) .stat-card {
    background: linear-gradient(135deg, #c9e9ff 0%, #a0d5ff 100%);
}

.col-md-1:nth-child(8) .stat-card,
.col-sm-3:nth-child(8) .stat-card {
    background: linear-gradient(135deg, #fff4c9 0%, #ffe590 100%);
}

/* Number Styling */
.stat-card h3 {
    color: #0e60b1;
    font-weight: 700 !important;
    font-size: 1.5rem !important;
    margin-bottom: 0.3rem !important;
    line-height: 1;
}

/* Label Styling */
.stat-card p {
    color: #0e60b1 !important;
    font-weight: 600 !important;
    font-size: 10px !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0 !important;
    line-height: 1.2;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stat-card {
        min-height: 80px;
        padding: 1rem 0.5rem !important;
    }

    .stat-card h3 {
        font-size: 1.3rem !important;
    }

    .stat-card p {
        font-size: 9px !important;
    }
}

@media (max-width: 576px) {
    .stat-card {
        min-height: 75px;
    }

    .stat-card h3 {
        font-size: 1.2rem !important;
    }
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
      <div class="page-header">
        <div class="page-block">
          <div class="row ">

            <div class="col-md-9 col-sm-9">
              <input type="text" id="icon-search" class="form-control mb-4"
              style="
    height: 35px;
    font-size: 11px;
"  placeholder="Search">
            </div>

          </div>

        </div>
      </div><br><br>

	  <div class="container" style="margin-top: -51px;">
                <div class="row g-4">
                    <!-- Row 1 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_cash">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total Cash</p>
                        </div>
                    </div>
                    <!-- Row 2 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_card">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total Card</p>
                        </div>
                    </div>
                    <!-- Row 3 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_cheque">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total Cheque</p>
                        </div>
                    </div>
                    <!-- Row 4 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_dd">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total DD</p>
                        </div>
                    </div>
                    <!-- Row 5 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_neft">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total Neft</p>
                        </div>
                    </div>
                    <!-- Row 6 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_credit">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total Credit</p>
                        </div>
                    </div>
                    <!-- Row 7 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_upi">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total UPI</p>
                        </div>
                    </div>
                    <!-- Row 8 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_amount">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px; color: #0e60b1 !important;">Total Amount</p>
                        </div>
                    </div>
                </div><br>
            </div>

        <!-- [ Main Content ] end -->
        <div class="row">
        <div class="col-xl-12 col-md-12" >

        <div class="card-body border-bottom pb-0">
                <div class="d-flex align-items-center justify-content-between">

                </div>
                <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">

                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link active"
                      id="analytics-tab-1"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-1-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-1-pane"
                      aria-selected="false"
                      >Income</button
                    >
                  </li>
                </ul>
              </div>

</div>
</div><br>

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">

                <div class="row">

<div class="col-xl-2 col-md-2">
<div class="card">

<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="dateviewsall"></span>

</div>
</div>
@php $zones = App\Models\TblZonesModel::select('name')->get(); @endphp
<div class="col-xl-3 col-md-3">
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

                        <div class="col-xl-3 col-md-3">
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
					<div class="col-xl-3 col-md-3">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_income.category" id="income_views" placeholder="Select Income" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch income_category"  id="income_type">
											<div data-value="">All</div>
											<div data-value="">O/P - Income</div>
											<div data-value="">I/P - Income</div>
											<div data-value="">Pharmacy - Income</div>
											<!--<div data-value="">Consolidated</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>


                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="dcounts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
<span class="cincome_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="cbranch_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="czone_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="income_search" class="badge bg-success value_views_mainsearch"></span>
<span  class="badge bg-danger clear_views" style="display:none;">Clear all</span>
</p><br>

        <div class="col-sm-12">

              <div class="card-body">
                <div class="table-container">
                    <table class="tbl">
                        <thead class="thd">
                            <tr class="trview">
                                <th class="thview"><i class="bi bi-hash"></i> S.NO</th>
                                <th class="thview"><i class="bi bi-geo-alt"></i> Location</th>
                                <th class="thview"><i class="bi bi-tag"></i> Type</th>
                                <th class="thview"><i class="bi bi-cash-coin"></i> Cash</th>
                                <th class="thview"><i class="bi bi-credit-card"></i> Card</th>
                                <th class="thview"><i class="bi bi-bank"></i> Cheque </th>
                                <th class="thview"><i class="bi bi-file-earmark-text"></i> DD </th>
                                <th class="thview"><i class="bi bi-arrow-left-right"></i> Neft</th>
                                <th class="thview"><i class="bi bi-wallet2"></i> Credit</th>
                                <th class="thview"><i class="bi bi-phone"></i> UPI</th>
                                <th class="thview"><i class="bi bi-calculator"></i> Total</th>
                            </tr>
                        </thead>

                        <tbody id="loader_row">
                            <tr>
                            <td colspan="11">
                                <div id="loader-container">
                                <div id="progress-bar">Loading: 0%</div>
                                <div id="error-message" style="color: red; display: none;"></div>
                                </div>
                            </td>
                            </tr>
                        </tbody>

                        <tbody id="daily_details" style="display:none;">

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

              </div>
            </div>
          </div>
          <!-- Column Rendering table end -->
          <!-- Multiple Table Control Elements start -->
          <!-- Row Created Callback table end -->

			<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
            <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('/assets/income/income-details.js') }}"></script>


<script type="text/javascript">

	const incomefetchUrl = "{{ route('superadmin.incomereportfetch') }}";
    const incomeBranchUrlfitter = "{{ route('superadmin.incomereportfilter') }}";
	const dateIncomeUrl = "{{ route('superadmin.incomedatefilter') }}";
	const incomeBranchfitter = "{{ route('superadmin.incomebranchfilter') }}";
    var filterTriggerTimer = null;
        // Set the initial start and end dates
        var start = moment();
        var end = moment();

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

        // Initialize the date range picker
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
                    if (!Array.isArray(values)) values = [];

                    if (values.includes(selectedValue)) {
                        values = values.filter(v => v !== selectedValue);
                        $(this).removeClass("selected");
                    } else {
                        values.push(selectedValue);
                        $(this).addClass("selected");
                    }

                    input.data("values", values);
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
