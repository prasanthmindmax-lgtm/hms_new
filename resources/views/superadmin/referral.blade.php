<!doctype html>
<html lang="en">
<!-- [Head] start -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<!-- [Head] end -->
<!-- [Body] Start -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
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
        max-height: 850px;
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
        padding: 15px;
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
    }

    .trview:last-child .tdview {
        border-bottom: none;
    }

    .trview:hover {
        background-color: #f1f1f1;
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
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .multiselect-input_views {
        width: 85%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .multiselect-options_views {

        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 85%;
        border: 1px solid #ccc;
        background: #fff;
        z-index: 10;
        max-height: 350px;
        overflow-y: auto;
        border-radius: 5px;
        z-index: 9999;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);

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

    .multiselect-container:focus-within .multiselect-options_views {
        display: block;
    }

    .multiselect-options label {
        display: block;
        padding: 8px 10px;
        cursor: pointer;
    }

    .multiselect-options_views label {
        display: block;
        padding: 8px 10px;
        cursor: pointer;
    }

    .multiselect-options label:hover {
        background: #f0f0f0;
    }
    .multiselect-options_views label:hover {
        background: #f0f0f0;
    }

    .multiselect-options input {
        margin-right: 10px;
    }

    .multiselect-options_views input {
        margin-right: 10px;
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
    .btn-primary.d-inline-flex:hover {
    background-color:rgb(255, 255, 255) !important; /* Change to your desired hover color */
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
}


.loading-wave {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    margin-right: -450%;
}

.loading-bar {
  width: 20px;
  height: 10px;
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

@keyframes loading-wave-animation {
  0% {
    height: 10px;
  }

  50% {
    height: 50px;
  }

  100% {
    height: 10px;
  }
}

.btn-primary.d-inline-flex:hover {
    background-color:rgb(255, 255, 255) !important; /* Change to your desired hover color */
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
}

 #dashboard_color {
      color: #ec008c;
    }



/* view design */

.placeholder-data {
    padding: 15px;
    color: #666;
    font-size: 14px;
    line-height: 1.6;
}

.placeholder-data p {
    margin-bottom: 8px;
}
/* Add Meeting/Patient Button Styles */
.add-meeting-btn, .add-patient-btn {
    margin: 10px 0; /* Reduced margin */
    padding: 5px 15px; /* Smaller padding (was 8px 25px) */
    font-size: 12px; /* Smaller font (was 14px) */
    border-radius: 50px; /* Maintain oval shape */
    background-color: #6a6ee4;
    color: white;
    border: none;
    cursor: pointer;
    transition: all 0.2s; /* Smoother hover */
    display: inline-flex;
    align-items: center;
    height: 28px; /* Fixed height for consistency */
}

/* Smaller plus icon */
.add-meeting-btn i, .add-patient-btn i {
    margin-right: 5px; /* Reduced spacing */
    font-size: 10px; /* Smaller icon (default is usually 14px) */
}

/* Optional hover effect */
.add-meeting-btn:hover, .add-patient-btn:hover {
    background-color: #5a5ed4;
    transform: scale(0.98); /* Slight press effect */
}
.patient-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

/* No Data Messages */
.no-meetings-message, .no-patients-message {
    padding: 20px;
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
    background-color: #f9f9f9;
    border-radius: 8px;
}

/* Modal Trigger Setup */
.modal-trigger {
    cursor: pointer;
}

.details-container{
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.section-box {
    background: #fff;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.section-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 12px;
    border-left: 4px solid #007bff;
    padding-left: 10px;
    color: #333;
}

.entry-card {
    background: #f9f9f9;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 10px 15px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 1.6;
}

.entry-card div {
    margin-bottom: 6px;
}

.no-data {
    color: #888;
    padding: 10px;
    text-align: center;
    font-style: italic;
}

/* Doctor Details Styles */
.doctor-container {
    padding: 10px;
    max-width: 100%;
}

.doctor-header {
    font-size: 14px;
    color: #333;
    margin-bottom: 10px;
}

.doctor-title {
    font-size: 14px;
    margin-bottom: 20px;
    color: #2c3e50;
}

.doctor-card {
    display: flex;
    padding: 5px;
    align-items: flex-start;
}

.doctor-card-left {
    margin-right: 20px;
}

.doctor-img {
    width: 300px;
    height: 150px;
    object-fit: cover;
        object-position: top;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.doctor-card-right {
    display: flex;
    gap: 30px;
}

.doctor-info-main {
    flex: 1;
}

.doctor-name {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
}

.doctor-specialization {
    margin: 4px 0;
    font-size: 14px;
    color: #7f8c8d;
}

.doctor-info-details {
    flex: 1;
}

.doctor-detail {
    margin: 2px 0;
    font-size: 14px;
    color: #34495e;
}

.doctor-detail strong {
    color: #2c3e50;
}

/* Meeting Details Styles */
.meeting-container {
    padding: 5px;
    border-radius: 10px;
    max-width: 100%;
}

.meeting-title {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
}

.meeting-subtitle {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 20px;
}

.meeting-timeline {
    display: flex;
    gap: 20px;
}

.meeting-timeline-left {
    width: 300px;
    text-align: center;
}
.meeting-img{
    height:150px;
}

.meeting-img {
    width: 100%;
    border-radius: 10px;
    margin-bottom: 5px;
    border: 1px solid #ddd;
}

.meeting-date {
    font-size: 13px;
    color: #555;
}

.meeting-timeline-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.meeting-entry {
    border-bottom: 1px solid #eaeaea;
    padding-bottom: 15px;
    display: flex;
    gap: 30px;
        flex-direction: column;
}

.meeting-entry:last-child {
    border-bottom: none;
}

.meeting-entry-header {
    width: 120px;
    display: flex;
    flex-direction: column;
}

.meeting-entry-date {
    font-size: 14px;
    color: #2c3e50;
    font-weight: bold;
}

.meeting-entry-time {
    font-size: 13px;
    color: #7f8c8d;
}

.meeting-entry-type {
    font-size: 12px;
    color: #95a5a6;
}

.meeting-entry-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.meeting-entry-name {
    font-size: 14px;
    color: #2c3e50;
    font-weight: bold;
    margin-bottom: 3px;
}

.meeting-entry-feedback {
    font-size: 13px;
    color: #34495e;
}

/* Patient Details Styles */
.patient-container {
    padding: 5px;
    border-radius: 10px;
    max-width: 100%;
}

.patient-title {
    margin-bottom: 15px;
    font-size: 18px;
    color: #2c3e50;
}

.patient-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.patient-table-header {
    background-color: #ecf0f1;
}

.patient-th {
    border: 1px solid #ddd;
    padding: 6px;
    text-align: left;
    font-weight: bold;
    color: #2c3e50;
}

.patient-td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    color: #34495e;
    vertical-align: top;
}

.patient-table-row:nth-child(even) {
    background-color: #f5f5f5;
}

.patient-table-row:hover {
    background-color: #eaf2f8;
}

/* No Data Styles */
.doctor-no-data,
.meeting-no-data,
.patient-no-data {
    padding: 20px;
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
    background-color: #f9f9f9;
    border-radius: 8px;
    margin: 20px 0;
}



/* vasanth */
.detail-card {
    background: #f4f8fc;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 15px;
    border-left: 4px solid #1976d2;
    font-family: 'Segoe UI', sans-serif;
}

.detail-line {
    margin-bottom: 8px;
    font-size: 13px; /* smaller size */
    color: #333;
}

.detail-line .label {
    font-weight: 600;
    color: #000;
    display: inline-block;
    min-width: 140px;
    font-size: 13px; /* match smaller size */
}

.meeting-details-container h6,
.patient-details-container h6 {
    font-size: 14px; /* smaller heading */
    font-weight: 600;
    color: #1976d2;
    margin-bottom: 10px;
}

</style>

<!-- 
<style>
    .recent-filters {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    
    .filter-group {
        margin-bottom: 25px;
    }
    
    .filter-group h3 {
        font-size: 16px;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }
    
    .filter-option {
        display: block;
        margin-bottom: 10px;
        cursor: pointer;
    }
    
    .filter-option input {
        margin-right: 10px;
    }
    
    .date-range-selector {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-bottom: 20px;
    }
    
    .calendar-container {
        display: flex;
        justify-content: space-between;
    }
    
    .month-calendar {
        width: 48%;
    }
    
    .month-header {
        text-align: center;
        font-weight: bold;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .month-title {
        flex: 1;
    }
    
    .month-nav-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 0 8px;
    }
    
    .week-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        margin-bottom: 10px;
        font-size: 12px;
        color: #666;
    }
    
    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }
    
    .day {
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
    }
    
    .day:hover {
        background: #f0f0f0;
    }
    
    .day.selected {
        background: #4CAF50;
        color: white;
    }
    
    .day.in-range {
        background: #a5d6a7;
        position: relative;
    }
    
    .day.in-range:not(.selected):before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: -3px;
        right: -3px;
        background: #c8e6c9;
        z-index: -1;
    }
    
    .day.other-month {
        color: #ccc;
    }
    
 
    
    .compare-label {
        font-weight: bold;
        margin-right: 15px;
        font-size: 16px;
    }
    
    .compare-fields {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .date-display {
        padding: 8px 12px;
        background: #f5f5f5;
        border-radius: 4px;
        min-width: 150px;
        text-align: center;
    }
</style> -->

<!-- 21-07-2025 -->
<!--  
<style>
    .recent-filters {
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .filter-group {
        margin-bottom: 15px;
    }
    
    .filter-group h3 {
        color: #333;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    
    .filter-option {
        display: block;
        margin-bottom: 8px;
        cursor: pointer;
    }
    
    .filter-option input {
        margin-right: 8px;
    }
    
    .date-range-selector {
        margin-bottom: 10px;
    }
    
    .month-calendar {
        width: 48%;
    }
    
    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    
    .day {
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        cursor: pointer;
    }
    
    .day:hover {
        background: #f0f0f0;
    }
    
    .day.selected {
        background: #4CAF50;
        color: white;
    }
    
    .day.in-range {
        background: #a5d6a7;
        position: relative;
    }
    
    .day.in-range:not(.selected):before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: -2px;
        right: -2px;
        background: #c8e6c9;
        z-index: -1;
    }
    
    .day.other-month {
        color: #ccc;
    }
    
    #calendarFilterDropdown {
        width: 600px;
    }
    
    .dropdown-content {
        max-height: 500px;
        overflow-y: auto;
        padding: 10px;
    }
</style> -->


<style>
    /* Apply sans-serif font to all elements with !important */
    #calendarFilterDropdown, 
    #calendarFilterDropdown * {
        font-family: sans-serif !important;
    }

    .recent-filters {
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 8px !important; /* Reduced space */
        padding: 6px !important; /* Reduced padding */
    }
    
    .filter-group {
        margin-bottom: 8px !important; /* Reduced space */
    }
    
    .filter-group h3 {
        color: #333;
        margin-bottom: 6px !important; /* Reduced space */
        padding-bottom: 2px !important; /* Reduced space */
        border-bottom: 1px solid #e0e0e0;
        font-size: 13px;
        font-weight: 600;
    }
    
    .filter-option {
        display: block;
        margin-bottom: 4px !important; /* Reduced space */
        cursor: pointer;
        font-size: 12px;
        color: #444;
    }
    
    .filter-option input {
        margin-right: 6px;
    }
    
    .date-range-selector {
        margin-bottom: 6px !important; /* Reduced space */
    }
    
    .month-calendar {
        width: 48%;
    }
    
    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    
    .day {
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        cursor: pointer;
        font-size: 11px;
        color: #333;
    }
    
    .day:hover {
        background: #7d81ea;
    }
    
    .day.selected {
        background: #7d81ea;
        color: white;
    }
    
    .day.in-range {
        background: #7d81ea;
    }
    
    .day.other-month {
        color: #aaa;
    }
    
    #calendarFilterDropdown {
        width: 580px;
        border: 1px solid #ddd;
        box-shadow: none;
        overflow: hidden !important; /* Remove scroll with !important */
    }
    
    .dropdown-content {
        padding: 6px !important; /* Reduced padding */
        overflow: hidden !important; /* Remove scroll with !important */
    }
    
    /* Month navigation buttons */
    .month-nav-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        color: #333;
        outline: none;
    }
    
    .month-nav-btn:hover {
        color: #000;
    }
    

      .meeting-month-nav-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        color: #333;
        outline: none;
    }
    
    .meeting-month-nav-btn {
        color: #000;
    }
    .month-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 500;
        color: #333;
    }
    
    .week-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-weight: 500;
        margin-bottom: 4px;
        font-size: 10px;
        color: #555;
    }
/* Updated Apply Filter button - rounded like close button */
#applyDateFilter {
    background-color: #6b6fe5 !important;
    color: white !important;
    border: none !important;
    padding: 6px 16px !important;
    border-radius: 16px !important; /* More rounded corners */
    font-size: 12px !important;
    cursor: pointer;
    margin-left: 8px !important;
    height: auto !important; /* Remove fixed height */
    line-height: normal !important;
    min-width: 80px !important; /* Minimum width */
    transition: all 0.2s ease !important;
    box-shadow: none !important;
    text-transform: none !important;
}

#applyDateFilter:hover {
    background-color: #5d60d0 !important;
    transform: translateY(-1px) !important;
}

#applyDateFilter:active {
    transform: translateY(0) !important;
}



/* Updated Apply Filter button - rounded like close button */
#applyMeetingDateFilter {
    background-color: #6b6fe5 !important;
    color: white !important;
    border: none !important;
    padding: 6px 16px !important;
    border-radius: 16px !important; /* More rounded corners */
    font-size: 12px !important;
    cursor: pointer;
    margin-left: 8px !important;
    height: auto !important; /* Remove fixed height */
    line-height: normal !important;
    min-width: 80px !important; /* Minimum width */
    transition: all 0.2s ease !important;
    box-shadow: none !important;
    text-transform: none !important;
}

#applyMeetingDateFilter:hover {
    background-color: #5d60d0 !important;
    transform: translateY(-1px) !important;
}

#applyMeetingDateFilter:active {
    transform: translateY(0) !important;
    
}

</style>

<!-- patient calender filter -->
<!-- 
<style>
    .patient-recent-filters {
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .patient-filter-group {
        margin-bottom: 15px;
    }
    
    .patient-filter-group h3 {
        color: #333;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    
    .patient-filter-option {
        display: block;
        margin-bottom: 8px;
        cursor: pointer;
    }
    
    .patient-filter-option input {
        margin-right: 8px;
    }
    
    .patient-date-range-selector {
        margin-bottom: 10px;
    }
    
    .patient-month-calendar {
        width: 48%;
    }
    
    .patient-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    
    .patient-day {
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        cursor: pointer;
    }
    
    .patient-day:hover {
        background: #f0f0f0;
    }
    
    .patient-day.selected {
        background: #4CAF50;
        color: white;
    }
    
    .patient-day.in-range {
        background: #a5d6a7;
        position: relative;
    }
    
    .patient-day.in-range:not(.selected):before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: -2px;
        right: -2px;
        background: #c8e6c9;
        z-index: -1;
    }
    
    .patient-day.other-month {
        color: #ccc;
    }
    
    #patientCalendarFilterDropdown {
        width: 600px;
    }
    
    .patient-dropdown-content {
        max-height: 500px;
        overflow-y: auto;
        padding: 10px;
    }
</style> -->

<style>
    /* Apply sans-serif font to all elements with !important */
    #patientCalendarFilterDropdown, 
    #patientCalendarFilterDropdown * {
        font-family: sans-serif !important;
    }

    .patient-recent-filters {
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 8px !important;
        padding: 6px !important;
    }
    
    .patient-filter-group {
        margin-bottom: 8px !important;
    }
    
    .patient-filter-group h3 {
        color: #333;
        margin-bottom: 6px !important;
        padding-bottom: 2px !important;
        border-bottom: 1px solid #e0e0e0;
        font-size: 13px;
        font-weight: 600;
    }
    
    .patient-filter-option {
        display: block;
        margin-bottom: 4px !important;
        cursor: pointer;
        font-size: 12px;
        color: #444;
    }
    
    .patient-filter-option input {
        margin-right: 6px;
    }
    
    .patient-date-range-selector {
        margin-bottom: 6px !important;
    }
    
    .patient-month-calendar {
        width: 48%;
    }
    
    .patient-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    
    .patient-day {
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        cursor: pointer;
        font-size: 11px;
        color: #333;
    }
    
    .patient-day:hover {
        background: #7d81ea;
    }
    
    .patient-day.selected {
        background: #7d81ea;
        color: white;
    }
    
    .patient-day.in-range {
        background: #7d81ea;
    }
    
    .patient-day.other-month {
        color: #aaa;
    }

    .patient-month-title {
        margin-top: 10px;
    }
    
    #patientCalendarFilterDropdown {
        width: 580px;
        border: 1px solid #ddd;
        box-shadow: none;
        overflow: hidden !important;
    }
    
    .patient-dropdown-content {
        padding: 6px !important;
        overflow: hidden !important;
    }
    
    /* Month navigation buttons */
    .patient-month-nav-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        color: #333;
        outline: none;
    }
    
    .patient-month-nav-btn:hover {
        color: #000;
    }
    
    .patient-month-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 500;
        color: #333;
    }
    
    .patient-week-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-weight: 500;
        margin-bottom: 4px;
        font-size: 10px;
        color: #555;
    }

    /* Updated Apply Filter button - rounded like close button */
    #patientApplyDateFilter {
        background-color: #7d81ea !important;
        color: white !important;
        border: none !important;
        padding: 6px 16px !important;
        border-radius: 16px !important;
        font-size: 12px !important;
        cursor: pointer;
        margin-left: 8px !important;
        height: auto !important;
        line-height: normal !important;
        min-width: 80px !important;
        transition: all 0.2s ease !important;
        box-shadow: none !important;
        text-transform: none !important;
    }

    #patientApplyDateFilter:hover {
        background-color: #7d81ea !important;
        transform: translateY(-1px) !important;
    }

    #patientApplyDateFilter:active {
        transform: translateY(0) !important;
    }
     #map {
            height: 100vh;
            width: 100%;
        }
        #controls {
            position: absolute;
            top: 10px;
            /* left: 10px; */
            right: 0px;
            background: white;
            padding: 10px;
            z-index: 5;
        }



</style>




<style>
    .timeline-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        overflow: hidden;
    }
    .timeline-header {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        font-weight: 600;
    }
    .timeline-subheader {
        padding: 10px 16px;
        background: #f9f9f9;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #eee;
    }
    .timeline-date {
        font-size: 13px;
        color: gray;
    }
    .activity-switch {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 500;
    }
    .timeline-events {
        padding: 12px 16px;
    }
    .event-item {
        position: relative;
        padding-left: 25px;
        margin-bottom: 15px;
    }
    .event-item:last-child {
        margin-bottom: 0;
    }
    .event-item::before {
        content: "";
        position: absolute;
        left: 21px;
        top: 22px;
        width: 2px;
        height: 80%;
        background: #ddd;
    }
    .event-dot {
        width: 14px;
        height: 14px;
        background: #fff;
        border: 2px solid #ccc;
        border-radius: 50%;
        position: absolute;
        left: 0;
        top: 3px;
        z-index: 1;
    }
    .event-time {
        font-size: 13px;
        font-weight: 600;
    position: absolute;
    padding: 4px 15px;
    left: -17px;
    }
    .event-title {
        font-size: 13px;
        margin: 0;
    }
    .event-location {
        font-size: 12px;
        color: gray;
    }
    .event-badge {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
        background: #28a745;
        color: #fff;
        margin-left: 5px;
    }
    .text-green {
        color: green;
    }

    .evn-two{
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
            padding: 15px;
    width: 80%;
    position: relative;
    right: -90px;
    }
</style>


<!-- patient filter end -->


<!-- 
<style>
    .recent-filters {
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .filter-group {
        margin-bottom: 15px;
    }
    
    .filter-group h3 {
        color: #333;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    
    .filter-option {
        display: block;
        margin-bottom: 8px;
        cursor: pointer;
    }
    
    .filter-option input {
        margin-right: 8px;
    }
    
    .date-range-selector {
        margin-bottom: 10px;
    }
    
    .month-calendar {
        width: 48%;
    }
    
    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    
    .day {
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        cursor: pointer;
    }
    
    .day:hover {
        background: #7d81ea;
    }
    
    .day.selected {
        background: #7d81ea;
        color: white;
    }
    
    .day.in-range {
        background: #7d81ea;
        position: relative;
    }
    
     .month-title {
    margin-top: 10px; 
}
    
    .day.in-range:not(.selected):before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: -2px;
        right: -2px;
        background: #c8e6c9;
        z-index: -1;
    }
    
    .day.other-month {
        color: #ccc;
    }
    
    #meetingCalendarFilterDropdown {
        width: 600px;
    }
    
    .dropdown-content {
        max-height: 500px;
        overflow-y: auto;
        padding: 10px;
    }
    
    .meeting-indicator {
        width: 4px;
        height: 4px;
        background-color: #4285f4;
        border-radius: 50%;
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
    }
</style> -->

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
                                    " placeholder="Search">
                        </div>
                        <div class="col-md-3 col-sm-3 add-doctors">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
                                height: 34px;
                                width: 133px;
                                font-size: 12px;
                                        background-color: #6a6ee4;
                                        --bs-btn-border-color: #6a6ee4;
                            "><i class="ti ti-plus f-18"></i>Add Doctor</a>
                        </div>
                        <div class="col-md-3 col-sm-3 add-meeting">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 addmeeting" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
                                        height: 34px;
                                        width: 133px;
                                        font-size: 12px;
                                            background-color: #6a6ee4;
                                        --bs-btn-border-color: #6a6ee4;
                                    "><i class="ti ti-plus f-18"></i>Meeting</a>
                        </div>
                        <div class="col-md-3 col-sm-3 add-patient">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 addpatient" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
                                height: 34px;
                                width: 133px;
                                font-size: 12px;
                                    background-color: #6a6ee4;
                                --bs-btn-border-color: #6a6ee4;
                            "><i class="ti ti-plus f-18"></i>Add Patient</a>
                                                    </div>
                    </div>
                </div>
            </div><br><br>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="container" style="margin-top: -51px;">
                <div class="row g-4">
                    <!-- Row 1 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="today_visits">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;"> Doctors/VHN</p>
                        </div>
                    </div>
                    <!-- Row 2 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_meeting">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;"> Meetings</p>
                        </div>
                    </div>
                    <!-- Row 3 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="patient_totals">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Patients</p>
                        </div>
                    </div>
                </div><br>
            </div>
            <!-- [ Main Content ] end -->






  <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                {{-- <img id="popupImage" src="" class="img-fluid" alt="Preview"> --}}
                <div class="container">
                    <div class="row">
                        {{-- <div class="col-lg-6">
                          <div class="timeline-container">
                            <div class="timeline-header">
                                <span>Bharathiraja R - Tiruppur</span>
                                <div class="activity-switch">
                                    <span>Activity</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-subheader">
                                Timeline
                            </div>

                            <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                                <span>02 Jul 2025 <small class="text-muted">Asia/Kolkata</small></span>
                                <span class="text-muted">0.44 km • 10h 26m</span>
                            </div>

                            <div class="timeline-events">
                                <div class="event-item">

                                    <div class="event-time">09:32 AM</div>
                                    <div class="evn-two">
                                    <p class="event-title">Tracking Started</p>
                                    </div>
                                </div>

                                <div class="event-item">

                                    <div class="event-time">09:32 AM</div>
                                    <div class="evn-two">
                                    <p class="event-title">Drive <span class="text-muted">0.44 km • 14m</span></p>
                                    </div>
                                </div>

                                <div class="event-item">
                                    <div class="event-time">09:32 AM</div>
                                    <div class="evn-two">
                                    <p class="event-title">Stop <span class="text-muted">10h 12m</span></p>
                                    <p class="event-location">Konappana Agrahara, Electronics City Phase 1, Bangalore South, Bengaluru Urban, Karnataka, 560100, India</p>
                                </div>
                                </div>

                                <div class="event-item">
                                    <div class="event-time text-green">07:58 PM</div>
                                    <div class="evn-two">
                                    <p class="event-title">Geotag <span class="event-badge">Punch Out</span></p>
                                    <p class="event-location">Konappana Agrahara, Electronics City Phase 1, Bangalore South, Bengaluru Urban, Karnataka, 560100, India</p>
                                </div>
                                </div>

                                <div class="event-item">

                                    <div class="event-time">07:58 PM</div>
                                    <div class="evn-two">
                                    <p class="event-title">Tracking Stopped</p>
                                </div>
                                </div>
                            </div>

                         </div>

                        </div> --}}


                        <div class="col-lg-12">
                            <div id="controls">
                                <button onclick="startReplay()">▶️ Play Route</button>
                            </div>
                            <div id="map" style="height:500px;"></div>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
</div>









            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card-body border-bottom pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                        <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab" data-bs-target="#analytics-tab-1-pane" type="button"
                                    role="tab"
                                    aria-controls="analytics-tab-1-pane"
                                    aria-selected="true">Doctors/VHN</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="analytics-tab-2"
                                    data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-2-pane"
                                    type="button"
                                    role="tab"
                                    aria-controls="analytics-tab-2-pane"
                                    aria-selected="false">Meetings</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="analytics-tab-3"
                                    data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-3-pane"
                                    type="button"
                                    role="tab"
                                    aria-controls="analytics-tab-3-pane"
                                    aria-selected="false">Patients</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><br>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">
                    <div class="row">
                        <!-- <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                                </div>
                                <span style="display:none;" id="dateviewsall"></span>
                            </div>
                        </div> -->

<!-- 
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div id="dateFilterTrigger" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%; font-size: 10px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <i class="fa fa-calendar"></i>&nbsp;
                <span id="selectedDateRange">Select Date</span>
            </div>
            <i class="fa fa-caret-down"></i>
        </div>
        <span style="display:none;" id="dateviewsall"></span>
    </div>
</div>

<div class="modal fade" id="calendarFilterModal" tabindex="-1" role="dialog" aria-labelledby="calendarFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calendarFilterModalLabel">Date Range Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="recent-filters">
                            <h3>Recently Used</h3>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="today"> Today
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="yesterday"> Yesterday
                            </label>
                        </div>
                        
                        <div class="filter-group">
                            <h3>Quick Select</h3>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="today" checked> Today
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="yesterday"> Yesterday
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="today_yesterday"> Today and Yesterday
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="last_7_days"> Last 7 Days
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="last_14_days"> Last 14 Days
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="last_28_days"> Last 28 Days
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="last_30_days"> Last 30 Days
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="this_week"> This Week
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="last_week"> Last Week
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="this_month"> This Month
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="last_month"> Last Month
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="maximum"> Maximum
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="filter" value="custom"> Custom
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="date-range-selector">
                            <select id="dateRangeSelect" class="form-control form-control-sm">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="today_yesterday">Today and Yesterday</option>
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="last_14_days">Last 14 Days</option>
                                <option value="last_28_days">Last 28 Days</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="maximum">Maximum</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        
                        <div class="calendar-container">
                            <div class="month-calendar">
                                <div class="month-header">
                                    <button class="month-nav-btn prev-month">&lt;</button>
                                    <div class="month-title" id="currentMonthHeader">June 2025</div>
                                    <button class="month-nav-btn next-month">&gt;</button>
                                </div>
                                <div class="week-days">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="days-grid" id="currentMonth"></div>
                            </div>
                            
                            <div class="month-calendar">
                                <div class="month-header">
                                    <button class="month-nav-btn prev-month">&lt;</button>
                                    <div class="month-title" id="nextMonthHeader">July 2025</div>
                                    <button class="month-nav-btn next-month">&gt;</button>
                                </div>
                                <div class="week-days">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="days-grid" id="nextMonth"></div>
                            </div>
                        </div>
                        
                        <div class="compare-section">
                            <div class="compare-label">Compare</div>
                            <div class="compare-fields">
                                <select id="filterSelect" class="form-control form-control-sm">
                                    <option value="last_7_days">Last 7 Days</option>
                                    <option value="last_14_days">Last 14 Days</option>
                                    <option value="last_28_days">Last 28 Days</option>
                                    <option value="last_30_days">Last 30 Days</option>
                                    <option value="custom">Custom</option>
                                </select>
                                <div class="date-display form-control form-control-sm" id="startDateDisplay">June 20, 2025</div>
                                <div class="date-display form-control form-control-sm" id="endDateDisplay">June 26, 2025</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="datemodalcancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyDateFilter">Apply Filter</button>
            </div>
        </div>
    </div>
</div> -->
<!-- calender filter -->


<div class="col-xl-2 col-md-2">
    <div class="card">
        <div id="dateFilterTrigger" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%; font-size: 10px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <i class="fa fa-calendar"></i>&nbsp;
                <span id="selectedDateRange">Select Date</span>
            </div>
            <i class="fa fa-caret-down"></i>
        </div>

        <span style="display:none;" id="dateviewsall"></span>
        
        <div id="calendarFilterDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ccc; box-shadow: 0 2px 10px rgba(0,0,0,0.2); width: 600px; margin-top: 5px;">
            <div class="dropdown-content" style="padding: 10px; max-height: 500px; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-3" style="padding-right: 0;">
                        <div class="recent-filters" style="padding: 10px;">
                            <h3 style="font-size: 14px; margin-bottom: 10px;">Recently Used</h3>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="today"> Today
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="yesterday"> Yesterday
                            </label>
                        </div>
                        
                        <div class="filter-group" style="padding: 10px;">
                            <h3 style="font-size: 14px; margin-bottom: 10px;">Quick Select</h3>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="today" checked> Today
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="yesterday"> Yesterday
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="today_yesterday"> Today and Yesterday
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="last_7_days"> Last 7 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="last_14_days"> Last 14 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="last_28_days"> Last 28 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="last_30_days"> Last 30 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="this_week"> This Week
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="last_week"> Last Week
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="this_month"> This Month
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="last_month"> Last Month
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="maximum"> Maximum
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="filter" value="custom"> Custom
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-9" style="padding-left: 10px;">
                        <div class="date-range-selector" style="margin-bottom: 10px;">
                            <select id="dateRangeSelect" class="form-control form-control-sm" style="font-size: 12px; height: 30px;">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="today_yesterday">Today and Yesterday</option>
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="last_14_days">Last 14 Days</option>
                                <option value="last_28_days">Last 28 Days</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="maximum">Maximum</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        
                        <div class="calendar-container" style="display: flex; margin-bottom: 10px;">
                            <div class="month-calendar" style="margin-right: 10px; width: 48%;">
                                <div class="month-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 12px;">
                                    <button class="month-nav-btn prev-month" style="padding: 0 5px; font-size: 12px;">&lt;</button>
                                    <div class="month-title" id="currentMonthHeader" style="font-size: 12px;">June 2025</div>
                                    <button class="month-nav-btn next-month" style="padding: 0 5px; font-size: 12px;">&gt;</button>
                                </div>
                                <div class="week-days" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 10px;">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="days-grid" id="currentMonth" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 11px;"></div>
                            </div>
                            
                            <div class="month-calendar" style="width: 48%;">
                                <div class="month-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 12px;">
                                    <button class="month-nav-btn prev-month" style="padding: 0 5px; font-size: 12px;">&lt;</button>
                                    <div class="month-title" id="nextMonthHeader" style="font-size: 12px;">July 2025</div>
                                    <button class="month-nav-btn next-month" style="padding: 0 5px; font-size: 12px;">&gt;</button>
                                </div>
                                <div class="week-days" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 10px;">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="days-grid" id="nextMonth" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 11px;"></div>
                            </div>
                        </div>
                        
                        <div class="compare-section" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <div class="compare-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Compare</div>
                            <div class="compare-fields" style="display: flex; gap: 5px; margin-top: 5px;">
                                <select id="filterSelect" class="form-control form-control-sm" style="flex: 1; font-size: 12px; height: 30px;">
                                    <option value="last_7_days">Last 7 Days</option>
                                    <option value="last_14_days">Last 14 Days</option>
                                    <option value="last_28_days">Last 28 Days</option>
                                    <option value="last_30_days">Last 30 Days</option>
                                    <option value="custom">Custom</option>
                                </select>
                                <div class="date-display form-control form-control-sm" id="startDateDisplay" style="flex: 1; font-size: 12px; height: 30px; line-height: 30px; padding: 0 5px;">June 20, 2025</div>
                                <div class="date-display form-control form-control-sm" id="endDateDisplay" style="flex: 1; font-size: 12px; height: 30px; line-height: 30px; padding: 0 5px;">June 26, 2025</div>
                            </div>
                        </div>
                        
                        <!-- Buttons moved right after compare section -->
                        <div style="display: flex; justify-content: flex-end; margin-top: 10px; gap: 5px;">
                            <button type="button" class="btn btn-secondary" id="datemodalcancel" style="padding: 3px 8px; font-size: 12px;">Cancel</button>
                            <button type="button" class="btn btn-primary" id="applyDateFilter" style="padding: 3px 8px; font-size: 12px;">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    use App\Models\TblZonesModel;
    use App\Models\TblLocationModel;

   
    $locations = null;
    $zones = null;
    $showFilters = true;

    if ($admin->access_limits == 1) {
        // Access limit 1 (Superadmin) → All zones and locations
        $zones = TblZonesModel::select('name', 'id')->get();
        $locations = TblLocationModel::select('name', 'id', 'zone_id')->get();

    } elseif ($admin->access_limits == 2) {
        // Access limit 2 (Zonal Admin) → User zone only + multi-locations under that zone
        $zoneIds = [];

        // Check if multi_location exists and is not empty
        if (!empty($admin->multi_location)) {
            // Parse comma-separated multi_location string to array
            $multiLocations = explode(',', $admin->multi_location);

            // Get zone IDs from multi-locations first
            $locationsFromMulti = TblLocationModel::whereIn('id', $multiLocations)
                ->pluck('zone_id')
                ->unique()
                ->toArray();
            
            // Merge with user's primary zone_id
            $zoneIds = array_unique(array_merge([$admin->zone_id], $locationsFromMulti));

            // Get all locations under user's primary zone
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->where('zone_id', $admin->zone_id)
                ->get();

            // Also include the multi-locations specifically (in case they're from different zones)
            $specificLocations = TblLocationModel::select('name', 'id', 'zone_id')
                ->whereIn('id', $multiLocations)
                ->get();

            // Merge collections and remove duplicates
            $locations = $locations->merge($specificLocations)->unique('id');
        } else {
            // If no multi_location, get locations from user's zone only
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->where('zone_id', $admin->zone_id)
                ->get();
            $zoneIds = [$admin->zone_id];
        }

        // Get zones based on collected zone IDs
        $zones = TblZonesModel::select('name', 'id')
            ->whereIn('id', $zoneIds)
            ->get();

    } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
        // Access limit 3 (Admin) → User branch only + multi-locations
        $branchIds = [];

        // Always include the primary branch_id
        $branchIds[] = $admin->branch_id;

        // Check if multi_location exists and is not empty
        if (!empty($admin->multi_location)) {
            // Parse comma-separated multi_location string to array
            $multiLocations = explode(',', $admin->multi_location);

            // Add multi-locations to branch IDs
            $branchIds = array_merge($branchIds, $multiLocations);

            // Get all specific locations
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->whereIn('id', $branchIds)
                ->get();
        } else {
            // If no multi_location, get only the user's branch
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->where('id', $admin->branch_id)
                ->get();
        }
        
        // Get zones based on locations
        $zoneIds = $locations->pluck('zone_id')->unique()->toArray();
        $zones = TblZonesModel::select('name', 'id')
            ->whereIn('id', $zoneIds)
            ->get();

    } elseif ($admin->access_limits == 4) {
        // Access limit 4 (Auditor) → All zones and locations
        $zones = TblZonesModel::select('name', 'id')->get();
        $locations = TblLocationModel::select('name', 'id', 'zone_id')->get();

    } 
@endphp

@if($showFilters)
    <!-- Zone Filter -->
    <div class="col-xl-2 col-md-2">
        <div class="card">
            <div class="dropdown">
                <input type="text" class="searchInput marketervalues_search" name="zone_name" id="zoneviews" placeholder="Select Zone">
                <input type="hidden" id="camp_zone_id">
                <div class="dropdown-options options_marketers selectzone_camp">
                    @if($zones->isNotEmpty())
                        @foreach($zones as $zonename)
                            <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                        @endforeach
                    @else
                        <div>No zones available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>




    <!-- Branch Filter -->
    <div class="col-xl-2 col-md-2">
        <div class="card">
            <div class="dropdown">
                <input type="text" class="searchInput marketervalues_search" name="branch_name" id="branchviews" placeholder="Select Branch">
                <div class="dropdown-options options_marketers" id="getlocation_camp">
                    @if($locations->isNotEmpty())
                        @foreach($locations as $location)
                            <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">{{$location->name}}</div>
                        @endforeach
                    @else
                        <div>No branches available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

   <!-- Zonal head Filter -->
@if($admin->access_limits == 1) 
    <div class="col-xl-2 col-md-2">
        <div class="card">
            <div class="dropdown">
                <input type="text" class="searchInput single_search marketervalues_search" name="zonal_head" id="zonalHeadFilter" placeholder="Select Zonal Head">
                <div class="dropdown-options single_search options_marketers" id="zonalHeadOptions">
                    <!-- @php
                            $zonalHeads = DB::table('users')
                                ->where('zonal_head', '1')
                                ->select('user_fullname', 'id')
                                ->get();

                    @endphp
                    @if($zonalHeads!="")
                        @foreach($zonalHeads as $zonalHead)
                            <div data-value="{{$zonalHead->id}}">{{$zonalHead->user_fullname}}</div>
                        @endforeach
                    @else
                        <div>No zonal heads available</div>
                    @endif -->
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Marketer Filter -->
 @if($admin->access_limits != 3) 
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search" name='userfullname' id="marketer_fetch" placeholder="Select Marketer">
            <div class="dropdown-options single_search options_marketers" id="marketerOptions">
                <!-- Marketers will be loaded dynamically via AJAX based on zone selection -->
                <div>Select a zone first</div>
            </div>
        </div>
    </div>
</div>

@endif


                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                            <div class="dropdown">
                            <input type="text" class="searchInput single_search marketervalues_search" name='special' id="special" placeholder="B2B Type">
                            <div class="dropdown-options single_search options_marketers">
                                <div value="ALLOPATHY DR">ALLOPATHY DR</div>
                                <div value="VHN">VHN</div>
                                <div value="ALLOPATHY HOSPITAL">ALLOPATHY HOSPITAL</div>
                                <div value="ALLOPATHY CLINIC">ALLOPATHY CLINIC</div>
                                <div value="AYUSH CLINIC">AYUSH CLINIC</div>
                                <div value="AYUSH DR">AYUSH DR</div>
                                <div value="AGENT">AGENT</div>
                                <div value="Others">Others</div>

                            </div>
                            </div>
                            </div>
                        </div>



                        <div class="col-xl-2 col-md-2">
                            <div class="">
                                <!-- <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter" style="height: 34px;width: 133px;font-size: 12px;        background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More Filters</a> -->
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>

                    </div>
                    <p style="margin-top: -9px;" class="text-muted f-12 mb-0">
                        <span class="text-truncate w-100"><span id="counts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
                        <span class="search_view" style="color:rgb(16 35 255);font-size: 12px;font-weight: unset;cursor: pointer;">Search</span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;display:none;" class="badge bg-danger clear_all_views">Clear all</span>
                        <span style="cursor: pointer;" class="badge bg-success value_edit" style="display:none;"></span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">Doctor ID</th>
                                            <th class="thview">Doctor Details</th>
                                            <th class="thview">Clinic/Hospital</th>
                                            <th class="thview">Location</th>
                                            <th class="thview">Contacts</th>
                                            <th class="thview">Branch</th>
                                            <th class="thview">Marketer</th>
                                            <th class="thview">View</th>
                                            <!-- <th class="thview">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="doctor_details1">
                                    <tbody id="doctor_details">
                                        <tr>
                                            <td >
                                            <div class="loading-wave">
  <div class="loading-bar"></div>
  <div class="loading-bar"></div>
  <div class="loading-bar"></div>
  <div class="loading-bar"></div>
</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelect">

                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">
                    <div class="col-xl-12 col-md-12">
                        <div class="row">


                            <!-- <div class="col-xl-2 col-md-2">
                                <div class="card">
                                    <div id="reportrange1" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down "></i>
                                    </div>
                                    <span style="display:none;"></span>
                                </div>
                            </div> -->

                        
 
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div id="meetingDateFilterTrigger" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%; font-size: 10px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <i class="fa fa-calendar"></i>&nbsp;
                <span id="selectedMeetingDateRange">Select  Date</span>
            </div>
            <i class="fa fa-caret-down"></i>
        </div>

        <span style="display:none;" id="meetingDateViewsAll"></span>
        
        <div id="meetingCalendarFilterDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ccc; box-shadow: 0 2px 10px rgba(0,0,0,0.2); width: 600px; margin-top: 5px;">
            <div class="dropdown-content" style="padding: 10px; max-height: 500px; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-3" style="padding-right: 0;">
                        <div class="recent-filters" style="padding: 10px;">
                            <h3 style="font-size: 14px; margin-bottom: 10px;">Recently Used</h3>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="today"> Today
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="yesterday"> Yesterday
                            </label>
                        </div>
                        
                        <div class="filter-group" style="padding: 10px;">
                            <h3 style="font-size: 14px; margin-bottom: 10px;">Quick Select</h3>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="today" checked> Today
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="yesterday"> Yesterday
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="today_yesterday"> Today and Yesterday
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="last_7_days"> Last 7 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="last_14_days"> Last 14 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="last_28_days"> Last 28 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="last_30_days"> Last 30 Days
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="this_week"> This Week
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="last_week"> Last Week
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="this_month"> This Month
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="last_month"> Last Month
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="maximum"> Maximum
                            </label>
                            <label class="filter-option" style="font-size: 12px;">
                                <input type="radio" name="meetingFilter" value="custom"> Custom
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-9" style="padding-left: 10px;">
                        <div class="date-range-selector" style="margin-bottom: 10px;">
                            <select id="meetingDateRangeSelect" class="form-control form-control-sm" style="font-size: 12px; height: 30px;">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="today_yesterday">Today and Yesterday</option>
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="last_14_days">Last 14 Days</option>
                                <option value="last_28_days">Last 28 Days</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="maximum">Maximum</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        
                        <div class="meetingcalendar-container" style="display: flex; margin-bottom: 10px;">
                            <div class="month-calendar" style="margin-right: 10px; width: 48%;">
                                <div class="month-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 12px;">
                                    <button class="meeting-month-nav-btn prev-month" style="padding: 0 5px; font-size: 12px;">&lt;</button>
                                    <div class="month-title" id="meetingCurrentMonthHeader" style="font-size: 12px;">June 2025</div>
                                    <button class="meeting-month-nav-btn next-month" style="padding: 0 5px; font-size: 12px;">&gt;</button>
                                </div>
                                <div class="week-days" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 10px;">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="days-grid" id="meetingCurrentMonth" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 11px;"></div>
                            </div>
                            
                            <div class="month-calendar" style="width: 48%;">
                                <div class="month-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 12px;">
                                    <button class="meeting-month-nav-btn prev-month" style="padding: 0 5px; font-size: 12px;">&lt;</button>
                                    <div class="month-title" id="meetingNextMonthHeader" style="font-size: 12px;">July 2025</div>
                                    <button class="meeting-month-nav-btn next-month" style="padding: 0 5px; font-size: 12px;">&gt;</button>
                                </div>
                                <div class="week-days" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 10px;">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="days-grid" id="meetingNextMonth" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 11px;"></div>
                            </div>
                        </div>
                        
                        <div class="compare-section" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <div class="compare-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Compare</div>
                            <div class="compare-fields" style="display: flex; gap: 5px; margin-top: 5px;">
                                <select id="meetingFilterSelect" class="form-control form-control-sm" style="flex: 1; font-size: 12px; height: 30px;">
                                    <option value="last_7_days">Last 7 Days</option>
                                    <option value="last_14_days">Last 14 Days</option>
                                    <option value="last_28_days">Last 28 Days</option>
                                    <option value="last_30_days">Last 30 Days</option>
                                    <option value="custom">Custom</option>
                                </select>
                                <div class="date-display form-control form-control-sm" id="meetingStartDateDisplay" style="flex: 1; font-size: 12px; height: 30px; line-height: 30px; padding: 0 5px;">June 20, 2025</div>
                                <div class="date-display form-control form-control-sm" id="meetingEndDateDisplay" style="flex: 1; font-size: 12px; height: 30px; line-height: 30px; padding: 0 5px;">June 26, 2025</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; margin-top: 10px; gap: 5px;">
                            <button type="button" class="btn btn-secondary" id="meetingDatemodalcancel" style="padding: 3px 8px; font-size: 12px;">Cancel</button>
                            <button type="button" class="btn btn-primary" id="applyMeetingDateFilter" style="padding: 3px 8px; font-size: 12px;">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





                        <div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search_meeting" name="zone_name" id="meeting_zonss" placeholder="Select Zone">
            <input type="hidden" id="meeting_zone_id">
            <div class="dropdown-options single_search options_meeting" id="meetingZoneOptions">
                <!-- Zones will be loaded via AJAX -->
                <div>Loading zones...</div>
            </div>
        </div>
    </div>
</div>

<!-- Branch Filter -->
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search_meeting" name="Branch_name" id="meeting_brans" placeholder="Select Branch">
            <div class="dropdown-options single_search options_meeting" id="meetingBranchOptions">
                <!-- Branches will be loaded via AJAX -->
                <div>Loading branches...</div>
            </div>
        </div>
    </div>
</div>

<!-- Marketer Filter -->
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search_meeting" name="userfullname" id="meeting_mark" placeholder="Select Marketer">
            <div class="dropdown-options single_search options_meeting" id="meetingMarketerOptions">
                <!-- Marketers will be loaded via AJAX -->
                <div>Loading marketers...</div>
            </div>
        </div>
    </div>
</div>




                            <div class="col-xl-2 col-md-2">
                                <div class="">
                                    <!-- <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filternew" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
         background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filters</a> -->
                                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                            </div>

                        </div>

                    </div>
                    <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="counts1">0</span> Rows for <span id="meetingdatefitter">Last 30 days</span></span>
                        <span style="color: #080fd399;font-size: 12px;font-weight: 600;" class="search_meeting">Search</span>
                        <span style="cursor: pointer;" class="badge bg-success value_meeting_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_meeting_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_meeting_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer; display:none;" class="badge bg-danger clear_all_meeting">Clear all</span>
                        <span style="cursor: pointer; display:none;" class="badge bg-success "></span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">Meeting ID & Time</th>
                                            <th class="thview">Doctor Name</th>
                                            <th class="thview">Clinic/Hospital</th>
                                            <th class="thview">Contact</th>
                                            <th class="thview">Marketer Name</th>
                                            <th class="thview">feedbacks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="meetingdetails">
                                        <tr>
                                            <td data-column-index="12">
                                                <img src="../assets/images/loader1.gif" style="
    width: 50%;
    margin-left: 200%;
" alt="Icon" class="icon">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelect1">
                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="pagination1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel" aria-labelledby="analytics-tab-3" tabindex="0">
                <div class="col-xl-12 col-md-12">
                    <div class="row">


                        <!-- <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="reportrange2" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>
                                <span style="display:none;"></span>
                            </div>
                        </div> -->




                        <div class="col-xl-2 col-md-2">
    <div class="card">
        <div id="patientDateFilterTrigger" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%; font-size: 10px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <i class="fa fa-calendar"></i>&nbsp;
                <span id="patientSelectedDateRange">Select Date</span>
            </div>
            <i class="fa fa-caret-down"></i>
        </div>

        <span style="display:none;" id="patientDateviewsall"></span>
        
        <div id="patientCalendarFilterDropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ccc; box-shadow: 0 2px 10px rgba(0,0,0,0.2); width: 600px; margin-top: 5px;">
            <div class="dropdown-content" style="padding: 10px; max-height: 500px; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-3" style="padding-right: 0;">
                        <div class="patient-recent-filters" style="padding: 10px;">
                            <h3 style="font-size: 14px; margin-bottom: 10px;">Recently Used</h3>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="today"> Today
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="yesterday"> Yesterday
                            </label>
                        </div>
                        
                        <div class="patient-filter-group" style="padding: 10px;">
                            <h3 style="font-size: 14px; margin-bottom: 10px;">Quick Select</h3>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="today" checked> Today
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="yesterday"> Yesterday
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="today_yesterday"> Today and Yesterday
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="last_7_days"> Last 7 Days
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="last_14_days"> Last 14 Days
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="last_28_days"> Last 28 Days
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="last_30_days"> Last 30 Days
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="this_week"> This Week
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="last_week"> Last Week
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="this_month"> This Month
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="last_month"> Last Month
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="maximum"> Maximum
                            </label>
                            <label class="patient-filter-option" style="font-size: 12px;">
                                <input type="radio" name="patientFilter" value="custom"> Custom
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-9" style="padding-left: 10px;">
                        <div class="patient-date-range-selector" style="margin-bottom: 10px;">
                            <select id="patientDateRangeSelect" class="form-control form-control-sm" style="font-size: 12px; height: 30px;">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="today_yesterday">Today and Yesterday</option>
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="last_14_days">Last 14 Days</option>
                                <option value="last_28_days">Last 28 Days</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="maximum">Maximum</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        
                        <div class="patient-calendar-container" style="display: flex; margin-bottom: 10px;">
                            <div class="patient-month-calendar" style="margin-right: 10px; width: 48%;">
                                <div class="patient-month-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 12px;">
                                    <button class="patient-month-nav-btn patient-prev-month" style="padding: 0 5px; font-size: 12px;">&lt;</button>
                                    <div class="patient-month-title" id="patientCurrentMonthHeader" style="font-size: 12px;">June 2025</div>
                                    <button class="patient-month-nav-btn patient-next-month" style="padding: 0 5px; font-size: 12px;">&gt;</button>
                                </div>
                                <div class="patient-week-days" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 10px;">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="patient-days-grid" id="patientCurrentMonth" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 11px;"></div>
                            </div>
                            
                            <div class="patient-month-calendar" style="width: 48%;">
                                <div class="patient-month-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 12px;">
                                    <button class="patient-month-nav-btn patient-prev-month" style="padding: 0 5px; font-size: 12px;">&lt;</button>
                                    <div class="patient-month-title" id="patientNextMonthHeader" style="font-size: 12px;">July 2025</div>
                                    <button class="patient-month-nav-btn patient-next-month" style="padding: 0 5px; font-size: 12px;">&gt;</button>
                                </div>
                                <div class="patient-week-days" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 10px;">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>
                                <div class="patient-days-grid" id="patientNextMonth" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 11px;"></div>
                            </div>
                        </div>
                        
                        <div class="patient-compare-section" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <div class="patient-compare-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Compare</div>
                            <div class="patient-compare-fields" style="display: flex; gap: 5px; margin-top: 5px;">
                                <select id="patientFilterSelect" class="form-control form-control-sm" style="flex: 1; font-size: 12px; height: 30px;">
                                    <option value="last_7_days">Last 7 Days</option>
                                    <option value="last_14_days">Last 14 Days</option>
                                    <option value="last_28_days">Last 28 Days</option>
                                    <option value="last_30_days">Last 30 Days</option>
                                    <option value="custom">Custom</option>
                                </select>
                                <div class="patient-date-display form-control form-control-sm" id="patientStartDateDisplay" style="flex: 1; font-size: 12px; height: 30px; line-height: 30px; padding: 0 5px;">June 20, 2025</div>
                                <div class="patient-date-display form-control form-control-sm" id="patientEndDateDisplay" style="flex: 1; font-size: 12px; height: 30px; line-height: 30px; padding: 0 5px;">June 26, 2025</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; margin-top: 10px; gap: 5px;">
                            <button type="button" class="btn btn-secondary" id="patientDatemodalcancel" style="padding: 3px 8px; font-size: 12px;">Cancel</button>
                            <button type="button" class="btn btn-primary" id="patientApplyDateFilter" style="padding: 3px 8px; font-size: 12px;">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



                      
                    <div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search_patient" name="zone_name" id="patient_zone" placeholder="Select Zone">
            <input type="hidden" id="patient_zone_id">
            <div class="dropdown-options single_search options_patient" id="patientZoneOptions">
                <!-- Zones will be loaded via AJAX -->
                <div>Loading zones...</div>
            </div>
        </div>
    </div>
</div>

<!-- Branch Filter -->
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search_patient" name="Branch_name" id="patient_branch" placeholder="Select Branch">
            <div class="dropdown-options single_search options_patient" id="patientBranchOptions">
                <!-- Branches will be loaded via AJAX -->
                <div>Loading branches...</div>
            </div>
        </div>
    </div>
</div>

<!-- Marketer Filter -->
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search_patient" name="userfullname" id="patient_marketer" placeholder="Select Marketer">
            <div class="dropdown-options single_search options_patient" id="patientMarketerOptions">
                <!-- Marketers will be loaded via AJAX -->
                <div>Loading marketers...</div>
            </div>
        </div>
    </div>
</div>





                        <div class="col-xl-2 col-md-2">
                            <div class="">
                                <!-- <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filterpatient" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
         background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filters</a> -->
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>

                    </div>
                    <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="counts2">0</span> Rows for <span id="patientviews">Last 30 days</span></span>
                        <span class="search_view_patient" style="color: #080fd399;font-size: 12px;font-weight: 600;">Search</span>
                        <span style="cursor: pointer;" class="badge bg-success value_patient_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_patient_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_patient_mainsearch"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-danger clear_all_views_patient" style="display:none;">Clear all</span>
                        <span class="badge bg-success value_edit" style="display:none;"></span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">Referal ID</th>
                                            <th class="thview">Wife Name</th>
                                            <th class="thview">Date</th>
                                            <th class="thview">Husband Name</th>
                                            <th class="thview">Marketer Name</th>
                                            <th class="thview">Doctor Name</th>
                                            <th class="thview">Hospital Name</th>
                                            <th class="thview">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patient_details">
                                        <tr>
                                            <td data-column-index="7">
                                                <img src="../assets/images/loader1.gif" style="width: 50%; margin-left: 200%;" alt="Icon" class="icon">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelect2">
                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="pagination2"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column Rendering table end -->
                    <!-- Multiple Table Control Elements start -->
                    <!-- Row Created Callback table end -->
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>More Filters</h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filter">
                                    <i class="ti ti-x f-20"></i>
                                </a>



                            </div><br>

                            <label class="form-label required" style="font-size: 12px;font-weight: 600;color:red;margin-left: 28px;" id="error_throws"></label>

                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">


                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                               <br> <label class="form-label required" style="font-size: 12px;font-weight: 600;">Specialization</label>
                                                <div class="multiselect-container" tabindex="0">
                                                    <input type="text" id="special_more" name="special" class="multiselect-input morefittersclr" placeholder="Select Specialization" readonly>
                                                    <div class="multiselect-options doctor_option">
                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY DR" onchange="updateSelectedValues()">ALLOPATHY DR
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="VHN" onchange="updateSelectedValues()">VHN
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY HOSPITAL" onchange="updateSelectedValues()">ALLOPATHY HOSPITAL
                                                        </label>

                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY CLINIC" onchange="updateSelectedValues()">MBBS
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Homeopathy" onchange="updateSelectedValues()">Homeopathy
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="NGO" onchange="updateSelectedValues()">NGO
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Others" onchange="updateSelectedValues()">Others
                                                        </label>
                                                    </div>
                                                </div><br>
                                            </div>
                                        </div>


                                    </div>
                                </div><br><br><br><br><br><br>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 mainclearall">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  id="morefitter_search"  class="btn btn-outline-primary w-50">Submit</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px; display:none;"  id="dismissmodelssss" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filternew">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>More Filters</h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filternew">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Specialization:</label>
                                                <div class="multiselect-container" tabindex="0">
                                                    <input type="text" id="special_more_meeting" name="special" class="multiselect-input morefittersclr_meeting" placeholder="Select Specialization" readonly>
                                                    <div class="multiselect-options meeting-option">
                                                        <label>
                                                            <input type="checkbox" value="Ayurvedic" onchange="updateSelectedValues()"> Ayurvedic
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Gynecologist" onchange="updateSelectedValues()">Gynecologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Andrologist" onchange="updateSelectedValues()">Andrologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY CLINIC" onchange="updateSelectedValues()"> MBBS Doctors
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="AYUSH CLINIC" onchange="updateSelectedValues()">AYUSH CLINIC
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="AYUSH DR" onchange="updateSelectedValues()">AYUSH DR
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="AGENT" onchange="updateSelectedValues()">AGENT
                                                        </label>
                                                    </div>
                                                </div><br>
                                            </div>
                                        </div>

                                    </div>
                                </div><br><br><br><br><br><br>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 mainclearall_meeting">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="meetingfitter_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filterpatient">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>More Filters</h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filterpatient">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Specialization</label>
                                                <div class="multiselect-container" tabindex="0">
                                                    <input type="text" id="special_more_patient" name="special" class="multiselect-input morefittersclr_patient" placeholder="Select Specialization" readonly>
                                                    <div class="multiselect-options patient-option">
                                                        <label>
                                                            <input type="checkbox" value="Ayurvedic" onchange="updateSelectedValues()"> Ayurvedic
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Gynecologist" onchange="updateSelectedValues()">Gynecologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Andrologist" onchange="updateSelectedValues()">Andrologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="MBBS Doctors" onchange="updateSelectedValues()"> MBBS Doctors
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Homeopathy" onchange="updateSelectedValues()">Homeopathy
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="NGO" onchange="updateSelectedValues()">NGO
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Others" onchange="updateSelectedValues()">Others
                                                        </label>
                                                    </div>
                                                </div><br>
                                            </div>
                                        </div>


                                    </div>
                                </div><br><br><br><br><br><br>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 mainclearall_patient">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="patientfitter_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filternotes">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>Notes #<span id="notesid"></span></h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filternotes">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                                    <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                                                    <p style="font-size: 12px;" id="doctor_names">Name : Dr.Aravindivf</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Employee Details</h4>
                                                    <p style="font-size: 12px;" id="empname_views_all">Emp Name : R. Anusuya</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Patient Details</h4>
                                                    <p style="font-size: 12px;" id="wifenames">Wife Name : nnn</p>
                                                    <p style="font-size: 12px; margin-top: -11px;" id="husbandnames">Husband Name : cccc</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Feedback</h4>
                                                    <p style="font-size: 12px;" id="notesfeedback">asdsadsa sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd asdsadsa
                                                        sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filterfeedback">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>Feedback #<span id="feedbackid"></span></h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filterfeedback">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                                    <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                                                    <p style="font-size: 12px;" id="doctor_names_feed">Name : Dr.Aravindivf</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Marketer Details</h4>
                                                    <p style="font-size: 12px;" id="empname_views_all_feed">Emp Name : R. Anusuya</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Feedback</h4>
                                                    <p style="font-size: 12px;" id="feedback_meetss"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<div class="col-sm-12">
    <div class="card-body pc-component btn-page">
        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add Doctor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                    </div>
                    <ul id="save_msgList"></ul>
                    <div id="error-message"></div>
                    <div class="modal-body">
                        <input type="hidden" class="userid" name="userid" id="userid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_doctor errorss"></span>
                                    <input type="text" class="form-control" id="doctor_name" name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Name">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Marketer Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_employee errorss"></span>

                                        <input type="text" class="form-control" id="empolyee_name" name="empolyee_name" readonly style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="{{ $admin->username }}" placeholder="Doctor Name">


                                </div>
                            </div>

                <div class="col-sm-3">
                    <div class="mb-3">
                        <label class="form-label required" style="font-size: 12px;font-weight: 600;">B2B :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_special errorss"></span>
                        <select class="form-control" style="
                                height: 42px;
                        " id="specialfitters" name="special">
                        <option value="">Select B2B</option>
                        <option value="ALLOPATHY DR">ALLOPATHY DR</option>
                        <option value="VHN">VHN</option>
                        <option value="ALLOPATHY HOSPITAL">ALLOPATHY HOSPITAL</option>
                        <option value="ALLOPATHY CLINIC">ALLOPATHY CLINIC</option>
                        <option value="AYUSH CLINIC">AYUSH CLINIC</option>
                        <option value="AYUSH DR">AYUSH DR</option>
                        <option value="AGENT">AGENT</option>
                    </select>
                    </div>
                </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Clinic / Hospital Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplname errorss"></span>
                <input type="text" class="form-control" id="hopsital_name" name="hopsital_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Address :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_adress errorss"></span>
                    <input type="text" class="form-control" id="address" name="address" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Address">
                </div>
            </div>


        
<div class="col-sm-3">
    <div class="mb-3">
        <label class="form-label required" style="font-size: 12px;font-weight: 600;">Location :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_city errorss"></span>
        <select class="form-control" id="city" name="city">
            <option value="">Select Location</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}">
                    {{ $location->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

    <!-- <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Location :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_city errorss"></span>
            <input type="text" class="form-control" readonly id="city" value="{{ $admin->branch_id }}" name="city" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="City">

        </div>
    </div> -->
    
    <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Contact Number :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_docontact errorss"></span>
            <input type="text" class="form-control" id="doc_contact" name="doc_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Contact Number">
        </div>
    </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Hospital Contact Number : </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                <input type="text" class="form-control" id="hpl_contact" name="hpl_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Hospital Contact Number">
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Hospital Online Link : </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hospital_link errorss"></span>
                    <input type="text" class="form-control" id="hospital_link" name="hospital_link" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Hospital Online Link">
                </div>
            </div>
    <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Map Link : </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_map_link errorss"></span>
            <input type="text" class="form-control" id="map_link" name="map_link" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Map Link">
        </div>
    </div>
    <div class="col-sm-6">
        <label class="form-label required" style="font-size: 12px;font-weight: 600;">Hospital Images : [ Min 2 - Max 5 ]</label>
        <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_images errorss"></span>
            <input name="files[]" id="image_uploads" type="file" style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" multiple />
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button1" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
        <button type="submit" id="submit-doctor-datas" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;" class="btn btn-primary">Submit</button>
    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Add meeting -->
        <div class="card-body pc-component btn-page">
            <div
                class="modal fade"
                id="exampleModal2"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                            <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add Meeting <span id="idsviews"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                        <div class="modal-body">
                            <input type="hidden" class="meetingschedule" name="ref_doctor_id" id="ref_doctor_id" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                            <div class="row">


            <!-- <div class="col-sm-3">
            <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_doctor errorss"></span>
            <div class="dropdown">
            <input type="text" class="searchInput single_search meetingschedule" name="doctor_name" id="meeting-doctorname" placeholder="Select doctor name">
            <div class="dropdown-options  meeting-doctorname" >
            <div data-id="101">Dr. A</div>
            <div data-id="102">Dr. B</div>
            </div>
            </div>
     </div>
        </div> -->


                  <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name:</label>
            <span style="font-size:10px; color:red;" class="error_doctor errorss"></span>
            <div class="dropdown">
                <input type="text" class="searchInput single_search meetingschedule" name="doctor_name" id="meeting-doctorname" 
                       style="height: 36px; border-radius: 5px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050; width: 100%; padding: 6px 12px;"
                       placeholder="Select doctor name">
                <div class="dropdown-options meeting-doctorname">
                    <div data-id="101">Dr. A</div>
                    <div data-id="102">Dr. B</div>
                </div>
            </div>
        </div>
    </div>


        <!-- Meeting Date -->
    <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Meeting Date:</label>
            <input type="date" class="form-control meetingschedule" name="meeting_date" 
                   style="height: 36px; border-radius: 5px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050; width: 100%; padding: 6px 12px;">
        </div>
    </div>
    
    <!-- Meeting Time -->
    <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Meeting Time:</label>
            <input type="time" class="form-control meetingschedule" name="meeting_time" 
                   style="height: 36px; border-radius: 5px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050; width: 100%; padding: 6px 12px;">
        </div>
    </div>

    <!-- Image Upload -->
    <div class="col-sm-3">
        <div class="mb-3">
            <label class="form-label" style="font-size: 12px;font-weight: 600;">Upload Image:</label>
            <input type="file" class="form-control meetingschedule" name="meeting_image" id= "meeting_image" accept="image/*" 
                   style="height: 36px; border-radius: 5px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050; width: 100%; padding: 6px 12px;">
        </div>
    </div>
                                <input type="hidden" class="form-control meetingschedule" id="emp_name_meeting" name="empolyee_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Emp name">
                                <input type="hidden" class="form-control meetingschedule" id="userfullname" name="userfullname" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Emp name">

                                <input type="hidden" class="form-control meetingschedule" id="special_name" name="special" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Specialization">
                                <input type="hidden" class="form-control meetingschedule" id="hops_name" name="hopsital_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Clinic / Hospital Name">
                                <input type="hidden" class="form-control meetingschedule" id="address_name" name="address" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Address">
                                <input type="hidden" class="form-control meetingschedule" id="city_name" name="city" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Address">
                                <input type="hidden" class="form-control meetingschedule" id="doc_contacts" name="doc_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Doctor Contact Number">
                                <input type="hidden" class="form-control meetingschedule" id="hpl_contacts" name="hpl_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Hospital Contact Number">
                                <div class="col-sm-12 ">
                                    <div class="mb-12">
                                        <label class="form-label required" style="font-size: 12px;font-weight: 600;">Meeting Feedbacks: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_feedbackss errorss"></span>
                                        <textarea require class="form-control meetingschedule" id="additional-notes" name="meeting_feedback" rows="6" style="border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050 !important; width: 100%; padding: 6px;" placeholder="Enter feedback details here"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button2" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit-doctor-meetings" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end meeting  -->
    <!-- Add patient -->
    <div class="card-body pc-component btn-page">
        <div
            class="modal fade"
            id="exampleModal3"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add patient <span id="idsviews"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                    </div>
                    <ul id="save_msgList"></ul>
                    <div id="error-message"></div>
                    <div class="modal-body">
                        <input type="hidden" class="patientschedule" name="ref_doctor_id" id="ref_patient_id" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_doctor errorss"></span>

                                    <div class="dropdown">
                                        <input type="text" class="searchInput single_search patientschedule" name="doctor_name" id="patient-doctorname" placeholder="Select Doctor Name">
                                        <div class="dropdown-options options_marketers patient-doctorname">
                                            <div data-value="B.Henry Remgious">B.Henry Remgious</div>
                                            <div data-value="S.Selvamurgan">S.Selvamurgan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control patientschedule" id="patient_emp_name" name="empolyee_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Emp name">
                             <input type="hidden" class="form-control patientschedule" id="userfullnamepatient" name="userfullname" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Emp name">

                            <input type="hidden" class="form-control patientschedule" id="patient_special_name" name="special" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Specialization">
                            <input type="hidden" class="form-control patientschedule" id="patient_hops_name" name="hopsital_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Clinic / Hospital Name">
                            <input type="hidden" class="form-control patientschedule" id="patient_address_name" name="address" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Address">
                            <input type="hidden" class="form-control patientschedule" id="patient_city_name" name="city" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Address">
                            <input type="hidden" class="form-control patientschedule" id="patient_doc_contacts" name="doc_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Doctor Contact Number">
                            <input type="hidden" class="form-control patientschedule" id="patient_hpl_contacts" name="hpl_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Hospital Contact Number">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Wife Name: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                                    <input type="text" class="form-control patientschedule" id="patient_wifename" name="wifename" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Wife Name">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Wife Contact: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                                    <input type="text" class="form-control patientschedule" id="patient_wifecontact" name="wife_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Wife Contact">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Husband Name: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                                    <input type="text" class="form-control patientschedule" id="patient_husbandname" name="husband_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Husband Name">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Husband Contact Num: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                                    <input type="text" class="form-control patientschedule" id="patient_husbandcontact" name="husband_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Husband Contact">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">MRD Num: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                                    <input type="text" class="form-control patientschedule" id="patient_mrn" name="mrn_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="MRN Num">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="mb-12">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Notes </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplcontact errorss"></span>
                                    <textarea require class="form-control patientschedule" id="patient_notes" name="notes" rows="6" style="border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050 !important; width: 100%; padding: 6px;" placeholder="Enter Notes details here"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button3" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit-doctor-patient" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end patient  -->
    <div class="offcanvas pc-announcement-offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="announcement">
        <div class="offcanvas-body p-0">
            <div id="ecom-filter" class="collapse collapse-horizontal show">
                <div class="ecom-filter">
                    <div class="card">
                        <!-- Sticky Header -->
                        <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                            <h5>Doctor details Edit : #<span id="uesrids"></span></h5>
                            <a
                                href="#"
                                class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                data-bs-dismiss="offcanvas"
                                data-bs-target="#announcement">
                                <i class="ti ti-x f-20"></i>
                            </a>
                        </div>
                        <!-- Scrollable Block -->
                        <div class="scroll-block position-relative">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_doctor errorss"></span>
                                            <input type="text" class="form-control editsall" id="doctorname_edits" name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Marketer Name:</label>
                                            <select class="mb-3 form-select editsall" id="emp_name" name="empolyee_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="empolyee_name">

                                                        <option value="{{ $admin->username }}">{{ $admin->username }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Contact:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplname errorss"></span>
                                            <input type="text" class="form-control editsall" id="contactviews" name="hpl_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                        </div><br>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">city:</label>
                                            <select class="mb-3 form-select editsall" id="citys" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="city">
                                                <option value="">Select city</option>
                                                <option value="Chennai">Chennai</option>
                                                <option value="Tiruppur">Tiruppur</option>
                                                <option value="Salem">Salem</option>
                                                <option value="Coimbatore">Coimbatore</option>
                                                <option value="Pollachi">Pollachi</option>
                                                <option value="Bangalore">Bangalore</option>
                                                <option value="Palakad">Palakad</option>
                                                <option value="Kozhikode">Kozhikode</option>
                                                <option value="Tiruppur">Tiruppur</option>
                                                <option value="Erode">Erode</option>
                                                <option value="Trichy">Trichy</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Address:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplname errorss"></span>
                                            <input type="text" class="form-control editsall" id="addressviews" name="address" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fixed Clear All Button -->
                            <div class="card-footer sticky-bottom bg-white">
                                <div class="d-flex justify-content-between">
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 ">Clear All</a>
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50 editsoveralls">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card-body pc-component btn-page">
    <div
        class="modal fade"
        id="exampleModal1"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Doctor Details : #<span id="doctor_ids"> 4</span> - <span id="Doctornamehead"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                </div>
        <div class="row">
            <div class="col-sm-7">
                <div class="mb-7">
                    <img src="" id="main">
                    <div id="thumbnails">
                                        <!-- <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                          <img src="../assets/images/gallery-grid/1722403363_IMG_20240731_105245156.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png"> -->
            </div>
        </div>
    </div>
<div class="col-sm-4"><br>
    <ul class="nav nav-tabs analytics-tab">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="doctor_info" style="padding: 0.5rem 0.8rem;" type="button">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="meeting_info" style="padding: 0.5rem 0.8rem;" type="button">Meeting info</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="patients_info" style="padding: 0.5rem 0.8rem;" type="button">Patients Info</button>
        </li>
    </ul><br>
        <div class="mb-4">
            <div class="card" id="doctor_info_details" style="overflow-y: auto; max-height: 400px;scrollbar-width: thin; /* For Firefox */ ">
                <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                    <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                    <p style="font-size: 12px;" class="doctor_names"></p>
                    <h4 class="alert-heading" style="font-size: 12px;margin-top: 15px;">Address details</h4>
                    <p style="font-size: 12px; margin-top: 0px;" id="docaddress"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Contact Details</h4>
                    <p style="font-size: 12px;" id="dcnum"></p>
                    <p style="font-size: 12px; ;margin-top: -12px;" id="hpnum"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Marketer Details</h4>
                    <p style="font-size: 12px;" class="empname_views_all"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Schedules</h4>
                    <p style="font-size: 12px;" id="visit_date"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Location</h4>
                    <p style="font-size: 12px;color: #d70ebb;" id="maplocation"></p>
                </div> <br>
            </div>
                                    <div class="card" id="meeting_info_details" style="overflow-y: auto; max-height: 400px;scrollbar-width: thin; /* For Firefox */ ">
                                        <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                            <h4 class="alert-heading" style="font-size: 12px;">Name details</h4>
                                            <p style="font-size: 12px; ;" class="empname_views_all">Employee Name : Vignesh</p>
                                            <h4 class="alert-heading" style="font-size: 12px;margin-top: 15px;">Hopsital Details</h4>
                                            <p style="font-size: 12px; margin-top: 0px;" class="hosptalnames">Hospital Name : Viyay 's hospital</p>
                                            <p style="font-size: 12px; ;margin-top: -12px;" class="cityviews">city : Chennai</p>
                                            <h4 class="alert-heading" style="font-size: 12px;">Meeting Details</h4>
                                            <p style="font-size: 12px;" id="totalmeetins">dfsdfdsfdsf</p>
                                            <!-- Table for additional details -->
                                            <table style="font-size: 12px; margin-top: 10px; width: 90%; border-collapse: collapse;" border="1">
                                                <thead>
                                                    <tr style="background-color: #f2f2f2;">
                                                        <th style="padding: 5px;">Time & Date</th>
                                                        <th style="padding: 5px;">Feedback</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="meeting_popdata">
                                                    <tr>
                                                        <td style="padding: 5px;">John Doe</td>
                                                        <td style="padding: 5px;">35</td>
                                                        <td style="padding: 5px;">Male</td>
                                                        <td style="padding: 5px;">2025-01-22</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> <br>
                                    </div>
                                    <div class="card" id="patients_info_details" style="overflow-y: auto; max-height: 400px; scrollbar-width: thin; /* For Firefox */">
                                        <div class="views-ali" style="margin-left: 20px; margin-top: 20px;">
                                            <h4 class="alert-heading" style="font-size: 12px;">Details</h4>
                                            <p style="font-size: 12px;" class="empname_views_all">Employee Name: Vignesh</p>
                                            <h4 class="alert-heading" style="font-size: 12px; margin-top: 15px;">Hospital Details</h4>
                                            <p style="font-size: 12px; margin-top: 0px;" class="hosptalnames">Hospital Name: Vijay's Hospital</p>
                                            <p style="font-size: 12px; margin-top: -12px;" class="cityviews">City: Chennai</p>
                                            <h4 class="alert-heading" style="font-size: 12px;">Patients Details</h4>
                                            <p style="font-size: 12px;" id="total_patient">Total Patients Count: 0</p>
                                            <!-- Table for additional details -->
                                            <table style="font-size: 12px; margin-top: 10px; width: 90%; border-collapse: collapse;" border="1">
                                                <thead>
                                                    <tr style="background-color: #f2f2f2;">
                                                        <th style="padding: 5px;">Wife Name</th>
                                                        <th style="padding: 5px;">MRD</th>
                                                        <th style="padding: 5px;">Husband Name</th>
                                                        <th style="padding: 5px;">MRD</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="patient_popdata">
                                                    <tr>
                                                        <td style="padding: 5px;">John Doe</td>
                                                        <td style="padding: 5px;">35</td>
                                                        <td style="padding: 5px;">Male</td>
                                                        <td style="padding: 5px;">2025-01-22</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement" aria-labelledby="announcementLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <p class="text-span">Today</p>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-success f-12">Big News</div>
                            <p class="mb-0 text-muted">2 min ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Able Pro is Redesigned</h5>
                        <p class="text-muted">Able Pro is completely renowed with high aesthetics User Interface.</p>
                        <img src="../assets/images/layout/img-announcement-1.png" alt="img" class="img-fluid mb-3" />
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid"><a class="btn btn-outline-secondary" href="https://1.envato.market/zNkqj6" target="_blank">Check Now</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-warning f-12">Offer</div>
                            <p class="mb-0 text-muted">2 hour ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Able Pro is in best offer price</h5>
                        <p class="text-muted">Download Able Pro exclusive on themeforest with best price. </p>
                        <a href="https://1.envato.market/zNkqj6" target="_blank"><img src="../assets/images/layout/img-announcement-2.png" alt="img" class="img-fluid" /></a>
                    </div>
                </div>
                <p class="text-span mt-4">Yesterday</p>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-primary f-12">Blog</div>
                            <p class="mb-0 text-muted">12 hour ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Featured Dashboard Template</h5>
                        <p class="text-muted">Do you know Able Pro is one of the featured dashboard template selected by Themeforest team.?</p>
                        <img src="../assets/images/layout/img-announcement-3.png" alt="img" class="img-fluid" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-primary f-12">Announcement</div>
                            <p class="mb-0 text-muted">12 hour ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Buy Once - Get Free Updated lifetime</h5>
                        <p class="text-muted">Get the lifetime free updates once you purchase the Able Pro.</p>
                        <img src="../assets/images/layout/img-announcement-4.png" alt="img" class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
        <script type="text/javascript">
            Dropzone.options.myDropzone = {
                acceptedFiles: "image/*", // Only accept image files (any image type)
                addRemoveLinks: true, // Optionally, show remove links for the file
                dictDefaultMessage: "Drag an image here or click to select one image"
            };


            
            // Set the initial start and end dates
            var start = moment().subtract(29, 'days');
            var end = moment();
            var start1 = moment().subtract(29, 'days');
            var end1 = moment();
            var start2 = moment().subtract(29, 'days');
            var end2 = moment();
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

            function cb1(start1, end1) {
                $("#meetingdatefitter").text(start1.format('DD/MM/YYYY') + ' - ' + end1.format('DD/MM/YYYY'));
                // Check the selected date range and adjust the display accordingly
                if (start1.isSame(end1, 'day')) {
                    // If the start and end date are the same, show the single date
                    if (start1.isSame(moment(), 'day')) {
                        $('#reportrange1 span').html('Today');
                    } else if (start1.isSame(moment().subtract(1, 'days'), 'day')) {
                        $('#reportrange1 span').html('Yesterday');
                    } else {
                        $('#reportrange1 span').html(start1.format('DD/MM/YYYY'));
                    }
                } else {
                    // For other ranges like "Last 7 Days", "This Month", etc.
                    $('#reportrange1 span').html(start1.format('DD/MM/YYYY') + ' - ' + end1.format('DD/MM/YYYY'));
                }
            }

            function cb2(start2, end2) {
                $("#patientviews").text(start2.format('DD/MM/YYYY') + ' - ' + end2.format('DD/MM/YYYY'));
                // Check the selected date range and adjust the display accordingly
                if (start2.isSame(end2, 'day')) {
                    // If the start and end date are the same, show the single date
                    if (start2.isSame(moment(), 'day')) {
                        $('#reportrange2 span').html('Today');
                    } else if (start2.isSame(moment().subtract(1, 'days'), 'day')) {
                        $('#reportrange2 span').html('Yesterday');
                    } else {
                        $('#reportrange2 span').html(start2.format('DD/MM/YYYY'));
                    }
                } else {
                    // For other ranges like "Last 7 Days", "This Month", etc.
                    $('#reportrange2 span').html(start2.format('DD/MM/YYYY') + ' - ' + end2.format('DD/MM/YYYY'));
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
            $('#reportrange1').daterangepicker({
                startDate: start1,
                endDate: end1,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb1);
            $('#reportrange2').daterangepicker({
                startDate: start2,
                endDate: end2,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb2);
            // Set initial date range text
            cb(start, end);
            cb1(start1, end1);
            cb2(start2, end2);
            $(document).on('click', '.editbtn', function(e) {
                $('#exampleModal').modal('show');
            });
            $(document).on('click', '.addmeeting', function(e) {
                $('#exampleModal2').modal('show');
            });
            $(document).on('click', '.addpatient', function(e) {
                $('#exampleModal3').modal('show');
            });
            $("#dashboard_color").css("color", "rgba(8, 15, 211, 0.6)");


            // Simulate fetching data
            const data = []; // Empty array means no data
            const tableBody = document.getElementById('table-body');
            const noDataMessage = document.getElementById('no-data');
            const prevButton = document.getElementById('prev-button');
            const nextButton = document.getElementById('next-button');

            function renderTable() {
                if (data.length === 0) {
                    noDataMessage.style.display = 'block';
                    tableBody.style.display = 'none';
                } else {
                    noDataMessage.style.display = 'none';
                    tableBody.style.display = 'table-row-group';
                }
            }
            // Initialize table rendering
            renderTable();
        </script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("meeting-doctorname");
        const dropdown = document.getElementById("doctor-options");
        const options = dropdown.querySelectorAll("div");

        // Show dropdown when input is focused
        input.addEventListener("focus", function() {
            dropdown.style.display = "block";
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener("click", function() {
                input.value = this.getAttribute("value"); // Set input value
                dropdown.style.display = "none"; // Hide dropdown
            });
        });

        // Filter options based on input value
        input.addEventListener("input", function() {
            let filter = input.value.toLowerCase();
            options.forEach(option => {
                let text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? "block" : "none";
            });
            dropdown.style.display = "block"; // Show dropdown while filtering
        });

        // Close dropdown if clicking outside
        document.addEventListener("click", function(event) {
            if (!event.target.closest(".dropdown")) {
                dropdown.style.display = "none";
            }
        });
    });
</script>


 <script src="{{ asset('/assets/referral/doctor-added.js') }}"></script>
  <script src="{{ asset('/assets/referral/doctor-details.js') }}"></script>
  <script src="{{ asset('/assets/referral/doctor-meeting.js') }}"></script>
  <script src="{{ asset('/assets/referral/doctor-patient.js') }}"></script>
  <script src="{{ asset('/assets/referral/referral_info.js') }}"></script>
      

  <!-- <script src="{{ asset('js/referral/doctor-added.js') }}"></script>
<script src="{{ asset('js/referral/doctor-details.js') }}"></script>
<script src="{{ asset('js/referral/doctor-meeting.js') }}"></script>
<script src="{{ asset('js/referral/doctor-patient.js') }}"></script>
<script src="{{ asset('js/referral/referral_info.js') }}"></script> -->



<script>

                         $(document).ready(function() {
        $(document).on("input", ".searchInput", function () {
        const searchText = $(this).val().toLowerCase().split(",").pop().trim();
        const currentValues = $(this).val().split(",").map(v => v.trim());
        $(this).siblings(".dropdown-options").find("div").each(function () {
            const optionText = $(this).text().trim().toLowerCase();
            const fullText = $(this).text().trim();

            // Always show, but dim/hint if selected
            const matchesSearch = optionText.includes(searchText);
            const isSelected = currentValues.includes(fullText);
            $(this).toggle(matchesSearch);
            $(this).toggleClass("selected", isSelected);
        });
    });

                        // Ensure only valid values remain in the input field (for multiple search)
                        $(document).on("blur", ".multi_search", function() {
                            const inputField = $(this);
                            const typedValues = inputField.val().split(",").map(v => v.trim());
                            const validOptions = inputField.siblings(".dropdown-options").find("div")
                                .map(function() {
                                    return $(this).text().trim();
                                }).get();

                            // Filter typed values to keep only valid options
                            const filteredValues = typedValues.filter(v => validOptions.includes(v));

                            inputField.data("values", filteredValues);
                            inputField.val(filteredValues.join(", "));
                        });

                        // Close dropdown when clicking outside
                        $(document).on("click", function(event) {
                            if (!$(event.target).closest(".dropdown").length) {
                                $(".dropdown").removeClass("active");
                            }
                        });

                         $(".dropdown input").on("focus", function () {
        // Close all dropdowns first
        $(".dropdown").removeClass("active");
        // Then open the one that's focused
        $(this).closest(".dropdown").addClass("active");
    });
                    });




                   $(document).on("click", ".dropdown-options div", function () {
        const selectedValue = $(this).text().trim();

        const inputField = $(this).closest(".dropdown").find(".searchInput");
        // alert(inputField);
        if (inputField.hasClass("single_search")) {
                                // SINGLE selection: Replace previous value
                                inputField.val(selectedValue);
                                inputField.closest(".dropdown").removeClass("active"); // Close dropdown
                            } else {
        const currentValues = inputField.val().split(",").map(v => v.trim()).filter(Boolean);

        if (!currentValues.includes(selectedValue)) {
            currentValues.push(selectedValue);
            inputField.val(currentValues.join(", "));
        }

        $(this).addClass("selected");

        $(this).closest(".dropdown").removeClass("active");
    }
    });

    // On input focus
    $(document).on("focus", ".searchInput", function () {
        const inputField = $(this);
        const currentValues = inputField.val().split(",").map(v => v.trim());

        inputField.siblings(".dropdown-options").find("div").each(function () {
            const optionText = $(this).text().trim();
            const isSelected = currentValues.includes(optionText);

            $(this).show();
            $(this).toggleClass("selected", isSelected);
        });

        $(this).closest(".dropdown").addClass("active");
    });
                    // Close dropdown when clicking outside
                    $(document).on("click", function(event) {
                        if (!$(event.target).closest(".dropdown").length) {
                            $(".dropdown").removeClass("active");
                        }
                    });



  $(document).ready(function () {
        $('.selectzone_camp > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
             $('#zoneviews').val(selectedText);
            $('#camp_zone_id').val(selectedType);
            $('#branchviews').val('');
            $('#getlocation_act').hide();

            $('#getlocation_act > div').removeClass('selected');

           $('#getlocation_act > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

       $('#zoneviews').on('input', function () {
            $('#camp_zone_id').val('');
            $('#getlocation_camp > div').show();
            $('#branchviews').val('');
            $('#getlocation_camp > div').removeClass('selected');
        });
    
             
//   $('#zoneviews').on('click', function () {
//       $('#zonalHeadFilter').val('');
         
//         });



        $('#branchviews').on('focus', function () {
            const selectedType = Number($('#camp_zone_id').val()); // use hidden ID

            if (selectedType) {
                $('#getlocation_camp > div')
                    .hide()
                    .filter(function () {
                        return Number($(this).data('type')) === selectedType;
                    })
                    .show();
            } else {
                $('#getlocation_camp > div').show().removeClass('selected');
            }

            $('#getlocation_camp').show();
        });
       $('#getlocation_camp > div').off('click').on('click', function () {
            const name = $(this).data('value');
            $('#branchviews').val(name);

            $('#getlocation_camp > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocation_camp').hide();
        });

        $('input.searchInput').attr('autocomplete', 'off');
    });





$(document).on('click', '.toggle-details', function(e) {
    e.stopPropagation();

    const id = $(this).data('id');
    const $detailRow = $('#detail-' + id);
    const $icon = $(this);

    if ($detailRow.is(':visible')) {
        $detailRow.slideUp(300);
        $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
    } else {
        $('.detail-row:visible').slideUp(300);
        $('.toggle-details').removeClass('fa-minus-circle').addClass('fa-plus-circle');

        $detailRow.slideDown(300);
        $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');

        if (!$detailRow.hasClass('loaded')) {
            loadDetailsForRow(id);
            $detailRow.addClass('loaded');
        }
    }
});



function loadDetailsForRow(id) {

     $('#doctor-details-' + id).html('<div class="loading-text">Loading doctor details...</div>');
        $.ajax({
            url: fetchUrl,
            type: "GET",
            data: { ref_doctor_id: id }, 
            success: function(response) {
                console.log("doctordata",response);
                renderDoctorDetailsForRow(id,response);
            },
            error: function(xhr, status, error) {
                $('#doctor-details-' + id).html('<div class="error-text">Failed to load doctor details</div>');
            }
        });
    

    $('#meeting-details-' + id).html('<div class="loading-text">Loading meeting details...</div>');
        $.ajax({
            url: meetingviews,
            type: "GET",
            data: { ref_doctor_id: id }, 
            success: function(response) {
                console.log("mdata",response);
                renderMeetingDetailsForRow(id, response);
            },
            error: function(xhr, status, error) {
                $('#meeting-details-' + id).html('<div class="error-text">Failed to load meeting details</div>');
            }
        });
    
    $('#patient-details-' + id).html('<div class="loading-text">Loading patient details...</div>');
        $.ajax({
            url: patientviews,
            type: "GET",
            data: { ref_doctor_id: id }, 
            success: function(response) {
                  console.log("pdata",response);
                renderPatientDetailsForRow(id, response);
            },
            error: function(xhr, status, error) {
                $('#patient-details-' + id).html('<div class="error-text">Failed to load patient details</div>');
            }
        });
}

function renderDoctorDetailsForRow(id, data) {
    const $container = $('#doctor-details-' + id);
    console.log("dfata",data);
    if (!data || data.length === 0) {
        $container.html('<div class="doctor-no-data">No doctor data found</div>');
        return;
    }

    let html = `
    
    <div class="doctor-container" style="font-family: sans-serif;">
        <div class="doctor-header">
            <span>MNDMN HMS</span>
        </div>
             <h6 class="doctor-title">${data[0].special}/Referal Agent</h6>
        <div class="doctor-card">`;

    data.forEach(doctor => {

          let imagePath = '../public/doctor_images/sharmila.jpg'; 
        if (doctor.image_paths) {
            try {
                const parsedPaths = JSON.parse(doctor.image_paths.replace(/\\/g, '/'));
                if (parsedPaths.length > 0) {
                    const fileName = parsedPaths[0].split('/').pop();
                    imagePath = `../public/doctor_images/${fileName}`;
                }
            } catch (e) {
                console.error("Error parsing image paths:", e);
            }
        }

        html += `
            <div class="doctor-card-left">
          <img src="${imagePath}" 
                     alt="${doctor.doctor_name}"
                     class="doctor-img"
                     onerror="this.onerror=null; this.src='../public/doctor_images/doctor.jpg';">

            </div>
            <div class="doctor-card-right">
                <div class="doctor-info-main">
                    <h3 class="doctor-name">Dr. ${doctor.doctor_name || 'N/A'}</h3>
                    <p class="doctor-specialization">${doctor.special || 'N/A'}</p>
                    <p class="doctor-detail"><strong>Contact:</strong> ${doctor.doc_contact || 'N/A'}</p>
                    <p class="doctor-detail"><strong>Hospital:</strong> ${doctor.hopsital_name || 'N/A'}</p>
                </div>
                <div class="doctor-info-details">
                    
                    <p class="doctor-detail"><strong>Address:</strong> ${doctor.address || 'N/A'}</p>
                    <p class="doctor-detail"><strong>Location:</strong> ${doctor.location_name || 'N/A'}</p>
                </div>
            </div>`;
    });

    html += `</div></div>`;
    $container.html(html);
}




function renderMeetingDetailsForRow(id, data) {

    const $container = $('#meeting-details-' + id);

    //  if (!data || data.length === 0) {
    //     let html = `
    //     <div class="meeting-container">
    //         <h3 class="meeting-title">Meetings Timeline</h3>
    //         <p class="meeting-subtitle">No meetings scheduled yet</p>
    //         <div class="meeting-timeline">
    //             <div class="meeting-timeline-left">
    //                 <img src="../public/doctor_images/meeting.jpg" alt="Meeting Image" class="meeting-img">
    //                 <button class="btn btn-primary add-meeting-btn" data-id="${id}" data-bs-toggle="modal" data-bs-target="#exampleModal2">
    //                     <i class="fas fa-plus"></i> Add Meeting
    //                 </button>
    //             </div>
    //             <div class="meeting-timeline-right">
    //                 <div class="no-meetings-message">
    //                     No meetings found for this doctor. Click "Add Meeting" to schedule one.
    //                 </div>
    //             </div>
    //         </div>
    //     </div>`;
    //     $container.html(html);
    //     return;
    // }

     if (!data || data.length === 0) {
    let html = `
    <div class="meeting-container">
        <div class="meeting-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 class="meeting-title">Meetings Timeline</h3>
               
        </div>
        <p class="meeting-subtitle">No meetings scheduled yet</p>
        <div class="meeting-timeline">
            <div class="meeting-timeline-left">
                <img src="../public/doctor_images/meeting.jpg" alt="Meeting Image" class="meeting-img">
            </div>
             <div class="meeting-timeline-right">
                <div class="placeholder-data">
                    <p>Doctor Name: xx***xx</p>
                    <p>Date: xx***xx</p>
                    <p>Time: xx***xx</p>
                    <p>Feedback: xx***xx</p>
                    
                </div>
            </div>
        </div>
    </div>`;
    $container.html(html);
    return;
}


    let html = `
    <div class="meeting-container">
        <h3 class="meeting-title">Meetings Timeline</h3>
        <p class="meeting-subtitle">Schedule Meeting with doctor</p>
        <div class="meeting-timeline">`;

    data.forEach((meeting, index) => {
        const formattedDate = moment(meeting.created_at).format("DD MMM YYYY");
        const formattedTime = moment(meeting.created_at).format("HH:mm A");
         const meetingImage = meeting.meeting_image 
        ? `../public/meeting_images/${meeting.meeting_image}`
        : '../public/doctor_images/meeting.jpg';
        
        if (index === 0) {
            html += `
            <div class="meeting-timeline-left">
   <img src="${meetingImage}" alt="Meeting Image" class="meeting-img" 
                 onerror="this.onerror=null; this.src='../assets/referral/meeting.jpg'">
                <div class="meeting-date">${formattedDate} &nbsp; ${formattedTime}</div>
            </div>
            <div class="meeting-timeline-right">`;
        }

        html += `
            <div class="meeting-entry">
            <div class="" style="display:flex">
                <div class="meeting-entry-header">
                    <span class="meeting-entry-date">${formattedDate}</span>
                    <span class="meeting-entry-time">${formattedTime}</span>
                     <span class="meeting-entry-type">${meeting.meeting_type || ' '}</span>
                </div>
                <div class="meeting-entry-content">
                    <span class="meeting-entry-name">Dr. ${meeting.doctor_name}</span>
                    
                </div>
            </div>
                <p class="meeting-entry-feedback" style="width:550px">${meeting.meeting_feedback || 'No feedback provided'}</p>
            </div>`;
    });

    html += `</div></div></div>`;
    $container.html(html);
}


// function renderMeetingDetailsForRow(id, data) {
//     const $container = $('#meeting-details-' + id);
//     if (!data || data.length === 0) {
//         $container.html('<div class="meeting-no-data">No meeting data found</div>');
//         return;
//     }

//     let html = `
//     <div class="meeting-container">
//         <h3 class="meeting-title">Meetings Timeline</h3>
//         <p class="meeting-subtitle">Schedule Meeting with doctor</p>
//         <div class="meeting-timeline">`;

//     data.forEach((meeting, index) => {
//         const formattedDate = moment(meeting.created_at).format("DD MMM YYYY");
//         const formattedTime = moment(meeting.created_at).format("HH:mm A");
        
//         if (index === 0) {
//             html += `
//             <div class="meeting-timeline-left">
// <img 
//   src="../assets/referral/meeting.jpg" 
//   alt="Meeting Image" 
//   class="meeting-img"
//   onerror="this.onerror=null; this.src='../public/doctor_images/avatar.jpg';"
// >
//                 <div class="meeting-date">${formattedDate} &nbsp; ${formattedTime}</div>
//             </div>
//             <div class="meeting-timeline-right">`;
//         }

//         html += `
//             <div class="meeting-entry">
//             <div class="" style="display:flex">
//                 <div class="meeting-entry-header">
//                     <span class="meeting-entry-date">${formattedDate}</span>
//                     <span class="meeting-entry-time">${formattedTime}</span>
//                      <span class="meeting-entry-type">${meeting.meeting_type || ' '}</span>
//                 </div>
//                 <div class="meeting-entry-content">
//                     <span class="meeting-entry-name">Dr. ${meeting.doctor_name}</span>
                    
//                 </div>
//             </div>
//                 <p class="meeting-entry-feedback" style="width:550px"><span style="font-weight:bold;">Feedback:</span>${meeting.meeting_feedback || 'No feedback provided'}</p>
//             </div>`;
//     });

//     html += `</div></div></div>`;
//     $container.html(html);
// }




function renderPatientDetailsForRow(id, data) {
    const $container = $('#patient-details-' + id);

   
    if (!data || data.length === 0) {
        let html = `
        <div class="patient-container">
            <div class="patient-header">
                <h3 class="patient-title">Walk-Ins Referred</h3>
                   
               
            </div>
            <table class="patient-table">
                <thead>
                    <tr class="patient-table-header">
                        <th class="patient-th patient-name">MRN Number</th>
                        <th class="patient-th patient-age">Wife Name</th>
                        <th class="patient-th patient-phone">Wife Contact</th>
                        <th class="patient-th patient-mrn">Date</th>
                        <th class="patient-th patient-treatment">Husband Name</th>
                        <th class="patient-th patient-amount">Husband Contact</th>
                        <th class="patient-th patient-date">Employee Name</th>
                        <th class="patient-th patient-date">Doctor Name</th>
                        <th class="patient-th patient-date">Hospital Name</th>
                        <th class="patient-th patient-date">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                       <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    <td class="patient-td">xx***xx</td>
                    </tr>
                </tbody>
            </table>
        </div>`;
        $container.html(html);
        return;
    }

    let html = `
    <div class="patient-container">
        <h3 class="patient-title">Walk-Ins Referred</h3>
        <table class="patient-table">
            <thead>
                <tr class="patient-table-header">
                    <th class="patient-th patient-name">MRN Number</th>
                    <th class="patient-th patient-age">Wife Name</th>
                    <th class="patient-th patient-phone">Wife Contact</th>
                    <th class="patient-th patient-mrn">Date</th>
                    <th class="patient-th patient-treatment">Husband Name</th>
                    <th class="patient-th patient-amount">Husband Contact</th>
                    <th class="patient-th patient-date">Employee Name</th>
                    <th class="patient-th patient-date">Doctor Name</th>
                    <th class="patient-th patient-date">Hospital Name</th>
                    <th class="patient-th patient-date">Notes</th>
                </tr>
            </thead>
            <tbody>`;

    data.forEach(patient => {
        const formattedDate = moment(patient.created_at).format("DD MMM YYYY");
        const phoneNumber = patient.wife_contact || patient.husband_contact || 'N/A';
        const amount = patient.amount_spent ? '₹' + patient.amount_spent.toLocaleString('en-IN') : 'N/A';
        
        html += `
            <tr class="patient-table-row">
                <td class="patient-td patient-name">${patient.mrn_number || 'N/A'}</td>
                <td class="patient-td patient-age">${patient.wifename || 'N/A'}</td>
                <td class="patient-td patient-phone">${patient.wife_contact}</td>
                <td class="patient-td patient-phone">${formattedDate}</td>
                <td class="patient-td patient-mrn">${patient.husband_name || 'N/A'}</td>
                <td class="patient-td patient-mrn">${patient.husband_contact || 'N/A'}</td>
                <td class="patient-td patient-treatment">${patient.empolyee_name || 'N/A'}</td>
                <td class="patient-td patient-amount">${patient.doctor_name}</td>
                <td class="patient-td patient-date">${patient.hopsital_name }</td>
                <td class="patient-td patient-date">${patient.notes }</td>
            </tr>`;
    });

    html += `</tbody></table></div>`;
    $container.html(html);
}


// function renderPatientDetailsForRow(id, data) {
//     const $container = $('#patient-details-' + id);
//     if (!data || data.length === 0) {
//         $container.html('<div class="patient-no-data">No patient data found</div>');
//         return;
//     }

//     let html = `
//     <div class="patient-container">
//         <h3 class="patient-title">Walk-Ins Referred</h3>
//         <table class="patient-table">
//             <thead>
//                 <tr class="patient-table-header">
//                     <th class="patient-th patient-name">MRN Number</th>
//                     <th class="patient-th patient-age">Wife Name</th>
//                     <th class="patient-th patient-phone">Wife Contact</th>
//                     <th class="patient-th patient-mrn">Date</th>
//                     <th class="patient-th patient-treatment">Husband Name</th>
//                     <th class="patient-th patient-amount">Husband Contact</th>
//                     <th class="patient-th patient-date">Employee Name</th>
//                     <th class="patient-th patient-date">Doctor Name</th>
//                     <th class="patient-th patient-date">Hospital Name</th>
//                     <th class="patient-th patient-date">Notes</th>
//                 </tr>
//             </thead>
//             <tbody>`;

//     data.forEach(patient => {
//         const formattedDate = moment(patient.created_at).format("DD MMM YYYY");
//         const phoneNumber = patient.wife_contact || patient.husband_contact || 'N/A';
//         const amount = patient.amount_spent ? '₹' + patient.amount_spent.toLocaleString('en-IN') : 'N/A';
        
//         html += `
//             <tr class="patient-table-row">
//                 <td class="patient-td patient-name">${patient.mrn_number || 'N/A'}</td>
//                 <td class="patient-td patient-age">${patient.wifename || 'N/A'}</td>
//                 <td class="patient-td patient-phone">${patient.wife_contact}</td>
//                 <td class="patient-td patient-phone">${formattedDate}</td>
//                 <td class="patient-td patient-mrn">${patient.husband_name || 'N/A'}</td>
//                 <td class="patient-td patient-mrn">${patient.husband_contact || 'N/A'}</td>
//                 <td class="patient-td patient-treatment">${patient.empolyee_name || 'N/A'}</td>
//                 <td class="patient-td patient-amount">${patient.doctor_name}</td>
//                 <td class="patient-td patient-date">${patient.hopsital_name }</td>
//                 <td class="patient-td patient-date">${patient.notes }</td>
//             </tr>`;
//     });

//     html += `</tbody></table></div>`;
//     $container.html(html);
// }




$(document).ready(function() {

    console.log('jQuery version:', $.fn.jquery);
console.log('Moment version:', moment.version);
console.log('DateRangePicker available:', $.fn.daterangepicker ? 'YES' : 'NO');
    // When zone is selected, load marketers for that zone
    $('.selectzone_camp div[data-value]').on('click', function() {
        const zoneId = $(this).data('value');
        $('#camp_zone_id').val(zoneId);
        $('#zoneviews').val($(this).text());
        
        // Load marketers via AJAX
        $.ajax({
            url: 'get-marketers-by-zone',
            type: 'GET',
            data: { zone_id: zoneId },
            success: function(response) {
                $('#marketerOptions').empty();
                if(response.length > 0) {
                    $.each(response, function(index, marketer) {
                        $('#marketerOptions').append(
                            `<div data-value="${marketer.user_fullname}" data-type="${marketer.zone_id}">${marketer.user_fullname}</div>`
                        );
                    });
                } else {
                    $('#marketerOptions').append('<div>No marketers in this zone</div>');
                }
            },
            error: function() {
                $('#marketerOptions').empty().append('<div>Error loading marketers</div>');
            }
        });

           
            $.ajax({
            url: 'get-zonal-heads-by-zone',
            type: 'GET',
            data: { zone_id: zoneId },
            success: function(response) {
                $('#zonalHeadOptions').empty();
                if(response.length > 0) {
                    $.each(response, function(index, zonalHead) {
                        $('#zonalHeadOptions').append(
                            `<div data-value="${zonalHead.id}" data-zone="${zonalHead.zone_id}">${zonalHead.user_fullname}</div>`
                        );
                    });
                } else {
                    $('#zonalHeadOptions').append('<div>No zonal heads in this zone</div>');
                }
            },
            error: function() {
                $('#zonalHeadOptions').empty().append('<div>Error loading zonal heads</div>');
            }
        });



    });

    // Initialize with all marketers if superadmin and no zone selected
    @if($admin->access_limits == 1)
        loadAllMarketers();
        loadAllZonalHeads()
    @endif


 

function loadAllZonalHeads() {
        $.ajax({
            url: 'get-all-zonal-heads',
            type: 'GET',
            success: function(response) {
                $('#zonalHeadOptions').empty();
                if(response.length > 0) {
                    $.each(response, function(index, zonalHead) {
                        $('#zonalHeadOptions').append(
                            `<div data-value="${zonalHead.id}" data-zone="${zonalHead.zone_id}">${zonalHead.user_fullname}</div>`
                        );
                    });
                } else {
                    $('#zonalHeadOptions').append('<div>No zonal heads available</div>');
                }
            }
        });
    }


    function loadAllMarketers() {
        $.ajax({
            url: 'get-all-marketers',
            type: 'GET',
            success: function(response) {
                $('#marketerOptions').empty();
                if(response.length > 0) {
                    $.each(response, function(index, marketer) {
                        $('#marketerOptions').append(
                            `<div data-value="${marketer.user_fullname}" data-type="${marketer.zone_id}">${marketer.user_fullname}</div>`
                        );
                    });
                } else {
                    $('#marketerOptions').append('<div>No marketers available</div>');
                }
            }
        });
    }
});




$(document).ready(function() {
                // Zonal Head filter functionality
                let selectedZonalHeadId = null;
                
                // Toggle dropdown
                $('#zonalHeadFilter').on('click', function() {
                    $('#zonalHeadOptions').toggle();
                });
                
                // Search functionality
                $('#zonalHeadFilter').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('#zonalHeadOptions div').each(function() {
                        const text = $(this).text().toLowerCase();
                        $(this).toggle(text.includes(searchTerm));
                    });
                });
                
                // Selection handler
                // $('#zonalHeadOptions div[data-value]').on('click', function() {
                $(document).on('click', '#zonalHeadOptions div[data-value]', function() {
                    selectedZonalHeadId = $(this).data('value');
                    $('#zonalHeadFilter').val($(this).text());
                    $('#zonalHeadOptions').hide();
                    
                    // Clear other filters if needed
                    // $('#zoneviews').val('');
                    // $('#camp_zone_id').val('');
                    //   $('#branchviews').val('');
                    
                    // Load marketers for this zonal head
                    $.ajax({
                        url: 'get-marketers-by-zonal-head',
                        type: 'GET',
                        data: { zonal_head_id: selectedZonalHeadId },
                        success: function(response) {
                            updateMarketerDropdown(response);
                        }
                    });
                });
                
                function updateMarketerDropdown(marketers) {
                    const $marketerOptions = $('#marketerOptions');
                    $marketerOptions.empty();
                    
                    if (marketers.length > 0) {
                        marketers.forEach(function(marketer) {
                            $marketerOptions.append(
                                `<div data-value="${marketer.user_fullname}">${marketer.user_fullname}</div>`
                            );
                        });
                    } else {
                        $marketerOptions.append('<div>No marketers found</div>');
                    }
                }
            });





// $(document).ready(function() {

//          $('#dateFilterTrigger').click(function() {
//         $('#calendarFilterModal').modal('show'); 
//     });

//     let currentDate = new Date();
//     let currentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
//     let nextMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
//     let customStartDate = null;
//     let customEndDate = null;

//     function renderCalendars() {
//         renderCalendar('currentMonth', currentMonth);
//         renderCalendar('nextMonth', nextMonth);
//     }
    
//     function renderCalendar(elementId, date) {
//         const container = document.getElementById(elementId);
//         container.innerHTML = '';
        
//         const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
//         const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        
//         const monthNames = ["January", "February", "March", "April", "May", "June", 
//                           "July", "August", "September", "October", "November", "December"];
//         document.getElementById(`${elementId}Header`).textContent = 
//             `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        
//         for (let i = 0; i < firstDay.getDay(); i++) {
//             const emptyDay = document.createElement('div');
//             emptyDay.className = 'day other-month';
//             container.appendChild(emptyDay);
//         }
        
//         for (let i = 1; i <= lastDay.getDate(); i++) {
//             const day = document.createElement('div');
//             day.className = 'day';
//             day.textContent = i;
//             day.dataset.date = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
//             container.appendChild(day);
//         }
//     }
    
//     function updateSelection(filter, today) {
//         const startDateDisplay = document.getElementById('startDateDisplay');
//         const endDateDisplay = document.getElementById('endDateDisplay');
//         let startDate, endDate;
        
//         if (['last_7_days', 'last_14_days', 'last_28_days', 'last_30_days', 'custom'].includes(filter)) {
//             document.getElementById('filterSelect').value = filter;
//         }
        
//         switch(filter) {
//             case 'today':
//                 startDate = new Date(today);
//                 endDate = new Date(today);
//                 break;
//             case 'yesterday':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - 1);
//                 endDate = new Date(startDate);
//                 break;
//             case 'today_yesterday':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - 1);
//                 endDate = new Date(today);
//                 break;
//             case 'last_7_days':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - 6);
//                 endDate = new Date(today);
//                 break;
//             case 'last_14_days':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - 13);
//                 endDate = new Date(today);
//                 break;
//             case 'last_28_days':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - 27);
//                 endDate = new Date(today);
//                 break;
//             case 'last_30_days':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - 29);
//                 endDate = new Date(today);
//                 break;
//             case 'this_week':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - startDate.getDay());
//                 endDate = new Date(today);
//                 endDate.setDate(endDate.getDate() + (6 - endDate.getDay()));
//                 break;
//             case 'last_week':
//                 startDate = new Date(today);
//                 startDate.setDate(startDate.getDate() - startDate.getDay() - 7);
//                 endDate = new Date(startDate);
//                 endDate.setDate(endDate.getDate() + 6);
//                 break;
//             case 'this_month':
//                 startDate = new Date(today.getFullYear(), today.getMonth(), 1);
//                 endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
//                 break;
//             case 'last_month':
//                 startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
//                 endDate = new Date(today.getFullYear(), today.getMonth(), 0);
//                 break;
//             case 'maximum':
//                 startDate = new Date(0);
//                 endDate = new Date(today);
//                 break;
//             case 'custom':
//                 if (customStartDate && customEndDate) {
//                     startDate = new Date(customStartDate);
//                     endDate = new Date(customEndDate);
//                 } else if (customStartDate) {
//                     startDate = new Date(customStartDate);
//                     endDate = new Date(customStartDate);
//                 } else {
//                     startDate = new Date(today);
//                     endDate = new Date(today);
//                 }
//                 break;
//         }
        
//         startDateDisplay.textContent = formatDisplayDate(startDate);
//         endDateDisplay.textContent = formatDisplayDate(endDate);
        
//         highlightDateRange(startDate, endDate);
        
//         updateDropdownText(filter, startDate, endDate);
//     }
    
//     function highlightDateRange(startDate, endDate) {
//         document.querySelectorAll('.day').forEach(day => {
//             day.classList.remove('selected', 'in-range');
//         });
        
//         const currentDate = new Date(startDate);
//         while (currentDate <= endDate) {
//             const dateString = formatDate(currentDate);
//             const dayElements = document.querySelectorAll(`.day[data-date="${dateString}"]`);
            
//             dayElements.forEach(day => {
//                 if (currentDate.getTime() === startDate.getTime() || 
//                     currentDate.getTime() === endDate.getTime()) {
//                     day.classList.add('selected');
//                 } else {
//                     day.classList.add('in-range');
//                 }
//             });
            
//             currentDate.setDate(currentDate.getDate() + 1);
//         }
//     }
    
//     function formatDate(date) {
//         const year = date.getFullYear();
//         const month = String(date.getMonth() + 1).padStart(2, '0');
//         const day = String(date.getDate()).padStart(2, '0');
//         return `${year}-${month}-${day}`;
//     }
    
//     function formatDisplayDate(date) {
//         const monthNames = ["January", "February", "March", "April", "May", "June",
//                           "July", "August", "September", "October", "November", "December"];
//         return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
//     }
    
//     function formatDateForServer(date) {
//         const day = String(date.getDate()).padStart(2, '0');
//         const month = String(date.getMonth() + 1).padStart(2, '0');
//         const year = date.getFullYear();
//         return `${day}/${month}/${year}`;
//     }
    
//     function updateDropdownText(filter, startDate, endDate) {
//         const selectElement = document.getElementById('dateRangeSelect');
//         const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
//                           "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
//         if (filter === 'today' || filter === 'yesterday') {
//             selectElement.querySelector(`option[value="${filter}"]`).text = 
//                 `${filter.charAt(0).toUpperCase() + filter.slice(1)} (${formatDisplayDate(startDate)})`;
//         } else if (filter === 'custom') {
//             const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
//             const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
//             selectElement.querySelector(`option[value="custom"]`).text = 
//                 `Custom (${startStr} - ${endStr})`;
//         } else {
//             const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
//             const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
//             selectElement.querySelector(`option[value="${filter}"]`).text = 
//                 `${filter.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')} (${startStr} - ${endStr})`;
//         }
//     }

//     // Initialize the calendar
//     renderCalendars();
//     $('input[value="today"]').prop('checked', true);
//     $('#dateRangeSelect').val('today');
//     updateSelection('today', currentDate);

//     // Event listeners
//     $('.prev-month').on('click', function() {
//         currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
//         nextMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
//         renderCalendars();
//         updateSelection($('input[name="filter"]:checked').val(), currentDate);
//     });
    
//     $('.next-month').on('click', function() {
//         currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
//         nextMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
//         renderCalendars();
//         updateSelection($('input[name="filter"]:checked').val(), currentDate);
//     });
    
//     $('input[name="filter"]').on('change', function() {
//         $('#dateRangeSelect').val(this.value);
//         updateSelection(this.value, currentDate);
//     });
    
//     $('#dateRangeSelect').on('change', function() {
//         $(`input[value="${this.value}"]`).prop('checked', true);
//         updateSelection(this.value, currentDate);
//     });

//     $('#filterSelect').on('change', function() {
//         updateSelection(this.value, currentDate);
//     });

//     $(document).on('click', '.day:not(.other-month)', function() {
//         const dateStr = $(this).data('date');
//         const dateParts = dateStr.split('-');
//         const clickedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
        
//         $('.day').removeClass('selected in-range');
        
//         if (!customStartDate || (customStartDate && customEndDate)) {
//             customStartDate = clickedDate;
//             customEndDate = null;
//             $(this).addClass('selected');
//         } 
//         else if (customStartDate && !customEndDate) {
//             if (clickedDate < customStartDate) {
//                 customEndDate = new Date(customStartDate);
//                 customStartDate = clickedDate;
//             } else {
//                 customEndDate = clickedDate;
//             }
            
//             const currentDate = new Date(customStartDate);
//             while (currentDate <= customEndDate) {
//                 const dateString = formatDate(currentDate);
//                 $(`.day[data-date="${dateString}"]`).each(function() {
//                     if (currentDate.getTime() === customStartDate.getTime() || 
//                         currentDate.getTime() === customEndDate.getTime()) {
//                         $(this).addClass('selected');
//                     } else {
//                         $(this).addClass('in-range');
//                     }
//                 });
                
//                 currentDate.setDate(currentDate.getDate() + 1);
//             }
            
//             $('input[value="custom"]').prop('checked', true);
//             $('#dateRangeSelect').val('custom');
//             $('#filterSelect').val('custom');
//             $('#startDateDisplay').text(formatDisplayDate(customStartDate));
//             $('#endDateDisplay').text(formatDisplayDate(customEndDate));
//         }
//     });

//     $('#datemodalcancel').on('click', function() {
  
//     $('#calendarFilterModal').modal('hide');
// });


//   $('#applyDateFilter').on('click', function() {
//     // Get the selected dates from your calendar UI
//     const startDate = $('#startDateDisplay').text(); // Format: "Month Day, Year"
//     const endDate = $('#endDateDisplay').text();     // Format: "Month Day, Year"
    
//     // Apply the filter
//     applyDateFilter(startDate, endDate);
    
//     // Close the modal
//     $('#calendarFilterModal').modal('hide');
// });

//     function fetchDataWithFilter(startDate, endDate) {
//         // This should match your existing fetchfitter function
//         $.ajax({
//             url: 'fetchfitter',
//             type: 'GET',
//             data: {
//                 datefiltervalue: `${startDate} - ${endDate}`,
//                 _token: '{{ csrf_token() }}'
//             },
//             success: function(response) {
//                 // Update your table or whatever with the response data
//                 console.log("date:",response);
//             },
//             error: function(xhr) {
//                 console.error(xhr.responseText);
//             }
//         });
//     }
// });

//new date filter



document.addEventListener('DOMContentLoaded', function() {
    const dateFilterTrigger = document.getElementById('dateFilterTrigger');
    const calendarFilterDropdown = document.getElementById('calendarFilterDropdown');
    
    dateFilterTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = calendarFilterDropdown.style.display === 'block';
        calendarFilterDropdown.style.display = isVisible ? 'none' : 'block';
    });
    
    document.addEventListener('click', function() {
        calendarFilterDropdown.style.display = 'none';
    });
    
    calendarFilterDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('datemodalcancel').addEventListener('click', function() {
        calendarFilterDropdown.style.display = 'none';
    });
    
    document.getElementById('applyDateFilter').addEventListener('click', function() {
        calendarFilterDropdown.style.display = 'none';
    });
    
    
});

$(document).ready(function() {


         $('#dateFilterTrigger').click(function() {
        $('#calendarFilterModal').modal('show'); 
    });

    let currentDate = new Date();
    let currentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    let nextMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
    let customStartDate = null;
    let customEndDate = null;

    function renderCalendars() {
        renderCalendar('currentMonth', currentMonth);
        renderCalendar('nextMonth', nextMonth);
    }
    
    function renderCalendar(elementId, date) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        
        const monthNames = ["January", "February", "March", "April", "May", "June", 
                          "July", "August", "September", "October", "November", "December"];
        document.getElementById(`${elementId}Header`).textContent = 
            `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        
        for (let i = 0; i < firstDay.getDay(); i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'day other-month';
            container.appendChild(emptyDay);
        }
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const day = document.createElement('div');
            day.className = 'day';
            day.textContent = i;
            day.dataset.date = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            container.appendChild(day);
        }
    }
    
    function updateSelection(filter, today) {
        const startDateDisplay = document.getElementById('startDateDisplay');
        const endDateDisplay = document.getElementById('endDateDisplay');
        let startDate, endDate;
        
        if (['last_7_days', 'last_14_days', 'last_28_days', 'last_30_days', 'custom'].includes(filter)) {
            document.getElementById('filterSelect').value = filter;
        }
        
        switch(filter) {
            case 'today':
                startDate = new Date(today);
                endDate = new Date(today);
                break;
            case 'yesterday':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 1);
                endDate = new Date(startDate);
                break;
            case 'today_yesterday':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 1);
                endDate = new Date(today);
                break;
            case 'last_7_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 6);
                endDate = new Date(today);
                break;
            case 'last_14_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 13);
                endDate = new Date(today);
                break;
            case 'last_28_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 27);
                endDate = new Date(today);
                break;
            case 'last_30_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 29);
                endDate = new Date(today);
                break;
            case 'this_week':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - startDate.getDay());
                endDate = new Date(today);
                endDate.setDate(endDate.getDate() + (6 - endDate.getDay()));
                break;
            case 'last_week':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - startDate.getDay() - 7);
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 6);
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'maximum':
                startDate = new Date(0);
                endDate = new Date(today);
                break;
            case 'custom':
                if (customStartDate && customEndDate) {
                    startDate = new Date(customStartDate);
                    endDate = new Date(customEndDate);
                } else if (customStartDate) {
                    startDate = new Date(customStartDate);
                    endDate = new Date(customStartDate);
                } else {
                    startDate = new Date(today);
                    endDate = new Date(today);
                }
                break;
        }
        
        startDateDisplay.textContent = formatDisplayDate(startDate);
        endDateDisplay.textContent = formatDisplayDate(endDate);
        
        highlightDateRange(startDate, endDate);
        
        updateDropdownText(filter, startDate, endDate);
    }
    
    function highlightDateRange(startDate, endDate) {
        document.querySelectorAll('.day').forEach(day => {
            day.classList.remove('selected', 'in-range');
        });
        
        const currentDate = new Date(startDate);
        while (currentDate <= endDate) {
            const dateString = formatDate(currentDate);
            const dayElements = document.querySelectorAll(`.day[data-date="${dateString}"]`);
            
            dayElements.forEach(day => {
                if (currentDate.getTime() === startDate.getTime() || 
                    currentDate.getTime() === endDate.getTime()) {
                    day.classList.add('selected');
                } else {
                    day.classList.add('in-range');
                }
            });
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function formatDisplayDate(date) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
                          "July", "August", "September", "October", "November", "December"];
        return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    }
    
    function formatDateForServer(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    function updateDropdownText(filter, startDate, endDate) {
        const selectElement = document.getElementById('dateRangeSelect');
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        if (filter === 'today' || filter === 'yesterday') {
            selectElement.querySelector(`option[value="${filter}"]`).text = 
                `${filter.charAt(0).toUpperCase() + filter.slice(1)} (${formatDisplayDate(startDate)})`;
        } else if (filter === 'custom') {
            const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
            const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
            selectElement.querySelector(`option[value="custom"]`).text = 
                `Custom (${startStr} - ${endStr})`;
        } else {
            const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
            const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
            selectElement.querySelector(`option[value="${filter}"]`).text = 
                `${filter.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')} (${startStr} - ${endStr})`;
        }
    }

    // Initialize the calendar
    renderCalendars();
    $('input[value="today"]').prop('checked', true);
    $('#dateRangeSelect').val('today');
    updateSelection('today', currentDate);

    // Event listeners
    $('.prev-month').on('click', function() {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
        nextMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
        renderCalendars();
        updateSelection($('input[name="filter"]:checked').val(), currentDate);
    });
    
    $('.next-month').on('click', function() {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
        nextMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
        renderCalendars();
        updateSelection($('input[name="filter"]:checked').val(), currentDate);
    });
    
    $('input[name="filter"]').on('change', function() {
        $('#dateRangeSelect').val(this.value);
        updateSelection(this.value, currentDate);
    });
    
    $('#dateRangeSelect').on('change', function() {
        $(`input[value="${this.value}"]`).prop('checked', true);
        updateSelection(this.value, currentDate);
    });

    $('#filterSelect').on('change', function() {
        updateSelection(this.value, currentDate);
    });

    // $('.calendar-container').on('click', '.day:not(.other-month)', function() {
    //     const dateStr = $(this).data('date');
    //     const dateParts = dateStr.split('-');
    //     const clickedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
        
    //     $('.day').removeClass('selected in-range');
        
    //     if (!customStartDate || (customStartDate && customEndDate)) {
    //         customStartDate = clickedDate;
    //         customEndDate = null;
    //         $(this).addClass('selected');
    //     } 
    //     else if (customStartDate && !customEndDate) {
    //         if (clickedDate < customStartDate) {
    //             customEndDate = new Date(customStartDate);
    //             customStartDate = clickedDate;
    //         } else {
    //             customEndDate = clickedDate;
    //         }
            
    //         const currentDate = new Date(customStartDate);
    //         while (currentDate <= customEndDate) {
    //             const dateString = formatDate(currentDate);
    //             $(`.day[data-date="${dateString}"]`).each(function() {
    //                 if (currentDate.getTime() === customStartDate.getTime() || 
    //                     currentDate.getTime() === customEndDate.getTime()) {
    //                     $(this).addClass('selected');
    //                 } else {
    //                     $(this).addClass('in-range');
    //                 }
    //             });
                
    //             currentDate.setDate(currentDate.getDate() + 1);
    //         }
            
    //         $('input[value="custom"]').prop('checked', true);
    //         $('#dateRangeSelect').val('custom');
    //         $('#filterSelect').val('custom');
    //         $('#startDateDisplay').text(formatDisplayDate(customStartDate));
    //         $('#endDateDisplay').text(formatDisplayDate(customEndDate));
    //     }
    // });

      $('.calendar-container').on('click', '.day:not(.other-month)', function() {
    const dateStr = $(this).data('date');
    const dateParts = dateStr.split('-');
    const clickedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
    
    $('#currentMonth .day, #nextMonth .day').removeClass('selected in-range');
    
    if (!customStartDate || (customStartDate && customEndDate)) {
        customStartDate = clickedDate;
        customEndDate = null;
        $(this).addClass('selected');
    } 
    else if (customStartDate && !customEndDate) {
        if (clickedDate < customStartDate) {
            customEndDate = new Date(customStartDate);
            customStartDate = clickedDate;
        } else {
            customEndDate = clickedDate;
        }
        
        const currentDate = new Date(customStartDate);
        while (currentDate <= customEndDate) {
            const dateString = formatDate(currentDate);
            $(`#currentMonth .day[data-date="${dateString}"], #nextMonth .day[data-date="${dateString}"]`).each(function() {
                if (currentDate.getTime() === customStartDate.getTime() || 
                    currentDate.getTime() === customEndDate.getTime()) {
                    $(this).addClass('selected');
                } else {
                    $(this).addClass('in-range');
                }
            });
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        $('input[value="custom"]').prop('checked', true);
        $('#dateRangeSelect').val('custom');
        $('#filterSelect').val('custom');
        $('#startDateDisplay').text(formatDisplayDate(customStartDate));
        $('#endDateDisplay').text(formatDisplayDate(customEndDate));
    }
});

    $('#datemodalcancel').on('click', function() {
  
    $('#calendarFilterModal').modal('hide');
});


  $('#applyDateFilter').on('click', function() {
    // Get the selected dates from your calendar UI
    const startDate = $('#startDateDisplay').text(); // Format: "Month Day, Year"
    const endDate = $('#endDateDisplay').text();     // Format: "Month Day, Year"
    
    // Apply the filter
    applyDateFilter(startDate, endDate);
    
    // Close the modal
    $('#calendarFilterModal').modal('hide');
});

   

    
});



// meeting filter

$(document).ready(function() {
    let allZones = [];
    let allBranches = [];
    let allMarketers = [];
    
    $.ajax({
        url: 'get-all-zones',
        type: 'GET',
        success: function(response) {
            allZones = response;
            const $zoneOptions = $('#meetingZoneOptions');
            $zoneOptions.empty();
            
            if(response.length > 0) {
                response.forEach(function(zone) {
                    $zoneOptions.append(
                        `<div data-value="${zone.id}">${zone.name}</div>`
                    );
                });
            } else {
                $zoneOptions.append('<div>No zones available</div>');
            }
        }
    });
    
    $.ajax({
        url: 'get-all-branches',
        type: 'GET',
        success: function(response) {
             console.log('Raw branches response:', response);
            allBranches = response;
             console.log('After assignment:', allBranches);
            updateBranchDropdown();
        }
    });
    
    // Load all marketers
    $.ajax({
        url: 'get-all-marketers',
        type: 'GET',
        success: function(response) {
            allMarketers = response;
            updatemeetingDropdown();
        }
    });
    
    // Zone selection handler
    $(document).on('click', '#meetingZoneOptions div[data-value]', function() {
        const zoneId = $(this).data('value');
        const zoneName = $(this).text();
        $('#meeting_zone_id').val(zoneId);
        $('#meeting_zonss').val(zoneName);
        
        // Filter branches and marketers based on selected zone
        updateBranchDropdown(zoneId);


                   $.ajax({
                        url: 'get-marketers-by-zone',
                        type: 'GET',
                        data: { zone_id: zoneId },
                        success: function(response) {
                            updatemeetingMarketerDropdown(response);
                        }
                    });


        // updatemeetingMarketerDropdown(zoneId);
    });
    
    // Branch selection handler
    $(document).on('click', '#meetingBranchOptions div[data-value]', function() {
        $('#meeting_brans').val($(this).text());
    });
    
    // Marketer selection handler
    $(document).on('click', '#meetingMarketerOptions div[data-value]', function() {
        $('#meeting_mark').val($(this).text());
    });
    
    // Update branch dropdown based on zone selection
    function updateBranchDropdown(zoneId = null) {
        const $branchOptions = $('#meetingBranchOptions');
        $branchOptions.empty();
        
        let filteredBranches = zoneId 
            ? allBranches.filter(branch => branch.zone_id == zoneId)
            : allBranches;
        if(filteredBranches.length > 0) {
            filteredBranches.forEach(function(branch) {
                $branchOptions.append(
                    `<div data-value="${branch.name}" data-zone="${branch.zone_id}">${branch.name}</div>`
                );
            });
        } else {
            $branchOptions.append(zoneId 
                ? '<div>No branches in this zone</div>'
                : '<div>No branches available</div>');
        }
    }

     function updatemeetingMarketerDropdown(marketers) {
                    const $marketerOptions = $('#meetingMarketerOptions');
                    $marketerOptions.empty();
                    
                    if (marketers.length > 0) {
                        marketers.forEach(function(marketer) {
                            $marketerOptions.append(
                                `<div data-value="${marketer.user_fullname}">${marketer.user_fullname}</div>`
                            );
                        });
                    } else {
                        $marketerOptions.append('<div>No marketers found</div>');
                    }
                }
    
    // Update marketer dropdown based on zone selection
    function updatemeetingDropdown(zoneId = null) {
        const $marketerOptions = $('#meetingMarketerOptions');
        $marketerOptions.empty();
        
        let filteredMarketers = zoneId ? allMarketers.filter(marketer => marketer.zone_id == zoneId): allMarketers;
        
        if(filteredMarketers.length > 0) {
            filteredMarketers.forEach(function(marketer) {
                $marketerOptions.append(
                    `<div data-value="${marketer.user_fullname}">${marketer.user_fullname}</div>`
                );
            });
        } else {
            $marketerOptions.append(zoneId
                ? '<div>No marketers in this zone</div>'
                : '<div>No marketers available</div>');
        }
    }


    // Search functionality for all dropdowns
    $('.marketervalues_search_meeting').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdownId = $(this).next('.dropdown-options').attr('id');
        
        $('#' + dropdownId + ' div').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    });
});



//meeting date filter
document.addEventListener('DOMContentLoaded', function() {
    const meetingDateFilterTrigger = document.getElementById('meetingDateFilterTrigger');
    const meetingCalendarFilterDropdown = document.getElementById('meetingCalendarFilterDropdown');
    
    meetingDateFilterTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = meetingCalendarFilterDropdown.style.display === 'block';
        meetingCalendarFilterDropdown.style.display = isVisible ? 'none' : 'block';
    });
    
    document.addEventListener('click', function() {
        meetingCalendarFilterDropdown.style.display = 'none';
    });
    
    meetingCalendarFilterDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('meetingDatemodalcancel').addEventListener('click', function() {
        meetingCalendarFilterDropdown.style.display = 'none';
    });
    
    document.getElementById('applyMeetingDateFilter').addEventListener('click', function() {
        meetingCalendarFilterDropdown.style.display = 'none';
    });
});

$(document).ready(function() {
    $('#meetingDateFilterTrigger').click(function() {
        $('#meetingCalendarFilterModal').modal('show'); 
    });

    let meetingCurrentDate = new Date();
    let meetingCurrentMonth = new Date(meetingCurrentDate.getFullYear(), meetingCurrentDate.getMonth(), 1);
    let meetingNextMonth = new Date(meetingCurrentDate.getFullYear(), meetingCurrentDate.getMonth() + 1, 1);
    let meetingCustomStartDate = null;
    let meetingCustomEndDate = null;

    // Sample data for meetings - replace with your actual data
    const meetingDates = [
        '2025-06-15',
        '2025-06-20',
        '2025-06-22',
        '2025-07-05',
        '2025-07-10',
        '2025-07-15'
    ];

    function renderMeetingCalendars() {
        renderMeetingCalendar('meetingCurrentMonth', meetingCurrentMonth);
        renderMeetingCalendar('meetingNextMonth', meetingNextMonth);
    }
    
    function renderMeetingCalendar(elementId, date) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        
        const monthNames = ["January", "February", "March", "April", "May", "June", 
                          "July", "August", "September", "October", "November", "December"];
        document.getElementById(`${elementId}Header`).textContent = 
            `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        
        for (let i = 0; i < firstDay.getDay(); i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'day other-month';
            container.appendChild(emptyDay);
        }
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const day = document.createElement('div');
            day.className = 'day';
            day.textContent = i;
            
            const dateStr = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            day.dataset.date = dateStr;
            
            // Add meeting indicator if this date has meetings
            if (meetingDates.includes(dateStr)) {
                const indicator = document.createElement('div');
                indicator.className = 'meeting-indicator';
                day.appendChild(indicator);
                day.style.position = 'relative';
            }
            
            container.appendChild(day);
        }
    }
    
    function updateMeetingSelection(filter, today) {
        const startDateDisplay = document.getElementById('meetingStartDateDisplay');
        const endDateDisplay = document.getElementById('meetingEndDateDisplay');
        let startDate, endDate;
        
        if (['last_7_days', 'last_14_days', 'last_28_days', 'last_30_days', 'custom'].includes(filter)) {
            document.getElementById('meetingFilterSelect').value = filter;
        }
        
        switch(filter) {
            case 'today':
                startDate = new Date(today);
                endDate = new Date(today);
                break;
            case 'yesterday':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 1);
                endDate = new Date(startDate);
                break;
            case 'today_yesterday':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 1);
                endDate = new Date(today);
                break;
            case 'last_7_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 6);
                endDate = new Date(today);
                break;
            case 'last_14_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 13);
                endDate = new Date(today);
                break;
            case 'last_28_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 27);
                endDate = new Date(today);
                break;
            case 'last_30_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 29);
                endDate = new Date(today);
                break;
            case 'this_week':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - startDate.getDay());
                endDate = new Date(today);
                endDate.setDate(endDate.getDate() + (6 - endDate.getDay()));
                break;
            case 'last_week':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - startDate.getDay() - 7);
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 6);
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'maximum':
                startDate = new Date(0);
                endDate = new Date(today);
                break;
            case 'custom':
                if (meetingCustomStartDate && meetingCustomEndDate) {
                    startDate = new Date(meetingCustomStartDate);
                    endDate = new Date(meetingCustomEndDate);
                } else if (meetingCustomStartDate) {
                    startDate = new Date(meetingCustomStartDate);
                    endDate = new Date(meetingCustomStartDate);
                } else {
                    startDate = new Date(today);
                    endDate = new Date(today);
                }
                break;
        }
        
        startDateDisplay.textContent = formatDisplayDate(startDate);
        endDateDisplay.textContent = formatDisplayDate(endDate);
        
        highlightMeetingDateRange(startDate, endDate);
        
        updateMeetingDropdownText(filter, startDate, endDate);
    }
    
    function highlightMeetingDateRange(startDate, endDate) {
        document.querySelectorAll('#meetingCurrentMonth .day, #meetingNextMonth .day').forEach(day => {
            day.classList.remove('selected', 'in-range');
        });
        
        const currentDate = new Date(startDate);
        while (currentDate <= endDate) {
            const dateString = formatDate(currentDate);
            const dayElements = document.querySelectorAll(`.day[data-date="${dateString}"]`);
            
            dayElements.forEach(day => {
                if (currentDate.getTime() === startDate.getTime() || 
                    currentDate.getTime() === endDate.getTime()) {
                    day.classList.add('selected');
                } else {
                    day.classList.add('in-range');
                }
            });
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function formatDisplayDate(date) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
                          "July", "August", "September", "October", "November", "December"];
        return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    }
    
    function updateMeetingDropdownText(filter, startDate, endDate) {
        const selectElement = document.getElementById('meetingDateRangeSelect');
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        if (filter === 'today' || filter === 'yesterday') {
            selectElement.querySelector(`option[value="${filter}"]`).text = 
                `${filter.charAt(0).toUpperCase() + filter.slice(1)} (${formatDisplayDate(startDate)})`;
        } else if (filter === 'custom') {
            const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
            const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
            selectElement.querySelector(`option[value="custom"]`).text = 
                `Custom (${startStr} - ${endStr})`;
        } else {
            const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
            const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
            selectElement.querySelector(`option[value="${filter}"]`).text = 
                `${filter.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')} (${startStr} - ${endStr})`;
        }
        
        // Update the trigger text
        if (filter === 'custom') {
            document.getElementById('selectedMeetingDateRange').textContent = 
                `${monthNames[startDate.getMonth()]} ${startDate.getDate()} - ${monthNames[endDate.getMonth()]} ${endDate.getDate()}`;
        } else {
            document.getElementById('selectedMeetingDateRange').textContent = 
                `${filter.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')}`;
        }
    }

    // Initialize the calendar
    renderMeetingCalendars();
    $('input[name="meetingFilter"][value="today"]').prop('checked', true);
    $('#meetingDateRangeSelect').val('today');
    updateMeetingSelection('today', meetingCurrentDate);

    // Event listeners
    $('.meeting-month-nav-btn.prev-month').on('click', function() {
        meetingCurrentMonth = new Date(meetingCurrentMonth.getFullYear(), meetingCurrentMonth.getMonth() - 1, 1);
        meetingNextMonth = new Date(meetingCurrentMonth.getFullYear(), meetingCurrentMonth.getMonth() + 1, 1);
        renderMeetingCalendars();
        updateMeetingSelection($('input[name="meetingFilter"]:checked').val(), meetingCurrentDate);
    });
    
    $('.meeting-month-nav-btn.next-month').on('click', function() {
        meetingCurrentMonth = new Date(meetingCurrentMonth.getFullYear(), meetingCurrentMonth.getMonth() + 1, 1);
        meetingNextMonth = new Date(meetingCurrentMonth.getFullYear(), meetingCurrentMonth.getMonth() + 1, 1);
        renderMeetingCalendars();
        updateMeetingSelection($('input[name="meetingFilter"]:checked').val(), meetingCurrentDate);
    });
    
    $('input[name="meetingFilter"]').on('change', function() {
        $('#meetingDateRangeSelect').val(this.value);
        updateMeetingSelection(this.value, meetingCurrentDate);
    });
    
    $('#meetingDateRangeSelect').on('change', function() {
        $(`input[name="meetingFilter"][value="${this.value}"]`).prop('checked', true);
        updateMeetingSelection(this.value, meetingCurrentDate);
    });

    $('#meetingFilterSelect').on('change', function() {
        updateMeetingSelection(this.value, meetingCurrentDate);
    });

    // $('.calendar-container').on('click', '.day:not(.other-month)', function() {
    //     const dateStr = $(this).data('date');
    //     const dateParts = dateStr.split('-');
    //     const clickedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
        
    //     $('#meetingCurrentMonth .day, #meetingNextMonth .day').removeClass('selected in-range');
        
    //     if (!meetingCustomStartDate || (meetingCustomStartDate && meetingCustomEndDate)) {
    //         meetingCustomStartDate = clickedDate;
    //         meetingCustomEndDate = null;
    //         $(this).addClass('selected');
    //     } 
    //     else if (meetingCustomStartDate && !meetingCustomEndDate) {
    //         if (clickedDate < meetingCustomStartDate) {
    //             meetingCustomEndDate = new Date(meetingCustomStartDate);
    //             meetingCustomStartDate = clickedDate;
    //         } else {
    //             meetingCustomEndDate = clickedDate;
    //         }
            
    //         const currentDate = new Date(meetingCustomStartDate);
    //         while (currentDate <= meetingCustomEndDate) {
    //             const dateString = formatDate(currentDate);
    //             $(`.day[data-date="${dateString}"]`).each(function() {
    //                 if (currentDate.getTime() === meetingCustomStartDate.getTime() || 
    //                     currentDate.getTime() === meetingCustomEndDate.getTime()) {
    //                     $(this).addClass('selected');
    //                 } else {
    //                     $(this).addClass('in-range');
    //                 }
    //             });
                
    //             currentDate.setDate(currentDate.getDate() + 1);
    //         }
            
    //         $('input[name="meetingFilter"][value="custom"]').prop('checked', true);
    //         $('#meetingDateRangeSelect').val('custom');
    //         $('#meetingFilterSelect').val('custom');
    //         $('#meetingStartDateDisplay').text(formatDisplayDate(meetingCustomStartDate));
    //         $('#meetingEndDateDisplay').text(formatDisplayDate(meetingCustomEndDate));
    //     }
    // });

     $('.meetingcalendar-container').on('click', '.day:not(.other-month)', function() {
    const dateStr = $(this).data('date');
    const dateParts = dateStr.split('-');
    const clickedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
    
    $('#meetingCurrentMonth .day, #meetingNextMonth .day').removeClass('selected in-range');
    
    if (!meetingCustomStartDate || (meetingCustomStartDate && meetingCustomEndDate)) {
        meetingCustomStartDate = clickedDate;
        meetingCustomEndDate = null;
        $(this).addClass('selected');
    } 
    else if (meetingCustomStartDate && !meetingCustomEndDate) {
        if (clickedDate < meetingCustomStartDate) {
            meetingCustomEndDate = new Date(meetingCustomStartDate);
            meetingCustomStartDate = clickedDate;
        } else {
            meetingCustomEndDate = clickedDate;
        }
        
        const currentDate = new Date(meetingCustomStartDate);
        while (currentDate <= meetingCustomEndDate) {
            const dateString = formatDate(currentDate);
            $(`#meetingCurrentMonth .day[data-date="${dateString}"], #meetingNextMonth .day[data-date="${dateString}"]`).each(function() {
                if (currentDate.getTime() === meetingCustomStartDate.getTime() || 
                    currentDate.getTime() === meetingCustomEndDate.getTime()) {
                    $(this).addClass('selected');
                } else {
                    $(this).addClass('in-range');
                }
            });
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        $('input[name="meetingFilter"][value="custom"]').prop('checked', true);
        $('#meetingDateRangeSelect').val('custom');
        $('#meetingFilterSelect').val('custom');
        $('#meetingStartDateDisplay').text(formatDisplayDate(meetingCustomStartDate));
        $('#meetingEndDateDisplay').text(formatDisplayDate(meetingCustomEndDate));
    }
});


    $('#meetingDatemodalcancel').on('click', function() {
        $('#meetingCalendarFilterModal').modal('hide');
    });

    $('#applyMeetingDateFilter').on('click', function() {
        // Get the selected dates
        const startDate = $('#meetingStartDateDisplay').text();
        const endDate = $('#meetingEndDateDisplay').text();
        
        // Here you would typically filter your meetings based on the selected date range
        console.log('Applying meeting date filter from', startDate, 'to', endDate);
        
        // Close the dropdown
        $('#meetingCalendarFilterDropdown').hide();
        
        // You would typically call a function here to update your meeting list
        filterMeetings1(startDate, endDate);
    });
});



//patient filter
$(document).ready(function() {
    let allZones = [];
    let allBranches = [];
    let allMarketers = [];
    
    // Load all zones
    $.ajax({
        url: 'get-all-zones',
        type: 'GET',
        success: function(response) {
            allZones = response;
            const $zoneOptions = $('#patientZoneOptions');
            $zoneOptions.empty();
            
            if(response.length > 0) {
                response.forEach(function(zone) {
                    $zoneOptions.append(
                        `<div data-value="${zone.id}">${zone.name}</div>`
                    );
                });
            } else {
                $zoneOptions.append('<div>No zones available</div>');
            }
        }
    });
    
    // Load all branches
    $.ajax({
        url: 'get-all-branches',
        type: 'GET',
        success: function(response) {
            allBranches = response;
            updatePatientBranchDropdown();
        }
    });
    
    // Load all marketers
    $.ajax({
        url: 'get-all-marketers',
        type: 'GET',
        success: function(response) {
            allMarketers = response;
            updatePatientDropdown();
        }
    });
    
    // Zone selection handler
    $(document).on('click', '#patientZoneOptions div[data-value]', function() {
        const zoneId = $(this).data('value');
        const zoneName = $(this).text();
        $('#patient_zone_id').val(zoneId);
        $('#patient_zone').val(zoneName);
        
        // Filter branches based on selected zone
        updatePatientBranchDropdown(zoneId);
        
        // Load marketers for selected zone via AJAX
        $.ajax({
            url: 'get-marketers-by-zone',
            type: 'GET',
            data: { zone_id: zoneId },
            success: function(response) {
                updatePatientMarketerDropdown(response);
            }
        });
    });
    
    // Branch selection handler
    $(document).on('click', '#patientBranchOptions div[data-value]', function() {
        $('#patient_branch').val($(this).text());
    });
    
    // Marketer selection handler
    $(document).on('click', '#patientMarketerOptions div[data-value]', function() {
        $('#patient_marketer').val($(this).text());
    });
    
    // Update branch dropdown based on zone selection
    function updatePatientBranchDropdown(zoneId = null) {
        const $branchOptions = $('#patientBranchOptions');
        $branchOptions.empty();
        
        let filteredBranches = zoneId 
            ? allBranches.filter(branch => branch.zone_id == zoneId)
            : allBranches;
            
        if(filteredBranches.length > 0) {
            filteredBranches.forEach(function(branch) {
                $branchOptions.append(
                    `<div data-value="${branch.name}" data-zone="${branch.zone_id}">${branch.name}</div>`
                );
            });
        } else {
            $branchOptions.append(zoneId 
                ? '<div>No branches in this zone</div>'
                : '<div>No branches available</div>');
        }
    }
    
    // Update marketer dropdown with provided marketers
    function updatePatientMarketerDropdown(marketers = null) {
        const $marketerOptions = $('#patientMarketerOptions');
        $marketerOptions.empty();
        
        if (marketers && marketers.length > 0) {
            marketers.forEach(function(marketer) {
                $marketerOptions.append(
                    `<div data-value="${marketer.user_fullname}">${marketer.user_fullname}</div>`
                );
            });
        } else {
            // alert(1);
            $marketerOptions.append('<div>No marketers available</div>');
        }
    }


     function updatePatientDropdown(zoneId = null) {
        const $marketerOptions = $('#patientMarketerOptions');
        $marketerOptions.empty();
        
        let filteredMarketers = zoneId ? allMarketers.filter(marketer => marketer.zone_id == zoneId): allMarketers;
        
        if(filteredMarketers.length > 0) {
            filteredMarketers.forEach(function(marketer) {
                $marketerOptions.append(
                    `<div data-value="${marketer.user_fullname}">${marketer.user_fullname}</div>`
                );
            });
        } else {
            $marketerOptions.append(zoneId
                ? '<div>No marketers in this zone</div>'
                : '<div>No marketers available</div>');
        }
    }

    // Search functionality for all dropdowns
    $('.marketervalues_search_patient').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdownId = $(this).next('.dropdown-options').attr('id');
        
        $('#' + dropdownId + ' div').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    });
});


//patient date filter
document.addEventListener('DOMContentLoaded', function() {
    const patientDateFilterTrigger = document.getElementById('patientDateFilterTrigger');
    const patientCalendarFilterDropdown = document.getElementById('patientCalendarFilterDropdown');
    
    patientDateFilterTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = patientCalendarFilterDropdown.style.display === 'block';
        patientCalendarFilterDropdown.style.display = isVisible ? 'none' : 'block';
    });
    
    document.addEventListener('click', function() {
        patientCalendarFilterDropdown.style.display = 'none';
    });
    
    patientCalendarFilterDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('patientDatemodalcancel').addEventListener('click', function() {
        patientCalendarFilterDropdown.style.display = 'none';
    });
    
    document.getElementById('patientApplyDateFilter').addEventListener('click', function() {
        patientCalendarFilterDropdown.style.display = 'none';
    });
});

$(document).ready(function() {
    $('#patientDateFilterTrigger').click(function() {
        $('#patientCalendarFilterModal').modal('show'); 
    });

    let patientCurrentDate = new Date();
    let patientCurrentMonth = new Date(patientCurrentDate.getFullYear(), patientCurrentDate.getMonth(), 1);
    let patientNextMonth = new Date(patientCurrentDate.getFullYear(), patientCurrentDate.getMonth() + 1, 1);
    let patientCustomStartDate = null;
    let patientCustomEndDate = null;

    function renderPatientCalendars() {
        renderPatientCalendar('patientCurrentMonth', patientCurrentMonth);
        renderPatientCalendar('patientNextMonth', patientNextMonth);
    }
    
    function renderPatientCalendar(elementId, date) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        
        const monthNames = ["January", "February", "March", "April", "May", "June", 
                          "July", "August", "September", "October", "November", "December"];
        document.getElementById(`${elementId}Header`).textContent = 
            `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        
        for (let i = 0; i < firstDay.getDay(); i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'patient-day patient-other-month';
            container.appendChild(emptyDay);
        }
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const day = document.createElement('div');
            day.className = 'patient-day';
            day.textContent = i;
            day.dataset.date = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            container.appendChild(day);
        }
    }
    
    function updatePatientSelection(filter, today) {
        const startDateDisplay = document.getElementById('patientStartDateDisplay');
        const endDateDisplay = document.getElementById('patientEndDateDisplay');
        let startDate, endDate;
        
        if (['last_7_days', 'last_14_days', 'last_28_days', 'last_30_days', 'custom'].includes(filter)) {
            document.getElementById('patientFilterSelect').value = filter;
        }
        
        switch(filter) {
            case 'today':
                startDate = new Date(today);
                endDate = new Date(today);
                break;
            case 'yesterday':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 1);
                endDate = new Date(startDate);
                break;
            case 'today_yesterday':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 1);
                endDate = new Date(today);
                break;
            case 'last_7_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 6);
                endDate = new Date(today);
                break;
            case 'last_14_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 13);
                endDate = new Date(today);
                break;
            case 'last_28_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 27);
                endDate = new Date(today);
                break;
            case 'last_30_days':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - 29);
                endDate = new Date(today);
                break;
            case 'this_week':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - startDate.getDay());
                endDate = new Date(today);
                endDate.setDate(endDate.getDate() + (6 - endDate.getDay()));
                break;
            case 'last_week':
                startDate = new Date(today);
                startDate.setDate(startDate.getDate() - startDate.getDay() - 7);
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 6);
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'maximum':
                startDate = new Date(0);
                endDate = new Date(today);
                break;
            case 'custom':
                if (patientCustomStartDate && patientCustomEndDate) {
                    startDate = new Date(patientCustomStartDate);
                    endDate = new Date(patientCustomEndDate);
                } else if (patientCustomStartDate) {
                    startDate = new Date(patientCustomStartDate);
                    endDate = new Date(patientCustomStartDate);
                } else {
                    startDate = new Date(today);
                    endDate = new Date(today);
                }
                break;
        }
        
        startDateDisplay.textContent = formatPatientDisplayDate(startDate);
        endDateDisplay.textContent = formatPatientDisplayDate(endDate);
        
        highlightPatientDateRange(startDate, endDate);
        
        updatePatientDropdownText(filter, startDate, endDate);
    }
    
    function highlightPatientDateRange(startDate, endDate) {
        document.querySelectorAll('.patient-day').forEach(day => {
            day.classList.remove('selected', 'in-range');
        });
        
        const currentDate = new Date(startDate);
        while (currentDate <= endDate) {
            const dateString = formatPatientDate(currentDate);
            const dayElements = document.querySelectorAll(`.patient-day[data-date="${dateString}"]`);
            
            dayElements.forEach(day => {
                if (currentDate.getTime() === startDate.getTime() || 
                    currentDate.getTime() === endDate.getTime()) {
                    day.classList.add('selected');
                } else {
                    day.classList.add('in-range');
                }
            });
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }
    
    function formatPatientDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function formatPatientDisplayDate(date) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
                          "July", "August", "September", "October", "November", "December"];
        return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    }
    
    function formatPatientDateForServer(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    function updatePatientDropdownText(filter, startDate, endDate) {
        const selectElement = document.getElementById('patientDateRangeSelect');
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        if (filter === 'today' || filter === 'yesterday') {
            selectElement.querySelector(`option[value="${filter}"]`).text = 
                `${filter.charAt(0).toUpperCase() + filter.slice(1)} (${formatPatientDisplayDate(startDate)})`;
        } else if (filter === 'custom') {
            const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
            const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
            selectElement.querySelector(`option[value="custom"]`).text = 
                `Custom (${startStr} - ${endStr})`;
        } else {
            const startStr = `${monthNames[startDate.getMonth()]} ${startDate.getDate()}`;
            const endStr = `${monthNames[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;
            selectElement.querySelector(`option[value="${filter}"]`).text = 
                `${filter.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')} (${startStr} - ${endStr})`;
        }
    }

    // Initialize the calendar
    renderPatientCalendars();
    $('input[name="patientFilter"][value="today"]').prop('checked', true);
    $('#patientDateRangeSelect').val('today');
    updatePatientSelection('today', patientCurrentDate);

    // Event listeners
    $('.patient-prev-month').on('click', function() {
        patientCurrentMonth = new Date(patientCurrentMonth.getFullYear(), patientCurrentMonth.getMonth() - 1, 1);
        patientNextMonth = new Date(patientCurrentMonth.getFullYear(), patientCurrentMonth.getMonth() + 1, 1);
        renderPatientCalendars();
        updatePatientSelection($('input[name="patientFilter"]:checked').val(), patientCurrentDate);
    });
    
    $('.patient-next-month').on('click', function() {
        patientCurrentMonth = new Date(patientCurrentMonth.getFullYear(), patientCurrentMonth.getMonth() + 1, 1);
        patientNextMonth = new Date(patientCurrentMonth.getFullYear(), patientCurrentMonth.getMonth() + 1, 1);
        renderPatientCalendars();
        updatePatientSelection($('input[name="patientFilter"]:checked').val(), patientCurrentDate);
    });
    
    $('input[name="patientFilter"]').on('change', function() {
        $('#patientDateRangeSelect').val(this.value);
        updatePatientSelection(this.value, patientCurrentDate);
    });
    
    $('#patientDateRangeSelect').on('change', function() {
        $(`input[name="patientFilter"][value="${this.value}"]`).prop('checked', true);
        updatePatientSelection(this.value, patientCurrentDate);
    });

    $('#patientFilterSelect').on('change', function() {
        updatePatientSelection(this.value, patientCurrentDate);
    });

    $('.patient-calendar-container').on('click', '.patient-day:not(.patient-other-month)', function() {
        const dateStr = $(this).data('date');
        const dateParts = dateStr.split('-');
        const clickedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
        
        $('.patient-day').removeClass('selected in-range');
        
        if (!patientCustomStartDate || (patientCustomStartDate && patientCustomEndDate)) {
            patientCustomStartDate = clickedDate;
            patientCustomEndDate = null;
            $(this).addClass('selected');
        } 
        else if (patientCustomStartDate && !patientCustomEndDate) {
            if (clickedDate < patientCustomStartDate) {
                patientCustomEndDate = new Date(patientCustomStartDate);
                patientCustomStartDate = clickedDate;
            } else {
                patientCustomEndDate = clickedDate;
            }
            
            const currentDate = new Date(patientCustomStartDate);
            while (currentDate <= patientCustomEndDate) {
                const dateString = formatPatientDate(currentDate);
                $(`.patient-day[data-date="${dateString}"]`).each(function() {
                    if (currentDate.getTime() === patientCustomStartDate.getTime() || 
                        currentDate.getTime() === patientCustomEndDate.getTime()) {
                        $(this).addClass('selected');
                    } else {
                        $(this).addClass('in-range');
                    }
                });
                
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            $('input[name="patientFilter"][value="custom"]').prop('checked', true);
            $('#patientDateRangeSelect').val('custom');
            $('#patientFilterSelect').val('custom');
            $('#patientStartDateDisplay').text(formatPatientDisplayDate(patientCustomStartDate));
            $('#patientEndDateDisplay').text(formatPatientDisplayDate(patientCustomEndDate));
        }
    });

    $('#patientDatemodalcancel').on('click', function() {
        $('#patientCalendarFilterModal').modal('hide');
    });

    $('#patientApplyDateFilter').on('click', function() {
        const startDate = $('#patientStartDateDisplay').text();
        const endDate = $('#patientEndDateDisplay').text();
        applyPatientDateFilter(startDate, endDate);
        $('#patientCalendarFilterModal').modal('hide');
    });
});


</script>



 <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_x0UBsCWEAD3kfXSUa9h0ASzew0M9cLw&callback=initMap"> </script>

        <!-- [ Main Content ] end -->
        @include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->

</html>