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
{{-- <link rel="stylesheet" href="{{ asset('/assets/css/enhanced-discount-form-styles.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('/assets/css/statistics.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    .progress-bar2 {
    height: 30px;
    width: 0%;
    background-color: #4caf50;
    color: white;
    font-weight: bold;
    text-align: center;
    line-height: 30px;
    transition: width 0.3s ease;
}
    </style>
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
    scrollbar-color: #efdaec #f1f1f1;
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
    border: 2px solid #6a6ee4;
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

/* .multiselect-container {
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

.pc-container .page-header+.row {
    padding-top: -5px;
    margin-top: -22px;
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
.dropdown.active .dropdown-options {
    display: block;
}

.dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
} */

.btn-primary.d-inline-flex:hover {
    background-color: rgb(255, 255, 255) !important;
    /* Change to your desired hover color */
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
}
.form-control {
    height: 30px;
}
.badge {
    cursor: pointer;
    }
    .button-container {
        display: flex;
        gap: 10px;
        margin-top: 7px;
    }
</style>

<style>
    .form-border {
    border: 1px solid #000;
    padding: 20px;
    }
    .form-title {
    font-weight: bold;
    font-size: 20px;
    }
    .form-subtitle {
    font-size: 14px;
    }
    .label-title {
    font-weight: bold;
    }
    .dotted-line {
    border-bottom: 1px dotted #000;
    padding-bottom: 5px;
    margin-bottom: 10px;
    }
    .form-row-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    }
    .form-label-col {
    width: 20%;
    font-weight: bold;
    }
    .form-colon {
    width: 2%;
    text-align: center;
    }
    .form-input {
    width: 100%;
    border: none;
    border-bottom: 1px dotted #000;
    }
    .form-input2 {
    width: 44%;
    border: none;
    border-bottom: 1px dotted #000;
    }
    .form-input3 {
    width: 40%;
    border: none;
    border-bottom: 1px dotted #000;
    }
    .signatures .col {
    text-align: left;
    font-weight: bold;
    border-bottom: 1px dotted #000;
    padding-top: 20px;
    }

    .avatar-upload {
        position: relative;
        display: inline-flex;
        margin: 0px 0px 0px 70px;
        .avatar-edit {
            position: absolute;
            left: 5px;
            z-index: 1;
            top: -10px;
            input {
                display: none;
                + label {
                    display: inline-block;
                    width: 34px;
                    height: 34px;
                    margin-bottom: 0;
                    border-radius: 100%;
                    background: #FFFFFF;
                    border: 1px solid transparent;
                    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
                    cursor: pointer;
                    font-weight: normal;
                    transition: all .2s ease-in-out;
                    &:hover {
                        background: #f1f1f1;
                        border-color: #d6d6d6;
                    }
                    &:after {
                        content: "\f093";
                        font-family: 'FontAwesome';
                        color: #757575;
                        position: absolute;
                        top: 10px;
                        left: 0;
                        right: 0;
                        text-align: center;
                        margin: auto;
                    }
                }
            }
        }
        .avatar-preview {
            width: 230px;
            height: 45px;
            position: relative;
            border-radius: 10px;
            border: 6px solid #F8F8F8;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
            > div {
                width: 100%;
                height: 100%;
                border-radius: 10px;
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
            }
        }
    }
    .signature-option-group{
        margin-bottom:5px;
    }
    .action-icons {
        display: inline-flex;
        gap: 8px;              /* space between ✔ and ✖ */
        align-items: center;
    }

.approve-btn,
.reject-btn {
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    user-select: none;
}

.approve-btn {
    color: green;
}

.reject-btn {
    color: red;
}

.approve-btn:hover,
.reject-btn:hover {
    opacity: 0.7;
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

  <div class="col-sm-12">
                    <div class="card-body pc-component btn-page">
                        <div class="modal fade" id="exampleModaluser" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                        <h5 class="modal-title" id="exampleModalLabel"
                                            style="color: #ffffff;font-size: 12px;">Discount Form Document</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            style="background-color: #ffffff;" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" class="userid" name="userid" id="userid">


                                            <div class="form-border">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                    <div class="form-title">Dr. ARAVIND’s IVF</div>
                                                    <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
                                                    </div>
                                                    <div class="text-center align-self-center px-3 border"><strong>DISCOUNT FORM</strong></div>
                                                    <div class="align-self-start">
                                                        <!-- S. No :  <input type="text" id="sno" style="border: none; border-bottom: 1px dotted #000;width: 100%;"> -->
                                                    </div>
                                                </div>

                                            <!-- Form Rows -->
                                                <div class="form-row-line mb-2">
                                                    <div class="form-label-col">Branch Name</div><div class="form-colon">:</div>
                                                    <span style="font-size: 10px; color: red;" class="error_location errorss"></span>
                                                    @php
                                                    $locations =
                                                    App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.id')->get();
                                                    @endphp
                                                    <div class="dropdown">
                                                        <input type="text" class="form-control searchInput single_search locationsearch"
                                                            placeholder="Select Branch"
                                                            style="color: #505050 !important;width: 100%;" required
                                                            name="zone_id" id="zone_id" autocomplete="off">
                                                        <div class="dropdown-options">
                                                            @if($locations)
                                                            @foreach ($locations as $location)
                                                            <div class="dropdown-item-loc"
                                                                data-value="{{ $location->id }}">{{ $location->name}}
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <!-- <input type="text" class="form-input" id="branchname"> -->
                                                </div>
                                            <!-- <div class="form-row-line mb-2">
                                                <div class="form-label-col">Discount Serial Number</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="dissno">
                                            </div> -->

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Wife Name</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_wifename errorss"></span>
                                                <input type="text" class="form-input2" id="wife_name">
                                                <div class="form-label-col" style="padding-left: 15px;width: 15%;">Wife MRD No</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_wifemrd errorss"></span>
                                                <input type="text" class="form-input3" id="wifemrdno">
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Husband Name</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_husname errorss"></span>
                                                <input type="text" class="form-input2" id="husband_name">
                                                <div class="form-label-col" style="padding-left: 15px;width: 15%;">Husband MRD No</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_husmrd errorss"></span>
                                                <input type="text" class="form-input3" id="husbandmrdno">
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Service Name</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_treatment errorss"></span>
                                                <input type="text" class="form-input" id="service_name">
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Total Bill Value</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_total errorss"></span>
                                                <input type="text" class="form-input" id="totalbill">
                                            </div>
                                            <div class="form-row-line mb-2">

                                                <div class="form-label-col">Discount Expected Request</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_exp errorss"></span>
                                                <div class="form-label-col" style="width: 10%;padding-left: 10px;">
                                            <input type="radio" id="examount" class="form-check-input" name="expectdis" value="Amount">
                                              <label for="examount">&#8377;</label>
                                              <input type="radio" id="expercentage" class="form-check-input" name="expectdis" value="Percentage">
                                              <label for="expercentage">%</label></div>
                                                <input type="text" class="form-input" style="width:30%" id="ex_discount_display">
                                                <span class="postdisamount" id="ex_discount_value" style="margin-left: 10px; font-weight: bold;"></span>
                                                <div class="form-label-col" style="width: 55%;padding-left: 10px;">
                                                <input type="radio" id="op" class="form-check-input" name="request" value="OP">
                                              <label for="html">op</label>
                                              <input type="radio" id="ip" class="form-check-input" name="request" value="IP">
                                              <label for="ip">IP</label>
                                              <input type="radio" id="pharmacy" class="form-check-input" name="request" value="Pharmacy">
                                              <label for="pharmacy">Pharmacy</label>
                                                </div>
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Post Discount</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_post errorss"></span>
                                            <div class="form-label-col" style="width: 10%; padding-left: 10px;">
                                            <input type="radio" class="form-check-input" id="postamount" name="postdis" value="Amount">&nbsp;
                                            <label for="postamount">&#8377;</label>&nbsp;
                                            <input type="radio" class="form-check-input" id="postpercentage" name="postdis" value="Percentage">&nbsp;
                                            <label for="postpercentage">%</label></div>
                                            <input type="text" class="form-input2" id="post_discount_display" style="width: 30%;">
                                            <span class="postdisamount" id="post_discount_value" style="margin-left: 10px; font-weight: bold;width: 55%;"></span>
                                            </div>

                                                <div class="form-row-line ">
                                                    <div class="form-label-col">Patient Ph. No</div><div class="form-colon">:</div>
                                                    <span style="font-size: 10px; color: red;" class="error_patient errorss"></span>
                                                    <input type="text" class="form-input3" style="width:99%;" id="patientph">
                                                </div>
                                            <div class="form-row-line ">
                                                <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_counsel errorss"></span>
                                                <input type="text" class="form-input" id="counselled_by">
                                            </div>

                                            <!-- Signature Section -->
                                            <div class="row signatures ">
                                            <div class="col">Wife Sign
                                                <div class="signature-option-group" data-target="wife">
                                                    <label><input type="radio" class="form-check-input" name="wife-signature" value="upload" checked> Upload Image</label>
                                                    <label><input type="radio" class="form-check-input" name="wife-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="wife-upload">
                                                    <i class="fa fa-close clear-sign" data-target="wife" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" />
                                                        <label for="imageUpload"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="imagePreview"></div>
                                                    </div>
                                                </div>

                                                <div class="digital-sign" id="wife-sign" style="display:none;">
                                                    <canvas id="wifeCanvas" width="300" height="50" style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger" onclick="clearCanvas('wifeCanvas')">Clear</button>
                                                </div>
                                            </div>

                                                <div class="col">Husband Sign
                                                <div class="signature-option-group" data-target="husband">
                                                    <label><input type="radio" class="form-check-input" name="husband-signature" value="upload" checked> Upload Image</label>
                                                    <label><input type="radio" class="form-check-input" name="husband-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="husband-upload">
                                                    <i class="fa fa-close clear-sign" data-target="husband" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="husbandsignimg" accept=".png, .jpg, .jpeg" />
                                                        <label for="husbandsignimg"></label>
                                                    </div>
                                                    <div class="avatar-preview"><div id="husimgPreview"></div></div>
                                                </div>

                                                <div class="digital-sign" id="husband-sign" style="display:none;">
                                                    <canvas id="husbandCanvas" width="300" height="50" style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger" onclick="clearCanvas('husbandCanvas')">Clear</button>
                                                </div>
                                            </div>

                                            </div>
                                            <div class="row signatures">

                                                <div class="col">Dr Sign
                                                <div class="signature-option-group" data-target="dr">
                                                    <label><input type="radio" class="form-check-input" name="dr-signature" value="upload" checked> Upload Image</label>
                                                    <label><input type="radio" class="form-check-input" name="dr-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="dr-upload">
                                                    <i class="fa fa-close clear-sign" data-target="dr" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="drsignimg" accept=".png, .jpg, .jpeg" />
                                                        <label for="drsignimg"></label>
                                                    </div>
                                                    <div class="avatar-preview"><div id="drimgPreview"></div></div>
                                                </div>

                                                <div class="digital-sign" id="dr-sign" style="display:none;">
                                                    <canvas id="drCanvas" width="300" height="50" style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger" onclick="clearCanvas('drCanvas')">Clear</button>
                                                </div>
                                            </div>

                                                <div class="col">CC Sign
                                                <div class="signature-option-group" data-target="cc">
                                                    <label><input type="radio" class="form-check-input" name="cc-signature" value="upload" checked> Upload Image</label>
                                                    <label><input type="radio" class="form-check-input" name="cc-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="cc-upload">
                                                    <i class="fa fa-close clear-sign" data-target="cc" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="ccsignimg" accept=".png, .jpg, .jpeg" />
                                                        <label for="ccsignimg"></label>
                                                    </div>
                                                    <div class="avatar-preview"><div id="ccimgPreview"></div></div>
                                                </div>

                                                <div class="digital-sign" id="cc-sign" style="display:none;">
                                                    <canvas id="ccCanvas" width="300" height="50" style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger" onclick="clearCanvas('ccCanvas')">Clear</button>
                                                </div>
                                            </div>

                                                <div class="col">Admin Sign
                                                <div class="signature-option-group" data-target="admin">
                                                    <label><input type="radio" class="form-check-input" name="admin-signature" value="upload" checked> Upload Image</label>
                                                    <label><input type="radio" class="form-check-input" name="admin-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="admin-upload">
                                                    <i class="fa fa-close clear-sign" data-target="admin" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="adminsignimg" accept=".png, .jpg, .jpeg" />
                                                        <label for="adminsignimg"></label>
                                                    </div>
                                                    <div class="avatar-preview"><div id="adminimgPreview"></div></div>
                                                </div>

                                                <div class="digital-sign" id="admin-sign" style="display:none;">
                                                    <canvas id="adminCanvas" width="300" height="50" style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger" onclick="clearCanvas('adminCanvas')">Clear</button>
                                                </div>
                                            </div>
                                            </div>

                                            <!-- Final Section -->
                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Final Authorised Amount</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="final_amount" readonly value="">
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">B.R. No.</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_brno errorss"></span>
                                                <input type="text" class="form-input" id="branch_no">
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Authorised By</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_authouris errorss"></span>
                                                <input type="text" class="form-input" id="authourised_by">
                                            </div>
                                            <div class="form-row-line ">
                                                <div class="form-label-col">Final Approved By</div><div class="form-colon">:</div>
                                                <span style="font-size: 10px; color: red;" class="error_approve errorss"></span>
                                                <input type="text" class="form-input" id="approveded_by">
                                            </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                                id="close-button" class="btn btn-outline-danger"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="submit_discountform"
                                                style="height: 34px;width: 133px;font-size: 12px;"
                                                class="btn btn-outline-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row ">
                        <div class="col-md-9 col-sm-9">
                            <input type="text" id="icon-search" class="form-control mb-4" style="
                                height: 35px;
                                font-size: 11px;
                            " placeholder="Search">
                        </div>
                        <div class="col-md-3 col-sm-3 ">
                            <a href="#" class="btn btn-primary d-inline-flex  gap-2 editbtn_user"
                                data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
                                height: 34px;
                                width: 133px;
                                font-size: 12px;
                                        background-color: #6a6ee4;
                                        --bs-btn-border-color: #6a6ee4;"><i class="ti ti-plus f-18"></i>Document</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <!-- [ Main Content ] end -->
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card-body border-bottom pb-0">
                        <div class="d-flex  justify-content-between">
                        </div>
                        <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-1-pane" type="button" role="tab"
                                    aria-controls="analytics-tab-1-pane" aria-selected="true">Discount Form
                                    Documents</button>
                            </li>
                             <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link" id="analytics-tab-2" data-bs-toggle="tab" data-bs-target="#analytics-tab-2-pane"
                                     type="button" role="tab" aria-controls="analytics-tab-2-pane" aria-selected="false">Saved Discount Form</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><br>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel"
                    aria-labelledby="analytics-tab-1" tabindex="0">
                    <div class="row">
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="myreportrange"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>

                                <span style="display:none;" id="mydateviewsall"></span>
                            </div>
                        </div>

                        @php
                        use App\Models\TblZonesModel;
                        use App\Models\TblLocationModel;

                        $locations = null;
                        $zones = null;

                        if ($admin->access_limits == 1) {
                            // Access limit 1 → All zones
                            $zones = TblZonesModel::select('name', 'id')->get();
                            $locations = TblLocationModel::select('name', 'id','zone_id')->get();

                        } else if ($admin->access_limits == 2) {
                            // Access limit 2 → User zone only + multi-locations under that zone
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

                                // Get all locations under these zones
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->where('zone_id', $admin->zone_id)
                                    ->get();

                                // Also include the multi-locations specifically (in case they're from different zones)
                                $specificLocations = TblLocationModel::select('name', 'id','zone_id')
                                    ->whereIn('id', $multiLocations)
                                    ->get();

                                // Merge collections and remove duplicates
                                $locations = $locations->merge($specificLocations)->unique('id');
                            } else {
                                // If no multi_location, get locations from user's zone only
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->where('zone_id', $admin->zone_id)
                                    ->get();
                                $zoneIds = [$admin->zone_id];
                            }

                            // Get zones based on collected zone IDs
                            $zones = TblZonesModel::select('name', 'id')
                                ->whereIn('id', $zoneIds)
                                ->get();

                        } else {
                            // Access limit 3 → User branch only + multi-locations
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
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->whereIn('id', $branchIds)
                                    ->get();
                            } else {
                                // If no multi_location, get only the user's branch
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->where('id', $admin->branch_id)
                                    ->get();
                            }
                            // Get zone for the user (for zone dropdown if needed)
                            $zoneIds = $locations->pluck('zone_id')->unique()->toArray();
                            $zones = TblZonesModel::select('name', 'id')
                                ->whereIn('id', $zoneIds)
                                ->get();
                        }
                        @endphp


                        {{-- @php $zone = App\Models\TblZonesModel::select('tblzones.name','tblzones.id')->get(); @endphp --}}
                        <div class="col-xl-2 col-md-2" >
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch"
                                        name="tblzones.name" id="dis_zone_views" placeholder="Select Zone"
                                        autocomplete="off">
                                        <input type="hidden" id="lic_zone_id">
                                    <div class="dropdown-options single_search selectzone sec_options_marketers">
                                        @if($zones)
                                        @foreach($zones as $zonename)
                                        <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @php $locat = App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.zone_id')->get(); @endphp --}}
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch"
                                        name="tbl_locations.name" id="dis_loc_views" placeholder="Select Branch"
                                        autocomplete="off">
                                    <div class="dropdown-options single_search sec_options_marketers" id="getlocation">

                                        @if($locations)
                                        @foreach($locations as $location)
                                        <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">{{$location->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search form-control documentdatasearch" name="phid" id="dis_mrd_views" placeholder="Enter the MRD" autocomplete="off">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2" hidden>
                            <div class="">
                                <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_mail_filter"  style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4; --bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>

                    <p style=" margin-top: -9px;" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="mycounts">0</span> Rows for <span
                                id="mydateallviews">Last 30 days</span></span>
                        <span class="my_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search
                            :</span>
                            <span style="cursor: pointer;" id="diszone_search"
                            class="badge bg-success value_views_mysearch"></span>
                            <span style="cursor: pointer;" id="disbranch_search"
                            class="badge bg-success value_views_mysearch"></span>
                              <span style="cursor: pointer;" id="dismrdno_search"
                            class="badge bg-success value_views_mysearch"></span>


                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-danger clear_my_views" style="display:none;">Clear all</span>
                    </p><br>

                    <!-- Progress Bar Container -->
                    <div id="loaderContainer" style="display:none; width: 100%; margin-top: 10px;">
                        <div id="progressBar" style="width: 0%; height: 20px; background: #4caf50; text-align: center; color: white;">
                            0%
                        </div>
                    </div>

                    <input type="hidden" id="hidden_wifemrd">
                    <input type="hidden" id="hidden_husmrd">
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                        <!-- <th class="thview">Id</th> -->
                                            <th class="thview">S.No</th>
                                            <th class="thview">Zone Name</th>
                                             <th class="thview">Branch Name</th>
                                            <th class="thview">Wife MRD No / Name</th>
                                            <th class="thview">Husband MRD No / Name</th>
                                            <th class="thview">Service Name</th>
                                            <th class="thview">Total Bill Value</th>
                                            <th class="thview">Discount Expected Request</th>
                                            <th class="thview">Post Discount</th>
                                            <th class="thview">Counselled By</th>
                                            <th class="thview">Authorised By</th>
                                            <th class="thview">Final Approved By</th>
                                            <th class="thview">B.R. No.</th>
                                            <th class="thview">Final Authorised Amount</th>
                                            <th class="thview" colspan="2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loader_row">
                                    <tr>
                                    <td colspan="15">
                                    <div id="loader-container">
                                    <div id="progress-bar">Loading: 0%</div>
                                    <div id="error-message" style="color: red; display: none;"></div>
                                    </div>
                                    </td>
                                    </tr>
                                    </tbody>
                                    <tbody id="document_tbl">
                                        <tr>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelectdocument">
                                        <option>3</option>
                                        <option>5</option>
                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="paginationdocument"></div>
                            </div>
                        </div>
                        </div></div>
                         <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">

                    <div class="row">
                        <div class="statistics-container">
                            <div class="statistics-header">
                                <h2>
                                    <i class="fas fa-chart-bar"></i>
                                    Discount Form Statistics
                                </h2>
                            </div>
                            
                            <div class="statistics-cards" id="statisticsCards">
                                <!-- Cards will be dynamically populated via JavaScript -->
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="mysavereportrange"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>

                                <span style="display:none;" id="mydateviewsallsave"></span>
                            </div>
                        </div>

                        {{-- @php $zone = App\Models\TblZonesModel::select('tblzones.name','tblzones.id')->get(); @endphp --}}
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search savedatasearch"
                                        name="tblzones.name" id="save_zone_views" placeholder="Select Zone"
                                        autocomplete="off">
                                    <input type="hidden" id="dissave_zone_id">
                                    <div class="dropdown-options single_search selectzonesave savedata_options">
                                        @if($zones)
                                        @foreach($zones as $zonename)
                                        <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @php $locat =
                        App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.zone_id')->get();
                        @endphp --}}
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search savedatasearch"
                                        name="tbl_locations.name" id="save_loc_views" placeholder="Select Branch"
                                        autocomplete="off">
                                    <div class="dropdown-options single_search savedata_options" id="getlocationsave">
                                        @if($locations)
                                        @foreach($locations as $location)
                                        <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">
                                            {{$location->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" 
                                        class="searchInput  documentdatasearch"
                                        name="tblzones.name" 
                                        id="dis_zone_views" 
                                        placeholder="Select Zone"
                                        autocomplete="off">
                                    <input type="hidden" id="lic_zone_id">
                                    <div class="dropdown-options  selectzone sec_options_marketers">
                                        @if($zones)
                                            @foreach($zones as $zonename)
                                                <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" 
                                        class="searchInput  documentdatasearch"
                                        name="tbl_locations.name" 
                                        id="dis_loc_views" 
                                        placeholder="Select Branch"
                                        autocomplete="off">
                                    <div class="dropdown-options  sec_options_marketers" id="getlocation">
                                        @if($locations)
                                            @foreach($locations as $location)
                                                <div data-value="{{$location->id}}" data-type="{{$location->zone_id}}">{{$location->name}}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-xl-2 col-md-2" hidden>
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search form-control savedatasearch"
                                        name="phid" id="save_mrd_views" placeholder="Enter the MRD" autocomplete="off">

                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-xl-2 col-md-2">
                        <div class="" hidden>
                            <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas_mail_filter" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;
                                --bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                      </div>

                        <p style="margin-top: -25px;" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="billsavecounts">0</span> Rows for <span
                            id="mydateallviewssave">All</span></span>
                    <span class="my_search_saveview " style="color: #e40505;font-size: 12px;font-weight: 600;">Search
                        :</span>
                    <span style="cursor: pointer;" id="savezone_search"
                        class="badge bg-success value_save_mysearch"></span>
                    <span style="cursor: pointer;" id="savebranch_search"
                        class="badge bg-success value_save_mysearch "></span>
                    <span style="cursor: pointer;" id="savemrdno_search"
                    class="badge bg-success value_save_mysearch "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-danger clear_my_saveviews " style="display:none;">Clear all</span>
                </p><br>


                <div class="row">
                <div class="col-sm-12">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="tbl">
                                <thead class="thd">
                                    <tr class="trview">
                                        <th class="thview">S.No</th>
                                            <th class="thview">Date</th>
                                            <th class="thview">Zone Name</th>
                                             <th class="thview">Branch Name</th>
                                            <th class="thview">Wife MRD No / Name</th>
                                            <th class="thview">Husband MRD No / Name</th>
                                            <th class="thview">Service Name</th>
                                            <th class="thview">Total Bill Value</th>
                                            <th class="thview">Discount Expected Request</th>
                                            <th class="thview">Post Discount</th>
                                            <th class="thview">Counselled By</th>
                                            <th class="thview">Authorised By</th>
                                            <th class="thview">Final Approved By</th>
                                            <th class="thview">B.R. No.</th>
                                            <th class="thview">Final Authorised Amount</th>
                                            <th class="thview">Uploaded By</th>
                                            @if($admin->access_limits == 1)
                                                <th class="thview">Admin Approved</th>
                                                <th class="thview">Zonal Approved</th>
                                                <th class="thview">Audit Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                                @elseif($admin->access_limits == 2)
                                                <th class="thview">Admin Approved</th>
                                                <th class="thview">Audit Approved</th>
                                                <th class="thview">Final Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                                @elseif($admin->access_limits == 3)
                                                <th class="thview">Zonal Approved</th>
                                                <th class="thview">Audit Approved</th>
                                                <th class="thview">Final Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                                @elseif($admin->access_limits == 4)
                                                <th class="thview">Admin Approved</th>
                                                <th class="thview">Zonal Approved</th>
                                                <th class="thview">Final Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                            @endif

                                            <th class="thview">Print</th>
                                    </tr>
                                </thead>
                                <tbody id="loader_row">
                                    <tr>
                                        <td colspan="15">
                                            <div id="loader-container">
                                                <div class="progress-bar2">Loading: 0%</div>
                                                <div id="error-message" style="color: red; display: none;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody id="sveddata_tbl">
                                    <tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="footer">
                            <div>
                                Items per page:
                                <select id="itemsPerPageSelectsave">
                                    <option>3</option>
                                    <option>5</option>
                                    <option>10</option>
                                    <option>15</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </div>
                            <div class="pagination" id="paginationsavedata"></div>

                        </div>
                    </div>
                </div>
                </div>
                <div id="printSection" style="display: none;"></div>

                <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
                    <div class="offcanvas-body p-0">
                        <div id="ecom-filter" class="collapse collapse-horizontal show">
                            <div class="ecom-filter">
                                <div class="card">
                                    <!-- Sticky Header -->
                                    <div
                                        class="card-header d-flex  justify-content-between sticky-top bg-white">
                                        <h5>Documents Filter</h5>
                                        <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                            data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filter">
                                            <i class="ti ti-x f-20"></i>
                                        </a>
                                    </div>
                                    <!-- Scrollable Block -->
                                    <div class="scroll-block position-relative">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-12">
                                                        <label class="form-label required"
                                                            style="font-size: 12px;font-weight: 600;">MRD
                                                            No:</label>&nbsp;&nbsp;<span
                                                            style="font-size:10px; color:red;" class="error_doctor errorss"></span>
                                                      </div>
                                                </div>
                                                <div class="col-sm-12" hidden>
                                                    <div class="mb-12"><br>
                                                        <label class="form-label required"
                                                            style="font-size: 12px;font-weight: 600;">Zone:</label>
                                                        <select class="mb-3 form-select zoneid"
                                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                            required name="empolyee_name">
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                        </div><br><br><br><br><br><br><br><br>
                                        <!-- Fixed Clear All Button -->
                                        <div class="card-footer sticky-bottom bg-white">
                                            <div class="d-flex justify-content-between">
                                                <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                                    data-bs-dismiss="offcanvas"
                                                    class="btn btn-outline-danger w-50 me-2 ">Clear All</a>
                                                <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                                    data-bs-dismiss="offcanvas" id="discountmorefil"
                                                    class="btn btn-outline-primary w-50">Submit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
                <!-- Add meeting -->
                <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Preview Discount Form<span
                                            id="docu_id"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <div class="form-border">
                                <input type="hidden" class="userid" name="dis_id" id="edit_disid" value="">
                                <input type="hidden" class="form-input" id="edit_dis_patient_ph">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                        <div class="form-title">Dr. ARAVIND’s IVF</div>
                                        <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
                                        </div>
                                        <div class="text-center align-self-center px-3 border"><strong>DISCOUNT FORM</strong></div>
                                        <div class="align-self-start">
                                            <!-- S. No :  <span><input type="text" id="edit_dis_sno" style="border: none;" readonly></sapn> -->
                                        </div>
                                    </div>
                                    <input type="text" id="ewife_mrd_no" style="border: none;" readonly>
                                    <input type="text" id="ehusband_mrd_no" style="border: none;" readonly>

                                    <br>
                                    <!-- Form Rows -->
                                    <div class="form-row-line mb-2">
                                        <div class="form-label-col">Branch Name</div><div class="form-colon">:</div>
                                        <div class="dropdown" style="width: 100%;">
                                          <input type="text" class="form-control searchInput single_search searchBranch"
                                                            name="zone_id" id="edit_zone_id" placeholder="Select Branch">
                                                        <div class="dropdown-options location-dropdown-options">
                                                            @if($locations)
                                                            @foreach ($locations as $location)
                                                            <div class="dropdown-item-loc_edit"
                                                                data-value="{{ $location->id }}">{{ $location->name }}
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                             <!-- <input type="text" class="form-input" id="edit_dis_branch_name"> -->
                                           </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Wife Name</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input2" id="edit_dis_wife_name" readonly>
                                                <div class="form-label-col" style="padding-left: 15px;width: 15%;">Wife MRD No</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input3" id="edit_dis_wife_mrd_no"  readonly>
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Husband Name</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input2" id="edit_dis_husband_name"  readonly>
                                                <div class="form-label-col" style="padding-left: 15px;width: 15%;">Husband MRD No</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input3" id="edit_dis_husband_mrd_no"  readonly>
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Service Name</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edit_dis_service_name" >
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Total Bill Value</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_total_bill" >
                                            </div>
                                            <div class="form-row-line mb-2">

                                                <div class="form-label-col">Discount Expected Request</div><div class="form-colon">:</div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="examountedit" name="expectdis" value="Amount">
                                                    <label for="examountedit">&#8377;</label></div>
                                                    <div class="form-check form-check-inline" style="margin-right: 0px;padding-left: 0px;">
                                                    <input type="radio" id="expercentageedit" name="expectdis" value="Percentage">
                                                    <label for="expercentageedit">%</label></div>
                                                    <input type="text" class="form-input" style="width:30%;" id="edittd_dis_expected_request">
                                                    <span class="postdisamount" id="ex_discount_value_edit" style="margin-left: 10px; font-weight: bold;"></span>

                                                <div class="form-label-col" style="width: 55%;padding-left: 10px;">
                                                    <input type="radio" id="op" name="disrequeste" class="form-check-input" value="OP">
                                                    <label for="html">op</label>
                                                    <input type="radio" id="ip" name="disrequeste" class="form-check-input" value="IP">
                                                    <label for="ip">IP</label>
                                                    <input type="radio" id="pharmacy" name="disrequeste" class="form-check-input" value="Pharmacy">
                                                    <label for="pharmacy">Pharmacy</label>
                                                </div>
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Post Discount</div><div class="form-colon">:</div>
                                                 <div class="form-check form-check-inline">
                                                    <input type="radio" id="postamountedit" name="postdis" value="Amount">&nbsp;
                                                    <label for="postamountedit">&#8377;</label>&nbsp;</div>
                                                    <div class="form-check form-check-inline" style="margin-right: 0px;padding-left: 0px;">
                                                    <input type="radio" id="postpercentageedit" name="postdis" value="Percentage">&nbsp;
                                                    <label for="postpercentageedit">%</label></div>
                                                    <input type="text" class="form-input" style="width:30%;" id="edittd_dis_post_discount">
                                                    <span class="postdisamount" id="post_discount_value_edit" style="margin-left: 10px; font-weight: bold;width: 55%;"></span>

                                            </div>

                                            <div class="form-row-line">
                                                <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_counselled_by">
                                            </div>

                                            <!-- Signature Section -->
                                                <div class="row signatures">
                                                <div class="col">
                                                <label>Wife Sign</label>
                                                <div class="signature-option-group mb-2" data-target="editwife">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="editwife-signature" value="upload" id="wifeUpload" checked>
                                                    <label class="form-check-label" for="wifeUpload">Upload Image</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="editwife-signature" value="draw" id="wifeDraw">
                                                    <label class="form-check-label" for="wifeDraw">Digital Sign</label>
                                                </div>
                                                </div>

                                                <div class="avatar-upload" id="editwife-upload" style="margin: 0px;">
                                                <i class="fa fa-close clear-sign" data-target="editwife" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                <div class="avatar-edit" style="position: relative;">
                                                    <input type="file" id="imagewsign" accept=".png, .jpg, .jpeg" />
                                                    <label for="imagewsign"></label>
                                                </div>
                                                <div class="avatar-preview" style="width: 200px;">
                                                    <div id="imagewifesign"></div>
                                                </div>
                                                </div>

                                                <div class="digital-sign mt-2" id="editwife-sign" style="display:none;">
                                                <canvas id="editwifeCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                                <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editwifeCanvas')">Clear</button>
                                                </div>
                                            </div>

                                            <div class="col">
                                             <label>Husband Sign</label>
                                                <div class="signature-option-group mb-2" data-target="edithusband">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="edithusband-signature" value="upload" id="husbandUpload" checked>
                                                    <label class="form-check-label" for="husbandUpload">Upload Image</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="edithusband-signature" value="draw" id="husbandDraw">
                                                    <label class="form-check-label" for="husbandDraw">Digital Sign</label>
                                                </div>
                                                </div>

                                                <div class="avatar-upload" id="edithusband-upload" style="margin: 0px;">
                                                <i class="fa fa-close clear-sign" data-target="edithusband" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                <div class="avatar-edit" style="position: relative;">
                                                    <input type="file" id="husbandsignimge" accept=".png, .jpg, .jpeg" />
                                                    <label for="husbandsignimge"></label>
                                                </div>
                                                <div class="avatar-preview" style="width: 200px;">
                                                    <div id="husimgPreviewe"></div>
                                                </div>
                                                </div>

                                                <div class="digital-sign mt-2" id="edithusband-sign" style="display:none;">
                                                <canvas id="edithusbandCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                                <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('edithusbandCanvas')">Clear</button>
                                                </div>
                                            </div>
                                            <div class="row signatures">
                                                <div class="col">
                                            <label>Dr Sign</label>
                                                    <div class="signature-option-group mb-2" data-target="editdr">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="editdr-signature" value="upload" id="drUpload" checked>
                                                        <label class="form-check-label" for="drUpload">Upload Image</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="editdr-signature" value="draw" id="drDraw">
                                                        <label class="form-check-label" for="drDraw">Digital Sign</label>
                                                    </div>
                                                    </div>

                                                    <div class="avatar-upload" id="editdr-upload" style="margin: 0px;">
                                                    <i class="fa fa-close clear-sign" data-target="editdr" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type="file" id="drsignimge" accept=".png, .jpg, .jpeg" />
                                                        <label for="drsignimge"></label>
                                                    </div>
                                                    <div class="avatar-preview" style="width: 200px;">
                                                        <div id="drimgPreviewe"></div>
                                                    </div>
                                                    </div>

                                                    <div class="digital-sign mt-2" id="editdr-sign" style="display:none;">
                                                    <canvas id="editdrCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editdrCanvas')">Clear</button>
                                                    </div>
                                            </div>

                                                <div class="col">
                                            <label>CC Sign</label>
                                                <div class="signature-option-group mb-2" data-target="editcc">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="editcc-signature" value="upload" id="ccUpload" checked>
                                                    <label class="form-check-label" for="ccUpload">Upload Image</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="editcc-signature" value="draw" id="ccDraw">
                                                    <label class="form-check-label" for="ccDraw">Digital Sign</label>
                                                </div>
                                                </div>

                                                <div class="avatar-upload" id="editcc-upload" style="margin: 0px;">
                                                <i class="fa fa-close clear-sign" data-target="editcc" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                <div class="avatar-edit" style="position: relative;">
                                                    <input type="file" id="ccsignimge" accept=".png, .jpg, .jpeg" />
                                                    <label for="ccsignimge"></label>
                                                </div>
                                                <div class="avatar-preview" style="width: 200px;">
                                                    <div id="ccimgPreviewe"></div>
                                                </div>
                                                </div>

                                                <div class="digital-sign mt-2" id="editcc-sign" style="display:none;">
                                                <canvas id="editccCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                                <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editccCanvas')">Clear</button>
                                                </div>
                                            </div>

                                                <div class="col">
                                           <label>Admin Sign</label>
                                            <div class="signature-option-group mb-2" data-target="editadmin">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editadmin-signature" value="upload" id="adminUpload" checked>
                                                <label class="form-check-label" for="adminUpload">Upload Image</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editadmin-signature" value="draw" id="adminDraw">
                                                <label class="form-check-label" for="adminDraw">Digital Sign</label>
                                            </div>
                                            </div>

                                            <div class="avatar-upload" id="editadmin-upload" style="margin: 0px;">
                                            <i class="fa fa-close clear-sign" data-target="editadmin" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                            <div class="avatar-edit" style="position: relative;">
                                                <input type="file" id="adminsignimge" accept=".png, .jpg, .jpeg" />
                                                <label for="adminsignimge"></label>
                                            </div>
                                            <div class="avatar-preview" style="width: 200px;">
                                                <div id="adminimgPreviewe"></div>
                                            </div>
                                            </div>

                                            <div class="digital-sign mt-2" id="editadmin-sign" style="display:none;">
                                            <canvas id="editadminCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editadminCanvas')">Clear</button>
                                            </div>
                                        </div>
                                            </div>

                                            <!-- Final Section -->
                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Final Authorised Amount</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_final_amt" readonly>
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">B.R. No.</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_brno" >
                                            </div>

                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Authorised By</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_auth_by">
                                            </div>
                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Final Approved By</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_final_approve">
                                            </div>
                                            </div>
                                      </div>
                                    <div class="modal-footer">
                                        <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                            id="close-button" class="btn btn-outline-danger"
                                            data-bs-dismiss="modal">Close</button>
                                              <button type="submit" id="editdiscountform"
                                            style="height: 34px;width: 133px;font-size: 12px;"
                                            class="btn btn-outline-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <div class="col-sm-12">
                <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Document Management system</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"><br>
                                        <div class="btn-group-vertical w-100" id="button_pdfs" style="
                                    margin-left: 11px;
                                ">
                                            <button type="button" class="btn btn-primary">Tab 1</button>
                                            <button type="button" class="btn btn-primary">Tab 2</button>
                                            <button type="button" class="btn btn-primary">Tab 3</button>
                                            <!-- More tabs if needed -->
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <embed id="pfmain" src="../public/document_data/1738390856_dictionary.pdf"
                                            width="100%" height="600px" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js">
</script>
<script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">
            Dropzone.options.myDropzone = {
                acceptedFiles: "image/*", // Only accept image files (any image type)
                addRemoveLinks: true, // Optionally, show remove links for the file
                dictDefaultMessage: "Drag an image here or click to select one image"
            };
            var start = moment(); // Today
            var end = moment();   // Today

            function my(start, end) {
                $("#mydateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                $("#mydateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

                if (start.isSame(end, 'day')) {
                    if (start.isSame(moment(), 'day')) {
                        $('#myreportrange span').html('Today');
                    } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                        $('#myreportrange span').html('Yesterday');
                    } else {
                        $('#myreportrange span').html(start.format('DD/MM/YYYY'));
                    }
                } else {
                    $('#myreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                }
            }

            $('#myreportrange').daterangepicker({
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
            }, my);
            // Initialize with 'Today'
            my(start, end);

        function mydata(start, end) {

        $("#mydateviewsallsave").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $("#mydateallviewssave").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        // Check the selected date range and adjust the display accordingly
        if (start.isSame(end, 'day')) {
            // If the start and end date are the same, show the single date
            if (start.isSame(moment(), 'day')) {
                $('#mysavereportrange span').html('Today');
            } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                $('#mysavereportrange span').html('Yesterday');
            } else {
                $('#mysavereportrange span').html(start.format('DD/MM/YYYY'));
            }
        } else {
            // For other ranges like "Last 7 Days", "This Month", etc.
            $('#mysavereportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format(
                'DD/MM/YYYY'));
        }
    }
    $('#mysavereportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')]
        }
    }, mydata);
    mydata(start, end);
    // Initial saved load: same as discount dashboard — API gets moredatefittervale=All (full list)
    $('#mysavereportrange span').html('All');
    $('#mydateallviewssave').text('All');
    $('#mydateviewsallsave').text('All');

            $(document).on('click', '.editbtn_user', function(e) {
                $('#exampleModaluser').modal('show');
            });
            $("#document_manage").css("color", "#080fd399");
            // Simulate fetching data
    const data = []; // Empty array means no data
    const tableBody = document.getElementById('table-body');
    const noDataMessage = document.getElementById('no-data');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');
    window.admin_user = {
        id: {{ $admin->id ?? 'null' }},
        name: @json($admin->username ?? ''),
        access_limits: @json($admin->access_limits ?? '')
    };

</script>
<script>
// $(document).ready(function() {

//         $(document).on("input", ".searchInput", function () {
//         const searchText = $(this).val().toLowerCase().split(",").pop().trim();
//         const currentValues = $(this).val().split(",").map(v => v.trim());

//         $(this).siblings(".dropdown-options").find("div").each(function () {
//             const optionText = $(this).text().trim().toLowerCase();
//             const fullText = $(this).text().trim();
//             const matchesSearch = optionText.includes(searchText);
//             const isSelected = currentValues.includes(fullText);

//             $(this).toggle(matchesSearch);
//             $(this).toggleClass("selected", isSelected);
//         });
//     });

//     // Ensure only valid values remain in the input field (for multiple search)
//     $(document).on("blur", ".multi_search", function() {
//         const inputField = $(this);
//         const typedValues = inputField.val().split(",").map(v => v.trim());
//         const validOptions = inputField.siblings(".dropdown-options").find("div")
//             .map(function() {
//                 return $(this).text().trim();
//             }).get();

//         // Filter typed values to keep only valid options
//         const filteredValues = typedValues.filter(v => validOptions.includes(v));

//         inputField.data("values", filteredValues);
//         inputField.val(filteredValues.join(", "));
//     });

//     // Close dropdown when clicking outside
//     $(document).on("click", function(event) {
//         if (!$(event.target).closest(".dropdown").length) {
//             $(".dropdown").removeClass("active");
//         }
//     });

//         $(".dropdown input").on("focus", function () {
//     // Close all dropdowns first
//     $(".dropdown").removeClass("active");
//     // Then open the one that's focused
//     $(this).closest(".dropdown").addClass("active");
//     });
// });


//         $(document).on("click", ".dropdown-options div", function () {
//         const selectedValue = $(this).text().trim();

//         const inputField = $(this).closest(".dropdown").find(".searchInput");
//         // alert(inputField);
//         if (inputField.hasClass("single_search")) {
//                                 // SINGLE selection: Replace previous value
//                                 inputField.val(selectedValue);
//                                 inputField.closest(".dropdown").removeClass("active"); // Close dropdown
//                             } else {
//         const currentValues = inputField.val().split(",").map(v => v.trim()).filter(Boolean);

//         if (!currentValues.includes(selectedValue)) {
//             currentValues.push(selectedValue);
//             inputField.val(currentValues.join(", "));
//         }

//         $(this).addClass("selected");

//         $(this).closest(".dropdown").removeClass("active");
//     }
//     });

    // On input focus
//     $(document).on("focus", ".searchInput", function () {
//         const inputField = $(this);
//         const currentValues = inputField.val().split(",").map(v => v.trim());

//         inputField.siblings(".dropdown-options").find("div").each(function () {
//             const optionText = $(this).text().trim();
//             const isSelected = currentValues.includes(optionText);

//             $(this).show();
//             $(this).toggleClass("selected", isSelected);
//         });

//         $(this).closest(".dropdown").addClass("active");
//     });
//     // Close dropdown when clicking outside
//     $(document).on("click", function(event) {
//         if (!$(event.target).closest(".dropdown").length) {
//             $(".dropdown").removeClass("active");
//         }
//     });

 // ============================================
// MODERN MULTI-SELECT DROPDOWN WITH ZONE-BRANCH FILTERING
// Clean and optimized version
// ============================================

$(document).ready(function () {

    /* ================================
       OPEN / CLOSE DROPDOWN
    ================================= */
    $(document).on('focus click', '.searchInput', function (e) {
        e.stopPropagation();
        $('.dropdown').not($(this).closest('.dropdown')).removeClass('active');
        $(this).closest('.dropdown').addClass('active');
    });

    $(document).on('click', function () {
        $('.dropdown').removeClass('active');
    });

    /* ================================
       OPTION CLICK (SINGLE + MULTI)
    ================================= */
    $(document).on('click', '.dropdown-options div', function (e) {
        e.stopPropagation();
        if ($(this).hasClass('no-results')) return;

        const $dropdown = $(this).closest('.dropdown');
        const $input = $dropdown.find('.searchInput');
        const isSingle = $input.hasClass('single_search');
        const text = $(this).text().trim();
        const value = $(this).data('value');

        if (isSingle) {
            $input.val(text);
            $dropdown.find('input[type="hidden"]').val(value || '');
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');
            $dropdown.removeClass('active');
            $input.trigger('change');
            return;
        }

        // MULTI SELECT
        let values = $input.val().split(',').map(v => v.trim()).filter(Boolean);

        if (values.includes(text)) {
            values = values.filter(v => v !== text);
            $(this).removeClass('selected');
        } else {
            values.push(text);
            $(this).addClass('selected');
        }

        $input.val(values.join(', '));
        $input.trigger('change');
    });

    /* ================================
       SEARCH INSIDE DROPDOWN
    ================================= */
    $(document).on('input', '.searchInput', function () {
        const search = $(this).val().split(',').pop().trim().toLowerCase();
        const $options = $(this).siblings('.dropdown-options');

        $options.find('div').not('.no-results').each(function () {
            if (!$(this).hasClass('filtered-by-zone')) {
                $(this).toggle($(this).text().toLowerCase().includes(search));
            }
        });

        $options.find('.no-results').remove();
        if ($options.find('div:visible').length === 0 && search) {
            $options.append('<div class="no-results">No results found</div>');
        }
    });

    /* ================================
       ZONE → BRANCH FILTER
    ================================= */
    $('#dis_zone_views').on('change', function () {
        filterBranches(
            $(this),
            '#lic_zone_id',
            '#dis_loc_views',
            '#getlocation'
        );
        // applyFilters();
    });

    $('#save_zone_views').on('change', function () {
        filterBranches(
            $(this),
            '#dissave_zone_id',
            '#save_loc_views',
            '#getlocationsave'
        );
    });

    function filterBranches($zoneInput, hiddenZoneId, branchInput, branchList) {

        const zoneNames = $zoneInput.val().split(',').map(v => v.trim()).filter(Boolean);
        const zoneIds = [];

        $zoneInput.siblings('.dropdown-options').find('div').each(function () {
            if (zoneNames.includes($(this).text().trim())) {
                zoneIds.push(String($(this).data('value')));
            }
        });

        $(hiddenZoneId).val(zoneIds.join(','));

        const $branches = $(branchList).find('div').not('.no-results');
        let visible = 0;

        $branches.each(function () {
            const match = zoneIds.length === 0 || zoneIds.includes(String($(this).data('type')));
            $(this).toggle(match).toggleClass('filtered-by-zone', !match);
            if (match) visible++;
        });

        const $branchInput = $(branchInput);
        const validBranches = $branches.filter(':visible').map(function () {
            return $(this).text().trim();
        }).get();

        const selected = $branchInput.val().split(',').map(v => v.trim());
        $branchInput.val(selected.filter(v => validBranches.includes(v)).join(', '));

        $(branchList).find('.no-results').remove();
        if (visible === 0) {
            $(branchList).append('<div class="no-results">No branches available</div>');
        }
    }

    /* ================================
       APPLY FILTER (AJAX / TABLE)
    ================================= */
    // function applyFilters() {
    //     console.log('Filter Applied:', {
    //         zone: $('#dis_zone_views').val(),
    //         branch: $('#dis_loc_views').val(),
    //         zone_id: $('#lic_zone_id').val()
    //     });

    //     // 👉 PLACE YOUR AJAX / TABLE RELOAD HERE
    // }

    /* ================================
       UX
    ================================= */
    $('.searchInput').attr('autocomplete', 'off');

});

</script>




<script>
   function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').css('background-image', 'url('+e.target.result +')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#imageUpload").change(function() {
    readURL(this);
});

function readURLhus(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#husimgPreview').css('background-image', 'url('+e.target.result +')');
            $('#husimgPreview').hide();
            $('#husimgPreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#husbandsignimg").change(function() {
    readURLhus(this);
});
function readURLdr(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#drimgPreview').css('background-image', 'url('+e.target.result +')');
            $('#drimgPreview').hide();
            $('#drimgPreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#drsignimg").change(function() {
    readURLdr(this);
});
function readURLcc(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#ccimgPreview').css('background-image', 'url('+e.target.result +')');
            $('#ccimgPreview').hide();
            $('#ccimgPreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#ccsignimg").change(function() {
    readURLcc(this);
});
function readURLadmin(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#adminimgPreview').css('background-image', 'url('+e.target.result +')');
            $('#adminimgPreview').hide();
            $('#adminimgPreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#adminsignimg").change(function() {
    readURLadmin(this);
});
//edit
function readURLedit(input) {
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          reader.onload = function (e) {
            $('#imagewifesign').css('background-image', 'url(' + e.target.result + ')');
            $('#imagewifesign').hide().fadeIn(650);
          };
          reader.readAsDataURL(input.files[0]);
        }
      }

      $('#imagewsign').change(function () {
        readURLedit(this);
      });
      function readURLhusedit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#husimgPreviewe').css('background-image', 'url('+e.target.result +')');
            $('#husimgPreviewe').hide();
            $('#husimgPreviewe').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#husbandsignimge").change(function() {
    readURLhusedit(this);
});
function readURLdredit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#drimgPreviewe').css('background-image', 'url('+e.target.result +')');
            $('#drimgPreviewe').hide();
            $('#drimgPreviewe').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#drsignimge").change(function() {
    readURLdredit(this);
});
function readURLccedit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#ccimgPreviewe').css('background-image', 'url('+e.target.result +')');
            $('#ccimgPreviewe').hide();
            $('#ccimgPreviewe').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#ccsignimge").change(function() {
    readURLccedit(this);
});
function readURLadminedit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#adminimgPreviewe').css('background-image', 'url('+e.target.result +')');
            $('#adminimgPreviewe').hide();
            $('#adminimgPreviewe').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#adminsignimge").change(function() {
    readURLadminedit(this);
});

// $(document).ready(function () {

//     /* =========================================================
//        COMMON MULTI-SELECT HELPER
//     ========================================================= */
//     function toggleMulti(input, hiddenInput, text, id) {
//         let texts = input.val() ? input.val().split(', ') : [];
//         let ids   = hiddenInput.val() ? hiddenInput.val().split(',') : [];

//         if (texts.includes(text)) {
//             texts = texts.filter(t => t !== text);
//             ids   = ids.filter(i => i !== String(id));
//         } else {
//             texts.push(text);
//             ids.push(id);
//         }

//         input.val(texts.join(', '));
//         hiddenInput.val(ids.join(','));
//     }

//     /* =========================================================
//        FILTER 1 : SEARCH (ZONE → BRANCH)
//     ========================================================= */

//     // ZONE MULTI SELECT
//     $('.selectzone > div').on('click', function () {
//         toggleMulti(
//             $('#dis_zone_views'),
//             $('#lic_zone_id'),
//             $(this).text(),
//             $(this).data('value')
//         );
//     });

//     // BRANCH INPUT FOCUS → FILTER BY SELECTED ZONES
//     $('#dis_loc_views').on('focus', function () {

//         let zoneIds = $('#lic_zone_id').val()
//             ? $('#lic_zone_id').val().split(',').map(Number)
//             : [];

//         if (zoneIds.length > 0) {
//             $('#getlocation > div')
//                 .hide()
//                 .filter(function () {
//                     return zoneIds.includes(Number($(this).data('type')));
//                 })
//                 .show();
//         } else {
//             $('#getlocation > div').show();
//         }

//         $('#getlocation').show();
//     });

//     // BRANCH MULTI SELECT
//     $('#getlocation > div').on('click', function () {

//         let input = $('#dis_loc_views');
//         let text  = $(this).data('value');

//         let values = input.val() ? input.val().split(', ') : [];

//         if (values.includes(text)) {
//             values = values.filter(v => v !== text);
//             $(this).removeClass('selected');
//         } else {
//             values.push(text);
//             $(this).addClass('selected');
//         }

//         input.val(values.join(', '));
//     });

//     /* =========================================================
//        FILTER 2 : SAVE (ZONE → BRANCH)
//     ========================================================= */

//     // ZONE MULTI SELECT
//     $('.selectzonesave > div').on('click', function () {
//         toggleMulti(
//             $('#save_zone_views'),
//             $('#dissave_zone_id'),
//             $(this).text(),
//             $(this).data('value')
//         );
//     });

//     // BRANCH INPUT FOCUS → FILTER BY SELECTED ZONES
//     $('#save_loc_views').on('focus', function () {

//         let zoneIds = $('#dissave_zone_id').val()
//             ? $('#dissave_zone_id').val().split(',').map(Number)
//             : [];

//         if (zoneIds.length > 0) {
//             $('#getlocationsave > div')
//                 .hide()
//                 .filter(function () {
//                     return zoneIds.includes(Number($(this).data('type')));
//                 })
//                 .show();
//         } else {
//             $('#getlocationsave > div').show();
//         }

//         $('#getlocationsave').show();
//     });

//     // BRANCH MULTI SELECT
//     $('#getlocationsave > div').on('click', function () {

//         let input = $('#save_loc_views');
//         let text  = $(this).data('value');

//         let values = input.val() ? input.val().split(', ') : [];

//         if (values.includes(text)) {
//             values = values.filter(v => v !== text);
//             $(this).removeClass('selected');
//         } else {
//             values.push(text);
//             $(this).addClass('selected');
//         }

//         input.val(values.join(', '));
//     });

//     /* =========================================================
//        UX IMPROVEMENTS
//     ========================================================= */

//     // Disable browser autofill
//     $('input.searchInput').attr('autocomplete', 'off');

//     // Close dropdowns on outside click
//     $(document).on('click', function (e) {
//         if (!$(e.target).closest('.dropdown').length) {
//             $('.dropdown-options').hide();
//         }
//     });

// });


     $(document).ready(function () {
        $('.selectzone > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
            $('#dis_zone_views').val(selectedText);
            $('#lic_zone_id').val(selectedType);
            $('#dis_loc_views').val('');
            $('#getlocation').hide();

            $('#getlocation > div').removeClass('selected');

           $('#getlocation > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

       $('#dis_zone_views').on('input', function () {
            $('#lic_zone_id').val('');
            $('#getlocation > div').show();
            $('#dis_loc_views').val('');
            $('#getlocation > div').removeClass('selected');
        });

        $('#dis_loc_views').on('focus', function () {
            const selectedType = Number($('#lic_zone_id').val()); // use hidden ID

            if (selectedType) {
                $('#getlocation > div')
                    .hide()
                    .filter(function () {
                        return Number($(this).data('type')) === selectedType;
                    })
                    .show();
            } else {
                // $('#getlocation > div').show().removeClass('selected');
            }

            $('#getlocation').show();
        });
       $('#getlocation > div').off('click').on('click', function () {
            const name = $(this).data('value');
            $('#dis_loc_views').val(name);

            $('#getlocation > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocation').hide();
        });

        $('input.searchInput').attr('autocomplete', 'off');
    });


    $(document).ready(function () {
        $('.selectzonesave > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
            $('#save_zone_views').val(selectedText);
            $('#dissave_zone_id').val(selectedType);
            $('#save_loc_views').val('');
            $('#getlocationsave').hide();

            $('#getlocationsave > div').removeClass('selected');

           $('#getlocationsave > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

       $('#save_zone_views').on('input', function () {
            $('#dissave_zone_id').val('');
            $('#getlocationsave > div').show();
            $('#save_loc_views').val('');
            $('#getlocationsave > div').removeClass('selected');
        });

        $('#save_loc_views').on('focus', function () {
            const selectedType = Number($('#dissave_zone_id').val()); // use hidden ID

            if (selectedType) {
                $('#getlocationsave > div')
                    .hide()
                    .filter(function () {
                        return Number($(this).data('type')) === selectedType;
                    })
                    .show();
            } else {
                $('#getlocationsave > div').show().removeClass('selected');
            }

            $('#getlocationsave').show();
        });

        
       $('#getlocationsave > div').off('click').on('click', function () {
            const name = $(this).data('value');
            $('#save_loc_views').val(name);

            $('#getlocationsave > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocationsave').hide();
        });

        $('input.searchInput').attr('autocomplete', 'off');
    });

// Signature toggle logic
$('input[type="radio"]').change(function() {
    const type = $(this).val();
    const group = $(this).closest('.signature-option-group');
    const target = group.data('target');

    if (type === 'upload') {
        $(`#${target}-upload`).show();
        $(`#${target}-sign`).hide();

        // Clear digital sign (canvas)
        const canvas = document.getElementById(`${target}Canvas`);
        if (canvas) {
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

    } else {
        $(`#${target}-upload`).hide();
        $(`#${target}-sign`).show();

        $(`#${target}-upload input[type="file"]`).val('');
        $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
    }
});
$(document).on('click', '.clear-sign', function () {
    const target = $(this).data('target');
    $(`#${target}-upload input[type="file"]`).val('');
    $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
});

// $('input[type=radio]').on('change', function () {
//   const target = $(this).closest('.signature-option-group').data('target');
//   const isUpload = $(this).val() === 'upload';
//   $(`#${target}-upload`).toggle(isUpload);
//   $(`#${target}-sign`).toggle(!isUpload);
// });


// Digital signature drawing
function setupCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext("2d");
    let drawing = false;

    canvas.addEventListener("mousedown", () => drawing = true);
    canvas.addEventListener("mouseup", () => drawing = false);
    canvas.addEventListener("mouseout", () => drawing = false);
    canvas.addEventListener("mousemove", function(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.strokeStyle = "#000";
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    });
}

function clearCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}
['wifeCanvas', 'husbandCanvas', 'drCanvas', 'ccCanvas', 'adminCanvas'].forEach(setupCanvas);
['editwifeCanvas', 'edithusbandCanvas', 'editdrCanvas', 'editccCanvas', 'editadminCanvas'].forEach(setupCanvas);



const disdocdetialsUrl="{{route('superadmin.discountformdoc_detials')}}";
const discountformeditUrl="{{route('superadmin.discountformeditsave')}}";
const mrdnoUrl="{{route('superadmin.discountmrdno')}}";
const discountdatatd ="{{route('superadmin.discount_datatdadded')}}";
const discountform_data="{{route('superadmin.discountform_data')}}";
const disformsave_data="{{route('superadmin.disformsave_data')}}";
const discountform_documentaddedUrl="{{route('superadmin.discount_documentadded')}}";
</script>

<script>
   $('#ex_discount_display').on('blur', function () {
  const totalBill = parseFloat($('#totalbill').val()) || 0;
  const rawValue = $(this).val().replace(/[^0-9.]/g, '');
  const discountValue = parseFloat(rawValue) || 0;

  const expercentage = $('#expercentage').is(':checked');
  const examount = $('#examount').is(':checked');

  let formattedInput = '';
  let discountAmount = 0;

  if (expercentage) {
    if (discountValue > 100) {
      alert('Please enter a valid discount: percentage cannot exceed 100%.');
      $(this).val('');
      $('#ex_discount_value').text('');
      return;
    }
    discountAmount = (totalBill * discountValue) / 100;
    formattedInput = discountValue + '%';

  } else if (examount) {
    if (discountValue > totalBill) {
      alert('Please enter a valid discount: amount cannot exceed total bill.');
      $(this).val('');
      $('#ex_discount_value').text('');
      return;
    }
    discountAmount = discountValue;
    formattedInput = discountValue + '₹';
  }

  // Update input with symbol and show discount amount
  $(this).val(formattedInput);
  $('#ex_discount_value').text(discountAmount.toFixed(2) + '₹');
});

$('#post_discount_display').on('blur', function () {
  const totalBill = parseFloat($('#totalbill').val()) || 0;
  const rawValue = $(this).val().replace(/[^0-9.]/g, '');
  const discountValue = parseFloat(rawValue) || 0;

  const isPercentage = $('#postpercentage').is(':checked');
  const isAmount = $('#postamount').is(':checked');

  let formattedInput = '';
  let discountAmount = 0;

  if (isPercentage) {
    if(discountValue > 100){
        alert('Please enter a valid discount: percentage cannot exceed 100%.');
        $(this).val('');
        $('#post_discount_value').text('');
        return;
    }
    discountAmount = (totalBill * discountValue) / 100;
    formattedInput = discountValue + '%';
  } else if (isAmount) {
    if (discountValue > totalBill) {
      alert('Please enter a valid discount: amount cannot exceed total bill.');
      $(this).val('');
      $('#post_discount_value').text('');
      return;
    }
    discountAmount = discountValue;
    formattedInput = discountValue + '₹';
  }

  $(this).val(formattedInput);

  $('#post_discount_value').text(discountAmount.toFixed(2) + '₹');
});
$('#totalbill').on('blur', function () {
  let inputVal = $(this).val().trim();
  let numericVal = inputVal.replace(/[^0-9.]/g, ''); // Keep only numbers and dot

  if (!numericVal || isNaN(numericVal)) {
    alert('Please enter a valid number for the total bill.');
    $(this).val('');
    return;
  }

  const totalBill = parseFloat(numericVal);
  const formattedInput = totalBill.toFixed(2) + '₹';

  $(this).val(formattedInput);
});

$(document).ready(function() {
    function updateFinalAmount() {
        var totalBill = parseFloat($('#totalbill').val()) || 0;
        var postDiscountText = $('#post_discount_value').text().trim();
        var postDiscount = parseFloat(postDiscountText) || 0;

        var finalAmount = totalBill - postDiscount;
        $('#final_amount').val(finalAmount.toFixed(2));
    }

    $('#totalbill').on('input', updateFinalAmount);

    // Trigger when the post_discount_value span changes
    const observer = new MutationObserver(updateFinalAmount);
    observer.observe(document.getElementById('post_discount_value'), { childList: true, characterData: true, subtree: true });
});
</script>

<script>
   $('#edittd_dis_expected_request').on('blur', function () {
  const totalBill = parseFloat($('#edittd_dis_total_bill').val()) || 0;
  const rawValue = $(this).val().replace(/[^0-9.]/g, '');
  const discountValue = parseFloat(rawValue) || "";
  const expercentage = $('#expercentageedit').is(':checked');
  const examount = $('#examountedit').is(':checked');

  let formattedInput = '';
  let discountAmount = 0;
  if (!expercentage && !examount) {
  alert('Please select either ₹ or % for the expected discount.');
  $(this).val('');
  $('#ex_discount_value_edit').text('');
  return;
}

  if (expercentage) {
    if (discountValue > 100) {
      alert('Please enter a valid discount: percentage cannot exceed 100%.');
      $(this).val('');
      $('#ex_discount_value_edit').text('');
      return;
    }
    discountAmount = (totalBill * discountValue) / 100;
    formattedInput = discountValue + '%';

  } else if (examount) {
    if (discountValue > totalBill) {
      alert('Please enter a valid discount: amount cannot exceed total bill.');
      $(this).val('');
      $('#ex_discount_value_edit').text('');
      return;
    }
    discountAmount = discountValue;
    formattedInput = discountValue + '₹';
  }

  // Update input with symbol and show discount amount
  $(this).val(formattedInput);
  $('#ex_discount_value_edit').text(discountAmount.toFixed(2) + '₹');
});

$('#edittd_dis_post_discount').on('blur', function () {
  const totalBill = parseFloat($('#edittd_dis_total_bill').val()) || 0;
  const rawValue = $(this).val().replace(/[^0-9.]/g, '');
  const discountValue = parseFloat(rawValue) || 0;
  const isPercentage = $('#postpercentageedit').is(':checked');
  const isAmount = $('#postamountedit').is(':checked');

  let formattedInput = '';
  let discountAmount = 0;
if (!isPercentage && !isAmount) {
  alert('Please select either ₹ or % for the expected discount.');
  $(this).val('');
  $('#ex_discount_value_edit').text('');
  return;
}

  if (isPercentage) {
    if(discountValue > 100){
        alert('Please enter a valid discount: percentage cannot exceed 100%.');
        $(this).val('');
        $('#post_discount_value_edit').text('');
        return;
    }
    discountAmount = (totalBill * discountValue) / 100;
    formattedInput = discountValue + '%';
  } else if (isAmount) {
    if (discountValue > totalBill) {
      alert('Please enter a valid discount: amount cannot exceed total bill value.');
      $(this).val('');
      $('#post_discount_value_edit').text('');
      return;
    }
    discountAmount = discountValue;
    formattedInput = discountValue + '₹';
  }

  $(this).val(formattedInput);

  $('#post_discount_value_edit').text(discountAmount.toFixed(2) + '₹');
});
$('#edittd_dis_total_bill').on('blur', function () {
  let inputVal = $(this).val().trim();
  let numericVal = inputVal.replace(/[^0-9.]/g, ''); // Keep only numbers and dot

  if (!numericVal || isNaN(numericVal)) {
    alert('Please enter a valid number for the total bill value.');
    $(this).val('');
    return;
  }
  const totalBill = parseFloat(numericVal);
  const formattedInput = totalBill.toFixed(2) + '₹';

  $(this).val(formattedInput);
});

$(document).ready(function() {
    function updateFinalAmount() {
        var totalBill = parseFloat($('#edittd_dis_total_bill').val()) || 0;
        var postDiscountText = $('#post_discount_value_edit').text().trim();
        var postDiscount = parseFloat(postDiscountText) || 0;

        var finalAmount = totalBill - postDiscount;
        $('#edittd_dis_final_amt').val(finalAmount.toFixed(2));
    }

    $('#edittd_dis_total_bill').on('input', updateFinalAmount);

    // Trigger when the post_discount_value span changes
    const observer = new MutationObserver(updateFinalAmount);
    observer.observe(document.getElementById('post_discount_value_edit'), { childList: true, characterData: true, subtree: true });
});

// $(document).on('click', '.approve-btn, .reject-btn', function (e) {
//     e.preventDefault();
//     e.stopPropagation();

//     let status = $(this).data('status'); // 1 = approve, 2 = reject
//     let id = $(this).closest('.action-icons').data('id');

//     let actionText = status == 1 ? 'Approve' : 'Reject';
//     let actionColor = status == 1 ? '#2e7d32' : '#c62828';

//     Swal.fire({
//         title: `Confirm ${actionText}?`,
//         text: `Are you sure you want to ${actionText.toLowerCase()} this bill?`,
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: actionColor,
//         cancelButtonColor: '#6c757d',
//         confirmButtonText: `Yes, ${actionText}`
//     }).then((result) => {

//         if (result.isConfirmed) {

//             $.ajax({
//                 url: "{{ route('superadmin.discount_approvereject') }}",
//                 type: "POST",
//                 data: {
//                     id: id,
//                     status: status,
//                     _token: $('meta[name="csrf-token"]').attr('content')
//                 },
//                 success: function (res) {
//                     Swal.fire(
//                         'Success!',
//                         res.message,
//                         'success'
//                     );
//                     setTimeout(function() {
//                         location.reload();
//                     }, 500);
//                 },
//                 error: function () {
//                     Swal.fire(
//                         'Error!',
//                         'Something went wrong',
//                         'error'
//                     );
//                 }
//             });

//         }
//     });
//     });

$(document).on('click', '.approve-btn, .reject-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const status = $btn.data('status');
    const id = $btn.data('id');
    const $row = $btn.closest('tr');

    const actionText = status == 1 ? 'Approve' : 'Reject';
    const actionColor = status == 1 ? '#2e7d32' : '#c62828';

    Swal.fire({
        title: `Confirm ${actionText}?`,
        text: `Are you sure you want to ${actionText.toLowerCase()} this discount?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: actionColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionText}`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            
            // Show loading state
            $row.addClass('updating-row');
            $btn.css('opacity', '0.5');

            $.ajax({
                url: "{{ route('superadmin.discount_approvereject') }}",
                type: "POST",
                data: {
                    id: id,
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the specific row
                        updateTableRow($row, res.record, status);
                        
                        // Update statistics cards
                        if (res.statistics) {
                            renderStatisticsCards(res.statistics);
                        }
                        
                        // Update count badges
                        if (res.counts) {
                            updateCountBadges(res.counts);
                        }

                        // Remove loading state
                        $row.removeClass('updating-row');

                    } else {
                        Swal.fire('Error!', res.message || 'Update failed', 'error');
                        $row.removeClass('updating-row');
                        $btn.css('opacity', '1');
                    }
                },
                error: function (xhr) {
                    let errorMsg = 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire('Error!', errorMsg, 'error');
                    $row.removeClass('updating-row');
                    $btn.css('opacity', '1');
                }
            });
        }
    });
});
</script>


                    <!-- [ Main Content ] end -->
                    @include('superadmin.superadminfooter')
                    <script src="{{ asset('/assets/document/discountdocument.js') }}"></script>
</body>
<!-- [Body] end -->

</html>